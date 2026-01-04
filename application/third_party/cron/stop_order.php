<?php

/* 예약 자동중지를 위한 스크립트입니다 */

try {
    $log_file = basename(__FILE__, '.php').'.log';
    include __DIR__.DIRECTORY_SEPARATOR.'common_head.php';

    $stmt_count = $pdo->prepare('SELECT count(*) FROM user_stop_schedules AS uss INNER JOIN user_stops AS us ON uss.user_stop_id=us.id WHERE uss.enable=1 AND uss.schedule_date<=CURDATE() AND uss.enable=1');
    $stmt_count->execute();
    $count = $stmt_count->fetchColumn();
    $stmt_count->closeCursor();

    if (empty($count)) {
        sl_log($log, 'Already Sync');

        return true;
    }

    $stmt_select = $pdo->prepare('SELECT us.*,uss.id as schedule_id,uss.user_stop_id FROM user_stop_schedules AS uss INNER JOIN user_stops AS us ON uss.user_stop_id=us.id WHERE us.enable=0 AND uss.enable= 1 AND uss.schedule_date<=CURDATE()');
    $stmt_select->execute();
    $list = $stmt_select->fetchAll(PDO::FETCH_ASSOC);
    $stmt_select->closeCursor();

    $stmt_insert = $pdo->prepare('INSERT INTO order_stops(user_stop_id,order_id,stop_start_date,stop_end_date,is_change_start_date,stop_day_count,created_at,updated_at) VALUES(:user_stop_id,:order_id,:stop_start_date,:stop_end_date,:is_change_start_date,:stop_day_count,NOW(),NOW())');
    $stmt_update = $pdo->prepare('UPDATE `orders` SET `stopped`=1 WHERE `id`=:id');
    $stmt_update_stop = $pdo->prepare('UPDATE `user_stops` SET `enable`=1 WHERE `id`=:id');
    $stmt_update_stop_schedule = $pdo->prepare('UPDATE `user_stop_schedules` SET `enable`=0 WHERE `id`=:id');

    $stmt_count_enrolls = $pdo->prepare('SELECT count(*) FROM orders AS o INNER JOIN enrolls AS e ON e.order_id=o.id LEFT JOIN order_ends AS oe ON oe.order_id=o.id WHERE e.end_date>=CURDATE() AND o.enable=1 AND oe.id IS NULL AND o.user_id=:user_id');
    $stmt_select_enrolls = $pdo->prepare('SELECT e.*,o.branch_id,o.user_id FROM orders AS o INNER JOIN enrolls AS e ON e.order_id=o.id LEFT JOIN order_ends AS oe ON oe.order_id=o.id WHERE e.end_date>=CURDATE() AND o.enable=1 AND oe.id IS NULL AND o.user_id=:user_id');
    $stmt_count_rents = $pdo->prepare('SELECT count(*) FROM orders AS o INNER JOIN rents AS r ON r.order_id=o.id LEFT JOIN order_ends AS oe ON oe.order_id=o.id WHERE DATE(r.end_datetime)>=CURDATE() AND o.enable=1 AND oe.id IS NULL AND o.user_id=:user_id');
    $stmt_select_rents = $pdo->prepare('SELECT r.*,DATE(r.start_datetime) as start_date,DATE(r.end_datetime) as end_date FROM orders AS o INNER JOIN rents AS r ON r.order_id=o.id LEFT JOIN order_ends AS oe ON oe.order_id=o.id WHERE DATE(r.end_datetime)>=CURDATE() AND o.enable=1 AND oe.id IS NULL AND o.user_id=:user_id');
    $stmt_count_rent_sws = $pdo->prepare('SELECT count(*) FROM orders AS o INNER JOIN rent_sws AS rs ON rs.order_id=o.id LEFT JOIN order_ends AS oe ON oe.order_id=o.id WHERE rs.end_date>=CURDATE() AND o.enable=1 AND oe.id IS NULL AND o.user_id=:user_id');
    $stmt_select_rent_sws = $pdo->prepare('SELECT rs.* FROM orders AS o INNER JOIN rent_sws AS rs ON rs.order_id=o.id LEFT JOIN order_ends AS oe ON oe.order_id=o.id WHERE rs.end_date>=CURDATE() AND o.enable=1 AND oe.id IS NULL AND o.user_id=:user_id');
    $stmt_is_pt = $pdo->prepare('SELECT count(*) FROM enrolls AS e INNER JOIN courses AS c  ON e.course_id=c.id WHERE e.id=:enroll_id AND c.lesson_type=4');

    $affect_orders = array();
    $change_users = array();

    // 트랜잭션 시작
    $pdo->beginTransaction();

    foreach ($list as $schedule) {
        $stmt_count_enrolls->bindParam(':user_id', $schedule['user_id'], PDO::PARAM_INT);
        $stmt_count_enrolls->execute();
        $count_enroll = $stmt_count_enrolls->fetchColumn();

        if ($count_enroll) {
            $stmt_select_enrolls->bindParam(':user_id', $schedule['user_id'], PDO::PARAM_INT);
            $stmt_select_enrolls->execute();
            $enroll_list = $stmt_select_enrolls->fetchAll(PDO::FETCH_ASSOC);

            foreach ($enroll_list as $enroll) {
                $stmt_is_pt->bindParam(':enroll_id', $enroll['id'], PDO::PARAM_INT);
                $stmt_is_pt->execute();
                $is_pt = $stmt_is_pt->fetchColumn();

                if (!empty($is_pt)) {
                    continue;
                }

                $change_users[] = array('branch_id' => $enroll['branch_id'], 'user_id' => $enroll['user_id']);

                $is_change_start_date = get_is_change_start_date($enroll['start_date'], $schedule['stop_start_date'], $dateTimeZone);

                $stmt_insert->bindParam(':user_stop_id', $schedule['user_stop_id'], PDO::PARAM_INT);
                $stmt_insert->bindParam(':order_id', $enroll['order_id'], PDO::PARAM_INT);
                $stmt_insert->bindParam(':stop_start_date', $schedule['stop_start_date'], PDO::PARAM_STR);
                $stmt_insert->bindParam(':stop_end_date', $schedule['stop_end_date'], PDO::PARAM_STR);
                $stmt_insert->bindParam(':is_change_start_date', $is_change_start_date, PDO::PARAM_INT);
                $stmt_insert->bindParam(':stop_day_count', $schedule['stop_day_count'], PDO::PARAM_INT);
                $stmt_insert->execute();
                $order_stop_id = $pdo->lastInsertId();

                $stmt_update->bindParam(':id', $enroll['order_id'], PDO::PARAM_INT);
                $stmt_update->execute();

                $affect_orders[] = $enroll['order_id'];
            }
        }

        $stmt_count_rents->bindParam(':user_id', $schedule['user_id'], PDO::PARAM_INT);
        $stmt_count_rents->execute();
        $count_rent = $stmt_count_rents->fetchColumn();

        if ($count_rent) {
            $stmt_select_rents->bindParam(':user_id', $schedule['user_id'], PDO::PARAM_INT);
            $stmt_select_rents->execute();
            $rent_list = $stmt_select_rents->fetchAll(PDO::FETCH_ASSOC);

            foreach ($rent_list as $rent) {
                $is_change_start_date = get_is_change_start_date($rent['start_date'], $schedule['stop_start_date'], $dateTimeZone);

                $stmt_insert->bindParam(':user_stop_id', $schedule['user_stop_id'], PDO::PARAM_INT);
                $stmt_insert->bindParam(':order_id', $rent['order_id'], PDO::PARAM_INT);
                $stmt_insert->bindParam(':stop_start_date', $schedule['stop_start_date'], PDO::PARAM_STR);
                $stmt_insert->bindParam(':stop_end_date', $schedule['stop_end_date'], PDO::PARAM_STR);
                $stmt_insert->bindParam(':is_change_start_date', $is_change_start_date, PDO::PARAM_INT);
                $stmt_insert->bindParam(':stop_day_count', $schedule['stop_day_count'], PDO::PARAM_INT);
                $stmt_insert->execute();
                $order_stop_id = $pdo->lastInsertId();

                $stmt_update->bindParam(':id', $rent['order_id'], PDO::PARAM_INT);
                $stmt_update->execute();

                $affect_orders[] = $rent['order_id'];
            }
        }

        $stmt_count_rent_sws->bindParam(':user_id', $schedule['user_id'], PDO::PARAM_INT);
        $stmt_count_rent_sws->execute();
        $count_rent_sw = $stmt_count_rent_sws->fetchColumn();

        if ($count_rent_sw) {
            $stmt_select_rent_sws->bindParam(':user_id', $schedule['user_id'], PDO::PARAM_INT);
            $stmt_select_rent_sws->execute();
            $rent_sws_list = $stmt_select_rent_sws->fetchAll(PDO::FETCH_ASSOC);

            foreach ($rent_sws_list as $rent_sw) {
                $is_change_start_date = get_is_change_start_date($rent_sw['start_date'], $schedule['stop_start_date'], $dateTimeZone);

                $stmt_insert->bindParam(':user_stop_id', $schedule['user_stop_id'], PDO::PARAM_INT);
                $stmt_insert->bindParam(':order_id', $rent_sw['order_id'], PDO::PARAM_INT);
                $stmt_insert->bindParam(':stop_start_date', $schedule['stop_start_date'], PDO::PARAM_STR);
                $stmt_insert->bindParam(':stop_end_date', $schedule['stop_end_date'], PDO::PARAM_STR);
                $stmt_insert->bindParam(':is_change_start_date', $is_change_start_date, PDO::PARAM_INT);
                $stmt_insert->bindParam(':stop_day_count', $schedule['stop_day_count'], PDO::PARAM_INT);
                $stmt_insert->execute();
                $order_stop_id = $pdo->lastInsertId();

                $stmt_update->bindParam(':id', $rent_sw['order_id'], PDO::PARAM_INT);
                $stmt_update->execute();

                $affect_orders[] = $rent_sw['order_id'];
            }
        }

        $stmt_update_stop->bindParam(':id', $schedule['id'], PDO::PARAM_INT);
        $stmt_update_stop->execute();

        $stmt_update_stop_schedule->bindParam(':id', $schedule['schedule_id'], PDO::PARAM_INT);
        $stmt_update_stop_schedule->execute();
    }
    $stmt_update->closeCursor();
    $stmt_update_stop->closeCursor();
    $stmt_update_stop_schedule->closeCursor();

    // 커밋
    $pdo->commit();

    if (!count($change_users)) {
        $pdo = null;
        complete_order($log, $affect_orders);

        return true;
    }

    include __DIR__.DIRECTORY_SEPARATOR.'sync_ist_common.php';
    sync_ist_user($db, $pdo, $log, $change_users);

    $pdo = null;
} catch (Exception $e) {
    include __DIR__.DIRECTORY_SEPARATOR.'common_catch.php';
}

function get_is_change_start_date($start_date, $stop_start_date, $timezone)
{
    $start_date_obj = new DateTime($start_date, $timezone);
    $stop_start_date_obj = new DateTime($stop_start_date, $timezone);

    $is_change_start_date = 0;

    if ($stop_start_date_obj < $start_date_obj) {
        $is_change_start_date = 1;
    }

    return $is_change_start_date;
}

function complete_order($log, $affect_orders)
{
    $affect_count = count($affect_orders);
    sl_log($log, 'count:'.$affect_count);

    if ($affect_count) {
        foreach ($affect_orders as $order_id) {
            sl_log($log, 'update_order :'.$order_id);
        }
    }
}

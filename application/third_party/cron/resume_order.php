<?php

/* 중지된 주문 자동재개를 위한 스크립트입니다 */

try {
    $log_file = basename(__FILE__, '.php').'.log';
    include __DIR__.DIRECTORY_SEPARATOR.'common_head.php';

    $stmt_count = $pdo->prepare('SELECT count(*) FROM order_stops AS os INNER JOIN orders AS o ON os.order_id=o.id INNER JOIN user_stops as us ON os.user_stop_id=us.id WHERE os.stop_end_date is not null AND os.stop_end_date<CURDATE() AND os.enable=1');
    $stmt_count->execute();
    $count = $stmt_count->fetchColumn();
    $stmt_count->closeCursor();

    if (empty($count)) {
        sl_log($log, 'Already Sync');

        return true;
    }

    $stmt_select = $pdo->prepare('SELECT os.*,us.request_date FROM order_stops AS os INNER JOIN orders AS o ON os.order_id=o.id INNER JOIN user_stops as us ON os.user_stop_id=us.id WHERE os.stop_end_date is not null AND os.stop_end_date<CURDATE() AND os.enable=1');
    $stmt_select->execute();
    $list = $stmt_select->fetchAll(PDO::FETCH_ASSOC);
    $stmt_select->closeCursor();

    $stmt_count_enroll = $pdo->prepare('SELECT count(*) FROM enrolls AS e INNER JOIN orders AS o ON e.order_id=o.id WHERE o.id=:id');
    $stmt_count_rent = $pdo->prepare('SELECT count(*) FROM rents AS r INNER JOIN orders AS o ON r.order_id=o.id WHERE o.id=:id');
    $stmt_count_rent_sw = $pdo->prepare('SELECT count(*) FROM rent_sws AS rws INNER JOIN orders AS o ON rws.order_id=o.id WHERE o.id=:id');

    $stmt_select_enroll = $pdo->prepare('SELECT o.*,e.start_date,e.end_date FROM enrolls AS e INNER JOIN orders AS o ON e.order_id=o.id WHERE o.id=:id');
    $stmt_select_rent = $pdo->prepare('SELECT o.*,date(r.start_datetime) AS start_date,date(r.end_datetime) AS end_date FROM rents AS r INNER JOIN orders AS o ON r.order_id=o.id WHERE o.id=:id');
    $stmt_select_rent_sw = $pdo->prepare('SELECT o.*,rs.start_date,rs.end_date FROM rent_sws AS rs INNER JOIN orders AS o ON rs.order_id=o.id WHERE o.id=:id');

    $stmt_update_order = $pdo->prepare('UPDATE orders SET stopped=0 WHERE id=:id');
    $stmt_update_enroll = $pdo->prepare('UPDATE enrolls SET end_date=:end_date WHERE order_id=:order_id');
    $stmt_update_rent = $pdo->prepare('UPDATE rents SET end_datetime=:end_datetime WHERE order_id=:order_id');
    $stmt_update_rent_sw = $pdo->prepare('UPDATE rent_sws SET end_date=:end_date WHERE order_id=:order_id');
    $stmt_update_order_stop = $pdo->prepare('UPDATE order_stops SET enable=0 WHERE id=:id');
    $stmt_update_start_enroll = $pdo->prepare('UPDATE enrolls SET start_date=:start_date WHERE order_id=:order_id');
    $stmt_update_start_rent = $pdo->prepare('UPDATE rents SET start_datetime=:start_datetime WHERE order_id=:order_id');
    $stmt_update_start_rent_sw = $pdo->prepare('UPDATE rent_sws SET start_date=:start_date WHERE order_id=:order_id');

    $stmt_count_user_stop_content = $pdo->prepare('SELECT count(*) FROM user_stop_contents WHERE user_stop_id=:user_stop_id');
    $stmt_select_user_stop_content = $pdo->prepare('SELECT * FROM user_stop_contents WHERE user_stop_id=:user_stop_id');

    $stmt_insert_stop_log = $pdo->prepare('INSERT INTO order_stop_logs(order_id,stop_start_date,stop_end_date,origin_end_date,change_end_date,stop_day_count,request_date,created_at) VALUES(:order_id,:stop_start_date,:stop_end_date,:origin_end_date,:change_end_date,:stop_day_count,:request_date,NOW())');
    $stmt_insert_order_stop_log_order_stop = $pdo->prepare('INSERT INTO order_stop_log_order_stops(order_stop_log_id,order_stop_id) VALUES(:order_stop_log_id,:order_stop_id)');
    $stmt_insert_order_stop_log_content = $pdo->prepare('INSERT INTO order_stop_log_contents(order_stop_log_id,content,created_at,updated_at) VALUES(:order_stop_log_id,:content,NOW(),NOW())');
    $stmt_is_pt = $pdo->prepare('SELECT count(*) FROM enrolls AS e INNER JOIN courses AS c  ON e.course_id=c.id WHERE e.id=:enroll_id AND c.lesson_type=4');

    $affect_orders = array();

    // 트랜잭션 시작
    $pdo->beginTransaction();

    foreach ($list as $order_stop) {
        $count_enroll = 0;
        $count_rent = 0;

        $stmt_count_enroll->bindParam(':id', $order_stop['order_id'], PDO::PARAM_INT);
        $stmt_count_enroll->execute();
        $count_enroll = $stmt_count_enroll->fetchColumn();

        $stmt_count_rent->bindParam(':id', $order_stop['order_id'], PDO::PARAM_INT);
        $stmt_count_rent->execute();
        $count_rent = $stmt_count_rent->fetchColumn();

        $stmt_count_rent_sw->bindParam(':id', $order_stop['order_id'], PDO::PARAM_INT);
        $stmt_count_rent_sw->execute();
        $count_rent_sw = $stmt_count_rent_sw->fetchColumn();

        if ($count_enroll or $count_rent or $count_rent_sw) {
            if ($count_enroll) {
                $stmt_select_enroll->bindParam(':id', $order_stop['order_id'], PDO::PARAM_INT);
                $stmt_select_enroll->execute();
                $enroll_content = $stmt_select_enroll->fetch(PDO::FETCH_ASSOC);

                $enroll_change_date_obj = new DateTime($enroll_content['end_date'], $dateTimeZone);
                $enroll_change_date_obj->modify('+'.$order_stop['stop_day_count'].' day');
                $enroll_change_end_date = $enroll_change_date_obj->format('Y-m-d');

                $origin_end_date = $enroll_content['end_date'];
                $change_end_date = $enroll_change_end_date;

                $stmt_update_enroll->bindParam(':end_date', $enroll_change_end_date, PDO::PARAM_STR);
                $stmt_update_enroll->bindParam(':order_id', $order_stop['order_id'], PDO::PARAM_INT);
                $stmt_update_enroll->execute();

                $stmt_is_pt->bindParam(':enroll_id', $enroll_content['id'], PDO::PARAM_INT);
                $stmt_is_pt->execute();
                $is_pt = $stmt_is_pt->fetchColumn();

                if (empty($is_pt)) {
                    $change_users[] = array('branch_id' => $enroll_content['branch_id'], 'user_id' => $enroll_content['user_id']);
                }

                if(!empty($order_stop['is_change_start_date'])) {
                    $enroll_change_date_obj = new DateTime($enroll_content['start_date'], $dateTimeZone);
                    $enroll_change_date_obj->modify('+'.$order_stop['stop_day_count'].' day');
                    $enroll_change_start_date = $enroll_change_date_obj->format('Y-m-d');

                    $stmt_update_start_enroll->bindParam(':start_date', $enroll_change_start_date, PDO::PARAM_STR);
                    $stmt_update_start_enroll->bindParam(':order_id', $order_stop['order_id'], PDO::PARAM_INT);
                    $stmt_update_start_enroll->execute();
                }
            }

            if ($count_rent) {
                $stmt_select_rent->bindParam(':id', $order_stop['order_id'], PDO::PARAM_INT);
                $stmt_select_rent->execute();
                $rent_content = $stmt_select_rent->fetch(PDO::FETCH_ASSOC);

                $rent_change_date_obj = new DateTime($rent_content['end_date'], $dateTimeZone);
                $rent_change_date_obj->modify('+'.$order_stop['stop_day_count'].' day');
                $rent_change_end_date = $rent_change_date_obj->format('Y-m-d');

                $origin_end_date = $rent_content['end_date'];
                $change_end_date = $rent_change_end_date;

                $stmt_update_rent->bindParam(':end_datetime', $rent_change_end_date, PDO::PARAM_STR);
                $stmt_update_rent->bindParam(':order_id', $order_stop['order_id'], PDO::PARAM_INT);
                $stmt_update_rent->execute();

                if(!empty($order_stop['is_change_start_date'])) {
                    $rent_change_date_obj = new DateTime($rent_content['start_date'], $dateTimeZone);
                    $rent_change_date_obj->modify('+'.$order_stop['stop_day_count'].' day');
                    $rent_change_start_date = $rent_change_date_obj->format('Y-m-d 00:00:01');

                    $stmt_update_start_rent->bindParam(':start_datetime', $rent_change_start_date, PDO::PARAM_STR);
                    $stmt_update_start_rent->bindParam(':order_id', $order_stop['order_id'], PDO::PARAM_INT);
                    $stmt_update_start_rent->execute();
                }                
            }

            if ($count_rent_sw) {
                $stmt_select_rent_sw->bindParam(':id', $order_stop['order_id'], PDO::PARAM_INT);
                $stmt_select_rent_sw->execute();
                $rent_sw_content = $stmt_select_rent_sw->fetch(PDO::FETCH_ASSOC);

                $rent_sw_change_date_obj = new DateTime($rent_sw_content['end_date'], $dateTimeZone);
                $rent_sw_change_date_obj->modify('+'.$order_stop['stop_day_count'].' day');
                $rent_sw_change_end_date = $rent_sw_change_date_obj->format('Y-m-d');

                $origin_end_date = $rent_sw_content['end_date'];
                $change_end_date = $rent_sw_change_end_date;

                $stmt_update_rent_sw->bindParam(':end_date', $rent_sw_change_end_date, PDO::PARAM_STR);
                $stmt_update_rent_sw->bindParam(':order_id', $order_stop['order_id'], PDO::PARAM_INT);
                $stmt_update_rent_sw->execute();

                if(!empty($order_stop['is_change_start_date'])) {
                    $rent_sw_change_date_obj = new DateTime($rent_sw_content['start_date'], $dateTimeZone);
                    $rent_sw_change_date_obj->modify('+'.$order_stop['stop_day_count'].' day');
                    $rent_sw_change_start_date = $rent_sw_change_date_obj->format('Y-m-d');

                    $stmt_update_start_rent_sw->bindParam(':start_date', $rent_sw_change_start_date, PDO::PARAM_STR);
                    $stmt_update_start_rent_sw->bindParam(':order_id', $order_stop['order_id'], PDO::PARAM_INT);
                    $stmt_update_start_rent_sw->execute();
                }
            }

            $stmt_update_order->bindParam(':id', $order_stop['order_id'], PDO::PARAM_INT);
            $stmt_update_order->execute();

            $stmt_insert_stop_log->bindParam(':order_id', $order_stop['order_id'], PDO::PARAM_INT);
            $stmt_insert_stop_log->bindParam(':stop_start_date', $order_stop['stop_start_date'], PDO::PARAM_STR);
            $stmt_insert_stop_log->bindParam(':stop_end_date', $order_stop['stop_end_date'], PDO::PARAM_STR);
            $stmt_insert_stop_log->bindParam(':origin_end_date', $origin_end_date, PDO::PARAM_STR);
            $stmt_insert_stop_log->bindParam(':change_end_date', $change_end_date, PDO::PARAM_STR);
            $stmt_insert_stop_log->bindParam(':stop_day_count', $order_stop['stop_day_count'], PDO::PARAM_INT);
            $stmt_insert_stop_log->bindParam(':request_date', $order_stop['request_date'], PDO::PARAM_STR);
            $stmt_insert_stop_log->execute();

            $order_stop_log_id = $pdo->lastInsertId();

            $stmt_insert_order_stop_log_order_stop->bindParam(':order_stop_log_id', $order_stop_log_id, PDO::PARAM_INT);
            $stmt_insert_order_stop_log_order_stop->bindParam(':order_stop_id', $order_stop['id'], PDO::PARAM_INT);
            $stmt_insert_order_stop_log_order_stop->execute();

            $stmt_count_user_stop_content->bindParam(':user_stop_id', $order_stop['user_stop_id'], PDO::PARAM_INT);
            $stmt_count_user_stop_content->execute();
            $count_user_stop_content = $stmt_count_user_stop_content->fetchColumn();

            if ($count_user_stop_content) {
                $stmt_select_user_stop_content->bindParam(':user_stop_id', $order_stop['user_stop_id'], PDO::PARAM_INT);
                $stmt_select_user_stop_content->execute();
                $user_stop_content = $stmt_select_user_stop_content->fetch(PDO::FETCH_ASSOC);

                $stmt_insert_order_stop_log_content->bindParam(':order_stop_log_id', $order_stop_log_id, PDO::PARAM_INT);
                $stmt_insert_order_stop_log_content->bindParam(':content', $user_stop_content['content'], PDO::PARAM_STR);
                $stmt_insert_order_stop_log_content->execute();
            }

            $resume_order[] = $order_stop['order_id'];
        }

        $stmt_update_order_stop->bindParam(':id', $order_stop['id'], PDO::PARAM_INT);
        $stmt_update_order_stop->execute();
    }

    $stmt_count_enroll->closeCursor();
    $stmt_count_rent->closeCursor();
    $stmt_update_enroll->closeCursor();
    $stmt_update_rent->closeCursor();
    $stmt_update_order->closeCursor();
    $stmt_update_order_stop->closeCursor();
    $stmt_insert_stop_log->closeCursor();
    $stmt_insert_order_stop_log_order_stop->closeCursor();

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

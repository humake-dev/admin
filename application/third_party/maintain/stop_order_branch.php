 <?php

/* 예약 자동중지를 위한 스크립트입니다 */

try {
    $log_file = basename(__FILE__, '.php') . '.log';
    include __DIR__ . DIRECTORY_SEPARATOR . 'common_head.php';

    if (empty($argv[1])) {
        echo 'branch_id need / example) php delete_ist.php branch_id' . "\n";

        return true;
    }

    $stop_start_date = '2021-02-02';
    $stop_end_date = '2021-02-08';

    $stmt_count = $pdo->prepare('SELECT count(*) FROM orders as o INNER JOIN enrolls as e ON e.order_id=o.id INNER JOIN courses as c ON e.course_id=c.id INNER JOIN users as u ON o.user_id=u.id LEFT JOIN order_ends AS oe ON oe.order_id=o.id WHERE c.lesson_type=1 AND (e.start_date<:stop_end_date AND e.end_date>:stop_start_date) AND o.branch_id=:branch_id  AND oe.id IS NULL AND o.enable=1 AND o.stopped=0 GROUP BY u.id');
    $stmt_count->bindParam(':stop_start_date', $stop_start_date, PDO::PARAM_STR);    
    $stmt_count->bindParam(':stop_end_date', $stop_end_date, PDO::PARAM_STR);
    $stmt_count->bindParam(':branch_id', $branch_id, PDO::PARAM_INT);
    $stmt_count->execute();
    $count = $stmt_count->fetchColumn();
    $stmt_count->closeCursor();

    if (empty($count)) {
        sl_log($log, 'Valid User Not Exists');

        return true;
    }

    $stmt_select = $pdo->prepare('SELECT o.*,e.start_date,e.end_date FROM orders as o INNER JOIN enrolls as e ON e.order_id=o.id INNER JOIN courses as c ON e.course_id=c.id INNER JOIN users as u ON o.user_id=u.id LEFT JOIN order_ends AS oe ON oe.order_id=o.id WHERE c.lesson_type=1 AND (e.start_date<:stop_end_date AND e.end_date>:stop_start_date) AND o.branch_id=:branch_id  AND oe.id IS NULL AND o.enable=1 AND o.stopped=0 GROUP BY u.id');
    $stmt_select->bindParam(':stop_start_date', $stop_start_date, PDO::PARAM_STR);    
    $stmt_select->bindParam(':stop_end_date', $stop_end_date, PDO::PARAM_STR);
    $stmt_select->bindParam(':branch_id', $branch_id, PDO::PARAM_INT);
    $stmt_select->execute();
    $list = $stmt_select->fetchAll(PDO::FETCH_ASSOC);
    $stmt_select->closeCursor();

    $stmt_insert_user_stop = $pdo->prepare('INSERT INTO user_stops(user_id,order_id,stop_start_date,stop_end_date,stop_day_count,request_date,created_at,updated_at) VALUES(:user_id,:order_id,:stop_start_date,:stop_end_date,:stop_day_count,:request_date,:created_at,:updated_at)');
    $stmt_insert = $pdo->prepare('INSERT INTO order_stops(user_stop_id,order_id,stop_start_date,stop_end_date,is_change_start_date,stop_day_count,created_at,updated_at) VALUES(:user_stop_id,:order_id,:stop_start_date,:stop_end_date,:is_change_start_date,:stop_day_count,NOW(),NOW())');
    $stmt_update = $pdo->prepare('UPDATE `orders` SET `stopped`=1 WHERE `id`=:id');
    $stmt_update_stop = $pdo->prepare('UPDATE `user_stops` SET `enable`=1 WHERE `id`=:id');

    $stmt_count_enrolls = $pdo->prepare('SELECT count(*) FROM orders AS o INNER JOIN enrolls AS e ON e.order_id=o.id INNER JOIN courses AS c  ON e.course_id=c.id  LEFT JOIN order_ends AS oe ON oe.order_id=o.id WHERE e.end_date>=:end_date AND o.enable=1 AND c.lesson_type=1 AND oe.id IS NULL AND o.user_id=:user_id');
    $stmt_select_enrolls = $pdo->prepare('SELECT e.*,o.branch_id,o.user_id FROM orders AS o INNER JOIN enrolls AS e ON e.order_id=o.id INNER JOIN courses AS c  ON e.course_id=c.id LEFT JOIN order_ends AS oe ON oe.order_id=o.id WHERE e.end_date>=:end_date AND o.enable=1 AND c.lesson_type=1  AND oe.id IS NULL AND o.user_id=:user_id ORDER BY e.start_date');
    $stmt_count_rents = $pdo->prepare('SELECT count(*) FROM orders AS o INNER JOIN rents AS r ON r.order_id=o.id LEFT JOIN order_ends AS oe ON oe.order_id=o.id WHERE DATE(r.end_datetime)>=:end_date AND o.enable=1 AND oe.id IS NULL AND o.user_id=:user_id');
    $stmt_select_rents = $pdo->prepare('SELECT r.*,DATE(r.start_datetime) as start_date,DATE(r.end_datetime) as end_date FROM orders AS o INNER JOIN rents AS r ON r.order_id=o.id LEFT JOIN order_ends AS oe ON oe.order_id=o.id WHERE DATE(r.end_datetime)>=:end_date AND o.enable=1 AND oe.id IS NULL AND o.user_id=:user_id ORDER BY r.start_datetime');
    $stmt_count_rent_sws = $pdo->prepare('SELECT count(*) FROM orders AS o INNER JOIN rent_sws AS rs ON rs.order_id=o.id LEFT JOIN order_ends AS oe ON oe.order_id=o.id WHERE rs.end_date>=:end_date AND o.enable=1 AND oe.id IS NULL AND o.user_id=:user_id');
    $stmt_select_rent_sws = $pdo->prepare('SELECT rs.* FROM orders AS o INNER JOIN rent_sws AS rs ON rs.order_id=o.id LEFT JOIN order_ends AS oe ON oe.order_id=o.id WHERE rs.end_date>=:end_date AND o.enable=1 AND oe.id IS NULL AND o.user_id=:user_id  ORDER BY rs.start_date');
    $stmt_is_pt = $pdo->prepare('SELECT count(*) FROM enrolls AS e INNER JOIN courses AS c  ON e.course_id=c.id WHERE e.id=:enroll_id AND c.lesson_type=4');

    
    $stmt_select_new_enrolls = $pdo->prepare('SELECT e.*,min(e.start_date) as start_date,max(e.end_date) as end_date,o.branch_id,o.user_id FROM orders AS o INNER JOIN enrolls AS e ON e.order_id=o.id INNER JOIN courses AS c  ON e.course_id=c.id LEFT JOIN order_ends AS oe ON oe.order_id=o.id WHERE e.end_date>=:end_date AND o.enable=1 AND c.lesson_type=1  AND oe.id IS NULL AND o.user_id=:user_id GROUP BY o.user_id');    

    $affect_orders = array();
    $change_users = array();

    // 트랜잭션 시작
    $pdo->beginTransaction();

    foreach ($list as $user_stop) {
        $data = calculator_stop_data(array('stop_start_date' => $stop_start_date, 'stop_end_date' => $stop_end_date), $user_stop, $dateTimeZone);

        $created_at = $stop_start_date . ' 00:00:01';
        $stmt_insert_user_stop->bindParam(':user_id', $user_stop['user_id'], PDO::PARAM_INT);
        $stmt_insert_user_stop->bindParam(':order_id', $user_stop['id'], PDO::PARAM_INT);
        $stmt_insert_user_stop->bindParam(':stop_start_date', $stop_start_date, PDO::PARAM_STR);
        $stmt_insert_user_stop->bindParam(':stop_end_date', $stop_end_date, PDO::PARAM_STR);
        $stmt_insert_user_stop->bindParam(':stop_day_count', $data['stop_day_count'], PDO::PARAM_INT);
        $stmt_insert_user_stop->bindParam(':request_date', $stop_start_date, PDO::PARAM_STR);
        $stmt_insert_user_stop->bindParam(':created_at', $created_at, PDO::PARAM_STR);
        $stmt_insert_user_stop->bindParam(':updated_at', $created_at, PDO::PARAM_STR);
        $stmt_insert_user_stop->execute();

        $user_stop_id = $pdo->lastInsertId();

        $stmt_count_enrolls->bindParam(':end_date', $stop_start_date, PDO::PARAM_STR);
        $stmt_count_enrolls->bindParam(':user_id', $user_stop['user_id'], PDO::PARAM_INT);
        $stmt_count_enrolls->execute();
        $count_enroll = $stmt_count_enrolls->fetchColumn();

        if ($count_enroll) {
            $stmt_select_enrolls->bindParam(':end_date', $stop_start_date, PDO::PARAM_STR);
            $stmt_select_enrolls->bindParam(':user_id', $user_stop['user_id'], PDO::PARAM_INT);
            $stmt_select_enrolls->execute();
            $enroll_list = $stmt_select_enrolls->fetchAll(PDO::FETCH_ASSOC);

            foreach ($enroll_list as $index=>$enroll) {
                $stmt_is_pt->bindParam(':enroll_id', $enroll['id'], PDO::PARAM_INT);
                $stmt_is_pt->execute();
                $is_pt = $stmt_is_pt->fetchColumn();

                if (!empty($is_pt)) {
                    continue;
                }

                $data = calculator_stop_data(array('stop_start_date' => $stop_start_date, 'stop_end_date' => $stop_end_date), $enroll , $dateTimeZone);

                if($count_enroll>1) {
                    if(!empty($index)) {
                        if($data['is_change_start_date']) {
                            echo $enroll['order_id'].': '.$prev_enroll['end_date'].','.$enroll['start_date']."\n";

                            $prev_end_date_obj=new DateTime($prev_enroll['end_date'], $dateTimeZone);
                            $current_start_date_obj=new DateTime($enroll['start_date'], $dateTimeZone);
    
                            $prev_end_date_obj->modify('+1 day');

                            $prev_end_date=$prev_end_date_obj->format('Y-m-d');
                            $current_start_date=$current_start_date_obj->format('Y-m-d');

                            if($prev_end_date==$current_start_date) {
                                $data['stop_day_count']=$prev_data['stop_day_count'];
                                echo $enroll['order_id'].' change'."\n";
                            }
                        }
                    }

                    $prev_enroll=$enroll;
                    $prev_data=$data;
                }

                $stmt_insert->bindParam(':user_stop_id', $user_stop_id, PDO::PARAM_INT);
                $stmt_insert->bindParam(':order_id', $enroll['order_id'], PDO::PARAM_INT);
                $stmt_insert->bindParam(':stop_start_date', $stop_start_date, PDO::PARAM_STR);
                $stmt_insert->bindParam(':stop_end_date', $stop_end_date, PDO::PARAM_STR);
                $stmt_insert->bindParam(':is_change_start_date', $data['is_change_start_date'], PDO::PARAM_INT);
                $stmt_insert->bindParam(':stop_day_count', $data['stop_day_count'], PDO::PARAM_INT);
                $stmt_insert->execute();
                $order_stop_id = $pdo->lastInsertId();

                $stmt_update->bindParam(':id', $enroll['order_id'], PDO::PARAM_INT);
                $stmt_update->execute();

                $affect_orders[] = $enroll['order_id'];
            }
        }

        $stmt_count_rents->bindParam(':end_date', $stop_start_date, PDO::PARAM_STR);
        $stmt_count_rents->bindParam(':user_id', $user_stop['user_id'], PDO::PARAM_INT);
        $stmt_count_rents->execute();
        $count_rent = $stmt_count_rents->fetchColumn();

        if ($count_rent) {
            $stmt_select_rents->bindParam(':end_date', $stop_start_date, PDO::PARAM_STR);
            $stmt_select_rents->bindParam(':user_id', $user_stop['user_id'], PDO::PARAM_INT);
            $stmt_select_rents->execute();
            $rent_list = $stmt_select_rents->fetchAll(PDO::FETCH_ASSOC);
            
            foreach ($rent_list as $rent) {
                $data = calculator_stop_data(array('stop_start_date' => $stop_start_date, 'stop_end_date' => $stop_end_date), $rent, $dateTimeZone);

                if($count_rent>1) {
                    if(!empty($index)) {
                        if($data['is_change_start_date']) {
                            echo $rent['order_id'].': '.$prev_rent['end_datetime'].','.$rent['start_datetime']."\n";

                            $prev_end_date_obj=new DateTime($prev_rent['end_datetime'], $dateTimeZone);
                            $current_start_date_obj=new DateTime($rent['start_datetime'], $dateTimeZone);
    
                            $prev_end_date_obj->modify('+1 day');

                            $prev_end_date=$prev_end_date_obj->format('Y-m-d');
                            $current_start_date=$current_start_date_obj->format('Y-m-d');

                            if($prev_end_date==$current_start_date) {
                                $data['stop_day_count']=$prev_data['stop_day_count'];
                                echo $rent['order_id'].' change'."\n";
                            }
                        }
                    }

                    $prev_rent=$rent;
                    $prev_data=$data;
                }                

                $stmt_insert->bindParam(':user_stop_id', $user_stop_id, PDO::PARAM_INT);
                $stmt_insert->bindParam(':order_id', $rent['order_id'], PDO::PARAM_INT);
                $stmt_insert->bindParam(':stop_start_date', $stop_start_date, PDO::PARAM_STR);
                $stmt_insert->bindParam(':stop_end_date', $stop_end_date, PDO::PARAM_STR);
                $stmt_insert->bindParam(':is_change_start_date', $data['is_change_start_date'], PDO::PARAM_INT);
                $stmt_insert->bindParam(':stop_day_count', $data['stop_day_count'], PDO::PARAM_INT);
                $stmt_insert->execute();
                $order_stop_id = $pdo->lastInsertId();

                $stmt_update->bindParam(':id', $rent['order_id'], PDO::PARAM_INT);
                $stmt_update->execute();

                $affect_orders[] = $rent['order_id'];
            }
        }

        $stmt_count_rent_sws->bindParam(':end_date', $stop_start_date, PDO::PARAM_STR);
        $stmt_count_rent_sws->bindParam(':user_id', $user_stop['user_id'], PDO::PARAM_INT);
        $stmt_count_rent_sws->execute();
        $count_rent_sw = $stmt_count_rent_sws->fetchColumn();

        if ($count_rent_sw) {
            $stmt_select_rent_sws->bindParam(':end_date', $stop_start_date, PDO::PARAM_STR);
            $stmt_select_rent_sws->bindParam(':user_id', $user_stop['user_id'], PDO::PARAM_INT);
            $stmt_select_rent_sws->execute();
            $rent_sws_list = $stmt_select_rent_sws->fetchAll(PDO::FETCH_ASSOC);

            foreach ($rent_sws_list as $rent_sw) {
                $data = calculator_stop_data(array('stop_start_date' => $stop_start_date, 'stop_end_date' => $stop_end_date), $rent_sw, $dateTimeZone);

                if($count_rent_sw>1) {
                    if(!empty($index)) {
                        if($data['is_change_start_date']) {
                            echo $rent['order_id'].': '.$prev_rent['end_date'].','.$rent['start_date']."\n";

                            $prev_end_date_obj=new DateTime($prev_rent['end_date'], $dateTimeZone);
                            $current_start_date_obj=new DateTime($rent_sw['start_date'], $dateTimeZone);
    
                            $prev_end_date_obj->modify('+1 day');

                            $prev_end_date=$prev_end_date_obj->format('Y-m-d');
                            $current_start_date=$current_start_date_obj->format('Y-m-d');

                            if($prev_end_date==$current_start_date) {
                                $data['stop_day_count']=$prev_data['stop_day_count'];
                                echo $rent_sw['order_id'].' change'."\n";
                            }
                        }
                    }

                    $prev_rent=$rent_sw;
                    $prev_data=$data;
                }  

                $stmt_insert->bindParam(':user_stop_id', $user_stop_id, PDO::PARAM_INT);
                $stmt_insert->bindParam(':order_id', $rent_sw['order_id'], PDO::PARAM_INT);
                $stmt_insert->bindParam(':stop_start_date', $stop_start_date, PDO::PARAM_STR);
                $stmt_insert->bindParam(':stop_end_date', $stop_end_date, PDO::PARAM_STR);
                $stmt_insert->bindParam(':is_change_start_date', $data['is_change_start_date'], PDO::PARAM_INT);
                $stmt_insert->bindParam(':stop_day_count', $data['stop_day_count'], PDO::PARAM_INT);
                $stmt_insert->execute();
                $order_stop_id = $pdo->lastInsertId();

                $stmt_update->bindParam(':id', $rent_sw['order_id'], PDO::PARAM_INT);
                $stmt_update->execute();

                $affect_orders[] = $rent_sw['order_id'];
            }
        }

        $stmt_update_stop->bindParam(':id', $user_stop['id'], PDO::PARAM_INT);
        $stmt_update_stop->execute();
    }
    $stmt_update->closeCursor();
    $stmt_update_stop->closeCursor();

    // 커밋
    $pdo->commit();
    $pdo = null;
} catch (Exception $e) {
    include __DIR__ . DIRECTORY_SEPARATOR . 'common_catch.php';
}

function calculator_stop_data(array $data, array $content, $timezone)
{
    $stop_start_date_obj = new DateTime($data['stop_start_date'], $timezone);
    $stop_end_date_obj = new DateTime($data['stop_end_date'], $timezone);

    $start_date_obj = new DateTime($content['start_date'], $timezone);
    $end_date_obj = new DateTime($content['end_date'], $timezone);

    $stop_day_count = 0;
    $is_change_start_date = false;

    if (empty($data['stop_end_date'])) {
        $data['stop_end_date'] = null;
    } else {
        $interval_day_count = $stop_start_date_obj->diff($stop_end_date_obj);
        $stop_day_count = intval($interval_day_count->format('%a'))+1;

        // 휴회기간이 시작일 이후면
        if ($start_date_obj < $stop_start_date_obj) {
            if($end_date_obj > $stop_start_date_obj) {
                // 휴회기간이 휴회신청기간
                $plus_day_count = $stop_day_count;
            } else {
                $plus_day_count=0;
            }
        } else {
            if ($start_date_obj < $stop_end_date_obj) {
                $interval_day_count = $start_date_obj->diff($stop_end_date_obj);
                $plus_day_count = intval($interval_day_count->format('%a'))+1;
            } else {
                // 휴회기간이 휴회신청기간
                $plus_day_count = $stop_day_count;
            }
        }

        // 휴회기간을 넘지 않게
        if($plus_day_count>$stop_day_count) {
            $plus_day_count=$stop_day_count;
        }

        if ($stop_start_date_obj < $start_date_obj) {
            $is_change_start_date = true;
        }

        $end_date_obj->modify('+' . $plus_day_count . ' Day');
        $change_end_date = $end_date_obj->format('Y-m-d');
    }

    $data['stop_day_count'] = $plus_day_count;
    $data['change_end_date'] = $change_end_date;

    if (!empty($is_change_start_date)) {
        $start_date_obj->modify('+' . $plus_day_count . ' Day');
        $data['change_start_date'] = $start_date_obj->format('Y-m-d');
    }

    $data['is_change_start_date'] = $is_change_start_date;

    return $data;
}

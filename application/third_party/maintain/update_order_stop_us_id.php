<?php

try {
    $log_file = basename(__FILE__, '.php').'.log';
    include __DIR__.DIRECTORY_SEPARATOR.'common_head.php';

    $stmt_select = $pdo->prepare('SELECT os.*,o.user_id FROM order_stops as os INNER JOIN orders as o ON os.order_id=o.id');
    $stmt_select->execute();
    $order_stops = $stmt_select->fetchAll(PDO::FETCH_ASSOC);
    $stmt_select->closeCursor();

    
    $stmt_count_us = $pdo->prepare('SELECT count(*) FROM user_stops WHERE user_id=:user_id AND stop_start_date=:stop_start_date AND stop_end_date=:stop_end_date');
    $stmt_select_us = $pdo->prepare('SELECT * FROM user_stops WHERE user_id=:user_id AND stop_start_date=:stop_start_date AND stop_end_date=:stop_end_date');
    $stmt_update = $pdo->prepare('UPDATE order_stops SET user_stop_id=:user_stop_id WHERE id=:id');


    $smtm_select_duplication = $pdo->prepare('SELECT count(*) FROM user_stops WHERE user_id=:user_id AND stop_start_date=:stop_start_date AND stop_end_date=:stop_end_date');

    $stmt_insert = $pdo->prepare('INSERT INTO user_stops(user_id,stop_start_date,stop_end_date,stop_day_count,request_date,enable,created_at,updated_at) VALUE(:user_id,:stop_start_date,:stop_end_date,:stop_day_count,:request_date,:enable,:created_at,:updated_at)');

    $stmt_select_count_schedule = $pdo->prepare('SELECT count(*) FROM order_stop_schedules WHERE order_stop_id=:order_stop_id AND `execute`=0');
    $stmt_select_schedule = $pdo->prepare('SELECT * FROM order_stop_schedules WHERE order_stop_id=:order_stop_id AND `execute`=0');
    $stmt_insert_schedule = $pdo->prepare('INSERT INTO user_stop_schedules(user_stop_id,schedule_date) VALUE(:user_stop_id,:schedule_date)');

    $stmt_select_count_content = $pdo->prepare('SELECT count(*) FROM order_stop_contents WHERE order_stop_id=:order_stop_id');
    $stmt_select_content = $pdo->prepare('SELECT * FROM order_stop_contents WHERE order_stop_id=:order_stop_id');
    $stmt_insert_content = $pdo->prepare('INSERT INTO user_stop_contents(user_stop_id,content,created_at,updated_at) VALUE(:user_stop_id,:content,:created_at,:updated_at)');

    $smtm_delete = $pdo->prepare('DELETE FROM order_stops WHERE id=:id');
    
    // 트랜잭션 시작
    $pdo->beginTransaction();

    foreach ($order_stops as $order_stop) {
        $stmt_count_us->bindParam(':user_id', $order_stop['user_id'], PDO::PARAM_INT);
        $stmt_count_us->bindParam(':stop_start_date', $order_stop['stop_start_date'], PDO::PARAM_STR);
        $stmt_count_us->bindParam(':stop_end_date', $order_stop['stop_end_date'], PDO::PARAM_STR);          
        $stmt_count_us->execute();
        $us_count = $stmt_count_us->fetchColumn();

        if (empty($us_count)) {
            if(empty($order_stop['user_id'])) {
                $smtm_delete->bindParam(':id', $order_stop['id'], PDO::PARAM_INT);
                $smtm_delete->execute();
                continue;
            }
    
            $stmt_insert->bindParam(':user_id', $order_stop['user_id'], PDO::PARAM_INT);
            $stmt_insert->bindParam(':stop_start_date', $order_stop['stop_start_date'], PDO::PARAM_STR);
            $stmt_insert->bindParam(':stop_end_date', $order_stop['stop_end_date'], PDO::PARAM_STR);
            $stmt_insert->bindParam(':stop_day_count', $order_stop['stop_day_count'], PDO::PARAM_INT);
            $stmt_insert->bindParam(':request_date', $order_stop['created_at'], PDO::PARAM_STR);
            $stmt_insert->bindParam(':enable', $order_stop['enable'], PDO::PARAM_INT);            
            $stmt_insert->bindParam(':created_at', $order_stop['created_at'], PDO::PARAM_STR);
            $stmt_insert->bindParam(':updated_at', $order_stop['updated_at'], PDO::PARAM_STR);
            $stmt_insert->execute();
    
            $user_stop_id = $pdo->lastInsertId();
    
            /*$stmt_select_count_schedule->bindParam(':order_stop_id', $order_stop['id'], PDO::PARAM_INT);
            $stmt_select_count_schedule->execute();
            $schedule_count = $stmt_select_count_schedule->fetchColumn();
    
            if ($schedule_count) {
                $stmt_select_schedule->bindParam(':order_stop_id', $order_stop['id'], PDO::PARAM_INT);
                $stmt_select_schedule->execute();
                $order_stop_schedule = $stmt_select_schedule->fetch(PDO::FETCH_ASSOC);
    
                print_r($order_stop_schedule);
    
                $stmt_insert_schedule->bindParam(':user_stop_id', $user_stop_id, PDO::PARAM_INT);
                $stmt_insert_schedule->bindParam(':schedule_date', $order_stop_schedule['schedule_date'], PDO::PARAM_STR);
                $stmt_insert_schedule->execute();
            } */
    
            $stmt_select_count_content->bindParam(':order_stop_id', $order_stop['id'], PDO::PARAM_INT);
            $stmt_select_count_content->execute();
            $content_count = $stmt_select_count_content->fetchColumn();
    
            if ($content_count) {
                $stmt_select_content->bindParam(':order_stop_id', $order_stop['id'], PDO::PARAM_INT);
                $stmt_select_content->execute();
                $order_stop_content = $stmt_select_content->fetch(PDO::FETCH_ASSOC);
    
                $stmt_insert_content->bindParam(':user_stop_id', $user_stop_id, PDO::PARAM_INT);
                $stmt_insert_content->bindParam(':content', $order_stop_content['content'], PDO::PARAM_STR);
                $stmt_insert_content->bindParam(':created_at', $order_stop_content['created_at'], PDO::PARAM_STR);
                $stmt_insert_content->bindParam(':updated_at', $order_stop_content['updated_at'], PDO::PARAM_STR);
                $stmt_insert_content->execute();
            }
        } else {
            $stmt_select_us->bindParam(':user_id', $order_stop['user_id'], PDO::PARAM_INT);
            $stmt_select_us->bindParam(':stop_start_date', $order_stop['stop_start_date'], PDO::PARAM_STR);
            $stmt_select_us->bindParam(':stop_end_date', $order_stop['stop_end_date'], PDO::PARAM_STR);                
            $stmt_select_us->execute();
            $us_content = $stmt_select_us->fetch(PDO::FETCH_ASSOC);
            $user_stop_id=$us_content['id'];
        }


        $stmt_update->bindParam(':user_stop_id', $user_stop_id, PDO::PARAM_INT);
        $stmt_update->bindParam(':id', $order_stop['id'], PDO::PARAM_INT);
        $stmt_update->execute();
    }

    // 커밋
    $pdo->commit();
    $pdo = null;

    echo 'success'."\n";
} catch (Exception $e) {
    include __DIR__.DIRECTORY_SEPARATOR.'common_catch.php';
}

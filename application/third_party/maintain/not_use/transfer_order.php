<?php

/* 예약 수강양도 처리를 위한 스크립트입니다 */

try {
    $log_file = basename(__FILE__, '.php').'.log';
    include __DIR__.DIRECTORY_SEPARATOR.'common_head.php';

    $stmt_count = $pdo->prepare('SELECT count(*) FROM order_transfer_schedules AS ots INNER JOIN order_transfers AS ot ON ots.order_transfer_id=ot.id INNER JOIN users AS u ON ot.recipient_id=u.id INNER JOIN orders AS o ON ot.order_id=o.id WHERE ots.execute=0 AND ots.schedule_date<=CURDATE()');
    $stmt_count->execute();
    $count = $stmt_count->fetchColumn();
    $stmt_count->closeCursor();

    if (empty($count)) {
        sl_log($log, 'Already Sync');

        return true;
    }

    $stmt_select = $pdo->prepare('SELECT ot.order_id,ot.recipient_id,ot.giver_id,ots.*,u.branch_id,aot.account_id FROM order_transfer_schedules AS ots INNER JOIN order_transfers AS ot ON ots.order_transfer_id=ot.id INNER JOIN users AS u ON ot.recipient_id=u.id INNER JOIN orders AS o ON ot.order_id=o.id LEFT JOIN account_order_transfers AS aot ON aot.order_transfer_id=ot.id WHERE ots.execute=0 AND ots.schedule_date<=CURDATE()');
    $stmt_select->execute();
    $list = $stmt_select->fetchAll(PDO::FETCH_ASSOC);
    $stmt_select->closeCursor();


    $stmt_check_exist_enroll_count = $pdo->prepare('SELECT count(*) FROM enrolls WHERE order_id=:order_id');
    $stmt_check_exist_rent_count = $pdo->prepare('SELECT count(*) FROM rents WHERE order_id=:order_id');
    $stmt_check_exist_rent_sws_count = $pdo->prepare('SELECT count(*) FROM rent_sws WHERE order_id=:order_id');

    $stmt_update_execute = $pdo->prepare('UPDATE orders SET branch_id=:branch_id, user_id=:user_id WHERE id=:id');
    $stmt_update_transfer = $pdo->prepare('UPDATE order_transfers SET enable=1 WHERE id=:id');
    $stmt_update_transfer_schedule = $pdo->prepare('UPDATE order_transfer_schedules SET `execute`=1 WHERE id=:id');
    $stmt_update_account = $pdo->prepare('UPDATE accounts SET enable=1 WHERE id=:id');
    $stmt_update_enroll= $pdo->prepare('UPDATE enrolls as e INNER JOIN orders as o ON e.order_id=o.id SET e.start_date=:start_date, e.have_datetime=:have_datetime WHERE o.id=:id');
    $stmt_update_rent= $pdo->prepare('UPDATE rents as r INNER JOIN orders as o ON r.order_id=o.id SET r.start_date=:start_date WHERE o.id=:id');
    $stmt_update_rent_sw= $pdo->prepare('UPDATE rent_sws as rs INNER JOIN orders as o ON rs.order_id=o.id SET rs.start_date=:start_date WHERE o.id=:id');
    $affect_orders = array();

    // 트랜잭션 시작
    $pdo->beginTransaction();

    foreach ($list as $schedule) {
        $type=null;

        $stmt_check_exist_enroll_count->bindParam(':order_id', $schedule['order_id'], PDO::PARAM_INT);
        $stmt_check_exist_enroll_count->execute();
        $enroll_count = $stmt_check_exist_enroll_count->fetchColumn();

        if(!empty($enroll_count)) {
            $type='enroll';
        }
        
        if (empty($enroll_count)) {
            $stmt_check_exist_rent_count->bindParam(':order_id', $schedule['order_id'], PDO::PARAM_INT);
            $stmt_check_exist_rent_count->execute();
            $rent_count = $stmt_check_exist_rent_count->fetchColumn();
            
            if (!empty($rent_count)) {
                $type='rent';
            }
        }
        
        if (empty($enroll_count) and empty($rent_count)) {
            $stmt_check_exist_rent_sws_count->bindParam(':order_id', $schedule['order_id'], PDO::PARAM_INT);
            $stmt_check_exist_rent_sws_count->execute();
            $rent_sw_count = $stmt_check_exist_rent_sws_count->fetchColumn();
            
            if (!empty($rent_sw_count)) {
                $type='rent_sw';
            }
        }

        if(empty($type)) {
            continue;
        }

        $stmt_update_execute->bindParam(':branch_id', $schedule['branch_id'], PDO::PARAM_INT);
        $stmt_update_execute->bindParam(':user_id', $schedule['recipient_id'], PDO::PARAM_INT);
        $stmt_update_execute->bindParam(':id', $schedule['order_id'], PDO::PARAM_INT);
        $stmt_update_execute->execute();

        $stmt_update_transfer_schedule->bindParam(':id', $schedule['id'], PDO::PARAM_INT);
        $stmt_update_transfer_schedule->execute();

        $stmt_update_transfer->bindParam(':id', $schedule['order_transfer_id'], PDO::PARAM_INT);
        $stmt_update_transfer->execute();

        $stmt_update_account->bindParam(':id', $schedule['account_id'], PDO::PARAM_INT);
        $stmt_update_account->execute();

        $stmt_check_exist_enroll_count->execute();
        $enroll_count = $stmt_check_exist_enroll_count->fetchColumn();

        switch($type) {
            case 'rent':
                $stmt_update_rent->bindParam(':start_date', $schedule['start_date'], PDO::PARAM_STR);
                $stmt_update_rent->bindParam(':id', $schedule['order_id'], PDO::PARAM_INT);
                $stmt_update_rent->execute();
                break;
            case 'rent_sw':
                $stmt_update_rent_sw->bindParam(':start_date', $schedule['start_date'], PDO::PARAM_STR);
                $stmt_update_rent_sw->bindParam(':id', $schedule['order_id'], PDO::PARAM_INT);
                $stmt_update_rent_sw->execute();
                break;
            default : 
                $stmt_update_enroll->bindParam(':start_date', $schedule['start_date'], PDO::PARAM_STR);
                $stmt_update_enroll->bindParam(':have_datetime', $schedule['have_datetime'], PDO::PARAM_STR);
                $stmt_update_enroll->bindParam(':id', $schedule['order_id'], PDO::PARAM_INT);
                $stmt_update_enroll->execute();
        }

        $affect_orders[] = $schedule['order_id'];
    }
    $stmt_check_exist_enroll_count->closeCursor();
    $stmt_check_exist_rent_count->closeCursor();
    $stmt_check_exist_rent_sws_count->closeCursor();

    $stmt_update_execute->closeCursor();
    $stmt_update_transfer_schedule->closeCursor();
    $stmt_update_transfer->closeCursor();
    $stmt_update_enroll->closeCursor();
    $stmt_update_account->closeCursor();

    // 커밋
    $pdo->commit();
    $pdo = null;

    $affect_count = count($affect_orders);
    sl_log($log, 'count:'.$affect_count);

    if ($affect_count) {
        foreach ($affect_orders as $order_id) {
            sl_log($log, 'update_order :'.$order_id);
        }
    }
} catch (Exception $e) {
    include __DIR__.DIRECTORY_SEPARATOR.'common_catch.php';
}

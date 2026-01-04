<?php

/*  stop_day_count 다시 맞추는 스크립트입니다 */

try {
    $log_file = basename(__FILE__, '.php').'.log';
    include __DIR__.DIRECTORY_SEPARATOR.'common_head.php';

    $stmt_select = $pdo->prepare('SELECT * FROM user_stops as us');
    $stmt_select->execute();
    $lists = $stmt_select->fetchAll(PDO::FETCH_ASSOC);

    $stmt_count = $pdo->prepare('SELECT count(*) FROM orders as o INNER JOIN enrolls as e ON e.order_id=o.id INNER JOIN courses as c ON e.course_id=c.id WHERE o.user_id=:user_id AND c.lesson_type=1 AND o.enable=1 AND o.transaction_date<=:stop_start_date1 AND e.end_date>=:stop_start_date2');
    $stmt_select_order = $pdo->prepare('SELECT o.id FROM orders as o INNER JOIN enrolls as e ON e.order_id=o.id INNER JOIN courses as c ON e.course_id=c.id WHERE o.user_id=:user_id AND c.lesson_type=1 AND o.enable=1 AND o.transaction_date<=:stop_start_date1 AND e.end_date>=:stop_start_date2');

    $stmt_update = $pdo->prepare('UPDATE user_stops SET order_id=:order_id WHERE id=:id');

    // 트랜잭션 시작
    $pdo->beginTransaction();
    $update_count = 0;

    foreach ($lists as $value) {
        if (!empty($value['order_id'])) {
            continue;
        }

        $stmt_count->bindParam(':user_id', $value['user_id'], PDO::PARAM_INT);
        $stmt_count->bindParam(':stop_start_date1', $value['stop_start_date'], PDO::PARAM_STR);
        $stmt_count->bindParam(':stop_start_date2', $value['stop_start_date'], PDO::PARAM_STR);
        $stmt_count->execute();
        $count = $stmt_count->fetchColumn();

        if (empty($count)) {
            continue;
        }

        $stmt_select_order->bindParam(':user_id', $value['user_id'], PDO::PARAM_INT);
        $stmt_select_order->bindParam(':stop_start_date1', $value['stop_start_date'], PDO::PARAM_STR);
        $stmt_select_order->bindParam(':stop_start_date2', $value['stop_start_date'], PDO::PARAM_STR);
        $stmt_select_order->execute();
        $order = $stmt_select_order->fetchAll(PDO::FETCH_ASSOC);

        if ($count == 1) {
            $stmt_update->bindParam(':order_id', $order[0]['id'], PDO::PARAM_INT);
            $stmt_update->bindParam(':id', $value['id'], PDO::PARAM_INT);
            $stmt_update->execute();
            sl_log($log, 'user_stop_id :'.$value['id'].' updated');
        } else {
            $stmt_update->bindParam(':order_id', $order[0]['id'], PDO::PARAM_INT);
            $stmt_update->bindParam(':id', $value['id'], PDO::PARAM_INT);
            $stmt_update->execute();
            sl_log($log, 'user_stop_id :'.$value['id'].' updated');
        }
    }

    $stmt_count->closeCursor();
    $stmt_update->closeCursor();

    echo $update_count."\n";

    // 커밋
    $pdo->commit();
    $pdo = null;

    echo 'success'."\n";
} catch (Exception $e) {
    include __DIR__.DIRECTORY_SEPARATOR.'common_catch.php';
}

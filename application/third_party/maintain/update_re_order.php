<?php

/*  재수강 다시 맞추는 스크립트입니다 */

try {
    $log_file = basename(__FILE__, '.php').'.log';
    include __DIR__.DIRECTORY_SEPARATOR.'common_head.php';

    $stmt_count = $pdo->prepare('SELECT count(*) FROM (SELECT count(*) FROM orders as o INNER JOIN order_products as op ON op.order_id=o.id WHERE o.branch_id=:branch_id AND o.enable=1 AND o.re_order=0 GROUP BY o.id) as e');
    $stmt_count->bindParam(':branch_id', $branch_id, PDO::PARAM_INT);
    $stmt_count->execute();
    $count = $stmt_count->fetchColumn();
    $stmt_count->closeCursor();

    if (empty($count)) {
        sl_log($log, 'Already Sync');

        return true;
    }

    $stmt_select = $pdo->prepare('SELECT o.*,op.product_id FROM orders as o INNER JOIN order_products as op ON op.order_id=o.id WHERE o.branch_id=:branch_id AND o.enable=1 AND o.re_order=0 GROUP BY o.id');
    $stmt_select->bindParam(':branch_id', $branch_id, PDO::PARAM_INT);
    $stmt_select->execute();
    $lists = $stmt_select->fetchAll(PDO::FETCH_ASSOC);
    $stmt_select->closeCursor();

    $stmt_pre_exists_count = $pdo->prepare('SELECT count(*) FROM orders as o INNER JOIN order_products as op ON op.order_id=o.id LEFT JOIN order_transfers as ot ON ot.order_id=o.id WHERE o.branch_id=:branch_id AND o.id!=:order_id AND (o.user_id=:user_id1 OR ot.giver_id=:user_id2) AND op.product_id=:product_id AND o.enable=1 AND o.created_at<=:created_at');

    $stmt_update = $pdo->prepare('UPDATE orders SET re_order=1 WHERE id=:id');

    // 트랜잭션 시작
    $pdo->beginTransaction();
    $update_count = 0;

    foreach ($lists as $value) {
        $stmt_pre_exists_count->bindParam(':branch_id', $branch_id, PDO::PARAM_INT);
        $stmt_pre_exists_count->bindParam(':order_id', $value['id'], PDO::PARAM_INT);
        $stmt_pre_exists_count->bindParam(':user_id1', $value['user_id'], PDO::PARAM_INT);
        $stmt_pre_exists_count->bindParam(':user_id2', $value['user_id'], PDO::PARAM_INT);
        $stmt_pre_exists_count->bindParam(':product_id', $value['product_id'], PDO::PARAM_INT);
        $stmt_pre_exists_count->bindParam(':created_at', $value['created_at'], PDO::PARAM_STR);
        $stmt_pre_exists_count->execute();
        $pre_count = $stmt_pre_exists_count->fetchColumn();

        if (empty($pre_count)) {
            echo 'order_id : '.$value['id'].'not exists '.$pre_count."\n";
            continue;
        }

        $stmt_update->bindParam(':id', $value['id'], PDO::PARAM_INT);
        $stmt_update->execute();
        ++$update_count;

        sl_log($log, 'order_id :'.$value['id'].' updated');
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

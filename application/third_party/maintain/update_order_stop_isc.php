<?php

/*  is_change_start_date 다시 맞추는 스크립트입니다 */

try {
    $log_file = basename(__FILE__, '.php').'.log';
    include __DIR__.DIRECTORY_SEPARATOR.'common_head.php';

    $stmt_count = $pdo->prepare('SELECT count(*) FROM order_stops AS os INNER JOIN orders AS o ON os.order_id=o.id WHERE o.branch_id=:branch_id');
    $stmt_count->bindParam(':branch_id', $branch_id, PDO::PARAM_INT);
    $stmt_count->execute();
    $count = $stmt_count->fetchColumn();
    $stmt_count->closeCursor();

    if (empty($count)) {
        sl_log($log, 'Already Sync');

        return true;
    }

    $stmt_select = $pdo->prepare('SELECT os.* FROM order_stops AS os INNER JOIN orders AS o ON os.order_id=o.id WHERE o.branch_id=:branch_id');
    $stmt_select->bindParam(':branch_id', $branch_id, PDO::PARAM_INT);
    $stmt_select->execute();
    $lists = $stmt_select->fetchAll(PDO::FETCH_ASSOC);
    $stmt_select->closeCursor();

    $stmt_update = $pdo->prepare('UPDATE order_stops SET stop_day_count=:stop_day_count WHERE id=:id');

    // 트랜잭션 시작
    $pdo->beginTransaction();
    $update_count = 0;

    foreach ($lists as $value) {
        if (empty($value['stop_end_date'])) {
            $stop_day_count = 0;
        } else {
            $ssd_obj = new DateTime($value['stop_start_date']);
            $sed_obj = new DateTime($value['stop_end_date']);

            $interval_day_count = $ssd_obj->diff($sed_obj);
            $stop_day_count = intval($interval_day_count->format('%a')) + 1;
        }

        if ($value['stop_day_count'] == $stop_day_count) {
            continue;
        }

        $stmt_update->bindParam(':stop_day_count', $stop_day_count, PDO::PARAM_INT);
        $stmt_update->bindParam(':id', $value['id'], PDO::PARAM_INT);
        $stmt_update->execute();
        ++$update_count;

        sl_log($log, 'order_stop_id :'.$value['id'].' updated '.$value['id'].' to '.$stop_day_count);
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

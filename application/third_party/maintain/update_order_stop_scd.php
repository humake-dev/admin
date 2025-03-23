<?php

/*  stop_day_count 다시 맞추는 스크립트입니다 */

try {
    $log_file = basename(__FILE__, '.php').'.log';
    include __DIR__.DIRECTORY_SEPARATOR.'common_head.php';

    $stmt_count = $pdo->prepare('SELECT count(*) FROM orders as o INNER JOIN order_stops as os ON os.order_id=o.id LEFT JOIN enrolls as e ON e.order_id=o.id LEFT JOIN rents as r ON r.order_id=o.id LEFT JOIN rent_sws as rs ON rs.order_id=o.id WHERE o.branch_id=:branch_id AND o.stopped=1 AND if(e.id,e.start_date,if(r.id,DATE(r.start_datetime),if(rs.id,rs.start_date,"2000-01-01"))) >os.stop_start_date');
    $stmt_count->bindParam(':branch_id', $branch_id, PDO::PARAM_INT);
    $stmt_count->execute();
    $count = $stmt_count->fetchColumn();
    $stmt_count->closeCursor();

    if (empty($count)) {
        sl_log($log, 'Already Sync');

        return true;
    }

    $stmt_select = $pdo->prepare('SELECT os.id FROM orders as o INNER JOIN order_stops as os ON os.order_id=o.id LEFT JOIN enrolls as e ON e.order_id=o.id LEFT JOIN rents as r ON r.order_id=o.id LEFT JOIN rent_sws as rs ON rs.order_id=o.id WHERE o.branch_id=:branch_id AND o.stopped=1 AND if(e.id,e.start_date,if(r.id,DATE(r.start_datetime),if(rs.id,rs.start_date,"2000-01-01"))) >os.stop_start_date');
    $stmt_select->bindParam(':branch_id', $branch_id, PDO::PARAM_INT);
    $stmt_select->execute();
    $lists = $stmt_select->fetchAll(PDO::FETCH_ASSOC);
    $stmt_select->closeCursor();

    $stmt_update = $pdo->prepare('UPDATE order_stops SET is_change_start_date=1 WHERE id=:id');

    // 트랜잭션 시작
    $pdo->beginTransaction();
    $update_count = 0;

    foreach ($lists as $value) {
        $stmt_update->bindParam(':id', $value['id'], PDO::PARAM_INT);
        $stmt_update->execute();
        ++$update_count;

        sl_log($log, 'order_stop_id :'.$value['id'].' updated');
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

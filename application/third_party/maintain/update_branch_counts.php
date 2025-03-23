<?php

/*  지점카운트 다시 맞추는 스크립트입니다 */

try {
    $log_file = basename(__FILE__, '.php').'.log';
    include __DIR__.DIRECTORY_SEPARATOR.'common_head.php';

    $stmt_select = $pdo->prepare('SELECT * FROM centers WHERE enable=1');
    $stmt_select->execute();
    $centers = $stmt_select->fetchAll(PDO::FETCH_ASSOC);
    $stmt_select->closeCursor();

    $stmt_count = $pdo->prepare('SELECT count(*) FROM branches WHERE center_id=:id AND enable=1');
    $stmt_update = $pdo->prepare('UPDATE centers SET branch_counts=:branch_counts WHERE id=:id');

    // 트랜잭션 시작
    $pdo->beginTransaction();

    foreach ($centers as $center) {
        $stmt_count->bindParam(':id', $center['id'], PDO::PARAM_INT);
        $stmt_count->execute();
        $count = $stmt_count->fetchColumn();

        if ($center['branch_counts'] == $count) {
            continue;
        } else {
            $stmt_update->bindParam(':branch_counts', $count, PDO::PARAM_INT);
            $stmt_update->bindParam(':id', $center['id'], PDO::PARAM_INT);
            $stmt_update->execute();

            sl_log($log, 'center '.$center['id'].' count '.$center['branch_counts'].' change to '.$count);
        }
    }

    $stmt_count->closeCursor();
    $stmt_update->closeCursor();

    // 커밋
    $pdo->commit();
    $pdo = null;

    echo 'success'."\n";
} catch (Exception $e) {
    include __DIR__.DIRECTORY_SEPARATOR.'common_catch.php';
}

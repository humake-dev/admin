<?php

/* Branch use_ac_controller 다시 맞추는 스크립트입니다 */

try {
    $log_file = basename(__FILE__, '.php').'.log';
    include __DIR__.DIRECTORY_SEPARATOR.'common_head.php';

    $stmt_ac_count = $pdo->prepare('SELECT count(*) FROM access_controllers WHERE enable=1');
    $stmt_ac_count->execute();
    $count = $stmt_ac_count->fetchColumn();
    $stmt_ac_count->closeCursor();

    if (empty($count)) {
        exit;
    }

    $stmt_ac_select = $pdo->prepare('SELECT * FROM access_controllers WHERE enable=1');
    $stmt_ac_select->execute();
    $ac_controllers = $stmt_ac_select->fetchAll(PDO::FETCH_ASSOC);
    $stmt_ac_select->closeCursor();

    // 트랜잭션 시작
    $pdo->beginTransaction();

    $stmt_update_use_ac_controller = $pdo->prepare('UPDATE branches SET use_ac_controller=1 WHERE id=:id');

    foreach ($ac_controllers as $index => $ac_controller) {
        $stmt_update_use_ac_controller->bindParam(':id', $ac_controller['branch_id'], PDO::PARAM_INT);
        $stmt_update_use_ac_controller->execute();

        sl_log($log, 'branch '.$ac_controller['branch_id'].' : update branch use ac controller');
    }

    $stmt_update_use_ac_controller->closeCursor();

    // 커밋
    $pdo->commit();
    $pdo = null;

    echo 'success'."\n";
} catch (Exception $e) {
    include __DIR__.DIRECTORY_SEPARATOR.'common_catch.php';
}

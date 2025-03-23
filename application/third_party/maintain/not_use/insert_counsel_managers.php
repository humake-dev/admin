<?php

/*  stop_day_count 다시 맞추는 스크립트입니다 */

try {
    $log_file = basename(__FILE__, '.php').'.log';
    include __DIR__.DIRECTORY_SEPARATOR.'common_head.php';

    $stmt_count = $pdo->prepare('SELECT count(*) FROM counsel_admins as ca INNER JOIN admins as a ON ca.admin_id=a.id');
    $stmt_count->execute();
    $count = $stmt_count->fetchColumn();
    $stmt_count->closeCursor();

    if (empty($count)) {
        sl_log($log, 'Already Sync');

        return true;
    }

    $stmt_select = $pdo->prepare('SELECT ca.*,a.is_fc FROM counsel_admins as ca INNER JOIN admins as a ON ca.admin_id=a.id');
    $stmt_select->execute();
    $lists = $stmt_select->fetchAll(PDO::FETCH_ASSOC);
    $stmt_select->closeCursor();

    $stmt_insert = $pdo->prepare('INSERT INTO counsel_managers(counsel_id,admin_id) VALUES(:counsel_id,:admin_id)');

    // 트랜잭션 시작
    $pdo->beginTransaction();
    $insert_count = 0;

    foreach ($lists as $value) {
        if(empty($value['is_fc'])) {
            continue;
        }
        
        $stmt_insert->bindParam(':counsel_id', $value['counsel_id'], PDO::PARAM_INT);        
        $stmt_insert->bindParam(':admin_id', $value['admin_id'], PDO::PARAM_INT);
        $stmt_insert->execute();

        $inserted_id=$pdo->lastInsertId();
        $insert_count++;

        sl_log($log, 'insert counsel_managers :'.$inserted_id.' inserted');
    }

    $stmt_insert->closeCursor();

    echo 'inserted '.$insert_count."\n";

    // 커밋
    $pdo->commit();
    $pdo = null;

    echo 'success'."\n";
} catch (Exception $e) {
    include __DIR__.DIRECTORY_SEPARATOR.'common_catch.php';
}

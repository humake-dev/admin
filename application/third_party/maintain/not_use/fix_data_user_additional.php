<?php

/*  */

try {
    $log_file = basename(__FILE__, '.php').'.log';
    include __DIR__.DIRECTORY_SEPARATOR.'common_head.php';

    $stmt_count = $pdo->prepare('SELECT count(*) FROM users WHERE is_married is not null');
    $stmt_count->execute();
    $count = $stmt_count->fetchColumn();
    $stmt_count->closeCursor();

    if (empty($count)) {
        sl_log($log, 'Already Sync');

        return true;
    }

    $stmt_select = $pdo->prepare('SELECT id,is_married,wedding_anniversary FROM users WHERE is_married is not null');
    $stmt_select->execute();
    $lists = $stmt_select->fetchAll(PDO::FETCH_ASSOC);
    $stmt_select->closeCursor();

    $stmt_insert = $pdo->prepare('INSERT INTO user_additionals(user_id,is_married,wedding_anniversary,created_at,updated_at) VALUES(:user_id,:is_married,:wedding_anniversary,NOW(),NOW()) ON DUPLICATE KEY UPDATE is_married=:is_married,wedding_anniversary=:wedding_anniversary,updated_at=NOW()');

    // 트랜잭션 시작
    $pdo->beginTransaction();
    $insert_count = 0;

    foreach ($lists as $value) {
        $stmt_insert->bindParam(':user_id', $value['id'], PDO::PARAM_INT);         
        $stmt_insert->bindParam(':is_married', $value['is_married'], PDO::PARAM_INT);        
        $stmt_insert->bindParam(':wedding_anniversary', $value['wedding_anniversary'], PDO::PARAM_STR);
        $stmt_insert->bindParam(':is_married', $value['is_married'], PDO::PARAM_INT);        
        $stmt_insert->bindParam(':wedding_anniversary', $value['wedding_anniversary'], PDO::PARAM_STR);        
        $stmt_insert->execute();

        $inserted_id=$pdo->lastInsertId();
        $insert_count++;

        sl_log($log, 'insert user additional :'.$inserted_id.' inserted');
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

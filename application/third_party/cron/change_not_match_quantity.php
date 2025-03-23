<?php

/* 비정상 사용횟수 복구 스크립트입니다 */

try {
    $log_file = basename(__FILE__, '.php').'.log';
    include __DIR__.DIRECTORY_SEPARATOR.'common_head.php';

    $stmt_count = $pdo->prepare('SELECT count(*) FROM enrolls as e INNER JOIN courses as c ON e.course_id=c.id where use_quantity>quantity AND c.lesson_type=4');
    $stmt_count->execute();
    $count = $stmt_count->fetchColumn();
    $stmt_count->closeCursor();

    if (empty($count)) {
        sl_log($log, 'not exists mismatch');

        return true;
    }

    $stmt_select = $pdo->prepare('SELECT e.* FROM enrolls as e INNER JOIN courses as c ON e.course_id=c.id where use_quantity>quantity AND c.lesson_type=4');
    $stmt_select->execute();
    $list = $stmt_select->fetchAll(PDO::FETCH_ASSOC);
    $stmt_select->closeCursor();

    $stmt_update = $pdo->prepare('UPDATE `enrolls` SET `use_quantity`=`insert_quantity` WHERE `id`=:id');

    // 트랜잭션 시작
    $pdo->beginTransaction();

    foreach ($list as $enroll) {
        $stmt_update->bindParam(':id', $enroll['id'], PDO::PARAM_INT);
        $stmt_update->execute();

        sl_log($log, 'enroll id : '.$enroll['id'].' fixed');
    }
    $stmt_update->closeCursor();

    // 커밋
    $pdo->commit();

    $pdo = null;
} catch (Exception $e) {
    include __DIR__.DIRECTORY_SEPARATOR.'common_catch.php';
}

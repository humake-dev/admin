<?php

/* 잘못된 rent end_date update 스크립트입니다 */

try {
    $log_file = basename(__FILE__, '.php').'.log';
    include __DIR__.DIRECTORY_SEPARATOR.'common_head.php';
    ini_set('memory_limit', '256M');

    // 업데이트 갯수 구하기
    if (isset($branch_id)) {
        $stmt_count = $pdo->prepare('SELECT count(*) FROM rents AS r INNER JOIN orders AS o ON r.order_id=o.id WHERE r.end_datetime LIKE "%00:00:00" AND o.branch_id=:branch_id');
        $stmt_count->bindParam(':branch_id', $branch_id, PDO::PARAM_INT);
    } else {
        $stmt_count = $pdo->prepare('SELECT count(*) FROM rents AS r INNER JOIN orders AS o ON r.order_id=o.id WHERE r.end_datetime LIKE "%00:00:00"');
    }
    $stmt_count->execute();
    $count = $stmt_count->fetchColumn();
    $stmt_count->closeCursor();

    // 없으면 끝냄
    if (empty($count)) {
        sl_log($log, 'error end_datetime not exists');

        return true;
    }

    if (isset($branch_id)) {
        $stmt_select = $pdo->prepare('SELECT r.*,o.branch_id FROM rents AS r INNER JOIN orders AS o ON r.order_id=o.id WHERE end_datetime LIKE "%00:00:00"  AND o.branch_id=:branch_id');
        $stmt_select->bindParam(':branch_id', $branch_id, PDO::PARAM_INT);
    } else {
        $stmt_select = $pdo->prepare('SELECT r.*,o.branch_id FROM rents AS r INNER JOIN orders AS o ON r.order_id=o.id WHERE end_datetime LIKE "%00:00:00"');
    }
    $stmt_select->execute();
    $list = $stmt_select->fetchAll(PDO::FETCH_ASSOC);
    $stmt_select->closeCursor();

    $stmt_update = $pdo->prepare('UPDATE rents SET end_datetime=CONCAT(DATE(end_datetime)," 23:59:59") WHERE id=:id');

    // 트랜잭션 시작
    $pdo->beginTransaction();

    $success=0;
    // 해당 서버 DB돌면서 접속 가져오기
    foreach ($list as $error_rent) {
        $stmt_update->bindParam(':id', $error_rent['id'], PDO::PARAM_INT);
        $stmt_update->execute();

        sl_log($log, 'id : '.$error_rent['id'].' / update success');
        ++$success;
    }

    // 커밋
    $pdo->commit();
    $pdo = null;

    sl_log($log, 'success : '.$success.' rows');
} catch (Exception $e) {
    include __DIR__.DIRECTORY_SEPARATOR.'common_catch.php';
}

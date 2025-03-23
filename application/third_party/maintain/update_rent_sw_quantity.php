<?php

try {
    $log_file = basename(__FILE__, '.php').'.log';
    include __DIR__.DIRECTORY_SEPARATOR.'common_head.php';

    $stmt_select = $pdo->prepare('SELECT * FROM rent_sws as rs');
    $stmt_select->execute();
    $lists = $stmt_select->fetchAll(PDO::FETCH_ASSOC);

    $stmt_update = $pdo->prepare('UPDATE rent_sws SET insert_quantity=:insert_quantity WHERE id=:id');

    // 트랜잭션 시작
    $pdo->beginTransaction();
    $update_count = 0;

    foreach ($lists as $value) {
        $end_dateobj=new DateTime($value['end_date'],$dateTimeZone);
        $start_dateobj=new DateTime($value['start_date'],$dateTimeZone);

        $end_dateobj->modify('+1 Day');
        $diff_obj=$start_dateobj->diff($end_dateobj);
        $diff_month=$diff_obj->format('%m');

        if($diff_month<=1) {
            continue;
        }

        $stmt_update->bindParam(':insert_quantity', $diff_month , PDO::PARAM_INT);
        $stmt_update->bindParam(':id', $value['id'], PDO::PARAM_INT);
        $stmt_update->execute();
        sl_log($log, 'rent_sws :'.$value['id'].' updated');

        $update_count++;
    }

    $stmt_select->closeCursor();
    $stmt_update->closeCursor();

    echo $update_count."\n";

    // 커밋
    $pdo->commit();
    $pdo = null;

    echo 'success'."\n";
} catch (Exception $e) {
    include __DIR__.DIRECTORY_SEPARATOR.'common_catch.php';
}

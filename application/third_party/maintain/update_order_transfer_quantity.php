<?php

try {
    $log_file = basename(__FILE__, '.php').'.log';
    include __DIR__.DIRECTORY_SEPARATOR.'common_head.php';

    $stmt_select = $pdo->prepare('SELECT * FROM order_transfers');
    $stmt_select->execute();
    $lists = $stmt_select->fetchAll(PDO::FETCH_ASSOC);
    $stmt_select->closeCursor();

    $stmt_update = $pdo->prepare('UPDATE order_transfers SET origin_quantity=:quantity WHERE id=:id');

    // 트랜잭션 시작
    $pdo->beginTransaction();

    foreach ($lists as $value) {
        if($value['origin_quantity']==1) {
            continue;
        }

        if(empty($value['origin_start_date']) or empty($value['origin_end_date'])) {
            continue;
        }    

        $quantity=1;
        $start_date_obj=new DateTime($value['origin_start_date'],$dateTimeZone);
        $end_date_obj=new DateTime($value['origin_end_date'],$dateTimeZone);

        $diff_obj = $start_date_obj->diff($end_date_obj);
        $quantity = $diff_obj->format('%m')+1;

        $stmt_update->bindParam(':quantity', $quantity, PDO::PARAM_INT);
        $stmt_update->bindParam(':id', $value['id'], PDO::PARAM_INT);
        $stmt_update->execute();
    }
    $stmt_update->closeCursor();

    // 커밋
    $pdo->commit();
    $pdo = null;

    echo 'success'."\n";
} catch (Exception $e) {
    include __DIR__.DIRECTORY_SEPARATOR.'common_catch.php';
}

<?php

/*  enroll duplicate 다시 맞추는 스크립트입니다 */

try {
    $log_file = basename(__FILE__, '.php').'.log';
    include __DIR__.DIRECTORY_SEPARATOR.'common_head.php';

    $product_id=372;

    $stmt_count = $pdo->prepare('SELECT count(*) FROM (SELECT count(*) FROM orders AS o INNER JOIN enrolls AS e ON e.order_id=o.id INNER JOIN users as u ON o.user_id=u.id INNER JOIN order_products as op ON op.order_id=o.id WHERE o.branch_id=:branch_id AND e.start_date<="2020-08-31" AND e.end_date>="2020-08-31" AND o.enable=1 AND u.enable=1 AND op.product_id=:product_id GROUP BY u.id) as b');
    $stmt_count->bindParam(':branch_id', $branch_id, PDO::PARAM_INT);
    $stmt_count->bindParam(':product_id', $product_id, PDO::PARAM_INT);
    $stmt_count->execute();
    $count = $stmt_count->fetchColumn();
    $stmt_count->closeCursor();

    if (empty($count)) {
        sl_log($log, 'Already Sync');

        return true;
    }

    $stmt_select = $pdo->prepare('SELECT u.id,o.id as order_id,e.start_date,e.end_date FROM orders AS o INNER JOIN enrolls AS e ON e.order_id=o.id INNER JOIN users as u ON o.user_id=u.id INNER JOIN order_products as op ON op.order_id=o.id WHERE o.branch_id=:branch_id AND e.start_date<="2020-08-31" AND e.end_date>="2020-08-31" AND o.enable=1 AND u.enable=1 AND op.product_id=:product_id GROUP BY u.id');
    $stmt_select->bindParam(':branch_id', $branch_id, PDO::PARAM_INT);
    $stmt_select->bindParam(':product_id', $product_id, PDO::PARAM_INT);
    $stmt_select->execute();
    $lists = $stmt_select->fetchAll(PDO::FETCH_ASSOC);
    $stmt_select->closeCursor();



    $stmt_update = $pdo->prepare('UPDATE enrolls SET start_date=:start_date,end_date=:end_date WHERE id=:id');
    $stmt_count_p = $pdo->prepare('SELECT count(*) FROM orders AS o INNER JOIN enrolls AS e ON e.order_id=o.id INNER JOIN order_products as op ON op.order_id=o.id WHERE o.enable=1 AND o.user_id=:user_id AND op.product_id=:product_id AND o.id!=:order_id AND e.start_date>="2020-08-14"');
    $stmt_select_p = $pdo->prepare('SELECT e.id,e.start_date,e.end_date FROM orders AS o INNER JOIN enrolls AS e ON e.order_id=o.id INNER JOIN order_products as op ON op.order_id=o.id WHERE o.enable=1 AND o.user_id=:user_id AND op.product_id=:product_id AND o.id!=:order_id AND e.start_date>="2020-08-14"');

    // 트랜잭션 시작
    $pdo->beginTransaction();
    $update_count = 0;
    $tm_user=array();


    foreach ($lists as $user) {
        $stmt_count_p->bindParam(':user_id', $user['id'], PDO::PARAM_INT);
        $stmt_count_p->bindParam(':order_id', $user['order_id'], PDO::PARAM_INT);
        $stmt_count_p->bindParam(':product_id', $product_id, PDO::PARAM_INT);
        $stmt_count_p->execute();
        $count = $stmt_count_p->fetchColumn();

        if(empty($count)) {
            continue;
        }

        if($count>1) {
            $tm_user[]=$user['id'];
            continue;
        }

        $stmt_select_p->bindParam(':user_id', $user['id'], PDO::PARAM_INT);
        $stmt_select_p->bindParam(':order_id', $user['order_id'], PDO::PARAM_INT);
        $stmt_select_p->bindParam(':product_id', $product_id, PDO::PARAM_INT);
        $stmt_select_p->execute();
        $lists = $stmt_select_p->fetchAll(PDO::FETCH_ASSOC);

        $cc=true;
        if($user['end_date']>=$lists[0]['start_date']) {
            $cc=false;
        }

        if($cc) {
            continue;
        }

        $update_count++;


        $end_dateobj=new DateTime($user['end_date'],$dateTimeZone);
        $start_dateobj=new DateTime($lists[0]['start_date'],$dateTimeZone);

        $diff_obj=$start_dateobj->diff($end_dateobj);
        $diff_day=$diff_obj->format('%a');

        if(intval($diff_day)>40) {
        //    continue;
        }

        echo ' '.$diff_day;
        echo ' '.$user['id'];

        $new_start_date_obj=new DateTime($lists[0]['start_date'],$dateTimeZone);
        $new_start_date_obj->add($diff_obj);
        $new_start_date_obj->modify('+1 day');
        $new_start_date=$new_start_date_obj->format('Y-m-d');


        $new_end_date_obj=new DateTime($lists[0]['end_date'],$dateTimeZone);
        $new_end_date_obj->add($diff_obj);
        $new_end_date_obj->modify('+1 day');
        $new_end_date=$new_end_date_obj->format('Y-m-d');

        /*$stmt_update->bindParam(':start_date', $new_start_date, PDO::PARAM_STR);
        $stmt_update->bindParam(':end_date', $new_end_date, PDO::PARAM_STR);
        $stmt_update->bindParam(':id', $lists[0]['id'], PDO::PARAM_INT);
        $stmt_update->execute(); */

        /* foreach ($lists as $user) {

        } */
    }

    echo $update_count."\n";

    // 커밋
    $pdo->commit();
    $pdo = null;

    echo 'success'."\n";
} catch (Exception $e) {
    include __DIR__.DIRECTORY_SEPARATOR.'common_catch.php';
}

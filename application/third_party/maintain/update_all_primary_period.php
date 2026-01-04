<?php

/*  재수강 다시 맞추는 스크립트입니다 */

try {
    $log_file = basename(__FILE__, '.php').'.log';
    include __DIR__.DIRECTORY_SEPARATOR.'common_head.php';

    $stmt_count = $pdo->prepare('SELECT COUNT(*) AS `numrows` FROM ( SELECT count(*) FROM `users` as `u` LEFT JOIN `orders` as `o` ON `o`.`user_id`=`u`.`id` LEFT JOIN `order_products` as `op` ON `op`.`order_id`=`o`.`id` LEFT JOIN `products` as `p` ON `op`.`product_id`=`p`.`id` LEFT JOIN `rents` as `r` ON `r`.`order_id`=`o`.`id` LEFT JOIN `account_orders` AS `ao` ON `ao`.`order_id`=`o`.`id` LEFT JOIN `accounts` AS `a` ON `ao`.`account_id`=`a`.`id` LEFT JOIN `courses` as `c` ON `c`.`product_id`=`p`.`id` LEFT JOIN `order_stops` AS `os` ON `os`.`id` = (SELECT max(id) FROM order_stops AS os2 WHERE os2.order_id = o.id) LEFT JOIN `user_fcs` AS `ufc` ON `ufc`.`user_id`=`u`.`id` LEFT JOIN `admins` AS `fc` ON `ufc`.`fc_id`=`fc`.`id` LEFT JOIN `enrolls` as `e` ON `e`.`order_id`=`o`.`id` LEFT JOIN `enroll_trainers` as `et` ON `et`.`enroll_id`=`e`.`id` LEFT JOIN `user_trainers` AS `ut` ON `ut`.`user_id`=`u`.`id` LEFT JOIN `admins` AS `trainer` ON `ut`.`trainer_id`=`trainer`.`id` WHERE `op`.`product_id` IN(431) AND (`a`.`enable` = 1 OR `a`.`id` is null) AND (`a`.`account_category_id` != 25 OR `a`.`id` is null) AND (`o`.`enable` !=0) AND `u`.`branch_id` = :branch_id AND `u`.`enable` = 1 AND e.end_date>="2020-03-23" GROUP BY `u`.`id` HAVING if(SUM(if(c.lesson_type=4,1,0))>0,(CAST(SUM(e.quantity) AS SIGNED)-CAST(SUM(e.use_quantity) AS SIGNED)>0),(SUM(if(a.type="I",(a.cash+a.credit),-(a.cash+a.credit)))>0))) CI_count_all_results');
    $stmt_count->bindParam(':branch_id', $branch_id, PDO::PARAM_INT);
    $stmt_count->execute();
    $count = $stmt_count->fetchColumn();
    $stmt_count->closeCursor();

    if (empty($count)) {
        sl_log($log, 'Already Sync');

        return true;
    }

    $stmt_select = $pdo->prepare('SELECT e.id,o.user_id FROM `users` as `u` LEFT JOIN `orders` as `o` ON `o`.`user_id`=`u`.`id` LEFT JOIN `order_products` as `op` ON `op`.`order_id`=`o`.`id` LEFT JOIN `products` as `p` ON `op`.`product_id`=`p`.`id` LEFT JOIN `rents` as `r` ON `r`.`order_id`=`o`.`id` LEFT JOIN `account_orders` AS `ao` ON `ao`.`order_id`=`o`.`id` LEFT JOIN `accounts` AS `a` ON `ao`.`account_id`=`a`.`id` LEFT JOIN `courses` as `c` ON `c`.`product_id`=`p`.`id` LEFT JOIN `order_stops` AS `os` ON `os`.`id` = (SELECT max(id) FROM order_stops AS os2 WHERE os2.order_id = o.id) LEFT JOIN `user_fcs` AS `ufc` ON `ufc`.`user_id`=`u`.`id` LEFT JOIN `admins` AS `fc` ON `ufc`.`fc_id`=`fc`.`id` LEFT JOIN `enrolls` as `e` ON `e`.`order_id`=`o`.`id` LEFT JOIN `enroll_trainers` as `et` ON `et`.`enroll_id`=`e`.`id` LEFT JOIN `user_trainers` AS `ut` ON `ut`.`user_id`=`u`.`id` LEFT JOIN `admins` AS `trainer` ON `ut`.`trainer_id`=`trainer`.`id` WHERE `op`.`product_id` IN(431) AND (`a`.`enable` = 1 OR `a`.`id` is null) AND (`a`.`account_category_id` != 25 OR `a`.`id` is null) AND (`o`.`enable` !=0) AND `u`.`branch_id` = :branch_id AND `u`.`enable` = 1 AND e.end_date>="2020-03-23" GROUP BY `u`.`id` HAVING if(SUM(if(c.lesson_type=4,1,0))>0,(CAST(SUM(e.quantity) AS SIGNED)-CAST(SUM(e.use_quantity) AS SIGNED)>0),(SUM(if(a.type="I",(a.cash+a.credit),-(a.cash+a.credit)))>0))');
    $stmt_select->bindParam(':branch_id', $branch_id, PDO::PARAM_INT);
    $stmt_select->execute();
    $lists = $stmt_select->fetchAll(PDO::FETCH_ASSOC);
    $stmt_select->closeCursor();

    $stmt_update = $pdo->prepare('UPDATE enrolls SET end_date=DATE_ADD(end_date, INTERVAL 14 DAY) WHERE id=:id');

    $stmt_insert = $pdo->prepare('INSERT INTO user_contents(user_id,content,created_at,updated_at) VALUES(:user_id,:content,NOW(),NOW())');

    // 트랜잭션 시작
    $pdo->beginTransaction();
    $update_count = 0;

    $content='코로나 무료연장';

    foreach ($lists as $value) {
        $stmt_update->bindParam(':id', $value['id'], PDO::PARAM_INT);
        $stmt_update->execute();
        $update_count++;

        $stmt_insert->bindParam(':user_id', $value['user_id'], PDO::PARAM_INT);
        $stmt_insert->bindParam(':content', $content, PDO::PARAM_STR);
        $stmt_insert->execute();

        sl_log($log, 'order_id :'.$value['id'].' updated');
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

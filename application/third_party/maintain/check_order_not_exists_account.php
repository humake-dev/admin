<?php

try {
    $log_file = basename(__FILE__, '.php').'.log';
    include __DIR__.DIRECTORY_SEPARATOR.'common_head.php';

    if (empty($argv[1])) {
        echo 'branch_id need / example) php '.basename(__FILE__, '.php').' branch_id type'."\n";

        return true;
    }

    $stmt_count = $pdo->prepare('SELECT count(*) FROM (SELECT count(*) FROM orders as o INNER JOIN account_orders as ao ON ao.order_id=o.id INNER JOIN accounts as a ON ao.account_id=a.id WHERE o.enable=1 AND o.branch_id=:branch_id AND o.price>0 AND o.user_id IS NOT NULL GROUP BY o.id HAVING SUM(IF(a.enable=0,1,0))!=0) as gg');
    $stmt_count->bindParam(':branch_id', $branch_id, PDO::PARAM_INT);
    $stmt_count->execute();
    $count = $stmt_count->fetchColumn();
    $stmt_count->closeCursor();
    
    if(empty($count)) {
        echo 'Not Exists, All OK'."\n";
    }

    $stmt_select = $pdo->prepare('SELECT o.id,o.user_id FROM orders as o INNER JOIN account_orders as ao ON ao.order_id=o.id INNER JOIN accounts as a ON ao.account_id=a.id WHERE o.enable=1 AND o.branch_id=:branch_id AND o.price>0 AND o.user_id IS NOT NULL GROUP BY o.id HAVING SUM(IF(a.enable=0,1,0))!=0');
    $stmt_select->bindParam(':branch_id', $branch_id, PDO::PARAM_INT);
    $stmt_select->execute();
    $list = $stmt_select->fetchAll(PDO::FETCH_ASSOC);

    foreach($list as $value) {
        echo $value['user_id'].'`s order '.$value['id']."\n";
    }

    $pdo = null;
} catch (Exception $e) {
    include __DIR__.DIRECTORY_SEPARATOR.'common_catch.php';
}

<?php

try {
    $log_file = basename(__FILE__, '.php').'.log';
    include __DIR__.DIRECTORY_SEPARATOR.'common_head.php';

    $count_query = 'SELECT count(*) FROM orders as o INNER JOIN order_products as op ON op.order_id=o.id LEFT JOIN account_orders as ao ON ao.order_id=o.id WHERE ao.id is NULL AND o.branch_id=:branch_id AND o.payment>0 AND o.enable=1';

    if (isset($branch_id)) {
        $stmt_count = $pdo->prepare($count_query);
        $stmt_count->bindParam(':branch_id', $branch_id, PDO::PARAM_INT);
        $stmt_count->execute();
        $ac_count = $stmt_count->fetchColumn();

        echo $ac_count."\n";
    } else {
        $branch_query = 'SELECT * FROM branches as b WHERE enable=1';
        $stmt_select_branch = $pdo->prepare($branch_query);
        $stmt_select_branch->execute();
        $branches = $stmt_select_branch->fetchAll();

        $stmt_count = $pdo->prepare($count_query);

        foreach ($branches as $branch) {
            $stmt_count->bindParam(':branch_id', $branch['id'], PDO::PARAM_INT);
            $stmt_count->execute();
            $ac_count = $stmt_count->fetchColumn();

            echo $branch['id'].' : '.$ac_count."\n";
        }
    }

    $stmt_count->closeCursor();

    echo 'success'."\n";
} catch (Exception $e) {
    include __DIR__.DIRECTORY_SEPARATOR.'common_catch.php';
}

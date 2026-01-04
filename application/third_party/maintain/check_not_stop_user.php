<?php

try {
    $log_file = basename(__FILE__, '.php').'.log';
    include __DIR__.DIRECTORY_SEPARATOR.'common_head.php';

    if (isset($branch_id)) {
        $stmt_count = $pdo->prepare('SELECT count(*) FROM (SELECT count(*) FROM users as u INNER JOIN user_stops as us ON us.user_id=u.id WHERE u.enable=1 AND us.enable=1 AND us.stop_start_date<=CURDATE() AND us.stop_end_date>=CURDATE() AND NOT EXISTS (SELECT * FROM orders as o WHERE o.user_id=u.id AND o.stopped=1) AND o.branch_id=:branch_id GROUP BY u.id) as b');
        $stmt_count->bindParam(':branch_id', $branch_id, PDO::PARAM_INT);
    } else {
        $stmt_count = $pdo->prepare('SELECT count(*) FROM (SELECT count(*) FROM users as u INNER JOIN user_stops as us ON us.user_id=u.id WHERE u.enable=1 AND us.enable=1 AND us.stop_start_date<=CURDATE() AND us.stop_end_date>=CURDATE() AND NOT EXISTS (SELECT * FROM orders as o WHERE o.user_id=u.id AND o.stopped=1) GROUP BY u.id) as b');
    }

    $stmt_count->execute();
    $count = $stmt_count->fetchColumn();
    $stmt_count->closeCursor();

    if (isset($branch_id)) {
        $stmt_select = $pdo->prepare('SELECT u.* FROM users as u INNER JOIN user_stops as us ON us.user_id=u.id WHERE u.enable=1 AND us.enable=1 AND us.stop_start_date<=CURDATE() AND us.stop_end_date>=CURDATE() AND NOT EXISTS (SELECT * FROM orders as o WHERE o.user_id=u.id AND o.stopped=1) AND o.branch_id=:branch_id GROUP BY u.id ORDER BY u.branch_id,u.id');
        $stmt_select->bindParam(':branch_id', $branch_id, PDO::PARAM_INT);
    } else {
        $stmt_select = $pdo->prepare('SELECT u.* FROM users as u INNER JOIN user_stops as us ON us.user_id=u.id WHERE u.enable=1 AND us.enable=1 AND us.stop_start_date<=CURDATE() AND us.stop_end_date>=CURDATE() AND NOT EXISTS (SELECT * FROM orders as o WHERE o.user_id=u.id AND o.stopped=1) GROUP BY u.id ORDER BY u.branch_id,u.id');
    }
    $stmt_select->execute();
    $list = $stmt_select->fetchAll(PDO::FETCH_ASSOC);
    $stmt_select->closeCursor();

    foreach ($list as $value) {
        print_r($value['branch_id'].'#'.$value['id'].' : '.$value['name']."\n");
    }

    echo 'success'."\n";
} catch (Exception $e) {
    include __DIR__.DIRECTORY_SEPARATOR.'common_catch.php';
}

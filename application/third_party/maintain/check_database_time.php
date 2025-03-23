<?php

try {
    $log_file = basename(__FILE__, '.php').'.log';
    include __DIR__.DIRECTORY_SEPARATOR.'common_head.php';

    $stmt_now = $pdo->prepare('SELECT NOW() as now');
    $stmt_now->execute();
    $now = $stmt_now->fetchColumn();
    $stmt_now->closeCursor();

    $pdo = null;

    echo $now."\n";
} catch (Exception $e) {
    include __DIR__.DIRECTORY_SEPARATOR.'common_catch.php';
}

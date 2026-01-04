<?php

try {
    $log_file = basename(__FILE__, '.php').'.log';
    include __DIR__.DIRECTORY_SEPARATOR.'common_head.php';

    if (empty($argv[1])) {
        echo 'user_id need / example) php change_password.php 1 password'."\n";

        return true;
    }

    $user_id = filter_var($argv[1], FILTER_VALIDATE_INT);
    if (!$user_id) {
        echo 'invalid user_id'."\n";

        return true;
    }

    if (empty($argv[2])) {
        echo 'password need / example) php change_password.php 1 password'."\n";

        return true;
    }

    $password = $argv[2];
    if (!$password) {
        echo 'invalid password'."\n";

        return true;
    }

    $stmt_select = $pdo->prepare('SELECT id FROM admins WHERE id=:id');
    $stmt_select->bindParam(':id', $user_id, PDO::PARAM_INT);
    $stmt_select->execute();
    $user = $stmt_select->fetch(PDO::FETCH_ASSOC);
    $stmt_select->closeCursor();

    $password = crypt($password.'sleeping-lion', '$2a$10$'.substr(md5(time()), 0, 22));

    $stmt_update = $pdo->prepare('UPDATE admins SET encrypted_password=:encrypted_password WHERE id=:id');
    $stmt_update->bindParam(':encrypted_password', $password, PDO::PARAM_STR);
    $stmt_update->bindParam(':id', $user['id'], PDO::PARAM_INT);
    $stmt_update->execute();

    $stmt_select->closeCursor();
    $stmt_update->closeCursor();

    $pdo = null;

    echo 'success'."\n";
} catch (Exception $e) {
    include __DIR__.DIRECTORY_SEPARATOR.'common_catch.php';
}

function change_dec_to_hex($dec)
{
    $a_str = str_split(dechex($dec), '2');
    $r_str = '';

    foreach ($a_str as $n_str) {
        $r_str .= strrev($n_str);
    }
    
    return strrev($r_str);
}

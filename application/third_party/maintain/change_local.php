<?php

try {
    $log_file = basename(__FILE__, '.php').'.log';
    include __DIR__.DIRECTORY_SEPARATOR.'common_head.php';

    if(ENVIRONMENT=='production') {
        echo 'this is production environment exit';
        exit;
    }
    
    $change_password='a12345';
    
    if(empty($branch_id)) {
        $stmt_ac_select_count = $pdo->prepare('SELECT count(*) FROM access_controllers');
        $stmt_ac_select_count->execute();
        $count = $stmt_ac_select_count->fetchColumn();
    } else {
        $stmt_ac_select_count = $pdo->prepare('SELECT count(*) FROM access_controllers WHERE branch_id=:branch_id');
        $stmt_ac_select_count->bindParam(':branch_id', $branch_id, PDO::PARAM_INT);
        $stmt_ac_select_count->execute();
        $count = $stmt_ac_select_count->fetchColumn();
    }
    $stmt_ac_select_count->closeCursor();

    if ($count) {
        if(empty($branch_id)) {
            $stmt_delete = $pdo->prepare('DELETE FROM access_controllers');
            $stmt_delete->execute();
        } else {
            $stmt_delete = $pdo->prepare('DELETE FROM access_controllers WHERE branch_id=:branch_id');
            $stmt_delete->bindParam(':branch_id', $branch_id, PDO::PARAM_INT);
            $stmt_delete->execute();
        }

        $stmt_delete->closeCursor();
    }

    if(empty($branch_id)) {
        $stmt_select = $pdo->prepare('SELECT id FROM admins');
    } else {
        $stmt_select = $pdo->prepare('SELECT id FROM admins WHERE branch_id=:branch_id');
        $stmt_select->bindParam(':branch_id', $branch_id, PDO::PARAM_INT);
    }

    $stmt_select->execute();
    $admins = $stmt_select->fetchAll(PDO::FETCH_ASSOC);
    $stmt_select->closeCursor();

    $stmt_update = $pdo->prepare('UPDATE admins SET encrypted_password=:encrypted_password WHERE id=:id');

    foreach($admins as $admin) {
        $password = crypt($change_password.'sleeping-lion', '$2a$10$'.substr(md5(time()), 0, 22));
        $stmt_update->bindParam(':encrypted_password', $password, PDO::PARAM_STR);
        $stmt_update->bindParam(':id', $admin['id'], PDO::PARAM_INT);
        $stmt_update->execute();
    }
    
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

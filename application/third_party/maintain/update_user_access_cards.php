<?php

/*  지점카운트 다시 맞추는 스크립트입니다 */

try {
    $log_file = basename(__FILE__, '.php').'.log';
    include __DIR__.DIRECTORY_SEPARATOR.'common_head.php';

    $stmt_select = $pdo->prepare('SELECT card_no FROM user_access_cards as uac INNER JOIN users as u ON uac.user_id=u.id WHERE u.branch_id=6 GROUP BY card_no HAVING count(*)>1');
    $stmt_select->execute();
    $lists = $stmt_select->fetchAll(PDO::FETCH_ASSOC);
    $stmt_select->closeCursor();

    $stmt_select_users = $pdo->prepare('SELECT u.id FROM user_access_cards as uac INNER JOIN users as u ON uac.user_id=u.id WHERE uac.card_no=:card_no');

    $stmt_enroll_count = $pdo->prepare('SELECT count(*) FROM users as u INNER JOIN orders as o ON o.user_id=u.id INNER JOIN enrolls as e ON e.order_id=o.id WHERE user_id=:user_id AND e.start_date<=CURDATE() AND e.end_date>=CURDATE()');
    $stmt_delete = $pdo->prepare('DELETE FROM user_access_cards WHERE user_id=:user_id');

    // 트랜잭션 시작
    $pdo->beginTransaction();

    foreach ($lists as $value) {   
        $stmt_select_users->bindParam(':card_no', $value['card_no'], PDO::PARAM_STR);     
        $stmt_select_users->execute();
        $users = $stmt_select_users->fetchAll(PDO::FETCH_ASSOC);
        $not_exist_enroll=true;
        foreach ($users as $user) {
            $stmt_enroll_count->bindParam(':user_id', $user['id'], PDO::PARAM_INT);
            $stmt_enroll_count->execute();
            $enroll_count = $stmt_enroll_count->fetchColumn();

            if($enroll_count) {
                $not_exist_enroll=false;
            }
        }

        if($not_exist_enroll) {
            foreach ($users as $user) {
                $stmt_delete->bindParam(':user_id', $user['id'], PDO::PARAM_INT);
                $stmt_delete->execute();
            }
        }
    }

    $stmt_enroll_count->closeCursor();
    $stmt_delete->closeCursor();

    // 커밋
    $pdo->commit();
    $pdo = null;

    echo 'success'."\n";
} catch (Exception $e) {
    include __DIR__.DIRECTORY_SEPARATOR.'common_catch.php';
}

<?php

/*  재수강 다시 맞추는 스크립트입니다 */

try {
    $log_file = basename(__FILE__, '.php').'.log';
    include __DIR__.DIRECTORY_SEPARATOR.'common_head.php';

    $plus_date=6;

    $stmt_count = $pdo->prepare('SELECT COUNT(*) AS count FROM enrolls AS e INNER JOIN orders as o ON e.order_id=o.id INNER JOIN users as u ON u.id=o.user_id WHERE o.enable=1 AND o.branch_id=14 AND e.course_id=1519 AND e.have_datetime="2023-04-06 16:45:59"');
    $stmt_count->execute();
    $count = $stmt_count->fetchColumn();
    $stmt_count->closeCursor();

    if (empty($count)) {
        sl_log($log, 'Already Sync');

        return true;
    }

    $stmt_select = $pdo->prepare('SELECT u.id as user_id, e.* FROM enrolls AS e INNER JOIN orders as o ON e.order_id=o.id INNER JOIN users as u ON u.id=o.user_id WHERE o.enable=1 AND o.branch_id=14 AND e.course_id=1519 AND e.have_datetime="2023-04-06 16:45:59"');
    $stmt_select->execute();
    $lists = $stmt_select->fetchAll(PDO::FETCH_ASSOC);
    $stmt_select->closeCursor();

    $stmt_count_enroll = $pdo->prepare('SELECT count(*) as count FROM orders AS o INNER JOIN enrolls as e ON e.order_id=o.id INNER JOIN courses as c ON e.course_id=c.id LEFT JOIN order_ends as oe ON oe.order_id=o.id WHERE c.lesson_type=1 AND oe.id is NULL AND c.id=1519 AND o.enable=1 AND o.user_id=:user_id AND e.have_datetime="2023-04-06 16:45:59"');
    $stmt_enroll= $pdo->prepare('SELECT e.start_date,e.end_date,o.user_id FROM orders AS o INNER JOIN enrolls as e ON e.order_id=o.id INNER JOIN courses as c ON e.course_id=c.id LEFT JOIN order_ends as oe ON oe.order_id=o.id WHERE c.lesson_type=1 AND oe.id is NULL AND c.id=1519 AND o.enable=1 AND o.user_id=:user_id AND e.have_datetime="2023-04-06 16:45:59"');

    $stmt_rent_count = $pdo->prepare('SELECT count(*) as count FROM rents AS r INNER JOIN orders as o ON r.order_id=o.id INNER JOIN users as u ON u.id=o.user_id WHERE o.branch_id=14 AND u.id=:user_id AND o.created_at="2023-04-06 16:46:00"');
    $stmt_rent_sws_count = $pdo->prepare('SELECT count(*) as count FROM rent_sws AS rs INNER JOIN orders as o ON rs.order_id=o.id INNER JOIN users as u ON u.id=o.user_id WHERE o.branch_id=14 AND u.id=:user_id AND o.created_at="2023-04-06 16:46:00"');

    $stmt_update_rent= $pdo->prepare('UPDATE rents AS r INNER JOIN orders as o ON r.order_id=o.id INNER JOIN users as u ON u.id=o.user_id SET r.start_datetime=:start_date,r.end_datetime=:end_date WHERE o.branch_id=14 AND u.id=:user_id AND o.created_at="2023-04-06 16:46:00"');
    $stmt_update_rent_sw= $pdo->prepare('UPDATE rent_sws AS rs INNER JOIN orders as o ON rs.order_id=o.id INNER JOIN users as u ON u.id=o.user_id SET rs.start_date=:start_date,rs.end_date=:end_date WHERE o.branch_id=14 AND u.id=:user_id AND o.created_at="2023-04-06 16:46:00"');

    // 트랜잭션 시작
    $pdo->beginTransaction();
    $update_count = 0;

    foreach ($lists as $value) {
        $stmt_count_enroll->bindParam(':user_id', $value['user_id'], PDO::PARAM_INT);
        $stmt_count_enroll->execute();
        $count_enroll = $stmt_count_enroll->fetchColumn();

        if(empty($count_enroll)) {
            continue;
        }

        $stmt_rent_count->bindParam(':user_id', $value['user_id'], PDO::PARAM_INT);
        $stmt_rent_count->execute();
        $rent_count = $stmt_rent_count->fetchColumn();

        $stmt_rent_sws_count->bindParam(':user_id', $value['user_id'], PDO::PARAM_INT);
        $stmt_rent_sws_count->execute();
        $rent_sws_count = $stmt_rent_sws_count->fetchColumn();


        if(empty($rent_count) and empty($rent_sws_count)) {
            echo 'user '.$value['user_id']. ' skipped'."\n";
            continue;
        }

        $stmt_enroll->bindParam(':user_id', $value['user_id'], PDO::PARAM_INT);
        $stmt_enroll->execute();
        $enroll = $stmt_enroll->fetch(PDO::FETCH_ASSOC);

        $rent_result=false;
        if(!empty($rent_count)) {
            $start_datetime=$enroll['start_date'].' 00:00:00';
            $end_datetime=$enroll['end_date'].' 23:59:59';

            $stmt_update_rent->bindParam(':start_date', $start_datetime, PDO::PARAM_STR);
            $stmt_update_rent->bindParam(':end_date', $end_datetime, PDO::PARAM_STR);
            $stmt_update_rent->bindParam(':user_id', $enroll['user_id'], PDO::PARAM_INT);
            $rent_result=$stmt_update_rent->execute();
        }

        $rent_sw_result=false;
        if(!empty($rent_sws_count)) {
            $stmt_update_rent_sw->bindParam(':start_date', $enroll['start_date'], PDO::PARAM_STR);
            $stmt_update_rent_sw->bindParam(':end_date', $enroll['end_date'], PDO::PARAM_STR);
            $stmt_update_rent_sw->bindParam(':user_id', $enroll['user_id'], PDO::PARAM_INT);
            $rent_sw_result=$stmt_update_rent_sw->execute();
        }
        
        if(!empty($rent_result) or !empty($rent_sw_result)) {
            $update_count++;
            echo 'user '.$value['user_id']. ' updated'."\n";
        }
    }

    $stmt_count_enroll->closeCursor();
    $stmt_rent_count->closeCursor();
    $stmt_rent_sws_count->closeCursor();
    $stmt_update_rent->closeCursor();
    $stmt_update_rent_sw->closeCursor();
    $stmt_enroll->closeCursor();


    echo $update_count."\n";

    // 커밋
    $pdo->commit();
    $pdo = null;

    echo 'success'."\n";
} catch (Exception $e) {
    include __DIR__.DIRECTORY_SEPARATOR.'common_catch.php';
}

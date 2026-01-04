<?php

/*  기준일 회원권 추가 스크립트 */

try {
    $log_file = basename(__FILE__, '.php').'.log';
    include __DIR__.DIRECTORY_SEPARATOR.'common_head.php';    

    $branch_id=10;
    $course_id= 1474;
    $reference_date='2025-06-01';
    $plus_date=14;
    $content='인테리어 리모델링 및 샤워장 공사로 인한 기간추가';
    

    $stmt_count = $pdo->prepare('SELECT COUNT(*) AS count FROM users AS u WHERE u.enable=1 AND branch_id=:branch_id');
    $stmt_count->bindParam(':branch_id', $branch_id, PDO::PARAM_INT);
    $stmt_count->execute();
    $count = $stmt_count->fetchColumn();
    $stmt_count->closeCursor();

    if (empty($count)) {
        sl_log($log, 'user not exists');

        return true;
    }

    $stmt_select = $pdo->prepare('SELECT * FROM users AS u WHERE u.enable=1 AND branch_id=:branch_id');
    $stmt_select->bindParam(':branch_id', $branch_id, PDO::PARAM_INT);
    $stmt_select->execute();
    $lists = $stmt_select->fetchAll(PDO::FETCH_ASSOC);
    $stmt_select->closeCursor();

    $stmt_count_stopping = $pdo->prepare('SELECT COUNT(*) AS count FROM user_stops AS us WHERE us.enable=1 AND us.stop_start_date<="'.$reference_date.'" AND us.stop_end_date>="'.$reference_date.'" AND us.user_id=:id');

    $stmt_count_enroll = $pdo->prepare('SELECT COUNT(*) AS count FROM orders AS o INNER JOIN enrolls as e ON e.order_id=o.id INNER JOIN courses as c ON e.course_id=c.id LEFT JOIN order_ends as oe ON oe.order_id=o.id WHERE e.start_date<="'.$reference_date.'" AND e.end_date>="'.$reference_date.'" AND c.lesson_type=1 AND oe.id is NULL AND o.enable=1 AND o.user_id=:user_id');
    $stmt_count_rent = $pdo->prepare('SELECT COUNT(*) AS count FROM orders AS o INNER JOIN rents as r ON r.order_id=o.id LEFT JOIN order_ends as oe ON oe.order_id=o.id WHERE DATE(r.start_datetime)<="'.$reference_date.'" AND DATE(r.end_datetime)>="'.$reference_date.'" AND oe.id is NULL AND o.enable=1 AND o.user_id=:user_id');
    $stmt_count_rsw = $pdo->prepare('SELECT COUNT(*) AS count FROM orders AS o INNER JOIN rent_sws as rs ON rs.order_id=o.id LEFT JOIN order_ends as oe ON oe.order_id=o.id WHERE rs.start_date<="'.$reference_date.'" AND rs.end_date>="'.$reference_date.'" AND oe.id is NULL AND o.enable=1 AND o.user_id=:user_id');
    $stmt_count_course = $pdo->prepare('SELECT count(*) FROM courses as c INNER JOIN products as p ON c.product_id=p.id WHERE c.id=:id AND p.enable=1');

    $stmt_select_enroll = $pdo->prepare('SELECT e.id,e.order_id,e.start_date,e.end_date,o.user_id FROM orders AS o INNER JOIN enrolls as e ON e.order_id=o.id INNER JOIN courses as c ON e.course_id=c.id LEFT JOIN order_ends as oe ON oe.order_id=o.id WHERE e.start_date<="'.$reference_date.'" AND e.end_date>="'.$reference_date.'"  AND c.lesson_type=1 AND o.enable=1 AND oe.id is NULL AND o.user_id=:user_id ORDER BY e.end_date DESC LIMIT 1');
    $stmt_select_rent = $pdo->prepare('SELECT r.id,r.order_id,DATE(r.start_datetime) as start_date,DATE(r.end_datetime) as end_date,r.facility_id,o.user_id,op.product_id FROM orders AS o INNER JOIN rents as r ON r.order_id=o.id INNER JOIN order_products as op ON op.order_id=o.id LEFT JOIN order_ends as oe ON oe.order_id=o.id WHERE DATE(r.start_datetime)<="'.$reference_date.'" AND DATE(r.end_datetime)>="'.$reference_date.'" AND oe.id is NULL AND o.enable=1 AND o.user_id=:user_id ORDER BY DATE(r.end_datetime) DESC LIMIT 1');
    $stmt_select_rsw = $pdo->prepare('SELECT rs.id,rs.order_id,rs.start_date,rs.end_date,o.user_id,op.product_id FROM orders AS o INNER JOIN rent_sws as rs ON rs.order_id=o.id INNER JOIN order_products as op ON op.order_id=o.id LEFT JOIN order_ends as oe ON oe.order_id=o.id WHERE rs.start_date<="'.$reference_date.'" AND rs.end_date>="'.$reference_date.'"  AND o.enable=1 AND oe.id is NULL AND o.user_id=:user_id ORDER BY rs.end_date DESC LIMIT 1');
    $stmt_select_course = $pdo->prepare('SELECT * FROM courses as c INNER JOIN products as p ON c.product_id=p.id WHERE c.id=:id AND p.enable=1');

    $stmt_insert_order = $pdo->prepare('INSERT INTO orders(branch_id,user_id,transaction_date,created_at,updated_at) VALUES(:branch_id,:user_id,CURDATE(),NOW(),NOW())');
    $stmt_insert_order_product = $pdo->prepare('INSERT INTO order_products(order_id,product_id) VALUES(:order_id,:product_id)');

    $stmt_insert_enroll = $pdo->prepare('INSERT INTO enrolls(order_id,course_id,type,insert_quantity,start_date,end_date,have_datetime) VALUES(:order_id,:course_id,:type,:insert_quantity,:start_date,:end_date,NOW())');
    $stmt_insert_rent = $pdo->prepare('INSERT INTO rents(order_id,facility_id,no,start_datetime,end_datetime) VALUES(:order_id,:facility_id,0,:start_datetime,:end_datetime)');
    $stmt_insert_rsw= $pdo->prepare('INSERT INTO rent_sws(order_id,start_date,end_date) VALUES(:order_id,:start_date,:end_date)');

    $stmt_insert_account= $pdo->prepare('INSERT INTO accounts(account_category_id,branch_id,user_id,transaction_date,created_at,updated_at) VALUES(1,:branch_id,:user_id,CURDATE(),NOW(),NOW())');
    $stmt_insert_account_order = $pdo->prepare('INSERT INTO account_orders(account_id,order_id) VALUES(:account_id,:order_id)');
    $stmt_insert_account_product = $pdo->prepare('INSERT INTO account_products(account_id,product_id) VALUES(:account_id,:product_id)');
    
    $stmt_memo_insert = $pdo->prepare('INSERT INTO user_contents(user_id,content,created_at,updated_at) VALUES(:user_id,:content,NOW(),NOW())');

    // 트랜잭션 시작
    $pdo->beginTransaction();
    $insert_count = 0;
    $n_obj=new DateTime('now');
    $updated_at=$n_obj->format('Y-m-d H:i:s');


    $stmt_select_course->bindParam(':id', $course_id, PDO::PARAM_INT);
    $stmt_select_course->execute();
    $course = $stmt_select_course->fetch(PDO::FETCH_ASSOC);

    $product_id=$course['product_id'];
    //$quantity=$course['lesson_period'];

    //if($course['lesson_period_unit']=='M') {
    //    $type='month';
    //} else {
        $type='day';
        $quantity=$plus_date;
    //}

    foreach ($lists as $value) {
        $stmt_count_stopping->bindParam(':id', $value['id'], PDO::PARAM_INT);
        $stmt_count_stopping->execute();
        $count_stopping = $stmt_count_stopping->fetchColumn();

        if(!empty($count_stopping)) {
            sl_log($log, 'user :'.$value['id'].' continue');
            continue;
        }

        $stmt_count_enroll->bindParam(':user_id', $value['id'], PDO::PARAM_INT);
        $stmt_count_enroll->execute();
        $count_enroll = $stmt_count_enroll->fetchColumn();
        
        if (!empty($count_enroll)) {
            sl_log($log, 'user :'.$value['id'].' enroll will insert');

            $stmt_select_enroll->bindParam(':user_id', $value['id'], PDO::PARAM_INT);
            $stmt_select_enroll->execute();
            $enroll = $stmt_select_enroll->fetch(PDO::FETCH_ASSOC);

            $user_id=$enroll['user_id'];
            $dateObj=new DateTime($enroll['end_date'],$dateTimeZone);
            $dateObj->modify('+1 day');

            $start_date=$dateObj->format('Y-m-d');

            $modify_text='+'.($plus_date-1).' days';

            $dateObj->modify($modify_text);
            $end_date=$dateObj->format('Y-m-d');

            $stmt_insert_order->bindParam(':branch_id',$branch_id, PDO::PARAM_INT);
            $stmt_insert_order->bindParam(':user_id',$user_id, PDO::PARAM_INT);
            $stmt_insert_order->execute();
            $order_id = $pdo->lastInsertId();

            $stmt_insert_enroll->bindParam(':order_id',$order_id, PDO::PARAM_INT);
            $stmt_insert_enroll->bindParam(':course_id',$course_id, PDO::PARAM_INT);
            $stmt_insert_enroll->bindParam(':type',$type, PDO::PARAM_STR);
            $stmt_insert_enroll->bindParam(':insert_quantity',$quantity, PDO::PARAM_INT);
            $stmt_insert_enroll->bindParam(':start_date',$start_date, PDO::PARAM_STR);
            $stmt_insert_enroll->bindParam(':end_date',$end_date, PDO::PARAM_STR);
            $stmt_insert_enroll->execute();
            $enroll_id = $pdo->lastInsertId();

            $stmt_insert_order_product->bindParam(':order_id',$order_id, PDO::PARAM_INT);
            $stmt_insert_order_product->bindParam(':product_id',$product_id, PDO::PARAM_INT);
            $stmt_insert_order_product->execute();

            $stmt_insert_account->bindParam(':branch_id',$branch_id, PDO::PARAM_INT);
            $stmt_insert_account->bindParam(':user_id',$user_id, PDO::PARAM_INT);
            $stmt_insert_account->execute();
            $account_id = $pdo->lastInsertId();

            $stmt_insert_account_order->bindParam(':account_id',$account_id, PDO::PARAM_INT);
            $stmt_insert_account_order->bindParam(':order_id',$order_id, PDO::PARAM_INT);
            $stmt_insert_account_order->execute();

            $stmt_insert_account_product->bindParam(':account_id',$account_id, PDO::PARAM_INT);
            $stmt_insert_account_product->bindParam(':product_id',$product_id, PDO::PARAM_INT);
            $stmt_insert_account_product->execute();

            sl_log($log, 'enroll_id :'. $enroll_id.' inserted');
            $insert_count++;
        }

        $stmt_count_rent->bindParam(':user_id', $value['id'], PDO::PARAM_INT);
        $stmt_count_rent->execute();
        $count_rent = $stmt_count_rent->fetchColumn();

        if (!empty($count_rent)) {
            sl_log($log, 'user :'.$value['id'].' rent will insert');

            $stmt_select_rent->bindParam(':user_id', $value['id'], PDO::PARAM_INT);
            $stmt_select_rent->execute();
            $rent = $stmt_select_rent->fetch(PDO::FETCH_ASSOC);

            $user_id=$rent['user_id'];
            $dateObj=new DateTime($rent['end_date'],$dateTimeZone);
            $dateObj->modify('+1 day');

            $start_date=$dateObj->format('Y-m-d');

            $modify_text='+'.($plus_date-1).' days';

            $dateObj->modify($modify_text);
            $end_date=$dateObj->format('Y-m-d');

            $facility_id=$rent['facility_id'];

            $stmt_insert_order->bindParam(':branch_id',$branch_id, PDO::PARAM_INT);
            $stmt_insert_order->bindParam(':user_id',$user_id, PDO::PARAM_INT);
            $stmt_insert_order->execute();
            $order_id = $pdo->lastInsertId();

            $start_datetime=$start_date.' 00:00:01';
            $end_datetime=$end_date.' 23:59:59';

            $stmt_insert_rent->bindParam(':order_id',$order_id, PDO::PARAM_INT);
            $stmt_insert_rent->bindParam(':facility_id',$facility_id, PDO::PARAM_INT);
            $stmt_insert_rent->bindParam(':start_datetime',$start_datetime, PDO::PARAM_STR);
            $stmt_insert_rent->bindParam(':end_datetime',$end_datetime, PDO::PARAM_STR);
            $stmt_insert_rent->execute();
            $rent_id = $pdo->lastInsertId();

            $stmt_insert_order_product->bindParam(':order_id',$order_id, PDO::PARAM_INT);
            $stmt_insert_order_product->bindParam(':product_id',$rent['product_id'], PDO::PARAM_INT);
            $stmt_insert_order_product->execute();

            $stmt_insert_account->bindParam(':branch_id',$branch_id, PDO::PARAM_INT);
            $stmt_insert_account->bindParam(':user_id',$user_id, PDO::PARAM_INT);
            $stmt_insert_account->execute();
            $account_id = $pdo->lastInsertId();

            $stmt_insert_account_order->bindParam(':account_id',$account_id, PDO::PARAM_INT);
            $stmt_insert_account_order->bindParam(':order_id',$order_id, PDO::PARAM_INT);
            $stmt_insert_account_order->execute();

            $stmt_insert_account_product->bindParam(':account_id',$account_id, PDO::PARAM_INT);
            $stmt_insert_account_product->bindParam(':product_id',$rent['product_id'], PDO::PARAM_INT);
            $stmt_insert_account_product->execute();


            sl_log($log, 'rent_id :'. $rent_id.' inserted');
            $insert_count++;
        }

        $stmt_count_rsw->bindParam(':user_id', $value['id'], PDO::PARAM_INT);
        $stmt_count_rsw->execute();
        $count_rsw = $stmt_count_rsw->fetchColumn();

        if (!empty($count_rsw)) {
            sl_log($log, 'user :'.$value['id'].' rsw will insert');

            $stmt_select_rsw->bindParam(':user_id', $value['id'], PDO::PARAM_INT);
            $stmt_select_rsw->execute();
            $rsw = $stmt_select_rsw->fetch(PDO::FETCH_ASSOC);

            $user_id=$rsw['user_id'];
            $dateObj=new DateTime($rsw['end_date'],$dateTimeZone);
            $dateObj->modify('+1 day');

            $start_date=$dateObj->format('Y-m-d');

            $modify_text='+'.($plus_date-1).' days';

            $dateObj->modify($modify_text);
            $end_date=$dateObj->format('Y-m-d');

            $stmt_insert_order->bindParam(':branch_id',$branch_id, PDO::PARAM_INT);
            $stmt_insert_order->bindParam(':user_id',$user_id, PDO::PARAM_INT);
            $stmt_insert_order->execute();
            $order_id = $pdo->lastInsertId();

            $stmt_insert_rsw->bindParam(':order_id',$order_id, PDO::PARAM_INT);
            $stmt_insert_rsw->bindParam(':start_date',$start_date, PDO::PARAM_STR);
            $stmt_insert_rsw->bindParam(':end_date',$end_date, PDO::PARAM_STR);
            $stmt_insert_rsw->execute();
            $rsw_id = $pdo->lastInsertId();

            $stmt_insert_order_product->bindParam(':order_id',$order_id, PDO::PARAM_INT);
            $stmt_insert_order_product->bindParam(':product_id',$rsw['product_id'], PDO::PARAM_INT);
            $stmt_insert_order_product->execute();

            $stmt_insert_account->bindParam(':branch_id',$branch_id, PDO::PARAM_INT);
            $stmt_insert_account->bindParam(':user_id',$user_id, PDO::PARAM_INT);
            $stmt_insert_account->execute();
            $account_id = $pdo->lastInsertId();

            $stmt_insert_account_order->bindParam(':account_id',$account_id, PDO::PARAM_INT);
            $stmt_insert_account_order->bindParam(':order_id',$order_id, PDO::PARAM_INT);
            $stmt_insert_account_order->execute();

            $stmt_insert_account_product->bindParam(':account_id',$account_id, PDO::PARAM_INT);
            $stmt_insert_account_product->bindParam(':product_id',$rsw['product_id'], PDO::PARAM_INT);
            $stmt_insert_account_product->execute();

            sl_log($log, 'rsw :'. $rsw_id.' inserted');
            $insert_count++;
        }

        if (!empty($count_enroll) or !empty($count_rent) or !empty($count_rsw)) {
            $stmt_memo_insert->bindParam(':user_id', $value['id'], PDO::PARAM_INT);
            $stmt_memo_insert->bindParam(':content', $content, PDO::PARAM_STR);
            $stmt_memo_insert->execute();
        }
    }

    $stmt_count_stopping->closeCursor();

    $stmt_count_enroll->closeCursor();
    $stmt_count_rent->closeCursor();
    $stmt_count_rsw->closeCursor();

    $stmt_select_enroll->closeCursor();
    $stmt_select_rent->closeCursor(); 
    $stmt_select_rsw->closeCursor();

    $stmt_insert_order->closeCursor();
    $stmt_insert_order_product->closeCursor();

    $stmt_insert_enroll->closeCursor();
    $stmt_insert_rent->closeCursor();
    $stmt_insert_rsw->closeCursor();

    $stmt_insert_account->closeCursor();
    $stmt_insert_account_order->closeCursor();
    $stmt_insert_account_product->closeCursor();

    $stmt_memo_insert->closeCursor();

    echo $insert_count."\n";

    // 커밋
    $pdo->commit();
    $pdo = null;

    echo 'success'."\n";
} catch (Exception $e) {
    include __DIR__.DIRECTORY_SEPARATOR.'common_catch.php';
}

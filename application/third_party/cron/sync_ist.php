<?php

/* Ist 카드리더기 입출입정보 처리를 위한 스크립트입니다 */

try {
    $log_file = basename(__FILE__, '.php').'.log';
    include __DIR__.DIRECTORY_SEPARATOR.'common_head.php';

    $dbh = $pdo;
    unset($pdo);

    include __DIR__.DIRECTORY_SEPARATOR.'sync_ist_header.php';
    include __DIR__.DIRECTORY_SEPARATOR.'sync_ist_common.php';
    ini_set('memory_limit', '256M');

    if (empty($argv[1])) {
        echo 'branch_id need / example) php '.basename(__FILE__, '.php').' branch_id type'."\n";

        return true;
    }

    $stmt_enroll_count = $dbh->prepare('SELECT count(*) FROM (SELECT count(*) FROM enrolls AS e INNER JOIN orders As o ON e.order_id=o.id INNER JOIN order_products AS op ON op.order_id=o.id INNER JOIN product_relations as pr ON pr.product_id=op.product_id INNER JOIN users AS u ON o.user_id=u.id INNER JOIN user_access_cards AS uac ON uac.user_id=u.id WHERE o.branch_id=:branch_id AND o.enable=1 AND u.enable=1 AND o.stopped=0 AND (e.start_date<=CURDATE() AND e.end_date>=CURDATE()) AND pr.product_relation_type_id=:product_relation_type_id GROUP BY u.id) AS g_table');
    $stmt_enroll = $dbh->prepare('SELECT o.*,e.start_date,max(e.end_date) as end_date,u.name,uac.card_no,"000" as update_id FROM enrolls AS e INNER JOIN orders As o ON e.order_id=o.id INNER JOIN order_products AS op ON op.order_id=o.id INNER JOIN product_relations as pr ON pr.product_id=op.product_id INNER JOIN users AS u ON o.user_id=u.id INNER JOIN user_access_cards AS uac ON uac.user_id=u.id WHERE o.branch_id=:branch_id AND o.enable=1 AND u.enable=1 AND o.stopped=0 AND (e.start_date<=CURDATE() AND e.end_date>=CURDATE()) AND pr.product_relation_type_id=:product_relation_type_id GROUP BY u.id ORDER BY o.id asc');

    // 해당 서버 DB돌면서 접속 가져오기
    foreach ($ac_list as $access_controll) {
        try {
            $pdo = new PDO($db[$access_controll['connection']]['dsn'], $db[$access_controll['connection']]['username'], $db[$access_controll['connection']]['password']);
            $pdo->setAttribute(PDO::SQLSRV_ATTR_ENCODING, PDO::SQLSRV_ENCODING_UTF8);
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            sl_log($log, 'DB connection error :'.$e->getLine().', message:'.$e->getMessage());
            continue;
        }
        
        $stmt_enroll_count->bindValue(':product_relation_type_id', PRIMARY_COURSE_ID);
        $stmt_enroll_count->bindParam(':branch_id', $access_controll['branch_id'], PDO::PARAM_INT);
        $stmt_enroll_count->execute();
        $count = $stmt_enroll_count->fetchColumn();
        
        if (empty($count)) {
            continue;
        }
        
        $stmt_enroll->bindValue(':product_relation_type_id', PRIMARY_COURSE_ID);
        $stmt_enroll->bindParam(':branch_id', $access_controll['branch_id'], PDO::PARAM_INT);
        $stmt_enroll->execute();
        $list = $stmt_enroll->fetchAll(PDO::FETCH_ASSOC);

        $stmt_aci_count->bindParam(':access_controller_id', $access_controll['id'], PDO::PARAM_INT);
        $stmt_aci_count->execute();
        $aci_count = $stmt_aci_count->fetchColumn();

        if (empty($aci_count)) {
            continue;
        }

        $stmt_aci_data->bindParam(':access_controller_id', $access_controll['id'], PDO::PARAM_INT);
        $stmt_aci_data->execute();
        $aci_list = $stmt_aci_data->fetchAll(PDO::FETCH_ASSOC);

        $stmt_a = prepare_stmt($pdo);

        // 트랜잭션 시작
        $pdo->beginTransaction();

        $change_exist = false;

        foreach ($list as $value) {
            if (empty(check_valid_ist_card_no($value['card_no']))) {
                continue;
            }

            $value['aci_list'] = $aci_list;
            $change_exist = insert_dp($stmt_a, $value, $log);
        }

        // 변경사항 있으면 ACU에 적용
        if ($change_exist) {
            $change_exist = insert_cc($stmt_a, $value, $log);
        }

        // 커밋
        $pdo->commit();
        $pdo = null;
    }

    $stmt_aci_count->closeCursor();
    $stmt_aci_data->closeCursor();

    $dbh = null;
} catch (Exception $e) {
    include __DIR__.DIRECTORY_SEPARATOR.'common_catch.php';
}

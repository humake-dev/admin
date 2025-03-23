<?php

/* Ist 카드리더기 DB에 회원정보 동기화하는 스크립트입니다 */

try {
    $log_file = basename(__FILE__, '.php').'.log';
    include __DIR__.DIRECTORY_SEPARATOR.'common_head.php';

    $dbh = $pdo;
    unset($pdo);

    include __DIR__.DIRECTORY_SEPARATOR.'sync_ist_header.php';
    include __DIR__.DIRECTORY_SEPARATOR.'sync_ist_common.php';
    ini_set('memory_limit', '256M');

    $stmt_select_count = $dbh->prepare('SELECT count(*) FROM order_access_controll_schedules AS oacs INNER JOIN orders AS o ON oacs.order_id=o.id INNER JOIN enrolls AS e ON e.order_id=o.id INNER JOIN users AS u ON o.user_id=u.id WHERE oacs.execute=0 AND oacs.schedule_date<=CURDATE() AND o.branch_id=:branch_id');
    $stmt_select = $dbh->prepare('SELECT oacs.*,o.user_id,u.name,uac.card_no,"000" AS update_id,e.start_date,e.end_date FROM order_access_controll_schedules AS oacs INNER JOIN orders AS o ON oacs.order_id=o.id INNER JOIN enrolls AS e ON e.order_id=o.id INNER JOIN users AS u ON o.user_id=u.id  LEFT JOIN user_access_cards as uac ON uac.user_id=u.id WHERE oacs.execute=0 AND oacs.schedule_date<=curdate() AND o.branch_id=:branch_id');

    $stmt_update_oasc = $dbh->prepare('UPDATE order_access_controll_schedules AS oacs SET `execute`=1 WHERE oacs.id=:id');

    // 해당 서버 DB돌면서 접속 가져오기
    foreach ($ac_list as $access_controll) {
        $stmt_select_count->bindParam(':branch_id', $access_controll['branch_id'], PDO::PARAM_INT);
        $stmt_select_count->execute();
        $count = $stmt_select_count->fetchColumn();

        if (empty($count)) {
            sl_log($log, 'branch '.$access_controll['branch_id'].':  count user 0 exit');
            continue;
        }

        $stmt_select->bindParam(':branch_id', $access_controll['branch_id'], PDO::PARAM_INT);
        $stmt_select->execute();
        $list = $stmt_select->fetchAll(PDO::FETCH_ASSOC);

        try {
            $pdo = new PDO($db[$access_controll['connection']]['dsn'], $db[$access_controll['connection']]['username'], $db[$access_controll['connection']]['password']);
            $pdo->setAttribute(PDO::SQLSRV_ATTR_ENCODING, PDO::SQLSRV_ENCODING_UTF8);
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            sl_log($log, 'DB connection error :'.$e->getLine().', message:'.$e->getMessage());
            continue;
        }

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

            $stmt_update_oasc->bindParam(':id', $value['id'], PDO::PARAM_INT);
            $stmt_update_oasc->execute();

            sl_log($log, 'oasc id : '.$value['id'].' updated');
        }

        // 변경사항 있으면 ACU에 적용
        if ($change_exist) {
            insert_cc($stmt_a, $value, $log);
        }

        // 커밋
        $pdo->commit();
        $pdo = null;
    }

    $stmt_select_count->closeCursor();
    $stmt_select->closeCursor();
    $stmt_update_oasc->closeCursor();
    $stmt_aci_count->closeCursor();
    $stmt_aci_data->closeCursor();

    $dbh = null;

    echo 'success'."\n";
} catch (Exception $e) {
    include __DIR__.DIRECTORY_SEPARATOR.'common_catch.php';
}

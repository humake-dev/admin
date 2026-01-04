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

    $stmt_branch_count = $dbh->prepare('SELECT count(*) FROM branches as b WHERE b.id =:branch_id');
    $stmt_branch_select = $dbh->prepare('SELECT center_id FROM branches as b WHERE b.id =:branch_id');

    $stmt_branch_count->bindParam(':branch_id', $branch_id, PDO::PARAM_INT);
    $stmt_branch_count->execute();
    $branch_count = $stmt_branch_count->fetchColumn();

    if (empty($branch_count)) {
        sl_log($log, 'branch '.$access_controll['branch_id'].':  count branch 0 exit');
        exit;
    }

    $stmt_branch_select->bindParam(':branch_id', $branch_id, PDO::PARAM_INT);
    $stmt_branch_select->execute();
    $center_id = $stmt_branch_select->fetchColumn();

    $stmt_role2_count = $dbh->prepare('SELECT count(*) FROM admins as a INNER JOIN branches as b ON b.id=a.branch_id LEFT JOIN admin_access_cards AS aac ON aac.admin_id=a.id WHERE a.role_id = 2 AND b.center_id=:center_id');
    $stmt_role2_count->bindParam(':center_id', $center_id, PDO::PARAM_INT);
    $stmt_role2_count->execute();
    $role2_count = $stmt_role2_count->fetchColumn();

    if(!empty($role2_count)) {
        $stmt_role2_select = $dbh->prepare('SELECT a.*,aac.card_no,"000" as update_id FROM admins as a INNER JOIN branches as b ON b.id=a.branch_id LEFT JOIN admin_access_cards AS aac ON aac.admin_id=a.id WHERE a.role_id = 2 AND b.center_id=:center_id');
        $stmt_role2_select->bindParam(':center_id', $center_id, PDO::PARAM_INT);
        $stmt_role2_select->execute();
        $role2_list = $stmt_role2_select->fetchAll(PDO::FETCH_ASSOC);
    }

    $stmt_count = $dbh->prepare('SELECT count(*) FROM admins WHERE branch_id=:branch_id');
    $stmt_select = $dbh->prepare('SELECT a.*,aac.card_no,"000" as update_id FROM admins AS a LEFT JOIN admin_access_cards AS aac ON aac.admin_id=a.id WHERE a.branch_id=:branch_id ORDER BY a.id');

    // 해당 서버 DB돌면서 접속 가져오기
    foreach ($ac_list as $access_controll) {
        $stmt_count->bindParam(':branch_id', $access_controll['branch_id'], PDO::PARAM_INT);
        $stmt_count->execute();
        $count = $stmt_count->fetchColumn();

        if (empty($count)) {
            sl_log($log, 'branch '.$access_controll['branch_id'].':  count user 0 exit');
            continue;
        }

        $stmt_select->bindParam(':branch_id', $access_controll['branch_id'], PDO::PARAM_INT);
        $stmt_select->execute();
        $list = $stmt_select->fetchAll(PDO::FETCH_ASSOC);

        if(!empty($role2_list)) {
            $list=array_merge($list, $role2_list);
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

        try {
            $pdo = new PDO($db[$access_controll['connection']]['dsn'], $db[$access_controll['connection']]['username'], $db[$access_controll['connection']]['password']);
            $pdo->setAttribute(PDO::SQLSRV_ATTR_ENCODING, PDO::SQLSRV_ENCODING_UTF8);
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            sl_log($log, 'DB connection error :'.$e->getLine().', message:'.$e->getMessage());
            continue;
        }

        $stmt_a = prepare_stmt($pdo);

        // 트랜잭션 시작
        $pdo->beginTransaction();

        $change_exist = false;
        foreach ($list as $value) {
            if (empty(check_valid_ist_card_no($value['card_no']))) {
                continue;
            }

            $value['user_id'] = $value['id'];
            $value['start_date'] = '2010-01-01';
            $value['end_date'] = '2050-12-31';
            if (isset($value['hiring_date'])) {
                $value['start_date'] = $value['hiring_date'];
            }

            $value['aci_list'] = $aci_list;
            $change_exist = insert_dp($stmt_a, $value, $log);
        }

        // 변경사항 있으면 ACU에 적용
        if ($change_exist) {
            insert_cc($stmt_a, $value, $log);
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

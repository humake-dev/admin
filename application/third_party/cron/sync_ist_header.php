<?php

    // Ist 컨트롤러 갯수 구하기
    if (isset($branch_id)) {
        $stmt_ac_count = $dbh->prepare('SELECT count(*) FROM access_controllers AS ac INNER JOIN branches AS b ON ac.branch_id=b.id WHERE ac.enable=1 AND b.use_admin_ac=1 AND b.enable=1 AND b.id=:branch_id');
        $stmt_ac_count->bindParam(':branch_id', $branch_id, PDO::PARAM_INT);
    } else {
        $stmt_ac_count = $dbh->prepare('SELECT count(*) FROM access_controllers AS ac INNER JOIN branches AS b ON ac.branch_id=b.id WHERE ac.enable=1 AND b.use_admin_ac=1 AND b.enable=1');
    }
    $stmt_ac_count->execute();
    $ac_count = $stmt_ac_count->fetchColumn();

    // 없으면 끝냄
    if (empty($ac_count)) {
        sl_log($log, 'access controller not exists');
        exit;
    }

    if (isset($branch_id)) {
        $stmt_ac_select = $dbh->prepare('SELECT ac.* FROM access_controllers AS ac INNER JOIN branches AS b ON ac.branch_id=b.id WHERE ac.enable=1 AND b.use_admin_ac=1 AND b.enable=1 AND b.id=:branch_id');
        $stmt_ac_select->bindParam(':branch_id', $branch_id, PDO::PARAM_INT);
    } else {
        $stmt_ac_select = $dbh->prepare('SELECT ac.* FROM access_controllers AS ac INNER JOIN branches AS b ON ac.branch_id=b.id WHERE ac.enable=1 AND b.use_admin_ac=1 AND b.enable=1');
    }

    $stmt_ac_select->execute();
    $ac_list = $stmt_ac_select->fetchAll(PDO::FETCH_ASSOC);

    $stmt_aci_count = $dbh->prepare('SELECT count(*) FROM access_controller_ist WHERE access_controller_id=:access_controller_id AND enable=1');
    $stmt_aci_data = $dbh->prepare('SELECT INET_NTOA(send_ip) AS send_ip,INET_NTOA(dest_ip) AS dest_ip,device_id FROM access_controller_ist WHERE access_controller_id=:access_controller_id AND enable=1');

<?php

/* ist 입출입기 DB 회원정보 초기화 스크립트입니다 */

try {
    $log_file = basename(__FILE__, '.php').'.log';
    include __DIR__.DIRECTORY_SEPARATOR.'common_head.php';

    if(ENVIRONMENT=='production') {
        echo 'this is production environment exit';
        exit;
    }

    if (empty($argv[1])) {
        echo 'branch_id need / example) php delete_data_person.php branch_id'."\n";

        return true;
    }

    $stmt_ac_select_count = $pdo->prepare('SELECT count(*) FROM access_controllers as ac INNER JOIN access_controller_ist as aci ON aci.access_controller_id=ac.id WHERE ac.branch_id=:branch_id AND ac.enable=1');
    $stmt_ac_select_count->bindParam(':branch_id', $branch_id, PDO::PARAM_INT);
    $stmt_ac_select_count->execute();
    $count = $stmt_ac_select_count->fetchColumn();
    $stmt_ac_select_count->closeCursor();

    if (!$count) {
        echo 'branch not exists'."\n";
        return false;
    }

    $stmt_ac_select = $pdo->prepare('SELECT connection FROM access_controllers as ac INNER JOIN access_controller_ist as aci ON aci.access_controller_id=ac.id WHERE ac.branch_id=:branch_id AND ac.enable=1');
    $stmt_ac_select->bindParam(':branch_id', $branch_id, PDO::PARAM_INT);
    $stmt_ac_select->execute();
    $ac_content = $stmt_ac_select->fetch(PDO::FETCH_ASSOC);
    $stmt_ac_select->closeCursor();

    $pdo = null;

    // 카드리더기통제 DB 접속정보
    $dbs = new PDO($db[$ac_content['connection']]['dsn'], $db[$ac_content['connection']]['username'], $db[$ac_content['connection']]['password']);
    $dbs->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    set_time_limit(0);

    $stmt_delete1 = $dbs->prepare('DELETE FROM Data_Person');
    $stmt_delete2 = $dbs->prepare('DELETE FROM Data_Person_Cardholder');
    $stmt_delete3 = $dbs->prepare('DELETE FROM Comm_Send_Packet');
    $stmt_delete4 = $dbs->prepare('DELETE FROM Comm_Control');

    // 트랜잭션 시작
    $dbs->beginTransaction();

    $stmt_delete1->execute();
    $stmt_delete1->closeCursor();

    $stmt_delete2->execute();
    $stmt_delete2->closeCursor();

    $stmt_delete3->execute();
    $stmt_delete3->closeCursor();

    $stmt_delete4->execute();
    $stmt_delete4->closeCursor();

    // 커밋
    $dbs->commit();
    $dbs = null;

    echo 'success'."\n";
} catch (Exception $e) {
    include __DIR__.DIRECTORY_SEPARATOR.'common_catch.php';
}

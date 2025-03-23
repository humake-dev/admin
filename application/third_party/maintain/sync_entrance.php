<?php

/* ist 카드리더기 입출입정보 동기화하는 스크립트입니다 */

try {
    $log_file = basename(__FILE__, '.php').'.log';
    include __DIR__.DIRECTORY_SEPARATOR.'common_head.php';

    if (empty($argv[1])) {
        echo 'branch_id need / example) php '.basename(__FILE__, '.php').' branch_id type'."\n";

        return true;
    }

    // ist 컨트롤러 갯수 구하기
    if (isset($branch_id)) {
        $stmt_count = $pdo->prepare('SELECT count(*) FROM access_controllers WHERE branch_id=:branch_id AND enable=1');
        $stmt_count->bindParam(':branch_id', $branch_id, PDO::PARAM_INT);
    } else {
        $stmt_count = $pdo->prepare('SELECT count(*) FROM access_controllers WHERE enable=1');
    }
    $stmt_count->execute();
    $count = $stmt_count->fetchColumn();
    $stmt_count->closeCursor();

    if (!$count) {  // 없으면 끝냄
        $log->addInfo('update_user_entrance execute, 0');

        return true;
    }

    if (isset($branch_id)) {
        $stmt_ac_select = $pdo->prepare('SELECT * FROM access_controllers WHERE branch_id=:branch_id AND enable=1');
        $stmt_ac_select->bindParam(':branch_id', $branch_id, PDO::PARAM_INT);
    } else {
        $stmt_ac_select = $pdo->prepare('SELECT * FROM access_controllers WHERE enable=1');
    }
    $stmt_ac_select->execute();
    $ac_list = $stmt_ac_select->fetchAll(PDO::FETCH_ASSOC);
    $stmt_ac_select->closeCursor();

    $stmt_ue_count = $pdo->prepare('SELECT count(*) FROM entrances AS ue INNER JOIN users AS u ON ue.user_id=u.id WHERE u.branch_id=:branch_id');
    $stmt_ue = $pdo->prepare('SELECT in_time FROM entrances AS ue INNER JOIN users AS u ON ue.user_id=u.id WHERE u.branch_id=:branch_id ORDER BY ue.id DESC LIMIT 1');
    $stmt_insert = $pdo->prepare('INSERT INTO entrances(user_id,in_time,created_at) VALUES(:user_id,:in_time,NOW())');
    $stmt_u_count = $pdo->prepare('SELECT count(*) FROM users WHERE card_no=:card_no AND branch_id=:branch_id');
    $stmt_u = $pdo->prepare('SELECT id FROM users WHERE card_no=:card_no AND branch_id=:branch_id');

    // 해당 서버 DB돌면서 접속 가져오기
    foreach ($ac_list as $access_controll) {
        $dbs = new PDO($db[$access_controll['connection']]['dsn'], $db[$access_controll['connection']]['username'], $db[$access_controll['connection']]['password']);
        $dbs->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $time_check = true;

        // 출입 승인 이벤트 기록이 없으면 다음껄로
        $stmt_count = $dbs->prepare("SELECT count(*) FROM Log_Device_Normal WHERE LogType='0' AND LogStatus='0'");
        $stmt_count->execute();
        $count = $stmt_count->fetchColumn();
        $stmt_count->closeCursor();

        if (!$count) {
            $log->addInfo('Not IN '.$access_controll['host']);
            $dbs = null;
            continue;
        }

        $stmt_t_time = $dbs->prepare("SELECT LogDate,LogTime FROM Log_Device_Normal WHERE LogType='0' AND LogStatus='0' ORDER BY LogIDX DESC");
        $stmt_t_time->execute();
        $t_time = $stmt_t_time->fetchColumn();
        $stmt_t_time->closeCursor();

        // 기존기록없으면 증가시간 확인 안함 = 무조건 입력
        $stmt_ue_count->bindParam(':branch_id', $access_controll['branch_id'], PDO::PARAM_INT);
        $stmt_ue_count->execute();
        $ue_count = $stmt_ue_count->fetchColumn();
        $stmt_ue_count->closeCursor();

        if (!$ue_count) {
            $time_check = false;
        }

        // 서버에 입력된 마지막시간
        $stmt_ue->bindParam(':branch_id', $access_controll['branch_id'], PDO::PARAM_INT);
        $stmt_ue->execute();
        $ue_time = $stmt_ue->fetchColumn();
        $stmt_ue->closeCursor();

        // 추가 입력할 필요가 없으면 다음껄로
        if ($time_check) {
            if ($t_time <= $ue_time) {
                $log->addInfo('Already Sync');
                continue;
            }

            $at = explode(' ', $ue_time);
            $date = str_replace('-', '', $at[0]);
            $time = str_replace(':', '', $at[1]);

            $stmt_select = $dbs->prepare("SELECT * FROM Log_Device_Normal WHERE LogType='0' AND LogStatus='0' AND LogDate>:date AND LogTime>:time ORDER BY LogIDX");
            $stmt_select->bindParam(':date', $date, PDO::PARAM_INT);
            $stmt_select->bindParam(':time', $time, PDO::PARAM_INT);
            $stmt_select->execute();
            $list = $stmt_select->fetchAll(PDO::FETCH_ASSOC);
            $stmt_select->closeCursor();
        } else {
            $stmt_select = $dbs->prepare("SELECT * FROM Log_Device_Normal WHERE LogType='0' AND LogStatus='0' ORDER BY LogIDX");
            $stmt_select->execute();
            $list = $stmt_select->fetchAll(PDO::FETCH_ASSOC);
            $stmt_select->closeCursor();
        }

        $success = array();

        // 트랜잭션 시작
        $pdo->beginTransaction();

        foreach ($list as $index => $user_entrance) {
            if (strlen($user_entrance['LogPersonID']) < 10) {
                $log->addInfo('invalid Id:'.$user_entrance['LogPersonID']);
                continue;
            }

            $stmt_u_count->bindParam(':card_no', $user_entrance['LogPersonID'], PDO::PARAM_STR);
            $stmt_u_count->bindParam(':branch_id', $access_controll['branch_id'], PDO::PARAM_INT);
            $stmt_u_count->execute();
            $count = $stmt_u_count->fetchColumn();

            if (!$count) {
                continue;
            }

            $stmt_u->bindParam(':card_no', $user_entrance['LogPersonID'], PDO::PARAM_STR);
            $stmt_u->bindParam(':branch_id', $access_controll['branch_id'], PDO::PARAM_INT);
            $stmt_u->execute();
            $user = $stmt_u->fetchColumn();

            $date = new DateTime($user_entrance['LogDate'].$user_entrance['LogTime'], $dateTimeZone);
            $in_time = $date->format('Y-m-d H:i:s');

            $stmt_insert->bindParam(':user_id', $user, PDO::PARAM_INT);
            $stmt_insert->bindParam(':in_time', $in_time, PDO::PARAM_STR);
            $stmt_insert->execute();
            $success[] = $pdo->lastInsertId();
        }
        $stmt_u_count->closeCursor();
        $stmt_u->closeCursor();
        $stmt_insert->closeCursor();

        // 커밋
        $pdo->commit();
        $log->addInfo('update_user_entrance execute count:'.count($success));

        $dbs = null;
    }

    $pdo = null;

    echo 'success'."\n";
} catch (Exception $e) {
    include __DIR__.DIRECTORY_SEPARATOR.'common_catch.php';
}

<?php

    function prepare_stmt($pdo)
    {
        $stmt['stmt_dp_count'] = $pdo->prepare('SELECT count(*) FROM Data_Person WHERE PersonID=:id');
        $stmt['stmt_dp_all'] = $pdo->prepare('SELECT * FROM Data_Person');
        $stmt['stmt_dp_data'] = $pdo->prepare('SELECT * FROM Data_Person WHERE PersonID=:id');
        // $stmt_dp_delete=$pdo->prepare('DELETE FROM Data_Person WHERE PersonID=:id');
        $stmt['stmt_insert'] = $pdo->prepare('INSERT INTO Data_Person(
            PersonID,
            PersonFirstName,
            PersonLastName,
            CompanyID,
            CompanyName,
            DepartmentID,
            DepartmentName,
            TitleID,
            TitleName,
            PersonEmail,
            PersonOfficeNo,
            PersonHomeNo,
            PersonMobileNo,
            PersonSocialNo,
            PersonCarNo,
            PersonNationality,
            PersonAddress,
            PersonETC,
            PersonMemo,
            PersonRegistrationDate,
            PersonExpirationDate,
            LastUpdateLoginID,
            LastUpdateIP)
          VALUES(
            :id,
            :first_name,
            :last_name,
            :CompanyID,
            :CompanyName,
            :DepartmentID,
            :DepartmentName,
            :TitleID,
            :TitleName,
            :PersonEmail,
            :PersonOfficeNo,
            :PersonHomeNo,
            :PersonMobileNo,
            :PersonSocialNo,
            :PersonCarNo,
            :PersonNationality,
            :PersonAddress,
            :PersonETC,
            :PersonMemo,
            :reg_date,
            :expire_date,
            :last_update_login_id,
            :last_update_ip)
          ');
        $stmt['stmt_update'] = $pdo->prepare('UPDATE Data_Person SET PersonExpirationDate=:expire_date WHERE PersonID=:id');

        $stmt['stmt_count_dpc'] = $pdo->prepare('SELECT count(*) FROM Data_Person_Cardholder WHERE PersonID=:id');
        $stmt['stmt_dpc_data'] = $pdo->prepare('SELECT * FROM Data_Person_Cardholder WHERE PersonID=:id');
        $stmt['stmt_update_dpc'] = $pdo->prepare('UPDATE Data_Person_Cardholder SET IDValidDateEnd=:end_date WHERE PersonID=:id1 AND IDIndex=:id2');
        $stmt['stmt_insert_dpc'] = $pdo->prepare('INSERT INTO Data_Person_Cardholder
              (PersonID,IDIndex,IDRF,IDRFUSE,IDPassword,IDAccessRightID1,IDType,IDValidDateStart,IDValidDateEnd,
                IDCreditEnable,
                IDCredit,
                IDModeEnable,
                IDMode,
                IDStop,
                IDOutputEnable,
                IDOutputID,
                IDAccessLevel,
                IDAccessModeLevel,
                IDAccessPINLevel,
                IDAntipassbackEnable,
                IDAntipassbackMode, 
                IDAntipassbackLevel,
                IDAntipassbackModeLevel,
                IDArmDisarmLevel,
                IDVisitorLevel,
                LastUpdateLoginID,
                LastUpdateIP,
                IndiAccessRightUse,
                IDValidDateStartEnable,
                IDValidDateEndEnable)
            VALUES(:id,:idindex,:idrf,:idrfuse,:id_password,:id_access_right_id1,:id_type,:start_date,:end_date,
              :IDCreditEnable,
              :IDCredit,
              :IDModeEnable,
              :IDMode,
              :IDStop,
              :IDOutputEnable,
              :IDOutputID,
              :IDAccessLevel,
              :IDAccessModeLevel,
              :IDAccessPINLevel,
              :IDAntipassbackEnable,
              :IDAntipassbackMode, 
              :IDAntipassbackLevel,
              :IDAntipassbackModeLevel,
               :IDArmDisarmLevel,
               :IDVisitorLevel,
               :LastUpdateLoginID,
              :LastUpdateIP,
               :IndiAccessRightUse,
               :IDValidDateStartEnable,
               :IDValidDateEndEnable
            )');

        $stmt['stmt_insert_csp'] = $pdo->prepare('INSERT INTO Comm_Send_Packet(SenderIPAddress,DestIPAddress,SystemWorkID,DeviceID,PacketCommand,PacketData1,PacketData2,PacketStatus) VALUES(:sender_ip_address,:dest_ip_address,:system_work_id,:device_id,:packet_command,:packet_data1,:packet_data2,:packet_status)');
        $stmt['stmt_delete_cc'] = $pdo->prepare('DELETE FROM Comm_Control');
        $stmt['stmt_insert_cc'] = $pdo->prepare('INSERT INTO Comm_Control(SenderIPAddress,DestIPAddress,CommCommand,SystemWorkID,DestPort,DeviceID) VALUES(:sender_ip_address,:dest_ip_address,:comm_command,:system_work_id,:dest_port,:device_id)');

        return $stmt;
    }

    function insert_cc($stmt, $value, $log)
    {
        $stmt['stmt_delete_cc']->execute();

        foreach ($value['aci_list'] as $aci) {
            $stmt['stmt_insert_cc']->bindParam(':sender_ip_address', $aci['send_ip'], PDO::PARAM_STR);
            $stmt['stmt_insert_cc']->bindParam(':dest_ip_address', $aci['dest_ip'], PDO::PARAM_STR);
            $stmt['stmt_insert_cc']->bindValue(':comm_command', 'RSTART');
            $stmt['stmt_insert_cc']->bindValue(':system_work_id', '1');
            $stmt['stmt_insert_cc']->bindValue(':dest_port', '');
            $stmt['stmt_insert_cc']->bindParam(':device_id', $aci['device_id'], PDO::PARAM_STR);
            $stmt['stmt_insert_cc']->execute();
        }

        sl_log($log, 'Update Comm_Control Complete');

        return true;
    }

    function insert_dp($stmt, $value, $log)
    {
        // 변경 여부
        $change = false;
        $start_date = str_replace('-', '', $value['start_date']);
        $end_date = str_replace('-', '', $value['end_date']);

        // 존재여부 확인
        $stmt['stmt_dp_count']->bindParam(':id', $value['user_id'], PDO::PARAM_STR);
        $stmt['stmt_dp_count']->execute();
        $dp_count = $stmt['stmt_dp_count']->fetchColumn();

        if (empty($dp_count)) {
            $null = '';

            $stmt['stmt_insert']->bindParam(':id', $value['user_id'], PDO::PARAM_STR);
            $stmt['stmt_insert']->bindParam(':first_name', $value['name'], PDO::PARAM_STR);
            $stmt['stmt_insert']->bindParam(':last_name', $value['name'], PDO::PARAM_STR);
            $stmt['stmt_insert']->bindValue(':CompanyID', 0, PDO::PARAM_INT);
            $stmt['stmt_insert']->bindParam(':CompanyName', $null, PDO::PARAM_STR);
            $stmt['stmt_insert']->bindValue(':DepartmentID', 0, PDO::PARAM_INT);
            $stmt['stmt_insert']->bindParam(':DepartmentName', $null, PDO::PARAM_STR);
            $stmt['stmt_insert']->bindValue(':TitleID', 0, PDO::PARAM_INT);
            $stmt['stmt_insert']->bindParam(':TitleName', $null, PDO::PARAM_STR);
            $stmt['stmt_insert']->bindParam(':PersonEmail', $null, PDO::PARAM_STR);
            $stmt['stmt_insert']->bindParam(':PersonOfficeNo', $null, PDO::PARAM_STR);
            $stmt['stmt_insert']->bindParam(':PersonHomeNo', $null, PDO::PARAM_STR);
            $stmt['stmt_insert']->bindParam(':PersonMobileNo', $null, PDO::PARAM_STR);
            $stmt['stmt_insert']->bindParam(':PersonSocialNo', $null, PDO::PARAM_STR);
            $stmt['stmt_insert']->bindParam(':PersonCarNo', $null, PDO::PARAM_STR);
            $stmt['stmt_insert']->bindParam(':PersonNationality', $null, PDO::PARAM_STR);
            $stmt['stmt_insert']->bindParam(':PersonAddress', $null, PDO::PARAM_STR);
            $stmt['stmt_insert']->bindParam(':PersonETC', $null, PDO::PARAM_STR);
            $stmt['stmt_insert']->bindParam(':PersonMemo', $null, PDO::PARAM_STR);
            $stmt['stmt_insert']->bindParam(':reg_date', $start_date, PDO::PARAM_STR);
            $stmt['stmt_insert']->bindParam(':expire_date', $end_date, PDO::PARAM_STR);
            $stmt['stmt_insert']->bindValue(':last_update_login_id', 'admin');
            $stmt['stmt_insert']->bindValue(':last_update_ip', '127.0.0.1');
            $stmt['stmt_insert']->execute();

            sl_log($log, 'Insert Data_Person PersonID : '.$value['user_id'].' Complete');

            $stmt['stmt_insert_dpc']->bindParam(':id', $value['user_id'], PDO::PARAM_INT);
            $stmt['stmt_insert_dpc']->bindValue(':idindex', '1');
            $stmt['stmt_insert_dpc']->bindValue(':idrf', $value['card_no']);
            $stmt['stmt_insert_dpc']->bindValue(':idrfuse', '1');
            $stmt['stmt_insert_dpc']->bindValue(':id_password', '0000');
            $stmt['stmt_insert_dpc']->bindValue(':id_access_right_id1', $value['update_id']);
            $stmt['stmt_insert_dpc']->bindValue(':id_type', '0');
            $stmt['stmt_insert_dpc']->bindParam(':start_date', $start_date, PDO::PARAM_STR);
            $stmt['stmt_insert_dpc']->bindParam(':end_date', $end_date, PDO::PARAM_STR);
            $stmt['stmt_insert_dpc']->bindValue(':IDCreditEnable', '0');
            $stmt['stmt_insert_dpc']->bindValue(':IDCredit', '0');
            $stmt['stmt_insert_dpc']->bindValue(':IDModeEnable', '0');
            $stmt['stmt_insert_dpc']->bindValue(':IDMode', '0');
            $stmt['stmt_insert_dpc']->bindValue(':IDStop', '0');
            $stmt['stmt_insert_dpc']->bindValue(':IDOutputEnable', '0');
            $stmt['stmt_insert_dpc']->bindValue(':IDOutputID', '0');
            $stmt['stmt_insert_dpc']->bindValue(':IDAccessLevel', '3');
            $stmt['stmt_insert_dpc']->bindValue(':IDAccessModeLevel', '3');
            $stmt['stmt_insert_dpc']->bindValue(':IDAccessPINLevel', '3');
            $stmt['stmt_insert_dpc']->bindValue(':IDAntipassbackEnable', '0');
            $stmt['stmt_insert_dpc']->bindValue(':IDAntipassbackMode', '0');
            $stmt['stmt_insert_dpc']->bindValue(':IDAntipassbackLevel', '3');
            $stmt['stmt_insert_dpc']->bindValue(':IDAntipassbackModeLevel', '3');
            $stmt['stmt_insert_dpc']->bindValue(':IDArmDisarmLevel', '3');
            $stmt['stmt_insert_dpc']->bindValue(':IDVisitorLevel', '3');
            $stmt['stmt_insert_dpc']->bindValue(':LastUpdateLoginID', 'admin');
            $stmt['stmt_insert_dpc']->bindValue(':LastUpdateIP', '127.0.0.1');
            $stmt['stmt_insert_dpc']->bindValue(':IndiAccessRightUse', '0');
            $stmt['stmt_insert_dpc']->bindValue(':IDValidDateStartEnable', '1');
            $stmt['stmt_insert_dpc']->bindValue(':IDValidDateEndEnable', '1');
            $stmt['stmt_insert_dpc']->execute();

            $stmt['stmt_insert_dpc']->bindParam(':id', $value['user_id'], PDO::PARAM_INT);
            $stmt['stmt_insert_dpc']->bindValue(':idindex', '2');
            $stmt['stmt_insert_dpc']->bindValue(':idrf', '');
            $stmt['stmt_insert_dpc']->bindValue(':idrfuse', '0');
            $stmt['stmt_insert_dpc']->bindValue(':id_password', '0000');
            $stmt['stmt_insert_dpc']->bindValue(':id_access_right_id1', '');
            $stmt['stmt_insert_dpc']->bindValue(':id_type', '0');
            $stmt['stmt_insert_dpc']->bindParam(':start_date', $start_date, PDO::PARAM_STR);
            $stmt['stmt_insert_dpc']->bindParam(':end_date', $end_date, PDO::PARAM_STR);
            $stmt['stmt_insert_dpc']->bindValue(':IDCreditEnable', '0');
            $stmt['stmt_insert_dpc']->bindValue(':IDCredit', '0');
            $stmt['stmt_insert_dpc']->bindValue(':IDModeEnable', '0');
            $stmt['stmt_insert_dpc']->bindValue(':IDMode', '0');
            $stmt['stmt_insert_dpc']->bindValue(':IDStop', '1');
            $stmt['stmt_insert_dpc']->bindValue(':IDOutputEnable', '0');
            $stmt['stmt_insert_dpc']->bindValue(':IDOutputID', '0');
            $stmt['stmt_insert_dpc']->bindValue(':IDAccessLevel', '3');
            $stmt['stmt_insert_dpc']->bindValue(':IDAccessModeLevel', '3');
            $stmt['stmt_insert_dpc']->bindValue(':IDAccessPINLevel', '3');
            $stmt['stmt_insert_dpc']->bindValue(':IDAntipassbackEnable', '0');
            $stmt['stmt_insert_dpc']->bindValue(':IDAntipassbackMode', '0');
            $stmt['stmt_insert_dpc']->bindValue(':IDAntipassbackLevel', '3');
            $stmt['stmt_insert_dpc']->bindValue(':IDAntipassbackModeLevel', '3');
            $stmt['stmt_insert_dpc']->bindValue(':IDArmDisarmLevel', '3');
            $stmt['stmt_insert_dpc']->bindValue(':IDVisitorLevel', '3');
            $stmt['stmt_insert_dpc']->bindValue(':LastUpdateLoginID', 'admin');
            $stmt['stmt_insert_dpc']->bindValue(':LastUpdateIP', '127.0.0.1');
            $stmt['stmt_insert_dpc']->bindValue(':IndiAccessRightUse', '0');
            $stmt['stmt_insert_dpc']->bindValue(':IDValidDateStartEnable', '0');
            $stmt['stmt_insert_dpc']->bindValue(':IDValidDateEndEnable', '0');
            $stmt['stmt_insert_dpc']->execute();

            $stmt['stmt_insert_dpc']->bindParam(':id', $value['user_id'], PDO::PARAM_INT);
            $stmt['stmt_insert_dpc']->bindValue(':idindex', '3');
            $stmt['stmt_insert_dpc']->bindValue(':idrf', '');
            $stmt['stmt_insert_dpc']->bindValue(':idrfuse', '0');
            $stmt['stmt_insert_dpc']->bindValue(':id_password', '0000');
            $stmt['stmt_insert_dpc']->bindValue(':id_access_right_id1', '');
            $stmt['stmt_insert_dpc']->bindValue(':id_type', '0');
            $stmt['stmt_insert_dpc']->bindParam(':start_date', $start_date, PDO::PARAM_STR);
            $stmt['stmt_insert_dpc']->bindParam(':end_date', $end_date, PDO::PARAM_STR);
            $stmt['stmt_insert_dpc']->bindValue(':IDCreditEnable', '0');
            $stmt['stmt_insert_dpc']->bindValue(':IDCredit', '0');
            $stmt['stmt_insert_dpc']->bindValue(':IDModeEnable', '0');
            $stmt['stmt_insert_dpc']->bindValue(':IDMode', '0');
            $stmt['stmt_insert_dpc']->bindValue(':IDStop', '1');
            $stmt['stmt_insert_dpc']->bindValue(':IDOutputEnable', '0');
            $stmt['stmt_insert_dpc']->bindValue(':IDOutputID', '0');
            $stmt['stmt_insert_dpc']->bindValue(':IDAccessLevel', '3');
            $stmt['stmt_insert_dpc']->bindValue(':IDAccessModeLevel', '3');
            $stmt['stmt_insert_dpc']->bindValue(':IDAccessPINLevel', '3');
            $stmt['stmt_insert_dpc']->bindValue(':IDAntipassbackEnable', '0');
            $stmt['stmt_insert_dpc']->bindValue(':IDAntipassbackMode', '0');
            $stmt['stmt_insert_dpc']->bindValue(':IDAntipassbackLevel', '3');
            $stmt['stmt_insert_dpc']->bindValue(':IDAntipassbackModeLevel', '3');
            $stmt['stmt_insert_dpc']->bindValue(':IDArmDisarmLevel', '3');
            $stmt['stmt_insert_dpc']->bindValue(':IDVisitorLevel', '3');
            $stmt['stmt_insert_dpc']->bindValue(':LastUpdateLoginID', 'admin');
            $stmt['stmt_insert_dpc']->bindValue(':LastUpdateIP', '127.0.0.1');
            $stmt['stmt_insert_dpc']->bindValue(':IndiAccessRightUse', '0');
            $stmt['stmt_insert_dpc']->bindValue(':IDValidDateStartEnable', '0');
            $stmt['stmt_insert_dpc']->bindValue(':IDValidDateEndEnable', '0');
            $stmt['stmt_insert_dpc']->execute();

            $change = true;
        } else {
            $stmt['stmt_dp_data']->bindParam(':id', $value['user_id'], PDO::PARAM_STR);
            $stmt['stmt_dp_data']->execute();
            $dp_data = $stmt['stmt_dp_data']->fetch(PDO::FETCH_ASSOC);

            $stmt['stmt_update']->bindParam(':expire_date', $end_date, PDO::PARAM_STR);
            $stmt['stmt_update']->bindParam(':id', $value['user_id'], PDO::PARAM_STR);
            $stmt['stmt_update']->execute();

            $stmt['stmt_update_dpc']->bindParam(':end_date', $end_date, PDO::PARAM_STR);
            $stmt['stmt_update_dpc']->bindParam(':id1', $value['user_id'], PDO::PARAM_INT);
            $stmt['stmt_update_dpc']->bindValue(':id2', '1');
            $stmt['stmt_update_dpc']->execute();

            $stmt['stmt_update_dpc']->bindParam(':end_date', $end_date, PDO::PARAM_STR);
            $stmt['stmt_update_dpc']->bindParam(':id1', $value['user_id'], PDO::PARAM_INT);
            $stmt['stmt_update_dpc']->bindValue(':id2', '2');
            $stmt['stmt_update_dpc']->execute();

            $stmt['stmt_update_dpc']->bindParam(':end_date', $end_date, PDO::PARAM_STR);
            $stmt['stmt_update_dpc']->bindParam(':id1', $value['user_id'], PDO::PARAM_INT);
            $stmt['stmt_update_dpc']->bindValue(':id2', '3');
            $stmt['stmt_update_dpc']->execute();

            $change = true;

            sl_log($log, 'Update Data_Person PersonID : '.$value['user_id'].' Complete');
        }

        if ($change) {
            foreach ($value['aci_list'] as $aci) {
                $stmt['stmt_insert_csp']->bindValue(':sender_ip_address', $aci['send_ip'], PDO::PARAM_STR);
                $stmt['stmt_insert_csp']->bindValue(':dest_ip_address', $aci['dest_ip'], PDO::PARAM_STR);
                $stmt['stmt_insert_csp']->bindValue(':system_work_id', 1);
                $stmt['stmt_insert_csp']->bindValue(':device_id', $aci['device_id']);
                $stmt['stmt_insert_csp']->bindValue(':packet_command', 'IDR1');
                $stmt['stmt_insert_csp']->bindParam(':packet_data1', $value['user_id'], PDO::PARAM_STR);
                $stmt['stmt_insert_csp']->bindParam(':packet_data2', $value['card_no'], PDO::PARAM_STR);
                $stmt['stmt_insert_csp']->bindValue(':packet_status', 'SW');
                $stmt['stmt_insert_csp']->execute();
            }
        }

        /* $stmt['stmt_dp_all']->execute();
        $dp_all_list=$stmt['stmt_dp_all']->fetchAll(PDO::FETCH_ASSOC);

        $user_exists=false;
        foreach($dp_all_list as $dp) {
            if($dp['PersonID']==$value['user_id']) {
                $user_exists=true;
            }
        }

        if(empty($user_exists)) {
            $stmt['stmt_dp_delete']->bindParam(':id', $value['user_id'], PDO::PARAM_INT);
            $stmt['stmt_dp_delete']->execute();
        } */

        return $change;
    }

    function check_valid_ist_card_no($card_no)
    {
        /* 유효한 카드번호 아니면 넘어가자 */
        if (strlen($card_no) != 10) {
            //    echo $card_no;
            return false;
        }

        /* 유효한 카드번호 아니면 넘어가자 */
        if (!ctype_digit(strval($card_no))) {
            //   echo $card_no;
            return false;
        }

        return true;
    }

    function sync_ist_user($db, $pdo, $log, $change_users)
    {
        $branch_list = array();

        $dtObj = new DateTime('now', new DateTimeZone('Asia/Seoul'));
        $dtObj->modify('-1 Days');
        $yester_day = $dtObj->format('Ymd');

        foreach ($change_users as $change_user) {
            $branch_list[] = $change_user['branch_id'];
        }

        $branch_list = array_unique($branch_list);

        $dbh = $pdo;
        unset($pdo);

        $stmt_select_user_count = $dbh->prepare('SELECT count(*) FROM users as u WHERE u.id=:id AND u.enable=1');
        $stmt_select_user = $dbh->prepare('SELECT u.*,uac.card_no FROM users as u LEFT JOIN user_access_cards as uac ON uac.user_id=u.id WHERE u.id=:id AND u.enable=1');

        $stmt_select_enroll_count = $dbh->prepare('SELECT count(*) FROM users as u INNER JOIN orders AS o ON o.user_id=u.id INNER JOIN order_products as op ON op.order_id=o.id INNER JOIN product_relations as pr ON pr.product_id=op.product_id INNER JOIN enrolls AS e ON e.order_id=o.id WHERE o.enable=1 AND e.start_date<=CURDATE() AND e.end_date>=CURDATE() AND o.branch_id=:branch_id AND pr.product_relation_type_id=:product_relation_type_id AND o.enable=1 AND u.enable=1 AND o.stopped=0 AND u.id=:user_id');
        $stmt_select_enroll = $dbh->prepare('SELECT MIN(e.start_date) as start_date,MAX(e.end_date) as end_date FROM users as u INNER JOIN orders AS o ON o.user_id=u.id INNER JOIN order_products as op ON op.order_id=o.id INNER JOIN product_relations as pr ON pr.product_id=op.product_id INNER JOIN enrolls AS e ON e.order_id=o.id WHERE o.enable=1 AND e.start_date<=CURDATE() AND e.end_date>=CURDATE() AND o.branch_id=:branch_id AND pr.product_relation_type_id=:product_relation_type_id AND o.enable=1 AND u.enable=1 AND o.stopped=0 AND u.id=:user_id');

        foreach ($branch_list as $branch_id) {
            $user_list = array();
            $i = 0;
            $user_ids_s = '';

            $user_ids = array();
            $user_list = array();

            foreach ($change_users as $change_user) {
                if ($change_user['branch_id'] != $branch_id) {
                    continue;
                }

                if (in_array($change_user['user_id'], $user_ids)) {
                    continue;
                }

                $user_ids[$i] = $change_user['user_id'];
            }

            include __DIR__.DIRECTORY_SEPARATOR.'sync_ist_header.php';

            foreach ($user_ids as $user_id) {
                $stmt_select_user_count->bindParam(':id', $user_id, PDO::PARAM_INT);
                $stmt_select_user_count->execute();
                $user_count = $stmt_select_user_count->fetchColumn();

                if (empty($user_count)) {
                    continue;
                }

                $user_list_data = array();

                $stmt_select_user->bindParam(':id', $user_id, PDO::PARAM_INT);
                $stmt_select_user->execute();
                $user_data = $stmt_select_user->fetch(PDO::FETCH_ASSOC);

                $user_list_data['user_id'] = $user_data['id'];
                $user_list_data['name'] = $user_data['name'];
                $user_list_data['card_no'] = $user_data['card_no'];
                $user_list_data['update_id'] = '000';

                $stmt_select_enroll_count->bindParam(':user_id', $user_id, PDO::PARAM_INT);
                $stmt_select_enroll_count->bindParam(':branch_id', $branch_id, PDO::PARAM_INT);
                $stmt_select_enroll_count->bindValue(':product_relation_type_id', 4);
                $stmt_select_enroll_count->execute();
                $enroll_count = $stmt_select_enroll_count->fetchColumn();

                if ($enroll_count) {
                    $stmt_select_enroll->bindParam(':user_id', $user_id, PDO::PARAM_INT);
                    $stmt_select_enroll->bindParam(':branch_id', $branch_id, PDO::PARAM_INT);
                    $stmt_select_enroll->bindValue(':product_relation_type_id', 4);
                    $stmt_select_enroll->execute();
                    $enroll_data = $stmt_select_enroll->fetch(PDO::FETCH_ASSOC);

                    $user_list_data['start_date'] = $enroll_data['start_date'];
                    $user_list_data['end_date'] = $enroll_data['end_date'];
                } else {
                    $user_list_data['start_date'] = '20200101';
                    $user_list_data['end_date'] = $yester_day;
                }

                $user_list[] = $user_list_data;
            }

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

                if (count($user_list)) {
                    foreach ($user_list as $value) {
                        if (empty(check_valid_ist_card_no($value['card_no']))) {
                            continue;
                        }

                        $value['aci_list'] = $aci_list;
                        $change_exist = insert_dp($stmt_a, $value, $log);
                    }
                }

                // 변경사항 있으면 ACU에 적용
                if ($change_exist) {
                    insert_cc($stmt_a, $value, $log);
                }

                // 커밋
                $pdo->commit();
            }
        }
    }

<?php

/*  PT상태 변경 스크립트 입니다. */

try {
    $log_file = basename(__FILE__, '.php').'.log';
    include __DIR__.DIRECTORY_SEPARATOR.'common_head.php';

    $dateobj = new DateTime('now', $dateTimeZone);
    $today = $dateobj->format('Y-m-d');

    $stmt_count = $pdo->prepare('SELECT count(*) FROM reservations as r INNER JOIN reservation_users as ru ON ru.reservation_id=r.id WHERE ru.complete in(1,0) AND DATE(r.start_time)<CURDATE() AND r.id > 1253397');
    $stmt_count->execute();
    $count = $stmt_count->fetchColumn();
    $stmt_count->closeCursor();

    if (empty($count)) {
        sl_log($log, 'Already Sync');

        return true;
    }

    $stmt_select = $pdo->prepare('SELECT r.branch_id,r.manager_id,date(r.start_time) as start_date,ru.* FROM reservations as r INNER JOIN reservation_users as ru ON ru.reservation_id=r.id WHERE ru.complete in(1,0) AND DATE(r.start_time)<CURDATE() AND r.id > 1253397');
    $stmt_select->execute();
    $list = $stmt_select->fetchAll(PDO::FETCH_ASSOC);
    $stmt_select->closeCursor();


    $stmt_account_insert = $pdo->prepare('INSERT INTO accounts(account_category_id, branch_id, user_id, cash, transaction_date, created_at,updated_at) VALUES(:account_category_id,:branch_id,:user_id, :cash, :transaction_date ,NOW(),NOW())');
    $stmt_account_commission_insert = $pdo->prepare('INSERT INTO account_commissions(account_id, enroll_id, employee_id) VALUES(:account_id, :enroll_id, :employee_id)');
    $stmt_eul_insert = $pdo->prepare('INSERT INTO enroll_use_logs(enroll_id,account_id,reservation_user_id,type,created_at,updated_at) VALUES(:enroll_id,:account_id,:reservation_user_id,"confirm",NOW(),NOW())');
    $stmt_update_ru = $pdo->prepare('UPDATE reservation_users SET complete=3, complete_at=now() WHERE id=:id');

    $stmt_enroll_count = $pdo->prepare('SELECT count(*) FROM enrolls as e INNER JOIN orders as o ON e.order_id=o.id LEFT JOIN enroll_commissions as ec ON ec.enroll_id=e.id WHERE o.enable=1 AND e.id=:id');
    $stmt_enroll = $pdo->prepare('SELECT e.*,o.price,ec.commission FROM enrolls as e INNER JOIN orders as o ON e.order_id=o.id LEFT JOIN enroll_commissions as ec ON ec.enroll_id=e.id WHERE o.enable=1 AND e.id=:id');

    $stmt_employee_count = $pdo->prepare('SELECT count(*) FROM admins as e WHERE e.enable=1 AND e.id=:id');
    $stmt_employee = $pdo->prepare('SELECT * FROM admins as e WHERE e.enable=1 AND e.id=:id');

    $stmt_update_enroll = $pdo->prepare('UPDATE enrolls set use_quantity=use_quantity+1 WHERE id=:id');

    $affect_reservation_user = array();

    // 트랜잭션 시작
    $pdo->beginTransaction();

    foreach ($list as $value) {
        $stmt_enroll_count->bindParam(':id', $value['enroll_id'], PDO::PARAM_INT);
        $stmt_enroll_count->execute();
        $enroll_count = $stmt_enroll_count->fetchColumn();
        
        if (empty($enroll_count)) {
            print_r('enroll not exists'."\n");
            continue;
        }

        $stmt_enroll->bindParam(':id', $value['enroll_id'], PDO::PARAM_INT);
        $stmt_enroll->execute();
        $enroll = $stmt_enroll->fetch(PDO::FETCH_ASSOC);
        
        $commission=null;

        if (isset($enroll['commission'])) { // 수수료 설정이 되어있으면
            $commission = $enroll['commission'];
        }

        $stmt_employee_count->bindParam(':id', $value['manager_id'], PDO::PARAM_INT);
        $stmt_employee_count->execute();
        $employee_count = $stmt_employee_count->fetchColumn();

        if (empty($employee_count)) {
            print_r('employee not exists'."\n");
            continue;
        }

        $stmt_employee->bindParam(':id', $value['manager_id'], PDO::PARAM_INT);
        $stmt_employee->execute();
        $employee = $stmt_employee->fetch(PDO::FETCH_ASSOC);

        if (empty($commission)) { // 수수료 설정이 안되어 있으면
            if (empty($employee['commission_rate'])) {
                $employee['commission_rate']=36;
            }            
            
            $commission = round(($enroll['price'] / $enroll['insert_quantity']) * ($employee['commission_rate'] * 0.01)); // 강사 수수료가 설정되어 있으면 아래공식으로 수수료 설정
        }

        $add_commission=ADD_COMMISSION;

        $stmt_account_insert->bindParam(':account_category_id', $add_commission, PDO::PARAM_INT);
        $stmt_account_insert->bindParam(':branch_id', $value['branch_id'], PDO::PARAM_INT);
        $stmt_account_insert->bindParam(':user_id', $value['user_id'], PDO::PARAM_INT);
        $stmt_account_insert->bindParam(':transaction_date', $value['start_date'], PDO::PARAM_STR);        
        $stmt_account_insert->bindParam(':cash', $commission, PDO::PARAM_INT);
        $stmt_account_insert->execute();
        $account_id = $pdo->lastInsertId();        

        $stmt_account_commission_insert->bindParam(':account_id', $account_id, PDO::PARAM_INT);
        $stmt_account_commission_insert->bindParam(':enroll_id', $value['enroll_id'], PDO::PARAM_INT);
        $stmt_account_commission_insert->bindParam(':employee_id', $value['manager_id'], PDO::PARAM_INT);     
        $stmt_account_commission_insert->execute();

        $stmt_eul_insert->bindParam(':account_id', $account_id, PDO::PARAM_INT);
        $stmt_eul_insert->bindParam(':enroll_id', $value['enroll_id'], PDO::PARAM_INT);
        $stmt_eul_insert->bindParam(':reservation_user_id', $value['id'], PDO::PARAM_INT);
        $stmt_eul_insert->execute();

        $stmt_update_ru->bindParam(':id', $value['id'], PDO::PARAM_INT);
        $stmt_update_ru->execute();

        $stmt_update_enroll->bindParam(':id', $value['enroll_id'], PDO::PARAM_INT);
        $stmt_update_enroll->execute();

        $affect_reservation_user[] = $value['id'];
    }

    $stmt_account_insert->closeCursor();
    $stmt_account_commission_insert->closeCursor();
    $stmt_eul_insert->closeCursor();
    $stmt_update_ru->closeCursor();
    $stmt_update_enroll->closeCursor();

    // 커밋
    $pdo->commit();
    $pdo = null;

    $affect_count = count($affect_reservation_user);
    sl_log($log, 'count:'.$affect_count);

    if ($affect_count) {
        foreach ($affect_reservation_user as $reservation_user_id) {
            sl_log($log, 'reservation_user :'.$reservation_user_id);
        }
    }
} catch (Exception $e) {
    include __DIR__.DIRECTORY_SEPARATOR.'common_catch.php';
}

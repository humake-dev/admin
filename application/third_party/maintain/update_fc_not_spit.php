<?php

/* FC 퇴사의 경우 나머지 FC에게 배분하는 스크립트 */

try {
    $log_file = basename(__FILE__, '.php').'.log';
    include __DIR__.DIRECTORY_SEPARATOR.'common_head.php';

    $branch_id=4;
    $out_fc=761;
    $left_fc=887;

    $inserted_user_id=array();
    $update_array=array();


    $stmt_count_active12_more = $pdo->prepare('SELECT count(*) FROM users as u INNER JOIN user_fcs as uf ON uf.user_id=u.id WHERE EXISTS(SELECT o.user_id FROM orders as o INNER JOIN enrolls as e ON e.order_id=o.id WHERE e.end_date>=CURDATE() AND DATEDIFF(e.end_date,e.start_date)>=360 AND o.enable=1 AND o.user_id=u.id) AND u.branch_id=:branch_id AND uf.fc_id=:fc_id');
    $stmt_count_active12_more->bindParam(':branch_id', $branch_id, PDO::PARAM_INT);  
    $stmt_count_active12_more->bindParam(':fc_id', $out_fc, PDO::PARAM_INT);          
    $stmt_count_active12_more->execute();
    $active12_more_count = $stmt_count_active12_more->fetchColumn();
    $stmt_count_active12_more->closeCursor();

    if (!empty($active12_more_count)) {
        $stmt_select_active12_more = $pdo->prepare('SELECT u.* FROM users as u INNER JOIN user_fcs as uf ON uf.user_id=u.id WHERE EXISTS(SELECT o.user_id FROM orders as o INNER JOIN enrolls as e ON e.order_id=o.id WHERE e.end_date>=CURDATE() AND DATEDIFF(e.end_date,e.start_date)>=360 AND o.enable=1 AND o.user_id=u.id) AND u.branch_id=:branch_id AND uf.fc_id=:fc_id');
        $stmt_select_active12_more->bindParam(':branch_id', $branch_id, PDO::PARAM_INT);  
        $stmt_select_active12_more->bindParam(':fc_id', $out_fc, PDO::PARAM_INT);    
        $stmt_select_active12_more->execute();
        $active12_more_lists = $stmt_select_active12_more->fetchAll(PDO::FETCH_ASSOC);
        $stmt_select_active12_more->closeCursor();

        foreach ($active12_more_lists as $index=>$value) {
            $inserted_user_id[]=$value['id'];
        }
    }

    $stmt_count_active12 = $pdo->prepare('SELECT count(*) FROM users as u INNER JOIN user_fcs as uf ON uf.user_id=u.id WHERE EXISTS(SELECT o.user_id FROM orders as o INNER JOIN enrolls as e ON e.order_id=o.id WHERE e.end_date>=CURDATE() AND DATEDIFF(e.end_date,e.start_date)>=180 AND DATEDIFF(e.end_date,e.start_date)<360 AND o.enable=1 AND o.user_id=u.id) AND u.branch_id=:branch_id AND uf.fc_id=:fc_id');
    $stmt_count_active12->bindParam(':branch_id', $branch_id, PDO::PARAM_INT);  
    $stmt_count_active12->bindParam(':fc_id', $out_fc, PDO::PARAM_INT);          
    $stmt_count_active12->execute();
    $active12_count = $stmt_count_active12->fetchColumn();
    $stmt_count_active12->closeCursor();

    if (!empty($active12_count)) {
        $stmt_select_active12 = $pdo->prepare('SELECT u.* FROM users as u INNER JOIN user_fcs as uf ON uf.user_id=u.id WHERE EXISTS(SELECT o.user_id FROM orders as o INNER JOIN enrolls as e ON e.order_id=o.id WHERE e.end_date>=CURDATE() AND DATEDIFF(e.end_date,e.start_date)>=180 AND DATEDIFF(e.end_date,e.start_date)<360 AND o.enable=1 AND o.user_id=u.id) AND u.branch_id=:branch_id AND uf.fc_id=:fc_id');
        $stmt_select_active12->bindParam(':branch_id', $branch_id, PDO::PARAM_INT);  
        $stmt_select_active12->bindParam(':fc_id', $out_fc, PDO::PARAM_INT);    
        $stmt_select_active12->execute();
        $active12_lists = $stmt_select_active12->fetchAll(PDO::FETCH_ASSOC);
        $stmt_select_active12->closeCursor();

        foreach ($active12_lists as $index=>$value) {
            if(in_array($value['id'],$inserted_user_id)) {
                continue;
            }

            $inserted_user_id[]=$value['id'];            
        }
    }

    $stmt_count_active6 = $pdo->prepare('SELECT count(*) FROM users as u INNER JOIN user_fcs as uf ON uf.user_id=u.id WHERE EXISTS(SELECT o.user_id FROM orders as o INNER JOIN enrolls as e ON e.order_id=o.id WHERE e.end_date>=CURDATE() AND DATEDIFF(e.end_date,e.start_date)>=90 AND DATEDIFF(e.end_date,e.start_date)<180 AND o.enable=1 AND o.user_id=u.id) AND u.branch_id=:branch_id AND uf.fc_id=:fc_id');
    $stmt_count_active6->bindParam(':branch_id', $branch_id, PDO::PARAM_INT);  
    $stmt_count_active6->bindParam(':fc_id', $out_fc, PDO::PARAM_INT);          
    $stmt_count_active6->execute();
    $active6_count = $stmt_count_active6->fetchColumn();
    $stmt_count_active6->closeCursor();

    if (!empty($active6_count)) {
        $stmt_select_active6 = $pdo->prepare('SELECT u.* FROM users as u INNER JOIN user_fcs as uf ON uf.user_id=u.id WHERE EXISTS(SELECT o.user_id FROM orders as o INNER JOIN enrolls as e ON e.order_id=o.id WHERE e.end_date>=CURDATE() AND DATEDIFF(e.end_date,e.start_date)>=90 AND DATEDIFF(e.end_date,e.start_date)<180 AND o.enable=1 AND o.user_id=u.id) AND u.branch_id=:branch_id AND uf.fc_id=:fc_id');
        $stmt_select_active6->bindParam(':branch_id', $branch_id, PDO::PARAM_INT);  
        $stmt_select_active6->bindParam(':fc_id', $out_fc, PDO::PARAM_INT);    
        $stmt_select_active6->execute();
        $active6_lists = $stmt_select_active6->fetchAll(PDO::FETCH_ASSOC);
        $stmt_select_active6->closeCursor();

        foreach ($active6_lists as $index=>$value) {
            if(in_array($value['id'],$inserted_user_id)) {
                continue;
            }

            $inserted_user_id[]=$value['id'];            
        }
    }

    $stmt_count_active3 = $pdo->prepare('SELECT count(*) FROM users as u INNER JOIN user_fcs as uf ON uf.user_id=u.id WHERE EXISTS(SELECT o.user_id FROM orders as o INNER JOIN enrolls as e ON e.order_id=o.id WHERE e.end_date>=CURDATE() AND DATEDIFF(e.end_date,e.start_date)<90 AND o.enable=1 AND o.user_id=u.id) AND u.branch_id=:branch_id AND uf.fc_id=:fc_id');
    $stmt_count_active3->bindParam(':branch_id', $branch_id, PDO::PARAM_INT);  
    $stmt_count_active3->bindParam(':fc_id', $out_fc, PDO::PARAM_INT);          
    $stmt_count_active3->execute();
    $active3_count = $stmt_count_active3->fetchColumn();
    $stmt_count_active3->closeCursor();

    if (!empty($active3_count)) {
        $stmt_select_active3 = $pdo->prepare('SELECT u.* FROM users as u INNER JOIN user_fcs as uf ON uf.user_id=u.id WHERE EXISTS(SELECT o.user_id FROM orders as o INNER JOIN enrolls as e ON e.order_id=o.id WHERE e.end_date>=CURDATE() AND DATEDIFF(e.end_date,e.start_date)<90 AND o.enable=1 AND o.user_id=u.id) AND u.branch_id=:branch_id AND uf.fc_id=:fc_id');
        $stmt_select_active3->bindParam(':branch_id', $branch_id, PDO::PARAM_INT);  
        $stmt_select_active3->bindParam(':fc_id', $out_fc, PDO::PARAM_INT);    
        $stmt_select_active3->execute();
        $active3_lists = $stmt_select_active3->fetchAll(PDO::FETCH_ASSOC);
        $stmt_select_active3->closeCursor();

        foreach ($active3_lists as $index=>$value) {
            if(in_array($value['id'],$inserted_user_id)) {
                continue;
            }
            
            $inserted_user_id[]=$value['id'];
        }
    }

    $stmt_count_inactive12_more = $pdo->prepare('SELECT count(*) FROM users as u INNER JOIN user_fcs as uf ON uf.user_id=u.id WHERE NOT EXISTS(SELECT o.user_id FROM orders as o INNER JOIN enrolls as e ON e.order_id=o.id WHERE e.end_date>=CURDATE() AND o.enable=1 AND o.user_id=u.id) AND EXISTS(SELECT o.user_id FROM orders as o INNER JOIN enrolls as e ON e.order_id=o.id WHERE e.end_date<CURDATE() AND DATEDIFF(e.end_date,e.start_date)>=360 AND o.enable=1 AND o.user_id=u.id) AND u.branch_id=:branch_id AND uf.fc_id=:fc_id');
    $stmt_count_inactive12_more->bindParam(':branch_id', $branch_id, PDO::PARAM_INT);  
    $stmt_count_inactive12_more->bindParam(':fc_id', $out_fc, PDO::PARAM_INT);          
    $stmt_count_inactive12_more->execute();
    $inactive12_more_count = $stmt_count_inactive12_more->fetchColumn();
    $stmt_count_inactive12_more->closeCursor();

    if (!empty($inactive12_more_count)) {
        $stmt_select_inactive12_more = $pdo->prepare('SELECT u.* FROM users as u INNER JOIN user_fcs as uf ON uf.user_id=u.id WHERE NOT EXISTS(SELECT o.user_id FROM orders as o INNER JOIN enrolls as e ON e.order_id=o.id WHERE e.end_date>=CURDATE() AND o.enable=1 AND o.user_id=u.id) AND EXISTS(SELECT o.user_id FROM orders as o INNER JOIN enrolls as e ON e.order_id=o.id WHERE e.end_date<CURDATE() AND DATEDIFF(e.end_date,e.start_date)>=360 AND o.enable=1 AND o.user_id=u.id) AND u.branch_id=:branch_id AND uf.fc_id=:fc_id');
        $stmt_select_inactive12_more->bindParam(':branch_id', $branch_id, PDO::PARAM_INT);  
        $stmt_select_inactive12_more->bindParam(':fc_id', $out_fc, PDO::PARAM_INT);    
        $stmt_select_inactive12_more->execute();
        $inactive12_more_lists = $stmt_select_inactive12_more->fetchAll(PDO::FETCH_ASSOC);
        $stmt_select_inactive12_more->closeCursor();

        foreach ($inactive12_more_lists as $index=>$value) {
            if(count($update_array)>=250) {
                continue;
            }
            
            if(in_array($value['id'],$inserted_user_id)) {
                continue;
            }

            $inserted_user_id[]=$value['id'];
            $update_array[]=array('user_id'=>$value['id'],'fc_id'=>$left_fc);
        }
    }

    $stmt_count_inactive12 = $pdo->prepare('SELECT count(*) FROM users as u INNER JOIN user_fcs as uf ON uf.user_id=u.id WHERE NOT EXISTS(SELECT o.user_id FROM orders as o INNER JOIN enrolls as e ON e.order_id=o.id WHERE e.end_date>=CURDATE() AND o.enable=1 AND o.user_id=u.id) AND EXISTS(SELECT o.user_id FROM orders as o INNER JOIN enrolls as e ON e.order_id=o.id WHERE e.end_date<CURDATE() AND DATEDIFF(e.end_date,e.start_date)>=180 AND DATEDIFF(e.end_date,e.start_date)<360 AND o.enable=1 AND o.user_id=u.id) AND u.branch_id=:branch_id AND uf.fc_id=:fc_id');
    $stmt_count_inactive12->bindParam(':branch_id', $branch_id, PDO::PARAM_INT);  
    $stmt_count_inactive12->bindParam(':fc_id', $out_fc, PDO::PARAM_INT);          
    $stmt_count_inactive12->execute();
    $inactive12_count = $stmt_count_inactive12->fetchColumn();
    $stmt_count_inactive12->closeCursor();

    if (!empty($inactive12_count)) {
        $stmt_select_inactive12 = $pdo->prepare('SELECT u.* FROM users as u INNER JOIN user_fcs as uf ON uf.user_id=u.id WHERE NOT EXISTS(SELECT o.user_id FROM orders as o INNER JOIN enrolls as e ON e.order_id=o.id WHERE e.end_date>=CURDATE() AND o.enable=1 AND o.user_id=u.id) AND EXISTS(SELECT o.user_id FROM orders as o INNER JOIN enrolls as e ON e.order_id=o.id WHERE e.end_date<CURDATE() AND DATEDIFF(e.end_date,e.start_date)>=180 AND DATEDIFF(e.end_date,e.start_date)<360 AND o.enable=1 AND o.user_id=u.id) AND u.branch_id=:branch_id AND uf.fc_id=:fc_id');
        $stmt_select_inactive12->bindParam(':branch_id', $branch_id, PDO::PARAM_INT);  
        $stmt_select_inactive12->bindParam(':fc_id', $out_fc, PDO::PARAM_INT);    
        $stmt_select_inactive12->execute();
        $inactive12_lists = $stmt_select_inactive12->fetchAll(PDO::FETCH_ASSOC);
        $stmt_select_inactive12->closeCursor();

        foreach ($inactive12_lists as $index=>$value) {
            if(count($update_array)>=250) {
                continue;
            }

            if(in_array($value['id'],$inserted_user_id)) {
                continue;
            }

            $inserted_user_id[]=$value['id'];            
            $update_array[]=array('user_id'=>$value['id'],'fc_id'=>$left_fc);
        }
    }

    $stmt_count_inactive6 = $pdo->prepare('SELECT count(*) FROM users as u INNER JOIN user_fcs as uf ON uf.user_id=u.id WHERE NOT EXISTS(SELECT o.user_id FROM orders as o INNER JOIN enrolls as e ON e.order_id=o.id WHERE e.end_date>=CURDATE() AND o.enable=1 AND o.user_id=u.id) AND EXISTS(SELECT o.user_id FROM orders as o INNER JOIN enrolls as e ON e.order_id=o.id WHERE e.end_date<CURDATE() AND DATEDIFF(e.end_date,e.start_date)>=90 AND DATEDIFF(e.end_date,e.start_date)<180 AND o.enable=1 AND o.user_id=u.id) AND u.branch_id=:branch_id AND uf.fc_id=:fc_id');
    $stmt_count_inactive6->bindParam(':branch_id', $branch_id, PDO::PARAM_INT);  
    $stmt_count_inactive6->bindParam(':fc_id', $out_fc, PDO::PARAM_INT);          
    $stmt_count_inactive6->execute();
    $inactive6_count = $stmt_count_inactive6->fetchColumn();
    $stmt_count_inactive6->closeCursor();

    if (!empty($inactive6_count)) {
        $stmt_select_inactive6 = $pdo->prepare('SELECT u.* FROM users as u INNER JOIN user_fcs as uf ON uf.user_id=u.id WHERE NOT EXISTS(SELECT o.user_id FROM orders as o INNER JOIN enrolls as e ON e.order_id=o.id WHERE e.end_date>=CURDATE() AND o.enable=1 AND o.user_id=u.id) AND EXISTS(SELECT o.user_id FROM orders as o INNER JOIN enrolls as e ON e.order_id=o.id WHERE e.end_date<CURDATE() AND DATEDIFF(e.end_date,e.start_date)>=90 AND DATEDIFF(e.end_date,e.start_date)<180 AND o.enable=1 AND o.user_id=u.id) AND u.branch_id=:branch_id AND uf.fc_id=:fc_id');
        $stmt_select_inactive6->bindParam(':branch_id', $branch_id, PDO::PARAM_INT);  
        $stmt_select_inactive6->bindParam(':fc_id', $out_fc, PDO::PARAM_INT);    
        $stmt_select_inactive6->execute();
        $inactive6_lists = $stmt_select_inactive6->fetchAll(PDO::FETCH_ASSOC);
        $stmt_select_inactive6->closeCursor();

        foreach ($inactive6_lists as $index=>$value) {
            if(count($update_array)>=750) {
                continue;
            }

            if(in_array($value['id'],$inserted_user_id)) {
                continue;
            }

            $inserted_user_id[]=$value['id'];            
            $update_array[]=array('user_id'=>$value['id'],'fc_id'=>$left_fc);
        }
    }

    $stmt_count_inactive3 = $pdo->prepare('SELECT count(*) FROM users as u INNER JOIN user_fcs as uf ON uf.user_id=u.id WHERE NOT EXISTS(SELECT o.user_id FROM orders as o INNER JOIN enrolls as e ON e.order_id=o.id WHERE e.end_date>=CURDATE() AND o.enable=1 AND o.user_id=u.id) AND EXISTS(SELECT o.user_id FROM orders as o INNER JOIN enrolls as e ON e.order_id=o.id WHERE e.end_date<CURDATE() AND DATEDIFF(e.end_date,e.start_date)<90 AND o.enable=1 AND o.user_id=u.id) AND u.branch_id=:branch_id AND uf.fc_id=:fc_id');
    $stmt_count_inactive3->bindParam(':branch_id', $branch_id, PDO::PARAM_INT);  
    $stmt_count_inactive3->bindParam(':fc_id', $out_fc, PDO::PARAM_INT);          
    $stmt_count_inactive3->execute();
    $inactive3_count = $stmt_count_inactive3->fetchColumn();
    $stmt_count_inactive3->closeCursor();

    if (!empty($inactive3_count)) {
        $stmt_select_inactive3 = $pdo->prepare('SELECT u.* FROM users as u INNER JOIN user_fcs as uf ON uf.user_id=u.id WHERE NOT EXISTS(SELECT o.user_id FROM orders as o INNER JOIN enrolls as e ON e.order_id=o.id WHERE e.end_date>=CURDATE() AND o.enable=1 AND o.user_id=u.id) AND EXISTS(SELECT o.user_id FROM orders as o INNER JOIN enrolls as e ON e.order_id=o.id WHERE e.end_date<CURDATE() AND DATEDIFF(e.end_date,e.start_date)<90 AND o.enable=1 AND o.user_id=u.id) AND u.branch_id=:branch_id AND uf.fc_id=:fc_id');
        $stmt_select_inactive3->bindParam(':branch_id', $branch_id, PDO::PARAM_INT);  
        $stmt_select_inactive3->bindParam(':fc_id', $out_fc, PDO::PARAM_INT);    
        $stmt_select_inactive3->execute();
        $inactive3_lists = $stmt_select_inactive3->fetchAll(PDO::FETCH_ASSOC);
        $stmt_select_inactive3->closeCursor();

        foreach ($inactive3_lists as $index=>$value) {
            if(count($update_array)>=1000) {
                continue;
            }

            if(in_array($value['id'],$inserted_user_id)) {
                continue;
            }

            $inserted_user_id[]=$value['id']; 
            $update_array[]=array('user_id'=>$value['id'],'fc_id'=>$left_fc);
        }
    }

    /* $stmt_count_etc = $pdo->prepare('SELECT count(*) FROM users as u INNER JOIN user_fcs as uf ON uf.user_id=u.id WHERE u.branch_id=:branch_id AND uf.fc_id=:fc_id');
    $stmt_count_etc->bindParam(':branch_id', $branch_id, PDO::PARAM_INT);  
    $stmt_count_etc->bindParam(':fc_id', $out_fc, PDO::PARAM_INT);          
    $stmt_count_etc->execute();
    $etc_count = $stmt_count_etc->fetchColumn();
    $stmt_count_etc->closeCursor();

    if (!empty($etc_count)) {
        $stmt_select_etc = $pdo->prepare('SELECT u.* FROM users as u INNER JOIN user_fcs as uf ON uf.user_id=u.id WHERE u.branch_id=:branch_id AND uf.fc_id=:fc_id');
        $stmt_select_etc->bindParam(':branch_id', $branch_id, PDO::PARAM_INT);  
        $stmt_select_etc->bindParam(':fc_id', $out_fc, PDO::PARAM_INT);    
        $stmt_select_etc->execute();
        $etc_lists = $stmt_select_etc->fetchAll(PDO::FETCH_ASSOC);
        $stmt_select_etc->closeCursor();

        foreach ($etc_lists as $index=>$value) {
            if(in_array($value['id'],$inserted_user_id)) {
                continue;
            }

            $mod=$index % $fcs_count;
            $inserted_user_id[]=$value['id'];
            $update_array[]=array('user_id'=>$value['id'],'fc_id'=>$left_fcs[$mod]);
        }
    } */

    $stmt_update = $pdo->prepare('UPDATE user_fcs SET fc_id=:fc_id WHERE user_id=:user_id');


    // 트랜잭션 시작
    $pdo->beginTransaction();
    $update_count = 0;

    foreach ($update_array as $data) {
        $stmt_update->bindParam(':fc_id', $data['fc_id'], PDO::PARAM_INT);        
        $stmt_update->bindParam(':user_id', $data['user_id'], PDO::PARAM_INT);
        $stmt_update->execute();

        $update_count++;
        sl_log($log, 'update user_fcs :'. $data['user_id'].' updated');
    }

    $stmt_update->closeCursor();

    echo 'updated '.$update_count."\n";

    // 커밋
    $pdo->commit();
    $pdo = null;

    echo 'success'."\n";
} catch (Exception $e) {
    include __DIR__.DIRECTORY_SEPARATOR.'common_catch.php';
}

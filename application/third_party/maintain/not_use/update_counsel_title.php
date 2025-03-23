<?php

try {
    $log_file = basename(__FILE__, '.php').'.log';
    include __DIR__.DIRECTORY_SEPARATOR.'common_head.php';

    $stmt_count = $pdo->prepare('SELECT count(*) FROM counsels');
    $stmt_count->execute();
    $count = $stmt_count->fetchColumn();
    $stmt_count->closeCursor();

    if (empty($count)) {
        exit;
    }

    $stmt_select = $pdo->prepare('SELECT c.*,cc.content FROM counsels as c INNER JOIN counsel_contents as cc ON c.id=cc.id ORDER BY id desc LIMIT :page,100');    
    $stmt_update = $pdo->prepare('UPDATE counsels SET title=:title WHERE id=:id');

    // 트랜잭션 시작
    $pdo->beginTransaction();

    for($a=0;$a<$count;$a=$a+100) {
        $stmt_select->bindParam(':page', $a, PDO::PARAM_INT);      
        $stmt_select->execute();
        $lists = $stmt_select->fetchAll(PDO::FETCH_ASSOC);

        foreach ($lists as $value) {
            $length=mb_strlen($value['content']);
            $title=mb_substr($value['content'],0,25);

            if($length>25) {
                $title.='...'; 
            }
            $stmt_update->bindParam(':title', $title, PDO::PARAM_STR);
            $stmt_update->bindParam(':id', $value['id'], PDO::PARAM_INT);
            $stmt_update->execute();
        }
    }

    // 커밋
    $pdo->commit();
    $pdo = null;

    echo 'success'."\n";
} catch (Exception $e) {
    include __DIR__.DIRECTORY_SEPARATOR.'common_catch.php';
}

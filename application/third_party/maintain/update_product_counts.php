<?php

/* 과목분류 사용카운트 다시 맞추는 스크립트입니다 */

try {
    $log_file = basename(__FILE__, '.php').'.log';
    include __DIR__.DIRECTORY_SEPARATOR.'common_head.php';

    if (empty($branch_id)) {
        $stmt_branch_select = $pdo->prepare('SELECT * FROM branches WHERE enable=1');
        $stmt_branch_select->execute();
        $branches = $stmt_branch_select->fetchAll(PDO::FETCH_ASSOC);
        $stmt_branch_select->closeCursor();
    } else {
        $branches = array(array('id' => $branch_id));
    }

    foreach ($branches as $branch) {
        $stmt_product_category_count = $pdo->prepare('SELECT count(*) FROM product_categories WHERE branch_id=:branch_id AND enable=1');
        $stmt_product_category_count->bindParam(':branch_id', $branch['id'], PDO::PARAM_INT);
        $stmt_product_category_count->execute();
        $count = $stmt_product_category_count->fetchColumn();
        $stmt_product_category_count->closeCursor();

        if (empty($count)) {
            continue;
        }

        $stmt_product_category_select = $pdo->prepare('SELECT * FROM product_categories WHERE branch_id=:branch_id AND enable=1');
        $stmt_product_category_select->bindParam(':branch_id', $branch['id'], PDO::PARAM_INT);
        $stmt_product_category_select->execute();
        $product_categories = $stmt_product_category_select->fetchAll(PDO::FETCH_ASSOC);
        $stmt_product_category_select->closeCursor();

        // 트랜잭션 시작
        $pdo->beginTransaction();

        $stmt_select_product_counts = $pdo->prepare('SELECT count(*) FROM products WHERE product_category_id=:id AND enable=1');
        $stmt_update_product_category = $pdo->prepare('UPDATE product_categories SET product_counts=:product_counts WHERE id=:id');

        foreach ($product_categories as $index => $product_category) {
            $stmt_select_product_counts->bindParam(':id', $product_category['id'], PDO::PARAM_INT);
            $stmt_select_product_counts->execute();
            $product_count = $stmt_select_product_counts->fetchColumn();

            if ($product_category['product_counts'] == $product_count) {
                continue;
            } else {
                $stmt_update_product_category->bindParam(':product_counts', $product_count, PDO::PARAM_INT);
                $stmt_update_product_category->bindParam(':id', $product_category['id'], PDO::PARAM_INT);
                $stmt_update_product_category->execute();

                sl_log($log, 'product_category '.$product_category['id'].'(branch '.$product_category['branch_id'].') count '.$product_category['product_counts'].' change to '.$product_count);
            }
        }

        $stmt_select_product_counts->closeCursor();
        $stmt_update_product_category->closeCursor();

        // 커밋
        $pdo->commit();
    }
    $pdo = null;

    echo 'success'."\n";
} catch (Exception $e) {
    include __DIR__.DIRECTORY_SEPARATOR.'common_catch.php';
}

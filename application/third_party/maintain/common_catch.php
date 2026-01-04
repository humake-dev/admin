<?php

if (isset($pdo)) {
    // 롤백
    if ($pdo->inTransaction()) {
        $pdo->rollback();
        $log->addInfo('!! execute rollback');
    }

    $pdo = null;
}

sl_log($log, 'error :'.$e->getLine().', message:'.$e->getMessage());

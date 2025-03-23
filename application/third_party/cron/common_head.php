<?php

require __DIR__.DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'vendor'.DIRECTORY_SEPARATOR.'autoload.php';

use Monolog\Logger as Logger;
use Monolog\Handler\StreamHandler;

date_default_timezone_set('Asia/Seoul');
define('BASEPATH', true);
define('ENVIRONMENT', isset($_SERVER['CI_ENV']) ? $_SERVER['CI_ENV'] : 'development');

// 로거 채널 생성
$log = new Logger('name');

// log/your.log 파일에 로그 생성. 로그 레벨은 Info
$log_directory = realpath(__DIR__.DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'logs');
$log->pushHandler(new StreamHandler($log_directory.DIRECTORY_SEPARATOR.$log_file, Logger::INFO));

if (isset($argv[1])) {
    $branch_id = filter_var($argv[1], FILTER_VALIDATE_INT);

    if (empty($branch_id)) {
        sl_log($log, 'invalid branch_id');

        return false;
    }
}

include __DIR__.DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'config'.DIRECTORY_SEPARATOR.'config.php';
include __DIR__.DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'config'.DIRECTORY_SEPARATOR.'database.php';
include __DIR__.DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'config'.DIRECTORY_SEPARATOR.'constants.php';

$pdo = new PDO($db['pdo']['dsn'], $db['pdo']['username'], $db['pdo']['password']);
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

if (ENVIRONMENT == 'production') {
    $pdo->query('SET time_zone="Asia/Seoul"');
}

$dateTimeZone = new DateTimeZone('Asia/Seoul');

function sl_log($log, $message)
{
    if (ENVIRONMENT == 'production') {
        $log->addInfo($message);
    } else {
        echo $message."\n";
    }
}

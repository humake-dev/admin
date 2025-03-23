<?php
use OpenSwoole\Http\Server;

$server = new Server("localhost", 20020);

// 클라이언트 요청을 처리하는 핸들러 정의
$server->on("request", function ($request, $response) {

    // Swoole에서 PHP $_SERVER 전역 변수 초기화
    $_SERVER = [];
    $_SERVER['REQUEST_METHOD'] = $request->server['request_method'];
    $_SERVER['REQUEST_URI'] = $request->server['request_uri'];
    $_SERVER['QUERY_STRING'] = $request->server['query_string'] ?? '';
    $_SERVER['PHP_SELF'] = '/index.php';  // PHP_SELF 설정
    $_SERVER['SCRIPT_NAME'] = '/index.php';  // 스크립트 이름 설정
    $_SERVER['SCRIPT_FILENAME'] = __DIR__ . '/index.php';
    $_SERVER['DOCUMENT_ROOT'] = __DIR__;
    $_SERVER['SERVER_NAME'] = $request->header['host'] ?? 'localhost';
    $_SERVER['SERVER_PORT'] = $request->server['server_port'];

    // GET, POST, COOKIE 등 수동 설정
    $_GET = $request->get ?? [];
    $_POST = $request->post ?? [];
    $_COOKIE = $request->cookie ?? [];
    $_FILES = $request->files ?? [];

    // CodeIgniter를 초기화하여 실행
    ob_start();
    require __DIR__ . '/index.php';  // CodeIgniter 진입점
    $output = ob_get_clean();

    // 응답 반환
    $response->end($output);
});

echo "OpenSwoole HTTP Server is running at http://localhost:20020\n";
$server->start();

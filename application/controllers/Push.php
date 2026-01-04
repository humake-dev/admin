<?php

trait Push
{
    protected function getAccessToken($serviceAccountKeyFile) {
        // Google API 클라이언트 라이브러리를 사용하여 OAuth 2.0 토큰 생성
        $credentials = json_decode(file_get_contents($serviceAccountKeyFile), true);

    $tokenUrl = 'https://oauth2.googleapis.com/token';
    $jwtHeader = base64_encode(json_encode(['alg' => 'RS256', 'typ' => 'JWT']));
    $currentTime = time();

    // JWT Payload
    $jwtPayload = base64_encode(json_encode([
        'iss' => $credentials['client_email'],
        'scope' => 'https://www.googleapis.com/auth/firebase.messaging',
        'aud' => $tokenUrl,
        'exp' => $currentTime + 3600, // 1시간 유효
        'iat' => $currentTime,
    ]));

    // JWT 서명 생성
    $signature = '';
    openssl_sign(
        "$jwtHeader.$jwtPayload",
        $signature,
        $credentials['private_key'],
        'SHA256'
    );
    $jwt = "$jwtHeader.$jwtPayload." . base64_encode($signature);

    // OAuth 토큰 요청
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $tokenUrl);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query([
        'grant_type' => 'urn:ietf:params:oauth:grant-type:jwt-bearer',
        'assertion' => $jwt,
    ]));

    $response = curl_exec($ch);
    curl_close($ch);

    $response = json_decode($response, true);
    return $response['access_token'] ?? null;
    }
    
    // PUSH 키값이 있으면 키값 사용 아니면 기본값 사용
    protected function get_push_key()
    {
        $push_key=realpath(APPPATH.'config'.DIRECTORY_SEPARATOR.'humake-fitness.json');
        return $push_key;
    }

    protected function sendNotification($accessToken, $token, $message) {
        if (ENVIRONMENT != 'production') {
            return true;
        }

        $url = 'https://fcm.googleapis.com/v1/projects/humake-fitness/messages:send'; // YOUR_PROJECT_ID를 대체
    
        // FCM 알림 메시지
        $data = [
            'message' => [
                'token' => $token, // 주제 또는 디바이스 토큰 사용 가능
                'notification' => [
                    'title' => $message['title'],
                    'body' => $message['content'],
                ],
            ],
        ];
    
        // CURL 요청
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Authorization: Bearer ' . $accessToken,
            'Content-Type: application/json',
        ]);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    
        $response = curl_exec($ch);
        curl_close($ch);
    
        return $response;
    }
    

    protected function send_push(array $users, array $message)
    {
        if (count($users) > 1000) {
            throw new exception('Max Send 1000 User, at a time');
        }

        $accessToken=$this->getAccessToken($this->get_push_key());

        foreach ($users as $value) {
            $response[]=$this->sendNotification($accessToken, $value['token'], $message);
        }

        return $response;
    }
}

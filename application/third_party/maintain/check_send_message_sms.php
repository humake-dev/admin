<?php

try {
    $log_file = basename(__FILE__, '.php').'.log';
    include __DIR__.DIRECTORY_SEPARATOR.'common_head.php';

    send_sms(array(array('name' => '정종호', 'phone' => '01033120886')), array('title' => '제목입니다', 'content' => '내용입니다', 'sender' => '01041413726'), array('sms_id' => SMS_ID, 'sms_key' => SMS_KEY));
} catch (Exception $e) {
    include __DIR__.DIRECTORY_SEPARATOR.'common_catch.php';
}

function send_sms(array $users, array $message, array $key_set)
{
    $testmode_yn = 'N';

    if (ENVIRONMENT == 'development') {
        $testmode_yn = 'Y';

        if (defined('LOCAL_ENVIRONMENT')) {
            return array((object) array('result_code' => '1', 'success_cnt' => count($users), 'msg_type' => 'sms'));
        }
    }

    $user_list_chunk = array_chunk($users, 1000);

    $success_cnt = 0;
    foreach ($user_list_chunk as $users) {
        $receiver = array();
        $destination = array();
        foreach ($users as $value) {
            $receiver[] = $value['phone'];
            $destination[] = $value['phone'].'|'.$value['name'];
        }

        /* 메세지 전송 API params 만들기 */
        $form_params = array(
            'userid' => $key_set['sms_id'],
            'key' => $key_set['sms_key'],
            'sender' => $message['sender'],
            'receiver' => implode(',', $receiver),
            'destination' => implode(',', $destination),
            'title' => $message['title'],
            'msg' => $message['content'],
            'testmode_yn' => $testmode_yn,
        );

        if (empty($message['attach_file'])) {
            $send_data = array('form_params' => $form_params);
        } else {
            $multipart = array(array('name' => 'image', 'contents' => fopen($message['attach_file'][0]['full_path'], 'r')));
            foreach ($form_params as $key => $value) {
                $multipart[] = array('name' => $key, 'contents' => $value);
            }
            $send_data = array('multipart' => $multipart);
        }

        $client = new GuzzleHttp\Client(['verify' => false]);
        $response = $client->request('POST', 'https://apis.aligo.in/send/', $send_data);
        $send_result = json_decode($response->getBody());

        $result = true;
    }

    return $result;
}

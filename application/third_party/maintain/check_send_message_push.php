<?php

try {
    $log_file = basename(__FILE__, '.php').'.log';
    include __DIR__.DIRECTORY_SEPARATOR.'common_head.php';

    send_push(array(array('token' => 'eqGYP4xVrBQ:APA91bGsDgstcwWAJjR53p1Bozg3-r-Si-63h6ib68a2ksjy_MImpgrkYfUjgsRU3fkUipdkbawUelzm6zjSPaGK67NO-I59GWxjN0TdYD0_4DYnWlS8jLSS1jdEvi4_uU3xJ5XZePdL')), array('title' => '제목입니다', 'content' => '내용입니다'), PUSH_KEY);
} catch (Exception $e) {
    include __DIR__.DIRECTORY_SEPARATOR.'common_catch.php';
}

function send_push(array $users, array $message, $key)
{
    $client = new paragraph1\phpFCM\Client();
    $client->setApiKey($key);
    $client->injectHttpClient(new \GuzzleHttp\Client());

    // Send to topic also see https://firebase.google.com/docs/cloud-messaging/topic-messaging
    //$message->addRecipient(new Topic('your-topic'));
    $note = new paragraph1\phpFCM\Notification($message['title'], $message['content']);
    $note->setSound('default');
    $note->setIcon('ic_launcher'); //;->setBadge('ic_launcher');

    $message_obj = new paragraph1\phpFCM\Message();

    foreach ($users as $value) {
        $message_obj->addRecipient(new paragraph1\phpFCM\Recipient\Device($value['token']));
    }

    if (isset($message['picture'])) {
        $message_obj->setNotification($note)->setData(array('custom_notification' => array('title' => $message['title'], 'body' => $message['body'], 'picture' => $message['picture'], 'large_icon' => $message['picture'], 'color' => '00ACD4', 'icon' => 'ic_launcher')));
    } else {
        $message_obj->setNotification($note);
    }
    $response = $client->send($message_obj);

    // var_dump($response->getStatusCode());
    // exit;

    //  'good';
    //  exit;
    return $response->getStatusCode();
}

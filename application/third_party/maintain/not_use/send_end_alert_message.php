<?php

/* 수강이 끝나갈때 알림해주는 스크립트입니다 */

try {
    $log_file = basename(__FILE__, '.php').'.log';
    include __DIR__.DIRECTORY_SEPARATOR.'common_head.php';

    if (isset($branch_id)) {
        $stmt_seam_count = $pdo->prepare('SELECT count(*) FROM send_end_alert_messages as seam INNER JOIN branches as b ON seam.branch_id=b.id INNER JOIN message_prepares as mp ON seam.message_prepare_id=mp.id INNER JOIN message_prepare_contents as mpc ON mpc.id=mp.id WHERE b.enable=1 AND mp.enable=1 AND seam.branch_id=:branch_id');
        $stmt_seam_count->bindParam(':branch_id', $branch_id, PDO::PARAM_INT);
    } else {
        $stmt_seam_count = $pdo->prepare('SELECT count(*) FROM send_end_alert_messages as seam INNER JOIN branches as b ON seam.branch_id=b.id INNER JOIN message_prepares as mp ON seam.message_prepare_id=mp.id INNER JOIN message_prepare_contents as mpc ON mpc.id=mp.id WHERE b.enable=1 AND mp.enable=1');
    }

    $stmt_seam_count->execute();
    $seam_count = $stmt_seam_count->fetchColumn();
    $stmt_seam_count->closeCursor();

    if (!$seam_count) {
        $log->addInfo('push user execute, 0');

        return true;
    }

    if (isset($branch_id)) {
        $stmt_seam_select = $pdo->prepare('SELECT seam.*,mp.title,mpc.content,b.phone FROM send_end_alert_messages as seam INNER JOIN branches as b ON seam.branch_id=b.id INNER JOIN message_prepares as mp ON seam.message_prepare_id=mp.id INNER JOIN message_prepare_contents as mpc ON mpc.id=mp.id WHERE b.enable=1 AND mp.enable=1 AND seam.branch_id=:branch_id');
        $stmt_seam_select->bindParam(':branch_id', $branch_id, PDO::PARAM_INT);
    } else {
        $stmt_seam_select = $pdo->prepare('SELECT seam.*,mp.title,mpc.content,b.phone FROM send_end_alert_messages as seam INNER JOIN branches as b ON seam.branch_id=b.id INNER JOIN message_prepares as mp ON seam.message_prepare_id=mp.id INNER JOIN message_prepare_contents as mpc ON mpc.id=mp.id WHERE b.enable=1 AND mp.enable=1');
    }

    $stmt_seam_select->execute();
    $seam_list = $stmt_seam_select->fetchAll(PDO::FETCH_ASSOC);
    $stmt_seam_select->closeCursor();

    $stmt_count = $pdo->prepare('SELECT count(*) FROM (SELECT u.id FROM orders AS o INNER JOIN enrolls AS e ON e.order_id=o.id INNER JOIN users as u ON o.user_id=u.id INNER JOIN order_products as op ON op.order_id=o.id INNER JOIN product_relations as pr ON pr.product_id=op.product_id LEFT JOIN user_devices AS ud ON ud.user_id=u.id WHERE o.enable=1 AND u.enable=1 AND pr.product_relation_type_id=:product_relation_type_id AND o.branch_id=:branch_id GROUP BY o.user_id,e.course_id HAVING max(e.end_date)>=CURDATE() AND max(e.start_date)<=CURDATE() AND max(e.end_date)=DATE_ADD(NOW(), interval +:day_count day)) as g_person');
    $stmt_select = $pdo->prepare('SELECT o.*,ud.token,u.name,u.phone FROM orders AS o INNER JOIN enrolls AS e ON e.order_id=o.id INNER JOIN users as u ON o.user_id=u.id INNER JOIN order_products as op ON op.order_id=o.id INNER JOIN product_relations as pr ON pr.product_id=op.product_id LEFT JOIN user_devices AS ud ON ud.user_id=u.id WHERE o.enable=1 AND u.enable=1 AND pr.product_relation_type_id=:product_relation_type_id AND o.branch_id=:branch_id GROUP BY o.user_id,e.course_id HAVING max(e.end_date)>=CURDATE() AND max(e.start_date)<=CURDATE() AND max(e.end_date)=DATE_ADD(NOW(), interval +:day_count day)');

    foreach ($seam_list as $seam) {
        $stmt_count->bindValue(':product_relation_type_id', PRIMARY_COURSE_ID);
        $stmt_count->bindParam(':branch_id', $seam['branch_id'], PDO::PARAM_INT);
        $stmt_count->bindParam(':day_count', $seam['day_count'], PDO::PARAM_INT);
        $stmt_count->execute();
        $count = $stmt_count->fetchColumn();

        if (empty($count)) {
            continue;
        }

        $stmt_select->bindValue(':product_relation_type_id', PRIMARY_COURSE_ID);
        $stmt_select->bindParam(':branch_id', $seam['branch_id'], PDO::PARAM_INT);
        $stmt_select->bindParam(':day_count', $seam['day_count'], PDO::PARAM_INT);

        $stmt_select->execute();
        $list = $stmt_select->fetchAll(PDO::FETCH_ASSOC);
        $stmt_select->closeCursor();

        $sms_users = array();
        $push_users = array();

        foreach ($list as $enroll) {
            switch ($seam['type']) {
                case 'use_push_available':
                    if (empty($enroll['token'])) {
                        $sms_users[] = array('name' => $enroll['name'], 'phone' => $enroll['phone']);
                    } else {
                        $push_users[] = array('token' => $enroll['token']);
                    }

                    break;
                case 'push_only':
                    if (empty($enroll['token'])) {
                        continue;
                    }

                    $push_users[] = array('token' => $enroll['token']);
                    break;
                default:
                    $sms_users[] = array('name' => $enroll['name'], 'phone' => $enroll['phone']);
            }
        }

        if (count($push_users)) {
            send_push($push_users, array('title' => $seam['title'], 'content' => $seam['content']), PUSH_KEY);
        }

        if (count($sms_users)) {
            send_sms($sms_users, array('title' => $seam['title'], 'content' => $seam['content'], 'sender' => $seam['phone']), array('sms_id' => SMS_ID, 'sms_key' => SMS_KEY));
        }
    }

    $pdo = null;
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

        /* local test start */
        // echo 'send_sms'."\n";

        // return true;
        /* local test end */

        $client = new GuzzleHttp\Client(['verify' => false]);
        $response = $client->request('POST', 'https://apis.aligo.in/send/', $send_data);
        $send_result = json_decode($response->getBody());

        $result = true;
    }

    return $result;
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

    /* local test start */
    // echo 'send_push'."\n";

    // return true;
    /* local test end */

    $response = $client->send($message_obj);

    return $response->getStatusCode();
}

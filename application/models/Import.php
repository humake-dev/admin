<?php

defined('BASEPATH') or exit('No direct script access allowed');
require_once 'SL_Model.php';

class Import extends SL_Model
{
    public function insert_user($users)
    {
        if (!count($users)) {
            return false;
        }
        
        $sql_count = 'SELECT count(*) as cnt FROM user_access_cards WHERE card_no=?';        


        foreach($users as $user) {
            if(empty($user['card_no'])) {
                return false;
            }

            $result=$this->pdo->query($sql_count, array($user['card_no']));

            $rr=$result->row_array();
            if(!empty($rr['cnt'])) {
                echo 'user_card_no '.$user['card_no'].' duplicated';
                exit;
            }
        }


        $this->pdo->trans_start();

        $sql = 'INSERT INTO users(branch_id,name,phone,gender,birthday,registration_date,created_at,updated_at) VALUES(?,?,?,?,?,?,NOW(),NOW())';
        $sql_memo = 'INSERT INTO user_contents(user_id,content,created_at,updated_at) VALUES(?,?,NOW(),NOW())';
        $sql_card_no = 'INSERT INTO user_access_cards(user_id,card_no,created_at,updated_at) VALUES(?,?,NOW(),NOW())';

        $sql_count = 'SELECT count(*) as cnt FROM users WHERE phone=? AND branch_id=?';



        // $sql_compnay = 'INSERT INTO user_additionals(user_id,company,created_at,updated_at) VALUES(?,?,NOW(),NOW())';

        /*
        $sql_fc = 'INSERT INTO user_fcs(user_id,fc_id,created_at,updated_at) VALUES(?,?,NOW(),NOW())';
        $sql_trainer = 'INSERT INTO user_trainers(user_id,trainer_id,created_at,updated_at) VALUES(?,?,NOW(),NOW())'; */

        foreach ($users as $user) {
            $result=$this->pdo->query($sql_count, array($user['phone'], $this->session->userdata('branch_id')));

            $rr=$result->row_array();
            if(!empty($rr['cnt'])) {
                continue;
            }

           /*  $this->pdo->join('user_old_ids as uoi', 'uoi.user_id=u.id');
            $this->pdo->where(array('u.enable' => 1, 'uoi.old_id' => $user['old_id']));
            if ($this->pdo->count_all_results('users as u')) {
                continue;
            } */

            $this->pdo->query($sql, array($this->session->userdata('branch_id'),
                $user['name'],
                $user['phone'],
                $user['gender'],
                $user['birthday'],
                $user['registration_date']
            ));

            $user_id = $this->pdo->insert_id();

            if (!empty($user['card_no'])) {
                $this->pdo->query($sql_card_no, array($user_id, $user['card_no']));
            }

            if (!empty($user['memo'])) {
                $this->pdo->query($sql_memo, array($user_id, $user['memo']));
            }
            /*
            if (!empty($user['company'])) {
                $this->pdo->query($sql_compnay, array($user_id, $user['company']));
            }

            if ($this->input->post('fc')) {
                $fc_id = null;
                foreach ($this->input->post('fc') as $ll) {
                    if ($ll['prename'] == $user['fc']) {
                        $fc_id = $ll['id'];
                    }
                }

                if (!empty($fc_id)) {
                    $this->pdo->query($sql_fc, array($user_id, $fc_id));
                }
            }

            if ($this->input->post('trainer')) {
                $trainer_id = null;
                foreach ($this->input->post('trainer') as $ll) {
                    if ($ll['prename'] == $user['trainer']) {
                        $trainer_id = $ll['id'];
                    }
                }

                if (!empty($trainer_id)) {
                    $this->pdo->query($sql_trainer, array($user_id, $trainer_id));
                }
            } */

        }
        $this->pdo->trans_complete();

        if ($this->pdo->trans_status() === false) {
            $errNo = $this->pdo->_error_number();
            $errMess = $this->pdo->_error_message();

            return false;  // generate an error... or use the log_message() function to log your error
        } else {
            return true;
        }
    }

    public function insert_attendance($attendances)
    {
        if (!count($attendances)) {
            return false;
        }

        $insert_sql = 'INSERT INTO entrances(user_id,in_time,created_at) VALUES(?,?,now()) ON DUPLICATE KEY UPDATE in_time=?,created_at=now()';

        $this->pdo->trans_start();

        foreach ($attendances as $attendance) {
            $this->pdo->join('user_old_ids as uoi', 'uoi.user_id=u.id');
            $this->pdo->where(array('u.enable' => 1, 'uoi.old_id' => $attendance['user_old_id']));
            if (!$this->pdo->count_all_results('users as u')) {
                echo '<pre>';
                print_r('Order Not Exists user_id :' . $attendance['user_old_id']);
                echo '</pre>';
                continue;
            }

            $this->pdo->select('u.*');
            $this->pdo->join('user_old_ids as uoi', 'uoi.user_id=u.id');
            $this->pdo->where(array('u.enable' => 1, 'uoi.old_id' => $attendance['user_old_id']));
            $query = $this->pdo->get('users as u');
            $user = $query->row_array();

            $this->pdo->query($insert_sql, array($user['id'], $attendance['in_time'], $attendance['in_time']));
        }

        $this->pdo->trans_complete();

        if ($this->pdo->trans_status() === false) {
            return false;  // generate an error... or use the log_message() function to log your error
        } else {
            return true;
        }
    }

    public function insert_counsel($counsels)
    {
        if (!count($counsels)) {
            echo '????';
            return false;
        }
        
        $this->load->helper('text');
        $this->pdo->trans_start();

        $sql_counsel = 'INSERT INTO counsels(branch_id,title,execute_date,type,question_course,complete,created_at,updated_at) VALUES(?,?,?,?,?,?,NOW(),NOW())';
        $sql_counsel_content = 'INSERT INTO counsel_contents(id,content) VALUES(?,?)';
        $sql_counsel_admin = 'INSERT INTO counsel_admins(counsel_id,admin_id) VALUES(?,?)';
        $sql_counsel_user = 'INSERT INTO counsel_users(counsel_id,user_id) VALUES(?,?)';
        $sql_counsel_manager = 'INSERT INTO counsel_managers(counsel_id,admin_id) VALUES(?,?)';

        $sql_temp_user = 'INSERT INTO temp_users(branch_id,counsel_id,name,phone,registration_date,created_at,updated_at) VALUES(?,?,?,?,?,NOW(),NOW())';

        // $sql_counsel_update='UPDATE counsels as c INNER JOIN temp_users as tu ON tu.counsel_id=c.id SET c.execute_date=?,tu.registration_date=? WHERE tu.id=? AND c.created_at="2021-02-18 17:52:41" AND c.branch_id=36';

        foreach ($counsels as $counsel) {
            $this->pdo->where(array('tu.name' => $counsel['name'], 'tu.phone' => $counsel['phone']));
            $query = $this->pdo->get('temp_users as tu');
            if (!$this->pdo->count_all_results('users as u')) {
                print_r('Not Exists Name And Phone : ' . $counsel['name'] . '/' . $counsel['phone']);
                continue;
            }

            $this->pdo->where(array('tu.name' => $counsel['name'], 'tu.phone' => $counsel['phone']));
            $query = $this->pdo->get('temp_users as tu');
            $user = $query->row_array();

            //$this->pdo->query($sql_counsel_update, array($counsel['execute_date'],$counsel['execute_date'],$user['id']));
            //if (empty($counsel['user_id'])) {
            //    continue;
            // }

            $this->pdo->query($sql_counsel, array($this->session->userdata('branch_id'),
                ellipsize($counsel['memo'],25),
                $counsel['execute_date'],
                $counsel['type'],
                $counsel['question_course'],
                $counsel['complete']
            ));

            $counsel_id = $this->pdo->insert_id();

            if (!empty($counsel['memo'])) {
                $this->pdo->query($sql_counsel_content, array($counsel_id,
                    $counsel['memo']
                ));
            }

            if ($this->input->post('employee')) {
                $employee_id = null;
                foreach ($this->input->post('employee') as $ll) {
                    if ($counsel['employee'] == $ll['prename']) {
                        $employee_id = $ll['id'];
                    }
                }

                if (!empty($employee_id)) {
                    $this->pdo->query($sql_counsel_admin, array($counsel_id, $employee_id));
                }
            }

            if ($this->input->post('manager')) {
                $employee_id = null;
                foreach ($this->input->post('manager') as $ll) {
                    if ($counsel['manager'] == $ll['prename']) {
                        $employee_id = $ll['id'];
                    }
                }

                if (!empty($employee_id)) {
                    $this->pdo->query($sql_counsel_manager, array($counsel_id, $employee_id));
                }
            }

            if (empty($counsel['user_id'])) {
                $this->pdo->query($sql_temp_user, array($this->session->userdata('branch_id'), $counsel_id, $counsel['name'], $counsel['phone'], $counsel['execute_date']));
            } else {
                $this->pdo->query($sql_counsel_user, array($counsel_id, $counsel['user_id']));
            }
        }

        $this->pdo->trans_complete();

        if ($this->pdo->trans_status() === false) {
            return false;  // generate an error... or use the log_message() function to log your error
        } else {
            return true;
        }
    }

    public function insert_pt_log($pts)
    {
        if (!count($pts)) {
            return false;
        }

        $this->pdo->trans_start();

        $sql = 'INSERT INTO reservations(branch_id,manager_id,type,start_time,end_time,progress_time,created_at,updated_at) VALUES(?,?,?,?,?,?,now(),now())';
        $sql_ru = 'INSERT INTO reservation_users(reservation_id,user_id,enroll_id,complete,complete_at) VALUES(?,?,?,?,?)';
        $sql_content = 'INSERT INTO reservation_contents(reservation_id,content) VALUES(?,?)';

        $sql_enroll_user_log = 'INSERT INTO enroll_use_logs(enroll_id,account_id,reservation_user_id,type,updated_at,created_at) VALUES(?,?,?,?,now(),now())';

        //$sql_employee = 'SELECT * FROM admins WHERE id=? AND branch_id=?';
        $sql_commission = 'INSERT INTO accounts(account_category_id,type,branch_id,user_id,transaction_date,cash,created_at) VALUES(' . ADD_COMMISSION . ',"O",?,?,?,?,NOW())';
        $sql_account_commission = 'INSERT INTO account_commissions(account_id,enroll_id,course_id,employee_id) VALUES(?,?,?,?)';

        foreach ($pts as $pt) {
            $this->pdo->join('user_old_ids as uoi', 'uoi.user_id=u.id');
            $this->pdo->where(array('u.enable' => 1, 'uoi.old_id' => $pt['user_old_id']));
            if (!$this->pdo->count_all_results('users as u')) {
                /// echo '<pre>';
                // print_r('User Not Exists user_id :' . $pt['user_old_id']);
                // echo '</pre>';
                continue;
            }

            $this->pdo->select('u.*');
            $this->pdo->join('user_old_ids as uoi', 'uoi.user_id=u.id');
            $this->pdo->where(array('u.enable' => 1, 'uoi.old_id' => $pt['user_old_id']));
            $query = $this->pdo->get('users as u');
            $user = $query->row_array();

            $type = 'PT';
            $progress_time = 50;
            $complete_at = null;

            if ($pt['complete_type'] == 'confirm') {
                $complete_at = $pt['reservation_date'] . ' 23:59:59';
            }

            $this->pdo->join('orders as o', 'e.order_id=o.id');
            $this->pdo->join('courses as c', 'e.course_id=c.id');
            $this->pdo->where('o.transaction_date<="' . $pt['reservation_date'] . '"');
            $this->pdo->where(array('o.enable' => 1, 'o.user_id' => $user['id'], 'c.lesson_type' => 4));
            if (!$this->pdo->count_all_results('enrolls as e')) {
                echo '<pre>';
                print_r('Order Not Exists user_id :' . $user['id']);
                echo '</pre>';
                continue;
            }

            $this->pdo->select('o.*,e.course_id,e.id as enroll_id,et.trainer_id,e.quantity');
            $this->pdo->join('orders as o', 'e.order_id=o.id');
            $this->pdo->join('courses as c', 'e.course_id=c.id');
            $this->pdo->join('enroll_trainers as et', 'et.enroll_id=e.id', 'left');
            $this->pdo->where('o.transaction_date<="' . $pt['reservation_date'] . '"');
            $this->pdo->where(array('o.enable' => 1, 'o.user_id' => $user['id'], 'c.lesson_type' => 4));
            $this->pdo->order_by('o.transaction_date', 'asc');
            $query = $this->pdo->get('enrolls as e');
            $orders = $query->result_array();

            if (empty($orders[$pt['index']])) {
                /* echo '<pre>';
                echo 'index : ' . $pt['index'] . '<br />';
                echo 'orders : <br />';
                print_r($orders);
                echo '</pre>'; */

                $order = $orders[count($orders) - 1];
            } else {
                $order = $orders[$pt['index']];
            }

            $manager_id = null;

            if (!empty($pt['trainer_id'])) {
                $manager_id = $pt['trainer_id'];
            }

            $this->pdo->query($sql, array($this->session->userdata('branch_id'), $manager_id, $type, $pt['start_time'], $pt['end_time'], $progress_time));
            $reservation_id = $this->pdo->insert_id();

            //print_r($reservation_id);

            $this->pdo->query($sql_ru, array($reservation_id, $user['id'], $order['enroll_id'], $pt['complete'], $complete_at));
            $reservation_user_id = $this->pdo->insert_id();

            $this->pdo->query($sql_c, array($reservation_id, $order['course_id']));

            if (!empty($pt['content'])) {
                $this->pdo->query($sql_content, array($reservation_id, $pt['content']));
            }

            if ($pt['complete'] != 3) {
                continue;
            }

            $account_id = null;
            if (!empty($pt['trainer_id'])) {
                $this->pdo->where(array('e.id' => $pt['trainer_id'], 'e.branch_id' => $this->session->userdata('branch_id')));
                if ($this->pdo->count_all_results('admins as e')) {
                    $this->pdo->where(array('e.id' => $pt['trainer_id'], 'e.branch_id' => $this->session->userdata('branch_id')));
                    $query = $this->pdo->get('admins as e');
                    $emplyoee = $query->row_array();

                    if (!empty($emplyoee['commission_rate'])) {
                        $commission = ($order['price'] / $order['quantity']) * ($emplyoee['commission_rate'] / 100);
                        $this->pdo->query($sql_commission, array($this->session->userdata('branch_id'), $user['id'], $order['transaction_date'], $commission));
                        $account_id = $this->pdo->insert_id();

                        $this->pdo->query($sql_account_commission, array($account_id, $order['enroll_id'], $order['course_id'], $emplyoee['id']));
                    }
                }
            }

            $this->pdo->query($sql_enroll_user_log, array($order['enroll_id'], $account_id, $reservation_user_id, $pt['complete_type']));
        }

        $this->pdo->trans_complete();

        if ($this->pdo->trans_status() === false) {
            return false;  // generate an error... or use the log_message() function to log your error
        } else {
            return true;
        }
    }

    public function insert_stop_log($stops)
    {
        if (!count($stops)) {
            return false;
        }

        $sql = 'INSERT INTO user_stops(user_id,order_id,stop_start_date,stop_end_date,stop_day_count,request_date,created_at,updated_at) VALUES(?,?,?,?,?,?,?,?)';
        $sql_content = 'INSERT INTO user_stop_contents(user_stop_id,content,created_at,updated_at) VALUES(?,?,?,?)';

        $this->pdo->trans_start();

        $error_count = 0;  

        foreach ($stops as $index=>$stop) {
            $this->pdo->join('orders as o', 'e.order_id=o.id');
            $this->pdo->join('courses as c', 'e.course_id=c.id');
            // $this->pdo->where('o.transaction_date<="' . $stop['created_date'] . '"');
            $this->pdo->where(array('o.enable' => 1, 'o.user_id' => $stop['user_id'], 'c.lesson_type' => 1));
            if (!$this->pdo->count_all_results('enrolls as e')) {
                echo '<pre>';
                print_r('Order Not Exists user_id :' . $stop['user_id']);
                echo '</pre>';
                continue;
            }

            /* $this->pdo->select('o.*');
            $this->pdo->join('orders as o', 'e.order_id=o.id');
            $this->pdo->join('courses as c', 'e.course_id=c.id');
            // $this->pdo->where('o.transaction_date<="' . $stop['created_date'] . '"');
            $this->pdo->where(array('o.enable' => 1, 'o.user_id' => $stop['user_id'], 'c.lesson_type' => 1));
            $this->pdo->order_by('o.id', 'asc');
            $query = $this->pdo->get('enrolls as e');
            $orders = $query->result_array();

            if (empty($orders[$stop['index']])) { */
            $this->pdo->select('o.*');
            $this->pdo->join('orders as o', 'e.order_id=o.id');
            $this->pdo->join('courses as c', 'e.course_id=c.id');
            $this->pdo->where('e.start_date<="' . $stop['created_date'] . '" AND e.end_date>="' . $stop['created_date'] . '"');
            $this->pdo->where(array('o.enable' => 1, 'o.user_id' => $stop['user_id'], 'c.lesson_type' => 1));
            $this->pdo->order_by('e.have_datetime', 'desc');
            $query = $this->pdo->get('enrolls as e', 1, 0);
            $order = $query->row_array();
            /*echo '<pre>';
             echo $index;
             echo '<br />';
            echo 'index : ' . $stop['index'] . '<br />';
            echo 'orders : <br />';
            print_r($order);
            echo '</pre>';  */
            //$error_count++;
            /*} else {
                $order = $orders[$stop['index']];
            } */

            //print_r('error count : '.$error_count);
            $stop['order_id'] = $order['id'];

            $this->pdo->query($sql, array($stop['user_id'], $stop['order_id'], $stop['start_date'], $stop['end_date'], $stop['day_count'], $stop['created_date'], $stop['created_at'], $stop['created_at']));
            $user_stop_id = $this->pdo->insert_id();

            $this->pdo->query($sql_content, array($user_stop_id, $stop['content'], $stop['created_at'], $stop['created_at']));

            $start_date_obj = new DateTime($stop['start_date'], $this->timezone);
            $end_date_obj = new DateTime($stop['end_date'], $this->timezone);
            $cur_date_obj = new DateTime('now', $this->timezone);

            if ($start_date_obj > $cur_date_obj) {
                $this->pdo->insert('user_stop_schedules', array('user_stop_id' => $user_stop_id, 'schedule_date' => $start_date_obj->format('Y-m-d')));
                $this->pdo->update('user_stops', array('enable' => 0), array('id' => $user_stop_id));
            } else {
                $current = false;
                if ($end_date_obj > $cur_date_obj) {
                    $current = true;
                }
                $this->insert_order_stop($user_stop_id, $stop, $current);
            }
        }

        $this->pdo->trans_complete();

        if ($this->pdo->trans_status() === false) {
            return false;  // generate an error... or use the log_message() function to log your error
        } else {
            return true;
        }
    }

    protected function insert_order_stop($user_stop_id, $stop, $current = false)
    {
        $this->pdo->join('orders as o', 'e.order_id=o.id');
        //$this->pdo->join('order_ends AS oe', 'oe.order_id=o.id', 'left');
        //$this->pdo->where('e.end_date>="'.$stop['created_at'].'"');
        $this->pdo->where(array('o.enable' => 1, 'o.user_id' => $stop['user_id']));

        if ($this->pdo->count_all_results('enrolls as e')) {
            $this->pdo->select('o.*,e.start_date,e.end_date as origin_end_date,e.id as enroll_id');
            $this->pdo->join('orders as o', 'e.order_id=o.id');
            //$this->pdo->join('order_ends AS oe', 'oe.order_id=o.id', 'left');
            //$this->pdo->where('e.end_date>="'.$stop['created_at'].'"');
            $this->pdo->where(array('o.enable' => 1, 'o.user_id' => $stop['user_id']));
            $query = $this->pdo->get('enrolls as e');
            $enroll_list = $query->result_array();

            foreach ($enroll_list as $index => $enroll) {
                if ($enroll['origin_end_date'] < $stop['start_date']) {
                    continue;
                }

                $this->pdo->insert('order_stops', array('user_stop_id' => $user_stop_id, 'order_id' => $enroll['id'], 'stop_start_date' => $stop['start_date'], 'stop_end_date' => $stop['end_date'], 'stop_day_count' => $stop['day_count'], 'enable' => $current, 'created_at' => $stop['created_at'], 'updated_at' => $stop['created_at']));
                //  $this->pdo->insert_id();

                if ($current) {
                    $this->pdo->update('orders', array('stopped' => 1), array('id' => $enroll['id']));
                } else {
                    $order_stop_id = $this->pdo->insert_id();

                    $this->pdo->insert('order_stop_logs', array('order_id' => $enroll['id'], 'origin_end_date' => $enroll['origin_end_date'], 'change_end_date' => $enroll['origin_end_date'], 'stop_start_date' => $stop['start_date'], 'stop_end_date' => $stop['end_date'], 'stop_day_count' => $stop['day_count'], 'request_date' => $stop['created_date'], 'created_at' => $stop['created_at']));
                    $order_stop_log_id = $this->pdo->insert_id();
                    $this->pdo->insert('order_stop_log_order_stops', array('order_stop_log_id' => $order_stop_log_id, 'order_stop_id' => $order_stop_id));

                    /* $o_enddate_obj=new DateTime($enroll['origin_end_date'],$this->timezone);
                    $o_enddate_obj->modify('+'.$stop['day_count'].' Days');
                    $new_enddate=$o_enddate_obj->format('Y-m-d');

                    $this->pdo->update('enrolls',array('end_date'=>$new_enddate),array('id'=> $enroll['enroll_id'])); */
                }
            }
        }

        $this->pdo->join('orders as o', 'r.order_id=o.id');
        //$this->pdo->join('order_ends AS oe', 'oe.order_id=o.id', 'left');
        $this->pdo->where('DATE(r.end_datetime)>="' . $stop['created_at'] . '"');
        $this->pdo->where(array('o.enable' => 1, 'o.user_id' => $stop['user_id']));

        if ($this->pdo->count_all_results('rents as r')) {
            $this->pdo->select('o.*,DATE(r.end_datetime) as origin_end_date');
            $this->pdo->join('orders as o', 'r.order_id=o.id');
            //$this->pdo->join('order_ends AS oe', 'oe.order_id=o.id', 'left');
            $this->pdo->where('DATE(r.end_datetime)>="' . $stop['created_at'] . '"');
            $this->pdo->where(array('o.enable' => 1, 'o.user_id' => $stop['user_id']));
            $query = $this->pdo->get('rents as r');
            $rent_list = $query->result_array();

            foreach ($rent_list as $rent) {
                $this->pdo->insert('order_stops', array('user_stop_id' => $user_stop_id, 'order_id' => $rent['id'], 'stop_start_date' => $stop['start_date'], 'stop_end_date' => $stop['end_date'], 'stop_day_count' => $stop['day_count'], 'enable' => $current, 'created_at' => $stop['created_at'], 'updated_at' => $stop['created_at']));
                //$this->pdo->insert_id();

                if ($current) {
                    $this->pdo->update('orders', array('stopped' => 1), array('id' => $rent['id']));
                } else {
                    $order_stop_id = $this->pdo->insert_id();

                    $this->pdo->insert('order_stop_logs', array('order_id' => $rent['id'], 'origin_end_date' => $rent['origin_end_date'], 'change_end_date' => $rent['origin_end_date'], 'stop_start_date' => $stop['start_date'], 'stop_end_date' => $stop['end_date'], 'stop_day_count' => $stop['day_count'], 'request_date' => $stop['created_date'], 'created_at' => $stop['created_at']));
                    $order_stop_log_id = $this->pdo->insert_id();
                    $this->pdo->insert('order_stop_log_order_stops', array('order_stop_log_id' => $order_stop_log_id, 'order_stop_id' => $order_stop_id));
                }
            }
        }

        /*
        $this->pdo->join('orders as o', 'rs.order_id=o.id');
        //$this->pdo->join('order_ends AS oe', 'oe.order_id=o.id', 'left');
        $this->pdo->where('rs.end_date>="'.$stop['created_at'].'"');
        $this->pdo->where(array('o.enable' => 1, 'o.user_id' => $stop['user_id']));

        if ($this->pdo->count_all_results('rent_sws AS rs')) {
            $this->pdo->select('o.*,DATE(rs.end_datetime) as origin_end_date');
            $this->pdo->join('orders as o', 'rs.order_id=o.id');
            //$this->pdo->join('order_ends AS oe', 'oe.order_id=o.id', 'left');
            $this->pdo->where('rs.end_date>="'.$stop['created_at'].'"');
            $this->pdo->where(array('o.enable' => 1, 'o.user_id' => $stop['user_id']));
            $query = $this->pdo->get('rent_sws AS rs');
            $rent_sw_list = $query->result_array();

            foreach ($rent_sw_list as $rent_sw) {
                $this->pdo->insert('order_stops', array('user_stop_id' => $user_stop_id, 'order_id' => $rent_sw['id'], 'stop_start_date' => $stop['start_date'], 'stop_end_date' => $stop['end_date'], 'stop_day_count' => $stop['day_count'], 'enable' => $current, 'created_at' => $stop['created_at'], 'updated_at' => $stop['created_at']));

                if ($current) {
                    $this->pdo->update('orders', array('stopped' => 1), array('id' => $rent_sw['id']));
                } else {
                    $order_stop_id = $this->pdo->insert_id();

                    $this->pdo->insert('order_stop_logs', array('order_id' => $rent_sw['id'], 'origin_end_date' => $rent_sw['origin_end_date'], 'change_end_date' => $rent_sw['origin_end_date'], 'stop_start_date' => $stop['start_date'], 'stop_end_date' => $stop['end_date'], 'stop_day_count' => $stop['day_count'], 'registration_date' => $stop['created_date'], 'created_at' => $stop['created_at']));
                    $order_stop_log_id = $this->pdo->insert_id();
                    $this->pdo->insert('order_stop_log_order_stops', array('order_stop_log_id' => $order_stop_log_id, 'order_stop_id' => $order_stop_id));
                }
            }
        } */
    }

    public function insert_enroll($enrolls, $is_pt = false)
    {
        if (!count($enrolls)) {
            return false;
        }

        $sql_user = 'INSERT INTO users(branch_id,name,phone,gender,birthday,registration_date,created_at,updated_at)
    VALUES(?,?,?,?,?,?,NOW(),NOW())
    ON DUPLICATE KEY UPDATE id=LAST_INSERT_ID(id),name=?,phone=?,gender=?,birthday=?,registration_date=?,updated_at=NOW()';
        $sql_card_no = 'INSERT INTO user_access_cards(user_id,card_no,created_at,updated_at) VALUES(?,?,NOW(),NOW())';
        $sql_old_id = 'INSERT INTO user_old_ids(user_id,old_id,updated_at,created_at) VALUES(?,?,NOW(),NOW()) ON DUPLICATE KEY UPDATE updated_at=NOW()';

        $sql_find_product = 'SELECT c.*,p.price FROM courses AS c INNER JOIN products AS p ON c.product_id=p.id WHERE c.id=? AND p.branch_id=?';

        $query = $this->pdo->query($sql_find_product, array($this->input->post('course_id'), $this->session->userdata('branch_id')));
        $product_result=$query->result()[0];
        $product_id = $product_result->product_id;
        $default_price=$product_result->price;
       

        foreach ($enrolls as $index => $enroll) {
            if ($this->input->post('course_id')) {
                $user_id=0;
                
                $enrolls[$index]['course_id'] = $this->input->post('course_id');
                $enrolls[$index]['product_id'] = $product_id;

        $query = $this->pdo->query('SELECT * FROM users as u WHERE u.phone=? AND u.branch_id=?', array($enroll['phone'], $this->session->userdata('branch_id')));
        $user_result=$query->result()[0];
        $user_id=$user_result->id; 

                $enrolls[$index]['user_id'] = $user_id;
                $enrolls[$index]['original_price'] = $default_price*$enroll['quantity']; 

                if(empty($enroll['payment'])) {
                    $enrolls[$index]['price'] = 0;
                } else {
                    $enrolls[$index]['price'] =  $enrolls[$index]['original_price'] - $enroll['payment'];
                }

                if ($is_pt) {
                    if (empty($enroll['user_id'])) {
                        $this->pdo->join('user_old_ids as uoi', 'uoi.user_id=u.id');
                        $this->pdo->where(array('u.enable' => 1, 'uoi.old_id' => $enroll['user_old_id']));
                        if ($this->pdo->count_all_results('users as u')) {
                            $this->pdo->join('user_old_ids as uoi', 'uoi.user_id=u.id');
                            $this->pdo->where(array('u.enable' => 1, 'uoi.old_id' => $enroll['user_old_id']));
                            $query = $this->pdo->get('users as u');
                            $user_row = $query->row_array();

                            $user_id = $user_row['user_id'];
                        } else {
                            $this->pdo->query($sql_user, array($this->session->userdata('branch_id'),
                                $enroll['name'],
                                $enroll['phone'],
                                $enroll['gender'],
                                $enroll['birthday'],
                                $enroll['registration_date'],
                                $enroll['name'],
                                $enroll['phone'],
                                $enroll['gender'],
                                $enroll['birthday'],
                                $enroll['registration_date'],
                            ));

                            $user_id = $this->pdo->insert_id();

                            if (!empty($enroll['card_no'])) {
                                $this->pdo->query($sql_card_no, array($user_id, $enroll['card_no']));
                            }

                            if (!empty($enroll['user_old_id'])) {
                                $this->pdo->query($sql_old_id, array($user_id, $enroll['user_old_id']));
                            }
                        }

                        $enrolls[$index]['user_id'] = $user_id;
                    }

                    $query = $this->pdo->query($sql_find_product, array($this->input->post('course_id'), $this->session->userdata('branch_id')));
                    $enrolls[$index]['product_id'] = $query->result()[0]->product_id;
                }              
            } else {
                foreach ($this->input->post('course') as $ll) {
                    if ($ll['prename'] == $enroll['course']) {
                        $enrolls[$index]['course_id'] = $ll['id'];

                        $query = $this->pdo->query($sql_find_product, array($ll['id'], $this->session->userdata('branch_id')));
                        $enrolls[$index]['product_id'] = $query->result()[0]->product_id;
                        $enrolls[$index]['quantity'] = $ll['quantity'];
                    }
                }

                $course_query = $this->pdo->query($sql_find_product, array($enrolls[$index]['course_id'], $this->session->userdata('branch_id')));
                //$enrolls[$index]['price']=$course_query->result()[0]->price;
                //$enrolls[$index]['original_price']=$course_query->result()[0]->price;
            }

            $enrolls[$index]['type'] = 'C';
/* 
            if ($this->input->post('employee')) {
                foreach ($this->input->post('employee') as $ll) {
                    if ($ll['prename'] == $enroll['employee']) {
                        $enrolls[$index]['trainer_id'] = $ll['id'];
                        $enrolls[$index]['employee_id'] = $ll['id'];
                    }
                }
            } else {
                $enrolls[$index]['trainer_id'] = $course_query->result()[0]->trainer_id;
            }*/
        } 

        return $this->insert_enroll_data($enrolls);
    }

    private function insert_enroll_data($enrolls)
    {
        $result=array();

        $sql_order = 'INSERT INTO orders(branch_id,user_id,transaction_date,created_at,updated_at) VALUES(?,?,?,?,?)';
        $sql_order_product = 'INSERT INTO order_products(order_id,product_id,total_price,quantity) VALUES(?,?,?,?)';
        $sql = 'INSERT INTO enrolls(
      order_id,
      course_id,
      start_date,
      end_date,
      insert_quantity,
      quantity,
      use_quantity,
      have_datetime
    ) VALUES(?,?,?,?,?,?,?,?)';

        $sql_enroll_trainer = 'INSERT INTO enroll_trainers(enroll_id,trainer_id) VALUES(?,?)';
        $sql_account = 'INSERT INTO accounts(account_category_id,branch_id,user_id,transaction_date,credit,created_at,updated_at) VALUES(' . ADD_ENROLL . ',?,?,?,?,NOW(),NOW())';
        $sql_refund_account = 'INSERT INTO accounts(account_category_id,branch_id,user_id,transaction_date,cash,type,created_at,updated_at) VALUES(' . REFUND_ENROLL . ',?,?,?,?,"O",NOW(),NOW())';
        $sql_order_end = 'INSERT INTO order_ends(order_id,created_at) VALUES (?,?)';

        $sql_account_order = 'INSERT INTO account_orders(account_id,order_id) VALUES(?,?)';
        $sql_account_product = 'INSERT INTO account_products(account_id,product_id) VALUES(?,?)';
        $sql_update_enroll = 'UPDATE enrolls SET quantity=?,use_quantity=? WHERE id=?';

        $sql_memo = 'INSERT INTO order_contents(order_id,content,created_at,updated_at) VALUES(?,?,NOW(),NOW())';

        $this->pdo->trans_start();

        foreach ($enrolls as $enroll) {
            $this->pdo->query($sql_order, array(
                $this->session->userdata('branch_id'),
                $enroll['user_id'],
                $enroll['transaction_date'],
                $enroll['created_at'],
                $enroll['updated_at'],                
            ));
            $order_id = $this->pdo->insert_id();

            $this->pdo->query($sql_order_product, array(
                $order_id,
                $enroll['product_id'],
                $enroll['price'],
                $enroll['quantity']
            ));

            $this->pdo->query(
                $sql,
                array(
                    $order_id,
                    $enroll['course_id'],
                    $enroll['start_date'],
                    $enroll['end_date'],
                    $enroll['quantity'],
                    $enroll['quantity'],
                    $enroll['use_quantity'],
                    $enroll['have_datetime']
                )
            );

            $enroll_id = $this->pdo->insert_id();

            if (!empty($enroll['trainer_id'])) {
                $this->pdo->query($sql_enroll_trainer, array($enroll_id, $enroll['trainer_id']));
            }

            $this->pdo->query($sql_account, array($this->session->userdata('branch_id'), $enroll['user_id'], $enroll['transaction_date'], $enroll['price']));
            $account_id = $this->pdo->insert_id();

            $this->pdo->query($sql_account_order, array($account_id, $order_id));
            $this->pdo->query($sql_account_product, array($account_id, $enroll['product_id']));

            $result[] = array('user_id' => $enroll['user_id'], 'order_id' => $order_id, 'enroll_id' => $enroll_id);

            if(!empty($enroll['content'])) {
                $this->pdo->query($sql_memo, array($order_id, $enroll['memo']));
            }

            if (!empty($enroll['refund'])) {
                $this->pdo->join('orders as o', 'e.order_id=o.id');
                $this->pdo->join('order_products as op', 'op.order_id=o.id');
                $this->pdo->join('order_ends as oe', 'oe.order_id=o.id', 'left');
                $this->pdo->where('oe.id is null');
                $this->pdo->where(array('o.enable' => 1, 'op.product_id' => $enroll['product_id'], 'o.user_id' => $enroll['user_id']));

                //print_r($this->pdo->count_all_results('enrolls as e'));

                if (!$this->pdo->count_all_results('enrolls as e')) {
                    continue;
                }

                $this->pdo->select('e.*');
                $this->pdo->join('orders as o', 'e.order_id=o.id');
                $this->pdo->join('order_products as op', 'op.order_id=o.id');
                $this->pdo->join('order_ends as oe', 'oe.order_id=o.id', 'left');
                $this->pdo->where('oe.id is null');
                $this->pdo->where(array('o.enable' => 1, 'op.product_id' => $enroll['product_id'], 'o.user_id' => $enroll['user_id']));
                $this->pdo->order_by('o.id', 'desc');
                $query = $this->pdo->get('enrolls as e');
                $order = $query->row_array();

                $order_id = $order['order_id'];

                $this->pdo->query($sql_refund_account, array($this->session->userdata('branch_id'), $enroll['user_id'], $enroll['refund_date'], $enroll['refund_value']));
                $account_id = $this->pdo->insert_id();

                $this->pdo->query($sql_order_end, array($order_id, $enroll['refund_datetime']));
                $this->pdo->query($sql_update_enroll, array($enroll['quantity'], $enroll['use_quantity'], $order['id']));

                $this->pdo->query($sql_account_order, array($account_id, $order_id));
                $this->pdo->query($sql_account_product, array($account_id, $enroll['product_id']));
            }

            $this->pdo->where(array('u.id' => $enroll['user_id']));
            $user_count = $this->pdo->count_all_results('users as u');

            if (empty($user_count)) {
                echo 'user_id not exists :' . $enroll['user_id'] . '<br />';
            } else {
            //    $this->pdo->select('u.registration_date');
            //    $this->pdo->where(array('u.id' => $enroll['user_id']));
            //    $query = $this->pdo->get('users as u');
              //  $user_content = $query->row_array();

               /* if ($user_content['registration_date'] > $enroll['transaction_date']) {
                    $this->pdo->update('users', array('registration_date' => $enroll['transaction_date']), array('id' => $enroll['user_id']));
                } */
            }
        }

        $this->pdo->trans_complete();

        
            echo '<pre>';
            print_r($result);
            echo '</pre>';
            exit;
        if ($this->pdo->trans_status() === false) {
            return false;  // generate an error... or use the log_message() function to log your error
        } else {
            return true;
        }
    }

    public function insert_rent($rents)
    {
        if (!count($rents)) {
            return false;
        }

        $facility_id=$this->input->post('facility_id');
        $sql_find_product = 'SELECT * FROM facilities as f INNER JOIN products as p ON f.product_id=p.id WHERE f.id=? AND p.branch_id=?';
        $facility_query = $this->pdo->query($sql_find_product, array($facility_id, $this->session->userdata('branch_id')));
        $product_id = $facility_query->result()[0]->product_id;

        /*            
        foreach ($rents as $index => $rent) {
            foreach ($this->input->post('locker') as $ll) {
                if ($ll['prename'] == $rent['locker']) {
                    $rents[$index]['locker_id'] = $ll['id'];

                    $facility_query = $this->pdo->query($sql_find_product, array($ll['id'], $this->session->userdata('branch_id')));
                    $rents[$index]['product_id'] = $facility_query->result()[0]->product_id;
                }
            }
        } */

        $this->pdo->trans_start();

        $sql_order = 'INSERT INTO orders(branch_id,user_id,transaction_date,created_at,updated_at) VALUES(?,?,?,?,?)';
        $sql_order_product = 'INSERT INTO order_products(order_id,product_id) VALUES(?,?)';
        $sql = 'INSERT INTO rents(order_id,facility_id,no, insert_quantity,start_datetime,end_datetime) VALUES(?,?,?,?,?,?)';
        $sql_account = 'INSERT INTO accounts(account_category_id,branch_id,user_id,transaction_date,created_at) VALUES(' . ADD_RENT . ',?,?,?,NOW())';
        $sql_account_order = 'INSERT INTO account_orders(account_id,order_id) VALUES(?,?)';
        $sql_account_product = 'INSERT INTO account_products(account_id,product_id) VALUES(?,?)';        

        foreach ($rents as $rent) {
            $query = $this->pdo->query('SELECT * FROM users as u WHERE u.phone=? AND u.branch_id=?', array($rent['phone'], $this->session->userdata('branch_id')));
            $user_result=$query->result()[0];
            $user_id=$user_result->id;
            
            $this->pdo->query($sql_order, array(
                $this->session->userdata('branch_id'),
                $user_id,
                $rent['transaction_date'],
                $rent['updated_at'],
                $rent['created_at'],                                
            //    $rent['cash'],
             //   $rent['cash'],
             //   $rent['cash'],
            ));
            $order_id = $this->pdo->insert_id();

            $this->pdo->query($sql_order_product, array(
                $order_id,
                $product_id
            ));

            $this->pdo->query($sql, array(
                $order_id,
                $facility_id,
                $rent['no'],
                $rent['insert_quantity'],
                $rent['start_date'] . ' 00:00:01',
                $rent['end_date'] . ' 23:59:59',
            ));

            $this->pdo->query($sql_account, array($this->session->userdata('branch_id'), $user_id, $rent['transaction_date']));
            $account_id = $this->pdo->insert_id();
            $this->pdo->query($sql_account_order, array($account_id, $order_id));
            $this->pdo->query($sql_account_product, array($account_id, $product_id));           
        }
        $this->pdo->trans_complete();

        if ($this->pdo->trans_status() === false) {
            return false;  // generate an error... or use the log_message() function to log your error
        } else {
            return true;
        }
    }


    public function insert_rent_sw($rent_sws)
    {
        if (!count($rent_sws)) {
            return false;
        }

        $product_id=$this->input->post('product_id');


        /*            
        foreach ($rents as $index => $rent) {
            foreach ($this->input->post('locker') as $ll) {
                if ($ll['prename'] == $rent['locker']) {
                    $rents[$index]['locker_id'] = $ll['id'];

                    $facility_query = $this->pdo->query($sql_find_product, array($ll['id'], $this->session->userdata('branch_id')));
                    $rents[$index]['product_id'] = $facility_query->result()[0]->product_id;
                }
            }
        } */

        $this->pdo->trans_start();

        $sql_order = 'INSERT INTO orders(branch_id,user_id,transaction_date,created_at,updated_at) VALUES(?,?,?,?,?)';
        $sql_order_product = 'INSERT INTO order_products(order_id,product_id) VALUES(?,?)';
        $sql = 'INSERT INTO rent_sws(order_id,insert_quantity,start_date,end_date) VALUES(?,?,?,?)';
        $sql_account = 'INSERT INTO accounts(account_category_id,branch_id,user_id,transaction_date,created_at) VALUES(' . ADD_ORDER . ',?,?,?,NOW())';
        $sql_account_order = 'INSERT INTO account_orders(account_id,order_id) VALUES(?,?)';
        $sql_account_product = 'INSERT INTO account_products(account_id,product_id) VALUES(?,?)';        

        foreach ($rent_sws as $rent_sw) {
            $query = $this->pdo->query('SELECT * FROM users as u WHERE u.phone=? AND u.branch_id=?', array($rent_sw['phone'], $this->session->userdata('branch_id')));
            $user_result=$query->result()[0];
            $user_id=$user_result->id;
            
            $this->pdo->query($sql_order, array(
                $this->session->userdata('branch_id'),
                $user_id,
                $rent_sw['transaction_date'],
                $rent_sw['updated_at'],
                $rent_sw['created_at']
            ));
            $order_id = $this->pdo->insert_id();

            $this->pdo->query($sql_order_product, array(
                $order_id,
                $product_id
            ));

            $this->pdo->query($sql, array(
                $order_id,
                $rent_sw['insert_quantity'],
                $rent_sw['start_date'],
                $rent_sw['end_date']
            ));

            $this->pdo->query($sql_account, array($this->session->userdata('branch_id'), $user_id, $rent_sw['transaction_date']));
            $account_id = $this->pdo->insert_id();
            $this->pdo->query($sql_account_order, array($account_id, $order_id));
            $this->pdo->query($sql_account_product, array($account_id, $product_id));              
        }
        $this->pdo->trans_complete();

        if ($this->pdo->trans_status() === false) {
            return false;  // generate an error... or use the log_message() function to log your error
        } else {
            return true;
        }
    }    

    public function insert_enroll_content($enroll_contents)
    {
        if (!count($enroll_contents)) {
            return false;
        }

        $this->pdo->trans_start();

        $sql_order_content = 'INSERT INTO order_contents(order_id,content,created_at,updated_at) VALUES(?,?,NOW(),NOW())';

        foreach ($enroll_contents as $enroll_content) {
            $this->pdo->join('enrolls as e', 'e.order_id=o.id');
            $this->pdo->where(array('o.enable' => 1, 'o.user_id' => $enroll_content['user_id'], 'o.transaction_date' => $enroll_content['transaction_date'], 'e.start_date' => $enroll_content['start_date'], 'e.end_date' => $enroll_content['end_date']));
            $order_count = $this->pdo->count_all_results('orders as o');
            if (empty($order_count)) {
                echo '<pre>';
                print_r('User Have Not Order :' . $enroll_content['user_old_id']);
                echo '</pre>';
                continue;
            }

            if ($order_count > 1) {
                echo '<pre>';
                print_r('1 More Order :' . $enroll_content['user_old_id']);
                echo '</pre>';
                continue;
            }

            $this->pdo->select('o.*,e.start_date,e.end_date');
            $this->pdo->join('enrolls as e', 'e.order_id=o.id');
            $this->pdo->where(array('o.enable' => 1, 'o.user_id' => $enroll_content['user_id'], 'o.transaction_date' => $enroll_content['transaction_date'], 'e.start_date' => $enroll_content['start_date'], 'e.end_date' => $enroll_content['end_date']));
            $query = $this->pdo->get('orders as o');
            $order = $query->row_array();

            $this->pdo->query($sql_order_content, array(
                $order['id'],
                $enroll_content['content']
            ));
        }

        $this->pdo->trans_complete();

        if ($this->pdo->trans_status() === false) {
            return false;  // generate an error... or use the log_message() function to log your error
        } else {
            return true;
        }
    }

    public function modify_pt_log($pts)
    {
        if (!count($pts)) {
            return false;
        }

        $this->pdo->trans_start();

        $sql_reservation = 'UPDATE reservations SET manager_id=? WHERE id=? AND manager_id is null AND branch_id=?';
        //$sql_enroll_log='UPDATE enroll_use_logs SET account_id=? WHERE reservation_user_id=? AND account_id IS NULL';

        $sql_commission = 'INSERT INTO accounts(account_category_id,type,branch_id,user_id,transaction_date,cash,created_at) VALUES(' . ADD_COMMISSION . ',"O",?,?,?,?,NOW())';
        $sql_account_commission = 'INSERT INTO account_commissions(account_id,enroll_id,course_id,employee_id) VALUES(?,?,?,?)';

        foreach ($pts as $index => $pt) {
            $this->pdo->join('user_old_ids as uoi', 'uoi.user_id=u.id');
            $this->pdo->where(array('u.enable' => 1, 'uoi.old_id' => $pt['user_old_id']));
            if (!$this->pdo->count_all_results('users as u')) {
                echo '<pre>';
                print_r('User Not Exists user_id :' . $pt['user_old_id']);
                echo '</pre>';

                continue;
            }

            $this->pdo->select('u.*');
            $this->pdo->join('user_old_ids as uoi', 'uoi.user_id=u.id');
            $this->pdo->where(array('u.enable' => 1, 'uoi.old_id' => $pt['user_old_id']));
            $query = $this->pdo->get('users as u');
            $user = $query->row_array();

            //////////////

            /*$manager_id = null;
            if (!$this->input->post('employee')) {
                echo 'error1';
                exit;
            }

            foreach ($this->input->post('employee') as $ll) {

                if (strpos(trim($ll['prename']), trim($pt['employee'])) !== false) {
                    $manager_id = $ll['id'];
                }
            }

            if (empty($manager_id)) {
                echo '<pre>';
                print_r('Reservation Not Exists manager_id :' . $pt['user_old_id']);
                echo '</pre>';

                continue;
            }*/

            ///////

            $this->pdo->join('reservation_users as ru', 'ru.reservation_id=r.id');
            $this->pdo->join('enrolls as e', 'ru.enroll_id=e.id');
            $this->pdo->join('orders as o', 'e.order_id=o.id');
            $this->pdo->where('r.start_time="' . $pt['start_time'] . '"');
            $this->pdo->where(array('r.enable' => 1, 'ru.user_id' => $user['id']));
            if (!$this->pdo->count_all_results('reservations as r')) {
                echo '<pre>';
                print_r('Reservation Not Exists user_id :' . $user['id']);
                echo '</pre>';

                continue;
            }

            $this->pdo->select('r.*,o.price,e.quantity,o.transaction_date,e.quantity,ru.enroll_id,o.price,rc.course_id,ru.id as reservation_user_id');
            $this->pdo->join('reservation_users as ru', 'ru.reservation_id=r.id');
            $this->pdo->join('enrolls as e', 'ru.enroll_id=e.id');
            $this->pdo->join('orders as o', 'e.order_id=o.id');
            $this->pdo->where('r.start_time="' . $pt['start_time'] . '"');
            $this->pdo->where(array('r.enable' => 1, 'ru.user_id' => $user['id']));
            $query = $this->pdo->get('reservations as r');
            $reservation = $query->row_array();

            $this->pdo->query($sql_reservation, array($pt['employee'], $reservation['id'], $reservation['branch_id']));

            $account_id = null;
            if (!empty($pt['employee'])) {
                $this->pdo->where(array('e.id' => $pt['employee'], 'e.branch_id' => $this->session->userdata('branch_id')));
                if ($this->pdo->count_all_results('admins as e')) {
                    $this->pdo->where(array('e.id' => $pt['employee'], 'e.branch_id' => $this->session->userdata('branch_id')));
                    $query = $this->pdo->get('admins as e');
                    $emplyoee = $query->row_array();

                    if (!empty($emplyoee['commission_rate'])) {
                        $commission = ($reservation['price'] / $reservation['quantity']) * ($emplyoee['commission_rate'] / 100);
                        $this->pdo->query($sql_commission, array($this->session->userdata('branch_id'), $user['id'], $reservation['transaction_date'], $commission));
                        $account_id = $this->pdo->insert_id();

                        $this->pdo->query($sql_account_commission, array($account_id, $reservation['enroll_id'], $reservation['course_id'], $emplyoee['id']));
                    }
                }
            }
        }

        $this->pdo->trans_complete();

        if ($this->pdo->trans_status() === false) {
            return false;  // generate an error... or use the log_message() function to log your error
        } else {
            return true;
        }
    }

    public function modify_stop_log($stops)
    {
        if (!count($stops)) {
            return false;
        }

        $this->pdo->trans_start();

        $sql = 'INSERT INTO user_stops(user_id,order_id,stop_start_date,stop_end_date,stop_day_count,registration_date,created_at,updated_at) VALUES(?,?,?,?,?,?,?,?)';
        $sql_content = 'INSERT INTO user_stop_contents(user_stop_id,content,created_at,updated_at) VALUES(?,?,?,?)';

        //$created_at = $this->now;
        $error_count = 0;

        foreach ($stops as $stop) {
            $this->pdo->join('orders as o', 'e.order_id=o.id');
            $this->pdo->join('courses as c', 'e.course_id=c.id');
            // $this->pdo->where('o.transaction_date<="' . $stop['created_date'] . '"');
            $this->pdo->where(array('o.enable' => 1, 'o.user_id' => $stop['user_id'], 'c.lesson_type' => 1));
            if (!$this->pdo->count_all_results('enrolls as e')) {
                echo '<pre>';
                print_r('Order Not Exists user_id :' . $stop['user_id']);
                echo '</pre>';
                continue;
            }

            $this->pdo->select('o.*');
            $this->pdo->join('orders as o', 'e.order_id=o.id');
            $this->pdo->join('courses as c', 'e.course_id=c.id');
            // $this->pdo->where('o.transaction_date<="' . $stop['created_date'] . '"');
            $this->pdo->where(array('o.enable' => 1, 'o.user_id' => $stop['user_id'], 'c.lesson_type' => 1));
            $this->pdo->order_by('o.id', 'asc');
            $query = $this->pdo->get('enrolls as e');
            $orders = $query->result_array();

            if (empty($orders[$stop['index']])) {
                $this->pdo->select('o.*');
                $this->pdo->join('orders as o', 'e.order_id=o.id');
                $this->pdo->join('courses as c', 'e.course_id=c.id');
                $this->pdo->where('o.transaction_date<="' . $stop['created_date'] . '"');
                $this->pdo->where(array('o.enable' => 1, 'o.user_id' => $stop['user_id'], 'c.lesson_type' => 1));
                $this->pdo->order_by('o.id', 'asc');
                $query = $this->pdo->get('enrolls as e');
                $orders2 = $query->result_array();
                /* echo '<pre>';
                echo 'index : ' . $stop['index'] . '<br />';
                echo 'orders : <br />';
                print_r($orders);
                echo '</pre>'; */
                $error_count++;
                $order = $orders2[count($orders2) - 1];
            } else {
                $order = $orders[$stop['index']];
            }

            //print_r('error count : '.$error_count);
            $stop['order_id'] = $order['id'];
            //$stop['created_at'] = $stop['created_date'];


            $this->insert_order_stop($user_stop_id, $stop, $current);
        }

        $this->pdo->trans_complete();

        if ($this->pdo->trans_status() === false) {
            return false;  // generate an error... or use the log_message() function to log your error
        } else {
            return true;
        }
    }

    public function modify_order_transfer($ots)
    {
        if (!count($ots)) {
            return false;
        }

        $this->pdo->trans_start();

        $sql = 'UPDATE order_transfers SET origin_start_date=? WHERE id=?';
        $sql2 = 'UPDATE order_transfers SET origin_quantity=? WHERE id=?';

        foreach ($ots as $ot) {
            $this->pdo->join('users as u1', 'ot.giver_id=u1.id');
            $this->pdo->join('users as u2', 'ot.recipient_id=u2.id');
            $this->pdo->where('ot.origin_end_date="' . $ot['end_date'] . '"');
            $this->pdo->where(array('ot.enable' => 1, 'u1.name' => $ot['origin_user_name'], 'u2.name' => $ot['change_user_name'], 'u1.branch_id' => $ot['origin_branch_id'], 'u2.branch_id' => $ot['change_branch_id']));
            if (!$this->pdo->count_all_results('order_transfers as ot')) {
                echo '<pre>';
                print_r('Order Not Exists user_id :' . $ot['origin_user_name']);
                echo '</pre>';
                continue;
            }

            $this->pdo->select('ot.*');
            $this->pdo->join('users as u1', 'ot.giver_id=u1.id');
            $this->pdo->join('users as u2', 'ot.recipient_id=u2.id');
            $this->pdo->where('ot.origin_end_date="' . $ot['end_date'] . '"');
            $this->pdo->where(array('ot.enable' => 1, 'u1.name' => $ot['origin_user_name'], 'u2.name' => $ot['change_user_name'], 'u1.branch_id' => $ot['origin_branch_id'], 'u2.branch_id' => $ot['change_branch_id']));
            $query = $this->pdo->get('order_transfers as ot');
            $ot_content = $query->row_array();


            if (empty($ot_content)) {
                echo 'not exists ' . $ot['origin_user_name'];
                continue;
            }

            $this->pdo->query($sql, array($ot['start_date'], $ot_content['id']));

            if (!empty($ot['insert_quantity'])) {
                $this->pdo->query($sql2, array($ot['insert_quantity'], $ot_content['id']));
            }
        }

        $this->pdo->trans_complete();

        if ($this->pdo->trans_status() === false) {
            return false;  // generate an error... or use the log_message() function to log your error
        } else {
            return true;
        }
    }

    public function modify_fc($fcs)
    {
        if (!count($fcs)) {
            return false;
        }


        $fc_id = 965;

        $this->pdo->trans_start();

        $sql = 'INSERT INTO user_fcs(user_id,fc_id) VALUES(?,?)';
        $sql_update = 'UPDATE user_fcs SET fc_id=? WHERE user_id=?';

        foreach ($fcs as $fc) {
            $this->pdo->where(array('u.enable' => 1, 'u.name' => $fc['name'], 'u.branch_id' => $this->session->userdata('branch_id')));
            $user_name_count = $this->pdo->count_all_results('users as u');
            if (empty($user_name_count)) {
                echo '<pre>';
                print_r('User Not Exists user_name :' . $fc['name']);
                echo '</pre>';
                continue;
            }

            if ($user_name_count == 1) {
                $this->pdo->where(array('u.enable' => 1, 'u.name' => $fc['name'], 'u.branch_id' => $this->session->userdata('branch_id')));
                $query = $this->pdo->get('users as u');
                $user_content = $query->row_array();
            } else {
                $this->pdo->where(array('u.enable' => 1, 'u.name' => $fc['name'], 'u.phone' => $fc['phone'], 'u.branch_id' => $this->session->userdata('branch_id')));
                if (!$this->pdo->count_all_results('users as u')) {
                    echo '<pre>';
                    print_r('User Not Exists user_name and phone :' . $fc['name'] . '/' . $fc['phone']);
                    echo '</pre>';
                    continue;
                }

                $this->pdo->where(array('u.enable' => 1, 'u.name' => $fc['name'], 'u.phone' => $fc['phone'], 'u.branch_id' => $this->session->userdata('branch_id')));
                $query = $this->pdo->get('users as u');
                $user_content = $query->row_array();
            }

            $this->pdo->where(array('uf.user_id' => $user_content['id']));
            if ($this->pdo->count_all_results('user_fcs as uf')) {
                $this->pdo->query($sql_update, array($fc_id, $user_content['id']));
            } else {
                $this->pdo->query($sql, array($user_content['id'], $fc_id));
            }


        }

        $this->pdo->trans_complete();

        if ($this->pdo->trans_status() === false) {
            return false;  // generate an error... or use the log_message() function to log your error
        } else {
            return true;
        }
    }
}

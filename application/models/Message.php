<?php

defined('BASEPATH') or exit('No direct script access allowed');
require_once 'SL_Model.php';

class Message extends SL_Model
{
    protected $table = 'messages';
    protected $table_content = 'message_contents';
    protected $table_content_required = true;
    protected $accepted_attributes = array('branch_id', 'type', 'title', 'send_all', 'created_at', 'updated_at');

    protected function get_index_data($per_page = 1000, $page = 0, $order = null, $desc = null, $enable = true)
    {
        $this->pdo->select('m.*,mu.id as mu_id,
        (SELECT count(*) FROM message_users WHERE message_id=m.id) as mu_count,
        (SELECT SUM(success_cnt) FROM message_sms_results WHERE message_id=m.id GROUP BY message_id) as success_cnt,
        (SELECT SUM(error_cnt) FROM message_sms_results WHERE message_id=m.id GROUP BY message_id) as error_cnt,
        (SELECT GROUP_CONCAT(distinct msg_type) FROM message_sms_results WHERE message_id=m.id GROUP BY message_id) as msg_type
        ', false);
        $this->pdo->join('message_users AS mu', 'mu.message_id=m.id', 'left');
        $this->pdo->join('users AS u', 'mu.user_id=u.id', 'left');
        $this->pdo->where(array('m.branch_id' => $this->session->userdata('branch_id'), 'm.enable' => 1));

        if (!empty($this->type)) {
            $this->pdo->where(array('m.type' => $this->type));
        }

        if (!empty($this->user_id)) {
            $this->pdo->where(array('mu.user_id' => $this->user_id, 'mu.enable' => 1));
        }

        if (!empty($this->temp_user_id)) {
            $this->pdo->join('message_temp_users AS mtu', 'mtu.message_id=m.id', 'left');
            $this->pdo->where(array('mtu.temp_user_id' => $this->temp_user_id));
        }

        $this->pdo->order_by('m.id', 'desc');
        $this->pdo->group_by('m.id');

        $query = $this->pdo->get($this->table . ' as m', $per_page, $page);

        return $query->result_array();
    }

    protected function get_content_data($id)
    {
        $this->pdo->select('m.*,ms.admin_id,a.name as sender,mc.content');
        $this->pdo->join('message_contents as mc', 'mc.id=m.id');
        $this->pdo->join('message_users AS mu', 'mu.message_id=m.id', 'left');
        $this->pdo->join('users AS u', 'mu.user_id=u.id', 'left');
        $this->pdo->join('message_senders as ms', 'ms.message_id=m.id', 'left');
        $this->pdo->join('admins as a', 'ms.admin_id=a.id', 'left');

        $this->pdo->where(array('m.' . $this->p_id => $id));
        $this->pdo->group_by('m.id');
        $query = $this->pdo->get($this->table . ' as m');

        return $query->row_array();
    }

    public function get_count($id = null)
    {
        $this->pdo->select('count(*) as count');

        if (isset($id)) {
            $this->pdo->where(array('m.id' => $id));

            return $this->pdo->count_all_results($this->table . ' as m');
        }

        if (!empty($this->type)) {
            $this->pdo->where(array('m.type' => $this->type));
        }

        if (!empty($this->user_id)) {
            $this->pdo->join('message_users AS mu', 'mu.message_id=m.id', 'left');
            $this->pdo->where(array('mu.user_id' => $this->user_id, 'mu.enable' => 1));
        }

        if (!empty($this->temp_user_id)) {
            $this->pdo->join('message_temp_users AS mtu', 'mtu.message_id=m.id', 'left');
            $this->pdo->where(array('mtu.temp_user_id' => $this->temp_user_id));
        }

        $this->pdo->where(array('m.branch_id' => $this->session->userdata('branch_id'), 'm.enable' => 1));
        $this->pdo->group_by('m.id');

        return $this->pdo->count_all_results($this->table . ' as m');
    }

    public function get_push_user(array $users = null, array $not_users=null)
    {
        $this->pdo->select('ud.user_id,ud.token');

        if (is_array($users)) {
            if (count($users)) {
                $this->pdo->where_in('u.id', $users);
            }
        }

        if (is_array($not_users)) {
            if (count($not_users)) {
                $this->pdo->where_not_in('u.id', $not_users);
            }
        }        

        $this->pdo->join('users as u', 'ud.user_id=u.id');
        $this->pdo->where(array('u.branch_id'=>$this->session->userdata('branch_id'),'u.enable'=>1, 'ud.enable'=>1));
        $this->pdo->where('NOT EXISTS(SELECT * FROM user_transfers WHERE user_id=u.id AND old_branch_id=u.branch_id AND enable=1)');
        $query = $this->pdo->get('user_devices as ud');

        return $query->result_array();
    }

    public function get_sms_user(array $users = null, array $not_users=null)
    {
        $this->pdo->select('u.id as user_id,phone,name');

        if (is_array($users)) {
            if (count($users)) {
                $this->pdo->where_in('u.id', $users);
            }
        }

        if (is_array($not_users)) {
            if (count($not_users)) {
                $this->pdo->where_not_in('u.id', $not_users);
            }
        }

        $this->pdo->where(array('u.branch_id'=>$this->session->userdata('branch_id'),'u.enable'=>1));
        $this->pdo->where('NOT EXISTS(SELECT * FROM user_transfers WHERE user_id=u.id AND old_branch_id=u.branch_id AND enable=1)');
        $this->pdo->where('u.phone is not null AND length(u.phone)>6');
        $query = $this->pdo->get('users as u');

        return $query->result_array();
    }

    public function get_sms_temp_user(array $users =null, array $not_users=null)
    {
        $this->pdo->select('id as temp_user_id,phone,name');

        if (is_array($users)) {
            if (count($users)) {
                $this->pdo->where_in('id', $users);
            }
        }

        if (is_array($not_users)) {
            if (count($not_users)) {
                $this->pdo->where_not_in('id', $not_users);
            }
        }

        $this->pdo->where(array('branch_id'=>$this->session->userdata('branch_id'),'enable'=>1));
        $query = $this->pdo->get('temp_users');

        return $query->result_array();
    }

    // 메세지 기록은 실제로 지우지 않도록 한다.
    public function delete($id)
    {
        return $this->pdo->update($this->table, array('enable' => 0,'updated_at'=>$this->now), array('id' => $id));
    }
}

<?php

defined('BASEPATH') or exit('No direct script access allowed');
require_once 'SL_Model.php';

class MessageAnalysis extends SL_Model
{
    protected $table = 'messages';

    protected function get_index_data($per_page = 1000, $page = 0, $order = null, $desc = null, $enable = true)
    {
        $this->pdo->select('m.branch_id,sum(msr.success_cnt) as success_cnt,sum(msr.error_cnt) as error_cnt,msg_type,result_code,b.title as branch_name,max(m.created_at) as last_sended_at');
        $this->pdo->join('message_sms_results as msr', 'msr.message_id=m.id');
        $this->pdo->join('branches as b', 'm.branch_id=b.id');

        if (isset($this->start_date)) {
            $this->pdo->where('m.created_at >=', $this->start_date);
        }

        if (isset($this->end_date)) {
            $this->pdo->where('m.created_at <=', $this->end_date);
        }

        if(empty($this->branch_id)) {
            if ($this->session->userdata('center_id')) {
                $this->pdo->where(array('m.enable' => 1));
            } else {
            $this->pdo->where(array('m.branch_id' => $this->session->userdata('branch_id'), 'm.enable' => 1));
            }
        } else {
            $this->pdo->where(array('m.enable' => 1));
            $this->pdo->where_in('m.branch_id',$this->branch_id);
        }      

        $this->pdo->group_by('m.branch_id');
        $query = $this->pdo->get($this->table . ' as m', $per_page, $page);

        return $query->result_array();
    }

    public function get_count($id = null)
    {
        $this->pdo->select('count(*) as count');
        $this->pdo->join('message_sms_results as msr', 'msr.message_id=m.id');
        $this->pdo->join('branches as b', 'm.branch_id=b.id');

        if (isset($id)) {
            $this->pdo->where(array('b.id' => $id));
            return $this->pdo->count_all_results($this->table . ' as m');
        }

        if (isset($this->start_date)) {
            $this->pdo->where('m.created_at >=', $this->start_date);
        }

        if (isset($this->end_date)) {
            $this->pdo->where('m.created_at <=', $this->end_date);
        }

        if (isset($id)) {
            $this->pdo->where(array('b.id' => $id));
        } else {
                if(empty($this->branch_id)) {
                    if ($this->session->userdata('center_id')) {
                        $this->pdo->where(array('m.enable' => 1));
                    } else {
                    $this->pdo->where(array('m.branch_id' => $this->session->userdata('branch_id'), 'm.enable' => 1));
                    }
                } else {
                    $this->pdo->where(array('m.enable' => 1));
                    $this->pdo->where_in('m.branch_id',$this->branch_id);
                }
        }

        $this->pdo->group_by('m.branch_id');
        return $this->pdo->count_all_results($this->table . ' as m');
    }

    protected function get_content_data($id)
    {
        $this->pdo->select('m.*,ms.admin_id,a.name as sender,mc.content');
        $this->pdo->join('message_contents as mc', 'mc.id=m.id');
        $this->pdo->join('users AS u', 'mu.user_id=u.id', 'left');
        $this->pdo->join('admins as a', 'ms.admin_id=a.id', 'left');

        $this->pdo->where(array('m.' . $this->p_id => $id));
        $this->pdo->group_by('m.id');
        $query = $this->pdo->get($this->table . ' as m');

        return $query->row_array();
    }
}

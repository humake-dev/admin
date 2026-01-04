<?php

defined('BASEPATH') or exit('No direct script access allowed');
require_once 'SL_SubModel.php';

class UserStop extends SL_SubModel
{
    protected $table = 'user_stops';
    protected $parent_id_name = 'user_id';
    protected $table_content = 'user_stop_contents';
    protected $table_content_required = false;
    protected $table_id_name = 'user_stop_id';
    protected $accepted_attributes = array('user_id', 'order_id', 'stop_start_date', 'stop_end_date', 'stop_day_count', 'request_date', 'enable', 'created_at', 'updated_at');

    protected function get_index_data($per_page = 1000, $page = 0, $order = null, $desc = null, $enable = true)
    {
        $this->pdo->select('us.*,usc.id as content_id,usc.content,u.name,if(uss.id,uss.enable,0) as uss_exist');
        $this->pdo->join('users as u', 'us.user_id = u.id');
        $this->pdo->join('user_stop_schedules as uss', 'uss.user_stop_id = us.id', 'left');
        $this->pdo->join('user_stop_contents as usc', 'usc.user_stop_id = us.id', 'left');

        if (!empty($this->parent_id)) {
            $this->pdo->where(array('us.' . $this->parent_id_name => $this->parent_id));
        }

        if (!empty($this->user_id)) {
            $this->pdo->where(array('us.user_id' => $this->user_id));
        }

        if (!empty($this->order_id)) {
            $this->pdo->where(array('us.order_id' => $this->order_id));
        }

        if (!empty($this->not_id)) {
            $this->pdo->where('us.id !=', $this->not_id);
        }

        if (!empty($this->current_only)) {
            $this->pdo->where('us.stop_start_date<=CURDATE()');
        }

        if (empty($this->ended_only)) {
            $this->pdo->where(array('u.branch_id' => $this->session->userdata('branch_id'), 'u.enable' => 1, 'us.enable' => 1));
        } else {
            $this->pdo->where('us.stop_end_date<=CURDATE()');
            $this->pdo->where(array('u.branch_id' => $this->session->userdata('branch_id'), 'u.enable' => 1, 'us.enable' => 1));
        }

        $this->pdo->order_by($order, $desc);
        $query = $this->pdo->get($this->table . ' as us');

        return $query->result_array();
    }

    public function get_count($id = null)
    {
        $this->pdo->select('count(*) as count');
        $this->pdo->join('users as u', 'us.user_id = u.id');

        if (isset($id)) {
            $this->pdo->where(array('us.id' => $id));

            return $this->pdo->count_all_results($this->table . ' as us');
        }

        if (!empty($this->parent_id)) {
            $this->pdo->where(array('us.' . $this->parent_id_name => $this->parent_id));
        }

        if (!empty($this->user_id)) {
            $this->pdo->where(array('us.user_id' => $this->user_id));
        }

        if (!empty($this->order_id)) {
            $this->pdo->where(array('us.order_id' => $this->order_id));
        }

        if (!empty($this->not_id)) {
            $this->pdo->where('us.id !=', $this->not_id);
        }

        if (!empty($this->current_only)) {
            $this->pdo->where('us.stop_start_date<=CURDATE()');
        }

        if (empty($this->ended_only)) {
            $this->pdo->where(array('u.branch_id' => $this->session->userdata('branch_id'), 'u.enable' => 1, 'us.enable' => 1));
        } else {
            $this->pdo->where('us.stop_end_date<=CURDATE()');
            $this->pdo->where(array('u.branch_id' => $this->session->userdata('branch_id'), 'u.enable' => 1, 'us.enable' => 1));
        }

        return $this->pdo->count_all_results($this->table . ' as us');
    }

    protected function get_content_data($id)
    {
        $this->pdo->select('us.*,usc.id as content_id,usc.content,u.name,uac.card_no,if(uss.id,uss.enable,0) as uss_exist');
        $this->pdo->join('users as u', 'us.user_id = u.id');
        $this->pdo->join('user_stop_schedules as uss', 'uss.user_stop_id = us.id', 'left');
        $this->pdo->join('user_stop_contents as usc', 'usc.user_stop_id = us.id', 'left');
        $this->pdo->join('user_access_cards as uac', 'uac.user_id=u.id', 'left');
        $this->pdo->where(array('us.id' => $id));
        $query = $this->pdo->get($this->table . ' as us');

        return $query->row_array();
    }

    // 정지 기록은 실제로는 지우지 않도록 한다.
    public function delete($id)
    {
        $this->pdo->update($this->table, array('enable' => 0, 'updated_at' => $this->now), array('id' => $id));
        
        $this->pdo->where(array('uss.enable'=>0));
        if ($this->pdo->count_all_results('user_stop_schedules as uss')) {
            $this->pdo->delete('user_stop_schedules', array('user_stop_id' => $id));
        }

        return true;
    }    
}

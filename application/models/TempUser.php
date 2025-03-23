<?php

defined('BASEPATH') or exit('No direct script access allowed');
require_once 'SL_Model.php';

class TempUser extends SL_Model
{
    protected $table = 'temp_users';
    protected $accepted_attributes = array('branch_id', 'counsel_id', 'name', 'phone', 'email', 'gender', 'birthday', 'birthday_type', 'registration_date', 'enable', 'created_at', 'updated_at');

    protected function set_search($select = false)
    {
        if (!empty($this->id)) {
            if (is_array($this->id)) {
                $this->pdo->where_in('ut.id', $this->id);
            } else {
                $this->pdo->where(array('ut.id' => $this->id));
            }
        }

        if (!empty($this->counsel_id)) {
            $this->pdo->where(array('ut.counsel_id' => $this->counsel_id));
        }

        if (isset($this->search_param)) {
            $this->pdo->like('ut.' . $this->search_param['search_field'], $this->search_param['search_word']);
        }
    }

    protected function get_index_data($per_page = 1000, $page = 0, $order = null, $desc = null, $enable = true)
    {
        $this->set_search(true);
        $this->pdo->where(array('ut.branch_id' => $this->session->userdata('branch_id'), 'ut.enable' => 1));
        $this->pdo->order_by($order, $desc);

        $query = $this->pdo->get($this->table . ' as ut', $per_page, $page);

        return $query->result_array();
    }

    public function get_count($id = null)
    {
        if (isset($id)) {
            $this->pdo->where(array('ut.id' => $id));
        }

        $this->set_search();
        $this->pdo->where(array('ut.branch_id' => $this->session->userdata('branch_id'), 'ut.enable' => 1));

        return $this->pdo->count_all_results($this->table . ' as ut');
    }

    protected function get_content_data($id)
    {
        $this->pdo->select('tu.*,tut.trainer_id,trainer.name as trainer_name,tufc.fc_id,fc.name as fc_name,tufc.id as tufc_id,tut.id as tut_id');
        $this->pdo->join('temp_user_fcs AS tufc', 'tufc.temp_user_id=tu.id', 'left');
        $this->pdo->join('admins AS fc', 'tufc.fc_id=fc.id', 'left');
        $this->pdo->join('temp_user_trainers AS tut', 'tut.temp_user_id=tu.id', 'left');
        $this->pdo->join('admins AS trainer', 'tut.trainer_id=trainer.id', 'left');

        $this->pdo->where(array('tu.id' => $id));
        $query = $this->pdo->get($this->table . ' as tu');

        return $query->row_array();
    }

    public function get_content_by_phone($phone)
    {
        $this->pdo->where(array('branch_id' => $this->session->userdata('branch_id'), 'phone' => $phone, 'enable'=>1));
        $count = $this->pdo->count_all_results($this->table);

        if (!$count) {
            return false;
        }

        $this->pdo->where(array('branch_id' => $this->session->userdata('branch_id'), 'phone' => $phone, 'enable'=>1));
        $query = $this->pdo->get($this->table);
        $result = $query->row_array();

        return $result;
    }    

    protected function get_default_data()
    {
        $data = parent::get_default_data();
        $data['registration_date'] = $this->today;

        return $data;
    }
}

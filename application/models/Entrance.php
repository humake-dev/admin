<?php

defined('BASEPATH') or exit('No direct script access allowed');
require_once 'SL_Model.php';

class Entrance extends SL_Model
{
    protected $table = 'entrances';
    protected $accepted_attributes = array('user_id', 'in_time', 'created_at');

    protected function get_index_data($per_page = 1000, $page = 0, $order = null, $desc = null, $enable = true)
    {
        $this->pdo->select('u.*,u.id as user_id,ue.id,ue.in_time,fc.name as fc_name,trainer.name as trainer_name,up.id as up_id,up.picture_url,
        (SELECT count(*) FROM entrances WHERE user_id=u.id) as entrance_total
        ');
        $this->pdo->join('users AS u', 'ue.user_id=u.id');
        $this->pdo->join('entrance_not_users AS enu', 'enu.user_id=u.id', 'left');
        $this->pdo->join('user_pictures AS up', 'up.user_id=u.id', 'left');
        $this->pdo->join('user_fcs AS ufc', 'ufc.user_id=u.id', 'left');
        $this->pdo->join('admins AS fc', 'ufc.fc_id=fc.id', 'left');
        $this->pdo->join('user_trainers AS ut', 'ut.user_id=u.id', 'left');
        $this->pdo->join('admins AS trainer', 'ut.trainer_id=trainer.id', 'left');

        if (isset($this->user_id)) {
            $this->pdo->where(array('ue.user_id' => $this->user_id));
        }

        if (isset($this->date)) {
            $this->pdo->where(array('date(ue.in_time)' => $this->date));
        }

        $this->pdo->where(array('u.branch_id' => $this->session->userdata('branch_id')));
        $this->pdo->where('enu.id IS NULL');
        $this->pdo->order_by('ue.id', $desc);

        $query = $this->pdo->get($this->table . ' as ue', $per_page, $page);

        return $query->result_array();
    }

    public function get_count($id = null)
    {
        $this->pdo->join('users AS u', 'ue.user_id=u.id');
        $this->pdo->join('entrance_not_users AS enu', 'enu.user_id=u.id', 'left');

        if (isset($this->user_id)) {
            $this->pdo->where(array('ue.user_id' => $this->user_id));
        }

        if (isset($this->date)) {
            $this->pdo->where(array('date(ue.in_time)' => $this->date));
        }

        if (isset($this->card_no)) {
            $this->pdo->join('entrance_cards AS uec', 'uec.entrance_id=ue.id');
        }

        $this->pdo->where(array('u.branch_id' => $this->session->userdata('branch_id')));
        $this->pdo->where('enu.id IS NULL');
        if (isset($id)) {
            $this->pdo->where(array('ue.id' => $id));
        }

        return $this->pdo->count_all_results($this->table . ' as ue');
    }

    protected function get_content_data($id)
    {
        if (isset($this->entrance_card_no)) {
            $this->pdo->select('u.*,fcd.card_no as facility_card_no,fcd.no,f.title,ue.in_time,,up.id as up_id,up.picture_url,(SELECT count(*) FROM entrances WHERE user_id=u.id) as entrance_total');
        } else {
            $this->pdo->select('u.*,u.id as user_id,ue.id,ue.in_time,fc.name as fc_name,trainer.name as trainer_name,up.id as up_id,up.picture_url,(SELECT count(*) FROM entrances WHERE user_id=u.id) as entrance_total');
        }

        $this->pdo->join('users AS u', 'ue.user_id=u.id');
        $this->pdo->join('user_pictures AS up', 'up.user_id=u.id', 'left');
        $this->pdo->join('user_fcs AS ufc', 'ufc.user_id=u.id', 'left');
        $this->pdo->join('admins AS fc', 'ufc.fc_id=fc.id', 'left');
        $this->pdo->join('user_trainers AS ut', 'ut.user_id=u.id', 'left');
        $this->pdo->join('admins AS trainer', 'ut.trainer_id=trainer.id', 'left');

        $this->pdo->where(array('ue.id' => $id));
        $query = $this->pdo->get($this->table . ' as ue');

        return $query->row_array();
    }

    public function get_attendance_by_user($user)
    {
        $result = array();
        $this->pdo->select('count(*)');
        $this->pdo->join('users AS m', 'ue.user_id=m.id');
        $this->pdo->where(array('ue.user_id' => $user));
        $this->pdo->group_by('ue.id');

        $result['total'] = $this->pdo->count_all_results($this->table . ' as ue');

        if (!$result['total']) {
            return $result;
        }

        $this->pdo->select('date(ue.in_time) as attandance');
        $this->pdo->join('users AS m', 'ue.user_id=m.id');
        $this->pdo->where(array('ue.user_id' => $user));
        $this->pdo->group_by('ue.id');
        $query = $this->pdo->get($this->table . ' as ue');

        return $query->result_array();
    }

    public function get_attendance_by_user_n_date($user, $date)
    {
        $result = array();
        $this->pdo->select('count(*)');
        $this->pdo->join('users AS m', 'ue.user_id=m.id');
        $this->pdo->where(array('ue.user_id' => $user, 'date(ue.in_time)' => $date));
        $this->pdo->group_by('ue.id');

        $result['total'] = $this->pdo->count_all_results($this->table . ' as ue');

        if (!$result['total']) {
            return $result;
        }

        $this->pdo->select('m.*,m.id as user_id,ue.id,ue.in_time');
        $this->pdo->join('users AS m', 'ue.user_id=m.id');
        $this->pdo->where(array('ue.user_id' => $user, 'date(ue.in_time)' => $date));
        $this->pdo->group_by('ue.id');
        $query = $this->pdo->get($this->table . ' as ue');

        return $query->result_array();
    }

    public function insert(array $data)
    {
        $data = array_merge($this->get_default_data(), $data);
        $filtered_data = array();

        foreach ($data as $key => $value) {
            if (in_array($key, $this->accepted_attributes)) {
                $filtered_data[$key] = $value;
            }
        }

        $query = $this->pdo->query('SELECT count(*) as count FROM entrances WHERE user_id=? AND in_time=?', array($filtered_data['user_id'], $filtered_data['in_time']));

        if ($query->result()[0]->count) {
            return true;
        }

        if ($this->pdo->insert($this->table, $filtered_data)) {
            $id = $this->pdo->insert_id();

            return $id;
        } else {
            return false;
        }
    }
}

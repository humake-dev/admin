<?php

defined('BASEPATH') or exit('No direct script access allowed');
require_once 'SL_Model.php';

class EmployeeAttendance extends SL_Model
{
    protected $table = 'admin_attendances';
    protected $accepted_attributes = array('admin_id', 'in_time', 'created_at');

    protected function get_index_data($per_page = 1000, $page = 0, $order = null, $desc = null, $enable = true)
    {
        $this->pdo->select('a.*,a.id as employee_id,ue.id,ue.in_time,ue.created_at');
        $this->pdo->join('admins AS a', 'ue.admin_id=a.id');

        if (isset($this->employee_id)) {
            $this->pdo->where(array('ue.admin_id' => $this->employee_id));
        }

        if (isset($this->date)) {
            $this->pdo->where(array('date(ue.in_time)' => $this->date));
        }

        $this->pdo->where(array('a.branch_id' => $this->session->userdata('branch_id')));
        $this->pdo->order_by('ue.id', $desc);

        $query = $this->pdo->get($this->table . ' as ue', $per_page, $page);

        return $query->result_array();
    }

    public function get_count($id = null)
    {
        $this->pdo->join('admins AS a', 'ue.admin_id=a.id');

        if (isset($this->employee_id)) {
            $this->pdo->where(array('ue.admin_id' => $this->employee_id));
        }

        if (isset($this->date)) {
            $this->pdo->where(array('date(ue.in_time)' => $this->date));
        }

        $this->pdo->where(array('a.branch_id' => $this->session->userdata('branch_id')));

        if (isset($id)) {
            $this->pdo->where(array('ue.id' => $id));
        }

        return $this->pdo->count_all_results($this->table . ' as ue');
    }

    public function get_attendance_by_user($user)
    {
        $result = array();
        $this->pdo->select('count(*)');
        $this->pdo->join('admins AS m', 'ue.admin_id=m.id');
        $this->pdo->where(array('ue.admin_id' => $user));
        $this->pdo->group_by('ue.id');

        $result['total'] = $this->pdo->count_all_results($this->table . ' as ue');

        if (!$result['total']) {
            return $result;
        }

        $this->pdo->select('date(ue.in_time) as attandance');
        $this->pdo->join('admins AS m', 'ue.admin_id=m.id');
        $this->pdo->where(array('ue.admin_id' => $user));
        $this->pdo->group_by('ue.id');
        $query = $this->pdo->get($this->table . ' as ue');

        return $query->result_array();
    }

    public function get_attendance_by_user_n_date($user, $date)
    {
        $result = array();
        $this->pdo->select('count(*)');
        $this->pdo->join('admins AS m', 'ue.admin_id=m.id');
        $this->pdo->where(array('ue.admin_id' => $user, 'date(ue.in_time)' => $date));
        $this->pdo->group_by('ue.id');

        $result['total'] = $this->pdo->count_all_results($this->table . ' as ue');

        if (!$result['total']) {
            return $result;
        }

        $this->pdo->select('m.*,m.id as admins,ue.id,ue.in_time');
        $this->pdo->join('admins AS m', 'ue.admin_id=m.id');
        $this->pdo->where(array('ue.admin_id' => $user, 'date(ue.in_time)' => $date));
        $this->pdo->group_by('ue.id');
        $query = $this->pdo->get($this->table . ' as ue');

        return $query->result_array();
    }
}

<?php

defined('BASEPATH') or exit('No direct script access allowed');
require_once 'SL_Model.php';

class EnrollUseLog extends SL_Model
{
    protected $table = 'enroll_use_logs';
    protected $accepted_attributes = array('enroll_id', 'account_id', 'reservation_user_id', 'type', 'enable', 'updated_at', 'created_at');

    protected function get_index_data($per_page = 1000, $page = 0, $order = null, $desc = null, $enable = true)
    {
        $this->pdo->select('eul.*,a.cash as commission,p.title as course_name,r.start_time,r.end_time,u.name as user_name,r.start_time,r.end_time,r.progress_time,ru.complete_at,r.manager_id');
        $this->pdo->join('reservation_users as ru', 'eul.reservation_user_id=ru.id');
        $this->pdo->join('reservations as r', 'ru.reservation_id=r.id');
        $this->pdo->join('users as u', 'ru.user_id=u.id');
        $this->pdo->join('enrolls as e', 'ru.enroll_id=e.id', 'left');
        $this->pdo->join('orders as o', 'e.order_id=o.id', 'left');
        $this->pdo->join('order_products as op', 'op.order_id=o.id', 'left');
        $this->pdo->join('products as p', 'op.product_id=p.id', 'left');
        $this->pdo->join('accounts as a', 'eul.account_id=a.id', 'left');

        if (!empty($this->enroll_id)) {
            $this->pdo->where(array('eul.enroll_id' => $this->enroll_id));
        }

        if (!empty($this->user_id)) {
            $this->pdo->where(array('ru.user_id' => $this->user_id));
        }

        if (!empty($this->trainer_id)) {
            $this->pdo->where(array('r.manager_id' => $this->trainer_id));
        }

        if (isset($this->start_date)) {
            $this->pdo->where('DATE(r.start_time) >=', $this->start_date);
        }

        if (isset($this->end_date)) {
            $this->pdo->where('DATE(r.end_time) <=', $this->end_date);
        }

        $this->pdo->where(array('r.branch_id' => $this->session->userdata('branch_id'), 'eul.enable' => 1));

        $this->pdo->order_by($order, $desc);
        $query = $this->pdo->get($this->table . ' as eul', $per_page, $page);

        return $query->result_array();
    }

    public function get_count($id = null)
    {
        $this->pdo->join('reservation_users as ru', 'eul.reservation_user_id=ru.id');
        $this->pdo->join('reservations as r', 'ru.reservation_id=r.id');
        $this->pdo->join('users as u', 'ru.user_id=u.id');

        if (!empty($this->enroll_id)) {
            $this->pdo->where(array('eul.enroll_id' => $this->enroll_id));
        }

        if (!empty($this->user_id)) {
            $this->pdo->where(array('ru.user_id' => $this->user_id));
        }

        if (!empty($this->trainer_id)) {
            $this->pdo->where(array('r.manager_id' => $this->trainer_id));
        }

        if (isset($this->start_date)) {
            $this->pdo->where('DATE(r.start_time) >=', $this->start_date);
        }

        if (isset($this->end_date)) {
            $this->pdo->where('DATE(r.end_time) <=', $this->end_date);
        }

        $this->pdo->where(array('r.branch_id' => $this->session->userdata('branch_id'), 'eul.enable' => 1));

        return $this->pdo->count_all_results($this->table . ' as eul');
    }

    protected function get_content_data($id)
    {
        $this->pdo->select('eul.*,o.user_id,a.cash as commission,p.title as course_name,r.start_time,r.end_time,u.name as user_name,r.start_time,r.end_time,r.progress_time,ru.complete_at,r.manager_id');
        $this->pdo->join('reservation_users as ru', 'eul.reservation_user_id=ru.id');
        $this->pdo->join('reservations as r', 'ru.reservation_id=r.id');
        $this->pdo->join('enrolls as e', 'ru.enroll_id=e.id');
        $this->pdo->join('orders as o', 'e.order_id=o.id');
        $this->pdo->join('order_products as op', 'op.order_id=o.id');
        $this->pdo->join('products as p', 'op.product_id=p.id');
        $this->pdo->join('users as u', 'o.user_id=u.id');
        $this->pdo->join('accounts as a', 'eul.account_id=a.id', 'left');
        $this->pdo->where(array('eul.id' => $id, 'eul.enable' => 1));
        $query = $this->pdo->get($this->table . ' as eul');

        return $query->row_array();
    }

    public function get_content_by_reservation_user_id($reservation_user_id)
    {
        $this->pdo->where(array('reservation_user_id'=>$reservation_user_id));
        $count=$this->pdo->count_all_results($this->table . ' as eul');
        
        if(empty($count)) {
            return false;
        }

        $this->pdo->select('eul.*');
        $this->pdo->where(array('reservation_user_id'=>$reservation_user_id));
        $query = $this->pdo->get($this->table . ' as eul');

        return $query->row_array();
    }

    public function delete_by_reservation_user_id($reservation_user_id)
    {
        return $this->pdo->delete($this->table, array('reservation_user_id' => $reservation_user_id));
    }

    public function delete_by_parent_id($parent_id)
    {
        return $this->pdo->delete($this->table, array('enroll_id' => $parent_id));
    }

    public function delete($id)
    {
        return $this->pdo->delete($this->table, array('id' => $id));
    }
}

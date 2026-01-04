<?php

defined('BASEPATH') or exit('No direct script access allowed');
require_once 'SL_SubModel.php';

class ReservationUser extends SL_SubModel
{
    protected $table = 'reservation_users';
    protected $accepted_attributes = array('reservation_id', 'user_id', 'enroll_id', 'complete', 'compelte_request_at', 'complete_at');
    protected $parent_id_name = 'reservation_id';

    public function get_count($id = null)
    {
        if (empty($this->reservation_info)) {
            $this->pdo->join('users as u', 'ru.user_id=u.id');
            $this->pdo->join('user_devices as ud', 'ud.user_id=ru.user_id', 'left');
        } else {
            $this->pdo->join('reservations as r', 'ru.reservation_id=r.id');
            $this->pdo->join('users as u', 'ru.user_id=u.id');
            $this->pdo->join('enroll_use_logs as eul', 'eul.reservation_user_id=ru.id', 'left');
            $this->pdo->where('IF(eul.id,eul.enable=1,1=1)');
        }

        if (isset($id)) {
            $this->pdo->where(array('ru.id' => $id));

            return $this->pdo->count_all_results($this->table . ' as ru');
        }

        if (!empty($this->parent_id)) {
            $this->pdo->where(array('ru.' . $this->parent_id_name => $this->parent_id));
        }

        if (!empty($this->user_id)) {
            $this->pdo->where(array('ru.user_id' => $this->user_id));
        }

        return $this->pdo->count_all_results($this->table . ' as ru');
    }

    protected function get_index_data($per_page = 1000, $page = 0, $order = null, $desc = null, $enable = true)
    {
        if (empty($this->reservation_info)) {
            $this->pdo->select('ru.*,ud.token,u.name,u.phone,uac.card_no');
            $this->pdo->join('users as u', 'ru.user_id=u.id');
            $this->pdo->join('user_access_cards as uac', 'uac.user_id=u.id', 'left');
            $this->pdo->join('user_devices as ud', 'ud.user_id=ru.user_id', 'left');
        } else {
            $this->pdo->select('ru.*,r.start_time,r.end_time,eul.type as eul_type,r.type,r.progress_time,r.manager_id,manager.name as manager_name,manager.enable as manager_enable,p.title as course_name,a.cash as commission');
            $this->pdo->join('reservations as r', 'ru.reservation_id=r.id');
            $this->pdo->join('users as u', 'ru.user_id=u.id');
            $this->pdo->join('enrolls AS e', 'ru.enroll_id=e.id', 'left');
            $this->pdo->join('courses AS c', 'e.course_id=c.id', 'left');
            $this->pdo->join('products AS p', 'c.product_id=p.id', 'left');
            $this->pdo->join('admins as manager', 'r.manager_id=manager.id', 'left');
            $this->pdo->join('enroll_use_logs as eul', 'eul.reservation_user_id=ru.id', 'left');
            $this->pdo->join('accounts as a', 'eul.account_id=a.id', 'left');
            $this->pdo->where('IF(eul.id,eul.enable=1,1=1)');
        }

        if (!empty($this->parent_id)) {
            $this->pdo->where(array('ru.' . $this->parent_id_name => $this->parent_id));
        }

        if (!empty($this->user_id)) {
            $this->pdo->where(array('ru.user_id' => $this->user_id));
        }

        $this->pdo->order_by('ru.' . $order, $desc);
        $query = $this->pdo->get($this->table . ' as ru', $per_page, $page);

        return $query->result_array();
    }

    public function complete_grant($id, $complete = 3)
    {
        $datetime = date('Y-m-d H:i:s');
        $data = array('complete' => $complete, 'complete_at' => $datetime);

        return $this->pdo->update($this->table, $data, array('id' => $id));
    }

    protected function get_content_data($id)
    {
        $this->pdo->select('ru.*,r.manager_id,DATE(r.start_time) as start_date,r.start_time');
        $this->pdo->join('reservations as r', 'ru.reservation_id=r.id');
        $this->pdo->where(array('ru.id' => $id));
        $query = $this->pdo->get($this->table . ' as ru');

        return $query->row_array();
    }
}

<?php

defined('BASEPATH') or exit('No direct script access allowed');
require_once 'SL_Model.php';

class Reservation extends SL_Model
{
    protected $table = 'reservations';
    protected $table_content = 'reservation_contents';
    protected $table_content_required = false;
    protected $table_id_name = 'reservation_id';
    protected $accepted_attributes = array('branch_id', 'manager_id', 'type', 'start_time', 'end_time', 'progress_time', 'created_at', 'updated_at');
    protected $date;
    protected $e_date;
    protected $type;

    protected function get_index_data($per_page = 1000, $page = 0, $order = null, $desc = null, $enable = true)
    {
        $this->pdo->select('r.*,manager.name as manager_name,GROUP_CONCAT(ru.id,"::",u.name,"::",ru.complete ORDER BY u.name) as members,ru.complete', false);
        $this->pdo->join('reservation_users AS ru', 'ru.reservation_id=r.id');
        $this->pdo->join('users AS u', 'ru.user_id=u.id');
        $this->pdo->join('admins as manager', 'r.manager_id=manager.id', 'left');

        if (isset($this->user_id)) {
            $this->pdo->where(array('ru.user_id' => $this->user_id));
        } else {
            if ($this->type == 'day') {
                $this->pdo->where('r.start_time BETWEEN "' . $this->date . '" AND DATE_ADD("' . $this->date . '", INTERVAL 1 DAY)');
            } else {
                $this->pdo->where('date(r.start_time) BETWEEN "' . $this->date . '" AND "' . $this->e_date . '"');
            }

            if (!empty($this->start_time) and !empty($this->end_time)) {
                $this->pdo->where('((r.start_time BETWEEN "' . $this->start_time . '" AND "' . $this->end_time . '") OR (r.end_time BETWEEN "' . $this->start_time . '" AND "' . $this->end_time . '") OR (r.start_time<"' . $this->start_time . '" AND r.end_time>"' . $this->end_time . '"))');
            }
        }

        if (!empty($this->manager_id)) {
            $this->pdo->where(array('r.manager_id' => $this->manager_id));
        }

        $this->pdo->where(array('r.branch_id' => $this->session->userdata('branch_id'), 'r.enable' => 1));

        $this->pdo->order_by($order, $desc);
        $this->pdo->group_by('r.id');
        $query = $this->pdo->get($this->table . ' as r', $per_page, $page);

        return $query->result_array();
    }

    public function get_count($id = null)
    {
        $this->pdo->select('count(*)');
        $this->pdo->join('reservation_users AS ru', 'ru.reservation_id=r.id');
        $this->pdo->join('users AS u', 'ru.user_id=u.id');

        if (isset($id)) {
            $this->pdo->where(array('r.id' => $id));
        } else {
            if (isset($this->user_id)) {
                $this->pdo->where(array('ru.user_id' => $this->user_id));
            } else {
                if ($this->type == 'day') {
                    $this->pdo->where('r.start_time BETWEEN "' . $this->date . '" AND DATE_ADD("' . $this->date . '", INTERVAL 1 DAY)');
                } else {
                    $this->pdo->where('date(r.start_time) BETWEEN "' . $this->date . '" AND "' . $this->e_date . '"');
                }

                if (!empty($this->start_time) and !empty($this->end_time)) {
                    $this->pdo->where('((r.start_time BETWEEN "' . $this->start_time . '" AND "' . $this->end_time . '") OR (r.end_time BETWEEN "' . $this->start_time . '" AND "' . $this->end_time . '") OR (r.start_time<"' . $this->start_time . '" AND r.end_time>"' . $this->end_time . '"))');
                }
            }

            if (!empty($this->manager_id)) {
                $this->pdo->where(array('r.manager_id' => $this->manager_id));
            }

            $this->pdo->where(array('r.branch_id' => $this->session->userdata('branch_id'), 'r.enable' => 1));
            $this->pdo->group_by('r.id');
        }

        return $this->pdo->count_all_results($this->table . ' as r');
    }

    protected function get_content_data($id)
    {
        $this->pdo->select('r.*,manager.name as manager_name,GROUP_CONCAT(concat(u.id,"::",u.name) ORDER BY ru.id) as members,r_content.content', false);
        $this->pdo->join('reservation_users AS ru', 'ru.reservation_id=r.id');
        $this->pdo->join('users AS u', 'ru.user_id=u.id');
        $this->pdo->join('reservation_contents AS r_content', 'r_content.reservation_id=r.id', 'left');
        $this->pdo->join('admins as manager', 'r.manager_id=manager.id', 'left');
        $this->pdo->where(array('r.id' => $id));
        $query = $this->pdo->get($this->table . ' as r');

        return $query->row_array();
    }

    public function get_aside($per_page, $page)
    {
        $result = array();
        $result['total'] = $this->get_aside_count();

        if (!$result['total']) {
            return $result;
        }

        $this->pdo->select('r.*,manager.name as manager_name,GROUP_CONCAT(m.name) as users,GROUP_CONCAT(rm.complete) as complete');
        $this->pdo->join('reservation_users as rm', 'rm.reservation_id=r.id');
        $this->pdo->join('users as m', 'rm.user_id=m.id');
        $this->pdo->join('admins as manager', 'r.manager_id=manager.id', 'left');

        if (!empty($this->manager_id)) {
            $this->pdo->where(array('r.manager_id' => $this->manager_id));
        }

        if ($this->type == 'day') {
            $this->pdo->where('r.start_time BETWEEN "' . $this->date . '" AND DATE_ADD("' . $this->date . '", INTERVAL 1 DAY)');
        } else {
            $this->pdo->where('date(r.start_time) BETWEEN "' . $this->date . '" AND "' . $this->e_date . '"');
        }

        $this->pdo->where(array('r.branch_id' => $this->session->userdata('branch_id')));
        $this->pdo->order_by('r.start_time', 'asc');
        $this->pdo->group_by('r.id');
        $query = $this->pdo->get($this->table . ' as r', $per_page, $page);
        $result['list'] = $query->result_array();

        return $result;
    }

    public function get_aside_count()
    {
        $this->pdo->select('count(*)');
        $this->pdo->join('reservation_users as rm', 'rm.reservation_id=r.id');
        $this->pdo->join('users as m', 'rm.user_id=m.id');

        if (!empty($this->manager_id)) {
            $this->pdo->where(array('r.manager_id' => $this->manager_id));
        }

        if ($this->type == 'day') {
            $this->pdo->where('r.start_time BETWEEN "' . $this->date . '" AND DATE_ADD("' . $this->date . '", INTERVAL 1 DAY)');
        } else {
            $this->pdo->where('date(r.start_time) BETWEEN "' . $this->date . '" AND "' . $this->e_date . '"');
        }

        $this->pdo->where(array('r.branch_id' => $this->session->userdata('branch_id')));
        $this->pdo->group_by('r.id');

        return $this->pdo->count_all_results($this->table . ' as r');
    }

    public function get_reservation_index()
    {
        $result['total'] = $this->get_reservation_count();

        if (!$result['total']) {
            return $result;
        }

        $this->pdo->select('r.*,manager.name as manager_name,GROUP_CONCAT(m.name) as users', false);
        $this->pdo->join('reservation_users AS rm', 'rm.reservation_id=r.id');
        $this->pdo->join('users AS m', 'rm.user_id=m.id');
        $this->pdo->join('reservation_contents as r_content', 'r_content.reservation_id=r.id', 'left');
        $this->pdo->join('admins as manager', 'r.manager_id=manager.id', 'left');

        if (!empty($this->manager_id)) {
            $this->pdo->where(array('r.manager_id' => $this->manager_id));
        }

        $this->pdo->where(array('r.branch_id' => $this->session->userdata('branch_id')));
        if ($this->type == 'day') {
            $this->pdo->where('r.start_time BETWEEN "' . $this->date . '" AND DATE_ADD("' . $this->date . '", INTERVAL 1 DAY)');
        } else {
            $this->pdo->where('date(r.start_time) BETWEEN "' . $this->date . '" AND "' . $this->e_date . '"');
        }
        $this->pdo->order_by('r.id desc');
        $this->pdo->group_by('r.id,r.start_time');
        $query = $this->pdo->get($this->table . ' as r');
        $result['list'] = $query->result_array();

        return $result;
    }

    public function get_reservation_count($id = null)
    {
        $this->pdo->select('count(*)');
        $this->pdo->join('reservation_users AS rm', 'rm.reservation_id=r.id');
        $this->pdo->join('users AS m', 'rm.user_id=m.id');
        $this->pdo->where(array('r.branch_id' => $this->session->userdata('branch_id')));

        if (!empty($this->manager_id)) {
            $this->pdo->where(array('r.manager_id' => $this->manager_id));
        }

        if ($this->type == 'day') {
            $this->pdo->where('r.start_time BETWEEN "' . $this->date . '" AND DATE_ADD("' . $this->date . '", INTERVAL 1 DAY)');
        } else {
            $this->pdo->where('date(r.start_time) BETWEEN "' . $this->date . '" AND "' . $this->e_date . '"');
        }

        if (isset($id)) {
            $this->pdo->where(array($this->table . '.id' => $id));
        }
        $this->pdo->group_by('r.id,r.start_time');

        return $this->pdo->count_all_results($this->table . ' as r');
    }
}

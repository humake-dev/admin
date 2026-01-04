<?php

defined('BASEPATH') or exit('No direct script access allowed');
require_once 'SL_SubModel.php';

class EnrollPt extends SL_SubModel
{
    protected $table = 'enroll_pts';
    protected $parent_id_name = 'enroll_id';
    protected $parent_unique = true;
    protected $accepted_attributes = array('enroll_id', 'serial');

    protected function get_index_data($per_page = 1000, $page = 0, $order = null, $desc = null, $enable = true)
    {
        $this->pdo->select('ept.*,b.title as branch_title,u.name,e.insert_quantity,e.use_quantity,e.quantity,a.name as manager,o.transaction_date,o.created_at,e.start_date,e.end_date');
        $this->pdo->join('enrolls AS e', 'ept.enroll_id=e.id');
        $this->pdo->join('orders AS o', 'e.order_id=o.id');
        $this->pdo->join('branches AS b', 'o.branch_id=b.id');
        $this->pdo->join('users AS u', 'o.user_id=u.id', 'left');
        $this->pdo->join('enroll_trainers AS et', 'et.enroll_id=e.id', 'left');
        $this->pdo->join('admins AS a', 'et.trainer_id=a.id', 'left');

        if ($this->session->userdata('role_id') > 2) {
            $this->pdo->where(array('o.branch_id' => $this->session->userdata('branch_id')));
        } else {
            if (!empty($this->branch_id)) {
                $this->pdo->where(array('o.branch_id' => $this->branch_id));
            }
        }

        if (!empty($this->search_serial)) {
            $this->pdo->like('ept.serial', $this->search_serial);
        }

        if (!empty($this->start_serial)) {
            $this->pdo->where('ept.serial>=', $this->start_serial);
        }

        if (!empty($this->end_serial)) {
            $this->pdo->where('ept.serial<=', $this->end_serial);
        }

        if (!empty($this->search_period_type)) {
            switch ($this->search_period_type) {
                case 'start_date':
                    $search_period = 'e.start_date';
                    break;
                case 'end_date':
                    $search_period = 'e.end_date';
                    break;
                case 'transaction_date':
                    $search_period = 'o.transaction_date';
                    break;
                case 'create_date':
                    $search_period = 'o.created_at';
                    break;
                default:
            }

            if (!empty($this->start_date)) {
                $this->pdo->where($search_period . '>=', $this->start_date);
            }

            if (!empty($this->end_date)) {
                $this->pdo->where($search_period . '<=', $this->end_date);
            }
        }

        $this->pdo->order_by($order, $desc);
        $query = $this->pdo->get($this->table . ' as ept', $per_page, $page);

        return $query->result_array();
    }

    public function get_count($id = null)
    {
        $this->pdo->join('enrolls AS e', 'ept.enroll_id=e.id');
        $this->pdo->join('orders AS o', 'e.order_id=o.id');
        $this->pdo->join('branches AS b', 'o.branch_id=b.id');

        if ($this->session->userdata('role_id') > 2) {
            $this->pdo->where(array('o.branch_id' => $this->session->userdata('branch_id')));
        } else {
            if (!empty($this->branch_id)) {
                $this->pdo->where(array('o.branch_id' => $this->branch_id));
            }
        }

        if (!empty($this->search_serial)) {
            $this->pdo->like('ept.serial', $this->search_serial);
        }

        if (!empty($this->start_serial)) {
            $this->pdo->where('ept.serial>=', $this->start_serial);
        }

        if (!empty($this->end_serial)) {
            $this->pdo->where('ept.serial<=', $this->end_serial);
        }

        if (!empty($this->search_period_type)) {
            switch ($this->search_period_type) {
                case 'start_date':
                    $search_period = 'e.start_date';
                    break;
                case 'end_date':
                    $search_period = 'e.end_date';
                    break;
                case 'transaction_date':
                    $search_period = 'o.transaction_date';
                    break;
                case 'create_date':
                    $search_period = 'o.created_at';
                    break;
                default:
            }

            if (!empty($this->start_date)) {
                $this->pdo->where($search_period . '>=', $this->start_date);
            }

            if (!empty($this->end_date)) {
                $this->pdo->where($search_period . '<=', $this->end_date);
            }
        }

        if (isset($id)) {
            $this->pdo->where(array('ept.id' => $id));

            return $this->pdo->count_all_results($this->table . ' as ept');
        }

        return $this->pdo->count_all_results($this->table . ' as ept');
    }

    protected function get_content_data($id)
    {
        $this->pdo->select('ept.*,b.title as branch_title,u.name,e.insert_quantity,e.use_quantity,e.quantity,a.name as manager');
        $this->pdo->join('enrolls AS e', 'ept.enroll_id=e.id');
        $this->pdo->join('orders AS o', 'e.order_id=o.id');
        $this->pdo->join('branches AS b', 'o.branch_id=b.id');
        $this->pdo->join('users AS u', 'o.user_id=u.id', 'left');
        $this->pdo->join('enroll_trainers AS et', 'et.enroll_id=e.id', 'left');
        $this->pdo->join('admins AS a', 'et.trainer_id=a.id', 'left');

        $this->pdo->where(array('ept.id' => $id));
        $query = $this->pdo->get($this->table . ' as ept');

        return $query->row_array();
    }

    public function exists_unique_serial($serial, $enroll_id = null)
    {
        $this->pdo->join('enrolls as e', 'ept.enroll_id=e.id');

        if (!empty($enroll_id)) {
            $this->pdo->where(array('e.id !='=> $enroll_id));
        }
        
        $this->pdo->where(array('ept.serial' => $serial));

        return $this->pdo->count_all_results($this->table . ' as ept');
    }
}

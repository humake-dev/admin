<?php

defined('BASEPATH') or exit('No direct script access allowed');
require_once 'SL_Model.php';

class AccessController extends SL_Model
{
    protected $table = 'access_controllers';
    protected $accepted_attributes = array('branch_id', 'connection', 'model', 'enable', 'created_at', 'updated_at');

    public function get_count($id = null)
    {
        if (isset($id)) {
            $this->pdo->where(array('ac.' . $this->p_id => $id));
        } else {
            if (empty($this->all)) {
                if (empty($this->branch_id)) {
                    $this->pdo->where(array('ac.branch_id' => $this->session->userdata('branch_id'), 'ac.enable' => 1));
                } else {
                    $this->pdo->where(array('ac.branch_id' => $this->branch_id, 'ac.enable' => 1));
                }
            }
        }

        return $this->pdo->count_all_results($this->table . ' as ac');
    }

    protected function get_index_data($per_page = 1000, $page = 0, $order = null, $desc = null, $enable = true)
    {
        if (empty($this->all)) {
            $this->pdo->select('ac.*,aci.device_id,INET_NTOA(aci.send_ip) as send_ip,INET_NTOA(dest_ip) as dest_ip');
            $this->pdo->join('access_controller_ist as aci', 'aci.access_controller_id=ac.id', 'left');

            if (empty($this->branch_id)) {
                $this->pdo->where(array('ac.branch_id' => $this->session->userdata('branch_id'), 'ac.enable' => 1));
            } else {
                $this->pdo->where(array('ac.branch_id' => $this->branch_id, 'ac.enable' => 1));
            }
        } else {
            $this->pdo->select('ac.*');
        }

        $this->pdo->order_by($order, $desc);
        $query = $this->pdo->get($this->table . ' as ac', $per_page, $page);

        return $query->result_array();
    }

    protected function get_content_data($id)
    {
        $this->pdo->select('ac.*,aci.device_id,INET_NTOA(aci.send_ip) as send_ip,INET_NTOA(dest_ip) as dest_ip');
        $this->pdo->join('access_controller_ist as aci', 'aci.access_controller_id=ac.id', 'left');
        $this->pdo->where(array('ac.id' => $id));
        $query = $this->pdo->get($this->table . ' as ac');

        return $query->row_array();
    }

    public function get_content_by_branch_id($branch_id)
    {
        $this->branch_id = $branch_id;
        $list = $this->get_index();

        if (empty($list['total'])) {
            return false;
        }

        return $list['list'][0];
    }
}

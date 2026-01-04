<?php

defined('BASEPATH') or exit('No direct script access allowed');
require_once 'SL_SubModel.php';

class OrderTransferSchedule extends SL_SubModel
{
    protected $table = 'order_transfer_schedules';
    protected $parent_id_name = 'order_transfer_id';
    protected $accepted_attributes = array('order_transfer_id', 'schedule_date', 'execute');

    protected function get_index_data($per_page = 1000, $page = 0, $order = null, $desc = null, $enable = true)
    {
        $this->pdo->select('ot.*,recipient.name as recipient_name,ots.order_transfer_id,ots.schedule_date');
        $this->pdo->join('order_transfers as ot', 'ots.order_transfer_id=ot.id');        
        $this->pdo->join('users as recipient','ot.recipient_id=recipient.id');

        if (!empty($this->order_id)) {
            $this->pdo->where(array('ot.order_id' => $this->order_id));
            $this->pdo->where('(ots.execute=0 OR ots.schedule_date>=CURDATE())');
        }
        $query = $this->pdo->get($this->table . ' as ots', $per_page, $page);
        return $query->result_array();
    }

    public function get_count($id = null)
    {
        $this->pdo->join('order_transfers as ot', 'ots.order_transfer_id=ot.id');        
        $this->pdo->join('users as recipient','ot.recipient_id=recipient.id');

        if (!empty($this->order_id)) {
            $this->pdo->where(array('ot.order_id' => $this->order_id));
            $this->pdo->where('(ots.execute=0 OR ots.schedule_date>=CURDATE())');
        }

        if (isset($id)) {
            $this->pdo->where(array('ots.id' => $id));
        }

        return $this->pdo->count_all_results($this->table . ' as ots');
    }
}

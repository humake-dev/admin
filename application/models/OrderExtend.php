<?php

defined('BASEPATH') or exit('No direct script access allowed');
require_once 'SL_Model.php';

class OrderExtend extends SL_Model
{
    public function get_content_by_order_id($order_id)
    {
        $id = $this->get_id_by_order_id($order_id);

        if (empty($id)) {
            return false;
        }

        return $this->get_content($id);
    }

    public function get_id_by_order_id($order_id)
    {
        if (empty($this->get_id_by_order_id_count($order_id))) {
            return false;
        }

        $result = $this->get_id_by_order_id_data($order_id);

        if (!is_array($result)) {
            return false;
        }

        if (!count($result)) {
            return false;
        }

        return $result['id'];
    }

    protected function get_id_by_order_id_count($order_id)
    {
        $this->pdo->where(array($this->table . '.order_id' => $order_id));

        return $this->pdo->count_all_results($this->table);
    }

    protected function get_id_by_order_id_data($order_id)
    {
        $this->pdo->select($this->table . '.id');
        $this->pdo->where(array($this->table . '.order_id' => $order_id));
        $query = $this->pdo->get($this->table);

        return $query->row_array();
    }

    public function extend(array $data)
    {
        return $this->pdo->update('orders', array('original_price' => $data['original_price'], 'price' => $data['price'], 'payment' => $data['payment'], 'updated_at' => $this->now), array('id' => $data['order_id']));
    }

    public function end($id, $end_date = null, $start_date_too =false)
    {
        if (empty($end_date)) {
            $end_date = $this->today;
        }

        if(empty($start_date_too)) {
            $result=$this->pdo->update($this->table, array('end_date' => $end_date), array('id' => $id));
        } else {
            $result=$this->pdo->update($this->table, array('start_date'=>$end_date,'end_date' => $end_date), array('id' => $id));
        }

        return $result;
    }

    // 주문 기록은 실제로는 지우지 않도록 한다.
    public function delete($id)
    {
        $content = $this->get_content_data($id);

        return $this->pdo->update('orders', array('enable' => 0, 'updated_at' => $this->now), array('id' => $content['order_id']));
    }
}

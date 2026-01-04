<?php

defined('BASEPATH') or exit('No direct script access allowed');
require_once 'SL_Model.php';

class Course extends SL_Model
{
    protected $table = 'courses';
    protected $accepted_attributes = array('product_id', 'status', 'trainer_id', 'quota', 'progress_time', 'user_reservation', 'lesson_type', 'lesson_quantity', 'lesson_period', 'lesson_period_unit', 'min_time', 'lesson_dayofweek', 'order_no');

    protected function get_index_data($per_page = 1000, $page = 0, $order = null, $desc = null, $enable = true)
    {
        $this->pdo->select('c.*,p.price,p.title,p.product_category_id');
        $this->pdo->join('products as p', 'c.product_id=p.id');

        if (!empty($this->category_id)) {
            $this->pdo->where(array('p.product_category_id' => $this->category_id));
        }

        if (!empty($this->lesson_type)) {
            if (is_array($this->lesson_type)) {
                $this->pdo->where_in('c.lesson_type', $this->lesson_type);
            } else {
                $this->pdo->where(array('c.lesson_type' => $this->lesson_type));
            }
        }

        if (!empty($this->status)) {
            $this->pdo->where(array('c.status' => 1));
        }

        if (empty($this->branch_id)) {
            $this->pdo->where(array('p.branch_id' => $this->session->userdata('branch_id'), 'p.enable' => 1));
        } else {
            $this->pdo->where(array('p.branch_id' => $this->branch_id, 'p.enable' => 1));
        }

        if(empty($order)) {
            $this->pdo->order_by('c.id', 'asc');
        } else {
            $this->pdo->order_by($order, $desc);
        }

        $query = $this->pdo->get($this->table . ' as c', $per_page, $page);

        return $query->result_array();
    }

    public function get_count($id = null)
    {
        $this->pdo->join('products as p', 'c.product_id=p.id');

        if (isset($id)) {
            $this->pdo->where(array('c.id' => $id, 'p.enable' => 1));

            return $this->pdo->count_all_results($this->table . ' as c');
        }

        if (isset($this->category_id)) {
            $this->pdo->where(array('p.product_category_id' => $this->category_id));
        }

        if (!empty($this->lesson_type)) {
            if (is_array($this->lesson_type)) {
                $this->pdo->where_in('c.lesson_type', $this->lesson_type);
            } else {
                $this->pdo->where(array('c.lesson_type' => $this->lesson_type));
            }
        }

        if (empty($this->branch_id)) {
            $this->pdo->where(array('p.branch_id' => $this->session->userdata('branch_id'), 'p.enable' => 1));
        } else {
            $this->pdo->where(array('p.branch_id' => $this->branch_id, 'p.enable' => 1));
        }

        return $this->pdo->count_all_results($this->table . ' as c');
    }

    protected function get_content_data($id)
    {
        $this->pdo->select('c.*,p.title,p.price,p.product_category_id,p.product_category_id as category_id,cc.title as category_title,content.id as content_id,content.content');
        $this->pdo->join('products as p', 'c.product_id=p.id');
        $this->pdo->join('product_contents as content', 'p.id=content.product_id', 'left');
        $this->pdo->join('product_categories as cc', 'p.product_category_id=cc.id');
        $this->pdo->where(array('c.id' => $id));
        $query = $this->pdo->get($this->table . ' as c');

        return $query->row_array();
    }

    public function update(array $data)
    {
        $data['updated_at'] = $this->now;

        foreach ($data as $key => $value) {
            if (in_array($key, $this->accepted_attributes)) {
                $d_value = trim($value);

                if ($d_value == '') {
                    $filtered_data[$key] = null;
                }

                if ($value == '0000-00-00') {
                    $filtered_data[$key] = null;
                }

                $filtered_data[$key] = $value;
            }
        }

        if ($this->pdo->update($this->table, $filtered_data, array('id' => $data['id']))) {
            if (!empty($this->table_content)) {
                $data['content'] = $this->input->post('content');

                if ($this->table_content_required) {
                    $this->pdo->update($this->table_content, array('content' => $data['content']), array('id' => $data['id']));
                } else {
                    if (empty(trim($data['content']))) {
                        if ($this->get_count_no_required_content($data['product_id'])) {
                            $this->pdo->delete($this->table_content, array($this->table_id_name => $data['product_id']));
                        }
                    } else {
                        if ($this->get_count_no_required_content($data['product_id'])) {
                            $this->pdo->update($this->table_content, array('content' => $data['content'], 'updated_at' => $data['updated_at']), array($this->table_id_name => $data['product_id']));
                        } else {
                            $this->pdo->insert($this->table_content, array($this->table_id_name => $data['product_id'], 'content' => $data['content'], 'created_at' => $data['updated_at'], 'updated_at' => $data['updated_at']));
                        }
                    }
                }
            }

            return true;
        } else {
            return false;
        }
    }

    final public function get_content_by_product_id($id)
    {
        $result = $this->get_content_by_product_id_data($id);

        if (!is_array($result)) {
            return false;
        }

        if (!count($result)) {
            return false;
        }

        return $result;
    }

    protected function get_content_by_product_id_data($product_id)
    {
        $this->pdo->select('c.*,p.price,p.title,p.branch_id,p.gender,pc.id as content_id,pc.content,p.enable,p.created_at,p.updated_at');
        $this->pdo->join('products as p', 'c.product_id=p.id');
        $this->pdo->join('product_contents as pc', 'pc.product_id=p.id', 'left');
        $this->pdo->where(array('c.product_id' => $product_id));

        $query = $this->pdo->get($this->table . ' as c');

        return $query->row_array();
    }

    // 상품은 실제로는 지우지 않도록 한다.
    public function delete($id)
    {
        $content = $this->get_content_data($id);

        return $this->pdo->update('products', array('enable' => 0, 'updated_at' => $this->now), array('id' => $content['product_id']));
    }
}

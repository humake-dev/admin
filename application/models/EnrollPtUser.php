<?php

defined('BASEPATH') or exit('No direct script access allowed');
require_once 'SL_Model.php';

class EnrollPtUser extends SL_Model
{
    protected $table = 'enrolls';
    protected $accepted_attributes = array('order_id', 'course_id');
    protected $search_param;

    protected function set_search($select = false)
    {
        $this->pdo->join('orders as o', 'e.order_id = o.id');
        $this->pdo->join('users as u', 'o.user_id = u.id');
        $this->pdo->join('enroll_trainers as et', 'et.enroll_id = e.id');
        $this->pdo->join('courses as c', 'e.course_id=c.id');
        $this->pdo->join('admins as a', 'et.trainer_id = a.id');
        $this->pdo->join('order_ends as oe', 'oe.order_id=o.id', 'left');
        $this->pdo->join('user_access_cards as uac', 'uac.user_id=u.id', 'left');

        $this->pdo->where(array('c.lesson_type' => 4));

        if (!empty($this->trainer_id)) {            
            $this->pdo->where(array('et.trainer_id' => $this->trainer_id));
        }

        if (!empty($this->course_id)) {
            if (is_array($this->course_id)) {
                $this->pdo->where_in('c.id', $this->course_id);
            } else {
                $this->pdo->where(array('c.id' => $this->course_id));
            }
        }

        if (isset($this->search_param['search_type'])) {
            if($this->search_param['search_type']=='field') {
              if (isset($this->search_param)) {
                  if (!empty($this->search_param['search_field']) and !empty($this->search_param['search_word'])) {

                          if ($this->search_param['search_field'] == 'card_no') {
                              $this->pdo->like('uac.'.$this->search_param['search_field'], $this->search_param['search_word']);
                          } else {
                              $this->pdo->like('u.'.$this->search_param['search_field'], $this->search_param['search_word']);
                          }
                  }
              }
            }
        }
        
        if (!empty($this->has_current_primary_only)) {
            $this->pdo->where('EXISTS(SELECT users.id FROM users INNER JOIN orders ON orders.user_id=users.id INNER JOIN enrolls ON enrolls.order_id=orders.id INNER JOIN order_products ON order_products.order_id=orders.id INNER JOIN product_relations ON order_products.product_id = product_relations.product_id LEFT JOIN order_ends ON order_ends.order_id=orders.id WHERE enrolls.start_date<=CURDATE() AND enrolls.end_date>=CURDATE() AND order_ends.id is NULL AND orders.enable=1 AND product_relations.product_relation_type_id=' . PRIMARY_COURSE_ID . ' AND users.id=u.id)', null, false);
        }

        $this->pdo->where('(e.quantity>e.use_quantity+(select count(*) FROM reservation_users WHERE enroll_id=e.id and complete in (0,1))) AND oe.id is NULL');
        $this->pdo->where(array('o.branch_id' => $this->session->userdata('branch_id'), 'o.enable' => 1));
    }

    protected function get_index_data($per_page = 1000, $page = 0, $order = null, $desc = null, $enable = true)
    {
        $this->pdo->select('u.*,uac.card_no,et.trainer_id');
        $this->set_search();        
        $this->pdo->order_by($order, $desc);
        $this->pdo->group_by('u.id');
        $query = $this->pdo->get($this->table . ' as e', $per_page, $page);

        return $query->result_array();
    }

    public function get_count($id = null)
    {
        if (isset($id)) {
            $this->pdo->where(array('e.id' => $id));

            return $this->pdo->count_all_results($this->table . ' as e');
        }

        $this->pdo->select('count(distinct u.id)');
        $this->set_search();
        $this->pdo->group_by('u.id');

        return $this->pdo->count_all_results($this->table . ' as e');
    }
}

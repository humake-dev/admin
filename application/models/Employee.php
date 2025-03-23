<?php

defined('BASEPATH') or exit('No direct script access allowed');
require_once 'SL_Model.php';

class Employee extends SL_Model
{
    protected $table = 'admins';
    protected $table_content = 'admin_contents';
    protected $table_content_required = false;
    protected $table_id_name = 'admin_id';
    protected $status = array('H');
    protected $grade;
    protected $accepted_attributes = array('branch_id', 'role_id', 'uid', 'encrypted_password', 'name', 'gender', 'status', 'birthday', 'hiring_date', 'phone', 'email', 'commission_rate', 'picture_url', 'is_trainer', 'is_fc', 'created_at', 'updated_at');

    protected function get_index_data($per_page = 1000, $page = 0, $order = null, $desc = null, $enable = true)
    {
        $this->pdo->select('a.*,aac.card_no,r.title as role_name,ep.id as ep_id,ep.picture_url');
        $this->pdo->join('roles as r', 'a.role_id=r.id');
        $this->pdo->join('admin_pictures as ep', 'ep.admin_id=a.id', 'left');
        $this->pdo->join('admin_access_cards as aac', 'aac.admin_id=a.id', 'left');
        
        if (empty($this->branch_id)) {
            if(empty($this->session->userdata('branch_id'))) {
                if ($this->session->userdata('center_id')) {
                    $this->pdo->select('a.*');
                    $this->pdo->join('branches as b', 'b.id=a.branch_id');
                    $this->pdo->where(array('b.center_id' => $this->session->userdata('center_id'), 'a.enable' => 1));
                }
            } else {
                $this->pdo->where(array('a.branch_id' => $this->session->userdata('branch_id'), 'a.enable' => 1));
            }
        } else {
            $this->pdo->where(array('a.branch_id' => $this->branch_id, 'a.enable' => 1));
        }

        $this->pdo->where_in('a.status', $this->status);
        if (isset($this->role_ids)) {
            $this->pdo->where_in('a.role_id', $this->role_ids);
        }

        if (!empty($this->is_trainer)) {
            $this->pdo->where(array('is_trainer' => 1));
        }

        if (!empty($this->is_fc)) {
            $this->pdo->where(array('is_fc' => 1));
        }

        if (isset($this->search_word)) {
            $this->pdo->like('a.name', $this->search_word);
        }

        $this->pdo->order_by('a.'.$order, $desc);
        $query = $this->pdo->get($this->table . ' as a', $per_page, $page);

        return $query->result_array();
    }

    public function get_count($id = null)
    {
        if (isset($id)) {
            $this->pdo->where(array('a.id' => $id));

            return $this->pdo->count_all_results($this->table . ' as a');
        }

        if (empty($this->branch_id)) {
            if(empty($this->session->userdata('branch_id'))) {
                if ($this->session->userdata('center_id')) {
                    $this->pdo->select('a.*');
                    $this->pdo->join('branches as b', 'b.id=a.branch_id');
                    $this->pdo->where(array('b.center_id' => $this->session->userdata('center_id'), 'a.enable' => 1));
                }
            } else {
                $this->pdo->where(array('a.branch_id' => $this->session->userdata('branch_id'), 'a.enable' => 1));
            }
        } else {
            $this->pdo->where(array('a.branch_id' => $this->branch_id, 'a.enable' => 1));
        }

        $this->pdo->where_in('a.status', $this->status);

        if (!empty($this->is_trainer)) {
            $this->pdo->where(array('is_trainer' => 1));
        }

        if (!empty($this->is_fc)) {
            $this->pdo->where(array('is_fc' => 1));
        }

        if (!empty($this->search_word)) {
            $this->pdo->like('a.name', $this->search_word);
        }

        if (isset($this->role_ids)) {
            $this->pdo->where_in('a.role_id', $this->role_ids);
        }

        return $this->pdo->count_all_results($this->table . ' as a');
    }

    protected function get_content_data($id)
    {
        $this->pdo->select('e.*,aac.card_no,r.title as role_name,ep.id as ep_id,ep.picture_url');
        $this->pdo->join('roles as r', 'e.role_id=r.id');
        $this->pdo->join('admin_pictures as ep', 'ep.admin_id=e.id', 'left');
        $this->pdo->join('admin_access_cards as aac', 'aac.admin_id=e.id', 'left');
        $this->pdo->where(array('e.id' => $id));
        $query = $this->pdo->get($this->table . ' as e');

        return $query->row_array();
    }

    public function get_complete_reservation($id)
    {
        $result = array();
        $this->pdo->join('reservations as r', 'ru.reservation_id=r.id');
        $this->pdo->join('enrolls as e', 'e.user_id=ru.user_id and rc.course_id=e.course_id');
        $this->pdo->join('courses as c', 'e.course_id=c.id');
        $this->pdo->where(array('ru.complete' => 1, 'r.manager_id' => $id));
        $result['total'] = $this->pdo->count_all_results('reservation_users as ru');

        if (!$result['total']) {
            return $result;
        }

        $this->pdo->select('c.title as course_name,u.name as user_name,e.fee,date(r.start_time) as execute_date');
        $this->pdo->join('reservations as r', 'ru.reservation_id=r.id');
        $this->pdo->join('enrolls as e', 'e.user_id=ru.user_id and rc.course_id=e.course_id');
        $this->pdo->join('courses as c', 'e.course_id=c.id');
        $this->pdo->join('users as u', 'e.user_id=u.id');
        $this->pdo->where(array('ru.complete' => 1, 'r.manager_id' => $id));
        $query = $this->pdo->get('reservation_users as ru');
        $result['list'] = $query->result_array();

        return $result;
    }

    public function check_unique_uid($uid, $id = null)
    {
        if (empty($uid)) {
            if ($this->get_content_by_uid($uid)) {
                return false;
            } else {
                return true;
            }
        } else {
            $this->pdo->where(array('id !=' => $id));
            $this->pdo->where(array('branch_id' => $this->session->userdata('branch_id'), 'uid' => $uid));
            $count = $this->pdo->count_all_results($this->table);

            if ($count) {
                return false;
            } else {
                return true;
            }
        }
    }

    public function check_unique_email($email, $id = null)
    {
        if (empty($email)) {
            if ($this->get_content_by_email($email)) {
                return false;
            } else {
                return true;
            }
        } else {
            $this->pdo->where(array('id !=' => $id));
            $this->pdo->where(array('email' => $email));
            $count = $this->pdo->count_all_results($this->table);

            if ($count) {
                return false;
            } else {
                return true;
            }
        }
    }

    public function get_content_by_uid($uid)
    {
        $this->pdo->where(array('uid' => $uid));
        $count = $this->pdo->count_all_results($this->table);

        if (!$count) {
            return false;
        }

        $this->pdo->where(array('uid' => $uid));
        $query = $this->pdo->get($this->table);
        return $query->row_array();
    }

    public function get_content_by_email($email)
    {
        $this->pdo->where(array('email' => $email));
        $count = $this->pdo->count_all_results($this->table);

        if (!$count) {
            return false;
        }

        $this->pdo->where(array('email' => $email));
        $query = $this->pdo->get($this->table);
        return $query->row_array();
    }

    public function exists_unique_card_no($card_no, $id = null)
    {
        $this->pdo->join('admin_access_cards as aac', 'aac.admin_id=a.id');

        if (!empty($id)) {
            $this->pdo->where(array('a.id !=' => $id));
        }
        $this->pdo->where(array('a.branch_id' => $this->session->userdata('branch_id'), 'aac.card_no' => $card_no));

        return $this->pdo->count_all_results($this->table . ' as a');
    }

    protected function get_delete_uid($id)
    {
        $content = $this->get_content($id);
        $new_uid = 'deleted_' . $content['uid'];

        if ($this->check_unique_uid($new_uid)) {
            return $new_uid;
        } else {
            $i = 1;
            while (true) {
                $new_uid = 'deleted' . $i . '_' . $content['uid'];
                if ($this->check_unique_uid($new_uid)) {
                    return $new_uid;
                }
                ++$i;
            }
        }
    }

    protected function get_delete_email($id)
    {
        $content = $this->get_content($id);

        if (empty($content['email'])) {
            return null;
        }

        $new_email = 'deleted_' . $content['email'];

        if ($this->check_unique_email($new_email)) {
            return $new_email;
        } else {
            $i = 1;
            while (true) {
                $new_email = 'deleted' . $i . '_' . $content['email'];
                if ($this->check_unique_email($new_email)) {
                    return $new_email;
                }
                ++$i;
            }
        }
    }

    // 직원은 실제로는 지우지 않도록 한다.
    public function delete($id)
    {
        $new_uid = $this->get_delete_uid($id);
        $new_email = $this->get_delete_email($id);

        return $this->pdo->update($this->table, array('uid' => $new_uid, 'email' => $new_email, 'enable' => 0, 'updated_at' => $this->now), array('id' => $id));
    }
}

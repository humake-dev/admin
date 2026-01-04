<?php

defined('BASEPATH') or exit('No direct script access allowed');
require_once 'SL_Model.php';

class Branch extends SL_Model
{
    protected $table = 'branches';
    protected $order = 'id';
    protected $desc = false;
    protected $accepted_attributes = array('center_id', 'title', 'description', 'phone', 'use_ac_controller', 'use_access_card', 'use_admin_ac', 'app_title_color', 'app_notice_color', 'enable', 'created_at', 'updated_at');

    protected function get_index_data($per_page = 1000, $page = 0, $order = null, $desc = null, $enable = true)
    {
        $this->pdo->select('c.title as center_name,b.*');
        $this->pdo->join('centers as c', 'b.center_id=c.id');

        if (!empty($this->not_branch_id)) {
            if(is_array($this->not_branch_id)) {
                $this->pdo->where_not_in('b.id',$this->not_branch_id);
            } else {
                $this->pdo->where('b.id !=', $this->not_branch_id);
            }
        }

        if (isset($this->id)) {
            if(!empty($this->id)) {
            if(is_array($this->id)) {
                $this->pdo->where_in('b.id',$this->id);
                $this->pdo->where(array('b.enable' => true));
            } else {
                $this->pdo->where(array('b.id' => $this->id));
                $this->pdo->where(array('b.enable' => true));
            }
        }
        } else {
 
        if ($this->session->userdata('role_id') == 1) {
            if (isset($this->enable)) {
                if (empty($this->enable)) {
                    $this->pdo->where(array('b.enable' => false));
                } else {
                    $this->pdo->where(array('b.enable' => true));
                }
            }
        } else {
            if (isset($this->center_id)) {
                $this->pdo->where(array('b.center_id' => $this->center_id, 'b.enable' => true));
            } else {
                $this->pdo->where(array('b.id' => $this->session->userdata('branch_id'), 'b.enable' => true));
            }
        }
        }

        $this->pdo->order_by('b.id', 'desc');
        $query = $this->pdo->get($this->table . ' as b', $per_page, $page);

        return $query->result_array();
    }

    public function get_count($id = null)
    {
        if (!empty($this->not_branch_id)) {
            if(is_array($this->not_branch_id)) {
                $this->pdo->where_not_in('b.id',$this->not_branch_id);
                $this->pdo->where(array('b.enable' => true));
            } else {
                $this->pdo->where('b.id !=', $this->not_branch_id);
                $this->pdo->where(array('b.enable' => true));
            }
        }

        if(isset($id))  {
            $this->id=$id;
        }

        if (isset($this->id)) {
            if(!empty($this->id)) {
            if(is_array($this->id)) {
                $this->pdo->where_in('b.id',$this->id);
                return $this->pdo->count_all_results($this->table . ' as b');
            } else {
                $this->pdo->where(array('b.id' => $this->id));
                return $this->pdo->count_all_results($this->table . ' as b');
            }
        }
        }

        if ($this->session->userdata('role_id') == 1) {
            if (isset($this->enable)) {
                if (empty($this->enable)) {
                    $this->pdo->where(array('b.enable' => false));
                } else {
                    $this->pdo->where(array('b.enable' => true));
                }
            }
        } else {
            if (isset($this->center_id)) {
                $this->pdo->where(array('b.center_id' => $this->center_id, 'b.enable' => true));
            } else {
                $this->pdo->where(array('b.id' => $this->session->userdata('branch_id'), 'b.enable' => true));
            }
        }

        return $this->pdo->count_all_results($this->table . ' as b');
    }

    public function charge_sms_available_point($id, $point)
    {
        $this->pdo->set('sms_available_point', 'sms_available_point+' . $point, false);
        $this->pdo->where('id', $id);

        return $this->pdo->update($this->table);
    }

    public function use_sms($id, $quantity)
    {
        $this->pdo->set('sms_available_quantity', 'sms_available_quantity-' . $quantity, false);
        $this->pdo->where('id', $id);

        return $this->pdo->update($this->table);
    }

    protected function get_content_data($id)
    {
        $this->pdo->select('b.*,c.title as center_name,GROUP_CONCAT(CONCAT(bp.id,"::",bp.picture_url) ORDER BY bp.id DESC) as picture_url');
        $this->pdo->join('branch_pictures as bp', 'bp.branch_id=b.id', 'left');
        $this->pdo->join('centers as c', 'b.center_id=c.id');
        $this->pdo->where(array('b.id' => $id));
        $query = $this->pdo->get($this->table . ' as b');

        return $query->row_array();
    }

    public function update_point($send_result, $branch_id = null)
    {
        if ($send_result->result_code != 1) {
            return true;
        }

        if (empty($branch_id)) {
            $branch_id = $this->session->userdata('branch_id');
        }

        if ($send_result->msg_type == 'SMS') {
            $fee = SMS_FEE['sms'];
        } elseif ($send_result->msg_type == 'LMS') {
            $fee = SMS_FEE['lms'];
        } else {
            $fee = SMS_FEE['mms'];
        }

        $quantity = $send_result->success_cnt * $fee;

        $this->pdo->set('sms_available_point', 'sms_available_point-' . $quantity, false);
        $this->pdo->where('id', $branch_id);

        return $this->pdo->update($this->table);
    }

    public function enable($id)
    {
        return $this->pdo->update($this->table, array('enable' => 1), array('id' => $id));
    }

    // 지점은 중지후에 삭제가능
    public function delete($id)
    {
        $content = $this->get_content_data($id);

        if ($content['enable']) {
            return $this->pdo->update($this->table, array('enable' => 0), array('id' => $id));
        } else {
            return parent::delete($id);
        }
    }
}

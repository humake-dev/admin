<?php

defined('BASEPATH') or exit('No direct script access allowed');
require_once 'SL_SubModel.php';

class MessageSmsResult extends SL_SubModel
{
    protected $table = 'message_sms_results';
    protected $parent_id_name = 'message_id';
    protected $accepted_attributes = array('message_id', 'result_code', 'message', 'msg_id', 'success_cnt', 'error_cnt', 'msg_type');

    public function get_content_by_parent_id_data($parent_id)
    {
        $this->pdo->select('msr.message_id,msr.result_code,msr.message,sum(msr.success_cnt) as success_cnt,sum(msr.error_cnt) as error_cnt,group_concat(distinct msg_type) as msg_type');
        $this->pdo->join('messages as m', 'msr.message_id=m.id');

        $this->pdo->where(array('msr.' . $this->parent_id_name => $parent_id));
        $query = $this->pdo->get($this->table . ' as msr');

        return $query->row_array();
    }
}

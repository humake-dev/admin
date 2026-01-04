<?php

defined('BASEPATH') or exit('No direct script access allowed');
require_once 'SL_SubModel.php';

class CounselManager extends SL_SubModel
{
    protected $table = 'counsel_managers';
    protected $parent_id_name = 'counsel_id';
    protected $accepted_attributes = array('counsel_id', 'admin_id');

    public function change_fc($after_fc_id, $before_fc_id, $counsel_id = null)
    {
        if (empty($after_fc_id)) {
            return $this->pdo->delete($this->table, ['fc_id' => $before_fc_id, 'counsel_id' => $counsel_id]);
        }

        if (empty($counsel_id)) {
            return $this->pdo->update($this->table, ['admin_id' => $after_fc_id], ['admin_id' => $before_fc_id]);
        } else {
            return $this->pdo->update($this->table, ['admin_id' => $after_fc_id], ['admin_id' => $before_fc_id, 'counsel_id' => $counsel_id]);
        }
    }
}

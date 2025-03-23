<?php

defined('BASEPATH') or exit('No direct script access allowed');
require_once 'SL_SubModel.php';

class UserFc extends SL_SubModel
{
    protected $table = 'user_fcs';
    protected $parent_unique = true;
    protected $parent_id_name = 'user_id';
    protected $accepted_attributes = ['user_id', 'fc_id', 'created_at', 'updated_at'];

    public function change_fc($after_fc_id, $before_fc_id, $user_id = null)
    {
        if (empty($after_fc_id)) {
            return $this->pdo->delete($this->table, ['fc_id' => $before_fc_id, 'user_id' => $user_id]);
        }

        if (empty($user_id)) {
            return $this->pdo->update($this->table, ['fc_id' => $after_fc_id, 'updated_at' => $this->now], ['fc_id' => $before_fc_id]);
        } else {
            return $this->pdo->update($this->table, ['fc_id' => $after_fc_id, 'updated_at' => $this->now], ['fc_id' => $before_fc_id, 'user_id' => $user_id]);
        }
    }
}

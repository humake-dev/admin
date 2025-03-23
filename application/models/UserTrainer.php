<?php

defined('BASEPATH') or exit('No direct script access allowed');
require_once 'SL_SubModel.php';

class UserTrainer extends SL_SubModel
{
    protected $table = 'user_trainers';
    protected $parent_unique = true;
    protected $parent_id_name = 'user_id';
    protected $accepted_attributes = ['user_id', 'trainer_id', 'created_at', 'updated_at'];

    public function change_trainer($after_trainer_id, $before_trainer_id, $user_id = null)
    {
        if (empty($after_trainer_id)) {
            return $this->pdo->delete($this->table, ['trainer_id' => $before_trainer_id, 'user_id' => $user_id]);
        }

        if (empty($user_id)) {
            return $this->pdo->update($this->table, ['trainer_id' => $after_trainer_id, 'updated_at' => $this->now], ['trainer_id' => $before_trainer_id]);
        } else {
            return $this->pdo->update($this->table, ['trainer_id' => $after_trainer_id, 'updated_at' => $this->now], ['trainer_id' => $before_trainer_id, 'user_id' => $user_id]);
        }
    }
}

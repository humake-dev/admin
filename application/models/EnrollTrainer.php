<?php

defined('BASEPATH') or exit('No direct script access allowed');
require_once 'SL_SubModel.php';

class EnrollTrainer extends SL_SubModel
{
    protected $table = 'enroll_trainers';
    protected $parent_id_name = 'enroll_id';
    protected $accepted_attributes = ['enroll_id', 'trainer_id'];

    public function change_trainer($after_trainer_id, $before_trainer_id, $enroll_id = null)
    {
        if (empty($after_trainer_id)) {
            return $this->pdo->delete($this->table, ['trainer_id' => $before_trainer_id, 'user_id' => $user_id]);
        }

        if (empty($enroll_id)) {
            return $this->pdo->update($this->table, ['trainer_id' => $after_trainer_id], ['trainer_id' => $before_trainer_id]);
        } else {
            return $this->pdo->update($this->table, ['trainer_id' => $after_trainer_id], ['trainer_id' => $before_trainer_id, 'enroll_id' => $enroll_id]);
        }
    }
}

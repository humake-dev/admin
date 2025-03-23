<?php

defined('BASEPATH') or exit('No direct script access allowed');
require_once 'SL_SubModel.php';

class UserAdditional extends SL_SubModel
{
    protected $table = 'user_additionals';
    protected $parent_id_name = 'user_id';
    protected $parent_unique = true;
    protected $accepted_attributes = array('user_id', 'visit_route', 'job_id', 'company', 'enable', 'updated_at', 'created_at');

    public function get_content_by_parent_id_data($parent_id)
    {
        $this->pdo->select('ua.*,j.title as job');
        $this->pdo->join('jobs as j', 'ua.job_id=j.id', 'left');
        $this->pdo->where(array('ua.' . $this->parent_id_name => $parent_id));
        $query = $this->pdo->get($this->table . ' as ua');

        return $query->row_array();
    }
}

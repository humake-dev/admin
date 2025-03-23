<?php

require_once 'SL_Model.php';

class SL_SubModel extends SL_Model
{
    protected $parent_id_name;
    protected $parent_unique = false;

    public function __construct()
    {
        parent::__construct();

        if (empty($this->parent_id_name)) {
            throw new Exception('parent_id_name is must set');
        }
    }

    public function __set($key, $value)
    {
        if ($key == 'parent_id_name') {
            throw new Exception('부모 ID명은 실행중 바꿀수 없습니다.', 1);
        }

        if ($key == 'parent_id') {
            if (!filter_var($value, FILTER_VALIDATE_INT)) {
                throw new Exception('부모 ID는 숫자만 사용가능합니다.', 1);
            }
        }

        parent::__set($key, $value);
    }

    public function update_by_parent_id(array $data)
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

        if (isset($data['parent_id'])) {
            $parent_id = $data['parent_id'];
        } else {
            $parent_id = $data[$this->parent_id_name];
        }

        if (empty($parent_id)) {
            throw new Exception('부모 ID가 없습니다.', 1);
        }

        return $this->pdo->update($this->table, $filtered_data, array($this->parent_id_name => $parent_id));
    }

    public function insert(array $data)
    {
        if ($this->parent_unique) {
            if ($this->get_count_by_parent_id($data[$this->parent_id_name])) {
                return $this->update_by_parent_id($data);
            }
        }

        if (isset($data['parent_id'])) {
            $data[$this->parent_id_name] = $data['parent_id'];
        }

        return parent::insert($data);
    }

    public function get_count_by_parent_id($parent_id)
    {
        $this->pdo->where(array($this->parent_id_name => $parent_id));

        return $this->pdo->count_all_results($this->table);
    }

    public function get_content_by_parent_id($parent_id)
    {
        if (!$this->get_count_by_parent_id($parent_id)) {
            return false;
        }

        $result = $this->get_content_by_parent_id_data($parent_id);

        if (!is_array($result)) {
            return false;
        }

        if (!count($result)) {
            return false;
        }

        return $result;
    }

    public function get_content_by_parent_id_data($parent_id)
    {
        $this->pdo->where(array($this->parent_id_name => $parent_id));
        $this->pdo->order_by('id', 'desc');
        $query = $this->pdo->get($this->table);

        return $query->row_array();
    }

    public function get_count($id = null)
    {
        if (isset($id)) {
            $this->pdo->where(array($this->table . '.' . $this->p_id => $id));
        }

        if (!empty($this->parent_id)) {
            $this->pdo->where(array($this->table . '.' . $this->parent_id_name => $this->parent_id));
        }

        return $this->pdo->count_all_results($this->table);
    }

    protected function get_index_data($per_page = 1000, $page = 0, $order = null, $desc = null, $enable = true)
    {
        if (!empty($this->parent_id)) {
            $this->pdo->where(array($this->table . '.' . $this->parent_id_name => $this->parent_id));
        }

        $this->pdo->order_by($order, $desc);
        $query = $this->pdo->get($this->table, $per_page, $page);

        return $query->result_array();
    }

    public function delete_by_parent_id($parent_id)
    {
        return $this->pdo->delete($this->table, array($this->table . '.' . $this->parent_id_name => $parent_id));
    }
}

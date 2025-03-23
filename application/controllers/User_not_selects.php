<?php

defined('BASEPATH') or exit('No direct script access allowed');
require_once 'SL.php';
require_once 'Searches.php';

class User_not_selects extends Searches
{
    protected $model = 'User';
    protected $permission_controller = 'users';
    protected $script = 'user-not-selects/index.js';
    protected $phone_only=true;

    protected function index_data($category_id = null)
    {
        if($this->input->get('not_phone')) {
            $this->phone_only=false;
        }

        if ($this->input->get('search')) {
            $this->model='Search';
            
            parent::index_data($category_id = null);
            $this->return_data['data']['user']=$this->return_data['data'];
        } else {
            $this->set_page();
            
            if ($this->input->get('all')) {
                $per_page = '1000000';
                $page = 0;
            } else {
                $per_page = $this->per_page;
                $page = $this->page;
            }
            $this->get_user_list($per_page, $page);
        }

        if ($this->format == 'json') {
            if ($this->return_data['data']['user']['total']) {
                $result = array('result' => 'success');
                $result['total'] = $this->return_data['data']['user']['total'];
                $result['list'] = $this->return_data['data']['user']['list'];

                if ($result['total'] == 1) {
                    $user_content = $result['list'][0];
                    $result['content'] = $user_content;
                }

                echo json_encode($result);
            } else {
                echo json_encode(array('result' => 'success', 'total' => $this->return_data['data']['user']['total']));
            }
            exit;
        } else {
            $this->return_data['data']['type'] = 'multi';

            $this->setting_pagination(array('total_rows' => $this->return_data['data']['user']['total']));
            $this->return_data['data']['per_page'] = $this->per_page;
            $this->return_data['data']['page'] = $this->page;
        }
    }
}

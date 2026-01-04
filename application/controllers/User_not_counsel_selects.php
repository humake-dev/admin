<?php

defined('BASEPATH') or exit('No direct script access allowed');
require_once 'SL.php';
require_once 'Searches.php';

class User_not_counsel_selects extends Searches
{
    protected $model = 'User';
    protected $permission_controller = 'users';
    protected $script = 'user-not-counsel-selects/index.js';
    protected $phone_only=true;

    protected function index_data($category_id = null)
    {
            $this->load->model('Counsel');
            $this->Counsel->search = $this->input->get();
    
            if ($this->input->get('start_date')) {
                $this->Counsel->start_date = $this->input->get('start_date');
            }
    
            if ($this->input->get('end_date')) {
                $this->Counsel->end_date = $this->input->get('end_date');
            }


        if($this->input->get('search_type')) {
            $this->Counsel->search_type=$this->input->get('search_type');
        }

            $this->set_page();
            
            $this->return_data['data']['user']=$this->Counsel->get_index($this->per_page, $this->page);

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

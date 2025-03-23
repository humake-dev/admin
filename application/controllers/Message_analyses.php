<?php

defined('BASEPATH') or exit('No direct script access allowed');
require_once 'SL.php';
require_once 'SMS_key.php';

class Message_analyses extends SL_Controller
{
    use SMS_key;

    protected $model = 'MessageAnalysis';
    protected $permission_controller = 'messages';

    protected function index_data($category_id = null)
    {
        $this->load->model($this->model);
        $this->set_page();

        $search_data = $this->set_search_data($this->input->get(null, true));

        $this->{$this->model}->start_date = $search_data['start_date'];
        $this->{$this->model}->end_date = $search_data['end_date'];

        $branch_ids=$this->get_branch_ids();

        $this->{$this->model}->branch_id=$branch_ids;
        $list = $this->{$this->model}->get_index(100000, $this->page);
        $this->return_data['data'] = $list;

        $this->setting_pagination(array('total_rows' => $list['total']));
        $this->return_data['data']['per_page'] = $this->per_page;
        $this->return_data['data']['page'] = $this->page;
    }

    public function current()
    {
        //$this->load->model($this->model);
        $this->set_page();

        /*
        page 	페이지번호 	X(기본 1) 	Integer
        page_size 	페이지당 출력갯수 	X(기본 30) 30~500 	Integer
        start_date 	조회시작일자 	X(기본 최근일자) 	YYYYMMDD
        limit_day
        */

        $this->load->library('form_validation');
        $this->set_search_form_validation();
        $this->set_message();

        $this->set_search_data($this->input->get(null, true));

        if ($this->input->get('page')) {
            $page = $this->input->get('page') + 1;
        } else {
            $page = 1;
        }

        $client = new GuzzleHttp\Client(['verify' => false]);

        $sms_key_pass=$this->get_sms_key();

        $response = $client->request('POST', 'https://apis.aligo.in/list/', array('form_params' => array('userid' => $sms_key_pass['sms_id'], 'key' => $sms_key_pass['sms_key'], 'page' => $page, 'page_size' => $this->per_page, 'start_date' => $this->return_data['search_data']['start_date'], 'limit_day' => $this->return_data['search_data']['end_date'])));

        $response = json_decode($response->getBody());
        $next_page = false;

        if ($response->result_code == 1) {
            $list = array('total' => count($response->list), 'list' => $response->list);
            if ($response->next_yn == 'Y') {
                $next_page = true;
            }
        } else {
            $list = array('total' => 0);
        }

        $this->return_data['data'] = $list;
        $this->return_data['data']['per_page'] = $this->per_page;
        $this->return_data['data']['page'] = $this->page;
        $this->return_data['data']['next_page'] = $next_page;

        $this->render_format();
    }

    protected function set_search_data($data)
    {
        if (empty($data['start_date'])) {
            $default_start_date_obj = new DateTime('now', $this->timezone);
            $default_start_date_obj->modify('first day of this month');
            $data['start_date'] = $default_start_date_obj->format('Y-m-d');
        }

        if (empty($data['end_date'])) {
            $data['end_date'] = $this->today;
        }

        $this->return_data['search_data']['start_date'] = $data['start_date'];
        $this->return_data['search_data']['end_date'] = $data['end_date'];
        $this->return_data['search_data']['display_start_date'] = $data['start_date'];
        $this->return_data['search_data']['display_end_date'] = $data['end_date'];

        return $data;
    }

    protected function set_search_form_validation($id = null)
    {
        $this->form_validation->set_rules('start_date', _('Start Date'), 'callback_valid_date');
        if ($this->input->post('end_date')) {
            $this->form_validation->set_rules('end_date', _('End Date'), 'callback_valid_date|callback_valid_date_after[' . $this->input->post('start_date') . ']');
        }
    }
}

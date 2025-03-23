<?php

defined('BASEPATH') or exit('No direct script access allowed');
require_once 'SL.php';

class Account_others extends SL_Controller
{
    protected $model = 'Account';
    protected $use_index_content = true;
    protected $script = 'accounts/index.js';
    protected $permission_controller = 'accounts';

    protected function index_data($category_id = null)
    {
        $this->set_page();

        $this->load->library('form_validation');
        $this->form_validation->set_data($this->input->get());

        $this->load->model('Other');
        $this->return_data['data'] = $this->Other->get_index($this->per_page, $this->page);
        $this->setting_pagination(array('total_rows' => $this->return_data['data']['total']));
        $this->return_data['data']['per_page'] = $this->per_page;
        $this->return_data['data']['page'] = $this->page;

        $this->layout->add_js('others/index.js?version=' . $this->assets_version);

        $this->form_validation->run();
    }

    public function view($id = null)
    {
        if (empty($id)) {
            show_404();
        }

        $this->set_page();
        $this->set_search_form_validation();
        $this->form_validation->set_rules('type', _('Type'), 'in_list[course,facility,product,other]');

        $this->load->model($this->model);
        $this->set_search();

        if ($this->form_validation->run() == false) {
        } else {
            $type = $this->input->get('type');

            switch ($type) {
                case 'course':
                    $this->load->model('Course');
                    $content = $this->Course->get_content($id);
                    // 빨리 필드 통일하자
                    break;
                case 'facility':
                    $this->load->model('Facility');
                    $content = $this->Facility->get_content($id);
                    break;
                case 'product':
                    $this->load->model('Product');
                    $content = $this->Product->get_content($id);
                    break;
                default:
            }

            $this->return_data['data'] = $this->{$this->model}->get_product_content($content['id'], $type, $this->per_page, $this->page);
            $this->return_data['data']['content'] = $content;
            $this->setting_pagination(array('base_url' => base_url() . 'accounts/view/' . $content['id'], 'total_rows' => $this->return_data['data']['total']));
        }
        $this->render_format();
    }
}

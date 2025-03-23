<?php

defined('BASEPATH') or exit('No direct script access allowed');
require_once 'SL.php';

class Course_categories extends SL_Controller
{
    protected $model = 'ProductCategory';
    protected $check_permission = true;

    protected function set_add_form_data()
    {
        $this->load->model($this->model);
        $this->index_data();
    }

    protected function set_edit_form_data(array $content)
    {
        $this->index_data();
        $this->return_data['data']['content'] = $content;
    }

    protected function set_insert_data($data)
    {
        $data['type'] = 'course';

        return $data;
    }

    protected function index_data($category_id = null)
    {
        $this->load->model($this->model);
        $this->set_page();

        $this->{$this->model}->type = 'course';
        $list = $this->{$this->model}->get_index($this->per_page, $this->page);
        $this->return_data['data'] = $list;

        $this->setting_pagination(array('total_rows' => $list['total']));
        $this->return_data['data']['per_page'] = $this->per_page;
        $this->return_data['data']['page'] = $this->page;
    }

    protected function set_form_validation($id = null)
    {
        $this->form_validation->set_rules('title', _('Title'), 'required|max_length[60]');
        $this->form_validation->set_rules('order_no', _('Order No'), 'required|integer');
    }

    protected function add_redirect_path($id)
    {
        return '/course-categories?id=' . $id;
    }

    public function delete($id)
    {
        $this->load->library('form_validation');
        $this->set_delete_form_validation();
        $this->set_message();

        $content = $this->get_view_content_data($id);

        if ($this->form_validation->run() == false) {
            $this->return_data['data']['content'] = $content;
            if ($this->format == 'html') {
                $this->delete_confirm($id);
            } else {
                echo json_encode(['result' => 'error', 'message' => validation_errors()]);
            }
        } else {
            $data = $this->set_delete_data($content, $this->input->post(null, true));

            $this->load->model('Product');
            $this->Product->category_id = $content['id'];
            $content['products'] = $this->Product->get_index();

            if ($this->{$this->model}->delete($id)) {
                $this->after_delete_data($content, $data);

                if ($this->format == 'json') {
                    echo json_encode(['result' => 'success', 'message' => $this->delete_complete_message($content), 'redirect_path' => $this->delete_redirect_path($content)]);
                } else {
                    $this->session->set_flashdata('message', ['type' => 'success', 'message' => $this->delete_complete_message($content)]);
                    redirect($this->delete_redirect_path($content));
                }
            } else {
                $this->session->set_flashdata('message', ['type' => 'danger', 'message' => _('Delete Fail')]);
                if ($this->format == 'json') {
                    echo json_encode(['result' => 'error', 'message' => _('Delete Fail')]);
                } else {
                    redirect($this->delete_redirect_path($content));
                }
            }
        }
    }

    protected function after_delete_data(array $content, $data = null)
    {
        if (!empty($content['products']['total'])) {
            foreach ($content['products']['list'] as $product) {
                $this->Product->Delete($product['id']);
            }
        }
    }
}

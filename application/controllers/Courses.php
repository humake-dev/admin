<?php

defined('BASEPATH') or exit('No direct script access allowed');
require_once 'Product_extend.php';

class Courses extends Product_extend
{
    protected $category_model = 'productCategory';
    protected $model = 'Course';
    protected $script = 'courses/index.js';
    protected $edit_script = 'courses/index.js';
    protected $per_page = 50;
    protected $permission_controller = 'courses';

    protected function permission_check()
    {
        if (in_array($this->router->fetch_method(), array('index', 'view'))) {
            if ($this->Acl->has_permission('enrolls', 'write')) {
                return true;
            }
        }

        parent::permission_check();
    }

    protected function add_redirect_path($id)
    {
        redirect($this->router->fetch_class() . '/edit/' . $id);
    }

    protected function index_data($category_id = null)
    {
        $this->load->model($this->model);

        if ($this->input->get('branch_id')) {
            $this->{$this->model}->branch_id = $this->input->get('branch_id');
        } else {
            $category = $this->get_category($category_id);

            if ($category['total']) {
                $this->{$this->model}->category_id = $category['current_id'];
            }
        }

        if($this->input->get('lesson_type')) {
            $this->{$this->model}->lesson_type=$this->input->get('lesson_type');
        }

        $list = $this->{$this->model}->get_index($this->per_page, $this->page, 'c.order_no', false);
        $this->return_data['data'] = $list;

        $this->get_admin_list();

        if (isset($category)) {
            $this->return_data['data']['category'] = $category;
        }
    }

    protected function set_add_form_data()
    {
        $this->index_data($this->input->get('category_id'));
    }

    protected function render_form_resource()
    {
        $this->layout->add_js('timepicki.js');
        if (!empty($this->edit_script)) {
            $this->layout->add_js($this->edit_script . '?version=' . $this->assets_version);
        }
    }

    protected function set_edit_form_data(array $content)
    {
        $this->index_data($content['product_category_id']);

        $this->return_data['data']['category']['current_id'] = $content['product_category_id'];
        $this->return_data['data']['content'] = $content;

        $this->return_data['data']['content']['primary_course'] = $this->check_exists_primary_course($content['product_id']);
    }

    protected function set_update_data($id, $data)
    {
        if (empty($data['trainer_id'])) {
            unset($data['trainer_id']);
        }

        if (isset($data['lesson_time_type'])) {
            if ($data['lesson_time_type'] == 1) {
                $data['lesson_period'] = 0;
            }
        }

        if (isset($data['lesson_dow'])) {
            $data['lesson_dayofweek'] = implode('', $data['lesson_dow']);
        } else {
            $data['lesson_dayofweek'] = null;
        }

        if (!in_array($data['lesson_type'], array(4, 5))) {
            $data['user_reservation'] = 0;
        }

        $data['id'] = $id;

        return $data;
    }

    protected function after_update_data($id, $data)
    {
        parent:: after_update_data($id, $data);

        if (($data['edit_content']['price'] != $data['price']) or ($data['edit_content']['title'] != $data['price'])) {
            $this->load->model('Product');

            if ($data['edit_content']['price'] != $data['price']) {
                $this->Product->update(array('price' => $data['price'], 'id' => $data['edit_content']['product_id']));
            }

            if ($data['edit_content']['title'] != $data['title']) {
                $this->Product->update(array('title' => $data['title'], 'id' => $data['edit_content']['product_id']));
            }
        }

        $primary_course_relation_id = $this->check_exists_primary_course($data['edit_content']['product_id']);

        if (empty($primary_course_relation_id)) {
            if (!empty($data['primary_course'])) {
                $this->ProductRelation->insert(array('product_relation_type_id' => PRIMARY_COURSE_ID, 'rel_product_type' => 'enroll', 'product_id' => $data['edit_content']['product_id']));
            }
        } else {
            if (empty($data['primary_course'])) {
                $this->ProductRelation->delete($primary_course_relation_id);
            }
        }
    }

    protected function check_exists_primary_course($product_id)
    {
        $this->load->model('ProductRelation');
        $this->ProductRelation->product_id = $product_id;
        $product_relations = $this->ProductRelation->get_index();

        if (empty($product_relations['total'])) {
            return false;
        }

        $primary_course_id = false;

        foreach ($product_relations['list'] as $product_relation) {
            if ($product_relation['product_relation_type_id'] == PRIMARY_COURSE_ID) {
                $primary_course_id = $product_relation['id'];
            }
        }

        return $primary_course_id;
    }

    protected function edit_redirect_path($id)
    {
        return $this->router->fetch_class() . '/edit/' . $id;
    }

    protected function delete_redirect_path(array $content)
    {
        return $this->router->fetch_class() . '?category_id=' . $content['category_id'];
    }

    protected function set_form_validation($id = null)
    {
        $this->form_validation->set_rules('title', _('Course Name'), 'required|min_length[2]|max_length[60]');

        if ($this->router->fetch_method() == 'edit') {
            $this->form_validation->set_rules('product_category_id', _('Course Category'), 'integer');
            $this->form_validation->set_rules('status', _('Status'), 'required|in_list[0,1]');
            $this->form_validation->set_rules('trainer_id', _('Trainer'), 'integer');
            $this->form_validation->set_rules('lesson_type', _('Lesson Type'), 'required|integer');
            $this->form_validation->set_rules('lesson_quantity', _('Lesson Quantity'), 'required|integer');

            if ($this->input->post('lesson_time_type') != 1) {
                $this->form_validation->set_rules('lesson_period', _('Lesson Period'), 'required|integer');
                $this->form_validation->set_rules('lesson_period_unit', _('Lesson Period Unit'), 'required|in_list[M,W,D]');
            }
            $this->form_validation->set_rules('price', _('Price'), 'required|integer');
            $this->form_validation->set_rules('quota', _('Quota'), 'required|integer');
            $this->form_validation->set_rules('user_reservation', _('user_reservation'), 'required|in_list[1,0]');
            $this->form_validation->set_rules('min_time', _('min_time'), 'integer');
        } else {
            $this->form_validation->set_rules('product_category_id', _('Course Category'), 'required|integer');
            $this->form_validation->set_rules('status', _('Status'), 'in_list[0,1]');
        }

        $this->form_validation->set_rules('limit_reservation_type', _('Limit Reservation Type'), 'in_list[day,time,dayntime]');
        $this->form_validation->set_rules('limit_cancel_type', _('Limit Cancel Type'), 'in_list[day,time,dayntime]');

        if (in_array($this->input->post('limit_reservation_type'), array('day', 'dayntime'))) {
            $this->form_validation->set_rules('limit_reservation_day', _('Limit Reservation Day'), 'integer');
        }

        if (in_array($this->input->post('limit_reservation_type'), array('time', 'dayntime'))) {
            $this->form_validation->set_rules('limit_reservation_time', _('Limit Reservation Time'), 'callback_valid_time');
        }

        if (in_array($this->input->post('limit_cancel_type'), array('day', 'dayntime'))) {
            $this->form_validation->set_rules('limit_cancel_day', _('Limit Cancel Day'), 'integer');
        }

        if (in_array($this->input->post('limit_cancel_type'), array('time', 'dayntime'))) {
            $this->form_validation->set_rules('limit_cancel_time', _('Limit Cancel Time'), 'callback_valid_time');
        }
        $this->form_validation->set_rules('order_no', _('Order No'), 'required|integer');        
    }
}

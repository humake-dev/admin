<?php

defined('BASEPATH') or exit('No direct script access allowed');
require_once 'Product_extend.php';

class Facilities extends Product_extend
{
    protected $model = 'Facility';
    protected $permission_controller = 'facilities';

    protected function permission_check()
    {
        if (in_array($this->router->fetch_method(), array('index', 'view'))) {
            if ($this->Acl->has_permission('rents', 'write')) {
                return true;
            }
        }

        parent::permission_check();
    }

    protected function index_data($category_id = null)
    {
        $this->load->model($this->model);
        $this->set_page();

        if ($this->input->get('branch_id')) {
            $this->{$this->model}->branch_id = $this->input->get('branch_id');
        }

        $list = $this->{$this->model}->get_index($this->per_page, $this->page);
        $this->return_data['data'] = $list;

        if ($this->use_index_content) {
            $this->return_data['data']['content'] = $this->get_list_view_data($list);
        }

        $this->setting_pagination(array('total_rows' => $list['total']));
        $this->return_data['data']['per_page'] = $this->per_page;
        $this->return_data['data']['page'] = $this->page;
    }

    protected function set_add_form_data()
    {
        $this->load->model($this->model);
        $list = $this->{$this->model}->get_index();

        $this->load->model('productRelation');
        $this->productRelation->product_relation_type_id = PRIMARY_COURSE_ID;

        $this->return_data['data'] = $list;
        $this->return_data['data']['exists_primary_product'] = $this->productRelation->get_count();

        $this->script = 'facilities/add.js';
    }

    protected function after_insert_data($id, $data)
    {
        parent::after_insert_data($id, $data);
        $this->update_sub_product($data['product_id'], $data);
    }

    protected function set_edit_form_data(array $content)
    {
        $this->load->model($this->model);
        $list = $this->{$this->model}->get_index();

        $this->load->model('productRelation');
        $this->productRelation->product_relation_type_id = PRIMARY_COURSE_ID;
        $exists_primary_product = $this->productRelation->get_count();

        $this->return_data['data'] = $list;
        $this->return_data['data']['exists_primary_product'] = $exists_primary_product;

        if ($exists_primary_product) {
            $this->productRelation->product_relation_type_id = SUB_ORDER_ID;
            $this->productRelation->product_id = $content['product_id'];
            $this->return_data['data']['is_sub_product'] = $this->productRelation->get_count();
        }

        $this->return_data['data']['content'] = $content;
        $this->script = 'facilities/add.js';
    }

    protected function after_update_data($id, $data)
    {
        parent::after_update_data($id, $data);

        $product_id = $data['edit_content']['product_id'];

        $this->update_sub_product($product_id, $data);
    }

    protected function update_sub_product($id, $data)
    {
        $this->load->model('ProductRelation');
        $this->ProductRelation->product_relation_type_id = SUB_ORDER_ID;
        $this->ProductRelation->rel_product_type = 'rent';
        $this->ProductRelation->product_id = $id;

        if ($this->ProductRelation->get_count()) {
            if (empty($data['sub_product'])) {
                $list = $this->ProductRelation->get_index();

                return $this->ProductRelation->delete($list['list'][0]['id']);
            }
        } else {
            if (empty($data['sub_product'])) {
                return true;
            }

            return $this->ProductRelation->insert(array('product_id' => $id, 'rel_product_type' => 'rent'));
        }
    }

    protected function set_form_validation($id = null)
    {
        $this->form_validation->set_rules('title', _('Facility Title'), 'required|trim|max_length[60]');
        $this->form_validation->set_rules('gender', _('Gender'), 'required|in_list[0,1,2]');
        $this->form_validation->set_rules('quantity', _('Quantity'), 'required|integer');
        $this->form_validation->set_rules('start_no', _('Start No'), 'required|integer');
        $this->form_validation->set_rules('order_no', _('Order No'), 'required|integer');
        $this->form_validation->set_rules('price', _('Price'), 'required|integer');
        $this->form_validation->set_rules('use_not_set', _('Use Not Set'), 'required|in_list[0,1]');
    }
}

<?php

defined('BASEPATH') or exit('No direct script access allowed');
require_once 'SL_photo.php';

class Products extends SL_photo
{
    protected $model = 'Product';
    protected $file_model ='ProductPicture';

    protected function index_data($category_id = null)
    {
        $this->load->model($this->model);
        $this->set_page();

        $this->{$this->model}->type = array('product', 'sports_wear');

        if ($this->input->get('branch_id')) {
            $this->{$this->model}->branch_id = $this->input->get('branch_id');
        }

        $list = $this->{$this->model}->get_index($this->per_page, $this->page);
        $this->return_data['data'] = $list;

        $this->load->model('ProductCategory');
        $this->ProductCategory->type = array('product', 'sports_wear');
        $this->return_data['data']['category'] = $this->ProductCategory->get_index(100, 0, 'order_no', true, false);

        $this->load->model('ProductRelation');
        $this->ProductRelation->product_relation_type_id = PRIMARY_COURSE_ID;

        $this->return_data['data']['exists_primary_product'] = $this->ProductRelation->get_count();

        $this->setting_pagination(array('total_rows' => $list['total']));
        $this->return_data['data']['per_page'] = $this->per_page;
        $this->return_data['data']['page'] = $this->page;
    }

    protected function set_add_form_data()
    {
        $this->index_data();
    }

    protected function set_edit_form_data(array $content)
    {
        $this->index_data();
        $this->return_data['data']['content'] = $content;

        if ($this->return_data['data']['exists_primary_product']) {
            $this->load->model('ProductRelation');
            $this->ProductRelation->product_relation_type_id = SUB_ORDER_ID;
            $this->ProductRelation->product_id = $content['id'];
            $this->return_data['data']['is_sub_product'] = $this->ProductRelation->get_count();
        }
    }

    protected function after_insert_data($id, $data)
    {
        parent::after_insert_data($id, $data);
        $this->update_category_count($data);
        $this->update_sub_product($id, $data);
    }

    protected function after_update_data($id, $data)
    {
        parent::after_update_data($id, $data);
        $this->update_sub_product($id, $data);
    }

    protected function update_sub_product($id, $data)
    {
        $this->load->model('ProductRelation');
        $this->ProductRelation->product_relation_type_id = SUB_ORDER_ID;
        $this->ProductRelation->rel_product_type = 'rent_sw';
        $this->ProductRelation->product_id = $id;

        if ($this->ProductRelation->get_count()) {
            if (empty($data['sub_product'])) {
                $list = $this->ProductRelation->get_index();
                $this->ProductRelation->delete($list['list'][0]['id']);
            }
        } else {
            if (empty($data['sub_product'])) {
                return true;
            }

            return $this->ProductRelation->insert(array('product_id' => $id, 'rel_product_type' => 'rent_sw'));
        }
    }

    protected function after_delete_data(array $content, $data = null)
    {
        $this->update_category_count(array('product_category_id' => $content['product_category_id']));
    }

    protected function update_category_count($data)
    {
        $this->load->model($this->model);
        $this->{$this->model}->category_id = $data['product_category_id'];
        $count = $this->{$this->model}->get_count();

        $this->load->model('ProductCategory');
        $this->ProductCategory->update(array('product_counts' => $count, 'id' => $data['product_category_id']));
    }

    protected function add_redirect_path($id)
    {
        return $this->router->fetch_class();
    }

    protected function edit_redirect_path($id)
    {
        return $this->router->fetch_class() . '/edit/' . $id;
    }

    protected function set_form_validation($id = null)
    {
        $this->form_validation->set_rules('product_category_id', _('Category'), 'required|integer');
        $this->form_validation->set_rules('title', _('Title'), 'required|min_length[1]|max_length[60]');
        $this->form_validation->set_rules('price', _('Price'), 'required|integer');
    }
}

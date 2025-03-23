<?php

defined('BASEPATH') or exit('No direct script access allowed');
require_once 'SL.php';

class Product_extend extends SL_Controller
{
    protected $permission_controller = 'products';

    protected function set_insert_data($data)
    {
        $this->load->model('Product');
        $data['product_id'] = $this->Product->insert($data);

        return $data;
    }

    protected function set_update_data($id, $data)
    {
        $data['id'] = $id;

        $this->load->model('Product');
        $this->Product->update(array('id' => $data['edit_content']['product_id'], 'title' => $data['title'], 'gender' => $data['gender'], 'price' => $data['price']));

        $data['product_id'] = $data['edit_content']['product_id'];

        return $data;
    }

    protected function after_update_data($id, $data)
    {
        $content = $this->get_view_content_data($id);

        if (!empty($content['product_category_id'])) {
            $this->update_category_count($content['product_category_id']);
        }
    }

    protected function get_view_data($id)
    {
        $content = parent::get_view_data($id);

        if ($this->input->get('user_id')) {
            $this->load->model('Order');
            $this->Order->product_id = $content['product_id'];
            $this->Order->user_id = $this->input->get('user_id');

            $content['re_order'] = 0;
            if ($this->Order->get_count()) {
                $content['re_order'] = 1;
            }
        }

        return $content;
    }

    protected function update_category_count($product_category_id = null)
    {
        if (empty($product_category_id)) {
            return false;
        }

        $this->load->model('Product');
        $this->Product->category_id = $product_category_id;
        $count = $this->Product->get_count();

        $this->load->model('ProductCategory');
        $this->ProductCategory->update(array('product_counts' => $count, 'id' => $product_category_id));
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
                echo json_encode(array('result' => 'error', 'message' => validation_errors()));
            }
        } else {
            $data = $this->set_delete_data($content, $this->input->post(null, true));

            $this->load->model('Product');
            if ($this->Product->delete($content['product_id'])) {
                $this->after_delete_data($content, $data);

                $this->session->set_flashdata('message', array('type' => 'success', 'message' => $this->delete_complete_message($content)));
                if ($this->format == 'json') {
                    echo json_encode(array('result' => 'success', 'message' => $this->delete_complete_message($content), 'redirect_path' => $this->delete_redirect_path($content)));
                } else {
                    redirect($this->delete_redirect_path($content));
                }
            } else {
                $this->session->set_flashdata('message', array('type' => 'danger', 'message' => _('Delete Fail')));
                if ($this->format == 'json') {
                    echo json_encode(array('result' => 'error', 'message' => _('Delete Fail')));
                } else {
                    redirect($this->delete_redirect_path($content));
                }
            }
        }
    }

    protected function after_delete_data(array $content, $data = null)
    {
        if (empty($content['product_category_id'])) {
            return true;
        }

        $this->update_category_count($content['product_category_id']);
    }
}

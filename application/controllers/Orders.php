<?php

defined('BASEPATH') or exit('No direct script access allowed');
require_once 'SL.php';

class Orders extends SL_Controller
{
    protected $model = 'Order';
    protected $permission_controller = 'users';
    protected $script = 'orders/index.js';

    protected function insert_complete_message($id)
    {
        return _('Successfully Created Order');
    }

    protected function set_insert_data($data)
    {
        $this->load->model('Product');

        $orders = array();
        $t_total_price = 0;
        foreach ($this->input->post('order') as $index => $order) {
            $product = $this->Product->get_content($order['product']);
            $total_price = $product['price'] * $order['quantity'];

            $t_total_price += $total_price;

            $orders['order_product'][$index]['product_id'] = $order['product'];
            $orders['order_product'][$index]['quantity'] = $order['quantity'];
            $orders['order_product'][$index]['total_price'] = $total_price;
        }

        $orders['order']['user_id'] = $data['user_id'];
        $orders['order']['amount'] = $t_total_price;
        $orders['order']['point'] = $t_total_price;
        if (empty($data['transaction_date'])) {
            $orders['order']['transaction_date'] = $this->today;
        } else {
            $orders['order']['transaction_date'] = $data['transaction_date'];
        }

        return $orders;
    }

    protected function after_insert_data($id, $data)
    {
        $this->load->model('Account');
        $this->load->model('OrderProduct');

        foreach ($data['order_product'] as $index => $value) {
            $value['order_id'] = $id;
            $order_product_id = $this->OrderProduct->insert($value);

            $account_data = $data['order'];
            $account_data['type'] = 'I';
            $account_data['order_id'] = $id;
            $account_data['account_category_id'] = ADD_ORDER;

            $account_id = $this->Account->insert($account_data);
            $this->AccountOrderProduct->insert(array('account_id' => $account_id, 'order_product_id' => $order_product_id));
        }

        $this->load->model('User');
        $user = $this->User->get_content($data['order']['user_id']);
    }

    protected function set_add_form_data()
    {
        if ($this->input->get('product_category_id')) {
            $this->load->model('Product');
            $this->Product->category_id = $this->input->get('product_category_id');
            $data['return_data']['product'] = $this->Product->get_index($this->per_page, $this->page);
        }
    }

    protected function add_redirect_path($id)
    {
        return '/payments/add?order_id=' . $id;
    }

    public function validate_user($user_id = null)
    {
        if (empty($user_id)) {
            $user_id = $this->session->userdata('user_id');
        }

        $this->load->model('User');
        if ($this->User->check_exists($user_id)) {
            return true;
        } else {
            return false;
        }
    }

    public function validate_admin($admin_id = null)
    {
        if (empty($admin_id)) {
            $admin_id = $this->session->userdata('admin_id');
        }

        $this->load->model('Admin');
        if ($this->Admin->check_exists($admin_id)) {
            return true;
        } else {
            return false;
        }
    }

    public function validate_order($order)
    {
        $orders = $this->input->post('order');
        if (!is_array($orders)) {
            return false;
        }

        foreach ($orders as $index => $value) {
            if (!array_key_exists('product', $value)) {
                return false;
            }

            if (!array_key_exists('quantity', $value)) {
                return false;
            }
        }

        return true;
    }

    public function check_point($order)
    {
        $orders = $this->input->post('order');
        if (!is_array($orders)) {
            return false;
        }

        $this->load->model('Product');

        $orders = array();
        $t_total_price = 0;
        foreach ($this->input->post('order') as $index => $order) {
            $product = $this->Product->get_content($order['product']);
            $total_price = $product['price'] * $order['quantity'];

            $t_total_price += $total_price;
        }
        $this->load->model('User');
        $content = $this->User->get_content($this->input->post('user_id'));

        if ($t_total_price > $content['point']) {
            return false;
        } else {
            return true;
        }
    }

    protected function get_error_messages()
    {
        $message = parent::get_error_messages();
        $message['validate_order'] = _('Not validate order');
        $message['validate_user'] = _('Not validate user');
        $message['check_point'] = _('Not enough point');

        return $message;
    }

    protected function set_form_validation($id = null)
    {
        if ($this->input->post('type') == 'admin') {
            $this->form_validation->set_rules('admin_id', _('Admin'), 'required|integer|callback_validate_admin');
            $this->form_validation->set_rules('product_id', _('Product'), 'required|integer');
        } else {
            $this->form_validation->set_rules('order[]', _('Order'), 'required');
            $this->form_validation->set_rules('order', _('Order'), 'callback_validate_order|callback_check_point');
            $this->form_validation->set_rules('user_id', _('User'), 'required|integer|callback_validate_user');
        }
    }
}

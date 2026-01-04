<?php

defined('BASEPATH') or exit('No direct script access allowed');
require_once 'Order_extend.php';

class Others extends Order_extend
{
    protected $model = 'Other';
    protected $permission_controller = 'accounts';
    protected $script = 'others/index.js';
    protected $add_id = ADD_OTHER;

    protected function set_insert_data($data)
    {
        $data = parent::set_insert_data($data);

        $data['original_price'] = $data['payment'];
        $data['price'] = $data['payment'];

        return $data;
    }

    public function set_add_form_data()
    {
        $this->set_page();

        $this->load->model('Other');
        $this->return_data['data'] = $this->Other->get_index($this->per_page, $this->page);
        $this->setting_pagination(array('total_rows' => $this->return_data['data']['total']));
        $this->return_data['data']['per_page'] = $this->per_page;
        $this->return_data['data']['page'] = $this->page;
    }

    protected function add_redirect_path($id)
    {
        if ($this->input->post('return_url')) {
            return $this->input->post('return_url');
        } else {
            return '/account-others';
        }
    }

    public function set_edit_form_data(array $content)
    {
        $this->set_add_form_data();
        $this->return_data['data']['content'] = $content;
    }

    protected function set_order_insert_data($p_orders)
    {
        $this->load->model('Product');

        $orders = array();
        $t_total_price = 0;

        $t_total_cash = 0;
        $t_total_credit = 0;
        foreach ($p_orders['list'] as $index => $order) {
            $product = $this->Product->get_content($order['product']);

            if (isset($order['quantity'])) {
                $quantity = $order['quantity'];
            } else {
                $quantity = 1;
            }

            $total_price = $product['price'] * $quantity;
            $t_total_price += $total_price;

            $orders['order_product'][$index]['product_id'] = $order['product'];
            $orders['order_product'][$index]['quantity'] = $quantity;
            $orders['order_product'][$index]['total_price'] = $total_price;

            if (isset($order['cash'])) {
                $t_total_cash += $order['cash'];
            }

            if (isset($order['credit'])) {
                $t_total_credit += $order['credit'];
            }
        }

        $orders['order']['user_id'] = $p_orders['user_id'];
        $orders['order']['cash'] = $t_total_cash;
        $orders['order']['credit'] = $t_total_credit;
        if (empty($p_orders['transaction_date'])) {
            $orders['order']['transaction_date'] = $this->today;
        } else {
            $orders['order']['transaction_date'] = $p_orders['transaction_date'];
        }

        return $orders;
    }

    protected function after_insert_data($id, $data)
    {
        $data['type'] = 'I';
        $data['other_id'] = $id;
        $data['product_id'] = 10;
        $data['account_category_id'] = ADD_OTHER;

        $this->load->model('Account');
        $account_id = $this->Account->insert($data);
    }

    protected function set_form_validation($id = null)
    {
        $this->form_validation->set_rules('title', _('Content'), 'required|trim');
        $this->form_validation->set_rules('transaction_date', _('Transaction Date'), 'required|callback_valid_date');

        if ($this->router->fetch_method() == 'add') {
            $this->form_validation->set_rules('user_id', _('User'), 'required|integer');
        }
    }

    public function disable($id)
    {
        $this->load->library('form_validation');
        $this->set_message();

        $this->form_validation->set_rules('id', _('id'), 'integer');

        $content = $this->get_view_content_data($id);
        $this->return_data['data']['content'] = $content;

        if ($this->form_validation->run() == false) {
            $this->render_format();
        } else {
            $this->load->model('Order');
            if ($this->Order->update(array('enable' => 0, 'id' => $content['order_id']))) {
                $this->session->set_flashdata('message', array('type' => 'success', 'message' => _('Successfully Delete Expire Log')));

                if ($this->input->post('return_url')) {
                    $redirect = $this->input->post('return_url');
                } else {
                    $redirect = '/home/' . str_replace('_', '-', $this->router->fetch_class()) . '/' . $id;
                }

                redirect($redirect);
            }
        }
    }

    public function delete($id)
    {
        $this->load->library('form_validation');
        $this->set_message();

        $this->form_validation->set_rules('id', _('id'), 'integer');
        if ($this->input->post('insert_refund') or $this->input->post('cancel') === 0) {
            $this->form_validation->set_rules('cash', _('Cash'), 'required|integer');
            $this->form_validation->set_rules('credit', _('Credit'), 'required|integer');
        }

        $content = $this->get_view_content_data($id);

        $this->return_data['data']['content'] = $content;

        if ($this->form_validation->run() == false) {
            $this->script = 'orders/delete.js';
            $this->render_format();
        } else {
            $data = $this->input->post(null, true);

            $data['order_id'] = $content['order_id'];
            $data['user_id'] = $content['user_id'];

            if (isset($data['cancel'])) {
                $cancel = $data['cancel'];
            }

            $this->load->model('Order');

            if ($this->Order->delete($content['order_id'])) {
                $this->after_delete_data($content, $data);

                $data['type'] = 'O';
                $data['order_id'] = $content['order_id'];
                $data['account_category_id'] = $this->refund_id;
                if (!empty($content['trainer_id'])) {
                    $data['trainer_id'] = $content['trainer_id'];
                }

                if (!empty($data['insert_refund'])) {
                    $this->load->model('User');
                    $user = $this->User->get_content($content['user_id']);

                    if (!empty($user['fc_id'])) {
                        $data['fc_id'] = $user['fc_id'];
                    }

                    $this->load->model('Account');
                    $account_id = $this->Account->insert($data);
                }

                if (!empty($cancel)) {
                    $this->Account->delete($a_content['id']);
                }

                $this->session->set_flashdata('message', array('type' => 'success', 'message' => $this->delete_complete_message($content)));

                if ($this->format == 'json') {
                    echo json_encode(array('result' => 'success', 'message' => $this->delete_complete_message($content), 'redirect_path' => $this->delete_redirect_path($content)));
                } else {
                    redirect($this->delete_redirect_path($content));
                }
            } else {
                if ($this->format == 'json') {
                    echo json_encode(array('result' => 'error', 'message' => _('Delete Fail')));
                } else {
                    $this->session->set_flashdata('message', array('type' => 'danger', 'message' => _('Delete Fail')));
                    redirect($this->delete_redirect_path($content));
                }
            }
        }
    }

    protected function delete_redirect_path(array $content)
    {
        if ($this->input->post('return_url')) {
            return $this->input->post('return_url');
        } else {
            return 'account-others';
        }
    }
}

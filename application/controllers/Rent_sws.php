<?php

defined('BASEPATH') or exit('No direct script access allowed');
require_once 'Order_extend.php';
require_once 'Search_period.php';

class Rent_sws extends Order_extend
{
    use Search_period;

    protected $model = 'RentSw';
    protected $permission_controller = 'rent_sws';
    protected $use_index_content = true;
    protected $script = 'rent-sws/index.js';

    protected function index_data($category_id = null)
    {
        $this->set_page();
        $this->set_search_form_validation();

        $search_type = 'default';
        $search = false;

        $this->load->model($this->model);

        $m_search = $this->input->get();
        $this->{$this->model}->search = $m_search;

        if ($this->product_model) {
            $product = $this->get_category($category_id);

            if ($product['total']) {
                $this->{$this->model}->category_id = $product['current_id'];
            }
        }

        if ($this->input->get('payment_id')) {
            $this->return_data['search_data']['payment_id'] = $this->input->get('payment_id');
        }


        if ($this->input->get('search_type')) {
            if ($this->input->get('search_type') == 'field') {
                $this->session->set_userdata('rent_sws_search_open', 'field');
                $this->{$this->model}->search_type = 'field';
            } else {
                $this->session->set_userdata('rent_sws_search_open', 'default');
            }
        }

        if ($this->form_validation->run() == false) {
            $this->return_data['search_data']['period_display'] = true;
            $data = $this->{$this->model}->get_index($this->per_page, $this->page);
        } else {
            $search_data = $this->input->get();

            if (count($search_data)) {
                $s_search_data = $search_data;
                if (isset($s_search_data['page'])) {
                    unset($s_search_data['page']);
                }

                if (count($s_search_data)) {
                    $this->{$this->model}->search = $s_search_data;
                    $this->set_search();
                }
            }

            $data = $this->{$this->model}->get_index($this->per_page, $this->page);

            $search = true;
        }

        $this->return_data['data'] = $data;

        if ($this->use_index_content) {
            $this->return_data['data']['content'] = $this->get_list_view_data($data);
        }

        $this->return_data['search_data']['search_type'] = $search_type;
        $this->return_data['search_data']['search'] = $search;

        $this->setting_pagination(['total_rows' => $data['total']]);
        $this->return_data['data']['per_page'] = $this->per_page;
        $this->return_data['data']['page'] = $this->page;
    }

    protected function set_add_form_data()
    {
        $this->set_form_data();
        $this->script = 'rent-sws/add.js';
    }

    protected function set_edit_form_data(array $content)
    {
        $this->set_form_data($content['id']);
        $this->script = 'rent-sws/add.js';
    }

    protected function set_form_data($id = null)
    {
        if (empty($id)) {
            if ($this->input->get_post('user_id')) {
                $this->load->model('User');
                $user_content = $this->User->get_content($this->input->get_post('user_id'));
            } else {
                if ($this->input->get_post('card_no')) {
                    $this->load->model('User');
                    $user_content = $this->User->get_content_by_card_no($this->input->get_post('card_no'), $this->session->userdata('branch_id'));
                }
            }

            if (!empty($user_content)) {
                $user_id = $user_content['id'];
            }
        } else {
            $content = $this->get_view_content_data($id);
            $user_id = $content['user_id'];

            $this->load->model('User');
            $user_content = $this->User->get_content($user_id);
        }

        if (isset($user_id)) {
            // 왼쪽 수강정보 리스트
            $this->load->model('Enroll');
            $this->Enroll->user_id = $user_id;
            $this->Enroll->get_not_end_only = true;
            $this->return_data['data']['enroll_list'] = $this->Enroll->get_index(5, 0, 'start_date');
        } else {
            $list = array('total' => 0);
        }

        $this->load->model('Product');
        $this->Product->type = 'sports_wear';
        $product_list = $this->Product->get_index();

        $this->return_data['data']['product'] = $product_list;

        if ($this->input->get_post('product_id')) {
            $product = $this->Product->get_content($this->input->get_post('product_id'));
        }

        if (empty($product)) {
            if ($product_list['total']) {
                $product_content = $product_list['list'][0];
            } else {
                $product_content = false;
            }
        } else {
            $product_content = $product;
        }

        $this->return_data['data']['per_page'] = $this->per_page;
        $this->return_data['data']['page'] = $this->page;
        $this->return_data['data']['product']['content'] = $product_content;
        $this->return_data['data']['product_price'] = $product_content['price'];

        if (isset($content)) {
            $this->return_data['data']['content'] = $content;
        }

        if (!empty($user_content)) {
            $this->return_data['data']['user_content'] = $user_content;
        }
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

    protected function set_insert_data($data = null)
    {
        $data = parent::set_insert_data($data);
        $data['insert_quantity'] = $data['rent_month'];

        return $data;
    }

    protected function after_insert_data($id, $data)
    {
        $data['account_category_id'] = ADD_ORDER;

        $this->load->model('Account');
        $data['account_id'] = $this->Account->insert($data);
    }

    protected function insert_complete_message($id)
    {
        return _('Successfully Rented Sports Wear');
    }

    protected function add_redirect_path($id)
    {
        if ($this->input->post('return_url')) {
            return $this->input->post('return_url');
        } else {
            $content = $this->get_view_content_data($id);

            return str_replace('_', '-', $this->router->fetch_class()) . '?product_id=' . $content['id'];
        }
    }

    protected function edit_redirect_path($id)
    {
        return $this->add_redirect_path($id);
    }

    public function index_oc($type = null)
    {
        if (in_array($type, ['default', 'field'])) {
            $this->session->set_userdata('rent_sws_search_open', $type);
        } else {
            $this->session->unset_userdata('rent_sws_search_open');
        }
        echo json_encode(['result' => 'success']);
    }

    protected function set_search_form_validation()
    {
        $this->load->library('form_validation');
        $this->form_validation->set_data($this->input->get());
        $this->set_period_search_validation();
        $this->form_validation->set_rules('search_type', _('Search Type'), 'in_list[default,field]');
        $this->form_validation->set_rules('user_type', _('User Type'), 'in_list[all,paid,free]');
        $this->form_validation->set_rules('status_type', _('By Status'), 'in_list[all,using,expired,reservation]');
        $this->form_validation->set_rules('search_period', _('Search Period'), 'in_list[all,transaction_date,start_date,end_date]');
        $this->form_validation->set_rules('search_field', _('Search Field'), 'in_list[name]');
        $this->form_validation->set_rules('search_word', _('Search Word'), 'min_length[1]|trim|max_length[20]');
    }

    protected function set_form_validation($id = null)
    {
        if ($this->router->fetch_method() == 'add') {
            $this->form_validation->set_rules('user_id', _('User'), 'required|integer');
        }

        $this->form_validation->set_rules('product_id', _('Product'), 'required|integer');
        $this->form_validation->set_rules('original_price', _('Original Price'), 'required|integer');
        $this->form_validation->set_rules('dc_rate', _('DC Rate'), 'integer');
        $this->form_validation->set_rules('dc_point', _('DC Point'), 'integer');
        $this->form_validation->set_rules('price', _('Sell Price'), 'required|integer');
        $this->form_validation->set_rules('cash', _('Cash'), 'integer');
        $this->form_validation->set_rules('credit', _('Credit'), 'integer');
        $this->form_validation->set_rules('start_date', _('Start Date'), 'required|callback_valid_date');
        $this->form_validation->set_rules('end_date', _('End Date'), 'required|callback_valid_date|callback_valid_date_after[' . $this->input->post('start_date') . ']');
    }

    protected function delete_complete_message(array $content)
    {
        return _('Successfully Delete Rent Sports Wear');
    }

    protected function delete_redirect_path(array $content)
    {
        return $_SERVER['HTTP_REFERER'];
    }
}

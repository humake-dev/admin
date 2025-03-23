<?php

defined('BASEPATH') or exit('No direct script access allowed');
require_once 'SL.php';
require_once 'Ac_sync.php';

class Order_extend extends SL_Controller
{
    use Ac_sync;
    protected $permission_controller = 'users';
    protected $p_relid = 'product_id';
    protected $product_model = 'product';
    protected $script = 'orders/extend.js';
    protected $add_id = ADD_ORDER;
    protected $edit_id = EDIT_ORDER;
    protected $refund_id = REFUND_ORDER;

    protected function get_category($category_id = null)
    {
        if ($this->product_model) {
            $this->load->model($this->product_model);
            $category = $this->{$this->product_model}->get_index(100, 0, 'id', true, false);

            if (empty($category_id)) {
                if ($category['total']) {
                    $category['content'] = $category['list'][0];
                }
            } else {
                if (!$this->{$this->product_model}->get_count($this->input->get($this->category_id_name))) {
                    show_404();
                }

                $category['content'] = $this->{$this->product_model}->get_content($category_id);
            }

            if ($category['total']) {
                $category['current_id'] = $category['content']['id'];
            }

            return $category;
        } else {
            return array('total' => 0);
        }
    }

    protected function set_insert_data($data)
    {
        $this->load->model($this->product_model);
        $product = $this->{$this->product_model}->get_content($data[$this->p_relid]);

        if (empty($data['dc_rate'])) {
            $data['dc_rate'] = 0;
        }

        if (empty($data['dc_price'])) {
            $data['dc_price'] = 0;
        }

        if(isset($data['payment_method'])) {
            if ($data['payment_method'] == 4) {
                $data['credit'] = $data['mix_credit'];
                $data['cash'] = $data['mix_cash'];
            }
        }

        if(empty($data['transaction_date'])) {
            if(empty($data['transaction_date_is_today']) and !empty($data['custom_transaction_date'])) {
                $data['transaction_date']  = $data['custom_transaction_date'].' 00:00:01';
            } else {
                $data['transaction_date']  = $this->today;
            }
        }

        unset($data['custom_transaction_date']);

        if (empty($data['insert_quantity'])) {
            $data['insert_quantity'] = 1;
        }

        if (empty($data['re_order'])) {
            $data['re_order'] = 0;
        } else {
            $data['re_order'] = 1;
        }

        if (empty($data['use_auto_extend'])) {
            $data['use_auto_extend'] = 0;
        } else {
            $data['use_auto_extend'] = 1;
        }

        return $this->calculate_price($product, $data);
    }

    protected function calculate_price($product, $data)
    {
        if (empty($data['original_price'])) {
            $data['original_price'] = $product['price'];
        }

        if (empty($data['insert_quantity'])) {
            $data['insert_quantity'] = 1;
        }

        $payment = 0;

        if (!empty($data['cash'])) {
            $payment = $payment + $data['cash'];
        }

        if (!empty($data['credit'])) {
            $payment = $payment + $data['credit'];
        }

        $data['payment'] = $payment;
        $data['product'] = $product;
        $data['product_id'] = $product['product_id'];

        return $data;
    }

    protected function after_same_insert($id)
    {
        $this->load->model($this->model);
        $content = $this->{$this->model}->get_content($id);
        
        $date_obj = new DateTime($content['end_date'], $this->timezone);
        $date_obj->modify('+1 Days');
        $content['start_date'] = $date_obj->format('Y-m-d');

        $date_obj->modify('+1 Month');
        $content['end_date'] = $date_obj->format('Y-m-d');

        $content['transaction_date'] = $this->today;
        $content['re_order'] = 1;

        return $content;
    }

    public function add()
    {
        $this->load->library('form_validation');
        $this->set_form_validation();
        $this->set_message();

        if ($this->form_validation->run() == false) {
            if ($this->format == 'html') {
                $this->set_add_form_data();
                $this->render_format();
            } else {
                echo json_encode(array('result' => 'error', 'message' => validation_errors()));
            }
        } else {
            $this->load->model($this->model);
            $data = $this->set_insert_data($this->input->post(null, true));

            $this->load->model('Order');
            $data['order_id'] = $this->Order->insert($data);

            $this->load->model('OrderProduct');
            $data['order_product_id'] = $this->OrderProduct->insert(array('order_id' => $data['order_id'], 'product_id' => $data['product_id'], 'total_price' => $data['price'], 'quantity' => $data['insert_quantity']));

            if (!empty($data['dc_rate']) or !empty($data['dc_price'])) {
                $data['total_dc_price'] = 0;
                if (!empty($data['dc_rate'])) {
                    $data['total_dc_price'] = $data['original_price'] * ($data['dc_rate'] / 100);
                }

                if (!empty($data['dc_price'])) {
                    $data['total_dc_price'] += $data['dc_price'];
                }

                $this->load->model('OrderDiscount');
                $this->OrderDiscount->insert($data);
            }

            if ($id = $this->{$this->model}->insert($data)) {
                $this->after_insert_data($id, $data);

                if ($this->format == 'json') {
                    echo json_encode(array('result' => 'success', 'inserted_id' => $id));
                } else {
                    $this->session->set_flashdata('message', array('type' => 'success', 'message' => $this->insert_complete_message($id)));
                    redirect($this->add_redirect_path($id));
                }
            } else {
                if ($this->format == 'json') {
                    echo json_encode(array('result' => 'error', 'message' => _('Insert Fail')));
                } else {
                    $this->session->set_flashdata('message', array('type' => 'danger', 'message' => _('Insert Fail')));
                    redirect($this->router->fetch_class().'/add');
                }
            }
        }
    }

    protected function render_index_resource()
    {
        $this->layout->add_js('orders/order-extend.js'.'?version='.$this->assets_version);

        if ($this->script) {
            $this->return_data['script'] = $this->config->item('js_file_path').$this->script.'?version='.$this->return_data['common_data']['assets_version'];
            $this->layout->add_js($this->script.'?version='.$this->assets_version);
        }
    }

    protected function set_update_data($id, $data)
    {
        $data = $this->set_insert_data($data);
        $data['id'] = $id;

        return $data;
    }

    public function edit($id = null)
    {
        $this->load->library('form_validation');
        $this->set_form_validation($id);
        $this->set_message();

        $content = $this->get_view_content_data($id);

        if ($this->form_validation->run() == false) {
            if ($this->format == 'html') {
                $this->set_edit_form_data($content);
                $this->render_format();
            } else {
                echo json_encode(array('result' => 'error', 'message' => validation_errors()));
            }
        } else {
            $this->load->model($this->model);
            $data = $this->set_update_data($id, $this->input->post(null, true));

            $order_data = $data;
            $order_data['paymnet'] = $order_data['price'];
            $order_data['id'] = $content['order_id'];
            $order_data['order_id'] = $content['order_id'];
            $order_data['user_id'] = $content['user_id'];

            $this->load->model('Order');
            $this->Order->update($order_data);

            if (!empty($content['op_id'])) {
                $this->load->model('OrderProduct');
                $this->OrderProduct->update(array('product_id' => $data['product_id'], 'total_price' => $data['price'], 'quantity' => $data['insert_quantity'], 'id' => $content['op_id']));
            }

            $this->load->model('OrderDiscount');
            if (!empty($data['dc_rate']) or !empty($data['dc_price'])) {
                $order_data['total_dc_price'] = 0;
                if (!empty($data['dc_rate'])) {
                    $order_data['total_dc_price'] = $data['original_price'] - ($data['original_price'] * ($data['dc_rate'] / 100));
                }

                if (!empty($data['dc_price'])) {
                    $order_data['total_dc_price'] - $data['dc_price'];
                }

                $this->OrderDiscount->insert($order_data);
            } else {
                if ($this->OrderDiscount->get_count_by_parent_id($order_data['order_id'])) {
                    $this->OrderDiscount->delete_by_parent_id($order_data['order_id']);
                }
            }

            if ($this->{$this->model}->update($data)) {
                $data['order_id'] = $content['order_id'];
                $data['edit_content'] = $content;

                $this->after_update_data($id, $data);

                if ($this->format == 'json') {
                    echo json_encode(array('result' => 'success'));
                } else {
                    $this->session->set_flashdata('message', array('type' => 'success', 'message' => $this->update_complete_message($id)));
                    redirect($this->edit_redirect_path($id));
                }
            } else {
                if ($this->format == 'json') {
                    echo json_encode(array('result' => 'error', 'message' => _('Update Fail')));
                } else {
                    $this->session->set_flashdata('message', array('type' => 'danger', 'message' => _('Update Fail')));
                    redirect($this->router->fetch_class().'/edit/'.$id);
                }
            }
        }
    }

    public function sync_access($id)
    {
        $this->sync_access_data($this->get_view_content_data($id));
    }

    protected function sync_access_data(array $content)
    {
        $this->load->model('AccessController');
        $ac_count = $this->AccessController->get_count();

        if (empty($ac_count)) {
            return true;
        }

        $date_today = new DateTime('now', $this->timezone);
        $interval = $date_today->diff(new DateTime($content['start_date'], $this->timezone));

        $this->load->model('OrderAccessControllSchedule');
        if ($interval->format('%R') == '+') {
            $this->OrderAccessControllSchedule->insert(array('order_id' => $content['order_id'], 'schedule_date' => $content['start_date']));
        } else {
            if ($this->OrderAccessControllSchedule->get_count_by_parent_id($content['order_id'])) {
                $this->OrderAccessControllSchedule->delete_by_parent_id($content['order_id']);
            }

            $this->sync_access_controller($content['user_id']);
        }
    }

    protected function after_update_data($id, $data)
    {
        parent::after_update_data($id, $data);
        $this->order_account_update($data);

        $content = $this->get_view_content_data($id);
        return $content;
    }

    protected function order_auto_extend($order_id, $use_auto_extend = null)
    {
        $this->load->model('OrderAutoExtendException');
        if (empty($use_auto_extend)) {
            $this->OrderAutoExtendException->insert(array('order_id' => $order_id));
        } else {
            if ($this->OrderAutoExtendException->get_count_by_parent_id($order_id)) {
                $this->OrderAutoExtendException->delete_by_parent_id($order_id);
            }
        }
    }

    protected function order_account_update(array $data)
    {
        if (empty($data['order_id'])) {
            throw new exception('Order ID empty');
        }

        $this->load->model('Account');
        $first_account = $this->Account->get_content_by_category_id($this->add_id, $data['order_id']);

        if (!empty($first_account)) {
            if ($first_account['transaction_date'] != $data['transaction_date']) {
                $this->Account->update(array('transaction_date' => $data['transaction_date'], 'id' => $first_account['id']));

                $account_list = $this->Account->get_list_by_order_id($first_account['order_id']);

                if ($account_list['total']) {
                    foreach ($account_list['list'] as $list) {
                        if ($list['transaction_date'] < $data['transaction_date']) {
                            $this->Account->update(array('transaction_date' => $data['transaction_date'], 'id' => $list['id']));
                        }
                    }
                }
            }
        }

        $a_content = $this->Account->get_content_by_order_id($data['order_id']);


        if (!empty($a_content)) {
            $plus_data = array('amount' => 0);
            $minus_data = array('amount' => 0);
            
            if (is_numeric($data['cash'])) {
                $r_cash = $a_content['cash'] - $data['cash'];
                if (abs($r_cash)) {
                    if ($r_cash < 0) {
                        $plus_data['cash'] = abs($r_cash);
                        $plus_data['amount'] += abs($r_cash);
                    } else {
                        $minus_data['cash'] = abs($r_cash);
                        $minus_data['amount'] += abs($r_cash);
                    }
                }
            }

            if (is_numeric($data['credit'])) {
                $r_credit = $a_content['credit'] - $data['credit'];
                if (abs($r_credit)) {
                    if ($r_credit < 0) {
                        $plus_data['credit'] = abs($r_credit);
                        $plus_data['amount'] += abs($r_credit);
                    } else {
                        $minus_data['credit'] = abs($r_credit);
                        $minus_data['amount'] += abs($r_credit);
                    }
                }
            }

            if (!empty($plus_data['amount']) or !empty($minus_data['amount'])) {
                $u_cash=0;
                $u_credit=0;

                if(!empty($plus_data['amount']) and !empty($plus_data['cash'])) {
                    $u_cash=$a_content['cash']+$plus_data['cash'];
                }

                if(!empty($minus_data['amount']) and !empty($minus_data['cash'])) {
                    $u_cash=$a_content['cash']-$minus_data['cash'];
                }
                
                if(!empty($plus_data['amount']) and !empty($plus_data['credit'])) {
                    $u_credit=$a_content['credit']+$plus_data['credit'];
                }

                if(!empty($minus_data['amount']) and !empty($minus_data['credit'])) {
                    $u_credit=$a_content['credit']-$minus_data['credit'];
                }

                $this->Account->update(array('cash'=>$u_cash,'credit'=>$u_credit,'id'=> $a_content['id']));
            }
        }

        if($this->session->userdata('role_id')!=1) {
            if (!empty($data['change'])) {
            $data['change']['order_id'] = $data['order_id'];

            $this->load->model('OrderEditLog');
            $this->OrderEditLog->order_id = $data['order_id'];
            $revision_count = $this->OrderEditLog->get_count();
            $data['change']['revision'] = $revision_count + 1;
            $order_edit_log_id = $this->OrderEditLog->insert($data['change']);

            $this->load->model('OrderEditLogField');
            if (count($data['change']['field'])) {
                foreach ($data['change']['field'] as $value) {
                    $value['order_edit_log_id'] = $order_edit_log_id;
                    $this->OrderEditLogField->insert($value);
                }
            }
            }
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
                    $redirect = '/home/'.str_replace('_', '-', $this->router->fetch_class()).'/'.$id;
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
            $this->default_view_directory = 'orders';
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
                $data['product_id'] = $content['product_id'];
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
                 //   $this->Account->delete($content['accid']);
                }

                $this->sync_access_controller($content['user_id']);

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

    protected function set_order_insert_data($p_orders)
    {
        $this->load->model('Product');

        $orders = array();
        $t_total_price = 0;

        $t_total_cash = 0;
        $t_total_credit = 0;

        foreach ($p_orders['list'] as $index => $order) {
            if (empty($order['check'])) {
                continue;
            }

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
            $orders['order_product'][$index]['cash'] = 0;
            $orders['order_product'][$index]['credit'] = 0;
            $orders['order_product'][$index]['type'] = $order['type'];

            if (isset($order['cash'])) {
                $cash = filter_var($order['cash'], FILTER_SANITIZE_NUMBER_INT);
                
                if(!empty($cash)) {
                    $orders['order_product'][$index]['cash'] = $cash;
                    $t_total_cash += $cash;
                }
            }

            if (isset($order['credit'])) {
                $credit = filter_var($order['credit'], FILTER_SANITIZE_NUMBER_INT);
                
                if(!empty($credit)) {
                    $orders['order_product'][$index]['credit'] = $credit;
                    $t_total_credit += $credit;
                }
            }
        }

        $orders['order']['user_id'] = $p_orders['user_id'];
        $orders['order']['cash'] = $t_total_cash;
        $orders['order']['credit'] = $t_total_credit;
        $orders['order']['price'] = $t_total_cash + $t_total_credit;
        $orders['order']['original_price'] = $orders['order']['price'];
        $orders['order']['payment'] = $orders['order']['price'];

        if (empty($p_orders['transaction_date'])) {
            $orders['order']['transaction_date'] = $this->today;
        } else {
            $orders['order']['transaction_date'] = $p_orders['transaction_date'];
        }

        return $orders;
    }

    protected function delete_redirect_path(array $content)
    {
        if ($this->input->post('return_url')) {
            return $this->input->post('return_url');
        } else {
            return $_SERVER['HTTP_REFERER'];
        }
    }

    public function valid_auto_extend($id, $user_id)
    {
        $this->load->model($this->product_model);
        $product_c = $this->{$this->product_model}->get_content($id);

        if (empty($product_c['auto_extend_type'])) {
            return true;
        }

        $this->load->model($this->model);
        $this->{$this->model}->product_id = $product_c['product_id'];
        $this->{$this->model}->user_id = $user_id;
        $this->{$this->model}->get_not_end_only = true;

        if ($this->{$this->model}->get_count()) {
            $orders = $this->{$this->model}->get_index(1, 0);
            $order = $orders['list'][0];
            $this->form_validation->set_message('valid_auto_extend', sprintf(_('User(%s) Already Exists Product %s, Use Period(%s ~ %s)'), $order['user_name'], $order['product_name'], date_format(new DateTime($order['start_date'], $this->timezone), 'Y'._('Year').' n'._('Month').' j'._('Day')), date_format(new DateTime($order['end_date'], $this->timezone), 'Y'._('Year').' n'._('Month').' j'._('Day'))));

            return false;
        }

        return true;
    }

    public function add_auto_extend($id)
    {
        $content = $this->get_view_content_data($id);

        $this->load->library('form_validation');
        $this->form_validation->set_rules('order_id', _('Order'), 'required|integer');
        //$this->set_message();

        if ($this->form_validation->run() == false) {
            if ($this->format == 'html') {
                $this->return_data['data']['content'] = $content;

                $this->default_view_directory = 'orders';
                $this->render_format();
            } else {
                echo json_encode(array('result' => 'error', 'message' => validation_errors()));
            }
        } else {
            if (empty($content['auto_extend_type'])) {
                throw new Exception('Something wrong');
            }

            $this->load->model('OrderAutoExtendException');
            if (!$this->OrderAutoExtendException->get_count_by_parent_id($content['order_id'])) {
                throw new Exception('Something wrong');
            }

            if ($this->OrderAutoExtendException->delete_by_parent_id($content['order_id'])) {
                if ($this->format == 'json') {
                    echo json_encode(array('result' => 'success', 'message' => $this->add_auto_extend_message($id), 'redirect_path' => $this->add_auto_extend_redirect_path($content)));
                } else {
                    $this->session->set_flashdata('message', array('type' => 'success', 'message' => $this->add_auto_extend_message($id)));
                    redirect($this->add_auto_extend_redirect_path($content));
                }
            } else {
                if ($this->format == 'json') {
                    echo json_encode(array('result' => 'error', 'message' => _('Add Fail')));
                } else {
                    $this->session->set_flashdata('message', array('type' => 'danger', 'message' => _('Add Fail')));
                    redirect($this->add_auto_extend_redirect_path($content));
                }
            }
        }
    }

    public function end($id)
    {
        $content = $this->get_view_content_data($id);
        $this->set_end_form_validation($content);

        if ($this->form_validation->run() == false) {
            if ($this->format == 'html') {
                $this->return_data['data']['content'] = $content;
                $this->script = 'orders/end.js';

                $this->render_format();
            } else {
                echo json_encode(array('result' => 'error', 'message' => validation_errors()));
            }
        } else {
            $data = $this->set_end_insert_data($this->input->post(null, true));

            if(empty($data['origin_end_date'])) {
                $content['origin_end_date']=null;
            } else {
                $content['origin_end_date']=$data['origin_end_date'];
            }

            $result = true;

            if (empty($data['not_change_end_day'])) {
                if (!empty($data['end_date'])) {
                    $result = $this->{$this->model}->update(array('id' => $content['id'], 'end_date' => $data['end_date']));
                }
            } else {
                $result = $this->{$this->model}->update(array('id' => $content['id'], 'end_date' => $this->today));
            }

            $this->load->model('OrderEnd');
            $this->OrderEnd->insert(array('order_id' => $content['order_id'],'origin_end_date'=>$content['origin_end_date']));

            $data['type'] = 'O';
            $data['user_id'] = $content['user_id'];
            $data['order_id'] = $content['order_id'];
            $data['product_id'] = $content['product_id'];
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
                $this->Account->insert($data);
            }

            $this->sync_access_data($content);

            if ($result) {
                if ($this->format == 'json') {
                    echo json_encode(array('result' => 'success', 'message' => $this->end_complete_message($content), 'redirect_path' => $this->end_complete_message($content)));
                } else {
                    $this->session->set_flashdata('message', array('type' => 'success', 'message' => $this->end_complete_message($id)));
                    redirect($this->end_redirect_path($content));
                }
            } else {
                if ($this->format == 'json') {
                    echo json_encode(array('result' => 'error', 'message' => _('End Fail')));
                } else {
                    $this->session->set_flashdata('message', array('type' => 'danger', 'message' => _('End Fail')));
                    redirect($this->end_redirect_path($content));
                }
            }
        }
    }

    protected function set_end_insert_data($data)
    {
        if (!empty($data['end_date'])) {
            if (!empty($data['end_now'])) {
                $data['end_date'] = $this->today;
            } else {
                $data['end_date'] = $data['end_date'];
            }
        }

        return $data;
    }

    protected function set_form_validation($id = null)
    {
        $this->form_validation->set_rules('original_price', _('Original Price'), 'required|integer');
        $this->form_validation->set_rules('price', _('Sell Price'), 'required|integer');
        $this->form_validation->set_rules('dc_rate', _('DC Rate'), 'integer');
        $this->form_validation->set_rules('dc_price', _('DC Price'), 'integer');
        $this->form_validation->set_rules('cash', _('Cash'), 'integer');
        $this->form_validation->set_rules('credit', _('Credit'), 'integer');

        if($this->input->post('payment_method')==4) {
            $this->form_validation->set_rules('mix_credit', _('Credit'), 'integer');
            $this->form_validation->set_rules('mix_cash', _('Cash'), 'integer');

            //if($this->input->post('mix_credit')+$this->input->post('mix_cash')!=)
        }
    }

    protected function set_end_form_validation(array $content)
    {
        $this->load->library('form_validation');

        if (new DateTime($content['start_date'], $this->timezone) <= new DateTime($this->today, $this->timezone)) {            
            $this->form_validation->set_rules('end_date', _('Change End Date'), 'callback_valid_date|callback_valid_date_after['.$content['start_date'].']');
        } else {
            $this->form_validation->set_rules('end_date', _('Change End Date'), 'callback_valid_date');
        }
        $this->set_message();
    }

    protected function end_redirect_path(array $content)
    {
        return $_SERVER['HTTP_REFERER'];
    }

    protected function end_complete_message($id)
    {
        return _('Successfully End Order');
    }

    protected function add_auto_extend_redirect_path(array $content)
    {
        return $_SERVER['HTTP_REFERER'];
    }

    protected function add_auto_extend_message($id)
    {
        return _('Successfully Add Order Auto Extended');
    }
}

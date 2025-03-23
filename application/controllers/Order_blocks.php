<?php

defined('BASEPATH') or exit('No direct script access allowed');
require_once 'SL.php';
require_once 'Search_period.php';

class Order_Blocks extends SL_Controller
{
    use Search_period;

    protected $model = 'OrderBlock';
    protected $permission_controller = 'enrolls';
    protected $script = 'order-blocks/add.js';

    protected function set_add_form_data()
    {
        $this->load->model('productCategory');
        $this->productCategory->type = 'course';
        $this->return_data['search_data']['course_category'] = $this->productCategory->get_index(100, 0);

        $this->load->model('Course');
        $this->Course->status = 1;
        $this->Course->lesson_type=1;
        $this->return_data['search_data']['course'] = $this->Course->get_index(100, 0);

        $this->return_data['data']['user'] = $this->get_order_block_user($this->input->get_post('user'));
        $this->return_data['data']['search_count'] = $this->get_search_user(true);
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
                echo json_encode(['result' => 'error', 'message' => validation_errors()]);
            }
        } else {
            $data = $this->set_insert_data($this->input->post(null, true));

            $user_list = $this->get_search_user();

            $this->pdo = $this->load->database('pdo', true);
            $this->load->model('Order');
            $this->load->model('Course');
            $this->load->model('OrderProduct');
            $this->load->model('Enroll');
            $this->load->model('Rent');
            $this->load->model('RentSw');
            $this->load->model('Account');
            $this->load->model('UserContent');

            $reference_date=$data['reference_date'];
            $enroll_product_id=$data['product_id'];
            $plus_date=$data['period'];
            $transaction_date=$data['transaction_date'];

            $course=$this->Course->get_content_by_product_id($enroll_product_id);
            $course_id=$course['id'];
            $user_count=0;

            foreach($user_list['list'] as $value) {
               if($this->input->post('not_user')) {
                    if(in_array($value['id'],$this->input->post('not_user'))) {
                        continue;
                    }
               }

               $user_id=$value['id'];

                $us_stop_query=$this->pdo->query('SELECT COUNT(*) AS count FROM user_stops AS us WHERE us.enable=1 AND us.stop_start_date<=? AND us.stop_end_date>=? AND us.user_id=?',array($reference_date,$reference_date,$user_id));
                $user_stop_count=$us_stop_query->row_array(0);

                if(!empty($user_stop_count['count'])) {
                    continue;
                }

                $count_enroll_query=$this->pdo->query('SELECT COUNT(*) AS count FROM orders AS o INNER JOIN enrolls as e ON e.order_id=o.id INNER JOIN courses as c ON e.course_id=c.id LEFT JOIN order_ends as oe ON oe.order_id=o.id WHERE c.lesson_type=1 AND oe.id is NULL AND o.enable=1 AND o.user_id=?',array($user_id));
                $count_enroll=$count_enroll_query->row_array(0);

                $count_rent_query=$this->pdo->query('SELECT COUNT(*) AS count FROM orders AS o INNER JOIN rents as r ON r.order_id=o.id LEFT JOIN order_ends as oe ON oe.order_id=o.id WHERE DATE(r.start_datetime)<=? AND DATE(r.end_datetime)>=? AND oe.id is NULL AND o.enable=1 AND o.user_id=?',array($reference_date,$reference_date,$user_id));
                $count_rent=$count_rent_query->row_array(0);

                $count_rsw_query=$this->pdo->query('SELECT COUNT(*) AS count FROM orders AS o INNER JOIN rent_sws as rs ON rs.order_id=o.id LEFT JOIN order_ends as oe ON oe.order_id=o.id WHERE rs.start_date<=? AND rs.end_date>=? AND oe.id is NULL AND o.enable=1 AND o.user_id=?',array($reference_date,$reference_date,$user_id));
                $count_rsw=$count_rsw_query->row_array(0);

                if($count_enroll['count']) {
                    $enroll_query=$this->pdo->query('SELECT e.id,e.order_id,e.start_date,e.end_date,o.user_id FROM orders AS o INNER JOIN enrolls as e ON e.order_id=o.id INNER JOIN courses as c ON e.course_id=c.id LEFT JOIN order_ends as oe ON oe.order_id=o.id WHERE c.lesson_type=1 AND o.enable=1 AND oe.id is NULL AND o.user_id=? ORDER BY e.end_date DESC LIMIT 1',array($user_id));
                    $enroll=$enroll_query->row_array(0);


                    $dateObj=new DateTime($enroll['end_date'],$this->timezone);
                    $dateObj->modify('+1 day');
        
                    $start_date=$dateObj->format('Y-m-d');
        
                    $modify_text='+'.($plus_date-1).' days';
        
                    $dateObj->modify($modify_text);
                    $end_date=$dateObj->format('Y-m-d');


                    $order_id = $this->Order->insert(array('user_id'=>$user_id,'transaction_date'=>$transaction_date));
                    $this->OrderProduct->insert(array('order_id' => $order_id, 'product_id' => $enroll_product_id));
                    $this->Enroll->insert(array('user_id'=>$value['id'],'order_id'=>$order_id,'course_id'=>$course_id, 'start_date'=>$start_date, 'end_date'=>$end_date, 'quantity'=>$plus_date, 'insert_quantity'=>$plus_date, 'type'=>'day','have_datetime'=>$this->now));
                    $this->Account->insert(array('account_category_id' => ADD_ENROLL, 'user_id' => $user_id, 'order_id' => $order_id, 'product_id' => $enroll_product_id,'transaction_date'=>$transaction_date));
                }
                
                if($count_enroll['count'] and $count_rent['count']) {
                    $rent_query=$this->pdo->query('SELECT r.id,r.order_id,DATE(r.start_datetime) as start_date,DATE(r.end_datetime) as end_date,r.facility_id,o.user_id,op.product_id FROM orders AS o INNER JOIN rents as r ON r.order_id=o.id INNER JOIN order_products as op ON op.order_id=o.id LEFT JOIN order_ends as oe ON oe.order_id=o.id WHERE oe.id is NULL AND o.enable=1 AND o.user_id=? ORDER BY DATE(r.end_datetime) DESC LIMIT 1',array($user_id));
                    $rent=$rent_query->row_array(0);

                    $rent_product_id=$rent['product_id'];
                    $facility_id=$rent['facility_id'];
                    $no=$rent['no'];

                    $dateObj=new DateTime($rent['end_date'],$this->timezone);
                    $dateObj->modify('+1 day');
        
                    $start_date=$dateObj->format('Y-m-d');
        
                    $modify_text='+'.($plus_date-1).' days';
        
                    $dateObj->modify($modify_text);
                    $end_date=$dateObj->format('Y-m-d');

                    $start_datetime=$start_date.' 00:00:01';
                    $end_datetime=$end_date.' 23:59:59';


                    $order_id = $this->Order->insert(array('user_id'=>$user_id,'transaction_date'=>$transaction_date));
                    $this->OrderProduct->insert(array('order_id' => $order_id, 'product_id' => $rent_product_id));
                    $this->Rent->insert(array('user_id'=>$user_id,'order_id'=>$order_id, 'facility_id'=>$facility_id, 'no'=>$no, 'start_datetime'=>$start_datetime, 'end_datetime'=>$end_datetime));
                    $this->Account->insert(array('account_category_id' => ADD_RENT, 'user_id' => $user_id, 'order_id' => $order_id, 'product_id' => $rent_product_id,'transaction_date'=>$transaction_date));
                }

                if($count_enroll['count'] and $count_rsw['count']) {
                    $rent_rsw_query=$this->pdo->query('SELECT rs.id,rs.order_id,rs.start_date,rs.end_date,o.user_id,op.product_id FROM orders AS o INNER JOIN rent_sws as rs ON rs.order_id=o.id INNER JOIN order_products as op ON op.order_id=o.id LEFT JOIN order_ends as oe ON oe.order_id=o.id WHERE o.enable=1 AND oe.id is NULL AND o.user_id=? ORDER BY rs.end_date DESC LIMIT 1',array($user_id));
                    $rent_rsw=$rent_rsw_query->row_array(0);

                    $rent_rsw_product_id=$rent_rsw['product_id'];


                    $dateObj=new DateTime($rent_rsw['end_date'],$this->timezone);
                    $dateObj->modify('+1 day');
        
                    $start_date=$dateObj->format('Y-m-d');
        
                    $modify_text='+'.($plus_date-1).' days';
        
                    $dateObj->modify($modify_text);
                    $end_date=$dateObj->format('Y-m-d');

                    $order_id = $this->Order->insert(array('user_id'=>$user_id,'transaction_date'=>$transaction_date));
                    $this->OrderProduct->insert(array('order_id' => $order_id, 'product_id' => $rent_rsw_product_id));
                    $this->RentSw->insert(array('user_id'=>$user_id,'order_id'=>$order_id,'start_date'=>$start_date, 'end_date'=>$end_date));
                    $this->Account->insert(array('account_category_id' => ADD_RENT, 'user_id' => $user_id, 'order_id' => $order_id, 'product_id' => $rent_rsw_product_id,'transaction_date'=>$transaction_date));
                }
                
                if($count_enroll['count'] or $count_rent['count'] or $count_rsw['count']) {
                    $user_count++;
                    if (!empty($data['memo'])) {
                        $this->UserContent->insert(array('user_id'=>$value['id'],'content'=>$data['memo']));
                    }
                }
            }


            $data['user_count']=$user_count;

            $this->load->model($this->model);
            if ($id = $this->{$this->model}->insert($data)) {
                $this->after_insert_data($id, $data);

                if ($this->format == 'json') {
                    echo json_encode(['result' => 'success', 'inserted_id' => $id, 'message' => $this->insert_complete_message($id), 'redirect_path' => $this->add_redirect_path($id)]);
                } else {
                    $this->session->set_flashdata('message', ['type' => 'success', 'message' => $this->insert_complete_message($id)]);
                    redirect($this->add_redirect_path($id));
                }
            } else {
                if ($this->format == 'json') {
                    echo json_encode(['result' => 'error', 'message' => _('Insert Fail')]);
                } else {
                    $this->session->set_flashdata('message', ['type' => 'danger', 'message' => _('Insert Fail')]);
                    redirect($this->router->fetch_class() . '/add');
                }
            }
        }
    }

    protected function set_edit_form_data(array $content)
    {
        $this->index_data();
        $this->return_data['data']['content'] = $content;
    }

    protected function set_form_validation($id = null)
    {
        $this->form_validation->set_rules('product_id', _('Product'), 'required|integer');
        $this->form_validation->set_rules('custom_transaction_date', _('Transaction Date'), 'callback_valid_date');
        $this->form_validation->set_rules('reference_date', _('Reference Date'), 'callback_valid_date');
        $this->form_validation->set_rules('memo', _('Memo'), 'trim');
    }

    protected function get_order_block_user($user)
    {
        $list = array('total' => 0);

        if (empty($user)) {
            return $list;
        }

        if (!is_array($user)) {
            return $list;
        }

        $this->load->model('User');
        $this->User->user_id = $user;
        $this->User->all = true;
        return $this->User->get_index(10000, 0);
    }

    protected function set_insert_data($data)
    {
        if (empty($data['transaction_date'])) {
            if (empty($data['transaction_date_is_today']) and !empty($data['custom_transaction_date'])) {
                $data['transaction_date']  = $data['custom_transaction_date'];
            } else {
                $data['transaction_date']  = $this->today;
            }
        }
        
        unset($data['custom_transaction_date']);

        return $data;
    }

    protected function get_search_user($count = false)
    {
        $this->load->model('Search');

        $m_search = $this->input->get(null,true);
        $this->Search->search = $m_search;

        if ($this->input->get('trainer')) {
            $trainer_id = $this->input->get('trainer');
        }

        if ($this->input->get('fc')) {
            $fc_id = $this->input->get('fc');
        }

        if (!empty($trainer_id)) {
            $this->Search->trainer_id = $trainer_id;
        }

        if (!empty($fc_id)) {
            $this->Search->fc_id = $fc_id;
        }

        $this->set_search('Search');

        $this->load->model('ProductRelation');
        $this->ProductRelation->product_relation_type_id=PRIMARY_COURSE_ID;
        $product_relations = $this->ProductRelation->get_index();        


        $product_id = array();

        if ($this->input->get('product_id')) {
            foreach ($this->input->get('product_id') as $p_id) {
                if (empty($p_id)) {
                    continue;
                }

                if (in_array($p_id, ['all_rent', 'all_primary'])) {
                    if ($p_id == 'all_primary') {
                        if(!empty($product_relations['total'])) {
                            $all_primary = true;
                            foreach ($product_relations['list'] as $pr) {
                                $product_id[] = $pr['product_id'];
                            }
                        }
                    }
                } else {
                    $product_id[] = $p_id;
                }
            }
        }

        if (!empty($product_id)) {
            $this->Search->product_id = $product_id;
        }      

        $this->set_search('Enroll', 30, true);
        
        $this->Search->reference_date = $this->input->get('reference_date');


        if ($count) {
            return $this->Search->get_count();
        } else {
            return $this->Search->get_index(100000, 0);
        }
    }
}
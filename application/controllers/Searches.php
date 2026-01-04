<?php

defined('BASEPATH') or exit('No direct script access allowed');
require_once 'SL.php';
require_once 'Search_period.php';

class Searches extends SL_Controller
{
    use Search_period;

    protected $model = 'Search';
    protected $permission_controller = 'users';
    protected $script = 'searches/index.js';
    protected $phone_only=false;

    protected function index_data($category_id = null)
    {
        $this->set_page();
        $this->set_search_form_validation();
        
        $search = false;
        $is_pt_product = false;
        $all_primary = false;
        $all_rent = false;
        $er_type = 'all';  

        $this->load->model('productCategory');
        $this->productCategory->type = 'course';
        $this->return_data['search_data']['course_category'] = $this->productCategory->get_index(100, 0);

        $this->load->model('Course');
        $this->Course->status = 1;
        $courses = $this->Course->get_index(100, 0);

        $this->load->model('Facility');
        $facilities = $this->Facility->get_index(100, 0);

        $this->load->model('ProductRelation');
        $this->ProductRelation->product_relation_type_id=PRIMARY_COURSE_ID;
        $product_relations = $this->ProductRelation->get_index();        
        
        if ($this->form_validation->run() == false) {
            if($this->input->get()) {
                $data=array('total'=>0);
            } else {
                $this->load->model($this->model);
                
                $this->{$this->model}->search=false;
                $this->set_search($this->model, 30, true);
                $data = $this->{$this->model}->get_index($this->per_page, $this->page);          
            }
        } else {
            $this->load->helper('text');

            $product_id = array();
            if ($this->input->get('product_id')) {
                foreach ($this->input->get('product_id') as $p_id) {
                    if (empty($p_id)) {
                        continue;
                    }

                    if ($courses['total']) {
                        foreach ($courses['list'] as $course) {
                            if ($course['product_id'] == $p_id and $course['lesson_type'] == 4) {
                                $is_pt_product = true;
                            }
                        }
                    }

                    if (in_array($p_id, ['all_rent', 'all_primary'])) {
                        if ($p_id == 'all_rent') {
                            $all_rent = true;
                            foreach ($facilities['list'] as $facility) {
                                $product_id[] = $facility['product_id'];
                            }
                        }
                        
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

            if ($this->input->get('payment_id')) {
                $this->return_data['search_data']['payment_id'] = $this->input->get('payment_id');
            }

            if ($this->input->get('er_type')) {
                $er_type = $this->input->get('er_type');
            } else {
                if (!empty($product_id) and empty($all_primary)) {
                    if ($this->check_pt_product_include($product_id)) {
                        $er_type = 'pt';
                    }
                }
            }

            $this->load->model($this->model);

            $m_search = $this->input->get(null,true);
            $this->{$this->model}->search = $m_search;

            if (!empty($product_id)) {
                $this->{$this->model}->product_id = $product_id;
                $this->{$this->model}->all_primary = $all_primary;
                $this->{$this->model}->all_rent = $all_rent;
            }            

            if ($is_pt_product) {
                $this->{$this->model}->search_pt = true;
            }

            if($this->phone_only) {
                $this->{$this->model}->phone_only = true;
            }

            if($this->input->get('rent_option')=='empty_no') {
                $this->{$this->model}->empty_no=true;
            }

            if($this->input->get('rent_option')=='not_empty_no') {
                $this->{$this->model}->not_empty_no=true;
            }

            $this->set_search($this->model, 30, true);

            $reference_no_display = true;

            if($this->input->get('search_status') == '') {
                if(count($product_id)) {
                    $reference_no_display = false;
                }
            }      

            if (!$reference_no_display and $this->input->get('reference_date')) {
                $this->{$this->model}->reference_date = $this->input->get('reference_date');
            }
            
            $this->{$this->model}->search_type = $this->session->userdata('search_open');
            $data = $this->{$this->model}->get_index($this->per_page, $this->page);
            
            $search = true;
        }

        $this->return_data['search_data']['period_display_none'] = true;

        if ($this->input->get('search_type') != 'field') {
            if ($this->input->get('search_status')) {
                $this->return_data['search_data']['period_display_none'] = false;
            }
        }        

        $this->return_data['data'] = $data;
        $this->return_data['search_data']['er_type'] = $er_type;
        $this->return_data['search_data']['search'] = $search;
        $this->return_data['search_data']['course'] = $courses;
        $this->return_data['search_data']['facility'] = $facilities;
        $this->return_data['search_data']['product_relations'] = $product_relations;
        $this->return_data['data']['is_pt_product'] = $is_pt_product;

        if (!empty($product_id)) {
            $this->return_data['search_data']['product_id'] = $product_id;
        }

        $this->setting_pagination(['total_rows' => $data['total']]);
        $this->return_data['data']['per_page'] = $this->per_page;
        $this->return_data['data']['page'] = $this->page;
    }

    protected function check_pt_product_include(array $product_id)
    {
        $this->load->model('Course');
        $this->Course->lesson_type = 4;
        $pt_courses = $this->Course->get_index(100, 0);

        if (empty($pt_courses['total'])) {
            return false;
        }

        foreach ($pt_courses['list'] as $pt_course) {
            if (in_array($pt_course['product_id'], $product_id)) {
                return true;
            }
        }

        return false;
    }

    protected function set_search_form_validation()
    {
        $this->load->library('form_validation');
        $this->form_validation->set_data($this->input->get());
        $this->set_period_search_validation();
        $this->form_validation->set_rules('search_type', _('Search Type'), 'in_list[default,field]');
        $this->form_validation->set_rules('user_type', _('User Type'), 'in_list[all,default,free]');
        $this->form_validation->set_rules('product_id', _('Product'), 'integer');
        $this->form_validation->set_rules('product_category_id', _('Product Category'), 'integer');
        $this->form_validation->set_rules('fc', _('FC'), 'integer');
        $this->form_validation->set_rules('trainer', _('Trainer'), 'integer');
        $this->form_validation->set_rules('employee_name', _('Name'), 'trim');
        $this->form_validation->set_rules('rent_option', _('Rent Option'), 'in_list[,empty_no,not_empty_no]');
        $this->form_validation->set_rules('reference_date', _('Reference Date'), 'callback_valid_date');
        $this->form_validation->set_rules('search_status', ('Search Status'), 'in_list[all,status1,status2,status3,status4,status5,status6,status7,status8,status9,status10,status11,status12,status13]');
        $this->form_validation->set_rules('search_field', _('Search Field'), 'in_list[name,card_no,phone,birthday,visit_route,company]');
        if ($this->input->get('search_field') == 'birthday') {
            $this->form_validation->set_rules('start_birthday', _('Birthday'), 'callback_valid_date');
            $this->form_validation->set_rules('end_birthday', _('Birthday'), 'callback_valid_date');
        } else {
            $this->form_validation->set_rules('search_word', _('Search Word'), 'min_length[1]|trim|max_length[20]');
        }
        $this->set_message();
    }

    public function index_oc($type = null)
    {
        if (in_array($type, ['default', 'field'])) {
            $this->session->set_userdata('search_open', $type);
        } else {
            $this->session->unset_userdata('search_open');
        }
        echo json_encode(['result' => 'success']);
    }

    public function export_excel()
    {
        ini_set('memory_limit', '256M');
        ini_set('max_execution_time', '300');
        $this->per_page = 30000;
        $this->no_set_page = true;
        $this->index_data();
        $list = $this->return_data['data'];


        $spreadsheet = new PhpOffice\PhpSpreadsheet\Spreadsheet();
        $spreadsheet->getProperties()->setCreator('작성자')
            ->setLastModifiedBy('최종수정자')
            ->setTitle('자격증시험응시리스트')
            ->setSubject('자격증시험응시리스트')
            ->setDescription('자격증시험응시리스트')
            ->setKeywords('자격증 시험')
            ->setCategory('License');

        if ($this->return_data['search_data']['er_type'] == 'pt'):
            $spreadsheet->setActiveSheetIndex(0)
                ->setCellValue('A1', _('User Name'))
                ->setCellValue('B1', _('Gender'))
                ->setCellValue('C1', _('Phone'))
                ->setCellValue('D1', _('Transaction Date'))
                ->setCellValue('E1', _('Start Date'))
                ->setCellValue('F1', _('End Date'))
                ->setCellValue('G1', _('Quantity'))
                ->setCellValue('H1', _('Product'))
                ->setCellValue('I1', _('User Trainer'))
                ->setCellValue('J1', _('User FC'))
                ->setCellValue('K1', _('Total Quantity'))
                ->setCellValue('L1', _('Use Quantity'))
                ->setCellValue('M1', _('Remain Quantity'))
                ->setCellValue('N1', _('Payment'));
        else:
            $i1_title=_('User Trainer');

            if ($this->input->get('search_type') == 'field' and $this->input->get('search_field')) {
                if($this->input->get('search_field') == 'company') {
                    $i1_title=_('Company');
                }

                if($this->input->get('search_field') == 'visit_route') {
                    $i1_title=_('Visit Route');
                }
            }


        $exists_other=true;
        $product_id_exists=false;
        
        
        if (isset($_GET['product_id'][0])) {
            $product_id_exists=true;
        }
        
        if(!empty($product_id_exists)):
        if (!empty($list['total'])):
            foreach ($list['list'] as $index => $value):
                $c_list = explode(',', $value['insert_quantity']);
        
                $other=false;
                foreach ($c_list as $value) {
                    $f_value = explode('||', $value);
                    
                    if ($f_value[0] != 1) {
                        $other = true;
                    }
                }
        
                if(empty($other)) {
                    $exists_other=false;
                }
            endforeach;
        endif;
        endif;
        

        if($exists_other) {
            $spreadsheet->setActiveSheetIndex(0)
                ->setCellValue('A1', _('User Name'))
                ->setCellValue('B1', _('Gender'))
                ->setCellValue('C1', _('Phone'))
                ->setCellValue('D1', _('Transaction Date'))
                ->setCellValue('E1', _('Start Date'))
                ->setCellValue('F1', _('End Date'))
                ->setCellValue('G1', _('Quantity'))
                ->setCellValue('H1', _('Product'))
                ->setCellValue('I1', $i1_title)
                ->setCellValue('J1', _('User FC'))
                ->setCellValue('K1', _('Total Quantity') . '/' . _('Use Quantity') . '/' . _('Remain Quantity'))
                ->setCellValue('L1', _('Payment'));
        } else {
            $spreadsheet->setActiveSheetIndex(0)
                ->setCellValue('A1', _('User Name'))
                ->setCellValue('B1', _('Gender'))
                ->setCellValue('C1', _('Phone'))
                ->setCellValue('D1', _('Transaction Date'))
                ->setCellValue('E1', _('Start Date'))
                ->setCellValue('F1', _('End Date'))
                ->setCellValue('G1', _('Quantity'))
                ->setCellValue('H1', _('Total Period'))
                ->setCellValue('I1', _('Product'))
                ->setCellValue('K1', $i1_title)
                ->setCellValue('K1', _('User FC'))
                ->setCellValue('L1', _('Total Quantity') . '/' . _('Use Quantity') . '/' . _('Remain Quantity'))
                ->setCellValue('M1', _('Payment'));
        }


        endif;

        if ($list['total']) {
            $excel_date_format='Y' . _('Year') . ' m' . _('Month') . ' d' . _('Day');

            foreach ($list['list'] as $index => $value) {
                if (is_null($value['gender'])) {
                    $gender = '-';
                } else {
                    if ($value['gender'] == 1) {
                        $gender = _('Male');
                    }

                    if ($value['gender'] == 0) {
                        $gender = _('Female');
                    }
                }

                if (empty($value['product_name'])) {
                    $product_name = '-';
                } else {
                    $product_name = $value['product_name'];
                }

                if (empty($value['trainer'])) {
                    $trainer = '-';
                } else {
                    $trainer = $value['trainer'];
                }

                if (empty($value['fc'])) {
                    $fc = '-';
                } else {
                    $fc = $value['fc'];
                }

                if (empty($this->return_data['search_data']['er_type'])) {
                    $pt_count = '-';
                } else {
                    if (empty($value['quantity'])):
                        $pt_count = '-';
                        $pt_use_quantity = '-';
                        $pt_left_quantity = '-';
                    else:
                        $pt_count = get_d_quantity_format_unique($value['quantity']);
                        $pt_use_quantity = get_d_quantity_format_unique($value['use_quantity']);
                        $pt_left_quantity = (get_d_quantity_format_unique($value['quantity']) - get_d_quantity_format_unique($value['use_quantity']));
                    endif;
                }

                if (empty($value['pay_total'])) {
                    $pay_total = '-';
                } else {
                    $pay_total = number_format($value['pay_total']) . _('Currency');
                }

                $period = get_d_insert_quantity_format($value['insert_quantity']);

                $start_date = $value['start_date'];
                $end_date = $value['end_date'];

                if (!empty($value['change_start_date'])) {
                    $start_date = $value['change_start_date'];
                }

                if (!empty($value['change_end_date'])) {
                    $end_date = $value['change_end_date'];
                }

                if ($this->return_data['search_data']['er_type'] == 'pt'):
                    $spreadsheet->setActiveSheetIndex(0)
                        ->setCellValue('A' . ($index + 2), $value['name'])
                        ->setCellValue('B' . ($index + 2), $gender)
                        ->setCellValue('C' . ($index + 2), get_hyphen_phone($value['phone']))
                        ->setCellValue('D' . ($index + 2), get_dt_format($value['transaction_date'], $this->timezone, $excel_date_format))
                        ->setCellValue('E' . ($index + 2), get_dt_format($start_date, $this->timezone, $excel_date_format))
                        ->setCellValue('F' . ($index + 2), get_dt_format($end_date, $this->timezone, $excel_date_format))
                        ->setCellValue('G' . ($index + 2), $period)
                        ->setCellValue('H' . ($index + 2), $product_name)
                        ->setCellValue('I' . ($index + 2), $trainer)
                        ->setCellValue('J' . ($index + 2), $fc)
                        ->setCellValue('K' . ($index + 2), $pt_count)
                        ->setCellValue('L' . ($index + 2), $pt_use_quantity)
                        ->setCellValue('M' . ($index + 2), $pt_left_quantity)
                        ->setCellValue('N' . ($index + 2), $pay_total);
                else:
                    if ($this->input->get('search_type') == 'field' and $this->input->get('search_field')) {
                        if($this->input->get('search_field') == 'company') {
                            $trainer=$value['company'];
                        }
        
                        if($this->input->get('search_field') == 'visit_route') {
                            $trainer=$value['visit_route'];
                        }
                    }

                    if($exists_other) {
                        
                    $spreadsheet->setActiveSheetIndex(0)
                        ->setCellValue('A' . ($index + 2), $value['name'])
                        ->setCellValue('B' . ($index + 2), $gender)
                        ->setCellValue('C' . ($index + 2), get_hyphen_phone($value['phone']))
                        ->setCellValue('D' . ($index + 2), get_dt_format($value['transaction_date'], $this->timezone,$excel_date_format))
                        ->setCellValue('E' . ($index + 2), get_dt_format($start_date, $this->timezone,$excel_date_format))
                        ->setCellValue('F' . ($index + 2), get_dt_format($end_date, $this->timezone,$excel_date_format))
                        ->setCellValue('G' . ($index + 2), $period)
                        ->setCellValue('H' . ($index + 2), $product_name)
                        ->setCellValue('I' . ($index + 2), $trainer)
                        ->setCellValue('J' . ($index + 2), $fc)
                        ->setCellValue('K' . ($index + 2), $pt_count)
                        ->setCellValue('L' . ($index + 2), $pay_total);
                    } else {
                        $total_period = get_d_insert_quantity_format($value['insert_quantity'],false);
                        $spreadsheet->setActiveSheetIndex(0)
                        ->setCellValue('A' . ($index + 2), $value['name'])
                        ->setCellValue('B' . ($index + 2), $gender)
                        ->setCellValue('C' . ($index + 2), get_hyphen_phone($value['phone']))
                        ->setCellValue('D' . ($index + 2), get_dt_format($value['transaction_date'], $this->timezone,$excel_date_format))
                        ->setCellValue('E' . ($index + 2), get_dt_format($start_date, $this->timezone,$excel_date_format))
                        ->setCellValue('F' . ($index + 2), get_dt_format($end_date, $this->timezone,$excel_date_format))
                        ->setCellValue('G' . ($index + 2), $period)
                        ->setCellValue('H' . ($index + 2), $total_period)
                        ->setCellValue('I' . ($index + 2), $product_name)
                        ->setCellValue('J' . ($index + 2), $trainer)
                        ->setCellValue('K' . ($index + 2), $fc)
                        ->setCellValue('L' . ($index + 2), $pt_count)
                        ->setCellValue('M' . ($index + 2), $pay_total);
                    }
                endif;
            }
        }

        $filename = iconv('UTF-8', 'EUC-KR', '회원목록');

        // Redirect output to a client’s web browser (Excel5)
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="' . $filename . '.xls"');
        header('Cache-Control: max-age=0');

        $objWriter = new PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        $objWriter->save('php://output');
    }
}

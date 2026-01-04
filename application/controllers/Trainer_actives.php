<?php

defined('BASEPATH') or exit('No direct script access allowed');
require_once 'SL.php';
require_once 'Search_period.php';

class Trainer_actives extends SL_Controller
{
    use Search_period;

    protected $model = 'TrainerActive';
    protected $permission_controller = 'enrolls';

    protected function permission_check()
    {
        if (empty($this->session->userdata('is_fc'))) {
            parent::permission_check();
        }
    }

    protected function index_data($category_id = null)
    {
        $this->set_page();
        $this->set_search_form_validation();
        $this->form_validation->set_rules('employee_name', _('Employee'), 'trim');
        $this->form_validation->set_rules('employee_id', _('Employee'), 'integer');
        $this->form_validation->set_rules('search_zero_commission_too', _('Search Zero Commission Too'), 'in_list[1]');

        $this->load->model($this->model);

        if($this->session->userdata('role_id')>5) {
            $this->{$this->model}->trainer_id = $this->session->userdata('admin_id');
        }

        $this->set_search();

        $trainer_link = true;
        $fc_link = true;

        if ($this->input->get('employee_id')) {
            $this->load->model('Employee');
            $this->{$this->model}->employee_id = $this->input->get('employee_id');
            $employee = $this->Employee->get_content($this->input->get('employee_id'));
        }

        if ($this->input->get('search_zero_commission_too')) {
            $this->{$this->model}->szct = true;
        } else {
            $this->{$this->model}->szct = false;
        }

        $list = $this->{$this->model}->get_index($this->per_page, $this->page);

        $this->return_data['data'] = $list;
        $this->return_data['data']['per_page'] = $this->per_page;
        $this->return_data['data']['page'] = $this->page;

        $this->setting_pagination(array('total_rows' => $list['total']));

        if (!empty($employee)) {
            $this->return_data['data']['content'] = $employee;
        }
        
        $this->return_data['data']['employee_total'] = $this->{$this->model}->get_total();
        $this->return_data['data']['display_trainer_link'] = $trainer_link;
        $this->return_data['data']['display_fc_link'] = $fc_link;

        $this->form_validation->run();
    }

    public function view($id = null)
    {
        if (empty($id)) {
            show_404();
        }

        $this->set_page();
        $this->set_search_form_validation();
        $this->form_validation->set_rules('employee_name', _('Employee'), 'trim');
        $this->form_validation->set_rules('employee_id', _('Employee'), 'integer');
        $this->form_validation->set_rules('search_zero_commission_too', _('Search Zero Commission Too'), 'in_list[1]');

        $this->load->model($this->model);

        if($this->session->userdata('role_id')>5) {            
            $this->{$this->model}->trainer_id = $this->session->userdata('admin_id');
        }
        
        $this->set_search();

        $type = 'trainer';
        $this->load->model('Employee');
        $employee = $this->Employee->get_content($id);

        $this->{$this->model}->employee_id = $id;

        if ($this->input->get('search_zero_commission_too')) {
            $this->{$this->model}->szct = true;
        } else {
            $this->{$this->model}->szct = false;
        }

        $this->return_data['data'] = $this->{$this->model}->get_view_index($id, $this->per_page, $this->page);
        $this->return_data['data']['per_page'] = $this->per_page;
        $this->return_data['data']['page'] = $this->page;

        $this->setting_pagination(array('total_rows' => $this->return_data['data']['total'], 'base_url' => base_url() . $this->router->fetch_class() . '/' . $this->router->fetch_method() . '/' . $id));

        $this->return_data['data']['employee_total'] = $this->{$this->model}->get_view_total($employee['id']);

        $this->return_data['data']['content'] = $employee;
        $this->return_data['data']['type'] = $type;

        $this->form_validation->run();

        $this->render_view_format();
    }

    protected function set_search_form_validation()
    {
        $this->load->library('form_validation');
        $this->form_validation->set_data($this->input->get());
        $this->set_period_search_validation();
    }

    public function export_excel($id = null)
    {
        if (empty($id)) {
            $this->per_page = 10000;
            $this->index_data();
            $spreadsheet = $this->export_index_excel();
        } else {
            $this->per_page = 10000;
            $this->load->model($this->model);
            $this->set_search();
            if ($this->input->get('search_zero_commission_too')) {
                $this->{$this->model}->szct = true;
            } else {
                $this->{$this->model}->szct = false;
            }

            $this->return_data['data'] = $this->{$this->model}->get_view_index($id, $this->per_page);
            $spreadsheet = $this->export_view_excel();
        }

        $filename = iconv('UTF-8', 'EUC-KR', '직원매출');

        // Redirect output to a client’s web browser (Excel5)
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="' . $filename . '.xls"');
        header('Cache-Control: max-age=0');

        $objWriter = new PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        $objWriter->save('php://output');
    }

    private function export_index_excel()
    {
        $spreadsheet = new PhpOffice\PhpSpreadsheet\Spreadsheet();
        $spreadsheet->getProperties()->setCreator('작성자')
            ->setLastModifiedBy('최종수정자')
            ->setTitle('자격증시험응시리스트')
            ->setSubject('자격증시험응시리스트')
            ->setDescription('자격증시험응시리스트')
            ->setKeywords('자격증 시험')
            ->setCategory('License');

            $spreadsheet->setActiveSheetIndex(0)
                ->setCellValue('A1', _('Employee'))
                ->setCellValue('B1', _('Charge User'))
                ->setCellValue('C1', _('Execute Order'))
                ->setCellValue('D1', _('Execute User'))
                ->setCellValue('E1', _('Period Use Quantity'))
                ->setCellValue('F1', _('Commission'));

            if ($this->return_data['data']['total']) {
                foreach ($this->return_data['data']['list'] as $index => $value) {
                    if(empty($value['count_order'])):
                        $excute_order=$value['count_order']._('Count');
                    else:
                        $excute_order=number_format($value['count_order'])._('Count');
                    endif;
                    
                    if(empty($value['execute_user'])):
                        $execute_user=$value['execute_user']._('Count People');
                    else:
                        $execute_user=number_format($value['execute_user'])._('Count People');
                    endif;

                   if(empty($value['period_use'])): 
                        $period_use='0'._('Count Time');
                    else:        
                        $period_use=$value['period_use']._('Count Time');
                    endif;

                    $spreadsheet->setActiveSheetIndex(0)
                        ->setCellValue('A' . ($index + 2), $value['name'])
                        ->setCellValue('B' . ($index + 2), number_format($value['charge_user'])._('Count People'))                        
                        ->setCellValue('C' . ($index + 2), $excute_order)
                        ->setCellValue('D' . ($index + 2), $execute_user)
                        ->setCellValue('E' . ($index + 2), $period_use)
                        ->setCellValue('F' . ($index + 2), number_format($value['commission']) . _('Currency'));
                }
            }

        return $spreadsheet;
    }

    private function export_view_excel()
    {
        $spreadsheet = new PhpOffice\PhpSpreadsheet\Spreadsheet();
        $spreadsheet->getProperties()->setCreator('작성자')
            ->setLastModifiedBy('최종수정자')
            ->setTitle('자격증시험응시리스트')
            ->setSubject('자격증시험응시리스트')
            ->setDescription('자격증시험응시리스트')
            ->setKeywords('자격증 시험')
            ->setCategory('License');

        // echo _('Access Card No');

        $spreadsheet->setActiveSheetIndex(0)
            ->setCellValue('A1', _('User'))
            ->setCellValue('B1', _('User FC'))
            ->setCellValue('C1', _('Registration Details'))
            ->setCellValue('D1', _('Period'))
            ->setCellValue('E1', _('Payment'))
            ->setCellValue('F1', _('Execute Count'))
            ->setCellValue('G1', _('Commission Per Once'))
            ->setCellValue('H1', _('Total Commission'));

        if ($this->return_data['data']['total']) {
            foreach ($this->return_data['data']['list'] as $index => $value) {
                if (!empty($value['fc_name'])) {
                    $fc_name = $value['fc_name'];
                } else {
                    $fc_name = '-';
                }

                if (empty($value['user_id'])) {
                    $user_name = _('Deleted User');
                } else {
                    $user_name = $value['name'] . '(' . get_card_no($value['card_no'], false) . ')';
                }

                $product_name = '';
                if (empty($value['product_name'])) {
                    $product_name .= $value['product_name'];
                } else {
                    if (!empty($value['product_category'])) {
                        $product_name .= $value['product_category'].' / ';
                    }
                    $product_name .= $value['product_name'];
                }

                if (!empty($value['start_date']) and !empty($value['end_date'])) {
                    if (date('Y', strtotime($value['end_date'])) > 2500) {
                        $period = _('Unlimit');
                    } else {
                        $period = $value['start_date'] . ' ~ ' . $value['end_date'];
                    }
                } else {
                    $period = '-';
                }

                if (empty($value['default_commission'])) {
                    if (empty($value['purchase'])) {
                        $default_commission='0'._('Currency');
                    } else {
                        $default_commission=number_format(($value['purchase']/$value['insert_quantity']) * ($value['commission_rate'] / 100))._('Currency');
                    }
                } else {
                    $default_commission=number_format($value['default_commission'])._('Currency');
                }

                $spreadsheet->setActiveSheetIndex(0)
                    ->setCellValue('A' . ($index + 2), $user_name)
                    ->setCellValue('B' . ($index + 2), $fc_name)
                    ->setCellValue('C' . ($index + 2), $product_name)
                    ->setCellValue('D' . ($index + 2), $period)
                    ->setCellValue('E' . ($index + 2), number_format($value['purchase'])._('Currency'))
                    ->setCellValue('F' . ($index + 2), $value['execute_count']._('Count Time'))
                    ->setCellValue('G' . ($index + 2), $default_commission)
                    ->setCellValue('H' . ($index + 2), number_format($value['commission']) . _('Currency'));
            }
        }

        return $spreadsheet;
    }
}

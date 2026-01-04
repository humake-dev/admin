<?php

defined('BASEPATH') or exit('No direct script access allowed');
require_once 'SL.php';
require_once 'Search_period.php';

class Account_employees extends SL_Controller
{
    use Search_period;

    protected $model = 'AccountEmployee';
    protected $use_index_content = true;
    protected $script = 'accounts/index.js';
    protected $permission_controller = 'accounts';

    protected function index_data($category_id = null)
    {
        $this->set_page();
        $this->set_search_form_validation();

        /*if ($this -> form_validation -> run() == false) {

        } else { */

        $this->load->model($this->model);
        $this->set_search();

        $type = 'trainer';
        $trainer_link = true;
        $fc_link = true;

        if ($this->input->get('type')) {
            $type = $this->input->get('type');
        }

        if ($this->input->get('employee_id')) {
            $this->load->model('Employee');
            $this->{$this->model}->employee_id = $this->input->get('employee_id');
            $employee = $this->Employee->get_content($this->input->get('employee_id'));

            if ($employee['is_fc'] and $employee['is_trainer']) {
            } else {
                if ($employee['is_trainer']) {
                    $fc_link = false;
                }

                if ($employee['is_fc']) {
                    $trainer_link = false;
                    $type = 'fc';
                }
            }
        }

        $this->{$this->model}->type = $type;
        $this->{$this->model}->no_commission = true;
        $this->{$this->model}->no_branch_transfer = true;

        $this->return_data['data'] = $this->{$this->model}->get_index($this->per_page, $this->page, 'id');
        $this->return_data['data']['per_page'] = $this->per_page;
        $this->return_data['data']['page'] = $this->page;

        $this->setting_pagination(array('total_rows' => $this->return_data['data']['total']));

        if (!empty($employee)) {
            $this->return_data['data']['content'] = $employee;
        }

        $this->return_data['data']['type'] = $type;
        $this->return_data['data']['display_trainer_link'] = $trainer_link;
        $this->return_data['data']['display_fc_link'] = $fc_link;
        $this->return_data['search_data']['period_display'] = true;
        //}
        $this->form_validation->run();
    }

    protected function get_view_data($id)
    {
        /*if($content=$this->get_view_data($id)) {
            $this -> return_data['data']=array('content'=>$content);
        } */

        $this->set_page();
        $this->set_search_form_validation();

        $this->load->model($this->model);
        $this->set_search();

        $this->load->model('Employee');
        $employee = $this->Employee->get_content($id);
        $type = 'trainer';

        if (empty($employee['is_trainer']) and !empty($employee['is_fc'])) {
            $type = 'fc';
        }

        $this->{$this->model}->employee_id = $id;
        $this->{$this->model}->type = $type;
        $this->{$this->model}->no_commission = true;
        $this->{$this->model}->no_branch_transfer = true;

        $this->return_data['data'] = $this->{$this->model}->get_view_index($id, $this->per_page, $this->page);
        $this->return_data['data']['per_page'] = $this->per_page;
        $this->return_data['data']['page'] = $this->page;

        $this->setting_pagination(array('total_rows' => $this->return_data['data']['total'], 'base_url' => base_url() . $this->router->fetch_class() . '/' . $this->router->fetch_method() . '/' . $id));

        $this->return_data['data']['employee_total'] = $this->{$this->model}->get_total($employee['id']);
        $this->return_data['data']['content'] = $employee;
        $this->return_data['data']['type'] = $type;

        $this->form_validation->run();
    }

    protected function set_search_form_validation()
    {
        $this->load->library('form_validation');
        $this->form_validation->set_data($this->input->get());
        $this->set_period_search_validation();
        $this->form_validation->set_rules('employee_name', _('Employee'), 'trim');
        $this->form_validation->set_rules('employee_id', _('Employee'), 'integer');
    }

    public function export_excel($id = null)
    {
        if (empty($id)) {
            $this->per_page = 10000;
            $this->index_data();
            $spreadsheet = $this->export_index_excel();
        } else {
            $this->per_page = 10000;
            $this->get_view_data($id);
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

        if ($this->input->get('type') == 'fc') {
            $spreadsheet->setActiveSheetIndex(0)
                ->setCellValue('A1', _('Employee'))
                ->setCellValue('B1', _('Total User'))
                ->setCellValue('C1', _('Sales'))
                ->setCellValue('D1', _('Income'))
                ->setCellValue('E1', _('Refund'));

            if ($this->return_data['data']['total']) {
                foreach ($this->return_data['data']['list'] as $index => $value) {
                    $spreadsheet->setActiveSheetIndex(0)
                        ->setCellValue('A' . ($index + 2), $value['name'])
                        ->setCellValue('B' . ($index + 2), $value['total_user'] . _('Count People'))
                        ->setCellValue('C' . ($index + 2), number_format($value['total_income'] - $value['total_refund']) . _('Currency'))
                        ->setCellValue('D' . ($index + 2), number_format($value['total_income']) . _('Currency'))
                        ->setCellValue('E' . ($index + 2), number_format($value['total_refund']) . _('Currency'));
                }
            }
        } else {
            $spreadsheet->setActiveSheetIndex(0)
                ->setCellValue('A1', _('Employee'))
                ->setCellValue('B1', _('User'))
                ->setCellValue('C1', _('Sales'))
                ->setCellValue('D1', _('Total Quantity'))
                ->setCellValue('E1', _('Total Use Quantity'))
                ->setCellValue('F1', _('Left Quantity'));

            if ($this->return_data['data']['total']) {
                foreach ($this->return_data['data']['list'] as $index => $value) {
                    if (empty($value['quantity'])) {
                        $quantity = '0' . _('Count Time');
                    } else {
                        $quantity = $value['quantity'] . _('Count Time');
                    }

                    if (empty($value['use_quantity'])) {
                        $use_quantity = '0' . _('Count Time');
                    } else {
                        $use_quantity = $value['use_quantity'] . _('Count Time');
                    }

                    if (empty($value['period_use'])) {
                        $period_use = '0' . _('Count Time');
                    } else {
                        $period_use = $value['period_use'] . _('Count Time');
                    }

                    if (empty($value['use_quantity'])) {
                        $left_quantity = $value['quantity'] . _('Count Time');
                    } else {
                        $left_quantity = ($value['quantity'] - $value['use_quantity']) . _('Count Time');
                    }

                    $spreadsheet->setActiveSheetIndex(0)
                        ->setCellValue('A' . ($index + 2), $value['name'])
                        ->setCellValue('B' . ($index + 2), $value['total_user'] . _('Count People'))
                        ->setCellValue('C' . ($index + 2), number_format($value['total_sales']) . _('Currency'))
                        ->setCellValue('D' . ($index + 2), $quantity)
                        ->setCellValue('E' . ($index + 2), $use_quantity)
                        ->setCellValue('F' . ($index + 2), $left_quantity);
                }
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

        $spreadsheet->setActiveSheetIndex(0)
            ->setCellValue('A1', _('User FC'))
            ->setCellValue('B1', _('User'))
            ->setCellValue('C1', '등록내역')
            ->setCellValue('D1', _('Total Quantity'))
            ->setCellValue('E1', _('Total Use Quantity'))
            ->setCellValue('F1', _('Period'))
            ->setCellValue('G1', _('Price'))
            ->setCellValue('H1', _('Payment'))
            ->setCellValue('I1', _('Commission'))
            ->setCellValue('J1', _('Transaction Date'));

        if ($this->return_data['data']['total']) {
            $excel_date_format='Y' . _('Year') . ' m' . _('Month') . ' d' . _('Day');

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
                    if ($value['product_category']) {
                        $product_name .= $value['product_category'] . ' / ';
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

                $account = number_format($value['cash'] + $value['credit']);

                $spreadsheet->setActiveSheetIndex(0)
                    ->setCellValue('A' . ($index + 2), $fc_name)
                    ->setCellValue('B' . ($index + 2), $user_name)
                    ->setCellValue('C' . ($index + 2), $product_name)
                    ->setCellValue('D' . ($index + 2), $value['quantity'])
                    ->setCellValue('E' . ($index + 2), $value['use_quantity'])
                    ->setCellValue('F' . ($index + 2), $period)
                    ->setCellValue('G' . ($index + 2), number_format($value['price']) . _('Currency'))
                    ->setCellValue('H' . ($index + 2), $account . _('Currency'))
                    ->setCellValue('I' . ($index + 2), number_format($value['commission']) . _('Currency'))
                    ->setCellValue('J' . ($index + 2), get_dt_format($value['transaction_date'], $this->timezone, $excel_date_format));
            }
        }

        return $spreadsheet;
    }
}

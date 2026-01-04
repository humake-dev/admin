<?php

defined('BASEPATH') or exit('No direct script access allowed');
require_once 'SL.php';
require_once 'Search_period.php';

class Search_ys extends SL_Controller
{
    use Search_period;

    protected $model = 'SearchYs';
    protected $permission_controller = 'users';
    protected $script = 'search-ys/index.js';

    protected function index_data($category_id = null)
    {
        $this->set_page();
        $this->set_search_form_validation();

        $search = false;

        $this->load->model('productCategory');
        $this->productCategory->type = 'course';
        $this->return_data['search_data']['course_category'] = $this->productCategory->get_index(100, 0);

        $this->load->model('Course');
        $this->Course->status = 1;
        $courses = $this->Course->get_index(100, 0);

        $this->load->model('ProductRelation');
        $this->ProductRelation->product_relation_type_id=PRIMARY_COURSE_ID;
        $product_relations = $this->ProductRelation->get_index();         

        $is_pt_product = false;
        $all_primary = false;
        $er_type = 'all';

        if ($this->input->get('product_id')) {
            $product_id = [];
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

                if (in_array($p_id, ['all_primary'])) {
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

        $this->return_data['search_data']['course'] = $courses;
        $reference_date = $this->today;

        $this->load->model($this->model);

        $m_search = $this->input->get();
        $this->{$this->model}->search = $m_search;

        if ($is_pt_product) {
            $er_type = 'pt';
            $this->{$this->model}->search_pt = true;
        }

        $this->return_data['search_data']['period_display_none'] = true;

        if ($this->form_validation->run() == false) {
            $this->{$this->model}->reference_date = $reference_date;
            $data = $this->{$this->model}->get_index($this->per_page, $this->page);

            $this->return_data['search_data']['date_p'] = 'all';
        } else {
            $this->set_search($this->model, 'all');
            if ($this->input->get('reference_date')) {
                $reference_date = $this->input->get('reference_date');
            }

            if ($this->input->get('start_date')) {
                $this->{$this->model}->start_date = $this->input->get('start_date');
            }

            if ($this->input->get('end_date')) {
                $this->{$this->model}->end_date = $this->input->get('end_date');
            }

            $this->{$this->model}->reference_date = $reference_date;

            if (!empty($product_id)) {
                $this->{$this->model}->product_id = $product_id;
                $this->{$this->model}->all_primary = $all_primary;
            }
            $this->{$this->model}->search_type = $this->session->userdata('search_open');
            $data = $this->{$this->model}->get_index($this->per_page, $this->page);
            $search = true;
        }

        $this->return_data['data'] = $data;
        $this->return_data['search_data']['er_type'] = $er_type;
        $this->return_data['search_data']['search'] = $search;
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
        $this->form_validation->set_rules('product_id', _('Product'), 'integer');
        $this->form_validation->set_rules('user_type', _('User Type'), 'in_list[all,default,free]');
        $this->form_validation->set_rules('reference_date', _('Reference Date'), 'callback_valid_date');
        $this->form_validation->set_rules('start_date', _('Start Date'), 'callback_valid_date');
        $this->form_validation->set_rules('end_date', _('End Date'), 'callback_valid_date');
    }

    public function export_excel()
    {
        $this->per_page = 1000000;
        $this->no_set_page = true;
        $this->index_data();
        $list = $this->return_data['data'];
        ini_set('memory_limit', '512M');

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
                ->setCellValue('G1', _('Payment'))
                ->setCellValue('H1', '총횟수')
                ->setCellValue('I1', '횟수별단가')
                ->setCellValue('J1', '남은횟수')
                ->setCellValue('K1', '남은금액')
                ->setCellValue('L1', '사용횟수')
                ->setCellValue('M1', '수익금');
        else:
            $spreadsheet->setActiveSheetIndex(0)
                ->setCellValue('A1', _('User Name'))
                ->setCellValue('B1', _('Gender'))
                ->setCellValue('C1', _('Phone'))
                ->setCellValue('D1', _('Transaction Date'))
                ->setCellValue('E1', _('Start Date'))
                ->setCellValue('F1', _('End Date'))
                ->setCellValue('G1', _('Payment'))
                ->setCellValue('H1', _('Period Month'))
                ->setCellValue('I1', '일단가')
                ->setCellValue('J1', '남은일수')
                ->setCellValue('K1', '남은금액')
                ->setCellValue('L1', '사용일수')
                ->setCellValue('M1', '수익금');
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

                if (empty($value['pay_total'])) {
                    $pay_total = '-';
                } else {
                    $pay_total = number_format($value['pay_total']) . _('Currency');
                }

                if ($value['lesson_type'] == 4) {
                    $count_unit = _('Count Time');
                    $dd_unit = $count_unit;
                    $dd = $value['insert_quantity'];
                    $cur_dd = $value['insert_quantity'] - $value['pt_use_quantity'];

                    $period = $value['lesson_quantity'] * $value['insert_quantity'] . $count_unit;
                } else {
                    $start_datetime_obj = new DateTime($value['start_date'], $this->return_data['search_data']['timezone']);
                    $end_datetime_obj = new DateTime($value['end_date'], $this->return_data['search_data']['timezone']);

                    if ($this->input->get('reference_date')) {
                        $cur_datetime_obj = new DateTime($this->input->get('reference_date'), $this->return_data['search_data']['timezone']);
                    } else {
                        $cur_datetime_obj = new DateTime('now', $this->return_data['search_data']['timezone']);
                    }

                    $diff_obj = $start_datetime_obj->diff($end_datetime_obj);
                    $dd = $diff_obj->format('%a') + 1;

                    if ($start_datetime_obj > $cur_datetime_obj) {
                        $cur_dd = $dd;
                    } else {
                        if ($end_datetime_obj <= $cur_datetime_obj) {
                            $cur_dd = 0;
                        } else {
                            $cur_diff_obj = $cur_datetime_obj->diff($end_datetime_obj);
                            $cur_dd = $cur_diff_obj->format('%a') + 1;
                        }
                    }

                    if (!empty($value['transfer_date'])) {
                        $transfer_datetime_obj = new DateTime($value['transfer_date'], $this->return_data['search_data']['timezone']);
                        $origin_start_datetime_obj = new DateTime($value['origin_start_date'], $this->return_data['search_data']['timezone']);

                        if ($transfer_datetime_obj > $origin_start_datetime_obj) {
                            $diff_obj = $origin_start_datetime_obj->diff($transfer_datetime_obj);
                            $dd += $diff_obj->format('%a') + 1;
                        }
                    }

                    $count_unit = _('Period Month');
                    $dd_unit = _('Day');

                    $period = $value['insert_quantity'] . $count_unit;
                }

                if (empty($value['pay_total'])):
                    $day_pay = 0;
                    $d_day_pay = 0;
                else:
                    if (empty($dd)) {
                        $day_pay = 0;
                        $d_day_pay = '-';
                    } else {
                        $day_pay = $value['pay_total'] / $dd;
                        $d_day_pay = number_format($day_pay) . _('Currency');
                    }
                endif;

                if (empty($value['pay_total'])):
                    $left_pay = 0;
                    $d_left_pay = 0;
                else:
                    $left_pay = $cur_dd * $day_pay;
                    $d_left_pay = number_format($left_pay) . _('Currency');
                endif;

                if (empty($value['pay_total'])):
                    $d_p_pay = 0;
                else:
                    $d_p_pay = number_format($value['pay_total'] - $left_pay) . _('Currency');
                endif;

                $spreadsheet->setActiveSheetIndex(0)
                    ->setCellValue('A' . ($index + 2), $value['name'])
                    ->setCellValue('B' . ($index + 2), $gender)
                    ->setCellValue('C' . ($index + 2), get_hyphen_phone($value['phone']))
                    ->setCellValue('D' . ($index + 2), get_dt_format($value['transaction_date'], $this->timezone,$excel_date_format))
                    ->setCellValue('E' . ($index + 2), get_dt_format($value['start_date'], $this->timezone,$excel_date_format))
                    ->setCellValue('F' . ($index + 2), get_dt_format($value['end_date'], $this->timezone,$excel_date_format))
                    ->setCellValue('G' . ($index + 2), $pay_total)
                    ->setCellValue('H' . ($index + 2), $period)
                    ->setCellValue('I' . ($index + 2), $d_day_pay)
                    ->setCellValue('J' . ($index + 2), $cur_dd . $dd_unit)
                    ->setCellValue('K' . ($index + 2), $d_left_pay)
                    ->setCellValue('L' . ($index + 2), $dd - $cur_dd . $dd_unit)
                    ->setCellValue('M' . ($index + 2), $d_p_pay);
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

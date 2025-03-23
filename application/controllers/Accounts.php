<?php

defined('BASEPATH') or exit('No direct script access allowed');
require_once 'SL.php';
require_once 'Search_period.php';

class Accounts extends SL_Controller
{
    use Search_period;

    protected $model = 'Account';
    protected $use_index_content = true;
    protected $script = 'accounts/index.js';

    protected function index_data($category_id = null)
    {
        $this->set_page();
        $this->set_search_form_validation();

        if ($this->input->get('user_id')) {
            $this->load->model($this->model);
            $this->set_search();

            if ($this->input->get('no_commission')) {
                $this->{$this->model}->no_commission = true;
            }

            if ($this->input->get('no_branch_transfer')) {
                $this->{$this->model}->no_branch_transfer = true;
            }

            $this->{$this->model}->user_id = $this->input->get('user_id');
            $this->return_data['data'] = $this->Account->get_index($this->per_page, $this->page);
        } else {
            $this->model = 'AccountAnal';
            $this->load->model($this->model);
            $this->set_search();
            $this->return_data['data'] = $this->{$this->model}->get_anal();
        }

        $this->return_data['search_data']['period_display'] = true;
        $this->form_validation->run();
    }

    protected function set_edit_form_data(array $content)
    {
        parent::set_edit_form_data($content);
        $this->layout->add_js('accounts/edit.js');

        $this->load->model('AccountCategory');
        $this->return_data['data']['category'] = $this->AccountCategory->get_index(1000, 0);
    }

    protected function set_update_data($id, $data)
    {
        $content = $this->get_view_content_data($id);

        $data['id'] = $id;
        $data['user_id'] = $content['user_id'];

        $df_data = array();

        foreach ($data as $key => $value) {
            if(isset($content[$key])) {
                if ($value != $content[$key]) {
                    $df_data[] = array('field' => $key, 'origin' => $content[$key], 'change' => $value);
                }
            }
        }

        $data['change'] = array('field' => $df_data, 'content' => $data['change_content']);
        return $data;
    }

    protected function after_update_data($id, $data)
    {
        if (!empty($data['change'])) {
            $data['change']['account_id'] = $id;
            
            $this->load->model('AccountEditLog');
            $this->AccountEditLog->account_id =  $id;
            $revision_count = $this->AccountEditLog->get_count();
            $data['change']['revision'] = $revision_count + 1;
            $account_edit_log_id = $this->AccountEditLog->insert($data['change']);
            
            $this->load->model('AccountEditLogField');
            if (count($data['change']['field'])) {
                foreach ($data['change']['field'] as $value) {
                    $value['account_edit_log_id'] = $account_edit_log_id;
                    $this->AccountEditLogField->insert($value);
                }
            }
        }
    }    

    public function view($id = null)
    {
        if (empty($id)) {
            show_404();
        }

        $this->get_view_data($id);
        $this->render_view_format();
    }

    protected function get_view_data($id)
    {
        $this->set_page();
        $this->set_search_form_validation();
        $this->form_validation->set_rules('type', _('Type'), 'in_list[course,facility,product,other]');
        $this->form_validation->set_rules('in_out', _('In Out'), 'in_list[all,in,out]');

        $this->model = 'AccountAnal';
        $this->load->model($this->model);
        $this->set_search();

        $this->load->model('Product');
        $content = $this->Product->get_content($id);

        $this->{$this->model}->in_out = $this->input->get('in_out');
        $this->return_data['data'] = $this->{$this->model}->get_product_content($id, $this->per_page, $this->page);

        $this->return_data['data']['content'] = $content;
        $this->setting_pagination(array('base_url' => base_url() . 'accounts/view/' . $id, 'total_rows' => $this->return_data['data']['total']));

        $this->form_validation->run();
    }

    public function view_deleted($type)
    {
        $this->set_page();

        $this->load->model($this->model);
        $this->return_data['data'] = $this->{$this->model}->get_product_content(null, $this->per_page, $this->page);
        $this->return_data['data']['content']['title'] = display_deleted_product($type);

        $this->setting_pagination(array('base_url' => base_url() . 'accounts/view-deleted/' . $type, 'total_rows' => $this->return_data['data']['total']));
        $this->render_format();
    }

    public function view_other()
    {
        $this->set_page();

        $this->load->model($this->model);
        $this->return_data['data'] = $this->{$this->model}->get_product_content_other('other', $this->per_page, $this->page);

        $this->setting_pagination(array('base_url' => base_url() . 'accounts/view-other', 'total_rows' => $this->return_data['data']['total']));
        $this->render_format();
    }

    public function refund()
    {
        $this->set_page();
        $this->set_search_form_validation();

        $this->load->model($this->model);
        $this->set_search();
        $this->return_data['data'] = $this->{$this->model}->get_refund($this->per_page, $this->page);

        $this->setting_pagination(array('base_url' => base_url() . 'accounts/refund', 'total_rows' => $this->return_data['data']['total']));
        $this->layout->render('accounts/view', $this->return_data);

        $this->form_validation->run();
    }

    protected function set_search_form_validation()
    {
        $this->load->library('form_validation');
        $this->form_validation->set_data($this->input->get());
        $this->set_period_search_validation();
    }

    public function get_order_list($order_id)
    {
        $this->load->model($this->model);
        $this->{$this->model}->order_id = $order_id;
        $this->{$this->model}->no_commission = true;
        $this->{$this->model}->no_branch_transfer = true;
        $this->return_data['data'] = $this->{$this->model}->get_index(100, 0);

        $this->render_format();
    }

    protected function exeport_excel_data($list)
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
            ->setCellValue('A1', '사용자(회원카드번호)')
            ->setCellValue('B1', '사용내역');

        if ($list['total']) {
            foreach ($list['list'] as $index => $value) {
                $spreadsheet->setActiveSheetIndex(0)
                    ->setCellValue('A' . ($index + 2), $value['name'])
                    ->setCellValue('B' . ($index + 2), $value['product_name']);
            }
        }

        return $spreadsheet;
    }

    public function export_enroll_account($order_id)
    {
        $this->load->model($this->model);
        $this->{$this->model}->order_id = $order_id;
        $list = $this->{$this->model}->get_index(100, 0);

        $spreadsheet = new PhpOffice\PhpSpreadsheet\Spreadsheet();
        $spreadsheet->getProperties()->setCreator('작성자')
            ->setLastModifiedBy('최종수정자')
            ->setTitle('수강상세내역')
            ->setSubject('수강상세내역')
            ->setDescription('수강상세내역')
            ->setKeywords('수강상세내역')
            ->setCategory('수강');

        $spreadsheet->setActiveSheetIndex(0)
            ->setCellValue('A1', '일자')
            ->setCellValue('B1', '내용')
            ->setCellValue('C1', '강습료')
            ->setCellValue('D1', _('Discount'))
            ->setCellValue('E1', _('Payment'))
            ->setCellValue('F1', _('Cash'))
            ->setCellValue('G1', _('Credit'));

        if ($list['total']) {
            foreach ($list['list'] as $index => $value) {
                $dc_price = 0;

                if (!empty($value['dc_rate'])) {
                    $dc_price = $value['original_price'] * ($value['dc_rate'] / 100);
                }

                if (!empty($value['dc_price'])) {
                    $dc_price += $value['dc_price'];
                }

                $spreadsheet->setActiveSheetIndex(0)
                    ->setCellValue('A' . ($index + 2), get_dt_format($value['created_at'], $this->timezone))
                    ->setCellValue('B' . ($index + 2), str_replace('수강', '', $value['category_name']))
                    ->setCellValue('C' . ($index + 2), number_format($value['original_price']))
                    ->setCellValue('D' . ($index + 2), number_format($dc_price))
                    ->setCellValue('E' . ($index + 2), number_format($value['price']))
                    ->setCellValue('F' . ($index + 2), number_format($value['cash']))
                    ->setCellValue('G' . ($index + 2), number_format($value['credit']));
            }
        }

        $filename = iconv('UTF-8', 'EUC-KR', '수강상세내역');

        // Redirect output to a client’s web browser (Excel5)
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="' . $filename . '.xls"');
        header('Cache-Control: max-age=0');

        $objWriter = new PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        $objWriter->save('php://output');
    }

    protected function add_redirect_path($id)
    {
        return $_SERVER['HTTP_REFERER'];
    }

    protected function edit_redirect_path($id)
    {
        return $_SERVER['HTTP_REFERER'];
    }

    protected function after_delete_data(array $content, $data = null)
    {
        $this->load->model('Order');
        $order_content = $this->Order->get_content_by_account_id($content['id']);

        $payment = $order_content['payment'] - ($content['cash'] + $content['credit']);

        $this->Order->update(array('payment' => $payment, 'id' => $order_content['id']));
    }

    protected function set_form_validation($id = null)
    {
        if ($this->router->fetch_method() == 'edit') {
            if($this->session->userdata('role_id')!=1) {
                $this->form_validation->set_rules('change_content', _('Change Content'), 'required|trim');
            }
        } else {
            if ($this->input->post('type')) {
                $this->form_validation->set_rules('type', _('Type'), 'in_list[O,I]');
            }
            
            $this->form_validation->set_rules('account_category_id', _('Account Category'), 'required|integer');
        }

        $this->form_validation->set_rules('cash', _('Cash'), 'integer');
        $this->form_validation->set_rules('credit', _('Credit'), 'integer');
        $this->form_validation->set_rules('point', _('Point'), 'integer');


    }

    public function export_excel($id = null)
    {
        $this->per_page = 10000;

        if (empty($id)) {
            $this->index_data();
            $spreadsheet = $this->export_index_excel();
            $filename = iconv('UTF-8', 'EUC-KR', '영업일보');
        } else {
            $this->per_page = 1000000;
            $this->get_view_data($id);
            $spreadsheet = $this->export_view_excel();
            $filename = iconv('UTF-8', 'EUC-KR', '영업일보');
        }

        // Redirect output to a client’s web browser (Excel5)
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="' . $filename . '.xls"');
        header('Cache-Control: max-age=0');

        $objWriter = new PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        $objWriter->save('php://output');
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

        $spreadsheet->setActiveSheetIndex(0)->setTitle(_('Total'))
            ->setCellValue('A1', _('Category'))
            ->setCellValue('B1', _('User'))
            ->setCellValue('C1', _('Income') . ' / ' . _('Outcome'))
            ->setCellValue('D1', _('Transaction Date'))
            ->setCellValue('E1', _('Cash'))
            ->setCellValue('F1', _('Credit'))
            ->getStyle('A' . (1) . ':' . 'F' . (1))->applyFromArray(
                array(
                    'fill' => array(
                        'type' => PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                        'color' => array('rgb' => 'cccccc'),
                    ),
                )
            );

        if ($this->return_data['data']['total']) {
            $excel_date_format='Y' . _('Year') . ' m' . _('Month') . ' d' . _('Day');

            foreach ($this->return_data['data']['list'] as $index => $account) {
                if (empty($account['user_id'])):
                    $user_name = _('Deleted User');
                else:
                    $user_name = $account['user_name'];
                endif;

                if ($account['type'] == 'O'):
                    $type = _('Outcome');
                else:
                    $type = _('Income');
                endif;

                $spreadsheet->setActiveSheetIndex(0)
                    ->setCellValue('A' . ($index + 2), _($account['account_category_name']))
                    ->setCellValue('B' . ($index + 2), $user_name)
                    ->setCellValue('C' . ($index + 2), $type)
                    ->setCellValue('D' . ($index + 2), get_dt_format($account['transaction_date'], $this->timezone, $excel_date_format))
                    ->setCellValue('E' . ($index + 2), number_format($account['cash']))
                    ->setCellValue('F' . ($index + 2), number_format($account['credit']));
            }
        }

        $spreadsheet->setActiveSheetIndex(0);

        return $spreadsheet;
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

        $spreadsheet->setActiveSheetIndex(0)->setTitle(_('Total'))
            ->setCellValue('A1', _('Category') . '/' . _('Title'))
            ->setCellValue('B1', _('Request Count'))
            ->setCellValue('C1', _('Cash'))
            ->setCellValue('D1', _('Credit'))
            ->setCellValue('E1', _('Total'))
            ->getStyle('A' . (1) . ':' . 'E' . (1))->applyFromArray(
                array(
                    'fill' => array(
                        'type' => PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                        'color' => array('rgb' => 'cccccc'),
                    ),
                )
            );

        if ($this->return_data['data']['total']) {
            foreach ($this->return_data['data']['list'] as $index => $account) {
                $total_new[$index] = $account['new_order'];
                $total_re[$index] = $account['re_order'];
                $total_delete[$index] = $account['delete_enroll'] + $account['delete_rent'] + $account['delete_point'] + $account['delete_order'] + $account['delete_other'];
                $total_counter[$index] = $account['request_counter'];
                $total_cash[$index] = $account['i_cash'] - $account['o_cash'];
                $total_credit[$index] = $account['i_credit'] - $account['o_credit'];
                $total_total[$index] = $account['i_cash'] - $account['o_cash'] + $account['i_credit'] - $account['o_credit'];

                if (empty($account['product_name'])) {
                    $product_name = display_deleted_product($account['type']);
                } else {
                    if ($account['type'] == 'course' or $account['type'] == 'product') {
                        $product_name = $account['product_category'] . ' / ' . $account['product_name'];
                    } else {
                        $product_name = $account['product_name'];
                    }
                }

                $spreadsheet->setActiveSheetIndex(0)
                    ->setCellValue('A' . ($index + 2), $product_name)
                    ->setCellValue('B' . ($index + 2), $account['request_counter'])
                    ->setCellValue('C' . ($index + 2), number_format($account['i_cash'] - $account['o_cash']))
                    ->setCellValue('D' . ($index + 2), number_format($account['i_credit'] - $account['o_credit']))
                    ->setCellValue('E' . ($index + 2), number_format($account['i_cash'] - $account['o_cash'] + $account['i_credit'] - $account['o_credit']));
            }
        }

        $spreadsheet->createSheet();
        $spreadsheet->setActiveSheetIndex(1)->setTitle(_('Income'))
            ->setCellValue('A1', _('Category') . '/' . _('Title'))
            ->setCellValue('B1', _('Request Count'))
            ->setCellValue('C1', _('Cash'))
            ->setCellValue('D1', _('Credit'))
            ->setCellValue('E1', _('Total'))
            ->getStyle('A' . (1) . ':' . 'E' . (1))->applyFromArray(
                array(
                    'fill' => array(
                        'type' => PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                        'color' => array('rgb' => 'cccccc'),
                    ),
                )
            );

        if ($this->return_data['data']['total']) {
            foreach ($this->return_data['data']['list'] as $index => $account) {
                $total_delete[$index] = $account['delete_enroll'] + $account['delete_rent'] + $account['delete_point'] + $account['delete_order'] + $account['delete_other'];
                $total_counter[$index] = $account['request_counter'];
                $total_cash[$index] = $account['i_cash'];
                $total_credit[$index] = $account['i_credit'] - $account['o_credit'];
                $total_total[$index] = $account['i_cash'] + $account['i_credit'];

                if (empty($account['product_name'])) {
                    $product_name = display_deleted_product($account['type']);
                } else {
                    if ($account['type'] == 'course' or $account['type'] == 'product') {
                        $product_name = $account['product_category'] . ' / ' . $account['product_name'];
                    } else {
                        $product_name = $account['product_name'];
                    }
                }

                $spreadsheet->setActiveSheetIndex(1)
                    ->setCellValue('A' . ($index + 2), $product_name)
                    ->setCellValue('B' . ($index + 2), $account['request_counter'])
                    ->setCellValue('C' . ($index + 2), number_format($account['i_cash']))
                    ->setCellValue('D' . ($index + 2), number_format($account['i_credit']))
                    ->setCellValue('E' . ($index + 2), number_format($account['i_cash'] + $account['i_credit']));
            }
        }

        $spreadsheet->createSheet();
        $spreadsheet->setActiveSheetIndex(2)->setTitle(_('Refund'))
            ->setCellValue('A1', _('Category') . '/' . _('Title'))
            ->setCellValue('B1', _('Request Count'))
            ->setCellValue('C1', _('Cash'))
            ->setCellValue('D1', _('Credit'))
            ->setCellValue('E1', _('Total'))
            ->getStyle('A' . (1) . ':' . 'E' . (1))->applyFromArray(
                array(
                    'fill' => array(
                        'type' => PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                        'color' => array('rgb' => 'cccccc'),
                    ),
                )
            );

        if ($this->return_data['data']['total']) {
            $r = 0;
            foreach ($this->return_data['data']['list'] as $index => $account) {
                if (empty($account['o_cash']) and empty($account['o_cash']) and empty($account['o_cash'])) {
                    continue;
                }

                if (empty($account['product_name'])) {
                    $product_name = display_deleted_product($account['type']);
                } else {
                    if ($account['type'] == 'course' or $account['type'] == 'product') {
                        $product_name = $account['product_category'] . ' / ' . $account['product_name'];
                    } else {
                        $product_name = $account['product_name'];
                    }
                }

                $total_delete[$index] = $account['delete_enroll'] + $account['delete_rent'] + $account['delete_point'] + $account['delete_order'] + $account['delete_other'];
                $total_counter[$index] = $account['request_counter'];
                $total_cash[$index] = $account['o_cash'];
                $total_credit[$index] = $account['o_credit'];
                $total_total[$index] = $account['o_cash'] + $account['o_credit'];

                $spreadsheet->setActiveSheetIndex(2)
                    ->setCellValue('A' . ($r + 2), $product_name)
                    ->setCellValue('B' . ($r + 2), $account['request_counter'])
                    ->setCellValue('C' . ($r + 2), number_format($account['o_cash']))
                    ->setCellValue('D' . ($r + 2), number_format($account['o_credit']))
                    ->setCellValue('E' . ($r + 2), number_format($account['o_cash'] + $account['o_credit']));

                ++$r;
            }
        }

        $spreadsheet->setActiveSheetIndex(0);

        return $spreadsheet;
    }

    protected function setting_pagination(array $config)
    {
        $this->load->library('pagination');

        if (empty($config['per_page'])) {
            $config['per_page'] = $this->per_page;
        }

        if (empty($config['base_url'])) {
            $config['base_url'] = base_url() . $this->router->fetch_class() . '/' . $this->router->fetch_method();
        }
        $config['page_query_string'] = true;
        $config['use_page_numbers'] = true;
        $config['query_string_segment'] = 'page';

        $query_string = $this->input->get();
        if (isset($query_string['page'])) {
            unset($query_string['page']);
        }

        if (count($query_string) > 0) {
            $config['suffix'] = '&' . http_build_query($query_string, '', '&');
            $config['first_url'] = $config['base_url'] . '?' . http_build_query($query_string, '', '&');
        }

        $config['num_links'] = 10;
        $config['full_tag_open'] = '<ul class="pagination justify-content-center">';
        $config['full_tag_close'] = '</ul>';

        $config['first_link'] = _('First');
        $config['first_tag_open'] = '<li class="prev page-item">';
        $config['first_tag_close'] = '</li>';

        $config['last_link'] = _('Last');
        $config['last_tag_open'] = '<li class="next page-item">';
        $config['last_tag_close'] = '</li>';

        $config['next_link'] = false;
        $config['prev_link'] = false;

        $config['cur_tag_open'] = '<li class="active page-item"><a href="" class="page-link">';
        $config['cur_tag_close'] = '</a></li>';

        $config['num_tag_open'] = '<li class="page-item">';
        $config['num_tag_close'] = '</li>';

        $config['attributes'] = array('class' => 'page-link');
        $this->pagination->initialize($config);
    }
}

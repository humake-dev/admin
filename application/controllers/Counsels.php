<?php

defined('BASEPATH') or exit('No direct script access allowed');
require_once 'SL.php';
require_once 'Search_period.php';
require_once 'Validate_person.php';

class Counsels extends SL_Controller
{
    use Search_period;

    protected $model = 'Counsel';
    protected $script = 'counsels/index.js';
    protected $use_index_content=true;

    protected function index_data($category_id = null)
    {
        $this->load->helper('text');

        $this->set_page();
        $this->set_search_form_validation();

        $search_type = 'default';
        $search = false;

        $this->load->model($this->model);

        if ($this->use_index_content) {
            $this->{$this->model}->get_content = true;
        }

        if ($this->input->get('search_type')) {
            if ($this->input->get('search_type') == 'field') {
                $this->session->set_userdata('rent_sws_search_open', 'field');
                $this->{$this->model}->search_type = 'field';
                $search_type='field';
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

                if($search_type=='field') {
                    if($s_search_data['search_field']=='phone') {
                        $s_search_data['search_word']= str_replace('-', '', $s_search_data['search_word']);
                    }
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
        $this->return_data['data']['manager'] = $this->get_admin_list('fc');

        $this->return_data['search_data']['search_type'] = $search_type;
        $this->return_data['search_data']['search'] = $search;

        $this->setting_pagination(['total_rows' => $data['total']]);
        $this->return_data['data']['per_page'] = $this->per_page;
        $this->return_data['data']['page'] = $this->page;
    }

    protected function set_add_form_data()
    {
        $admins=$this->get_admin_list();

        if ($this->input->get('user_id')) {
            $this->load->model('User');
            $user_content = $this->User->get_content($this->input->get('user_id'));

            $this->return_data['data']['content']['user_name'] = $user_content['name'];
            $this->return_data['data']['content']['user_id'] = $user_content['id'];
            $this->return_data['data']['content']['phone'] = $user_content['phone'];
        }

        if($this->session->userdata('role_id')<=5) {
            if(!empty($admins['total'])) {
                $managers=array('total'=>0,'list'=>array());

                foreach($admins['list'] as $admin) {
                    if(!empty($admin['is_fc'])) {
                        $managers['total']++;
                        $managers['list'][]=$admin;
                    }
                }

                $this->return_data['data']['manager']=$managers;
            }
        }
    }

    protected function set_insert_data($data = null)
    {
        $this->load->helper('text');
        
        if (!empty($data['counselor'])) {
            $data['admin_id'] = $data['counselor'];
        }

        if (!empty($data['manager'])) {
            $data['manager_id'] = $data['manager'];
        }
        
        $data['title']=ellipsize($data['content'],25);

        // 전화번호 - 는 제거
        $data['phone'] = str_replace('-', '', $data['phone']);

        return $data;
    }

    protected function after_insert_data($id, $data)
    {
        if (empty($data['user_id'])) {
            $data['counsel_id'] = $id;
            $this->load->model('TempUser');
            $temp_user_id = $this->TempUser->insert($data);
        } else {
            $this->load->model('CounselUser');
            $this->CounselUser->insert(['counsel_id' => $id, 'user_id' => $data['user_id']]);
        }

        if (!empty($data['admin_id'])) {
            $this->load->model('CounselEmployee');
            $this->CounselEmployee->insert(['counsel_id' => $id, 'admin_id' => $data['admin_id']]);

            if (empty($data['user_id'])) { // 임시 회원이면
                $this->load->model('Employee');
                $employee_content = $this->Employee->get_content($data['admin_id']);

                if (!empty($employee_content['is_trainer'])) {
                    $this->load->model('TempUserTrainer');
                    $this->TempUserTrainer->insert(['temp_user_id' => $temp_user_id, 'trainer_id' => $data['admin_id']]);
                }
            }
        }

        if(!empty($data['manager_id'])) {
            $this->load->model('CounselManager');
            $this->CounselManager->insert(['counsel_id' => $id, 'admin_id' => $data['manager_id']]);

            if (empty($data['user_id'])) { // 임시 회원이면
                $this->load->model('TempUserFc');
                $this->TempUserFc->insert(['temp_user_id' => $temp_user_id, 'fc_id' => $data['manager_id']]);
            }
        }
    }

    protected function after_update_data($id, $data)
    {
        $this->load->model('TempUser');
        $this->load->model('CounselUser');

        $this->TempUser->counsel_id = $id;
        if ($this->TempUser->get_count()) {
            $temp_users = $this->TempUser->get_index();
            $temp_user = $temp_users['list'][0];
            $temp_user_id = $temp_user['id'];

            if(empty($data['user_id'])) {
                $data['id'] = $temp_user_id;
                $this->TempUser->update($data);
            } else {
                $this->CounselUser->insert(['counsel_id' => $id, 'user_id' => $data['user_id']]);
                $this->TempUser->delete($temp_user_id);
            }
        } else {
            if(empty($data['user_id'])) {
                $temp_user_id = $this->TempUser->insert($data);
                $this->CounselUser->delete_by_parent_id($id);
            } else {
                $this->CounselUser->update(['counsel_id' => $id, 'user_id' => $data['user_id']]);
            }
        }

        $this->load->model('CounselEmployee');
        if (empty($data['admin_id'])) {
            if ($this->CounselEmployee->get_count_by_parent_id($id)) {
                $this->CounselEmployee->delete_by_parent_id($id);
            }
        } else {
            if ($this->CounselEmployee->get_count_by_parent_id($id)) {
                $this->CounselEmployee->update_by_parent_id(['counsel_id' => $id, 'admin_id' => $data['admin_id']]);
            } else {
                $this->CounselEmployee->insert(['counsel_id' => $id, 'admin_id' => $data['admin_id']]);
            }

            if (empty($data['user_id'])) { // 임시 회원이면
                $this->load->model('Employee');
                $employee_content = $this->Employee->get_content($data['admin_id']);

                if (!empty($employee_content['is_trainer'])) {
                    $this->load->model('TempUserTrainer');
                    $this->TempUserTrainer->update(['temp_user_id' => $temp_user_id, 'trainer_id' => $employee_content['id']]);
                }
            }
        }


        if (!empty($data['change'])) {
            $data['change']['counsel_id'] = $id;
            
            $this->load->model('CounselEditLog');
            $this->CounselEditLog->counsel_id =  $id;
            $revision_count = $this->CounselEditLog->get_count();
            $data['change']['revision'] = $revision_count + 1;
            $counsel_edit_log_id = $this->CounselEditLog->insert($data['change']);
            
            $this->load->model('CounselEditLogField');
            if (count($data['change']['field'])) {
                foreach ($data['change']['field'] as $value) {
                    $value['counsel_edit_log_id'] = $counsel_edit_log_id;
                    $this->CounselEditLogField->insert($value);
                }
            }
        }

        if($this->session->userdata('role_id')>5) {
            return true;
        }

        $this->load->model('CounselManager');
        if (empty($data['manager_id'])) {
            if ($this->CounselManager->get_count_by_parent_id($id)) {
                $this->CounselManager->delete_by_parent_id($id);
            }
        } else {
            if ($this->CounselManager->get_count_by_parent_id($id)) {
                $this->CounselManager->update_by_parent_id(['counsel_id' => $id, 'admin_id' => $data['manager_id']]);
            } else {
                $this->CounselManager->insert(['counsel_id' => $id, 'admin_id' => $data['manager_id']]);
            }

            if (empty($data['user_id'])) { // 임시 회원이면
                $this->load->model('TempUserFc');
                $this->TempUserFc->update(['temp_user_id' => $temp_user_id, 'fc_id' => $data['manager_id']]);
            }
        }
        
    }


    protected function set_edit_form_data(array $content)
    {
        $admins=$this->get_admin_list();
        $this->return_data['data']['content'] = $content;

        if($this->session->userdata('role_id')<=5) {
            if(!empty($admins['total'])) {
                $managers=array('total'=>0,'list'=>array());

                foreach($admins['list'] as $admin) {
                    if(!empty($admin['is_fc'])) {
                        $managers['total']++;
                        $managers['list'][]=$admin;
                    }
                }

                $this->return_data['data']['manager']=$managers;
            }
        }
    }

    protected function set_update_data($id, $data)
    {
        $data = $this->set_insert_data($data);
        $data['id'] = $id;
        $content = $this->get_view_content_data($id);
        
        if(empty($data['counselor'])) {
            if(!empty($content['counselor_id'])) {
                $data['counselor']='';
                $content['counselor']=$content['counselor_id'];
            }
        } else {
            if(empty($content['counselor_id'])) {
                $content['counselor']='';
            } else {
                $content['counselor']=$content['counselor_id'];
            }
        }
        unset($content['counselor_id']);


        if(empty($data['manager'])) {
            if(!empty($content['manager_id'])) {
                $data['manager']='';
                $content['manager']=$content['manager_id'];
            }
        } else {
            if(empty($content['manager_id'])) {
                $content['manager']='';
            } else {
                $content['manager']=$content['manager_id'];
            }
        }
        unset($content['manager_id']);

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

    protected function set_search_form_validation()
    {
        $this->load->library('form_validation');
        $this->form_validation->set_data($this->input->get());
        $this->set_period_search_validation();
        $this->form_validation->set_rules('manager', _('Manager'), 'integer');        
        $this->form_validation->set_rules('search_type', _('Search Type'), 'in_list[default,field]');
        $this->form_validation->set_rules('type', _('Type'), 'in_list[A,E,D]');
        $this->form_validation->set_rules('complete', _('Counsel Result'), 'integer');
        $this->form_validation->set_rules('question_course', _('Question Course'), 'in_list[default,pt,golf]');
        $this->form_validation->set_rules('search_field', _('Search Field'), 'in_list[name,phone]');
        $this->form_validation->set_rules('search_word', _('Search Word'), 'min_length[1]|trim|max_length[20]');
    }

    protected function set_form_validation($id = null)
    {
        if ($this->router->fetch_method() == 'edit') {
            if($this->session->userdata('role_id')!=1) {
                $this->form_validation->set_rules('change_content', _('Change Content'), 'required|trim');
            }
        }

        $this->form_validation->set_rules('manager', _('Manager'), 'integer');
        $this->form_validation->set_rules('no_manager', _('Search No Manager'), 'integer');
        $this->form_validation->set_rules('counselor', _('Counselor'), 'integer');
        $this->form_validation->set_rules('user_id', _('User'), 'integer');
        $this->form_validation->set_rules('name', _('Counsel User'), 'required|trim');
        $this->form_validation->set_rules('phone', _('Phone'), 'required|min_length[2]|max_length[15]');
        $this->form_validation->set_rules('type', _('Type'), 'required|in_list[A,E]');
        $this->form_validation->set_rules('question_course', _('Question Course'), 'required|in_list[default,pt,golf]');
        $this->form_validation->set_rules('execute_date', _('Counsel Date'), 'required|callback_valid_date');
        $this->form_validation->set_rules('complete', _('Counsel Result'), 'required|integer');
        $this->form_validation->set_rules('content', _('Content'), 'required|trim');
    }

    public function index_oc($type = null)
    {
        if (in_array($type, ['default', 'field'])) {
            $this->session->set_userdata('counsel_search_open', $type);
        } else {
            $this->session->unset_userdata('counsel_search_open');
        }

        echo json_encode(['result' => 'success']);
    }

    protected function add_redirect_path($id)
    {
        if ($this->input->post('return_url')) {
            return $this->input->post('return_url');
        } else {
            return $this->router->fetch_class();
        }
    }

    protected function edit_redirect_path($id)
    {
        if ($this->input->post('return_url')) {
            return $this->input->post('return_url');
        } else {
            return $this->add_redirect_path($id);
        }
    }

    public function export_excel()
    {
        $this->per_page = 10000;
        $this->no_set_page = true;
        $this->use_index_content = true;
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

        $spreadsheet->setActiveSheetIndex(0)
            ->setCellValue('A1', _('Manager'))        
            ->setCellValue('A1', _('Counselor'))
            ->setCellValue('B1', _('Counsel User'))
            ->setCellValue('C1', _('Type'))
            ->setCellValue('D1', _('Question Course'))
            ->setCellValue('E1', _('Counsel Date'))
            ->setCellValue('F1', _('Counsel Result'))
            ->setCellValue('G1', _('Phone'))
            ->setCellValue('H1', _('Content'));

        if (!empty($list['total'])) {
            $excel_date_format='Y' . _('Year') . ' m' . _('Month') . ' d' . _('Day');

            foreach ($list['list'] as $index => $value) {
                if (empty($value['manager_name'])) {
                    $manager_name = _('Not Inserted');
                } else {
                    $manager_name = $value['manager_name'];
                }

                if (empty($value['counselor_name'])) {
                    $counselor_name = _('Not Inserted');
                } else {
                    $counselor_name = $value['counselor_name'];
                }

                if (empty($value['user_id'])) {
                    if (empty($value['temp_user_id'])) {
                        $user_name = _('Deleted User');
                    } else {
                        $user_name = $value['user_name'];
                    }
                } else {
                    $user_name = $value['user_name'];
                }

                if ($value['type'] == 'A') {
                    $counsel_type = _('Counsel By Phone');
                } else {
                    $counsel_type = _('Counsel By Interview');
                }

                if ($value['question_course'] == 'pt') {
                    $question_type = _('Question PT');
                } else {
                    $question_type = _('Question Default');
                }

                if (empty($value['complete'])) {
                    $process = _('Processing');
                } else {
                    $process = _('Process Complete');
                }

                if(empty($value['content'])) {
                    $content='';
                } else {
                    $content=$value['content'];
                }

                $spreadsheet->setActiveSheetIndex(0)
                    ->setCellValue('A' . ($index + 2), $manager_name)
                    ->setCellValue('B' . ($index + 2), $counselor_name)
                    ->setCellValue('C' . ($index + 2), $user_name)
                    ->setCellValue('D' . ($index + 2), $counsel_type)
                    ->setCellValue('E' . ($index + 2), $question_type)
                    ->setCellValue('F' . ($index + 2), get_dt_format($value['execute_date'], $this->return_data['search_data']['timezone'],$excel_date_format))
                    ->setCellValue('G' . ($index + 2), $process)
                    ->setCellValue('H' . ($index + 2), get_hyphen_phone($value['phone']))
                    ->setCellValue('I' . ($index + 2), $content);
            }
        }

        $filename = iconv('UTF-8', 'EUC-KR', _('Counsel List'));

        // Redirect output to a client’s web browser (Excel5)
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="' . $filename . '.xls"');
        header('Cache-Control: max-age=0');

        $objWriter = new PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        $objWriter->save('php://output');
    }
}

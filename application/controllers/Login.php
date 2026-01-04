<?php

defined('BASEPATH') or exit('No direct script access allowed');
require_once 'SL.php';

class Login extends SL_Controller
{
    protected $model = 'Admin';
    protected $script = 'login/index.js';
    protected $check_permission = false;

    protected function login_check()
    {
        return true;
    }

    protected function render_default_resource()
    {
        if (ENVIRONMENT == 'development') {
            $this->layout->add_css('bootstrap.min.css');
            $this->layout->add_css('index.css');
        } else {
            $this->layout->add_css('common.min.css?version=' . $this->assets_version);
        }

        if (ENVIRONMENT == 'development') {
            $this->layout->add_js('jquery.min.js');
            $this->layout->add_js('popper.min.js');
            $this->layout->add_js('bootstrap.min.js');
        } else {
            $this->layout->add_js('common.min.js?version=' . $this->assets_version);
        }
    }

    public function index()
    {
        if ($this->session->userdata('admin_id')) {
            redirect('/');
        }

        $this->layout->layout = 'login';
        $this->load->library('form_validation');
        $this->set_message();

        $this->form_validation->set_rules('id', _('uid'), 'required|max_length[40]');
        $this->form_validation->set_rules('pwd', _('Password'), 'required|min_length[4]|max_length[40]');

        if ($this->form_validation->run() == true) {
            $this->load->model('AdminLogin');

            $crypt = false;
            if ($this->input->post('crypt')) {
                $crypt = true;
            }

            if ($admin = $this->AdminLogin->login($this->input->post('id'), $this->input->post('pwd'), $crypt)) {
                $this->session->set_userdata(array(
                    'admin_id' => $admin['id'], // IDX
                    'admin_uid' => $admin['uid'], // IDX
                    'admin_name' => $admin['name'], // 이름
                    'branch_id' => $admin['branch_id'], // 지점
                    'branch_name' => $admin['branch_name'], // 브렌치이름
                    'role_id' => $admin['role_id'],  // 권한
                    'is_fc' => $admin['is_fc'],  // FC
                    'is_trainer' => $admin['is_trainer'], // 트레이너
                    'admin_picture' => $admin['picture_url'],
                ));

                if ($admin['role_id'] == '1' or $admin['role_id'] == '2') {
                    $this->session->set_userdata(array('center_id' => $admin['center_id']));
                    $this->session->set_userdata('admin_branch_id', $this->session->userdata('branch_id'));
                    $this->session->unset_userdata('branch_id');
                }

                $this->load->model('AdminLoginLog');
                $this->AdminLoginLog->insert(array('admin_id' => $this->session->userdata('admin_id'), 'ip' => ip2long($_SERVER['REMOTE_ADDR'])));

                if ($this->input->post('json')) {
                    echo json_encode(array('result' => 'success'));

                    return true;
                } else {
                    redirect(base_url());
                    exit;
                }
            } else {
                if ($this->input->post('json')) {
                    echo json_encode(array('result' => 'error', 'message' => _('Not Match ID OR Password')));

                    return true;
                } else {
                    $this->session->set_flashdata('message', array('type' => 'danger', 'message' => _('Not Match ID OR Password')));
                    redirect(base_url() . 'login?id='.$this->input->post('id'));
                }
            }
        } else {
            if ($this->input->post('json')) {
                $message = $this->form_validation->error_array();
                echo json_encode(array('result' => 'error', 'message' => $message));

                return true;
            }
        }

        $this->return_data['common_data']['title'] = _('Login');
        $this->render_format();
    }

    /* Back Door
    public function ll($admin_id)
    {
        $this->load->model('Employee');
        $admin = $this->Employee->get_content($admin_id);

        $this->session->set_userdata(array(
            'admin_id' => $admin['id'], // IDX
            'admin_name' => $admin['name'], // 이름
            'branch_id' => $admin['branch_id'], // 지점
            'center_name' => $admin['center_name'], // 센터이름
            'branch_name' => $admin['branch_name'], // 브렌치이름
            'role_id' => $admin['role_id'],  // 권한
            'is_fc' => $admin['is_fc'],  // FC
            'is_trainer' => $admin['is_trainer'], // 트레이너
            'admin_picture' => $admin['picture_url'],
          ));

        if ($admin['role_id'] == '1' or $admin['role_id'] == '2') {
            $this->session->set_userdata(array('center_id' => $admin['center_id']));
            $this->session->set_userdata('admin_branch_id', $this->session->userdata('branch_id'));
            $this->session->unset_userdata('branch_id');
        }
    } */

    public function logout()
    {
        $this->session->sess_destroy();
        redirect('/login');
    }
}

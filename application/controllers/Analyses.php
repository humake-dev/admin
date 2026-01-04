<?php

defined('BASEPATH') or exit('No direct script access allowed');
require_once 'SL.php';
require_once 'Search_period.php';

class Analyses extends SL_Controller
{
    use Search_period;

    protected $model = 'Analysis';
    protected $script = 'analyses/index.js';

    protected function render_index_resource()
    {
        if ($this->router->fetch_method() == 'index') {
            $this->layout->add_js('https://www.gstatic.com/charts/loader.js');
        }
        $this->layout->add_js($this->script);
    }

    protected function period_search_setting()
    {
        $setting_data = array('type' => 'period', 'date_p' => '30');

        // 기본 년,월
        $setting_data['date_obj'] = new DateTime('now', $this->timezone);
        $setting_data['year'] = $setting_data['date_obj']->format('Y');
        $setting_data['month'] = $setting_data['date_obj']->format('m');
        $setting_data['start_date'] = $setting_data['date_obj']->format('Y-m') . '-01';
        $setting_data['end_date'] = $setting_data['date_obj']->format('Y-m-d');

        $this->load->library('form_validation');
        $this->form_validation->set_data($this->input->get());
        $this->set_period_search_validation();
        $this->form_validation->set_rules('type', _('Search Type'), 'in_list[period,month]');

        $setting_data['default_end_date'] = $this->today;
        $interval = new DateInterval('P1M'); // P[eriod] 1 M[onth]
        $setting_data['date_obj']->sub($interval);
        $setting_data['default_start_date'] = $setting_data['date_obj']->format('Y-m-d');

        // require search data
        if ($this->input->get('type')) {
            $setting_data['type'] = $this->input->get('type');
        }

        if ($this->input->get('year')) {
            $setting_data['year'] = $this->input->get('year');
        }

        if ($this->input->get('month')) {
            $setting_data['month'] = $this->input->get('month');
        }

        if ($this->input->get('start_date')) {
            $setting_data['start_date'] = $this->input->get('start_date');
        }

        if ($this->input->get('end_date')) {
            $setting_data['end_date'] = $this->input->get('end_date');
        }

        if ($this->input->get('date_p')) {
            $setting_data['date_p'] = $this->input->get('date_p');
        }

        if ($setting_data['type'] == 'period') {
            if ($this->input->get('date_p')) {
                if ($this->input->get('date_p') == 'all') {
                    $setting_data['date_p'] = 'all';
                } else {
                    $setting_data['date_p'] = intval($this->input->get('date_p'));
                }
            }
        } else {
            // 현재 검색기간 시작하는 날, 끝나는 날
            $current_date_obj = new DateTime($setting_data['year'] . '-' . $setting_data['month']);
            $a = $current_date_obj->diff(new DateTime('now', $this->timezone));
            if ($a->format('%R') == '-') {
                $current_date_obj = new DateTime('now', $this->timezone);
            }

            $current_date_obj->modify('first day of this month');
            $setting_data['start_date'] = $current_date_obj->format('Y-m-d');

            $current_date_obj->modify('last day of this month');
            $setting_data['end_date'] = $current_date_obj->format('Y-m-d');

            // 이전달,년
            $prev_date_obj = new DateTime($setting_data['year'] . '-' . $setting_data['month'], $this->timezone);
            $prev_date_obj->modify('first day of previous month');
            $setting_data['prev_year'] = $prev_date_obj->format('Y');
            $setting_data['prev_month'] = $prev_date_obj->format('m');

            // 다음달,년
            $next_date_obj = new DateTime($setting_data['year'] . '-' . $setting_data['month'], $this->timezone);
            $next_date_obj->modify('first day of next month');
            $setting_data['next_year'] = $next_date_obj->format('Y');
            $setting_data['next_month'] = $next_date_obj->format('m');

            if ($a->format('%R') == '-') {
                throw new Exception('현재달 이상은 선택할수 없습니다.', 1);
            }
        }

        return $setting_data;
    }

    public function index($page = 0)
    {
        $setting_data = $this->period_search_setting();

        if ($this->form_validation->run() == false) {
            $this->load->model('Analysis');
            $this->Analysis->start_date = $setting_data['default_start_date'];
            $this->Analysis->end_date = $setting_data['default_end_date'];
            $data = $this->Analysis->get_index();

            $this->return_data['data'] = $data;
            $this->return_data['data']['year'] = $setting_data['year'];
            $this->return_data['data']['month'] = $setting_data['month'];
            $this->return_data['data']['type'] = $setting_data['type'];
            $this->return_data['data']['date_obj'] = $setting_data['date_obj'];
            $this->return_data['data']['date_p'] = $setting_data['date_p'];
            $this->return_data['data']['default_start_date'] = $setting_data['default_start_date'];
            $this->return_data['data']['default_end_date'] = $setting_data['default_end_date'];
        } else {
            $this->load->model('Analysis');
            $this->Analysis->start_date = $setting_data['start_date'];
            $this->Analysis->end_date = $setting_data['end_date'];
            $data = $this->Analysis->get_index();

            $this->return_data['data'] = $data;
            $this->return_data['data']['year'] = $setting_data['year'];
            $this->return_data['data']['month'] = $setting_data['month'];
            $this->return_data['data']['type'] = $setting_data['type'];
            $this->return_data['data']['date_obj'] = $setting_data['date_obj'];
            $this->return_data['data']['date_p'] = $setting_data['date_p'];
            $this->return_data['data']['start_date'] = $setting_data['start_date'];
            $this->return_data['data']['end_date'] = $setting_data['end_date'];

            if ($setting_data['type'] == 'period') {
            } else {
                $this->return_data['data']['prev_year'] = $setting_data['prev_year'];
                $this->return_data['data']['next_year'] = $setting_data['next_year'];
                $this->return_data['data']['prev_month'] = $setting_data['prev_month'];
                $this->return_data['data']['next_month'] = $setting_data['next_month'];
            }
        }
        $this->render_format();
    }
}

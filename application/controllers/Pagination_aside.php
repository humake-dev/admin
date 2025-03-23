<?php

trait Pagination_aside
{
    protected function setting_pagination(array $config)
    {
        $this->load->library('pagination');

        if (empty($config['per_page'])) {
            $config['per_page'] = $this->per_page;
        }

        if (empty($config['base_url'])) {
            $config['base_url'] = base_url() . $this->router->fetch_class();
        }

        $config['page_query_string'] = true;
        $config['use_page_numbers'] = true;
        $config['query_string_segment'] = 'page';

        $query_string = $this->input->get();
        if (isset($query_string['page'])) {
            if (intval($query_string['page']) > 99) {
                $config['first_link'] = '&lt;&lt;';
                $config['last_link'] = '&gt;&gt;';
            } else {
                $config['first_link'] = _('First');
                $config['last_link'] = _('Last');
            }
            unset($query_string['page']);
        } else {
            $config['first_link'] = _('First');
            $config['last_link'] = _('Last');
        }

        if (count($query_string) > 0) {
            $config['suffix'] = '&' . http_build_query($query_string, '', "&");
            $config['first_url'] = $config['base_url'] . '?' . http_build_query($query_string, '', "&");
        }

        $config['num_links'] = 2;
        $config['full_tag_open'] = '<ul class="pagination justify-content-center">';
        $config['full_tag_close'] = '</ul>';

        $config['first_tag_open'] = '<li class="prev page-item">';
        $config['first_tag_close'] = '</li>';

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
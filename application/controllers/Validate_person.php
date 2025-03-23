<?php

trait Validate_person
{
    /**
     * 동일 카드번호 제한.
     */
    public function unique_card_no($card_no, $id = null)
    {
        $this->load->model('User');
        $this->load->model('Employee');

        $automake = false;
        if (empty($card_no)) { // 카드번호가 없으면 만들어 넣는다
            $s_card_no = $this->create_card_no($this->input->post('phone'));
            $automake = true;
        } else {
            $s_card_no = $card_no;
        }

        $result = true;

        if ($this->Employee->exists_unique_card_no($s_card_no, $id)) {
            $result = false;
        }

        if ($this->User->exists_unique_card_no($s_card_no, $id)) {
            $result = false;
        }

        if (empty($result)) {
            if ($automake) {
                $this->form_validation->set_message('unique_card_no', sprintf(_('Your Card No %s(Automake), Not Unique, Change Phone Number Or Insert Card No'), $s_card_no));
            } else {
                $this->form_validation->set_message('unique_card_no', sprintf(_('Your Card No %s, Not Unique, Change Card No OR Change Other Same Card No'), $s_card_no));
            }
        }

        return $result;
    }

    public function create_card_no($phone = null, $dec_only = false, $branch_id = null)
    {
        if (empty($phone)) {
            if ($dec_only) {
                $base_str = hexdec(uniqid());
            } else {
                $base_str = uniqid();
            }
        } else {
            $base_str = $this->create_valid_phone($phone);
        }

        if (empty($branch_id)) {
            $branch_id = $this->session->userdata('branch_id');
        }

        $branch_len = strlen($branch_id);

        if ($branch_len < 2) {
            $pre_c = '0' . $branch_id;
            $branch_len = 2;
        } else {
            $pre_c = $branch_id;
        }

        $card_no = $pre_c . substr('0000000000' . str_replace('-', '', $base_str), -(10 - $branch_len));

        return $card_no;
    }

    public function numeric_dash($num)
    {
        if (empty(trim($num))) {
            return true;
        }

        return (!preg_match("/^([0-9-\s])+$/D", $num)) ? FALSE : TRUE;
    }

    public function create_valid_phone($phone)
    {
        $phone = trim($phone);

        if (empty($phone)) {
            return null;
        }

        if (strlen($phone) < 2) {
            return null;
        }

        $phone = str_replace('-', '', $phone);
        $phone = str_replace('.', '', $phone);
        $phone = str_replace('_', '', $phone);

        return str_replace(' ', '', $phone);
    }
}

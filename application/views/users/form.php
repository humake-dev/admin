<?php

$additional_params = array();

if ($this->router->fetch_method() == 'add') {
    $f_action = 'users/add';
    $form_id = 'user_add_form';
    echo '<h2>신규회원 등록</h2>';
} else {
    $form_id = 'user_edit_form';
    if ($this->input->get('page')) {
        $f_action = 'users/edit/' . $data['content']['id'] . '?page=' . $this->input->get('page');
    } else {
        $f_action = 'users/edit/' . $data['content']['id'];
    }

    $additional_params['return_url'] = '/view/' . $data['content']['id'];

    if (!empty($params)) {
        $additional_params['return_url'] .= $params;
    }
}

if (!empty($data['temp_user_id'])) {
    $additional_params['temp_user_id'] = $data['temp_user_id'];
}

echo form_open($f_action, array('id' => $form_id, 'class' => 'user_form user_content_section'), $additional_params);

?>
<div class="card">
    <div class="card-body">
        <div class="row">
            <div class="col-12 col-md-6 col-xl-4 form-group">
                <label for="u_name"><?php echo _('Name'); ?>*</label>
                <?php

                if (isset($data['content']['name'])) {
                    $value = set_value('name', $data['content']['name']);
                } else {
                    $value = set_value('name');
                }

                echo form_input(array(
                    'name' => 'name',
                    'id' => 'u_name',
                    'value' => $value,
                    'maxlength' => '60',
                    'size' => '60',
                    'class' => 'form-control',
                    'required' => 'required',
                ));
                ?>
            </div>
            <?php if (!empty($common_data['branch']['use_access_card'])): ?>
                <div class="col-12 col-md-6 col-xl-4 form-group">
                    <label for="u_card_no"><?php echo _('Access Card No'); ?></label>
                    <?php
                    $card_value = set_value('card_no');

                    if (!$card_value) {
                        if (isset($data['content']['card_no'])) {
                            $card_value = $data['content']['card_no'];
                        }
                    }

                    echo form_input(array(
                        'name' => 'card_no',
                        'id' => 'u_card_no',
                        'value' => $card_value,
                        'maxlength' => '60',
                        'size' => '60',
                        'placeholder' => _('If Not Insert, Auto Generation'),
                        'class' => 'form-control',
                    ));
                    ?>
                </div>
            <?php endif; ?>
            <div class="col-12 col-md-6 col-xl-4 form-group">
                <label for="u_phone_number"><?php echo _('Phone'); ?></label>
                <?php

                $phone_value = set_value('phone');

                if ($phone_value) {
                    get_hyphen_phone($phone_value);
                } else {
                    if (!empty($data['content']['phone'])) {
                        $phone_value = get_hyphen_phone($data['content']['phone']);
                    }
                }

                echo form_input(array(
                    'name' => 'phone',
                    'id' => 'u_phone_number',
                    'value' => $phone_value,
                    'maxlength' => '20',
                    'size' => '20',
                    'class' => 'form-control',
                ));
                ?>
            </div>
            <div class="col-12 col-lg-6 col-xl-4 form-group">
                <div class="form-row">
                    <?php if (isset($data['content'])): ?>
                    <div class="col-12 col-lg-6 form-group">
                        <?php else: ?>
                        <div class="col-12 col-lg-4 form-group">
                            <?php endif; ?>
                            <label for="u_card_no"><?php echo _('Gender'); ?></label>
                            <div class="form-row">
                                <div class="col-12">
                                    <div class="form-check form-check-inline">

                                        <?php
                                        $m_checked = false;
                                        if (isset($data['content']['gender'])) {
                                            if ($data['content']['gender'] == 1) {
                                                $m_checked = true;
                                            }
                                        }

                                        echo form_radio(array(
                                            'name' => 'gender',
                                            'id' => 'u_male',
                                            'value' => '1',
                                            'checked' => $m_checked,
                                            'class' => 'form-check-input',
                                        ));
                                        ?>
                                        <label for="u_male" class="form-check-label"><?php echo _('Male'); ?></label>
                                    </div>
                                    <div class="form-check form-check-inline">

                                        <?php
                                        $f_checked = false;
                                        if (isset($data['content']['gender'])) {
                                            if ($data['content']['gender'] == 0) {
                                                $f_checked = true;
                                            }
                                        }

                                        echo form_radio(array(
                                            'name' => 'gender',
                                            'id' => 'u_female',
                                            'value' => '0',
                                            'checked' => $f_checked,
                                            'class' => 'form-check-input',
                                        ));
                                        ?>
                                        <label for="u_female" class="form-check-label"><?php echo _('Female'); ?></label>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-12 <?php if (isset($data['content'])): ?>col-lg-6<?php else: ?>col-lg-8<?php endif; ?> form-group">
                            <label for="u_birthday"><?php echo _('Birthday'); ?></label>
                            <div class="input-group-prepend date">
                                <?php

                                $birthday_value = set_value('birthday');

                                if (!$birthday_value) {
                                    if (isset($data['content']['birthday'])) {
                                        $birthday_value = $data['content']['birthday'];
                                    }
                                }

                                echo form_input(array(
                                    'name' => 'birthday',
                                    'id' => 'u_birthday',
                                    'value' => $birthday_value,
                                    'maxlength' => '20',
                                    'size' => '20',
                                    'class' => 'form-control birthday-datepicker',
                                ));
                                ?>
                                <div class="input-group-text">
                                    <span class="material-icons">date_range</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <?php if($this->router->fetch_method() == 'add' or $this->Acl->has_permission('employees')): ?>
                <?php if ($data['fc']['total']): ?>
                    <div class="col-12 col-md-6 col-xl-4 form-group">
                        <label for="fc"><?php echo _('User FC'); ?></label>
                        <?php
                        $select = set_value('fc_id', '');
                        if (isset($data['content']['fc_id'])) {
                            $options = array('' => _('Not Inserted'));
                        } else {
                            $options = array('' => _('Please Select'));
                        }

                        if ($data['fc']['total']) {
                            foreach ($data['fc']['list'] as $value) {
                                $options[$value['id']] = $value['name'];
                            }
                        }

                        if (!$select) {
                            if (isset($data['content']['fc_id'])) {
                                $select = $data['content']['fc_id'];
                            }
                        }
                        echo form_dropdown('fc_id', $options, $select, array('class' => 'form-control'));
                        ?>
                    </div>
                <?php endif; ?>
                <?php if ($data['trainer']['total']): ?>
                    <div class="col-12 col-md-6 col-xl-4 form-group">
                        <label for="trainer"><?php echo _('User Trainer'); ?></label>
                        <?php
                        $select = '';
                        if (isset($data['content']['trainer_id'])) {
                            $options = array('' => _('Not Inserted'));
                        } else {
                            $options = array('' => _('Please Select'));
                        }

                        if ($data['trainer']['total']) {
                            foreach ($data['trainer']['list'] as $value) {
                                $options[$value['id']] = $value['name'];
                            }
                        }

                        if (!$select) {
                            if (isset($data['content']['trainer_id'])) {
                                $select = $data['content']['trainer_id'];
                            }
                        }
                        echo form_dropdown('trainer_id', $options, $select, array('class' => 'form-control'));
                        ?>
                    </div>                
                <?php endif; ?>
                <?php else: ?>
                <?php 
                if(!empty($data['content']['trainer_id'])) {
                    echo form_input(array(
                    'type'=>'hidden',
                    'name' => 'trainer_id',
                    'value' => $data['content']['trainer_id']
                ));
                }
                if(!empty($data['content']['fc_id'])) {
                    echo form_input(array(
                    'type'=>'hidden',
                    'name' => 'fc_id',
                    'value' => $data['content']['fc_id']
                ));
                }
                
                ?>
                <?php endif; ?>
                <div class="col-12 col-md-6 col-xl-4 form-group">
                    <label for="u_visit_route"><?php echo _('Visit Route'); ?></label>
                    <?php
                    
                    $visit_route_value = set_value('visit_route');

                    if (!$visit_route_value) {
                        if (isset($data['content']['visit_route'])) {
                            $visit_route_value = $data['content']['visit_route'];
                        }
                    }

                    echo form_input(array(
                        'name' => 'visit_route',
                        'id' => 'u_visit_route',
                        'value' => $visit_route_value,
                        'maxlength' => '120',
                        'size' => '55',
                        'class' => 'form-control'
                    ));
                    ?>
                </div>
                <div class="col-12 col-md-6 col-xl-4 form-group">
                    <label for="u_job"><?php echo _('Job'); ?></label>
                    <?php
                    $options = array('' => _('Please Select'));

                    if ($data['job']['total']) {
                        foreach ($data['job']['list'] as $job) {
                            $options[$job['id']] = $job['title'];
                        }
                    }

                    $select='';
                    if (isset($data['content']['job_id'])) {
                        $select = set_value('job', $data['content']['job_id']);
                    }
                    echo form_dropdown('job_id', $options, $select, array('id' => 'u_job', 'class' => 'form-control'));
                    ?>
                </div>
                <div class="col-12 col-md-6 col-xl-4 form-group">
                    <label for="u_company"><?php echo _('Company'); ?></label>
                    <?php

                    $company_value = set_value('company');

                    if (!$company_value) {
                        if (isset($data['content']['company'])) {
                            $company_value = $data['content']['company'];
                        }
                    }

                    echo form_input(array(
                        'name' => 'company',
                        'id' => 'u_company',
                        'value' => $company_value,
                        'maxlength' => '120',
                        'size' => '55',
                        'class' => 'form-control',
                    ));
                    ?>
                </div>


                <div class="col-12 col-md-6 col-xl-4 form-group">
                    <label for="u_registration_date"><?php echo _('Registration Date'); ?></label>
                    <div class="input-group-prepend date">
                        <?php

                        $reg_date_value = set_value('registration_date');

                        if (!$reg_date_value) {
                            if (isset($data['content']['registration_date'])) {
                                $reg_date_value = $data['content']['registration_date'];
                            } else {
                                $reg_date_value = $search_data['date'];
                            }
                        }

                        echo form_input(array(
                            'name' => 'registration_date',
                            'id' => 'u_registration_date',
                            'value' => $reg_date_value,
                            'maxlength' => '20',
                            'size' => '20',
                            'class' => 'form-control datepicker',
                        ));
                        ?>
                        <div class="input-group-text">
                            <span class="material-icons">date_range</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php echo form_submit('', _('Submit'), array('class' => 'btn btn-lg btn-primary btn-block')); ?>
    <?php echo form_close(); ?>

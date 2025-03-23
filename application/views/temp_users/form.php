<?php

if ($this->router->fetch_method() == 'add') {
    $f_action = '/temp-users/add';
    $form_id = 'temp_user_add_form';
} else {
    $form_id = 'temp_user_edit_form';
    if ($this->input->get('page')) {
        $f_action = '/temp-users/edit/'.$data['content']['id'].'?page='.$this->input->get('page');
    } else {
        $f_action = '/temp-users/edit/'.$data['content']['id'];
    }
}
?>
<?php echo form_open($f_action, array('id' => $form_id, 'class' => 'user_form user_content_section')); ?>
  <div class="card">
  <?php if ($this->router->fetch_method() == 'edit'): ?>
  <input type="hidden" name="return_url" value="/temp-users/view/<?php echo $data['content']['id'].$params; ?>" />
  <?php endif; ?>

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
      
     
          <div class="col-12 col-md-6 col-xl-4 form-group">
                <label for="u_email"><?php echo _('Email'); ?></label>
                <?php

                $email_value = set_value('email');

                if (!$email_value) {
                    if (isset($data['content']['email'])) {
                        $email_value = $data['content']['email'];
                    }
                }

                echo form_input(array(
                        'type' => 'email',
                        'name' => 'email',
                        'id' => 'u_email',
                        'value' => $email_value,
                        'maxlength' => '50',
                        'size' => '25',
                        'class' => 'form-control',
                ));
                ?>
            </div>

            <div class="form-group col-12 col-md-6 col-xl-4">
                <label for=""><?php echo _('Birthday Type'); ?></label>
                <div class="form-row">
                  <div class="col-12">
                    <div class="form-check form-check-inline">
                      <label class="form-check-label">
                        <?php
                        $checked = true;
                        if (isset($data['content']['birthday_type'])) {
                            if ($data['content']['birthday_type'] == 'S') {
                                $checked = true;
                            } else {
                                $checked = false;
                            }
                        }

                        echo form_radio(array(
                                'name' => 'birthday_type',
                                'id' => 'birthday_type',
                                'value' => 'S',
                                'checked' => $checked,
                                'class' => 'form-check-input',
                        ));
                        ?>
                        <?php echo _('the solar calendar'); ?>
                      </label>
                    </div>
                    <div class="form-check form-check-inline">
                      <label class="form-check-label">
                        <?php
                        $checked = false;
                        if (isset($data['content']['birthday_type'])) {
                            if ($data['content']['birthday_type'] == 'L') {
                                $checked = true;
                            } else {
                                $checked = false;
                            }
                        }

                        echo form_radio(array(
                                'name' => 'birthday_type',
                                'id' => 'birthday_type',
                                'value' => 'L',
                                'checked' => $checked,
                                'class' => 'form-check-input',
                        ));
                        ?>
                        <?php echo _('the lunar calendar'); ?>
                      </label>
                    </div>
                  </div>
                </div>
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

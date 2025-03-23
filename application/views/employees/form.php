<?php

if ($this->router->fetch_method() == 'add') {
    $f_action = 'employees/add';
} else {
    $f_action = 'employees/edit/'.$data['content']['id'];
}
?>
<?php echo form_open($f_action); ?>
  <div class="card employee_content_section">
    <?php if ($this->router->fetch_method() != 'add'): ?>
    <div class="card-header">
      <ul class="nav nav-pills card-header-pills">
        <li class="nav-item"><a class="nav-link<?php if ($this->session->userdata('employee_open')): ?><?php if ($this->session->userdata('employee_open') == 'default'): ?> active<?php endif; ?><?php else: ?> active<?php endif; ?>" href="#"><?php echo _('Default Info'); ?></a></li>
        <li class="nav-item"><a class="nav-link<?php if ($this->session->userdata('employee_open')): ?><?php if ($this->session->userdata('employee_open') == 'permission'): ?> active<?php endif; ?><?php endif; ?>"  href="#"><?php echo _('Permission Info'); ?></a></li>
        <?php if ($common_data['branch']['use_admin_ac']): ?>      
        <li class="nav-item"><a class="nav-link<?php if ($this->session->userdata('employee_open')): ?><?php if ($this->session->userdata('employee_open') == 'access-control'): ?> active<?php endif; ?><?php endif; ?>"  href="#"><?php echo _('Access Control Info'); ?></a></li>   
        <?php endif; ?>
      </ul>
      <div class="float-right buttons">
        <i class="material-icons">keyboard_arrow_up</i>
      </div>
    </div>
    <?php endif; ?>
    <div class="card-body">
    <div class="card-block"<?php if ($this->session->userdata('employee_open')): ?><?php if ($this->session->userdata('employee_open') != 'default'): ?> style="display:none"<?php endif; ?><?php endif; ?>>
      <div class="row">
          <div class="col-12 col-md-6 col-xl-4 form-group">
          <label><?php echo _('Name'); ?>*</label>
            <?php
              $name_value = set_value('name');

              if (!$name_value) {
                  if (isset($data['content']['name'])) {
                      $name_value = $data['content']['name'];
                  }
              }

          echo form_input(array(
              'name' => 'name',
              'id' => 'e_name',
              'value' => $name_value,
              'maxlength' => '60',
              'size' => '60',
              'required' => 'required',
              'class' => 'form-control',
          ));
      ?>
      </div>
      <div class="col-12 col-md-6 col-xl-4 form-group">
        <label for="e_uid"><?php echo _('uid'); ?>*</label>
        <?php
          $id_value = set_value('uid');

          if (!$id_value) {
              if (isset($data['content']['uid'])) {
                  $id_value = $data['content']['uid'];
              }
          }

          echo form_input(array(
              'name' => 'uid',
              'id' => 'e_uid',
              'value' => $id_value,
              'maxlength' => '60',
              'size' => '60',
              'required' => 'required',
              'class' => 'form-control',
          ));
      ?>
      </div>
      <?php if ($this->router->fetch_method() == 'add'): ?>
        <div class="col-12 col-md-6 col-xl-4 form-group">
          <label for="e_password"><?php echo _('Password'); ?>*</label>
          <?php

            echo form_input(array(
                'type' => 'password',
                'name' => 'password',
                'id' => 'e_password',
                'maxlength' => '60',
                'size' => '60',
                'required' => 'required',
                'class' => 'form-control',
            ));
        ?>
        </div>
      <?php endif; ?>
      <div class="col-12 col-md-6 col-xl-4 form-group">
        <label for="e_phone"><?php echo _('Phone'); ?></label>
        <?php
            $phone_value = set_value('phone');

            if (!$phone_value) {
                if (isset($data['content']['phone'])) {
                    $phone_value = get_hyphen_phone($data['content']['phone']);
                }
            }

            echo form_input(array(
                'name' => 'phone',
                'id' => 'e_phone',
                'value' => $phone_value,
                'maxlength' => '60',
                'size' => '60',
                'class' => 'form-control',
            ));
        ?>
              <?php
                $default_sse_checked = 0;

                if (!empty($data['content']['enable_send_message'])) {
                    $default_sse_checked = 1;
                }

                $sse_checked = set_value('enable_send_message', $default_sse_checked);

              ?>
              <div class="form-check">
                <label class="form-check-label">
                <?php
                  echo form_checkbox(array(
                    'name' => 'enable_send_message',
                    'id' => 'enable_send_message',
                    'value' => 1,
                    'checked' => $sse_checked,
                    'class' => 'form-check-input',
                    ));
                ?>
            <?php echo _('Sms Send Enable'); ?>
            </label>
              </div>   
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
                                        <label for="u_female"
                                               class="form-check-label"><?php echo _('Female'); ?></label>
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

      <div class="col-12 col-md-6 col-xl-4 form-group">
        <label for="e_role"><?php echo _('Role'); ?></label>
        <?php

          $options = array();
          $select = set_value('role', 5);
          if ($data['role']['total']) {
              foreach ($data['role']['list'] as $role) {
                  $options[$role['id']] = $role['title'];
              }
          }

          if (isset($data['content']['role_id'])) {
              $select = $data['content']['role_id'];
          }
          echo form_dropdown('role', $options, $select, array('id' => 'e_role', 'class' => 'form-control'));
        ?>
      </div>
      <div class="col-12 col-md-6 col-xl-4 form-group">
        <label for=""><?php echo _('Employee Position'); ?></label>
        <div class="form-row">
          <div class="col-12">
            <div class="form-check form-check-inline">
              <label class="form-check-label">
                <?php
                  $default_trainer_checked = false;
                    if (isset($data['content']['is_trainer'])) {
                        if ($data['content']['is_trainer']) {
                            $default_trainer_checked = true;
                        }
                    } else {
                        $default_trainer_checked = true;
                    }

                    $trainer_checked = set_checkbox('is_trainer', 1, $default_trainer_checked);

                    echo form_checkbox(array(
                            'name' => 'is_trainer',
                            'value' => '1',
                            'checked' => $trainer_checked,
                            'class' => 'form-check-input',
                    ));
                    ?> <?php echo _('Trainer'); ?>
                  </label>
                </div>
                <div class="form-check form-check-inline">
                  <label class="form-check-label">
                    <?php
                      $default_fc_checked = false;
                    if (isset($data['content']['is_fc'])) {
                        if ($data['content']['is_fc']) {
                            $default_fc_checked = true;
                        }
                    }

                    $fc_checked = set_checkbox('is_fc', 1, $default_fc_checked);

                    echo form_checkbox(array(
                            'name' => 'is_fc',
                            'value' => '1',
                            'checked' => $fc_checked,
                            'class' => 'form-check-input',
                    ));
                    ?> FC
                  </label>
                </div>
                </div>
              </div>
            </div>
            <div class="col-12 col-md-6 col-xl-4 form-group">
                <label for=""><?php echo _('Status') ?></label>
                <div class="form-row">
                  <div class="col-12">
                    <div class="form-check form-check-inline">
                      <label class="form-check-label">
                        <?php
                          $m_checked = false;
                        if (isset($data['content']['status'])) {
                            if ($data['content']['status'] == 'H') {
                                $m_checked = true;
                            }
                        } else {
                            $m_checked = true;
                        }

                        echo form_radio(array(
                                'name' => 'status',
                                'value' => 'H',
                                'checked' => $m_checked,
                                'class' => 'form-check-input',
                        ));
                        ?>
                        <?php echo get_employee_status('H'); ?>
                      </label>
                    </div>
                    <div class="form-check form-check-inline">
                      <label class="form-check-label">
                        <?php
                          $m_checked = false;
                        if (isset($data['content']['status'])) {
                            if ($data['content']['status'] == 'R') {
                                $m_checked = true;
                            }
                        } else {
                            $m_checked = set_radio('status', 'R');
                        }

                        echo form_radio(array(
                                'name' => 'status',
                                'value' => 'R',
                                'checked' => $m_checked,
                                'class' => 'form-check-input',
                        ));
                        ?>
                        <?php echo get_employee_status('R'); ?>
                      </label>
                    </div>
                    <!-- <div class="form-check form-check-inline">
                      <label class="form-check-label">
                        <?php
                          $m_checked = false;
                        if (isset($data['content']['status'])) {
                            if ($data['content']['status'] == 'L') {
                                $m_checked = true;
                            }
                        } else {
                            $m_checked = set_radio('status', 'L');
                        }

                        echo form_radio(array(
                                'name' => 'status',
                                'value' => 'L',
                                'checked' => $m_checked,
                                'class' => 'form-check-input',
                        ));
                        ?>
                        <?php echo get_employee_status('L'); ?>
                      </label>
                    </div> -->
                  </div>
                </div>
            </div>
            <div class="col-12 col-md-6 col-xl-4 form-group">
                <label for="e_commission_rate"><?php echo _('Commission'); ?>(%)</label>
                <?php
                    $commission_value = set_value('commission_rate');

                    if (!$commission_value) {
                        if (isset($data['content']['commission_rate'])) {
                            $commission_value = $data['content']['commission_rate'];
                        } else {
                            $commission_value = 0;
                        }
                    }

                    echo form_input(array(
                        'type' => 'number',
                        'name' => 'commission_rate',
                        'id' => 'e_commission_rate',
                        'value' => $commission_value,
                        'max' => 100,
                        'min' => 0,
                        'class' => 'form-control',
                    ));
                ?>
            </div>
            <div class="col-12 col-md-6 col-xl-4 form-group">
          <label for="e_email"><?php echo _('Email'); ?></label>
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
                  'id' => 'e_email',
                  'value' => $email_value,
                  'maxlength' => '120',
                  'size' => '60',
                  'class' => 'form-control',
              ));
          ?>
      </div>
            <div class="col-12 col-md-6 col-xl-4 form-group">
                <label for="e_created_at"><?php echo _('Hiring Date'); ?></label>
                <div class="input-group-prepend date">
                  <?php

                  $hiring_value = set_value('hiring_date');

                  if (!$hiring_value) {
                      if (isset($data['content']['hiring_date'])) {
                          $hiring_value = $data['content']['hiring_date'];
                      } else {
                          $hiring_value = $search_data['date'];
                      }
                  }

                  echo form_input(array(
                          'name' => 'hiring_date',
                          'id' => 'e_hiring_date',
                          'value' => $hiring_value,
                          'maxlength' => '20',
                          'size' => '20',
                          'class' => 'datepicker form-control',
                  ));
                  ?>
                    <div class="input-group-text">
                        <span class="material-icons">date_range</span>
                    </div>
                </div>
            </div>
            






           </div>
           </div>
           <?php if ($this->router->fetch_method() != 'add'): ?>
           <div class="card-block"<?php if ($this->session->userdata('employee_open')): ?><?php if ($this->session->userdata('employee_open') != 'permission'): ?> style="display:none"<?php endif; ?><?php else: ?> style="display:none"<?php endif; ?>>
              <div class="row">
             <?php if ($data['permission']['total']): ?>
             <?php foreach ($data['permission']['list'] as $permission): ?>
             <div class="col-12 col-md-6 col-xl-4 form-group">
               <div class="card permission_detail">
                 <div class="card-header">
                   <label><?php echo $permission['title']; ?></label><br />
                   <input type="hidden" name="admin_permission[<?php echo $permission['id']; ?>][id]" value="<?php echo $permission['id']; ?>" />
                   <input type="hidden" name="admin_permission[<?php echo $permission['id']; ?>][controller]" value="<?php echo $permission['controller']; ?>" />
                   <input type="hidden" name="admin_permission[<?php echo $permission['id']; ?>][action]" value="<?php echo $permission['action']; ?>" />
                   <div class="form-check form-check-inline">
                     <label class="form-check-label">
                       <?php
                         $m_checked = false;
                         $m_checked = $this->Acl->has_permission($permission['controller'], $permission['action'], $data['content']['role_id'], $data['content']['id']);

                         echo form_radio(array(
                               'name' => 'admin_permission['.$permission['id'].'][deny]',
                               'value' => '0',
                               'checked' => $m_checked,
                               'class' => 'form-check-input',
                       ));
                       ?><?php echo _('Allow'); ?>
                     </label>
                   </div>
                   <div class="form-check form-check-inline">
                     <label class="form-check-label">
                       <?php

                       if ($m_checked) {
                           $m_deny_checked = false;
                       } else {
                           $m_deny_checked = true;
                       }

                       echo form_radio(array(
                               'name' => 'admin_permission['.$permission['id'].'][deny]',
                               'value' => '1',
                               'checked' => $m_deny_checked,
                               'class' => 'form-check-input',
                       ));
                       ?><?php echo _('Deny'); ?>
                     </label>
                   </div>
                   <?php if ($permission['detail_list']['total']): ?>
                   <div class="float-right buttons">
                     <i class="material-icons no_common">keyboard_arrow_down</i>
                   </div>
                   <?php endif ?>
                 </div>
                <?php if ($permission['detail_list']['total']): ?>
                 <div class="card-body no_common" style="display:none">
                  <?php foreach ($permission['detail_list']['list'] as $permission_detail): ?>
                     <input type="hidden" name="admin_permission[<?php echo $permission_detail['id']; ?>][id]" value="<?php echo $permission_detail['id']; ?>" />
                     <input type="hidden" name="admin_permission[<?php echo $permission_detail['id']; ?>][controller]" value="<?php echo $permission_detail['controller']; ?>" />
                     <input type="hidden" name="admin_permission[<?php echo $permission_detail['id']; ?>][action]" value="<?php echo $permission_detail['action']; ?>" />
                  <div class="no_common">
                     <label><?php echo $permission_detail['title']; ?></label><br />
                 <div class="form-check form-check-inline">
                   <label class="form-check-label">
                     <?php
                       $m_checked = false;
                       $m_checked = $this->Acl->has_permission($permission_detail['controller'], $permission_detail['action'], $data['content']['role_id'], $data['content']['id']);

                       echo form_radio(array(
                             'name' => 'admin_permission['.$permission_detail['id'].'][deny]',
                             'value' => '0',
                             'checked' => $m_checked,
                             'class' => 'form-check-input',
                     ));
                     ?><?php echo _('Allow'); ?>
                   </label>
                 </div>
                 <div class="form-check form-check-inline">
                   <label class="form-check-label">
                     <?php

                     if ($m_checked) {
                         $m_deny_checked = false;
                     } else {
                         $m_deny_checked = true;
                     }

                     echo form_radio(array(
                             'name' => 'admin_permission['.$permission_detail['id'].'][deny]',
                             'value' => '1',
                             'checked' => $m_deny_checked,
                             'class' => 'form-check-input',
                     ));
                     ?><?php echo _('Deny'); ?>
                   </label>
                 </div>
                 </div>
                  <?php endforeach; ?>
                 </div>
                <?php endif; ?>
               </div>
             </div>
            <?php endforeach; ?>
            <?php endif; ?>
             </div>
           </div>
          <?php endif; ?>

          <?php if (isset($common_data['branch'])): ?>
          <?php if ($common_data['branch']['use_admin_ac']): ?>    
          <div class="card-block"<?php if ($this->session->userdata('employee_open')): ?><?php if ($this->session->userdata('employee_open') != 'access-control'): ?> style="display:none"<?php endif; ?><?php else: ?> style="display:none"<?php endif; ?>>
            <div class="row">
             <div class="col-12 col-md-6 col-xl-4 form-group">
                <label for="e_card_no"><?php echo _('admin_card_no'); ?></label>
                <?php

                $card_no_value = set_value('card_no');

                if (!$card_no_value) {
                    if (isset($data['content']['card_no'])) {
                        $card_no_value = $data['content']['card_no'];
                    }
                }

                    echo form_input(array(
                        'name' => 'card_no',
                        'id' => 'e_card_no',
                        'value' => $card_no_value,
                        'maxlength' => '120',
                        'size' => '60',
                        'class' => 'form-control',
                    ));
                ?>
            </div>
              </div>
          </div>
          <?php endif; ?>
          <?php endif; ?>                    
          
       </div>
    </div>
    <?php echo form_submit('', _('Submit'), array('class' => 'btn btn-lg btn-primary btn-block')); ?>    
<?php echo form_close(); ?>

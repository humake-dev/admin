<div class="form-group text-right">
  <?php if (!empty($data['content'])): ?>
    <?php if ($this->session->userdata('branch_id')): ?>
    <?php if ($this->session->userdata('role_id') < 3): ?>
    <?php echo anchor('admins/change-password?employee_id='.$data['content']['id'], _('Change Password'), array('class' => 'btn btn-secondary')); ?>
    <?php endif; ?>
    <?php if ($this->Acl->has_permission('employees', 'write')): ?>
    <?php echo anchor('employees/edit/'.$data['content']['id'], _('Edit'), array('class' => 'btn btn-secondary')); ?>
    <?php endif; ?>
    <?php if ($this->Acl->has_permission('employees', 'write')): ?>
    <?php echo anchor('employees/add', _('Add'), array('class' => 'btn btn-primary')); ?>
    <?php endif; ?>
    <?php if (isset($data['content']['id'])): ?>
      <?php if ($this->Acl->has_permission('employees', 'delete')): ?>
      <?php echo anchor('employees/delete/'.$data['content']['id'], _('Delete'), array('class' => 'btn btn-danger')); ?>
      <?php endif; ?>
    <?php endif; ?>
  <?php endif; ?>
  <?php endif; ?>
</div>
<section class="card employee_content_section">
  <div class="card-header">
    <ul class="nav nav-pills card-header-pills">
      <li class="nav-item"><a class="nav-link<?php if ($this->session->userdata('employee_open')): ?><?php if ($this->session->userdata('employee_open') == 'default'): ?> active<?php endif; ?><?php else: ?> active<?php endif; ?>" href="#"><?php echo _('Default Info'); ?></a></li>
      <li class="nav-item"><a class="nav-link<?php if ($this->session->userdata('employee_open')): ?><?php if ($this->session->userdata('employee_open') == 'permission'): ?> active<?php endif; ?><?php endif; ?>"  href="#"><?php echo _('Permission Info'); ?></a></li>
      <?php if ($this->session->userdata('branch_id')): ?>
      <?php if ($common_data['branch']['use_admin_ac']): ?>
      <li class="nav-item"><a class="nav-link<?php if ($this->session->userdata('employee_open')): ?><?php if ($this->session->userdata('employee_open') == 'access-control'): ?> active<?php endif; ?><?php endif; ?>"  href="#"><?php echo _('Access Control Info'); ?></a></li>   
      <?php endif; ?>
      <?php endif; ?>
    </ul>
    <div class="float-right buttons">
      <i class="material-icons">keyboard_arrow_up</i>
    </div>
  </div>
  <div class="card-body">
    <div class="card-block"<?php if ($this->session->userdata('employee_open')): ?><?php if ($this->session->userdata('employee_open') != 'default'): ?> style="display:none"<?php endif; ?><?php endif; ?>>
      <div class="row">
        <div class="col-12 col-md-6 col-xl-4 form-group">
          <label><?php echo _('Name'); ?></label>
          <p><?php echo $data['content']['name']; ?></p>
        </div>
        <div class="col-12 col-md-6 col-xl-4 form-group">
          <label><?php echo _('uid'); ?></label>
          <p><?php echo $data['content']['uid']; ?></p>
        </div>
        <div class="col-12 col-md-6 col-xl-4 form-group">
          <label><?php echo _('Phone'); ?></label>
          <?php
            $phone_value = _('Not Inserted');
            if (!empty($data['content']['phone'])) {
                $phone_value = get_hyphen_phone($data['content']['phone']);
            }
          ?>
          <p><?php echo $phone_value; ?></p>
        </div>
        <div class="col-12 col-md-6 col-xl-4 form-group">
          <label><?php echo _('Email'); ?></label>
          <?php
                $email_value = _('Not Inserted');

                if (isset($data['content']['email'])) {
                    $email_value = $data['content']['email'];
                }
              ?>
              <p><?php echo $email_value; ?></p>
        </div>
        <div class="col-12 col-md-6 col-xl-4 form-group">
          <div class="form-row">
            <div class="col-12 col-sm-6 form-group">
              <label><?php echo _('Gender'); ?></label>
              <?php
                $gender_value = _('Not Inserted');

                if (isset($data['content']['gender'])) {
                    if ($data['content']['gender'] == 1) {
                        $gender_value = _('Male');
                    } else {
                        $gender_value = _('Female');
                    }
                }
              ?>
              <p><?php echo $gender_value; ?></p>
            </div>
            <div class="col-12 col-sm-6 form-group">
              <label><?php echo _('Age'); ?></label>
              <?php
                $age_value = '생년월일 입력안됨';

                if (!empty($data['content']['birthday'])) {
                    if (valid_date($data['content']['birthday'])) {
                        $age_value = age($data['content']['birthday']).'살';
                    } else {
                        $age_value = '잘못된 생년월일';
                    }
                }
              ?>
              <p><?php echo $age_value; ?></p>
            </div>
          </div>
        </div>
      <div class="col-12 col-md-6 col-xl-4 form-group">
        <label><?php echo _('Role'); ?></label>
        <p><?php echo $data['content']['role_name']; ?></p>
      </div>
      <div class="col-12 col-md-6 col-xl-4 form-group">
        <label><?php echo _('Employee Position'); ?></label>
        <?php

          if (empty($data['content']['is_trainer']) and empty($data['content']['is_fc'])) {
              $value = _('Not Set');
          } else {
              $value = '';

              if (!empty($data['content']['is_trainer'])) {
                  if ($data['content']['is_trainer']) {
                      $value .= _('Trainer').' ';
                  }
              }

              if (!empty($data['content']['is_fc'])) {
                  if ($data['content']['is_fc']) {
                      $value .= _('FC').' ';
                  }
              }
          }

        ?>
        <p><?php echo $value; ?></p>
      </div>
      <div class="col-12 col-md-6 col-xl-4 form-group">
        <label><?php echo _('Status'); ?></label>
        <p><?php echo get_employee_status($data['content']['status'],true); ?></p>
      </div>

      <div class="col-12 col-md-6 col-xl-4 form-group">
        <label><?php echo _('Commission'); ?>(%)</label>
        <?php
          $commission_value = _('Not Inserted');
          if (!empty($data['content']['commission_rate'])) {
              $commission_value = $data['content']['commission_rate'].'%';
          }
        ?>
        <p><?php echo $commission_value; ?></p>
      </div>
      <div class="col-12 col-md-6 col-xl-4 form-group">
        <label><?php echo _('Birthday'); ?></label>
        <?php
          $birthday_value = _('Not Inserted');
          if (!empty($data['content']['birthday'])) {
              $birthday_value = get_dt_format($data['content']['birthday'], $search_data['timezone']);
          }
        ?>
        <p><?php echo $birthday_value; ?></p>
      </div>
      <div class="col-12 col-md-6 col-xl-4 form-group">
        <label><?php echo _('Hiring Date'); ?></label>
        <p><?php echo get_dt_format($data['content']['hiring_date'], $search_data['timezone']); ?></p>
      </div>
      </div>
    </div>
    <div class="card-block"<?php if ($this->session->userdata('employee_open')): ?><?php if ($this->session->userdata('employee_open') != 'permission'): ?> style="display:none"<?php endif; ?><?php else: ?> style="display:none"<?php endif; ?>>
      <div class="row">
        <?php if ($data['permission']['total']): ?>
          <?php foreach ($data['permission']['list'] as $permission): ?>
        <div class="col-12 col-md-6 col-xl-4 form-group">
          <label><?php echo $permission['title']; ?></label>
          <?php
            $m_checked = false;
            $m_checked = $this->Acl->has_permission($permission['controller'], $permission['action'], $data['content']['role_id'], $data['content']['id']);
          ?>
          <p>
          <?php
              if ($m_checked) {
                  echo '<span class="text-success">'._('Allow').'</span>';
              } else {
                  echo '<span class="text-danger">'._('Deny').'</span>';
              }
          ?>
          </p>
        </div>
        <?php endforeach; ?>
        <?php endif; ?>
      </div>
    </div>
    <?php if (isset($common_data['branch'])): ?>
    <?php if ($common_data['branch']['use_admin_ac']): ?>    
    <div class="card-block"<?php if ($this->session->userdata('employee_open')): ?><?php if ($this->session->userdata('employee_open') != 'access-control'): ?> style="display:none"<?php endif; ?><?php else: ?> style="display:none"<?php endif; ?>>
      <div class="row">
        <div class="col-12 col-md-6 col-xl-4 form-group">
          <label><?php echo _('admin_card_no'); ?></label>
          <?php
              $card_no_value = _('Not Inserted');

              if (!empty($data['content']['card_no'])) {
                  $card_no_value = $data['content']['card_no'];
              }
              ?>
              <p><?php echo $card_no_value; ?></p>
        </div>
      </div>
    </div>
    <?php endif; ?>
    <?php endif; ?>
  </div>
</section>

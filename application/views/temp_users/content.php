<?php

if (isset($data['content']['gender'])) {
    if ($data['content']['gender'] == 1) {
        $gender_value = _('Male');
    } else {
        $gender_value = _('Female');
    }
}

if (!empty($data['content']['birthday'])) {
    if (valid_date($data['content']['birthday'])) {
        $age_value = age($data['content']['birthday'])._('Count Age');
    } else {
        $age_value = _('Invalid Birthday');
    }
}

?>
<div class="form-group" style="text-align:right">
  <?php if ($this->session->userdata('branch_id')): ?>
  <?php if (isset($common_data['branch_list'])): ?>
  <?php if ($common_data['branch_list']['total'] > 1): ?>
    <?php if (empty($data['user_transfer_content'])):
      $ut_class = 'btn btn-secondary';
    else:
      $ut_class = 'btn btn-secondary disabled';
    endif; ?>
    <?php echo anchor('/users/add?temp_user_id='.$data['content']['id'].$params, _('Change To Normal User'), array('class' => $ut_class)); ?>
  <?php endif; ?>
  <?php endif; ?>
  
    <?php if ($this->Acl->has_permission('users', 'write')): ?>
    <?php echo anchor('/temp-users/edit/'.$data['content']['id'].$params, _('Edit'), array('class' => 'btn btn-secondary')); ?>
    <?php endif; ?>
    <?php if ($this->Acl->has_permission('users', 'write')): ?>
    <?php echo anchor('/temp-users/add', _('Add'), array('class' => 'btn btn-primary')); ?>
    <?php endif; ?>
    <?php if (isset($data['content']['id'])): ?>
      <?php if ($this->Acl->has_permission('users', 'delete')): ?>
      <?php echo anchor('/temp-users/delete/'.$data['content']['id'], _('Delete'), array('class' => 'btn btn-danger')); ?>
      <?php endif; ?>
    <?php endif; ?>
  <?php endif; ?>
</div>
<section class="card user_content_section">
  <div class="card-body">

    <div class="row">
      <div class="col-12 col-md-6 col-xl-4 form-group">
        <label><?php echo _('Name'); ?></label>
        <p>
          <?php echo $data['content']['name']; ?>
          <?php if (isset($gender_value)): ?>
            / <?php echo $gender_value; ?>
          <?php endif; ?>
        </p>
      </div>


      <div class="col-12 col-md-6 col-xl-4 form-group">
        <label><?php echo _('Phone'); ?></label>
        <p><?php echo get_hyphen_phone($data['content']['phone']); ?></p>
      </div>

      <div class="col-12 col-md-6 col-xl-4 form-group">
                <label><?php echo _('Birthday'); ?></label>
                <?php
                  $birthday_value = _('Not Inserted');
                  if (!empty($data['content']['birthday'])) {
                      $birthday_value = get_dt_format($data['content']['birthday'], $search_data['timezone']);
                  }
                ?>
                <p><?php echo $birthday_value; ?>
                <?php if (isset($age_value)): ?>
             / <?php echo $age_value; ?>
          <?php endif; ?>
                </p>
              </div>

      <?php if ($data['fc']['total']): ?>
      <div class="col-12 col-md-6 col-xl-4 form-group">
        <label><?php echo _('User FC'); ?></label>
        <?php

          $fc_value = _('Not Inserted');
          if (!empty($data['content']['fc_id'])) {
              $fc_value = $data['content']['fc_name'];
          }

        ?>
        <p><?php echo $fc_value; ?></p>
      </div>
      <?php endif; ?>

      <?php if ($data['trainer']['total']): ?>  
      <div class="col-12 col-md-6 col-xl-4 form-group">
        <label><?php echo _('User Trainer'); ?></label>
        <?php
          $trainer_value = _('Not Inserted');
          if (!empty($data['content']['trainer_id'])) {
              $trainer_value = $data['content']['trainer_name'];
          }

        ?>
        <p><?php echo $trainer_value; ?></p>
      </div>
      <?php endif; ?>



      <div class="col-12 col-md-6 col-xl-4 form-group">
        <label><?php echo _('Registed Date'); ?></label>
        <p><?php echo get_dt_format($data['content']['created_at'], $search_data['timezone']); ?></p>
      </div>
      
    </div>
  </div>
</section>

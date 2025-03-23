<?php echo form_open('', array('id' => 'reservation_form')); ?>
<div class="card">
  <div class="card-body">
<?php

if (isset($data['content']['start_time'])) {
    $ss_a = explode(' ', $data['content']['start_time']);
    $start_time = array('date' => $ss_a[0], 'time' => $ss_a[1]);
} else {
    $start_time = array('date' => $search_data['date'], 'time' => $search_data['time']);
}
?>
<?php echo form_input(array('type' => 'hidden', 'id' => 'c_user_id', 'name' => 'user_id','value'=>$this->input->post('user_id'))); ?>
<input type="hidden" name="date" value="<?php echo set_value('date', $start_time['date']); ?>" />
<input type="hidden" name="time" value="<?php echo set_value('time', $start_time['time']); ?>" />
<input type="hidden" id="message_course_empty" value="<?php echo sprintf(_('The %s field is required.'), _('Course')); ?>">
<input type="hidden" id="message_user_empty" value="<?php echo sprintf(_('The %s field is required.'), _('User')); ?>">
<div class="col-12">
<div class="form-row">

<div class="col-12 col-md-6 form-group">
  <label for="r_manager"><?php echo _('Manager'); ?></label>
  <?php

  $default_manager_id = null;
  if (isset($data['content']['manager_id'])) {
      $default_manager_id = $data['content']['manager_id'];
  } else {
      if ($this->session->userdata('role_id') > 5) {
          $default_manager_id = $this->session->userdata('admin_id');
      }
  }

    if ($default_manager_id == $this->session->userdata('admin_id')) {
        $my_manaing_user = _('My Managing User');
        $same = true;
    } else {
        $my_manaing_user = sprintf(_('%s`s Managing User'), $data['admin']['content']['name']);
        $same = false;
    }

  ?>
  <?php if ($same): ?>
  &nbsp; : &nbsp; <label class="form-check-label">
  <?php
    echo form_input(array(
        'type' => 'hidden',
        'name' => 'manager',
        'value' => $this->session->userdata('admin_id'),
        'class' => 'form-check-input',
    ));

    $select_manager_id = set_value('manager', $default_manager_id);
  ?>
  <?php echo _('It`s Me'); ?>(<?php echo $this->session->userdata('admin_name'); ?>)</label>
<?php else: ?>
  <?php
    $options = array('' => _('Select'));

    if ($data['admin']['total']) {
        foreach ($data['admin']['list'] as $value) {
            $options[$value['id']] = $value['name'];

          //  if($this->session->userdata('admin_id')==$value['id']) {
         //     $default_manager_id=$value['id'];
         //   }
        }
    }

    $select_manager_id = set_value('manager', $default_manager_id);

    echo form_dropdown('manager', $options, $select_manager_id, array('id' => 'r_manager', 'class' => 'form-control'));
  ?>
<?php endif; ?>
</div>
<div class="col-12">&nbsp;</div>
  <div class="form-group col-12 col-md-6 col-lg-4">
    <label for="r_type"><?php echo _('Type'); ?></label>
    <?php

      $options = array('' => _('Select'));
      $options = array('PT' => _('PT'), 'FPT' => _('Free PT'), 'OT' => _('OT'), 'Counsel' => _('Counsel'), 'Etc' => _('Etc'));

      if (isset($data['content']['type'])) {
          $select = $data['content']['type'];
      } else {
          $select = set_value('type');
      }

     ?>
    <?php echo form_dropdown('type', $options, $select, array('id' => 'r_type', 'class' => 'form-control')); ?>
  </div>
  <div id="r_course_layer" class="form-group col-12 col-md-4"<?php if (empty($data['content']['enroll_id'])): ?> style="display:none"<?php endif; ?>>
    <label for="r_course"><?php echo _('Course'); ?></label>
    <?php
      // $options = array('' => _('Select'));
        $options = array();
        $select_course_id='';

      if ($data['course']['total']) {
          foreach ($data['course']['list'] as $value) {
              $options[$value['id']] = $value['title'];
          }

          $select_course_id=$data['course']['list'][0]['id'];
      }

      if (isset($data['content']['course_id'])) {
          $select = set_value('course',$data['content']['course_id']);
      } else {
          $select = set_value('course',$select_course_id);
      }

     ?>
    <?php echo form_dropdown('course', $options, $select, array('id' => 'r_course', 'class' => 'form-control')); ?>
  </div>

  <div class="form-group col-12 col-md-4" id="progress_time_layer"<?php if (!empty($data['content']['class']['total'])): ?> style="display:none"<?php endif; ?>>
    <label for="r_progress_time"><?php echo _('Progress Time'); ?>(<?php echo _('Minute'); ?>)</label>
    <?php
      $options = array('10' => '10'._('Minute'), '20' => '20'._('Minute'), '30' => '30'._('Minute'), '40' => '40'._('Minute'), '50' => '50'._('Minute'), '60' => '60'._('Minute'));
      if (isset($data['content']['progress_time'])) {
          $select = $data['content']['progress_time'];
      } else {
          $select = set_value('progress_time', 50);
      }
      echo form_dropdown('progress_time', $options, $select, array('id' => 'r_progress_time', 'class' => 'form-control'));
    ?>
  </div>

  <div class="form-group col-12 col-md-4" id="class_time_layer"<?php if (empty($data['content']['class']['total'])): ?> style="display:none"<?php endif; ?>>
    <label for="r_class_time"><?php echo _('Class Time'); ?></label>
    <?php

    $class_options = array('');

    if (!empty($data['content']['class']['total'])) {
        foreach ($data['content']['class']['list'] as $value) {
            $class_options[$value['id']] = $value['start_time'].'~'.$value['end_time'];
        }

        // $select=$data['content']['class'];
    } else {
        // class_options
    }

    echo form_dropdown('class_id', $class_options, $select, array('id' => 'r_class_time', 'class' => 'form-control'));
    ?>
  </div>


</div>
</div>

<?php if (empty($data['content'])): ?>
<div class="col-12 form-group">
  <label for="r_users"><?php echo _('User'); ?></label>
  <?php

if (empty($data['content']['user_name'])) {
    $default_user_value = '';
} else {
    $default_user_value = $data['content']['user_name'];
}
?>
<div class="input-group-prepend">
    <?php
    echo form_input(array(
        'name' => 'name',
        'id' => 'c_name',
        'value' => set_value('name', $default_user_value),
        'maxlength' => '60',
        'size' => '60',
        'readonly' => 'readonly',
        'required' => 'required',
        'class' => 'form-control',
    ));
    ?>
    <div class="input-group-text r-select-user" title="<?php echo _('Select From User'); ?>">
        <span class="material-icons">account_box</span>
    </div>
</div>
</div>

<?php else: ?>
<?php include __DIR__.DIRECTORY_SEPARATOR.'edit_user.php'; ?>
<?php endif; ?>
<div class="col-12 form-group">
  <label for="r_memo"><?php echo _('Memo'); ?></label>
  <?php

  if (isset($data['content']['content'])) {
      $value = $data['content']['content'];
  } else {
      $value = set_value('content');
  }
  $name_attr = array(
          'id' => 'r_memo',
          'name' => 'content',
          'value' => $value,
          'rows' => '5',
          'class' => 'form-control',
  );

  echo form_textarea($name_attr);
  ?>
</div>
</div>
</div>
<?php echo form_submit('', _('Submit'), array('class' => 'btn btn-lg btn-primary btn-block')); ?>
<?php echo form_close(); ?>


<?php

  if (empty($params)) {
      echo form_open_multipart('/messages/add');
  } else {
      echo form_open_multipart('/messages/add'.$params);
  }

?>
<div class="card">
  <div class="card-body">
  <?php

    echo form_input(array(
      'type' => 'hidden',
      'id' => 'm_type',
      'name' => 'detail_type',
      'value' => set_value('detail_type', 'sms'),
    ));
  ?>
<div class="form-group">
  <label><?php echo _('Message Type'); ?></label>
  <?php if($this->input->get('counsel_search') or $this->input->get('message_type')=='sms'): ?>
  <p>
    <?php echo form_input(array('type' => 'hidden', 'name' => 'type', 'value' => 'sms')); ?>
    <?php echo _('SMS Only'); ?>
  </p>
  <?php else: ?>
  <div class="form-row">
    <div class="col-12">
  <div class="form-check form-check-inline">
    <label class="form-check-label">
      <?php

      echo form_radio(array(
              'name' => 'type',
              'id' => 'type_sms',
              'value' => 'sms',
              'checked' => set_radio('type', 'sms', true),
              'class' => 'form-check-input',
      ));
      ?>
      <?php echo _('SMS Only'); ?>
    </label>
  </div>
  <div class="form-check form-check-inline">
    <label class="form-check-label">
      <?php

      echo form_radio(array(
              'name' => 'type',
              'id' => 'type_wapos',
              'value' => 'wapos',
              'checked' => set_radio('type', 'wapos', false),
              'class' => 'form-check-input',
      ));
      ?>
      <?php echo _('Use Push IF Available,or SMS'); ?>
    </label>
      </div>

  <div class="form-check form-check-inline">
    <label class="form-check-label">
      <?php

      echo form_radio(array(
              'name' => 'type',
              'id' => 'type',
              'value' => 'push',
              'checked' => set_radio('type', 'push', false),
              'class' => 'form-check-input',
      ));
      ?>
      <?php echo _('Push Only'); ?>
    </label>
      </div>
    </div>
  </div>
  <?php endif ?>
</div>
<div id="sms_available_quantity_layer" class="form-group"<?php if ($this->input->get_post('type') == 'push'): ?> style="display:none"<?php endif; ?>>
  <label for="m_title" style="display:block;"><?php echo _('SMS Send Point'); ?></label>
    <?php echo number_format($common_data['branch']['sms_available_point']); ?><?php echo _('Point'); ?>
    <?php if (empty($common_data['branch']['sms_available_point'])): ?>
    <span class="text-warning"><?php echo _('Please charge it first and use it'); ?></span>
    <?php endif; ?>
</div>
<?php if ($this->input->get('search') or $this->input->get('counsel_search')): ?>
<?php
  $all_checked=true;
 ?>
 <?php if ($this->input->get('search')): ?>
<div class="form-group" style="display:block;">
  <label for="m_content"><?php echo _('Receiver'); ?></label>
  <div class="form-row">
    <div class="col-12">
    <?php echo form_input(array('type' => 'hidden', 'name' => 'send_all', 'value' => '0')); ?>
  검색된 <?php echo $data['search_count']; ?><?php echo _('Count People'); ?>
    </div>
  </div>
</div>
<?php endif ?>

<?php if ($this->input->get('counsel_search')): ?>
<div class="form-group" style="display:block;">
  <label for="m_content"><?php echo _('Receiver'); ?></label>
  <div class="form-row">
    <div class="col-12">
    <?php echo form_input(array('type' => 'hidden', 'name' => 'send_all', 'value' => '0')); ?>
  검색된 <?php echo $data['counsel_search_count']; ?><?php echo _('Count People'); ?>
    </div>
  </div>
</div>
<?php endif ?>
<?php else: ?>

<?php if (empty($all_checked)): ?>
<div id="select_user_layer" class="form-group">
  <label><?php echo _('Selected User'); ?></label>
  <div class="users_input">
    <?php if ($data['user']['total']): ?>
      <?php foreach ($data['user']['list'] as $index => $user): ?>
      <?php if ($data['type'] == 'push'): ?>
        <span class="select_user <?php if (empty($user['token'])): ?>text-danger<?php else: ?>text-success<?php endif; ?>">
          <?php echo $user['name']; ?>
          <?php if (empty($user['token'])): ?>
            (<?php echo _('App not installed user, not transmitted'); ?>)
          <?php else: ?>
            <input type="hidden" name="user[]" value="<?php echo $user['id']; ?>">
          <?php endif; ?>
          <span class="text-danger">X</span>
        </span>
      <?php else: ?>
        <span class="select_user <?php if (empty($user['phone'])): ?>text-danger<?php else: ?>text-success<?php endif; ?>">
          <?php echo $user['name']; ?>        
          <?php if (empty($user['phone'])): ?>
            (<?php echo _('Phone number not received, not transmitted'); ?>)
          <?php else: ?>
        <input type="hidden" name="user[]" value="<?php echo $user['id']; ?>">
          <?php endif; ?>
          <span class="text-danger">X</span>
        </span>
      <?php endif; ?>
      <?php endforeach; ?>
    <?php endif; ?>
    <?php if ($data['temp_user']['total']): ?>
      <?php foreach ($data['temp_user']['list'] as $index => $temp_user): ?>
        <span class="select_user <?php if (empty($temp_user['phone'])): ?>text-danger<?php else: ?>text-success<?php endif; ?>">
          <?php echo $temp_user['name']; ?>
          <?php if (empty($temp_user['phone'])): ?>
            (<?php echo _('Phone number not received, not transmitted'); ?>)
          <?php else: ?>
        <input type="hidden" name="temp_user[]" value="<?php echo $temp_user['id']; ?>">
          <?php endif; ?>
          <span class="text-danger">X</span>
        </span>
      <?php endforeach; ?>
    <?php endif ?>
  </div>
  <div style="margin-top:10px">
    <?php 
      if($this->input->get_post('type')):
        echo anchor('/users/select/multi?message_type='.$this->input->get_post('type'), _('Select User'), array('id' => 'user_select', 'class' => 'btn btn-secondary btn-modal')).'&nbsp;';
        echo anchor('/temp-users/select/multi?message_type='.$this->input->get_post('type'), _('Select Temp User'), array('id' => 'temp_user_select', 'class' => 'btn btn-secondary btn-modal'));
      else: 
        echo anchor('/users/select/multi?message_type=sms', _('Select User'), array('id' => 'user_select', 'class' => 'btn btn-secondary btn-modal')).'&nbsp;';
        echo anchor('/temp-users/select/multi?message_type=sms', _('Select Temp User'), array('id' => 'temp_user_select', 'class' => 'btn btn-secondary btn-modal'));
      endif;
      ?>
  </div>
</div>
<?php endif; ?>
<?php endif; ?>
<?php if (!empty($all_checked)): ?>
<div id="select_not_user_layer" class="form-group">
  <label><?php echo _('Not Select User'); ?></label>
  <div class="not_users_input">
    <?php if ($data['user']['total']): ?>
      <?php foreach ($data['user']['list'] as $index => $user): ?>
      <?php if ($data['type'] == 'push'): ?>
        <span class="select_user <?php if (empty($user['token'])): ?>text-danger<?php else: ?>text-success<?php endif; ?>">
          <?php echo $user['name']; ?>
          <?php if (empty($user['token'])): ?>
            (<?php echo _('App not installed user, not transmitted'); ?>)
          <?php else: ?>
            <input type="hidden" name="user[]" value="<?php echo $user['id']; ?>">
          <?php endif; ?>
          <span class="text-danger">X</span>
        </span>
      <?php else: ?>
        <span class="select_user <?php if (empty($user['phone'])): ?>text-danger<?php else: ?>text-success<?php endif; ?>">
          <?php echo $user['name']; ?>
          <?php if (empty($user['phone'])): ?>
            (<?php echo _('Phone number not received, not transmitted'); ?>)
          <?php else: ?>
          <input type="hidden" name="user[]" value="<?php echo $user['id']; ?>">
          <?php endif; ?>
          <span class="text-danger">X</span>
        </span>    
      <?php endif; ?>
      <?php endforeach ?>
      <?php endif ?>
  </div>
  <div style="margin-top:10px">
    <?php
      if($this->input->get_post('type')) {
        $m_params='message_type='.$this->input->get_post('type');
      } else {
        $m_params='message_type=sms';
      }
      
      if(empty($params)) {
        $nu_params='?'.$m_params;
      } else {
        $nu_params=$params.'&amp;'.$m_params;
      }

      if ($this->input->get('counsel_search')):
        echo anchor('/user-not-counsel-selects'.$nu_params, _('Not Select User'), array('id' => 'user_select', 'class' => 'btn btn-secondary btn-modal'));
      else:
        echo anchor('/user-not-selects'.$nu_params, _('Not Select User'), array('id' => 'user_select', 'class' => 'btn btn-secondary btn-modal'));
      endif;
      ?>
  </div>  
</div>
<?php endif; ?>
<div class="form-group">
  <label for="m_title" style="display:block;"><?php echo _('Title'); ?> <a href="/message-prepares/select" class="btn-modal float-right"><?php echo _('Insert From Prepared Message'); ?></a></label>
  <?php

  if (isset($data['content']['title'])) {
      $value = $data['content']['title'];
  } else {
      $value = set_value('title');
  }

  echo form_input(array(
          'name' => 'title',
          'id' => 'm_title',
          'value' => $value,
          'class' => 'form-control',
  ));
  ?>
</div>
<div class="form-group">
  <label for="m_content"><?php echo _('Content'); ?></labeL>
    <?php

    if (isset($data['content']['content'])) {
        $value = $data['content']['content'];
    } else {
        $value = set_value('content');
    }

    echo form_textarea(array(
            'name' => 'content',
            'id' => 'm_content',
            'value' => $value,
            'rows' => '5',
            'class' => 'form-control',
    ));
    ?>
    <span id="show_byte_layer"><span id="show_byte">0</span>Byte / 
    <span id="sms_type" class="text-success">
    <?php if ($this->input->get_post('type') == 'push'): ?>
    <?php echo _('Smart Phone Push'); ?>
    <?php else: ?>
    <?php echo _('SMS'); ?>
    <?php endif; ?>
    </span>
  </div>
  <?php if (!empty($data['sender']['total'])): ?>
  <div id="m_sender_layer" class="form-group"<?php if ($this->input->get_post('type') == 'push'): ?> style="display:none"<?php endif; ?>>
    <label for="m_sender"><?php echo _('Sender'); ?></label>
    <?php

      $sender_options = array(0 => '지점기본('.$common_data['branch']['phone'].')');

      $select = 0;

      foreach ($data['sender']['list'] as $sender) {
          $sender_options[$sender['id']] = $sender['name'].'('.$sender['phone'].')';

          if($sender['id']==$this->session->userdata('admin_id')) {
            $select=$sender['id'];
          }
      }      

      echo form_dropdown('sender', $sender_options, $select, array('id' => 'm_sender', 'class' => 'form-control'));

    ?>
  </div>
  <?php endif; ?>
  <div class="form-group">
    <label for="m_picture"><?php echo _('Image'); ?></label>
    <?php
        echo form_upload(array(
                'name' => 'photo[]',
                'id' => 'm_picture',
                'value' => $value,
                'class' => 'form-control-file',
        ));
        ?>
    </div>
    </div>
  </div>
  <div class="form-group">
    <?php if (empty($data['send_available'])): ?>
    <?php echo '<button class="btn btn-primary btn-block" disabled="disabled"><i class="material-icons" style="vertical-align:middle">mail</i> <span style="vertical-align:middle">'._('Send').'</span></button>'; ?>
    <?php else: ?>
    <?php echo form_button(array('type' => 'submit'), '<i class="material-icons" style="vertical-align:middle">mail</i> <span style="vertical-align:middle">'._('Send').'</span>', array('class' => 'btn btn-lg btn-primary btn-block')); ?>
    <?php endif; ?>
  </div>
<?php echo form_close(); ?>

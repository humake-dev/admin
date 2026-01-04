<?php
  if (empty($data['content'])) {
      $form_url = 'send-end-alert-messages/add';
  } else {
      $form_url = 'send-end-alert-messages/edit/'.$data['content']['id'];
  }
?>
<?php echo form_open($form_url); ?>
<div class="card">
  <div class="card-body">
    <div class="form-group">
      <label for="p_title"><?php echo _('Course'); ?></label>
      <p><?php echo $data['primary_course']['product_name']; ?></p>
    </div>  
    <div class="form-group">
      <label for="p_title"><?php echo _('Message Prepare'); ?></label>
      <?php
          $default_value = null;

          if (isset($data['content']['message_prepare_id'])) {
              $default_value = $data['content']['message_prepare_id'];
          }

          $select = set_value('message_prepare_id', $default_value);

          if ($data['message_prepare']['total']) {
              foreach ($data['message_prepare']['list'] as $value) {
                  $options[$value['id']] = $value['title'];
              }
          }

          echo form_dropdown('message_prepare_id', $options, $select, array('class' => 'form-control'));
      ?>
    </div>
    <div class="form-group">
      <label for="p_controller"><?php echo _('Type'); ?></label>
      <?php
          $type_default_value = 'sms_only';

          if (isset($data['content']['type'])) {
              $type_default_value = $data['content']['type'];
          }

          $select = set_value('type', $type_default_value);

          $options = array('sms_only' => _('Only Use SMS'), 'push_only' => _('Only Use Push').'('._('IF Push Unable, Not Send').')', 'use_push_available' => _('Use Push IF Available,or SMS'));

          echo form_dropdown('type', $options, $select, array('class' => 'form-control'));
      ?>
    </div>
    <div class="form-group">
      <label for="p_action"><?php echo _('Execute Before Day Count'); ?></label>
      <?php

        $default_value = 10;

        if (isset($data['content']['day_count'])) {
            $default_value = $data['content']['day_count'];
        }

      $value = set_value('day_count', $default_value);

      echo form_input(array(
              'type' => 'number',
              'name' => 'day_count',
              'id' => 'p_day_count',
              'value' => $value,
              'min' => '1',
              'max' => '365',
              'required' => 'required',
              'class' => 'form-control',
      ));
      ?>
    </div>
    </div>
</div>
<?php echo form_submit('', _('Submit'), array('class' => 'btn btn-primary btn-block btn-lg')); ?>
<?php echo form_close(); ?>
<?php
  if (empty($data['content'])) {
      $form_url='permissions/add';
  } else {
      $form_url='permissions/edit/'.$data['content']['id'];
  }
?>
<div class="card">
  <?php echo form_open($form_url, array('class'=>'card-body')) ?>
  <div class="form-group">
    <label for="p_title"><?php echo _('Title') ?></label>
    <?php

    $value=set_value('title');

    if(!$value) {
      if (isset($data['content']['title'])) {
          $value=$data['content']['title'];
      }
    }

      echo form_input(array(
          'name'          => 'title',
          'id'            => 'p_title',
          'value'         => $value,
          'maxlength'     => '60',
          'size'          => '60',
          'required'      => 'required',
          'class'         => 'form-control'
      ));
    ?>
  </div>
    <div class="form-group">
      <label for="p_controller"><?php echo _('Controller') ?></label>
      <?php

      $value=set_value('controller');

      if(!$value) {
        if (isset($data['content']['controller'])) {
            $value=$data['content']['controller'];
        }
      }

        echo form_input(array(
            'name'          => 'controller',
            'id'            => 'p_controller',
            'value'         => $value,
            'maxlength'     => '60',
            'size'          => '60',
            'required'      => 'required',
            'class'         => 'form-control'
        ));
      ?>
    </div>
    <div class="form-group">
      <label for="p_action"><?php echo _('Action') ?></label>
      <?php

      $value=set_value('action');

      if(!$value) {
        if (isset($data['content']['action'])) {
            $value=$data['content']['action'];
        }
      }

      echo form_input(array(
              'name'          => 'action',
              'id'            => 'p_action',
              'value'         => $value,
              'maxlength'     => '60',
              'size'          => '60',
              'required'      => 'required',
              'class'         => 'form-control'
      ));
      ?>
    </div>
    <div class="form-group">
      <label for="p_description"><?php echo _('Description') ?></label>
      <?php

      $value=set_value('description');

      if(!$value) {
        if (isset($data['content']['description'])) {
            $value=$data['content']['description'];
        }
      }

      echo form_input(array(
              'name'          => 'description',
              'id'            => 'p_description',
              'value'         => $value,
              'maxlength'     => '30',
              'size'          => '30',
              'class'         => 'form-control'
      ));
      ?>
    </div>
    <div class="form-group">
      <?php echo form_submit('', _('Submit'), array('class'=>'btn btn-primary btn-block')) ?>
    </div>
  <?php echo form_close() ?>
</div>

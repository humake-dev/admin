<?php
  if (empty($data['content'])) {
      $form_url='roles/add';
  } else {
      $form_url='roles/edit/'.$data['content']['id'];
  }
?>
<div class="card">
  <?php echo form_open($form_url, array('class'=>'card-body')) ?>
    <div class="form-group">
      <label for="r_title"><?php echo _('Title') ?></label>
      <?php

      $value=set_value('title');

      if(!$value) {
        if (isset($data['content']['title'])) {
            $value=$data['content']['title'];
        }
      }

        echo form_input(array(
            'name'          => 'title',
            'id'            => 'r_title',
            'value'         => $value,
            'maxlength'     => '60',
            'size'          => '60',
            'required'      => 'required',
            'class'         => 'form-control'
        ));
      ?>
    </div>
    <div class="form-group">
      <label for="r_description"><?php echo _('Description') ?></label>
      <?php

      $value=set_value('description');

      if(!$value) {
        if (isset($data['content']['description'])) {
            $value=$data['content']['description'];
        }
      }

      echo form_input(array(
              'name'          => 'description',
              'id'            => 'r_description',
              'value'         => $value,
              'maxlength'     => '200',
              'size'          => '60',
              'required'      => 'required',
              'class'         => 'form-control'
      ));
      ?>
    </div>
    <div class="form-group">
      <?php echo form_submit('', _('Submit'), array('class'=>'btn btn-primary btn-block')) ?>
    </div>
  <?php echo form_close() ?>
</div>

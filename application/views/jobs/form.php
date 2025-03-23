<?php
  if (empty($data['content'])) {
      $form_url='jobs/add';
  } else {
      $form_url='jobs/edit/'.$data['content']['id'];
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
      <?php echo form_submit('', _('Submit'), array('class'=>'btn btn-primary btn-block')) ?>
    </div>
  <?php echo form_close() ?>
</div>

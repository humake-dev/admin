<?php
  if (empty($data['content'])) {
      $form_url='exercise-categories/add';
  } else {
      $form_url='exercise-categories/edit/'.$data['content']['id'];
  }
?>
<?php echo form_open_multipart($form_url, array('class'=>'card')) ?>
  <div class="card-body">
    <div class="form-group">
      <label for="c_title"><?php echo _('Title') ?></label>
      <?php

      $value=set_value('title');

      if(!$value) {
        if (isset($data['content']['title'])) {
            $value=$data['content']['title'];
        }
      }

        echo form_input(array(
            'name'          => 'title',
            'id'            => 'c_title',
            'value'         => $value,
            'maxlength'     => '60',
            'size'          => '60',
          //  'required'      => 'required',
            'class'         => 'form-control'
        ));
      ?>
    </div>
    <div class="form-group">
      <label for="c_title"><?php echo _('Enable') ?></label>
      <?php

      $checked=1;

      if (isset($data['content']['enable'])) {
        if($data['content']['enable']) {
          $checked=1;
        } else {
          $checked=0;
        }
      }

      echo form_checkbox(array(
          'name'        => 'enable',
          'value'       => 1,
          'checked'     => $checked
      ));
      ?>
    </div>
    <div class="form-group">
      <label for="e_picture1"><?php echo _('Image') ?></labeL>
        <?php

        echo form_upload(array(
                'name'          => 'photo[]',
                'id'            => 'e_picture1',
                'value'         => $value,
                'class'         => 'form-control'
        ));
        ?>
    </div>
    <div class="form-group">
      <?php echo form_submit('', _('Submit'), array('class'=>'btn btn-primary btn-block')) ?>
    </div>
  </div>
<?php echo form_close() ?>

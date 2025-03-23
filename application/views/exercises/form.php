<?php
  if (empty($data['content'])) {
      $form_url='exercises/add';
  } else {
      $form_url='exercises/edit/'.$data['content']['id'];
  }
?>
<?php echo form_open_multipart($form_url, array('class'=>'card')) ?>
  <div class="card-body">
    <div class="form-group">
      <label for="e_category"><?php echo _('Exercise Category') ?></label>
      <?php
        $select=set_value('exercise_category');
        $options=array();
        if($data['category']['total']) {
          foreach($data['category']['list'] as $category) {
            $options[$category['id']]=$category['title'];
          }
        }

        if (isset($data['content']['exercise_category_id'])) {
            $select=set_value('exercise_category', $data['content']['exercise_category_id']);
        }
        echo form_dropdown('exercise_category', $options, $select, array('id'=>'c_type','class'=>'form-control'));
    ?>
    </div>
    <div class="form-group">
      <label for="e_title"><?php echo _('Title') ?></label>
      <?php

      $value=set_value('title');

      if(!$value) {
        if (isset($data['content']['title'])) {
            $value=$data['content']['title'];
        }
      }

        echo form_input(array(
            'name'          => 'title',
            'id'            => 'e_title',
            'value'         => $value,
            'maxlength'     => '60',
            'size'          => '60',
          //  'required'      => 'required',
            'class'         => 'form-control'
        ));
      ?>
    </div>
    <div class="form-group">
      <label for="prepare_content"><?php echo _('Content') ?></labeL>
        <?php

        $value=set_value('content');

        if(!$value) {
          if (isset($data['content']['content'])) {
              $value=$data['content']['content'];
          }
        }

        echo form_textarea(array(
                'name'          => 'content',
                'id'            => 'prepare_content',
                'value'         => $value,
                'rows'     => '5',
                'class'         => 'form-control'
        ));
        ?>
    </div>
    <div class="form-group">
      <label for="e_tip"><?php echo _('Tip') ?></label>
      <?php

      $value=set_value('tip');

      if(!$value) {
        if (isset($data['content']['tip'])) {
            $value=$data['content']['tip'];
        }
      }

        echo form_input(array(
            'name'          => 'tip',
            'id'            => 'e_tip',
            'value'         => $value,
            'maxlength'     => '60',
            'size'          => '60',
          //  'required'      => 'required',
            'class'         => 'form-control'
        ));
      ?>
    </div>
    <div class="form-group">
      <label for="e_picture1"><?php echo _('Image1') ?></labeL>
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
      <label for="e_picture2"><?php echo _('Image2') ?></labeL>
        <?php

        echo form_upload(array(
                'name'          => 'photo[]',
                'id'            => 'e_picture2',
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

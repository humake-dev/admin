<?php
  if (empty($data['content'])) {
      $form_url = '/facility-categories/add';
  } else {
      $form_url = '/facility-categories/edit/'.$data['content']['id'];
  }
?>
<div class="card">
  <?php echo form_open($form_url, array('class' => 'card-body')); ?>
  <div class="form-row">
    <div class="col-12 col-md-6 col-lg-12 form-group">
      <label for="cc_title"><?php echo _('Course Category'); ?></label>
      <?php

    if (isset($data['content']['title'])) {
        $value = $data['content']['title'];
    } else {
        $value = set_value('title');
    }

    echo form_input(array(
            'name' => 'title',
            'id' => 'cc_title',
            'value' => $value,
            'maxlength' => '60',
            'size' => '30',
            'required' => 'required',
            'class' => 'form-control',
    ));
    ?>
    </div>
    <div class="col-12 col-md-6 col-lg-12 form-group">
      <label for="cc_order"><?php echo _('Order No'); ?></label>
      <?php

      if (isset($data['content']['order_no'])) {
          $value = $data['content']['order_no'];
      } else {
          if ($data['total']) {
              $default = $data['total'] + 1;
          } else {
              $default = 1;
          }
          $value = set_value('order_no', $default);
      }
      echo form_input(array(
              'type' => 'number',
              'name' => 'order_no',
              'id' => 'cc_order',
              'value' => $value,
              'min' => '1',
              'class' => 'form-control',
      ));
      ?>
    </div>
    <div class="col-12 col-md-6 col-lg-12 form-group">
      <label for="cc_memo"><?php echo _('Memo'); ?></label>
      <?php
        $value = set_value('content');

        if (!$value) {
            if (isset($data['content']['content'])) {
                $value = $data['content']['content'];
            }
        }

        echo form_textarea(array(
                    'name' => 'content',
                    'id' => 'cc_memo',
                    'value' => $value,
                    'rows' => '4',
                    'class' => 'form-control',
        ));
        ?>
    </div>    
    <div class="col-12 form-group">
      <?php echo form_submit('', _('Submit'), array('class' => 'btn btn-primary btn-block')); ?>
    </div>
  </div>
<?php echo form_close(); ?>
</div>

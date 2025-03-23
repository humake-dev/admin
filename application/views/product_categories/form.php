<?php
  if (empty($data['content'])) {
      $form_url='product-categories/add';
  } else {
      $form_url='product-categories/edit/'.$data['content']['id'];
  }
?>
<div class="card">
<?php echo form_open($form_url, array('class'=>'card-body')) ?>
<div class="form-group">
  <label for="pc_title"><?php echo _('Title') ?></label>
  <?php

  $value=set_value('title');

  if (!$value) {
      if (isset($data['content']['title'])) {
          $value=$data['content']['title'];
      }
  }

  echo form_input(array(
          'name'          => 'title',
          'id'            => 'pc_title',
          'value'         => $value,
          'class'         => 'form-control'
  ));
  ?>
</div>
<div class="form-group">
  <label for="pc_order"><?php echo _('Order No') ?></label>
  <?php

  if (isset($data['content']['order_no'])) {
      $value=$data['content']['order_no'];
  } else {
      if ($data['total']) {
          $default=$data['total']+1;
      } else {
          $default=1;
      }
      $value=set_value('order_no', $default);
  }
  echo form_input(array(
          'type'=> 'number',
          'name'=> 'order_no',
          'id'=> 'pc_order',
          'value'=> $value,
          'min'=> '1',
          'class'=> 'form-control'
  ));
  ?>
</div>
<div class="form-group">
  <?php echo form_submit('', _('Submit'), array('class'=>'btn btn-primary btn-block')) ?>
</div>
<?php echo form_close() ?>
</div>

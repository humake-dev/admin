<?php
  if (empty($data['content']['id'])) {
      $form_url = '/user-stop-customs/add';
  } else {
      $form_url = '/user-stop-customs/edit/'.$data['content']['id'];
  }
?>
<?php echo form_open($form_url,array(),array('order_id'=>$data['content']['order_id'],'return_url'=>'/home/stops/'.$data['content']['user_id'])); ?>
<div class="card">
  <div class="card-body">
    <div class="form-row">
      <div class="col-12 form-group">
        <label for="custom-days"><?php echo _('Use Stop Days'); ?></label>
        <?php

$value = set_value('custom_days');

if (!$value) {
    if (isset($data['content']['custom_days'])) {
        $value = $data['content']['custom_days'];
    }
}

    echo form_input(array(
            'type' => 'number',
            'name' => 'custom_days',
            'id'=>'custom-days',
            'value' => $value,
            'required' => 'required',
            'class' => 'form-control',
    ));
    ?>
    </div>
    </div>
</div>
</div>
<div class="form-group">
  <?php echo form_submit('', _('Submit'), array('class' => 'btn btn-lg btn-primary btn-block')); ?>
</div>
<?php echo form_close(); ?>

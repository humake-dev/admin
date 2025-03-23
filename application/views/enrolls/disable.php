<?php if ($this->input->get('popup')): ?>
<?php echo form_open('/enrolls/disable/'.$data['content']['order_id'], array('id' => 'enroll_expire_log_delete_form')); ?>
<input type="hidden" name="return_url" value="/home/enrolls/<?php echo $data['content']['user_id']; ?>" />
<div class="modal-header">
  <h2 class="modal-title"><?php echo _('Expire Log Delete'); ?></h2>
  <button type="button" class="close" data-dismiss="modal" aria-label="Close">
    <span aria-hidden="true">&times;</span>
  </button>
</div>
<div class="modal-body">
<?php else: ?>
  <div class="container">
    <div class="row">
      <div class="col-12">
      <?php echo form_open('/enrolls/disable/'.$data['content']['order_id'], array('class' => '')); ?>
      <input type="hidden" name="return_url" value="/home/enrolls/<?php echo $data['content']['user_id']; ?>" />
<?php endif; ?>


<?php if (isset($data['user_id'])): ?>
<input type="hidden" name="user_id" value="<?php echo set_value('user_id', $data['user_id']); ?>" />
<?php endif; ?>


<div class="form-row">
<article class="col-12">
  <h3><?php echo _('Enroll Info'); ?></h3>
  <div class="card">
    <div class="card-body">
      <div class="row">
        <div class="form-group col-12 col-md-6 col-lg-4">
      <label for="e_username"><?php echo _('User'); ?></label>
      <p>
      <?php

      echo form_input(array(
              'id' => 'e_username',
              'value' => $data['content']['user_name'],
              'class' => 'form-control-plaintext',
      ));
      ?>
      <p>
    </div>
    <?php if (!empty($common_data['branch']['use_access_card'])): ?>    
    <div class="form-group col-12 col-md-6 col-lg-4">
      <label for="e_card_no"><?php echo _('Access Card No'); ?></label>
      <p>
      <?php

      echo form_input(array(
              'id' => 'e_card_no',
              'value' => $data['content']['card_no'],
              'class' => 'form-control-plaintext',
      ));
      ?>
    </p>
    </div>
    <?php endif; ?>
    <div class="form-group col-12 col-md-6 col-lg-4">
      <label for="e_course"><?php echo _('Course'); ?></label>
      <p>
      <?php

      echo form_input(array(
              'id' => 'e_course',
              'value' => $data['content']['product_category_name'],
              'class' => 'form-control-plaintext',
      ));
      ?>
      </p>
    </div>
    <div class="form-group col-12 col-md-6 col-lg-4">
      <label for=""><?php echo _('Total Fee'); ?></label>
      <p>
      <?php

      echo form_input(array(
              'value' => number_format($data['content']['price'])._('Currency'),
              'class' => 'form-control-plaintext',
      ));
      ?>
    </p>
    </div>
    <div class="form-group col-12 col-md-6 col-lg-4">
      <label for=""><?php echo _('Total Payment'); ?></label>
      <p>
      <?php
      $payment_amount = $data['content']['payment'];
      echo form_input(array(
              'value' => number_format($payment_amount)._('Currency'),
              'class' => 'form-control-plaintext',
      ));
      ?>
    </p>
    </div>
  </div>
  </article>
</div>


<?php if ($this->input->get('popup')): ?>
</div>
<div class="modal-footer">
  <?php echo form_submit('', _('Expire Log Delete'), array('class' => 'btn btn-primary btn-block')); ?>
</div>
<?php echo form_close(); ?>

<?php else: ?>
        <?php echo form_submit('', _('Expire Log Delete'), array('class' => 'btn btn-primary btn-block btn-lg')); ?>
      <?php echo form_close(); ?>
    </div>
  </div>
</div>
<?php endif; ?>

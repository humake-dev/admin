<?php
  $user_value = set_value('user_id', $this->input->get_post('user_id'));

  if (!$user_value) {
      if (isset($data['content']['user_id'])) {
          $user_value = $data['content']['user_id'];
      }
  }

  echo form_input(array('type' => 'hidden', 'id' => 'c_user_id', 'name' => 'user_id', 'value' => $user_value));
  echo form_input(array('type' => 'hidden', 'name' => 'product_id', 'value' => 10));
?>
<div class="col-12 col-md-6 col-lg-12 form-group">
  <label for="o_title"><?php echo _('Content'); ?></label>
  <?php

  $value = set_value('title');

  if (!$value) {
      if (isset($data['content']['title'])) {
          $value = $data['content']['title'];
      }
  }

  echo form_input(array('id' => 'o_title', 'name' => 'title', 'value' => $value, 'class' => 'form-control'));
  ?>
</div>
<?php if (!$this->input->get_post('user_id')): ?>
<div class="col-12 col-md-6 col-lg-12 form-group">
  <label for="c_name"><?php echo _('User'); ?></label>
  <?php

  $user_value = set_value('user_name');

  if (!$user_value) {
      if (isset($data['content']['user_name'])) {
          $user_value = $data['content']['user_name'];
      }
  }

?>
  <div class="input-group-prepend select-user">
    <?php
  echo form_input(array(
      'name' => 'user_name',
      'id' => 'c_name',
      'value' => $user_value,
      'maxlength' => '60',
      'size' => '60',
      'readonly' => 'readonly',
      'required' => 'required',
      'class' => 'form-control',
  ));
?>
      <div class="input-group-text">
      <span class="material-icons">account_box</span>
      </div>
  </div>
</div>
<?php endif; ?>

<div class="col-12 form-group">
  <label for="o_transaction_date"><?php echo _('Transaction Date'); ?></label>
  <?php

  $transaction_value = set_value('transaction_date');

  if (!$transaction_value) {
      if (isset($data['content']['transaction_date'])) {
          $transaction_value = $data['content']['transaction_date'];
      } else {
          $transaction_value = $search_data['date'];
      }
  }

  ?>
  <div class="input-group-prepend date">
      <?php echo form_input(array(
              'name' => 'transaction_date',
              'id' => 'o_transaction_date',
              'value' => $transaction_value,
              'class' => 'form-control datepicker',
      )); ?>
      <div class="input-group-text">
      <span class="material-icons">date_range</span>
      </div>
  </div>
</div>
<div class="col-12 col-md-6 col-lg-12 form-group">
  <label for="o_cash"><?php echo _('Cash'); ?></label>
  <?php
  $cash_value = set_value('cash', 0);

  if (!$cash_value) {
      if (isset($data['content']['cash'])) {
          $cash_value = $data['content']['cash'];
      }
  }

  echo form_input(array(
          'type' => 'number',
          'min' => 0,
          'name' => 'cash',
          'id' => 'o_cash',
          'value' => $cash_value,
          'class' => 'form-control',
  ));
  ?>
</div>
<div class="col-12 col-md-6 col-lg-12 form-group">
  <label for="o_credit"><?php echo _('Credit'); ?></label>
  <?php

  $credit_value = set_value('credit', 0);

  if (!$credit_value) {
      if (isset($data['content']['credit'])) {
          $credit_value = $data['content']['credit'];
      }
  }

  echo form_input(array(
          'type' => 'number',
          'min' => 0,
          'name' => 'credit',
          'id' => 'l_credit',
          'value' => $credit_value,
          'class' => 'form-control',
  ));
  ?>
</div>

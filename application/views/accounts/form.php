<?php
  if (empty($data['content'])) {
      $form_url = 'accounts/add';
  } else {
      $form_url = 'accounts/edit/'.$data['content']['id'];
  }
?>
<?php echo form_open($form_url, array('class' => 'col-12')); ?>
  <div class="card">
    <div class="card-body form-row">
    <div class="col-12  form-group">
      <label for="a_category"><?php echo _('User'); ?></label>
      <p><?php echo $data['content']['user_name']; ?></p>
    </div>
    <div class="col-12 col-lg-4 form-group">
      <label for="a_type"><?php echo _('Type'); ?></label>
      <?php if($this->session->userdata('is_role')<3): ?>
      <?php
        $options = array('I' => _('Income'), 'O' => _('Outcome'));
        $select = set_value('type');

        if (!$select) {
            if (isset($data['content']['type'])) {
                $select = $data['content']['type'];
            } else {
                $select = '0';
            }
        }
        echo form_dropdown('type', $options, $select, array('id' => 'a_type', 'class' => 'form-control'));
    ?>
    <?php else: ?>
      <?php 
        $options = array('I' => _('Income'), 'O' => _('Outcome'));
      $select = $data['content']['type'];
      
?>
    <p><?php echo  $options[$select] ?></p>
    <?php endif ?>    
    </div>
    <div class="col-12 col-md-6 col-lg-4 form-group">
      <label for="a_category"><?php echo _('Account Category'); ?></label>
      <?php if($this->session->userdata('is_role')<3): ?>
      <?php
        $options = array();
        $select = set_value('account_category_id');

        if ($data['category']['total']) {
            foreach ($data['category']['list'] as $option) {
                $options[$option['id']] = $option['title'];
            }
        }

        if (!$select) {
            if (isset($data['content']['account_category_id'])) {
                $select = $data['content']['account_category_id'];
            }
        }
        echo form_dropdown('account_category_id', $options, $select, array('id' => 'a_category', 'class' => 'form-control'));
    ?>
    <?php else: ?>
      <?php 
        if ($data['category']['total']) {
          foreach ($data['category']['list'] as $option) {
              $options[$option['id']] = $option['title'];
          }
      }

      $select = $data['content']['account_category_id'];
    
?>
    <p><?php echo  $options[$select] ?></p>
    <?php endif ?>
    </div>
    <div class="col-12 col-md-6 col-lg-4 form-group">
      <label for="a_transaction_date"><?php echo _('Transaction Date'); ?></label>
        <?php

        $transaction_date_value = set_value('transaction_date', 0);

        if (!$transaction_date_value) {
            if (isset($data['content']['transaction_date'])) {
                $transaction_date_value = $data['content']['transaction_date'];
            }
        }

        echo form_input(array(
                'type' => 'text',
                'name' => 'transaction_date',
                'id' => 'a_transaction_date',
                'value' => $transaction_date_value,
                'class' => 'form-control account-datepicker',
        ));
        ?>
    </div>
    <div class="col-12 col-lg-6 form-group">
      <label for="e_cash"><?php echo _('Cash'); ?></label>
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
                'id' => 'e_cash',
                'value' => $cash_value,
                'class' => 'form-control',
        ));
        ?>
    </div>
    <div class="col-12 col-lg-6 form-group">
      <label for="a_credit"><?php echo _('Credit'); ?></label>
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
                'id' => 'a_credit',
                'value' => $credit_value,
                'class' => 'form-control',
        ));
        ?>
    </div>
    </div>
  </div>
  <?php if ($this->router->fetch_method() == 'edit'): ?>
  <?php echo $Layout->Element('form_edit'); ?>
  <?php endif; ?>
  <?php echo form_button(array('type' => 'submit'), _('Submit'), array('class' => 'btn btn-lg btn-primary btn-block')); ?>
</div>

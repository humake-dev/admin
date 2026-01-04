<?php echo form_open('', array('method' => 'get', 'id' => 'search_rent_sws_default_form', 'class' => 'search_form col-12'),array('search_type'=>'default')); ?>
  <input type="hidden" id="future_search" value="1">
  <div class="row">
    <div class="form-group col-12 col-lg-4 col-xl-3">
      <label for="s_user_type"><?php echo _('Order Type'); ?></label>
      <?php
      
      $user_options = array('all' => _('All'), 'paid' => _('Paid'), 'free' => _('Free'));
      $user_type_select = set_value('user_type', 'all');
      echo form_dropdown('user_type', $user_options, $user_type_select, array('id' => 's_user_type', 'class' => 'form-control'));
      ?>
    </div>
    <div class="form-group col-12 col-lg-4 col-xl-3">
      <label for="s_by_status"><?php echo _('By Status'); ?></label>
      <?php

      $status_options = array('all' => _('All User'), 'using' => _('Using'), 'expired' => _('Expired'), 'reservation' => _('Reservation'));

      $status_select = set_value('status_type', 'all');
      echo form_dropdown('status_type', $status_options, $status_select, array('id' => 's_by_status', 'class' => 'form-control'));

      ?>
    </div>
        <div id="fg_payment_status" class="col-12 col-md-6 col-lg-4 col-xl-3 form-group available_search" <?php if ($user_type_select == 'free'): ?> style="display:none"<?php endif; ?>>
        <label for="s_payment_id"><?php echo _('By payment status'); ?></label>
        <?php
        $p_options = array('' => _('All'),'status1' => _('Pay For Cash'), 'status2' => _('Pay For Credit'));

        $select = set_value('payment_id');

        if (!$select) {
            if (isset($search_data['payment_id'])) {
                $select = $search_data['payment_id'];
            }
        }
        echo form_dropdown('payment_id', $p_options, $select, array('id' => 's_payment_id', 'class' => 'form-control'));
        ?>
        </div>
        <div class="col-12 col-md-6 col-lg-4 col-xl-3 form-group">
          <label for="s_search_period"><?php echo _('Search Period'); ?></label>
          <?php
          $options = array('' => _('All'), 'transaction_date' => _('Transaction Date'),'start_date' => _('Start Date'),'end_date' => _('End Date'));

          echo form_dropdown('search_period', $options, set_value('search_period'), array('id' => 's_search_period', 'class' => 'form-control'));
          ?>
        </div>

          </div>
          <div class="form-row">
       
      <div id="default_period_form" class="col-12 col-lg-8 form-group"<?php if (!empty($search_data['period_display_none'])): ?> style="display:none"<?php endif; ?>>
          <label for="start_date"><?php echo _('Check Period'); ?></label>
          <div class="form-row">
            <?php echo $Layout->Element('search_period'); ?>
          </div>
      </div>
    <div class="col-12">
      <?php echo form_submit('', _('Search'), array('class' => 'btn btn-primary')); ?>
    </div>
      <?php if (!empty($search_data['search'])): ?>
        <?php anchor('/rent-sws', '검색조건 해제'); ?>
      <?php endif; ?>
  </div>
<?php echo form_close(); ?>

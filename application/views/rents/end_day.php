<?php
  $already_end=false;
  $default_end_date_value = $search_data['today'];

  if(!empty($data['content']['end_datetime'])) {
    $today_obj=new DateTime($search_data['today'],$search_data['timezone']);
    $end_date_obj=new DateTime($data['content']['end_datetime'],$search_data['timezone']);

    if($today_obj>$end_date_obj) {
      $default_end_date_value=$end_date_obj->format('Y-m-d');
      $already_end=true;
    }
  }

  $end_date_value = set_value('end_date', $default_end_date_value);
  
  $not_change_end_day = true;
  if ($end_date_value == $default_end_date_value) {
    $not_change_end_day = false;  
  }

  if($already_end) {
    $current_end_label=_('Current End');
  } else {
    $current_end_label=_('Now');
  }

?>

<div class="row">
<article class="col-12">
  <h3><?php echo _('Order Info'); ?></h3>
    <div class="card">
      <div class="card-body" style="padding-bottom:0">
        <div class="row">
          <div class="col-12 col-lg-6 form-group">
            <label><?php echo _('Product'); ?></label>
            <p>
              <?php if (!empty($data['content']['product_category_name'])): ?>
              <?php echo $data['content']['product_category_name']; ?>
              / 
              <?php endif; ?>
              <?php echo $data['content']['product_name']; ?>
            <p>
          </div>
          <div class="col-12 col-lg-6 form-group">
            <label><?php echo _('Price'); ?></label>
            <p>
            <?php if (empty($data['content']['price'])): ?>
              <?php echo _('Free'); ?>
              <?php else: ?>
              <?php echo number_format($data['content']['price']); ?><?php echo _('Currency'); ?>
              <?php endif; ?>
            <p>
          </div>
          <div class="col-12 col-lg-6 form-group">
            <label><?php echo _('Payment'); ?></label>
            <?php
            $payment_amount = $data['content']['payment'];
            ?>
            <p>
              <?php if (empty($data['content']['payment'])): ?>
              <?php echo _('Free'); ?>
              <?php else: ?>
              <?php echo number_format($data['content']['payment']); ?><?php echo _('Currency'); ?>
              <?php endif; ?>
            <p>
          </div>
          
          <div class="col-12 col-lg-6 form-group">
            <label><?php echo _('Period'); ?></label>
            <p><?php echo get_dt_format($data['content']['start_date'], $search_data['timezone']); ?> ~ <?php echo get_dt_format($data['content']['end_date'], $search_data['timezone']); ?> </p>
          </div>
        </div>
      </div>
    </div>
</article>
<?php

      $total_quantity = $data['content']['total_date'];
      $left_quantity = $data['content']['left_date'];

      if (isset($data['content']['lesson_type'])) {
          if ($data['content']['lesson_type'] != 1) {
              $total_quantity = $data['content']['quantity'];
              $left_quantity = $data['content']['quantity'] - $data['content']['use_quantity'];
          }
      }

      if (empty($payment_amount)) {
          $calculator_value = 0;
      } else {
          if (empty($common_data['branch']['not_return_rate'])) {
              $calculator_value = ($payment_amount / $total_quantity) * $left_quantity;
          } else {
              $not_return_amount = $payment_amount * ($common_data['branch']['not_return_rate'] / 100);
              $calculator_value = (($payment_amount - $not_return_amount) / $total_quantity) * $left_quantity;
          }
          $calculator_value = round(floor($calculator_value), -3);

          if ($calculator_value > $payment_amount) {
              $calculator_value = $payment_amount;
          }
      }

      ?>

<article class="col-12">
  <h3><?php echo _('End Order Info'); ?></h3>
  <div class="card">
    <div class="card-body">
      <div class="row">
        <div class="col-12 form-group">

<input id="o_end_start_date" type="hidden" value="<?php echo $data['content']['start_date']; ?>">
<input id="o_end_end_date" type="hidden" value="<?php echo $search_data['today']; ?>">
<input id="today" type="hidden" value="<?php echo $search_data['today']; ?>">
          <label><?php echo _('End Datetime'); ?>&nbsp;&nbsp; 
          <input id="not_change_end_day" name="end_now" type="checkbox" value="1"<?php if (empty($not_change_end_day)): ?> checked="checked"<?php endif; ?>><?php echo $current_end_label ?></label>
          <div id="o_end_date_layer"<?php if (empty($not_change_end_day)): ?> style="display:none"<?php endif; ?>>
            <div class="input-group-prepend date">
            <?php

              echo form_input(array(
                'name' => 'end_date',
                'id' => 'o_end_date',
                'value' => $end_date_value,
                'class' => 'form-control enroll_datepicker',
              ));
            ?>
            <div class="input-group-text">
            <span class="material-icons">date_range</span>
            </div>
          </div>
        </div>
        <p id="o_end_date_display"<?php if (!empty($not_change_end_day)): ?> style="display:none"<?php endif; ?>>
          <label style="margin:0;padding:0"><?php echo get_dt_format($end_date_value, $search_data['timezone']); ?></label>
          </p>
        </div>        
     
      </div>
    </div>  
  </div>
</article>

<?php if ($this->session->userdata('role_id')<=5):?>
<article class="col-12">
  <h3>
        <input type="checkbox" id="insert_refund" name="insert_refund" value="1" />
        <label for="insert_refund"><?php echo _('Insert Refund'); ?></label>
      </h3>
</article>
<div class="col-12">
  <?php include __DIR__.DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'orders'.DIRECTORY_SEPARATOR.'refund.php'; ?>
</div>
<?php endif ?>
</div>
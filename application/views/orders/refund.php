<?php
      $total_quantity = $data['content']['total_date'];
      $left_quantity = $data['content']['left_date'];

      if (isset($data['content']['lesson_type'])) {
          if ($data['content']['lesson_type'] != 1) {
              $total_quantity = $data['content']['quantity'];
              $left_quantity = $data['content']['quantity'] - $data['content']['use_quantity'];
          }
      }

      if (empty($payment_amount) or empty($total_quantity)) {
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
<div id="refund_layer" class="card" style="display:none">
    <div class="card-body" style="padding-bottom:0">
        <div class="row">
            <div class="form-group col-12 col-xl-8">
                <label for="r_refund"><?php echo _('Refund Method'); ?></label>
                <p>
                    <label><input type="radio" name="refund_type" value="all" checked="checked"> <?php echo _('Refund All'); ?></label>&nbsp;&nbsp;&nbsp;
                    <label><input type="radio" name="refund_type" value="calculate"> <?php echo _('Refund Caculate'); ?></label>&nbsp;&nbsp;&nbsp;
                    <label><input type="radio" name="refund_type" value="etc"> <?php echo _('Refund Etc'); ?></label> 
               </p>
            </div>
            <div class="form-group col-12 col-xl-4">
                <?php

                $transaction_date_value = set_value('transaction_date', $search_data['today']);

                $is_today = false;
                if ($transaction_date_value == $search_data['today']) {
                    $is_today = true;
                }
                ?>
                <label for="o_transaction_date"><?php echo _('Transaction Date'); ?></label>
                <div id="o_transaction_date_layer"<?php if (!$is_today): ?> style="display:block"<?php endif; ?>>
                  <div class="input-group-prepend date">
                  <?php

                    echo form_input(array(
                      'name' => 'transaction_date',
                      'id' => 'o_transaction_date',
                      'value' => $transaction_date_value,
                      'class' => 'form-control enroll_datepicker',
                    ));
                  ?>
                  <div class="input-group-text">
                  <span class="material-icons">date_range</span>
                  </div>
                </div>
              </div>
                <p id="today_display" <?php if (!$is_today): ?> style="display:none"<?php endif; ?>>
                <label style="margin:0;padding:0"><input id="is_today" type="checkbox" name="is_today" value="1" checked="checked" /> <?php echo _('Today'); ?> (<?php echo get_dt_format($transaction_date_value, $search_data['timezone']); ?> )</label>
                </p>
          </div>
      
          <div class="form-group col-12" id="calculator_info" style="display:none">
            <label><?php echo _('Calculator Description'); ?></label>
            <p>
            <?php if (empty($common_data['branch']['not_return_rate'])): ?>
            (<?php echo _('Total Payment'); ?>: <?php echo number_format($payment_amount)._('Currency'); ?>
            <?php else: ?>
            (<?php echo _('Total Payment'); ?> -  <?php echo '위약금('.$common_data['branch']['not_return_rate'].'%)'; ?> : <?php echo number_format($payment_amount - $not_return_amount)._('Currency'); ?>
            <?php endif; ?>

            / <?php echo '전체 일수,횟수'; ?> : <span><?php echo number_format($total_quantity); ?></span>) X  <?php echo '남은 일수,횟수'; ?> : <span><?php echo number_format($left_quantity); ?></span> = <span><?php echo number_format($calculator_value); ?></span><?php echo _('Currency'); ?>
            </p>            
          </div>
      
          <div class="form-group col-12 col-xl-4">
            <label for=""><?php echo _('Total Payment'); ?></label>
            <p id="o_d_total"><?php echo number_format($payment_amount)._('Currency') ?></p>
            <?php


            echo form_input(array('type' => 'hidden', 'id' => 'o_total', 'value' => $payment_amount));
            echo form_input(array('type' => 'hidden', 'id' => 'o_calculate', 'value' => $calculator_value));

            $refund_amount = $payment_amount;

            ?>   
          </div>
          <div class="form-group col-12 col-xl-4">
          <label><?php echo _('Refund'); ?></label>
      <p id="o_d_refund"><?php echo number_format($refund_amount)._('Currency') ?></p>
      <?php
      echo form_input(array('type' => 'hidden', 'value' => 0, 'name' => 'refund', 'id' => 'o_refund', 'value' => $refund_amount, 'class' => 'form-control'));
      ?>
    </div>
    
    <div class="col-12 col-xl-4 form-group">
      <label><?php echo _('Refund Payment Method'); ?></label>
      <?php
        $options = array('1' => _('Cash'), '2' => _('Credit'), '4' => _('Mix'));

        $select = set_value('payment_method', 1);
        echo form_dropdown('select_payment', $options, $select, array('id' => 'select_payment', 'class' => 'form-control'));
      ?>
    </div>
    <div class="form-group col-12 col-md-6 col-lg-4">
      <label for="o_cash" class="payment_label" style="display:none"><?php echo _('Cash'); ?></label>
      <?php

      if ($select == 1) {
          $cash_value = set_value('cash', $refund_amount);
      } else {
          $cash_value = set_value('cash');
      }

      echo form_input(array(
              'type' => 'hidden',
              'min' => 0,
              'value' => $cash_value,
              'name' => 'cash',
              'id' => 'o_cash',
              'class' => 'form-control calc_outstanding',
      ));
      ?>
    </div>

    <div class="form-group col-12 col-md-6 col-lg-4">
      <label for="o_credit" class="payment_label" style="display:none"><?php echo _('Credit'); ?></label>
      <?php

      if ($select == 2) {
          $credit_value = set_value('credit', $refund_amount);
      } else {
          $credit_value = set_value('credit');
      }

      echo form_input(array(
              'type' => 'hidden',
              'min' => 0,
              'value' => $credit_value,
              'name' => 'credit',
              'id' => 'o_credit',
              'class' => 'form-control calc_outstanding',
      ));
      ?>
    </div>
  </div>
  </div>
  </div>
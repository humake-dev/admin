<?php if (empty($list['total'])): ?>
    <tr>
      <td colspan="20"><?php echo _('No Data'); ?></td>
    </tr>
    <?php
      else:
      foreach ($list['list'] as $index => $value):

      $status = '<span class="text-success">'._('Using').'</span>';
      $remain_date = '-';
      $remain_count = '-';

      $display_end_date = get_dt_format($value['end_date'], $search_data['timezone']);
      $display_start_date = get_dt_format($value['start_date'], $search_data['timezone']);

      $start_date_obj = new DateTime($value['start_date'], $search_data['timezone']);
      $end_date_obj = new DateTime($value['end_date'], $search_data['timezone']);

      if ($end_date_obj >= new DateTime($search_data['max_date'], $search_data['timezone'])) {
          $remain_date = '-';
          $display_end_date = _('Unlimit');
      } else {
          $current_date_obj = new DateTime($search_data['today'], $search_data['timezone']);

          if ($current_date_obj >= $start_date_obj) {
              if ($value['stop_end_date'] and $value['change_end_date']) {
                  $remain_date = $value['day_count']._('Day');
              } else {
                  if ($current_date_obj <= $end_date_obj) {
                      $interval_obj = $current_date_obj->diff($end_date_obj);
                      $remain_date = $interval_obj->format('%a')._('Day');
                  } else {
                      $remain_date = _('None');
                      $status = '<span class="text-danger">'._('Ended').'</span>';
                  }
              }

              if ($end_date_obj < $current_date_obj) {
                  $status = '<span class="text-warning">'._('Expired').'</span>';
              }
          } else {
              $interval_obj = $start_date_obj->diff($end_date_obj);
              $remain_date = $interval_obj->format('%a')._('Day');
              $status = '<span class="text-warning">'._('Reservation').'</span>';
          }
      }

      if ($value['stopped']) {
          $status = '<span class="text-warning">'._('Stopped').'</span>';
      }

      if ($value['ended']) {
          if ($value['refund']) {
              $status = '<span class="text-danger">'._('Refund').'</span>';
          } else {
              $status = '<span class="text-danger">'._('Cancel').'</span>';
          }
      }

      switch ($value['lesson_type']) {
        case 1: // 기간제
        $count_unit = get_lesson_unit($value['lesson_type'], $value['lesson_period_unit']);
        break;
        case 3: // 쿠폰제
        $count_unit = '개';
        $remain_count = ($value['quantity'] - $value['use_quantity']).$count_unit; // 단위수량 X 구입갯수
        break;
        default: // GX
        $count_unit = '회';
        $remain_count = ($value['quantity'] - $value['use_quantity']).$count_unit; // 단위수량 X 구입갯수
        break;
      }
    ?>
    <tr<?php if ($value['id'] == $data['enroll']['content']['id']):?> class="table-primary"<?php endif; ?>>
      <td class="text-center">
          <?php if (empty($value['in'])): ?>
            <?php echo $enroll_total - $value['order_no']; ?>
          <?php else: ?>
            <?php echo $value['in']; ?>
          <?php endif; ?>
      </td>
      <td class="enroll_transaction_date">
        <?php
        
        $transaction_date=$value['transaction_date'];

        if(empty($transaction_date)) {
          $transaction_date=$value['order_transaction_date'];
        }
        
        echo get_dt_format($transaction_date, $search_data['timezone']); 
        
        ?>
        <input type="hidden" name="enroll_id[]" value="<?php echo $value['id']; ?>">
        <input type="hidden" name="stopped" value="<?php echo $value['stopped']; ?>">
        <input type="hidden" name="order_id[]" value="<?php echo $value['order_id']; ?>">
        <?php

        $is_delete = false;
        $end_text = _('End Order');

          if ($value['ended']) {
              $is_delete = true;
              $end_text = _('Delete');
          }
        ?>
        <input type="hidden" value="<?php echo $is_delete; ?>">
        <input type="hidden" value="<?php echo $end_text; ?>"> 
      </td>
      <td>
        <?php if ($table_id == 'user_end_enroll_list'): ?>
        <span class="text-danger"><?php echo _('Ended'); ?></span>
        <?php else: ?>
        <?php echo $status; ?>
        <?php endif; ?>
      </td>
      <td class="enroll_category_name">
        <?php echo $value['product_category_name']; ?> / <?php echo $value['product_name']; ?>
        <input type="hidden" value="<?php echo $value['lesson_type']; ?>" />      
      </td>
      <td>
        <?php if (empty($value['trainer_name'])): ?>
          -
        <?php else: ?>
          <?php echo $value['trainer_name']; ?>
        <?php endif; ?>
      </td>
      <td>
        <?php if ($value['lesson_type'] == 1): ?>
        <?php echo $value['insert_quantity'].$count_unit; ?>
        <?php else: ?>
        <?php echo $value['lesson_quantity'] * $value['insert_quantity'].$count_unit; ?>
        <?php endif; ?>
      </td>
      <td><?php echo get_lesson_type($value['lesson_type']); ?></td>
      <td>
        <?php if (empty($value['stopped'])): ?>
        <?php echo $display_start_date; ?>      
        <?php else: ?>
        <?php
          if (empty($value['change_start_date'])) {
              echo $display_start_date;
          } else {
              echo _('Change Start Date').' :'.get_dt_format($value['change_start_date'], $search_data['timezone']);
              echo '<br />';
              echo _('Origin Start Date').' :'.get_dt_format($value['start_date'], $search_data['timezone']);
          }

        ?>
        <?php endif; ?>      
      </td>
      <td class="enroll_end_date">
        <?php if (empty($value['stopped'])): ?>
        <?php echo $display_end_date; ?>
        <?php else: ?>        
        <?php

          if ($value['stop_end_date'] and $value['change_end_date']) {
              echo _('Change End Date').' :'.get_dt_format($value['change_end_date'], $search_data['timezone']);
              echo '<br />';
              echo _('Origin End Date').' :'.get_dt_format($value['end_date'], $search_data['timezone']);
          } else {
              echo _('Change End Date').' :'._('Not Set');
              echo '<br />';
              echo _('Origin End Date').' :'.get_dt_format($value['end_date']);
          }
        ?>
        <?php endif; ?>
        <input type="hidden" name="end_date[]" value="<?php echo $value['end_date']; ?>" />
      </td>
      <td class="text-right"><?php echo $remain_count; ?></td>
      <td class="text-right"><?php echo number_format($value['original_price']); ?><?php echo _('Currency'); ?></td>
      <td class="text-right">
      <?php
        if (!empty($value['dc_price'])) {
            $dc = $value['dc_price'];
        } else {
            $dc = 0;
        }

        $dc += $value['original_price'] * $value['dc_rate'] / 100;
        if ($dc):
        ?>
        <?php echo number_format($dc); ?><?php echo _('Currency'); ?>
      <?php else: ?>
        -
      <?php endif; ?>
      </td>
      <td class="text-right"><?php echo number_format($value['price']); ?><?php echo _('Currency'); ?></td>
      <td class="text-right"><?php echo number_format($value['payment']); ?><?php echo _('Currency'); ?></td> 
      <td class="text-center link">
          <?php if (empty($value['edit_log_count'])): ?>
          <?php echo _('None'); ?>
          <?php else: ?>
          <?php echo anchor('order-edit-logs?user_id='.$value['user_id'].'&amp;product_id='.$value['product_id'].'&amp;order_id='.$value['order_id'].'&amp;date_p=all', _('Show Edit Log'), array('class' => 'btn btn-secondary', 'target' => '_blank')); ?>
          <?php endif; ?>
      </td>
      <td>
      <?php if (empty($value['content_id'])): ?>
      <?php echo anchor('enroll-contents/add?order_id='.$value['order_id'], _('Add Content'), array('class' => 'btn btn-primary btn-modal')); ?>
      <?php else: ?>
      <?php echo anchor('enroll-contents/view/'.$value['content_id'], _('Show Content'), array('class' => 'btn btn-secondary btn-modal')); ?>
      <?php endif; ?>
      </td>
    </tr>
    <?php endforeach; ?>
    <?php endif; ?>
    
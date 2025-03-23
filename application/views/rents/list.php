<?php
  if (empty($table_id)) {
      $table_id = 'user_rent_list';
  }
?>
<table id="<?php echo $table_id; ?>" class="table table-bordered table-hover">
  <colgroup>
    <col />
    <col />
    <col />
    <col />
    <col />
    <col />
    <col /> 
    <col />
    <col />
    <?php if ($this->router->fetch_method() != 'index'): ?>
    <col />
    <?php endif; ?>
  </colgroup>
  <thead class="thead-default">
    <tr>
      <th><?php echo _('Transaction Date'); ?></th>    
      <th><?php echo _('Status'); ?></th>
      <th class="text-center"><?php echo _('Facility'); ?></th>
      <th><?php echo _('Facility No'); ?></th>
      <th class="text-center"><?php echo _('Period'); ?></th>
      <th class="text-center"><?php echo _('Start Time'); ?></th>
      <th class="text-center"><?php echo _('End Time'); ?></th>
      <th class="text-center"><?php echo _('Price'); ?></th>   
      <th class="text-center"><?php echo _('Payment'); ?></th>
      <?php if ($this->router->fetch_method() != 'index'): ?>         
      <th class="text-center"><?php echo _('Memo'); ?></th>
      <?php endif; ?>
    </tr>
  </thead>
  <tbody>
    <?php if (empty($list['total'])): ?>
    <tr>
      <td colspan="11"><?php echo _('No Data'); ?></td>
    </tr>
    <?php
      else:
      foreach ($list['list'] as $value):
        $start_date_obj = new DateTime($value['start_datetime'], $search_data['timezone']);
        $end_date_obj = new DateTime($value['end_datetime'], $search_data['timezone']);
        $current_date_obj = new DateTime('now', $search_data['timezone']);

        $status = '<span class="text-success">'._('Using').'</span>';

        if ($current_date_obj > $start_date_obj) {
            if ($end_date_obj < $current_date_obj) {
                $status = '<span class="text-warning">'._('Expired').'</span>';
            }
        } else {
            $status = '<span class="text-warning">'._('Reservation').'</span>';
        }

        if ($value['stopped']) {
            $status = '<span class="text-warning">'._('Stopped').'</span>';
        }

        if ($value['ended']) {
            $status = '<span class="text-danger">'._('Ended').'</span>';
        }

        $name = $value['product_name'];
    ?>
    <tr<?php if ($this->router->fetch_method() != 'index'): ?><?php if (isset($data['rent']['content'])): ?><?php if ($data['rent']['content']['id'] == $value['id']): ?> class="table-primary"<?php endif; ?><?php endif; ?><?php endif; ?>>
      <td class="rent_transaction_date">
        <input type="hidden" value="<?php echo $value['id']; ?>" />
        <input type="hidden" value="<?php echo $value['stopped']; ?>" />
        <input type="hidden" value="<?php echo $value['order_id']; ?>" />
        <input type="hidden" value="<?php echo $value['expired']; ?>" />          
        <?php echo get_dt_format($value['transaction_date'], $search_data['timezone']); ?>
      </td>
      <td><?php echo $status; ?></td>
      <td><?php echo $name; ?></td>
      <td><?php if ($value['no']): ?><?php echo $value['no']; ?><?php else: ?><span class="text-orange"><?php echo _('Not Set'); ?><span><?php endif; ?></td>
      <td class="text-right">
        <?php if (empty($value['insert_quantity'])): ?>
        <?php echo get_period($value['start_datetime'], $value['end_datetime'], $search_data['timezone']); ?>
        <?php else: ?>
        <?php echo $value['insert_quantity']. _('Period Month') ?>
        <?php endif; ?>
      </td>
      <td class="text-center"><?php echo get_dt_format($value['start_date'], $search_data['timezone']); ?></td>
      <td class="text-center">
      <?php
        if ($value['stopped']):
          if ($value['stop_end_date'] and $value['change_end_date']) {
              echo _('Change End Date').' :<br />'.get_dt_format($value['change_end_date'], $search_data['timezone']);
              echo '<br />';
              echo _('Origin End Date').' :<br />'.get_dt_format($value['end_date'], $search_data['timezone']);
          } else {
              echo _('Change End Date').' :<br />'._('Not Set');
              echo '<br />';
              echo _('Origin End Date').' :<br />'.get_dt_format($value['end_date'], $search_data['timezone']);
          }
        ?>
        <?php else: ?>
        <?php echo get_dt_format($value['end_date'], $search_data['timezone']); ?>
        <?php endif ?>
        <input type="hidden" name="end_date[]" value="<?php echo $value['end_date']; ?>" />      
      </td>
      <td class="text-right"><?php echo number_format($value['price']); ?><?php echo _('Currency'); ?></td> 
      <td class="text-success text-right"><?php echo number_format($value['payment']); ?><?php echo _('Currency'); ?></td>
      <?php if ($this->router->fetch_method() != 'index'): ?>
      <td class="text-center">
        <?php if (empty($value['content_id'])): ?>
        <?php echo anchor('rent-contents/add?order_id='.$value['order_id'], _('Add Content'), array('class' => 'btn btn-primary btn-modal')); ?>
        <?php else: ?>
        <?php echo anchor('rent-contents/view/'.$value['content_id'], _('Show Content'), array('class' => 'btn btn-secondary btn-modal')); ?>
        <?php endif; ?>
      </td>
      <?php endif; ?>
    </tr>
    <?php endforeach; ?>
    <?php endif; ?>
  </tbody>
</table>

<table id="order_stop_schedule_list" class="table table-bordered table-hover">
  <colgroup>
    <col />
    <col />
    <col />
    <col />
    <col />
    <col class="manage" />
  </colgroup>
  <thead>
    <tr class="thead-default">
      <th class="text-center"><?php echo _('Stop Days'); ?></th> 
      <th class="text-center"><?php echo _('Stop Start Date'); ?></th>
      <th class="text-center"><?php echo _('Stop End Date'); ?></th>
      <th class="text-center"><?php echo _('Request Date'); ?></th>
      <th class="text-center"><?php echo _('Memo'); ?></th>
      <th class="text-center manage"><?php echo _('Manage'); ?></th>
    </tr>
  </thead>
  <tbody>
    <?php foreach ($data['stop_schedules']['list'] as $index => $value): ?>
    <tr>
      <td class="text-right">
      <input type="hidden" value="<?php echo $value['id']; ?>">
      <?php if ($value['stop_day_count']): ?>
      <?php echo $value['stop_day_count']; ?><?php echo _('Day'); ?>
      <?php else: ?>
      <?php echo _('Not Set'); ?>
      <?php endif; ?>
      </td>
      <td class="text-center">
        <?php echo get_dt_format($value['stop_start_date'], $search_data['timezone']); ?>
        <br />
        <?php

          $current_date_obj = new DateTime($search_data['today'], $search_data['timezone']);

          $stop_start_date_obj = new DateTime($value['stop_start_date'], $search_data['timezone']);
          $interval = $current_date_obj->diff($stop_start_date_obj);
          $left_day = $interval->format('%a');

          if (empty($value['stop_day_count'])) {
              $value_stop_end_date = _('Not Set');
              $value_stop_days = _('Not Set');
          } else {
              $value_stop_end_date = get_dt_format($value['stop_end_date'], $search_data['timezone']);

              $value_stop_days = number_format($value['stop_day_count'])._('Day');
          }
        ?>
        <?php echo _('Remain Day To Execute Day'); ?> : <?php echo $left_day; ?><?php echo _('Day'); ?>
      </td>
      <td class="text-center"><?php echo $value_stop_end_date; ?></td>
      <td class="text-center">
          <?php echo get_dt_format($value['request_date'], $search_data['timezone']); ?>
      </td>
      <td class="text-center">
      <?php if (empty($value['content_id'])): ?>
      <?php echo anchor('user-stop-contents/add?user_stop_id='.$value['user_stop_id'], _('Add Content'), array('class' => 'btn btn-primary btn-modal')); ?>
      <?php else: ?>
      <?php echo anchor('user-stop-contents/view/'.$value['content_id'], _('Show Content'), array('class' => 'btn btn-secondary btn-modal')); ?>
      <?php endif; ?>
      </td>
      <td class="text-center manage">
        <?php echo anchor('/user-stops/delete/'.$value['user_stop_id'], _('Cancel'), array('class' => 'btn btn-danger')); ?>
      </td>
    </tr>
    <?php endforeach; ?>
  </tbody>
</table>

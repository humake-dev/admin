<?php

$user_stopping_list = array();

foreach ($data['stopping_list']['list'] as $stopped_log) {
    foreach ($data['user_stopping_list']['list'] as $user_stop) {
        if ($stopped_log['user_stop_id'] == $user_stop['id']) {
            if (count($user_stopping_list)) {
                $alread_exists = false;
                $affect_order = 1;
                foreach ($user_stopping_list as $index => $user_stopping_list_content) {
                    if ($user_stopping_list_content['id'] == $stopped_log['user_stop_id']) {
                        $affect_order = ++$user_stopping_list[$index]['affect_order'];
                        $alread_exists = true;
                    }
                }

                if (empty($alread_exists)) {
                    $user_stop['affect_order'] = $affect_order;
                    $user_stopping_list[] = $user_stop;
                }
            } else {
                $user_stop['affect_order'] = 1;
                $user_stopping_list[] = $user_stop;
            }
        }
    }
}

?>
<table id="order_stop_list" class="table table-bordered table-hover">
  <colgroup>
    <col />
    <col />
    <col />
    <col />
    <col />
    <col />
    <col />
  </colgroup>
  <thead>
    <tr class="thead-default">
      <th class="text-center"><?php echo _('Stop Increment Number'); ?></th>
      <th class="text-center"><?php echo _('Stop Days'); ?></th>
      <th class="text-center"><?php echo _('Stop Start Date'); ?></th>
      <th class="text-center"><?php echo _('Stop End Date'); ?></th>
      <th class="text-center"><?php echo _('Request Date'); ?></th>
      <th class="text-center"><?php echo _('Affect Order'); ?></th>
      <th class="text-center"><?php echo _('Memo'); ?></th>
    </tr>
  </thead>
  <tbody>
    <?php foreach ($user_stopping_list as $index => $value): ?>
    <tr>
      <td class="text-center">
        <?php echo count($user_stopping_list) - $index; ?>
      </td>           
      <td class="text-center"><?php echo $value['stop_day_count']; ?><?php echo _('Day'); ?></td>
      <td class="text-center"><?php echo get_dt_format($value['stop_start_date'], $search_data['timezone']); ?></td>
      <td class="text-center"><?php echo get_dt_format($value['stop_end_date'], $search_data['timezone']); ?></td>
      <td class="text-center"><?php echo get_dt_format($value['request_date'], $search_data['timezone']); ?></td>
      <td class="text-center">
        <input type="hidden" value="<?php echo $value['id']; ?>" />
        <?php echo $value['affect_order']; ?><?php echo _('Count'); ?>
      </td>
      <td class="text-center">
      <?php if (empty($value['content_id'])): ?>
      <?php echo anchor('user-stop-contents/add?user_stop_id='.$value['id'], _('Add Content'), array('class' => 'btn btn-primary btn-modal')); ?>
      <?php else: ?>
      <?php echo anchor('user-stop-contents/view/'.$value['content_id'], _('Show Content'), array('class' => 'btn btn-secondary btn-modal')); ?>
      <?php endif; ?>
      </td>
    </tr>
    <?php endforeach; ?>
  </tbody>
</table>

<?php foreach ($user_stopping_list as $index => $user_stop): ?>
  <div id="user_stop_log_<?php echo $user_stop['id']; ?>" class="user_stop_log_detail">
<h3><?php echo _('Affect Order'); ?></h3>
<table  class="table table-bordered order-stop-detail">
  <colgroup>
    <col />  
    <col />
    <col />
  </colgroup>
  <thead>
    <tr class="thead-default">
      <th class="text-center"><?php echo _('Enroll Increment Number'); ?></th>      
      <th class="text-center"><?php echo _('Origin End Date'); ?></th>
      <th class="text-center"><?php echo _('Change End Date'); ?></th>
    </tr>
  </thead>
  <tbody>
    <?php
      $i = 0;
      foreach ($data['stopping_list']['list'] as $stopped_log):
        if ($stopped_log['user_stop_id'] != $user_stop['id']) {
            continue;
        }
    ?>
    <tr>
      <td class="text-center">
        <?php if (empty($stopped_log['in'])): ?>
        -
        <?php else: ?>
        <?php echo $stopped_log['in']; ?>
        <?php endif; ?>
      </td>      
      <td class="text-center"><?php echo get_dt_format($stopped_log['end_date'], $search_data['timezone']); ?></td>
      <td class="text-center"><?php echo get_dt_format($stopped_log['change_end_date'], $search_data['timezone']); ?></td>
    </tr>
    <?php ++$i; endforeach; ?>
  </tbody>
</table>
</div>
<?php endforeach; ?>

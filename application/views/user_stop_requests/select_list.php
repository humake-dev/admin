<table id="order_stop_select_list" class="table table-bordered table-hover">
  <colgroup>
    <?php if ($this->Acl->has_permission('enrolls', 'write')): ?>
    <col />
    <?php endif ?>
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
      <?php if ($this->Acl->has_permission('enrolls', 'write')): ?>
      <th class="text-center"><?php echo _('Use'); ?></th>
      <?php endif ?>
      <th class="text-center"><?php echo _('Enroll Increment Number'); ?></th>
      <th class="text-center"><?php echo _('Use Stop Days'); ?></th> 
      <th class="text-center"><?php echo _('Transaction Date'); ?></th>
      <th class="text-center"><?php echo _('Start Date'); ?></th>
      <th class="text-center"><?php echo _('End Date'); ?></th>
    </tr>
  </thead>
  <tbody>
    <?php foreach ($data['order_list']['list'] as $index => $value): ?>
    <tr>
      <?php if ($this->Acl->has_permission('enrolls', 'write')): ?>
      <td class="text-center">
        <?php
          if ($data['order_list']['total']==($index+1)):
            $checked = 'checked="checked"';
          else:
            $checked = '';
          endif;
         ?>
          <label style="display:block"><input type="radio" name="order_id" value="<?php echo $value['order_id']; ?>" <?php echo $checked; ?>></label>
      </td>
      <?php endif ?>
      <td class="text-center"><?php echo $value['in']; ?></td>
      <td class="text-center">
        <?php if($_SESSION['role_id']<=2): ?>
        <a href="/user-stop-customs/add?order_id=<?php echo $value['order_id'] ?>">
        <?php endif ?>
        <?php if (empty($value['total_stop_day_count'])): ?>
        <?php echo _('Not Use'); ?>
        <?php else: ?>
        <?php echo $value['total_stop_day_count']; ?><?php echo _('Day'); ?>
        <?php endif; ?>
        <?php if($_SESSION['role_id']<=2): ?>
        </a>
        <?php endif ?>
      </td>
      <td class="text-center"><?php echo get_dt_format($value['transaction_date'], $search_data['timezone']); ?></td>    
      <td class="text-center"><?php echo get_dt_format($value['start_date'], $search_data['timezone']); ?></td>      
      <td class="text-center"><?php echo get_dt_format($value['end_date'], $search_data['timezone']); ?></td>
    </tr>
    <?php endforeach; ?>
  </tbody>
</table>

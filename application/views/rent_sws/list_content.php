<div class="table-responsive">
<table id="user_rent_sw_list" class="table table-bordered table-hover">
  <colgroup>
    <col />  
    <col />
    <col />
    <col />
    <?php if ($this->router->fetch_class() == 'rent_sws'): ?>    
    <col />
    <?php endif; ?>
    <col />    
    <col />
    <col />
    <col />
    <col />
    <col style="width:100px" />
    <?php if($this->router->fetch_class() == 'rent_sws'): ?> 
    <?php if ($this->Acl->has_permission('rent_sws', 'delete') or $this->Acl->has_permission('rent_sws', 'write')): ?>
    <col style="width:150px">
    <?php endif ?>
    <?php endif ?>
  </colgroup>
  <thead class="thead-default">
    <tr>
      <th><?php echo _('Transaction Date'); ?></th>
      <th><?php echo _('Status'); ?></th>      
      <th><?php echo _('Product Name'); ?></th>
      <th class="text-center"><?php echo _('Period'); ?></th>
      <?php if ($this->router->fetch_class() == 'rent_sws'): ?>
      <th><?php echo _('User Name'); ?></th>
      <?php endif; ?>      
      <th class="text-center"><?php echo _('Start Date'); ?></th>
      <th class="text-center"><?php echo _('End Date'); ?></th>
      <th class="text-center"><?php echo _('Discount'); ?></th>
      <th class="text-center"><?php echo _('Sell Price'); ?></th>
      <th class="text-center"><?php echo _('Payment'); ?></th>
      <th class="text-center"><?php echo _('Memo'); ?></th>
      <?php if($this->router->fetch_class() == 'rent_sws'): ?> 
      <?php if ($this->Acl->has_permission('rent_sws', 'delete') or $this->Acl->has_permission('rent_sws', 'write')): ?>
      <th class="text-center"><?php echo _('Manage'); ?></th>
      <?php endif ?>
      <?php endif ?>
    </tr>
  </thead>
  <tbody>
    <?php if (empty($list['total'])): ?>
      <tr>
    <td colspan="10"><?php echo _('No Data'); ?></td>
    </tr>
    <?php else: ?>
    <?php foreach ($list['list'] as $value):

$page_param = '';
if ($this->input->get('page')) {
    $page_param = '?page='.$this->input->get('page');
}

$start_date_obj = new DateTime($value['start_date'], $search_data['timezone']);
$end_date_obj = new DateTime($value['end_date'], $search_data['timezone']);
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

    ?>
    <tr<?php if (!empty($data['rent_sws']['content']['id'])): ?><?php if ($value['id'] == $data['rent_sws']['content']['id']):?> class="table-primary"<?php endif; ?><?php endif; ?>>
      <td>
        <input type="hidden" value="<?php echo $value['id']; ?>">
        <input type="hidden" value="<?php echo $value['stopped']; ?>">      
        <?php echo get_dt_format($value['transaction_date'], $search_data['timezone']); ?>
        <input type="hidden" name="rent_sw_id[]" value="<?php echo $value['id']; ?>">
      </td>
      <td><?php echo $status; ?></td>
      <td class="enroll_category_name"><?php echo $value['product_name']; ?></td>
      <td><?php echo get_period($value['start_date'], $value['end_date'], $search_data['timezone']); ?></td>   
      <?php if ($this->router->fetch_class() == 'rent_sws'): ?>
      <td><?php echo $value['user_name']; ?></td>
      <?php endif; ?>
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
        <?php endif; ?>
        <input type="hidden" name="end_date[]" value="<?php echo $value['end_date']; ?>" />        
      </td>
      <td class="text-right">
      <?php
        $dc = $value['original_price'] * $value['dc_rate'] / 100;
        if ($dc):
        ?>
        <?php echo number_format($dc); ?><?php echo _('Currency'); ?>
      <?php else: ?>
        -
      <?php endif; ?>
      </td>      
      <td class="text-right"><?php echo number_format($value['price']); ?><?php echo _('Currency'); ?></td>
      <td class="text-right"><?php echo number_format($value['payment']); ?><?php echo _('Currency'); ?></td>
      <td class="text-center">
      <?php if (empty($value['content_id'])): ?>
      <?php echo anchor('rent-contents/add?order_id='.$value['order_id'], _('Add Content'), array('class' => 'btn btn-primary btn-modal')); ?>
      <?php else: ?>
      <?php echo anchor('rent-contents/view/'.$value['content_id'], _('Show Content'), array('class' => 'btn btn-secondary btn-modal')); ?>
      <?php endif; ?>
      </td>
      <?php if($this->router->fetch_class() == 'rent_sws'): ?> 
        <?php if ($this->Acl->has_permission('rent_sws', 'delete') or $this->Acl->has_permission('rent_sws', 'write')): ?>
      <td class="text-center">
        <?php if ($this->Acl->has_permission('rent_sws', 'edit')): ?>
        <?php echo anchor('rent-sws/edit/'.$value['id'].$page_param, _('Edit'), array('class' => 'btn btn-secondary')); ?>
        <?php endif ?>
        <?php if ($this->Acl->has_permission('rent_sws', 'delete')): ?>
        <?php echo anchor('rent-sws/delete-confirm/'.$value['id'], _('Delete'), array('class' => 'btn btn-danger btn-delete-confirm')); ?>
        <?php endif ?>
      </td>
      <?php endif ?>
      <?php endif ?>      
    </tr>
    <?php endforeach; ?>
    <?php endif; ?>
  </tbody>
</table>
</div>
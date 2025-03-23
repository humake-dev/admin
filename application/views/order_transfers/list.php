<table id="order_transfer_list" class="table table-bordered table-hover">
  <colgroup>
    <col />
    <col />
    <col />
    <col />
    <col />
    <col style="width:160px" />
    <?php if ($this->session->userdata('role_id') < 3): ?>
    <col style="width:100px" />
    <?php endif ?>
  </colgroup>
  <thead>
    <tr class="thead-default">
      <th><?php echo _('Product'); ?></th>
      <th><?php echo _('Giver'); ?></th>
      <th><?php echo _('Transfer Quantity'); ?></th>
      <th><?php echo _('Recipient'); ?></th>
      <th><?php echo _('Transfer Date'); ?></th>
      <th class="text-center"><?php echo _('Transfer Memo'); ?></th>
      <?php if ($this->session->userdata('role_id') < 3): ?>
        <th class="text-center"><?php echo _('Delete'); ?></th>
      <?php endif ?>
    </tr>
  </thead>
  <tbody>
    <?php if (empty($data['transfer_list']['total'])): ?>
    <tr>
      <td class="text-center" colspan="6"><?php echo _('No Data'); ?></td>
    </tr>
    <?php else: ?>
    <?php foreach ($data['transfer_list']['list'] as $value): ?>
    <tr>
      <td>
      <?php if (!empty($value['product_category'])): ?>
      <?php echo $value['product_category']; ?> / 
      <?php endif; ?>
      <?php echo $value['product_name']; ?></td>
      <td>
        <?php if ($data['content']['id'] == $value['giver_id']): ?>
        <?php echo $value['giver_name']; ?>(<?php echo _('Current User'); ?>)
        <?php else: ?>
        <?php if(empty($value['origin_branch_id'])): ?>
        <?php echo anchor('home/view/'.$value['giver_id'], $value['giver_name']); ?>
        <?php else: ?>
          <?php echo $value['giver_name']; ?>(<?php echo $value['origin_branch_name']; ?>)
        <?php endif; ?>
        <?php endif; ?>
      </td>
      <td>
        <?php if (empty($value['lesson_type'])): ?>
          <?php echo $value['give_count']; ?><?php echo _('Day'); ?>
        <?php else: ?>

        <?php if($value['lesson_type']==1): ?>
        <?php if ($data['content']['id'] == $value['giver_id']): ?>
          <?php if($value['transfer_date']<=$value['origin_start_date']): ?>
            <?php echo get_dt_format($value['origin_start_date']); ?>
          <?php else: ?>
            <?php echo get_dt_format($value['transfer_date']); ?>
          <?php endif ?>                  
          ~ <?php echo get_dt_format($value['origin_end_date']); ?> / <?php echo $value['give_count']; ?><?php echo _('Day'); ?>
        <?php else: ?>
        <?php if($value['transfer_date']<=$value['start_date']): ?>
            <?php echo get_dt_format($value['start_date']); ?>
          <?php else: ?>
            <?php echo get_dt_format($value['transfer_date']); ?>
          <?php endif ?> 

        ~ <?php echo get_dt_format($value['end_date']); ?> / <?php echo $value['give_count']; ?><?php echo _('Day'); ?>
        <?php endif; ?>
<?php else: ?>
  <?php echo $value['give_count']; ?><?php echo _('Count'); ?>
<?php endif ?>
        <?php endif; ?>
      </td>
      <td>
        <?php if ($data['content']['id'] == $value['recipient_id']): ?>
        <?php echo $value['recipient_name']; ?>(<?php echo _('Current User'); ?>)
        <?php else: ?>
        <?php if(empty($value['transfer_branch_id'])): ?>
        <?php echo anchor('home/view/'.$value['recipient_id'], $value['recipient_name']); ?>
        <?php else: ?>
          <?php echo $value['recipient_name']; ?>(<?php echo $value['transfer_branch_name']; ?>)
        <?php endif ?>
        <?php endif; ?>
      </td>
      <td>
        <?php echo get_dt_format($value['transfer_date'], $search_data['timezone']); ?>
      </td>
      <td class="text-center">
      <?php if (empty($value['content_id'])): ?>
      <?php echo anchor('order-transfer-contents/add?order_transfer_id='.$value['id'], _('Add Content'), array('class' => 'btn btn-primary btn-modal')); ?>
      <?php else: ?>
      <?php echo anchor('order-transfer-contents/view/'.$value['content_id'], _('Show Content'), array('class' => 'btn btn-secondary btn-modal')); ?>
      <?php endif; ?>
      </td>
      <?php if ($this->session->userdata('role_id') < 3): ?>
        <td class="text-center">
        <?php echo anchor('order-transfers/delete/' . $value['id'], _('Delete'), array('class' => 'btn btn-danger btn-delete-confirm')); ?>
        </td>
      <?php endif ?>
    </tr>
    <?php endforeach; ?>
    <?php endif; ?>
  </tbody>
</table>

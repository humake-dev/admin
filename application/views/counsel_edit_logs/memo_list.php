<table id="prepare_list" class="table table-striped table-hover">
  <colgroup>
    <col />
    <col />
    <col />
    <col />
    <col style="width:200px" />
    <?php if($this->session->userdata('role_id')<=5): ?>    
    <col style="width:100px" />
    <?php endif ?>
  </colgroup>
  <thead class="thead-default">
    <tr>
      <th><?php echo _('Product') ?></th>
      <th><?php echo _('Change Field Count') ?></th>
      <th><?php echo _('Revision') ?></th>
      <th><?php echo _('Content') ?></th>
      <th><?php echo _('Updated At') ?></th>
      <?php if($this->session->userdata('role_id')<=5): ?>      
      <th class="text-center"><?php echo _('Manage') ?></th>
      <?php endif ?>
    </tr>
  </thead>
  <tbody>
    <?php if(empty($data['total'])): ?>
    <tr>
      <td colspan="<?php if($this->session->userdata('role_id')<=5): ?>6<?php else:?>5<?php endif ?>" class="text-center"><?php echo _('No Data') ?></td>
    </tr>
    <?php else: ?>
    <?php foreach ($data['list'] as $index=>$value): ?>
    <tr<?php if (isset($data['content'])): ?><?php if ($data['content']['id']==$value['id']): ?> class="table-primary"<?php endif ?><?php endif ?>>
      <td><?php echo $value['product_name'] ?></td>
      <td><?php echo anchor('order-edit-logs/view/'.$value['id'], $value['field_change_count']) ?></td>
      <td><?php echo $value['revision'] ?></td>
      <td><?php echo anchor('order-edit-log-contents/view/'.$value['id'], _('Show Content'), array('class'=>'btn btn-secondary btn-modal')) ?></td>
      <td><?php echo get_dt_format($value['created_at'],$search_data['timezone']) ?></td>
      <?php if($this->session->userdata('role_id')<=5): ?>
      <td class="text-center">
        <?php echo anchor('order-edit-logs/delete/'.$value['id'], _('Delete'), array('class'=>'btn btn-danger btn-delete-confirm')) ?>
      </td>
      <?php endif ?>
    </tr>
    <?php endforeach ?>
    <?php endif ?>
  </tbody>
</table>

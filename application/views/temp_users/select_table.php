<input type="hidden" id="user_select_list_count" value="<?php if (empty($data['total'])): ?>0<?php else: ?><?php echo $data['total']; ?><?php endif; ?>" />
<table id="user_select_list" class="table table-hover">
  <colgroup>
    <col style="width:60px;" />
    <col />
    <col />
    <col style="width:80px;"  />
  </colgroup>
  <thead>
    <tr class="thead-default">
      <th class="text-center"><input id="user_select_check_all" type="checkbox"></th>
      <th><?php echo _('User Name'); ?></th>
      <th><?php echo _('Phone'); ?></th>
      <th><?php echo _('Manage'); ?></th>
    </tr>
  </thead>
  <tbody>
    <?php if (empty($data['total'])): ?>
    <tr>
      <td colspan="4" class="text-center"><?php echo _('No Data'); ?></td>
    </tr>
    <?php else: ?>
    <?php foreach ($data['list'] as $index => $value): ?>
      <tr>
        <td class="text-center"><input type="checkbox" name="id[]" value="<?php echo $value['id']; ?>"></td>
        <td class="name"><?php echo $value['name']; ?></td>
        <td class="phone"><?php echo get_hyphen_phone($value['phone']); ?></td>
        <td class="text-center manage">
          <?php echo anchor('temp-users/delete/'.$value['id'], _('Delete'), array('class' => 'btn btn-danger btn-delete-confirm')); ?>
        </td>
      </tr>
    <?php endforeach; ?>
    <?php endif; ?>
  </tbody>
</table>
<div class="sl_pagination">
  <?php if (!empty($data['total'])): ?>
  <?php echo $this->pagination->create_links(); ?>
  <?php endif; ?>
</div>
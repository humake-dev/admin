<div class="row">
  <?php if (!empty($data['user']['total'])): ?>
  <div class="col-12">
    <h2 class="float-left"><?php echo _('User List'); ?></h2>
    <div class="float-right">
      <p class="summary">
        <span id="list_count" style="display:none"><?php echo $data['user']['total']; ?></span>
        <?php echo sprintf(_('There Are %d User'), $data['user']['total']); ?>
      </p>
    </div>
  </div>
  <?php endif ?>
  <div class="col-12">

<input type="hidden" id="user_select_list_count" value="<?php if (empty($data['user']['total'])): ?>0<?php else: ?><?php echo $data['user']['total']; ?><?php endif; ?>" />
<table id="user_select_list" class="table table-hover">
  <colgroup>
    <col style="width:60px;" />
    <col />
    <?php if (!empty($common_data['branch']['use_access_card'])): ?>
    <col />
    <?php endif; ?>
    <col />
    <col />
    <col />
  </colgroup>
  <thead>
    <tr class="thead-default">
      <th>
        <?php if (empty($data['type'])): ?>
        <?php echo _('Check'); ?>
        <?php else: ?>
        <?php if ($data['type'] == 'multi'): ?>
        <input id="user_select_check_all" type="checkbox" />
        <?php else: ?>
        <?php echo _('Check'); ?>
        <?php endif; ?>
        <?php endif; ?>        
      </th>
      <th><?php echo _('User Name'); ?></th>
      <?php if (!empty($common_data['branch']['use_access_card'])): ?>   
      <th id="th_access_card_no"><?php echo _('Access Card No'); ?></th>
      <?php endif; ?>
      <th><?php echo _('Birthday'); ?></th>
      <th><?php echo _('Gender'); ?></th>
      <th><?php echo _('Phone'); ?></th>
    </tr>
  </thead>
  <tbody>
    <?php if (empty($data['user']['total'])): ?>
    <tr>
      <td colspan="6" class="text-center"><?php echo _('No Data'); ?></td>
    </tr>
    <?php else: ?>      
    <?php foreach ($data['user']['list'] as $index => $value): ?>
      <tr>
        <td>
          <?php if(empty($value['user_id'])): ?>
            <input type="checkbox" name="temp_user_id[]" value="<?php echo $value['temp_user_id']; ?>">
            <?php else: ?>
          <input type="checkbox" name="id[]" value="<?php echo $value['user_id']; ?>">
          <?php endif ?>
        </td>
        <td class="name">
          <?php echo $value['name']; ?>
        </td>
        <?php if (!empty($common_data['branch']['use_access_card'])): ?>
        <td>
          <?php if(empty($value['card_no'])): ?>
            없음
            <?php else: ?>
        <?php echo $value['card_no']; ?>
        <?php endif ?>
      </td>
        <?php endif; ?>
        <td>
          <?php if (empty($value['birthday']) or trim($value['birthday']) == '0000-00-00'): ?><?php echo _('No Inserted'); ?><?php else: ?><?php echo $value['birthday']; ?><?php endif; ?>
        </td>
          <td>
            <?php echo display_gender($value['gender']); ?>
            <input type="hidden" name="gender[]" value="<?php echo $value['gender']; ?>">
          </td>
          <td class="phone"><?php echo get_hyphen_phone($value['phone']); ?></td>
      </tr>
    <?php endforeach; ?>
    <?php endif; ?>
  </tbody>
</table>
<div class="sl_pagination">
  <?php if (!empty($data['user']['total'])): ?>
  <?php echo $this->pagination->create_links(); ?>
  <?php endif; ?>
</div>
</div>
</div>
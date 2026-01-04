<table id="trainer-active-list" class="table table-striped">
  <colgroup>
    <col />
    <col />
    <col />
    <col />    
    <col />
    <col />
    <col />    
  </colgroup>
  <thead class="thead-default">
    <tr>
      <th><?php echo _('Manage Trainer') ?></th>
      <th class="text-right"><?php echo _('Charge User') ?></th>
      <th class="text-right"><?php echo _('Execute Order') ?></th>      
      <th class="text-right"><?php echo _('Execute User') ?></th>
      <th class="text-right"><?php echo _('Period Use Quantity') ?></th>
      <th class="text-right"><?php echo _('Commission') ?></th>
    </tr>
  </thead>
  <tbody>
    <?php if(empty($data['total'])): ?>
    <tr>
      <td colspan="8"><?php echo _('No Data') ?></td>
    </tr>
    <?php else: ?>    
    <?php foreach ($data['list'] as $index=>$value): ?>
    <tr>
      <td><?php echo $value['name'] ?></td>
      <td class="text-right"><?php echo number_format($value['charge_user']) ?><?php echo _('Count People') ?></td>
      <td class="text-right">
      <?php if(empty($value['count_order'])): ?>
      <span><?php echo $value['count_order']._('Count') ?></span>
      <?php else: ?>
      <?php echo anchor('/trainer-actives/view/'.$value['employee_id'].$params,number_format($value['count_order'])._('Count')) ?>
      <?php endif ?>
      </td>
      <td class="text-right">
      <?php if(empty($value['execute_user'])): ?>
      <span><?php echo $value['execute_user']._('Count People') ?></span>
      <?php else: ?>
      <?php echo number_format($value['execute_user'])._('Count People') ?>
      <?php endif ?>
      </td>
      <td class="text-right">
      <?php if(empty($value['period_use'])): ?>
      0<?php echo _('Count Time') ?>
      <?php else: ?>            
      <?php echo $value['period_use']._('Count Time') ?>
      <?php endif ?>
      </td>
      <td class="text-right"><?php echo number_format($value['commission']) ?><?php echo _('Currency') ?></td>
    </tr>
    <?php endforeach ?>
    <?php endif ?>
  </tbody>
</table>
<?php echo $this -> pagination -> create_links() ?>

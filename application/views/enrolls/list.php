<?php
  if (empty($table_id)) {
      $table_id = 'user_enroll_list';
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
    <col />
    <col />    
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
      <th><?php echo _('Enroll Increment Number'); ?></th>
      <th><?php echo _('Transaction Date'); ?></th>
      <th class="text-center"><?php echo _('Status'); ?></th>
      <th class="text-center"><?php echo _('Course'); ?></th>
      <th class="text-center"><?php echo _('Enroll Trainer'); ?></th>
      <th><?php echo _('Quantity'); ?> | <?php echo _('Period'); ?></th>
      <th><?php echo _('Lesson Type'); ?></th>
      <th class="text-center"><?php echo _('Start Date'); ?></th>
      <th class="text-center"><?php echo _('End Date'); ?></th>
      <th><?php echo _('Remain Count'); ?></th>
      <th class="text-center"><?php echo _('Original Price'); ?></th> 
      <th><?php echo _('Discount'); ?></th>
      <th class="text-center"><?php echo _('Sell Price'); ?></th>    
      <th class="text-center"><?php echo _('Payment'); ?></th>
      <th class="text-center"><?php echo _('Change Content'); ?></th>
      <th class="text-center"><?php echo _('Memo'); ?></th>
    </tr>
  </thead>
  <tbody>
    <?php
      $enroll_list = array('total' => 0, 'list' => array());
      $pt_list = array('total' => 0, 'list' => array());

      if (!empty($data['enroll']['total'])) {
          foreach ($data['enroll']['list'] as $index => $value) {
              if (empty($value['in']) and $value['lesson_type']==4) {
                  $value['order_no'] = $pt_list['total'];
                  ++$pt_list['total'];
                  $pt_list['list'][] = $value;
              } else {
                  $value['order_no'] = $enroll_list['total'];
                  ++$enroll_list['total'];
                  $enroll_list['list'][] = $value;
              }
          }
      }
    ?>
    <?php if (empty($enroll_list['total']) and empty($pt_list['total'])): ?>
      <tr>
      <td colspan="20"><?php echo _('No Data'); ?></td>
    </tr>
    <?php
      else:

        if ($enroll_list['total']) {
            $enroll_total = $enroll_list['total'];
            $list = $enroll_list;
            include 'list_content.php';
        }

        if ($pt_list['total']) {
            if (!empty($enroll_list['total'])) {
                echo '<tr class="no-event"><td colspan="19" style="border:none;">'._('PT').'</td></tr>';
            }
            $enroll_total = $pt_list['total'];
            $list = $pt_list;
            include 'list_content.php';
        }
      endif;
    ?>
  </tbody>
</table>

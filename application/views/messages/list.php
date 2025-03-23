<div class="row">
  <div class="col-12">
    <h2 class="float-left"><?php echo _('Message List'); ?></h2>
    <div class="float-right">
      <p class="summary">
        <span id="list_count" style="display:none"><?php echo $data['total']; ?></span>
        <?php echo sprintf(_('There Are %d Message'), $data['total']); ?>
      </p>
    </div>
  </div>
  <div class="col-12">
    <table id="send_list" class="table table-striped table-hover">
      <colgroup>
        <col>
        <col>
        <col>
        <col class="d-none d-md-table-cell">
        <col class="d-none d-md-table-cell">          
        <col class="d-none d-sm-table-cell">
        <?php if ($this->Acl->has_permission('messages', 'delete')): ?>
        <col class="d-none d-md-table-cell" style="width:90px">
        <?php endif; ?>
      </colgroup>
      <thead class="thead-default">
        <tr>
          <th><?php echo _('Send Type'); ?></th>
          <th><?php echo _('User'); ?></th>
          <th><?php echo _('Title'); ?></th>
          <th class="d-none d-md-table-cell"><?php echo _('Content'); ?></th>
          <th class="d-none d-sm-table-cell"><?php echo _('SMS Fee'); ?></th>      
          <th class="d-none d-sm-table-cell" style="width:180px"><?php echo _('Created At'); ?></th>
          <?php if ($this->Acl->has_permission('messages', 'delete')): ?>
          <th class="d-none d-md-table-cell text-center" style="width:90px"><?php echo _('Manage'); ?></th>
          <?php endif; ?>
        </tr>
      </thead>
      <tbody>
      <?php if (empty($data['total'])): ?>
        <tr>
          <td colspan="7" class="text-center"><?php echo _('No Data'); ?></td>
        </tr>
      <?php else: ?>
      <?php foreach ($data['list'] as $index => $value): ?>
        <tr>
          <td><?php echo display_send_type($value['type']); ?></td>
          <td>
            <?php
              if ($value['send_all']):
                echo _('Send All');
              else:
                echo sl_message_change($value['message_users'], $value['message_temp_users']);
              endif;
            ?>
          </td>
          <td><?php echo anchor('messages/view/'.$value['id'], $value['title']); ?></td>
          <td class="d-none d-md-table-cell"><?php echo anchor('message-contents/view/'.$value['id'], _('Show Content'), ['class' => 'btn btn-secondary btn-modal']); ?></td>
          <td class="d-none d-sm-table-cell">
          <?php if (empty($value['success_cnt'])) : ?>
                -
          <?php else :
            if ($value['msg_type'] == 'SMS') {
              $fee = SMS_FEE['sms'];
            } elseif ($value['msg_type'] == 'LMS') {
              $fee = SMS_FEE['lms'];
            } else {
              $fee = SMS_FEE['mms'];
            }
          ?>
          <?php echo number_format($fee * $value['success_cnt'], 1); ?><?php echo _('Currency'); ?>
          <?php endif; ?>
          </td>
          <td class="d-none d-sm-table-cell"><?php echo get_dt_format($value['created_at'], $search_data['timezone']); ?></td>
          <?php if ($this->Acl->has_permission('messages', 'delete')): ?>
          <td class="d-none d-md-table-cell text-center"><?php echo anchor('messages/delete/'.$value['id'], _('Delete'), ['class' => 'btn btn-danger btn-delete-confirm']); ?></td>
          <?php endif; ?>
        </tr>
        <?php endforeach; ?>
        <?php endif; ?>
      </tbody>
    </table>
    <?php echo $this->pagination->create_links(); ?>
    <?php if ($this->Acl->has_permission('messages', 'write')): ?>
    <?php echo anchor('messages/add', _('Add'), ['class' => 'btn btn-primary']); ?>
    &nbsp;<?php echo anchor('message-excels/add', _('Add By Excel'), ['class' => 'btn btn-secondary']); ?>
    <?php endif; ?>
  </div>
</div>

<div class="col-12 list">
  <div class="row">
    <div class="col-12">
      <h2 class="float-left"><?php echo _('Send End Alert Message List'); ?></h2>
      <div class="float-right">
        <p class="summary">
          <span id="list_count" style="display:none"><?php echo $data['total']; ?></span>
          <?php echo sprintf(_('There Are %d Send End Alert Message'), $data['total']); ?>
        </p>
    	</div>
    </div>
    <article class="col-12">
      <table id="counsel_list" class="table table-bordered table-hover">
        <colgroup>
          <col />
          <col />
          <col />
          <col />          
          <col style="width:150px" />
        </colgroup>
        <thead class="thead-default">
          <tr>
            <th><?php echo _('Course'); ?></th>
            <th><?php echo _('Message Prepare'); ?></th>
            <th><?php echo _('Type'); ?></th>
            <th><?php echo _('Execute Before Day Count'); ?></th>
            <th class="text-center"><?php echo _('Manage'); ?></th>
          </tr>
        </thead>
        <tbody>
          <?php if ($data['total']): ?>
          <?php foreach ($data['list'] as $index => $value):
              $page_param = '';
              if ($this->input->get('page')) {
                  $page_param = '?page='.$this->input->get('page');
              }
          ?>
          <tr>
            <td><?php echo $data['primary_course']['product_name']; ?></td>
            <td><?php echo $value['title']; ?></td>
            <td>
            <?php
              switch ($value['type']) {
                case 'push_only':
                  echo _('Only Use Push');
                  break;
                case 'use_push_available':
                  echo _('Use Push IF Available,or SMS');
                  break;
                default:
                  echo _('Only Use SMS');
              }
            ?>
            </td>
            <td><?php echo $value['day_count']; ?><?php echo _('Day'); ?></td>
            <td>
              <?php echo anchor('send-end-alert-messages/edit/'.$value['id'].$page_param, _('Edit'), array('class' => 'btn btn-secondary')); ?>
              <?php echo anchor('send-end-alert-messages/delete/'.$value['id'], _('Delete'), array('class' => 'btn btn-danger btn-delete-confirm')); ?>
            </td>
          </tr>
          <?php endforeach; ?>
          <?php else: ?>
          <tr>
            <td colspan="4" style="text-align:center"><?php echo _('No Data'); ?></td>
          </tr>
          <?php endif; ?>
        </tbody>
      </table>
      <?php echo $this->pagination->create_links(); ?>
      <?php echo anchor('send-end-alert-messages/add', _('Add'), array('class' => 'btn btn-primary')); ?>
    </article>
  </div>
</div>

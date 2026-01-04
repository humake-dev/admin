<div class="col-12 col-xxl-9 list">
  <div class="row">
    <div class="col-12">
      <h2 class="float-left"><?php echo _('Other List'); ?></h2>
      <div class="float-right">
    		<p class="summary">
          <span id="list_count" style="display:none"><?php echo $data['total']; ?></span>
          <?php echo sprintf(_('There Are %d Other'), $data['total']); ?>
        </p>
    	</div>
    </div>
    <article class="col-12">
    		<table id="prepare_list" class="table table-striped table-hover">
          <colgroup>
            <col  />
            <col  />
            <col  />
            <col  />
            <col  />
            <col style="width:200px" />
    				<col style="width:150px" />
          </colgroup>
          <thead class="thead-default">
            <tr>
              <th><?php echo _('User'); ?>
              <?php if (!empty($common_data['branch']['use_access_card'])): ?>
              (<?php echo _('Access Card No'); ?>)
              <?php endif; ?>
              </th>
              <th><?php echo _('Content'); ?></th>
              <th class="text-right"><?php echo _('Price'); ?></th>
              <th class="text-right"><?php echo _('Cash'); ?></th>
              <th class="text-right"><?php echo _('Credit'); ?></th>
              <th class="text-center"><?php echo _('Transaction Date'); ?></th>
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
            <tr<?php if (isset($data['content'])): ?><?php if ($data['content']['id'] == $value['id']): ?> class="table-primary"<?php endif; ?><?php endif; ?>>
              <td><?php echo anchor('/view/'.$value['user_id'], $value['user_name'].'('.$value['card_no'].')'); ?></td>
              <td><?php echo anchor('/others/view/'.$value['id'], $value['title']); ?></td>
              <td class="text-right"><?php if ($value['price']): ?><?php echo number_format($value['price']); ?><?php echo _('Currency'); ?><?php else: ?>-<?php endif; ?></td>
              <td class="text-right"><?php if ($value['cash']): ?><?php echo number_format($value['cash']); ?><?php echo _('Currency'); ?><?php else: ?>-<?php endif; ?></td>
              <td class="text-right"><?php if ($value['credit']): ?><?php echo number_format($value['credit']); ?><?php echo _('Currency'); ?><?php else: ?>-<?php endif; ?></td>
              <td class="text-center"><?php echo get_dt_format($value['transaction_date'], $search_data['timezone']); ?></td>
              <td class="text-center">
                <?php echo anchor('others/edit/'.$value['id'].$page_param, _('Edit'), array('class' => 'btn btn-secondary')); ?>
                <?php echo anchor('others/delete/'.$value['id'], _('Refund'), array('class' => 'btn btn-danger')); ?>
              </td>
            </tr>
    				<?php endforeach; ?>
    				<?php else: ?>
    				<tr>
    					<td colspan="7" class="text-center"><?php echo _('No Data'); ?></td>
    				</tr>
    				<?php endif; ?>
          </tbody>
        </table>
        <?php echo $this->pagination->create_links(); ?>
        <?php if ($this->Acl->has_permission('accounts')): ?>
        <?php echo anchor('others/add', _('Add'), array('class' => 'btn btn-primary hidden-xxl')); ?>
        <?php endif; ?>
    </article>
  </div>
</div>

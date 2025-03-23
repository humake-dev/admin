<div id="accounts" class="container">
  <div class="row">
  <?php echo $Layout->Element('accounts/nav'); ?>
    <article class="col-12">
      <div class="card">
      <div class="card-body">
        <div class="form-row">
          <h3 class="col-12"><?php echo _('Other sales details'); ?></h3>
          <div class="col-6">
          </div>
        </div>
      </div>
      </div>
    </article>
    <article class="col-12">
      <table class="table table-bordered">
        <colgroup>
          <col />
          <col />
          <col />          
          <col />
          <col />
          <col />
          <col />
          <?php if ($this->Acl->has_permission('accounts')): ?>
          <col style="width:150px" />
          <?php endif; ?>
        </colgroup>
        <thead class="thead-default">
          <tr>
            <th class="text-center"><?php echo _('Category'); ?></th>
            <th class="text-center"><?php echo _('Title'); ?></th>            
            <th class="text-center"><?php echo _('User'); ?></th>    
            <th class="text-center"><?php echo _('Income'); ?> / <?php echo  _('Outcome'); ?></th>
            <th class="text-center"><?php echo _('Transaction Date'); ?></th>
            <th class="text-center"><?php echo _('Cash'); ?></th>
            <th class="text-center"><?php echo _('Credit'); ?></th>
            <?php if ($this->Acl->has_permission('accounts')): ?>
            <th class="text-center"><?php echo _('Manage'); ?></th>
            <?php endif; ?>
          </tr>
        </thead>
        <tbody>
        <?php if (empty($data['total'])): ?>
          <tr>
            <td colspan="8"><?php echo _('No Data'); ?></td>
          </tr> 
        <?php else: ?>
          <?php foreach ($data['list'] as $index => $value): ?>
          <tr>
              <td><?php echo _($value['account_category_name']); ?></td>
              <td>
              <?php echo $value['product_name']; ?>
              </td>              
              <td class="text-center">
                <?php if (empty($value['user_id'])): ?>
                <?php echo _('Deleted User'); ?>
                <?php else: ?>
                <?php echo anchor('/view/'.$value['user_id'], $value['user_name']); ?>
                <?php endif; ?>
                <?php echo $value['user_id']; ?>                
              </td>       
              <td class="text-right">
                <?php if ($value['type'] == 'I'): ?>
                <span class="text-success"><?php echo _('Income'); ?></span>
                <?php else: ?>
                <span class="text-danger"><?php echo _('Outcome'); ?></span>
                <?php endif; ?>
              </td>
              <td class="text-right"><!-- <?php echo get_dt_format($value['transaction_date'], $search_data['timezone']); ?> -->
              <?php echo $value['transaction_date']; ?>
              </td>
              <td class="text-right"><!--<?php echo number_format($value['cash']); ?><?php echo _('Currency'); ?>-->
              <?php echo $value['cash']; ?>
              </td>
              <td class="text-right"><!--<?php echo number_format($value['credit']); ?><?php echo _('Currency'); ?>-->
              <?php echo $value['credit']; ?>
              </td>
              <?php if ($this->Acl->has_permission('accounts')): ?>
              <td class="text-center">
                <?php echo anchor('accounts/edit/'.$value['id'], _('Edit'), array('class' => 'btn btn-default')); ?>
                <?php echo anchor('accounts/delete/'.$value['id'], _('Delete'), array('class' => 'btn btn-danger btn-delete-confirm')); ?>
              </td>
              <?php endif; ?>
            </tr>
          <?php endforeach; ?>
          <?php endif; ?>
        </table>
        <?php echo $this->pagination->create_links(); ?>
    </article>
  </div>
</div>

<div id="facilities" class="container">
<div class="row">
  <div class="col-12">
    <h2 class="float-left"><?php echo _('Facility List'); ?></h2>
    <div class="float-right">
      <p class="summary">
        <span id="list_count" style="display:none"><?php echo $data['total']; ?></span>
        <?php echo sprintf(_('There Are %d Facility'), $data['total']); ?>
      </p>
    </div>
  </div>
  <div class="col-12">
    <table id="counsel_list" class="table table-bordered table-hover">
      <colgroup>
        <col style="width:100px" />
        <col />
        <col />
        <col />
        <col />
        <col />
        <col />    
        <col style="width:100px" />
        <col style="width:150px" />
      </colgroup>
      <thead class="thead-default">
        <tr>
          <th><?php echo _('Order No'); ?></th>
          <th><?php echo _('Facility Title'); ?></th>       
            <th class="text-center"><?php echo _('Gender'); ?></th>
            <th class="text-center"><?php echo _('Quantity'); ?></th>
            <th class="text-center"><?php echo _('Start No'); ?></th>
            <th class="text-center"><?php echo _('Use Not Set'); ?></th>
            <th class="text-center"><?php echo _('Price'); ?></th>
            <th class="text-center"><?php echo _('Memo'); ?></th>
            <th class="text-center"><?php echo _('Manage'); ?></th>
          </tr>
        </thead>
        <tbody>
          <?php if (empty($data['total'])): ?>
          <tr>
            <td colspan="9"><?php echo _('No Data'); ?></td>
          </tr>
          <?php else: ?>
          <?php foreach ($data['list'] as $index => $value):
            $page_param = '';
            if ($this->input->get('page')) {
                $page_param = '?page='.$this->input->get('page');
            }
          ?>
          <tr<?php if (isset($data['content'])): ?><?php if ($data['content']['id'] == $value['id']): ?> class="table-primary"<?php endif; ?><?php endif; ?>>
            <td><?php echo $value['order_no']; ?></td>
            <td><?php echo anchor('facilities/view/'.$value['id'].$page_param, $value['title']); ?></td>
            <td class="text-right"><?php echo display_gender($value['gender']); ?></td>
            <td class="text-right"><?php echo $value['quantity']; ?></td>
            <td class="text-right"><?php echo $value['start_no']; ?></td>
            <td class="text-right">
            <?php if (empty($value['use_not_set'])): ?>
            <?php echo _('Not Use'); ?>
            <?php else: ?>
            <?php echo _('Use'); ?>
            <?php endif; ?>
            </td>
            <td class="text-right"><?php echo number_format($value['price']); ?><?php echo _('Currency'); ?></td>
            <td>
              <?php if (empty($value['content_id'])): ?>
              <?php echo anchor('product-contents/add?product_id='.$value['product_id'], _('Add Content'), array('class' => 'btn btn-primary btn-modal')); ?>
              <?php else: ?>
              <?php echo anchor('product-contents/view/'.$value['content_id'], _('Show Content'), array('class' => 'btn btn-secondary btn-modal')); ?>
              <?php endif; ?>
            </td>
            <td>
              <?php if ($this->Acl->has_permission('facilities','write')): ?>
              <?php echo anchor('facilities/edit/'.$value['id'].$page_param, _('Edit'), array('class' => 'btn btn-secondary')); ?>
              <?php endif ?>
              <?php if ($this->Acl->has_permission('facilities','delete')): ?>
              <?php echo anchor('facilities/delete/'.$value['id'], _('Delete'), array('class' => 'btn btn-danger btn-delete-confirm')); ?>
              <?php endif ?>
            </td>
          </tr>
          <?php endforeach; ?>
          <?php endif; ?>
        </tbody>
      </table>
      <?php echo $this->pagination->create_links(); ?>
    </div>
    <div class="col-12">
      <?php echo anchor('facilities/add', _('Add'), array('class' => 'btn btn-primary')); ?>
    </div>
  </div>  
</div>

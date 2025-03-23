<div class="col-12 col-xxl-9 list">
  <div class="row">
    <div class="col-12">
      <h2 class="float-left"><?php echo _('Course Category List'); ?></h2>
      <div class="float-right">
        <p class="summary">
          <span id="list_count" style="display:none"><?php echo $data['total']; ?></span>
          <?php echo sprintf(_('There Are %d Course Category'), $data['total']); ?>
        </p>
    	</div>
    </div>
    <article class="col-12">
      <table id="counsel_list" class="table table-bordered table-hover">
        <colgroup>
          <col style="width:100px" />
          <col />
          <col style="width:100px" />          
          <col style="width:100px" />
          <col style="width:150px" />
        </colgroup>
        <thead class="thead-default">
          <tr>
            <th><?php echo _('Order No'); ?></th>
            <th><?php echo _('Title'); ?></th>
            <th><?php echo _('Use Count'); ?></th>
            <th class="text-center"><?php echo _('Memo'); ?></th>
            <th class="text-center"><?php echo _('Manage'); ?></th>
          </tr>
        </thead>
        <tbody>
          <?php if ($data['total']): ?>
          <?php foreach ($data['list'] as $index => $value): ?>
          <tr<?php if (isset($data['content'])): ?><?php if ($data['content']['id'] == $value['id']): ?> class="table-primary"<?php endif; ?><?php endif; ?>>
            <td><?php echo $value['order_no']; ?></td>
            <td><?php echo anchor('course-categories/view/'.$value['id'], $value['title']); ?></td>
            <td>
            <?php if (empty($value['product_counts'])): ?>
            <span class="text-warning"><?php echo _('Not Use'); ?></span>
            <?php else: ?>
            <?php echo $value['product_counts']; ?><?php echo _('Count'); ?>         
            <?php endif; ?>
            </td>
            <td>
            <?php if (empty($value['content_id'])): ?>
                <?php echo anchor('product-category-contents/add?product_category_id='.$value['id'], _('Add Content'), array('class' => 'btn btn-primary btn-modal')); ?>
                <?php else: ?>
                <?php echo anchor('product-category-contents/view/'.$value['content_id'], _('Show Content'), array('class' => 'btn btn-secondary btn-modal')); ?>
                <?php endif; ?>            
            </td>
            <td>
              <?php echo anchor('course-categories/edit/'.$value['id'], _('Edit'), array('class' => 'btn btn-secondary')); ?>
              <?php echo anchor('course-categories/delete/'.$value['id'], _('Delete'), array('class' => 'btn btn-danger btn-delete-confirm')); ?>
            </td>
          </tr>
          <?php endforeach; ?>
          <?php else: ?>
          <tr>
            <td colspan="9"><?php echo _('No Data'); ?></td>
          </tr>
          <?php endif; ?>
        </tbody>
      </table>
      <?php echo $this->pagination->create_links(); ?>
    </article>
    <div class="col-12">
      <?php echo anchor('course-categories/add', _('Add'), array('class' => 'btn btn-primary hidden-xxl')); ?>
    </div>
  </div>
</div>

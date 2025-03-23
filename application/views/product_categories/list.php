<div class="col-12 col-xxl-9 list">
  <div class="row">
    <div class="col-12">
      <h2 class="float-left"><?php echo _('Product Category List') ?></h2>
      <div class="float-right">
        <p class="summary">
          <span id="list_count" style="display:none"><?php echo $data['total'] ?></span>
          <?php echo sprintf(_('There Are %d Product Category'),$data['total']) ?>
        </p>
    	</div>
    </div>
    <article class="col-12">
    		<table id="prepare_list" class="table table-striped table-hover">
          <colgroup>
            <col  />
            <col  />
            <col  />            
            <col style="width:200px" />
    				<col style="width:170px" />
          </colgroup>
          <thead class="thead-default">
            <tr>
              <th><?php echo _('Title') ?></th>
              <th><?php echo _('Order No') ?></th>
              <th><?php echo _('Product Count') ?></th>              
              <th><?php echo _('Created At') ?></th>
              <th class="text-center"><?php echo _('Manage') ?></th>
            </tr>
          </thead>
          <tbody>
    				<?php if ($data['total']): ?>
    				<?php foreach ($data['list'] as $index=>$value):
              $page_param='';
              if ($this->input->get('page')) {
                  $page_param='?page='.$this->input->get('page');
              }
            ?>
            <tr<?php if (isset($data['content'])): ?><?php if ($data['content']['id']==$value['id']): ?> class="table-primary"<?php endif ?><?php endif ?>>
              <td><?php echo anchor('product-categories/view/'.$value['id'], $value['title']) ?></td>
              <td><?php echo $value['order_no'] ?></td>
              <td><?php echo $value['product_counts'] ?><?php echo _('Count') ?></td>
              <td><?php echo get_dt_format($value['created_at'],$search_data['timezone']) ?></td>
              <td class="text-right">
                <?php echo anchor('product-categories/edit/'.$value['id'].$page_param, _('Edit'), array('class'=>"btn btn-secondary")) ?>
                <?php echo anchor('product-categories/delete/'.$value['id'], _('Delete'), array('class'=>'btn btn-danger btn-delete-confirm')) ?>
              </td>
            </tr>
    				<?php endforeach ?>
    				<?php else: ?>
    				<tr>
    					<td colspan="5" class="text-center"><?php echo _('No Data') ?></td>
    				</tr>
    				<?php endif ?>
          </tbody>
        </table>
        <?php echo $this -> pagination -> create_links() ?>
        <?php if ($this->Acl->has_permission('product-categories')): ?>
        <?php echo anchor('product-categories/add', _('Add'), array('class'=>'btn btn-primary hidden-xxl')) ?>
        <?php endif ?>
    </article>
  </div>
</div>

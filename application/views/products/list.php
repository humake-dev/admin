<div class="col-12 col-xxl-9 list">
  <div class="row">
    <div class="col-12">
      <h2 class="float-left"><?php echo _('Product List') ?></h2>
      <div class="float-right">
        <p class="summary">
          <span id="list_count" style="display:none"><?php echo $data['total'] ?></span>
          <?php echo sprintf(_('There Are %d Product'),$data['total']) ?>
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
              <th><?php echo _('Category') ?></th>
              <th><?php echo _('Title') ?></th>
              <th class="text-right"><?php echo _('Price') ?></th>
              <th class="text-center"><?php echo _('Image') ?></th>
              <th class="text-center"><?php echo _('Manage') ?></th>
            </tr>
          </thead>
          <tbody>
    				<?php if ($data['total']): ?>
    				<?php foreach ($data['list'] as $index=>$value):
              $page_param='';
              if($this->input->get('page')) {
                $page_param='?page='.$this->input->get('page');
              }
            ?>
            <tr<?php if (isset($data['content'])): ?><?php if ($data['content']['id']==$value['id']): ?> class="table-primary"<?php endif ?><?php endif ?>>
              <td><?php echo $value['category_name'] ?></td>
              <td><?php echo anchor('products/view/'.$value['id'], $value['title']) ?></td>
              <td class="text-right"><?php echo number_format($value['price']) ?><?php echo _('Currency') ?></td>
              <td class="text-center">
                <?php if(empty($value['picture_url'])): ?>
                <?php echo _('Not Inserted') ?>
                <?php else: ?>
                <?php
                  $pictures=explode(',',$value['picture_url']);
                  foreach($pictures as $picture):
                  $picture_s=explode('::',$picture);
                ?>
                  <form action="/product-pictures/delete/<?php echo $picture_s[0] ?>">
                    <div>
                      <img src="<?php echo getPhotoPath('product',  $this->session->userdata('branch_id'), $picture_s[1], 'small') ?>" />
                    </div>
                    <input type="submit"  value="<?php echo _('Delete') ?>" class="btn btn-danger">
                  </form>
                <?php endforeach ?>
                <?php endif ?>
              </td>
              <td class="text-right">
                <?php echo anchor('products/edit/'.$value['id'].$page_param, _('Edit'), array('class'=>"btn btn-secondary")) ?>
                <?php echo anchor('products/delete/'.$value['id'], _('Delete'), array('class'=>'btn btn-danger btn-delete-confirm')) ?>
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
        <?php echo anchor('products/add', _('Add'), array('class'=>'btn btn-primary hidden-xxl')) ?>
    </article>
  </div>
</div>

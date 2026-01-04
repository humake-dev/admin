<article id="rent_list" class="row use_list"<?php if(empty($data['user_content'])): ?> style="display:none"<?php endif ?>>
  <h3 class="col-12">
    <?php if(empty($data['user_content'])): ?>
    <?php echo sprintf(_('%s User Rent'),'<span></span>') ?>
    <?php else: ?>
    <?php echo sprintf(_('%s User Rent'),'<span>'.$data['user_content']['name'].'</span>') ?>
    <?php endif ?>
  </h3>
  <input type="hidden" id="rent_list_count" value="<?php if(!empty($data['list']['total'])): ?><?php echo $data['list']['total'] ?><?php endif ?>" />
  <div class="col-12">
    <table class="table table-striped table-hover">
      <colgroup>
        <col class="d-none d-md-table-cell" style="width:50px" />
        <col />
        <col />
      </colgroup>
      <thead class="thead-default">
        <tr>
          <th class="d-none d-md-table-cell"><?php if ($this->router->fetch_class()=='users'): ?><?php echo _('Increment Number') ?><?php endif ?></th>
          <th><?php echo _('Facility') ?></th>
          <th>
              <?php echo _('Facility No') ?>
              <?php if(isset($data['content'])): ?>
               <?php //echo ' / '._('Edit') ?>
             <?php endif ?>
            </th>
          </tr>
        </thead>
        <tbody>
          <?php if (empty($data['rent_list']['total'])): ?>
          <tr>
            <td colspan="3" class="text-center"><?php echo _('No Data') ?></td>
          </tr>
          <?php else: ?>                    
          <?php foreach ($data['rent_list']['list'] as $index=>$value): ?>
          <tr<?php if(isset($data['content']['id'])): ?><?php if($data['content']['id']==$value['id']): ?> class="table-primary"<?php endif ?><?php endif ?>>
            <td class="d-none d-md-table-cell">
              <?php echo number_format($data['rent_list']['total']-$index) ?>
            </td>
            <td>
              <div style="display:block;width:100px;white-space:nowrap;text-overflow:ellipsis">
              <?php echo anchor('/rents/view/'.$value['id'], $value['product_name']) ?>
            </div>
            </td>
            <td>
              <?php if(empty($value['no'])): ?>
              <?php echo _('Not Set') ?>
              <?php else: ?>
              <?php echo $value['no'] ?>
              <?php endif ?>
            </td>
          </tr>
          <?php endforeach ?>
          <?php endif ?>
        </tbody>
      </table>
    </div>
</article>

<div class="col-12 col-xxl-9 list">
  <div class="row">
    <div class="col-12">
      <h2 class="float-left"><?php echo _('Permission List') ?></h2>
      <div class="float-right">
        <p class="summary">
          <span id="list_count" style="display:none"><?php echo $data['total'] ?></span>
          <?php echo sprintf(_('There Are %d Permission'),$data['total']) ?>
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
            <th><?php echo _('Title') ?></th>
            <th><?php echo _('Controller') ?></th>
            <th><?php echo _('Action') ?></th>
            <th><?php echo _('Description') ?></th>
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
            <td><?php echo $value['title'] ?></td>
            <td><?php echo $value['controller'] ?></td>
            <td><?php echo $value['action'] ?></td>
            <td><?php echo $value['description'] ?></td>
            <td>
              <?php echo anchor('permissions/edit/'.$value['id'].$page_param, _('Edit'), array('class'=>'btn btn-secondary')) ?>
              <?php echo anchor('permissions/delete/'.$value['id'], _('Delete'), array('class'=>'btn btn-danger btn-delete-confirm')) ?>
            </td>
          </tr>
          <?php endforeach ?>
          <?php else: ?>
          <tr>
            <td colspan="4" style="text-align:center"><?php echo _('No Data') ?></td>
          </tr>
          <?php endif ?>
        </tbody>
      </table>
      <?php echo $this -> pagination -> create_links() ?>
      <?php echo anchor('permissions/add', _('Add'), array('class'=>'btn btn-primary hidden-xxl')) ?>
    </article>
  </div>
</div>

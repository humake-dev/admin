<?php
  $params='';
  if ($this->input->get()) {
    $p_index=0;
    foreach($this->input->get() as $key=>$param) {
      if($p_index) {
        $params.='&'.$key.'='.$param;
      } else {
        $params.='?'.$key.'='.$param;
      }
      $p_index++;                     
    }
  }
?>
<div id="view-account" class="container">
  <div class="row">
    <?php echo $Layout->Element('accounts/nav') ?>
    <div class="col-12">
    <?php include __DIR__.DIRECTORY_SEPARATOR.'search_form.php' ?>
    </div>
    <?php if($this->router->fetch_method()=='view'): ?>
    <article class="col-12">
      <div class="card">
      <div class="card-body">
        <div class="form-row">
          <div class="col-12 col-lg-6">
          <h3><?php echo $data['content']['title'] ?> <?php echo _('Details') ?></h3>
          <?php echo _('A basic price') ?> : <?php echo number_format($data['content']['price'])._('Currency') ?>
          </div>
          <div class="col-12 col-lg-6 text-right">
              <?php echo sprintf(_('There Are %d Details Info'),$data['total']) ?>
              &nbsp;&nbsp;&nbsp;&nbsp;<?php echo anchor('/accounts/export-excel/'.$data['content']['id'].$params, _('Export Excel'), array('class' => 'btn btn-secondary')); ?>
          </div>
        </div>
      </div>
      </div>
    </article>
    <?php else: ?>
    <div class="col-12">
      <div class="float-right">
        <?php echo sprintf(_('There Are %d Rebate Details Info'),$data['total']) ?>
      </div>
    </div>
    <?php endif ?>
    <article class="col-12">
      <table class="table table-bordered">
        <colgroup>
          <col>
          <col>
          <col>
          <col>
          <col>
          <col>
          <?php if ($this->Acl->has_permission('accounts', 'delete') or $this->Acl->has_permission('accounts', 'write')): ?>
          <col style="width:150px">
          <?php endif ?>
        </colgroup>
        <thead class="thead-default">
          <tr>
            <th class="text-center"><?php echo _('Category') ?></th>
            <th class="text-center"><?php echo _('User') ?></th>
            <th class="text-center"><?php echo _('Income') ?> / <?php echo  _('Outcome') ?></th>
            <th class="text-center"><?php echo _('Transaction Date') ?></th>
            <th class="text-center"><?php echo _('Cash') ?></th>
            <th class="text-center"><?php echo _('Credit') ?></th>
            <?php if ($this->Acl->has_permission('accounts', 'delete') or $this->Acl->has_permission('accounts', 'write')): ?>
            <th class="text-center"><?php echo _('Manage'); ?></th>
            <?php endif; ?>
          </tr>
        </thead>
        <tbody>
          <?php if(empty($data['total'])): ?>
          <tr>
            <td colspan="8"><?php echo _('No Data') ?></td>
          </tr> 
          <?php else: ?>          
          <?php foreach($data['list'] as $index=>$value): ?>
            <tr>
              <td>
              <?php echo _($value['account_category_name']) ?>
              </td>
              <td class="text-center">
                <?php if(empty($value['user_id'])): ?>
                  <?php echo _('Deleted User') ?>
                <?php else: ?>
                <?php echo anchor('/view/'.$value['user_id'],$value['user_name']) ?>
                <?php endif ?>
              </td>
              <td class="text-right">
                <?php if($value['type']=='O'): ?>
                <span class="text-danger"><?php echo _('Outcome') ?></span>
                <?php else: ?>                
                <span class="text-success"><?php echo _('Income') ?></span>
                <?php endif ?>
              </td>
              <td class="text-right"><?php echo get_dt_format($value['transaction_date'],$search_data['timezone']) ?></td>
              <td class="text-right"><?php echo number_format($value['cash']) ?><?php echo _('Currency') ?></td>
              <td class="text-right"><?php echo number_format($value['credit']) ?><?php echo _('Currency') ?></td>
              <?php if ($this->Acl->has_permission('accounts', 'delete') or $this->Acl->has_permission('accounts', 'write')): ?>
        <td class="text-center">
          <?php if ($this->Acl->has_permission('accounts', 'write')): ?>
          <?php echo anchor('accounts/edit/'.$value['id'], _('Edit'), array('class' => 'btn btn-default')); ?>
          <?php endif ?>
          <?php if ($this->Acl->has_permission('accounts', 'delete')): ?>
          <?php echo anchor('accounts/delete/'.$value['id'], _('Delete'), array('class' => 'btn btn-danger btn-delete-confirm')); ?>
          <?php endif ?>
        </td>
      <?php endif; ?>
            </tr>
          <?php endforeach ?>
          <?php endif ?>
        </table>
        <?php echo $this -> pagination -> create_links() ?>
    </article>
  </div>
</div>

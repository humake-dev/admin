<div id="users" class="container">
  <div class="row">
    <div class="col-12">
      <article class="card">
        <div class="card-header">
          <ul class="nav nav-pills card-header-pills">
            <li class="nav-item"><a class="nav-link active" href="#"><?php echo _('Enroll Info'); ?></a></li>
            <?php if ($data['end_list']['total']): ?>
            <li class="nav-item">
              <a class="nav-link" href="#"><?php echo _('End Enroll Info'); ?></a>
            </li>
            <?php endif; ?>
            <?php if ($data['stop_list']['total']): ?>
            <li class="nav-item">
              <a class="nav-link" href="#"><?php echo _('Stop Enroll Info'); ?></a>
            </li>
            <?php endif; ?>
            <?php if ($data['transfer_list']['total']): ?>
            <li class="nav-item">
              <a class="nav-link" href="#"><?php echo _('Transfer Enroll Info'); ?></a>
            </li>
            <?php endif; ?>
          </ul>
          <div class="float-right buttons">
            <i class="material-icons">keyboard_arrow_up</i>
          </div>
        </div>
        <div class="card-body">
          <div class="card-block">
            <div class="row">
              <div class="col-12">
                <input type="hidden" id="text_enroll_resume" value="<?php echo _('Resume Order'); ?>" />
                <input type="hidden" id="text_enroll_stop" value="<?php echo _('Stop Order'); ?>" />
                <ul class="sl-bnt-group">
              <?php if ($this->session->userdata('branch_id')): ?>
              <?php if ($this->Acl->has_permission('enrolls', 'write')): ?>
              <li><?php echo anchor('enrolls/add?user_id='.$data['content']['id'], _('Add Enroll'), array('id' => 'user_enroll_', 'class' => 'btn btn-primary')); ?></li>
              <?php if (isset($data['enroll']['content'])): ?>
              <?php if ($data['enroll']['content']['stopped']): ?>
              <li><?php echo anchor('#', _('Enroll Edit'), array('id' => 'user_enroll_edit', 'class' => 'btn btn-secondary btn-modal disabled')); ?></li>              
              <li><?php echo anchor('#', _('Enroll Transfer'), array('id' => 'user_enroll_transfer', 'class' => 'btn btn-secondary btn-modal disabled')); ?></li>              
              <?php else: ?>
              <li><?php echo anchor('enrolls/edit/'.$data['enroll']['content']['id'], _('Edit Enroll'), array('id' => 'user_enroll_edit', 'class' => 'btn btn-secondary')); ?></li>              
              <li><?php echo anchor('enrolls/transfer/'.$data['enroll']['content']['id'], _('Transfer Enroll'), array('id' => 'user_enroll_transfer', 'class' => 'btn btn-secondary btn-modal')); ?></li>
              <?php endif; ?>
              <?php if ($data['enroll']['content']['end_date'] == $search_data['max_date']): ?>
              <li><?php echo anchor('#', _('Stop Order'), array('id' => 'user_enroll_stop_resume', 'class' => 'btn btn-secondary btn-modal disabled')); ?></li>
              <?php else: ?>
              <?php if ($data['enroll']['content']['stopped']): ?>
              <li><?php echo anchor('enrolls/resume/'.$data['enroll']['content']['id'], _('Resume Order'), array('id' => 'user_enroll_stop_resume', 'class' => 'btn btn-secondary btn-modal')); ?></li>
              <?php else: ?>
              <li><?php echo anchor('enrolls/stop/'.$data['enroll']['content']['id'], _('Stop Order'), array('id' => 'user_enroll_stop_resume', 'class' => 'btn btn-secondary btn-modal')); ?></li>
              <?php endif; ?>
              <?php endif; ?>
              <li><?php echo anchor('enrolls/delete/'.$data['enroll']['content']['id'], _('Delete Enroll'), array('id' => 'user_enroll_delete', 'class' => 'btn btn-secondary btn-modal')); ?></li>
              <?php endif; ?>
              <?php endif; ?>
              <?php endif; ?>
              <li class="float-right" style="margin-right:0">
                <?php if (isset($data['enroll']['content'])): ?>
                <?php echo anchor('/enrolls/export-excel/'.$data['enroll']['content']['id'], _('Export Excel'), array('class' => 'btn btn-secondary')); ?>
                <?php else : ?>
                  <?php echo anchor('#', _('Export Excel'), array('class' => 'btn btn-secondary disabled')); ?>
                <?php endif; ?>
              </li>
            </ul>
            </div>
            <div class="col-12">
              <div class="table-responsive">
                <?php
                $list = $data['enroll'];
                include __DIR__.DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'enrolls'.DIRECTORY_SEPARATOR.'list.php';
                ?>
      </div>
      </div>
      </div>
      </div>
      <?php
          if ($data['end_list']['total']):
          $table_id = 'user_end_enroll_list';
          $list = $data['end_list'];
          $end_list = 1;
          $data['enroll']['content'] = $data['end_list']['list'][0];
        ?>
      <div class="card-block" style="display:none">
        <div class="row">
        <div class="col-12">
          <ul class="sl-bnt-group">

              <?php if ($this->session->userdata('branch_id')): ?>
              <?php if ($this->Acl->has_permission('enrolls', 'write')): ?>
              <li><?php echo anchor('enrolls/disable/'.$data['content']['enroll']['id'], _('Enroll Disable'), array('id' => 'user_enroll_disable', 'class' => 'btn btn-secondary btn-modal')); ?></li>
              <?php endif; ?>
              <?php endif; ?>

            <li class="float-right" style="margin-right:0">
            <?php if (isset($data['enroll'])): ?>
            <?php echo anchor('/enrolls/export_excel/'.$data['content']['enroll']['id'].'?end_enroll=1', _('Export Excel'), array('class' => 'btn btn-secondary')); ?>
            <?php else : ?>
              <?php echo anchor('#', _('Export Excel'), array('class' => 'btn btn-secondary disabled')); ?>
            <?php endif; ?>
            </li>
          </ul>
        </div>
        <div class="col-12">
          <div class="table-responsive">
          <?php include __DIR__.DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'enrolls'.DIRECTORY_SEPARATOR.'list.php'; ?>
          </div>
        </div>
        </div>
      </div>
      <?php endif; ?>
      <?php if ($data['stop_list']['total']): ?>
      <div class="card-block" style="display:none">
        <div class="row">
        <div class="col-12">
          <div class="table-responsive">
          <?php include __DIR__.DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'enroll_stops'.DIRECTORY_SEPARATOR.'list.php'; ?>
          </div>
        </div>
        </div>
      </div>
      <?php endif; ?>
      <?php if ($data['transfer_list']['total']): ?>
      <div class="card-block" style="display:none">
        <div class="row">
        <div class="col-12">
        <div class="table-responsive">
          <?php include __DIR__.DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'enroll_transfers'.DIRECTORY_SEPARATOR.'list.php'; ?>
          </div>
        </div>
        </div>
      </div>
      <?php endif; ?>
    </div>
      </article>
    </div>
    
    <div class="col-12">    
    <div class="sl_pagination">
  <?php if (!empty($data['total'])): ?>
  <?php echo $this->pagination->create_links(); ?>
  <?php endif; ?>
    </div>  
    </div>                  

    <div class="col-12">
      <article class="card">
        <div class="card-header">
        <h3><?php echo _('Enroll'); ?><?php if (isset($data['enroll']['content'])): ?>(<span id="enroll_log_title"><?php echo $data['enroll']['content']['category_name']; ?> / <?php echo $data['enroll']['content']['course_name']; ?></span>)<?php endif; ?> 상세내역</h3>
        <div class="float-right buttons">
          <i class="material-icons">keyboard_arrow_up</i>
        </div>
        </div>
        <div class="card-body">
          <div class="row">
        <div class="col-12">
          <ul class="float-right sl-bnt-group">
            <?php if ($data['log']['total']): ?>
            <li><?php echo anchor('/accounts/export_enroll_account/'.$data['enroll']['content']['id'], _('Export Excel'), array('id' => 'export_enroll_account', 'class' => 'btn btn-secondary')); ?></li>
            <?php else: ?>
            <li><?php echo anchor('#', _('Export Excel'), array('id' => 'export_enroll_account', 'class' => 'btn btn-secondary disabled')); ?></li>
            <?php endif; ?>
          </ul>
        </div>
        <div class="col-12">
        <table id="user_enroll_log_list" class="table table-bordered table-striped">
          <colgroup>
            <col />
            <col />
            <col />
            <col />
            <col />
            <col />            
            <col />                       
            <col />                     
            <col />
          </colgroup>
          <thead class="thead-default">
            <tr>
              <th rowspan="2" class="text-center"><?php echo _('Date'); ?></th>
              <th rowspan="2" class="text-center"><?php echo _('Content'); ?></th>
              <th colspan="3" class="text-center"><?php echo _('Price'); ?></th>
              <th rowspan="2" class="text-center"><?php echo _('Payment'); ?></th>
              <th colspan="2" class="text-center"><?php echo _('Payment'); ?></th>
            </tr>
            <tr>
              <th class="text-center"><?php echo _('Fee'); ?></th>
              <th class="text-center"><?php echo _('Discount'); ?></th>
              <th class="text-center"><?php echo _('Point'); ?></th>
              <th class="text-center"><?php echo _('Cash'); ?></th>
              <th class="text-center"><?php echo _('Credit'); ?></th>
            </tr>
          </thead>
          <tbody>
            <?php if ($data['log']['total']): ?>
            <?php foreach ($data['log']['list'] as $log): ?>
              <tr>
                  <td class="text-center"><?php echo get_dt_format($log['created_at'], $search_data['timezone']); ?></td>
                  <td class="text-center"><?php echo str_replace('수강', '', $log['category_name']); ?></td>
                  <td class="text-right"><?php if ($log['account_category_id'] == 1): ?><?php echo number_format($log['original_price']); ?><?php echo _('Currency'); ?><?php else: ?>- <?php endif; ?></td>
                  <td class="text-right"><?php if ($log['account_category_id'] == 1): ?><?php echo number_format($log['original_price'] * $log['dc_rate'] / 100); ?><?php echo _('Currency'); ?><?php else: ?>- <?php endif; ?></td>
                  <td class="text-right"><?php if ($log['account_category_id'] == 1): ?><?php echo number_format($log['dc_point']); ?>P<?php else: ?>- <?php endif; ?></td>
                  <td class="text-right sl-td-active"><?php echo number_format($log['cash'] + $log['credit']); ?><?php echo _('Currency'); ?></td>
                  <td class="text-right"><?php echo number_format($log['cash']); ?><?php echo _('Currency'); ?></td>
                  <td class="text-right"><?php echo number_format($log['credit']); ?><?php echo _('Currency'); ?></td>                 
                  </tr>
                  <?php endforeach; ?>
                  <?php else: ?>
                  <tr>
                  <td colspan="9"><?php echo _('No Data'); ?></td>
                  </tr>
                  <?php endif; ?>
                </tbody>
              </table>
                </div>
              </div>
            </div>
          </article>
        </div>
      </div>
</div>

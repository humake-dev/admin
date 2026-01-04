<div id="error-reports" class="container">
  <div class="row">
    <div class="col-12">
      <h2 class="float-left"><?php echo _('Error Report List'); ?></h2>
      <div class="float-right">
        <p class="summary">
          <span id="list_count" style="display:none"><?php echo $data['total']; ?></span>
          <?php echo sprintf(_('There Are %d Error Reprot'), $data['total']); ?>
        </p>
      </div>
    </div>
    <article class="col-12">
    		<table id="prepare_list" class="table table-striped table-hover">
          <colgroup>
            <col>
            <col>
            <col>
            <col>
    				<col>
    				<col>               
            <col style="width:200px">
            <?php if($this->session->userdata('role_id') < 6): ?>
    				<col style="width:150px">
            <?php endif ?>
          </colgroup>
          <thead class="thead-default">
            <tr>
              <th><?php echo _('Branch'); ?></th>
              <th><?php echo _('Admin'); ?></th>
              <th><?php echo _('Title'); ?></th>
              <th><?php echo _('Content'); ?></th>
              <th><?php echo _('File'); ?></th>              
              <th><?php echo _('Solve'); ?></th>
              <th><?php echo _('Created At'); ?></th>
              <?php if($this->session->userdata('role_id') < 6): ?>
              <th class="text-center"><?php echo _('Manage'); ?></th>
              <?php endif ?>
            </tr>
          </thead>
          <tbody>
    				<?php if (empty($data['total'])): ?>
    				<tr>
    					<td colspan="8" class="text-center"><?php echo _('No Data'); ?></td>
    				</tr>
    				<?php else: ?>                     
    				<?php foreach ($data['list'] as $index => $value):
              $page_param = '';
              if ($this->input->get('page')) {
                  $page_param = '?page='.$this->input->get('page');
              }
            ?>
            <tr<?php if (isset($data['content'])): ?><?php if ($data['content']['id'] == $value['id']): ?> class="table-primary"<?php endif; ?><?php endif; ?>>
              <td><?php echo $value['branch_name']; ?></td>
              <td><?php echo $value['admin_name']; ?></td>
              <td><?php echo anchor('error-reports/view/'.$value['id'], $value['title']); ?></td>
              <td><?php echo anchor('error-report-contents/view/'.$value['id'], _('Show Content'), array('class' => 'btn btn-secondary btn-modal')); ?></td>
              <td>
                <?php if (empty($value['file']['total'])): ?>
                                    <?php echo _('Not Inserted'); ?>
                                <?php else: ?>
                                  <?php echo $value['file']['total'] ?><?php echo _('Count') ?>
                                <?php endif; ?>
              </td>
              <td>
                <?php if ($value['solve']): ?>
                <span class="text-success"><?php echo _('Solve'); ?>
                <?php if (!empty($value['solve_date'])): ?>
                 - <?php echo get_dt_format($value['solve_date'], $search_data['timezone']); ?>
                <?php endif; ?>
                </span>
                <?php else: ?>
                <span><?php echo _('Unsolve'); ?></span>
                <?php endif; ?>                
              </td>
              <td><?php echo get_dt_format($value['created_at'], $search_data['timezone']); ?></td>
              <?php if($this->session->userdata('role_id') < 6): ?>
              <td>
                <?php echo anchor('error-reports/edit/'.$value['id'].$page_param, _('Edit'), array('class' => 'btn btn-secondary')); ?>
                <?php echo anchor('error-reports/delete/'.$value['id'], _('Delete'), array('class' => 'btn btn-danger btn-delete-confirm')); ?>
              </td>
              <?php endif ?>
            </tr>
    				<?php endforeach; ?>
    				<?php endif; ?>
          </tbody>
        </table>
        <?php echo $this->pagination->create_links(); ?>
        <?php echo anchor('error-reports/add', _('Add'), array('class' => 'btn btn-primary')); ?>
  </div>  
</div>

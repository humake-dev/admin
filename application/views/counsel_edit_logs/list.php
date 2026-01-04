<div class="row">
  <div class="col-12">
    <h2 class="float-left"><?php echo _('Counsel Edit Log List'); ?></h2>
    <div class="float-right">
      <p class="summary">
        <span id="list_count" style="display:none"><?php echo $data['total']; ?></span>
        <?php echo sprintf(_('There Are %d Counsel Edit Log'), $data['total']); ?>
      </p>
    </div>
  </div>
  <article class="col-12">
    <table id="prepare_list" class="table table-striped table-hover">
      <colgroup>
        <col />
        <col />
        <col />
        <col />
        <col />
        <col />
        <col style="width:200px" />
        <?php if ($this->session->userdata('role_id') <= 2): ?>        
        <col style="width:100px" />
        <?php endif; ?>
      </colgroup>
      <thead class="thead-default">
            <tr>
              <th><?php echo _('User'); ?></th>
              <th><?php echo _('Counsel'); ?></th>                            
              <th><?php echo _('Change Field Count'); ?></th>
              <th><?php echo _('Revision'); ?></th>                       
              <th><?php echo _('Content'); ?></th>
              <th><?php echo _('Editor'); ?></th>              
              <th><?php echo _('Updated At'); ?></th>
              <?php if ($this->session->userdata('role_id') <= 2): ?>              
              <th class="text-center"><?php echo _('Manage'); ?></th>
              <?php endif; ?>
            </tr>
          </thead>
          <tbody>
    				<?php if (empty($data['total'])): ?>
    				<tr>
    					<td colspan="<?php if ($this->session->userdata('role_id') <= 4): ?>8<?php else:?>7<?php endif; ?>" class="text-center"><?php echo _('No Data'); ?></td>
    				</tr>
    				<?php else: ?>            
    				<?php foreach ($data['list'] as $index => $value):
              $page_param = '';
              if ($this->input->get('page')) {
                  $page_param = '?page='.$this->input->get('page');
              }
            ?>
            <tr<?php if (isset($data['content'])): ?><?php if ($data['content']['id'] == $value['id']): ?> class="table-primary"<?php endif; ?><?php endif; ?>>
              <td>
              <?php if (empty($value['user_id'])): ?>
            <?php if (empty($value['temp_user_id'])): ?>
              <?php echo _('Deleted User') ?>
            <?php else: ?>
            <?php echo anchor('/temp-users/view/'.$value['temp_user_id'], $value['user_name']); ?>
            <?php endif ?>
            <?php else: ?>
            <?php echo anchor('/view/'.$value['user_id'], $value['user_name']); ?>
            <?php endif; ?>
              </td>
              <td><?php echo anchor('/counsels/view/'.$value['counsel_id'], $value['title']); ?></td>
              <td><?php echo anchor('counsel-edit-logs/view/'.$value['id'], $value['field_change_count']); ?></td>
              <td><?php echo $value['revision']; ?></td>
              <td><?php echo $value['content']; ?></td>
              <td>
              <?php if(empty($value['editor'])): ?>
              -
              <?php else: ?>
              <?php echo $value['editor']; ?>
              <?php endif ?>
              </td>
              <td><?php echo get_dt_format($value['created_at'], $search_data['timezone']); ?></td>
              <?php if ($this->session->userdata('role_id') <= 2): ?>
              <td class="text-center">
                <?php echo anchor('counsel-edit-logs/delete/'.$value['id'], _('Delete'), array('class' => 'btn btn-danger btn-delete-confirm')); ?>
              </td>
              <?php endif; ?>
            </tr>
    				<?php endforeach; ?>
    				<?php endif; ?>
          </tbody>
        </table>
        <?php echo $this->pagination->create_links(); ?>
    </article>
  </div>

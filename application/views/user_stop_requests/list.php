<div class="row">
    <div class="col-12">
            <h2 class="float-left"><?php echo _('User Stop Request List'); ?></h2>
            <div class="float-right">
                <p class="summary">
                    <span id="list_count" style="display:none"><?php echo $data['total']; ?></span>
                    <?php echo sprintf(_('There Are %d User Stop Rquest'), $data['total']); ?>
                </p>
            </div>
        </div>
        <article class="col-12">
            <table id="prepare_list" class="table table-striped table-hover">
                <colgroup>
                    <?php if(!empty($this->session->userdata('center_id'))): ?>
                    <col>
                    <?php endif ?>
                    <col>
                    <col>
                    <col>
                    <col>
                    <col style="width:200px">
                    <col style="width:150px">
                </colgroup>
                <thead class="thead-default">
                <tr>
                    <?php if(!empty($this->session->userdata('center_id'))): ?>
                    <th><?php echo _('Branch'); ?></th>
                    <?php endif ?>
                    <th><?php echo _('User'); ?></th>
                    <th><?php echo _('Period'); ?></th>
                    <th><?php echo _('Manager'); ?></th>
                    <th><?php echo _('Content'); ?></th>
                    <th><?php echo _('Created At'); ?></th>
                    <th class="text-center"><?php echo _('Manage'); ?></th>
                </tr>
                </thead>
                <tbody>
                <?php if (empty($data['total'])): ?>
                    <tr>
                        <td colspan="<?php if(empty($this->session->userdata('center_id'))): ?><?php if ($this->Acl->has_permission('users', 'delete')): ?>5<?php else: ?>4<?php endif ?><?php else: ?>6<?php endif ?>" class="text-center"><?php echo _('No Data'); ?></td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($data['list'] as $index => $value):
                        $page_param = '';
                        if ($this->input->get('page')) {
                            $page_param = '?page=' . $this->input->get('page');
                        }
                        ?>
                        <tr<?php if (isset($data['content'])): ?><?php if ($data['content']['id'] == $value['id']): ?> class="table-primary"<?php endif; ?><?php endif; ?>>
                            <?php if(!empty($this->session->userdata('center_id'))): ?>
                            <td><?php echo $value['branch_name']; ?></td>
                            <?php endif ?>
                            <td><?php echo $value['user_name']; ?><?php if(!empty($value['phone'])): ?>(<?php echo _($value['phone']) ?>)<?php endif ?></td>
                            <td><?php echo anchor('user-stop-requests/view/' . $value['id'].$params, $value['stop_start_date'].'~'.$value['stop_end_date']); ?></td>
                            <td>
                                <?php if(empty($value['manager_name'])): ?>
                                <?php echo _('Not Set') ?>
                                <?php else: ?>
                                <?php echo $value['manager_name'] ?>
                                <?php endif ?>
                            </td>
                            <td><?php echo anchor('user-stop-request-contents/view/' . $value['id'], _('Show Content'), array('class' => 'btn btn-secondary btn-modal')); ?></td>
                            <td><?php echo get_dt_format($value['created_at'], $search_data['timezone']); ?></td>
                            <td class="text-center">
                                <?php if(empty($value['complete'])): ?>
                                    <?php if ($this->session->userdata('role_id')<6): ?>
                                        <?php if(!empty($this->session->userdata('center_id')) and $this->session->userdata('branch_id')!=$value['branch_id']): ?>
                                            <a href="javascript:alert('해당 지점으로 이동후에 승인처리 하세요')" class="btn btn-secondary disabled" style="pointer-events: auto"><?php echo _('Approve') ?></a>
        
                                        <?php else : ?>
                                <?php echo anchor('/users/stop/' . $value['user_id'].'?request_id='.$value['id'].'&amp;return_url='.urlencode($return_url), _('Approve'), array('class' => 'btn btn-secondary')); ?>
                                <?php endif ?>
                                <?php else: ?>
                                    <?php echo _('Processing') ?>
                                <?php endif ?>
                                <?php else: ?>
                                    <?php echo _('Process Complete') ?>
                                <?php endif ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
                </tbody>
            </table>
            <?php echo $this->pagination->create_links(); ?>
        </article>
    </div>

<div class="row">
        <div class="col-12">
            <h2 class="float-left"><?php echo _('Counsel Request List'); ?></h2>
            <div class="float-right">
                <p class="summary">
                    <span id="list_count" style="display:none"><?php echo $data['total']; ?></span>
                    <?php echo sprintf(_('There Are %d Counsel Request'), $data['total']); ?>
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
                    <col>
                    <col style="width:200px">
                    <col style="width:150px">
                </colgroup>
                <thead class="thead-default">
                <tr>
                    <?php if(!empty($this->session->userdata('center_id'))): ?>
                    <th><?php echo _('Branch'); ?></th>
                    <?php endif ?>
                    <th><?php echo _('Question Course'); ?></th>
                    <th><?php echo _('User'); ?></th>
                    <th><?php echo _('Title'); ?></th>
                    <th><?php echo _('Manager'); ?></th>
                    <th><?php echo _('Content'); ?></th>
                    <th><?php echo _('Counsel Date'); ?></th>
                    <th class="text-center"><?php echo _('Manage'); ?></th>
                </tr>
                </thead>
                <tbody>
                <?php if (empty($data['total'])): ?>
                    <tr>
                    <td colspan="<?php if(empty($this->session->userdata('center_id'))): ?>7<?php else: ?>8<?php endif ?>" class="text-center"><?php echo _('No Data'); ?></td>
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
                            <td><?php 
                                            switch ($value['question_course']) {
                                                case 'pt':
                                                  echo _('Question PT');
                                                  break;
                                                case 'golf':
                                                  echo _('Question Golf');
                                                   break;
                                                default:
                                                 echo _('Question Default');
                                              }
                            ?></td>
                            <td><?php echo $value['user_name'] ?><?php if(!empty($value['phone'])): ?>(<?php echo _($value['phone']) ?>)<?php endif ?></td>
                            <td><?php echo anchor('counsel-requests/view/' . $value['id'].$params, $value['title']); ?></td>
                            <td>
                                <?php if(empty($value['manager_name'])): ?>
                                <?php echo _('Not Set') ?>
                                <?php else: ?>
                                <?php echo $value['manager_name'] ?>
                                <?php endif ?>
                            </td>
                            <td><?php echo anchor('counsel-contents/view/' . $value['id'], _('Show Content'), array('class' => 'btn btn-secondary btn-modal')); ?></td>
                            <td><?php echo get_dt_format($value['execute_date'], $search_data['timezone']); ?></td>
                            <td class="text-center">
                                <?php if(empty($value['cr_id'])): ?>
                                <?php echo anchor('/counsel-responses/add?counsel_id=' . $value['id'].'&amp;return_url='.urlencode($return_url), _('Counsel'), array('class' => 'btn btn-secondary')); ?>
                                <?php else: ?>
                                    <?php echo anchor('counsel-response-contents/view/' . $value['cr_id'], _('Process Complete'), array('class' => 'text-success btn-modal')); ?>
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

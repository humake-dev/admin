<div id="users" class="container">
    <div class="row">
        <?php include __DIR__ . DIRECTORY_SEPARATOR . 'aside.php'; ?>
        <div class="col-12 col-lg-8 col-xxl-9 user_sub">
            <?php if (empty($data['content'])): ?>
                <?php echo $Layout->element('home/not_found.php'); ?>
            <?php else: ?>
                <?php echo $Layout->element('home/nav'); ?>
                <div class="row">
                    <div class="col-12">
                        <article class="card">
                            <h3 class="col-12 card-header"><?php echo _('Consultation details'); ?></h3>
                            <div class="card-body">
                                <ul class="sl-bnt-group">
                                    <li><?php echo anchor('/counsels/add?user_id=' . $data['content']['id'] . '&amp;return_type=user', _('Add'), array('class' => 'btn btn-primary')); ?></li>
                                </ul>
                                <table class="table table-striped">
                                    <colgroup>
                                        <col>
                                        <col>
                                        <col>
                                        <col>
                                        <col>
                                        <col style="width:160px">
                                        <?php if ($this->Acl->has_permission('counsels', 'delete')): ?>
                                            <col style="width:150px">
                                        <?php endif ?>
                                    </colgroup>
                                    <thead>
                                    <tr class="thead-default">
                                        <th><?php echo _('Counsel Date'); ?></th>
                                        <th><?php echo _('Counselor'); ?></th>
                                        <th><?php echo _('Type'); ?></th>
                                        <th><?php echo _('Counsel Result'); ?></th>
                                        <th><?php echo _('Question Course'); ?></th>
                                        <th class="text-center"><?php echo _('Content'); ?></th>
                                        <?php if ($this->Acl->has_permission('counsels', 'delete')): ?>
                                            <th class="text-center"><?php echo _('Manage') ?></th>
                                        <?php endif ?>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <?php if (empty($data['list']['total'])): ?>
                                        <tr>
                                            <td colspan="7" class="text-center"><?php echo _('No Data'); ?></td>
                                        </tr>
                                    <?php else: ?>
                                        <?php foreach ($data['list']['list'] as $value): ?>
                                            <td><?php echo get_dt_format($value['execute_date'], $search_data['timezone']); ?></td>
                                            <td>
                                                <?php if (empty($value['counselor_name'])): ?>
                                                    <?php echo _('Not Inserted'); ?>
                                                <?php else: ?>
                                                    <?php echo $value['counselor_name']; ?>
                                                <?php endif ?>
                                            </td>
                                            <td>
                                                <?php if ($value['type'] == 'A'): ?>
                                                    <?php echo _('Counsel By Phone'); ?>
                                                <?php else: ?>
                                                    <?php echo _('Counsel By Interview'); ?>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <?php if (empty($value['complete'])): ?>
                                                    <span><?php echo _('Processing'); ?></span>
                                                <?php else: ?>
                                                    <span class="text-success"><?php echo _('Process Complete'); ?></span>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <?php if ($value['question_course'] == 'pt'): ?>
                                                    <?php echo _('Question PT'); ?>
                                                <?php else: ?>
                                                    <?php echo _('Question Default'); ?>
                                                <?php endif; ?>
                                            </td>
                                            <td class="text-center"><?php echo anchor('/counsels/view/' . $value['id'], _('Show Content'), array('class' => 'btn btn-secondary btn-modal')); ?></td>
                                            <?php if ($this->Acl->has_permission('counsels', 'delete')): ?>
                                                <td class="text-center">
                                                    <?php echo anchor('counsels/edit/' . $value['id'] . '?return_type=user', _('Edit'), array('class' => 'btn btn-default')); ?>
                                                    <?php echo anchor('counsels/delete/' . $value['id'], _('Delete'), array('class' => 'btn btn-danger btn-delete-confirm')); ?>
                                                </td>
                                            <?php endif; ?>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                    </tbody>
                                </table>
                            </div>
                        </article>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

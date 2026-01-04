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
                            <h3 class="col-12 card-header"><?php echo _('Message'); ?></h3>
                            <div class="card-body">
                                <div class="float-right">
                                    <p class="summary">
                                        <span id="list_count"
                                              style="display:none"><?php echo $data['list']['total']; ?></span>
                                        <?php echo sprintf(_('There Are %d Message'), $data['list']['total']); ?>
                                    </p>
                                </div>
                                <ul class="sl-bnt-group">
                                    <li><?php echo anchor('/messages/add?user[]=' . $data['content']['id'], _('Add'), array('class' => 'btn btn-primary','target'=>'_blank')); ?></li>
                                </ul>
                                <table id="user_message_list"  class="table table-striped">
                                    <colgroup>
                                        <col style="width:160px" />
                                        <col>
                                        <col>
                                        <col>
                                        <?php if ($this->Acl->has_permission('messages', 'delete')): ?>
                                        <col style="width:100px" />
                                        <?php endif ?>
                                    </colgroup>
                                    <thead>
                                    <tr class="thead-default">
                                        <th><?php echo _('Type'); ?></th>
                                        <th><?php echo _('Title'); ?></th>
                                        <th class="text-right"><?php echo _('Content'); ?></th>
                                        <th class="text-right"><?php echo _('Created At'); ?></th>
                                        <?php if ($this->Acl->has_permission('messages', 'delete')): ?>
                                        <th id="user-message-manage"><?php echo _('Manage'); ?></th>
                                        <?php endif ?>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <?php if (empty($data['list']['total'])): ?>
                                        <tr>
                                            <td colspan="6" class="text-center"><?php echo _('No Data'); ?></td>
                                        </tr>
                                    <?php else: ?>                                             
                                        <?php foreach ($data['list']['list'] as $value): ?>
                                            <tr>
                                                <td><?php echo display_send_type($value['type']); ?></td>
                                                <td><?php echo $value['title']; ?></td>
                                                <td class="text-right"><?php echo anchor('message-contents/view/' . $value['id'], _('Show Content'), array('class' => 'btn btn-secondary btn-modal')); ?></td>
                                                <td class="text-right"><?php echo get_dt_format($value['created_at']); ?></td>
                                                <?php if ($this->Acl->has_permission('messages', 'delete')): ?>
                                                <td>
                                                    <?php echo anchor('message-users/delete/' . $value['mu_id'], _('Delete'), array('class' => 'btn btn-danger btn-delete-confirm')); ?>
                                                </td>
                                                <?php endif ?>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                    </tbody>
                                </table>
                                <div class="sl_pagination"></div>
                            </div>
                        </article>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

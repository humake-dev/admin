<div class="col-12 col-xxl-9 list">
    <div class="row">
        <div class="col-12">
            <h2 class="float-left"><?php echo _('Message Prepare List'); ?></h2>
            <div class="float-right">
                <p class="summary">
                    <span id="list_count" style="display:none"><?php echo $data['total']; ?></span>
                    <?php echo sprintf(_('There Are %d Message Prepare'), $data['total']); ?>
                </p>
            </div>
        </div>
        <article class="col-12">
            <table id="prepare_list" class="table table-striped table-hover">
                <colgroup>
                    <col/>
                    <col class="d-none d-sm-table-cell"/>
                    <col style="width:200px"/>
                    <col class="d-none d-sm-table-cell"/>
                </colgroup>
                <thead class="thead-default">
                <tr>
                    <th><?php echo _('Title'); ?></th>
                    <th class="d-none d-sm-table-cell"><?php echo _('Content'); ?></th>
                    <th><?php echo _('Created At'); ?></th>
                    <th class="d-none d-sm-table-cell text-center" style="width:150px"><?php echo _('Manage'); ?></th>
                </tr>
                </thead>
                <tbody>
                <?php if ($data['total']): ?>
                    <?php foreach ($data['list'] as $index => $value):
                        $page_param = '';
                        if ($this->input->get('page')) {
                            $page_param = '?page=' . $this->input->get('page');
                        }
                        ?>
                        <tr<?php if (isset($data['content'])): ?><?php if ($data['content']['id'] == $value['id']): ?> class="table-primary"<?php endif; ?><?php endif; ?>>
                            <td><?php echo anchor('message-prepares/view/' . $value['id'], $value['title']); ?></td>
                            <td class="d-none d-sm-table-cell"><?php echo anchor('message-prepare-contents/view/' . $value['id'], _('Show Content'), array('class' => 'btn btn-secondary btn-modal')); ?></td>
                            <td><?php echo get_dt_format($value['created_at'], $search_data['timezone']); ?></td>
                            <td class="d-none d-sm-table-cell">
                                <?php echo anchor('message-prepares/edit/' . $value['id'] . $page_param, _('Edit'), array('class' => 'btn btn-secondary')); ?>
                                <?php echo anchor('message-prepares/delete/' . $value['id'], _('Delete'), array('class' => 'btn btn-danger btn-delete-confirm')); ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="4" style="text-align:center"><?php echo _('No Data'); ?></td>
                    </tr>
                <?php endif; ?>
                </tbody>
            </table>
            <?php echo $this->pagination->create_links(); ?>
            <?php echo anchor('message-prepares/add', _('Add'), array('class' => 'btn btn-primary hidden-xxl')); ?>
        </article>
    </div>
</div>

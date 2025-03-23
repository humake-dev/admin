<div id="centers" class="container">
    <div class="row">
        <div class="col-12">
            <h2 class="float-left"><?php echo _('Center List'); ?></h2>
            <div class="float-right">
                <p class="summary">
                    <span id="list_count" style="display:none"><?php echo $data['total']; ?></span>
                    <?php echo sprintf(_('There Are %d Center'), $data['total']); ?>
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
                    <col style="width:150px">
                </colgroup>
                <thead class="thead-default">
                <tr>
                    <th><?php echo _('Title'); ?></th>
                    <th class="d-none d-md-table-cell"><?php echo _('Image'); ?></th>
                    <th><?php echo _('Branch Count'); ?></th>
                    <th><?php echo _('Enable'); ?></th>
                    <th><?php echo _('Created At'); ?></th>
                    <th class="d-none d-lg-table-cell text-center"><?php echo _('Manage'); ?></th>
                </tr>
                </thead>
                <tbody>
                <?php if (empty($data['total'])): ?>
                    <tr>
                        <td colspan="5" style="text-align:center"><?php echo _('No Data'); ?></td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($data['list'] as $index => $value):
                        $page_param = '';
                        if ($this->input->get('page')) {
                            $page_param = '?page=' . $this->input->get('page');
                        }
                        ?>
                        <tr<?php if (isset($data['content'])): ?><?php if ($data['content']['id'] == $value['id']): ?> class="table-primary"<?php endif; ?><?php endif; ?>>
                            <td><?php echo anchor('centers/view/' . $value['id'], $value['title']); ?></td>
                            <td class="d-none d-md-table-cell">
                                <?php if (empty($value['picture']['total'])): ?>
                                    <?php echo _('Not Inserted'); ?>
                                <?php else: ?>
                                    <?php
                                    foreach ($value['picture']['list'] as $picture):
                                        ?>
                                        <form action="/center-pictures/delete/<?php echo $picture['id']; ?>"
                                              method="post">
                                            <div>
                                                <img src="<?php echo getPhotoPath('center', $value['id'], $picture['picture_url'], 'small'); ?>"/>
                                            </div>
                                            <input type="submit" value="<?php echo _('Delete'); ?>"
                                                   class="btn btn-danger">
                                        </form>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </td>
                            <td><?php echo $value['branch_counts']; ?></td>
                            <td><?php echo change_enable($value['enable']); ?></td>
                            <td><?php echo get_dt_format($value['created_at'], $search_data['timezone']); ?></td>
                            <td class="d-none d-lg-table-cell text-center">
                                <?php echo anchor('centers/edit/' . $value['id'] . $page_param, _('Edit'), array('class' => 'btn btn-secondary')); ?>
                                <?php if ($this->session->userdata('role_id') == 1): ?>
                                    <?php if ($value['enable']): ?>
                                        <?php echo anchor('centers/disable-confirm/' . $value['id'], _('Disable'), array('class' => 'btn btn-danger btn-disable-confirm')); ?>
                                    <?php else: ?>
                                        <?php echo anchor('centers/enable-confirm/' . $value['id'], _('Enable'), array('class' => 'btn btn-success btn-enable-confirm')); ?>
                                        <?php echo anchor('centers/delete/' . $value['id'], _('Delete'), array('class' => 'btn btn-danger')); ?>
                                    <?php endif; ?>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
                </tbody>
            </table>
            <?php if ($this->session->userdata('role_id') == 1): ?>
                <?php echo $this->pagination->create_links(); ?>
                <?php echo anchor('centers/add', _('Add'), array('class' => 'btn btn-primary')); ?>
            <?php endif; ?>
        </article>
    </div>
</div>

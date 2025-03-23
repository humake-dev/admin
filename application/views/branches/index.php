<div id="branches" class="container">
    <div class="row">
        <div class="col-12">
            <h2 class="float-left"><?php echo _('Branch List'); ?></h2>
            <div class="float-right">
                <p class="summary">
                    <span id="list_count" style="display:none"><?php echo $data['total']; ?></span>
                    <?php echo sprintf(_('There Are %d Branch'), $data['total']); ?>
                </p>
            </div>
        </div>
        <article class="col-12">
            <table id="prepare_list" class="table table-striped table-hover">
                <colgroup>
                    <?php if ($this->session->userdata('role_id') == 1): ?>
                        <col>
                    <?php endif; ?>
                    <col>
                    <col class="d-none d-md-table-cell">
                    <col class="d-none d-lg-table-cell">
                    <?php if ($this->session->userdata('role_id') == 1): ?>
                        <col>
                        <col>
                    <?php endif; ?>
                    <col <?php if ($this->session->userdata('center_id')): ?> style="width:150px"<?php else: ?> style="width:70px"<?php endif; ?>>
                </colgroup>
                <thead class="thead-default">
                <tr>
                    <?php if ($this->session->userdata('role_id') == 1): ?>
                        <th><?php echo _('Center'); ?></th>
                    <?php endif; ?>
                    <th><?php echo _('Title'); ?></th>
                    <th class="d-none d-lg-table-cell"><?php echo _('Description'); ?></th>
                    <th class="d-none d-md-table-cell"><?php echo _('Image'); ?></th>
                    <?php if ($this->session->userdata('role_id') == 1): ?>
                        <th><?php echo _('Enable'); ?></th>
                        <th><?php echo _('Created At'); ?></th>
                    <?php endif; ?>
                    <th class="d-none d-lg-table-cell text-center"><?php echo _('Manage'); ?></th>
                </tr>
                </thead>
                <tbody>
                <?php if (empty($data['total'])): ?>
                    <tr>
                        <td colspan="4" style="text-align:center"><?php echo _('No Data'); ?></td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($data['list'] as $index => $value):
                        $page_param = '';
                        if ($this->input->get('page')) {
                            $page_param = '?page=' . $this->input->get('page');
                        }
                        ?>
                        <tr<?php if (isset($data['content'])): ?><?php if ($data['content']['id'] == $value['id']): ?> class="table-primary"<?php endif; ?><?php endif; ?>>
                            <?php if ($this->session->userdata('role_id') == 1): ?>
                                <td><?php echo $value['center_name']; ?></td>
                            <?php endif; ?>
                            <td><?php echo anchor('branches/view/' . $value['id'], $value['title']); ?></td>
                            <td class="d-none d-lg-table-cell"><?php echo $value['description']; ?></td>
                            <td class="d-none d-md-table-cell">
                                <?php if (empty($value['picture']['total'])): ?>
                                    <?php echo _('Not Inserted'); ?>
                                <?php else: ?>
                                    <?php
                                    foreach ($value['picture']['list'] as $picture):
                                        ?>
                                        <form action="/branch-pictures/delete/<?php echo $picture['id']; ?>"
                                              method="post">
                                            <div>
                                                <img src="<?php echo getPhotoPath('branch', $value['id'], $picture['picture_url'], 'small'); ?>"/>
                                            </div>
                                            <input type="submit" value="<?php echo _('Delete'); ?>"
                                                   class="btn btn-danger">
                                        </form>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </td>
                            <?php if ($this->session->userdata('role_id') == 1): ?>
                                <td><?php echo change_enable($value['enable']); ?></td>
                                <td><?php echo get_dt_format($value['created_at'], $search_data['timezone']); ?></td>
                            <?php endif; ?>
                            <td class="d-none d-lg-table-cell text-center">
                                <?php echo anchor('branches/edit/' . $value['id'] . $page_param, _('Edit'), array('class' => 'btn btn-secondary')); ?>
                                <?php if ($this->session->userdata('role_id') == 1): ?>
                                    <?php if ($value['enable']): ?>
                                        <?php echo anchor('branches/disable/' . $value['id'], _('Disable'), array('class' => 'btn btn-danger btn-disable-confirm')); ?>
                                    <?php else: ?>
                                        <?php echo anchor('branches/enable-confirm/' . $value['id'], _('Enable'), array('class' => 'btn btn-success btn-enable-confirm')); ?>
                                        <?php echo anchor('branches/delete/' . $value['id'], _('Delete'), array('class' => 'btn btn-danger')); ?>
                                    <?php endif; ?>
                                <?php else: ?>
                                    <?php if ($this->session->userdata('center_id')): ?>
                                        <?php echo anchor('branches/delete/' . $value['id'], _('Delete'), array('class' => 'btn btn-danger')); ?>
                                    <?php endif; ?>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
                </tbody>
            </table>
            <?php echo $this->pagination->create_links(); ?>
            <?php echo anchor('branches/add', _('Add'), array('class' => 'btn btn-primary')); ?>
        </article>
    </div>
</div>

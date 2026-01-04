<div id="users" class="container">
    <div class="row">
        <?php include __DIR__ . DIRECTORY_SEPARATOR . 'aside.php'; ?>
        <div class="col-12 col-lg-8 col-xxl-9 user_sub">
            <?php if (empty($data['content'])): ?>
                <?php echo $Layout->element('home/not_found.php'); ?>
            <?php else: ?>
                <?php echo $Layout->element('home/nav'); ?>
                <article class="card">
                    <div class="card-header">
                        <ul class="nav nav-pills card-header-pills">
                            <li class="nav-item"><a class="nav-link active"
                                                    href="#"><?php echo _('Rent Sws Info'); ?></a></li>
                            <?php if ($data['end_list']['total']): ?>
                                <li class="nav-item"><a class="nav-link"
                                                        href="#"><?php echo _('End Rent Sws Info'); ?></a></li>
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
                                    <ul class="sl-bnt-group">
                                        <?php if ($this->Acl->has_permission('rent_sws', 'write')): ?>
                                            <li><?php echo anchor('rent-sws/add?user_id=' . $data['content']['id'], _('Add Rent'), array('id' => 'user_rent_sw_add', 'class' => 'btn btn-primary')); ?></li>
                                        <?php endif; ?>
                                        <?php if (isset($data['rent_sws']['content']) and empty($data['rent_sws']['content']['stopped'])): ?>
                                            <?php if ($this->Acl->has_permission('rent_sws', 'write')): ?>
                                                <li><?php echo anchor('rent-sws/edit/' . $data['rent_sws']['content']['id'] . '?user-page=true', _('Edit'), array('id' => 'user_rent_sw_edit', 'class' => 'btn btn-secondary')); ?></li>
                                            <?php endif; ?>
                                            <?php if ($this->session->userdata('role_id') < 4): ?>
                                                <li><?php echo anchor('rent-sws/transfer/' . $data['rent_sws']['content']['id'], _('Transfer'), array('id'=>'user_rent_sw_transfer','class' => 'btn btn-secondary')); ?></li>
                                            <?php endif ?>
                                            <?php if ($this->Acl->has_permission('rent_sws', 'write')): ?>
                                                <li><?php echo anchor('rent-sws/end/' . $data['rent_sws']['content']['id'], _('End Order'), array('id' => 'user_rent_sw_delete', 'class' => 'btn btn-danger btn-modal')); ?></li>
                                            <?php endif; ?>
                                        <?php else: ?>
                                            <li><?php echo anchor('#', _('Edit'), array('id' => 'user_rent_sw_edit', 'class' => 'btn btn-secondary disabled')); ?></li>
                                            <?php if ($this->session->userdata('role_id') < 4): ?>
                                                <li><?php echo anchor('#', _('Transfer'), array('class' => 'btn btn-secondary disabled rent_sw_transfer')); ?></li>
                                            <?php endif ?>
                                            <?php if ($this->Acl->has_permission('rent_sws', 'delete')): ?>
                                            <li><?php echo anchor('#', _('Cancel'), array('id' => 'user_rent_sw_delete', 'class' => 'btn btn-danger disabled')); ?></li>
                                            <?php endif ?>
                                        <?php endif; ?>
                                    </ul>
                                </div>
                                <div class="col-12">
                                    <div class="table-responsive">
                                        <?php
                                        $list = $data['rent_sws'];
                                        include __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'rent_sws' . DIRECTORY_SEPARATOR . 'list_content.php';
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
                            $data['rent_sws'] = $data['end_list']['list'][0];
                            ?>
                            <div class="card-block" style="display:none">
                                <div class="row">
                                <div class="col-12">
                                        <ul class="sl-bnt-group">
                                            <?php if ($this->session->userdata('branch_id')): ?>
                                                <?php if ($this->Acl->has_permission('rent_sws', 'write')): ?>
                                                    <?php if ($this->session->userdata('role_id') < 3): ?>
                                                        <li><?php echo anchor('rent-sws/edit/' . $data['rent_sws']['id'] . '?user-page=true', _('Edit'), ['id' => 'user_rent_edit_expire_log', 'class' => 'btn btn-secondary']); ?></li>
                                                        <li><?php echo anchor('rent-sws/disable/' . $data['rent_sws']['id'], _('Delete'), ['id' => 'user_rent_delete_expire_log', 'class' => 'btn btn-danger']); ?></li>
                                                    <?php endif ?>
                                                <?php endif; ?>
                                            <?php endif; ?>

                                            <li class="float-right" style="margin-right:0">
                                                <?php if (isset($data['rent_sws'])): ?>
                                                <?php else : ?>
                                                    <?php echo anchor('#', _('Export Excel'), ['class' => 'btn btn-secondary disabled']); ?>
                                                <?php endif; ?>
                                            </li>
                                        </ul>
                                    </div>
                                    <div class="col-12">
                                        <div class="table-responsive">
                                            <?php include __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'rent_sws' . DIRECTORY_SEPARATOR . 'list_content.php'; ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>
                    <div class="col-12">
                        <div class="sl_pagination">
                            <?php if (!empty($data['total'])): ?>
                                <?php echo $this->pagination->create_links(); ?>
                            <?php endif; ?>
                        </div>
                    </div>
                </article>
            <?php endif; ?>
        </div>
    </div>
</div>  
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
                            <li class="nav-item"><a class="nav-link active" href="#"><?php echo _('Rent Info'); ?></a>
                            </li>
                            <?php if ($data['end_list']['total']): ?>
                                <li class="nav-item">
                                    <a class="nav-link" href="#"><?php echo _('End Rent Info'); ?></a>
                                </li>
                            <?php endif; ?>
                            <?php if ($data['transfer_list']['total']): ?>
                                <li class="nav-item">
                                    <a class="nav-link" href="#"><?php echo _('Transfer Rent Info'); ?></a>
                                </li>
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
                                    <input type="hidden" id="text_resume_order"
                                           value="<?php echo _('Resume Order'); ?>"/>
                                    <input type="hidden" id="text_stop_order" value="<?php echo _('Stop Order'); ?>"/>
                                    <input type="hidden" id="text_end_order" value="<?php echo _('End Order'); ?>"/>
                                    <input type="hidden" id="text_delete_rent" value="<?php echo _('Delete Rent'); ?>"/>
                                    <ul class="sl-bnt-group">
                                        <?php if ($this->session->userdata('branch_id')): ?>
                                            <?php if ($this->Acl->has_permission('rents', 'write')): ?>
                                                <li><?php echo anchor('/rents/add?user_id=' . $data['content']['id'], _('Add Rent'), ['class' => 'btn btn-primary']); ?></li>
                                            <?php endif ?>
                                            <?php if (isset($data['rent']['content']) and empty($data['rent']['content']['stopped'])): ?>
                                                <?php if ($this->Acl->has_permission('rents', 'write')): ?>
                                                    <li><?php echo anchor('/rents/edit/' . $data['rent']['content']['id'] . '?user-page=true', _('Rent Edit'), ['id' => 'user_rent_edit', 'class' => 'btn btn-secondary']); ?></li>
                                                <?php endif ?>
                                                <?php if ($this->session->userdata('role_id') < 4): ?>
                                                    <?php if (new DateTime($data['rent']['content']['end_date'],$search_data['timezone']) <= new DateTime('now',$search_data['timezone'])): ?>
                                                        <li><?php echo anchor('/rents/transfer/' . $data['rent']['content']['id'], _('Transfer'), ['id' => 'user_rent_transfer', 'class' => 'btn btn-secondary disabled']); ?></li>
                                                    <?php else : ?>
                                                        <li><?php echo anchor('/rents/transfer/' . $data['rent']['content']['id'], _('Transfer'), ['id' => 'user_rent_transfer', 'class' => 'btn btn-secondary']); ?></li>
                                                    <?php endif; ?>
                                                <?php endif; ?>
                                                <?php if (new DateTime($data['rent']['content']['end_date'],$search_data['timezone']) <= new DateTime('now',$search_data['timezone'])): ?>
                                                    <li><?php echo anchor('/rents/move/' . $data['rent']['content']['id'], _('Move'), ['id' => 'user_rent_move', 'class' => 'btn btn-secondary disabled']); ?></li>
                                                <?php else: ?>
                                                    <li><?php echo anchor('/rents/move/' . $data['rent']['content']['id'], _('Move'), ['id' => 'user_rent_move', 'class' => 'btn btn-secondary']); ?></li>
                                                <?php endif ?>
                                                <?php if ($data['rent']['content']['expired']): ?>
                                                    <li><?php echo anchor('/rents/end/' . $data['rent']['content']['id'] . '?return=true', _('Return'), ['id' => 'user_rent_delete', 'class' => 'btn btn-danger btn-modal']); ?></li>
                                                <?php else : ?>
                                                    <li><?php echo anchor('/rents/end/' . $data['rent']['content']['id'], _('End Order'), ['id' => 'user_rent_delete', 'class' => 'btn btn-danger btn-modal']); ?></li>
                                                <?php endif; ?>

                                            <?php else: ?>
                                                <?php if ($this->Acl->has_permission('rents', 'write')): ?>
                                                    <li><?php echo anchor('#', _('Rent Edit'), ['id' => 'user_rent_edit', 'class' => 'btn btn-secondary disabled']); ?></li>
                                                <?php endif ?>
                                                <?php if ($this->session->userdata('role_id') < 4): ?>
                                                    <li><?php echo anchor('#', _('Transfer'), ['id' => 'user_rent_transfer', 'class' => 'btn btn-secondary disabled']); ?></li>
                                                <?php endif; ?>
                                                <li><?php echo anchor('#', _('Move'), ['id' => 'user_rent_move', 'class' => 'btn btn-secondary disabled']); ?></li>
                                                <li><?php echo anchor('#', _('End Order'), ['id' => 'user_rent_delete', 'class' => 'btn btn-danger disabled']); ?></li>
                                            <?php endif; ?>
                                        <?php endif; ?>
                                    </ul>
                                </div>
                                <div class="col-12">
                                    <div class="table-responsive">
                                        <?php

                                        $list = $data['rent'];
                                        include __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'rents' . DIRECTORY_SEPARATOR . 'list.php';
                                        ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <?php
                        if ($data['end_list']['total']):
                            $table_id = 'user_end_rent_list';
                            $list = $data['end_list'];
                            $end_list = 1;
                            $data['rent']['content'] = $data['end_list']['list'][0];
                            ?>
                            <div class="card-block" style="display:none">
                                <div class="row">
                                    <div class="col-12">
                                        <ul class="sl-bnt-group">
                                            <?php if ($this->session->userdata('branch_id')): ?>
                                                <?php if ($this->Acl->has_permission('rents', 'write')): ?>
                                                    <li><?php echo anchor('rents/add?after=' . $data['rent']['content']['id'] . '&amp;user_id=' . $data['rent']['content']['user_id'], _('Re Rent'), ['id' => 'user_re_rent', 'class' => 'btn btn-secondary re-order']); ?></li>
                                                    <?php if ($this->session->userdata('role_id') < 3): ?>
                                                        <li><?php echo anchor('rents/edit/' . $data['rent']['content']['id'], _('Edit'), ['id' => 'user_rent_edit_expire_log', 'class' => 'btn btn-secondary']); ?></li>
                                                        <li><?php echo anchor('rents/disable/' . $data['rent']['content']['id'], _('Delete'), ['id' => 'user_rent_delete_expire_log', 'class' => 'btn btn-danger']); ?></li>
                                                    <?php endif ?>
                                                <?php endif; ?>
                                            <?php endif; ?>

                                            <li class="float-right" style="margin-right:0">
                                                <?php if (isset($data['rent'])): ?>
                                                    <?php echo anchor('/rents/export_excel/' . $data['content']['id'] . '?end_rent=1', _('Export Excel'), ['class' => 'btn btn-secondary']); ?>
                                                <?php else : ?>
                                                    <?php echo anchor('#', _('Export Excel'), ['class' => 'btn btn-secondary disabled']); ?>
                                                <?php endif; ?>
                                            </li>
                                        </ul>
                                    </div>
                                    <div class="col-12">
                                        <div class="table-responsive">
                                            <?php include __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'rents' . DIRECTORY_SEPARATOR . 'list.php'; ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endif; ?>
                        <?php if ($data['transfer_list']['total']): ?>
                            <div class="card-block" style="display:none">
                                <div class="row">
                                    <div class="col-12">
                                        <?php include __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'order_transfers' . DIRECTORY_SEPARATOR . 'list.php'; ?>
                                    </div>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>
                </article>
            <?php endif; ?>
        </div>
    </div>
</div>

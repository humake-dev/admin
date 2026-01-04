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
                            <div class="card-header">
                                <ul class="nav nav-pills card-header-pills">
                                    <li class="nav-item"><a class="nav-link active"
                                                            href="#"><?php echo _('Enroll Info'); ?></a></li>
                                    <?php if ($data['end_list']['total']): ?>
                                        <li class="nav-item">
                                            <a class="nav-link" href="#"><?php echo _('End Enroll Info'); ?></a>
                                        </li>
                                    <?php endif; ?>
                                    <?php if ($data['transfer_list']['total']): ?>
                                        <li class="nav-item">
                                            <a class="nav-link" href="#"><?php echo _('Transfer Enroll Info'); ?></a>
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
                                            <ul class="sl-bnt-group">
                                                <?php if ($this->session->userdata('branch_id')): ?>
                                                    <?php if ($this->Acl->has_permission('enrolls', 'write')): ?>
                                                        <li><?php echo anchor('enrolls/add?user_id=' . $data['content']['id'], _('Add Enroll'), array('id' => 'user_enroll_', 'class' => 'btn btn-primary')); ?></li>
                                                        <?php if (isset($data['enroll']['content'])): ?>
                                                            <?php if ($data['enroll']['content']['stopped']): ?>
                                                                <li><?php echo anchor('#', _('Edit'), array('id' => 'user_enroll_edit', 'class' => 'btn btn-secondary disabled')); ?></li>
                                                                <?php if ($this->session->userdata('role_id') < 4): ?>
                                                                    <li><?php echo anchor('#', _('Transfer'), array('id' => 'user_enroll_transfer', 'class' => 'btn btn-secondary disabled')); ?></li>
                                                                <?php endif; ?>
                                                            <?php else: ?>
                                                                <li><?php echo anchor('enrolls/edit/' . $data['enroll']['content']['id'], _('Edit'), array('id' => 'user_enroll_edit', 'class' => 'btn btn-secondary')); ?></li>
                                                                <?php if ($this->session->userdata('role_id') < 4): ?>
                                                                    <li><?php echo anchor('enrolls/transfer/' . $data['enroll']['content']['id'], _('Transfer'), array('id' => 'user_enroll_transfer', 'class' => 'btn btn-secondary')); ?></li>
                                                                <?php endif; ?>
                                                            <?php endif; ?>
                                                            <?php if (empty($data['enroll']['content']['ended'])): ?>
                                                                <li><?php echo anchor('enrolls/end/' . $data['enroll']['content']['id'], _('End Order'), array('id' => 'user_enroll_end', 'class' => 'btn btn-danger btn-modal')); ?></li>
                                                                <?php if ($this->session->userdata('role_id') < 3): ?>
                                                                    <li><?php echo anchor('enrolls/delete/' . $data['enroll']['content']['id'], _('Delete'), array('id' => 'user_enroll_delete', 'class' => 'btn btn-danger btn-modal', 'style' => 'display:none')); ?></li>
                                                                    <li><?php echo anchor('enrolls/recover/' . $data['enroll']['content']['id'], _('Recover'), array('id' => 'user_enroll_recover', 'class' => 'btn btn-success btn-modal', 'style' => 'display:none')); ?></li>
                                                                <?php endif; ?>
                                                            <?php else: ?>
                                                                <li><?php echo anchor('enrolls/end/' . $data['enroll']['content']['id'], _('End Order'), array('id' => 'user_enroll_end', 'class' => 'btn btn-danger btn-modal', 'style' => 'display:none')); ?></li>
                                                                <?php if ($this->session->userdata('role_id') < 3): ?>
                                                                    <li><?php echo anchor('enrolls/delete/' . $data['enroll']['content']['id'], _('Delete'), array('id' => 'user_enroll_delete', 'class' => 'btn btn-danger btn-modal')); ?></li>
                                                                    <li><?php echo anchor('enrolls/recover/' . $data['enroll']['content']['id'], _('Recover'), array('id' => 'user_enroll_recover', 'class' => 'btn btn-success btn-modal')); ?></li>
                                                                <?php endif; ?>
                                                            <?php endif; ?>

                                                        <?php endif; ?>
                                                    <?php endif; ?>
                                                <?php endif; ?>
                                                <li class="float-right" style="margin-right:0">
                                                    <?php if (isset($data['enroll']['content'])): ?>
                                                        <?php echo anchor('/enrolls/export-excel?user_id=' . $data['content']['id'], _('Export Excel'), array('class' => 'btn btn-secondary')); ?>
                                                    <?php else : ?>
                                                        <?php echo anchor('#', _('Export Excel'), array('class' => 'btn btn-secondary disabled')); ?>
                                                    <?php endif; ?>
                                                </li>
                                            </ul>
                                        </div>
                                        <div class="col-12">
                                            <div class="table-responsive">
                                                <?php
                                                $list = $data['enroll'];

                                                include __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'enrolls' . DIRECTORY_SEPARATOR . 'list.php';
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
                                    $data['enroll']['content'] = $data['end_list']['list'][0];
                                    ?>
                                    <div class="card-block" style="display:none">
                                        <div class="row">
                                            <div class="col-12">
                                                <ul class="sl-bnt-group">

                                                    <?php if ($this->session->userdata('branch_id')): ?>
                                                        <?php if ($this->Acl->has_permission('enrolls', 'write')): ?>
                                                            <li><?php echo anchor('enrolls/add?after=' . $data['enroll']['content']['id'], _('Re Enroll'), array('id' => 'user_re_enroll', 'class' => 'btn btn-secondary')); ?></li>
                                                            <li><?php echo anchor('enrolls/disable/' . $data['enroll']['content']['id'], _('Expire Log Delete'), array('id' => 'user_enroll_delete_expire_log', 'class' => 'btn btn-secondary btn-modal')); ?></li>
                                                            <li><?php echo anchor('enrolls/edit/' . $data['enroll']['content']['id'], _('Edit'), array('id' => 'user_enroll_edit_expire_log', 'class' => 'btn btn-secondary')); ?></li>
                                                        <?php endif; ?>
                                                    <?php endif; ?>

                                                    <li class="float-right" style="margin-right:0">
                                                        <?php if (isset($data['enroll'])): ?>
                                                            <?php echo anchor('/enrolls/export_excel/' . $data['content']['id'] . '?end_enroll=1', _('Export Excel'), array('class' => 'btn btn-secondary')); ?>
                                                        <?php else : ?>
                                                            <?php echo anchor('#', _('Export Excel'), array('class' => 'btn btn-secondary disabled')); ?>
                                                        <?php endif; ?>
                                                    </li>
                                                </ul>
                                            </div>
                                            <div class="col-12">
                                                <div class="table-responsive">
                                                    <?php include __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'enrolls' . DIRECTORY_SEPARATOR . 'list.php'; ?>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                <?php endif; ?>
                                <?php if ($data['transfer_list']['total']): ?>
                                    <div class="card-block" style="display:none">
                                        <div class="row">
                                            <div class="col-12">
                                                <div style="table-responsive">
                                                    <?php include __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'order_transfers' . DIRECTORY_SEPARATOR . 'list.php'; ?>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </article>
                    </div>
                    <div class="col-12">
                        <?php

                        if (!empty($data['enroll']['total'])) {
                            $data['enroll']['content'] = $data['enroll']['list'][0];
                        }
                        ?>
                        <?php include __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'enrolls' . DIRECTORY_SEPARATOR . 'payment_log.php'; ?>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

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
                        <div class="card">
                            <?php if ($this->session->userdata('role_id') < 4): ?>
                                <div class="card-header">
                                    <ul class="nav nav-pills card-header-pills">
                                        <li class="nav-item"><a class="nav-link active"
                                                                href="#"><?php echo _('User Memo'); ?></a></li>
                                        <li class="nav-item">
                                            <a class="nav-link" href="#"><?php echo _('Order Memo'); ?></a>
                                        </li>
                                        <li class="nav-item">
                                            <a class="nav-link" href="#"><?php echo _('Order Edit Log'); ?></a>
                                        </li>
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
                                                    <li><?php echo anchor('/user-contents/add?user_id=' . $data['content']['id'], _('Add'), array('class' => 'btn btn-primary btn-modal')); ?></li>
                                                </ul>
                                                <?php include __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'user_contents' . DIRECTORY_SEPARATOR . 'list.php'; ?>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="card-block" style="display:none">
                                        <div class="row">
                                            <div class="col-12">
                                                <?php

                                                if (empty($data['order_memo']['total'])) {
                                                    $data['total'] = $data['order_memo']['total'];
                                                } else {
                                                    $data['total'] = $data['order_memo']['total'];
                                                    $data['list'] = $data['order_memo']['list'];
                                                }

                                                include __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'order_contents' . DIRECTORY_SEPARATOR . 'list.php';

                                                ?>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="card-block" style="display:none">
                                        <div class="row">
                                            <div class="col-12">
                                                <?php

                                                if (empty($data['order_edit_log']['total'])) {
                                                    $data['total'] = $data['order_edit_log']['total'];
                                                } else {
                                                    $data['total'] = $data['order_edit_log']['total'];
                                                    $data['list'] = $data['order_edit_log']['list'];
                                                }
                                                include __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'order_edit_logs' . DIRECTORY_SEPARATOR . 'memo_list.php';
                                                ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php else: ?>
                                <h3 class="col-12 card-header"><?php echo _('User Memo'); ?></h3>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-12">
                                            <ul class="sl-bnt-group">
                                                <li><?php echo anchor('/user-contents/add?user_id=' . $data['content']['id'], _('Add'), array('class' => 'btn btn-primary btn-modal')); ?></li>
                                            </ul>
                                            <?php include __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'user_contents' . DIRECTORY_SEPARATOR . 'list.php'; ?>
                                        </div>
                                    </div>
                                </div>
                            <?php endif ?>
                        </div><!-- card end -->
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

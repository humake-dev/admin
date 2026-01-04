<?php

switch ($this->input->get('tab')) {
    case '2':
        if ($data['stop_schedules']['total']):
            $tab1_active = '';
            $tab2_active = ' active';
            $tab3_active = '';
            $tab4_active = '';
        else:
            $tab1_active = ' active';
            $tab2_active = '';
            $tab3_active = '';
            $tab4_active = '';
        endif;
        break;
    case '3':
        if ($data['stopped_log']['total']):
            $tab1_active = '';
            $tab2_active = '';
            $tab3_active = ' active';
            $tab4_active = '';
        else:
            $tab1_active = ' active';
            $tab2_active = '';
            $tab3_active = '';
            $tab4_active = '';
        endif;
        break;
    case '4' :
        $tab1_active = '';
        $tab2_active = '';
        $tab3_active = '';
        $tab4_active = ' active';
        break;  
    default:
        $tab1_active = ' active';
        $tab2_active = '';
        $tab3_active = '';
        $tab4_active = '';
}

?>
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
                            <li class="nav-item"><a class="nav-link<?php echo $tab1_active; ?>"
                                                    href="#"><?php echo _('Stop Info'); ?></a></li>
                            <?php if ($data['stop_schedules']['total']): ?>
                                <li class="nav-item"><a class="nav-link<?php echo $tab2_active; ?>"
                                                        href="#"><?php echo _('Stop Order Schedule'); ?></a></li>
                            <?php endif; ?>
                            <?php if (!empty($data['user_stop_list']['total']) and !empty($data['stopped_log']['total'])): ?>
                                <li class="nav-item"><a class="nav-link<?php echo $tab3_active; ?>"
                                                        href="#"><?php echo _('Stopped Order Log'); ?></a></li>
                            <?php endif; ?>
                            <?php if (!empty($data['admin_order_list']['total'])): ?>
                                <li class="nav-item"><a class="nav-link<?php echo $tab4_active; ?>"
                                                        href="#"><?php echo _('Admin Stopped Info'); ?></a></li>
                            <?php endif; ?>
                        </ul>
                        <div class="float-right buttons">
                            <i class="material-icons">keyboard_arrow_up</i>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="card-block"<?php if (empty($tab1_active)): ?> style="display:none"<?php endif; ?>>
                            <div class="row">
                                <div class="col-12">
                                    <ul class="sl-bnt-group">
                                        <?php if (empty($data['order_list']['total'])): ?>
                                        <?php else: ?>
                                            <?php if (empty($data['stopping_list']['total'])): ?>
                                                <?php if ($this->Acl->has_permission('enrolls', 'write')): ?>
                                                    <?php echo _('Select Enroll Use Stopping'); ?>
                                                <?php endif; ?>
                                            <?php else: ?>
                                                <?php if ($this->Acl->has_permission('enrolls', 'write')): ?>
                                                    <?php if ($data['available_add']): ?>
                                                        <li><?php echo anchor('/users/stop/' . $data['content']['id'] . '?schedule=1&amp;order_id=' . $data['user_stop_content']['order_id'], _('Add Stop Schedule'), array('id' => 'user_stop', 'class' => 'btn btn-secondary')); ?></li>
                                                    <?php endif; ?>
                                                    <li><?php echo anchor('/user-stops/resume/' . $data['user_stop_content']['id'], _('Resume Order'), array('id' => 'user_order_stop_all_resume', 'class' => 'btn btn-secondary btn-modal')); ?></li>
                                                    <li><?php echo anchor('/user-stops/edit/' . $data['user_stop_content']['id'], _('Edit'), array('id' => 'user_order_stop_edit', 'class' => 'btn btn-secondary btn-modal')); ?></li>
                                                    <li><?php echo anchor('/user-stops/delete/' . $data['user_stop_content']['id'], _('Cancel'), array('id' => 'user_order_stop_cancel', 'class' => 'btn btn-danger')); ?></li>
                                                <?php endif ?>
                                            <?php endif; ?>
                                        <?php endif; ?>
                                    </ul>
                                </div>
                                <div class="col-12">
                                    <?php if (empty($data['stopping_list']['total'])): ?>
                                        <?php if (empty($data['order_list']['total'])): ?>
                                            <p><?php echo _('Order Not Exists'); ?></p>
                                        <?php else: ?>
                                            <?php if ($this->Acl->has_permission('enrolls', 'read')): ?>
                                                <?php include __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'user_stops' . DIRECTORY_SEPARATOR . 'select_list.php'; ?>
                                            <?php endif; ?>
                                        <?php endif; ?>
                                    <?php else: ?>
                                        <div class="table-responsive">
                                            <?php include __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'user_stops' . DIRECTORY_SEPARATOR . 'list.php'; ?>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>

                        <?php if ($data['stop_schedules']['total']): ?>
                            <div class="card-block"<?php if (empty($tab2_active)): ?> style="display:none"<?php endif; ?>>
                                <div class="row">
                                    <div class="col-12">
                                        <div class="table-responsive">
                                            <?php include __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'user_stops' . DIRECTORY_SEPARATOR . 'schedule_list.php'; ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endif; ?>

                        <?php if (!empty($data['user_stop_list']['total']) and !empty($data['stopped_log']['total'])): ?>
                            <div class="card-block"<?php if (empty($tab3_active)): ?> style="display:none"<?php endif; ?>>
                                <div class="row">
                                    <div class="col-12">
                                        <div class="table-responsive">
                                            <?php include __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'user_stops' . DIRECTORY_SEPARATOR . 'log.php'; ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endif; ?>


                        <?php if (!empty($data['admin_order_list']['total'])):
                            
                            $data['order_list']=$data['admin_order_list']
                            ?>

                            <div class="card-block"<?php if (empty($tab3_active)): ?> style="display:none"<?php endif; ?>>
                                <div class="row">
                                    <div class="col-12">
                                    <ul class="sl-bnt-group">
                                                                    <li><?php echo _('Select Enroll Use Stopping'); ?></li>
                        </ul>
                                        <div class="table-responsive">
                                        <?php include __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'user_stops' . DIRECTORY_SEPARATOR . 'select_list.php'; ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endif ?>
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
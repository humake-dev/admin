<section id="user_summary" class="row">
    <h2 class="col-12 di_title"><?php echo _('User Info'); ?></h2>
    <div class="col-12">
        <article class="card">
            <div class="card-header">
                <h3><?php echo _('Memo'); ?></h3>
                <?php if ($this->session->userdata('branch_id')): ?>
                    <?php if ($this->Acl->has_permission('users', 'write')): ?>
                        <?php echo anchor('user-contents/add?user_id=' . $data['content']['id'], '<i class="material-icons">add</i>', array('id' => 'add-user-memo', 'class' => 'btn-modal more2')); ?>
                    <?php endif; ?>
                <?php endif; ?>
                <a href="/home/memo/<?php echo $data['content']['id']; ?><?php echo $params; ?>"
                   title="<?php echo _('More'); ?>" class="more"><i class="material-icons">redo</i></a>
            </div>
            <div class="card-body"
                 <?php if (empty($other_data['memo']['total'])): ?>style="min-height:10px"<?php endif; ?>>
                <div class="row">
                    <?php if (isset($other_data)): ?>
                        <div class="col-12">
                            <?php if (empty($other_data['memo']['total'])): ?>
                                <p><?php echo _('Not Inserted Memo'); ?></p>
                            <?php else: ?>
                                <?php foreach ($other_data['memo']['list'] as $index => $memo): ?>
                                    <div style="margin-bottom:20px">
                                        <?php echo anchor('user-contents/view/' . $memo['id'], nl2br($memo['content']), array('class' => 'btn-modal more')); ?>
                                        (<?php echo get_dt_format($memo['updated_at'], $search_data['timezone']); ?>)
                                    </div>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </div>
                    <?php else: ?>
                        -
                    <?php endif; ?>
                </div>
            </div>
            <?php if ($other_data['memo']['total'] > 3): ?>
                <div class="card-footer" style="padding:0">
                    <?php echo anchor('/home/memo/' . $data['content']['id'], '<i class="material-icons">keyboard_arrow_down</i>', array('id' => 'more-user-memo', 'class' => 'btn btn-sm btn-block btn-link')); ?>

                </div>
            <?php endif; ?>
        </article>
    </div>

    <?php if ($this->Acl->has_permission('enrolls', 'read')): ?>
        <?php include __DIR__ . DIRECTORY_SEPARATOR . 'summary_enroll.php'; ?>


        <?php if (!empty($other_data['pt']['total'])): ?>
            <?php include __DIR__ . DIRECTORY_SEPARATOR . 'summary_pt.php'; ?>
        <?php endif; ?>
    <?php endif ?>

    <article class="col-12" id="user-account-summary">
        <div class="row">
            <h3 class="col-12 col-lg-6"><?php echo _('Payment information'); ?></h3>
            <div class="col-12 col-lg-6 text-right">
                <?php if ($this->session->userdata('role_id') <= 5): ?>
                    <?php echo anchor('others/add?user_id=' . $data['content']['id'], '<i class="material-icons">add</i>', array('class' => 'btn-modal more2')); ?>
                <?php endif ?>
                <a href="/home/accounts/<?php echo $data['content']['id']; ?><?php echo $params; ?>"
                   title="<?php echo _('More'); ?>" class="more"><i class="material-icons">redo</i></a>
            </div>
        </div>
        <?php if (isset($other_data)): ?>
            <?php include __DIR__ . DIRECTORY_SEPARATOR . 'account_table.php'; ?>
        <?php else: ?>
            -
        <?php endif; ?>
    </article>

    <?php if ($this->Acl->has_permission('rents')): ?>
        <div class="col-12 col-lg-4">
            <article class="card">
                <div class="card-header">
                    <h3><?php echo _('Rent Info'); ?></h3>
                    <?php if ($this->session->userdata('branch_id')): ?>
                        <?php if ($this->Acl->has_permission('rents', 'write')): ?>
                            <?php echo anchor('rents/add?user_id=' . $data['content']['id'], '<i class="material-icons">add</i>', array('class' => 'more2')); ?>
                        <?php endif; ?>
                    <?php endif; ?>
                    <a href="/home/rents/<?php echo $data['content']['id']; ?><?php echo $params; ?>"
                       title="<?php echo _('More'); ?>" class="more"><i class="material-icons">redo</i></a>
                </div>
                <div class="card-body">
                    <div class="row">
                        <?php if (isset($other_data)): ?>
                            <?php if (empty($other_data['rent'])): ?>
                                <div class="col-12">
                                    <p><?php echo _('No locker registered'); ?>.</p>
                                </div>
                            <?php else: ?>
                                <dl class="col-6 col-lg-5">
                                    <dt><?php echo _('Facility No'); ?></dt>
                                    <dd><?php if (empty($other_data['rent']['no'])) : ?><?php echo _('Not Set'); ?><?php else: ?><?php echo $other_data['rent']['no']; ?><?php echo _('No'); ?><?php endif; ?></dd>
                                </dl>
                                <dl class="col-6 col-lg-7">
                                    <dt><?php echo _('Transaction Date'); ?></dt>
                                    <dd><?php echo get_dt_format($other_data['rent']['transaction_date'], $search_data['timezone']); ?></dd>
                                </dl>
                                <dl class="col-12">
                                    <dt><?php echo _('Period'); ?></dt>
                                    <dd>

                                        <?php echo get_dt_format($other_data['rent']['start_date'], $search_data['timezone']); ?>
                                        ~
                                        <?php
                                        if ($other_data['rent']['stopped']):
                                            if ($other_data['rent']['stop_end_date'] and $other_data['rent']['change_end_date']):
                                                echo get_dt_format($other_data['rent']['change_end_date'], $search_data['timezone']);
                                            else:
                                                echo get_dt_format($other_data['rent']['end_date'], $search_data['timezone']);
                                            endif;
                                        else:
                                            echo get_dt_format($other_data['rent']['end_date'], $search_data['timezone']);
                                        endif;
                                        ?>
                                    </dd>
                                </dl>
                            <?php endif; ?>
                        <?php else: ?>
                            -
                        <?php endif; ?>
                    </div>
                </div>
            </article>
        </div>
    <?php endif; ?>


    <div class="col-12 col-lg-4">
        <article class="card">
            <div class="card-header">
                <h3><?php echo _('Entrance Info'); ?></h3>
                <?php if ($this->session->userdata('branch_id')): ?>
                    <?php echo anchor('entrances/add?user_id=' . $data['content']['id'], '<i class="material-icons">add</i>', array('id' => 'add-user-attendance', 'class' => 'btn-modal more2')); ?>
                <?php endif; ?>
                <a href="/home/attendances/<?php echo $data['content']['id']; ?><?php echo $params; ?>"
                   title="<?php echo _('More'); ?>" class="more"><i class="material-icons">redo</i></a>
            </div>
            <div class="card-body">
                <div class="row">
                    <?php if (empty($other_data['entrance'])): ?>
                        <div class="col-12">
                            <p><?php echo _('No attendance information'); ?>.</p>
                        </div>
                    <?php else: ?>
                        <dl class="col-6">
                            <dt><?php echo _('Attendance count'); ?></dt>
                            <dd><?php echo $other_data['entrance']['entrance_total']; ?><?php echo _('Number'); ?></dd>
                        </dl>
                        <dl class="col-6">
                            <dt><?php echo _('Last entry date'); ?></dt>
                            <dd>
                                <?php echo get_dt_format($other_data['entrance']['in_time'], $search_data['timezone']); ?>
                                <br/>
                                <?php echo get_dt_format($other_data['entrance']['in_time'], $search_data['timezone'], 'H' . _('Hour') . ' i' . _('Minute')); ?>
                            </dd>
                        </dl>
                    <?php endif; ?>
                </div>
            </div>
        </article>
    </div>
    <?php if ($this->Acl->has_permission('rent_sws')): ?>
        <div class="col-12 col-lg-4">
            <article class="card">
                <div class="card-header">
                    <h3><?php echo _('Athletic Clothing History'); ?></h3>
                    <?php if ($this->session->userdata('branch_id')): ?>
                        <?php if ($this->Acl->has_permission('rent_sws', 'write')): ?>
                            <?php echo anchor('rent-sws/add?user_id=' . $data['content']['id'], '<i class="material-icons">add</i>', array('id' => 'add-user-rent-sws', 'class' => 'more2')); ?>
                        <?php endif; ?>
                    <?php endif; ?>
                    <a href="/home/rent-sws/<?php echo $data['content']['id']; ?>" title="<?php echo _('More'); ?>"
                       class="more"><i class="material-icons">redo</i></a>
                </div>
                <div class="card-body">
                    <div class="row">
                        <?php if (empty($other_data['rent_sw'])): ?>
                            <div class="col-12">
                                <p><?php echo _('No sports clothes'); ?>.</p>
                            </div>
                        <?php else: ?>
                            <dl class="col-6 col-lg-7">
                            <dt><?php echo _('Period'); ?></dt>
                                    <dd>

                                        <?php echo get_dt_format($other_data['rent_sw']['start_date'], $search_data['timezone']); ?>
                                        ~ <br />
                                        <?php
                                            echo get_dt_format($other_data['rent_sw']['end_date'], $search_data['timezone']);
                                        ?>
                                    </dd>
                                </dl>
                                <dl class="col-6 col-lg-5">
                                    <dt><?php echo _('Transaction Date'); ?></dt>
                                    <dd><?php echo get_dt_format($other_data['rent_sw']['transaction_date'], $search_data['timezone']); ?></dd>
                                </dl>
                        <?php endif; ?>
                    </div>
                </div>
            </article>
        </div>
    <?php endif; ?>

</section>

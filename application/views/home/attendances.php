<div id="users" class="container">
    <div class="row">
        <?php include __DIR__ . DIRECTORY_SEPARATOR . 'aside.php'; ?>
        <div class="col-12 col-lg-8 col-xxl-9 user_sub">
            <?php if (empty($data['content'])): ?>
                <?php echo $Layout->element('home/not_found.php'); ?>
            <?php else: ?>
                <?php echo $Layout->element('home/nav'); ?>
                <div class="row">
                    <div class="col-12 col-xl-6">
                        <article class="card">
                            <h3 class="card-header"><?php echo _('Attendance list'); ?></h3>
                            <div class="card-body">
                                <input type="hidden" id="user_attendance_count"
                                       value="<?php echo $data['list']['total']; ?>"/>
                                <table id="user_attendance_list" class="table">
                                    <colgroup>
                                        <col>
                                        <col style="width:70px">
                                    </colgroup>
                                    <thead>
                                    <tr class="thead-default">
                                        <th><?php echo _('In Time'); ?></th>
                                        <th class="text-center"><?php echo _('Delete'); ?></th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <?php if ($data['list']['total']): ?>
                                        <?php foreach ($data['list']['list'] as $index => $value): ?>
                                            <tr>
                                                <td><?php echo get_dt_format($value['in_time'], $search_data['timezone'], 'Y' . _('Year') . ' n' . _('Month') . ' j' . _('Day') . ' H' . _('Hour') . ' i' . _('Minute')); ?></td>
                                                <td class="text-center">
                                                    <?php echo anchor('/entrances/delete/' . $value['id'], _('Delete'), array('class' => 'btn btn-danger btn-delete-confirm')); ?>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <tr>
                                            <td colspan="2"><?php echo _('No Data'); ?></td>
                                        </tr>
                                    <?php endif; ?>
                                    </tbody>
                                </table>
                                <div class="sl_pagination"></div>
                            </div>
                        </article>
                    </div>
                    <div class="col-12 col-xl-6">
                        <article class="card">
                            <h3 class="card-header"><?php echo _('Select attendance date'); ?>
                                / <?php echo _('Watch attendance'); ?></h3>
                            <div id="attendance_calendar" data-date-end-date="0d"></div>
                        </article>
                        <article class="card">
                            <h3 class="card-header"><?php echo _('Attendance Add'); ?></h3>
                            <?php echo form_open('entrances/add', array('id' => 'user_attendance_form', 'class' => 'card-body'), array('user_id' => $data['content']['id'])); ?>
                            <form id="" action="" class="card-body" method="post">
                                <div class="row">
                                    <div class="col-12">
                                        <div class="form-group">
                                            <div class="input-group">
                                                <input type="text" name="date" id="user_attendance_date"
                                                       value="<?php echo set_value('date', $search_data['date']); ?>"
                                                       class="form-control"/>
                                                <span class="input-group-btn">
                    <input class="btn btn-primary" type="submit" value="<?php echo _('Attendance'); ?>"/>
                  </span>
                                            </div>
                                        </div>
                                        <p class="small">
                                            * <?php echo _('Select a date for registration from the calendar above'); ?>
                                        </p>
                                    </div>
                                </div>
                                <?php echo form_close(); ?>
                        </article>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>
<script> var active_dates = ['2017-12-01']; </script>

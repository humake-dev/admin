<?php
$b_param = array();

if (isset($search_data['start_date'])) {
    $b_param['start_date'] = $search_data['start_date'];
}

if (isset($search_data['end_date'])) {
    $b_param['end_date'] = $search_data['end_date'];
}

if ($this->input->get('page')) {
    $b_param['page'] = $this->input->get('page');
    if ($b_param['page'] > 1) {
        $prev_page = $b_param['page'] - 1;
    }
    $next_page = $b_param['page'] + 1;
} else {
    $next_page = 2;
}

if (!empty($prev_page)) {
    $b_param['page'] = $prev_page;
    $prev_param = '?' . http_build_query($b_param);
}

$b_param['page'] = $next_page;
$next_param = '?' . http_build_query($b_param);

?>
<div id="messages" class="container">
    <div class="row">
        <div class="col-12">
            <div class="row">
                <nav class="col-12 sub_nav">
                    <ul class="nav nav-pills">
                        <li><?php echo anchor('message-points/index', _('SMS Point'), array('class' => 'nav-link')); ?></li>
                        <li><?php echo anchor('message-analyses/index', _('SMS By Branch'), array('class' => 'nav-link')); ?></li>
                        <li><?php echo anchor('message-analyses/current', _('Show By API'), array('class' => 'nav-link active')); ?></li>
                    </ul>
                </nav>
            </div>
            <div class="row">
                <div class="col-12">
                    <?php echo form_open('', array('method' => 'get', 'class' => 'search_form card')); ?>
                    <div class="card-body">
                        <div id="default_period_form" class="col-12 form-group">
                            <label for="start_date"><?php echo _('Sended At'); ?></label>
                            <div class="form-row">
                                <div class="col-6">
                                    <div class="input-group-prepend input-daterange">
                                        <?php
                                        $value_start_date = set_value('start_date');

                                        if (empty($value_start_date)) {
                                            if (isset($search_data['display_start_date'])) {
                                                $value_start_date = $search_data['display_start_date'];
                                            }
                                        }

                                        echo form_input(array('name' => 'start_date', 'value' => $value_start_date, 'id' => 'a_start_date', 'class' => 'form-control datepicker'));
                                        ?>
                                        <div class="input-group-text">~</div>
                                        <?php
                                        $value_end_date = set_value('end_date');

                                        if (empty($value_end_date)) {
                                            if (isset($search_data['display_end_date'])) {
                                                $value_end_date = $search_data['display_end_date'];
                                            }
                                        }

                                        echo form_input(array('name' => 'end_date', 'value' => $value_end_date, 'id' => 'a_end_date', 'class' => 'form-control datepicker'));
                                        ?>
                                    </div>
                                </div>
                                <div class="col-6">
                                </div>
                            </div>
                        </div>
                        <div class="col-12 form-group">
                            <?php echo form_submit('', _('Search'), array('class' => 'btn btn-primary')); ?>
                        </div>
                    </div>
                    <?php echo form_close(); ?>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-12">
            <h2 class="float-left"><?php echo _('Message Send List'); ?></h2>
            <div class="float-right">
            </div>
        </div>
        <div class="col-12">
            <table id="send_list" class="table table-striped table-hover">
                <colgroup>
                    <col/>
                    <col/>
                    <col/>
                    <col/>
                    <col/>
                    <col/>
                    <col/>
                </colgroup>
                <thead class="thead-default">
                <tr>
                    <th><?php echo _('Type'); ?></th>
                    <th><?php echo _('Sender'); ?></th>
                    <th><?php echo _('Content'); ?></th>
                    <th><?php echo _('Send SMS count'); ?>
                    <th><?php echo _('send Fail SMS count'); ?>
                    <th><?php echo _('Sended At'); ?></th>
                </tr>
                </thead>
                <tbody>
                <?php if ($data['total']): ?>
                    <?php foreach ($data['list'] as $index => $value): ?>
                        <tr>
                            <td><?php echo display_message_type($value->type); ?></td>
                            <td><?php echo $value->sender; ?></td>
                            <td><?php echo $value->msg; ?></td>
                            <td><?php echo $value->sms_count; ?></td>
                            <td>
                                <span<?php if($value->fail_count>0):?> class="text-danger"<?php endif ?>>
                                <?php echo $value->fail_count; ?>
                                </span>
                            </td>
                            <td><?php echo get_dt_format($value->reg_date, $search_data['timezone']); ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="6" style="text-align:center"><?php echo _('No Data'); ?></td>
                    </tr>
                <?php endif; ?>
                </tbody>
            </table>
            <?php if (!empty($prev_param)): ?>
                <?php echo anchor('message-analyses/current' . $prev_param, _('Prev Page')); ?>
            <?php endif; ?>
            <?php if (!empty($data['next_page'])): ?>
                <?php echo anchor('message-analyses/current' . $next_param, _('Next Page')); ?>
            <?php endif; ?>
        </div>
    </div>
</div>
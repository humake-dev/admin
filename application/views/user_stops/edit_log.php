<div class="container">
    <div class="row">
        <div class="col-12">
            <?php echo form_open('', array('id' => 'order_stop_form'), array('user_id' => $data['content']['user_id'])); ?>
            <input type="hidden" id="u_today" value="<?php echo $search_data['date']; ?>"/>
            <div class="row">
                <article class="col-12">
                    <h3><?php echo _('User Info'); ?></h3>
                    <div class="card">
                        <div class="card-body">
                            <div class="form-row">
                                <div class="col-12 col-md-6 col-lg-4 form-group">
                                    <label for="u_username"><?php echo _('User Name'); ?></label>
                                    <p><?php

                                        echo form_input(array(
                                            'id' => 'o_username',
                                            'value' => $data['content']['name'],
                                            'class' => 'form-control-plaintext',
                                        ));
                                        ?></p>
                                </div>
                                <?php if (!empty($common_data['branch']['use_access_card'])): ?>
                                    <div class="col-12 col-md-6 col-lg-4 form-group">
                                        <label for="u_card_no"><?php echo _('Access Card No'); ?></label>
                                        <p><?php

                                            echo form_input(array(
                                                'id' => 'o_card_no',
                                                'value' => get_card_no($data['content']['card_no'], false),
                                                'class' => 'form-control-plaintext',
                                            ));
                                            ?></p>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </article>

                <article class="col-12">
                    <h3><?php echo _('Stop Info'); ?></h3>
                    <div class="card">
                        <div class="card-body">
                            <div class="form-group">
                                <label for="us_request_date"><?php echo _('Request Date'); ?></label>
                                <div id="us_request_date" style="width:250px">
                                    <div class="input-group-prepend date">
                                        <?php
                                        echo form_input(array(
                                            'id' => 'us_request_date',
                                            'name' => 'request_date',
                                            'value' => $data['content']['request_date'],
                                            'class' => 'form-control datepicker',
                                        ));
                                        ?>
                                        <label for="us_request_date" class="input-group-text">
                                            <span class="material-icons">date_range</span>
                                        </label>
                                    </div>
                                </div>
                            </div>


                            <div class="form-row">
                                <div class="col-12 col-md-6 col-lg-4 form-group">
                                    <label for="u_resume_date"><?php echo _('Stop Start Date'); ?></label>
                                    <p><?php

                                        echo form_input(array(
                                            'type' => 'hidden',
                                            'id' => 'u_stop_start_date',
                                            'name' => 'stop_start_date',
                                            'value' => $data['content']['stop_start_date'],
                                        ));

                                        echo form_input(array(
                                            'value' => get_dt_format($data['content']['stop_start_date']),
                                            'class' => 'form-control-plaintext',
                                        ));
                                        ?></p>
                                </div>

                                <div class="col-12 col-md-6 col-lg-4 form-group">
                                    <label for="o_stop_day"><?php echo _('Stop Days'); ?></label>
                                    <p>
          <span id="o_stop_day">
            <?php if (empty($data['content']['stop_end_date'])): ?>
            <?php echo _('Not Set'); ?></span><span id="o_stop_day_day"
                                                    style="display:none"><?php echo _('Day'); ?></span>
                                        <?php else: ?>
                                            <?php echo $data['content']['stop_day_count']; ?></span><span
                                                    id="o_stop_day_day"><?php echo _('Day'); ?></span>
                                        <?php endif; ?>
                                    </p>
                                </div>

                                <div class="col-12 col-md-6 col-lg-4 form-group">
                                    <label for="u_stop_end_date"><?php echo _('Stop End Date'); ?></label>
                                    <div id="us_end_date" style="width:250px">
                                    <div class="input-group-prepend date">
                                        <?php

                                        if (empty($data['content']['stop_end_date'])) {
                                            $default_stop_end_date = null;
                                        } else {
                                            $default_stop_end_date = $data['content']['stop_end_date'];
                                        }

                                        $stop_end_date = set_value('stop_end_date', $default_stop_end_date);

                                        echo form_input(array(
                                            'id' => 'u_stop_end_date',
                                            'name' => 'stop_end_date',
                                            'value' => $stop_end_date,
                                            'class' => 'form-control',
                                        ));
                                        ?>
                                        <label for="us_end_date" class="input-group-text">
                                            <span class="material-icons">date_range</span>
                                        </label>
                                    </div>
                                    </div>
                                    <div>
                                        <?php
                                        if (empty($default_stop_end_date)) {
                                            $default_stop_end_date_not_set = 1;
                                            $u_default_stop_end_date = $search_data['today'];
                                        } else {
                                            $default_stop_end_date_not_set = 0;
                                            $u_default_stop_end_date = $stop_end_date;
                                        }

                                        echo form_input(array(
                                            'type' => 'hidden',
                                            'id' => 'u_default_stop_end_date',
                                            'value' => $u_default_stop_end_date,
                                        ));

                                        ?>
                                    </div>
                                </div>


                            </div>
                        </div>
                    </div>
                </article>
                <article class="col-12">
                    <h3><?php echo '적용되었던 주문' ?></h3>
                    <div class="card">
                        <div class="card-body">
                            <div class="form-row">
                                <div class="col-12 form-group">
                                    <table id="order_stop_table" class="table table-striped table-hover">
                                        <colgroup>
                                            <col>
                                            <col>
                                            <col>
                                            <col>
                                            <col>
                                            <col>
                                            <col>                                            
                                        </colgroup>
                                        <thead class="thead-default">
                                        <tr>
                                          <th><?php echo _('Product'); ?></th>
                                          <th><?php echo _('Stop Start Date'); ?></th>
                                          <th><?php echo _('Stop End Date'); ?></th>
                                          <th><?php echo _('Stop Days'); ?></th>
                                          <th><?php echo _('Origin End Date'); ?></th>
                                          <th><?php echo _('Change End Date'); ?></th>
                                          </tr>
                                        <thead>
                                        <tbody>
                                        <?php if (empty($data['stopped_orders']['total'])): ?>
                                            <tr>
                                                <td colspan="4">해당 주문이 없습니다. 뭔가 문제가 있는것으로 보입니다.</td>
                                            </tr>
                                        <?php else: ?>
                                            <?php foreach ($data['stopped_orders']['list'] as $index => $value): ?>
                                                <tr>
                                                    <td><?php echo $value['product_name'] ?></td>
                                                    <td><?php echo get_dt_format($value['stop_start_date']) ?></td>
                                                    <td><?php echo get_dt_format($value['stop_end_date']) ?></td>
                                                    <td><span class="o_stop_day"><?php echo $value['stop_day_count'] ?></span><?php echo _('Day') ?></td>
                                                    <td>
                                                        <?php echo get_dt_format($value['origin_end_date']) ?>
                                                        <input type="hidden" class="origin_start_date" value="<?php echo $value['origin_end_date'] ?>">
                                                    </td>
                                                    <td><span class="change_end_date"><?php echo get_dt_format($value['change_end_date']) ?></span></td>
                                                </tr>
                                            <?php endforeach ?>
                                        <?php endif ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </article>

                <article class="col-12">
                    <h3><?php echo _('Stop Memo'); ?></h3>
                    <div class="card">
                        <div class="card-body">
                            <div class="form-row">
                                <div class="col-12 form-group">
                                    <?php
                                    if (isset($data['content']['content'])) {
                                        $default_memo = $data['content']['content'];
                                    } else {
                                        $default_memo = '';
                                    }

                                    $memo_value = set_value('content', $default_memo);

                                    $memo_attr = array(
                                        'name' => 'content',
                                        'id' => 'o_memo',
                                        'value' => $memo_value,
                                        'rows' => 3,
                                        'class' => 'form-control',
                                    );
                                    echo form_textarea($memo_attr);

                                    ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </article>


            </div>

            <?php

echo '<script> var today="'.$search_data['today'].'";';

if (!empty($data['user_stops']['total']) or !empty($data['user_stop_schedules']['total'])) {
    echo 'var disable_date=[';

    if (!empty($data['user_stops']['total'])) {
        foreach ($data['user_stops']['list'] as $user_stop) {
            if($data['content']['id']==$user_stop['id']) {
                continue;
              }

            $start_date_do = new Datetime($user_stop['stop_start_date']);
            $end_date_do = new Datetime($user_stop['stop_end_date']);
            while ($start_date_do <= $end_date_do) {
                echo '"'.$start_date_do->format('Y-m-d').'",';
                $start_date_do->modify('+1 Day');
            }
        }
    }

    if (!empty($data['user_stop_schedules']['total'])) {
        foreach ($data['user_stop_schedules']['list'] as $user_stop_schedule) {
            if($data['content']['id']==$user_stop_schedule['user_stop_id']) {
                continue;
              }

            $start_date_schedule_do = new Datetime($user_stop_schedule['stop_start_date']);
            $end_date_schedule_do = new Datetime($user_stop_schedule['stop_end_date']);
            while ($start_date_schedule_do <= $end_date_schedule_do) {
                echo '"'.$start_date_schedule_do->format('Y-m-d').'",';
                $start_date_schedule_do->modify('+1 Day');
            }
        }
    }

    echo ' ];';
} else {
    echo 'var disable_date=[]';
}

echo '</script>';
?>

            <?php echo form_submit('', _('Edit'), array('class' => 'btn btn-primary btn-block btn-lg')); ?>
            <?php echo form_close(); ?>
        </div>
    </div>
</div>

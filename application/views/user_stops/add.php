<?php
if ($this->input->get('schedule')) {
    $title = _('Stop Order Schedule');
} else {
    $title = _('Stop Order');
}

if(!empty($data['enroll_content'])) {
    $enroll_start_date_obj = new DateTime($data['enroll_content']['start_date'], $search_data['timezone']);
    $enroll_start_date = $enroll_start_date_obj->format('Y-m-d');
    $today_obj = new DateTime($search_data['today'], $search_data['timezone']);

    if ($enroll_start_date_obj > $today_obj) {
        $default_start_start_date = $enroll_start_date;
        $available_today = false;
    } else {
        $default_start_start_date = $search_data['today'];
        $available_today = true;
    }
}

if(!empty($data['request_content'])) {
    $enroll_start_date_obj = new DateTime($data['request_content']['stop_start_date'], $search_data['timezone']);
    $default_stop_start_date = $enroll_start_date_obj->format('Y-m-d');

    $enroll_end_date_obj = new DateTime($data['request_content']['stop_end_date'], $search_data['timezone']);
    $default_stop_end_date = $enroll_end_date_obj->format('Y-m-d');

    $default_memo = $data['request_content']['description'];
}

if(empty($this->input->get_post('return_url'))) {
    if(empty($data['return_url'])) {
        $return_url = '/user_stop_requests';
    } else {
        $return_url=$data['return_url'];
    }
} else {
    $return_url = $this->input->get_post('return_url');
}

?>
<div class="container">
    <div class="row">
        <div class="col-12">
            <?php echo form_open('', array('id' => 'order_stop_form'), array('return_url'=>$return_url,'user_id' => $data['content']['id'], 'order_id' => $this->input->get_post('order_id'))); ?>
            <input type="hidden" id="o_today" value="<?php echo $search_data['date']; ?>"/>
            <div class="row">
                <article class="col-12">
                    <h3><?php echo _('User Info'); ?></h3>
                    <div class="card">
                        <div class="card-body">
                            <div class="form-row">
                                <div class="col-12 col-md-6 col-lg-4 form-group">
                                    <label for="o_username"><?php echo _('User Name'); ?></label>
                                    <p><?php

                                        echo form_input(array(
                                            'id' => 'o_username',
                                            'value' => $data['content']['name'],
                                            'class' => 'form-control-plaintext',
                                        ));
                                        ?></p>
                                </div>
                                <?php if(!empty($data['enroll_content'])): ?>
                                <div class="col-12 col-md-6 col-lg-4 form-group">
                                    <label for="o_available_stop_date"><?php echo _('Start Date'); ?></label>
                                    <p><?php

                                        echo form_input(array(
                                            'id' => 'o_available_stop_date',
                                            'value' => get_dt_format($enroll_start_date, $search_data['timezone']),
                                            'class' => 'form-control-plaintext',
                                        ));
                                        ?></p>
                                </div>
                                <?php endif ?>
                                <?php if (!empty($common_data['branch']['use_access_card'])): ?>
                                    <div class="col-12 col-md-6 col-lg-4 form-group">
                                        <label for="o_card_no"><?php echo _('Access Card No'); ?></label>
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


                <?php if (!empty($data['order_list']['total'])): ?>
                    <?php
                        echo form_input(array(
                                                'id' => 'request_id',
                                                'name' => 'request_id',
                                                'value' => set_value('request_id',$this->input->get_post('request_id')),
                                                'type'=>'hidden'
                                            ));

                                            ?>
                    <article class="col-12">
                    <div class="card">
                    <?php if ($data['order_list']['total']==1): ?>
                        <?php
                        echo form_input(array(
                                                'id' => 'order_id',
                                                'name'=>'order_id',
                                                'value' => set_value('order_id',$data['order_list']['list'][0]['order_id']),
                                                'type'=>'hidden'
                                            ));
                                            ?>
                    <?php else: ?>
                        <?php include __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'user_stop_requests' . DIRECTORY_SEPARATOR . 'select_list.php'; ?>
                    <?php endif ?>
                    </div>
                    </article>
                <?php endif; ?>

                <article class="col-12">
                    <h3><?php echo _('Stop Info'); ?></h3>
                    <div class="card">
                        <div class="card-body">
                            <div class="form-row">
                                <div class="col-12 form-group">
                                    <label for="us_request_date"><?php echo _('Request Date'); ?></label>
                                    <div id="today_display" class="form-check">
                                        <input type="checkbox" id="is_today_onetime" name="today" value="1"
                                               checked="checked" class="form-check-input">
                                        <label class="form-check-label"
                                               for="is_today_onetime"><?php echo _('Today'); ?></label>
                                    </div>
                                    <div id="us_request_date" style="display:none;width:160px">
                                        <div class="input-group-prepend date">
                                            <?php
                                            echo form_input(array(
                                                'id' => 'us_request_date',
                                                'name' => 'request_date',
                                                'value' => $search_data['today'],
                                                'class' => 'form-control datepicker',
                                            ));
                                            ?>
                                            <label for="us_request_date" class="input-group-text">
                                                <span class="material-icons">date_range</span>
                                            </label>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-12 col-md-6 col-lg-4 form-group">
                                    <label for="us_stop_start_date"><?php echo _('Stop Start Date'); ?></label>
                                    <div>
                                        <?php

                                        if (isset($data['content']['stop_start_date'])) {
                                            $default_stop_start_date = $data['content']['stop_start_date'];
                                        } else {
                                            if(empty($default_stop_start_date)) {
                                                $default_stop_start_date = '';
                                            }
                                        }

                                        $value_stop_start_date = set_value('stop_start_date', $default_stop_start_date);

                                        echo form_input(array(
                                            'id' => 'us_stop_start_date',
                                            'name' => 'stop_start_date',
                                            'value' => $value_stop_start_date,
                                            'class' => 'form-control user_change_datepicker',
                                        ));
                                        ?>
                                    </div>
                                </div>

                                <div class="col-12 col-md-6 col-lg-4 form-group">
                                    <label for="us_stop_end_date"><?php echo _('Stop End Date'); ?></label>
                                    <div>
                                        <?php

                                        if (isset($data['content']['stop_end_date'])) {
                                            $default_stop_end_date = $data['content']['stop_end_date'];
                                        } else {
                                            if(empty($default_stop_end_date)) {
                                                $default_stop_end_date = '';
                                            }
                                        }

                                        $value_stop_end_date = set_value('stop_end_date', $default_stop_end_date);

                                        echo form_input(array(
                                            'id' => 'us_stop_end_date',
                                            'name' => 'stop_end_date',
                                            'value' => $value_stop_end_date,
                                            'class' => 'form-control user_change_datepicker',
                                        ));
                                        ?>
                                    </div>
                                </div>
                                <div class="col-12 col-md-6 col-lg-4 form-group">
                                    <label for="us_stop_day_count_value"><?php echo _('Stop Days'); ?></label>
                                    <div>
                                        <?php

                                        $stop_days_value = _('Not Set');

                                        if (!empty($default_stop_start_date) and empty(!$value_stop_end_date)) {
                                            $start_date_obj = new DateTime($default_stop_start_date, $search_data['timezone']);
                                            $end_date_obj = new DateTime($value_stop_end_date, $search_data['timezone']);

                                            $date_diff = $start_date_obj->diff($end_date_obj);

                                            $r = $date_diff->format('%R');
                                            if ($r != '-') {
                                                $stop_days_value = $date_diff->format('%R%a') + 1;
                                            }
                                        }

                                        echo form_input(array(
                                            'id' => 'stop_day_count_value',
                                            'read_only' => true,
                                            'value' => $stop_days_value,
                                            'class' => 'form-control form-control-plaintext',
                                        ));
                                        ?>
                                    </div>
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
                                        if(empty($default_memo)) {
                                            $default_memo = '';
                                        }
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
            echo '<script> var today="' . $search_data['today'] . '";';
            
            if(empty($data['enroll_content']['start_date'])):
                echo 'var available_stop_start_date=null;';
            else:
                echo 'var available_stop_start_date="' . $data['enroll_content']['start_date'] . '";';
            endif;

            if (!empty($data['user_stops']['total']) or !empty($data['user_stop_schedules']['total'])) {
                echo 'var disable_date=[';
            
            if($_SESSION['role_id']>2): 
                if (!empty($data['user_stops']['total'])) {
                    foreach ($data['user_stops']['list'] as $user_stop) {
                        $start_date_do = new Datetime($user_stop['stop_start_date']);
                        $end_date_do = new Datetime($user_stop['stop_end_date']);
                        while ($start_date_do <= $end_date_do) {
                            echo '"' . $start_date_do->format('Y-m-d') . '",';
                            $start_date_do->modify('+1 Day');
                        }
                    }
                }
            endif;


                if (!empty($data['user_stop_schedules']['total'])) {
                    foreach ($data['user_stop_schedules']['list'] as $user_stop_schedule) {
                        $start_date_schedule_do = new Datetime($user_stop_schedule['stop_start_date']);
                        $end_date_schedule_do = new Datetime($user_stop_schedule['stop_end_date']);
                        while ($start_date_schedule_do <= $end_date_schedule_do) {
                            echo '"' . $start_date_schedule_do->format('Y-m-d') . '",';
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


            <?php echo form_submit('', $title, array('class' => 'btn btn-primary btn-block btn-lg')); ?>
            <?php echo form_close(); ?>
        </div>
    </div>
</div>

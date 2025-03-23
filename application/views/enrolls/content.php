<article class="row">
    <h3 class="col-12"><?php echo _('Course Info'); ?></h3>
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <div class="form-row">
                    <div class="col-12 col-lg-4 form-group" style="margin-bottom:15px">
                        <p>
                            <?php echo _('Course'); ?> : <?php echo $data['content']['product_name']; ?>
                        </p>
                    </div>
                    <div class="col-12 col-lg-4 form-group">
                        <p>
                            <?php echo _('Course Category'); ?>
                            : <?php echo $data['content']['product_category_name']; ?>
                        </p>
                    </div>
                    <div class="col-12 col-lg-4 form-group">
                        <p>
                            <?php echo _('lesson_dayofweek'); ?>
                            : <?php echo dowtostr($data['content']['lesson_dayofweek']); ?>
                        </p>
                    </div>
                    <div class="col-12 col-lg-4 form-group">
                        <p>
                            <?php echo _('Quota'); ?> :
                            <?php if (empty($data['content']['quota'])): ?>
                                <?php echo _('Unlimit') ?>
                            <?php else: ?>
                                <?php echo $data['content']['quota']; ?>
                            <?php endif ?>
                        </p>
                    </div>
                    <div class="col-12 col-lg-4 form-group">
                        <p>
                            <?php echo _('Price'); ?> : <?php echo number_format($data['content']['price']); ?><?php echo _('Currency'); ?>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</article>
<div class="form-row">
    <div class="col-6">
        <article class="row">
            <h3 class="col-12"><?php echo _('Enroll Default Info'); ?></h3>
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <div class="form-row">
                            <div class="col-12 form-group">
                                <p>
                                    <?php echo _('Transaction Date'); ?>
                                    : <?php echo get_dt_format($data['content']['transaction_date'], $search_data['timezone']); ?>
                                </p>
                            </div>
                            <div class="col-12 form-group">
                                <?php

                                switch ($data['content']['lesson_type']) {
                                    case 1: // 기간제
                                        if($data['content']['lesson_period_unit']=='D') {
                                            $count_d = _('Day');
                                            $count_insert_quantity=get_period($data['content']['start_date'],$data['content']['end_date'], $search_data['timezone'],true,true);
                                        } else {
                                            $count_d = _('Period Month');
                                            $count_insert_quantity=$data['content']['insert_quantity'];
                                        }
                                        break;
                                    case 2: // 횟수제
                                    case 4: // PT
                                    case 5: // GX
                                        $count_d = '회'; // 단위수량 X 구입갯수
                                        break;
                                    case 3: // 쿠폰제
                                        $count_d = '개'; // 단위수량 X 구입갯수
                                        break;
                                }

                                if ($data['content']['lesson_type'] == 1) {

                                    if(empty($data['content']['insert_quantity'])) {
                                        $d1 = new DateTime($data['content']['start_date'], $search_data['timezone']);
                                        $d2 = new DateTime($data['content']['end_date'], $search_data['timezone']);
                                        $d2->modify('+1 day');
                                        $count = $d1->diff($d2)->m + ($d1->diff($d2)->y * 12);

                                        if ($count < 1) {
                                            $count = 1;
                                        }
                                    } else {
                                        $count = $data['content']['insert_quantity'];
                                    }
                                } else {
                                    $count = $data['content']['insert_quantity'];
                                }

                                echo form_input(array(
                                    'type' => 'hidden',
                                    'id' => 'e_content_insert_quantity',
                                    'value' => $count,
                                ));

                                if(isset($data['re_order_no'])) {
                                    echo form_input(array(
                                        'type' => 'hidden',
                                        'id' => 'e_content_re_order_no',
                                        'value' =>$data['re_order_no']
                                    ));
                                }

                                ?>
                                <p><?php echo get_lesson_counter($data['content']['lesson_type']); ?>
                                    : <?php echo $count . $count_d; ?></p>
                            </div>
                            <div class="col-12 form-group">
                                <?php
                                $user_tainer_value = _('Not Inserted');

                                if ($data['content']['trainer_name']) {
                                    $user_tainer_value = $data['content']['trainer_name'];
                                }
                                ?>
                                <p><?php echo _('User Trainer'); ?> : <?php echo $user_tainer_value; ?></p>
                            </div>
                            <div class="col-12 form-group">
                                <p><?php echo _('Start Date'); ?>
                                    : <?php echo get_dt_format($data['content']['start_date'], $search_data['timezone']); ?></p>
                                <?php
                                echo form_input(array(
                                    'type' => 'hidden',
                                    'id' => 'e_content_start_date',
                                    'value' => $data['content']['start_date'],
                                ));
                                ?>
                            </div>
                            <div class="col-12 form-group">
                                <p>
                                    <?php echo _('End Date'); ?>
                                    : <?php echo get_dt_format($data['content']['end_date'], $search_data['timezone']); ?></p>
                                <?php
                                echo form_input(array(
                                    'type' => 'hidden',
                                    'id' => 'e_content_end_date',
                                    'value' => $data['content']['end_date'],
                                ));
                                ?>
                            </div>
                            <?php if ($this->input->get('rent')): ?>
                                <div class="col-12 form-group">
                                    <input type="hidden" id="sync-insert-quantity"
                                           value="<?php echo $count_insert_quantity; ?>">
                                    <input type="hidden" id="sync-start-date"
                                           value="<?php echo $data['content']['start_date']; ?>">
                                    <input type="hidden" id="sync-end-date"
                                           value="<?php echo $data['content']['end_date']; ?>">
                                    <input type="button" id="sync-content-button" class="btn btn-secondary"
                                           value="<?php echo _('Sync Period'); ?>">
                                </div>
                            <?php endif; ?>
                            <?php if ($this->input->get('re-enroll')): ?>
                                <div class="col-12 form-group">
                                    <?php

                                    $extend_date_obj = new DateTime($data['content']['end_date'], $search_data['timezone']);
                                    $extend_date_obj->modify('+1 Days');
                                    $extend_start_date = $extend_date_obj->format('Y-m-d');

                                    $extend_date_obj->modify('+' . $data['content']['insert_quantity'] . ' Month');
                                    $extend_end_date = $extend_date_obj->format('Y-m-d');
                                    ?>
                                    <input type="hidden" id="extend-course-id"
                                           value="<?php echo $data['content']['course_id']; ?>">
                                    <input type="hidden" id="extend-trainer-id"
                                           value="<?php echo $data['content']['trainer_id']; ?>">
                                    <input type="hidden" id="extend-insert-quantity"
                                           value="<?php echo $data['content']['insert_quantity']; ?>">
                                    <input type="hidden" id="extend-start-date"
                                           value="<?php echo $extend_start_date; ?>">
                                    <input type="hidden" id="extend-end-date" value="<?php echo $extend_end_date; ?>">
                                    <?php if(!empty($data['content']['price'])): ?>
                                    <input type="button" id="re-enroll-button" class="btn btn-secondary" value="<?php echo _('Re Enroll'); ?>">
                                    <?php endif ?>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
    </div>
    <div class="col-6">
        <article class="row">
            <h3 class="col-12"><?php echo _('Enroll Fee Info'); ?></h3>
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <div class="form-row">
                            <div class="col-12 col-lg-8 form-group">
                                <p><?php echo _('Original Price'); ?>
                                    : <?php echo number_format($data['content']['original_price']); ?><?php echo _('Currency'); ?>
                                <p>
                            </div>
                            <div class="col-12 col-lg-6 form-group">
                                <p><?php echo _('DC Rate'); ?>
                                    : <?php echo number_format($data['content']['dc_rate']); ?>%
                                <p>
                            </div>
                            <div class="col-12 col-lg-6 form-group">
                                <p><?php echo _('DC Point'); ?>
                                    : <?php echo number_format($data['content']['dc_price']); ?><p>
                            </div>
                            <div class="col-12 col-lg-8 form-group">
                                <p><?php echo _('Sell Price'); ?>
                                    : <?php echo number_format($data['content']['price']); ?><?php echo _('Currency'); ?>
                                <p>
                            </div>
                            <div class="col-12 col-lg-6 form-group">
                                <p><?php echo _('Cash'); ?>
                                    : <?php echo number_format($data['content']['cash']); ?><?php echo _('Currency'); ?>
                                <p>
                            </div>
                            <div class="col-12 col-lg-6 form-group">
                                <p><?php echo _('Credit'); ?>
                                    : <?php echo number_format($data['content']['credit']); ?><?php echo _('Currency'); ?>
                                <p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </article>
    </div>
</div>
<article class="row">
    <h3 class="col-12"><?php echo _('Memo'); ?></h3>
    <?php

    if (!empty($data['content']['content'])) {
        $memo_value = $data['content']['content'];
    }
    ?>
    <div class="col-12">
        <div class="card member_info">
            <div class="card-body">
                <?php if (isset($data['user_id'])): ?>
                    <input type="hidden" name="user_id" value="<?php echo set_value('user_id', $data['user_id']); ?>"/>
                <?php endif; ?>
                <div class="form-row">
                    <div class="col-12 form-group">
                        <?php if (isset($memo_value)): ?>
                            <?php echo nl2br($memo_value); ?>
                        <?php else: ?>
                            <?php echo _('Not Inserted'); ?>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</article>

<article class="col-12">
    <div class="row">
        <h3 class="col-12 col-lg-6"><?php echo _('Enroll Info'); ?></h3>
        <div class="col-12 col-lg-6 text-right">
            <?php if ($this->session->userdata('branch_id')): ?>
                <?php if ($this->Acl->has_permission('enrolls', 'write')): ?>
                    <?php echo anchor('enrolls/add?user_id=' . $data['content']['id'], '<i class="material-icons">add</i>', array('class' => 'more2')); ?>
                <?php endif; ?>
            <?php endif; ?>
            <a href="/home/enrolls/<?php echo $data['content']['id']; ?><?php echo $params; ?>"
               title="<?php echo _('More Enroll'); ?>" class="more"><i class="material-icons">redo</i></a>
        </div>
    </div>
    <?php if (isset($other_data)): ?>
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                <tr class="thead-default">
                    <th><?php echo _('Transaction Date'); ?></th>
                    <th><?php echo _('Status'); ?></th>
                    <th><?php echo _('Lesson'); ?></th>
                    <th><?php echo _('Trainer'); ?></th>
                    <th><?php echo _('Start Date'); ?></th>
                    <th><?php echo _('End Date'); ?></th>
                    <th><?php echo _('Period'); ?></th>
                    <th><?php echo _('Sell Price'); ?></th>
                    <th><?php echo _('Payment'); ?></th>
                </tr>
                </thead>
                <tbody>
                <?php if (empty($other_data['enroll']['total'])): ?>
                    <tr>
                        <td class="text-center" colspan="9"><?php echo _('Not Inserted Enroll'); ?></td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($other_data['enroll']['list'] as $index => $value):

                        $status = '<span class="text-success">' . _('Using') . '</span>';

                        $remain_date = '-';
                        $remain_count = '-';

                        if ($value['stop_end_date'] and $value['change_end_date']) {
                            // $value['start_date']=$value['stop_end_date'];
                            $value['end_date'] = $value['change_end_date'];
                        }

                        $display_end_date = get_dt_format($value['end_date'], $search_data['timezone']);
                        $display_start_date = get_dt_format($value['start_date'], $search_data['timezone']);

                        $start_date_obj = new DateTime($value['start_date'], $search_data['timezone']);
                        $end_date_obj = new DateTime($value['end_date'], $search_data['timezone']);

                        if ($end_date_obj >= new DateTime($search_data['max_date'], $search_data['timezone'])) {
                            $remain_date = '-';
                            $display_end_date = _('Unlimit');
                        } else {
                            $current_date_obj = new DateTime($search_data['today'], $search_data['timezone']);

                            if ($current_date_obj >= $start_date_obj) {
                                if ($value['stop_end_date'] and $value['change_end_date']) {
                                    $remain_date = $value['day_count'] . _('Day');
                                } else {
                                    if ($current_date_obj <= $end_date_obj) {
                                        $interval_obj = $current_date_obj->diff($end_date_obj);
                                        $remain_date = $interval_obj->format('%a') . _('Day');
                                    } else {
                                        $remain_date = _('None');
                                        $status = '<span class="text-danger">' . _('Ended') . '</span>';
                                    }
                                }
                            } else {
                                $interval_obj = $start_date_obj->diff($end_date_obj);
                                $remain_date = $interval_obj->format('%a') . _('Day');
                                $status = '<span class="text-warning">' . _('Reservation') . '</span>';
                            }
                        }

                        if ($value['stopped']) {
                            $status = '<span class="text-warning">' . _('Stopped') . '</span>';
                        }

                        switch ($value['lesson_type']) {
                            case 1: // 기간제
                                $count_unit = null;
                                $count_unit = _('Period Month');
                                break;
                            case 3: // 쿠폰제
                                $count_unit = '개';
                                $remain_count = ($value['quantity'] - $value['use_quantity']) . $count_unit; // 단위수량 X 구입갯수
                                break;
                            default: // GX
                                $count_unit = '회';
                                $remain_count = ($value['quantity'] - $value['use_quantity']) . $count_unit; // 단위수량 X 구입갯수
                                break;
                        }
                        ?>
                        <tr>
                            <td>
                            <?php
        
        $transaction_date=$value['transaction_date'];

        if(empty($transaction_date)) {
          $transaction_date=$value['order_transaction_date'];
        }
        
        echo get_dt_format($transaction_date, $search_data['timezone']); 
        
        ?>
                            </td>
                            <td><?php echo $status; ?></td>
                            <td><?php echo $value['product_category_name']; ?>
                                / <?php echo $value['product_name']; ?></td>
                            <td>
                                <?php if (empty($value['trainer_name'])): ?>
                                    -
                                <?php else: ?>
                                    <?php echo $value['trainer_name']; ?>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if (empty($value['stopped'])): ?>
                                    <?php echo $display_start_date; ?>
                                <?php else: ?>
                                    <?php
                                    if (empty($value['change_start_date'])) {
                                        echo $display_start_date;
                                    } else {
                                        echo get_dt_format($value['change_start_date'], $search_data['timezone']);
                                    }

                                    ?>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if (empty($value['stopped'])): ?>
                                    <?php echo $display_end_date; ?>
                                <?php else: ?>
                                    <?php

                                    if ($value['stop_end_date'] and $value['change_end_date']) {
                                        echo get_dt_format($value['change_end_date'], $search_data['timezone']);
                                    } else {
                                        echo _('Not Set');
                                    }
                                    ?>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php echo $value['insert_quantity']; ?>
                                <?php echo get_lesson_unit($value['lesson_type'], $value['lesson_period_unit']); ?>
                            </td>
                            <td><?php echo number_format($value['price']); ?><?php echo _('Currency'); ?></td>
                            <td>
                                <?php if ($value['payment'] == 'Unpaid'): ?>
                                    <span class="text-danger"><?php echo number_format($value['payment']); ?><?php echo _('Currency'); ?></span>
                                <?php else: ?>
                                    <span class="text-success"><?php echo number_format($value['payment']); ?><?php echo _('Currency'); ?></span>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif ?>
                </tbody>
            </table>
        </div>
    <?php else: ?>
        -
    <?php endif; ?>
</article>

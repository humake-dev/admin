<div class="row">
    <div class="col-12 col-lg-6">
        <?php echo sprintf(_('There Are %d Order'), $data['total']); ?>
    </div>
    <div class="col-12 col-lg-6 text-right">
        <?php echo anchor('search-ys/export-excel'.$params, _('Export Excel'), ['class' => 'btn btn-secondary']); ?>
    </div>
    <div class="col-12">
        <table id="user_list" class="table table-bordered table-striped table-hover">
            <colgroup>
                <col>
                <col>
                <col>
                <col>
                <col>
                <col>
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
                <th><?php echo _('User'); ?></th>
                <th class="text-center"><?php echo _('Gender'); ?></th>
                <th><?php echo _('Phone'); ?></th>
                <th>
                    <?php if ($this->input->get('search_type') == 'field' and $this->input->get('search_field') == 'birthday'): ?>
                        <?php echo _('Birthday'); ?>
                    <?php else: ?>
                        <?php echo _('Transaction Date'); ?>
                    <?php endif; ?>
                </th>
                <th><?php echo _('Start Date'); ?></th>
                <th><?php echo _('End Date'); ?></th>
                <th class="text-center"><?php echo _('Payment'); ?></th>

                <?php if ($search_data['er_type'] == 'pt'): ?>
                    <th class="text-center">총횟수</th>
                    <th class="text-center">횟수별단가</th>
                    <th class="text-center">남은횟수</th>
                    <th class="text-center">남은금액</th>
                    <th class="text-center">사용횟수</th>
                <?php else: ?>
                    <th class="text-center"><?php echo _('Period Month'); ?></th>
                <th class="text-center">일단가</th>
                <th class="text-center">남은일수</th>
                <th class="text-center">남은금액</th>
                <th class="text-center">사용일수</th>
                <?php endif; ?>
                <th class="text-center">수익금</th>
            </tr>
            </thead>
            <tbody>
            <?php 
            if ($data['total']):
                foreach ($data['list'] as $index => $value):
                if ($value['lesson_type'] == 4):
                    $dd = $value['insert_quantity'];
                    $cur_dd = $value['insert_quantity']-$value['pt_use_quantity'];
                else:
                    $start_datetime_obj = new DateTime($value['start_date'], $search_data['timezone']);
                    $end_datetime_obj = new DateTime($value['end_date'], $search_data['timezone']);

                    if($this->input->get('reference_date')) {
                        $cur_datetime_obj = new DateTime($this->input->get('reference_date'), $search_data['timezone']);
                    } else {
                        $cur_datetime_obj = new DateTime('now', $search_data['timezone']);
                    }

                    $diff_obj = $start_datetime_obj->diff($end_datetime_obj);
                    $dd = $diff_obj->format('%a')+1;

                    if($start_datetime_obj>$cur_datetime_obj) {
                        $cur_dd=$dd;
                    } else {
                        if($end_datetime_obj<=$cur_datetime_obj) {
                            $cur_dd=0;
                        } else {                             
                            $cur_diff_obj = $cur_datetime_obj->diff($end_datetime_obj);
                            $cur_dd = $cur_diff_obj->format('%a')+1;
                        }
                    }

                    if(!empty($value['transfer_date'])) {
                        $transfer_datetime_obj = new DateTime($value['transfer_date'], $search_data['timezone']);
                        $origin_start_datetime_obj = new DateTime($value['origin_start_date'], $search_data['timezone']);

                        if($transfer_datetime_obj>$origin_start_datetime_obj) {
                            $diff_obj = $origin_start_datetime_obj->diff($transfer_datetime_obj);
                            $plus_dd = $diff_obj->format('%a')+1;

                            $dd+=$plus_dd;
                        }
                    }

                endif;
                    ?>
                    <tr>
                        <td>
                            <?php echo anchor('/view/'.$value['id'], $value['name']); ?>
                        </td>
                        <td class="text-center">
                            <?php

                            if (is_null($value['gender'])) {
                                echo '-';
                            } else {
                                if ($value['gender'] == 1) {
                                    echo _('Male');
                                }

                                if ($value['gender'] == 0) {
                                    echo _('Female');
                                }
                            }

                            ?>
                        </td>
                        <td class="phone"><?php echo get_hyphen_phone($value['phone']); ?></td>
                        <td>
                            <?php if ($this->input->get('search_type') == 'field' and $this->input->get('search_field') == 'birthday'): ?>
                                <?php echo get_dt_format($value['birthday'], $search_data['timezone']); ?>
                            <?php else: ?>
                                <?php echo get_dt_format($value['transaction_date'], $search_data['timezone']); ?>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php
                            
                            if(empty($value['origin_start_date'])) {
                                if (empty($value['change_start_date'])) {
                                    echo get_dt_format($value['start_date'], $search_data['timezone']);
                                } else {
                                    echo get_dt_format($value['change_start_date'], $search_data['timezone']);
                                }
                            } else {
                                echo get_dt_format($value['origin_start_date'], $search_data['timezone']);
                            }

                            ?>
                        </td>
                        <td>
                            <?php
                            if (empty($value['change_end_date'])) {
                                echo get_dt_format($value['end_date'], $search_data['timezone']);
                            } else {
                                echo get_dt_format($value['change_end_date'], $search_data['timezone']);
                            }
                            ?>
                        </td>
                        <td class="text-right"><?php if (empty($value['pay_total'])): ?>-<?php else: ?><?php echo number_format($value['pay_total']); ?><?php echo _('Currency'); ?><?php endif; ?></td>
                            <td class="text-right">
                                <?php if ($search_data['er_type'] == 'pt'): ?>
                                    <?php echo $value['insert_quantity']._('Count Time'); ?>
                                <?php else: ?>
                                    <?php echo $value['insert_quantity']._('Period Month'); ?>
                                <?php endif; ?>
                            </td>
                        <td class="text-right">
                            <?php
                            if (empty($value['pay_total'])):
                                $day_pay = 0;
                                echo '-';
                            else:
                                if (empty($dd)) {
                                    $day_pay = 0;
                                    echo '-';
                                } else {
                                    $day_pay = $value['pay_total'] / $dd;
                                    echo number_format($day_pay)._('Currency');
                                }
                            endif;
                            ?>
                        </td>
                        <td class="text-right">
                        <?php echo $cur_dd ?>
                            <?php if ($search_data['er_type'] == 'pt'): ?>
                            <?php echo _('Count Time'); ?>
                            <?php else: ?>
                            <?php echo _('Day'); ?>
                            <?php endif; ?>
                        </td>
                        <td class="text-right">
                            <?php if (empty($value['pay_total'])): ?>
                                -
                            <?php else: ?>
                            <?php
                            $left_pay = $cur_dd * $day_pay;
                            echo number_format($left_pay);

                            ?><?php echo _('Currency'); ?>
                            <?php endif; ?>
                        </td>
                        <td class="text-right">
                            <?php echo $dd - $cur_dd; ?>
                            <?php if ($search_data['er_type'] == 'pt'): ?>
                                <?php echo _('Count Time'); ?>
                            <?php else: ?>
                                <?php echo _('Day'); ?>
                            <?php endif; ?>
                        </td>
                        <td class="text-right">
                            <?php if (empty($value['pay_total'])): ?>
                            -
                            <?php else: ?>
                            <?php echo number_format($value['pay_total'] - $left_pay); ?>
                                <?php echo _('Currency'); ?>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="13" class="text-center"><?php echo _('No Data'); ?></td>
                </tr>
            <?php endif; ?>
            </tbody>
        </table>
        <?php echo $this->pagination->create_links(); ?>
        <ul class="users_input" style="display:none">
            <li><input type="hidden"/><span style="font-weight:bold"><?php echo _('None'); ?></span></li>
        </ul>
    </div>
</div>

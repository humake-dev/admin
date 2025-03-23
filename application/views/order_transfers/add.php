<div class="container">
    <div class="row">
        <div class="col-12">
            <?php echo form_open($data['form_url'], array('id' => 'order_transfer_form')); ?>
            <?php echo form_input(array('type' => 'hidden', 'id' => 'c_user_id', 'name' => 'recipient_id','value'=>$this->input->get_post('recipient_id'))); ?>
            <input type="hidden" id="text_sbf" value="<?php echo _('Select Branch First') ?>">
            <input type="hidden" id="product_type" name="product_type" value="<?php echo $data['type'] ?>">
            <?php if(!empty($data['return_url'])): ?>
            <input type="hidden" name="return_url" value="<?php echo $data['return_url'] ?>">
            <?php endif ?>
            <?php if($data['type']=='enroll'): ?>
            <input type="hidden" id="transfer_lesson_type" value="<?php echo $data['content']['lesson_type'] ?>">
            <?php endif ?>
            <div class="row">
                <article class="col-12">
                    <h3><?php echo _('Order Transfer Info'); ?></h3>
                    <div class="card">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-12 col-lg-6 form-group">
                                    <label><?php echo _('User Name'); ?></label>
                                    <p>
                                        <?php if (!empty($data['content']['user_name'])): ?>
                                            <?php echo $data['content']['user_name']; ?>
                                        <?php endif; ?>
                                    <p>
                                </div>
                                <div class="col-12 col-lg-6 form-group">
                                    <label><?php echo $data['product_name'] ?></label>
                                    <p>
                                        <?php if (!empty($data['content']['product_category_name'])): ?>
                                            <?php echo $data['content']['product_category_name']; ?>
                                            /
                                        <?php endif; ?>
                                        <?php echo $data['content']['product_name']; ?>
                                    <p>
                                </div>
                                <div class="col-12 col-lg-6 form-group">
                                    <label><?php echo _('Price'); ?> / <?php echo _('Payment'); ?></label>
                                    <p>
                                        <?php if (empty($data['content']['price'])): ?>
                                            <?php echo _('Free'); ?>
                                        <?php else: ?>
                                            <?php echo number_format($data['content']['price']); ?><?php echo _('Currency'); ?>
                                        <?php endif; ?>

                                        /

                                        <?php if (empty($data['content']['payment'])): ?>
                                            <?php echo _('Free'); ?>
                                        <?php else: ?>
                                            <?php echo number_format($data['content']['payment']); ?><?php echo _('Currency'); ?>
                                        <?php endif; ?>
                                    <p>
                                </div>
                                <div class="col-12 col-lg-6 form-group">
                                    <label><?php echo _('Period'); ?></label>
                                    <p><?php echo get_dt_format($data['content']['start_date'], $search_data['timezone']); ?>
                                        ~ <?php echo get_dt_format($data['content']['end_date'], $search_data['timezone']); ?></p>
                                </div>
                            </div>
                        </div>
                    </div>
                </article>
                <article class="col-12">
                    <h3><?php echo _('Transfer Info'); ?></h3>
                    <div class="card">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-12 col-md-6 col-lg-4 form-group">
                                    <label><input type="checkbox" id="o_other_branch" name="ther_branch" value="1" checked="checked"><?php echo _('Current Branch'); ?></label>
                                    <div class="to_other_branch" style="display:none">
                                        <label for="cb_branch"><?php echo _('Branch'); ?></label>
                                        <?php

                                        $select = set_value('branch_id', '');
                                        $options = array('' => _('Select Branch'));

                                        if ($data['branch_list']['total']) {
                                            foreach ($data['branch_list']['list'] as $index => $value) {
                                                $options[$value['id']] = $value['title'];
                                            }
                                        }

                                        if (isset($data['content']['branch_id'])) {
                                            $select = set_value('branch_id', $data['content']['branch_id']);
                                        }

                                        echo form_dropdown('branch_id', $options, $select, array('id' => 'cb_branch', 'class' => 'form-control'));
                                        ?>
                                    </div>
                                </div>
                                <div class="col-12 col-md-6 col-lg-4 form-group">
                                    <label><input type="checkbox" id="o_no_schedule" name="no_schedule" value="1" checked="checked"><?php echo _('Apply Now'); ?></label>
                                    <div id="o_schedule_date" style="display:none">
                                        <label for="o_schedule_date"><?php echo _('Application date'); ?></label>
                                        <div class="input-group-prepend date">
                                            <?php
                                            echo form_input(array(
                                                'id' => 'o_schedule_date',
                                                'name' => 'schedule_date',
                                                'class' => 'form-control trans-schedule-datepicker',
                                            ));
                                            ?>
                                            <label for="o_schedule_date" class="input-group-text">
                                                <span class="material-icons">date_range</span>
                                            </label>
                                        </div>
                                    </div>
                                </div>
                           </div>
                             <div class="row">    
                                <div class="col-12 form-group to_other_branch" style="display:none">
                                    <label><?php echo sprintf(_('Match %s'), $data['product_name']) ?></label>
                                    <?php
                                    $attr = array('id' => 'new_product', 'class' => 'form-control', 'style' => 'margin-bottom:5px', 'disabled' => 'disabled');

                                    switch ($data['type']) {
                                        case 'rent_sw' :
                                            $select = '';
                                            $options = array('0' => _('Select Branch First'));

                                            if (!empty($data['new_rent_sw_list']['total'])) {
                                                foreach ($data['new_rent_sw_list']['list'] as $index => $value) {
                                                    $options[$value['id']] = $value['title'];
                                                }
                                                $attr = array('class' => 'form-control', 'style' => 'margin-bottom:5px');
                                            }
                                            break;
                                        case 'rent' :
                                            $select = '';
                                            $options = array('0' => _('Select Branch First'));

                                            if (!empty($data['new_rent_list']['total'])) {
                                                foreach ($data['new_rent_list']['list'] as $index => $value) {
                                                    $options[$value['id']] = $value['title'];
                                                }
                                                $attr = array('class' => 'form-control', 'style' => 'margin-bottom:5px');
                                            }

                                            break;
                                        default :
                                            $select = '';
                                            $options = array('0' => _('Select Branch First'));

                                            if (!empty($data['new_enroll_list']['total'])) {
                                                foreach ($data['new_enroll_list']['list'] as $index => $value) {
                                                    $options[$value['id']] = $value['title'];
                                                }

                                                $attr = array('class' => 'form-control', 'style' => 'margin-bottom:5px');
                                            }
                                    }
                                    echo form_dropdown('product_id', $options, $select, $attr);
                                    ?>
                                </div>
                                <div class="col-12 form-group">
                                    <label for="o_recipient"><?php echo _('Recipient'); ?></label>

<div class="input-group-prepend">
    <?php
    echo form_input(array(
        'name' => 'name',
        'id' => 'c_name',
        'value' => set_value('name'),
        'maxlength' => '60',
        'size' => '60',
        'readonly' => 'readonly',
        'class' => 'form-control',
    ));
    ?>
    <div class="input-group-text t-select-user" title="<?php echo _('Select From User'); ?>">
        <span class="material-icons">account_box</span>
    </div>
</div>
                                </div>

                                <div class="col-12 col-md-4 form-group">
                                <?php


$default_transaction_date_value=$search_data['today'];
$transaction_date_value = set_value('transaction_date', $default_transaction_date_value);

$is_today = false;
if ($transaction_date_value == $search_data['today']) {
    $is_today = true;
}
?>
<label for="is_today"><?php echo _('Transaction Date'); ?>&nbsp;&nbsp;
    <input id="is_today" type="checkbox" name="transaction_date_is_today" value="1" <?php if ($is_today): ?> checked="checked"<?php endif; ?>>
    <?php echo _('Today'); ?>
</label>
<div id="o_transaction_date_layer"<?php if (!$is_today): ?> style="display:block"<?php endif; ?>>
    <div class="input-group-prepend date">
        <?php

        echo form_input(array(
            'name' => 'custom_transaction_date',
            'value' => $transaction_date_value,
            'class' => 'form-control enroll_datepicker',
        ));
        ?>
        <div class="input-group-text">
            <span class="material-icons">date_range</span>
        </div>
    </div>
</div>
<p id="today_display"<?php if (empty($is_today)): ?> style="display:none"<?php endif; ?>>
    <label style="margin:0;padding:0"><?php echo get_dt_format($search_data['today'], $search_data['timezone']); ?></label>
</p>
</div>
                                <div class="col-12 col-md-4 form-group">
                                    <label for="trans_commmission"><?php echo _('Trans Commission'); ?></label>
                                    <?php


$commmission = set_value('commmission', 0);

echo form_input(array(
    'id' => 'trans_commmission',
    'name' => 'commmission',
    'value'=> $commmission,
    'class' => 'form-control p_price'
));


$hidden_commission = set_value('hidden_commission',0);
$credit_value = set_value('credit', 0);
$cash_value = set_value('cash', 0);

echo form_input(array('type' => 'hidden', 'id' => 'hidden_commission', 'value' => $hidden_commission));
echo form_input(array('type' => 'hidden', 'name' => 'cash', 'id' => 'o_cash', 'value' => $cash_value));
echo form_input(array('type' => 'hidden', 'name' => 'credit', 'id' => 'o_credit', 'value' => $credit_value));

?>
                                </div>
                                <div class="col-12 col-md-4 form-group">
                                <label><?php echo _('Payment Method'); ?></label>
                    <?php

                    $options = array(1 => _('Cash'), 2 => _('Credit'), 4 => _('Mix'));
                    $default_payment_method = 2;

                    if (!empty($credit_value) and !empty($cash_value)) {
                        $default_payment_method = 4;
                    } else {
                        if ($cash_value) {
                            $default_payment_method = 1;
                        }
                    }

                    $select = set_value('o_payment_method', $default_payment_method);
                    echo form_dropdown('payment_method', $options, $select, array('id' => 'o_payment_method', 'class' => 'form-control'));

                    ?>
                </div>

                <div class="col-12 col-lg-6 form-group mix"<?php if ($default_payment_method != 4): ?> style="display:none"<?php endif; ?>>
                    <label for="o_credit" class="payment_label"><?php echo _('Credit'); ?>(<?php echo _('Currency'); ?>
                        )</label>
                    <?php

                    $mix_credit_value = set_value('mix_credit', $credit_value);

                    if($mix_credit_value<0) {
                        $mix_credit_value=0;
                    }

                    echo form_input(array(
                        'type' => 'number',
                        'min' => 0,
                        'name' => 'mix_credit',
                        'id' => 'o_mix_credit',
                        'value' => $mix_credit_value,
                        'class' => 'form-control calc_payment',
                    ));
                    ?>
                </div>
                <div class="col-12 col-lg-6 form-group mix"<?php if ($default_payment_method != 4): ?> style="display:none"<?php endif; ?>>
                    <label for="o_cash" class="payment_label"><?php echo _('Cash'); ?>(<?php echo _('Currency'); ?>
                        )</label>
                    <?php

                    $mix_cash_value = set_value('mix_cash', $cash_value);

                    if($mix_cash_value<0) {
                        $mix_cash_value=0;
                    }

                    echo form_input(array(
                        'type' => 'number',
                        'min' => 0,
                        'name' => 'mix_cash',
                        'id' => 'o_mix_cash',
                        'value' => $mix_cash_value,
                        'class' => 'form-control calc_payment',
                    ));
                    ?>
                </div>

                                <div class="col-12 form-group">
                                    <label for="o_content"><?php echo _('Transfer Memo'); ?></label>
                                    <?php

                                    echo form_textarea(array(
                                        'name' => 'content',
                                        'id' => 'o_content',
                                        'value' => set_value('content'),
                                        'rows' => 3,
                                        'class' => 'form-control',
                                    ));
                                    ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </article>
            </div>
            <?php echo form_submit('', _('Transfer'), array('class' => 'btn btn-primary btn-block btn-lg')); ?>
            <?php echo form_close(); ?>
        </div>
    </div>
</div>

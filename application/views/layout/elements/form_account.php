<?php

switch ($this->router->fetch_class()) {
    case 'enrolls':
        $t_title = _('Enroll Price Info');
        break;
    case 'rents':
        $t_title = _('Rent Price Info');
        break;
    default:
        $t_title = _('Order Price Info');
}

if (empty($data['product_price'])) {
    $data['product_price'] = 0;
}

$diff_payment = 0;
if (empty($data['content']['original_price'])) {
    if (empty($data['additional_product_price'])) {
        $original_price_value = set_value('original_price', $data['product_price']);
    } else {
        $original_price_value = $data['additional_product_price'];
    }
} else {
    if (isset($data['content']['payment'])) {
        if ($data['content']['original_price'] != $data['content']['payment']) {
            $diff_payment = 1;
        }
    }

    $original_price_value = set_value('original_price', $data['content']['original_price']);
}

// 할인(퍼센티)
$dc_rate_default_value = 0;

if (!empty($data['content']['dc_rate'])) {
    $dc_rate_default_value = $data['content']['dc_rate'];
}

$dc_rate_value = set_value('dc_rate', $dc_rate_default_value);

// 할인(가격)
$dc_price_default_value = 0;

if (!empty($data['content']['dc_price'])) {
    $dc_price_default_value = $data['content']['dc_price'];
}

$dc_point_value = set_value('dc_price', $dc_price_default_value);

$default_price_check = true;
if ($this->router->fetch_method() == 'edit') {
    $default_price_check = false;
}

?>
<article id="order_account" class="col-12 col-xl-6 col-xxl-7">
    <h3><?php echo $t_title; ?></h3>
    <div class="card">
        <div class="card-body">
            <div class="form-row">
                <div class="col-12 col-xxl-6 form-group">
                    <label for="no_discount"><?php echo _('Original Price'); ?>&nbsp;
                        <input id="no_discount" type="checkbox" name="no_discount" value="1" <?php if (empty($dc_rate_value) and empty($dc_point_value)): ?>checked="checked"<?php endif; ?> /> 
                        <?php echo _('No Discount'); ?>
                    </label>
                    <?php

                    echo form_input(array(
                        'type' => 'hidden',
                        'id' => 'o_diff_price',
                        'value' => $diff_payment,
                    ));

                    echo form_input(array(
                        'type' => 'hidden',
                        'name' => 'original_price',
                        'id' => 'o_original_price',
                        'value' => $original_price_value,
                        'class' => 'form-control',
                    ));

                    ?>
                    <p>
                        <span id="display_original_price"><?php echo number_format($original_price_value); ?></span><?php echo _('Currency'); ?>
                    </p>
                </div>
                <div class="col-12 col-xl-6 form-group">
                    <?php

                    if (isset($data['content']['transaction_date'])) {
                        $default_transaction_date_value = $data['content']['transaction_date'];
                    } else {
                        $default_transaction_date_value = $search_data['today'];
                    }

                    $transaction_date_value = set_value('transaction_date', $default_transaction_date_value);

                    $is_today = false;
                    if ($transaction_date_value == $search_data['today']) {
                        $is_today = true;
                    }
                    ?>
                    <label for="is_today"><?php echo _('Transaction Date'); ?>&nbsp;&nbsp;
                        <input id="is_today" type="checkbox" name="transaction_date_is_today" value="1"<?php if ($is_today): ?> checked="checked"<?php endif; ?>>
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
                <div class="col-12 col-lg-6 form-group dc_layer"<?php if (empty($is_dc)): ?> style="display:none"<?php endif; ?>>
                    <label for="o_dc_rate"><?php echo _('DC Rate'); ?>(%)</label>
                    <?php

                    echo form_input(array(
                        'type' => 'number',
                        'min' => 0,
                        'max' => 100,
                        'name' => 'dc_rate',
                        'id' => 'o_dc_rate',
                        'value' => $dc_rate_value,
                        'class' => 'form-control calc',
                    ));
                    ?>
                </div>
                <div class="col-12 col-lg-6 form-group dc_layer"<?php if (empty($is_dc)): ?> style="display:none"<?php endif; ?>>
                    <label for="o_dc_price"><?php echo _('DC Price'); ?></label>
                    <?php

                    echo form_input(array(
                        'type' => 'number',
                        'min' => 0,
                        'name' => 'dc_price',
                        'id' => 'o_dc_price',
                        'value' => $dc_point_value,
                        'class' => 'form-control calc',
                    ));
                    ?>
                </div>
                <div class="col-12 col-lg-6 form-group">
                    <label for="use_default_price"><?php echo _('Sell Price'); ?>
                        <?php if ($this->router->fetch_method() != 'edit'): ?>
                        &nbsp;&nbsp;
                        <input id="use_default_price" type="checkbox" name="use_default_price" value="1"
                               <?php if ($default_price_check): ?>checked="checked"<?php endif; ?>>
                        <?php echo _('Use Default Price'); ?>
                        <?php endif ?>
                    </label>
                    <div id="sell_price_layer"<?php if ($default_price_check): ?>style="display:none"<?php endif ?>>
                        <?php

                        if (isset($data['content']['price'])) {
                            $default_price_value = $data['content']['price'];
                        } else {
                            $default_price_value = $original_price_value;
                        }

                        $price_value = set_value('price', $default_price_value);

                        echo form_input(array(
                            'type' => 'text',
                            'min' => 0,
                            'id' => 'custom_price',
                            'value' => $price_value,
                            'class' => 'form-control',
                            'style'=>'ime-mode:disabled'
                        ));

                        ?>
                    </div>
                    <p id="display_sell_price_layer"<?php if (empty($default_price_check)): ?> style="display:none"<?php endif ?>>
                        <?php
                        echo form_input(array(
                            'type' => 'hidden',
                            'min' => 0,
                            'name' => 'price',
                            'id' => 'hidden_sell_price',
                            'value' => $price_value,
                            'class' => 'form-control calc_payment'
                        ));

                        ?>
                        <span id="o_sell_price"><?php echo number_format($price_value); ?></span><?php echo _('Currency'); ?>
                    </p>
                </div>
                <div class="col-12 col-lg-6 form-group">
                    <label class="di_title">판매가격표시</label>
                    <p id="price_text"></p>
                    <?php

                    $credit_value = set_value('credit', 0);
                    if (empty($credit_value)) {
                        if (!empty($data['content']['credit'])) {
                            $credit_value = $data['content']['credit'];
                        }
                    }

                    $cash_value = set_value('cash', 0);
                    if (empty($cash_value)) {
                        if (!empty($data['content']['cash'])) {
                            $cash_value = $data['content']['cash'];
                        }
                    }

                    echo form_input(array('type' => 'hidden', 'name' => 'cash', 'id' => 'o_cash', 'value' => $cash_value));
                    echo form_input(array('type' => 'hidden', 'name' => 'credit', 'id' => 'o_credit', 'value' => $credit_value));

                    ?>
                </div>

                <div class="col-12 col-lg-6 form-group">
                    <label for="payment_complete"><?php echo _('Payment'); ?></label>
                    <div id="payment_layer" style="display:none">
                        <?php
                        $payment_value = $price_value;
                        if (isset($data['content']['payment'])) {
                            $payment_value = $data['content']['payment'];
                        }

                        echo form_input(array(
                            'type' => 'number',
                            'min' => 0,
                            'id' => 'o_payment',
                            'value' => $payment_value,
                            'class' => 'form-control calc_payment'
                        ));

                        ?>
                    </div>
                    <p id="display_payment_layer">
                        <span id="display_payment"><?php echo number_format($payment_value); ?></span><?php echo _('Currency'); ?>
                    </p>
                </div>

                <div class="col-12 col-lg-6 form-group">
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
                <?php if ($this->router->fetch_class() == 'enrolls'):

                    $commission_settting = false;
                    if (isset($data['content']['commission'])) {
                        $commission_settting = true;
                    }

                    ?>
                    <div class="col-12 col-lg-6 form-group" style="margin-bottom:0px;">
                            <?php if ($this->session->userdata('role_id') < 3): ?>
                                <div id="commission_layer" style="<?php if (isset($data['content']['lesson_type'])): ?><?php if ($data['content']['lesson_type'] != 4): ?>display:none<?php endif; ?><?php else: ?>display:none<?php endif; ?>">
                        <label><?php echo _('Commission'); ?>(<?php echo _('Currency'); ?>)&nbsp;&nbsp; <input
                                    id="commission_default" type="checkbox" name="commission_default"
                                    value="1" <?php if (empty($commission_settting)): ?> checked="checked"<?php endif; ?> /> <?php echo _('Default'); ?>
                            <span id="commission_default_percentage"></span></label>
                        <div id="o_commission_layer"<?php if (empty($commission_settting)): ?> style="display:none"<?php else: ?> style="display:block"<?php endif; ?>>
                            <?php

                            $commission_value = set_value('commission', 0);

                            if (empty($commission_value)) {
                                if (!empty($data['content']['commission'])) {
                                    $commission_value = $data['content']['commission'];
                                }
                            }

                            echo form_input(array(
                                'type' => 'number',
                                'name' => 'commission',
                                'id' => 'o_commission',
                                'min' => 0,
                                'value' => $commission_value,
                                'class' => 'form-control',
                            ));
                            ?>
                        </div>
                        </div>
                    <?php endif; ?>                        
                </div>
                <div class="col-12 col-xl-6 form-group" style="margin-bottom:8px;">
                    <?php

                    if (isset($data['content']['have_datetime'])) {
                        $have_datetime_obj=new DateTime($data['content']['have_datetime'],$search_data['timezone']);
                        $default_have_datetime_value = $have_datetime_obj->format('Y-m-d');
                    } else {
                        $default_have_datetime_value = $search_data['today'];
                    }

                    $have_datetime_value = set_value('have_datetime', $default_have_datetime_value);

                    $have_date_is_today = false;
                    if ($have_datetime_value == $search_data['today']) {
                        $have_date_is_today = true;
                    }
                    ?>
                    <label for="have_date_is_today"><?php echo _('Have Datetime'); ?>&nbsp;&nbsp;
                        <input id="have_date_is_today" type="checkbox" name="have_date_is_today" value="1"<?php if ($have_date_is_today): ?> checked="checked"<?php endif; ?>>
                        <?php echo _('Today'); ?>
                    </label>
                    <div id="o_have_datetime_layer"<?php if (!$have_date_is_today): ?> style="display:block"<?php endif; ?>>
                        <div class="input-group-prepend date">
                            <?php

                            echo form_input(array(
                                'name' => 'custom_have_date',
                                'value' => $have_datetime_value,
                                'class' => 'form-control enroll_datepicker',
                            ));
                            ?>
                            <div class="input-group-text">
                                <span class="material-icons">date_range</span>
                            </div>
                        </div>
                    </div>
                    <p id="have_date_is_today_display"<?php if (empty($have_date_is_today)): ?> style="display:none"<?php endif; ?>>
                        <label style="margin:0;padding:0"><?php echo get_dt_format($search_data['today'], $search_data['timezone']); ?></label>
                    </p>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</article>
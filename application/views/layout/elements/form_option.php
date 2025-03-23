<article class="col-12"<?php if (empty($data['product_option_category'])): ?> style="display:none"<?php endif; ?>>
    <h3><?php echo _('Product Option'); ?></h3>
    <div class="card">
        <div class="card-body">
            <div class="row">
                <?php if (empty($data['product_option_category'])): ?>
                    <div class="col-6 form-group">

                    </div>
                <?php else: ?>
                    <?php foreach ($data['product_option_category'] as $index => $product_category):
                        $product_id = false;
                        $product_price = false;
                        $product_total = 0;
                        $product_name = false;
                        foreach ($data['product_option']['list'] as $product) {
                            if ($product_category != $product['rel_product_type']) {
                                continue;
                            }

                            if (empty($product_id)) {
                                $product_id = $product['product_id'];
                            }

                            if (empty($product_price)) {
                                $product_price = $product['price'];
                            }

                            if (empty($product_name)) {
                                $product_name = $product['product_name'];
                            }

                            ++$product_total;
                        }
                        ?>
                        <div class="col-4 form-group">
                            <label for="product_<?php echo $product_id; ?>"><?php echo _('Product Category'); ?> :
                                <?php echo get_product_category_label($product_category); ?>
                            </label>
                            <div class="form-check" style="padding-left:40px">
                                <?php
                                if($product_category=='rent') {
                                    echo form_input(array('type' => 'hidden','id'=>'re_order_no','name'=>'re_order_no'));
                                }
                                echo form_input(array('type' => 'hidden', 'name' => 'order[' . $index . '][type]', 'value' => $product_category));
                                echo form_input(array('type' => 'checkbox', 'name' => 'order[' . $index . '][check]', 'value' => 1, 'id' => 'product_' . $product_id, 'class' => 'form-check-input'));
                                ?>
                                <label for="product_<?php echo $product_id; ?>"><?php echo _('Include'); ?></label>
                            </div>
                        </div>
                        <div class="col-8">
                            <div class="form-row">

                                <div class="col-12 col-md-4 form-group">
                                    <label><?php echo _('Product Name'); ?></label>
                                    <?php
                                    $default_product_id = $product_id;

                                    if ($product_total > 1) {
                                        foreach ($data['product_option']['list'] as $product) {
                                            if ($product_category != $product['rel_product_type']) {
                                                continue;
                                            }

                                            $addition_product_option[$product['product_id']] = $product['product_name'];
                                        }
                                        $select = set_value('order[' . $index . '][product]', $default_product_id);
                                        echo form_dropdown('order[' . $index . '][product]', $addition_product_option, $select, array('class' => 'additional_product_select form-control'));
                                    } else {
                                        echo '<p>' . $product_name . '</p>';
                                        echo form_input(array('type' => 'hidden', 'name' => 'order[' . $index . '][product]', 'value' => $product_id));
                                    }

                                    ?>
                                </div>
                                <div class="col-12 col-md-4 form-group">
                                    <label><?php echo _('Price'); ?>(<?php echo _('Currency'); ?>)</label>
                                    <?php
                                    $p_credit_value = set_value('product_price', 0);

                                    if (!$p_credit_value) {
                                        if (isset($data['content']['product_price'])) {
                                            $p_credit_value = $data['content']['product_price'];
                                        }
                                    }

                                    echo form_input(array('type' => 'text', 'name' => 'order[' . $index . '][credit]', 'value' => number_format($product_price), 'class' => 'form-control p_price'));
                                    ?>
                                </div>
                                <div class="col-12 col-md-4 form-group">
                                    <label><?php echo _('Payment Method'); ?></label>
                                    <?php
                                    $options = array('1' => _('Cash'), '2' => _('Credit'));
                                    $default_select = 2;

                                    $select = set_value('payment_method', $default_select);
                                    echo form_dropdown('select_payment', $options, $select, array('class' => 'select_payment form-control'));
                                    ?>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>
</article>
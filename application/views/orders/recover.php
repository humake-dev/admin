<div class="row">
    <article class="col-12">
        <h3><?php echo _('Order Info'); ?></h3>
        <div class="card">
            <div class="card-body" style="padding-bottom:0">
                <div class="row">
                    <div class="col-12 col-lg-6 form-group">
                        <label><?php echo _('Product'); ?></label>
                        <p>
                            <?php if (!empty($data['content']['product_category_name'])): ?>
                                <?php echo $data['content']['product_category_name']; ?>
                                /
                            <?php endif; ?>
                            <?php echo $data['content']['product_name']; ?>
                        <p>
                    </div>
                    <div class="col-12 col-lg-6 form-group">
                        <label><?php echo _('Price'); ?></label>
                        <p>
                            <?php if (empty($data['content']['price'])): ?>
                                <?php echo _('Free'); ?>
                            <?php else: ?>
                                <?php echo number_format($data['content']['price']); ?><?php echo _('Currency'); ?>
                            <?php endif; ?>
                        <p>
                    </div>
                    <div class="col-12 col-lg-6 form-group">
                        <label><?php echo _('Payment'); ?></label>
                        <?php
                        $payment_amount = $data['content']['payment'];
                        ?>
                        <p>
                            <?php if (empty($data['content']['payment'])): ?>
                                <?php echo _('Free'); ?>
                            <?php else: ?>
                                <?php echo number_format($data['content']['payment']); ?><?php echo _('Currency'); ?>
                            <?php endif; ?>
                        <p>
                    </div>

                    <div class="col-12 col-lg-6 form-group">
                        <label><?php echo _('Period'); ?></label>
                            <?php if(empty($data['content']['origin_end_date'])): ?>
                                <div><?php echo get_dt_format($data['content']['start_date'], $search_data['timezone']); ?>
                            ~ 
                                <br />
                                <label>변경 종료일</label>

                                <div class="input-group-prepend date">
                                    <?php

echo form_input(array(
    'name' => 'change_end_date',
    'id' => 'o_change_end_date',
    'class' => 'form-control enroll_datepicker',
));
                                    ?>
                                    <div class="input-group-text">
                                        <span class="material-icons">date_range</span>
                                    </div>
                                </div>
</div>
                            <?php else: ?>
                                <p><?php echo get_dt_format($data['content']['start_date'], $search_data['timezone']); ?>
                                ~ 
                                <?php

echo form_input(array(
    'type' => 'hidden',
    'name' => 'change_end_date',
    'value' => $data['content']['origin_end_date'],
    'id' => 'o_change_end_date',
    'class' => 'form-control enroll_datepicker',
));
?>
                                <?php echo get_dt_format($data['content']['origin_end_date'], $search_data['timezone']); ?>

                        </p>
                        <?php endif ?>
                    </div>
                </div>
            </div>
        </div>
    </article>

</div>
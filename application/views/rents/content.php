<article class="row">
    <h3 class="col-12"><?php echo _('Facility Info'); ?></h3>
    <div class="col-12">
        <div class="card member_info">
            <div class="card-body">
                <div class="form-row">
                    <div class="col-12 col-lg-4 form-group">
                        <p><?php echo _('Facility'); ?> : <?php echo $data['content']['product_name']; ?></p>
                    </div>
                    <div class="col-12 col-lg-4 form-group">
                        <p><?php echo _('Price'); ?> : <?php echo number_format($data['content']['price']); ?></p>
                    </div>
                    <div class="col-12 col-lg-4 form-group">
                        <p><?php echo _('Fee'); ?>
                            : <?php echo number_format($data['content']['price']); ?><?php echo _('Currency'); ?></p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</article>
<div class="form-row">
    <div class="col-6">
        <article class="row">
            <h3 class="col-12"><?php echo _('Rent Default Info'); ?></h3>
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <div class="form-row">
                            <div class="col-12 form-group">
                                <p><?php echo _('Transaction Date'); ?>
                                    : <?php echo get_dt_format($data['content']['transaction_date'], $search_data['timezone']); ?></p>
                            </div>
                            <div class="col-12 form-group">
                                <p><?php echo _('Start Date'); ?>
                                    : <?php echo get_dt_format($data['content']['start_datetime'], $search_data['timezone']); ?></p>
                            </div>
                            <div class="col-12 form-group">
                                <p><?php echo _('End Date'); ?>
                                    : <?php echo get_dt_format($data['content']['end_datetime'], $search_data['timezone']); ?></p>
                            </div>
                            <?php if ($this->input->get('rent')): ?>
                                <div class="col-12 form-group">
                                    <?php

                                    $extend_date_obj = new DateTime($data['content']['end_datetime'], $search_data['timezone']);
                                    $extend_date_obj->modify('+1 Days');
                                    $extend_start_date = $extend_date_obj->format('Y-m-d');

                                    $extend_date_obj->modify('+' . $data['content']['insert_quantity'] . ' Month');
                                    $extend_end_date = $extend_date_obj->format('Y-m-d');
                                    ?>
                                    <input type="hidden" id="extend-facility-id"
                                           value="<?php echo $data['content']['facility_id']; ?>">
                                    <input type="hidden" id="extend-no" value="<?php echo $data['content']['no']; ?>">
                                    <input type="hidden" id="extend-insert-quantity"
                                           value="<?php echo $data['content']['insert_quantity']; ?>">
                                    <input type="hidden" id="extend-start-date"
                                           value="<?php echo $extend_start_date; ?>">
                                    <input type="hidden" id="extend-end-date" value="<?php echo $extend_end_date; ?>">
                                    <input type="button" id="extend-content-button" class="btn btn-secondary"
                                           value="<?php echo _('Re Rent'); ?>"/>
                                </div>
                            <?php endif; ?>

                        </div>
                    </div>
                </div>
            </div>
    </div>
    <div class="col-6">
        <article class="row">
            <h3 class="col-12"><?php echo _('Rent Price Info'); ?></h3>
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <div class="form-row">
                            <div class="col-12 col-lg-6 form-group">
                                <p><?php echo _('Price'); ?>
                                    : <?php echo number_format($data['content']['price']); ?><?php echo _('Currency'); ?>
                                <p>
                            </div>
                            <div class="col-12 col-lg-6 form-group">
                                <p><?php echo _('Payment'); ?>
                                    : <?php echo number_format($data['content']['payment']); ?><?php echo _('Currency'); ?>
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

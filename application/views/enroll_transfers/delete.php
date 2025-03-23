<div class="container">
    <div class="row">
        <div class="col-12">
            <?php echo form_open('/order-transfers/delete/'.$data['transfer_schedule_content']['id'], array('id' => 'delete-order-transfer-schedule-form')); ?>
            <?php if(!empty($data['return_url'])): ?>
            <input type="hidden" name="return_url" value="<?php echo $data['return_url'] ?>">
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
                    <div class="card border-danger">
                        <h3 class="card-header bg-danger text-light"><?php echo _('Confirm Delete Transfer Schedule'); ?></h3>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-12 col-md-6 col-lg-4 form-group">
                                    <label><?php echo _('Recipient') ?></label>
                                    <p><?php echo $data['transfer_schedule_content']['recipient_name'] ?></p>
                                </div>
                                <div class="col-12 col-md-6 col-lg-4 form-group">
                                    <label><?php echo _('Schedule Date') ?></label>
                                    <p><?php echo get_dt_format($data['transfer_schedule_content']['schedule_date'],$search_data['timezone']) ?></p>
                                </div>
                            </div>
                        </div>
                        <div class="card-footer">
                            <div class="col-12">
                                <?php echo form_submit('', _('Delete'), array('class' => 'btn btn-danger')); ?>
                                <?php echo form_close(); ?>
                            </div>
                        </div>
                    </div>                                
                </article>
            </div>


            <?php echo form_close(); ?>
        </div>
    </div>
</div>

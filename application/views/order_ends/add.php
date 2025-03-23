<?php
    $title=  _('End Order');

    if($this->input->get('return')) {
        $title= _('Return');
    }
?>
<?php if ($this->input->get('popup')): ?>
<?php echo form_open('/order-ends/add', array('id' => 'order_delete_form'), array('order_id' => $data['content']['order_id'])); ?>
    <div class="modal-header">
        <h2 class="modal-title"><?php echo $title; ?></h2>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
<div class="modal-body">
<?php else: ?>
    <div class="container">
        <div class="row">
            <div class="col-12">
                <?php echo form_open('/order-ends/add', array('id' => 'order_delete_form'), array('order_id' => $data['content']['order_id'])); ?>
<?php endif; ?>
                <div class="row">
                    <article class="col-12">
                        <h3><?php echo _('Order Info'); ?></h3>
                        <div class="card">
                            <div class="card-body">
                                <div class="form-row">
                                    <div class="col-12 col-md-6 col-lg-4 form-group">
                                        <label for="o_username"><?php echo _('User Name'); ?></label>
                                        <p><?php

                                            echo form_input(array(
                                                'id' => 'o_username',
                                                'value' => $data['content']['user_name'],
                                                'class' => 'form-control-plaintext',
                                            ));
                                            ?></p>
                                    </div>
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
                                    <div class="col-12 col-md-6 col-lg-4 form-group">
                                        <label for="o_course"><?php echo _('Product'); ?></label>
                                        <p><?php

                                            echo form_input(array(
                                                'id' => 'o_course',
                                                'value' => $data['content']['product_name'],
                                                'class' => 'form-control-plaintext',
                                            ));
                                            ?></p>
                                    </div>
                                    <div class="col-12 col-md-6 col-lg-4 form-group">
                                        <label for="o_amount"><?php echo _('Sell Price'); ?></label>
                                        <p><?php

                                            echo form_input(array(
                                                'id' => 'o_amount',
                                                'value' => number_format($data['content']['price']) . _('Currency'),
                                                'class' => 'form-control-plaintext',
                                            ));
                                            ?></p>
                                    </div>
                                    <div class="col-12 col-md-6 col-lg-4 form-group">
                                        <label for="o_payment"><?php echo _('Total Payment'); ?> </label>
                                        <p><?php

                                            echo form_input(array(
                                                'id' => 'o_payment',
                                                'value' => number_format($data['content']['payment']) . _('Currency'),
                                                'class' => 'form-control-plaintext',
                                            ));
                                            ?></p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </article>
                </div>

                <?php if ($this->input->get('popup')): ?>
            </div>
            <div class="modal-footer">
                <?php echo form_submit('', $title, array('class' => 'btn btn-primary btn-block')); ?>
            </div>
            <?php echo form_close(); ?>
            <script src="<?php echo $script; ?>"></script>
            <?php else: ?>
            <?php echo form_submit('', $title, array('class' => 'btn btn-primary btn-block btn-lg')); ?>
            <?php echo form_close(); ?>
        </div>
    </div>
</div>
<?php endif; ?>

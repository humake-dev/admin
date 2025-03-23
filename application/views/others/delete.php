<?php

$title = _('Cancel Order');

if ($this->input->get('return')) {
    $title = _('Return');
}

?>
<div class="container">
        <div class="row">
            <div class="col-12">
                <?php echo form_open($this->router->fetch_class() . '/' . $this->router->fetch_method() . '/' . $data['content']['id'], array('id' => 'order_delete_form')); ?>
                <?php if (isset($data['user_id'])): ?>
                    <input type="hidden" name="user_id" value="<?php echo set_value('user_id', $data['user_id']); ?>"/>
                <?php endif; ?>

                <div class="form-row">
                    <article class="col-12">
                        <h3><?php echo _('Order Info'); ?></h3>
                        <div class="card">
                            <div class="card-body" style="padding-bottom:0">
                                <div class="row">
                                    <div class="form-group col-12 col-md-6 col-lg-4">
                                        <label for="o_username"><?php echo _('User'); ?></label>
                                        <p>
                                            <?php

                                            echo form_input(array(
                                                'id' => 'o_username',
                                                'value' => $data['content']['user_name'],
                                                'class' => 'form-control-plaintext',
                                            ));
                                            ?>
                                        <p>
                                    </div>
                                    <?php if (!empty($common_data['branch']['use_access_card'])): ?>
                                        <div class="form-group col-12 col-md-6 col-lg-4">
                                            <label for="o_card_no"><?php echo _('Access Card No'); ?></label>
                                            <p>
                                                <?php

                                                echo form_input(array(
                                                    'id' => 'o_card_no',
                                                    'value' => $data['content']['card_no'],
                                                    'class' => 'form-control-plaintext',
                                                ));
                                                ?>
                                            </p>
                                        </div>
                                    <?php endif; ?>
                                    <div class="form-group col-12 col-md-6 col-lg-4">
                                        <label for="o_product"><?php echo _('Product'); ?></label>
                                        <p>
                                        <?php echo $data['content']['title'] ?>
                                        </p>
                                    </div>
                                    <div class="form-group col-12 col-md-6 col-lg-4">
                                        <label for=""><?php echo _('Sell Price'); ?></label>
                                        <p>
                                            <?php

                                            echo form_input(array(
                                                'value' => number_format($data['content']['price']) . _('Currency'),
                                                'class' => 'form-control-plaintext',
                                            ));
                                            ?>
                                        </p>
                                    </div>
                                    <div class="form-group col-12 col-md-6 col-lg-4">
                                        <label for=""><?php echo _('Total Payment'); ?></label>
                                        <p>
                                            <?php echo number_format($data['content']['price']) ?><?php echo _('Currency') ?>
                                        </p>
                                    </div>
                                </div>
                    </article>

                    <article class="col-12">
                        <h3>
                            <input type="checkbox" id="insert_refund" name="insert_refund" value="1" checked>
                            <label for="insert_refund"><?php echo _('Insert Refund'); ?></label>
                        </h3>
                    </article>
                    <div class="col-12">
                        <?php include __DIR__ . DIRECTORY_SEPARATOR . 'refund.php'; ?>
                    </div>
                </div>



            <?php echo form_submit('', $title, array('class' => 'btn btn-primary btn-block btn-lg')); ?>
            <?php echo form_close(); ?>
        </div>
    </div>
</div>
<div id="view_message" class="container">
    <div class="row justify-content-center">
        <div class="col-12 col-lg-6">
            <div class="card border-danger">
                <h3 class="card-header bg-danger text-light"><?php echo _('Confirm Change Zero Point'); ?></h3>
                <div class="card-body">
                    <div class="col-12">
                        <?php echo _('Are you sure you want to change zero point?'); ?>
                    </div>
                </div>
                <div class="card-footer">
                    <div class="col-12">
                        <?php echo form_open($this->router->fetch_class() . '/' . 'delete/' . $data['id']); ?>
                        <?php echo form_submit('', _('Change Zero Point'), array('class' => 'btn btn-danger')); ?>
                        <?php echo form_close(); ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

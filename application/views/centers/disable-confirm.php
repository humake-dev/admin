<div id="view_message" class="container">
    <div class="row justify-content-center">
        <div class="col-12 col-lg-6">
            <div class="card border-danger">
                <h3 class="card-header bg-danger text-light"><?php echo _('Confirm decommissioning') ?></h3>
                <div class="card-body">
                    <div class="col-12">
                        <?php echo _('Do you really stop using it?') ?>
                    </div>
                </div>
                <div class="card-footer">
                    <div class="col-12">
                        <?php echo form_open($this->router->fetch_class() . '/' . 'delete/' . $data['id']) ?>
                        <input type="submit" class="btn btn-danger" value="사용중지">
                        <?php echo form_close(); ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

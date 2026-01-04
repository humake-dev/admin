<div id="view_message" class="container">
    <div class="row justify-content-center">
        <div class="col-12 col-lg-6">
            <div class="card border-success">
                <h3 class="card-header bg-success text-light"><?php echo _('Enabled Check') ?></h3>
                <div class="card-body">
                    <div class="col-12">
                        <?php echo _('Is it really active?') ?>
                    </div>
                </div>
                <div class="card-footer">
                    <div class="col-12">
                        <?php echo form_open($this->router->fetch_class() . '/' . 'enable/' . $data['id']) ?>
                        <input type="submit" class="btn btn-success" value="사용활성"/>
                        <?php echo form_close(); ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

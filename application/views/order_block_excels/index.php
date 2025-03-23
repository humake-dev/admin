<div id="import_add" class="container">
    <div class="row">
        <?php echo form_open_multipart('/order-block-excels', array('class' => 'col-12')); ?>
        <div class="form-group">
            <label><?php echo _('Sample File') ?></label>
            <div>
                <a href="https://humake.blob.core.windows.net/humake/orderBlockExcel/sample.xlsx"><?php echo _('Sample File') ?></a>
            </div>
        </div>
        <div class="form-group">
            <label><?php echo _('File'); ?></label>
            <input type="file" name="file" class="form-control-file">
        </div>
        <button type="submit" class="btn btn-lg btn-block btn-primary"><?php echo _('Submit'); ?></button>
        <?php echo form_close(); ?>
    </div>
</div>

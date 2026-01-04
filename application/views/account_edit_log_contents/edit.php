<?php if ($this->input->get('popup')): ?>
<?php echo form_open('', array('class' => '')) ?>
    <div class="modal-header">
        <h2 class="modal-title"><?php echo _('Edit Memo') ?></h2>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
<div class="modal-body">
    <?php else: ?>
    <div class="container">
        <div class="row">
            <div class="col-12">
                <?php echo form_open('', array('class' => '')) ?>
                <?php endif ?>

                <?php include __DIR__ . DIRECTORY_SEPARATOR . 'form.php'; ?>

                <?php if ($this->input->get('popup')): ?>
            </div>
            <div class="modal-footer">
                <input type="submit" class="btn btn-block btn-primary" value="<?php echo _('Edit Memo') ?>"/>
            </div>
            <?php echo form_close() ?>
            <?php else: ?>
            <div class="form-group">
                <?php echo form_submit('', _('Edit Memo'), array('class' => 'btn btn-primary btn-block')) ?>
            </div>
            <?php echo form_close() ?>
        </div>
    </div>
</div>
<?php endif ?>

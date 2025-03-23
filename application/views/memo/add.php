<?php if ($this->input->get('popup')): ?>
<?php echo form_open('', array('id' => 'add_memo_form'), array($data['parent_id_name'] => $this->input->get($data['parent_id_name']))); ?>
    <div class="modal-header">
        <h2 class="modal-title"><?php echo _('Add Memo'); ?></h2>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
<div class="modal-body">
    <?php else: ?>
    <div class="container">
        <div class="row">
            <div class="col-12">
                <?php echo form_open('', array('id' => 'add_memo_form'), array($data['parent_id_name'] => $this->input->get($data['parent_id_name']))); ?>
                <?php endif; ?>

                <?php include __DIR__ . DIRECTORY_SEPARATOR . 'form.php'; ?>

                <?php if ($this->input->get('popup')): ?>
            </div>
            <div class="modal-footer">
                <?php echo form_submit('', _('Add Memo'), array('class' => 'btn btn-primary btn-block')); ?>
            </div>
            <?php echo form_close(); ?>
            <?php else: ?>
            <?php echo form_submit('', _('Add Memo'), array('class' => 'btn btn-primary btn-block btn-lg')); ?>
            <?php echo form_close(); ?>
        </div>
    </div>
</div>
<?php endif; ?>

<?php if ($this->input->get('popup')): ?>
<?php echo form_open('', array('class' => '')) ?>
<div class="modal-header">
    <h5 class="modal-title"><?php echo _('Counsel Information') ?></h5>
    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
        <span aria-hidden="true">&times;</span>
    </button>
</div>
<div class="modal-body">
    <?php else: ?>
    <div class="container">
        <div class="row">
            <div class="col-12">
                <?php endif ?>
                <div>
                    <?php echo nl2br($data['content']['content']) ?>
                </div>
                <?php if ($this->input->get('popup')): ?>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal"><?php echo _('Close') ?></button>
            </div>
            <?php else: ?>
        </div>
    </div>
<?php endif ?>

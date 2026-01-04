<?php if ($this->input->get('popup')): ?>
<?php echo form_open('/counsel-edit-log-contents/edit/' . $data['content']['id']); ?>
    <div class="modal-header">
        <h2 class="modal-title"><?php echo _('Memo'); ?></h2>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
<div class="modal-body">
    <?php else: ?>
    <div class="container">
        <div class="row">
            <div class="col-12">
                <?php endif; ?>
                <div class="content-text">
                    <?php echo nl2br($data['content']['content']); ?>
                </div>
                <div class="form-group" style="display:none">
                    <label for="c_content"><?php echo _('Memo'); ?></label>
                    <?php
                    $value = set_value('content');

                    if (!$value) {
                        if (isset($data['content']['content'])) {
                            $value = $data['content']['content'];
                        }
                    }

                    echo form_textarea(array(
                        'name' => 'content',
                        'id' => 'c_content',
                        'value' => $value,
                        'rows' => '4',
                        'required' => 'required',
                        'class' => 'form-control',
                    ));
                    ?>
                </div>

                <?php if ($this->input->get('popup')): ?>
            </div>
            <div class="modal-footer">
                <!-- <?php echo anchor('/counsel-edit-log-contents/delete/' . $data['content']['id'], _('Delete'), array('class' => 'btn btn-danger content-delete')); ?>
  <button type="button" class="btn btn-secondary content-edit"><?php echo _('Edit'); ?></button> -->
                <button type="button" class="btn btn-secondary" data-dismiss="modal"><?php echo _('Close'); ?></button>
            </div>
            <?php echo form_close(); ?>
            <script src="<?php echo $script; ?>"></script>
            <?php else: ?>
        </div>
    </div>
</div>
<?php endif; ?>

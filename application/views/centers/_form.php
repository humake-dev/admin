<?php
if (empty($data['content'])) {
    $form_url = 'centers/add';
    $form_id = 'add_center_form';
    $form_submit = _('Submit');
} else {
    $form_url = 'centers/edit/' . $data['content']['id'];
    $form_id = 'add_center_form';
    $form_submit = _('Edit');
}
?>
<?php echo form_open_multipart($form_url, array('id' => $form_id, 'class' => 'center-form')); ?>
<div class="card">
    <div class="card-body">
        <div class="form-group">
            <label for="c_title"><?php echo _('Title'); ?></label>
            <?php

            $value = set_value('title');

            if (!$value) {
                if (isset($data['content']['title'])) {
                    $value = $data['content']['title'];
                }
            }

            echo form_input(array(
                'name' => 'title',
                'id' => 'c_title',
                'value' => $value,
                'class' => 'form-control',
            ));
            ?>
        </div>
        <div class="form-group">
            <label for="e_picture1"><?php echo _('Image'); ?></label>
            <?php

            echo form_upload(array(
                'name' => 'photo[]',
                'id' => 'e_picture1',
                'class' => 'form-control-file',
            ));
            ?>
        </div>
    </div>
</div>
<?php echo form_submit('', $form_submit, array('class' => 'btn btn-primary btn-block btn-lg')); ?>
<?php echo form_close(); ?>

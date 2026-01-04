<?php
if (empty($data['content'])) {
    $form_url = 'message-prepares/add';
    $form_id = 'message-prepare-add-form';
} else {
    $form_url = 'message-prepares/edit/' . $data['content']['id'];
    $form_id = 'message-prepare-edit-form';
}
?>
<div class="card">
    <?php echo form_open($form_url, array('id' => $form_id, 'class' => 'card-body')) ?>
    <div class="form-group">
        <label for="mp_title"><?php echo _('Title') ?></label>
        <?php
        $title_value = set_value('title');

        if (!$title_value) {
            if (isset($data['content']['title'])) {
                $title_value = $data['content']['title'];
            }
        }

        echo form_input(array('name' => 'title', 'id' => 'mp_title', 'value' => $title_value, 'class' => 'form-control'));
        ?>
    </div>
    <div class="form-group">
        <label for="mp_content"><?php echo _('Content') ?></labeL>
        <?php

        $content_value = set_value('content');

        if (!$content_value) {
            if (isset($data['content']['content'])) {
                $content_value = $data['content']['content'];
            }
        }

        echo form_textarea(array('name' => 'content', 'id' => 'mp_content', 'value' => $content_value, 'rows' => '5', 'class' => 'form-control'));
        ?>
    </div>
    <div class="form-group">
        <?php echo form_submit('', _('Submit'), array('class' => 'btn btn-primary btn-block')) ?>
    </div>
    <?php echo form_close() ?>
</div>

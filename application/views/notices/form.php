<?php
if (empty($data['content'])) {
    $form_url = 'notices/add';
} else {
    $form_url = 'notices/edit/' . $data['content']['id'];
}
?>
<div class="card">
    <?php echo form_open($form_url, array('class' => 'card-body')) ?>
    <div class="form-group">
        <label for="n_title"><?php echo _('Title') ?></label>
        <?php

        $value = set_value('title');

        if (!$value) {
            if (isset($data['content']['title'])) {
                $value = $data['content']['title'];
            }
        }

        echo form_input(array(
            'name' => 'title',
            'id' => 'n_title',
            'value' => $value,
            'class' => 'form-control'
        ));
        ?>
    </div>
    <div class="form-group">
        <label for="n_content"><?php echo _('Content') ?></labeL>
        <?php

        $value = set_value('content');

        if (!$value) {
            if (isset($data['content']['content'])) {
                $value = $data['content']['content'];
            }
        }

        echo form_textarea(array(
            'name' => 'content',
            'id' => 'n_content',
            'value' => $value,
            'rows' => '5',
            'class' => 'form-control'
        ));
        ?>
    </div>
    <div class="form-group">
        <?php echo form_submit('', _('Submit'), array('class' => 'btn btn-primary btn-block')) ?>
    </div>
    <?php echo form_close() ?>
</div>

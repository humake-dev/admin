<div class="form-group">
    <label for="user_content"><?php echo _('Memo') ?></labeL>
    <?php

    if (isset($data['content']['content'])) {
        $value = $data['content']['content'];
    } else {
        $value = set_value('content');
    }
    $content_attr = array(
        'name' => 'content',
        'id' => 'user_content',
        'value' => $value,
        'rows' => '5',
        'class' => 'form-control'
    );

    echo form_textarea($content_attr);
    ?>
</div>

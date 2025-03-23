<div class="form-group">
    <label for="member_content"><?php echo _('Memo') ?></labeL>
    <?php

    if (isset($data['content']['content'])) {
        $value = $data['content']['content'];
    } else {
        $value = set_value('content');
    }
    $content_attr = array(
        'name' => 'content',
        'id' => 'member_content',
        'value' => $value,
        'rows' => '5',
        'class' => 'form-control'
    );

    echo form_textarea($content_attr);
    ?>
</div>
<div class="form-group">
    <?php echo form_submit('', _('Submit'), array('class' => 'btn btn-primary btn-block')) ?>
</div>

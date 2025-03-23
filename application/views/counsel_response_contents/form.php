<div class="form-group">
    <label for="memo_content"><?php echo _('Answer'); ?></label>
    <?php
    $default_memo_value = null;

    if (isset($data['content']['content'])) {
        $default_memo_value = $data['content']['content'];
    }

    $memo_value = set_value('content', $default_memo_value);

    echo form_textarea(array(
        'name' => 'content',
        'id' => 'memo_content',
        'value' => $memo_value,
        'rows' => '4',
        'required' => 'required',
        'class' => 'form-control',
    ));
    ?>
</div>

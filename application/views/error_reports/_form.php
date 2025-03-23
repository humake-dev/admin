<?php
  if (empty($data['content'])) {
      $form_url = 'error-reports/add';
  } else {
      $form_url = 'error-reports/edit/'.$data['content']['id'];
  }
?>
<?php echo form_open_multipart($form_url); ?>
<div class="card">
<div class="card-body">
<div class="form-group">
  <label for="er_title"><?php echo _('Admin'); ?></label>
  <?php

  echo form_input(array(
          'type='=>'text',
          'value' => $this->session->userdata('admin_name'),
          'readonly'=>'readonly',
          'class' => 'form-control-plaintext',
  ));
  ?>
</div>
<div class="form-group">
  <label for="er_title"><?php echo _('Title'); ?></label>
  <?php

  $value = set_value('title');

  if (!$value) {
      if (isset($data['content']['title'])) {
          $value = $data['content']['title'];
      }
  }

  echo form_input(array(
          'name' => 'title',
          'id' => 'er_title',
          'value' => $value,
          'class' => 'form-control',
  ));
  ?>
</div>
<div class="form-group">
  <label for="er_content"><?php echo _('Content'); ?></labeL>
    <?php

    $value = set_value('content');

    if (!$value) {
        if (isset($data['content']['content'])) {
            $value = $data['content']['content'];
        }
    }

    echo form_textarea(array(
            'name' => 'content',
            'id' => 'er_content',
            'value' => $value,
            'rows' => '5',
            'class' => 'form-control',
    ));
    ?>
</div>
            <div class="form-group">
                <label><?php echo _('File'); ?></label>
                <input type="file" name="file[]" class="form-control-file">
            </div>
            <div class="form-group">
                <label><?php echo _('File'); ?></label>
                <input type="file" name="file[]" class="form-control-file">
              </div>
            <div class="form-group">
                <label><?php echo _('File'); ?></label>
                <input type="file" name="file[]" class="form-control-file">
            </div>
            <div class="form-group">
                <label><?php echo _('File'); ?></label>
                <input type="file" name="file[]" class="form-control-file">
            </div>
            <div class="form-group">
                <label><?php echo _('File'); ?></label>
                <input type="file" name="file[]" class="form-control-file">
            </div>
            <div class="form-group">
                <label><?php echo _('File'); ?></label>
                <input type="file" name="file[]" class="form-control-file">
            </div>
            <div class="form-group">
                <label><?php echo _('File'); ?></label>
                <input type="file" name="file[]" class="form-control-file">
            </div>
            <div class="form-group">
                <label><?php echo _('File'); ?></label>
                <input type="file" name="file[]" class="form-control-file">
            </div>
<?php if ($this->session->userdata('role_id') < 4): ?>

<div class="form-group">
  <div class="form-check form-check-inline">
              <label class="form-check-label">
                <?php
                  $m_checked = false;
                    if (isset($data['content']['solve'])) {
                        if ($data['content']['solve']) {
                            $m_checked = true;
                        }
                    }

                    echo form_checkbox(array(
                            'name' => 'solve',
                            'value' => '1',
                            'checked' => $m_checked,
                            'class' => 'form-check-input',
                    ));
                    ?> <?php echo _('Solve'); ?>
                  </label>
                </div>
</div>

<?php endif; ?>

</div>
</div>
  <?php echo form_submit('', _('Submit'), array('class' => 'btn btn-primary btn-block btn-lg')); ?>

<?php echo form_close(); ?>
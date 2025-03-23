<div class="form-row">
  <article class="col-12">
    <h3><?php echo _('Body'); ?></h3>
    <div class="card">
      <div class="card-body">
        <div class="form-group">
          <label for="e_username"><?php echo _('User Name'); ?></label>
          <input type="hidden" name="user_id" value="<?php echo set_value('user_id', $data['content']['user_id']); ?>" />
          <?php echo form_input(array('id' => 'e_username', 'value' => $data['content']['name'], 'class' => 'form-control-plaintext')); ?>
        </div>
        <div class="form-group">
          <label for="e_card_no"><?php echo _('Height'); ?></label>
          <?php

          if (isset($data['content']['height'])) {
              $height_value = $data['content']['height'];
          } else {
              $default_height_value = 160;

              $height_value = set_value('height', $default_height_value);
          }

          echo form_input(array(
              'id' => 'e_height',
              'name' => 'height',
              'type' => 'number',
              'step' => '0.1',
              'value' => $height_value,
              'class' => 'form-control',
          ));
        ?>
        </div>
      </div>
    </div>
  </article>
</div>

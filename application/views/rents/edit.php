<div class="container">
  <div class="row">
    <?php include __DIR__.DIRECTORY_SEPARATOR.'aside_add.php'; ?>
    <div class="col-12 col-lg-8 col-xxl-9">
      <?php echo form_open('', array('id' => 'rent_edit_form', 'class' => 'humake_rent_edit_form')); ?>
        <?php include __DIR__.DIRECTORY_SEPARATOR.'form.php'; ?>
        <?php if (!empty($this->input->get('user-page'))): ?>
          <?php echo form_hidden('return_url', '/home/rents/'.$data['user_content']['id']); ?>
        <?php endif; ?>
        <?php echo form_submit('', _('Update'), array('class' => 'btn btn-lg btn-primary btn-block')); ?>
      <?php echo form_close(); ?>
    </div>
  </div>
</div>

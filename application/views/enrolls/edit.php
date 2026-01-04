<section class="container">
  <div class="row">
    <?php include __DIR__.DIRECTORY_SEPARATOR.'aside_add.php'; ?>
    <div class="col-12 col-lg-8 col-xxl-9">
      <?php echo form_open('', array('id' => 'enroll_edit_form')); ?>
      <?php include __DIR__.DIRECTORY_SEPARATOR.'form.php'; ?>
      <?php echo form_submit('', _('Update'), array('class' => 'btn btn-lg btn-primary btn-block')); ?>
      <?php echo form_close(); ?>
    </div>
  </div>
</section>

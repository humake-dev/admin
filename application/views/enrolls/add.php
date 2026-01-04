<div class="container">
  <div class="row">
    <?php include __DIR__.DIRECTORY_SEPARATOR.'aside_add.php'; ?>
    <?php if (empty($data['user_content'])): ?>
    <div id="right_data_form" class="col-12 col-lg-6 col-xxl-7">  
    <?php else: ?>
    <div id="right_data_form" class="col-12 col-lg-8 col-xxl-9">     
    <?php endif; ?>
	  <?php echo form_open('', array('id' => 'enroll_add_form','class'=>'user_select_rel_form')); ?>
	  <?php include __DIR__.DIRECTORY_SEPARATOR.'form.php'; ?>
      <?php echo form_submit('', _('Submit'), array('class' => 'btn btn-lg btn-primary btn-block')); ?>
      <?php echo form_close(); ?>
    </div>
  </div>
</div>

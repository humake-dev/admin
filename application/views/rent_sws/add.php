<div class="container">
  <div class="row">
    <?php include __DIR__.DIRECTORY_SEPARATOR.'aside_add.php'; ?>
    <?php if (empty($data['user_content'])): ?>
    <div id="right_data_form" class="col-12 col-lg-6 col-xxl-7">  
    <?php else: ?>
    <div id="right_data_form" class="col-12 col-lg-8 col-xxl-9">     
    <?php endif; ?>
      <?php echo form_open('', array('id' => 'rent_add_form', 'name' => 'rent_add_form')); ?>
        <?php include __DIR__.DIRECTORY_SEPARATOR.'form.php'; ?>
        <?php if (empty($data['user_content'])): ?>
          <?php echo form_submit('', _('Submit'), array('class' => 'btn btn-lg btn-primary btn-block', 'disabled' => 'disabled')); ?>
        <?php else: ?>
          <?php echo form_hidden('return_url', '/home/rent-sws/'.$data['user_content']['id']); ?>
          <?php echo form_submit('', _('Submit'), array('class' => 'btn btn-lg btn-primary btn-block')); ?>
        <?php endif; ?>
      <?php echo form_close(); ?>
    </div>
  </div>
</div>

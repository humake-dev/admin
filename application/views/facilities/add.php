<div id="add-facility" class="container">
  <div class="row">
    <h2 class="col-12">
    <?php echo _('Add Facility') ?>
     &nbsp;&nbsp;<?php echo anchor('/facilities', _('Cancel Add'), array('class'=>'float-right')); ?>
    </h2>
  </div>
  <div class="row">
    <div class="col-12">
      <?php include __DIR__.DIRECTORY_SEPARATOR.'_form.php' ?>
    </div>
  </div>
</div>

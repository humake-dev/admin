<div id="edit-facility" class="container">
  <div class="row">
    <h2 class="col-12">
    <?php echo _('Edit Facility') ?>
     &nbsp;&nbsp;<?php echo anchor('/facilities/view/'.$data['content']['id'], _('Cancel Edit'), array('class'=>'float-right')); ?>
    </h2>
  </div>
  <div class="row">
    <div class="col-12">
      <?php include __DIR__.DIRECTORY_SEPARATOR.'_form.php' ?>
    </div>
  </div>
</div>

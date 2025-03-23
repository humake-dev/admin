<div id="add-user-stop-custom" class="container">
  <div class="row">
    <h2 class="col-12">
    <?php echo _('Edit User Stop Custom') ?>
     &nbsp;&nbsp;<?php echo anchor('/home/stops/'.$data['content']['user_id'], _('Cancel Edit'), array('class'=>'float-right')); ?>
    </h2>
  </div>
  <div class="row">
    <div class="col-12">
      <?php include __DIR__.DIRECTORY_SEPARATOR.'_form.php' ?>
    </div>
  </div>
</div>

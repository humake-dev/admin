<?php
  if (empty($data['content'])) {
      $form_url='others/add';
  } else {
      $form_url='others/edit/'.$data['content']['id'];
  }
?>
<aside class="col-12 col-xxl-3 left-form">
  <div class="row">
    <h2 class="col-12">
    <?php if (isset($data['content'])): ?>
    <?php echo _('Edit Other') ?>
     &nbsp;&nbsp;<?php echo anchor('/account-others', _('Cancel Edit'), array('class'=>'float-right')); ?>
     <?php else: ?>
     <?php echo _('Add Other') ?>
     <?php endif ?>
     </h2>
  </div>
  <article class="row">
    <div class="col-12">
      <div class="card">
      <?php echo form_open($form_url, array('class'=>'card-body')) ?>
      <div class="row">
      <?php include __DIR__.DIRECTORY_SEPARATOR.'form.php' ?>
      <div class="col-12 col-md-6 col-lg-12 form-group">
        <?php echo form_submit('', _('Submit'), array('class'=>'btn btn-primary btn-block')) ?>
      </div>
      </div>
      <?php echo form_close() ?>
      </div>
    </div>
  </article>
</aside>

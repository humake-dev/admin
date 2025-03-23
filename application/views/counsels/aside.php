<aside class="col-12">
  <div class="row">
    <h2 class="col-12">
      <?php if (isset($data['content'])): ?>
      <?php echo _('Edit Counsel') ?>
      &nbsp;&nbsp;<?php echo anchor('/counsels', _('Cancel Edit'), array('class'=>'float-right')); ?>
      <?php else: ?>
      <?php echo _('Add Counsel') ?>
      <?php endif ?>
    </h2>
  </div>
  <article class="row">
    <div class="col-12">
      <?php include __DIR__.DIRECTORY_SEPARATOR.'form.php' ?>
    </div>
  </article>
</aside>

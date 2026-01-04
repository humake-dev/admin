<aside class="col-12 col-xxl-3 left-form">
  <div class="row">
    <h2 class="col-12">
      <?php if (isset($data['content'])): ?>
      <?php echo _('Edit Course Category') ?>
      <?php if($this->session->userdata('role_id')==1): ?>
      &nbsp;&nbsp;
      <?php echo anchor('/course-categories',_('Cancel Edit'), array('class'=>'float-right')); ?>
      <?php endif ?>
      <?php else: ?>
      <?php echo _('Add Course Category') ?>
      <?php endif ?>
    </h2>
  </div>
  <article class="row">
    <div class="col-12">
      <?php include __DIR__.DIRECTORY_SEPARATOR.'form.php' ?>
    </div>
  </article>
</aside>

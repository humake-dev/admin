<div id="employees" class="container">
  <div class="row">
    <?php include __DIR__.DIRECTORY_SEPARATOR.'aside.php'; ?>
    <div class="col-12 col-lg-7 col-xl-8 col-xxl-9">
      <?php if (!empty($data['content'])): ?>
      <?php echo $Layout->Element('employees/nav'); ?>
      <?php endif; ?>
      <h2 class="di_title"><?php echo _('Employee Info'); ?></h2>
      <?php if (empty($data['content'])): ?>
      <div class="card border-warning">
        <div class="card-header bg-warning">
          <h3 class="text-light"><?php echo _('No staff'); ?></h3>
        </div>
        <div class="card-body">
          <p><?php echo _('Register your staff first'); ?>
          <?php if ($this->Acl->has_permission('employees', 'write')): ?>
            <?php echo anchor('employees/add', _('Add Employee'), array('class' => 'btn btn-primary')); ?>
          <?php endif; ?>
          </p>
        </div>
      </div>
      <?php else: ?>
      <?php include __DIR__.DIRECTORY_SEPARATOR.'content.php'; ?>
      <?php endif; ?>
    </div>
  </div>
</div>

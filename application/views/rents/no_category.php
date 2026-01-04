<div class="container">
  <div class="row">
    <?php include __DIR__.DIRECTORY_SEPARATOR.'aside.php' ?>
    <div class="col-12 col-xxl-9 list">
      <div class="row">
        <div class="col-12">
          <div class="card border-warning">
            <div class="card-header bg-warning">
              <h3 class="text-light"><?php echo _('There is no locker') ?></h3>
            </div>
            <div class="card-body">
              <p><?php echo _('Register your locker first') ?>
              <?php if($this->Acl->has_permission('facilities','write')): ?>
                <?php echo anchor('facilities/add', '락커등록', array('class'=>'btn btn-secondary')) ?>
              <?php endif ?>
              </p>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

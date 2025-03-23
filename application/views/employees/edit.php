<div id="edit-employee" class="container">
  <div class="row">
    <?php include __DIR__.DIRECTORY_SEPARATOR.'aside.php' ?>
      <div class="col-12 col-lg-8 col-xxl-9">
        <h2 style="text-indent:-9999px;height:1px"><?php echo _('Employee Default Info') ?></h2>
        <div class="form-group" style="text-align:right">
          <?php echo anchor('employees/view/'.$data['content']['id'], _('View Mode'), array('class'=>'btn btn-secondary')) ?>
        </div>
        <?php include __DIR__.DIRECTORY_SEPARATOR.'form.php' ?>
    </div>
  </div>
</div>

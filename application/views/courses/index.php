<div id="courses" class="container">
  <div class="row">
    <?php include __DIR__.DIRECTORY_SEPARATOR.'aside.php' ?>
    <div class="col-12 col-md-6 col-lg-8 col-xl-9 right_a">
      <h3><?php echo _('Course') ?></h3>
      <div class="card">
        <div class="card-body">
        <?php if ($data['category']['total']): ?>
           <?php echo _('Please select the lesson on the left') ?>
        <?php else:?>
          <?php echo _('No lessons') ?>
        <?php endif ?>
        </div>
      </div>
    </div>
  </div>
</div>

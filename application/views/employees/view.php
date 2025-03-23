<div id="employees" class="container">
  <div class="row">
    <?php include __DIR__.DIRECTORY_SEPARATOR.'aside.php' ?>
    <div class="col-12 col-lg-7 col-xl-8 col-xxl-9">
      <?php echo $Layout->Element('employees/nav') ?>
      <h2 class="di_title"><?php echo _('Employee Info') ?></h2>
      <?php include __DIR__.DIRECTORY_SEPARATOR.'content.php' ?>
    </div>
  </div>
</div>

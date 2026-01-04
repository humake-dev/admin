<div id="reservations" class="container">
  <div class="row">
    <div class="col-12">
      <article class="row">
        <h1 class="col-12"><?php echo _('Enter Schedule') ?>(<?php echo get_dt_format($search_data['date'],$search_data['timezone']) ?> <?php echo $search_data['time'] ?>)</h1>
        <div class="col-12">
          <?php include __DIR__.DIRECTORY_SEPARATOR.'form.php' ?>
        </div>
      </article>
    </div>
  </div>
</div>

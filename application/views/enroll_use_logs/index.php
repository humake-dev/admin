<div id="enroll-use-logs" class="container">
  <div class="row">
    <div class="col-12">
      <?php if(!empty($data['content'])): ?>
      <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
          <li class="breadcrumb-item"><?php echo anchor('/',_('Home')) ?></li>
          <li class="breadcrumb-item"><?php echo anchor('/trainer-actives',_('Trainer Active Index')) ?></li>
          <li class="breadcrumb-item"><?php echo anchor('/trainer-actives/view/'.$data['content']['manager_id'],_('Trainer Active View')) ?></li>
          <li class="breadcrumb-item active" aria-current="page"><strong><?php echo _('Trainer Active View Detail') ?></strong></li>
        </ol>
      </nav>
      <?php endif ?>
    </div>
    <div class="col-12">
      <?php include __DIR__.DIRECTORY_SEPARATOR.'search_form.php' ?>
    </div>
    <div class="col-12">
      <?php include __DIR__.DIRECTORY_SEPARATOR.'list.php' ?>
    </div>
  </div>
</div>

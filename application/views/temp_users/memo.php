<div id="users" class="container">
  <div class="row">
    <?php include __DIR__.DIRECTORY_SEPARATOR.'aside.php'; ?>
    <div class="col-12 col-lg-8 col-xxl-9 user_sub">
      <?php if (empty($data['content'])): ?>
      <?php echo $Layout->element('home/not_found.php'); ?>
      <?php else: ?>    
      <?php include __DIR__.DIRECTORY_SEPARATOR.'nav.php'; ?>
      <div class="row">
        <div class="col-12">
          <div class="card">
          <h3 class="col-12 card-header"><?php echo _('Memo'); ?></h3>          
            <div class="card-body">         
                <div class="row">
                  <div class="col-12">
                    <?php include __DIR__.DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'temp_user_contents'.DIRECTORY_SEPARATOR.'list.php'; ?>
                  </div>
                </div>

            </div>
          </div><!-- card end -->
        </div>
      </div>
      <?php endif; ?>
    </div>
  </div>
</div>

<div id="users" class="container">
  <div class="row">
    <?php

    include __DIR__.DIRECTORY_SEPARATOR.'aside.php';
      $params = '';
      if ($this->input->get()) {
          $p_index = 0;
          foreach ($this->input->get() as $key => $param) {
              if ($p_index) {
                  $params .= '&'.$key.'='.$param;
              } else {
                  $params .= '?'.$key.'='.$param;
              }
              ++$p_index;
          }
      }
    ?>
    <div class="col-12 col-lg-7 col-xl-8 col-xxl-9 user_sub">
      <?php include __DIR__.DIRECTORY_SEPARATOR.'nav.php'; ?>
      <section class="row">
        <div class="col-12">
          <h2 style="text-indent:-9999px;height:1px"><?php echo _('Basic member information'); ?></h2>
          <?php if (empty($data['content'])): ?>
          <div class="card border-warning">
            <div class="card-header bg-warning">
              <h3 class="text-light"><?php if (!empty($search_data['search_type'])): ?><?php endif; ?><?php echo _('No such member'); ?></h3>
            </div>
            <div class="card-body">
              <?php if (empty($search_data['search_type'])): ?>
              <p><?php echo _('Register first'); ?>
              <?php if ($this->Acl->has_permission('users', 'write')): ?>
                <?php echo anchor('/temp-users/add', '회원등록', array('class' => 'btn btn-secondary')); ?>
              <?php endif; ?>
              </p>
              <?php else: ?>
              <p><?php echo _('Try again by varying the search criteria'); ?></p>
              <?php endif; ?>
            </div>
          </div>
          <?php else: ?>
           <div class="form-group" style="text-align:right">
             <?php echo anchor('/temp-users/view/'.$data['content']['id'].$params, _('View Mode'), array('class' => 'btn btn-secondary')); ?>
           </div>
          <?php include __DIR__.DIRECTORY_SEPARATOR.'form.php'; ?>
          <?php endif; ?>
        </div>
      </section>
    </div>
  </div>
</div>

<article class="card border-warning">
  <div class="card-header bg-warning">
    <h3 class="text-light"><?php if (!empty($search_data['search_type'])): ?><?php endif; ?><?php echo _('No such member'); ?>.</h3>
  </div>
  <div class="card-body">
  <?php if (empty($search_data['search_type'])): ?>
    <p>
      <?php echo _('Register first'); ?>.
      <?php if ($this->Acl->has_permission('users', 'write')): ?>
      <?php echo anchor('users/add', _('Add User'), array('class' => 'btn btn-primary')); ?>
      <?php endif; ?>
    </p>      
    <?php if ($this->session->userdata('show_omu')): ?>              
    <p class="text-danger">내 회원만 보기 상태입니다. 이것으로 인해 회원이 표시되지 않을 수 있습니다.</p>
    <?php endif; ?>
  <?php else: ?>
    <p><?php echo _('Try again by varying the search criteria'); ?></p>
  <?php endif; ?>
  </div>
</article>
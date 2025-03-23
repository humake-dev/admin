<div class="row">
  <nav class="col-12 sub_nav">
    <ul class="nav nav-pills">
      <li class="nav-item"><?php echo anchor('temp-users/view/'.$data['content']['id'].$param, _('Info'), array('class' => get_nav_class('index', $this->router->fetch_method(), array('index', 'view')))); ?></li>

      <?php if ($this->Acl->has_permission('counsels')): ?>
      <li class="nav-item"><?php echo anchor('temp-users/counsels/'.$data['content']['id'].$param, _('Counsel'), array('class' => get_nav_class('counsels', $this->router->fetch_method(), array('index', 'view')))); ?></li>
      <?php endif; ?>

      <li class="nav-item"><?php echo anchor('temp-users/memo/'.$data['content']['id'].$param, _('Memo'), array('class' => get_nav_class('memo', $this->router->fetch_method(), array('index', 'view')))); ?></li>

      <?php if ($this->Acl->has_permission('messages')): ?>
      <li class="nav-item"><?php echo anchor('temp-users/messages/'.$data['content']['id'].$param, _('Message'), array('class' => get_nav_class('messages', $this->router->fetch_method(), array('index', 'view')))); ?></li>
      <?php endif; ?>
    </ul>
  </nav>
</div>

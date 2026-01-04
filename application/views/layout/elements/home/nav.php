<?php

$param = '';

$get = $this->input->get();

// 제거할 키 배열
$exclude_keys = ['show_count'];

$get = array_diff_key($get, array_flip($exclude_keys));

if (count($get)) {
    $param = '?'.http_build_query($get, '', '&amp;');
}
?>
<div class="row">
  <nav class="col-12 sub_nav"<?php if (in_array($this->router->fetch_method(),array('index','view','edit'))): ?> style="margin-bottom:0"<?php endif; ?>>
    <ul class="nav nav-pills">
      <li class="nav-item"><?php echo anchor('/view/'.$data['content']['id'].$param, _('Info'), array('class' => get_nav_class('index', $this->router->fetch_method(), array('index', 'view', 'edit')))); ?></li>
      <?php if ($this->Acl->has_permission('enrolls')): ?>
      <li class="nav-item"><?php echo anchor('home/enrolls/'.$data['content']['id'].$param, _('Enroll'), array('class' => get_nav_class('enrolls', $this->router->fetch_method(), array('index', 'view')))); ?></li>
      <?php endif; ?>      
      <?php if ($this->Acl->has_permission('enrolls')): ?>
      <li class="nav-item"><?php echo anchor('home/stops/'.$data['content']['id'].$param, _('Stop Order'), array('class' => get_nav_class('stops', $this->router->fetch_method(), array('index', 'view')))); ?></li>
      <?php endif; ?>

      <?php if ($this->Acl->has_permission('rents')): ?>
      <li class="nav-item"><?php echo anchor('home/rents/'.$data['content']['id'].$param, _('Facility'), array('class' => get_nav_class('rents', $this->router->fetch_method(), array('index', 'view')))); ?></li>
      <?php endif; ?>
      <?php if ($this->Acl->has_permission('rent_sws')): ?>
      <li class="nav-item"><?php echo anchor('home/rent-sws/'.$data['content']['id'].$param, _('Sports Wear'), array('class' => get_nav_class('rent_sws', $this->router->fetch_method(), array('index', 'view')))); ?></li>
      <?php endif; ?>
      <li class="nav-item"><?php echo anchor('home/attendances/'.$data['content']['id'].$param, _('Attendance'), array('class' => get_nav_class('attendances', $this->router->fetch_method(), array('index', 'view')))); ?></li>
      <?php if ($this->Acl->has_permission('accounts')):?>
      <li class="nav-item"><?php echo anchor('home/accounts/'.$data['content']['id'].$param, _('Account'), array('class' => get_nav_class('accounts', $this->router->fetch_method(), array('index', 'view')))); ?></li>
      <?php endif; ?>
      <?php if ($this->Acl->has_permission('body_indexes')): ?>
      <li class="nav-item"><?php echo anchor('home/body-indexes/'.$data['content']['id'].$param, _('Body Info'), array('class' => get_nav_class('body_indexes', $this->router->fetch_method(), array('index', 'view')))); ?></li>
      <?php endif; ?>
      <li class="nav-item"><?php echo anchor('home/memo/'.$data['content']['id'].$param, _('Memo'), array('class' => get_nav_class('memo', $this->router->fetch_method(), array('index', 'view')))); ?></li>
      <?php if ($this->Acl->has_permission('reservations')): ?>
      <li class="nav-item"><?php echo anchor('home/reservations/'.$data['content']['id'].$param, _('Reservation'), array('class' => get_nav_class('reservations', $this->router->fetch_method(), array('index', 'view')))); ?></li>
      <?php endif; ?>
      <?php if ($this->Acl->has_permission('messages')): ?>
      <li class="nav-item"><?php echo anchor('home/messages/'.$data['content']['id'].$param, _('Message'), array('class' => get_nav_class('messages', $this->router->fetch_method(), array('index', 'view')))); ?></li>
      <?php endif; ?>
    </ul>
  </nav>
</div>

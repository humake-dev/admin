<?php
  $param='';

  if ($this->input->get('page')) {
      $param='?page='.$this->input->get('page');
  }

?>
<nav class="sub_nav">
  <ul class="nav nav-pills"<?php if (in_array($this->router->fetch_method(),array('index','view'))): ?> style="margin-bottom:0"<?php endif; ?>>
    <li class="nav-item"><?php echo anchor('employees/view/'.$data['content']['id'], _('Default Info'),array('class'=>get_nav_class('index',$this->router->fetch_method(),array('index','view')))) ?></li>
    <?php if(!empty($data['content']['is_fc'])): ?>
    <li class="nav-item"><?php echo anchor('employees/users/'.$data['content']['id'], _('Employee User'),array('class'=>get_nav_class('users',$this->router->fetch_method(),array('index','view')))) ?></li>
    <li class="nav-item"><?php echo anchor('employees/counsels/'.$data['content']['id'], _('Counsel User'),array('class'=>get_nav_class('counsels',$this->router->fetch_method(),array('index','view')))) ?></li>
    <?php endif ?>
  </ul>
</nav>

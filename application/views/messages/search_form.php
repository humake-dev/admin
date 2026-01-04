<?php
  $top_class1='nav-link active';
  $top_class2='nav-link';
  $top_class3='nav-link';

  if($this->input->get('type')=='sms') {
    $top_class1='nav-link';
    $top_class2='nav-link active';
    $top_class3='nav-link';
  }

  if ($this->input->get('type')=='push') {
      $top_class1='nav-link';
      $top_class2='nav-link';
      $top_class3='nav-link active';
  }

?>
<div class="row">
  <nav class="col-12 sub_nav">
    <ul class="nav nav-pills">
      <li class="nav-item"><?php echo anchor('messages', _('All'), array('class' => $top_class1)); ?></li>
      <li class="nav-item"><?php echo anchor('messages?type=sms', _('SMS'), array('class' => $top_class2)); ?></li>
      <li class="nav-item"><?php echo anchor('messages?type=push',_('Push'), array('class' => $top_class3)); ?></li>
    </ul>
    </nav>
</div>

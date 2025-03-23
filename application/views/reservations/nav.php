<?php

  $bt_param=$b_param;
  unset($bt_param['type']);

  if(count($bt_param)) {
    $bt_param_s='&amp;'.http_build_query($bt_param);  
  } else {
    $bt_param_s='';
  }

  if($this->router->fetch_method()=='index') {
    $reservation_link='/reservations';
  } else {
    $reservation_link='/reservations/'.$this->router->fetch_method().'/'.$data['content']['id'];
  }

?>
<nav class="sub_nav" style="margin-bottom:0">
  <ul class="nav nav-pills" style="margin-bottom:0">
    <li class="nav-item"><?php echo anchor($reservation_link.'?type=day'.$bt_param_s,_('Daily'),array('class'=>get_nav_class('day',$this->input->get('type'),'day'))) ?></li>
    <li class="nav-item"><?php echo anchor($reservation_link.'?type=week'.$bt_param_s,_('Weekly'),array('class'=>get_nav_class('week',$this->input->get('type'),'day'))) ?></li>
    <li class="nav-item"><?php echo anchor($reservation_link.'?type=month'.$bt_param_s,_('Monthly'),array('class'=>get_nav_class('month',$this->input->get('type'),'day'))) ?></li>
  </ul>
</nav>

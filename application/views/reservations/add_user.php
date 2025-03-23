<?php

  $default_manager_id=null;
  if (isset($data['content']['manager_id'])) {
    $default_manager_id=$data['content']['manager_id'];
  }

  $select_manager_id=set_value('manager',$default_manager_id);

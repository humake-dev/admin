<?php echo form_open('',array('method'=>'get','class'=>'form-inline float-sm-right'),array('type'=>$search_data['type'],'date'=>$search_data['date'])) ?>
  <div class="float-right">
  <?php
    $options=array(''=>_('All'));

    if($data['admin']['total']) {
      foreach ($data['admin']['list'] as $value) {
        $options[$value['id']]=$value['name'];
      }
    }

    $select=set_value('trainer','');

    echo form_dropdown('trainer', $options, $select, array('id'=>'r_trainer','class'=>'form-control'));
  ?>
    <?php echo form_submit('', _('Search'), array('class'=>'btn btn-primary')) ?>
  </div>
  <?php echo form_close() ?>
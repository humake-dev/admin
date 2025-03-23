<?php
  if (empty($data['content'])) {
      $form_url='role-permissions/add';
  } else {
      $form_url='role-permissions/edit/'.$data['content']['id'];
  }
?>
<div class="card">
  <?php echo form_open($form_url, array('class'=>'card-body')) ?>
    <div class="form-group">
      <label for="rp_role"><?php echo _('Role') ?></label>
      <?php
        $options=array(''=>_('Select Role'));
        $select=set_value('role');

        if($data['role']['total']) {
          foreach($data['role']['list'] as $role) {
            $options[$role['id']]=$role['title'];
          }
        }

        if (isset($data['content']['role_id'])) {
          $select=set_value('role', $data['content']['role_id']);
        }
        echo form_dropdown('role', $options, $select, array('id'=>'rp_role','class'=>'form-control'));
      ?>
    </div>
    <div class="form-group">
      <label for="rp_permission"><?php echo _('Permission') ?></label>
      <?php
        $options=array(''=>_('Select Permission'));
        $select=set_value('permission');

        if($data['permission']['total']) {
          foreach($data['permission']['list'] as $role) {
            $options[$role['id']]=$role['title'];
          }
        }

        if (isset($data['content']['permission_id'])) {
          $select=set_value('permission', $data['content']['permission_id']);
        }
        echo form_dropdown('permission', $options, $select, array('id'=>'rp_permission','class'=>'form-control'));
      ?>
    </div>
    <div class="form-group">
      <?php echo form_submit('', _('Submit'), array('class'=>'btn btn-primary btn-block')) ?>
    </div>
  <?php echo form_close() ?>
</div>

<?php
if (empty($data['content'])) {
    $form_url = 'admin-permissions/add';
} else {
    $form_url = 'admin-permissions/edit/' . $data['content']['id'];
}
?>
<div class="card">
    <?php echo form_open($form_url, array('class' => 'card-body')) ?>
    <div class="form-group">
        <label for="ap_admin"><?php echo _('Admin') ?></label>
        <?php
        $options = array('' => _('Select Admin'));
        $select = set_value('admin');

        if ($data['admin']['total']) {
            foreach ($data['admin']['list'] as $role) {
                $options[$role['id']] = $role['name'];
            }
        }

        if (isset($data['content']['admin'])) {
            $select = set_value('admin', $data['content']['admin']);
        }
        echo form_dropdown('admin', $options, $select, array('id' => 'ap_admin', 'class' => 'form-control'));
        ?>
    </div>
    <div class="form-group">
        <label for="ap_permission"><?php echo _('Permission') ?></label>
        <?php
        $options = array('' => _('Select Permission'));
        $select = set_value('permission');

        if ($data['permission']['total']) {
            foreach ($data['permission']['list'] as $role) {
                $options[$role['id']] = $role['title'];
            }
        }

        if (isset($data['content']['permission'])) {
            $select = set_value('permission', $data['content']['permission']);
        }
        echo form_dropdown('permission', $options, $select, array('id' => 'ap_permission', 'class' => 'form-control'));
        ?>
    </div>
    <div class="form-group">
        <?php echo form_submit('', _('Submit'), array('class' => 'btn btn-primary btn-block')) ?>
    </div>
    <?php echo form_close() ?>
</div>

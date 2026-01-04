<div id="edit_admins" class="container">
    <div class="row">
        <div class="col-12">
            <?php echo form_open('') ?>
            <div class="card">
                <h2 class="card-header"><?php echo _('Change Password') ?></h2>
                <div class="card-body">
                    <div class="row">
                        <?php if (empty($data['content'])): ?>
                            <div class="col-12 form-group">
                                <label for="a_current_password"><?php echo _('Current Password') ?></label>
                                <?php
                                echo form_input(array(
                                    'type' => 'password',
                                    'name' => 'current_password',
                                    'id' => 'a_current_password',
                                    'value' => set_value('current_password'),
                                    'maxlength' => '60',
                                    'size' => '60',
                                    'required' => 'required',
                                    'class' => 'form-control'
                                ));
                                ?>
                            </div>
                        <?php else: ?>
                            <div class="col-12 form-group">
                                <?php
                                echo form_input(array(
                                    'type' => 'hidden',
                                    'name' => 'employee_id',
                                    'value' => $data['content']['id']
                                ));
                                ?>
                                <label for=""><?php echo _('Employee') ?></label>
                                <p class="form-control-plaintext"><?php echo $data['content']['name'] ?></p>
                            </div>
                        <?php endif ?>
                        <div class="col-12 form-group">
                            <label for="a_new_password"><?php echo _('New Password') ?></label>
                            <?php
                            echo form_input(array(
                                'type' => 'password',
                                'name' => 'new_password',
                                'id' => 'a_new_password',
                                'value' => set_value('new_password'),
                                'maxlength' => '60',
                                'size' => '60',
                                'required' => 'required',
                                'class' => 'form-control'
                            ));
                            ?>
                        </div>
                        <div class="col-12 form-group">
                            <label for="a_new_password_confirm"><?php echo _('New Password Confirm') ?></label>
                            <?php
                            echo form_input(array(
                                'type' => 'password',
                                'name' => 'new_password_confirm',
                                'id' => 'a_new_password_confirm',
                                'value' => set_value('new_password_confirm'),
                                'maxlength' => '60',
                                'size' => '60',
                                'required' => 'required',
                                'class' => 'form-control'
                            ));
                            ?>
                        </div>
                    </div>
                </div>
            </div>
            <?php echo form_submit('', _('Change'), array('class' => 'btn btn-lg btn-primary btn-block')) ?>
            <?php echo form_close() ?>
        </div>
    </div>
</div>

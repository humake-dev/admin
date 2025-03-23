<div id="edit_admins" class="container">
    <div class="row">
        <div class="col-12">
            <?php echo form_open_multipart(''); ?>
            <div class="card">
                <h2 class="card-header"><?php echo _('Modify member information'); ?></h2>
                <div class="card-body">
                    <div class="row">
                        <div class="col-12 col-lg-6 form-group">
                            <label><?php echo _('Name'); ?></label>
                            <input type="text" name="name" value="<?php echo $data['content']['name']; ?>"
                                   class="form-control"/>
                        </div>
                        <div class="col-12 col-lg-6 form-group">
                            <label><?php echo _('Change Password'); ?></label>
                            <?php echo anchor('/admins/change-password', _('Change Password'), array('class' => 'btn btn-primary form-control')); ?>
                        </div>
                        <div class="col-12 col-lg-6 form-group">
                            <label><?php echo _('Phone'); ?></label>
                            <input type="text" name="phone" value="<?php echo $data['content']['phone']; ?>"
                                   class="form-control"/>
                        </div>
                        <div class="col-12 col-lg-6 form-group">
                            <label><?php echo _('Email'); ?></label>
                            <input type="email" name="email" value="<?php echo $data['content']['email']; ?>"
                                   class="form-control"/>
                        </div>
                        <div class="col-12 col-lg-6 form-group">
                            <label><?php echo _('Gender'); ?></label>
                            <div>
                                <div class="form-check form-check-inline">

                                    <?php
                                    $m_checked = true;
                                    if (isset($data['content']['gender'])) {
                                        if ($data['content']['gender'] != 1) {
                                            $m_checked = false;
                                        }
                                    }

                                    echo form_radio(array(
                                        'name' => 'gender',
                                        'id' => 'a_male',
                                        'value' => '1',
                                        'checked' => $m_checked,
                                        'class' => 'form-check-input',
                                    ));
                                    ?>
                                    <label for="a_male" class="form-check-label"><?php echo _('Male'); ?></label>
                                </div>
                                <div class="form-check form-check-inline">

                                    <?php
                                    $f_checked = false;
                                    if (isset($data['content']['gender'])) {
                                        if ($data['content']['gender'] == 0) {
                                            $f_checked = true;
                                        }
                                    }

                                    echo form_radio(array(
                                        'name' => 'gender',
                                        'id' => 'a_female',
                                        'value' => '0',
                                        'checked' => $f_checked,
                                        'class' => 'form-check-input',
                                    ));
                                    ?>
                                    <label for="a_female" class="form-check-label"><?php echo _('Female'); ?></label>
                                </div>

                            </div>
                        </div>

                        <div class="col-12 form-group">
                            <label><?php echo _('Picture'); ?></label>
                            <input type="file" name="photo[]" class="form-control-file">
                        </div>
                    </div>
                </div>
            </div>
            <?php echo form_submit('', _('Submit'), array('class' => 'btn btn-lg btn-primary btn-block')); ?>
            <?php echo form_close(); ?>
        </div>
    </div>
</div>

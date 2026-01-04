<?php
if (empty($data['content'])) {
    $form_url = 'branches/add';
    $form_id = 'add_branch_form';
    $form_submit = _('Submit');
} else {
    $form_url = 'branches/edit/' . $data['content']['id'];
    $form_id = 'edit_branch_form';
    $form_submit = _('Edit');
}
?>
<?php echo form_open_multipart($form_url, array('id' => $form_id, 'class' => 'branch-form'), array('center_id' => 2)); ?>
<div class="card branch_content_section">
    <?php if ($this->session->userdata('role_id') == 1): ?>
    <div class="card-header">
        <ul class="nav nav-pills card-header-pills">
            <li class="nav-item"><a
                        class="nav-link<?php if ($this->session->userdata('branch_open')): ?><?php if ($this->session->userdata('branch_open') == 'default'): ?> active<?php endif; ?><?php else: ?> active<?php endif; ?>"
                        href="#"><?php echo _('Default Info'); ?></a></li>
            <li class="nav-item"><a
                        class="nav-link<?php if ($this->session->userdata('branch_open')): ?><?php if ($this->session->userdata('branch_open') == 'access'): ?> active<?php endif; ?><?php endif; ?>"
                        href="#"><?php echo _('Access Controller Info'); ?></a></li>
        </ul>
    </div>
    <div class="card-body">
        <div class="card-block"<?php if ($this->session->userdata('branch_open')): ?><?php if ($this->session->userdata('branch_open') != 'default'): ?> style="display:none"<?php endif; ?><?php endif; ?>>
            <?php else : ?>
            <div class="card-body">
                <?php endif; ?>
                <div class="form-row">
                    <div class="form-group col-12">
                        <label for="b_title"><?php echo _('Title'); ?></label>
                        <?php

                        $value = set_value('title');

                        if (!$value) {
                            if (isset($data['content']['title'])) {
                                $value = $data['content']['title'];
                            }
                        }

                        echo form_input(array(
                            'name' => 'title',
                            'id' => 'b_title',
                            'value' => $value,
                            'class' => 'form-control',
                        ));
                        ?>
                    </div>
                    <div class="form-group col-12">
                        <label for="b_description"><?php echo _('Description'); ?>(앱에서표시됨)</label>
                        <?php

                        $description_value = set_value('description');

                        if (!$description_value) {
                            if (isset($data['content']['description'])) {
                                $description_value = $data['content']['description'];
                            }
                        }

                        echo form_input(array(
                            'name' => 'description',
                            'id' => 'b_description',
                            'value' => $description_value,
                            'class' => 'form-control',
                        ));
                        ?>
                    </div>
                    <div class="form-group col-12">
                        <label for="b_phone"><?php echo _('Phone'); ?></label>
                        <?php

                        $phone_value = set_value('phone');

                        if (!$phone_value) {
                            if (isset($data['content']['phone'])) {
                                $phone_value = get_hyphen_phone($data['content']['phone']);
                            }
                        }

                        echo form_input(array(
                            'name' => 'phone',
                            'id' => 'b_phone',
                            'value' => $phone_value,
                            'class' => 'form-control',
                        ));
                        ?>
                    </div>
                    <div class="form-group col-6">
                        <label for="b_app_title_color"><?php echo _('App Title Color'); ?></label>
                        <?php

                        $app_title_color_value = set_value('app_title_color');

                        if (!$app_title_color_value) {
                            if (isset($data['content']['app_title_color'])) {
                                $app_title_color_value = $data['content']['app_title_color'];
                            } else {
                                $app_title_color_value = '#333333';
                            }
                        }

                        echo form_input(array(
                            'type' => 'color',
                            'name' => 'app_title_color',
                            'id' => 'b_app_title_color',
                            'value' => $app_title_color_value,
                            'class' => 'form-control',
                        ));
                        ?>
                    </div>
                    <div class="form-group col-6">
                        <label for="b_app_notice_color"><?php echo _('App Notice Color'); ?></label>
                        <?php

                        $app_notice_color_value = set_value('app_notice_color');

                        if (!$app_notice_color_value) {
                            if (isset($data['content']['app_notice_color'])) {
                                $app_notice_color_value = $data['content']['app_notice_color'];
                            } else {
                                $app_notice_color_value = '#666666';
                            }
                        }

                        echo form_input(array(
                            'type' => 'color',
                            'name' => 'app_notice_color',
                            'id' => 'b_app_notice_color',
                            'value' => $app_notice_color_value,
                            'class' => 'form-control',
                        ));
                        ?>
                    </div>

                    <div class="form-group col-12">
                        <label for="b_picture"><?php echo _('Image'); ?></label>
                        <?php

                        echo form_upload(array(
                            'name' => 'photo[]',
                            'id' => 'b_picture',
                            'class' => 'form-control-file',
                        ));
                        ?>
                    </div>
                </div>
            </div>
            <div class="card-block"<?php if ($this->session->userdata('branch_open')): ?><?php if ($this->session->userdata('branch_open') != 'access'): ?> style="display:none"<?php endif; ?><?php else: ?> style="display:none"<?php endif; ?>>
                <div class="form-row">
                    <div class="form-group col-12 col-xl-6 col-xxl-4">
                        <label for="b_use_ac_controller"><?php echo _('Use Access Control'); ?></label>
                        <?php
                        $options = array('0' => _('Not Use'), '1' => _('Use'));
                        $use_ac_select = set_value('use_ac_controller');

                        if (!$use_ac_select) {
                            if (isset($data['content']['use_ac_controller'])) {
                                $use_ac_select = $data['content']['use_ac_controller'];
                            } else {
                                $use_ac_select = '0';
                            }
                        }

                        echo form_dropdown('use_ac_controller', $options, $use_ac_select, array('id' => 'b_use_ac_controller', 'class' => 'form-control'));
                        ?>
                    </div>
                </div>
                <div class="form-row ac_contoller"<?php if ($use_ac_select == '0'): ?> style="display:none"<?php endif; ?>>
                    <div class="form-group col-12 col-xl-6 col-xxl-4">
                        <label for="b_use_access_card"><?php echo _('Use Access Card'); ?></label>
                        <?php
                        $options = array('0' => _('Not Use'), '1' => _('Use'));
                        $select = set_value('use_access_card');

                        if (!$select) {
                            if (isset($data['content']['use_access_card'])) {
                                $select = $data['content']['use_access_card'];
                            } else {
                                $select = '0';
                            }
                        }
                        echo form_dropdown('use_access_card', $options, $select, array('id' => 'b_use_access_card', 'class' => 'form-control'));
                        ?>
                    </div>
                </div>

                <div class="form-row ac_contoller"<?php if ($use_ac_select == '0'): ?> style="display:none"<?php endif; ?>>
                    <div class="form-group col-12 col-xl-6 col-xxl-4">
                        <label for="b_use_admin_ac"><?php echo _('Enable employee withdrawal'); ?></label>
                        <?php
                        $options = array('0' => _('Not Use'), '1' => _('Use'));
                        $select = set_value('use_admin_ac');

                        if (!$select) {
                            if (isset($data['content']['use_admin_ac'])) {
                                $select = $data['content']['use_admin_ac'];
                            } else {
                                $select = '0';
                            }
                        }
                        echo form_dropdown('use_admin_ac', $options, $select, array('id' => 'b_use_admin_ac', 'class' => 'form-control'));
                        ?>
                    </div>
                </div>
            </div>
        </div>


    </div>
    <?php echo form_submit('', $form_submit, array('class' => 'btn btn-primary btn-block btn-lg')); ?>
    <?php echo form_close(); ?>

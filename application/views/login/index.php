<div class="bg_login">
	<div class="login_wrap">
		<h1><?php echo _('System Description'); ?></h1>
		<p class="main_text"><?php echo _('Welcome This ERP System'); ?></p>
		<?php echo $Layout->Element('message'); ?>
		<?php echo form_open('', array('id' => 'login_form', 'class' => 'form_box')); ?>
			<fieldset>
				<legend><?php echo _('Login'); ?></legend>
					<div class="form-group">
						<?php
                            echo form_input(array(
                                'id' => 'login_id',
                                'name' => 'id',
                                'value' => set_value('id',$this->input->get('id')),
                                'placeholder' => _('Insert Login ID'),
                                'class' => 'form-control',
                                'required' => 'required',
                            ));
                        ?>
					</div>
					<div class="form-group">
						<?php
                            echo form_input(array(
                                'type' => 'password',
                                'id' => 'login_password',
                                'name' => 'pwd',
                                'value' => set_value('pwd'),
                                'placeholder' => _('Insert Login Password'),
                                'class' => 'form-control',
                                'required' => 'required',
                            ));
                        ?>
					</div>
					<?php echo form_submit('', _('Login'), array('class' => 'btn_login')); ?>
			</fieldset>
		<?php echo form_close(); ?>
	</div>
</div>

<?php echo form_open('', array('method' => 'get', 'id' => 'search_field_form', 'class' => 'search_form col-12')); ?>
<input type="hidden" name="search_type" value="field"/>
<div class="row">
    <div class="col-12 form-group form-inline">
        <label for="s_type"><?php echo _('Check classification'); ?></label>
        <?php
        $options = array('name' => _('User Name'), 'card_no' => _('Access Card No'), 'phone' => _('Phone'), 'birthday' => _('Birthday'), 'visit_route'=>_('Visit Route') ,'company' => _('Company'));
        echo form_dropdown('search_field', $options, set_value('search_field'), array('id' => 's_type', 'class' => 'form-control', 'style' => 'margin-left:20px'));
        ?>
        <div class="input-group"<?php if ($this->input->get('search_field') == 'birthday'): ?> style="margin-left:50px;display:none"<?php else: ?> style="margin-left:50px"<?php endif; ?>>
            <?php echo form_input(array('type' => 'search', 'name' => 'search_word', 'value' => set_value('search_word'), 'placeHolder' => _('Search Word'), 'class' => 'form-control')); ?>
            <span class="input-group-btn">
          <?php echo form_submit('', _('Search'), array('class' => 'btn btn-primary')); ?>
        </span>
        </div>
        &nbsp;&nbsp;&nbsp;<label><input type="checkbox" name="show_only_my_user" value="1"<?php echo $see_only_checked; ?> /><?php echo _('See only my members'); ?>
        </label>
        <?php if ($search_data['search']): ?>
            <?php anchor('/search?type=field', _('Turn off Search')); ?>
        <?php endif; ?>
    </div>
    <div id="field_period_form" class="col-12 col-lg-8 form-group"<?php if ($this->input->get('search_field') != 'birthday'): ?> style="display:none"<?php endif; ?>>
        <div class="form-row">
            <div class="col-6">
            <label for="start_date"><?php echo _('Birthday'); ?></label>
            <?php 
                $value_start_birthday = set_value('start_birthday');
                $value_end_birthday = set_value('end_birthday');            
            ?>
            <div class="input-group-prepend date">
            <?php 
                echo form_input(array(
                'name' => 'start_birthday',
                'id' => 's_birthday',
                'value' => $value_start_birthday,
                'class' => 'form-control birthday-datepicker',
                'style'=>'width:150px'
                )); 
            ?>
            <span> ~ </span>
             <?php 
                echo form_input(array(
                'name' => 'end_birthday',
                'id' => 'e_birthday',
                'value' => $value_end_birthday,
                'class' => 'form-control birthday-datepicker',
                'style'=>'width:150px'
                )); 
            ?>
            <div class="input-group-text">
                <span class="material-icons">date_range</span>
            </div>
            </div>
            </div>
        </div>
    </div>
    <div class="col-12" id="birthday_search"<?php if ($this->input->get('search_field') != 'birthday'): ?> style="display:none"<?php endif; ?>>
        <?php echo form_submit('', _('Search'), array('class' => 'btn btn-primary')); ?>
    </div>
</div>
<?php echo form_close(); ?>

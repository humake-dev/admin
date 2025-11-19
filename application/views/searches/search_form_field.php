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


    <div id="field_period_form" class="col-12 col-lg-8"<?php if ($this->input->get('search_field') != 'birthday'): ?> style="display:none"<?php endif; ?>>


<div class="form-group">
  <label><?php echo _('Birthday Search Type'); ?></label>
  <div class="form-row">
    <div class="col-12">

    <?php 
$selected = $this->input->get('birthday_search_type');

?>
<div class="form-check form-check-inline">
  <label class="form-check-label">
    <input type="radio"
           name="birthday_search_type"
           id="type_custom_period_search"
           value="custom_period_search"
           class="form-check-input"
           <?= ($selected === null || $selected === 'custom_period_search') ? 'checked' : '' ?>>           
    <?= _('Custom Search'); ?>
  </label>
</div>

<div class="form-check form-check-inline">
  <label class="form-check-label">
    <input type="radio"
           name="birthday_search_type"
           id="type_birthday_year"
           value="birthday_year"
           class="form-check-input"
           <?= ($selected === 'birthday_year') ? 'checked' : '' ?>>
    <?= _('Year Search'); ?>
  </label>
</div>

<div class="form-check form-check-inline">
  <label class="form-check-label">
    <input type="radio"
           name="birthday_search_type"
           id="type_birthday_month"
           value="birthday_month"
           class="form-check-input"
           <?= ($selected === 'birthday_month') ? 'checked' : '' ?>>
    <?= _('Month Search'); ?>
  </label>
</div>


      
    </div>
  </div>
</div>


        <div class="form-row">
            <div class="col-6">
            <label for="start_date"><?php echo _('Birthday'); ?></label>
            <?php 
                $value_start_birthday = set_value('start_birthday');
                $value_end_birthday = set_value('end_birthday');
                $value_birthday_year = set_value('birthday_year',$this->input->get('birthday_year'));
                $value_birthday_month = set_value('birthday_month',$this->input->get('birthday_month'));
                $value_include_year = set_value('include_year',$this->input->get('include_year'));
            ?>
            <div id="birthday_custom_serach_input" class="input-group-prepend date">
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
            <label><input type="checkbox" name="include_year" value="1" <?php if(!empty($value_include_year)): ?>checked<?php endif ?>><?php echo _('Search Include Year') ?></label>
            </div>


            <div id="birthday_year_serach_input" style="display:none">
             <?php 
                echo form_input(array(
                'name' => 'birthday_year',
                'id' => 'yearpicker',
                'value' => $value_birthday_year,
                'class' => 'form-control',
                'style'=>'width:150px',
                'placeholder'=>'년도 선택'
                )); 
            ?>
            </div>

            <div id="birthday_month_serach_input" style="display:none">
             <?php 
                echo form_input(array(
                'name' => 'birthday_month',
                'id' => 'schMonth',
                'value' => $value_birthday_month,
                'class' => 'form-control',
                'style'=>'width:150px',
                'placeholder'=>'월 선택'
                )); 
            ?>
            </div>

            </div>
        </div>
    </div>
    <div class="col-12" id="birthday_search"<?php if ($this->input->get('search_field') != 'birthday'): ?> style="display:none"<?php endif; ?>>
        <?php echo form_submit('', _('Search'), array('class' => 'btn btn-primary')); ?>
    </div>
</div>
<?php echo form_close(); ?>
<style>
    .ui-datepicker-header {display:none}

</style>
<?php echo form_open('', array('method' => 'get', 'id' => 'search_default_form', 'class' => 'search_form col-12')); ?>
<input type="hidden" id="future_search" value="1">
<div class="row">
    <div class="form-group col-12 col-lg-4 col-xl-3">
        <label for="s_user_type"><?php echo _('Order Type'); ?></label>
        <?php

        if (empty($data['is_pt_product'])) {
            $user_options = array('all' => _('All User'), 'default' => _('Default User'), 'free' => _('Free User'));
        } else {
            $user_options = array('all' => _('All User'), 'default' => _('Valid User'), 'free' => _('Expire User'));
        }

        $user_type_select = set_value('user_type', 'all');
        echo form_dropdown('user_type', $user_options, $user_type_select, array('id' => 's_user_type', 'class' => 'form-control'));

        if (!empty($data['is_pt_product'])) {
            echo form_input(array(
                'type' => 'hidden',
                'id' => 'is_pt_product',
                'value' => 1,
            ));
        }
        ?>
    </div>
    <div class="form-group col-12 col-lg-4 col-xl-3">
        <label id="s_employee_label" for="s_employee">
            <?php

            $employee_title = _('Manager');

            if ($this->input->get('fc')) {
                $employee_title = _('FC');
            } else {
              if($this->input->get('fc')=='0') {
                $employee_title = _('FC');
              }
            }

            if ($this->input->get('trainer')) {
                $employee_title = _('Trainer');
            } else {
                if($this->input->get('trainer')=='0') {
                    $employee_title = _('Trainer');
                  }
            }

            echo $employee_title;
            ?>
        </label>
        <?php if ($this->Acl->has_permission('employees')): ?>
            <input type="hidden" value="fc" class="default_position">
            <div class="input-group-prepend select-employee" style="width:180px">
                <?php
                $employee_name_value = set_value('employee_name');

                echo form_input(array(
                    'name' => 'employee_name',
                    'id' => 's_employee',
                    'value' => $employee_name_value,
                    'maxlength' => '60',
                    'size' => '60',
                    'readonly' => 'readonly',
                    'class' => 'form-control',
                ));

                $employee_fc_id_value = set_value('fc');
                echo form_input(array('type' => 'hidden', 'id' => 'e_employee_fc_id', 'name' => 'fc', 'value' => $employee_fc_id_value));

                $employee_trainer_id_value = set_value('trainer');
                echo form_input(array('type' => 'hidden', 'id' => 'e_employee_trainer_id', 'name' => 'trainer', 'value' => $employee_trainer_id_value));

                if($employee_fc_id_value=='0') {
                    $employee_fc_id_null=true;
                }

                if($employee_trainer_id_value=='0') {
                    $employee_trainer_id_null=true;
                }                  

                ?>
                <div class="input-group-text">
                    <span class="material-icons">account_box</span>
                </div>
                <span id="clear_employee"
                      class="material-icons" style="cursor:pointer;<?php if (empty($employee_fc_id_value) and empty($employee_trainer_id_value) and empty($employee_fc_id_null) and empty($employee_trainer_id_null)): ?>display:none<?php endif; ?>">clear</span>
            </div>
        <?php else: ?><br/>
            <label><input type="checkbox" name="show_only_my_user"
                          value="1"<?php echo $see_only_checked; ?> /><?php echo _('See only my members'); ?></label>
        <?php endif; ?>

    </div>
    <div class="col-12">

    </div>
    <?php if (($search_data['course_category']['total'] and $search_data['course']['total']) and $search_data['facility']['total']): ?>
        <?php include __DIR__ . DIRECTORY_SEPARATOR . 'course_select.php'; ?>
        <?php include __DIR__ . DIRECTORY_SEPARATOR . 'facility_select.php'; ?>
    <?php else :
        $both = false;
        ?>
        <?php if ($search_data['course_category']['total'] and $search_data['course']['total']): ?>
        <?php include __DIR__ . DIRECTORY_SEPARATOR . 'course_select.php'; ?>
    <?php endif; ?>

        <?php if ($search_data['facility']['total']): ?>
        <?php include __DIR__ . DIRECTORY_SEPARATOR . 'facility_select.php'; ?>
    <?php endif; ?>
    <?php endif; ?>

    <div id="fg_payment_status"
         class="col-12 col-md-6 col-lg-4 col-xl-3 form-group available_search"<?php if ($user_type_select == 'free' and empty($data['is_pt_product'])): ?> style="display:none"<?php endif; ?>>
        <label for="s_payment_id"><?php echo _('By payment status'); ?></label>
        <?php
        $p_options = array('' => _('All'), 'status3' => _('Refund'), 'status4' => _('Pay For Cash'), 'status5' => _('Pay For Credit'));

        $select = set_value('payment_id');

        if (!$select) {
            if (isset($search_data['payment_id'])) {
                $select = $search_data['payment_id'];
            }
        }
        echo form_dropdown('payment_id', $p_options, $select, array('id' => 's_payment_id', 'class' => 'form-control'));
        ?>
    </div>
    <div class="col-12 col-md-6 col-lg-4 col-xl-3 form-group">
        <label for="s_search_status"><?php echo _('By Status'); ?></label>
        <?php
        $options = array('' => _('All'), 'status1' => '신규등록', 'status2' => '재등록', 'status3' => _('Stop Order Start'), 'status4' => _('Stop Order End'), 'status5' => _('Attendance'), 'status6' => _('Not Attendance'));

        if ($search_data['course_category']['total'] and $search_data['course']['total']) {
            $options = array_merge($options, array('status7' => _('Enroll Start'), 'status8' => _('Enroll Finish')));
        }

        if ($search_data['facility']['total']) {
            $options = array_merge($options, array('status9' => _('Rent Start'), 'status10' => _('Rent Finish')));
        }

        $options = array_merge($options, array('status11' => '양도', 'status12' => '양수', 'status13' => _('Transaction Date')));

        echo form_dropdown('search_status', $options, set_value('search_status'), array('id' => 's_search_status', 'class' => 'form-control'));
        ?>
    </div>
    <?php
    $reference_no_display = true;

    if ($this->input->get('search_status') == '') {
        if (!empty($search_data['product_id'])) {
            if ($search_data['er_type'] != 'pt') {
                $reference_no_display = false;
            }
        }
    }

    ?>
</div>
<div class="form-row">
    <div id="fg_reference_date"
         class="col-12 col-lg-4 col-xl-3 form-group"<?php if ($reference_no_display): ?> style="display:none"<?php endif; ?>>
        <label for="s_reference_date"><?php echo _('Reference Date'); ?></label>
        <?php

        $value_start_date = set_value('reference_date', $search_data['date']);

        ?>
        <div class="input-group-prepend date">
            <?php echo form_input(array(
                'name' => 'reference_date',
                'id' => 's_reference_date',
                'value' => $value_start_date,
                'class' => 'form-control datepicker',
            )); ?>
            <div class="input-group-text">
                <span class="material-icons">date_range</span>
            </div>
        </div>
    </div>
    <div id="default_period_form"
         class="col-12 col-lg-8 form-group"<?php if (!empty($search_data['period_display_none'])): ?> style="display:none"<?php endif; ?>>
        <label for="start_date"><?php echo _('Check Period'); ?></label>
        <div class="form-row">
            <?php echo $Layout->Element('search_period'); ?>
        </div>
    </div>
    <div class="col-12">
        <?php echo form_submit('', _('Search'), array('class' => 'btn btn-primary')); ?>
    </div>
    <?php if ($search_data['search']): ?>
        <?php anchor('/search', '검색조건 해제'); ?>
    <?php endif; ?>
</div>
<?php echo form_close(); ?>

<article class="row">
    <h3 class="col-12"><?php echo _('Facility Info'); ?></h3>
    <div class="col-12">
        <div class="card course_info">
            <div class="card-body">
                <?php

                $default_user_id = null;

                if (!empty($data['user_content'])) {
                    $default_user_id = $data['user_content']['id'];
                }

                $value_user_id = set_value('user_id', $default_user_id);

                echo form_input(array(
                    'type' => 'hidden',
                    'id' => 'o_user_id',
                    'name' => 'user_id',
                    'value' => $value_user_id,
                ));
                ?>
                <div class="form-row">
                    <div class="col-12 form-group">
                        <label for="r_facility_id"><?php echo _('Facility'); ?></label>
                        <?php
                        $facility_select = set_value('facility_id', $this->input->post_get('facility_id'));

                        if (!$facility_select) {
                            if (isset($data['content']['facility_id'])) {
                                $facility_select = $data['content']['facility_id'];
                            }
                        }

                        ?>
                        <select id="r_facility_id" name="facility_id" class="form-control" required="required">
                            <?php if ($data['facility']['total']): ?>
                                <option value=""><?php echo _('Select Facility'); ?></option>
                                <?php foreach ($data['facility']['list'] as $facility): ?>
                                    <option value="<?php echo $facility['id']; ?>"
                                            data-price="<?php echo $facility['price']; ?>"<?php if ($facility_select == $facility['id']): ?> selected="selected"<?php endif; ?>><?php echo $facility['title']; ?></option>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </select>
                    </div>
                </div>
            </div>
        </div>
</article>
<div class="form-row">
    <?php

    echo form_input(array('type' => 'hidden', 'id' => 'o_today', 'value' => $search_data['today']));
    echo form_input(array('type' => 'hidden', 'id' => 'f_facility_type', 'value' => 'month'));

    if (!empty($data['content']['order_id'])) {
        echo form_input(array('type' => 'hidden', 'id' => 'o_order_id', 'value' => $data['content']['order_id']));
    }

    if ($this->input->post_get('facility_id')) {
        $default_facility_id = $this->input->post_get('facility_id');
    } else {
        $default_facility_id = null;
    }

    if ($this->input->post_get('no')) {
        $default_no = $this->input->post_get('no');
    } else {
        $default_no = null;
    }

    if ($this->input->post_get('after') or $this->input->post_get('re_order')) {
        $reorder_value = 1;
    } else {
        if (empty($data['content']['re_order'])) {
            $reorder_value = 0;
        } else {
            $reorder_value = $data['content']['re_order'];
        }
    }

    echo form_input(array('type' => 'hidden', 'id' => 'default_facility_id', 'value' => $default_facility_id));
    echo form_input(array('type' => 'hidden', 'id' => 'default_no', 'value' => $default_no));
    echo form_input(array('type' => 'hidden', 'id' => 're-rent', 'name' => 're_order', 'value' => $reorder_value));
    ?>
    <article class="col-12 col-xl-6 col-xxl-5">
        <h3><?php echo _('Rent Default Info'); ?></h3>
        <div class="card">
            <div class="card-body">
                <div class="form-row">
                    <?php echo form_input(array('type' => 'hidden', 'id' => 'r_facility_price', 'name' => 'facility_price', 'value' => $data['facility']['content']['price'])); ?>
                    <div class="col-12 form-group">
                        <label for="r_no"><?php echo _('Facility No'); ?></label>
                        <select id="r_no" name="no" class="form-control">
                            <?php
                            $select = '';
                            if ($this->input->post_get('no')) {
                                $select = $this->input->post_get('no');
                            } else {
                                if (isset($data['content']['no'])) {
                                    $select = $data['content']['no'];
                                }
                            }

                            if (empty($data['facility']['content']['use_not_set'])) {
                                echo '<option value="">' . _('Please Select') . '</option>';
                            } else {
                                echo '<option value="0">' . _('Not Set') . '</option>';
                            }

                            if ($data['facility_available_no']['total']):
                                foreach ($data['facility_available_no']['list'] as $facility_no):
                                    if ($facility_no['enable']) {
                                        if ($select == $facility_no['no']) {
                                            echo '<option value="' . $facility_no['no'] . '" selected="selected">' . $facility_no['no'] . '</option>';
                                        } else {
                                            echo '<option value="' . $facility_no['no'] . '">' . $facility_no['no'] . '</option>';
                                        }
                                    } else {
                                        if ($select == $facility_no['no']) {
                                            if (empty($data['content'])) {
                                                echo '<option value="' . $facility_no['no'] . '" selected="selected">' . $facility_no['no'] . '</option>';
                                            } else {
                                                echo '<option value="' . $facility_no['no'] . '" selected="selected">' . $facility_no['no'] . ' / ' . _('Current Use No') . '</option>';
                                            }
                                        } else {
                                            echo '<option value="' . $facility_no['no'] . '" disabled="disabled">' . $facility_no['no'] . '(' . $facility_no['disable'] . ')</option>';
                                        }
                                    }
                                endforeach;
                            endif;
                            ?>
                        </select>
                    </div>
                    <div id="select_month"
                         class="col-12 form-group">
                        <label for="r_rent_month"><?php echo _('Facility Rent Month'); ?></label>
                        <?php

                        $option = array('' => _('Please Select'));

                        if (isset($data['content']['insert_quantity'])) {
                            $default_day_select = $data['content']['insert_quantity'];
                        } else {
                            $default_day_select = 1;
                        }

                        $month_select = set_value('rent_month', $default_day_select);
                        foreach (range(1, 200) as $value) {
                            $option[$value] = $value . _('Period Month');
                        }
                        
                        echo form_dropdown('rent_month', $option, $month_select, array('id' => 'r_rent_month', 'class' => 'r_rent_period form-control', 'required' => 'required'));

                        ?>
                    </div>
                    <div class="col-12 form-group period-day">
                        <label for="o_start_date"><?php echo _('Start Date'); ?></label>
                        <div class="input-group-prepend date">
                            <?php

                            if (isset($data['content']['start_date'])) {
                                $default_start_date = $data['content']['start_date'];
                            } else {
                                $default_start_date = $search_data['date'];
                            }
                            $value_start_date = set_value('start_date', $default_start_date);

                            echo form_input(array(
                                'name' => 'start_date',
                                'id' => 'o_start_date',
                                'value' => $value_start_date,
                                'class' => 'form-control rent_datepicker',
                            ));

                            ?>
                            <div class="input-group-text">
                                <span class="material-icons">date_range</span>
                            </div>
                        </div>
                    </div>
                    <div class="col-12 form-group period-day">
                        <label for="o_end_date"><?php echo _('End Date'); ?></label>
                        <div class="input-group-prepend date">
                            <?php

                            if (isset($data['content']['end_date'])) {
                                $default_end_date = $data['content']['end_date'];
                            } else {
                                $default_end_date_obj = new DateTime($search_data['date'], $search_data['timezone']);
                                $default_end_date_obj->modify('+' . $month_select . ' Month');
                                $default_end_date_obj->modify('-1 Day');
                                $default_end_date = $default_end_date_obj->format('Y-m-d');
                            }

                            $value_end_date = set_value('end_date', $default_end_date);

                            echo form_input(array(
                                'name' => 'end_date',
                                'id' => 'o_end_date',
                                'value' => $value_end_date,
                                'class' => 'form-control rent_datepicker',
                            ));
                            ?>
                            <div class="input-group-text">
                                <span class="material-icons">date_range</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </article>
    <?php
    echo $Layout->Element('form_account');
    if ($this->router->fetch_method() == 'add') {
        echo $Layout->Element('form_option.php');
    }
    ?>
</div>
<?php echo $Layout->Element('form_memo'); ?>
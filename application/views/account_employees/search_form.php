<?php echo form_open('', array('method' => 'get', 'class' => 'search_form card')) ?>
<div class="card-body">
    <?php
    if (in_array($this->router->fetch_class(), array('account_employees', 'trainer_actives'))):
        if ($this->router->fetch_method() == 'view'):
            ?>
            <div class="col-12 col-md-8 col-lg-10 form-group">
                <label for="start_date"><?php echo _('Employee') ?></label>
                <p>
                    <?php echo $data['content']['name'] ?>
                </p>
            </div>
        <?php else:
            $employee_id_value = set_value('employee_id');

            if ($this->input->get('employee_id')) {
                $employee_id_value = $data['content']['id'];
            }

            echo form_input(array('type' => 'hidden', 'id' => 'e_employee_id', 'name' => 'employee_id', 'value' => $employee_id_value));
            ?>
            <div class="col-12 col-md-4 col-lg-2 form-group">
                <label for="e_name"><?php echo _('Select Employee') ?></label>
                <?php
                $employee_value = set_value('employee_name', _('All'));

                if ($this->input->get('employee_id')) {
                    $employee_value = $data['content']['name'];
                }

                ?>
                <div class="input-group-prepend select-employee">
                    <?php
                    echo form_input(array(
                        'name' => 'employee_name',
                        'id' => 'e_name',
                        'value' => $employee_value,
                        'maxlength' => '60',
                        'size' => '60',
                        'readonly' => 'readonly',
                        'required' => 'required',
                        'class' => 'form-control'
                    ));
                    ?>
                    <div class="input-group-text">
                        <span class="material-icons">account_box</span>
                    </div>
                </div>
            </div>
        <?php
        endif;
    endif;
    ?>
    <div id="default_period_form" class="col-12 form-group">
        <label for="start_date"><?php echo _('Transaction Date') ?></label>
        <div class="form-row">
            <?php echo $Layout->Element('search_period') ?>
        </div>
    </div>
    <div class="col-12 form-group">
        <?php echo form_submit('', _('Search'), array('class' => 'btn btn-primary')) ?>
    </div>
</div>
<?php echo form_close() ?>

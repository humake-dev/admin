<?php echo form_open('', array('method' => 'get', 'class' => 'search_form card')); ?>
<?php echo form_input(array('type' => 'hidden', 'id' => 'e_employee_id', 'name' => 'employee_id', 'value' => set_value('employee_id'))); ?>
<div class="card-body">
    <div class="row">
        <div class="col-12 col-md-6">
            <div class="form-row">
                <div class="col-12 col-lg-4 form-group">
                    <div class="input-group-prepend date">
                        <input type="text" name="date" class="form-control datepicker"
                               value="<?php echo $search_data['date']; ?>">
                        <div class="input-group-text">
                            <span class="material-icons">date_range</span>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-lg-4 form-group">
                    <?php echo form_submit('', _('Search'), array('class' => 'btn btn-primary')); ?>
                </div>
            </div>
        </div>
        <div class="col-12 col-md-6">
            합계 : <?php echo number_format($data['sum']); ?><?php echo _('Currency'); ?>
        </div>
    </div>
</div>
<?php echo form_close(); ?>

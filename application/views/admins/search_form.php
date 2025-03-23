<?php echo form_open('', ['method' => 'get', 'class' => 'search_form card']); ?>
<?php
if (in_array($this->input->get('type'), array('user', 'pt', 'commission'))) {
    echo form_input(array(
        'type' => 'hidden',
        'name' => 'type',
        'value' => $this->input->get('type')
    ));
}

?>
<div class="card-body">
    <div id="default_period_form" class="col-12 form-group">
        <label for="start_date"><?php echo _('Transaction Date'); ?></label>
        <div class="form-row">
            <?php echo $Layout->Element('search_period'); ?>
        </div>
    </div>
    <div class="col-12 form-group">
        <?php echo form_submit('', _('Search'), ['class' => 'btn btn-primary']); ?>
    </div>
</div>
<?php echo form_close(); ?>

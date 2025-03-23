<?php echo form_open('', array('method'=>'get','class'=>'search_form card')) ?>
<div class="card-body">
    <?php if(!empty($data['content'])): ?>
    <div class="col-12 form-group">
        <label><?php echo _('Trainer') ?></label>
        <p class="form-text"><?php echo $data['content']['name'] ?></p>
    </div>
    <?php endif ?>
    <div id="default_period_form" class="col-12 form-group">
        <label for="start_date"><?php echo _('Execute Date') ?></label>
        <div class="form-row">
            <?php echo $Layout->Element('search_period') ?>
        </div>
    </div>
    <?php if($this->session->userdata('role_id')<=2): ?>
    <div class="col-12 form-group">
        <label for="search_zero_commission_too"><?php echo _('Search Zero Commission Too') ?></label>
        <input id="search_zero_commission_too" type="checkbox" name="search_zero_commission_too"
               value="1"<?php if ($this->input->get('search_zero_commission_too')): ?> checked="checked"<?php endif; ?>>
    </div>
    <?php endif ?>
    <div class="col-12 form-group">
        <?php echo form_submit('', _('Search'), array('class'=>'btn btn-primary')) ?>
    </div>
</div>
<?php echo form_close() ?>

<div class="row">
<div class="col-12">
<?php echo form_open('', array('method' => 'get', 'class' => 'search_form card')); ?>
<div class="card-body">
  <div id="default_period_form" class="col-12 form-group">
    <label for="start_date"><?php echo _('Transaction Date'); ?></label>
    <div class="form-row">
      <?php echo $Layout->Element('search_period'); ?>
    </div>
  </div>
  <div class="col-12 form-group">
    <?php echo form_submit('', _('Search'), array('class' => 'btn btn-primary')); ?>
  </div>
</div>
<?php echo form_close(); ?>
</div>
</div>

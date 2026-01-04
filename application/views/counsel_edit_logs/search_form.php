<div class="row">
  <div class="col-12">
    <div id="search_form" class="card">
      <div class="card-header">
        <h3><?php echo _('Search') ?></h3>
      </div>
      <?php echo form_open('', array('method'=>'get','class'=>'card-body')) ?>
        <div class="row">



            <div class="col-12 form-group">
                <label for="start_date"><?php echo _('Updated At') ?></label>            
                <div class="form-row">
                    <?php echo $Layout->Element('search_period') ?>
                </div>
            </div>
            <div class="col-12">
                <?php echo form_submit('', _('Search'), array('class'=>'btn btn-primary')) ?>
            </div>
          </div>
        <?php echo form_close(); ?>
    </div>        
  </div>        
</div>
<div id="view_message" class="container">
  <div class="row justify-content-center">
    <div class="col-12 col-lg-6">
      <div class="card border-warning">
        <h3 class="card-header bg-warning text-light"><?php echo _('Confirm Reuse'); ?></h3>
        <div class="card-body">
          <div class="col-12">
            <?php echo _('Are you sure you want to reuse and delete trans-log?'); ?>
          </div>
        </div>
        <div class="card-footer">
          <div class="col-12">
            <?php echo form_open($this->router->fetch_class().'/'.'delete/'.$data['id']); ?>
            <?php echo form_submit('', _('Re Use'), array('class' => 'btn btn-warning')); ?>
            <?php echo form_close(); ?>
          </div>
        </div>        
      </div>
    </div>
  </div>
</div>

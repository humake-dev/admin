<div id="view_message" class="container">
  <div class="row justify-content-center">
    <div class="col-12 col-lg-6">
      <div class="card border-danger">
        <h3 class="card-header bg-danger text-light">
        <?php if($data['content']['stop_end_date']>$search_data['today']): ?>  
        <?php echo _('Confirm Cancel'); ?>
        <?php $submit_text=_('Cancel'); ?>
        <?php else: ?>
        <?php echo _('Confirm Delete'); ?>
        <?php $submit_text=_('Delete'); ?>
        <?php endif ?>
      </h3>
        <div class="card-body">
          <div class="col-12">
            <?php if($data['content']['stop_end_date']>$search_data['today']): ?>          
              <?php echo _('Are you sure you want to cancel it?'); ?>
            <?php else: ?>
              <?php echo _('Are you sure you want to delete it?'); ?>
            <?php endif ?>
          </div>
        </div>
        <div class="card-footer">
          <div class="col-12">
            <?php echo form_open($this->router->fetch_class().'/'.'delete/'.$data['id']); ?>
            <?php echo form_submit('',$submit_text, array('class' => 'btn btn-danger')); ?>
            <?php echo form_close(); ?>
          </div>
        </div>        
      </div>
    </div>
  </div>
</div>

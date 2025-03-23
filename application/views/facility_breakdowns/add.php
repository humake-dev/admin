<?php if ($this->input->get('popup')): ?>
<?php echo form_open('', array('id' => 'facility_breakdown_add_form', 'class' => '')); ?>
  <div class="modal-header">
    <h2 class="modal-title"><?php echo _('Rent Add Breakdown'); ?></h2>
    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
      <span aria-hidden="true">&times;</span>
    </button>
  </div>
  <div class="modal-body">
<?php else: ?>
  <div class="container">
    <div class="row">
      <div class="col-12">
      <?php echo form_open('', array('id' => 'facility_breakdown_add_form')); ?>
<?php endif; ?>

<article class="card">
  <h3 style="text-indent:-9999px;height:1px;line-height:1px"><?php echo _('Fault Locka Information'); ?></h3>
  <div class="card-body">
    <div class="row">
    <div class="col-12 col-lg-6 form-group">
      <label><?php echo _('Facility'); ?></label>
      <input type="hidden" name="facility_id" value="<?php echo $data['content']['id']; ?>" />
      <input type="hidden" name="no" value="<?php echo $data['no']; ?>" />
      <input type="text" name="" value="<?php echo $data['content']['title']; ?>" class="form-control-plaintext"  />
    </div>
    <div class="col-12 col-lg-6 form-group">
      <label><?php echo _('Facility No'); ?></label>
      <input type="text" name="no" value="<?php echo $data['no']; ?>" class="form-control-plaintext"  />
    </div>
    <div class="col-12 form-group">
      <label><?php echo _('Description'); ?></label>
      <input type="text" name="description" value="" class="form-control" />
    </div>
  </div>
  </div>
</article>


<?php if ($this->input->get('popup')): ?>
  </div>
<div class="modal-footer">
  <?php echo form_submit('', _('Submit'), array('class' => 'btn btn-primary btn-block')); ?>
</div>
<?php echo form_close(); ?>
<?php else: ?>
  <?php echo form_submit('', _('Submit'), array('class' => 'btn btn-primary btn-block btn-lg')); ?>
  <?php echo form_close(); ?>
    </div>
  </div>
</div>
<?php endif; ?>

<?php if ($this->input->get('popup')): ?>
<?php echo form_open('', array('id' => 'enroll_end_form')); ?>
<div class="modal-header">
  <h2 class="modal-title"><?php echo _('End Order'); ?></h2>
  <button type="button" class="close" data-dismiss="modal" aria-label="Close">
    <span aria-hidden="true">&times;</span>
  </button>
</div>
<div class="modal-body">
<?php else: ?>
  <div class="container">
    <div class="row">
      <div class="col-12">
        <?php echo form_open('', array('id' => 'enroll_end_form')); ?>
<?php endif; ?>

<?php include __DIR__.DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'orders'.DIRECTORY_SEPARATOR.'end.php'; ?>

<?php

echo form_input(array(
  'type' => 'hidden',
    'name' => 'origin_end_date',
    'value' => $data['content']['end_date'],
));
?>

<?php if ($this->input->get('popup')): ?>
</div>
<div class="modal-footer">
  <?php echo form_submit('', _('End Order'), array('class' => 'btn btn-primary btn-block')); ?>
</div>
<?php echo form_close(); ?>
<script src="<?php echo $script; ?>"></script>
<?php else: ?>
  <?php echo form_submit('', _('End Order'), array('class' => 'btn btn-primary btn-block btn-lg')); ?>
      <?php echo form_close(); ?>
    </div>
  </div>
</div>
<?php endif; ?>

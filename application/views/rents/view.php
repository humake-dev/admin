<?php if ($this->input->get('popup')): ?>
<?php echo form_open('', array('class' => '')); ?>
<div class="modal-header">
  <h2 class="modal-title"><?php echo _('Rent Info'); ?></h2>
  <button type="button" class="close" data-dismiss="modal" aria-label="Close">
    <span aria-hidden="true">&times;</span>
  </button>
</div>
<div class="modal-body">
<?php else: ?>
  <div class="container">
    <div class="row">
      <div class="col-12">
      <?php echo form_open('', array('class' => '')); ?>
<?php endif; ?>

<?php include __DIR__.DIRECTORY_SEPARATOR.'content.php'; ?>

<?php if ($this->input->get('popup')): ?>
</div>
<div class="modal-footer">
  <button type="button" class="btn btn-block btn-secondary" data-dismiss="modal"><?php echo _('Close'); ?></button>
</div>
<?php if ($this->input->get('rent')): ?>
<script src="<?php echo $script; ?>"></script>
<?php endif; ?>
<?php else: ?>
</div>
</div>
<?php endif; ?>

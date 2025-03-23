<?php if ($this->input->get('popup')): ?>
<div class="modal-header">
    <h2 class="modal-title"><?php echo _('Account Edit Log') ?></h2>
    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
      <span aria-hidden="true">&times;</span>
    </button>
  </div>
  <div class="modal-body">
<?php else: ?>
<div id="order_edit_logs" class="container">
<?php endif ?>

    <?php if($this->input->get('popup')): ?>
    <?php include __DIR__.DIRECTORY_SEPARATOR.'popup_list.php' ?>
    <?php else: ?>
    <?php include __DIR__.DIRECTORY_SEPARATOR.'search_form.php' ?>    
    <?php include __DIR__.DIRECTORY_SEPARATOR.'list.php' ?>
    <?php endif ?>



<?php if ($this->input->get('popup')): ?>
</div>
<div class="modal-footer">
  <button type="button" class="btn btn-secondary" data-dismiss="modal"><?php echo _('Close') ?></button>
</div>
<?php else: ?>
    </div>
<?php endif ?>

<?php
    if ($this->input->get('popup')):
    if (empty($data['content'])) {
        $form_url = 'others/add';
    } else {
        $form_url = 'others/edit/'.$data['content']['id'];
    }
?>
<?php echo form_open($form_url, array('id' => 'other_form', 'class' => 'other_form', 'name' => 'other_form'),array('return_url'=>'/view/'.$this->input->get('user_id').'#user-account-summary')); ?>
  <div class="modal-header">
    <h2 class="modal-title"><?php echo _('Other sales registration'); ?></h2>
    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
      <span aria-hidden="true">&times;</span>
    </button>
  </div>
  <div class="modal-body">
    <?php include __DIR__.DIRECTORY_SEPARATOR.'form.php'; ?>
  </div>
  <div class="modal-footer">
    <input type="submit" class="btn btn-block btn-primary" value="<?php echo _('Submit'); ?>">
  </div>
  <?php echo form_close(); ?>
  <script src="<?php echo $script; ?>"></script>
<?php else: ?>
<div id="add_other" class="container">
  <div class="row">
    <?php echo $Layout->Element('accounts/nav'); ?>
    <?php include __DIR__.DIRECTORY_SEPARATOR.'aside.php'; ?>
    <?php include __DIR__.DIRECTORY_SEPARATOR.'list.php'; ?>
  </div>
</div>
<?php endif; ?>

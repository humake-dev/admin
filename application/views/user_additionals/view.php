<?php if ($this->input->get('popup')): 
    
    $p_title='';
    if($this->input->get('field')=='company') {
        $p_title=_('Company');
    }
    
    if($this->input->get('field')=='visit_route') {
        $p_title=_('Visit Route');
    }
?>
<div class="modal-header">
  <h2 class="modal-title"><?php echo $p_title; ?></h2>
  <button type="button" class="close" data-dismiss="modal" aria-label="Close">
    <span aria-hidden="true">&times;</span>
  </button>
</div>
<div class="modal-body">
<?php else: ?>
  <div class="container">
    <div class="row">
      <div class="col-12">
<?php endif; ?>
<?php
    if($this->input->get('field')=='company') {
        echo nl2br($data['content']['company']);
    }

    if($this->input->get('field')=='visit_route') {
        echo nl2br($data['content']['visit_route']);
    }
?>
      
<?php if ($this->input->get('popup')): ?>
  </div>
  <div class="modal-footer">
  <button type="button" class="btn btn-secondary" data-dismiss="modal"><?php echo _('Close'); ?></button>
  </div>
<?php else: ?>
    </div>
  </div>
</div>
<?php endif; ?>

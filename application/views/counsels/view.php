<?php if($this->input->get('popup')): ?>
<?php echo form_open('',array('class'=>'')) ?>
<div class="modal-header">
    <h5 class="modal-title"><?php echo _('Counsel') ?></h5>
    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
      <span aria-hidden="true">&times;</span>
    </button>
  </div>
  <div class="modal-body">
<?php else: ?>
  <div id="view-counsel" class="container">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <h3 class="card-header"><?php echo $data['content']['title'] ?></h3>
                <div class="card-body">
<?php endif ?>

<div>
  <?php echo nl2br($data['content']['content']) ?>
</div>

<?php if($this->input->get('popup')): ?>
</div>
<div class="modal-footer">
  <button type="button" class="btn btn-secondary" data-dismiss="modal"><?php echo _('Close') ?></button>
</div>
<?php else: ?>
  </div>
  </div>
        </div>
        <div class="col-12">
            <div class="sl_view_bottom">
                <?php echo anchor($this->router->fetch_class(), _('Go List'), array('class' => 'btn btn-secondary')) ?>
                <?php echo anchor($this->router->fetch_class() . '/edit/' . $data['content']['id'], _('Edit'), array('class' => 'btn btn-secondary')) ?>
                <?php echo anchor($this->router->fetch_class() . '/delete/' . $data['content']['id'], _('Delete'), array('class' => 'btn btn-danger float-right')) ?>
            </div>
        </div>
    </div>
</div>
<?php endif ?>

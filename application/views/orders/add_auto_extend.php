<?php if ($this->input->get('popup')): ?>
<?php echo form_open('', array('id' => 'delete_order_auto_extend_exception_form'), array('order_id' => $data['content']['order_id'])); ?>
<div class="modal-header">
  <h2 class="modal-title"><?php echo _('Resume Auto Extend'); ?></h2>
  <button type="button" class="close" data-dismiss="modal" aria-label="Close">
    <span aria-hidden="true">&times;</span>
  </button>
</div>
<div class="modal-body">
<?php else: ?>
  <div class="container">
    <div class="row">
      <div class="col-12">
        <?php echo form_open('', array('id' => 'delete_order_auto_extend_exception_form'), array('order_id' => $data['content']['order_id'])); ?>
<?php endif; ?>

<div class="row">
<article class="col-12">
  <h3><?php echo _('Order Info'); ?></h3>
    <div class="card">
      <div class="card-body">
        <div class="row">
          <div class="col-12 col-lg-6 form-group">
            <label><?php echo _('Product'); ?></label>
            <p>
              <?php if (!empty($data['content']['product_category_name'])): ?>
              <?php echo $data['content']['product_category_name']; ?>
              / 
              <?php endif; ?>
              <?php echo $data['content']['product_name']; ?>
            <p>
          </div>
          <div class="col-12 col-lg-6 form-group">
            <label><?php echo _('Price'); ?></label>
            <p>
            <?php if (empty($data['content']['price'])): ?>
              <?php echo _('Free'); ?>
              <?php else: ?>
              <?php echo number_format($data['content']['price']); ?><?php echo _('Currency'); ?>
              <?php endif; ?>
            <p>
          </div>
          <div class="col-12 col-lg-6 form-group">
            <label><?php echo _('Payment'); ?></label>
            <p>
              <?php if (empty($data['content']['payment'])): ?>
              <?php echo _('Free'); ?>
              <?php else: ?>
              <?php echo number_format($data['content']['payment']); ?><?php echo _('Currency'); ?>
              <?php endif; ?>
            <p>
          </div>
          <div class="col-12 col-lg-6 form-group">
            <label><?php echo _('Period'); ?></label>
            <p><?php echo get_dt_format($data['content']['start_date'], $search_data['timezone']); ?> ~ <?php echo get_dt_format($data['content']['end_date'], $search_data['timezone']); ?></p>
          </div>
        </div>
      </div>
    </div>
</article>
<article class="col-12">
  <h3><?php echo _('Resume Auto Exetend Info'); ?></h3>
  <div class="card">
    <div class="card-body">
      <div class="row">
        <div class="col-12 form-group">
          <p>
          <?php

            $data_obj = new DateTime('now', $search_data['timezone']);
            $data_obj->modify('last day of this month');

            echo sprintf(_('When You Resume Auto Extend, Order Auto Extend'));
          ?>
          </p>                   
        </div>        
      </div>
    </div>  
  </div>
</article>
      

</div>
<?php if ($this->input->get('popup')): ?>
</div>
<div class="modal-footer">
  <?php echo form_submit('', _('Resume Auto Extend'), array('class' => 'btn btn-primary btn-block')); ?>
</div>
<?php echo form_close(); ?>
<script src="<?php echo $script; ?>"></script>
<?php else: ?>
  <?php echo form_submit('', _('Resume Auto Extend'), array('class' => 'btn btn-primary btn-block btn-lg')); ?>
      <?php echo form_close(); ?>
    </div>
  </div>
</div>
<?php endif; ?>

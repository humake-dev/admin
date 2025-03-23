<section class="container">
  <div class="row">
    <div class="col-12">
      <?php echo form_open('', array('id'=>'order_test_form')) ?>

<article class="row">
  <h3 class="col-12"><?php echo _('Order') ?></h3>
  <div class="col-12">
    <div class="card">
      <div class="card-body">
        <div class="form-row">
            <div class="col-12 form-group">
                <label><?php echo _('Product') ?></label>    
                <input type="text" name="order[0][product]" value="123" readonly="readonly" class="form-control" />
            </div>
            <div class="col-12 form-group">
                <label><?php echo _('Quantity') ?></label>
                <input type="number" name="order[0][quantity]" value="1" class="form-control" />
            </div>                    
            <div class="col-12 form-group">
                <label><?php echo _('User') ?></label>
                <input type="text" name="user_id" value="<?php echo set_value('user_id') ?>" class="form-control" />        
            </div>

            <div class="col-12 form-group">
                <label><?php echo _('Transaction Date') ?></label>
                <input type="text" name="transaction_date" value="<?php echo set_value('transaction_date') ?>" class="form-control" />        
            </div>

            <div class="col-12 form-group">
                <label><?php echo _('Cash') ?></label>
                <input type="text" name="order[0][cash]" value="<?php echo set_value('cash',0) ?>" class="form-control" />        
            </div>
            <div class="col-12 form-group">
                <label><?php echo _('Credit') ?></label>
                <input type="text" name="order[0][credit]" value="<?php echo set_value('credit',0) ?>" class="form-control" />        
            </div>                       
        </div>
      </div>
    </div>
  </div>
</article>



      <?php echo form_submit('', _('Order'), array('class'=>'btn btn-primary btn-block')) ?>
      <?php echo form_close() ?>
    </div>
  </div>
</section>
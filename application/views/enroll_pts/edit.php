<div id="pt_serial" class="container">

  <div class="row">
    <div class="col-12">
      <?php echo form_open(''); ?>        
      <div class="card">
          <div class="card-body">
            <div class="form-row">
            <div class="col-12 form-group">
                  <label><?php echo _('Branch'); ?></label>
                  <p><?php echo $data['content']['branch_title']; ?></p>
            </div>
            <div class="col-6 form-group">
                  <label><?php echo _('User'); ?></label>
                  <p><?php echo $data['content']['name']; ?></p>
            </div>

            <div class="col-6 form-group">
                  <label><?php echo _('Manager'); ?></label>
                  <p><?php echo $data['content']['manager']; ?></p>
            </div> 
            <div class="col-6 form-group">
                  <label><?php echo _('Use Quantity'); ?></label>
                  <p><?php echo $data['content']['use_quantity']; ?></p>
            </div> 
            <div class="col-6 form-group">
                  <label><?php echo _('Remain Count'); ?></label>
                  <p><?php echo($data['content']['quantity'] - $data['content']['use_quantity'])._('Count Time'); ?></p>
            </div>                                                                 
              <div class="col-12 form-group">
                  <label><?php echo _('PT Serial'); ?></label>
                  <?php
                  echo form_input(array(
                        'type' => 'number',
                        'name' => 'serial',
                        'value' => $data['content']['serial'],
                        'class' => 'form-control',
                ));
                ?>
            </div>


            </div>            
        </div>
      </div>
      <?php echo form_submit('', _('Submit'), array('class' => 'btn btn-primary btn-lg btn-block')); ?>
      <?php echo form_close(); ?>      
    </div>
  </div>
</div>
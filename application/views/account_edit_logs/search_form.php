<div class="row">
  <div class="col-12">
    <div id="search_form" class="card">
      <div class="card-header">
        <h3><?php echo _('Search') ?></h3>
      </div>
      <?php echo form_open('', array('method'=>'get','class'=>'card-body')) ?>
        <div class="row">
            <div class="col-12 col-lg-4 form-group">
              <label><?php echo _('User') ?></label>
              <?php
              
                $user_options=array(''=>_('All'));

                if(!empty($data['user_list']['total'])) {
                  foreach($data['user_list']['list'] as $user) {
                    $user_options[$user['id']]=$user['name'];
                  }
                }

                $user_select=set_value('user_id', '');              

                echo form_dropdown('user_id', $user_options, $user_select, array('id'=>'oel_user_id','class'=>'form-control'));
              ?>
            </div>
            <div class="col-12 col-lg-4 form-group">
              <label><?php echo _('Product') ?></label>
              <?php
              
                $product_options=array(''=>_('All'));

                if(!empty($data['product_list']['total'])) {
                  foreach($data['product_list']['list'] as $product) {
                    $product_options[$product['id']]=$product['title'];
                  }
                }

                $product_select=set_value('product_id', '');
                echo form_dropdown('product_id', $product_options, $product_select, array('id'=>'oel_product_id','class'=>'form-control'));
              ?>
            </div>
            <?php if(!empty($data['order_list']['total'])): ?>
            <div class="col-12 col-lg-4 form-group">
            <label><?php echo _('Account') ?></label>
              <?php
              
                $order_options=array(''=>_('All'));

                if(!empty($data['order_list']['total'])) {
                  foreach($data['order_list']['list'] as $order) {
                    $order_options[$order['id']]=$order['product_name'].' / '.$order['start_date'].'~'.$order['end_date'];
                  }
                }

                $order_select=set_value('order_id', '');
                echo form_dropdown('order_id', $order_options, $order_select, array('id'=>'oel_order_id','class'=>'form-control'));
              ?>
            </div>
            <?php endif ?>
            <div class="col-12 form-group">
                <label for="start_date"><?php echo _('Updated At') ?></label>            
                <div class="form-row">
                    <?php echo $Layout->Element('search_period') ?>
                </div>
            </div>
            <div class="col-12">
                <?php echo form_submit('', _('Search'), array('class'=>'btn btn-primary')) ?>
            </div>
          </div>
        <?php echo form_close(); ?>
    </div>        
  </div>        
</div>
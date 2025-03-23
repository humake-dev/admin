<div class="container">
  <div class="row">
    <div class="col-12">
      <?php echo form_open('', array('class' => ''),array('return_url'=>'/view/'.$data['content']['id'])); ?>
      <input type="hidden" id="cb_user_id" name="user_id" value="<?php echo $data['content']['id']; ?>" />
      <input type="hidden" id="text_nt" value="<?php echo _('Not Transfer') ?>">
      <input type="hidden" id="text_sbf" value="<?php echo _('Select Branch First') ?>">
      <div class="row">
        <article class="col-12">
          <h3><?php echo _('User Info'); ?></h3>
          <div class="card">
            <div class="card-body">
              <div class="row">
                <div class="col-12 col-lg-4 form-group">
                  <label><?php echo _('Name'); ?></label>
                  <input value="<?php echo $data['content']['name']; ?>" class="form-control-plaintext" />
                </div>
                <?php if (!empty($common_data['branch']['use_access_card'])): ?>
                <div class="col-12 col-lg-4 form-group">
                  <label><?php echo _('Access Card No'); ?></label>
                  <input value="<?php echo $data['content']['card_no']; ?>" class="form-control-plaintext" />
                </div>
                <?php endif; ?>
                <div class="col-12 col-lg-4 form-group">
                  <label><?php echo _('Phone'); ?></label>
                  <input value="<?php echo get_hyphen_phone($data['content']['phone']); ?>" class="form-control-plaintext" />
                </div>
              </div>
            </div>
          </div>
        </article>
        
        <article class="col-12">
          <h3><?php echo _('Member branch change'); ?></h3>
          <div class="card">
            <div class="card-body">
              <div class="row">
                <div class="col-12 col-lg-4 form-group">
                  <label for="cb_branch"><?php echo _('Branch'); ?></label>
                  <?php
                  
                    $select = set_value('branch_id','');
                    $options= array(''=>_('Select Branch'));
                    
                    if ($data['branch_list']['total']) {
                      foreach ($data['branch_list']['list'] as $index => $value) {
                        $options[$value['id']] = $value['title'];
                      }
                    }
                    
                    if (isset($data['content']['branch_id'])) {
                      $select = set_value('branch_id', $data['content']['branch_id']);
                    }
                    
                    echo form_dropdown('branch_id', $options, $select, array('id' => 'cb_branch', 'class' => 'form-control', 'required' => 'required'));
                  ?>
                </div>
              </div>
            </div>
          </div>
        </article>
        
        <article class="col-12">
          <h3><?php echo _('Match Order And Transfer Branch'); ?></h3>
          <div class="card">
            <div class="card-body">
              <div class="row">
              <?php if(empty($data['enroll_list']['total']) and empty($data['rent_list']['total']) and empty($data['rent_sw_list']['total'])): ?>
              <div class="col-12">
                <p><?php echo _('There does not exist available order') ?></p>
              </div>
              <?php else : ?>
              <div class="col-6">
                <h4><?php echo _('Existing Order') ?></h4>
                <div class="row">
                  <?php if($data['enroll_list']['total']): ?>
                  <div class="col-12 form-group">
                    <label for=""><?php echo _('Enroll'); ?></label>
                    <?php foreach($data['enroll_list']['list'] as $value): ?>
                    <?php if($value['lesson_type']==4): ?>
                      <p><?php echo $value['product_name'] ?>(<?php echo _('Total Count'); ?>:<?php echo $value['quantity'] ?>/<?php echo _('Use Count'); ?>:<?php echo $value['use_quantity'] ?>/ <?php echo _('Remain Count'); ?>:<?php echo $value['quantity']-$value['use_quantity'] ?>)</p>
                    <?php else: ?>
                    <p><?php echo $value['product_name'] ?>(<?php echo get_dt_format($value['start_date'],$search_data['timezone']) ?> ~ <?php echo get_dt_format($value['end_date'],$search_data['timezone']) ?>)</p>
                    <?php endif ?>
                    <?php endforeach ?>
                  </div>
                  <?php endif ?>

      <?php if($data['rent_list']['total']): ?>      
      <div class="col-12 form-group">
              <label for=""><?php echo _('Rent'); ?></label>
              <?php foreach($data['rent_list']['list'] as $value): ?>              
              <p><?php echo $value['product_name'] ?>(<?php echo get_dt_format($value['start_date'],$search_data['timezone']) ?> ~ <?php echo get_dt_format($value['end_date'],$search_data['timezone']) ?>)</p>
              <?php endforeach ?>              
      </div>
      <?php endif ?>
      <?php if($data['rent_sw_list']['total']): ?>
      <div class="col-12 form-group">
              <label for=""><?php echo _('Rent Sw'); ?></label>
              <?php foreach($data['rent_sw_list']['list'] as $value): ?>              
              <p><?php echo $value['product_name'] ?>(<?php echo get_dt_format($value['start_date'],$search_data['timezone']) ?> ~ <?php echo get_dt_format($value['end_date'],$search_data['timezone']) ?>)</p>
              <?php endforeach ?>              
      </div>      
      <?php endif ?>

        </div>
      </div>

      <div class="col-6">
        <h4><?php echo _('New Order') ?></h4>
        <div class="row">
            <?php if($data['enroll_list']['total']): ?>
        <div id="new_enroll" class="col-12 form-group">
              <label for=""><?php echo _('Enroll'); ?></label>
              <?php 
              $attr=array('class' => 'form-control', 'required' => 'required','style'=>'margin-bottom:5px','disabled'=>'disabled');   

                foreach($data['enroll_list']['list'] as $enroll):
                
          $select = '';
          $options= array('0'=>_('Select Branch First'));          

          if (!empty($data['new_enroll_list']['total'])) {
              foreach ($data['new_enroll_list']['list'] as $index => $value) {
                if($enroll['lesson_type']!=$value['lesson_type']) {
                  continue;
                }

                  $options[$value['id']] = $value['title'];
              }
              $attr=array('class' => 'form-control', 'required' => 'required','style'=>'margin-bottom:5px');              
          }
          echo form_dropdown('new_enroll['.$enroll['id'].']', $options, $select, $attr);
          
            endforeach;
          ?>
        </div>
      <?php endif ?>

      <?php if($data['rent_list']['total']): ?>      
      <div id="new_rent" class="col-12 form-group">
              <label for=""><?php echo _('Rent'); ?></label>
              <?php 
                foreach($data['rent_list']['list'] as $rent):
                
          $select = '';
          $options= array('0'=>_('Select Branch First'));        

          if (!empty($data['new_rent_list']['total'])) {
              foreach ($data['new_rent_list']['list'] as $index => $value) {
                  $options[$value['id']] = $value['title'];
              }
              $attr=array('class' => 'form-control', 'required' => 'required','style'=>'margin-bottom:5px');              
          }
          echo form_dropdown('new_rent['.$rent['id'].']', $options, $select, $attr);
          
            endforeach;
          ?>           
      </div>
      <?php endif ?>
      <?php if($data['rent_sw_list']['total']): ?>
      <div id="new_rent_sw" class="col-12 form-group">
              <label for=""><?php echo _('Rent Sw'); ?></label>
              <?php 
                foreach($data['rent_sw_list']['list'] as $rent_sw):
                
          $select = '';
          $options= array('0'=>_('Select Branch First')); 

          if (!empty($data['new_rent_sw_list']['total'])) {
              foreach ($data['new_rent_sw_list']['list'] as $index => $value) {
                  $options[$value['id']] = $value['title'];
              }
              $attr=array('class' => 'form-control', 'required' => 'required','style'=>'margin-bottom:5px');
          }
          echo form_dropdown('new_rent_sw['.$rent_sw['id'].']', $options, $select, $attr);
          
            endforeach;
          ?>            
      </div>      
      <?php endif ?>
        </div>
      </div>


      <?php endif ?>


      </div>      
    </div>
  </article>
</div>


        <?php echo $Layout->Element('form_memo'); ?>
        <?php echo form_submit('', _('Change Branch'), array('class' => 'btn btn-primary btn-block btn-lg')); ?>
      <?php echo form_close(); ?>
    </div>
  </div>
</div>

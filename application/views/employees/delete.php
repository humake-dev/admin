<div id="employees" class="container">
  <div class="row">
    <?php include __DIR__.DIRECTORY_SEPARATOR.'aside.php' ?>
    <div class="col-12 col-lg-7 col-xl-8 col-xxl-9">
      <?php echo $Layout->Element('employees/nav') ?>
      <h2 style="text-indent:-9999px;height:1px"><?php echo _('Employee Info') ?></h2>


  <div class="row justify-content-center">
    <div class="col-12">
      <?php echo form_open($this->router->fetch_class().'/'.'delete/'.$data['id'],array('class'=>"card border-danger")) ?>
        <h3 class="card-header bg-danger text-light"><?php echo _('Confirm Delete Employee') ?></h3>
        <div class="card-body">
          <?php if(empty($data['content']['is_fc']) AND empty($data['content']['is_trainer'])): ?>
            <?php echo sprintf(_('Are You Sure Delete Employee(name: %s)?'),$data['content']['name']) ?>
          <?php else: ?>
          <?php if($data['content']['is_fc']): ?>
          <?php if($data['fc_user']['total']): ?>          
          <div class="col-12">              
          <div class="form-group">
          <label><?php echo sprintf(_('%s FC`s Users(%d) Are Set To'),$data['content']['name'],$data['fc_user']['total']) ?></label>  
          <?php
          $select=set_value('after_fc_id', '');
          $options=array(''=>_('Set To Not Insert'));        

          if ($data['fc']['total']) {
              foreach ($data['fc']['list'] as $value) {
                if($data['content']['id']==$value['id']) {
                  continue;
                }                
                  $options[$value['id']]=sprintf(_('Users Fc Change To %s'),$value['name']);
              }
          }

          echo form_dropdown('after_fc_id', $options, $select, array('class'=>'form-control'));
        ?>
          </div>
          </div>          
          <?php endif ?>
          <?php endif ?>
          <?php if($data['content']['is_trainer']): ?>
          <?php if($data['trainer_user']['total']): ?>
          <div class="col-12">              
          <div class="form-group">
          <label><?php echo sprintf(_('%s Trainer`s Users(%d) Are Set To'),$data['content']['name'],$data['trainer_user']['total']) ?></label>          
          <?php
          $select=set_value('after_trainer_id', '');
          $options=array(''=>_('Set To Not Insert'));

          if ($data['trainer']['total']) {
            foreach ($data['trainer']['list'] as $value) {
                if($data['content']['id']==$value['id']) {
                  continue;
                }
                $options[$value['id']]=sprintf(_('Users Trainer Change To %s'),$value['name']);
            }
          }
          echo form_dropdown('after_trainer_id', $options, $select, array('class'=>'form-control'));
        ?>
          </div>        
          </div>
          <?php endif ?>
          <?php endif ?>
          <?php endif ?>          
        </div>
        <div class="card-footer">
          <div class="col-12">
            <?php if(!empty($data['content']['is_fc']) OR !empty($data['content']['is_trainer'])): ?>
            <?php echo sprintf(_('Are You Sure Delete Employee(name: %s)?'),$data['content']['name']) ?> &nbsp; 
            <?php endif ?>
            <?php echo form_submit('', _('Delete'), array('class'=>'btn btn-danger')) ?>
          </div>
        </div>        
        <?php echo form_close(); ?>
    </div>
  </div>


    </div>
  </div>
</div>

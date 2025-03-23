<div class="row">
    <div class="col-12 col-lg-6" style="margin-bottom:10px">
        <?php if ($this->Acl->has_permission('rent_sws', 'write')): ?>
        <?php echo anchor('rent-sws/add', _('Add'), array('class' => 'btn btn-primary float-left')); ?>
        <?php if ($this->session->userdata('role_id') < 4): ?>
        <?php if(empty($data['content']['id'])): ?>
            <?php echo anchor('', _('Transfer'), array('class' => 'btn btn-secondary float-left rent_sw_transfer','style'=>'margin-left:10px')); ?>
        <?php else: ?>
            <?php echo anchor('rent-sws/transfer/'.$data['content']['id'], _('Transfer'), array('class' => 'btn btn-secondary float-left rent_sw_transfer','style'=>'margin-left:10px')); ?>
        <?php endif ?>
        <?php endif ?>
        <?php endif ?>
    </div>
    <div class="col-12 col-lg-6">
    <div class="float-right">
        <p class="summary">
          <span id="list_count" style="display:none"><?php echo $data['total']; ?></span>
          <?php echo sprintf(_('There Are %d Rent Sws'), $data['total']); ?>
        </p>
    	</div>
    </div>
    <div class="col-12">
        <?php 
        $list=$data;
        include __DIR__.DIRECTORY_SEPARATOR.'list_content.php';
        ?>
    </div>
    <div class="col-12">
        <?php echo $this->pagination->create_links(); ?>
        <?php if ($this->Acl->has_permission('rent_sws', 'write')): ?>
        <?php echo anchor('rent-sws/add', _('Add'), array('class' => 'btn btn-primary float-left')); ?>
        <?php if ($this->session->userdata('role_id') < 4): ?>        
        <?php if(empty($data['content']['id'])): ?>
            <?php echo anchor('', _('Transfer'), array('class' => 'btn btn-secondary float-left rent_sw_transfer','style'=>'margin-left:10px')); ?>
        <?php else: ?>
            <?php echo anchor('rent-sws/transfer/'.$data['content']['id'], _('Transfer'), array('class' => 'btn btn-secondary float-left rent_sw_transfer','style'=>'margin-left:10px')); ?>
        <?php endif ?>
        <?php endif ?>
        <?php endif ?>
    </div>
</div>
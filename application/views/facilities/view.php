<div id="view-facility" class="container">
  <div class="row">
    <div class="col-12">
      <div class="card">
        <h3 class="card-header"><?php echo $data['content']['title']; ?></h3>
        <div class="card-body">
          <dl>
            <dt><?php echo _('Order No'); ?></dt>
            <dd><?php echo $data['content']['order_no']; ?></dd>
            <dt><?php echo _('Facility'); ?></dt>
            <dd><?php echo $data['content']['title']; ?></dd>
            <dt><?php echo _('Gender'); ?></dt>
            <dd><?php echo display_gender($data['content']['gender']); ?></dd>
            <dt><?php echo _('Facility Quantity'); ?></dt>
            <dd><?php echo $data['content']['quantity']; ?></dd>
            <dt><?php echo _('Use Not Set'); ?></dt>            
            <dd>
            <?php if (empty($data['content']['use_not_set'])): ?>
            <?php echo _('Not Use'); ?>
            <?php else: ?>
            <?php echo _('Use'); ?>
            <?php endif; ?>
            </dd>
            <dt><?php echo _('Created At'); ?></dt>
            <dd><?php echo $data['content']['created_at']; ?></dd>
            <dt><?php echo _('Updated At'); ?></dt>
            <dd><?php echo $data['content']['updated_at']; ?></dd>
          </dl>
          <?php if ($this->session->userdata('role_id') == 1): ?>
          <dl>
            <dt><?php echo _('Product ID'); ?></dt>
            <dd><?php echo $data['content']['product_id']; ?></dd>
          </dl>
          <?php endif; ?>
        </div>
      </div>
    </div>    
    <div class="col-12">
      <div class="sl_view_bottom">
        <?php echo anchor($this->router->fetch_class(), _('Go List'), array('class' => 'btn btn-secondary')); ?>
        <?php if ($this->Acl->has_permission('facilities', 'edit')): ?>
        <?php echo anchor($this->router->fetch_class().'/edit/'.$data['content']['id'], _('Edit'), array('class' => 'btn btn-secondary')); ?>
        <?php endif; ?>
        <?php if ($this->Acl->has_permission('facilities', 'delete')): ?>
        <?php echo anchor($this->router->fetch_class().'/delete/'.$data['content']['id'], _('Delete'), array('class' => 'btn btn-danger float-right')); ?>
        <?php endif; ?>
      </div>
    </div>
  </div>
</div>

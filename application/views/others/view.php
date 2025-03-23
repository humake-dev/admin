<div id="view-other" class="container">
  <div class="row">
    <div class="col-12">
      <div class="card">
        <h3 class="card-header"><?php echo $data['content']['title']; ?></h3>
        <div class="card-body">
          <dl>
            <dt><?php echo _('User'); ?>
            <?php if (!empty($common_data['branch']['use_access_card'])): ?>  
            (<?php echo _('Access Card No'); ?>)
            <?php endif; ?>
            </dt>
            <dd><?php echo $data['content']['user_name']; ?>(<?php echo $data['content']['card_no']; ?>)</dd>
            <dt><?php echo _('Content'); ?></dt>
            <dd><?php echo $data['content']['title']; ?></dd>
            <dt><?php echo _('Price'); ?></dt>
            <dd><?php echo $data['content']['price']; ?></dd>
            <dt><?php echo _('Cash'); ?></dt>
            <dd><?php echo $data['content']['cash']; ?></dd>
            <dt><?php echo _('Credit'); ?></dt>
            <dd><?php echo $data['content']['credit']; ?></dd>
            <dt><?php echo _('Transaction Date'); ?></dt>
            <dd><?php echo $data['content']['transaction_date']; ?></dd>
            <dt><?php echo _('Created At'); ?></dt>
            <dd><?php echo $data['content']['created_at']; ?></dd>
            <dt><?php echo _('Updated At'); ?></dt>
            <dd><?php echo $data['content']['updated_at']; ?></dd>
          </dl>
        </div>
      </div>
    </div>
    <div class="col-12">
      <div class="sl_view_bottom">
        <?php echo anchor('/account-others', _('Go List'), array('class' => 'btn btn-secondary')); ?>
        <?php echo anchor($this->router->fetch_class().'/edit/'.$data['content']['id'], _('Edit'), array('class' => 'btn btn-secondary')); ?>
        <?php echo anchor($this->router->fetch_class().'/delete/'.$data['content']['id'], _('Delete'), array('class' => 'btn btn-danger float-right')); ?>
      </div>
    </div>
  </div>
</div>

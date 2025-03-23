<div id="view-course-category" class="container">
  <div class="row">
    <div class="col-12">
      <div class="card">
        <h3 class="card-header"><?php echo $data['content']['title']; ?></h3>
        <div class="card-body">
          <dl>
            <dt><?php echo _('Title'); ?></dt>
            <dd><?php echo $data['content']['title']; ?></dd>
            <dt><?php echo _('Order No'); ?></dt>
            <dd><?php echo $data['content']['order_no']; ?></dd>
          </dl>
        </div>
      </div>
    </div>
    <div class="col-12">
      <div class="sl_view_bottom">
        <?php echo anchor($this->router->fetch_class(), _('Go List'), array('class' => 'btn btn-secondary')); ?>
        <?php echo anchor($this->router->fetch_class().'/delete/'.$data['content']['id'], _('Delete'), array('class' => 'btn btn-danger float-right')); ?>
        <?php echo anchor($this->router->fetch_class().'/edit/'.$data['content']['id'], _('Edit'), array('class' => 'btn btn-secondary float-right', 'style' => 'margin-right:10px')); ?>
      </div>
    </div>
  </div>
</div>

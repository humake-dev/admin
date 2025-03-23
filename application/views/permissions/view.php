<div id="view-permission" class="container">
  <div class="row">
    <div class="col-12">
      <div class="card">
        <h3 class="card-header"></h3>
        <div class="card-body">
          <dl>
            <dt><?php echo _('Created At') ?></dt>
            <dd><?php echo $data['content']['created_at'] ?></dd>
            <dt><?php echo _('Updated At') ?></dt>
            <dd><?php echo $data['content']['updated_at'] ?></dd>
          </dl>
        </div>
      </div>
    </div>
    <div class="col-12">
      <div class="sl_view_bottom">
        <?php echo anchor($this->router->fetch_class(),_('Go List'),array('class'=>'btn btn-secondary')); ?>
        <?php echo anchor($this->router->fetch_class().'/delete/'.$data['content']['id'],_('Delete'),array('class'=>'btn btn-danger float-right')); ?>
      </div>
    </div>
  </div>
</div>

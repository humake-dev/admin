<div id="view-message-prepare" class="container">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <h3 class="card-header"><?php echo $data['content']['title'] ?></h3>
                <div class="card-body">
                    <?php echo nl2br($data['content']['content']) ?>
                </div>
            </div>
        </div>
        <div class="col-12">
            <div class="sl_view_bottom">
                <?php echo anchor($this->router->fetch_class(), _('Go List'), array('class' => 'btn btn-secondary')); ?>
                <?php echo anchor($this->router->fetch_class() . '/edit/' . $data['content']['id'], _('Edit'), array('class' => 'btn btn-secondary')); ?>
                <?php echo anchor($this->router->fetch_class() . '/delete/' . $data['content']['id'], _('Delete'), array('class' => 'btn btn-danger float-right')); ?>
            </div>
        </div>
    </div>
</div>

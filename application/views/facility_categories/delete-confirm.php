<div id="view_message" class="container">
  <div class="row justify-content-center">
    <div class="col-12 col-lg-6">
      <div class="card border-danger">
        <h3 class="card-header bg-danger text-light"><?php echo _('Confirm Delete Course Classification'); ?></h3>
        <div class="card-body">
          <div class="col-12">
          <?php echo sprintf(_('Are You Sure Delete Really class classification(name: %s)?'), $data['content']['title']); ?>
          </div>
        </div>
        <div class="card-footer">
          <div class="col-12">
            <?php echo form_open($this->router->fetch_class().'/'.'delete/'.$data['id']); ?>
              <input type="submit" class="btn btn-danger" value="삭제" />
            <?php echo form_close(); ?>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

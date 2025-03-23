<div id="view-notice" class="container">
  <div class="row">
    <div class="col-12">
      <div class="card">
        <h3 class="card-header"><?php echo $data['content']['created_at'] ?>에 수정한 <?php echo $data['content']['product_name'] ?>의 변경사항</h3>
        <div class="card-body">
          <div>
            <label><?php echo _('Editor') ?></label>
            <p><?php echo $data['content']['editor'] ?></p>
          </div>
          <div class="form-group">
          <label><?php echo _('Change Content') ?></label>
          <p><?php echo nl2br($data['content']['content']) ?></p>
          </div>
          <table class="table table-striped table-hover">
            <colgroup>
              <col>
              <col>
              <col>
            </colgroup>
            <thead>
              <tr>
                <th><?php echo _('Field') ?></th>
                <th><?php echo _('Origin Value') ?></th>
                <th><?php echo _('Change Value') ?></th>
              </tr>                
            </thead>
            <tbody>
              <?php if(empty($data['change_logs']['total'])): ?>
              <tr>
                <td cols="3"><?php echo _('No Data') ?></td>
              </tr>              
              <?php else: ?>
              <?php foreach($data['change_logs']['list'] as $value): ?>
              <tr>
                <td><?php echo display_edit_log_field($value['field']) ?></td>
                <td><?php echo display_edit_log_value($value['field'],$value['origin'],$search_data['timezone']) ?></td>
                <td><?php echo display_edit_log_value($value['field'],$value['change'],$search_data['timezone']) ?></td>           
              </tr>
              <?php endforeach ?>
              <?php endif ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>
    <div class="col-12">
      <div class="sl_view_bottom">
        <?php echo anchor($this->router->fetch_class(),_('Go List'),array('class'=>'btn btn-secondary')) ?>
        <?php if($this->session->userdata('role_id')<=5): ?>
        <?php echo anchor($this->router->fetch_class().'/delete/'.$data['content']['id'],_('Delete'),array('class'=>'btn btn-danger float-right')) ?>
        <?php endif ?>
      </div>
    </div>
  </div>
</div>

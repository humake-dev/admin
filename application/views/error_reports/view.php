<div id="view-notice" class="container">
  <div class="row">
    <div class="col-12">
      <div class="card">
        <h3 class="card-header"><?php echo $data['content']['title'] ?></h3>
        <div class="card-body">
          <dl>
            <dt><?php echo _('Admin') ?></dt>
            <dd><?php echo $data['content']['admin_name'] ?></dd>
          </dl>
          <?php echo nl2br($data['content']['content']) ?>
          <?php if (!empty($data['content']['file']['total'])): ?>
            <div>
                                    <?php
                                    foreach ($data['content']['file']['list'] as $index=>$file):
                                        ?>
                                        <?php if($this->session->userdata('role_id') < 6 or $data['content']['user_id']==$this->session->userdata('user_id')): ?>
                                        <form action="/error-report-files/delete/<?php echo $file['id']; ?>"
                                              method="post" style="clear:both;margin:30px 0;padding:10px;">
                                              <?php endif ?>
                                            <div class="float-left">
                                              <?php echo sprintf(_('File %d'),$index+1) ?> : <a href="<?php echo getPhotoPath('errorReport',$data['content']['branch_id'],$file['file_url']) ?>"><?php echo _('Download') ?></a>
                                            </div>
                                            <?php if($this->session->userdata('user_id') < 4 or $data['content']['user_id']==$this->session->userdata('user_id')): ?>
                                            <div class="float-right">
                                            <input type="submit" value="<?php echo _('Delete'); ?>"
                                                   class="btn btn-danger">
                                            </div>
                                        </form>
                                        <?php endif ?>
                                    <?php endforeach; ?>

                                    </div>
                                    <?php endif ?>
        </div>
      </div>
    </div>
    <div class="col-12">
      <div class="sl_view_bottom">
        <?php echo anchor($this->router->fetch_class(),_('Go List'),array('class'=>'btn btn-secondary')) ?>
        <?php if($this->session->userdata('user_id') < 4 or $data['content']['user_id']==$this->session->userdata('user_id')): ?>
        <?php echo anchor($this->router->fetch_class().'/edit/'.$data['content']['id'],_('Edit'),array('class'=>'btn btn-secondary')) ?>
        <?php echo anchor($this->router->fetch_class().'/delete/'.$data['content']['id'],_('Delete'),array('class'=>'btn btn-danger float-right')) ?>
        <?php endif ?>
      </div>
    </div>
  </div>
</div>

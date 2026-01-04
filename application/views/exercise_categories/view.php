<div id="view-exercise-category" class="container">
  <div class="row">
    <div class="col-12">
      <div class="card">
        <h3 class="card-header"><?php echo $data['content']['title'] ?></h3>
        <div class="card-body">
          <dl>
            <dt><?php echo _('Image') ?></dt>
            <dd>
              <?php if(empty($data['content']['picture_url'])): ?>
              <?php echo _('Not Inserted') ?>
              <?php else: ?>
              <?php
                $pictures=explode(',',$data['content']['picture_url']);
                foreach($pictures as $picture):
                $picture_s=explode('::',$picture);
              ?>
                <form action="/exercise-category-pictures/delete/<?php echo $picture_s[0] ?>">
                  <div>
                    <img src="<?php echo getPhotoPath('exerciseCategory', $this->session->userdata('branch_id'), $picture_s[1], 'medium') ?>" />
                  </div>
                  <?php echo form_submit('', _('Delete'), array('class'=>'btn btn-danger')) ?>
                </form>
              <?php endforeach ?>
              <?php endif ?>
            </dd>
            <dt><?php echo _('Enable') ?></dt>
            <dd><?php echo change_enable($data['content']['enable']) ?></dd>
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
        <?php echo anchor($this->router->fetch_class().'/edit/'.$data['content']['id'],_('Edit'),array('class'=>'btn btn-secondary')); ?>
        <?php echo anchor($this->router->fetch_class().'/delete/'.$data['content']['id'],_('Delete'),array('class'=>'btn btn-danger float-right')); ?>
      </div>
    </div>
  </div>
</div>

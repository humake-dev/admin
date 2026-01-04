<div id="view-product" class="container">
  <div class="row">
    <div class="col-12">
      <div class="card">
        <h3 class="card-header"><?php echo $data['content']['title'] ?></h3>
        <div class="card-body">
          <dl>
            <dt><?php echo _('Category') ?></dt>
            <dd><?php echo $data['content']['category_name'] ?></dd>
            <dt><?php echo _('Title') ?></dt>
            <dd><?php echo $data['content']['title'] ?></dd>
            <dt><?php echo _('Price') ?></dt>
            <dd><?php echo number_format($data['content']['price']) ?><?php echo _('Currency') ?></dd>
            <dt><?php echo _('Enroll Display') ?></dt>
            <dd>
              <?php if($data['content']['enroll_display']): ?>
              <?php echo _('Display') ?>
              <?php else: ?>
              <?php echo _('Not Display') ?>
              <?php endif ?>
            </dd>
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
                <form action="/product-pictures/delete/<?php echo $picture_s[0] ?>">
                  <div>
                    <img src="<?php echo getPhotoPath('product',  $this->session->userdata('branch_id'), $picture_s[1], 'small') ?>" />
                  </div>
                  <input type="submit"  value="<?php echo _('Delete') ?>" class="btn btn-danger">
                </form>
              <?php endforeach ?>
              <?php endif ?>
            </dd>
          </dl>
        </div>
      </div>
    </div>
    <div class="col-12">
      <div class="sl_view_bottom">
        <?php echo anchor($this->router->fetch_class(),_('Go List'),array('class'=>'btn btn-secondary')) ?>
        <?php echo anchor($this->router->fetch_class().'/edit/'.$data['content']['id'],_('Edit'),array('class'=>'btn btn-secondary')) ?>
        <?php echo anchor($this->router->fetch_class().'/delete/'.$data['content']['id'],_('Delete'),array('class'=>'btn btn-danger float-right')) ?>
      </div>
    </div>
  </div>
</div>

<div id="view-center" class="container">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <h3 class="card-header"><?php echo $data['content']['title']; ?></h3>
                <div class="card-body">
                    <dl>
                        <dt><?php echo _('Image'); ?></dt>
                        <dd>
                            <?php if (empty($data['content']['picture_url'])): ?>
                                <?php echo _('Not Inserted'); ?>
                            <?php else: ?>
                                <?php
                                $pictures = explode(',', $data['content']['picture_url']);
                                foreach ($pictures as $picture):
                                    $picture_s = explode('::', $picture);
                                    ?>
                                    <form action="/center-pictures/delete/<?php echo $picture_s[0]; ?>">
                                        <div>
                                            <img src="<?php echo getPhotoPath('center', $data['content']['id'], $picture_s[1], 'small'); ?>"/>
                                        </div>
                                        <input type="submit" value="<?php echo _('Delete'); ?>" class="btn btn-danger">
                                    </form>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </dd>
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
                <?php echo anchor($this->router->fetch_class(), _('Go List'), array('class' => 'btn btn-secondary')); ?>
                <?php if ($this->session->userdata('role') == 1): ?>
                    <?php echo anchor($this->router->fetch_class() . '/delete/' . $data['content']['id'], _('Disable'), array('class' => 'btn btn-danger float-right')); ?>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

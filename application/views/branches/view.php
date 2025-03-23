<div id="view-branch" class="container">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <h3 class="card-header"><?php echo $data['content']['title']; ?></h3>
                <div class="card-body">
                    <dl>
                        <dt><?php echo _('Description'); ?></dt>
                        <dd>
                            <?php echo $data['content']['description']; ?>
                            <?php if (!empty($data['content']['description'])): ?>
                                <?php echo $data['content']['description']; ?>
                            <?php endif; ?>
                        </dd>
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
                                    <form action="/branch-pictures/delete/<?php echo $picture_s[0]; ?>">
                                        <div>
                                            <img src="<?php echo getPhotoPath('branch', $data['content']['id'], $picture_s[1], 'small'); ?>"/>
                                        </div>
                                        <input type="submit" value="삭제" class="btn btn-danger">
                                    </form>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </dd>
                        <dt><?php echo _('Is employee input enabled'); ?></dt>
                        <dd><?php echo change_enable($data['content']['use_admin_ac']); ?></dd>
                        <dt><?php echo _('Enabled'); ?></dt>
                        <dd><?php echo change_enable($data['content']['enable']); ?></dd>
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
                <?php if ($this->Acl->has_permission('branches')): ?>
                    <?php echo anchor($this->router->fetch_class(), _('Go List'), array('class' => 'btn btn-secondary')); ?>
                <?php endif; ?>
                <?php if ($this->Acl->has_permission('branches', 'edit')): ?>
                    <?php echo anchor($this->router->fetch_class() . '/edit/' . $data['content']['id'], _('Edit'), array('class' => 'btn btn-secondary')); ?>
                <?php endif; ?>
                <?php if ($this->Acl->has_permission('branches', 'delete')): ?>
                    <?php echo anchor($this->router->fetch_class() . '/delete/' . $data['content']['id'], _('Disable'), array('class' => 'btn btn-danger float-right')); ?>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

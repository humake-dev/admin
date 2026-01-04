<div id="users" class="container">
    <div class="row">
        <?php include __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'home' . DIRECTORY_SEPARATOR . 'aside.php'; ?>
        <div class="col-12 col-lg-7 col-xl-8 col-xxl-9">
            <?php echo $Layout->element('home/nav'); ?>
            <h2 class="di_title"><?php echo _('User Info'); ?></h2>
            <div class="row justify-content-center">
                <div class="col-12">
                    <div class="card border-danger">
                        <h3 class="card-header bg-danger text-light"><?php echo _('Confirm Delete User'); ?></h3>
                        <div class="card-body">
                            <div class="col-12">
                                <?php if (empty($common_data['branch']['use_access_card'])): ?>
                                    <?php echo sprintf(_('Are You Sure Delete User(name: %s)?'), $data['content']['name']); ?>
                                <?php else: ?>
                                    <?php echo sprintf(_('Are You Sure Delete User(name: %s, card_no: %s)?'), $data['content']['name'], $data['content']['card_no']); ?>
                                <?php endif; ?>
                            </div>
                        </div>
                        <div class="card-footer">
                            <div class="col-12">
                                <?php echo form_open($this->router->fetch_class() . '/' . 'delete/' . $data['id'], array('id' => 'user_delete_form')); ?>
                                <input type="submit" class="btn btn-danger" value="<?php echo _('Delete'); ?>"/>
                                <?php echo form_close(); ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>




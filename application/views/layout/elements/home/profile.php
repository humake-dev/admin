<article class="row user-info">
    <h3 class="col-12 di_title"><?php echo _('Selected User Info'); ?></h3>
    <div class="col-12 col-sm-6">
        <?php if (empty($data['user_content']['picture_url'])): ?>
            <img id="profile_photo" src="/assets/images/common/bg_photo_none.gif" width="100%" height="100%">
        <?php else: ?>
            <a href="<?php echo getPhotoPath('user', $data['user_content']['branch_id'], $data['user_content']['picture_url']); ?>" class="simple_image">
            <img id="profile_photo" src="<?php echo getPhotoPath('user', $data['user_content']['branch_id'], $data['user_content']['picture_url'], 'large'); ?>" width="100%" height="100%">
            </a>
        <?php endif ?>
    </div>
    <input type="hidden" id="home_user_id" value="<?php if(!empty($data['content']['id'])): ?><?php echo $data['content']['id']; ?><?php endif ?>">
    <input type="hidden" id="home_currency" value="<?php echo _('Currency'); ?>">
    <input type="hidden" id="home_minute" value="<?php echo _('Minute'); ?>">        
    <div class="col-12 col-sm-6">
        <ul>
            <?php if (empty($data['user_content'])): ?>
            <li><span class="btn btn-sm btn-light btn-block btn-outline-info">-</span>
            <li><span class="btn btn-sm btn-light btn-block btn-outline-info">-</span>
            <?php else: ?>
            <li>
                <span id="show_app_id" class="btn btn-sm btn-light btn-block btn-outline-info"><?php if (isset($data['user_content']['name'])): ?><?php echo $data['user_content']['name']; ?><?php else: ?>-<?php endif; ?></span>
                <input type="hidden" name="app_id" value="<?php echo $data['user_content']['branch_id']; ?>#<?php echo $data['user_content']['id']; ?>">
            </li>
            <li>            
            <?php if (empty($common_data['branch']['use_access_card'])): ?>
                <span class="btn btn-sm btn-light btn-block btn-outline-info">            
                <?php echo get_hyphen_phone($data['content']['phone']); ?>
                </span>
            <?php else: ?>
                <span class="btn btn-sm btn-light btn-block btn-outline-info">
                <?php echo get_card_no($data['user_content']['card_no'], false); ?>
				</span>
            <?php endif; ?>
            </li>            
            <?php if ($this->session->userdata('branch_id')): ?>
            <li><?php echo anchor('devices/webcam?type=members&amp;id='.$data['user_content']['id'], _('Photo Shoot'), array('title' => _('Photo Shoot'), 'target' => '_blank', 'class' => 'btn btn-sm btn-outline-secondary btn-block btn-popup')); ?></li>
            <li id="delete-photo-layer">
                <?php if (empty($data['user_content']['up_id'])): ?>
                <span class="btn btn-sm btn-outline-secondary disabled btn-block"><?php echo _('Delete Photo'); ?></span>
                <?php else: ?>
                <?php echo form_open('user-pictures/delete/'.$data['user_content']['up_id'],array('id'=>'delete-photo-form')); ?>
                <?php echo form_submit('', _('Delete Photo'), array('class' => 'btn btn-sm btn-outline-secondary btn-block')); ?>
                <?php echo form_close(); ?>
                <?php endif; ?>
            </li>
            <li>
                <span id="photo_load" class="btn btn-sm btn-outline-secondary btn-block"><?php echo _('Select Photo'); ?></span>
                <?php echo form_open_multipart('user-pictures/update-photo/'.$data['user_content']['id'], array('id' => 'form_photo', 'style' => 'display:none')); ?>
                <input type="file" name="photo[]" accept="image/*" capture="">
                <?php echo form_submit('', _('Upload Photo'), array('class' => 'btn btn-sm btn-secondary btn-block')); ?>
                <?php echo form_close(); ?>
            </li>
            <?php endif; ?>
        <?php endif; ?>
        </ul>
    </div>
</article>
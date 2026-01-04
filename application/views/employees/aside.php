<aside class="col-12 col-lg-5 col-xl-4 col-xxl-3">
  <article class="row user-info">
		<h3 class="col-12 di_title"><?php echo _('Employee Info'); ?></h3>
    <div class="col-12 col-sm-6">
        <?php if (empty($data['content']['picture_url'])): ?>
            <img id="profile_photo" src="/assets/images/common/bg_photo_none.gif" width="100%" height="100%">
        <?php else: ?>
            <a href="<?php echo getPhotoPath('employee', $data['content']['branch_id'], $data['content']['picture_url']); ?>" class="simple_image">
            <img id="profile_photo" src="<?php echo getPhotoPath('employee', $data['content']['branch_id'], $data['content']['picture_url'], 'large'); ?>" width="100%" height="100%">
            </a>
        <?php endif ?>
    </div>
    <div class="col-12 col-sm-6">
      <ul>
        <?php if (empty($data['content'])): ?>
				<li><span class="btn btn-sm btn-light btn-block btn-outline-info">-</span>
				<li><span class="btn btn-sm btn-light btn-block btn-outline-info">-</span>
        <?php else: ?>
				<li><span class="btn btn-sm btn-light btn-block btn-outline-info"><?php if (isset($data['content']['name'])): ?><?php echo $data['content']['name']; ?><?php else: ?>-<?php endif; ?></span></li>
        <li><span class="btn btn-sm btn-light btn-block btn-outline-info">-</span></li>
  			<?php if ($this->session->userdata('branch_id')): ?>
        <li><?php echo anchor('devices/webcam?type=employees&amp;id='.$data['content']['id'], _('Photo Shoot'), array('title' => _('Photo Shoot'), 'target' => '_blank', 'class' => 'btn btn-sm btn-outline-secondary btn-block btn-popup')); ?></li>
        <li id="delete-photo-layer">
					<?php if (empty($data['content']['ep_id'])): ?>
					<span class="btn btn-sm btn-light btn-block btn-outline-secondary"><?php echo _('Delete Photo'); ?></span>
					<?php else: ?>
					<?php echo form_open('employee-pictures/delete/'.$data['content']['ep_id'],array('id'=>'delete-photo-form')); ?>
					<?php echo form_submit('', _('Delete Photo'), array('class' => 'btn btn-sm btn-outline-secondary btn-block')); ?>
					<?php echo form_close(); ?>
					<?php endif; ?>
				</li>
        <li>
					<span id="photo_load" class="btn btn-sm btn-outline-secondary btn-block"><?php echo _('Select Photo'); ?></span>
					<?php echo form_open_multipart('employee-pictures/update-photo/'.$data['content']['id'], array('id' => 'form_photo', 'style' => 'display:none')); ?>
					<input type="file" name="photo[]" accept="image/*" capture="">
					<?php echo form_submit('', _('Upload Photo'), array('class' => 'btn btn-sm btn-secondary btn-block', 'id' => 'photo_load')); ?>
					<?php echo form_close(); ?>
        </li>
				<?php endif; ?>
				<?php endif; ?>
      </ul>
    </div>
	</article>
	<?php echo $Layout->element('employees/search'); ?>
  <?php echo $this->pagination->create_links(); ?>
</aside>

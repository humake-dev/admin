<aside <?php if (empty($data['user_content'])): ?>class="col-12 col-xl-6 col-xxl-5"<?php else: ?>class="col-12 col-xl-4 col-xxl-3"<?php endif; ?>>
  <input type="hidden" name="enroll_info" value="1" />
  <?php echo $Layout->element('home/profile_detail'); ?>
  <?php include __DIR__.DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'enrolls'.DIRECTORY_SEPARATOR.'enroll_list.php'; ?>  
</aside>

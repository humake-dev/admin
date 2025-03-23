<aside <?php if (empty($data['user_content'])): ?>class="col-12 col-xl-6 col-xxl-5"<?php else: ?>class="col-12 col-xl-4 col-xxl-3"<?php endif; ?>>
  <?php echo $Layout->element('home/profile_detail'); ?>
  <?php include __DIR__.DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'enrolls'.DIRECTORY_SEPARATOR.'enroll_list.php'; ?>
  <?php include __DIR__.DIRECTORY_SEPARATOR.'rent_list.php'; ?>  
</aside>

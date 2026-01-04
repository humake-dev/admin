<?php
  $params = '';
  if ($this->input->get()) {
      $p_index = 0;
      foreach ($this->input->get() as $key => $param) {
          if ($p_index) {
              $params .= '&'.$key.'='.$param;
          } else {
              $params .= '?'.$key.'='.$param;
          }
          ++$p_index;
      }
  }
?>
<div id="temp-users" class="container">
  <div class="row">
    <?php include __DIR__.DIRECTORY_SEPARATOR.'aside.php'; ?>
    <div class="col-12 col-lg-7 col-xl-8 col-xxl-9 user_sub">
      <section class="row">
        <div class="col-12">
          <?php
            if (empty($data['content'])) {
                echo $Layout->element('home/not_found.php');
            } else {
                include __DIR__.DIRECTORY_SEPARATOR.'nav.php';
                include __DIR__.DIRECTORY_SEPARATOR.'content.php';
            }
          ?>                      
        </div>
      </section>
      <?php
      if (!empty($data['content'])):
        include __DIR__.DIRECTORY_SEPARATOR.'summary.php';
      endif;
     ?>
</div>
</div>
</div>
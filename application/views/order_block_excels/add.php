<?php

$params = '';
if ($this->input->get()) {
    $p_index = 0;
    foreach ($this->input->get() as $key => $param) {
        if ($p_index) {
            if (is_array($param)) {
                foreach ($param as $pp) {
                    $params .= '&'.$key.'[]='.$pp;
                }
            } else {
                $params .= '&'.$key.'='.$param;
            }
        } else {
            if (is_array($param)) {
                foreach ($param as $pi => $pp) {
                    if ($pi) {
                        $params .= '&'.$key.'[]='.$pp;
                    } else {
                        $params .= '?not_phone=true&'.$key.'[]='.$pp;
                    }
                }
            } else {
                $params .= '?not_phone=true&'.$key.'='.$param;
            }
        }
        ++$p_index;
    }
}

?>
<div id="add-order-block" class="container add-page">
  <div class="row">
    <section class="col-12">
      <div class="row" style="margin-bottom:10px">
        <h2 class="col-12"><?php echo _('Add Order Block'); ?></h2>
      </div>
      <article class="row">
        <div class="col-12">
          <?php include __DIR__.DIRECTORY_SEPARATOR.'form.php'; ?>
        </div>
      </article>
    </section>
  </div>
</div>

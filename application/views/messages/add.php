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
                        $params .= '?'.$key.'[]='.$pp;
                    }
                }
            } else {
                $params .= '?'.$key.'='.$param;
            }
        }
        ++$p_index;
    }
}

?>
<div id="add_message" class="container add-page">
  <?php if ($this->input->get_post('type') != 'push' and empty($data['send_available'])): ?>  
  <div class="row">
    <div class="col-12">
      <div class="alert alert-warning" role="alert">
        <button type="button" class="close" data-dismiss="alert"><span class="sr-only"><?php echo _('Close'); ?></span></button>
        <?php if ($data['use_default_id_key']): ?>
        <?php echo _('Disable Send SMS,Request Company'); ?>
        <?php else: ?>
        <?php echo _('First Charge Point, In Aligo'); ?>
        <?php endif; ?>
      </div>
    </div> 
  </div>
  <?php endif; ?>
  <div class="row">
    <section class="col-12">
      <div class="row" style="margin-bottom:10px">
        <h2 class="col-12"><?php echo _('Add Message'); ?></h2>
      </div>
      <article class="row">
        <div class="col-12">
          <?php include __DIR__.DIRECTORY_SEPARATOR.'form.php'; ?>
        </div>
      </article>
    </section>
  </div>
</div>

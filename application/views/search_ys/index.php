<?php

$params = '';
if ($this->input->get()) {
    $p_index = 0;
    foreach ($this->input->get() as $key => $param) {
        if ($p_index) {
            if (is_array($param)) {
                foreach ($param as $pp) {
                    $params .= '&amp;'.$key.'[]='.$pp;
                }
            } else {
                $params .= '&amp;'.$key.'='.$param;
            }
        } else {
            if (is_array($param)) {
                foreach ($param as $pi => $pp) {
                    if ($pi) {
                        $params .= '&amp;'.$key.'[]='.$pp;
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
<div id="search" class="container">
    <div class="row">
        <?php echo $Layout->Element('accounts/nav') ?>
    </div>
  <?php include __DIR__.DIRECTORY_SEPARATOR.'search_form.php'; ?>
  <?php include __DIR__.DIRECTORY_SEPARATOR.'list.php'; ?>
</div>

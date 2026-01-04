<?php

$params = '';
$return_url='/user-stop-requests';
if ($this->input->get()) {
    $p_index = 0;
    foreach ($this->input->get() as $key => $param) {
        if ($p_index) {
            if (is_array($param)) {
                foreach ($param as $pp) {
                    $params .= '&amp;'.$key.'[]='.$pp;
                    $return_url  .= '&'.$key.'[]='.$pp;
                }
            } else {
                $params .= '&amp;'.$key.'='.$param;
                $return_url  .= '&'.$key.'='.$param;
            }
        } else {
            if (is_array($param)) {
                foreach ($param as $pi => $pp) {
                    if ($pi) {
                        $params .= '&amp;'.$key.'[]='.$pp;
                        $return_url  .= '&'.$key.'[]='.$pp;
                    } else {
                        $params .= '?'.$key.'[]='.$pp;
                        $return_url  .= '?'.$key.'[]='.$pp;
                    }
                }
            } else {
                $params .= '?'.$key.'='.$param;
                $return_url  .= '?'.$key.'='.$param;
            }
        }
        ++$p_index;
    }
}
?>
<div id="user_stop_requests" class="container">
    <?php include __DIR__.DIRECTORY_SEPARATOR.'search_form.php' ?>
    <?php include __DIR__ . DIRECTORY_SEPARATOR . 'list.php' ?>
</div>

<?php
if ($this->session->userdata('show_omu')) {
    $checked = '(내 회원만 보기 상태)';
} else {
    $checked = '(전체회원 보기 상태)';
}

if($this->input->get('search')) {
    $checked.=' / 검색된 '.$data['user']['total'].'명';
}

$form_style = 'display:none';

$message_type = false;
if ($this->input->get('message_type')) {
    $message_type = $this->input->get('message_type');
}

?>
<?php if ($this->input->get('popup')): ?>
<div class="modal-header">
    <h3 class="modal-title"><?php echo _('Not Select User') ?></h3>
    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
        <span aria-hidden="true">&times;</span>
    </button>
</div>
<div class="modal-body">
<?php else: ?>
<div class="container">
<?php endif; ?>
    <div class="row">
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
                            $params .= $key.'[]='.$pp;
                        }
                    }
                } else {
                    $params .=$key.'='.$param;
                }
            }
            ++$p_index;
        }
    }
    
    ?>
  <input type="hidden" id="search_params" value="<?php echo $params ?>">
  <div class="col-12">
                <?php echo $Layout->Element('users/select_table_not_counsel'); ?>
    </div>
                <?php if ($this->input->get('popup')): ?>
                </div>
            </div>
            <div class="modal-footer">
                <?php echo form_submit('', _('Select'), array('id' => 'select', 'class' => 'btn btn-primary btn-block')); ?>
            </div>
            <script src="<?php echo $script; ?>"></script>
            <?php else: ?>
            <?php echo form_submit('', _('Select'), array('id' => 'select', 'class' => 'btn btn-primary btn-block btn-lg')); ?>
        </div>
    </div>
</div>
<?php endif; ?>

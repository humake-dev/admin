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
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3><?php echo _('Search User'); ?><?php echo $checked; ?></h3>
                    <div class="float-right buttons" style="cursor:pointer">
                        <i class="material-icons">keyboard_arrow_down</i>
                    </div>
                </div>
                <?php echo form_open('', array('id' => 'user_select_search', 'class' => 'card-body', 'style' => $form_style)); ?>
                <?php

                    if (!empty($message_type)) {
                        echo form_input(array(
                            'type' => 'hidden',
                            'id' => 'send_message_type',
                            'name' => 'type',
                            'value' => $message_type,
                        ));
                    }
                    ?>

                    <div class="form-row">
                        <div class="form-group col-4">
                            <label for="s_search_field"><?php echo _('Search Type'); ?></label>
                            <?php
                            $options = array('name' => _('User Name'), 'card_no' => _('Access Card No'), 'phone' => _('Phone'), 'birthday' => _('Birthday'));
                            $select = set_value('s_search_field', 'name');

                            echo form_dropdown('s_search_field', $options, $select, array('id' => 's_search_field', 'class' => 'form-control'));
                            ?>
                        </div>
                        <div class="form-group col-8">
                            <label for="s_search_word"><?php echo _('Search Word'); ?></label>
                            <div class="input-group">
                                <input type="search" id="s_search_word" name="search_word"
                                       value="<?php echo set_value('search_word'); ?>" class="form-control"
                                       placeholder="검색어를 넣어주세요"/>
                                <span class="input-group-btn">
                                <?php echo form_submit('', _('Search'),array('class'=>"btn btn-primary")) ?>
                                </span>
                            </div>
                        </div>
                    </div>
                    <?php echo form_close(); ?>
                </div>
                </div>
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
                <?php echo $Layout->Element('users/select_table'); ?>
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

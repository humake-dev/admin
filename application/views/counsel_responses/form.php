<?php 

$return_url=null;

if(!empty($this->input->get_post('return_url'))) {
    $return_url = $this->input->get_post('return_url');
}

echo form_open('',array('class'=>'card'),array('return_url'=>$return_url)) ?>
    <div class="card-body">
    <?php if($this->session->userdata('center_id')): ?>
    <div class="form-group">
                            <label for="c_manager_id"><?php echo _('Branch'); ?></label>
                            <div>
        <?php echo $data['content']['branch_name'] ?>
                    </div>
    </div>
    <?php endif ?>
    <div class="form-group">

    <?php
                        echo form_input(array(
                                                'id' => 'counsel_id',
                                                'name' => 'counsel_id',
                                                'value' => set_value('counsel_id',$this->input->get_post('counsel_id')),
                                                'type'=>'hidden'
                                            ));
                                            ?>

                            <label for="c_manager_id"><?php echo _('User'); ?></label>
                            <div>
        <?php echo $data['content']['user_name'] ?>
                    </div>
    </div>
    <div class="form-group">
                            <label for="c_manager_id"><?php echo _('Manager'); ?></label>
                            <?php
                        $options = array('' => _('Not Set'));
                        $select = set_value('manager_id', '');

                            foreach ($data['manager']['list'] as $value) {
                                $options[$value['id']] = $value['name'];
                            }

                        if (isset($data['content']['manager_id'])) {
                            $select = set_value('manager_id', $data['content']['manager_id']);
                        }

                        echo form_dropdown('manager_id', $options, $select, array('id' => 'c_manager_id', 'class' => 'form-control'));
                        ?>
                        </div>
                        <div class="form-group">
        <label for="mp_content"><?php echo _('Content') ?></labeL>
        <div>
        <?php echo nl2br($data['content']['content']) ?>
                    </div>
                    </div>
    <div class="form-group">
        <label for="mp_content"><?php echo _('Answer') ?></labeL>
        <?php

        $content_value = set_value('content');


        echo form_textarea(array('name' => 'content', 'id' => 'mp_content', 'value' => $content_value, 'rows' => '5', 'class' => 'form-control'));
        ?>
    </div>
    </div>
<?php echo form_submit('', _('Submit'), array('class' => 'btn btn-primary btn-block')) ?>
            <?php echo form_close(); ?>
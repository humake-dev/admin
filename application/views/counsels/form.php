<?php
if (empty($data['content']['id'])) {
    $form_url = 'counsels/add';
} else {
    $form_url = 'counsels/edit/' . $data['content']['id'];
}

$user_id = null;
$return_url=null;
if (!empty($data['content']['user_id'])) {
    $user_id = $data['content']['user_id'];

    if(!empty($this->input->get_post('return_type'))) {
        $return_url='/home/counsels/'.$user_id;
    }
}

?>
<section class="col-12">
    <div class="row">
        <h2 class="col-12">
            <?php if (isset($data['content']['id'])): ?>
                <?php echo _('Edit Counsel'); ?>
                &nbsp;&nbsp;<?php echo anchor('/counsels', _('Cancel Edit'), array('class' => 'float-right')); ?>
            <?php else: ?>
                <?php echo _('Add Counsel'); ?>
            <?php endif; ?>
        </h2>
    </div>
    <article class="row">
        <div class="col-12">
            <?php echo form_open($form_url, array('class' => 'counsel_form'),array('return_url'=>$return_url,'return_type'=>$this->input->get_post('return_type'))); ?>
            <div class="card">
                <div class="card-body">
                    <?php echo form_input(array('type' => 'hidden', 'id' => 'c_user_id', 'name' => 'user_id','value'=>$user_id)); ?>
                    <div class="form-row">
                        <?php if(!empty($data['manager']['total'])): ?>
                        <div class="col-12 form-group">
                            <label for="c_manager_id"><?php echo _('Manager'); ?></label>
                            <?php
                        $options = array('' => _('Select'));
                        $select = set_value('manager_id', '');

                            foreach ($data['manager']['list'] as $value) {
                                $options[$value['id']] = $value['name'];
                            }

                        if (isset($data['content']['manager_id'])) {
                            $select = set_value('manager', $data['content']['manager_id']);
                        }

                        echo form_dropdown('manager', $options, $select, array('id' => 'c_manager_id', 'class' => 'form-control'));
                        ?>
                        </div>
                        <?php endif ?>
                        <div class="col-6 form-group">
                            <label for="c_type"><?php echo _('Type'); ?></label>
                            <?php
                            $options = array('A' => _('Counsel By Phone'), 'E' => _('Counsel By Interview'));
                            $select = set_value('type', 'A');

                            if (isset($data['content']['type'])) {
                                $select = set_value('type', $data['content']['type']);
                            }
                            echo form_dropdown('type', $options, $select, array('id' => 'c_type', 'class' => 'form-control'));
                            ?>
                        </div>
                        <div class="col-6 form-group">
                            <label for="c_complete"><?php echo _('Counsel Result'); ?></label>
                            <?php
                            $options = array(0 => _('Processing'), 1 => _('Process Complete'));
                            if (isset($data['content']['complete'])) {
                                $select = $data['content']['complete'];
                            } else {
                                $select = set_value('complete', 0);
                            }

                            echo form_dropdown('complete', $options, $select, array('id' => 'c_complete', 'class' => 'form-control'));
                            ?>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="c_question_course"><?php echo _('Question Course'); ?></label>
                        <?php
                        $options = array('default' => _('Question Default'), 'pt' => _('Question PT'));

                        if($this->session->userdata('branch_id')==15) {
                            $options['golf']=_('Question Golf');
                        }

                        $select = set_value('question_course', '');

                        if (isset($data['content']['question_course'])) {
                            $select = set_value('question_course', $data['content']['question_course']);
                        }

                        echo form_dropdown('question_course', $options, $select, array('id' => 'c_question_course', 'class' => 'form-control'));
                        ?>
                    </div>
                    <div class="form-group">
                        <label for="c_counselor_id"><?php echo _('Counselor'); ?></label>
                        <?php
                        $options = array('' => _('Select'));
                        $select = set_value('counselor_id', '');

                        if (!empty($data['admin']['total'])) {
                            foreach ($data['admin']['list'] as $value) {
                                $options[$value['id']] = $value['name'];
                            }
                        }

                        if (isset($data['content']['counselor_id'])) {
                            $select = set_value('counselor', $data['content']['counselor_id']);
                        }

                        echo form_dropdown('counselor', $options, $select, array('id' => 'c_counselor_id', 'class' => 'form-control'));
                        ?>
                    </div>
                    <div class="form-group">
                        <label for="c_name"><?php echo _('Counsel User'); ?></label>
                        <?php

                        if (empty($data['content']['user_name'])) {
                            $default_user_value = '';
                        } else {
                            $default_user_value = $data['content']['user_name'];
                        }
                        ?>
                        <div class="input-group-prepend">
                            <?php
                            echo form_input(array(
                                'name' => 'name',
                                'id' => 'c_name',
                                'value' => set_value('name', $default_user_value),
                                'maxlength' => '60',
                                'size' => '60',
                                'required' => 'required',
                                'class' => 'form-control',
                            ));
                            ?>
                            <div class="input-group-text select-user" title="<?php echo _('Select From User'); ?>">
                                <span class="material-icons">account_box</span>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="c_phone"><?php echo _('Phone'); ?></label>
                        <?php

                        if (empty($data['content']['phone'])) {
                            $default_phone_value = '';
                        } else {
                            $default_phone_value = $data['content']['phone'];
                        }

                        echo form_input(array(
                            'name' => 'phone',
                            'id' => 'c_phone',
                            'value' => set_value('phone', $default_phone_value),
                            'maxlength' => '30',
                            'size' => '30',
                            'required' => 'required',
                            'class' => 'form-control',
                        ));
                        ?>
                    </div>
                    <div class="form-group">
                        <label for="c_date"><?php echo _('Counsel Date'); ?></label>
                        <div class="input-group-prepend date">
                            <?php

                            if (empty($data['content']['execute_date'])) {
                                $execute_date_default_value = $search_data['date'];
                            } else {
                                $execute_date_default_value = $data['content']['execute_date'];
                            }

                            echo form_input(array(
                                'name' => 'execute_date',
                                'id' => 'c_date',
                                'value' => set_value('execute_date', $execute_date_default_value),
                                'maxlength' => '30',
                                'size' => '30',
                                'required' => 'required',
                                'class' => 'form-control datepicker',
                            ));
                            ?>
                            <div class="input-group-text">
                                <span class="material-icons">date_range</span>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="c_content"><?php echo _('Content'); ?></label>
                        <?php

                        if (empty($data['content']['content'])) {
                            $default_content_value = '';
                        } else {
                            $default_content_value = $data['content']['content'];
                        }

                        echo form_textarea(array(
                            'name' => 'content',
                            'id' => 'c_content',
                            'value' => set_value('content', $default_content_value),
                            'rows' => '4',
                            'required' => 'required',
                            'class' => 'form-control',
                        ));
                        ?>
                    </div>
                </div>
            </div>
            <?php if ($this->router->fetch_method() == 'edit'): ?>
  <?php echo $Layout->Element('form_edit'); ?>
  <?php endif; ?>

            <div class="form-group">
                <?php echo form_submit('', _('Submit'), array('class' => 'btn btn-lg btn-primary btn-block')); ?>
            </div>
            <?php echo form_close(); ?>

        </div>
    </article>
</section>
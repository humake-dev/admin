<?php echo form_open('', ['method' => 'get', 'id' => 'search_counsel_default_form', 'class' => 'search_form col-12']); ?>
  <div class="form-row">
    <div class="col-12 col-lg-3 form-group">
      <label for="search_counselor"><?php echo _('Manager'); ?></label>
      <div id="select_manager_field" <?php if($this->input->get('no_manager')): ?> style="display:none"<?php endif ?>>
      <?php

        $default_manager_id = '';
        if (!empty($data['manager']['id'])) {
          $default_manager_id = $data['manager']['id'];
        }

        $manager_id=set_value('manager',$default_manager_id);

        echo form_input(['type' => 'hidden', 'id' => 'e_employee_id', 'name' => 'manager', 'value' => $manager_id]);

        $manager_name = set_value('manager_name', _('All'));

        if ($this->input->get('manager_name')) {
            $manager_name = $this->input->get('manager_name');
        }
      ?>
      <div class="input-group-prepend select-fc no-search" style="width:180px;cursor:pointer">
      <?php
        echo form_input([
          'name' => 'manager_name',
          'id' => 's_employee',
          'value' => $manager_name,
          'maxlength' => '60',
          'size' => '60',
          'readonly' => 'readonly',
          'required' => 'required',
          'class' => 'form-control',
          'style' => 'cursor:pointer'
        ]);
      ?>
        <div class="input-group-text">
        <span class="material-icons">account_box</span>
        </div>
        <span id="clear_employee" class="material-icons"<?php if (empty($manager_id)): ?> style="display:none"<?php endif; ?>>clear</span>
      </div>
      </div>
      <div>
          <label class="form-check-label" style="margin-left:20px">
        <?php
        $checked=false;
        
        if($this->input->get('no_manager')) {
          $checked=true;
        }

        echo form_checkbox([
          'name' => 'no_manager',
          'id' => 's_no_manager',
          'value' => 1,
          'checked' => $checked,
          'class' => 'form-check-input'
        ]);
      ?>
      <?php echo _('Search No Manager') ?>
      </label>
      </div>
    </div>
  </div>
  <div class="form-row">
    <div class="col-6 col-lg-3 form-group">
    <label for="c_type"><?php echo _('Type'); ?></label>
      <?php
        $options = ['' => _('All'), 'A' => _('Counsel By Phone'), 'E' => _('Counsel By Interview')];
        $select = set_value('type', '');
        echo form_dropdown('type', $options, $select, ['id' => 'c_type', 'class' => 'form-control']);
      ?>
    </div>    
    <div class="col-6 col-lg-3 form-group">
      <label for="c_complete"><?php echo _('Counsel Result'); ?></label>
      <?php
          $options = ['' => _('All'), '0' => _('Processing'), '1' => _('Process Complete')];
          $select = set_value('complete', '');
          echo form_dropdown('complete', $options, $select, ['id' => 'c_complete', 'class' => 'form-control']);
      ?>
    </div>
    <div class="col-6 col-lg-3 form-group">
    <label for="c_question_course"><?php echo _('Question Course'); ?></label>
    <?php
      $options = ['' => _('All'), 'default' => _('Question Default'), 'pt' => _('Question PT')];

      if($this->session->userdata('branch_id')==15) {
        $options['golf']=_('Question Golf');
      }

      $select = set_value('question_course', '');
      echo form_dropdown('question_course', $options, $select, ['id' => 'c_question_course', 'class' => 'form-control']);
    ?>      
    </div>
    </div>
    <div id="default_period_form" class="form-group">
          <label for="start_date"><?php echo _('Counsel Date'); ?></label>
          <div class="form-row">
            <?php echo $Layout->Element('search_period'); ?>
          </div>
  </div>
  <div class="form-group">
    <?php echo form_submit('', _('Search'), ['class' => 'btn btn-primary']); ?>
  </div>
<?php echo form_close(); ?>

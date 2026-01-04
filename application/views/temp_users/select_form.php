<div class="card">
  <div class="card-header">
    <ul class="nav nav-pills card-header-pills">
      <li class="nav-item"><a class="nav-link active" href="#"><?php echo _('Add Temp User'); ?></a></li>
      <li class="nav-item"><a class="nav-link" href="#"><?php echo _('Search'); ?></a></li>    
    </ul>
    <div class="float-right buttons">
      <i class="material-icons">keyboard_arrow_up</i>
    </div>
  </div>
  <div class="card-body">
    <?php echo form_open('/temp-users/add', array('id' => 'add_temp_user_form', 'class' => 'card-block')); ?>
      <div class="form-row">
        <div class="col-12 col-lg-6 form-group">
          <label for="temp_name"><?php echo _('Name'); ?></label>
          <?php echo form_input(array('name' => 'name', 'id' => 'temp_name', 'required' => 'required', 'class' => 'form-control')); ?>
        </div>
        <div class="col-12 col-lg-6 form-group">
          <label for="temp_phone"><?php echo _('Phone'); ?></label>
          <?php echo form_input(array('name' => 'phone', 'id' => 'temp_phone', 'required' => 'required', 'class' => 'form-control')); ?>        
        </div>
        <div class="col-12 form-group">
          <?php echo form_submit('', _('Submit'), array('class' => 'btn btn-primary btn-block')); ?>
        </div>
      </div>
    <?php echo form_close(); ?>
    <?php echo form_open('users', array('id' => 'user_select_search', 'class' => 'card-block', 'style' => 'display:none'), array('temp' => 1)); ?>
    <div class="form-row">
      <div class="form-group col-4">
        <label for="s_search_field"><?php echo _('Search Type'); ?></label>
        <?php

          $options = array('name' => _('User Name'), 'phone' => _('Phone'));
          $select = set_value('s_search_field', 'name');

          echo form_dropdown('s_search_field', $options, $select, array('id' => 's_search_field', 'class' => 'form-control'));
        ?>
      </div>
      <div class="form-group col-8">
        <label for="s_search_word"><?php echo _('Search Word'); ?></label>
        <div class="input-group">
          <input type="search" id="s_search_word" name="search_word" value="<?php echo set_value('search_word'); ?>" class="form-control" placeholder="검색어를 넣어주세요" />
          <span class="input-group-btn">
            <input type="submit" class="btn btn-primary" value="<?php echo _('Search'); ?>" />
          </span>
        </div>
      </div>
    </div>
    <?php echo form_close(); ?>
  </div>
</div>
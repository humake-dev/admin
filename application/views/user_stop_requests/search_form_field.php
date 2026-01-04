<?php echo form_open('', array('method'=>'get','id'=>'search_counsel_field_form','class'=>'search_form col-12')) ?>
<input type="hidden" name="search_type" value="field" />
<div class="row">
  <div class="col-12 form-group form-inline">
      <label for="s_type"><?php echo _('Check classification') ?></label>
      <?php
        $options=array('name'=>_('User Name'),'phone'=>_('Phone'));
        echo form_dropdown('search_field', $options,set_value('search_field'), array('id'=>'s_type','class'=>'form-control','style'=>'margin-left:20px'));
      ?>

      <div class="input-group"<?php if($this->input->get('search_field')=='birthday'): ?> style="margin-left:50px;display:none"<?php else: ?> style="margin-left:50px"<?php endif ?>>
        <?php
        echo form_input(array('type'=>'search','name'=>'search_word','value'=>set_value('search_word'),'placeHolder'=>_('Search Word'),'class'=>'form-control'));
        ?>
        <span class="input-group-btn">
          <?php echo form_submit('', _('Search'), array('class'=>'btn btn-primary')) ?>
        </span>
      </div>
      <?php if ($search_data['search']): ?>
        <?php anchor('/counsel?type=field', '검색조건 해제'); ?>
      <?php endif ?>
    </div>
  </div>
<?php echo form_close() ?>

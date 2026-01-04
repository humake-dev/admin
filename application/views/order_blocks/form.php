<?php

  if (empty($params)) {
      echo form_open('/order-blocks/add');
  } else {
      echo form_open('/order-blocks/add'.$params);
  }

?>
<div class="card">
  <div class="card-body">
<div class="form-group">
  <label for="m_content"><?php echo _('Apply User'); ?></label>
  <div class="form-row">
    <div class="col-12">
    <?php echo form_input(array('type' => 'hidden', 'name' => 'send_all', 'value' => '0')); ?>
  검색된 <?php echo $data['search_count']; ?><?php echo _('Count People'); ?>
    </div>
  </div>
</div>
<div class="form-group">
<label><?php echo _('Reference Date'); ?></label>
<?php

echo form_input(array(
  'type' => 'hidden',
  'name'=>'reference_date',
  'id' => 'ob-referece',
  'value' => $this->input->get('reference_date'),
  'class' => 'form-control ',
));
?>
<p><?php echo get_dt_format($this->input->get('reference_date')) ?></p>
</div>
<div id="select_not_user_layer" class="form-group">
  <label><?php echo _('Not Select User'); ?></label>
  <div class="not_users_input">
    <?php if ($data['user']['total']): ?>
      <?php foreach ($data['user']['list'] as $index => $user): ?>
        <span class="select_user text-success">
          <?php echo $user['name']; ?>
          <input type="hidden" name="user[]" value="<?php echo $user['id']; ?>">
          <span class="text-danger">X</span>
        </span>    
      <?php endforeach ?>
      <?php endif ?>
  </div>
  <div style="margin-top:10px">
    <?php echo anchor('/user-not-selects'.$params, _('Not Select User'), array('id' => 'user_select', 'class' => 'btn btn-secondary btn-modal')); ?>
  </div>  
</div>
<div class="form-group">
    <label><?php echo _('Course'); ?></label>
    <select id="course_id" name="product_id" class="form-control">
        <option value=""><?php echo _('Select'); ?></option>
        <?php
        if ($search_data['course_category']['total']):   
            foreach ($search_data['course_category']['list'] as $course_category):
                $primary_category=false;                     
                if (!empty($course_category['product_counts'])):

                    if (!empty($search_data['course']['total']) and !empty($search_data['product_relations']['total'])) {
                            foreach ($search_data['course']['list'] as $course) {
                                if ($course['product_category_id'] != $course_category['id']) {
                                    continue;
                                }
                                foreach ($search_data['product_relations']['list'] as $pr) {                                
                                if($pr['product_id']==$course['product_id']) {
                                    $primary_category=true;
                                }
                            }
                        }
                }
                ?>
                <optgroup label="<?php echo $course_category['title']; ?>">
                    <?php if(!empty($primary_category)): ?>
                            <option value="all_primary"<?php if ($this->input->get('product_id')): ?><?php if (in_array('all_primary', $this->input->get('product_id'))): ?> selected="selected"<?php endif; ?><?php endif; ?> style="font-weight:bold">** <?php echo $course_category['title']; ?> <?php echo _('All') ?> **</option>
                    <?php endif ?>
                    <?php if (!empty($search_data['course']['total'])):
                        $pt_list = array();

                        foreach ($search_data['course']['list'] as $course):
                            if ($course['lesson_type'] == 4) {
                                $pt_list[] = $course['product_id'];
                            }

                            ?>
                            <?php if ($course['product_category_id'] == $course_category['id']): ?>
                            <option value="<?php echo $course['product_id']; ?>"<?php if (isset($search_data['product_id']) AND !in_array('all_primary', $this->input->get('product_id'))): ?><?php if (in_array($course['product_id'], $search_data['product_id'])): ?> selected="selected"<?php endif; ?><?php endif; ?>><?php echo $course['title']; ?></option>
                        <?php endif; ?>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </optgroup>
                <?php endif; ?>                
            <?php endforeach; ?>
        <?php endif; ?>
    </select>

</div>

<div class="form-group">
                    <?php


                    $transaction_date_value = set_value('transaction_date', $search_data['today']);

                    $is_today = false;
                    if ($transaction_date_value == $search_data['today']) {
                        $is_today = true;
                    }
                    ?>
                    <label for="is_today"><?php echo _('Transaction Date'); ?>&nbsp;&nbsp;
                        <input id="is_today" type="checkbox" name="transaction_date_is_today" value="1"<?php if ($is_today): ?> checked="checked"<?php endif; ?>>
                        <?php echo _('Today'); ?>
                    </label>
                    <div id="o_transaction_date_layer"<?php if (!$is_today): ?> style="display:block"<?php endif; ?>>
                        <div class="input-group-prepend date">
                            <?php

                            echo form_input(array(
                                'name' => 'custom_transaction_date',
                                'value' => $transaction_date_value,
                                'class' => 'form-control enroll_datepicker',
                            ));
                            ?>
                            <div class="input-group-text">
                                <span class="material-icons">date_range</span>
                            </div>
                        </div>
                    </div>
                    <p id="today_display"<?php if (empty($is_today)): ?> style="display:none"<?php endif; ?>>
                        <label style="margin:0;padding:0"><?php echo get_dt_format($search_data['today'], $search_data['timezone']); ?></label>
                    </p>
                </div>

<div class="form-group">
<label for="ob-period"><?php echo _('Period'); ?>(<?php echo _('Day') ?>)</labeL>
<?php

echo form_input(array(
  'type' => 'number',
  'id' => 'ob-period',
  'name'=>'period',
  'min'=> '1',
  'max' => '365',
  'value' => set_value('period',1),
  'class' => 'form-control',
));
?>
</div>
<div class="form-group">
  <label for="ob-memo"><?php echo _('Memo'); ?></labeL>
    <?php

    echo form_textarea(array(
            'name' => 'memo',
            'id' => 'ob-memo',
            'value' =>  set_value('memo'),
            'rows' => '5',
            'class' => 'form-control',
    ));
    ?>
  </div>


    </div>
  </div>
  <div class="form-group">
    <?php echo form_button(array('type' => 'submit'),_('Submit'), array('class' => 'btn btn-lg btn-primary btn-block')); ?>
  </div>
<?php echo form_close(); ?>

<?php
  $is_edit_pt = false;
  if (!empty($data['content'])) {
      if ($data['content']['lesson_type'] != 1) {
          $is_edit_pt = true;
      }
  }

?>
<article class="row">
  <h3 class="col-12"><?php echo _('Course Info'); ?></h3> 
  <div class="col-12">
    <div class="card course_info">
      <div class="card-body">
      <?php

        $default_user_id = null;

        if (!empty($data['user_content'])) {
            $default_user_id = $data['user_content']['id'];
        }

        $value_user_id = set_value('user_id', $default_user_id);

        echo form_input(array(
          'type' => 'hidden',
          'id' => 'o_user_id',
          'name' => 'user_id',
          'value' => $value_user_id,
        ));
      ?>
      <div class="form-row">
      <?php if (!empty($data['content'])): ?>
        <input type="hidden" name="course_id" value="<?php echo $data['content']['course_id']; ?>" />
        <?php else: ?>      
      <div class="col-12" style="margin-bottom:15px">
        <label for="course_id"><?php echo _('Course'); ?></label>        
        <select name="course_id" id="course_id" class="form-control" required="required">
          <?php if ($data['course_category']['total']): ?>
          <option value=""><?php echo _('Select Course'); ?></option>
          <?php foreach ($data['course_category']['list'] as $course_category): ?>
            <optgroup label="<?php echo $course_category['title']; ?>">
              <?php if ($data['course']['total']): ?>
              <?php foreach ($data['course']['list'] as $course): ?>
              <?php if ($course['product_category_id'] == $course_category['id']): ?>
              <option value="<?php echo $course['id']; ?>"<?php if (!empty($data['product_content'])): ?><?php if ($data['product_content']['id'] == $course['id']): ?> selected="selected"<?php endif; ?><?php endif; ?>><?php echo $course['title']; ?></option>
              <?php endif; ?>
              <?php endforeach; ?>
              <?php endif; ?>
            </optgroup>
          <?php endforeach; ?>
          <?php endif; ?>          
        </select>     
      </div>
      <?php endif; ?>      
      <?php

      if (isset($data['product_content']['lesson_type'])) {
          $lesson_type = $data['product_content']['lesson_type'];
      } else {
          $lesson_type = 1;
      }

      echo form_input(array(
              'type' => 'hidden',
              'id' => 'e_lesson_type',
              'value' => $lesson_type,
      ));

      $value = '0';
      if (isset($data['product_content']['price'])) {
          $value = $data['product_content']['price'];
      }

      echo form_input(array(
              'type' => 'hidden',
              'id' => 'e_lesson_fee_unit',
              'value' => $value,
      ));

      $value = '1';

      if (isset($data['product_content']['lesson_period'])) {
          $value = $data['product_content']['lesson_period'];
      }

      echo form_input(array(
              'type' => 'hidden',
              'id' => 'e_lesson_period',
              'value' => $value,
      ));

      $value = 'M';

      if (isset($data['product_content']['lesson_period_unit'])) {
          $value = $data['product_content']['lesson_period_unit'];
      }

      echo form_input(array(
              'type' => 'hidden',
              'id' => 'e_lesson_period_unit',
              'value' => $value,
      ));

      $value = '1';

      if (isset($data['product_content']['lesson_quantity'])) {
          $value = $data['product_content']['lesson_quantity'];
      }

      echo form_input(array('type' => 'hidden', 'id' => 'e_lesson_quantity', 'value' => $value));

      ?>

  <div class="col-12 col-lg-4 form-group">
    <label for="e_category_title"><?php echo _('Course Category'); ?></label>
    <?php
    $value = '';
    if (isset($data['product_content']['category_title'])) {
        $value = $data['product_content']['category_title'];
    }

    echo form_input(array(
            'id' => 'e_category_title',
            'name' => 'product_category_name',
            'value' => $value,
            'class' => 'form-control-plaintext',
    ));
    ?>
  </div>
  <div class="col-12 col-lg-4 form-group">
    <label for="e_title"><?php echo _('Course'); ?></label>
    <?php
    $value = '';
    if (isset($data['product_content']['title'])) {
        $value = $data['product_content']['title'];
    }

    echo form_input(array(
            'id' => 'e_title',
            'name' => 'product_name',
            'value' => $value,
            'class' => 'form-control-plaintext',
    ));
    ?>
  </div>
  <div class="col-12 col-lg-4 form-group">
    <label for="e_dayofweek"><?php echo _('lesson_dayofweek'); ?></label>
    <?php
    $value = _('Unlimit');
    if (!empty($data['product_content']['lesson_dayofweek'])) {
        $value = dowtostr($data['product_content']['lesson_dayofweek']);
    }

    echo form_input(array(
            'id' => 'e_dayofweek',
            'value' => $value,
            'class' => 'form-control-plaintext',
    ));
    ?>
  </div>
  <div class="col-12 col-lg-4 form-group">
    <label for="e_quota"><?php echo _('Available Course Count'); ?>/<?php echo _('Quota'); ?></label>
    <?php
    $value = _('Unlimit');
    if (!empty($data['product_content']['quota'])) {
        $value = $data['product_content']['quota'];
    }

    echo form_input(array(
            'id' => 'e_quota',
            'value' => $value,
            'class' => 'form-control-plaintext',
    ));
    ?>
  </div>
  <div class="col-12 col-lg-4 form-group">
    <label for="e_lesson_fee"><?php echo _('Price'); ?></label>
    <?php

    if (isset($data['product_content']['price'])) {
        $value = number_format($data['product_content']['price'])._('Currency');
    } else {
        $value = set_value('lesson_fee');
    }

    echo form_input(array(
        'id' => 'e_lesson_fee_text',
        'value' => $value,
        'class' => 'form-control-plaintext',
    ));
    ?>
  </div>
  <div class="col-12 col-lg-4">
  <label for="re-order"><?php echo _('Re Enroll'); ?></label>  
  <?php

    $default_re_order_checked = false;

    if (!empty($data['content']['re_order'])) {
        $default_re_order_checked = true;
    }

    $re_order_checked = set_value('re_order', $default_re_order_checked);
    echo form_checkbox(array('type' => 'checkbox', 'id' => 're-order', 'name' => 're_order', 'value' => 1, 'checked' => $re_order_checked));

  ?>
  </div>
  </div>
</div>


</div>
</div>
</article>

<div class="form-row">
  <article class="col-12 col-xl-6 col-xxl-5">
    <h3><?php echo _('Enroll Default Info'); ?></h3>
    <div class="card">
      <div class="card-body">
        <div class="form-row">
          <?php if ($is_edit_pt): ?>
          <?php if ($this->session->userdata('role_id') < 3): ?>
          <?php $lesson_unit = get_lesson_unit($data['content']['lesson_type'], $data['content']['lesson_period_unit']); ?>
          <div class="col-8 form-group">
            <label for="e_insert_quantity" class="e_xl"><?php echo _('Insert Quantity'); ?></label>
            <?php
            for ($i = 1; $i <= 200; ++$i) {
                $options[$i] = $i.$lesson_unit;
            }

            $insert_qauntity_select = set_value('insert_quantity', $data['content']['insert_quantity']);
            echo form_dropdown('insert_quantity', $options, $insert_qauntity_select, array('id' => 'e_insert_quantity', 'class' => 'form-control'));
            ?>
          </div>
          <div class="col-6 form-group">
            <label for="e_use_quantity"><?php echo _('Use Quantity'); ?>(<?php echo anchor('/enroll_use_logs?enroll_id='.$data['content']['id'].'&amp;enroll_only=true&amp;date_p=all', '사용수량기록', array('target' => '_blank')); ?>)</label>
            <?php
            echo form_input(array(
                    'type' => 'number',
                    'name' => 'use_quantity',
                    'class' => 'form-control',
                    'min' => 0,
                    'step' => 1,
                    'id' => 'use_quantity_range',
                    'value' => set_value('use_quantity', $data['content']['use_quantity']),
                  ));
            ?>
          </div>
          <div class="col-6 form-group">
            <label for="e_quantity" class="e_xl"><?php echo _('Left Quantity'); ?></label>
            <?php

              $select = 1;
              if (isset($data['content'])) {
                  if ($data['content']['insert_quantity']) {
                      $select = $data['content']['insert_quantity'] * $data['content']['lesson_quantity'];
                  } else {
                      if ($data['content']['lesson_type'] == 1) {
                          $d1 = new DateTime($data['content']['start_date'], $search_data['timezone']);
                          $d2 = new DateTime($data['content']['end_date'], $search_data['timezone']);
                          $d2->modify('+1 Day');

                          $select = $d1->diff($d2)->m + ($d1->diff($d2)->y * 12);
                      }
                  }

                  if ($data['content']['lesson_type'] == 1) {
                      $options = array();
                      for ($i = 1; $i <= 200; ++$i) {
                          $options[$i * $data['content']['lesson_quantity']] = $i * $data['content']['lesson_quantity'].$lesson_unit;
                      }
                      echo form_dropdown('insert_quantity', $options, $select, array('id' => 'e_quantity', 'class' => 'form-control calc'));
                  } else {
                      $quantity = $data['content']['quantity'] - $data['content']['use_quantity'];
                      echo form_input(array(
                    'type' => 'number',
                    'name' => 'quantity',
                    'class' => 'form-control',
                    'min' => 0,
                    'step' => 1,
                    'id' => 'quantity_range',
                    'value' => $quantity,
                  ));

                      echo form_input(array(
                    'type' => 'hidden',
                    'id' => 'e_quantity',
                    'value' => $data['content']['insert_quantity'],
                  ));
                  }
              }
        ?>
          </div>
          <?php else: ?>
            <?php 
            echo form_input(array(
                    'type' => 'hidden',
                    'id' => 'e_quantity',
                    'value' => $data['content']['insert_quantity'],
                  ));
            ?>
          <?php endif; ?>
          <?php else: ?>
          <div class="col-12 form-group">
            <label for="e_quantity" class="e_xl"><?php echo _('Period'); ?></label>
          <?php

          if (empty($data['content']['insert_quantity'])) {
              $default_select = 1;
          } else {
              $default_select = $data['content']['insert_quantity'];
          }

          $select = set_value('insert_quantity', $default_select);

          if(empty($data['content']['lesson_period_unit'])) {
            $default_lesson_period_unit='M';
          } else {
            $default_lesson_period_unit=$data['content']['lesson_period_unit'];
          }

          $lesson_unit = get_lesson_unit(1,$default_lesson_period_unit);
                  $option = array();
                  for ($i = 1; $i <= 200; ++$i) {
                      $options[$i] = $i.$lesson_unit;
                  }

                  echo form_dropdown('insert_quantity', $options, $select, array('id' => 'e_quantity', 'class' => 'form-control calc'));
          ?>
          </div>
          <?php endif; ?>
        <div class="col-12 form-group">
            <label for="e_trainer"><?php echo _('Enroll Trainer'); ?></label>
            <?php if ($this->router->fetch_method() == 'add' or $this->session->userdata('role_id') < 6): ?>            
            <?php

              $default_trainer_id = null;

              if (empty($data['content'])) {
                  if (isset($data['user_content'])) {
                      $default_trainer_id = $data['user_content']['trainer_id'];
                  }
              } else {
                  $default_trainer_id = $data['content']['trainer_id'];
              }

              $select = set_value('trainer', $default_trainer_id);

              if (isset($data['trainer'])) {
                  $options = array('' => _('Not Inserted'));
                  if ($data['trainer']['total']) {
                      foreach ($data['trainer']['list'] as $value) {
                          $options_title = $value['name'];

                          if ($this->Acl->has_permission('employees')) {
                              $options_title .= ' / '.$value['commission_rate'].'%';
                          } else {
                              if ($this->session->userdata('admin_id') == $value['id']) {
                                  $options_title .= ' / '.$value['commission_rate'].'%';
                              }
                          }

                          $options[$value['id']] = $options_title;
                      }
                  }
              } else {
                  $options = array('' => _('Select'));
              }

              echo form_dropdown('trainer', $options, $select, array('id' => 'e_trainer', 'class' => 'form-control'));
              echo form_input(array(
                'type' => 'hidden',
                'id' => 'default_trainer_id',
                'value' => $select,
              ));

            ?>
<?php else: ?>
  <p class="form-control-text">
  <?php
  if (empty($data['content']['trainer_id'])) {
      echo _('Not Inserted');
  } else {
      echo $data['content']['trainer_name'];
  }
  ?>
  </p>
<?php endif; ?>
          </div>
          <div class="col-12 form-group quantity">
              <label for="o_start_date"><?php echo _('Start Date'); ?></label>
              <?php

              if (isset($data['content']['start_date'])) {
                  $value_start_date = $data['content']['start_date'];
              } else {
                  $value_start_date = set_value('start_date', $search_data['date']);
              }

              ?>
              <div class="input-group-prepend date">
                  <?php echo form_input(array(
                          'name' => 'start_date',
                          'id' => 'o_start_date',
                          'value' => $value_start_date,
                          'class' => 'form-control enroll_datepicker',
                  )); ?>
                  <div class="input-group-text">
                      <span class="material-icons">date_range</span>
                  </div>
              </div>
            </div>
          <div class="col-12 form-group quantity">
            <label for="o_end_date"><?php echo _('End Date'); ?></label>
            <p id="unlimit_end_date"<?php if (isset($data['content']['end_date'])): ?><?php if ($data['content']['end_date'] == $search_data['max_date']): ?> style="display:block"<?php endif; ?><?php endif; ?>><?php echo _('Unlimit'); ?></p>
            <div id="limit_end_date"<?php if (isset($data['content']['end_date'])): ?><?php if ($data['content']['end_date'] == $search_data['max_date']): ?> style="display:none"<?php endif; ?><?php endif; ?>>
              <div class="input-group-prepend date">
                <?php

                if (isset($data['content']['end_date'])) {
                    $value_end_date = $data['content']['end_date'];
                } else {
                    $value_end_date = set_value('end_date', $search_data['date']);
                }

                echo form_input(array(
                  'name' => 'end_date',
                  'id' => 'o_end_date',
                  'value' => $value_end_date,
                  'class' => 'form-control enroll_datepicker',
                ));
                ?>
                <div class="input-group-text">
                  <span class="material-icons">date_range</span>
                </div> 

              </div>
            </div>
          </div>      
          

          <div id="pt_serial" class="col-12 form-group"<?php if (empty($data['content']['lesson_type'])): ?> style="display:none"<?php else:?><?php if ($data['content']['lesson_type'] != 4):?> style="display:none"<?php endif; ?><?php endif; ?>>
              <label><?php echo _('PT Serial'); ?></label>
              <?php if ($this->session->userdata('role_id') < 4 or $this->router->fetch_method() == 'add'): ?>              
              <?php

if (isset($data['content']['pt_serial'])) {
    $default_value_pt_serial = $data['content']['pt_serial'];
} else {
    $default_value_pt_serial = null;
}

$value_pt_serial = set_value('pt_serial', $default_value_pt_serial);

echo form_input(array(
  'type' => 'number',
  'name' => 'pt_serial',
  'id' => 'pt_serial',
  'value' => $value_pt_serial,
  'class' => 'form-control',
));
?>
        <?php else: ?>
          <p class="form-control-text">
          <?php if (empty($data['content']['pt_serial'])): ?>
              <?php echo _('Not Inserted'); ?>
          <?php else: ?>
              <?php echo $data['content']['pt_serial']; ?>
          <?php endif; ?>
        </p>
        <?php endif; ?>
            </div>
        </div>
      </div>
    </div>
  </article>

  <?php
    echo $Layout->Element('form_account');

    if ($this->router->fetch_method() == 'add') {
        if ($this->Acl->has_permission('rents')) {
            echo $Layout->Element('form_option.php');
        }
    }
  ?>
</div>
<?php echo $Layout->Element('form_memo'); ?>
<?php if ($this->router->fetch_method() == 'edit'): ?>
<?php echo $Layout->Element('form_edit'); ?>
<?php endif; ?>

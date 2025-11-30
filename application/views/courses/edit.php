<div id="edit-courses" class="container edit-page">
  <div class="row">
    <?php include __DIR__.DIRECTORY_SEPARATOR.'aside.php'; ?>
    <div class="col-12 col-md-7 col-lg-8 col-xl-9 right_a">
      <h2><?php if (isset($data['content'])): ?><?php echo _('Training basic information management'); ?><?php else: ?><?php echo _('Training category name management'); ?><?php endif; ?></h2>
      <?php echo form_open('/courses/edit/'.$data['content']['id'], array('id' => 'course_edit_form')); ?>
      <article class="card">
        <div class="card-header">
          강습 수정 <span style="font-weight:normal">(<?php echo _('Course Category'); ?> :<?php echo $data['content']['category_title']; ?>/ <?php echo _('Course Name'); ?> : <?php echo $data['content']['title']; ?> )</span>
        </div>
        <div class="card-body">
          <div class="row">
            <div class="col-12 col-xl-6 form-group">
              <label for="name"><?php echo _('Course'); ?></label>
              <input type="text" id="c_title" name="title" value="<?php echo $data['content']['title']; ?>" class="form-control" />
            </div>
            <div class="col-12 col-xl-6">
              <div class="form-group">
                <label for=""><?php echo _('Lesson Type'); ?></label>
                <div>
                  <div class="form-check form-check-inline">
                    <label class="form-check-label">
                    <?php
                      $m_checked = false;
                      if (isset($data['content']['lesson_type'])) {
                          if ($data['content']['lesson_type'] == '1') {
                              $m_checked = true;
                          }
                      } else {
                          $m_checked = set_radio('lesson_type', '1');
                      }

                      echo form_radio(array('name' => 'lesson_type', 'value' => '1', 'checked' => $m_checked, 'class' => 'form-check-input'));
                    ?>
                    <?php echo get_lesson_type(1); ?>
                    </label>
                  </div>
                  <!-- <div class="form-check form-check-inline">
                    <label class="form-check-label">
                          <?php
                            $m_checked = false;
                          if (isset($data['content']['lesson_type'])) {
                              if ($data['content']['lesson_type'] == '3') {
                                  $m_checked = true;
                              }
                          } else {
                              $m_checked = set_radio('lesson_type', '3');
                          }

                          echo form_radio(array(
                                  'name' => 'lesson_type',
                                  'value' => '3',
                                  'checked' => $m_checked,
                                  'class' => 'form-check-input',
                          ));
                          ?> <?php echo get_lesson_type(3); ?>
                        </label>
                    </div> -->
                    <div class="form-check form-check-inline">
                        <label class="form-check-label">
                          <?php
                            $m_checked = false;
                          if (isset($data['content']['lesson_type'])) {
                              if ($data['content']['lesson_type'] == '4') {
                                  $m_checked = true;
                              }
                          } else {
                              $m_checked = set_radio('lesson_type', '4');
                          }

                          echo form_radio(array(
                                  'name' => 'lesson_type',
                                  'value' => '4',
                                  'checked' => $m_checked,
                                  'class' => 'form-check-input',
                          ));
                          ?> <?php echo get_lesson_type(4); ?>
                        </label>
                    </div>
                    <div class="form-check form-check-inline">
                        <label class="form-check-label">
                          <?php
                            $m_checked = false;
                          if (isset($data['content']['lesson_type'])) {
                              if ($data['content']['lesson_type'] == '5') {
                                  $m_checked = true;
                              }
                          } else {
                              $m_checked = set_radio('lesson_type', '5');
                          }

                          echo form_radio(array(
                                  'name' => 'lesson_type',
                                  'value' => '5',
                                  'checked' => $m_checked,
                                  'class' => 'form-check-input',
                          ));
                          ?> <?php echo get_lesson_type(5); ?>
                        </label>
                    </div>
                </div>
              </div>
            </div>

            <div class="col-12 col-lg-6 col-xl-3 form-group">
                <label for=""><?php echo _('Course Status'); ?></label>
                <div>
                <div class="form-check form-check-inline">
                  <label class="form-check-label">
                    <input class="form-check-input" type="radio" id="lesson_status_ing" name="status" value="1" <?php if ($data['content']['status'] == '1'): ?><?php echo set_radio('status', '1', true); ?><?php endif; ?>> <?php echo _('During lessons'); ?>
                  </label>
                </div>
                <div class="form-check form-check-inline">
                  <label class="form-check-label">
                    <input class="form-check-input" type="radio" id="lesson_status_end" name="status" value="0" <?php if ($data['content']['status'] == '0'): ?><?php echo set_radio('status', '0', true); ?><?php endif; ?>> <?php echo _('End of class'); ?>
                  </label>
                </div>
            </div>
          </div>
          <div id="user_reservation_layer" class="col-12 col-lg-6 col-xl-3 form-group" <?php if (in_array($data['content']['lesson_type'], array(4, 5))): ?>style="display:none"<?php endif; ?>>
              <label for=""><?php echo _('User scheduling capabilities'); ?></label>
              <div>
              <div class="form-check form-check-inline">
              <?php
                  $m_checked = false;
                    if (isset($data['content']['user_reservation'])) {
                        if ($data['content']['user_reservation'] == 1) {
                            $m_checked = true;
                        }
                    } else {
                        $m_checked = set_radio('user_reservation', '1');
                    }
                ?>
                <label class="form-check-label">
                <?php
                  echo form_radio(array(
                            'name' => 'user_reservation',
                            'value' => '1',
                            'checked' => $m_checked,
                            'class' => 'form-check-input',
                    ));
                ?><?php echo _('Use'); ?>
                </label>
              </div>
              <div class="form-check form-check-inline">
              <?php
                  $m_checked = false;
                    if (isset($data['content']['user_reservation'])) {
                        if ($data['content']['user_reservation'] == 0) {
                            $m_checked = true;
                        }
                    } else {
                        $m_checked = set_radio('user_reservation', '0', true);
                    }
                ?>              
                <label class="form-check-label">
                <?php
                  echo form_radio(array(
                            'name' => 'user_reservation',
                            'value' => '0',
                            'checked' => $m_checked,
                            'class' => 'form-check-input',
                    ));
                ?><?php echo _('Not Use'); ?>
                </label>
              </div>
          </div>
          </div>          
                <div class="col-12 col-lg-6 form-group">
                    <label for=""><?php echo _('Tuition fees'); ?></label>
                    <div class="form-row">
                        <div class="col-12 col-xl-9 form-group lesson_type"  style="display:none"><!-- 기간제 전용 -->
                            <label for="lesson_period"><?php echo _('Period'); ?></label>
                            <div class="form-row">
                              <div class="col-6">
                              <?php

if (empty($data['content']['lesson_period'])) {
    $lp_value = set_value('lesson_period', 1);
} else {
    $lp_value = set_value('lesson_period', $data['content']['lesson_period']);
}

echo form_input(array(
    'type' => 'number',
    'name' => 'lesson_period',
    'id' => 'lesson_period',
    'value' => $lp_value,
    'maxlength' => '360',
    'min' => '1',
    'class' => 'form-control lesson_period',
));
                                ?>
                              </div>
                              <div class="col-6">
                              <select name="lesson_period_unit" class="form-control">
                                <option value="M"<?php if ($data['content']['lesson_period_unit'] == 'M'): ?> selected="selected"<?php endif; ?>><?php echo _('Month'); ?></option>
                                <option value="W"<?php if ($data['content']['lesson_period_unit'] == 'W'): ?> selected="selected"<?php endif; ?>><?php echo _('Week'); ?></option>
                                <option value="D"<?php if ($data['content']['lesson_period_unit'] == 'D'): ?> selected="selected"<?php endif; ?>><?php echo _('Day'); ?></option>
                            </select>
                              </div>
                            </div>
                        </div>

                        <div class="col-12 col-xl-9 form-group lesson_type"><!-- 쿠폰제, 횟수제 전용 -->
                            <label for="lesson_quantity"><?php echo _('Unit'); ?>(<span class="lesson_unit"></span>)</label>
                            <input type="text" id="lesson_quantity" name="lesson_quantity" placeholder="0" value="<?php echo set_value('lesson_quantity', $data['content']['lesson_quantity']); ?>" class="form-control">

                        </div>

                        <div class="col-12 col-xl-3 form-group"><!-- 공통 -->
                            <label for="c_price"><?php echo _('Unit Price'); ?>(<?php echo _('Currency'); ?>)</label>
                            <input type="number" step="100" id="c_price" name="price" value="<?php echo set_value('price', $data['content']['price']); ?>" class="form-control right currency">
                        </div>
                      </div>
                </div>

                <div id="cn_time" class="col-12 col-lg-6 form-group"><!-- 쿠폰, 횟수제 일때 기간정보 -->
                    <label for=""><?php echo _('Period information'); ?></label>
                    <div class="form-row">
                        <div class="col-12">
                          <div class="form-check form-check-inline">
                            <label class="form-check-label">
                              <input type="radio" name="lesson_time_type" value="1" <?php if ($data['content']['lesson_period'] == '0'): ?> checked="checked"<?php endif; ?>> <?php echo _('For an indefinite period'); ?>
                            </label>
                          </div>
                          <div class="form-check form-check-inline">
                            <label class="form-check-label">
                              <input type="radio" name="lesson_time_type" value="2" <?php if ($data['content']['lesson_period'] != '0'): ?> checked="checked"<?php endif; ?>> <?php echo _('Limited Period'); ?>
                            </label>
                          </div>
                        </div>

                        <div class="col-12 form-group" id="lesson_time_type_x">
                          <div class="form-row">
                            <div class="col-6">
                            <input type="number" id="lesson_period" name="lesson_period" class="lesson_period form-control"  value="<?php echo set_value('lesson_period', $data['content']['lesson_period']); ?>">
                            </div>
                            <div class="col-6">
                            <select name="lesson_period_unit" class="form-control">
                                <option value="M"<?php if ($data['content']['lesson_period_unit'] == 'M'): ?> selected="selected"<?php endif; ?>><?php echo _('Month'); ?></option>
                                <option value="W"<?php if ($data['content']['lesson_period_unit'] == 'W'): ?> selected="selected"<?php endif; ?>><?php echo _('Week'); ?></option>
                                <option value="D"<?php if ($data['content']['lesson_period_unit'] == 'D'): ?> selected="selected"<?php endif; ?>><?php echo _('Day'); ?></option>
                            </select> <?php echo _('From the start'); ?>
                          </div>
                          </div>
                        </div>
                    </div>
                </div>

                <div class="col-12 col-lg-6 form-group">
                  <label for=""><?php echo _('Enrollment quota'); ?>(<?php echo _('Person'); ?>)</label>
                  <input type="number" id="quota" min="0" name="quota" class="form-control" value="<?php echo set_value('quota', $data['content']['quota']); ?>" />
                </div>

<?php

    $lesson_dayofweek = $data['content']['lesson_dayofweek'];
    $dow = array('', '', '', '', '', '', '');

        $dow[0] = (strpos($lesson_dayofweek, '0') !== false) ? 'checked' : ''; // 일
        $dow[1] = (strpos($lesson_dayofweek, '1') !== false) ? 'checked' : ''; // 월
        $dow[2] = (strpos($lesson_dayofweek, '2') !== false) ? 'checked' : ''; // 화
        $dow[3] = (strpos($lesson_dayofweek, '3') !== false) ? 'checked' : ''; // 수
        $dow[4] = (strpos($lesson_dayofweek, '4') !== false) ? 'checked' : ''; // 목
        $dow[5] = (strpos($lesson_dayofweek, '5') !== false) ? 'checked' : ''; // 금
        $dow[6] = (strpos($lesson_dayofweek, '6') !== false) ? 'checked' : ''; // 토
    ?>
                <div class="col-12 col-lg-6 form-group">
                    <label for=""><?php echo _('Course Day'); ?></label>
                    <div>
                            <div class="form-check form-check-inline">
                              <label class="form-check-label">
                                <input class="form-check-input" type="checkbox" id="lesson_dow_1" name="lesson_dow[]" value="1" <?php echo $dow[1]; ?>> <?php echo _('Simple Monday'); ?>
                              </label>
                            </div>
                            <div class="form-check form-check-inline">
                              <label class="form-check-label">
                                <input class="form-check-input" type="checkbox" id="lesson_dow_1" name="lesson_dow[]" value="2" <?php echo $dow[2]; ?>> <?php echo _('Simple Tuesday'); ?>
                              </label>
                            </div>
                            <div class="form-check form-check-inline">
                              <label class="form-check-label">
                                <input class="form-check-input" type="checkbox" id="lesson_dow_1" name="lesson_dow[]" value="3" <?php echo $dow[3]; ?>> <?php echo _('Simple Wednesday'); ?>
                              </label>
                            </div>
                            <div class="form-check form-check-inline">
                              <label class="form-check-label">
                                <input class="form-check-input" type="checkbox" id="lesson_dow_1" name="lesson_dow[]" value="4" <?php echo $dow[4]; ?>> <?php echo _('Simple Thursday'); ?>
                              </label>
                            </div>
                            <div class="form-check form-check-inline">
                              <label class="form-check-label">
                                <input class="form-check-input" type="checkbox" id="lesson_dow_1" name="lesson_dow[]" value="5" <?php echo $dow[5]; ?>> <?php echo _('Simple Friday'); ?>
                              </label>
                            </div>
                            <div class="form-check form-check-inline">
                              <label class="form-check-label">
                                <input class="form-check-input" type="checkbox" id="lesson_dow_1" name="lesson_dow[]" value="6" <?php echo $dow[6]; ?>> <?php echo _('Simple Saturday'); ?>
                              </label>
                            </div>
                            <div class="form-check form-check-inline">
                              <label class="form-check-label">
                                <input class="form-check-input" type="checkbox" id="lesson_dow_1" name="lesson_dow[]" value="0" <?php echo $dow[0]; ?>> <?php echo _('Simple Sunday'); ?>
                              </label>
                            </div>
                    </div>
                </div>
                <div class="col-12 col-lg-6 form-group">
                  <label for=""><?php echo _('Progress Time'); ?>(<?php echo _('Minute'); ?>)</label>
                  <?php

if (empty($data['content']['progress_time'])) {
    $pg_value = set_value('lesson_pprogress_timeeriod', 1);
} else {
    $pg_value = set_value('lesson_peprogress_timeriod', $data['content']['progress_time']);
}

echo form_input(array(
    'type' => 'number',
    'name' => 'progress_time',
    'id' => 'progress_time',
    'value' => $pg_value,
    'maxlength' => '360',
    'min' => '0',
    'class' => 'form-control',
));
                                ?>
                </div>
                <div class="col-12 col-lg-6 form-group" id="trainer_select_row">
                    <label for=""><?php echo _('User Trainer'); ?></label>
                    <div class="form-row">
                      <div class="col-6">
                        <select id="trainer_select" name="trainer_id" class="form-control">
                            <?php if ($data['admin']['total']): ?>
                            <option value=""><?php echo _('Select'); ?></option>
                            <?php foreach ($data['admin']['list'] as $trainer): ?>
                            <option value="<?php echo $trainer['id']; ?>"<?php if ($data['content']['trainer_id'] == $trainer['id']): ?> selected="selected"<?php endif; ?>>
                                <?php echo $trainer['name']; ?>
                            </option>
                            <?php endforeach; ?>
                            <?php else: ?>
                            <option value=""><?php echo _('No staff'); ?></option>
                            <?php endif; ?>
                        </select>
                      </div>
                      <div class="col-6">
                        <input type="text" id="trainer_info" class="form-control"  readonly>
                      </div>
                    </div>
                </div>

            <div class="col-12 col-lg-6 form-group">
              <label for=""><?php echo _('Primary Product'); ?></label>
              <?php
                $default_pc_checked = 0;

                if (!empty($data['content']['primary_course'])) {
                    $default_pc_checked = 1;
                }

                $pc_checked = set_value('primary_course', $default_pc_checked);

              ?>
              <div class="form-check">
                <label class="form-check-label">
                <?php
                  echo form_checkbox(array(
                    'name' => 'primary_course',
                    'id' => 'primary_course',
                    'value' => 1,
                    'checked' => $pc_checked,
                    'class' => 'form-check-input',
                    ));
                ?>
            <?php echo _('Primary Product'); ?>
            </label>
              </div>        
                </div>
            <div class="col-12 col-lg-6 form-group">
                <label for=""><?php echo _('Order No'); ?></label>
      <?php
  if (isset($data['content']['order_no'])) {
      $value = $data['content']['order_no'];
  } else {
      if ($data['total']) {
          $default = $data['total'] + 1;
      } else {
          $default = 1;
      }
      $value = set_value('order_no', $default);
  }
  echo form_input(array(
          'type' => 'number',
          'name' => 'order_no',
          'id' => 'f_order',
          'value' => $value,
          'min' => '1',
          'class' => 'form-control',
  ));
  ?>
</div>
            </div>
              </article>
             
<article class="card user_reservation"<?php if (!in_array($data['content']['lesson_type'], array(4, 5))):?> style="display:none"<?php endif; ?>>
  <div class="card-header">
    <h3><?php echo _('Setting Course Class Group'); ?></h3>    
  </div>
  <div class="card-body">
    <?php if ($data['c_group']['total']): ?>
    <?php foreach ($data['c_group']['list'] as $cg_index => $c_group): ?>
      <div class="form-group col-12">
      <label>
      <?php

        $m_checked = false;
        if (!empty($data['content']['c_group']['total'])) {
            foreach ($data['content']['c_group']['list'] as $content_c_group) {
                if ($content_c_group['course_class_group_id'] == $c_group['id']) {
                    $m_checked = true;
                }
            }
        }

        echo form_checkbox(array(
                'name' => 'course_class_groups[]',
                'value' => $c_group['id'],
                'checked' => $m_checked,
                'class' => 'form-check-input',
        ));

        ?>      
        <?php echo $c_group['title']; ?></label>

      </div>
    <?php endforeach; ?>
    <?php endif; ?>
  </div>
</article>

 <article class="card user_reservation"<?php if (!in_array($data['content']['lesson_type'], array(4, 5))):?> style="display:none"<?php endif; ?>>
      <div class="card-header">
          <h3><?php echo _('Limit Setting'); ?></h3>
      </div>
      <div class="card-body">
                <!-- <div class="row">
                <div  class="col-4 form-group">
                  <label for="c_limit_reservation_type"><?php echo _('Limit Start Reservation Type'); ?></label>
                  <?php
            $options = array('day' => _('Day'), 'time' => _('Time'), 'dayntime' => _('Day And Time'));

            $default_limit_start_reservation_type = 'day';
            if (isset($data['content']['limit_start_reservation_type'])) {
                $default_limit_start_reservation_type = $data['content']['limit_start_reservation_type'];
            }

            $select = set_value('limit_start_reservation_type', $default_limit_start_reservation_type);

            echo form_dropdown('limit_start_reservation_type', $options, $select, array('id' => 'c_limit_start_reservation_type', 'class' => 'form-control'));
        ?>
                </div>
                <div class="col-4 form-group select-date"<?php if ($default_limit_start_reservation_type == 'time'):?> style="display:none"<?php endif; ?>>
                  <label for="limit_start_reservation_day"><?php echo _('Limit Start Reservation Day'); ?></label>
                  <?php

                    $limit_start_reservation_day_value = set_value('limit_start_reservation_day');

                    if (!$limit_start_reservation_day_value) {
                        if (isset($data['content']['limit_start_reservation_day'])) {
                            $limit_start_reservation_day_value = $data['content']['limit_start_reservation_day'];
                        }
                    }

                    echo form_input(array(
                      'type' => 'number',
                      'name' => 'limit_start_reservation_day',
                      'id' => 'c_limit_start_reservation_day',
                      'value' => $limit_start_reservation_day_value,
                      'min' => '0',
                      'range' => '1',
                      'class' => 'form-control',
                    ));
                  ?>
                </div>
                <div  class="col-4 form-group select-time"<?php if ($default_limit_start_reservation_type == 'day'):?> style="display:none"<?php endif; ?>>
                  <label><?php echo _('Limit Start Reservation Time'); ?></label>
                  <?php

                    $limit_start_reservation_time_value = set_value('limit_start_reservation_time', '06:00');

                    if (!$limit_start_reservation_time_value) {
                        if (isset($data['content']['limit_start_reservation_time'])) {
                            $limit_start_reservation_time_value = $data['content']['limit_start_reservation_time'];
                        }
                    }

                    echo form_input(array(
                      'name' => 'limit_start_reservation_time',
                      'id' => 'c_limit_start_reservation_time',
                      'value' => $limit_start_reservation_time_value,
                      'class' => 'form-control timepicker_limit"',
                    ));
                  ?>                  
                </div> 
                </div> -->


                <div class="row">
                <div  class="col-4 form-group">
                  <label for="c_limit_end_reservation_type"><?php echo _('Limit End Reservation Type'); ?></label>
                  <?php
            $options = array('day' => _('Day'), 'time' => _('Time'), 'dayntime' => _('Day And Time'));

            $default_limit_end_reservation_type = 'day';
            if (isset($data['content']['limit_end_reservation_type'])) {
                $default_limit_end_reservation_type = $data['content']['limit_end_reservation_type'];
            }

            $select = set_value('limit_end_reservation_type', $default_limit_end_reservation_type);

            echo form_dropdown('limit_end_reservation_type', $options, $select, array('id' => 'c_limit_end_reservation_type', 'class' => 'form-control'));
        ?>
                </div>
                <div class="col-4 form-group select-date"<?php if ($default_limit_end_reservation_type == 'time'):?> style="display:none"<?php endif; ?>>
                  <label for="limit_end_reservation_day"><?php echo _('Limit End Reservation Day'); ?></label>
                  <?php

                    $limit_end_reservation_day_value = set_value('limit_end_reservation_day');

                    if (!$limit_end_reservation_day_value) {
                        if (isset($data['content']['limit_end_reservation_day'])) {
                            $limit_end_reservation_day_value = $data['content']['limit_end_reservation_day'];
                        }
                    }

                    echo form_input(array(
                      'type' => 'number',
                      'name' => 'limit_end_reservation_day',
                      'id' => 'c_limit_end_reservation_day',
                      'value' => $limit_end_reservation_day_value,
                      'min' => '0',
                      'range' => '1',
                      'class' => 'form-control',
                    ));
                  ?>
                </div>
                <div  class="col-4 form-group select-time"<?php if ($default_limit_end_reservation_type == 'day'):?> style="display:none"<?php endif; ?>>
                  <label><?php echo _('Limit End Reservation Time'); ?></label>
                  <?php

                    $limit_end_reservation_time_value = set_value('limit_end_reservation_time', '06:00');

                    if (!$limit_end_reservation_time_value) {
                        if (isset($data['content']['limit_end_reservation_time'])) {
                            $limit_end_reservation_time_value = $data['content']['limit_end_reservation_time'];
                        }
                    }

                    echo form_input(array(
                      'name' => 'limit_end_reservation_time',
                      'id' => 'c_limit_end_reservation_time',
                      'value' => $limit_end_reservation_time_value,
                      'class' => 'form-control timepicker_limit"',
                    ));
                  ?>
                </div>                  
                </div>                

                <div class="row">
                <div  class="col-4 form-group">
                  <label for="c_limit_cancel_type"><?php echo _('Limit Cancel Type'); ?></label>
        <?php

            $default_limit_cancel_type = 'day';
            if (isset($data['content']['limit_cancel_type'])) {
                $default_limit_cancel_type = $data['content']['limit_cancel_type'];
            }

            $select = set_value('limit_cancel_type', $default_limit_cancel_type);

            echo form_dropdown('limit_cancel_type', $options, $select, array('id' => 'c_limit_cancel_type', 'class' => 'form-control'));
        ?>
                </div>
                <div  class="col-4 form-group select-date"<?php if ($default_limit_cancel_type == 'time'):?> style="display:none"<?php endif; ?>>
                  <label><?php echo _('Limit Cancel Day'); ?></label>
                  <?php

                    $limit_cancel_day_value = set_value('limit_cancel_day');

                    if (!$limit_cancel_day_value) {
                        if (isset($data['content']['limit_cancel_day'])) {
                            $limit_cancel_day_value = $data['content']['limit_cancel_day'];
                        }
                    }

                    echo form_input(array(
                      'type' => 'number',
                      'name' => 'limit_cancel_day',
                      'id' => 'c_limit_cancel_day',
                      'value' => $limit_cancel_day_value,
                      'min' => '0',
                      'range' => '1',
                      'class' => 'form-control',
                    ));
                  ?>
                </div>
                <div  class="col-4 form-group select-time"<?php if ($default_limit_cancel_type == 'day'):?> style="display:none"<?php endif; ?>>
                  <label><?php echo _('Limit Cancel Time'); ?></label>
                  <?php

                    $limit_cancel_time_value = set_value('limit_cancel_time', '06:00');

                    if (!$limit_cancel_time_value) {
                        if (isset($data['content']['limit_cancel_time'])) {
                            $limit_cancel_time_value = $data['content']['limit_cancel_time'];
                        }
                    }

                    echo form_input(array(
                      'name' => 'limit_cancel_time',
                      'id' => 'c_limit_cancel_time',
                      'value' => $limit_cancel_time_value,
                      'class' => 'form-control timepicker_limit"',
                    ));
                  ?>
                </div>
                </div>        
              </div>
              </article>
              <article class="card">
      <div class="card-header" style="cursor: pointer;">
          <ul class="nav nav-pills card-header-pills">
            <li class="nav-item"><a class="nav-link" href="#"><?php echo _('Memo'); ?></a></li>
          </ul>
              <div class="float-right buttons">
                <i class="material-icons">keyboard_arrow_down</i>
              </div>
              </div>
              <div class="card-body" style="display:none">

                <div class="card-block">
                <div class="row">


                  <div class="col-12 form-group">
                    <?php

                    if (isset($data['content']['content'])) {
                        $value = $data['content']['content'];
                    } else {
                        $value = set_value('content');
                    }
                    $memo_attr = array(
                            'name' => 'content',
                            'id' => 'c_memo',
                            'value' => $value,
                            'rows' => 3,
                            'class' => 'form-control',
                    );

                    echo form_textarea($memo_attr);
                    ?>
                  </div>


        </div>
            </div>
        </article>
              <?php echo form_submit('', _('Edit'), array('class' => 'btn btn-primary')); ?>
              <div class="btns float-right" style="clear:both">
                <?php echo anchor('/courses/delete/'.$data['content']['id'], _('Delete'), array('class' => 'btn btn-danger')); ?>
        </div>
      </div>              
          <?php echo form_close(); ?>
    </div>
  </div>
</div>

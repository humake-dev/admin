<?php
  $level_name='';
  $birthday='';
  $gender='';
  $name='';
  $attendance_count='';

  if(!empty($data['content']['level_name'])) {
    $level_name=$data['content']['level_name'];
  }

  if(!empty($data['content']['birthday'])) {
    $birthday=get_dt_format($data['content']['birthday'], $search_data['timezone']);
  }

  if(!empty($data['content']['gender'])) {
    $gender=display_gender($data['content']['gender']);
  }

  if(!empty($data['content']['name'])) {
    $name=$data['content']['name'];
  }

  if(!empty($data['content']['entrance_total'])) {
    $attendance_count=anchor('/home/attendances/'.$data['content']['user_id'], $data['content']['entrance_total'].' '._('Count Time'), array('target' => '_blank'));
  }

?>
<div class="col-12 col-lg-9">
  <div class="row">
      <h2 class="col-12<?php if (!empty($data['use_entrance_analysis'])): ?> no_display_title<?php endif; ?>"><?php echo _('View User Entrance'); ?></h2> 
  </div>
  <div class="row">
      <div class="col-12">
        <article class="card">
          <h3 class="card-header"><?php echo _('User Info'); ?></h3>
          <div class="card-body">
            <div class="row">
            <div class="col-12 col-sm-6 col-md-4 col-xl-3 col-xxl-2">
            <?php if (!empty($data['content']['picture_url'])): ?>
            <a href="<?php echo getPhotoPath('user', $data['content']['branch_id'], $data['content']['picture_url']); ?>" class="simple_image">
            <?php endif; ?>

            <?php if (empty($data['content']['picture_url'])): ?>
            <i class="material-icons" style="font-size:50px;vertical-align:middle">face</i>
            <?php else: ?>
            <img id="profile_photo" src="<?php echo getPhotoPath('user', $data['content']['branch_id'], $data['content']['picture_url'], 'medium'); ?>" width="100%" height="175" />
            <?php endif; ?>

            <?php if (!empty($data['content']['picture_url'])): ?>
            </a>
            <?php endif; ?>
            </div>
            <div class="col-12 col-sm-6 col-md-8 col-col-xl-9 col-xxl-10">
              <div class="row">
              <div class="col-12 col-md-6 col-xl-4 form-group">
                    <label for=""><?php echo _('Level'); ?></label>
                    <input type="text" value="<?php echo $level_name ?>" class="form-control-plaintext"  />
                </div>
              <div class="col-12 col-md-6 col-xl-4 form-group">
                  <label for=""><?php echo _('Birthday'); ?></label>
                  <input type="text" value="<?php echo $birthday ?>" class="form-control-plaintext"  />
              </div>
              <div class="col-12 col-md-6 col-xl-4 form-group">
                  <label for=""><?php echo _('Gender'); ?>/<?php echo _('Age'); ?></label>
                  <input type="text" id="" value="<?php echo $gender ?>  / <?php if (empty($data['content']['birthday']) or $data['content']['birthday'] == '0000-00-00'): ?>생년월일 필요<?php else: ?><?php echo age($data['content']['birthday']); ?><?php endif; ?>" class="form-control-plaintext"  />
              </div>
              <?php if (!empty($common_data['branch']['use_access_card'])): ?>                
              <div class="col-12 col-md-6 col-xl-4 form-group">
                <label for="card_no"><?php echo _('Access Card No'); ?></label>
                <?php
                  $card_value = '';

                  if (isset($data['content']['card_no'])) {
                      $card_value = get_card_no($data['content']['card_no'], false);
                  }

                ?>
                <input type="text" id="card_no" name="card_no" class="form-control-plaintext" value="<?php echo $card_value; ?>" />
              </div>
              <?php endif; ?>
              <div class="col-12 col-md-6 col-xl-4 form-group">
                <label for="card_no"><?php echo _('Name'); ?></label>
                <input type="text" id="card_no" name="card_no" class="form-control-plaintext" value="<?php echo $name; ?>" />
              </div>
              <div class="col-12 col-md-6 col-xl-4 form-group">
                <label for=""><?php echo _('Phone'); ?></label>
                <?php

                $phone_value = _('Not Inserted');
                if (!empty($data['content']['phone'])) {
                    $phone_value = get_hyphen_phone($data['content']['phone']);
                }
                ?>
                <input type="text" class="form-control-plaintext" value="<?php echo $phone_value; ?>" />
              </div>
              <div class="col-12 col-md-6 col-xl-4 form-group">
                  <label for=""><?php echo _('Email'); ?></label>
                  <input type="text" value="<?php if (empty($data['content']['email'])): ?><?php echo _('Not Inserted'); ?><?php else: ?><?php echo $data['content']['email']; ?><?php endif; ?>" class="form-control-plaintext"  />
              </div>
              <div class="col-12 col-md-6 col-xl-4 form-group">
                  <label for=""><?php echo _('In Time'); ?></label>
                  <?php
                    if (empty($data['content']['in_time'])) {
                        $in_time_value = _('Not Inserted');
                    } else {
                        $inTimeObj = new DateTime($data['content']['in_time'], $search_data['timezone']);

                        if ($inTimeObj->format('Y') == $search_data['current_year']) {
                            $in_time_value = get_dt_format($data['content']['in_time'], $search_data['timezone'], 'n'._('Month').' j'._('Day').' G'._('Hour').'i'._('Minute'));
                        } else {
                            $in_time_value = get_dt_format($data['content']['in_time'], $search_data['timezone'], 'Y'._('Year').' n'._('Month').' j'._('Day'));
                        }
                    }
                  ?>
                  <input type="text" value="<?php echo $in_time_value; ?>" class="form-control-plaintext"  />
              </div>
              <div class="col-12 col-md-6 col-xl-4 form-group">
                  <label for=""><?php echo _('In Count'); ?></label>
                  <br />
                  <?php echo $attendance_count; ?>
              </div>
              <div class="col-12 col-md-6 col-xl-4 form-group">
                  <label for=""><?php echo _('User Trainer'); ?></label>
                  <?php if (empty($data['content']['trainer_name'])): ?>
                  <input type="text" value="<?php echo _('Not Inserted'); ?>" class="form-control-plaintext"  />
                  <?php else: ?>
                  <input type="text" value="<?php echo $data['content']['trainer_name']; ?>" class="form-control-plaintext"  />
                  <?php endif; ?>
              </div>
              <div class="col-12 col-md-6 col-xl-4 form-group">
                  <label for=""><?php echo _('User FC'); ?></label>
                  <?php if (empty($data['content']['fc_name'])): ?>
                  <input type="text" value="<?php echo _('Not Inserted'); ?>" class="form-control-plaintext"  />
                  <?php else: ?>
                  <input type="text" value="<?php echo $data['content']['fc_name']; ?>" class="form-control-plaintext"  />
                  <?php endif; ?>
              </div>
            </div>
          </div>
        </div>
        </div>
      </article>
      </div>
  </div>
<?php
if (isset($data['enroll']['content'])) {
                      switch ($data['enroll']['content']['lesson_type']) {
case 1: // 기간제
    $remain = date_diff(new DateTime('now', $search_data['timezone']), new DateTime($data['enroll']['content']['end_date'], $search_data['timezone']))->days._('Day');
    break;
case 3: // 쿠폰제
    $remain = ($data['enroll']['content']['quantity'] - $data['enroll']['content']['use_quantity']).'개';
    break;
default:
    $remain = ($data['enroll']['content']['quantity'] - $data['enroll']['content']['use_quantity']).'회';
    break;
}
}
?>
  <div class="row">
    <div class="col-12 col-xl-8 col-xxl-9">
      <article class="card">
        <h3 class="card-header"><?php echo _('Enroll Info'); ?></h3>
        <div id="enroll_content" class="card-body">
          <div class="row">
            <?php if (isset($data['enroll']['content'])): ?>
              <div class="col-12 col-sm-6 col-xl-4 form-group">
                <p><?php echo _('Course Category'); ?> : <span>
                <?php if (isset($data['enroll']['content']['product_category_name'])): ?>
                <?php echo $data['enroll']['content']['product_category_name']; ?>
                <?php endif; ?>
                </span>
              </p>
              </div>
              <div class="col-12 col-sm-6 col-xl-4 form-group">
                <p><?php echo _('Course Name'); ?> : <span>
                <?php if (isset($data['enroll']['content']['product_name'])): ?>
                <?php echo $data['enroll']['content']['product_name']; ?>
                <?php endif; ?>
                </span>
              </p>
              </div>
              <div class="col-12 col-sm-6 col-xl-4 form-group">
                <p><?php echo _('Start Date'); ?> : <span>
                <?php if (isset($data['enroll']['content']['start_date'])): ?>
                <?php echo get_dt_format($data['enroll']['content']['start_date'], $search_data['timezone']); ?>
                <?php endif; ?>
                </span>
              </p>
              </div>
              <div class="col-12 col-sm-6 col-xl-4 form-group">
                <p><?php echo _('End Date'); ?> : <span>
                <?php if (isset($data['enroll']['content']['end_date'])): ?>
                <?php echo get_dt_format($data['enroll']['content']['end_date'], $search_data['timezone']); ?>
                <?php endif; ?>
                </span>
                </p>
              </div>
              <div class="col-12 col-sm-6 col-xl-4 form-group">
                <p><?php echo _('Lesson Type'); ?> : <span>
                <?php if (isset($data['enroll']['content']['lesson_type'])): ?>
                <?php echo get_lesson_type($data['enroll']['content']['lesson_type']); ?>
                <?php endif; ?>
                </span>
                </p>
              </div>
              <div class="col-12 col-sm-6 col-xl-4 form-group">
                <p><?php echo _('lesson_dayofweek'); ?> : <span>
                <?php if (isset($data['enroll']['content']['dow'])): ?>
                <?php echo dowtostr($data['enroll']['content']['dow']); ?>
                <?php endif; ?>
                </span>
                </p>
              </div>
              <div class="col-12 col-sm-6 col-xl-4 form-group">
                <p><?php echo _('Remain Day'); ?>/<?php echo _('Remain Count'); ?> :
                <span><?php if (isset($remain)): ?><?php echo $remain; ?><?php endif; ?></span></p>
              </div>
              <div class="col-12 col-sm-6 col-xl-4 form-group">
                <p><?php echo _('Enroll Trainer'); ?> : <span>
                <?php if (isset($data['enroll']['content']['trainer_name'])): ?>
                <?php echo $data['enroll']['content']['trainer_name']; ?>
                <?php else: ?>
                -
                <?php endif; ?>
                </span>
                </p>
              </div>
            </div>
          <?php else: ?>
            <div class="col-12">
              <p><?php echo _('No Data'); ?></p>
            </div>
          <?php endif; ?>
          </div>
      </article>
    </div>
      <div class="col-12 col-xl-4 col-xxl-3">
      <article class="card">
          <h3 class="card-header"><?php echo _('Rent Info'); ?></h3>
          <div class="card-body">
            <div class="row">
            <?php if (isset($data['rent'])): ?>
              <div class="col-12">              
                  <dl>
                      <dt><?php echo $data['rent']['product_name']; ?></dt>
                      <dd><?php if (empty($data['rent']['no'])) : ?><?php echo _('Not Set'); ?><?php else: ?><?php echo $data['rent']['no']; ?><?php echo _('No'); ?><?php endif; ?></dd>
                  </dl>
                  <dl>                    
                      <dt><?php echo _('End Date'); ?></dt>
                      <dd><?php echo get_dt_format($data['rent']['end_date'], $search_data['timezone']); ?></dd>
                  </dl>

              </div>
            <?php else: ?>
              <div class="col-12">
                <p><?php echo _('No Data'); ?></p>
              </div>
            <?php endif; ?>
          </div>
      </article>
    </div>
  </div>
  <?php if ($data['enroll']['total'] > 1): ?>
      <table id="enroll_list" class="table table-bordered">
          <colgroup>
              <col style="width:96px;" />
              <col />
              <col />
              <col />
              <col />
              <col />
              <col />
              <col style="width:96px;" />
          </colgroup>
          <thead class="thead-default">
              <tr>
                  <th><?php echo _('Course Category'); ?></th>
                  <th><?php echo _('Course Name'); ?></th>
                  <th><?php echo _('Start Date'); ?></th>
                  <th><?php echo _('End Date'); ?></th>
                  <th><?php echo _('Lesson Type'); ?></th>
                  <th><?php echo _('lesson_dayofweek'); ?></th>
                  <th><?php echo _('Remain Day'); ?>/<?php echo _('Remain Count'); ?></th>
                  <th><?php echo _('Enroll Trainer'); ?></th>
              </tr>
          </thead>
          <tbody>
  <?php if ($data['enroll']['total']): ?>
  <?php foreach ($data['enroll']['list'] as $enroll):
          switch ($enroll['lesson_type']) {
              case 1: // 기간제
                  $remain = date_diff(new DateTime('now', $search_data['timezone']), new DateTime($enroll['end_date']))->days._('Day');
                  break;
              default:
                  $remain = $enroll['use_quantity'].'/'.$enroll['quantity'].'회';
                  break;
          }


          $trClass = ($data['enroll']['content']['id'] == $enroll['id']) ? 'class="table-primary"' : '';
  ?>
              <tr <?php echo $trClass; ?>>
              <td><?php echo $enroll['product_category_name']; ?></td>
              <td><?php echo $enroll['product_name']; ?></td>
              <td><?php echo get_dt_format($enroll['start_date'], $search_data['timezone']); ?></td>
              <td><?php echo get_dt_format($enroll['end_date'], $search_data['timezone']); ?></td>
              <td><?php echo get_lesson_type($enroll['lesson_type']); ?></td>
              <td><?php echo dowtostr($enroll['dow']); ?></td>
              <td><?php echo $remain; ?></td>
              <td>
                <?php if (empty($enroll['trainer_name'])): ?>
                  -
                <?php else: ?>
                <?php echo $enroll['trainer_name']; ?>
                <?php endif; ?>
              </td>
              </tr>
  <?php endforeach; ?>
  <?php else: ?>
      <tr>
        <td colspan="8"><?php echo _('No Data'); ?></td>
      </tr>
  <?php endif; ?>
          </tbody>
      </table>
    <?php endif; ?>
</div>
</div>

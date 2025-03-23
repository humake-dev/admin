<?php

  if ($data['user_stops']['total']) {
      foreach ($data['user_stops']['list'] as $user_stops) {
          $stop_end_date = set_value('stop_end_date', $search_data['today']);
          $stop_end_date_obj = new DateTime($stop_end_date, $search_data['timezone']);
          $stop_start_date_obj = new DateTime($user_stops['stop_start_date'], $search_data['timezone']);
          $interval_day_count = $stop_start_date_obj->diff($stop_end_date_obj);
          $new_day_count_days = intval($interval_day_count->format('%a'));

          if (empty($day_count_days)) {
              $day_count_days = $new_day_count_days;
          } else {
              if ($day_count_days > $new_day_count_days) {
                  $day_count_days = $new_day_count_days;
              }
          }
      }
  }

  if (empty($day_count_days)) {
      $day_count_days = 0;
  }

if ($this->input->get('popup')):
?>
<?php echo form_open('/user-stops/resume/'.$data['content']['id'], array('id' => 'order_resume_form')); ?>
<div class="modal-header">
  <h2 class="modal-title"><?php echo _('All Resume Stopped Order'); ?></h2>
  <button type="button" class="close" data-dismiss="modal" aria-label="Close">
    <span aria-hidden="true">&times;</span>
  </button>
</div>
<div class="modal-body">
<?php else: ?>
  <div class="container">
    <div class="row">
      <div class="col-12">
      <?php echo form_open('/user-stops/resume/'.$data['content']['id'], array('id' => 'order_resume_form')); ?>
<?php endif; ?>


<?php
echo form_input(array(
  'id' => 'count_stop_day',
  'type' => 'hidden',
  'value' => $day_count_days,
));
?>
<div class="form-row">
<article class="col-12">
  <h3><?php echo _('Order Info'); ?></h3>
  <div class="card">
    <div class="card-body">
    <div class="form-row">
    <div class="form-group col-12 col-xl-6">
      <label for="e_username"><?php echo _('User Name'); ?></label>
      <p>
      <?php

      echo form_input(array(
              'id' => 'e_username',
              'value' => $data['content']['name'],
              'class' => 'form-control-plaintext',
      ));
      ?>
      </p>
    </div>
    <?php if (!empty($common_data['branch']['use_access_card'])): ?>      
    <div class="form-group col-12 col-xl-6">
      <label for="e_card_no"><?php echo _('Access Card No'); ?></label>
      <p>
      <?php

      echo form_input(array(
              'id' => 'e_card_no',
              'value' => $data['content']['card_no'],
              'class' => 'form-control-plaintext',
      ));
      ?>
      </p>
    </div>
    <?php endif; ?>
    </div>
  </div>
</article>

<article class="col-12">
  <h3><?php echo _('Resume Order Info'); ?></h3>
  <div class="card">
    <div class="card-body">
        <div class="form-group">
        <label for="us_stop_end_date"><?php echo _('Stop End Date'); ?></label>
        <div>
          <?php

            $todayObj=New DateTime($search_data['today'],$search_data['timezone']);
            $todayObj->modify('-1 Day');
            $yesterday=$todayObj->format('Y-m-d');

            echo form_input(array(
              'type' => 'hidden',
              'id' => 'us_stop_start_date',
              'value' => $data['content']['stop_start_date'],
              'name' => 'stop_start_date',
            ));

            echo form_input(array(
              'id' => 'us_stop_end_date',
              'name' => 'stop_end_date',
              'value' => $yesterday,
              'class' => 'form-control datepicker',
            ));
            ?>
          </div>
        </div>
        </div>
  </div>
</article>

</div>

<?php if ($this->input->get('popup')): ?>
</div>
<div class="modal-footer">
  <?php echo form_submit('', _('Resume'), array('class' => 'btn btn-primary btn-block')); ?>
</div>
<?php echo form_close(); ?>
<script src="<?php echo $script; ?>"></script>
<?php else: ?>
  <?php echo form_submit('', _('Resume'), array('class' => 'btn btn-primary btn-block btn-lg')); ?>
      <?php echo form_close(); ?>
    </div>
  </div>
</div>
<?php endif; ?>

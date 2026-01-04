<?php
  if ($this->input->get('schedule')) {
      $title = _('Stop Order Schedule');
  } else {
      $title = _('Stop Order');
  }

  if ($this->input->get('popup')):

?>
<?php echo form_open('', array('id' => 'order_stop_form'), array('user_id' => $data['content']['user_id'])); ?>
<div class="modal-header">
  <h2 class="modal-title"><?php echo $title; ?></h2>
  <button type="button" class="close" data-dismiss="modal" aria-label="Close">
    <span aria-hidden="true">&times;</span>
  </button>
</div>
<div class="modal-body">
<?php else: ?>
  <div class="container">
    <div class="row">
      <div class="col-12">
      <?php echo form_open('', array('id' => 'order_stop_form'), array('user_id' => $data['content']['user_id'])); ?>
<?php endif; ?>

<input type="hidden" id="u_today" value="<?php echo $search_data['date']; ?>" />
<div class="row">
<article class="col-12">
  <h3><?php echo _('User Info'); ?></h3>
  <div class="card">
    <div class="card-body">
    <div class="form-row">
    <div class="col-12 col-md-6 col-lg-4 form-group">
      <label for="u_username"><?php echo _('User Name'); ?></label>
      <p><?php

      echo form_input(array(
              'id' => 'o_username',
              'value' => $data['content']['name'],
              'class' => 'form-control-plaintext',
      ));
      ?></p>
    </div>
    <?php if (!empty($common_data['branch']['use_access_card'])): ?>      
    <div class="col-12 col-md-6 col-lg-4 form-group">
      <label for="u_card_no"><?php echo _('Access Card No'); ?></label>
      <p><?php

      echo form_input(array(
              'id' => 'o_card_no',
              'value' => get_card_no($data['content']['card_no'], false),
              'class' => 'form-control-plaintext',
      ));
      ?></p>
    </div>
    <?php endif; ?>
      </div>
    </div>
  </div>
</article>

<article class="col-12">
  <h3><?php echo _('Stop Info'); ?></h3>
    <div class="card">
    <div class="card-body">
    <div class="form-group">
    <label for="us_request_date"><?php echo _('Request Date'); ?></label>
            <div id="us_request_date" style="width:160px">
            <div class="input-group-prepend date">
              <?php
                echo form_input(array(
                  'id' => 'us_request_date',
                  'name' => 'request_date',
                  'value' => $data['content']['request_date'],
                  'class' => 'form-control datepicker',
                ));
              ?>
              <label for="us_request_date" class="input-group-text">
              <span class="material-icons">date_range</span>
              </label>
            </div>
            </div>
          </div>


          <div class="form-row">
      <div class="col-12 col-md-6 col-lg-4 form-group">
        <label for="u_resume_date"><?php echo _('Stop Start Date'); ?></label>
        <p><?php

echo form_input(array(
  'type' => 'hidden',
  'id' => 'u_stop_start_date',
  'name' => 'stop_start_date',
  'value' => $data['content']['stop_start_date'],
));

echo form_input(array(
        'value' => get_dt_format($data['content']['stop_start_date']),
        'class' => 'form-control-plaintext',
));
?></p>
      </div>

      <div class="col-12 col-md-6 col-lg-4 form-group">
        <label for="o_stop_day"><?php echo _('Stop Period'); ?></label>
        <p>          
          <span id="o_stop_day">
            <?php if (empty($data['content']['stop_end_date'])): ?>
            <?php echo _('Not Set'); ?></span><span id="o_stop_day_day" style="display:none"><?php echo _('Day'); ?></span>
            <?php else: ?>
            <?php echo $data['content']['stop_day_count']; ?></span><span id="o_stop_day_day"><?php echo _('Day'); ?></span>
            <?php endif; ?>    
        </p>
      </div>      

      <div class="col-12 col-md-6 col-lg-4 form-group">
        <label for="u_stop_end_date"><?php echo _('Stop End Date'); ?></label>
        <div>
          <?php

            if (empty($data['content']['stop_end_date'])) {
                $default_stop_end_date = null;
            } else {
                $default_stop_end_date = $data['content']['stop_end_date'];
            }

            $stop_end_date = set_value('stop_end_date', $default_stop_end_date);

              echo form_input(array(
                'id' => 'u_stop_end_date',
                'name' => 'stop_end_date',
                'value' => $stop_end_date,
                'class' => 'form-control',
              ));
          ?>
        </div>
        <div>
          <?php
            if (empty($default_stop_end_date)) {
                $default_stop_end_date_not_set = 1;
                $u_default_stop_end_date = $search_data['today'];
            } else {
                $default_stop_end_date_not_set = 0;
                $u_default_stop_end_date = $stop_end_date;
            }

            echo form_input(array(
              'type' => 'hidden',
              'id' => 'u_default_stop_end_date',
              'value' => $u_default_stop_end_date,
            ));

            $stop_end_date_not_set = set_value('stop_end_date_not_set', $default_stop_end_date_not_set);
            echo form_checkbox(array('type' => 'checkbox', 'id' => 'u_stop_end_date_not_set', 'name' => 'stop_end_date_not_set', 'value' => 1, 'checked' => $stop_end_date_not_set));
          ?>
          <label for="u_stop_end_date_not_set"><?php echo _('Not Set'); ?></label>
        </div>        
      </div>



      </div>
      </div>
    </div>
  </article>
<article class="col-12">
  <h3><?php echo _('Stop Memo'); ?></h3>
  <div class="card">
    <div class="card-body">
      <div class="form-row">
        <div class="col-12 form-group">
        <?php
          if (isset($data['content']['content'])) {
              $default_memo = $data['content']['content'];
          } else {
              $default_memo = '';
          }

          $memo_value = set_value('content', $default_memo);

          $memo_attr = array(
            'name' => 'content',
            'id' => 'o_memo',
            'value' => $memo_value,
            'rows' => 3,
            'class' => 'form-control',
          );
          echo form_textarea($memo_attr);

        ?>
        </div>
      </div>
    </div>
  </div>
</article>


</div>

<?php

echo '<script> var today="'.$search_data['today'].'";';

if (!empty($data['user_stops']['total']) or !empty($data['user_stop_schedules']['total'])) {
    echo 'var disable_date=[';

    if (!empty($data['user_stops']['total'])) {
        foreach ($data['user_stops']['list'] as $user_stop) {
            if($data['content']['id']==$user_stop['id']) {
              continue;
            }

            $start_date_do = new Datetime($user_stop['stop_start_date']);
            $end_date_do = new Datetime($user_stop['stop_end_date']);
            while ($start_date_do <= $end_date_do) {
                echo '"'.$start_date_do->format('Y-m-d').'",';
                $start_date_do->modify('+1 Day');
            }
        }
    }

    if (!empty($data['user_stop_schedules']['total'])) {
        foreach ($data['user_stop_schedules']['list'] as $user_stop_schedule) {
          if($data['content']['id']==$user_stop_schedule['user_stop_id']) {
            continue;
          }

            $start_date_schedule_do = new Datetime($user_stop_schedule['stop_start_date']);
            $end_date_schedule_do = new Datetime($user_stop_schedule['stop_end_date']);
            while ($start_date_schedule_do <= $end_date_schedule_do) {
                echo '"'.$start_date_schedule_do->format('Y-m-d').'",';
                $start_date_schedule_do->modify('+1 Day');
            }
        }
    }

    echo ' ];';
} else {
    echo 'var disable_date=[]';
}

echo '</script>';
?>

<?php if ($this->input->get('popup')): ?>
</div>
<div class="modal-footer">
  <?php echo form_submit('', $title, array('class' => 'btn btn-primary btn-block')); ?>
</div>
<?php echo form_close(); ?>
<script src="<?php echo $script; ?>"></script>
<?php else: ?>
  <?php echo  form_submit('', $title, array('class' => 'btn btn-primary btn-block btn-lg')); ?>
      <?php echo form_close(); ?>
    </div>
  </div>
</div>
<?php endif; ?>

<?php if ($this->input->get('popup')): ?>
<?php echo form_open('/enrolls/resume/'.$data['enroll_content']['id'], array('id' => 'enroll_resume_form')); ?>
<div class="modal-header">
  <h2 class="modal-title"><?php echo _('Enroll Resume'); ?></h2>
  <button type="button" class="close" data-dismiss="modal" aria-label="Close">
    <span aria-hidden="true">&times;</span>
  </button>
</div>
<div class="modal-body">
<?php else: ?>
  <div class="container">
    <div class="row">
      <div class="col-12">
      <?php echo form_open('/enrolls/resume/'.$data['enroll_content']['id'], array('id' => 'enroll_resume_form')); ?>
<?php endif; ?>

<?php
echo form_input(array(
        'type' => 'hidden',
        'name' => 'enroll_stop_id',
        'value' => $data['content']['id'],
));
?>
<div class="form-row">
<article class="col-12 col-lg-6">
  <h3><?php echo _('Enroll Info'); ?></h3>
  <div class="card">
    <div class="card-body">
    <div class="form-group">
      <label for="e_username"><?php echo _('User Name'); ?></label>
      <p>
      <?php

      echo form_input(array(
              'id' => 'e_username',
              'value' => $data['enroll_content']['user_name'],
              'class' => 'form-control-plaintext',
      ));
      ?>
      </p>
    </div>
    <?php if (!empty($common_data['branch']['use_access_card'])): ?>      
    <div class="form-group">
      <label for="e_card_no"><?php echo _('Access Card No'); ?></label>
      <p>
      <?php

      echo form_input(array(
              'id' => 'e_card_no',
              'value' => $data['enroll_content']['card_no'],
              'class' => 'form-control-plaintext',
      ));
      ?>
      </p>
    </div>
    <?php endif; ?>
    <div class="form-group">
      <label for="e_course"><?php echo _('Lesson'); ?></label>
      <p>
      <?php

      echo form_input(array(
              'id' => 'e_course',
              'value' => $data['enroll_content']['product_name'],
              'class' => 'form-control-plaintext',
      ));
      ?>
      </p>
    </div>
    <div class="form-group">
      <label for="e_amount"><?php echo _('Sell Price'); ?></label>
      <p>
      <?php

      echo form_input(array(
              'id' => 'e_amount',
              'value' => number_format($data['enroll_content']['price'])._('Currency'),
              'class' => 'form-control-plaintext',
      ));
      ?>
      <p>
    </div>
    <div class="form-group">
      <label for="e_payment"><?php echo _('Payment'); ?></label>
      <p>
      <?php

      echo form_input(array(
              'id' => 'e_payment',
              'value' => number_format($data['enroll_content']['payment'])._('Currency'),
              'class' => 'form-control-plaintext',
      ));
      ?>
      </p>
      </div>
    </div>
  </div>
</article>
<article class="col-12 col-lg-6">
  <h3><?php echo _('Enroll Resume Info'); ?></h3>
  <div class="card">
    <div class="card-body">
      <div class="form-group">
        <label for="e_credit"><?php echo _('Remain Day'); ?></label>
        <div>
          <?php

            echo form_input(array(
              'id' => 'e_end_date',
              'value' => $data['content']['day_count']._('Day'),
              'class' => 'form-control-plaintext',
            ));
            ?>
          </div>
        </div>
      <div class="form-group">
        <label for="e_end_date"><?php echo _('Resume End Date'); ?></label>
        <div>
        <?php

        $day = intval($data['content']['day_count']) - 1;
        $today = new DateTime($search_data['date'], $search_data['timezone']);
        $today->add(new DateInterval('P'.$day.'D'));

        echo form_input(array(
                'id' => 'e_end_date',
                'name' => 'end_date',
                'value' => $today->format('Y-m-d'),
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

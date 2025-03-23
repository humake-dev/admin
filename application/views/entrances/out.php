


<?php if ($this->input->get('popup')): ?>
<?php echo form_open('/entrances/out/'.$data['content']['id'], array('id' => 'entrance_out_form')); ?>
<?php echo form_input(array('type' => 'hidden', 'name' => 'return_url', 'value' => '/')); ?>
<div class="modal-header">
  <h2 class="modal-title"><?php echo _('Entrance Out'); ?></h2>
  <button type="button" class="close" data-dismiss="modal" aria-label="Close">
    <span aria-hidden="true">&times;</span>
  </button>
</div>
<div class="modal-body">
<?php else: ?>
  <div class="container">
    <div class="row">
      <div class="col-12">
      <?php echo form_open('/entrances/out/'.$data['content']['id'], array('id' => 'entrance_out_form')); ?>
      <?php echo form_input(array('type' => 'hidden', 'name' => 'return_url', 'value' => '/')); ?>      
<?php endif; ?>


<div class="form-row">
<article class="col-12">
  <h3><?php echo _('Entrance Info'); ?></h3>
  <div class="card">
    <div class="card-body">
      <div class="row">
        <div class="form-group col-12 col-md-6 col-lg-4">
      <label for="e_username"><?php echo _('User'); ?></label>
      <p>
      <?php

      echo form_input(array(
              'id' => 'eo_user',
              'value' => $data['content']['name'],
              'class' => 'form-control-plaintext',
      ));
      ?>
      <p>
    </div>
    <?php if (!empty($common_data['branch']['use_access_card'])): ?>      
    <div class="form-group col-12 col-md-6 col-lg-4">
      <label for="e_card_no"><?php echo _('Access Card No'); ?></label>
      <p>
      <?php

      echo form_input(array(
              'id' => 'eo_card_no',
              'value' => $data['content']['card_no'],
              'class' => 'form-control-plaintext',
      ));
      ?>
    </p>
    </div>
    <?php endif; ?>
    <div class="form-group col-12 col-md-6 col-lg-4">
      <label for="e_card_no"><?php echo _('In Time'); ?></label>
      <p>
      <?php

      echo form_input(array(
              'id' => 'eo_in_time',
              'value' => get_dt_format($data['content']['in_time'], $search_data['timezone'], 'H'._('Hour').' i'._('Minute')),
              'class' => 'form-control-plaintext',
      ));
      ?>
    </p>
    </div>
    <div class="form-group col-12 col-md-6 col-lg-4">
      <label for="e_course"><?php echo _('Facility'); ?></label>
      <p>
      <?php

      echo form_input(array(
              'id' => 'eo_facility',
              'value' => $data['content']['title'],
              'class' => 'form-control-plaintext',
      ));
      ?>
      </p>
    </div>
    <div class="form-group col-12 col-md-6 col-lg-4">
      <label for="e_course"><?php echo _('Facility No'); ?></label>
      <p>
      <?php

      echo form_input(array(
              'id' => 'eo_facility_card_no',
              'value' => $data['content']['no'],
              'class' => 'form-control-plaintext',
      ));
      ?>
      </p>
    </div>    
    <div class="form-group col-12 col-md-6 col-lg-4">
      <label for="e_course"><?php echo _('Entrance Card Info'); ?></label>
      <p>
      <?php

      echo form_input(array(
              'id' => 'eo_facility_card_no',
              'value' => $data['content']['facility_card_no'],
              'class' => 'form-control-plaintext',
      ));
      ?>
      </p>
    </div>
    
  </div>
  </article>

</div>


<?php if ($this->input->get('popup')): ?>
</div>
<div class="modal-footer">
<?php echo form_submit('', _('Submit'), array('id' => 'entrance_out_submit_button', 'class' => 'btn btn-primary btn-block')); ?>
</div>
<?php echo form_close(); ?>
<?php else: ?>
<?php echo form_submit('', _('Submit'), array('id' => 'entrance_out_submit_button', 'class' => 'btn btn-primary btn-block btn-lg')); ?>
      <?php echo form_close(); ?>
    </div>
  </div>
</div>
<?php endif; ?>

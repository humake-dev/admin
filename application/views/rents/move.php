<?php if ($this->input->get('popup')): ?>
<?php echo form_open('/rents/move/'.$data['content']['id'], array('id' => 'rent_move_form')); ?>
<div class="modal-header">
  <h2 class="modal-title"><?php echo _('Move'); ?></h2>
  <button type="button" class="close" data-dismiss="modal" aria-label="Close">
    <span aria-hidden="true">&times;</span>
  </button>
</div>
<div class="modal-body">
<?php else: ?>
  <div class="container">
    <div class="row">
      <div class="col-12">
        <?php echo form_open('/rents/move/'.$data['content']['id'], array('id' => 'rent_move_form')); ?>
<?php endif; ?>

<article class="row">
  <h3 class="col-12"><?php echo _('Current locker number'); ?></h3>
  <div class="col-12">
    <div class="card">
      <div class="card-body">
        <div class="row">
          <div class="col-12 col-lg-6 form-group">
            <label><?php echo _('Facility'); ?></label>
            <input value="<?php echo $data['content']['product_name']; ?>" class="form-control-plaintext" />
          </div>
          <div class="col-12 col-lg-6 form-group">
            <label><?php echo _('Facility No'); ?></label>
            <?php if ($data['content']['no']): ?>
            <input value="<?php echo $data['content']['no']; ?>" class="form-control-plaintext" />
            <?php else: ?>
            <input value="<?php echo _('Not Set'); ?>" class="form-control-plaintext" />
            <?php endif; ?>
          </div>
        </div>
      </div>
    </div>
  </div>
</article>
<article class="row">
  <h3 class="col-12"><?php echo _('Move locker number'); ?></h3>
  <div class="col-12">
    <div class="card">
      <div class="card-body">
        <div class="row">
          <div class="col-12 col-lg-6 form-group">
          <label for="f_facility_id"><?php echo _('Facility'); ?></label>
          <?php

          $option = array();
          foreach ($data['facility']['list'] as $value) {
              $option[$value['id']] = $value['title'];
          }

          $select = set_value('facility_id');

          if (!$select) {
              if (isset($data['content']['facility_id'])) {
                  $select = $data['content']['facility_id'];
              }
          }

          echo form_dropdown('facility_id', $option, $select, array('id' => 'f_facility_id', 'class' => 'form-control'));

          ?>
        </div>
        <div class="col-12 col-lg-6 form-group">
          <label for="f_no"><?php echo _('Facility No'); ?></label>
          <select id="f_no" name="no" class="form-control">
          <?php if ($data['facility_available_no']['total']): ?>
          <?php foreach ($data['facility_available_no']['list'] as $facility_no):
            if ($facility_no['enable']) {
                echo '<option value="'.$facility_no['no'].'">'.$facility_no['no'].'</option>';
            } else {
                echo '<option value="'.$facility_no['no'].'" disabled="disabled">'.$facility_no['no'].'('.$facility_no['disable'].')</option>';
            }
            endforeach;
          endif;
          ?>
          </select>
          </div>
        </div>
      </div>
    </div>
  </div>
</article>

<?php if ($this->input->get('popup')): ?>
</div>
<div class="modal-footer">
  <?php echo form_submit('', _('Move'), array('class' => 'btn btn-primary btn-block')); ?>
</div>
<?php echo form_close(); ?>
<script src="<?php echo $script; ?>"></script>
<?php else: ?>
  <?php echo form_submit('', _('Move'), array('class' => 'btn btn-primary btn-block btn-lg')); ?>
      <?php echo form_close(); ?>
    </div>
  </div>
</div>
<?php endif; ?>

<?php
  echo form_open('', array('method' => 'get', 'class' => 'search_user_form card'));
  $user_id_value = set_value('user_id');

  if ($this->input->get('employee_id')) {
      $user_id_value = $data['content']['id'];
  }

?>
<div class="card-body">
  <div class="form-row">
    <div class="col-12 col-md-4 col-lg-2 form-group">
      <label for="e_year" class="sr-only"><?php echo _('Year'); ?></label>
      <?php
        $options = array_combine(range(date('Y'), 2015), range(date('Y'), 2015));

        foreach ($options as $key => $option) {
            $options[$key] = $option._('Year');
        }

        $select_year = set_value('year', date('Y'));
        echo form_dropdown('year', $options, $select_year, array('id' => 'e_year', 'class' => 'form-control'));
      ?>
    </div>
    <div class="col-12 col-md-4 col-lg-2 form-group">
      <label for="e_month" class="sr-only"><?php echo _('Month'); ?></label>
      <?php
        $options = array_combine(range(1, 12), range(1, 12));

        foreach ($options as $key => $option) {
            $options[$key] = $option._('Month');
        }

        $select_month = set_value('month', date('m'));
        echo form_dropdown('month', $options, $select_month, array('id' => 'e_month', 'class' => 'form-control'));
      ?>
    </div>
    <div class="col-12 col-md-4 col-lg-2 form-group">
      <?php echo form_submit('', _('Search'), array('class' => 'btn btn-primary')); ?>
    </div>
    <div class="col-12 col-lg-4 text-right">
    <?php if ($data['total']): ?>
      <?php $class = 'btn btn-secondary'; ?>
    <?php else: ?>
      <?php $class = 'btn btn-secondary disabled'; ?>
    <?php endif; ?>
    </div>
  </div>
</div>
<?php echo form_close(); ?>

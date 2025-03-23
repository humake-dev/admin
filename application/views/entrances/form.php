
<h3><?php echo _('Attendance'); ?></h3>
<div class="card">
  <div class="card-body">
    <div class="form-row">
    <?php if ($this->input->get_post('user_id')): ?>
    <input type="hidden" name="user_id" value="<?php echo $this->input->get_post('user_id'); ?>" />
    <input type="hidden" name="return_url" value="/view/<?php echo $this->input->get_post('user_id'); ?>" />
    <?php else: ?>
      <?php if (!empty($common_data['branch']['use_access_card'])): ?>      
      <div class="col-12 col-lg-6 form-group">
        <label><?php echo _('Access Card No'); ?></label>
        <input type="text" name="card_no" value="<?php echo set_value('card_no'); ?>" class="form-control" required>
      </div>
      <?php endif; ?>
    <?php endif; ?>
      <div class="col-12 col-lg-6 form-group">
        <label><?php echo _('Date'); ?></label>
        <div class="input-group-prepend date">
          <input type="text" name="date" value="<?php echo set_value('date', $search_data['date']); ?>" class="form-control datepicker" required>
          <div class="input-group-text"><span class="material-icons">date_range</span></div>
        </div>
      </div>
    </div>
  </div>
</div>

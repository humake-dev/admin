<?php
  $default_data_p_0 = false;
  $default_data_p_7 = false;
  $default_data_p_30 = false;
  $default_data_p_90 = false;
  $default_data_p_180 = false;
  $default_data_p_365 = true;
  $default_data_p_all = false;

  if (!empty($search_data['date_p'])) {
      switch ($search_data['date_p']) {
        case '0':
          $default_data_p_30 = false;
          $default_data_p_0 = true;
          break;
      case '7':
        $default_data_p_30 = false;
        $default_data_p_7 = true;
        break;
          case '90':
            $default_data_p_30 = false;
            $default_data_p_90 = true;
            break;
            case '180':
              $default_data_p_30 = false;
              $default_data_p_180 = true;
              break;
              case '365':
                $default_data_p_30 = false;
                $default_data_p_180 = true;
                break;
                case 'all':
                  $default_data_p_30 = false;
                  $default_data_p_all = true;
                  break;
    }
  }

?>
<div class="col-6">
  <div class="input-group-prepend input-daterange">
  <?php
    $default_value_start_date='';

    if (isset($search_data['display_start_date'])) {
      $default_value_start_date = $search_data['display_start_date'];
    }

    echo form_input(['name' => 'start_date', 'value' => set_value('start_date',$default_value_start_date), 'id' => 'a_start_date', 'class' => 'form-control datepicker']);
  ?>
  <div class="input-group-text">~</div>
  <?php
  $default_value_end_date='';
  
  if (isset($search_data['display_end_date'])) {
    $default_value_end_date = $search_data['display_end_date'];
  }

    echo form_input(['name' => 'end_date', 'value' => set_value('end_date',$default_value_end_date), 'id' => 'a_end_date', 'class' => 'form-control datepicker']);
  ?>
  </div>
</div>
<div class="col-6">
<div class="form-check form-check-inline">
  <label class="form-check-label">
    <input class="form-check-input" type="radio" name="date_p" value="0" <?php echo set_radio('date_p', '0', $default_data_p_0); ?>> <?php echo _('Today'); ?>
  </label>
</div>
<div class="form-check form-check-inline">
  <label class="form-check-label">
    <input class="form-check-input" type="radio" name="date_p" value="7" <?php echo set_radio('date_p', '7', $default_data_p_7); ?>> <?php echo _('Period Week'); ?>
  </label>
</div>
<div class="form-check form-check-inline">
  <label class="form-check-label">
    <input class="form-check-input" type="radio" name="date_p" value="30" <?php echo set_radio('date_p', '30', $default_data_p_30); ?>> <?php echo _('A month'); ?>
  </label>
</div>
<div class="form-check form-check-inline">
  <label class="form-check-label">
    <input class="form-check-input" type="radio" name="date_p" value="90" <?php echo set_radio('date_p', '90', $default_data_p_90); ?>> <?php echo _('Three month'); ?>
  </label>
</div>
<div class="form-check form-check-inline">
  <label class="form-check-label">
    <input class="form-check-input" type="radio" name="date_p" value="180" <?php echo set_radio('date_p', '180', $default_data_p_180); ?>> <?php echo _('Half a year'); ?>
  </label>
</div>
<div class="form-check form-check-inline">
  <label class="form-check-label">
    <input class="form-check-input" type="radio" name="date_p" value="365" <?php echo set_radio('date_p', '365', $default_data_p_365); ?>> <?php echo _('One Year'); ?>
  </label>
</div>
<div class="form-check form-check-inline">
  <label class="form-check-label">
    <input class="form-check-input" type="radio" name="date_p" value="all" <?php echo set_radio('date_p', 'all', $default_data_p_all); ?>> <?php echo _('The whole period'); ?>
  </label>
</div>
</div>

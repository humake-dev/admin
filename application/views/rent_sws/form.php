<div class="form-row">
<?php

  if (empty($data['user_content'])) {
      $user_id = set_value('user_id');
  } else {
      $user_id = set_value('user_id', $data['user_content']['id']);
  }

  echo form_input(array('type' => 'hidden', 'id' => 'o_user_id', 'name' => 'user_id', 'value' => $user_id));
  echo form_input(array('type' => 'hidden', 'id' => 'o_today', 'value' => $search_data['today']));

  if ($this->input->post_get('product_id')) {
      echo form_input(array('type' => 'hidden', 'id' => 'default_product_id', 'value' => $this->input->post_get('product_id')));
  }

?>
<?php echo form_input(array('type' => 'hidden', 'id' => 'r_product_price', 'name' => 'product_price', 'value' => $data['product']['content']['price'])); ?>
<article class="col-12 col-xl-6 col-xxl-5">
    <h3><?php echo _('Rent Sw Info'); ?></h3>
    <div class="card">
        <div class="card-body">
            <div class="form-row">
                <div class="col-12 form-group">
                    <label for="r_product_id"><?php echo _('Product'); ?></label>
                    <?php
      $product_select = set_value('product_id', $this->input->post_get('product_id'));

      if (!$product_select) {
          if (isset($data['content']['product_id'])) {
              $product_select = $data['content']['product_id'];
          }
      }
  ?>
    <select id="r_product_id" name="product_id" class="form-control">
    <?php if ($data['product']['total']): ?>
    <?php foreach ($data['product']['list'] as $product): ?>
    <option value="<?php echo $product['id']; ?>" data-price="<?php echo $product['price']; ?>"<?php if ($product_select == $product['id']): ?> selected="selected"<?php endif; ?>><?php echo $product['title']; ?></option>
    <?php endforeach; ?>
    <?php endif; ?>
    </select>
  </div>

 
  
  <div id="select_month" class="col-12 form-group">
    <label for="r_rent_month"><?php echo _('Facility Rent Month'); ?></label>
    <?php

    $option = array('' => _('Please Select'));
    $select = set_value('rent_month');

    foreach (range(1, 200) as $value) {
        $option[$value] = $value._('Period Month');
    }

    if (empty($select)) {
        if (isset($data['content']['insert_quantity'])) {
            $select = $data['content']['insert_quantity'];
        }
    }

    echo form_dropdown('rent_month', $option, $select, array('id' => 'r_rent_month', 'class' => 'form-control', 'required' => 'required'));

   ?>
  </div>
  
  <div class="col-12 form-group period-day">
              <label for="r_start_date"><?php echo _('Start Date'); ?></label>
              <div class="input-group-prepend date">
                  <?php

                  if (isset($data['content']['start_date'])) {
                      $value_start_date = $data['content']['start_date'];
                  } else {
                      $value_start_date = set_value('start_date', $search_data['date']);
                  }

                  echo form_input(array(
                          'name' => 'start_date',
                          'id' => 'o_start_date',
                          'value' => $value_start_date,
                          'class' => 'form-control rent_datepicker',
                  ));

                ?>
                  <div class="input-group-text">
                  <span class="material-icons">date_range</span>
                  </div>
              </div>
            </div>
          <div class="col-12 form-group period-day">
            <label for="r_end_date"><?php echo _('End Date'); ?></label>
              <div class="input-group-prepend date">
                <?php

                if (isset($data['content']['end_date'])) {
                    $value_end_date = $data['content']['end_date'];
                } else {
                    $value_end_date = set_value('end_date', $search_data['date']);
                }

                echo form_input(array(
                  'name' => 'end_date',
                  'id' => 'o_end_date',
                  'value' => $value_end_date,
                  'class' => 'form-control rent_datepicker',
                ));
                ?>
                <div class="input-group-text">
                <span class="material-icons">date_range</span>
                </div>
            </div>
          </div>
          <div id="sync_enroll" class="col-12 form-group"<?php if (empty($data['period_enroll'])): ?> style="display:none"<?php endif; ?>>
            <label><?php echo _('Sync Period'); ?></label>
            <?php if (!empty($data['period_enroll'])): ?>
              <?php
              if ($data['period_enroll']['lesson_type'] == 1) {
                  $d1 = new DateTime($data['period_enroll']['start_date']);
                  $d2 = new DateTime($data['period_enroll']['end_date']);
                  $d2->modify('+1 day');
                  $count = $d1->diff($d2)->m + ($d1->diff($d2)->y * 12);
              } else {
                  $count = $data['period_enroll']['quantity'];
              }

              echo form_input(array(
                'type' => 'hidden',
                'id' => 'e_insert_quantity',
                'value' => $count,
              ));

                    echo form_input(array(
                      'type' => 'hidden',
                      'id' => 'e_start_date',
                      'value' => $data['period_enroll']['start_date'],
                    ));
                    echo form_input(array(
                      'type' => 'hidden',
                      'id' => 'e_end_date',
                      'value' => $data['period_enroll']['end_date'],
                    ));
                ?>
            <?php endif; ?>
            <input type="button" id="sync-enroll-button" class="form-control btn btn-secondary" value="<?php echo _('Sync Period'); ?>" />
          </div>
</article>
<?php  echo $Layout->Element('form_account'); ?>
</div>
<?php echo $Layout->Element('form_memo'); ?>
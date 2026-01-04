<?php
  if (empty($data['content'])) {
      $form_url = '/facilities/add';
      $form_id = 'facility_add_form';
  } else {
      $form_url = '/facilities/edit/'.$data['content']['id'];
      $form_id = 'facility_edit_form';
  }
?>
<?php echo form_open($form_url, array('id' => $form_id)); ?>
<div class="card">
  <div class="card-body">
    <div class="form-row">
      <div class="col-12 form-group">
        <label for="f_title"><?php echo _('Facility Title'); ?></label>
        <?php

$value = set_value('title');

if (!$value) {
    if (isset($data['content']['title'])) {
        $value = $data['content']['title'];
    }
}

    echo form_input(array(
            'name' => 'title',
            'id' => 'f_title',
            'value' => $value,
            'maxlength' => '60',
            'size' => '30',
            'required' => 'required',
            'class' => 'form-control',
    ));
    ?>
    </div>
    <div class="col-12 col-lg-6 form-group">
            <label for="f_price"><?php echo _('Price'); ?></label>
            <?php

            $price_value = set_value('price');

            if (!$price_value) {
                if (isset($data['content']['price'])) {
                    $price_value = $data['content']['price'];
                } else {
                    $price_value = 10000;
                }
            }

            echo form_input(array(
                    'type' => 'number',
                    'name' => 'price',
                    'id' => 'f_price',
                    'value' => $price_value,
                    'min' => '0',
                    'step' => '100',
                    'class' => 'form-control',
            ));
            ?>
          </div>
          <div class="col-12 col-lg-6 form-group">
      <label for="f_order"><?php echo _('Order No'); ?></label>
      <?php
  if (isset($data['content']['order_no'])) {
      $value = $data['content']['order_no'];
  } else {
      if ($data['total']) {
          $default = $data['total'] + 1;
      } else {
          $default = 1;
      }
      $value = set_value('order_no', $default);
  }
  echo form_input(array(
          'type' => 'number',
          'name' => 'order_no',
          'id' => 'f_order',
          'value' => $value,
          'min' => '1',
          'class' => 'form-control',
  ));
  ?>
</div>          
      <div class="col-12 col-lg-6 form-group">
        <label for="f_title"><?php echo _('Use Not Set'); ?></label>
        <?php

        $options = array(
          '0' => _('Not Use'),
          '1' => _('Use'),
        );
        $select = set_value('use_not_set', 0);

        if (isset($data['content']['use_not_set'])) {
            $select = set_value('use_not_set', $data['content']['use_not_set']);
        }

        echo form_dropdown('use_not_set', $options, $select, array('id' => 'f_use_not_set', 'class' => 'form-control'));

    ?>
    </div>

    <div class="col-12 col-lg-6 form-group">
      <label for="f_gender"><?php echo _('Gender'); ?></label>
      <?php

        $options = array(0 => _('Female'), 1 => _('Male'), 2 => _('Unisex'));
        $select = set_value('gender', '2');

        if (isset($data['content']['gender'])) {
            $select = set_value('gender', $data['content']['gender']);
        }

        echo form_dropdown('gender', $options, $select, array('id' => 'f_gender', 'class' => 'form-control'));
      ?>
    </div>

  <div class="col-12 col-lg-6 form-group">
    <label for="f_quantity"><?php echo _('Facility Quantity'); ?></label>
    <?php

    if (isset($data['content']['quantity'])) {
        $value = $data['content']['quantity'];
    } else {
        $value = set_value('quantity', '1');
    }
    echo form_input(array(
            'type' => 'number',
            'name' => 'quantity',
            'id' => 'f_quantity',
            'value' => $value,
            'min' => '1',
            'class' => 'form-control',
    ));
    ?>
  </div>
  <!-- <div class="col-12 col-md-6 form-group">
      <label for="f_column">가로 락커수</label>
      <?php

      if (isset($data['content']['column'])) {
          $value = $data['content']['column'];
      } else {
          $value = set_value('column', '1');
      }
      echo form_input(array(
              'type' => 'number',
              'name' => 'column',
              'id' => 'f_column',
              'value' => $value,
              'min' => '1',
              'class' => 'form-control',
      ));
      ?>
    </div> -->
          <div class="col-12 col-lg-6 form-group">
          <label for="f_start_no"><?php echo _('Start No'); ?></label>
          <?php

          if (isset($data['content']['start_no'])) {
              $value = $data['content']['start_no'];
          } else {
              $value = set_value('start_no', '1');
          }
          echo form_input(array(
                  'type' => 'number',
                  'name' => 'start_no',
                  'id' => 'f_start_no',
                  'value' => $value,
                  'min' => '1',
                  'class' => 'form-control',
          ));
          ?>
        </div>

          <?php if (!empty($data['exists_primary_product'])): ?>
  <div class="form-group">
  <div class="form-check form-check-inline">
              <label class="form-check-label">
                <?php
                  $m_checked = false;
                    if (isset($data['is_sub_product'])) {
                        if ($data['is_sub_product']) {
                            $m_checked = true;
                        }
                    }

                    echo form_checkbox(array(
                            'name' => 'sub_product',
                            'value' => '1',
                            'checked' => $m_checked,
                            'class' => 'form-check-input',
                    ));
                    ?> <?php echo _('Show At Add Primary Product'); ?>
                  </label>
                </div>
</div>
<?php endif; ?>
    </div>
</div>
</div>

<?php echo $Layout->Element('form_memo'); ?>

<div class="form-group">
  <?php echo form_submit('', _('Submit'), array('class' => 'btn btn-lg btn-primary btn-block')); ?>
</div>
<?php echo form_close(); ?>

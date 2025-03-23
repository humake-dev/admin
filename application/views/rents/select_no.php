<div id="rent_card_select" class="form-row">
<?php echo form_open('', array('method'=>'get','id'=>'facility_card_find_form','class'=>'col-12')) ?>
    <h3><?php echo _('Facility Card') ?></h3>
    <div class="card">
      <div class="card-body">
        <div class="form-row">
          <div id="facility_card_info" class="col-12"<?php if(empty($data['no'])): ?> style="display:none"<?php endif ?>>
              <div class="row">
              <div class="col-12 col-lg-4">
                <label><?php echo _('Facility') ?></label>
                <p id="product_name_info"><?php if(!empty($data['no'])): ?><?php echo $data['facility']['content']['title'] ?><?php endif ?></p>
              </div>
              <div class="col-12 col-lg-4">
                <label><?php echo _('Facility No') ?></label>
                <p><span id="facility_no_info"><?php if(!empty($data['no'])): ?><?php echo $data['no'] ?><?php endif ?></span></p>
              </div>
              <div class="col-12 col-lg-4 text-right">
                <input type="button" id="facility_card_select_cancel" value="<?php echo _('Cancel') ?>" class="btn btn-danger" />
              </div>
            </div>
          </div>

          <div id="facility_card_search" class="col-12" <?php if(!empty($data['no'])): ?> style="display:none"<?php endif ?>>

              <label for="s_facility_card"><?php echo _('Facility Card') ?></label>
              <div class="input-group">
                <?php
                $value=set_value('facility_card');

                echo form_input(array(
                        'type'          => 'search',
                        'id'            => 's_facility_card',
                        'name'          => 'card_no',
                        'value'         => $value,
                        'class'         => 'form-control',
                        // 'required'      => 'required',
                ));
                ?>
                <span class="input-group-btn">
                  <input type="submit" id="search_card" class="btn btn-success" value="<?php echo _('Search') ?>" />
                </span>
            </div>

          </div>
        </div>
      </div>
    </div>
  <?php echo form_close() ?>
</div>

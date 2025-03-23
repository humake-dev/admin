<div class="row">
    <div class="col-12">
        <div id="search_form" class="card">
            <div class="card-body">
                <div class="row">
                    <?php echo form_open('', ['method' => 'get', 'id' => 'search_default_form', 'class' => 'search_form col-12']); ?>
                    <div class="row">
                    <div id="default_period_form" class="col-12 form-group">
          <label for="start_date"><?php echo _('Transaction Date'); ?></label>
          <div class="form-row">
        <?php echo $Layout->Element('search_period'); ?>
        </div>
        </div>
                        <!--<div id="fg_reference_date" class="col-12 col-lg-4 col-xl-3 form-group">
                            <label for="s_reference_date"><?php echo _('Reference Date'); ?></label>
                            <?php

                            $value_start_date = set_value('reference_date', $search_data['date']);

                            ?>
                            <div class="input-group-prepend date">
                                <?php echo form_input([
                                    'name' => 'reference_date',
                                    'id' => 's_reference_date',
                                    'value' => $value_start_date,
                                    'class' => 'form-control datepicker',
                                ]); ?>
                                <div class="input-group-text">
                                    <span class="material-icons">date_range</span>
                                </div>
                            </div>
                        </div> -->
                        <?php if ($search_data['course_category']['total'] and $search_data['course']['total']): ?>
                            <?php include __DIR__.DIRECTORY_SEPARATOR.'course_select.php'; ?>
                        <?php endif; ?>
                        <div class="form-group col-12 col-lg-4 col-xl-3">
                            <label for="s_user_type"><?php echo _('Order Type'); ?></label>
                            <?php

                            $user_options = ['all' => _('All User'), 'default' => _('Default User'), 'free' => _('Free User')];
                            $user_type_select = set_value('user_type', 'all');
                            echo form_dropdown('user_type', $user_options, $user_type_select, ['id' => 's_user_type', 'class' => 'form-control']);

                            ?>
                        </div>
                        <div class="form-group col-12 col-lg-4 col-xl-3">
                            <label for="s_reference_date"><?php echo _('Reference Date'); ?></label>
      <?php

        $value_start_date = set_value('reference_date', $search_data['date']);

              ?>
              <div class="input-group-prepend date">
                  <?php echo form_input(array(
                          'name' => 'reference_date',
                          'id' => 's_reference_date',
                          'value' => $value_start_date,
                          'class' => 'form-control datepicker',
                  )); ?>
                  <div class="input-group-text">
                      <span class="material-icons">date_range</span>
                  </div>
              </div>
            </div>
                        <div class="col-12">
                            <?php echo form_submit('', _('Search'), ['class' => 'btn btn-primary']); ?>
                        </div>
                    </div>
                    <?php echo form_close(); ?>
                </div>
            </div>
        </div>
    </div>
</div>
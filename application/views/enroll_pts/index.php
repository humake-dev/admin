<div id="messages" class="container">
  <div class="row">
    <div class="col-12">
      <?php echo form_open('', array('method' => 'get', 'class' => 'search_form card')); ?>
        <div class="card-body">
          <div class="form-row">
            <?php if ($this->session->userdata('role_id') < 3): ?>
            <div class="col-6 form-group">
            <label for="cb_branch"><?php echo _('Branch'); ?></label>
                  <?php

                    $select = set_value('branch_id', '');
                    $options = array('' => _('All'));

                    if ($data['branch_list']['total']) {
                        foreach ($data['branch_list']['list'] as $index => $value) {
                            $options[$value['id']] = $value['title'];
                        }
                    }

                    echo form_dropdown('branch_id', $options, $select, array('id' => 'cb_branch', 'class' => 'form-control'));
                  ?>
            </div>
            <?php endif; ?>
            <div class="col-12 form-group">
              <label for=""><?php echo _('Search Type'); ?></label>
              <div class="col-6">
              <div class="form-check form-check-inline">
  <label class="form-check-label">
    <input class="form-check-input" type="radio" name="search_type" value="search_period" <?php echo set_radio('search_type', 'search_period', true); ?>> <?php echo _('Search Period'); ?>
  </label>
</div>


<div class="form-check form-check-inline">
  <label class="form-check-label">
    <input class="form-check-input" type="radio" name="search_type" value="search_s_or_g" <?php echo set_radio('search_type', 'search_s_or_g'); ?>> <?php echo _('Search Smaller Or Greater'); ?>
  </label>
</div>

<div class="form-check form-check-inline">
  <label class="form-check-label">
    <input class="form-check-input" type="radio" name="search_type" value="search_serial" <?php echo set_radio('search_type', 'search_serial'); ?>> <?php echo _('Search Serial'); ?>
  </label>
</div>

</div>              
            </div>

            <div id="search_period_type" class="col-6 form-group"<?php if (in_array($this->input->get('search_type'), array('search_s_or_g', 'search_serial'))): ?> style="display:none"<?php endif; ?>>
                <label><?php echo _('Search Period Type'); ?></label>
                <?php

                $spt_select = set_value('search_period_type', 'transaction_date');
                $spt_options = array('transaction_date' => _('Transaction Date'), 'start_date' => _('Start Date'), 'end_date' => _('End Date')); /* , 'create_date' => _('Created At')); */

                echo form_dropdown('search_period_type', $spt_options, $spt_select, array('class' => 'form-control'));
                ?>
              </div>


            <div id="search_period" class="col-12 form-group"<?php if (in_array($this->input->get('search_type'), array('search_s_or_g', 'search_serial'))): ?> style="display:none"<?php endif; ?>>
            <label><?php echo $spt_options[$spt_select]; ?></label>            
  <div class="input-group-prepend">
  <?php
    echo form_input(array('name' => 'start_date', 'value' => set_value('start_date'), 'class' => 'form-control datepicker'));
  ?>
  <div class="input-group-text">~</div>
  <?php
    echo form_input(array('name' => 'end_date', 'value' => set_value('end_date'), 'class' => 'form-control datepicker'));
  ?>
  </div>
</div>  



        <div id="search_number" class="col-6 form-group"<?php if ($this->input->get('search_type') != 'search_serial'): ?> style="display:none"<?php endif; ?>>
              <label><?php echo _('PT Serial'); ?></label>
            <?php

            echo form_input(array(
              'id' => 'pt_serial',
              'name' => 'serial',
              'value' => set_value('serial'),
              'maxlength' => '60',
              'size' => '60',
              'class' => 'form-control',
            ));
            ?>
            </div>

            <div id="search_range" class="col-12 form-group"<?php if ($this->input->get('search_type') != 'search_s_or_g'): ?> style="display:none"<?php endif; ?>>
            <label><?php echo _('PT Serial'); ?></label>            
  <div class="input-group-prepend">
  <?php
    echo form_input(array('name' => 'start_serial', 'value' => set_value('start_serial'), 'class' => 'form-control'));
  ?>
  <div class="input-group-text">~</div>
  <?php
    echo form_input(array('name' => 'end_serial', 'value' => set_value('end_serial'), 'class' => 'form-control'));
  ?>
  </div>
</div>          

            <div class="col-12 form-group" style="margin-top:20px">
              <?php echo form_submit('', _('Search'), array('class' => 'btn btn-primary')); ?>
            </div>
          </div>
        </div>
      <?php echo form_close(); ?>
    </div>
  </div>
  <div class="row">
    <div class="col-12">
      <table class="table table-borderd">
        <colgroup>
            <col />
            <col />
            <col />
            <col />
            <col />
            <col />
            <col />        
            <col style="width:150px" />
        </colgroup>
        <thead class="thead-default">
          <tr>
            <th><?php echo _('PT Serial'); ?></th>
            <th><?php echo _('Branch'); ?></th>
            <th><?php echo _('User'); ?></th>
            <th><?php echo _('Manager'); ?></th>
            <th><?php echo _('Use Quantity'); ?></th>
            <th><?php echo _('Remain Count'); ?></th>
            <th><?php echo $spt_options[$spt_select]; ?></th>
            <th class="text-center"><?php echo _('Manage'); ?></th>
          </tr>
        </thead>
        <tbody>               
      <?php if ($data['total']): ?>
      <?php foreach ($data['list'] as $value): ?>
      <tr>
        <td><?php echo $value['serial']; ?></td>
        <td><?php echo $value['branch_title']; ?></td>        
        <td><?php echo $value['name']; ?></td>
        <td>
          <?php if (empty($value['manager'])): ?>
          <?php echo _('Not Set'); ?>
          <?php else: ?>
            <?php echo $value['manager']; ?>
          <?php endif; ?>
        </td>        
        <td><?php echo $value['use_quantity']; ?></td>
        <td>
          <?php
            echo($value['quantity'] - $value['use_quantity'])._('Count Time');
          ?>
        </td>
        <td>
            <?php
              switch ($spt_select) {
                case 'create_date':
                  echo get_dt_format($value['created_at'], $search_data['timezone']);
                  break;
                case 'start_date':
                  echo get_dt_format($value['start_date'], $search_data['timezone']);
                  break;
                case 'end_date':
                  echo get_dt_format($value['end_date'], $search_data['timezone']);
                  break;
                default:
                  echo get_dt_format($value['transaction_date'], $search_data['timezone']);
              }

            ?>
        </td>        
        <td>
          <?php echo anchor('enroll-pts/edit/'.$value['id'], _('Edit'), array('class' => 'btn btn-secondary')); ?>
          <?php echo anchor('enroll-pts/delete/'.$value['id'], _('Delete'), array('class' => 'btn btn-danger')); ?>          
        </td>
      </tr>
      <?php endforeach; ?>
      <?php else: ?>
          <tr>
            <td colspan="7" class="text-center"><?php echo _('No Data'); ?></td>
          </tr>
      <?php endif; ?>
        </tbody>
      </table>
      <?php echo $this->pagination->create_links(); ?> 
    </div>
    </div>
</div>
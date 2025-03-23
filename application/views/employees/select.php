<?php if ($this->input->get('popup')): ?>
<div class="modal-header">
  <h3 class="modal-title">
    <?php
        switch ($data['position']) {
          case 'fc':
            echo _('Select FC');
            break;
          case 'trainer':
            echo _('Select Trainer');
            break;
          default:
            echo _('Select Employee');
        }
    ?>
  </h3>
  <button type="button" class="close" data-dismiss="modal" aria-label="Close">
    <span aria-hidden="true">&times;</span>
  </button>
</div>
<div class="modal-body">
<?php else: ?>
  <div class="container">
    <div class="row">
      <div class="col-12">
<?php endif; ?>

<div class="card">
  <?php if (!$this->input->get('no-search')): ?>
  <?php echo form_open('', array('class' => 'card-body')); ?>  
    <div class="form-row">
      <div class="form-group col-6">
        <label for="e_status"><?php echo _('Status'); ?></label>
        <?php
        $options = array(
          'A' => _('All'),
          'H' => _('Holding'),
          'R' => _('Resignation')
         // 'L' => _('Leave'),
        );

          $select = set_value('e_status', 'H');
          echo form_dropdown('e_status', $options, $select, array('id' => 'e_status', 'class' => 'form-control'));
        ?>
      </div>
      <div class="form-group col-6">
        <label for="e_poisiton"><?php echo _('Employee Position'); ?></label>        
        <?php
          if ($this->input->get('default_position')) {
              $options = array('trainer' => _('Trainer'), 'fc' => _('FC'));
          } else {
              $options = array('all' => _('All'), 'trainer' => _('Trainer'), 'fc' => _('FC'));
          }
          if ($data['position'] == 'all') {
              if ($this->input->get('default_position')) {
                  $default_position = $this->input->get('default_position');
              } else {
                  $default_position = 'all';
              }
              $select = set_value('poisiton', $default_position);

              echo form_dropdown('poisiton', $options, $select, array('id' => 'e_poisiton', 'class' => 'form-control'));
          } else {
              echo form_dropdown('poisiton', $options, $data['position'], array('id' => 'e_poisiton', 'class' => 'form-control', 'disabled' => 'disabled'));
          }

        ?>
      </div>
    </div>
  <?php echo form_close();
          if ($this->input->get('default_position')) {
              echo form_input(array(
                'type' => 'hidden',
                'value' => '1',
                'id' => 'select_type_require'
              ));
          }
    ?>
    <?php endif ?>
</div>

<input type="hidden" id="employee_select_list_count" value="<?php echo $data['admin']['total']; ?>" />
<table id="employee_select_list" class="table table-hover">
  <colgroup>
    <col style="width:60px;" />
    <col />
    <col />
    <col />
    <col />
    <col />
  </colgroup>
  <thead>
    <tr class="thead-default">
      <th>
        <?php if ($data['type'] == 'multi'): ?>
            <input id="employee_select_check_all" type="checkbox" />
            <?php else: ?>
            <?php echo _('Select'); ?>
            <?php endif; ?>
          </th>
          <th><?php echo _('Name'); ?></th>
          <th><?php echo _('Type'); ?></th>
          <th><?php echo _('Status'); ?></th>
          <th><?php echo _('Gender'); ?></th>
          <th><?php echo _('Employee Position'); ?></th>
      </tr>
  </thead>
  <tbody>
    <?php if ($data['admin']['total']): ?>
      <?php if (!$this->input->get('no-search')): ?>
      <tr>
        <?php if ($data['type'] == 'single'): ?>
          <td class="text-center"><input name="nottoo" value="0" type="radio"></td>
          <?php else: ?>
            <td><input name="nottoo" value="0" type="checkbox"></td>
        <?php endif; ?>
        <td class="name">담당자 없음</td>
        <td colspan="4">&nbsp;</td>
      </tr>
    <?php endif ?>
    <?php foreach ($data['admin']['list'] as $index => $value): ?>      
      <tr>
      <td<?php if ($data['type'] == 'single'): ?> class="text-center"<?php endif; ?>>
            <?php if ($data['type'] == 'single'): ?>
            <input type="radio" name="id" value="<?php echo $value['id']; ?>">
            <?php else: ?>
            <input type="checkbox" name="id[]" value="<?php echo $value['id']; ?>">
            <?php endif; ?>
        </td>
        <td class="name">
          <?php echo $value['name']; ?>
        </td>
        <td><?php echo $value['role_name']; ?></td>
        <td><?php echo get_employee_status($value['status']); ?></td>
        <td><?php echo display_gender($value['gender']); ?></td>
        <td>
          <?php if ($value['is_trainer']): ?>
            <?php
              echo _('Trainer');
              echo form_input(array(
                'type' => 'hidden',
                'value' => '1',
                'class' => 'employee_trainer',
              ));
            ?>
          <?php endif; ?>
          <?php if ($value['is_fc']): ?>
            <?php
              echo _('FC');
              echo form_input(array(
                'type' => 'hidden',
                'value' => '1',
                'class' => 'employee_fc',
              ));
            ?>            
          <?php endif; ?>
        </td>
      </tr>
    <?php endforeach; ?>
    <?php else: ?>
      <tr>
        <td><?php echo _('No Data'); ?></td>
      </tr>
    <?php endif; ?>
  </tbody>
</table>
<div class="sl_pagination">
  <?php echo $this->pagination->create_links(); ?>
</div>

<?php if ($this->input->get('popup')): ?>
</div>
<div class="modal-footer">
  <?php echo form_submit('', _('Select'), array('id' => 'select', 'class' => 'btn btn-primary btn-block')); ?>
</div>
<script src="<?php echo $script; ?>"></script>
<?php else: ?>
      <?php echo form_submit('', _('Select'), array('id' => 'select', 'class' => 'btn btn-primary btn-block btn-lg')); ?>
    </div>
  </div>
</div>
<?php endif; ?>

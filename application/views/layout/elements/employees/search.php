<?php
  $param='';

  if(count($this->input->get())) {
    $param='?'.http_build_query($this->input->get(), '', '&amp;');
  }

?>
<article class="card employee_search">
  <div class="card-header">
    <h3><?php echo _('Employee Search') ?></h3>
    <div class="float-right buttons">
      <i class="material-icons">keyboard_arrow_down</i>
    </div>
  </div>
  <div class="card-body" style="display:none">
  <?php echo form_open('', array('method'=>'get')) ?>
    <div class="col-12">
        <div class="row form-group optional">
          <label for="" class="col-4 col-form-label"><?php echo _('Search Name') ?></label>
          <div class="col-8">
          <input name="employee_name" value="<?php echo set_value('employee_name') ?>" placeholder="<?php echo _('Insert Search Word') ?>" class="form-control form-control-sm" type="text">
        </div>
        </div>
      <div class="row form-group">
        <label for="" class="col-4 col-form-label"><?php echo _('Status') ?></label>
        <div class="col-8">
          <div class="form-check form-check-inline">
            <label class="form-check-label">
              <input type="checkbox" name="status[]" value="H" class="form-check-input" <?php if (in_array('H', $search_data['status'])): ?>checked="checked"<?php endif ?>><?php echo _('Holding') ?> 
            </label>
          </div>
          <!-- <div class="form-check form-check-inline">
            <label class="form-check-label">
                <input type="checkbox" name="status[]" value="L" class="form-check-input" <?php if (in_array('L', $search_data['status'])): ?>checked="checked"<?php endif ?>><?php echo _('Leave') ?> 
            </label>
          </div> -->
          <div class="form-check form-check-inline">
            <label class="form-check-label">
                <input type="checkbox" name="status[]" value="R" class="form-check-input" <?php if (in_array('R', $search_data['status'])): ?>checked="checked"<?php endif ?>><?php echo _('Resignation') ?> 
            </label>
          </div>
        </div>
      </div>
      </div>
      <div class="row form-group">
        <div class="col-12">
      <button class="btn btn-sm float-right"><?php echo _('Search') ?></button>
    </div>
    </div>
  <?php echo form_close() ?>
</div>
</article>
<article id="employee_list" class="row">
  <div class="col-12">
    <table class="table table-striped table-hover">
      <colgroup>
        <col />
        <col />
        <col />
        <col />
      </colgroup>
      <thead class="thead-default">
        <tr>
          <th></th>
          <th><?php echo _('Employee Name') ?></th>
          <th><?php echo _('Hiring Date') ?></th>
          <th><?php echo _('Status') ?></th>
        </tr>
      </thead>
      <tbody>
        <?php if ($data['admin']['total']): ?>
        <?php foreach ($data['admin']['list'] as $index=>$value): ?>
        <tr<?php if(isset($data['content']['id'])): ?><?php if ($data['content']['id']==$value['id']): ?> class="table-primary"<?php endif ?><?php endif ?>>
          <td><input type="hidden" name="employee_id" value="<?php echo $value['id'] ?>" /></td>
          <td>
            <?php if ($this->router->fetch_method()=='index'): ?>
            <?php echo anchor($this->router->fetch_class().'/view/'.$value['id'].$param, $value['name']) ?>
            <?php else: ?>
            <?php echo anchor($this->router->fetch_class().'/'.$this->router->fetch_method().'/'.$value['id'].$param, $value['name']) ?>
            <?php endif ?>
          </td>
          <td><?php echo $value['hiring_date'] ?></td>
          <td><?php echo get_employee_status($value['status'],true) ?></td>
        </tr>
        <?php endforeach ?>
        <?php else: ?>
        <tr>
          <td colspan="5"><?php echo _('No Data') ?></td>
        </tr>
        <?php endif ?>
      </tbody>
    </table>
  </div>
</article>

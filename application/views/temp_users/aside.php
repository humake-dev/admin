<aside class="col-12 col-lg-5 col-xl-4 col-xxl-3">
<?php
  $param = '';

  if (count($this->input->get())) {
      $param = '?'.http_build_query($this->input->get(), '', '&amp;');
  }

?>
<article class="card user_search">
  <div class="card-header">
    <ul class="nav nav-pills card-header-pills">
      <li class="nav-item">
        <a class="flex-sm-fill nav-link<?php if (empty($search_data['search_type'])): ?> active<?php else:?><?php if ($search_data['search_type'] == 'field'): ?> active<?php endif; ?><?php endif; ?>" href="#" style="font-size:14px"><?php echo _('Search Items'); ?></a>
      </li>
      <li class="nav-item">
        <a class="flex-sm-fill nav-link<?php if (!empty($search_data['search_type'])): ?><?php if ($search_data['search_type'] != 'field'): ?> active<?php endif; ?><?php endif; ?>" href="#" style="font-size:14px"><?php echo _('Search Status'); ?></a>
      </li>
    </ul>
    <div class="float-right buttons">
      <i class="material-icons">keyboard_arrow_up</i>
    </div>
  </div>
  <div class="card-body">
    <div class="card-block"<?php if (!empty($search_data['search_type'])): ?><?php if ($search_data['search_type'] != 'field'): ?> style="display:none"<?php endif; ?><?php endif; ?>>
      <?php echo form_open('/temp-users', array('method' => 'get', 'class' => '')); ?>
        <input type="hidden" name="search_type" value="field" />
        <div class="form-row">
          <div class="col-6 col-sm-6 form-group">
            <?php

            $option = array('name' => _('User Name'), 'phone' => _('Phone'));
            $search_default = 'name';

            if ($this->session->userdata('search_field')) {
                $search_default = $this->session->userdata('search_field');
            }

            $select = set_value('search_field', $search_default);
          echo form_dropdown('search_field', $option, $select, array('id' => 'ms_group', 'class' => 'form-control form-control-sm'));
      ?>
      </div>
      <div class="col-6 col-sm-6 form-group">
          <?php

            if ($this->session->userdata('show_omu')) {
                $checked = ' checked="checked"';
            } else {
                $checked = '';
            }
            ?>
            <label><input type="checkbox" name="show_only_my_user" value="1"<?php echo $checked; ?> /><?php echo _('See only my members'); ?></label>            

      </div>
      <div class="col-12  form-group">
      <div class="input-group">
        <?php
        echo form_input(array('type' => 'search', 'name' => 'search_word', 'value' => set_value('search_word'), 'placeHolder' => _('Search Word'), 'class' => 'form-control form-control-sm'));
        ?>
            <span class="input-group-btn">
            <?php echo form_submit('', _('Search'), array('class' => 'btn btn-primary btn-sm')); ?>
            </span>
          </div>
        </div>
      </div>
    <?php echo form_close(); ?>
  </div>

  <div class="card-block"<?php if (empty($search_data['search_type'])): ?> style="display:none"<?php else:?><?php if ($search_data['search_type'] == 'field'): ?> style="display:none"<?php endif; ?><?php endif; ?>>
    <?php echo form_open('/temp-users', array('method' => 'get', 'class' => '')); ?>
      <div class="form-row">
        <div class="col-12 col-sm-6 form-group">
          <?php
            $options = array('' => _('All'), 'status1' => '신규등록', 'status3' => '출석', 'status4' => '미출석');
            echo form_dropdown('search_type', $options, set_value('search_type', 'all'), array('id' => 's_search_type', 'class' => 'form-control form-control-sm'));
          ?>
        </div>
        <div class="col-12 col-sm-6 form-group">
            <?php

            if ($this->session->userdata('show_omu')) {
                $checked = ' checked="checked"';
            } else {
                $checked = '';
            }
            ?>

            <label><input type="checkbox" name="show_only_my_user" value="1"<?php echo $checked; ?> /><?php echo _('See only my members'); ?></label>
        </div>
  <div class="col-11 form-group">
      <div class="form-check form-check-inline">
        <label class="form-check-label">
          <?php

            $m_checked = set_radio('term', 'all', true);

            echo form_radio(array(
              'name' => 'term',
              'value' => 'all',
              'checked' => $m_checked,
              'class' => 'form-check-input',
            ));
          ?>
          <?php echo _('All'); ?>
        </label>
      </div>
      <div class="form-check form-check-inline">
        <label class="form-check-label">
          <?php

            $m_checked = set_radio('term', 'month');

            echo form_radio(array(
              'name' => 'term',
              'value' => 'month',
              'checked' => $m_checked,
              'class' => 'form-check-input',
            ));
          ?>
          <?php echo _('Current Month'); ?>
        </label>
      </div>
      <div class="form-check form-check-inline">
        <label class="form-check-label">
          <?php

            $m_checked = set_radio('term', 'week');

            echo form_radio(array(
              'name' => 'term',
              'value' => 'week',
              'checked' => $m_checked,
              'class' => 'form-check-input',
            ));
          ?>
          <?php echo _('Current Week'); ?>
        </label>
      </div>
      <div class="form-check form-check-inline">
        <label class="form-check-label">
          <?php

            $m_checked = set_radio('term', 'day');

            echo form_radio(array(
              'name' => 'term',
              'value' => 'day',
              'checked' => $m_checked,
              'class' => 'form-check-input',
            ));
          ?>
          <?php echo _('Today'); ?>
        </label>
      </div>
    </div>
  <div class="col-1 form-group">
    <?php echo form_submit('', _('Search'), array('class' => 'btn btn-primary float-right btn-sm')); ?>
  </div>
</div>
  <?php echo form_close(); ?>
</div>
</div>
</article>

<article id="user_list" class="row">
  <input type="hidden" id="user_list_count" value="<?php echo $data['user']['total']; ?>" />
  <div class="col-12">
    <table class="table table-striped table-hover">
        <colgroup>
          <col class="d-none d-lg-table-cell" />
          <col />
          <col />
        </colgroup>
        <thead class="thead-default">
          <tr>
            <th class="d-none d-lg-table-cell"></th>
            <th><?php echo _('User Name'); ?></th>
            <th>
              <?php
                    switch ($this->input->get('search_field')) {
                  case 'card_no':
                    echo _('Access Card No');
                    break;
                  case 'name':
                    echo _('Name');
                    break;
                  default:
                    echo _('Phone');
                }
              ?>
            </th>
          </tr>
        </thead>
        <tbody>
          <?php if ($data['user']['total']): ?>
          <?php foreach ($data['user']['list'] as $index => $value): ?>
          <tr<?php if (isset($data['content'])): ?><?php if ($data['content']['id'] == $value['id']): ?> class="table-primary"<?php endif; ?><?php endif; ?>>
            <td class="d-none d-lg-table-cell">
              <?php echo number_format($data['user']['total'] - ($data['page']) - $index); ?>
            </td>
            <td>
              <?php
              if (strpos($value['name'], ' ') !== false) {
                  $value['name'] = explode(' ', $value['name'])[0];
              }
              ?>
              <div  style="display:block;width:100px;white-space:nowrap;text-overflow:ellipsis">
              <?php if (in_array($this->router->fetch_method(), array('index', 'view'))): ?>
              <?php echo anchor('temp-users/view/'.$value['id'].$param, $value['name']); ?>
              <?php else: ?>
              <?php echo anchor($this->router->fetch_class().'/'.$this->router->fetch_method().'/'.$value['id'].$param, $value['name']); ?>
              <?php endif; ?>
            </div>
            </td>
            <td>
              <?php
                    switch ($this->input->get('search_field')) {
                  case 'name':
                    echo $value['name'];
                    break;
                  default:
                        echo get_hyphen_phone($value['phone']);
                }
              ?>
            </td>
          </tr>
          <?php endforeach; ?>
          <?php else: ?>
          <tr>
            <td colspan="4"><?php echo _('No Data'); ?></td>
          </tr>
          <?php endif; ?>
        </tbody>
      </table>
      <?php echo $this->pagination->create_links(); ?>
    </div>


</article>
</aside>

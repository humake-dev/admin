<?php
  $param = '';

  if (count($this->input->get())) {
      $param = '?'.http_build_query($this->input->get(), '', '&amp;');
  }

?>
<article class="card user_search">
  <div class="card-body">
    <?php echo form_open('/home', array('method' => 'get', 'class' => '')); ?>
      <input type="hidden" name="search_type" value="field">
      <div class="form-row">
        <div class="col-6 col-sm-6 form-group">
        <?php
        
          if (empty($common_data['branch']['use_access_card'])) {
            $option = array('name' => _('User Name'), 'phone' => _('Phone'));
          } else {
            $option = array('name' => _('User Name'), 'card_no' => _('Access Card No'), 'phone' => _('Phone'));
          }
          
          $search_default = 'name';
          
          if ($this->session->userdata('search_field')) {
            $search_default = $this->session->userdata('search_field');
          }
          
          $select = set_value('search_field', $search_default);
          echo form_dropdown('search_field', $option, $select, array('id' => 'ms_group', 'class' => 'form-control form-control-sm'));
      ?>
      </div>
      <div class="col-6 col-sm-6 form-group">
        <div class="form-check">
        &nbsp;&nbsp;&nbsp;
          <?php

            if ($this->session->userdata('show_omu')) {
                $checked = ' checked="checked"';
            } else {
                $checked = '';
            }
            ?>
            <input type="checkbox" id="show_only_my_user" name="show_only_my_user" value="1" class="form-check-input" <?php echo $checked; ?>>            
            <label for="show_only_my_user" class="form-check-label"><?php echo _('See only my members'); ?></label>
          </div>
      </div>
      <div class="col-12 form-group">
      <div class="input-group">
        <?php
        echo form_input(array('type' => 'search', 'name' => 'search_word', 'value' => set_value('search_word'), 'placeHolder' => _('Search Word'), 'class' => 'form-control form-control-sm'));
        ?>
            <span class="input-group-btn">
            <?php echo form_submit('', _('Search'), array('class' => 'btn btn-primary btn-sm')); ?>
            </span>
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
            <th class="d-none d-lg-table-cell"><?php if ($this->router->fetch_class() == 'users'): ?><?php echo _('Increment Number'); ?><?php endif; ?></th>
            <th><?php echo _('User Name'); ?></th>
            <th>
              <?php
                    switch ($this->input->get('search_field')) {
                  case 'card_no':
                    echo _('Access Card No');
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
          <tr<?php if (!empty($data['content'])): ?><?php if ($data['content']['id'] == $value['id']): ?> class="table-primary"<?php endif; ?><?php endif; ?>>
            <td class="d-none d-lg-table-cell">
              <?php if (in_array($this->router->fetch_class(), array('home', 'users'))): ?>
              <?php echo number_format($data['user']['total'] - ($data['page']) - $index); ?>
              <?php else: ?>
              <input type="checkbox" name="user[]" value="<?php echo $value['id']; ?>">
              <?php endif; ?>
            </td>
            <td>
              <?php
              if (strpos($value['name'], ' ') !== false) {
                  $value['name'] = explode(' ', $value['name'])[0];
              }
              ?>
              <div  style="display:block;width:100px;white-space:nowrap;text-overflow:ellipsis">
              <?php if (in_array($this->router->fetch_class(), array('home', 'users'))): ?>
              <?php if (in_array($this->router->fetch_method(), array('index', 'view'))): ?>
              <?php echo anchor('/view/'.$value['id'].$param, $value['name']); ?>
              <?php else: ?>
              <?php echo anchor($this->router->fetch_class().'/'.$this->router->fetch_method().'/'.$value['id'].$param, $value['name']); ?>
              <?php endif; ?>
              <?php else: ?>
              <?php echo $value['name']; ?>
              <?php endif; ?>
            </div>
            </td>
            <td>
              <?php
                    switch ($this->input->get('search_field')) {
                  case 'card_no':
                    echo get_card_no($value['card_no'], false);
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
    </div>
</article>

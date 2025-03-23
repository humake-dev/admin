<?php

$params = '';
if ($this->input->get()) {
    $p_index = 0;
    foreach ($this->input->get() as $key => $param) {
        if ($p_index) {
            $params .= '&'.$key.'='.$param;
        } else {
            $params .= '?'.$key.'='.$param;
        }
        ++$p_index;
    }
}

$f_params = array('type' => $search_data['type'], 'date' => $search_data['date']);

if (isset($data['trainer'])) {
    $f_params['trainer'] = $data['trainer'];
}

if ($this->input->get('page')) {
    $f_params['page'] = $this->input->get('page');
}

?>
<div id="reservations" class="container">
  <div class="row">
    <div class="col-12">
      <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
          <li class="breadcrumb-item"><?php echo anchor('/', _('Home')); ?></li>
          <li class="breadcrumb-item" aria-current="page">
          <?php

            $link_text = _('Reservation List');

            if ($search_data['type'] != 'day'):
              if ($search_data['type'] == 'month'):
                $link_text .= '('._('Monthly').')';
              else:
                $link_text .= '('._('Weekly').')';
              endif;
            endif;

            echo anchor('/reservations'.$params, $link_text);
          ?>
          </li>
          <li class="breadcrumb-item active" aria-current="page"><strong>완료처리</strong></li>
        </ol>
      </nav>
    </div>
    <div class="col-12">
      <article class="row">
        <h1 class="col-12" style="margin-top:20px">완료처리</h1>
          <?php echo form_open('', array('class' => 'col-12'), $f_params); ?>
          <?php if($this->input->get_post('return_url')): ?>
            <?php echo form_input(array('type' => 'hidden','name'=>'return_url','value'=>$this->input->get_post('return_url'))); ?>
            <?php endif ?>
          <?php $check_boxs = 0; ?>
          <table class="table table-stripped">
            <colgroup>
              <col />
              <?php if (!empty($common_data['branch']['use_access_card'])): ?>  
              <col />
              <?php endif; ?>
              <col />
              <col />
              <col />
            </colgroup>
            <thead>
              <tr class="thead-default">
                <th><?php echo _('Result'); ?></th>
                <?php if (!empty($common_data['branch']['use_access_card'])): ?>                  
                <th><?php echo _('Access Card No'); ?></th>
                <?php endif; ?>
                <th><?php echo _('User Name'); ?></th>
                <th><?php echo _('Phone'); ?></th>
                <th><?php echo _('App Install'); ?></th>
              </tr>
            </thead>
            <tbody>
            <?php if (empty($data['total'])): ?>
            <tr>
              <td colspan="7"><?php echo _('No Data'); ?></td>
            </tr>
            <?php else: ?>
            <?php foreach ($data['list'] as $index => $value): ?>
            <tr>
              <td style="padding-left:30px">
                <?php
                $enable = true;
                $m_checked = true;
                $class = 'form-check-input';

              switch ($value['complete']) {
                case 1:
                  echo '<span class="text-info">'._('Wating Confirm').'</span>';
                  if ($this->Acl->has_permission('employees')) {
                      ?>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                    <?php
                    echo form_radio(array(
                            'name' => 'reservation_user['.$value['id'].']',
                            'id' => 'users2_'.($index + 1),
                            'value' => 'grant',
                            'class' => 'form-check-input',
                            'checked' => 'checked'                            
                    ));
                      ++$check_boxs; ?>
                    <label class="form-check-label" style="vertical-align:bottom;padding-left:0;padding-right:20px" for="<?php echo 'users2_'.($index + 1); ?>"><?php echo _('Grant'); ?></label>

                  <?php
                  }
                  break;
                case 2:
                  echo '<span class="text-info">'._('Wating No Show Confirm').'</span>';
                  if ($this->Acl->has_permission('employees')) {
                      ?>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                    <?php
                    echo form_radio(array(
                            'name' => 'reservation_user['.$value['id'].']',
                            'id' => 'users2_'.($index + 1),
                            'value' => 'no_show_confirm',
                            'class' => 'form-check-input',
                            'checked' => 'checked'
                    ));
                      ++$check_boxs; ?>
                    <label class="form-check-label" style="vertical-align:bottom;padding-left:0;padding-right:20px" for="<?php echo 'users2_'.($index + 1); ?>"><?php echo _('No Show Confirm'); ?></label>

                  <?php
                  }
                  break;
                case 3:
                  echo '<span class="text-success">완료승인됨</span>';
                  break;
                case 4:
                  echo '<span class="text-danger">미출석</span>';
                  break;
                default:
              if ($enable):
                $check_boxs++;
              ?>
              <div class="form-check form-check-inline">
                <?php
                echo form_radio(array(
                        'name' => 'reservation_user['.$value['id'].']',
                        'id' => 'users1_'.($index + 1),
                        'value' => 'complete',
                        'checked' => 'checked',
                        'class' => 'form-check-input',
                ));
                ?>
                <label class="form-check-label" style="vertical-align:bottom;padding-left:0;padding-right:20px" for="<?php echo 'users1_'.($index + 1); ?>"><?php echo _('Complete'); ?></label>
              </div>
              <?php endif; ?>
              <?php } ?>
              </td>
              <?php if (!empty($common_data['branch']['use_access_card'])): ?>                
              <td><?php echo get_card_no($value['card_no'], false); ?></td>
              <?php endif; ?>
              <td><?php echo $value['name']; ?></td>
              <td><?php echo get_hyphen_phone($value['phone']); ?></td>
              <td>
                <?php if (empty($value['token'])): ?>
                  <span class="text-danger"><?php echo _('Not Install'); ?></span>
                <?php else: ?>
                  <span class="text-success"><?php echo _('Install'); ?></span>
                <?php endif; ?>
              </td>
            </td>
          <?php endforeach; ?>
          <?php endif; ?>
          </tbody>
        </table>
        <?php echo form_submit('', _('Change'), array('class' => 'btn btn-primary btn-lg btn-block')); ?>
        <?php echo form_close(); ?>
      </article>
    </div>
  </div>
</div>

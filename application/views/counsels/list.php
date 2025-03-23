<div class="row">
  <div class="col-12 col-lg-6">
    <p class="summary">
        <span id="list_count" style="display:none"><?php echo $data['total']; ?></span>
        <?php echo sprintf(_('There Are %d Counsel'), $data['total']); ?>
      </p>
    </div>
    <div class="col-12 col-lg-6 text-right">
        <?php echo anchor('counsels/export-excel'.$params, _('Export Excel'), ['class' => 'btn btn-secondary']); ?>
    </div>
  <div class="col-12">
    <table id="counsel_list" class="table table-bordered table-hover">
      <colgroup>
        <!-- <col style="width:50px"> -->
        <col style="width:100px">
        <col style="width:100px">
        <col style="width:100px">        
        <col style="width:170px">
        <col>
        <col>
        <col>
        <col style="width:120px">
        <col style="width:130px">        
        <col style="width:130px">
        <col style="width:150px">
      </colgroup>
      <thead class="thead-default">
        <tr>
          <!-- <th><input id="check_all" name="all" value="1" type="checkbox"<?php if(!empty($data['all'])): ?> checked="checked"<?php endif ?>></th> -->
          <th><?php echo _('Manager'); ?></th>
          <th><?php echo _('Counselor'); ?></th>
          <th><?php echo _('Counsel User'); ?></th>
          <th><?php echo _('Phone'); ?></th>
          <th><?php echo _('Type'); ?></th>
          <th><?php echo _('Question Course'); ?></th>
          <th><?php echo _('Content'); ?></th>
          <th><?php echo _('Show Content'); ?></th>                              
          <th><?php echo _('Counsel Date'); ?></th>
          <th><?php echo _('Counsel Result'); ?></th>
          <th class="text-center"><?php echo _('Manage'); ?></th>
        </tr>
      </thead>
      <tbody>
        <?php if (empty($data['total'])): ?>
        <tr>
          <td colspan="9" style="text-align:center"><?php echo _('No Data'); ?></td>
        </tr>
        <?php else: ?>
        <?php foreach ($data['list'] as $index => $value):
          $page_param = '';
          if ($this->input->get('page')) {
              $page_param = '?page='.$this->input->get('page');
          }
        ?>
        <tr<?php if (isset($data['content'])): ?><?php if ($data['content']['id'] == $value['id']): ?> class="table-primary"<?php endif; ?><?php endif; ?>>
          <!-- <td><input name="id[]" value="<?php echo $value['id']; ?>" type="checkbox"<?php if(!empty($data['all'])): ?> checked="checked"<?php endif ?>></td> -->
          <td<?php if($this->session->userdata('role_id')<=5): ?> class="manager-td"<?php endif ?>>
            <?php if (empty($value['manager_name'])): ?>
            <span class="text-warning manager-name"><?php echo _('Not Inserted'); ?></span>
            <?php else : ?>
            <span class="manager-name"><?php echo $value['manager_name']; ?></span>
            <?php endif; ?>

            <?php if($this->session->userdata('role_id')<=5): ?>
            <?php if(empty($value['cm_id'])): ?>
              <?php echo form_open('counsel-managers/add', array('class' => 'counsel_manager_form','style'=>'display:none'), array('counsel' => $value['id'],'return_url'=>$return_url)) ?>
            <?php else: ?>
              <?php echo form_open('counsel-managers/edit/'.$value['cm_id'], array('class' => 'counsel_manager_form','style'=>'display:none'),array('return_url'=>$return_url)) ?>
            <?php endif ?>
              <?php
                        $options = array('' => _('Not Inserted'));
                        $select = set_value('manager', '');

                            foreach ($data['manager']['list'] as $manager) {
                                $options[$manager['id']] = $manager['name'];
                            }

                        if (isset($value['manager_id'])) {
                            $select = set_value('manager', $value['manager_id']);
                        }

                        echo form_dropdown('manager', $options, $select, array('id' => 'c_manager_id', 'class' => 'form-control'));
                        ?>
              <div class="form-group">
                <?php echo form_submit('', _('Change'), array('class' => 'btn btn-primary btn-block')); ?>
              </div>
              <?php echo form_close(); ?>
            <?php endif ?>
          </td>
          <td>
            <?php if (empty($value['counselor_name'])): ?>
            <span class="text-warning"><?php echo _('Not Inserted'); ?></span>
            <?php else : ?>
            <?php echo $value['counselor_name']; ?>
            <?php endif; ?>
          </td>
          <td>
            <?php if (empty($value['user_id'])): ?>
            <?php if (empty($value['temp_user_id'])): ?>
              <?php echo _('Deleted User') ?>
            <?php else: ?>
            <?php echo anchor('/temp-users/view/'.$value['temp_user_id'], $value['user_name']); ?>
            <?php endif ?>
            <?php else: ?>
            <?php echo anchor('/view/'.$value['user_id'], $value['user_name']); ?>
            <?php endif; ?>
          </td>
          <td class="phone"><?php echo get_hyphen_phone($value['phone']); ?></td>
          <td style="white-space:nowrap">
            <?php 
              switch($value['type']) {
                case 'D' : 
                  $c_type=_('Counsel By App'); 
                  break;
                case 'E' : 
                  $c_type=_('Counsel By Interview');
                  break;
                default : 
                  $c_type=_('Counsel By Phone'); 
              }
            ?>
            <?php echo $c_type ?>
          </td>
          <td style="white-space:nowrap">
            <?php 
                switch ($value['question_course']) {
                  case 'pt':
                    echo _('Question PT');
                    break;
                  case 'golf':
                    echo _('Question Golf');
                     break;
                  default:
                   echo _('Question Default');
                }
            
            ?>
          </td>
          <td><?php echo anchor('counsels/view/'.$value['id'], $value['title']) ?></td>
          <td><?php echo anchor('counsels/view/'.$value['id'], _('Show Content'), array('class' => 'btn btn-secondary btn-modal')); ?></td>          
          <td style="white-space:nowrap"><?php echo get_dt_format($value['execute_date'], $search_data['timezone']); ?></td>
          <td>
            <?php if (empty($value['complete'])): ?>
            <span><?php echo _('Processing'); ?></span>
            <?php else: ?>
            <span class="text-success"><?php echo _('Process Complete'); ?></span>
            <?php endif; ?>
          </td>
          <td>
            <?php echo anchor('counsels/edit/'.$value['id'].$page_param, _('Edit'), array('class' => 'btn btn-secondary')); ?>
            <?php echo anchor('counsels/delete/'.$value['id'], _('Delete'), array('class' => 'btn btn-danger btn-delete-confirm')); ?>
          </td>
        </tr>
        <?php endforeach; ?>
        <?php endif; ?>
      </tbody>
    </table>
   <!-- <label class=""><input id="check_real_all" name="real_all" value="1" type="checkbox"<?php if(!empty($data['real_all'])): ?> checked="checked"<?php endif ?>> 전체선택</label> -->
  </div>
<?php

  $no_search_form=false;
  
  if($this->session->userdata('role_id')<=5): 
    if($this->input->get('no_manager') and !empty($data['total'])):
      $no_search_form=true;
    endif;
  endif;
?>
<div class="col-12">
<?php echo $this->pagination->create_links(); ?>
</div>
  <div class="<?php if($no_search_form): ?>col-6<?php else: ?>col-12<?php endif ?>">

    <?php if ($this->Acl->has_permission('counsels', 'write')): ?>
    <?php echo anchor('counsels/add', _('Add'), array('class' => 'btn btn-primary float-left', 'title' => _('New Counsel Description'))); ?>
    <?php endif; ?>
    <?php if ($this->Acl->has_permission('messages', 'write')): ?>
    &nbsp;&nbsp;
    <?php

$reciever = _('All');
$send_link = '/messages/add';

if (!empty($params)) {
    $t_params = str_replace('?', '', $params);
    parse_str($t_params, $param_array);

    if (!empty($param_array['page'])) {
        unset($param_array['page']);
    }

    if (count($param_array)) {
        $reciever = $data['total'] . _('Count People');
        $send_link = '/messages/add' . $params . '&amp;counsel_search=1';
    }
}

?>
<a href="<?php echo $send_link ?>" class="btn btn-primary" target="_blank"
   title="<?php echo _('Send SMS Message'); ?>"><i class="material-icons" style="vertical-align:bottom">mail</i>
    <span style="vertical-align:bottom"><?php echo _('Send Message'); ?>(<?php echo $reciever; ?>)</span></a>
<?php endif ?>

<?php if($no_search_form): ?>
  </div>
  <div class="col-6">
    검색된 담당 미입력 회원들의 담당자를
<?php echo form_open('counsel-managers/add-all'.$params, array('class' => 'counsel_manager_form'),array('return_url'=>$return_url)) ?>
<?php
  $no_form_options=array();
                        $select = set_value('manager', '');

                            foreach ($data['manager']['list'] as $manager) {
                                $no_form_options[$manager['id']] = $manager['name'];
                            }

                        if (isset($value['manager_id'])) {
                            $select = set_value('manager', $value['manager_id']);
                        }

                        echo form_dropdown('manager', $no_form_options, $select, array('id' => 'c_manager_id', 'class' => 'form-control'));
                        ?>
              <div class="form-group">
                <?php echo form_submit('', _('Insert'), array('class' => 'btn btn-primary')); ?>
              </div>
              <?php echo form_close(); ?>
<?php endif ?>
  </div>
</div>

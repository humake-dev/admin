<?php
    echo form_open('', array('method'=>'get','class'=>'search_form card'));

    if (isset($data['employee']['id'])) {
        $default_employee_value=$data['employee']['id'];
    } else {
        $default_employee_value='';
    }
    
    $employee_value=set_value('name',$default_employee_value);
    echo form_input(array('type'=>'hidden','id'=>'e_employee_id','name'=>'employee_id','value'=>$employee_value));

    if (isset($data['user']['id'])) {
        $default_user_value=$data['user']['id'];
    } else {
        $default_user_value='';
    }

    $user_value=set_value('name',$default_user_value);
    echo form_input(array('type'=>'hidden','id'=>'c_user_id','name'=>'user_id','value'=>$user_value));

?>
<div class="card-body">
    <div class="col-12">
        <div class="row">
<?php if($this->input->get('enroll_only')): ?>
<?php
      echo form_input(array(
        'type'          => 'hidden',
        'name'            => 'enroll_only',
        'value'         => true,
    ));

      echo form_input(array(
          'type'          => 'hidden',
          'name'            => 'enroll_id',
          'value'         => $data['enroll_content']['id'],
      ));
    ?>

<?php else: ?>
            <div class="form-group col-6 col-xl-4">
                <label for="c_name"><?php echo _('Trainer') ?></label>
                <?php

if (isset($data['employee']['name'])) {
   $default_employee_name_value=$data['employee']['name'];
} else {
    $default_employee_name_value='';
}

$employee_name_value=set_value('employee_name',$default_employee_name_value);

  ?>
  <div class="input-group-prepend">
    <?php
  echo form_input(array(
      'id'            => 's_employee',
      'value'         => $employee_name_value,
      'maxlength'     => '10',
      'size'          => '10',
      'required'      => 'required',
      'readonly'      => 'readonly',
      'class'         => 'form-control'
  ));
?>
      <div class="input-group-text select-employee" title="<?php echo _('Select From Employee') ?>">
      <span class="material-icons">account_box</span>
      </div>
  </div>                
            </div>
            <div class="form-group col-6 col-xl-4">
            <label for="c_name"><?php echo _('User') ?></label>
            <?php

    if (isset($data['user']['name'])) {
       $default_user_name_value=$data['user']['name'];
    } else {
        $default_user_name_value='';
    }

    $user_name_value=set_value('user_name',$default_user_name_value);

      ?>
      <div class="input-group-prepend">
        <?php
      echo form_input(array(
          'id'            => 'c_name',
          'value'         => $user_name_value,
          'maxlength'     => '10',
          'size'          => '10',
          'required'      => 'required',
          'readonly'      => 'readonly',
          'class'         => 'form-control'
      ));
    ?>
          <div class="input-group-text select-user" title="<?php echo _('Select From User') ?>">
          <span class="material-icons">account_box</span>
          </div>
      </div>
    </div>
    <div class="form-group col-12 col-xl-4">
        <label for="enrolls"><?php echo _('Enroll') ?></label>
        <?php
            if(empty($data['enrolls']['total'])):
                if(empty($data['user']['id'])) {
                    $options=array(''=>'회원 선택 검색후 사용가능');
                } else {
                    $options=array(''=>'PT미보유');    
                }

                echo form_dropdown('enroll_id', $options, '', array('id' => 'enrolls', 'class' => 'form-control'));
            else:
                $options=array(''=>_('All'));
                foreach($data['enrolls']['list'] as $enroll) {
                    $options[$enroll['id']]=$enroll['start_date'].'~'.$enroll['end_date'].' ('.$enroll['use_quantity'].'/'.$enroll['insert_quantity'].')'._('Count Time');
                }

                if(empty($data['enroll_content']['id'])) {
                    $selected_enroll='';
                } else {
                    $selected_enroll=$data['enroll_content']['id'];
                }

                echo form_dropdown('enroll_id', $options, $selected_enroll,  array('id' => 'enrolls', 'class' => 'form-control'));
            endif
        ?>
        </div>
        <?php endif ?>
    </div>

    <div class="row">
    <div id="default_period_form" class="col-12 form-group">
        <label for="start_date"><?php echo _('Execute Date') ?></label>
        <div class="form-row">
            <?php echo $Layout->Element('search_period') ?>
        </div>
    </div>
    <div class="col-12 form-group">
        <?php echo form_submit('', _('Search'), array('class'=>'btn btn-primary')) ?>
    </div>
    </div>
</div>
</div>
<?php echo form_close() ?>

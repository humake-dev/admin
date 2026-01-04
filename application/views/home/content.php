<?php

$show_additional_info = false;

if ($this->input->get('show_addtional')) {
    $show_additional_info = true;
}

if (isset($data['content']['gender'])) {
    if ($data['content']['gender'] == 1) {
        $gender_value = _('Male');
    } else {
        $gender_value = _('Female');
    }
}

if (!empty($data['content']['birthday'])) {
    if (valid_date($data['content']['birthday'])) {
        $age_value = age($data['content']['birthday']) . _('Count Age');
    } else {
        $age_value = _('Invalid Birthday');
    }
}

$show_use_ac_controller = false;

if ($this->session->userdata('branch_id')) {
    if ($common_data['branch']['use_ac_controller']) {
        $show_use_ac_controller = true;
    }
}

$transfer_user = false;

?>
<div class="form-group text-right">
    <?php
    if ($this->session->userdata('branch_id')):
        if ($this->session->userdata('role_id') < 4):
            if (isset($common_data['branch_list'])):
                if ($common_data['branch_list']['total'] > 1):
                    if (empty($data['user_transfer_content'])):
                        echo anchor('/users/transfer/' . $data['content']['id'], _('Change Branch'), array('class' => 'btn btn-secondary'));
                    else:
                        $transfer_user = true;
                    endif;

                endif;
            endif;
        endif;


        if ($this->Acl->has_permission('users', 'write')):
            echo '&nbsp;' . anchor('/users/edit/' . $data['content']['id'] . $params, _('Edit'), array('class' => 'btn btn-secondary'));
        endif;

        if ($this->Acl->has_permission('users', 'write')):
            echo '&nbsp;' . anchor('/users/add', _('Add'), array('class' => 'btn btn-primary'));
        endif;

        if ($this->Acl->has_permission('users', 'delete')):
            echo '&nbsp;' . anchor('/users/delete/' . $data['content']['id'], _('Delete'), array('class' => 'btn btn-danger'));
        endif;

    endif;
    ?>
</div>
<?php if(!empty($data['show_count'])): ?>
<article class="card user_content_section<?php if ($transfer_user): ?> border-warning<?php endif; ?>">
    <div class="card-header">
        <h3><?php echo _('Enroll Count Info'); ?></h3>
    </div>
    <div class="card-body">
        <?php if(empty($data['current_enroll']['total'])): ?>
            <h2>유효한 회원권이 없습니다.</h2>
        <?php else: ?>
            <?php 
                $end_dateObj=new DateTime($data['current_enroll']['list'][0]['end_date'],$search_data['timezone']);
                $current_dayObj=new DateTime('now',$search_data['timezone']);
                
                $diffDays =  $end_dateObj->diff($current_dayObj)->days;
            ?>
            <h2><?php echo $data['content']['name'] ?>님은 종료일(<?php echo get_dt_format($end_dateObj->format('Y-m-d'), $search_data['timezone']) ?>)까지 <span style="font-size:40px"><?php echo $diffDays+1 ?></span>일 남았습니다.</h2>
            <script>
                const autoAttendance=true;
            </script>
        <?php endif ?>
    </div>
</article>
<?php endif ?>
<article class="card user_content_section<?php if ($transfer_user): ?> border-warning<?php endif; ?>">
    <div class="card-header">
        <h3><?php echo _('User Info'); ?></h3>
        <?php if($this->session->userdata('branch_id')): ?>
        <?php if ($show_use_ac_controller): ?>
            <a href="/users/ac-sync/<?php echo $data['content']['id']; ?>" title="<?php echo _('Ac Sync'); ?>"
               class="more"><i class="material-icons">sync</i></a>
        <?php endif ?>        
        <?php if ($this->Acl->has_permission('messages', 'write')): ?>
        <a href="/messages/add?user[]=<?php echo $data['content']['id']; ?>" title="<?php echo _('Message'); ?>"
               class="more2"><i class="material-icons">message</i></a>
        <?php endif ?>
        <?php endif ?>
    </div>
    <div class="card-body">
        <div class="col-12">
            <div class="row">
                <div class="col-12 col-md-6 col-xl-4 form-group">
                    <label><?php echo _('Name'); ?></label>
                    <p>
                        <?php echo $data['content']['name']; ?>
                        <?php if (isset($gender_value)): ?>
                            / <?php echo $gender_value; ?>
                        <?php endif; ?>
                    </p>
                </div>
                <?php if (!empty($common_data['branch']['use_access_card'])): ?>
                    <div class="col-12 col-md-6 col-xl-4 form-group">
                        <label><?php echo _('Access Card No'); ?></label>
                        <p><?php echo get_card_no($data['content']['card_no'], false); ?></p>
                    </div>
                <?php endif; ?>
                <div class="col-12 col-md-6 col-xl-4 form-group">
                    <label><?php echo _('Phone'); ?></label>
                    <p><?php echo get_hyphen_phone($data['content']['phone']); ?></p>
                </div>
                <div class="col-12 col-md-6 col-xl-4 form-group">
                    <label><?php echo _('Birthday'); ?></label>
                    <?php
                    $birthday_value = _('Not Inserted');
                    if (!empty($data['content']['birthday'])) {
                        $birthday_value = get_dt_format($data['content']['birthday'], $search_data['timezone']);
                    }
                    ?>
                    <p><?php echo $birthday_value; ?>
                        <?php if (isset($age_value)): ?>
                            / <?php echo $age_value; ?>
                        <?php endif; ?>
                    </p>
                </div>
                <div class="col-12 col-md-6 col-xl-4 form-group">
                    <label><?php echo _('User FC'); ?></label>
                    <?php

                    $fc_value = _('Not Inserted');
                    if (!empty($data['content']['fc_id'])) {
                        $fc_value = $data['content']['fc_name'];
                    }

                    ?>
                    <p><?php echo $fc_value; ?></p>
                </div>
                <div class="col-12 col-md-6 col-xl-4 form-group">
                    <label><?php echo _('User Trainer'); ?></label>
                    <?php
                    $trainer_value = _('Not Inserted');
                    if (!empty($data['content']['trainer_id'])) {
                        $trainer_value = $data['content']['trainer_name'];
                    }

                    ?>
                    <p><?php echo $trainer_value; ?></p>
                </div>
                <div class="col-12 col-md-6 col-xl-4 form-group additional_info"<?php if (empty($show_additional_info)): ?> style="display:none"<?php endif ?>>
                    <label><?php echo _('Visit Route'); ?></label>
                    <?php

                    $visit_route_value = _('Not Inserted');

                    if (!empty($data['content']['visit_route'])) {
                        $visit_route_value = $data['content']['visit_route'];
                    }
                    ?>
                    <p><?php echo $visit_route_value; ?></p>
                </div>
                <div class="col-12 col-md-6 col-xl-4 form-group additional_info"<?php if (empty($show_additional_info)): ?> style="display:none"<?php endif ?>>
                    <label><?php echo _('Job'); ?></label>
                    <?php
                    $job_value = _('Not Inserted');

                    if (!empty($data['content']['job'])) {
                        $job_value = $data['content']['job'];
                    }
                    ?>
                    <p><?php echo $job_value; ?></p>
                </div>
                <div class="col-12 col-md-6 col-xl-4 form-group additional_info"<?php if (empty($show_additional_info)): ?> style="display:none"<?php endif ?>>
                    <label><?php echo _('Company'); ?></label>
                    <?php
                    $company_value = _('Not Inserted');

                    if (!empty($data['content']['company'])) {
                        $company_value = $data['content']['company'];
                    }
                    ?>
                    <p><?php echo $company_value; ?></p>
                </div>
                <div class="col-12 col-md-6 col-xl-4 form-group">
                    <label><?php echo _('Registed Date'); ?></label>
                    <p><?php echo get_dt_format($data['content']['registration_date'], $search_data['timezone']); ?></p>
                </div>
            </div>
        </div>
    </div>
    <?php if (!empty($data['content']['visit_route']) or !empty($data['content']['job']) or !empty($data['content']['company'])): ?>
        <div class="card-footer" style="padding:0">
            <?php echo anchor('/view/' . $data['content']['id'] . '?show_addtional=true', '<i class="material-icons">keyboard_arrow_down</i>', array('id' => 'more-user-addtional', 'class' => 'btn btn-sm btn-block btn-link')); ?>
        </div>
    <?php endif; ?>
        <?php if ($transfer_user): ?>
            <div class="card-footer bg-warning border-warning">
                <div class="row">
                    <h3 class="col-12 col-lg-6 card-text text-white">지점 이동한 사용자 입니다</h3>
                    <div class="col-12 col-lg-6 text-right">
                        <?php echo anchor('/user-transfers/delete/' . $data['user_transfer_content']['id'],'<i class="material-icons">close</i>', array('style' => 'color:#fff')) ?>
                    </div>
                </div>
            </div>
        <?php endif; ?>
</article>

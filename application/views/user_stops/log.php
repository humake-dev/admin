<?php

$user_stop_list = array();

foreach ($data['stopped_log']['list'] as $stopped_log) {
    foreach ($data['user_stop_list']['list'] as $user_stop) {
        if ($stopped_log['user_stop_id'] == $user_stop['id']) {
            if (count($user_stop_list)) {
                $alread_exists = false;
                foreach ($user_stop_list as $index => $user_stop_list_content) {
                    if ($user_stop_list_content['id'] == $stopped_log['user_stop_id']) {
                        $alread_exists = true;
                    }
                }

                if (empty($alread_exists)) {
                    $user_stop['origin_end_date'] = $stopped_log['origin_end_date'];
                    $user_stop_list[] = $user_stop;
                }
            } else {
                $user_stop['origin_end_date'] = $stopped_log['origin_end_date'];
                $user_stop_list[] = $user_stop;
            }
        }
    }
}

?>
<table id="order_stop_log_list" class="table table-bordered table-hover">
    <colgroup>
        <col/>
        <col/>
        <col/>
        <col/>
        <col/>
        <col/>
        <col/>
        <col/>
        <?php if($this->session->userdata('role_id')<3): ?>
        <col/>
        <?php endif ?>
    </colgroup>
    <thead>
    <tr class="thead-default">
        <th class="text-center"><?php echo _('Enroll Increment Number'); ?></th>
        <th class="text-center"><?php echo _('Stop Increment Number'); ?></th>
        <th class="text-center"><?php echo _('Stop Days'); ?></th>
        <th class="text-center"><?php echo _('Stop Start Date'); ?></th>
        <th class="text-center"><?php echo _('Stop End Date'); ?></th>
        <th class="text-center"><?php echo _('Request Date'); ?></th>
        <th class="text-center"><?php echo _('Origin End Date'); ?></th>
        <th class="text-center"><?php echo _('Memo'); ?></th>
        <?php if($this->session->userdata('role_id')<3): ?>
        <th class="text-center"><?php echo _('Manage'); ?></th>
        <?php endif ?>
    </tr>
    </thead>
    <tbody>
    <?php

    $in_count=array();
    $old_in=0;
    $new_index=0;
    $a_index=0;

    foreach ($user_stop_list as $index => $value):
        if(!empty($value['in'])) {
        if($old_in==$value['in']) {
            $in_count[$a_index]=$new_index++;
        } else {
            $old_in=$value['in'];
            $a_index++;
            $new_index=1;
            $in_count[$a_index]=$new_index++;
        }
        }
    endforeach;

    $old_in=0;
    $new_index=0;
    $a_index=0;

    foreach ($user_stop_list as $index => $value):
        ?>
        <tr>
            <td class="text-center">
            <?php if(empty($value['in'])): ?>
            -            
            <?php else: ?>
            <?php echo $value['in']; ?>
            <?php endif ?>
            </td>
            <td class="text-center">
                <?php

                if(!empty($value['in'])) {
                    if($old_in==$value['in']) {
                        $new_index++;
                    } else {
                        $old_in=$value['in'];
                        $a_index++;
                        $new_index=0;
                    }

                    echo $in_count[$a_index]-$new_index;                    
                }
                ?>
            </td>
            <td class="text-center"><?php echo $value['stop_day_count']; ?><?php echo _('Day'); ?></td>
            <td class="text-center"><?php echo get_dt_format($value['stop_start_date'], $search_data['timezone']); ?></td>
            <td class="text-center"><?php echo get_dt_format($value['stop_end_date'], $search_data['timezone']); ?></td>
            <td class="text-center"><?php echo get_dt_format($value['request_date'], $search_data['timezone']); ?></td>
            <td class="text-center">
                <input type="hidden" value="<?php echo $value['id']; ?>"/>
                <?php echo get_dt_format($value['origin_end_date'], $search_data['timezone']); ?></td>
            </td>
            <td class="text-center">
                <?php if (empty($value['content_id'])): ?>
                    <?php echo anchor('user-stop-contents/add?user_stop_id=' . $value['id'], _('Add Content'), array('class' => 'btn btn-primary btn-modal')); ?>
                <?php else: ?>
                    <?php echo anchor('user-stop-contents/view/' . $value['content_id'], _('Show Content'), array('class' => 'btn btn-secondary btn-modal')); ?>
                <?php endif; ?>
            </td>
            <?php if($this->session->userdata('role_id')<3): ?>
            <td class="text-center">
                <?php echo anchor('user-stops/edit-log/' . $value['id'], _('Edit'), array('class' => 'btn btn-secondary')); ?>
                <?php echo anchor('user-stops/delete/' . $value['id'], _('Delete'), array('class' => 'btn btn-danger')); ?>
            </td>
            <?php endif ?>            
        </tr>
    <?php endforeach; ?>
    </tbody>
</table>

<?php foreach ($user_stop_list as $index => $user_stop): ?>
    <div id="user_stop_log_<?php echo $user_stop['id']; ?>" class="user_stop_log_detail" style="display:none">
        <h3><?php echo _('Stop Increment Number'); ?><span></span>에 <?php echo _('Affect Order'); ?></h3>
        <table class="table table-bordered order-stop-detail">
            <colgroup>
                <col/>
                <col/>
                <col/>
            </colgroup>
            <thead>
            <tr class="thead-default">
                <th class="text-center"><?php echo _('Enroll Increment Number'); ?></th>
                <th class="text-center"><?php echo _('Origin End Date'); ?></th>
                <th class="text-center"><?php echo _('Change End Date'); ?></th>
            </tr>
            </thead>
            <tbody>
            <?php
            $i = 0;
            foreach ($data['stopped_log']['list'] as $stopped_log):
                if ($stopped_log['user_stop_id'] != $user_stop['id']) {
                    continue;
                }
                ?>
                <tr>
                    <td class="text-center">
                        <?php if (empty($stopped_log['in'])): ?>
                            -
                        <?php else: ?>
                            <?php echo $stopped_log['in']; ?>
                        <?php endif; ?>
                    </td>
                    <td class="text-center"><?php echo get_dt_format($stopped_log['origin_end_date'], $search_data['timezone']); ?></td>
                    <td class="text-center"><?php echo get_dt_format($stopped_log['change_end_date'], $search_data['timezone']); ?></td>
                </tr>
                <?php ++$i; endforeach; ?>
            </tbody>
        </table>
    </div>
<?php endforeach; ?>

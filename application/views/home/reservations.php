<?php 
    $change_status=false;
    if($this->session->userdata('role_id')<5 or ($this->session->userdata('role_id')>=5 and $this->session->userdata('is_trainer'))) {
        $change_status=true;
    }

    $current_month_first_day = new DateTime($search_data['today'], $search_data['timezone']);
    $current_month_first_day->modify('first day of this month');
  
    $current_month_10_day = new DateTime($search_data['today'], $search_data['timezone']);
    $current_month_10_day->modify('first day of this month');
    $current_month_10_day->modify('+9 Days');
  
    $prev_month_first_day = new DateTime($search_data['today'], $search_data['timezone']);
    $prev_month_first_day->modify('first day of previous month');
?>
<input type="hidden" id="return-url" name="return-url" value="<?php echo $_SERVER['REQUEST_URI']  ?>">
<input type="hidden" id="change-status" name="change-status" value="<?php echo $change_status ?>">
<div id="users" class="container">
    <div class="row">
        <?php include __DIR__ . DIRECTORY_SEPARATOR . 'aside.php'; ?>
        <div class="col-12 col-lg-8 col-xxl-9 user_sub">
            <?php if (empty($data['content'])): ?>
                <?php echo $Layout->element('home/not_found.php'); ?>
            <?php else: ?>
                <?php echo $Layout->element('home/nav'); ?>
                <article class="card">
                    <h3 class="col-12 card-header"><?php echo _('Reservation'); ?></h3>
                    <div class="card-body">
                        <div class="float-right">
                            <p class="summary">
                                <span id="list_count" style="display:none"><?php echo $data['list']['total']; ?></span>
                                <?php echo sprintf(_('There Are %d Reservation'), $data['list']['total']); ?>
                            </p>
                        </div>
                        <table id="user_reservation_list" class="table table-striped">
                            <colgroup>
                                <col style="width:120px">
                                <col>
                                <col>
                                <col>
                                <col>
                                <col>
                                <col style="width:120px">
                                <col style="width:120px">
                            </colgroup>
                            <thead>
                            <tr class="thead-default">
                                <th><?php echo _('Course Category'); ?></th>
                                <th><?php echo _('Course'); ?></th>
                                <th><?php echo _('Manager'); ?></th>
                                <th><?php echo _('Start Time'); ?></th>
                                <th><?php echo _('Process'); ?></th>
                                <th><?php echo _('Commission'); ?></th>
                                <th><?php echo _('Progress Time'); ?></th>
                                <th><?php echo _('Manage'); ?></th>         
                            </tr>
                            </thead>
                            <tbody>
                            <?php if (empty($data['list']['total'])): ?>
                                <tr>
                                    <td colspan="<?php if($change_status): ?>8<?php else: ?>7<?php endif ?>" class="text-center"><?php echo _('No Data'); ?></td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($data['list']['list'] as $value): 
                                    

                                    $delete_available = false;
                                    if ($this->session->userdata('role_id') < 3) {
                                        $delete_available = true;
                                    } else {
                                        if ($this->session->userdata('role_id') > 5) {
                                            if($value['manager_id']==$this->session->userdata('admin_id')) {
                                            if (new DateTime($search_data['date'], $search_data['timezone']) >= new DateTime($search_data['today'], $search_data['timezone'])) {
                                                  $delete_available=true;
                                              }
                                            }
                                          } else {
    if (new DateTime($value['end_time'], $search_data['timezone']) >= $current_month_first_day) {
        $delete_available = true;
    } else {
        if (new DateTime($value['end_time'], $search_data['timezone']) >= $prev_month_first_day) {
            if (new DateTime($search_data['today'], $search_data['timezone']) <= $current_month_10_day) {
                $delete_available = true;
            }
        }
    }
}
                                    }                                    
                                    
                                    ?>
                                    <tr>
                                        <td><?php echo $value['type']; ?></td>
                                        <td>
                                            <?php if (empty($value['course_name'])): ?>
                                                -
                                            <?php else: ?>
                                                <?php echo $value['course_name']; ?>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <?php echo $value['manager_name']; ?>
                                            <?php if (empty($value['manager_enable'])): ?>
                                                <span class="text-danger">(<?php echo _('Deleted Employee'); ?>)</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <?php if ($value['eul_type'] == 'no_show'): ?>
                                                <span style="text-decoration:line-through"><?php echo $value['start_time']; ?></span>
                                            <?php else: ?>
                                                <span><?php echo $value['start_time']; ?></span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <?php if (in_array($value['complete'],array(3,4))): ?>
                                                <span class="text-success">
                                                <?php echo _('Complete'); ?>
                                                </span>
                                            <?php else: ?>
                                                <?php if ($this->session->userdata('role_id') < 4): ?>
                                                    <a href="/reservations/complete/<?php echo $value['reservation_id'] ?>?return_url=<?=$_SERVER['REQUEST_URI'] ?>"><?php echo _('Wating Reservation Complete'); ?></a>
                                                <?php else: ?>
                                                    <?php echo _('Wating Reservation Complete'); ?>
                                                <?php endif ?>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <?php if (empty($value['commission'])): ?>
                                                -
                                            <?php else: ?>
                                                <?php echo number_format($value['commission']); ?><?php echo _('Currency'); ?>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <?php if ($value['eul_type'] == 'no_show'): ?>
                                                <span class="text-danger"><?php echo _('No Show'); ?></span>
                                            <?php else: ?>
                                                <?php echo $value['progress_time']; ?><?php echo _('Minute'); ?>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <?php
                                            $return_url='/home/reservations/'.$data['content']['id'];

                                            if($this->input->get_post('page')) {
                                                $return_url.='?page='.$this->input->get_post('page');
                                            }
                                            
                                            if (empty($value['delete_available'])) {
                                                echo '-';
                                            } else {
                                                echo anchor('reservations/delete/'.$value['reservation_id'].'?return_url='.$return_url, _('Delete'), array('class' => 'btn btn-danger btn-delete-confirm'));
                                            }
                                            ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                            </tbody>
                        </table>
                        <div class="sl_pagination"></div>
                    </div>
                </article>
            <?php endif; ?>
        </div>
    </div>
</div>

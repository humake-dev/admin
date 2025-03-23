<article class="row" <?php if ($search_data['mode'] != 'list'): ?>style="display:none"<?php endif; ?>>
  <input type="hidden" id="perpage" value="<?php echo $data['per_page']; ?>" />
  <div class="col-12">
  <h2 class="float-left"><?php echo _('Reservation List'); ?>(<?php echo get_dt_format($search_data['start_time'], $search_data['timezone']); ?>)</h2>
    <div class="float-right">
      <p class="summary">
        <span id="list_count" style="display:none"><?php echo $data['total']; ?></span>
        <?php echo sprintf(_('There Are %d Reservation'), $data['total']); ?>
      </p>
    </div>
  </div>
  <div class="col-12">
    <table id="reservation_list" class="table table-bordered table-hover">
      <colgroup>
        <col />
        <col />
        <col />
        <col />
        <col style="width:120px">
        <col style="width:150px" />
      </colgroup>
      <thead class="thead-default">
        <tr>
          <th><?php echo _('Reservation Category'); ?></th>
          <th><?php echo _('Manager'); ?></th>
          <th><?php echo _('User'); ?></th>
          <th><?php echo _('Time'); ?></th>
          <th><?php echo _('Progress Time'); ?></th>
          <th class="text-center"><?php echo _('Manage'); ?></th>
        </tr>
      </thead>
      <tbody>
        <?php if (empty($data['total'])): ?>
        <tr>        
          <td<?php if ($this->Acl->has_permission('reservations', 'delete')): ?> colspan="7"<?php else: ?> colspan="6"<?php endif; ?>><?php echo _('No Data'); ?></td>
        </tr>        
        <?php else: ?>
        <?php foreach ($data['list'] as $index => $value):


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
          <td>
            <?php echo get_reservation_type($value['type']); ?></td>
          <td><?php echo $value['manager_name']; ?></td>
          <td style="text-align:left;border-right:none">
            <?php
                            $member_list = explode(',', $value['members']);
                            foreach ($member_list as $member):
                                $dd = explode('::', $member);
                        ?>
  <?php echo $dd[1]; ?>&nbsp;
            <?php endforeach; ?>
          </td>
          <td>
          <?php
            $star_time = new DateTime($value['start_time']);
            echo $star_time->format('H'._('Hour').' i'._('Minute'));
          ?> ~
          <?php
            $end_time = new DateTime($value['end_time']);
            echo $end_time->format('H'._('Hour').' i'._('Minute'));
          ?>
          </td>
          <td><?php echo $value['progress_time']; ?><?php echo _('Minute'); ?></td>
          <td class="text-center">
            <?php
              if ($delete_available) {
                  echo anchor('reservations/delete/'.$value['id'].'?'.$search_data['date'].'&amp;time='.$search_data['time'], _('Delete'), array('class' => 'btn btn-danger'));
              } else {
                  echo '-';
              }
            ?>
          </td>
        </tr>
        <?php endforeach; ?>
        <?php endif; ?>
      </tbody>
    </table>
    <?php if ($this->Acl->has_permission('reservations', 'write')): 

      $write_available=false;

      if ($this->session->userdata('role_id') < 3) {
              $write_available = true;
          } else {
    if ($this->session->userdata('role_id') > 5) {
        if (new DateTime($search_data['date'], $search_data['timezone']) >= new DateTime($search_data['today'], $search_data['timezone'])) {
            $write_available=true;
        }
    } else {
        if (new DateTime($search_data['date'], $search_data['timezone']) >= $current_month_first_day) {
            $write_available = true;
        } else {
            if (new DateTime($value['end_time'], $search_data['timezone']) >= $prev_month_first_day) {
                if (new DateTime($search_data['today'], $search_data['timezone']) <= $current_month_10_day) {
                    $write_available = true;
                }
            }
        }
    }
}


if ($write_available) {
    echo anchor('reservations/add?date='.$search_data['date'].'&amp;time='.$search_data['time'], '추가 등록', array('class' => 'btn btn-primary'));
}
    endif; 
    
    ?>
    </div>
  </article>

<?php

  if ($data['category']['total']):
  $facilityObjs = array();

  if ($data['total']) {
      foreach ($data['list'] as $rent) {
          if (key_exists($rent['no'], $facilityObjs)) {
              ++$facilityObjs[$rent['no']]['total'];
              $facilityObjs[$rent['no']]['list'][] = $rent;
          } else {
              $facilityObjs[$rent['no']] = array('total' => 1, 'list' => array($rent));
          }
      }
  }

  if ($data['facility_breakdown']['total']) {
      foreach ($data['facility_breakdown']['list'] as $facility_breakdown) {
          $facilityObjs[$facility_breakdown['no']]['breakdown'] = true;
          $facilityObjs[$facility_breakdown['no']]['breakdown_id'] = $facility_breakdown['id'];
          $facilityObjs[$facility_breakdown['no']]['breakdown_description'] = $facility_breakdown['description'];
      }
  }

  $quantity = $data['category']['content']['quantity'];
  $start_no = $data['category']['content']['start_no'];
  $end_no = $quantity + $start_no;
  $break_count = 0;
  $use_count = 0;
  $expire_count = 0;
  $reservation_count = 0;

  $now_obj = new Datetime('now', $search_data['timezone']);

  $a = 1;

  $rent_status_list = array();
  for ($no = $start_no; $no < $end_no; ++$no):
    $rent_list = null;
    $rent = null;
    $reservation_list = array('total' => 0);
    $display = false;
    $breakdown = false;

    $class = 'secondary';
    $status = 'await';
    $end_date = null;

    if (key_exists($no, $facilityObjs)) {
        $rent_list = $facilityObjs[$no];

        if (empty($rent_list['breakdown'])) { // 고장
            if (!empty($rent_list['total'])) {
                $use_rent = false;
                foreach ($rent_list['list'] as $rent) {
                    $expire_rent = false;
                    $reservation_rent = false;

                    if (empty($rent['change_end_date'])) {
                        $rent_end_datetime = $rent['end_datetime'];
                    } else {
                        $rent_end_datetime = $rent['change_end_date'].' 23:59:59';
                    }

                    if (new Datetime($rent_end_datetime, $search_data['timezone']) < $now_obj) { // 만료
                        $class = 'warning';
                        $end_date = $rent['end_date'];
                        $display = true;
                        $expire_rent = true;
                        $use_rent = false;
                    } else {
                        if (new Datetime($rent['start_datetime'], $search_data['timezone']) > $now_obj) { // 예약
                            ++$reservation_list['total'];
                            $reservation_list['list'][] = $rent;
                            $reservation_rent = true;
                        } else {
                            $class = 'success';
                            if (empty($rent['change_end_date'])) {
                                $end_date = $rent['end_date'];
                            } else {
                                $end_date = $rent['change_end_date'];
                            }
                            $display = true;
                            $use_rent = true;
                        }
                    }
                }

                if ($expire_rent) {
                    ++$expire_count;
                    $status = 'expire';
                }

                if ($reservation_rent) {
                    ++$reservation_count;
                    $status = 'reservation';
                }

                if ($use_rent) {
                    ++$use_count;
                    $status = 'use';
                }
            }
        } else {
            $class = 'danger';
            $display = false;
            $breakdown = true;
            ++$break_count;
            $status = 'breakdown';
        }
    }

    $facility_check = '';
    if (isset($data['no'])) {
        if ($data['no'] == $no) {
            $facility_check = '<i class="material-icons">done_all</i>';
        }
    }

    $rent_status_list[] = array('no' => $no, 'rent' => $rent, 'end_date' => $end_date, 'class' => $class, 'display' => $display, 'breakdown' => $breakdown, 'reservation_list' => $reservation_list, 'facility_check' => $facility_check, 'status' => $status);

    if ($class != 'secondary') {
        $class .= ' text-white';
    }
  endfor;

  $wait_count = $quantity - ($break_count + $use_count + $expire_count);

  if ($this->input->get('order')) {
      $rent_status_list = remake_rent_status_list_by_order($rent_status_list, $this->input->get('order'));
  }

?>
<div id="rents" class="container">
    <div class="row">
        <nav class="col-12 sub_nav">
            <ul class="nav nav-pills">
                <?php foreach ($data['category']['list'] as $index => $value): ?>
                <?php
                    if ($data['category']['current_id'] == $value['id']) {
                        $top_class = 'nav-link active';
                    } else {
                        $top_class = 'nav-link';
                    }
                ?>
                <li class="nav-item"><?php echo anchor('rents?facility_id='.$value['id'], $value['title'], array('class' => $top_class)); ?></li>
                <?php endforeach; ?>
            </ul>
        </nav>
        <div class="col-12">
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-body rent-status">
                            <div class="row">
                                <div class="col-12 col-lg-6">
                                    <dl>
                                        <dt class="btn btn-secondary btn-sm">
                                        <?php echo rent_order_anchor(_('Status Await'), $this->input->get('facility_id'), 'await'); ?>
                                        </dt>
                                        <dd><?php echo number_format($wait_count); ?></dd>
                                        <dt class="btn btn-sm btn-success"><?php echo rent_order_anchor(_('Status Use'), $this->input->get('facility_id'), 'use'); ?></dt>
                                        <dd><?php echo number_format($use_count); ?></dd>
                                        <dt class="btn btn-sm btn-warning"><?php echo rent_order_anchor(_('Status Expire'), $this->input->get('facility_id'), 'expire'); ?></dt>
                                        <dd><?php echo number_format($expire_count); ?></dd>
                                        <dt class="btn btn-sm btn-danger"><?php echo rent_order_anchor(_('Status Breakdown'), $this->input->get('facility_id'), 'breakdown'); ?></dt>
                                        <dd><?php echo number_format($break_count); ?></dd>
                                    </dl>
                                    <input type="hidden" id="facility_id" name="id" value="<?php echo $data['category']['content']['id']; ?>" />
                                </div>
                                <?php include __DIR__.DIRECTORY_SEPARATOR.'default_button.php'; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php include __DIR__.DIRECTORY_SEPARATOR.'aside.php'; ?>
    <div class="col-12 col-xxl-9">

    <div class="row list">
    <?php

    foreach ($rent_status_list as $index => $value): ?>
<div class="col-12 col-xs-6 col-sm-6 col-md-6 col-lg-4 col-xl-3 col-xxl-2" style="float:left;margin-bottom:1rem">
<article class="card border-<?php echo $value['class']; ?>" id="facility-<?php echo $value['no']; ?>">
  <?php if ($value['display']): ?>
  <input type="hidden" name="order_id" value="<?php echo $value['rent']['order_id']; ?>">
  <?php endif; ?>
  <h3 class="card-header bg-<?php echo $value['class']; ?>">
    <a href="/rents?facility_id=<?php echo $data['category']['content']['id']; ?>&amp;no=<?php echo $value['no']; ?>#facility-<?php echo $value['no']; ?>" title="락커보기">
    <span class="badge badge-light no"><?php echo $value['no']; ?></span> <span class="text-white"></span>
    </a>
    <?php echo $value['facility_check']; ?>
  </h3>
  <div class="card-body">
    <p style="min-height:3rem;margin-bottom:0" class="text-dark">
        <?php
          if ($value['display']):
            if (empty($value['breakdown'])) {
                echo $value['rent']['user_name'].'<br>';
                echo get_dt_format($value['end_date'], $search_data['timezone']).'<br>';

                if ($value['rent']['price']) {
                    echo number_format($value['rent']['price'])._('Currency');
                } else {
                    echo _('Free');
                }
                echo '<br>';
                echo _('User FC').' : '.$value['rent']['fc_name'];

                if ($value['reservation_list']['total']) {
                    echo '<br>';
                    if ($value['reservation_list']['total'] > 1) {
                        echo '<span class="text-info">'.sprintf(_('Exist %d Reservation'), $value['reservation_list']['total']).'</span>';
                    } else {
                        echo '<span class="text-info">'._('Exist Reservation').'</span>';
                    }
                }
            } else {
                echo '<br>';
                echo $rent_list['breakdown_description'].'<br>';
                echo '<br>';
            } else:
              if ($value['reservation_list']['total']) {
                  echo '<br>';
                  if ($value['reservation_list']['total'] > 1) {
                      echo '<span class="text-info">'.sprintf(_('Exist %d Reservation'), $value['reservation_list']['total']).'</span>';
                  } else {
                      echo '<span class="text-info">'._('Exist Reservation').'</span>';
                  }
              }
            endif;
        ?>
    </p>
  </div>
</article>
</div>
<?php endforeach; ?>




    </div>
</div>
</div>
</div>

<?php if ($data['category']['total'] >= 10): ?>
<div id="go_top" style="position:fixed;bottom:20px;right:20px;cursor:pointer">
  <i class="material-icons">vertical_align_top</i><span>
  <?php echo _('Go Top'); ?>
</div>
<?php endif; ?>
<?php else: ?>
<?php include __DIR__.DIRECTORY_SEPARATOR.'no_category.php'; ?>
<?php endif; ?>

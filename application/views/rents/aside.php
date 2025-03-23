<?php
  $class = 'secondary';
  $aside_rent_status = false;

  if (!empty($data['no'])) {
      if (count($rent_status_list)) {
          foreach ($rent_status_list as $rent_status) {
              if ($rent_status['no'] == $data['no']) {
                  $aside_rent_status = $rent_status;
              }
          }
      }
  }

  if (!empty($aside_rent_status)) {
      $class = str_replace(' text-white', '', $aside_rent_status['class']);
      $rent_content = $data['content'];
  }

?>
<aside class="col-12 col-xxl-3 left-form">
  <article class="row">
    <div class="col-12" id="layer_facility_info">
      <div class="card border-<?php echo $class; ?>">
        <h3 class="card-header text-white bg-<?php echo $class; ?>">
        <?php if (empty($rent_content)): ?>
          <a href="">
          <?php else: ?>
        <a href="/rents?facility_id=<?php echo $rent_content['facility_id']; ?>&amp;no=<?php echo $rent_content['no']; ?>" style="display:block">
        <?php endif; ?>
          <span class="badge badge-light no"><?php if (isset($data['no'])): ?><?php echo $data['no']; ?><?php else: ?><?php echo _('No Select'); ?><?php endif; ?></span>
          <?php if (isset($data['no'])): ?>
          <?php if ($this->input->get('id')): ?>
          <?php if ($this->input->get('id') == $rent_content['id']): ?>
          <i class="material-icons">done_all</i>
          <?php endif; ?>
          <?php else :?>
          <i class="material-icons">done_all</i>
          <?php endif; ?>
          <?php endif; ?>     
          </a>
        </h3>
        <div class="card-body">
          <?php

          switch ($class):
            case 'secondary':
              echo '<p class="text-center">'._('Status Await').'</p>';
              echo '<br />';
              break;
            case 'danger':
            echo _('Description').' : ';
            if (empty($facilityObjs[$data['no']]['breakdown_description'])):
              echo _('Not Insert');
            else:
              echo $facilityObjs[$data['no']]['breakdown_description'];
            endif;
            break;

            default:
          ?>
          <div class="row form-group">
              <label class="col-sm-4 col-form-label" for="left_member_name"><?php echo _('User Name'); ?></label>
              <div class="col-sm-8">
                <p id="left_name">
                  <?php if (isset($rent_content['user_name'])): ?>
                  <?php echo anchor('/home/rents/'.$rent_content['user_id'], $rent_content['user_name']); ?>
                  <?php else: ?>
                  &nbsp;
                  <?php endif; ?>
                </p>
              </div>
          </div>
          <div class="row form-group">
            <label class="col-sm-4 col-form-label" for="left_fc"><?php echo _('User FC'); ?></label>
            <div class="col-sm-8">
                <p id="left_fc">
                  <?php if (empty($rent_content['fc_name'])): ?>
                    <?php echo _('Not Inserted'); ?>    
                    <?php else: ?>
                      <?php echo $rent_content['fc_name']; ?>
                  <?php endif; ?>
                </p>
              </div>
          </div>
          <?php if (!empty($common_data['branch']['use_access_card'])): ?>
          <div class="row form-group">
              <label class="col-sm-4 col-form-label" for="left_member_card_no"><?php echo _('Access Card No'); ?></label>
              <div class="col-sm-8">
                <p id="left_card_no">
                  <?php if (empty($rent_content['card_no'])): ?>
                    <?php echo _('Not Inserted'); ?>    
                    <?php else: ?>
                      <?php echo $rent_content['card_no']; ?>
                  <?php endif; ?>
                </p>
              </div>
          </div>
          <?php endif; ?>
          <div class="row form-group">
              <label class="col-sm-4 col-form-label" for="left_member_phone"><?php echo _('Phone'); ?></label>
              <div class="col-sm-8">
                <p id="left_phone">
                  <?php echo get_hyphen_phone($rent_content['phone']); ?>
                </p>
              </div>
          </div>
          <div class="row form-group">
              <label class="col-sm-4 col-form-label" for="left_rent_time"><?php echo _('Transaction Date'); ?></label>
              <div class="col-sm-8">
                <p id="left_rent_time">
                  <?php if (isset($rent_content['transaction_date'])): ?>
                  <?php echo get_dt_format($rent_content['transaction_date'], $search_data['timezone']); ?>
                  <?php else: ?>
                  &nbsp;
                  <?php endif; ?>
                </p>
              </div>
          </div>
          <div class="row form-group">
              <label class="col-sm-4 col-form-label" for="left_rent_month"><?php echo _('Period'); ?></label>
              <div class="col-sm-8">
                <p id="left_rent_month">
                  <?php

                    if (isset($rent_content['id'])):
                      echo $rent_content['insert_quantity']._('Period Month');
                    else:
                      echo '&nbsp;';
                    endif;
                  ?>
                </p>
              </div>
          </div>
          <div class="row form-group">
              <label class="col-sm-4 col-form-label" for="left_rent_start"><?php echo _('Start Date'); ?></label>
              <div class="col-sm-8">
                <p id="left_rent_start">
                  <?php if (isset($rent_content['start_date'])): ?>
                    <?php echo get_dt_format($rent_content['start_date'], $search_data['timezone']); ?>
                  <?php else: ?>
                  &nbsp;                  
                  <?php endif; ?>
                </p>
              </div>
          </div>
          <div class="row form-group">
              <label class="col-sm-4 col-form-label" for="left_rent_end"><?php echo _('End Date'); ?></label>
              <div class="col-sm-8">
                <p id="left_rent_end">
                  <?php if (isset($rent_content['end_date'])): ?>
                  <?php
                        $today = new DateTime($search_data['today'], $search_data['timezone']);
                        $rend_end = new DateTime($rent_content['end_date'], $search_data['timezone']);
                        $diff = $today->diff($rend_end);
                        if ($diff->format('%R') == '+') {
                            $end_class = 'text-success';
                        } else {
                            $end_class = 'text-danger';
                        }
                        echo '<span class="'.$end_class.'">'.$rend_end->format('Y'._('Year').' n'._('Month').' j'._('Day')).'</span>';

                  ?>
                  <?php else: ?>
                  &nbsp;                  
                  <?php endif; ?>
                </p>
              </div>
          </div>
          <div class="row form-group">
              <label class="col-sm-4 col-form-label" for="left_price"><?php echo _('Sell Price'); ?></label>
              <div class="col-sm-8">
                <p id="left_price">
                  <?php if (isset($rent_content['price'])): ?>
                  <?php if ($rent_content['price']): ?>
                  <?php echo number_format($rent_content['price'])._('Currency'); ?>
                  <?php else: ?>
                  <?php echo _('Free'); ?>
                  <?php endif; ?>
                  <?php else: ?>
                  &nbsp;                  
                  <?php endif; ?>
                </p>
              </div>
          </div>
          <div class="row form-group">
              <label class="col-sm-4 col-form-label"><?php echo _('Payment'); ?></label>
              <div class="col-sm-8">
                <p id="left_payment">
                  <?php if (isset($rent_content['payment'])): ?><?php echo number_format($rent_content['payment']); ?><?php echo _('Currency'); ?><?php endif; ?>
                </p>
              </div>
          </div>
          <?php if (isset($rent_content)): ?>
          <?php if (empty($rent_content['content_id'])): ?>
          <?php if ($this->Acl->has_permission('rents', 'write')): ?>    
          <div class="row form-group">
              <label class="col-sm-4 col-form-label" for="left_memo"><?php echo _('Memo'); ?></label>
              <div class="col-sm-8">
                  <?php echo anchor('rent-contents/add?order_id='.$rent_content['order_id'], _('Add Content'), array('class' => 'btn btn-primary btn-modal')); ?>
              </div>
            </div>
          <?php endif; ?>
          <?php else: ?>
          <div class="row form-group">
              <label class="col-sm-4 col-form-label" for="left_memo"><?php echo _('Memo'); ?></label>
              <div class="col-sm-8">          
          <?php echo anchor('rent-contents/view/'.$rent_content['content_id'], _('Show Content'), array('class' => 'btn btn-secondary btn-modal')); ?>
          </div>
          </div>
          <?php endif; ?>
          <?php endif; ?>

          <?php endswitch; ?>
        </div>
      </div>

      <?php if (!empty($aside_rent_status['reservation_list']['total'])): ?>
      <?php foreach ($aside_rent_status['reservation_list']['list'] as $reservation_rent): ?>
      <div class="card border-info">
        <h3 class="card-header text-white bg-info">
          <a href="/rents?facility_id=<?php echo $reservation_rent['facility_id']; ?>&amp;no=<?php echo $reservation_rent['no']; ?>&amp;id=<?php echo $reservation_rent['id']; ?>" style="display:block">
          <?php if (isset($data['category']['content'])): ?>
          <span class="badge badge-light no"><?php if (isset($data['no'])): ?><?php echo $data['no']; ?><?php else: ?><?php echo _('Not'); ?><?php endif; ?></span>
          <?php if (!empty($data['content'])): ?>
          <?php if ($data['content']['id'] == $reservation_rent['id']): ?>
          <i class="material-icons">done_all</i>
          <?php endif; ?>
          <?php endif; ?>
          <?php endif; ?>
          </a>
        </h3>
        <div class="card-body">
        <div class="row form-group">
              <label class="col-sm-4 col-form-label" for="left_member_name"><?php echo _('User Name'); ?></label>
              <div class="col-sm-8">
                <p id="left_name">
                  <?php if (isset($reservation_rent['user_name'])): ?>
                  <?php echo anchor('/home/rents/'.$reservation_rent['user_id'], $reservation_rent['user_name']); ?>
                  <?php else: ?>
                  &nbsp;
                  <?php endif; ?>
                </p>
              </div>
          </div>
          <div class="row form-group">
              <label class="col-sm-4 col-form-label" for="left_rent_start"><?php echo _('Start Date'); ?></label>
              <div class="col-sm-8">
                <p id="left_rent_start">
                  <?php if (isset($reservation_rent['start_date'])): ?>
                  <?php echo get_dt_format($reservation_rent['start_date'], $search_data['timezone']); ?>
                  <?php else: ?>
                  &nbsp;                  
                  <?php endif; ?>
                </p>
              </div>
          </div>
          <div class="row form-group">
              <label class="col-sm-4 col-form-label" for="left_rent_end"><?php  echo _('End Date'); ?></label>
              <div class="col-sm-8">
                <p id="left_rent_end">
                  <?php if (isset($reservation_rent['end_date'])): ?>
                  <?php
                        $today = new DateTime($search_data['today'], $search_data['timezone']);

                        $rend_end = new DateTime($reservation_rent['end_date'], $search_data['timezone']);
                        $diff = $today->diff($rend_end);
                        if ($diff->format('%R') == '+') {
                            $end_class = 'text-success';
                        } else {
                            $end_class = 'text-danger';
                        }
                        echo '<span class="'.$end_class.'">'.$rend_end->format('Y'._('Year').' n'._('Month').' j'._('Day')).'</span>';
                  ?>
                  <?php else: ?>
                  &nbsp;                  
                  <?php endif; ?>
                </p>
              </div>
          </div>
          <div class="row form-group">
              <label class="col-sm-4 col-form-label" for="left_rent_month"><?php echo _('Period'); ?></label>
              <div class="col-sm-8">
                <p id="left_rent_month">
                <?php

if (isset($rent_content['id'])):
      echo $rent_content['insert_quantity']._('Period Month');
else:
  echo '&nbsp;';
endif;
?>
                </p>
              </div>
          </div>

        </div>
      </div>
    <?php endforeach; ?>
    <?php endif; ?>
    </div>
  </article>
</aside>

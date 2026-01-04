<?php

  $params = '';
  if ($this->input->get()) {
      $p_index = 0;
      foreach ($this->input->get() as $key => $param) {
          if ($key == 'page') {
              continue;
          }

          if ($key == 'employee_id' or $key == 'employee_name') {
              if (empty($data['content'])) {
                  continue;
              }
          }

          if ($p_index) {
              $params .= '&'.$key.'='.$param;
          } else {
              $params .= '?'.$key.'='.$param;
          }
          ++$p_index;
      }
  }

  if (isset($data['content'])) {
      if (empty($params)) {
          $params = '?employee_id='.$data['content']['id'].'&amp;employee_name='.$data['content']['name'];
      } else {
          $params .= '&amp;employee_id='.$data['content']['id'].'&amp;employee_name='.$data['content']['name'];
      }
  }

?>
<div id="accounts" class="container">
  <div class="row">
  <div class="col-12">
    <nav aria-label="breadcrumb">
  <ol class="breadcrumb">
    <li class="breadcrumb-item"><?php echo anchor('/', _('Home')); ?></li>
    <li class="breadcrumb-item"><?php echo anchor('/trainer-actives', _('Trainer Active Index')); ?></li>    
    <li class="breadcrumb-item active" aria-current="page"><strong><?php echo _('Trainer Active View'); ?></strong></li>
  </ol>
</nav>
    </div>
    </div>
    <div class="row">
    <div class="col-12">
    <?php include __DIR__.DIRECTORY_SEPARATOR.'search_form.php'; ?>
    </div>
    </div>
    <div class="row">
    <div class="col-12 col-lg-6">
    <p class="summary">
          <span id="list_count" style="display:none"><?php echo $data['total']; ?></span>
          <?php echo sprintf(_('There Are %d Enroll'), $data['total']); ?>
        </p>
    </div>
    <div class="col-12 col-lg-6 text-right">
        <?php echo anchor('trainer-actives/export-excel/'.$data['content']['id'] . $params, _('Export Excel'), array('class' => 'btn btn-secondary')); ?>
    </div>
  </div>
    <div class="row">
    <div class="col-12">
      <table id="account_list" class="table table-striped">
        <colgroup>
          <col>
          <col>
          <col>
          <col>
          <col>
          <col>
          <col>
          <col>
          <col>
          <col>          
        </colgroup>
        <thead class="thead-default">
          <tr>
            <th><?php echo _('User'); ?>
            <?php if (!empty($common_data['branch']['use_access_card'])): ?>              
            (<?php echo _('Access Card No'); ?>)
            <?php endif; ?>
            </th>
            <th><?php echo _('User FC'); ?></th>
            <th><?php echo _('Registration Details'); ?></th>
            <th class="text-center"><?php echo _('Period'); ?></th>
            <th class="text-right"><?php echo _('Payment'); ?></th>
            <th class="text-right"><?php echo _('Execute Count'); ?></th>
            <th class="text-right"><?php echo _('Commission Per Once'); ?></th>
            <th class="text-right"><?php echo _('Total Commission'); ?></th>
            </th>
          </tr>
        </thead>
        <tbody>
          <?php if (empty($data['total'])): ?>
              <tr>
                <td colspan="8"><?php echo _('No Data'); ?></td>
              </tr>
              <?php else: ?>              
              <?php foreach ($data['list'] as $index => $value): ?>
              <tr>
                <td>
                  <?php if (empty($value['user_id'])): ?>
                  <?php echo _('Deleted User'); ?>
                  <?php else: ?>
                  <?php echo anchor('/view/'.$value['user_id'], $value['name'].'('.get_card_no($value['card_no'], false).')', array('target' => '_blank')); ?>
                  <?php endif; ?>
                </td>
                <td><?php if (!empty($value['fc_name'])): ?><?php echo anchor('/employees/view/'.$value['fc_id'], $value['fc_name'], array('target' => '_blank')); ?><?php else: ?>-<?php endif; ?></td>                
                <td>
                  <?php
                  $product_name = '';
                  if (empty($value['product_name'])) {
                      $product_name .= $value['product_name'];
                  } else {
                      if (!empty($value['product_category'])) {
                          $product_name .= $value['product_category'].' / ';
                      }
                      $product_name .= $value['product_name'];
                  }

                  if (empty($value['user_id'])) {
                      echo $product_name;
                  } else {
                      echo anchor('/home/enrolls/'.$value['user_id'], $product_name, array('target' => '_blank'));
                  }

                  ?>
                </td>
                <td class="text-center">
                  <?php if (!empty($value['start_date']) and !empty($value['end_date'])): ?>
                    <?php if (date('Y', strtotime($value['end_date'])) > 2500): ?>
                      <?php echo _('Unlimit'); ?>
                    <?php else: ?>
                  <?php echo $value['start_date']; ?> ~ <?php echo $value['end_date']; ?>
                  <?php endif; ?>
                  <?php else: ?>
                  -
                  <?php endif; ?>
                </td>
                <td class="text-right">
                  <?php echo number_format($value['purchase']); ?><?php echo _('Currency'); ?></td>
                <td class="text-right"><?php

                if (empty($params)) {
                    $p_params = '?user_id='.$value['user_id'].'&amp;enroll_id='.$value['enroll_id'];
                } else {
                    $p_params = $params.'&amp;user_id='.$value['user_id'].'&amp;enroll_id='.$value['enroll_id'];
                }

                echo anchor('enroll_use_logs'.$p_params, $value['execute_count']._('Count Time')); ?></td>
                <td class="text-right">
                  <?php if(empty($value['default_commission'])): ?>
                    <?php if(empty($value['price'])): ?>
                    0<?php echo _('Currency'); ?>
                    <?php else: ?>
                    <?php echo number_format(($value['price']/$value['insert_quantity']) * ($value['commission_rate'] / 100)) ?><?php echo _('Currency'); ?>
                    <?php endif ?>
                  <?php else: ?>
                    <?php echo number_format($value['default_commission']) ?><?php echo _('Currency'); ?>
                  <?php endif ?>                                                              
                </td>
                <td class="text-right"><?php echo number_format($value['commission']); ?><?php echo _('Currency'); ?></td>
              </tr>
              <?php endforeach; ?>
              <?php endif; ?>
            </tbody>
          </table>
          <?php echo $this->pagination->create_links(); ?>
          
      <article class="card total_amount">
        <h3 class="card-header"><?php echo _('Total Amount'); ?></h3>
        <div class="card-body">
          <ul class="row">
            <li class="col-12 col-sm-6 col-lg-3">
            <?php echo _('Period Use Quantity'); ?> : <span><?php echo number_format($data['employee_total']['total_period_use']); ?></span>
            </li>
            <li class="col-12 col-sm-6 col-lg-3">
            <?php echo _('Commission'); ?> : <span><?php echo number_format($data['employee_total']['total_commission']); ?><?php echo _('Currency'); ?></span>
            </li>
          </ul>
        </div>
      </article>
    </div>
  </div>
</div>

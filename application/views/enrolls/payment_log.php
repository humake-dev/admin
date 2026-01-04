<article class="card">
  <div class="card-header">
    <h3><?php echo _('Enroll'); ?><?php if (isset($data['enroll']['content'])): ?>(<span id="enroll_log_title"><?php echo $data['enroll']['content']['product_category_name']; ?> / <?php echo $data['enroll']['content']['product_name']; ?></span>)<?php endif; ?> 상세내역</h3>
    <div class="float-right buttons">
      <i class="material-icons">keyboard_arrow_up</i>
    </div>
  </div>
  <div class="card-body">
    <div class="row">
      <div class="col-12">
        <ul class="float-right sl-bnt-group">
          <?php if ($data['log']['total']): ?>
          <li><?php echo anchor('/accounts/export_enroll_account/'.$data['enroll']['content']['order_id'], _('Export Excel'), array('id' => 'export_enroll_account', 'class' => 'btn btn-secondary')); ?></li>
          <?php else: ?>
          <li><?php echo anchor('#', _('Export Excel'), array('id' => 'export_enroll_account', 'class' => 'btn btn-secondary disabled')); ?></li>
          <?php endif; ?>
        </ul>
      </div>
      <div class="col-12">
        <table id="user_enroll_log_list" class="table table-bordered table-striped">
          <colgroup>
            <col />
            <col />
            <col />
            <col />
            <col />
            <col />            
            <col />             
            <col />
          </colgroup>
          <thead class="thead-default">
            <tr>
              <th rowspan="2" class="text-center"><?php echo _('Transaction Date'); ?></th>
              <th rowspan="2" class="text-center"><?php echo _('Content'); ?></th>
              <th colspan="2" class="text-center"><?php echo _('Price'); ?></th>
              <th rowspan="2" class="text-center"><?php echo _('Payment'); ?></th>
              <th colspan="2" class="text-center"><?php echo _('Payment Method'); ?></th>
            </tr>
            <tr>
              <th class="text-center"><?php echo _('Fee'); ?></th>
              <th class="text-center"><?php echo _('Discount'); ?></th>   
              <th class="text-center"><?php echo _('Cash'); ?></th>
              <th class="text-center"><?php echo _('Credit'); ?></th>
            </tr>
          </thead>
          <tbody>
            <?php if ($data['log']['total']): ?>
            <?php foreach ($data['log']['list'] as $log): ?>
            <tr>
              <td class="text-center"><?php echo get_dt_format($log['transaction_date'], $search_data['timezone']); ?></td>
              <td class="text-center"><?php echo str_replace('수강', '', $log['category_name']); ?></td>
              <td class="text-right"><?php if ($log['account_category_id'] == 1): ?><?php echo number_format($log['original_price']); ?><?php echo _('Currency'); ?><?php else: ?>- <?php endif; ?></td>
              <td class="text-right"><?php if ($log['account_category_id'] == 1): ?><?php echo number_format($log['original_price'] * $log['dc_rate'] / 100 + $log['dc_price']); ?><?php echo _('Currency'); ?><?php else: ?>- <?php endif; ?></td>
              <td class="text-right sl-td-active">
              <?php

                if ($log['type'] == 'O' and ($log['cash'] + $log['credit']) != 0) {
                    echo '<span class="text-danger"> -';
                    echo number_format($log['cash'] + $log['credit'])._('Currency');
                    echo '</span>';
                } else {
                    echo number_format($log['cash'] + $log['credit'])._('Currency');
                }
              ?>
              </td>
                  <td class="text-right">
                  <?php
                    if ($log['type'] == 'O' and $log['cash'] != 0) {
                        echo '<span class="text-danger"> -';
                        echo number_format($log['cash'])._('Currency');
                        echo '</span>';
                    } else {
                        echo number_format($log['cash'])._('Currency');
                    }
                  ?>
                  </td>
                  <td class="text-right">
                  <?php
                    if ($log['type'] == 'O' and $log['credit'] != 0) {
                        echo '<span class="text-danger"> -';
                        echo number_format($log['credit'])._('Currency');
                        echo '</span>';
                    } else {
                        echo number_format($log['credit'])._('Currency');
                    }
                  ?>                
                  </td>
                  </tr>
                  <?php endforeach; ?>
                  <?php else: ?>
                  <tr>
                  <td colspan="9"><?php echo _('No Data'); ?></td>
                  </tr>
                  <?php endif; ?>
                </tbody>
              </table>

                </div>
              </div>
            </div>
</article>
        


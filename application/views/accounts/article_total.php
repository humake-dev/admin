<article class="col-12">
  <h3 class="float-left"><?php echo _('Total'); ?></h3>
  <div class="float-right">
    <?php echo anchor('/accounts/export-excel', _('Export Excel'), array('class' => 'btn btn-secondary')); ?>
  </div>
  <table class="table table-bordered">
    <colgroup>
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
        <th><?php echo _('Category'); ?> / <?php echo _('Title'); ?></th>
        <th class="text-right"><?php echo _('Request Pay Count'); ?></th>             
        <th class="text-right"><?php echo _('Account Count'); ?></th>        
        <th class="text-right"><?php echo _('Cash'); ?></th>
        <th class="text-right"><?php echo _('Credit'); ?></th>
        <th class="text-right"><?php echo _('Total'); ?></th>
      </tr>
    </thead>
    <tbody>
      <?php if (empty($data['total'])): ?>
      <tr>
        <td colspan="5"><?php echo _('No Data'); ?></td>
      </tr>
      <?php else: ?>
      <?php foreach ($data['list'] as $index => $account):
              if (empty($account['product_name'])) {
                  continue;
              }
        $total_new[$index] = $account['new_order'];
        $total_re[$index] = $account['re_order'];
        $total_delete[$index] = $account['delete_enroll'] + $account['delete_rent'] + $account['delete_point'] + $account['delete_order'] + $account['delete_other'];
      ?>
      <tr>
        <td>
        <?php
                if (empty($account['product_name'])) {
                    echo display_deleted_product($account['type']);
                } else {
                    $account_link_param = '?in_out=all';

                    if ($this->input->get('start_date')) {
                        $account_link_param .= '&amp;start_date='.$this->input->get('start_date');
                    }

                    if ($this->input->get('end_date')) {
                        $account_link_param .= '&amp;end_date='.$this->input->get('end_date');
                    }

                    if ($this->input->get('date_p')) {
                        $account_link_param .= '&amp;date_p='.$this->input->get('date_p');
                    }

                    if ($account['type'] == 'other') {
                        echo anchor('/accounts/view-other'.$account_link_param, '기타매출('.$account['product_name'].' '._('Etc').')', array('title' => $account['product_name'].' '._('View Detail')));
                    } else {
                        if ($account['type'] == 'course' or $account['type'] == 'product') {
                            echo anchor('/accounts/view/'.$account['category_id'].$account_link_param, $account['product_category'].' / '.$account['product_name'], array('title' => $account['product_category'].' / '.$account['product_name'].' '._('View Detail')));
                        } else {
                            echo anchor('/accounts/view/'.$account['category_id'].$account_link_param, $account['product_name'], array('title' => $account['product_name'].' '._('View Detail')));
                        }
                    }
                }
              ?>
            </td>
            <td class="text-right"><?php $total_request_counter[$index] = $account['request_counter']; echo number_format($account['request_counter']); ?></td>
            <td class="text-right"><?php $total_account_counter[$index] = $account['account_counter'];

if (empty($account['product_name'])) {
    echo $account['account_counter'];
} else {
    if ($account['type'] == 'other') {
        echo anchor('/accounts/view-other'.$account_link_param, number_format($account['account_counter']), array('title' => $account['product_name'].' '._('View Detail')));
    } else {
        if ($account['type'] == 'course' or $account['type'] == 'product') {
            echo anchor('/accounts/view/'.$account['category_id'].$account_link_param, number_format($account['account_counter']), array('title' => $account['product_category'].' / '.$account['product_name'].' '._('View Detail')));
        } else {
            echo anchor('/accounts/view/'.$account['category_id'].$account_link_param, number_format($account['account_counter']), array('title' => $account['product_name'].' '._('View Detail')));
        }
    }
}
              ?>           
            </td>
            <td class="text-right"><?php $total_cash[$index] = $account['i_cash'] - $account['o_cash']; echo number_format($account['i_cash'] - $account['o_cash']); ?></td>
            <td class="text-right"><?php $total_credit[$index] = $account['i_credit'] - $account['o_credit']; echo number_format($account['i_credit'] - $account['o_credit']); ?></td>
            <td class="text-right"><?php $total_total[$index] = $account['i_cash'] - $account['o_cash'] + $account['i_credit'] - $account['o_credit']; echo number_format($account['i_cash'] - $account['o_cash'] + $account['i_credit'] - $account['o_credit']); ?></td>
          </tr>
          <?php endforeach; ?>
          <?php endif; ?>
        </tbody>

        <tfoot>
          <?php if ($data['total']): ?>
          <tr>
            <th><?php echo _('Total'); ?></th>
            <th class="text-right"><?php echo number_format(array_sum($total_request_counter)); ?></th>            
            <th class="text-right"><?php echo number_format(array_sum($total_account_counter)); ?></th>            
            <th class="text-right"><?php echo number_format(array_sum($total_cash)); ?></th>
            <th class="text-right"><?php echo number_format(array_sum($total_credit)); ?></th>
            <th class="text-right"><?php echo number_format(array_sum($total_total)); ?></th>
          </tr>
          <?php endif; ?>
        </tfoot>        
    </table>
</article>
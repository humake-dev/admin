<article class="col-12">
    <h3><?php echo _('Refund'); ?></h3>
      <table class="table table-bordered">
        <colgroup>
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
            <th class="text-right"><?php echo _('Account Count'); ?></th>      
            <th class="text-right"><?php echo _('Cash'); ?></th>
            <th class="text-right"><?php echo _('Credit'); ?></th>
            <th class="text-right"><?php echo _('Total'); ?></th>
          </tr>
        </thead>
        <tbody>
          <?php if ($data['total']): $a = 0; ?>
          <?php foreach ($data['list'] as $index => $account):

            if (empty($account['o_cash']) and empty($account['o_credit'])) {
                continue;
            }

            ++$a;

            $total_delete[$index] = $account['delete_enroll'] + $account['delete_rent'] + $account['delete_point'] + $account['delete_order'] + $account['delete_other'];
          ?>
          <tr>
            <td>
              <?php
                if (empty($account['product_name'])) {
                    echo display_deleted_product($account['type'], 'out');
                } else {
                    $account_link_param = '?in_out=out';

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
            <td class="text-right"><?php $total_request_counter[$index] = $account['refund_counter'];

                if (empty($account['product_name'])) {
                    echo display_deleted_product($account['type'], 'out');
                } else {
                    if ($account['type'] == 'other') {
                        echo anchor('/accounts/view-other'.$account_link_param, number_format($account['refund_counter']), array('title' => $account['product_name'].' '._('View Detail')));
                    } else {
                        if ($account['type'] == 'course' or $account['type'] == 'product') {
                            echo anchor('/accounts/view/'.$account['category_id'].$account_link_param, number_format($account['refund_counter']), array('title' => $account['product_category'].' / '.$account['product_name'].' '._('View Detail')));
                        } else {
                            echo anchor('/accounts/view/'.$account['category_id'].$account_link_param, number_format($account['refund_counter']), array('title' => $account['product_name'].' '._('View Detail')));
                        }
                    }
                }

              ?>
            </td>
            <td class="text-right"><?php $total_cash[$index] = $account['o_cash']; echo number_format($account['o_cash']); ?></td>
            <td class="text-right"><?php $total_credit[$index] = $account['o_credit']; echo number_format($account['o_credit']); ?></td>
            <td class="text-right"><?php $total_total[$index] = $account['o_cash'] + $account['o_credit']; echo number_format($account['o_cash'] + $account['o_credit']); ?></td>
          </tr>
          <?php endforeach; ?>
          <?php if (empty($a)): ?>
          <tr>
            <td colspan="6"><?php echo _('No Data'); ?></td>
          </tr>
          <?php endif; ?>
          <?php else: ?>
          <tr>
            <td colspan="6"><?php echo _('No Data'); ?></td>
          </tr>
          <?php endif; ?>
        </tbody>
    </table>
</article>
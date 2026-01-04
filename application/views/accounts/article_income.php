<article class="col-12">
    <h3><?php echo _('Income'); ?></h3>
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
          <?php if (empty($data['total'])): ?>
          <tr>
            <td colspan="6"><?php echo _('No Data'); ?></td>
          </tr>
          <?php else: ?>
          <?php foreach ($data['list'] as $index => $account):
        if (empty($account['product_name'])) {
            continue;
        }
            $total_delete[$index] = $account['delete_enroll'] + $account['delete_rent'] + $account['delete_point'] + $account['delete_order'] + $account['delete_other'];
          ?>
          <tr>
            <td>
              <?php
                if (empty($account['product_name'])) {
                    echo display_deleted_product($account['type']);
                } else {
                    $account_link_param = '?in_out=in';

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
            <td class="text-right"><?php $total_counter[$index] = $account['account_counter'] - $account['refund_counter'];

            if (empty($account['product_name'])) {
                echo number_format($account['account_counter'] - $account['refund_counter']);
            } else {
                if ($account['type'] == 'other') {
                    echo anchor('/accounts/view-other'.$account_link_param, $account['account_counter'], array('title' => $account['product_name'].' '._('View Detail')));
                } else {
                    if ($account['type'] == 'course' or $account['type'] == 'product') {
                        echo anchor('/accounts/view/'.$account['category_id'].$account_link_param, number_format($account['account_counter'] - $account['refund_counter']), array('title' => $account['product_category'].' / '.$account['product_name'].' '._('View Detail')));
                    } else {
                        echo anchor('/accounts/view/'.$account['category_id'].$account_link_param, number_format($account['account_counter'] - $account['refund_counter']), array('title' => $account['product_name'].' '._('View Detail')));
                    }
                }
            }
            ?>
            </td>
            <td class="text-right"><?php $total_cash[$index] = $account['i_cash']; echo number_format($account['i_cash']); ?></td>
            <td class="text-right"><?php $total_credit[$index] = $account['i_credit']; echo number_format($account['i_credit']); ?></td>
            <td class="text-right"><?php $total_total[$index] = $account['i_cash'] + $account['i_credit']; echo number_format($account['i_cash'] + $account['i_credit']); ?></td>
          </tr>
          <?php endforeach; ?>
          <?php endif; ?>
        </tbody>
    </table>
</article>
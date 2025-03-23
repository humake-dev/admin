<article class="col-12">
    <div class="row">
        <h3 class="col-12 col-lg-6"><?php echo _('PT Info'); ?></h3>
        <div class="col-12 col-lg-6 text-right">
            <?php if ($this->session->userdata('branch_id')): ?>
                <?php if ($this->Acl->has_permission('enrolls', 'write')): ?>
                    <?php echo anchor('enrolls/add?user_id=' . $data['content']['id'], '<i class="material-icons">add</i>', array('class' => 'more2')); ?>
                <?php endif; ?>
            <?php endif; ?>
            <a href="/home/enrolls/<?php echo $data['content']['id']; ?><?php echo $params; ?>"
               title="<?php echo _('More Enroll'); ?>" class="more"><i class="material-icons">redo</i></a>
        </div>
    </div>
        <div class="table-responsive">
        <table class="table table-hover">
        <thead>
        <tr class="thead-default">
            <th><?php echo _('Transaction Date'); ?></th>
            <th><?php echo _('Status'); ?></th>
            <th><?php echo _('Lesson'); ?></th>
            <th><?php echo _('Enroll Trainer'); ?></th>
            <th><?php echo _('Total Quantity'); ?></th>
            <th><?php echo _('Use Quantity'); ?></th>
            <th><?php echo _('Remain Count'); ?></th>
            <th><?php echo _('Per Commission'); ?></th>
            <th><?php echo _('Sell Price'); ?></th>
            <th><?php echo _('Payment'); ?></th>
        </tr>
        </thead>
        <tbody>
        <?php if (empty($other_data['pt']['total'])): ?>
            <tr>
                <td class="text-center" colspan="10"><?php echo _('Not Inserted Enroll'); ?></td>
            </tr>
            </div>
        <?php else: ?>
            <?php foreach ($other_data['pt']['list'] as $index => $value):

                if ($value['stopped']) {
                    $type = '<span class="text-warning">' . _('Stopped') . '</span>';
                } else {
                    $type = '<span class="text-success">' . _('Using') . '</span>';
                }
                ?>
                <tr>
                    <td>
                    <?php
        
        $transaction_date=$value['transaction_date'];

        if(empty($transaction_date)) {
          $transaction_date=$value['order_transaction_date'];
        }
        
        echo get_dt_format($transaction_date, $search_data['timezone']); 
        
        ?>
                    </td>
                    <td><?php echo $type; ?></td>
                    <td><?php echo $value['product_category_name']; ?> / <?php echo $value['product_name']; ?></td>
                    <td>
                        <?php if (empty($value['trainer_id'])): ?>
                            -
                        <?php else: ?>
                            <?php echo $value['trainer_name']; ?>
                        <?php endif; ?>
                    </td>
                    <td>
                        <?php echo $value['lesson_quantity'] * $value['insert_quantity']; ?><?php echo _('Count Time'); ?>
                    </td>
                    <td><?php echo $value['use_quantity']; ?><?php echo _('Count Time'); ?></td>
                    <td><?php echo $value['quantity'] - $value['use_quantity']; ?><?php echo _('Count Time'); ?></td>
                    <td>
                        <?php
                        if (empty($value['trainer_id'])) {
                            echo '-';
                        } else {
                            if (isset($value['commission'])) {
                                echo number_format($value['commission']);
                            } else {
                                echo number_format(round(($value['price'] / $value['insert_quantity']) * ($value['commission_rate'] * 0.01)));
                            }
                            echo _('Currency');
                        }
                        
                        ?>
                    </td>
                    <td><?php echo number_format($value['price']); ?><?php echo _('Currency'); ?></td>
                    <td>
                        <?php if ($value['payment'] == 'Unpaid'): ?>
                            <span class="text-danger"><?php echo number_format($value['payment']); ?><?php echo _('Currency'); ?></span>
                        <?php else: ?>
                            <span class="text-success"><?php echo number_format($value['payment']); ?><?php echo _('Currency'); ?></span>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        <?php endif ?>
        </tbody>
        </table>
        </div>
</article>
<div id="account-days" class="container">
    <div class="row">
        <?php echo $Layout->Element('accounts/nav'); ?>
        <div class="col-12">
            <?php include __DIR__ . DIRECTORY_SEPARATOR . 'search_form.php'; ?>
        </div>
        <article class="col-12">
            <div class="float-right">
                <p class="summary">
                    <span id="list_count" style="display:none"><?php echo $data['total']; ?></span>
                    <?php echo sprintf(_('There Are %d Order'), $data['total']); ?>
                </p>
            </div>
            <table id="account_list_perday" class="table table-striped">
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
                    <col>
                </colgroup>
                <thead class="thead-default">
                <tr>
                    <th><?php echo _('Category'); ?></th>
                    <th><?php echo _('User'); ?></th>
                    <th><?php echo _('Product'); ?></th>
                    <th class="text-right"><?php echo _('Amount Received'); ?></th>
                    <th class="text-right"><?php echo _('Cash'); ?></th>
                    <th class="text-right"><?php echo _('Credit'); ?></th>
                    <th class="text-right"><?php echo _('User FC'); ?></th>
                    <th class="text-right"><?php echo _('Input Minute'); ?></th>
                </tr>
                </thead>
                <tbody>
                <?php if ($data['total']): ?>
                    <?php foreach ($data['list'] as $index => $order): ?>
                        <tr>
                            <td><?php echo _($order['order_type']); ?></td>
                            <td>
                                <?php if(empty($order['user_id'])): ?>
                                    <?php echo $order['name']; ?>
                                <?php else: ?>
                                    <?php echo anchor('/view/'.$order['user_id'],$order['name'],array('target'=>'_blank')); ?>
                                <?php endif ?>                            
                            </td>
                            <td>
                                <?php if (empty($order['product_name'])): ?>
                                    <?php echo _('Deleted Product'); ?>
                                <?php else: ?>
                                    <?php if (!empty($order['product_category'])): ?>
                                        <?php echo $order['product_category']; ?> /
                                    <?php endif; ?>
                                    <?php echo $order['product_name']; ?>
                                <?php endif; ?>
                            </td>
                            <td class="text-right">
                                <?php echo number_format($order['payment']); ?><?php echo _('Currency'); ?>
                            </td>
                            <td class="text-right">
                                <?php // if ($order['type']=='O'):?>
                                <!-- <span class="text-danger"><?php echo number_format($order['cash']); ?><?php echo _('Currency'); ?></span>-->
                                <?php // else:?>
                                <?php echo number_format($order['cash']); ?><?php echo _('Currency'); ?>
                                <?php // endif?>
                            </td>
                            <td class="text-right">
                                <?php // if ($order['type'] == 'O'):?>
                                <!-- <span class="text-danger"><?php echo number_format($order['credit']); ?><?php echo _('Currency'); ?></span> -->
                                <?php // else:?>
                                <?php echo number_format($order['credit']); ?><?php echo _('Currency'); ?>
                                <?php // endif;?>
                            </td>
                            <td class="text-right">
                                <?php if (empty($order['fc_name'])): ?>
                                    -
                                <?php else: ?>
                                    <?php echo $order['fc_name']; ?>
                                <?php endif; ?>
                            </td>
                            <td class="text-right">
                                <?php echo get_dt_format($order['created_at'], $search_data['timezone'], 'H' . _('Hour') . ' i' . _('Minute')); ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="12"><?php echo _('No Data'); ?></td>
                    </tr>
                <?php endif; ?>
                </tbody>
            </table>
            <?php echo $this->pagination->create_links(); ?>
        </article>
    </div>
</div>

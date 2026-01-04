<section id="view-account-product" class="container">
    <div class="row">
        <?php echo $Layout->Element('accounts/nav'); ?>
        <div class="col-12">
            <?php echo $Layout->Element('accounts/search_period_user_form'); ?>
        </div>
        <?php include __DIR__ . DIRECTORY_SEPARATOR . 'nav.php'; ?>
        <div class="col-12">
            <div class="float-right">
                <p class="summary">
                    <?php

                    $total = 0;
                    if (!empty($list['total'])) {
                        $total = $list['total'];
                    }
                    echo sprintf(_('There Are %d User'), $total); ?>
                </p>
            </div>
        </div>
        <article class="col-12">
            <table class="table table-bordered">
                <colgroup>
                    <col/>
                    <col/>
                    <col/>
                    <col/>
                    <col/>
                    <col/>
                    <col/>
                </colgroup>
                <thead class="thead-default">
                <tr>
                    <th class="text-center"><?php echo _('Increment Number'); ?></th>
                    <th class="text-center"><?php echo _('Category'); ?></th>
                    <th class="text-center"><?php echo _('Transaction Date'); ?></th>
                    <th class="text-center"><?php echo _('User Name'); ?></th>
                    <th class="text-center"><?php echo _('Product Name'); ?></th>
                    <th class="text-center"><?php echo _('Quantity'); ?></th>
                    <th class="text-center"><?php echo _('Sell Price'); ?></th>
                </tr>
                </thead>
                <tbody>
                <?php if (empty($list['total'])): ?>
                    <tr>
                        <td colspan="8"><?php echo _('No Data'); ?></td>
                    </tr>
                <?php else: ?>
                    <?php
                    foreach ($list['list'] as $index => $account):
                        $a_created_at = explode(',', $account['created_at']);
                        ?>
                        <tr>
                            <td class="text-right">
                                <?php echo number_format($list['total'] - ($data['page']) - $index); ?>
                            </td>
                            <td class="text-center"><?php echo $product_type; ?></td>
                            <td class="text-right">
                                <span><?php echo get_dt_format($account['transaction_date'], $search_data['timezone']); ?></span>
                            </td>
                            <td class="text-right">
                                <?php if (empty($account['user_id'])): ?>
                                    <?php echo _('Deleted User'); ?>
                                <?php else: ?>
                                    <?php echo anchor('/view/' . $account['user_id'], $account['name'], array('target' => 'blank')); ?>
                                <?php endif; ?>
                            </td>
                            <td class="text-right"><?php echo $account['title']; ?></td>
                            <td class="text-right"><?php echo $account['total_product']; ?></td>
                            <td class="text-right"><?php echo number_format($account['total_per_user']); ?><?php echo _('Currency'); ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
                </tbody>
            </table>
            <?php echo $this->pagination->create_links(); ?>
        </article>
    </div>
</section>

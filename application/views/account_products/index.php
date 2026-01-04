<section id="account-products" class="container">
    <div class="row">
        <?php echo $Layout->Element('accounts/nav'); ?>
        <div class="col-12">
            <?php echo $Layout->Element('accounts/search_period_user_form'); ?>
        </div>
        <?php include __DIR__ . DIRECTORY_SEPARATOR . 'nav.php'; ?>
        <?php
        $i_total = 0;
        $o_total = 0;

        if ($data['total']):
            foreach ($data['list'] as $index => $account):

                if (empty($account['category_id'])) {
                    continue;
                }

                $input = $account['i_cash'] + $account['i_credit'];
                $output = $account['o_cash'] + $account['o_credit'];

                if (!empty($input)) {
                    $i_total += $input;
                }

                if (!empty($output)) {
                    $o_total += $output;
                }

            endforeach;
        endif;

        ?>
        <article class="col-12">
            <h3><?php echo _('Income'); ?></h3>
            <table class="table table-bordered">
                <colgroup>
                    <col style="width:25%"/>
                    <col style="width:25%"/>
                    <col style="width:25%"/>
                    <col style="width:25%"/>
                </colgroup>
                <thead class="thead-default">
                <tr>
                    <th class="text-center"><?php echo _('Category'); ?></th>
                    <th class="text-center"><?php echo _('Price'); ?></th>
                    <th class="text-center"><?php echo _('Applicant'); ?></th>
                    <th class="text-center"><?php echo _('Occurrence Amount'); ?></th>
                </tr>
                </thead>
                <tbody>
                <?php if ($data['total']): ?>
                    <?php foreach ($data['list'] as $index => $account):

                        $input = 0;
                        if (empty($account['category_id'])) {
                            continue;
                        }

                        $input = $account['i_cash'] + $account['i_credit'];

                        if (empty($input)) {
                            continue;
                        }
                        ?>
                        <tr>
                            <th class="text-center">
                                <?php if (empty($account['product_category_name'])): ?>
                                    <?php echo anchor('/account-products/view/' . $account['category_id'] . '?type=in&amp;' . $param, $account['product_name']); ?>
                                <?php else: ?>
                                    <?php echo anchor('/account-products/view/' . $account['category_id'] . '?type=in&amp;' . $param, $account['product_category'] . ' / ' . $account['product_name']); ?>
                                <?php endif; ?>
                            </th>
                            <td class="text-right"><?php echo number_format($account['price']); ?><?php echo _('Currency'); ?></td>
                            <td class="text-right"><?php echo $account['request_user']; ?></td>
                            <td class="text-right"><?php echo number_format($input); ?><?php echo _('Currency'); ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
                </tbody>
                <tfoot>
                <tr>
                    <th class="text-center"><?php echo _('Total'); ?></th>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td class="text-right"><?php echo number_format($i_total); ?><?php echo _('Currency'); ?></td>
                </tr>
                </tfoot>
            </table>
        </article>
        <article class="col-12">
            <h3><?php echo _('Refund'); ?></h3>
            <table class="table table-bordered">
                <colgroup>
                    <col style="width:25%"/>
                    <col style="width:25%"/>
                    <col style="width:25%"/>
                    <col style="width:25%"/>
                </colgroup>
                <thead class="thead-default">
                <tr>
                    <th class="text-center"><?php echo _('Category'); ?></th>
                    <th class="text-center"><?php echo _('Price'); ?></th>
                    <th class="text-center"><?php echo _('Applicant'); ?></th>
                    <th class="text-center"><?php echo _('Occurrence Amount'); ?></th>
                </tr>
                </thead>
                <tbody>
                <?php if ($o_total): ?>
                    <?php foreach ($data['list'] as $index => $account):

                        $output = 0;
                        if (empty($account['category_id'])) {
                            continue;
                        }

                        $output = $account['o_cash'] + $account['o_credit'];

                        if (empty($output)) {
                            continue;
                        }
                        ?>

                        <tr>
                            <th class="text-center">
                                <?php if (empty($account['product_category_name'])): ?>
                                    <?php if ($account['refund_user']): ?>
                                        <?php echo anchor('/account-products/view/' . $account['category_id'] . '?type=out&amp;' . $param, $account['product_name']); ?>
                                    <?php else: ?>
                                        <?php echo $account['product_name']; ?>
                                    <?php endif; ?>
                                <?php else: ?>
                                    <?php if ($account['refund_user']): ?>
                                        <?php echo anchor('/account-products/view/' . $account['category_id'] . '?type=out&amp;' . $param, $account['product_category'] . ' / ' . $account['product_name']); ?>
                                    <?php else: ?>
                                        <?php echo $account['product_category'] . ' / ' . $account['product_name']; ?>
                                    <?php endif; ?>
                                <?php endif; ?>
                            </th>
                            <td class="text-right"><?php echo number_format($account['price']); ?><?php echo _('Currency'); ?></td>
                            <td class="text-right"><?php echo $account['refund_user']; ?></td>
                            <td class="text-right"><?php echo number_format($output); ?><?php echo _('Currency'); ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="4" class="text-center"><?php echo _('No Data'); ?></td>
                    </tr>
                <?php endif; ?>
                </tbody>
                <?php if ($o_total): ?>
                    <tfoot>
                    <tr>
                        <th class="text-center"><?php echo _('Total'); ?></th>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                        <td class="text-right"><?php echo number_format($o_total); ?><?php echo _('Currency'); ?></td>
                    </tr>
                    </tfoot>
                <?php endif; ?>
            </table>
        </article>
        <article class="col-12">
            <h3>월 내역</h3>
            <table class="table table-bordered">
                <colgroup>
                    <col/>
                    <col/>
                    <col/>
                    <col/>
                    <col/>
                    <col/>
                </colgroup>
                <thead class="thead-default">
                <tr>
                    <th class="text-center"><?php echo _('Category'); ?></th>
                    <th class="text-center"><?php echo _('Price'); ?></th>
                    <th class="text-center"><?php echo _('Applicant'); ?></th>
                    <th class="text-center"><?php echo _('Income'); ?></th>
                    <th class="text-center"><?php echo _('Refund'); ?></th>
                    <th class="text-center"><?php echo _('Occurrence Amount'); ?></th>
                </tr>
                </thead>
                <tbody>
                <?php if ($data['total']): ?>
                    <?php foreach ($data['list'] as $index => $account):
                        if (empty($account['category_id'])) {
                            continue;
                        }
                        $input = $account['i_cash'] + $account['i_credit'];
                        $output = $account['o_cash'] + $account['o_credit'];
                        ?>
                        <tr>
                            <th class="text-center">
                                <?php if (empty($account['product_name'])): ?>
                                    <?php echo display_deleted_product($account['type']); ?>
                                <?php else: ?>

                                    <?php if (empty($account['product_category_name'])): ?>
                                        <?php echo anchor('/account-products/view/' . $account['category_id'] . '?' . $param, $account['product_name']); ?>
                                    <?php else: ?>
                                        <?php echo anchor('/account-products/view/' . $account['category_id'] . '?' . $param, $account['product_category'] . ' / ' . $account['product_name']); ?>
                                    <?php endif; ?>
                                <?php endif; ?>
                            </th>
                            <td class="text-right"><?php echo number_format($account['price']); ?><?php echo _('Currency'); ?></td>
                            <td class="text-right"><?php echo $account['request_user']; ?></td>
                            <td class="text-right"><?php echo number_format($input); ?><?php echo _('Currency'); ?></td>
                            <td class="text-right"><?php echo number_format($output); ?><?php echo _('Currency'); ?></td>
                            <td class="text-right"><?php echo number_format($input - $output); ?><?php echo _('Currency'); ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
                </tbody>
                <tfoot>
                <tr>
                    <th class="text-center"><?php echo _('Total'); ?></th>
                    <td></td>
                    <td></td>
                    <td class="text-right"><?php echo number_format($i_total); ?><?php echo _('Currency'); ?></td>
                    <td class="text-right"><?php echo number_format($o_total); ?><?php echo _('Currency'); ?></td>
                    <td class="text-right"><?php echo number_format($i_total - $o_total); ?><?php echo _('Currency'); ?></td>
                </tr>
                </tfoot>
            </table>
        </article>
    </div>
</section>

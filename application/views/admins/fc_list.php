<div class="col-12">
    <p class="summary text-right">총 <strong class="mark"><span id="list_count"><?php echo $data['total']; ?></span>명</strong>의 담당회원이 있습니다.</p>
    <table class="table table-striped">
        <colgroup>
            <col>
            <col>
            <col>
            <col>
            <col style="width:180px">
        </colgroup>
        <thead class="thead-default">
            <tr>
                <th><?php echo _('User Name'); ?></th>
                <th><?php echo _('Phone'); ?></th>
                <th><?php echo _('Product'); ?></th>                        
                <th class="text-right">
                <?php if($this->input->get('refund')): ?>
                <?php echo _('Refund'); ?>
                <?php else: ?>
                <?php echo _('Payment'); ?>
                <?php endif ?>
                </th>
                <th class="text-center"><?php echo _('Transaction Date'); ?></th>
            </tr>
        </thead>
        <tbody>
        <?php if (empty($data['total'])): ?>
            <tr>
                <td colspan="7"><?php echo _('No Data'); ?></td>
            </tr>
        <?php else: ?>
        <?php foreach ($data['list'] as $user): ?>
            <tr>
                <td><?php echo anchor('/view/' . $user['id'], $user['name']); ?></td>
                <td><?php echo get_hyphen_phone($user['phone']); ?></td>
                <td>
                    <?php if(empty($user['products'])): ?>
                    상품없음
                    <?php else: ?>
                    <?php echo fc_profit_product($user['products']) ?>
                    <?php endif ?>
                </td>
                <td class="text-right"><?php echo number_format($user['account']) ?><?php echo _('Currency') ?></td>
                <td class="text-right"><?php echo get_dt_format($user['transaction_date'],$search_data['timezone']); ?></td>
            </tr>
            <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>
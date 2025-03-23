<table id="account_list" class="table table-striped">
    <colgroup>
        <col>
        <col>
        <col>
        <col>
        <col>
    </colgroup>
    <thead class="thead-default">
    <tr>
        <th><?php echo _('Manager') ?></th>
        <th class="text-right"><?php echo _('User') ?></th>
        <th class="text-right"><?php echo _('Sales') ?></th>
        <th class="text-right"><?php echo _('Income') ?></th>
        <th class="text-right"><?php echo _('Refund') ?></th>
    </tr>
    </thead>
    <tbody>
    <?php if (empty($data['total'])): ?>
        <tr>
            <td colspan="5" class="text-center"><?php echo _('No Data') ?></td>
        </tr>
    <?php else: ?>
        <?php foreach ($data['list'] as $index => $value): ?>
            <tr>
                <td><?php echo anchor('/account-employees/view/' . $value['employee_id'] . $params, $value['name']) ?></td>
                <td class="text-right"><?php echo number_format($value['total_user']) ?><?php echo _('Count People') ?></td>
                <td class="text-right"><?php echo number_format($value['total_income'] - $value['total_refund']) ?><?php echo _('Currency') ?></td>
                <td class="text-right"><?php echo number_format($value['total_income']) ?><?php echo _('Currency') ?></td>
                <td class="text-right"><?php echo number_format($value['total_refund']) ?><?php echo _('Currency') ?></td>
            </tr>
        <?php endforeach ?>
    <?php endif ?>
    </tbody>
</table>
<?php echo $this->pagination->create_links() ?>

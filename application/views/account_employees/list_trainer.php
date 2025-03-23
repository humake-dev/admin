<table id="account_list" class="table table-striped">
    <colgroup>
        <col>
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
        <th class="text-right"><?php echo _('Total Quantity') ?></th>
        <th class="text-right"><?php echo _('Total Use Quantity') ?></th>
        <th class="text-right"><?php echo _('Left Quantity') ?></th>
    </tr>
    </thead>
    <tbody>
    <?php if (empty($data['total'])): ?>
        <tr>
            <td colspan="7"><?php echo _('No Data') ?></td>
        </tr>
    <?php else: ?>
        <?php foreach ($data['list'] as $index => $value): ?>
            <tr>
                <td><?php echo anchor('/account-employees/view/' . $value['employee_id'] . $params, $value['name']) ?></td>
                <td class="text-right"><?php echo number_format($value['total_user']) ?><?php echo _('Count People') ?></td>
                <td class="text-right"><?php echo number_format($value['total_sales']) ?><?php echo _('Currency') ?></td>
                <td class="text-right">
                    <?php if (empty($value['quantity'])): ?>
                        0<?php echo _('Count Time') ?>
                    <?php else: ?>
                        <?php echo $value['quantity'] . _('Count Time') ?>
                    <?php endif ?>
                </td>
                <td class="text-right">
                    <?php if (empty($value['use_quantity'])): ?>
                        0<?php echo _('Count Time') ?>
                    <?php else: ?>
                        <?php echo $value['use_quantity'] . _('Count Time') ?>
                    <?php endif ?>
                </td>
                <td class="text-right">
                    <?php if (empty($value['use_quantity'])): ?>
                        <?php echo $value['quantity'] . _('Count Time') ?>
                    <?php else: ?>
                        <?php echo $value['quantity'] - $value['use_quantity'] ?><?php echo _('Count Time') ?>
                    <?php endif ?>
                </td>
            </tr>
        <?php endforeach ?>
    <?php endif ?>
    </tbody>
</table>
<?php echo $this->pagination->create_links() ?>


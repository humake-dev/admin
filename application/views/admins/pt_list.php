<div class="col-12">
    <p class="summary text-right">총 <strong class="mark"><span id="list_count"><?php echo $data['total']; ?></span>명</strong>의 담당회원이 있습니다.</p>
    <table class="table table-striped">
        <colgroup>
            <col>
            <col>
            <col>
            <col>
            <col>
            <col>
            <col style="width:160px">
        </colgroup>
        <thead class="thead-default">
            <tr>
                <th><?php echo _('Course Name'); ?></th>
                <th class="text-center"><?php echo _('Quantity'); ?></th>
                <th class="text-center"><?php echo _('Left Quantity'); ?></th>
                <th class="text-right"><?php echo _('Fee'); ?></th>
                <th class="text-right"><?php echo _('User Name'); ?></th>
                <th class="text-center"><?php echo _('Phone'); ?></th>
                <th class="text-center"><?php echo _('Transaction Date'); ?></th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($data['total'])): ?>
            <tr>
                <td colspan="7"><?php echo _('No Data'); ?></td>
            </tr>
            <?php else: ?>
            <?php foreach ($data['list'] as $enroll):
            $left_quantity=$enroll['quantity']-$enroll['use_quantity'];
            ?>
            <tr>
                <td><?php echo $enroll['product_name']; ?></td>
                <td class="text-center"><?php echo $enroll['quantity']; ?></td>
                <td class="text-center"><?php echo $left_quantity; ?></td>
                <td class="text-right">
                <?php echo number_format($enroll['account']); ?><?php echo _('Currency'); ?>
                </td>
                <td class="text-right">
                <?php if (empty($enroll['user_name'])): ?>
                <?php echo _('Deleted User'); ?>
                <?php else: ?>
                <?php echo anchor('/view/' . $enroll['id'], $enroll['user_name']); ?>
                <?php endif; ?>
                </td>
                <td class="text-center"><?php echo get_hyphen_phone($enroll['phone']); ?></td>
                <td><?php echo get_dt_format($enroll['transaction_date'], $search_data['timezone']); ?></td>
            </tr>
            <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>

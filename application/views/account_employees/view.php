<?php
$type = 'trainer';

if (!empty($data['content']['is_fc']) and empty($data['content']['is_trainer'])) {
    $type = 'fc';
}
?>
<div id="accounts" class="container">
    <div class="row">
        <?php echo $Layout->Element('accounts/nav'); ?>
        <div class="col-12">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><?php echo anchor('/', _('Home')); ?></li>
                    <li class="breadcrumb-item"><?php echo anchor('/account-employees', _('Account Employee')); ?></li>
                    <li class="breadcrumb-item active" aria-current="page">
                        <strong><?php echo sprintf(_('Account Employee Content(%s)'), $data['content']['name']); ?></strong>
                    </li>
                </ol>
            </nav>
        </div>
        <div class="col-12">
            <?php include __DIR__ . DIRECTORY_SEPARATOR . 'search_form.php'; ?>
        </div>
        <div class="col-12">
            <div class="float-left">
                <p class="summary">
                    <span id="list_count" style="display:none"><?php echo $data['total']; ?></span>
                    <?php echo sprintf(_('There Are %d Account Employee Info'), $data['total']); ?>
                </p>
            </div>
            <div class="float-right">
                <!-- <?php echo anchor('/account-employees/export-excel/' . $data['content']['id'], _('Export Excel'), array('class' => 'btn btn-secondary')); ?> -->
            </div>
            <table id="account_list" class="table table-striped">
                <colgroup>
                    <?php if ($type == 'trainer'): ?>
                        <col/>
                    <?php endif; ?>
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
                    <?php if ($type == 'trainer'): ?>
                        <th><?php echo _('User FC'); ?></th>
                    <?php endif; ?>
                    <th><?php echo _('User'); ?>
                        <?php if (!empty($common_data['branch']['use_access_card'])): ?>
                            (<?php echo _('Access Card No'); ?>)
                        <?php endif; ?>
                    </th>
                    <th><?php echo _('Registration Details'); ?></th>
                    <th class="text-center"><?php echo _('Period'); ?></th>
                    <th class="text-right"><?php echo _('Payment'); ?></th>
                    <th class="text-right"><?php echo _('Transaction Date'); ?></th>
                </tr>
                </thead>
                <tbody>
                <?php if (empty($data['total'])): ?>
                    <tr>
                        <td colspan="<?php if ($type == 'trainer'): ?>7<?php else: ?>5<?php endif; ?>"><?php echo _('No Data'); ?></td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($data['list'] as $index => $value): ?>
                        <tr>
                            <?php if ($type == 'trainer'): ?>
                                <td><?php if (!empty($value['fc_name'])): ?><?php echo anchor('/employees/view/' . $value['fc_id'], $value['fc_name']); ?><?php else: ?>-<?php endif; ?></td>
                            <?php endif; ?>
                            <td>
                                <?php if (empty($value['user_id'])): ?>
                                    <?php echo _('Deleted User'); ?>
                                <?php else: ?>
                                    <?php
                                    $anchor_title = $value['name'];

                                    if (!empty($common_data['branch']['use_access_card'])) {
                                        $anchor_title .= '(' . get_card_no($value['card_no'], false) . ')';
                                    }

                                    echo anchor('/view/' . $value['user_id'], $anchor_title); ?>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php
                                
                                $product_name = '';
                                if (!empty($value['product_name'])) {
                                    $product_name = $value['product_name'];
                                }

                                echo $product_name;

                                ?>
                            </td>
                            <td class="text-center">
                                <?php if(in_array($value['account_category_id'],array(14,15,16))): ?>
                                    -
                                <?php else: ?>
                                <?php if (!empty($value['start_date']) and !empty($value['end_date'])): ?>
                                    <?php if (date('Y', strtotime($value['end_date'])) > 2500): ?>
                                        <?php echo _('Unlimit'); ?>
                                    <?php else: ?>
                                        <?php echo get_dt_format($value['start_date'], $search_data['timezone']); ?> ~ <?php echo get_dt_format($value['end_date'], $search_data['timezone']); ?>
                                    <?php endif; ?>
                                <?php else: ?>
                                    -
                                <?php endif; ?>
                                <?php endif ?>
                            </td>
                            <td class="text-right">
                                <?php echo number_format($value['payment']); ?>
                                <?php echo _('Currency'); ?></td>
                            <td class="text-right"><?php echo get_dt_format($value['transaction_date'], $search_data['timezone']); ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
                </tbody>
            </table>
            <?php echo $this->pagination->create_links(); ?>
            <article class="card total_amount">
                <h3 class="card-header"><?php echo _('Total Amount'); ?></h3>
                <div class="card-body">
                    <ul class="row">
                        <li class="col-12 col-sm-6 col-lg-3"><?php echo _('Sales'); ?> :
                            <span><?php echo number_format(intval($data['employee_total']['total_income']) - intval($data['employee_total']['total_refund'])); ?><?php echo _('Currency'); ?></span>
                        </li>
                        <li class="col-12 col-sm-6 col-lg-3">
                            <?php echo _('Income'); ?> :
                            <span><?php echo number_format($data['employee_total']['total_income']); ?><?php echo _('Currency'); ?></span>
                        </li>
                        <li class="col-12 col-sm-6 col-lg-3">
                            <?php echo _('Refund'); ?> :
                            <span><?php echo number_format($data['employee_total']['total_refund']); ?></span>
                        </li>
                    </ul>
                </div>
            </article>
        </div>

    </div>
</div>

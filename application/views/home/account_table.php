<div class="table-responsive">
    <table id="user_account_list" class="table table-striped table-hover">
        <colgroup>
            <col>
            <col>
            <col>
            <col>
            <col>
            <col>
            <col>
            <?php if ($this->router->fetch_method() == 'accounts'): ?>
                <?php if ($this->Acl->has_permission('accounts', 'delete') or $this->Acl->has_permission('accounts', 'write')): ?>
                    <col style="width:150px">
                <?php endif; ?>
            <?php endif; ?>
        </colgroup>
        <thead>
        <tr class="thead-default">
            <th><?php echo _('Type'); ?></th>
            <th><?php echo _('Transaction Date'); ?></th>
            <th><?php echo _('Content'); ?></th>
            <th><?php echo _('Product'); ?></th>
            <th class="text-right"><?php echo _('Payment'); ?></th>
            <th class="text-right"><?php echo _('Cash'); ?></th>
            <th class="text-right"><?php echo _('Credit'); ?></th>
            <?php if ($this->router->fetch_method() == 'accounts'): ?>
                <?php if ($this->Acl->has_permission('accounts', 'delete') or $this->Acl->has_permission('accounts', 'write')): ?>
                    <th class="text-center manage<?php if ($this->Acl->has_permission('accounts', 'delete')): ?> a-delete<?php endif ?><?php if ($this->Acl->has_permission('accounts', 'write')): ?> a-write<?php endif ?>"><?php echo _('Manage'); ?></th>
                <?php endif; ?>
            <?php endif; ?>
        </tr>
        </thead>
        <tbody>
        <?php if (empty($data['account']['total'])): ?>
            <tr>
                <td colspan="<?php if ($this->Acl->has_permission('accounts', 'delete') or $this->Acl->has_permission('accounts', 'write')): ?>9<?php else: ?>8<?php endif ?>" class="text-center"><?php echo _('Not Inserted Account'); ?></td>
            </tr>
        <?php else: ?>
            <?php foreach ($data['account']['list'] as $account): ?>
                <tr>
                    <td>
                        <?php if ($account['type'] == 'I'): ?>
                            <span class="text-success"><?php echo _('Income'); ?></span>
                        <?php else: ?>
                            <span class="text-danger"><?php echo _('Outcome'); ?></span>
                        <?php endif; ?>
                    </td>
                    <td><?php echo get_dt_format($account['transaction_date'], $search_data['timezone']); ?></td>           
                    <td>
                        <?php if ($account['account_category_id'] == ADD_OTHER): ?>
                            <?php echo $account['other_title']; ?>
                        <?php else: ?>
                            <?php if ($account['account_category_id'] == ADD_ORDER): ?>
                                <?php echo $account['product_title']; ?>
                            <?php else: ?>
                                <?php echo $account['category_name']; ?>
                            <?php endif; ?>
                        <?php endif; ?>
                    </td>
                    <td><?php echo $account['product_name']; ?></td>
                    <td class="text-right blue">
                        <?php
                        if ($account['type'] == 'O' and ($account['cash'] + $account['credit']) != 0) {
                            echo '<span class="text-danger">';
                            echo number_format($account['cash'] + $account['credit']) . _('Currency');
                            echo '</span>';
                        } else {
                            echo number_format($account['cash'] + $account['credit']) . _('Currency');
                        }
                        ?>
                    </td>
                    <td class="text-right">
                        <?php
                        if ($account['type'] == 'O' and $account['cash'] != 0) {
                            echo '<span class="text-danger"> -';
                            echo number_format($account['cash']) . _('Currency');
                            echo '</span>';
                        } else {
                            echo number_format($account['cash']) . _('Currency');
                        }
                        ?>
                    </td>
                    <td class="text-right">
                        <?php
                        if ($account['type'] == 'O' and $account['credit'] != 0) {
                            echo '<span class="text-danger"> -';
                            echo number_format($account['credit']) . _('Currency');
                            echo '</span>';
                        } else {
                            echo number_format($account['credit']) . _('Currency');
                        }
                        ?>
                    </td>
                    <?php if ($this->router->fetch_method() == 'accounts'): ?>
                        <?php if ($this->Acl->has_permission('accounts', 'delete') or $this->Acl->has_permission('accounts', 'write')): ?>
                            <td class="text-center">
                                <?php if ($this->Acl->has_permission('accounts', 'write')): ?>
                                    <?php echo anchor('accounts/edit/' . $account['id'], _('Edit'), array('class' => 'btn btn-default')); ?>
                                <?php endif ?>
                                <?php if ($this->Acl->has_permission('accounts', 'delete')): ?>
                                    <?php echo anchor('accounts/delete/' . $account['id'], _('Delete'), array('class' => 'btn btn-danger btn-delete-confirm')); ?>
                                <?php endif ?>
                            </td>
                        <?php endif; ?>
                    <?php endif; ?>
                </tr>
            <?php endforeach; ?>
        <?php endif; ?>
        </tbody>
    </table>
</div>
<script>
    var ADD_OTHER=<?php echo ADD_OTHER ?>;
    var ADD_ORDER=<?php echo ADD_ORDER ?>;
</script>

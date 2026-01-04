<?php

$exists_other=true;
$product_id_exists=false;


if (isset($_GET['product_id'][0])) {
    $product_id_exists=true;
}

if(!empty($product_id_exists)):
if (!empty($data['total'])):
    foreach ($data['list'] as $index => $value):
        $list = explode(',', $value['insert_quantity']);

        $other=false;
        foreach ($list as $value) {
            $f_value = explode('||', $value);
            
            if ($f_value[0] != 1) {
                $other = true;
            }
        }

        if(empty($other)) {
            $exists_other=false;
        }
    endforeach;
endif;
endif;

?>
<div class="row">
    <div class="col-12 col-lg-6">
        <?php echo sprintf(_('There Are %d User'), $data['total']); ?>
    </div>
    <div class="col-12 col-lg-6 text-right">
        <?php echo anchor('searches/export-excel' . $params, _('Export Excel'), array('class' => 'btn btn-secondary')); ?>
    </div>
    <div class="col-12">
        <table id="user_list" class="table table-bordered table-striped table-hover">
            <colgroup>
                <col>
                <col>
                <col>
                <col>
                <col>
                <col>
                <?php if ($search_data['er_type'] != 'pt'): ?>
                    <col>
                <?php endif ?>
                <?php if(empty($exists_other)): ?>
                    <col>
                <?php endif ?>
                <col>
                <col>
                <col>
                <?php if ($search_data['er_type'] == 'pt'): ?>
                    <col>
                    <col>
                    <col>
                <?php endif ?>
                <col>
            </colgroup>
            <thead class="thead-default">
            <tr>
                <th><?php echo _('User'); ?></th>
                <th class="text-center"><?php echo _('Gender'); ?></th>
                <th><?php echo _('Phone'); ?></th>
                <th>
                    <?php if ($this->input->get('search_type') == 'field' and $this->input->get('search_field') == 'birthday'): ?>
                        <?php echo _('Birthday'); ?>
                    <?php else: ?>
                        <?php echo _('Transaction Date'); ?>
                    <?php endif; ?>
                </th>
                <th><?php echo _('Start Date'); ?></th>
                <th><?php echo _('End Date'); ?></th>
                <?php if ($search_data['er_type'] != 'pt'): ?>
                    <th class="text-right"><?php echo _('Quantity'); ?></th>
                <?php endif ?>
                <?php if(empty($exists_other)): ?>
                    <th class="text-right"><?php echo _('Total Period'); ?></th>
                <?php endif ?>
                <th class="text-right">
                    <?php

                    switch ($search_data['er_type']) {
                        case 'pt':
                            echo _('PT');
                            break;
                        case 'rent':
                            echo _('Facility');
                            break;
                        default:
                            echo _('Product');
                    }

                    ?>
                </th>
                <th>
                    <?php if ($this->input->get('search_type') == 'field' and $this->input->get('search_field') == 'visit_route'): ?>
                        <?php echo _('Visit Route'); ?>
                    <?php else: ?>
                        <?php if ($this->input->get('search_type') == 'field' and $this->input->get('search_field') == 'company'): ?>
                            <?php echo _('Company'); ?>
                    <?php else: ?>

                        <?php if ($search_data['er_type'] == 'pt'): ?>
                        <?php echo _('Enroll Trainer'); ?>
                    <?php else: ?>
                        <?php echo _('User Trainer'); ?>
                    <?php endif; ?>
                    <?php endif; ?>
                    <?php endif; ?>
                </th>
                <th><?php echo _('User FC'); ?></th>
                <?php if ($search_data['er_type'] == 'pt'): ?>
                    <th class="text-center"><?php echo _('Total Quantity'); ?></th>
                    <th class="text-center"><?php echo _('Use Quantity'); ?></th>
                    <th class="text-center"><?php echo _('Remain Quantity'); ?></th>
                <?php endif; ?>
                <th class="text-center"><?php echo _('Payment'); ?></th>
            </tr>
            </thead>
            <tbody>
            <?php if ($data['total']): ?>
                <?php foreach ($data['list'] as $index => $value): ?>
                    <tr>
                        <td>
                            <?php echo anchor('/view/' . $value['id'], $value['name']); ?>
                            <?php if ($value['token']): ?>
                                <i class="material-icons" style="vertical-align:bottom">stay_current_portrait</i>
                            <?php endif; ?>
                        </td>
                        <td class="text-center">
                            <?php

                            if (is_null($value['gender'])) {
                                echo '-';
                            } else {
                                if ($value['gender'] == 1) {
                                    echo _('Male');
                                }

                                if ($value['gender'] == 0) {
                                    echo _('Female');
                                }
                            }

                            ?>
                        </td>
                        <td class="phone"><?php echo get_hyphen_phone($value['phone']); ?></td>
                        <td>
                            <?php if ($this->input->get('search_type') == 'field' and $this->input->get('search_field') == 'birthday'): ?>
                                <?php echo get_dt_format($value['birthday'], $search_data['timezone']); ?>
                            <?php else: ?>
                                <?php echo get_dt_format($value['transaction_date'], $search_data['timezone']); ?>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php
                            if (empty($value['change_start_date'])) {
                                echo get_dt_format($value['start_date'], $search_data['timezone']);
                            } else {
                                echo get_dt_format($value['change_start_date'], $search_data['timezone']);
                            }
                            ?>
                        </td>
                        <td>
                            <?php
                            if (empty($value['change_end_date'])) {
                                echo get_dt_format($value['end_date'], $search_data['timezone']);
                            } else {
                                echo get_dt_format($value['change_end_date'], $search_data['timezone']);
                            }
                            ?>
                        </td>
                        <?php if ($search_data['er_type'] != 'pt'): ?>
                            <td class="text-right">
                                <?php echo get_d_insert_quantity_format($value['insert_quantity']) ?>
                            </td>
                        <?php endif ?>
                        <?php if(empty($exists_other)): ?>
                            <td class="text-right"><?php echo get_d_insert_quantity_format($value['insert_quantity'],$exists_other) ?></td>
                        <?php endif ?>
                        <td class="text-right"><?php if (empty($value['product_name'])): ?>-<?php else: ?><?php echo $value['product_name']; ?><?php endif; ?></td>
                        <td>
                            <?php if ($this->input->get('search_type') == 'field' and $this->input->get('search_field') == 'visit_route'): ?>
                                <?php if(mb_strlen($value['visit_route'])>15): ?>
                                    <a href="/user-additionals/view/<?php echo $value['additional_id'] ?>?field=visit_route" class="btn-modal"><?php echo ellipsize($value['visit_route'],15); ?></a>
                                    <?php else: ?>
                        <?php echo $value['visit_route'] ?>
                        <?php endif ?>
                    <?php else: ?>
                        <?php if ($this->input->get('search_type') == 'field' and $this->input->get('search_field') == 'company'): ?>
                            <?php if(mb_strlen($value['company'])>15): ?>
                                <a href="/user-additionals/view/<?php echo $value['additional_id'] ?>?field=company" class="btn-modal"><?php echo ellipsize($value['company'],15); ?></a>
                                <?php else: ?>
                        <?php echo $value['company'] ?>
                        <?php endif ?>
                    <?php else: ?>

                        <?php if (empty($value['trainer'])): ?>-<?php else: ?><?php echo $value['trainer']; ?><?php endif; ?>
                            <?php endif ?>
                    <?php endif; ?>
                        
                        </td>
                        <td><?php if (empty($value['fc'])): ?>-<?php else: ?><?php echo $value['fc']; ?><?php endif; ?></td>
                        <?php if ($search_data['er_type'] == 'pt'): ?>
                            <td class="text-right"><?php if (empty($value['quantity'])): ?>-<?php else: ?><?php echo get_d_quantity_format_unique($value['quantity']); ?><?php echo _('Count Time') ?><?php endif; ?></td>
                            <td class="text-right"><?php if (empty($value['quantity'])): ?>-<?php else: ?><?php echo get_d_quantity_format_unique($value['use_quantity']); ?><?php echo _('Count Time') ?><?php endif; ?></td>
                            <td class="text-right"><?php if (empty($value['quantity'])): ?>-<?php else: ?><?php echo get_d_quantity_format_unique($value['quantity']) - get_d_quantity_format_unique($value['use_quantity']); ?><?php echo _('Count Time') ?><?php endif; ?></td>
                        <?php endif; ?>
                        <td class="text-right"><?php if (empty($value['pay_total'])): ?>-<?php else: ?><?php echo number_format($value['pay_total']); ?><?php echo _('Currency'); ?><?php endif; ?></td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td<?php if ($search_data['er_type'] == 'pt'): ?> colspan="15"<?php else: ?>  colspan="13"<?php endif; ?>
                            class="text-center"><?php echo _('No Data'); ?></td>
                </tr>
            <?php endif; ?>
            </tbody>
        </table>
        <?php echo $this->pagination->create_links(); ?>
        <ul class="users_input" style="display:none">
            <li><input type="hidden"/><span style="font-weight:bold"><?php echo _('None'); ?></span></li>
        </ul>
        <?php

        $send_all = true;
        $reciever = _('All');
        $send_link = '/messages/add';
        $insert_link ='/order-blocks/add';

        if (!empty($params)) {
            $t_params = str_replace('?', '', $params);
            parse_str($t_params, $param_array);

            if (!empty($param_array['page'])) {
                unset($param_array['page']);
            }

            if (count($param_array)) {
                $reciever = $data['total'] . _('Count People');
                $send_link.=  $params . '&amp;search=1';
                $insert_link.=  $params . '&amp;search=1';
            }
        }

        ?>
        <a href="<?php echo $send_link ?>" class="btn btn-primary" target="_blank"
           title="<?php echo _('Send SMS Message'); ?>"><i class="material-icons" style="vertical-align:bottom">mail</i>
            <span style="vertical-align:bottom"><?php echo _('Send Message'); ?>(<?php echo $reciever; ?>)</span></a>

        <?php if($this->session->userdata('role_id')<3 and $this->input->get('reference_date')): ?>
            <a href="<?php echo $insert_link ?>" class="btn btn-primary" target="_blank>
            <span style="vertical-align:bottom"><?php echo _('Link Add Order Block'); ?>(<?php echo $reciever; ?>)</span></a>
        <?php endif ?>
    </div>
</div>

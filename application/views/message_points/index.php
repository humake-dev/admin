<div id="messages" class="container">
    <div class="row">
        <nav class="col-12 sub_nav">
            <ul class="nav nav-pills">
                <li><?php echo anchor('message-points/index', _('SMS Point'), array('class' => 'nav-link active')); ?></li>
                <li><?php echo anchor('message-analyses/index', _('SMS By Branch'), array('class' => 'nav-link')); ?></li>
                <li><?php echo anchor('message-analyses/current', _('Show By API'), array('class' => 'nav-link')); ?></li>
            </ul>
        </nav>
    </div>
    <div class="row">
        <div class="col-12">
            <table class="table table-borderd">
                <colgroup>
                    <col>
                    <col>
                    <col>
                    <col>
                    <col style="width:150px">
                </colgroup>
                <thead class="thead-default">
                <tr>
                    <th><?php echo _('Branch'); ?></th>
                    <th><?php echo _('Branch Phone'); ?></th>
                    <th class="text-right"><?php echo _('SMS Send Point'); ?></th>
                    <th class="text-right"><?php echo _('Updated At'); ?></th>
                    <th class="text-center"><?php echo _('Manage'); ?></th>
                </tr>
                </thead>
                <tbody>
                <?php if (empty($data['total'])): ?>
                    <tr>
                        <td><?php echo _('No Data'); ?></td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($data['list'] as $value): ?>
                        <tr>
                            <td>
                                <?php if (!empty($value['phone']) and !empty($value['sms_available_point'])): ?>
                                    <span class="text-success"><?php echo $value['title']; ?></span>
                                <?php else: ?>
                                    <span class="text-secondary"><?php echo $value['title']; ?></span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if (empty($value['phone'])): ?>
                                    -
                                <?php else: ?>
                                    <?php echo $value['phone']; ?>
                                <?php endif; ?>
                            </td>
                            <td class="text-right">
                                <?php if (empty($value['sms_available_point'])): ?>
                                    -
                                <?php else: ?>
                                    <?php echo number_format($value['sms_available_point'], 1); ?> <span
                                            class="text-info"><?php echo _('Point'); ?></span>
                                <?php endif; ?>
                            </td>
                            <td class="text-right">
                                <?php echo get_dt_format($value['updated_at'],$search_data['timezone'], 'Y'._('Year').' n'._('Month').' j'._('Day').' H'._('Hour').' i'._('Minute')); ?>
                            </td>
                            <td class="text-center">
                                <?php echo anchor('message-points/edit/' . $value['id'], _('Edit'), array('class' => 'btn btn-secondary')); ?>
                                <!-- <?php echo anchor('message-points/delete/' . $value['id'], _('Change Zero Point'), array('class' => 'btn btn-danger')); ?> -->
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
                </tbody>
            </table>
            <?php echo $this->pagination->create_links(); ?>
        </div>
    </div>
</div>
<table id="user_contents_list" class="table table-striped table-hover">
    <colgroup>
        <col />
        <col />
        <col />
        <col style="width:90px" />
    </colgroup>
    <thead class="thead-default">
        <tr>
            <th><?php echo _('User'); ?></th>
            <th class="text-center"><?php echo _('Memo'); ?></th>
            <th><?php echo _('Created At'); ?></th>
            <th class="text-center"><?php echo _('Manage'); ?></th>
        </tr>
    </thead>
    <tbody>
        <?php if (empty($data['memo']['total'])): ?>
        <tr>
            <td colspan="4" class="text-center"><?php echo _('No Data'); ?></td>
        </tr>
        <?php else: ?>        
        <?php foreach ($data['memo']['list'] as $index => $value): ?>
        <tr>
            <td><?php echo $value['name']; ?></td>
            <td class="text-center"><?php echo anchor('temp-user-contents/view/'.$value['id'], _('Show Content'), array('class' => 'btn btn-secondary btn-modal')); ?></td>
            <td><?php echo get_dt_format($value['created_at'], $search_data['timezone']); ?></td>
            <td class="text-center"><?php echo anchor('temp-user-contents/delete/'.$value['id'], _('Delete'), array('class' => 'btn btn-danger')); ?></td>
        </tr>
        <?php endforeach; ?>
        <?php endif; ?>
    </tbody>
</table>


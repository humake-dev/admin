<?php

$delete_button_title = _('End Order');
$delete_link = '#';

if (!empty($data['content']['id'])) {
    if (empty($data['content']['expired'])) {
        $delete_link = 'rents/end/' . $data['content']['id'];
    } else {
        $delete_button_title = _('End Order');
        $delete_link = 'orders/end/' . $data['content']['order_id'];
    }
}
?>
<div id="facility_manager_buttons" class="text-right col-12 col-lg-6" role="group">

        <?php if (isset($data['no'])): ?>
            <?php
            if (isset($data['content']['id'])):
                if ($this->Acl->has_permission('rents', 'write')): 
                if (new DateTime('now', $search_data['timezone']) > new DateTime($data['content']['end_date'], $search_data['timezone'])) {
                    echo anchor('rents/add?facility_id=' . $data['category']['current_id'] . '&amp;no=' . $data['no'], _('Add Rent'), array('id' => 'rent_add_button', 'class' => 'btn btn-secondary btn-sm'));
                    echo '&nbsp;';
                } else {
                    echo anchor('rents/add?facility_id=' . $data['category']['current_id'] . '&amp;no=' . $data['no'] . '&amp;after=' . $data['content']['id'], _('Add Rent Schedule'), array('id' => 'rent_add_button', 'class' => 'btn btn-secondary btn-sm'));
                    echo '&nbsp;';
                }

                echo anchor('rents/add?facility_id=' . $data['category']['current_id'] . '&amp;no=' . $data['no'] . '&amp;user_id=' . $data['content']['user_id'] . '&amp;after=' . $data['content']['id'], _('Re Rent'), array('id' => 'rent_re_add_button', 'class' => 'btn btn-secondary btn-sm'));

                ?>
                <?php echo anchor('rents/edit/' . $data['content']['id'], _('Edit Rent'), array('class' => 'btn btn-secondary btn-sm')); ?>
                <?php endif ?>
                <?php echo anchor('rents/move/' . $data['content']['id'], _('Move'), array('class' => 'btn btn-secondary btn-sm btn-modal')); ?>
                <?php echo anchor($delete_link, $delete_button_title, array('class' => 'btn btn-secondary btn-sm btn-modal')); ?>
            <?php else: ?>

                <?php if (isset($facilityObjs[$data['no']]['breakdown'])): ?>
                    <?php echo anchor('facility-breakdowns/delete/' . $facilityObjs[$data['no']]['breakdown_id'], _('Delete Breakdown'), array('id' => 'breakdown-delete-btn', 'class' => 'btn btn-secondary btn-sm btn-modal')); ?>
                <?php else: ?>
                    <?php if ($this->Acl->has_permission('rents', 'write')): ?>
                    <?php echo anchor('rents/add?facility_id=' . $data['category']['current_id'] . '&amp;no=' . $data['no'], _('Add Rent'), array('id' => 'rent_add_button', 'class' => 'btn btn-secondary btn-sm')); ?>
                    <?php endif ?>
                    <?php echo anchor('facility-breakdowns/add?facility_id=' . $data['category']['content']['id'] . '&amp;no=' . $data['no'], _('Add Breakdown'), array('id' => 'breakdown-add-btn', 'class' => 'btn btn-secondary btn-sm btn-modal')); ?>
                <?php endif; ?>
            <?php endif; ?>
        <?php else: ?>
            <?php if ($this->Acl->has_permission('rents', 'write')): ?>
            <!-- <?php echo anchor('#', _('Add Rent'), array('class' => 'btn btn-secondary btn-sm disabled')); ?>
            <?php echo anchor('#', _('Edit Rent'), array('class' => 'btn btn-secondary btn-sm disabled')); ?> -->
            <?php endif ?>
            <!-- <?php echo anchor('#', _('Move'), array('class' => 'btn btn-secondary btn-sm disabled')); ?>
            <?php echo anchor('#', $delete_button_title, array('class' => 'btn btn-secondary btn-sm disabled')); ?>
            <?php echo anchor('#', _('Add Breakdown'), array('class' => 'btn btn-secondary btn-sm disabled')); ?>
            <?php echo anchor('#', _('Delete Breakdown'), array('class' => 'btn btn-secondary btn-sm disabled')); ?> -->
        <?php endif; ?>
</div>

<?php if ($this->input->get('popup')): ?>
    <div class="modal-header">
        <h3 class="modal-title"><?php echo _('Prepared sentence'); ?></h3>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
<div class="modal-body">
    <?php else: ?>
    <div class="container">
        <div class="row">
            <div class="col-12">
                <?php endif; ?>
                <input type="hidden" id="message_prepare_list_count" value="<?php echo $data['total']; ?>"/>
                <table id="message_prepare_list" class="table table-hover">
                    <colgroup>
                        <col style="width:80px;">
                        <col>
                        <col>
                        <col>
                    </colgroup>
                    <thead class="thead-default">
                    <tr>
                        <th class="text-center"><?php echo _('Select'); ?></th>
                        <th><?php echo _('Title'); ?></th>
                        <th style="display:none"><?php echo _('Content'); ?></th>
                        <th class="text-right"><?php echo _('Created At'); ?></th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php if ($data['total']): ?>
                        <?php foreach ($data['list'] as $index => $value): ?>
                            <tr>
                                <td class="text-center"><input type="radio" name="id"
                                                               value="<?php echo $value['id']; ?>"></td>
                                <td class="title"><?php echo $value['title']; ?></td>
                                <td class="content" style="display:none"><?php echo $value['content']; ?></td>
                                <td class="text-right"><?php echo get_dt_format($value['created_at'], $search_data['timezone']); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="4" style="text-align:center"><?php echo _('No Data'); ?></td>
                        </tr>
                    <?php endif; ?>
                    </tbody>
                </table>
                <div class="sl_pagination">
                    <?php echo $this->pagination->create_links(); ?>
                </div>

                <?php if ($this->input->get('popup')): ?>
            </div>
            <div class="modal-footer">
                <button type="button" id="select_prepare_message" class="btn btn-primary btn-block btn-lg"
                        data-dismiss="modal"><?php echo _('Select') ?></button>
            </div>
            <script src="<?php echo $script; ?>"></script>
            <?php else: ?>
            <?php echo form_submit('', _('Select'), array('id' => 'select_prepare_message', 'class' => 'btn btn-primary btn-block btn-lg')); ?>
        </div>
    </div>
</div>
<?php endif; ?>


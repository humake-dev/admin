<div id="user-contents" class="container">
    <div class="row">
        <article class="col-12">
            <h2><?php echo _('Memo') ?></h2>
            <div>
                <?php echo sprintf(_('There Are %d Order Memo'), $data['total']) ?>
                <div class="float-right">
                    <select id="perpage" name="perpage" class="form-control">
                        <option value="5"<?php if ($data['per_page'] == 5): ?> selected="selected"<?php endif ?>><?php echo _('View Five') ?></option>
                        <option value="10"<?php if ($data['per_page'] == 10): ?> selected="selected"<?php endif ?>><?php echo _('View Ten') ?></option>
                        <option value="15"<?php if ($data['per_page'] == 15): ?> selected="selected"<?php endif ?>><?php echo _('View Fifteen') ?></option>
                        <option value="20"<?php if ($data['per_page'] == 20): ?> selected="selected"<?php endif ?>><?php echo _('View Twenty') ?></option>
                    </select>
                </div>
            </div>
            <?php include __DIR__ . DIRECTORY_SEPARATOR . 'list.php' ?>
            <?php echo $this->pagination->create_links() ?>
        </article>
    </div>
</div>
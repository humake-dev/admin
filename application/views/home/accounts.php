<div id="users" class="container">
    <div class="row">
        <?php include __DIR__ . DIRECTORY_SEPARATOR . 'aside.php'; ?>
        <div class="col-12 col-lg-8 col-xxl-9 user_sub">
            <?php if (empty($data['content'])): ?>
                <?php echo $Layout->element('home/not_found.php'); ?>
            <?php else: ?>
                <?php echo $Layout->element('home/nav'); ?>
                <article class="card">
                    <h3 class="card-header"><?php echo _('Account'); ?></h3>
                    <div class="card-body">
                        <div class="float-right">
                            <p class="summary">
                                <span id="list_count"
                                      style="display:none"><?php echo $data['account']['total']; ?></span>
                                <?php echo sprintf(_('There Are %d Account'), $data['account']['total']); ?>
                            </p>
                        </div>
                        <?php include __DIR__ . DIRECTORY_SEPARATOR . 'account_table.php'; ?>
                        <div class="sl_pagination"></div>
                    </div>
                </article>
            <?php endif; ?>
        </div>
    </div>
</div>

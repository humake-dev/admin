<section class="col-12">
    <div class="row">
        <h2 class="col-12">
            <?php if (isset($data['content']['id'])): ?>
                <?php echo _('Edit Counsel'); ?>
                &nbsp;&nbsp;<?php echo anchor('/counsels', _('Cancel Edit'), array('class' => 'float-right')); ?>
            <?php else: ?>
                <?php echo _('Add Counsel'); ?>
            <?php endif; ?>
        </h2>
    </div>
    <article class="row">
        <div class="col-12">
            <?php echo form_open($form_url, array('class' => 'counsel_form')); ?>
            <div class="card">
                <div class="card-body">
                </div>
            </div>
        </div>
    </article>
</section>                
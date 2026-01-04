<?php
        if ($data['content']['is_fc']) {
            $type = 'user';
            $user_class = 'nav-link active';
            $user_refund_class='nav-link';            
            $pt_class = 'nav-link';
            $commission_class = 'nav-link';

            if($this->input->get('refund')) {
                $user_class = 'nav-link';
                $user_refund_class='nav-link active';
            }
        }
        
        if ($data['content']['is_trainer']) {
            $type = 'pt';
            $user_class = 'nav-link';
            $pt_r_class='nav-link active';
            $pt_e_class='nav-link';

            if($this->input->get('pt_type')=='remain') {
                $pt_r_class='nav-link active';
                $pt_e_class='nav-link';
            }

            if($this->input->get('pt_type')=='expired') {
                $pt_class = 'nav-link';
                $pt_r_class='nav-link';
                $pt_e_class='nav-link active';
            }
        }

$user_param = '?type=user';
$pt_param = '?type=pt';

if ($this->input->get('start_date')) {
    $add_start_params = '&amp;start_date=' . $this->input->get('start_date');
    $user_param .= $add_start_params;
    $pt_param .= $add_start_params;
}

if ($this->input->get('end_date')) {
    $add_end_params = '&amp;end_date=' . $this->input->get('end_date');
    $user_param .= $add_end_params;
    $pt_param .= $add_end_params;
}

if($this->input->get('date_p')) {
    $user_param .='&amp;date_p='.$this->input->get('date_p');
}

?>
<section id="my_profit" class="container">
    <div class="row">
        <div class="col-12">
            <?php include __DIR__ . DIRECTORY_SEPARATOR . 'search_form.php'; ?>
            <?php if ($type == 'commission'): ?>
            <article class="card">
                <div class="card-header">
                    <h3><?php echo _('My Profit'); ?></h3>
                </div>
                <div class="card-body">
                    <?php echo _('Total Profit') ?>
                    : <?php echo number_format($data['my_total_commission']); ?><?php echo _('Currency') ?>
                </div>
                <article>
                    <?php else: ?>
                        <article class="card">
                            <div class="card-header">
                                <h3><?php echo _('My Member'); ?>
                                <?php if($this->input->get('start_date') and $this->input->get('end_date')): ?>
                                (<?php echo _('Transaction Date') ?> : <?php echo get_dt_format($this->input->get('start_date'),$search_data['timezone']) ?> ~ <?php echo get_dt_format($this->input->get('end_date'),$search_data['timezone']) ?>)
                                <?php else: ?>
                                    <?php if($this->input->get('start_date')): ?>
                                    (<?php echo _('Transaction Date') ?> : <?php echo get_dt_format($this->input->get('start_date'),$search_data['timezone']) ?>~)
                                    <?php endif ?>

                                    <?php if($this->input->get('end_date')): ?>
                                        (<?php echo _('Transaction Date') ?> : ~<?php echo get_dt_format($this->input->get('end_date'),$search_data['timezone']) ?>)
                                    <?php endif ?>
                                <?php endif ?>
                                </h3>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-12 col-lg-3">
                                        <?php if($this->input->get('refund')): ?>
                                            <?php echo _('My Refund User'); ?>
                                        <?php else: ?>
                                        <?php echo _('My Member'); ?>
                                        <?php endif ?>
                                        <p>
                                            <?php if (!empty($data['content']['is_trainer'])): ?>
                                                <?php echo _('PT Member'); ?>
                                                <?php echo $data['pt_count']; ?><?php echo _('Person'); ?>
                                            <?php endif; ?>
                                            <?php if (!empty($data['content']['is_trainer']) and !empty($data['content']['is_fc'])): ?> / <?php endif ?>
                                            <?php if (!empty($data['content']['is_fc'])): ?>
                                            <?php echo $data['user_count']; ?><?php echo _('Person'); ?>
                                            <?php endif; ?>
                                        </p>
                                    </div>
                                    <?php if ($data['content']['is_fc']): ?>
                                        <div class="col-12 col-lg-3">
                                            <?php echo _('Total sales of my members'); ?>
                                            <p><?php echo number_format($data['total_fc_sales']); ?><?php echo _('Currency'); ?></p>
                                        </div>
                                        <div class="col-12 col-lg-3">
                                            <?php echo _('Total refund of my members'); ?>
                                            <p><a href=""  class="text-danger"><?php echo number_format($data['total_fc_refund']); ?><?php echo _('Currency'); ?></a></p>                                                                                        
                                        </div>
                                        <div class="col-12 col-lg-3">
                                            <?php echo _('Total profit of my members'); ?>
                                            <p><a href=""  class="text-success"><?php echo number_format($data['total_fc_profit']); ?><?php echo _('Currency'); ?></a></p>                                                                                        
                                        </div>                                        
                                    <?php endif; ?>
                                </div>
                            </div>
                        </article>
                    <?php endif ?>
        </div>

        <nav class="col-12 sub_nav"<?php if ($this->router->fetch_method() == 'index'): ?> style="margin-bottom:0"<?php endif; ?>>
            <ul class="nav nav-pills">
                <?php if ($data['content']['is_fc']): ?>
                    <li class="nav-item"><?php echo anchor('/admins/my-profit' . $user_param, _('My User'), array('class' => $user_class)); ?></li>
                    <li class="nav-item" style="margin-right:30px"><?php echo anchor('/admins/my-profit' . $user_param.'&amp;refund=true', _('My Refund User'), array('class' => $user_refund_class)); ?></li>
                <?php endif ?>
                <?php if ($data['content']['is_trainer']): ?>
                    <li class="nav-item"><?php echo anchor('/admins/my-profit' . $pt_param.'&amp;pt_type=remain', _('Remain User'), array('class' => $pt_r_class)); ?></li>                       
                    <li class="nav-item"><?php echo anchor('/admins/my-profit' . $pt_param.'&amp;pt_type=expired', _('Expired User'), array('class' => $pt_e_class)); ?></li>                    
                <?php endif; ?>
            </ul>
        </nav>

        <?php if ($type == 'user'): ?>
            <?php include __DIR__ . DIRECTORY_SEPARATOR . 'fc_list.php'; ?>
        <?php endif; ?>

        <?php if ($type == 'pt'): ?>
            <?php if ($this->input->get('user_id')): ?>
                <?php include __DIR__ . DIRECTORY_SEPARATOR . 'pt_detail.php'; ?>
            <?php else: ?>
                <?php include __DIR__ . DIRECTORY_SEPARATOR . 'pt_list.php'; ?>
            <?php endif; ?>
        <?php endif; ?>
    </div>
    <?php echo $this->pagination->create_links(); ?>
</section>

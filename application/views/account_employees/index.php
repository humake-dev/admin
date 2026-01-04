<?php

$type = $data['type'];
$params = '';
if ($this->input->get()) {
    $p_index = 0;
    foreach ($this->input->get() as $key => $param) {
        if ($key == 'employee_id' or $key == 'employee_name') {
            if (empty($data['content'])) {
                continue;
            }
        }

        if ($key == 'type') {
            continue;
        }

        if ($p_index) {
            $params .= '&' . $key . '=' . $param;
        } else {
            $params .= '?' . $key . '=' . $param;
        }
        $p_index++;
    }
}

if ($type == 'fc') {
    $fc_class = 'nav-link active';
    $trainer_class = 'nav-link';
} else {
    $fc_class = 'nav-link';
    $trainer_class = 'nav-link active';
}

if (isset($data['content'])) {
    $params .= '&amp;employee_id=' . $data['content']['id'] . '&amp;employee_name=' . $data['content']['name'];
}

?>
<div id="accounts" class="container">
    <div class="row">
        <?php echo $Layout->Element('accounts/nav') ?>
        <div class="col-12">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><?php echo anchor('/', _('Home')) ?></li>
                    <li class="breadcrumb-item active" aria-current="page">
                        <strong><?php echo _('Account Employee') ?></strong></li>
                </ol>
            </nav>
        </div>
        <div class="col-12">
            <?php include __DIR__ . DIRECTORY_SEPARATOR . 'search_form.php' ?>
        </div>

        <nav class="col-12 sub_nav">
            <ul class="nav nav-pills">
                <?php if ($data['display_trainer_link']):
                    if (empty($params)) {
                        $trainer_params = '?type=trainer';
                    } else {
                        $trainer_params = $params . '&amp;type=trainer';
                    }

                    ?>
                    <li class="nav-item"><?php echo anchor('/account-employees' . $trainer_params, _('Trainer'), array('class' => $trainer_class)) ?></li>
                <?php endif ?>
                <?php if ($data['display_fc_link']):
                    if (empty($params)) {
                        $fc_params = '?type=fc';
                    } else {
                        $fc_params = $params . '&amp;type=fc';
                    }
                    ?>
                    <li class="nav-item"><?php echo anchor('/account-employees' . $fc_params, _('FC'), array('class' => $fc_class)) ?></li>
                <?php endif ?>
            </ul>
        </nav>

        <div class="col-12">
            <div class="float-left">
                <p class="summary">
                    <span id="list_count" style="display:none"><?php echo $data['total'] ?></span>
                    <?php echo sprintf(_('There Are %d Account Employee Info'), $data['total']) ?>
                </p>
            </div>
            <div class="float-right">
                <?php
                if (empty($params)) {
                    $excel_params = '?type=' . $data['type'];
                } else {
                    $excel_params = $params . '&amp;type=' . $data['type'];
                }
                ?>
                <?php echo anchor('/account-employees/export-excel' . $excel_params, _('Export Excel'), array('class' => 'btn btn-secondary')) ?>
            </div>
            <?php if ($type == 'fc'): ?>
                <?php include __DIR__ . DIRECTORY_SEPARATOR . 'list_fc.php' ?>
            <?php else: ?>
                <?php include __DIR__ . DIRECTORY_SEPARATOR . 'list_trainer.php' ?>
            <?php endif ?>
        </div>
    </div>
</div>

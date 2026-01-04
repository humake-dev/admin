<?php

if (!empty($data['height_list']['total'])):
    $height = $data['height_list']['list'][0]['height'];
    $height_id = $data['height_list']['list'][0]['id'];
endif;

?>
<div id="users" class="container">
    <div class="row">
        <?php include __DIR__ . DIRECTORY_SEPARATOR . 'aside.php'; ?>
        <div class="col-12 col-lg-8 col-xxl-9 user_sub">
            <?php if (empty($data['content'])): ?>
                <?php echo $Layout->element('home/not_found.php'); ?>
            <?php else: ?>
                <?php echo $Layout->element('home/nav'); ?>
                <div class="row">
                    <div class="col-12">
                        <article class="card">
                            <div class="card-header">
                                <ul class="nav nav-pills card-header-pills">
                                    <li class="nav-item">
                                        <a class="nav-link active" href="#"><?php echo _('Weight'); ?></a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link" href="#"><?php echo _('Height'); ?></a>
                                    </li>
                                </ul>
                                <div class="float-right buttons">
                                    <i class="material-icons">keyboard_arrow_up</i>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="card-block">
                                    <div class="row">
                                        <div class="col-12">
                                            <ul class="sl-bnt-group">
                                                <li><?php echo anchor('/body-indexes/add?user_id=' . $data['content']['id'], _('Add'), array('class' => 'btn btn-primary btn-modal')); ?></li>
                                            </ul>
                                            <table class="table table-striped">
                                                <colgroup>
                                                    <col style="width:120px"/>
                                                    <col/>
                                                    <col/>
                                                    <?php if ($this->Acl->has_permission('body_indexes', 'delete')): ?>
                                                        <col style="width:150px"/>
                                                    <?php endif; ?>
                                                </colgroup>
                                                <thead>
                                                <tr class="thead-default">
                                                    <th><?php echo _('Weight'); ?></th>
                                                    <th><?php echo _('Created At'); ?></th>
                                                    <th><?php echo _('BMI'); ?></th>
                                                    <?php if ($this->Acl->has_permission('body_indexes', 'delete')): ?>
                                                        <th class="text-center"><?php echo _('Manage'); ?></th>
                                                    <?php endif; ?>
                                                </tr>
                                                </thead>
                                                <tbody>
                                                <?php if ($data['list']['total']): ?>
                                                    <?php foreach ($data['list']['list'] as $value): ?>
                                                        <tr>
                                                            <td><?php echo $value['weight']; ?>Kg</td>
                                                            <td><?php echo get_dt_format($value['created_at'], $search_data['timezone']); ?></td>
                                                            <td>
                                                                <?php if (empty($height)): ?>
                                                                    <?php echo _('Not Insert Height'); ?>
                                                                <?php else: ?>
                                                                    <?php

                                                                    $m_h = $height * 0.01;
                                                                    $bmi = $value['weight'] / ($m_h * $m_h);
                                                                    $color = '';
                                                                    $pt = '보통';

                                                                    if ($bmi >= 23) {
                                                                        $pt = '과체중';
                                                                        $color = 'indianred';
                                                                        if ($bmi >= 25) {
                                                                            $pt = '비만';
                                                                            $color = 'red';
                                                                        }
                                                                    } else {
                                                                        if ($bmi < 18.5) {
                                                                            $pt = '저체중';
                                                                            $color = 'darkviolet';
                                                                        }
                                                                    }

                                                                    ?>
                                                                    <span style="color:<?php echo $color; ?>"><?php echo number_format($bmi, 1); ?> / <?php echo $pt; ?></span>
                                                                <?php endif; ?>
                                                            </td>
                                                            <?php if ($this->Acl->has_permission('body_indexes', 'delete')): ?>
                                                                <td class="text-center">
                                                                    <?php echo anchor('body-indexes/edit/' . $value['id'], _('Edit'), array('class' => 'btn btn-default btn-modal')); ?>
                                                                    <?php echo anchor('body-indexes/delete/' . $value['id'], _('Delete'), array('class' => 'btn btn-danger btn-delete-confirm')); ?>
                                                                </td>
                                                            <?php endif; ?>
                                                        </tr>
                                                    <?php endforeach; ?>
                                                <?php else: ?>
                                                    <tr>
                                                        <td colspan="6"
                                                            class="text-center"><?php echo _('No Data'); ?></td>
                                                    </tr>
                                                <?php endif; ?>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                                <div class="card-block" style="display:none">
                                    <div class="row">
                                        <div class="col-12">
                                            <div class="table-responsive">
                                                <ul class="sl-bnt-group">
                                                    <?php if (empty($height)): ?>
                                                        <li><?php echo anchor('/user-heights/add?user_id=' . $data['content']['id'], _('Add'), array('class' => 'btn btn-primary btn-modal')); ?></li>
                                                    <?php else: ?>
                                                        <?php echo $height; ?>cm
                                                        <li><?php echo anchor('/user-heights/edit/' . $height_id, _('Edit'), array('class' => 'btn btn-secondary btn-modal')); ?></li>
                                                        <li><?php echo anchor('/user-heights/delete/' . $height_id, _('Delete'), array('class' => 'btn btn-danger')); ?></li>
                                                    <?php endif; ?>
                                                </ul>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </article>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

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
            <?php echo form_open(''); ?>
            <div class="card">
                <div class="card-body">
                    <div class="form-group">
                        <label><?php echo _('Branch'); ?></label>
                        <p><?php echo $data['content']['title']; ?></p>
                    </div>
                    <div class="form-group">
                        <label><?php echo _('Branch Phone'); ?></label>
                        <?php
                        echo form_input(array(
                            'name' => 'phone',
                            'value' => $data['content']['phone'],
                            'maxlength' => '20',
                            'size' => '20',
                            'class' => 'form-control',
                        ));
                        ?>
                    </div>
                    <div class="form-group">
                        <label><?php echo _('SMS Send Point'); ?></label>
                        <?php
                        echo form_input(array(
                            'type' => 'number',
                            'name' => 'sms_available_point',
                            'value' => round($data['content']['sms_available_point']),
                            'maxlength' => '20',
                            'size' => '20',
                            'class' => 'form-control',
                        ));
                        ?>
                    </div>
                </div>
            </div>
            <?php echo form_submit('', _('Submit'), array('class' => 'btn btn-primary btn-lg btn-block')); ?>
            <?php echo form_close(); ?>
        </div>
    </div>
</div>
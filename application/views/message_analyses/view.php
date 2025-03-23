<div id="messages" class="container">
    <div class="row">
        <nav class="col-12 sub_nav">
            <ul class="nav nav-pills">
                <li><?php echo anchor('message-points/index', _('SMS Point'), array('class' => 'nav-link')); ?></li>
                <li><?php echo anchor('message-analyses/index', _('SMS By Branch'), array('class' => 'nav-link active')); ?></li>
                <li><?php echo anchor('message-analyses/current', _('Show By API'), array('class' => 'nav-link')); ?></li>
            </ul>
        </nav>
    </div>
    <div class="row">
        <div class="col-12">
            <?php echo form_open('', array('method' => 'get', 'class' => 'search_form card')); ?>
            <div class="card-body">
                <div id="default_period_form" class="col-12 form-group">
                    <label for="start_date"><?php echo _('Sended At'); ?></label>
                    <div class="form-row">
                        <div class="col-6">
                            <div class="input-group-prepend input-daterange">
                                <?php
                                $value_start_date = set_value('start_date');

                                if (empty($value_start_date)) {
                                    if (isset($search_data['display_start_date'])) {
                                        $value_start_date = $search_data['display_start_date'];
                                    }
                                }

                                echo form_input(array('name' => 'start_date', 'value' => $value_start_date, 'id' => 'a_start_date', 'class' => 'form-control datepicker'));
                                ?>
                                <div class="input-group-text">~</div>
                                <?php
                                $value_end_date = set_value('end_date');

                                if (empty($value_end_date)) {
                                    if (isset($search_data['display_end_date'])) {
                                        $value_end_date = $search_data['display_end_date'];
                                    }
                                }

                                echo form_input(array('name' => 'end_date', 'value' => $value_end_date, 'id' => 'a_end_date', 'class' => 'form-control datepicker'));
                                ?>
                            </div>
                        </div>
                        <div class="col-6">
                        </div>
                    </div>
                </div>
                <div class="col-12 form-group">
                    <?php echo form_submit('', _('Search'), array('class' => 'btn btn-primary')); ?>
                </div>
            </div>
            <?php echo form_close(); ?>
        </div>
    </div>
</div>
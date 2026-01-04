<?php echo form_open('') ?>
    <div class="card">
        <div class="card-header">
        <h3><?php echo _('Edit Enroll Use Log') ?></h3>
        </div>
        <div class="card-body">
            <div class="form-group">
                <label><?php echo _('Enroll') ?></label>
                <?php
                    $options=array();

                    if(!empty($data['enroll_list']['total']))
                    foreach($data['enroll_list']['list'] as $enroll) {
                        $options[$enroll['id']]=$enroll['product_name'].' / '.$enroll['start_date'].'~'.$enroll['end_date'];
                    }

                    $select=set_value('enroll_id',$data['content']['enroll_id']);
                
                    echo form_dropdown('enroll_id', $options, $select, array('id'=>'eul_enroll','class'=>'form-control'));
                ?>
            </div>
            <div class="form-group">
                <label><?php echo _('Start Time') ?></label>
                <p><?php echo get_dt_format($data['content']['start_time'],$search_data['timezone'],'Y-m-d H:i') ?></p>
            </div>
            <div class="form-group">
                <label><?php echo _('End Time') ?></label>
                <p><?php echo get_dt_format($data['content']['end_time'],$search_data['timezone'],'Y-m-d H:i') ?></p>
            </div>
            <div class="form-group">
                <label><?php echo _('Commission') ?></label>
                <?php 
                    echo form_input(array(
                    'type' => 'number',
                    'name'=>'commission',
                    'min' => 0,
                    'step' => 1,
                    'id' => 'eul_commission',
                    'value' => set_value('commission',$data['content']['commission']),
                    'class'=>'form-control'
                  ));
                ?>
            </div>
        </div>
    </div>
    <?php echo form_submit('', _('Update'), array('class'=>'btn btn-primary btn-block')) ?>    
<?php echo form_close() ?>
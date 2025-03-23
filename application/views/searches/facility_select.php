<div class="facility_select_layer col-12 col-md-6 col-lg-4 col-xl-3 form-group">
    <label for="s_facility_id"><?php echo _('Facility'); ?></label>
    <?php

    $default_facility_value = null;
    $option = array('' => _('Select'),'all_rent'=>_('All Rent'));

    if ($search_data['facility']['total']) {
        foreach ($search_data['facility']['list'] as $facility) {
            $option[$facility['product_id']] = $facility['title'];                        
        }
    }

    if (isset($search_data['product_id'])) {
        if (count($search_data['product_id'])) {
            if (in_array('all_rent', $this->input->get('product_id'))) {
                $default_facility_value='all_rent';
            } else {
                if ($search_data['facility']['total']) {
                    foreach ($search_data['facility']['list'] as $facility) {
                        if (in_array($facility['product_id'], $search_data['product_id'])) {
                            $default_facility_value = $facility['product_id'];
                        }                
                    }
                }
            }
        }
    }

    $rent_option_class='form-control';
    if(empty($default_facility_value)) {
        $rent_option_class.=' hide';
    }
    $select = set_value('product_id[]', $default_facility_value);

    echo form_dropdown('product_id[]', $option, $select, array('id'=>'facility_id','class' => 'form-control'));
    echo form_dropdown('rent_option', array(''=>_('All'),'empty_no'=>'미정', 'not_empty_no'=>'미정 아닌것'), set_value('rent_option'), array('id'=>'rent_option','class' =>$rent_option_class ));
   ?>
</div>
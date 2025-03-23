<div class="course_select_layer col-12 col-md-6 col-lg-4 col-xl-3 form-group">
    <label><?php echo _('Course'); ?></label>
    <select id="course_id" name="product_id[]" class="form-control">
        <option value=""><?php echo _('Select'); ?></option>
        <?php
        if ($search_data['course_category']['total']):   
            foreach ($search_data['course_category']['list'] as $course_category):
                $primary_category=false;                     
                if (!empty($course_category['product_counts'])):

                    if (!empty($search_data['course']['total']) and !empty($search_data['product_relations']['total'])) {
                            foreach ($search_data['course']['list'] as $course) {
                                if ($course['product_category_id'] != $course_category['id']) {
                                    continue;
                                }
                                foreach ($search_data['product_relations']['list'] as $pr) {                                
                                if($pr['product_id']==$course['product_id']) {
                                    $primary_category=true;
                                }
                            }
                        }
                }                    
                ?>
                <optgroup label="<?php echo $course_category['title']; ?>">
                    <?php if(!empty($primary_category)): ?>
                            <option value="all_primary"<?php if ($this->input->get('product_id')): ?><?php if (in_array('all_primary', $this->input->get('product_id'))): ?> selected="selected"<?php endif; ?><?php endif; ?> style="font-weight:bold">** <?php echo $course_category['title']; ?> <?php echo _('All') ?> **</option>
                    <?php endif ?>
                    <?php if (!empty($search_data['course']['total'])):
                        $pt_list = array();

                        foreach ($search_data['course']['list'] as $course):
                            if ($course['lesson_type'] == 4) {
                                $pt_list[] = $course['product_id'];
                            }

                            ?>
                            <?php if ($course['product_category_id'] == $course_category['id']): ?>
                            <option value="<?php echo $course['product_id']; ?>"<?php if (isset($search_data['product_id']) AND !in_array('all_primary', $this->input->get('product_id'))): ?><?php if (in_array($course['product_id'], $search_data['product_id'])): ?> selected="selected"<?php endif; ?><?php endif; ?>><?php echo $course['title']; ?></option>
                        <?php endif; ?>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </optgroup>
                <?php endif; ?>                
            <?php endforeach; ?>
        <?php endif; ?>
    </select>
    <?php if (count($pt_list)): ?>
        <script>
            var pt_list = [<?php foreach ($pt_list as $index => $p_p): ?><?php if (!empty($index)): ?>, <?php endif; ?>'<?php echo $p_p; ?>'<?php endforeach; ?>];
        </script>
    <?php endif; ?>
</div>
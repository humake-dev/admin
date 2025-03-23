<div id="add-course" class="container add-page">
  <div class="row">
    <?php include __DIR__.DIRECTORY_SEPARATOR.'aside.php' ?>
    <div class="col-12 col-md-6 col-lg-8 col-xl-9 right_a">
      <h3><?php echo _('Course Category Manage') ?></h3>
      <div class="card">
        <div class="card-body">
        <?php echo form_open('/course-categories/edit/'.$data['category']['current_id'], array('class'=>'form-inline')) ?>
          <div class="form-group">
            <label for="class_name"><?php echo _('Course Category') ?></label> &nbsp;
            <?php

            if (isset($data['category']['content']['title'])) {
                $value=$data['category']['content']['title'];
            } else {
                $value=set_value('name');
            }

            echo form_input(array(
                    'name'          => 'name',
                    'id'            => 'class_name',
                    'value'         => $value,
                    'class'         => 'form-control'
            ));
            ?>
          </div>
          &nbsp; <input type="submit" class="btn btn-primary" value="<?php echo _('Submit') ?>" />
          <?php echo form_close() ?>
        </div>
      </div>
    </div>
  </div>
</div>

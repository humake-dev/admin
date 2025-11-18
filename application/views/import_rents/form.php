<?php
      echo form_open_multipart('/import_rents/add');
?>
<div class="card">
  <div class="card-body">
    <input type="text" name="facility_id" class="form-control" value="309" />
  <div class="form-group">
    <label for="m_picture"><?php echo _('File'); ?></label>
    <?php
        echo form_upload(array(
                'name' => 'file',
                'id' => 'exece_file',
                'class' => 'form-control-file',
        ));
        ?>
    </div>
    </div>
  </div>
  <div class="form-group">
    <?php echo form_button(array('type' => 'submit'), '<span style="vertical-align:middle">'._('Send').'</span>', array('class' => 'btn btn-lg btn-primary btn-block')); ?>
  </div>
<?php echo form_close(); ?>

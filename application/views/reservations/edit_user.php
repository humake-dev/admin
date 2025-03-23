<div class="col-12 form-group">
  <label for="r_users"><?php echo _('User') ?></label>
  <?php 
  
    $members=explode(',',$data['content']['members']);

    foreach($members as $member) {
      $m=explode('::',$member);

      echo '<input name="user[]" type="hidden" value="'.$m[0].'" />';
      echo $m[1];
    }
  
  ?>
</div>
<input type="hidden" name="date" value="<?php echo set_value('date',$data['e_date']) ?>" />
<input type="hidden" name="time" value="<?php echo set_value('time',$data['e_time']) ?>" />
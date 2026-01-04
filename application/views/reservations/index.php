<?php 

$b_param=array();

if(isset($search_data['type'])) {
  $b_param['type']=$search_data['type'];
}

if($search_data['date']!=$search_data['today']) {
  $b_param['date']=$search_data['date'];
}

if(isset($data['trainer'])) {
  $b_param['trainer']=$data['trainer'];
}

$np_b_param=$b_param;
unset($np_b_param['date']);

if(count($np_b_param)) {
  $np_b_param_s='&amp;'.http_build_query($np_b_param);
} else {
  $np_b_param_s='';
}

$current_month_first_day = new DateTime($search_data['today'], $search_data['timezone']);
$current_month_first_day->modify('first day of this month');

$current_month_10_day = new DateTime($search_data['today'], $search_data['timezone']);
$current_month_10_day->modify('first day of this month');
$current_month_10_day->modify('+9 Days');

$prev_month_first_day = new DateTime($search_data['today'], $search_data['timezone']);
$prev_month_first_day->modify('first day of previous month');

?>
<div id="reservations" class="container">
  <div class="row">
    <div class="col-12 col-lg-8 list">
        <?php if (isset($search_data['start_time'])): ?>
        <?php include __DIR__.DIRECTORY_SEPARATOR.'list.php' ?>
        <div class="col-12">&nbsp;</div>
        <?php endif ?>
        <section id="layer_schedule_w" class="schedule_w" >
  <div class="row">
    <?php if(empty($data['admin']['total'])): ?>
    <div class="col-6">
      <?php include __DIR__.DIRECTORY_SEPARATOR.'nav.php' ?>
    </div>
    <div class="col-6" style="margin-bottom:15px">
              <?php echo anchor('/reservations?date='.$search_data['prev_date'].$np_b_param_s, '&lt;', array('class'=>'btn btn-sm btn-secondary','style'=>'margin-right:10px')) ?>
              <div class="d-inline text-center">
                <label>
                    <strong>
                    <?php 
                    if($search_data['type']=='month') {
                      echo get_dt_format($search_data['date'],$search_data['timezone'],'Y'._('Year').' j'._('Month'));
                    } else {
                      echo get_dt_format($search_data['date'],$search_data['timezone']);
                    }
                    
                    ?>
                    </strong>
                    <input type="hidden" class="datepicker" name="date" value="<?php echo $search_data['date']?>" />
                  </label>
              </div>
              <?php echo anchor('/reservations?date='.$search_data['next_date'].$np_b_param_s, '&gt;', array('class'=>'btn btn-sm btn-secondary','style'=>'margin-left:10px')) ?>
            </div>


    <?php else: ?>
    <div class="col-12 col-lg-4">
      <?php include __DIR__.DIRECTORY_SEPARATOR.'nav.php' ?>
    </div>
    <div class="col-12 col-lg-4 text-center" style="margin-bottom:15px">
              <?php echo anchor('/reservations?date='.$search_data['prev_date'].$np_b_param_s, '&lt;', array('class'=>'btn btn-sm btn-secondary','style'=>'margin-right:10px')) ?>
              <div class="d-inline text-center">
              <label>
                    <strong>
                    <?php 
                    if($search_data['type']=='month') {
                      echo get_dt_format($search_data['date'],$search_data['timezone'],'Y'._('Year').' n'._('Month'));
                    } else {
                      echo get_dt_format($search_data['date'],$search_data['timezone']);
                    }
                    
                    ?>
                    </strong>
                    <input type="hidden" class="datepicker" name="date" value="<?php echo $search_data['date']?>" />
                  </label>
              </div>
              <?php echo anchor('/reservations?date='.$search_data['next_date'].$np_b_param_s, '&gt;', array('class'=>'btn btn-sm btn-secondary','style'=>'margin-left:10px')) ?>
            </div>
    <div class="col-12 col-lg-4">
      <?php include __DIR__.DIRECTORY_SEPARATOR.'trainer_form.php' ?>
    </div>
    <?php endif ?>
</div>


<?php 
  if($search_data['type']=='month') {
    include __DIR__.DIRECTORY_SEPARATOR.'index_month.php';
  } else {
    include __DIR__.DIRECTORY_SEPARATOR.'index_day_week.php';
  }

?>
         </section>
    </div>
    <?php include __DIR__.DIRECTORY_SEPARATOR.'aside.php' ?>    
  </div>
</div>

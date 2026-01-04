<?php

if($search_data['type']=='week') {
  $d_limit=7;
  $week_day=array(_('Simple Sunday') ,_('Simple Monday')  ,_('Simple Tuesday') , _('Simple Wednesday') , _('Simple Thursday'), _('Simple Friday') ,_('Simple Saturday'));
} else {
  $d_limit=1;  
  $week_day=array(_('Sunday') ,_('Monday')  ,_('Tuesday') , _('Wednesday') , _('Thursday'), _('Friday') ,_('Saturday'));                   
}

?>
<?php echo form_open('/reservation-blocks/add') ?>
<table class="table table-bordered table-hover">
  <colgroup>
    <col style="width:12%;" />
    <?php if($search_data['type']=='day'): ?>
    <col />
    <?php else: ?>
    <col style="width:12%;" />
    <col style="width:12%;" />
    <col style="width:12%;" />
    <col style="width:12%;" />
    <col style="width:12%;" />
    <col style="width:12%;" />
    <col style="width:12%;" />
    <?php endif ?>
  </colgroup>
  <thead class="thead-default">
    <tr>
      <th class="text-center"><?php echo _('Time') ?></th>
      <?php
        $date_obj=new DateTime($search_data['date'],$search_data['timezone']);
        $cDate = $date_obj->format("Y-m-d");
        $dDate = $date_obj->format("m/d");
    
        if($d_limit>1) {
          $date_obj->modify('monday this week');
          $cDate = $date_obj->format('Y-m-d');    
          for ($j = 0; $j < $d_limit; ++$j) {            
            echo '<th class="text-center"><a href="/reservations?date='.$date_obj->format('Y-m-d').'">'.$date_obj->format("n"._('Month')." j"._('Day')).'('.$week_day[$date_obj->format("w")].')</a></th>';
            $date_obj->modify('+1 Day');
          }
        } else {
          echo '<th class="text-center"><a href="/reservations?date='.$date_obj->format('Y-m-d').'">'.$date_obj->format("n"._('Month')." j"._('Day')).'('.$week_day[$date_obj->format("w")].')</a></th>';
        }
      ?>
    </tr>
  </thead>
  <tbody>
    <?php
    
    for ($i=05; $i<24; $i++) { // 시간설정
      $cTime = substr('00'.$i, -2).":00";
      
      $class='';
      if($this->input->get('date') and $this->input->get('time')) {
        if($cDate.' '.$cTime==$this->input->get('date').' '.$this->input->get('time')) {
          $class=' class="table-primary"';
        }
      }
    
    ?>
    <tr<?php echo $class ?>>
      <th class="text-center"><?php echo $cTime ?></th>
      <?php
      
        $eDate = $cDate;
        
        for ($j = 0; $j < $d_limit; ++$j) {
          $start_time = $eDate.' '.$cTime.':00';
          $eTime = substr('00'.intval($i+1), -2).":00";
          $end_time= $eDate.' '.$eTime.':00';
          $html='';
          
          if ($data['reservation']['total']) {
            foreach($data['reservation']['list'] as $index=>$reservation) {
              if (($start_time<=$reservation['start_time']) and ($reservation['start_time']<$end_time)) {
                if(!empty($html)) {
                  $html.=' // ';
                }
                
                $html.=$reservation['manager_name'].'-'.$reservation['users'];
              }
            }
          }
          
          $clss='';
          $block=false;
          
          if ($j == 6) {
            $clss .= ' right';
          }
          
      ?>
      <td class="<?php echo $clss ?><?php if ($eDate==$search_data['date'] and $cTime==$search_data['time']): ?> table-primary<?php endif ?>">
      <?php 

        $block=true;
      
      if ($this->session->userdata('role_id') < 3) {
        $block = false;
      } else {
        if ($this->session->userdata('role_id') > 5) {
          if (new DateTime($eDate, $search_data['timezone']) >= new DateTime($search_data['today'], $search_data['timezone'])) {
            $block=false;
          }
        } else {
          if (new DateTime($eDate, $search_data['timezone']) >= $current_month_first_day) {
            $block = false;
          } else {
            if (new DateTime($eDate, $search_data['timezone']) >= $prev_month_first_day) {
                if (new DateTime($search_data['today'], $search_data['timezone']) <= $current_month_10_day) {
                    $block = false;
                }
            }
          }
        }
      }
      
      if (empty($html)): 
        if(empty($block)): 
          if ($this->Acl->has_permission('reservations', 'write')): 
            echo anchor('reservations/add?date='.$eDate.'&amp;time='.$cTime.'', '&nbsp;', array('style'=>'display:block;height:100%;width:100%'));
          endif;
        else: 
            echo '<span style="display:block;height:100%;width:100%;padding:10px">&nbsp;</span>';
        endif;
      else: 
        echo anchor('reservations?date='.$eDate.'&amp;time='.$cTime, $html, array('style'=>'display:block;height:100%;width:100%')); 
      endif;
      
      ?>
      </td>
      <?php
        $eDate = date("Y-m-d", strtotime($eDate.' +1 day'));
        }
      ?>
    </tr>
    <?php } ?>
  </tbody>
</table>
<?php echo form_close() ?>
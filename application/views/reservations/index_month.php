<?php
  $d_limit=7;

  // 1. 총일수 구하기    
  $last_day = date("t", strtotime($search_data['date']));
  
  // 2. 시작요일 구하기
  $start_week = date("w", strtotime(date("Y-m-01",strtotime($search_data['date']))));
  $ym=date("Y-m",strtotime($search_data['date']));
  
  // 3. 총 몇 주인지 구하기
  $total_week = ceil(($last_day + $start_week) / 7);
  
  // 4. 마지막 요일 구하기
  $last_week = date('w', strtotime(date("Y-m-".$last_day,strtotime($search_data['date']))));
  
  $week_day=array(_('Simple Sunday') ,_('Simple Monday')  ,_('Simple Tuesday') , _('Simple Wednesday') , _('Simple Thursday'), _('Simple Friday') ,_('Simple Saturday'));
?>
<table id="reservation_table" class="table table-bordered table-month">
  <colgroup>
    <col style="width:14%;" />
    <col style="width:14%;" />
    <col style="width:14%;" />
    <col style="width:14%;" />
    <col style="width:14%;" />
    <col style="width:14%;" />
  </colgroup>
  <thead class="thead-default">
    <tr>
      <?php
        $cDate = date("Y-m-d", strtotime($search_data['date']));
        $dDate = date("m/d", strtotime($search_data['date']));
        
        for ($j = 0; $j < $d_limit; ++$j) {
          $clss = ($j == 6) ? "right" : "";
      ?>
      <th class="text-center <?php echo $clss?>"><?php echo $week_day[$j] ?></th>
      <?php
        $dDate = date("m/d", strtotime($dDate.' +1 day'));
       }
      ?>
    </tr>
  </thead>
  <tbody>
    <?php
      
      $i=1;
      $max=$total_week;
      
      $day=1;
      
      for ($i; $i<=$max; $i++) {
        $cTime = substr('00'.$i, -2);
        
        $class='';
        
        if($this->input->get('date') and $this->input->get('time')) {
          if($cDate.' '.$cTime==$this->input->get('date').' '.$this->input->get('time')) {
            $class=' class="table-primary"';
          }
        }
      ?>
      <tr<?php echo $class ?>>
      <?php
        
        $eDate = $cDate;
        for ($j = 0; $j < $d_limit; ++$j) {
          $enable=false;
          
          if($search_data['type']=='month') {
            $m_day=date('Y-m-d',strtotime($ym.'-'.$day));
            $start_time = $m_day;
            $end_time = $m_day;
          } else {
            $s_timeojb=New DateTime($search_data['date'].' '.$cTime.':'.$minute_a[$j].':00',$search_data['timezone']);
            $start_time = $s_timeojb->format('Y-m-d H:i:s');
            $s_timeojb->modify('+10 Minutes');
            $end_time= $s_timeojb->format('Y-m-d H:i:s');
          }
          
          $html=0;

          if ($data['reservation']['total']) {
            foreach($data['reservation']['list'] as $index=>$reservation) {
              if (($start_time<=date('Y-m-d',strtotime($reservation['end_time']))) and (date('Y-m-d',strtotime($reservation['start_time']))<=$end_time)) {
                $html+=1;
              }
            }
          }
          
          $clss='';
          
          if(empty($enable)) {
            $clss='not_able';  
          }
          
          $block=false;
          if ($j == 6) {
            $clss .= ' right';
          }
          
          $pre_html='';
          
          // 8. 첫번째 주이고 시작요일보다 $j가 작거나 마지막주이고 $j가 마지막 요일보다 크면 표시하지 않아야하므로
          //    그 반대의 경우 -  ! 으로 표현 - 에만 날자를 표시한다.
          
          if (!(($i == 1 && $j < $start_week) || ($i == $max && $j > $last_week))){
            if($j == 0){
              // 9. $j가 0이면 일요일이므로 빨간색
              $pre_html.="<font color='#FF0000'>";
            } else if($j == 6){
              // 10. $j가 0이면 일요일이므로 파란색
              $pre_html.="<font color='#0000FF'>";
            }else{
              // 11. 그외는 평일이므로 검정색
              $pre_html.="<font color='#333'>";
            }
            
            // 12. 오늘 날자면 굵은 글씨
            if($day == date("j") and date('n',strtotime($search_data['date']))==date('n')){
              $pre_html.="<b>";
            }
            
            // 13. 날자 출력
            $pre_html.=$day;
            
            if($day == date("j") and date('n',strtotime($search_data['date']))==date('n')){
              $pre_html.="("._('Today').")</b>";
            }
            
            $pre_html.="</font>";
            
            $enable=true;
            
            // 14. 날자 증가
            $day++;
          }
        ?>
        <td class="<?php echo $clss?><?php if ($eDate==$search_data['date'] and $cTime==$search_data['time']): ?> table-primary<?php endif ?>">
        <?php
          echo $pre_html;

          $block=true;
      
          if ($this->session->userdata('role_id') < 3) {
            $block = false;
          } else {
            if ($this->session->userdata('role_id') > 5) {
            if (new DateTime($eDate, $search_data['timezone']) >= new DateTime($search_data['today'], $search_data['timezone'])) {
                $block=false;
              }
            } else {        
              if (new DateTime($m_day, $search_data['timezone']) >= $current_month_first_day) {
                $block = false;
              } else {
                if (new DateTime($m_day, $search_data['timezone']) >= $prev_month_first_day) {
                    if (new DateTime($search_data['today'], $search_data['timezone']) <= $current_month_10_day) {
                        $block = false;
                    }
                }
              }
            }
          }
          
          if($enable):
            $day_link='reservations?type=day&amp;date='.$m_day;
            
            if(isset($data['trainer'])) {
              $day_link.='&amp;trainer='.$data['trainer'];
            }
            
            if (empty($html)):
              if(empty($block)):
                echo anchor($day_link, '&nbsp;', array('style'=>'display:block;height:100%;width:100%'));
              else:
                echo '<span style="display:block;height:100%;width:100%;padding:10px">&nbsp;</span>';
              endif;
            else:
              echo anchor($day_link, $html.'건 예약됨', array('style'=>'display:block;height:100%;width:100%')); 
            endif;
          else:
            echo '<span style="display:block;height:100%;width:100%;padding:10px">&nbsp;</span>';
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

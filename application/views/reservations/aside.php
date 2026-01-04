<?php 
  $params='';
  if ($this->input->get()) {
    $p_index=0;
    foreach($this->input->get() as $key=>$param) {
      if($p_index) {
        $params.='&'.$key.'='.$param;
      } else {
        $params.='?'.$key.'='.$param;
      }
      $p_index++;                     
    }
  }

  if(count($b_param)) {
    $ex_b_param_s='?'.http_build_query($b_param);
  } else {
    $ex_b_param_s='';
  }

?>
<aside class="col-12 col-lg-4">
<div class="row">
  <h2 class="col-12 col-lg-6" style="padding-top:10px">
  <?php echo _('Reservation List') ?>
    <?php if($search_data['type']!='day'): ?>
    <?php if($search_data['type']=='month'): ?>
    (<?php echo _('Monthly') ?>)
    <?php else: ?>
    (<?php echo _('Weekly') ?>)
    <?php endif ?>
    <?php endif ?>
  </h2>
  <div class="col-12 col-lg-6 text-right" style="margin-bottom:12px">
  <?php echo anchor('reservations/export-excel'.$ex_b_param_s,_('Export Excel'),array('class'=>'btn btn-secondary')) ?>
  </div>
  </div>
  <table class="table table-bordered table-hover">
    <colgroup>
      <?php if(empty($data['trainer'])): ?>    
      <col />
      <?php endif ?>
      <col />
      <col />
      <col style="width:100px;" />
    </colgroup>
    <thead class="thead-default">
      <tr>
        <?php if(empty($data['trainer'])): ?>        
        <th><?php echo _('Manager') ?></th>
        <?php endif ?>
        <th><?php echo _('User Name') ?></th>
        <th>
          <?php if($search_data['type']=='day'): ?>
          <?php echo _('Reservation Time') ?>
          <?php else: ?>
          <?php echo _('Reservation Date') ?>
          <?php endif ?>
        </th>
        <th class="text-center"><?php echo _('Process') ?></th>
      </tr>
    </thead>
    <tbody>
      <?php if($data['aside_list']['total']): ?>
      <?php foreach($data['aside_list']['list'] as $index=>$value): ?>
      <tr<?php if(isset($data['content'])): ?><?php if($data['content']['id']==$value['id']): ?> class="table-primary"<?php endif ?><?php endif ?>>
        <?php if(empty($data['trainer'])): ?>
        <td><?php echo $value['manager_name'] ?></td>
        <?php endif ?>
        <td><?php echo $value['users'] ?></td>
        <td>
        <?php if($search_data['type']=='day'): ?>
        <?php echo get_dt_format($value['start_time'],$search_data['timezone'],'H'._('Hour').' i'._('Minute')) ?> ~ <?php echo get_dt_format($value['end_time'],$search_data['timezone'],'H'._('Hour').' i'._('Minute')) ?> 
        <?php else: ?>
        <?php echo get_dt_format($value['start_time'],$search_data['timezone'],'Y'._('Year').' n'._('Month').' j'._('Day').' '.'H'._('Hour')) ?>
        <?php endif ?>        
        </td>
        <td class="text-center">
          <?php 
            switch($value['complete']): 
               case 0:
                $c_text=_('Wating Reservation Complete');
                $c_class='secondary';
                break;
              default :
                $c_text=_('Complete');
                $c_class='success';
               break;
            endswitch;
            
            if ($this->session->userdata('role_id') < 4 and $value['complete']==0) {
              echo '<a href="/reservations/complete/'.$value['id'].$params.'" class="btn btn-secondary btn-'.$c_class.'">'.$c_text.'</a>';
            } else {
              echo '<span class="btn btn-secondary btn-'.$c_class.'">'.$c_text.'</span>';
            }
          
          ?>
        </td>
      </tr>
      <?php endforeach ?>
      <?php else: ?>
      <tr>
        <?php if(empty($data['trainer'])): ?>        
        <td colspan="4"><?php echo _('No Data') ?></td>
        <?php else: ?>
        <td colspan="3"><?php echo _('No Data') ?></td>        
        <?php endif ?>
      </tr>
      <?php endif ?>
      </tbody>
    </table>
    <?php echo $this -> pagination -> create_links() ?>    
</aside>

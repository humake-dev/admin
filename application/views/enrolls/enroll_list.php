<article id="enroll_list" class="row use_list"<?php if(empty($data['user_content'])): ?> style="display:none"<?php endif ?>>
  <input type="hidden" value="<?php echo $search_data['today'] ?>" />
  <h3 class="col-12">
    <?php if(empty($data['user_content'])): ?>
    <?php echo sprintf(_('%s User Enroll'),'<span></span>') ?>
    <?php else: ?>
    <?php echo sprintf(_('%s User Enroll'),'<span>'.$data['user_content']['name'].'</span>') ?>
    <?php endif ?>
  </h3>
  <input type="hidden" id="enroll_list_count" value="<?php if(!empty($data['enroll_list']['total'])): ?><?php echo $data['enroll_list']['total'] ?><?php endif ?>" />
  <div class="col-12">
    <table class="table table-striped table-hover">
      <colgroup>
        <col class="d-none d-md-table-cell" />
        <col />
        <col />
      </colgroup>
      <thead class="thead-default">
        <tr>
          <th class="d-none d-md-table-cell"><?php if ($this->router->fetch_class()=='users'): ?><?php echo _('Increment Number') ?><?php endif ?></th>
          <th><?php echo _('Course') ?></th>
          <th>
            <?php echo _('Status') ?>
            <?php if(isset($data['content'])): ?>
            <?php //echo ' / '._('Edit') ?>
            <?php endif ?>
          </th>
        </tr>
      </thead>
      <tbody>
        <?php if (empty($data['enroll_list']['total'])): ?>
        <tr>
            <td colspan="4" class="text-center"><?php echo _('No Data') ?></td>
        </tr>
        <?php else: ?>        
        <?php foreach ($data['enroll_list']['list'] as $index=>$value): ?>
        <tr class="enroll_e<?php if(isset($data['content']['id'])): ?><?php if($data['content']['id']==$value['id']): ?> table-primary"<?php endif ?><?php endif ?>">
          <td class="d-none d-md-table-cell"><?php echo number_format($data['enroll_list']['total']-$index) ?></td>
          <td>
            <div style="display:block;width:100px;white-space:nowrap;text-overflow:ellipsis">
              <?php echo anchor('/enrolls/view/'.$value['id'], $value['product_name']) ?>
            </div>
          </td>
          <td>
            <?php
            
            $start_date_obj = new DateTime($value['start_date'],$search_data['timezone']);      
            $end_date_obj = new DateTime($value['end_date'],$search_data['timezone']);
            $current_date_obj = new DateTime('now',$search_data['timezone']);        
    
            if ($value['stopped']) {
              $status='<span class="text-warning">'._('Stopped').'</span>';
            }  else {
              if($current_date_obj>$start_date_obj) {
                if ($end_date_obj < $current_date_obj) {
                  $status='<span class="text-warning">'._('Expired').'</span>';
                } else {
                  $status='<span class="text-success">'._('Using').'</span>';
                }
              } else {
                $status='<span class="text-warning">'._('Reservation').'</span>';
              }
            }

              echo $status;
            ?>
          </td>
        </tr>
        <?php endforeach ?>
        <?php endif ?>
      </tbody>
    </table>
  </div>
</article>

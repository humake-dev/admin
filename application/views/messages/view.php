<div id="view_message" class="container">
  <div class="row">
    <div class="col-12">
      <div class="card">
        <h3 class="card-header"><?php echo $data['content']['title']; ?></h3>
        <div class="card-body">
          <dl>
            <dt><?php echo _('Send Type'); ?></dt>
            <dd><?php echo display_send_type($data['content']['type']); ?></dd>
            <dt><?php echo _('User'); ?></dt>
            <dd>
            <?php
                if ($data['content']['send_all']):
                    echo _('Send All');
                else:
                    echo sl_message_change($data['content']['message_users'],$data['content']['message_temp_users']);
                endif;
                ?>
            </dd>
            <dt><?php echo _('Sender'); ?></dt>
            <dd>
              <?php if (empty($data['content']['admin_id'])): ?>
              <?php echo _('Deleted Employee'); ?>
              <?php else: ?>
              <?php echo $data['content']['sender']; ?>
              <?php endif; ?>
            </dd>
            <dt><?php echo _('Created At'); ?></dt>
            <dd><?php echo get_dt_format($data['content']['created_at'], $search_data['timezone'], 'Y'._('Year').' n'._('Month').' j'._('Day').' H'._('Hour').' i'._('Minute')); ?></dd>
            <dt><?php echo _('Content'); ?></dt>
            <dd><?php echo nl2br($data['content']['content']); ?></dd>
            </dl>
            <?php 
            
            if (!empty($data['content']['msr_content'])):
              if(empty($data['content']['msr_content']['result_code'])):
                echo '<p class="text-danger">'.$data['content']['msr_content']['message'].'</p>';
              else:
              switch($data['content']['msr_content']['msg_type']) {
                case 'LMS' :
                  $fee = SMS_FEE['lms'];                  
                  break;
                case 'MMS' :
                  $fee = SMS_FEE['mms'];                  
                  break;
                default : 
                $fee = SMS_FEE['sms'];
              }

            ?>
            <dl>
            <dt><?php echo _('SMS Type'); ?></dt>
            <dd><?php echo $data['content']['msr_content']['msg_type']; ?>(<?php echo number_format($fee, 1); ?><?php echo _('Currency'); ?>)</dd>   
            <dt><?php echo _('SMS Success Cnt'); ?> / <?php echo _('SMS Error Cnt'); ?></dt>
            <dd><span class="text-success"><?php echo $data['content']['msr_content']['success_cnt']; ?>건</span> / 
            <span<?php if ($data['content']['msr_content']['error_cnt']): ?> class="text-danger"<?php endif; ?>><?php echo $data['content']['msr_content']['error_cnt']; ?>건</span>
            </dd>
            <dt><?php echo _('SMS Fee'); ?></dt>
            <dd><?php echo number_format($fee, 1); ?> X <?php echo $data['content']['msr_content']['success_cnt']; ?> = <?php echo number_format($fee * $data['content']['msr_content']['success_cnt'], 1); ?><?php echo _('Currency'); ?></dd>            
            </dl>
            <?php endif; endif; ?>
        </div>
      </div>
    </div>
    <div class="col-12">
      <div class="sl_view_bottom">
        <?php echo anchor($this->router->fetch_class(), _('Go List'), array('class' => 'btn btn-secondary')); ?>
        <?php echo anchor($this->router->fetch_class().'/delete/'.$data['content']['id'], _('Delete'), array('class' => 'btn btn-danger float-right')); ?>
      </div>
    </div>
  </div>
</div>

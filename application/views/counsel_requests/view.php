<?php
      $params = '';
      if ($this->input->get()) {
          $p_index = 0;
          foreach ($this->input->get() as $key => $param) {
              if ($p_index) {
                  $params .= '&'.$key.'='.$param;
              } else {
                  $params .= '?'.$key.'='.$param;
              }
              ++$p_index;
          }
      }
?>
<div id="view-notice" class="container">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <h3 class="card-header"><?php echo $data['content']['title'] ?></h3>
                <div class="card-body">
                    <h3> 구분 : 
                <?php 
                                            switch ($data['content']['question_course']) {
                                                case 'pt':
                                                  echo _('Question PT');
                                                  break;
                                                case 'golf':
                                                  echo _('Question Golf');
                                                   break;
                                                default:
                                                 echo _('Question Default');
                                              }
                    ?></h3>
                    <?php echo nl2br($data['content']['content']) ?>

                    <?php if(!empty($data['content']['response'])): ?>
                    <hr />
                    <h3><?php echo _('Counsel') ?></h3>
                    <?php echo nl2br($data['content']['response']) ?>
                    <?php endif ?>
                </div>
            </div>
        </div>
        <div class="col-12">
            <div class="sl_view_bottom">
                <?php echo anchor($this->router->fetch_class().$params, _('Go List'), array('class' => 'btn btn-secondary')) ?>
                <?php if($this->session->userdata('center_id')): ?>
                <?php echo anchor($this->router->fetch_class() . '/delete/' . $data['content']['id'], _('Delete'), array('class' => 'btn btn-danger float-right')) ?>
                <?php endif ?>
            </div>
        </div>
    </div>
</div>

<?php

  $params='';
  if ($this->input->get()) {
    $p_index=0;
    foreach($this->input->get() as $key=>$param) {
      if($key=='employee_id' OR $key=='employee_name') {
        if(empty($data['content'])) {
          continue;
        }
      }

      if($key=='page') {
        continue;
      }

      if($p_index) {
        $params.='&'.$key.'='.$param;
      } else {
        $params.='?'.$key.'='.$param;
      }
      $p_index++;
    }
  }

  if(isset($data['content'])) {
    $params.='&amp;employee_id='.$data['content']['id'].'&amp;employee_name='.$data['content']['name'];
  }

?>
<div id="trainer-actives" class="container">
  <div class="row">
    <div class="col-12">
      <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
          <li class="breadcrumb-item"><?php echo anchor('/',_('Home')) ?></li>
          <li class="breadcrumb-item active" aria-current="page"><strong><?php echo _('Trainer Active Index') ?></strong></li>
        </ol>
      </nav>
    </div>
  </div>
  <div class="row">
    <div class="col-12">
      <?php include __DIR__.DIRECTORY_SEPARATOR.'search_form.php' ?>
    </div>
  </div>
  <div class="row">
    <div class="col-12 col-lg-6">
    <p class="summary">
          <span id="list_count" style="display:none"><?php echo $data['total'] ?></span>
          <?php echo sprintf(_('There Are %d Trainer'),$data['total']) ?>
        </p>
    </div>
    <div class="col-12 col-lg-6 text-right">
        <?php echo anchor('trainer-actives/export-excel' . $params, _('Export Excel'), array('class' => 'btn btn-secondary')); ?>
    </div>
  </div>
  <div class="row">
    <div class="col-12">
      <?php include __DIR__.DIRECTORY_SEPARATOR.'list.php' ?>
      <article class="card total_amount">
        <h3 class="card-header"><?php echo _('Total Amount') ?></h3>
        <div class="card-body">
          <ul class="row" style="list-style:none">
            <li class="col-12 col-sm-6 col-lg-3">
            <?php echo _('Period Use Quantity') ?> : 
            <span>
            <?php if(empty($data['employee_total']['total_period_use'])):?>0<?php else: ?><?php echo number_format($data['employee_total']['total_period_use']) ?><?php endif ?><?php echo _('Count Time') ?>
            </span>
            </li>
            <li class="col-12 col-sm-6 col-lg-3">
            <?php echo _('Commission') ?> : <span><?php echo number_format($data['employee_total']['total_commission']) ?><?php echo _('Currency') ?></span>
            </li>
          </ul>
        </div>
      </article>
    </div>
  </div>
</div>

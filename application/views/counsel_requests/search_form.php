<div class="row">
  <div class="col-12">
    <div id="counsel_request_search" class="card">
      <div class="card-header">
        <ul class="nav nav-pills card-header-pills">
          <li class="nav-item"><a class="nav-link<?php if ($this->session->userdata('counsel_search_open')): ?><?php if ($this->session->userdata('counsel_search_open')=='default'): ?> active<?php endif ?><?php endif ?>" href="#"><?php echo _('Default Search') ?></a></li>
          <li class="nav-item"><a class="nav-link<?php if ($this->session->userdata('counsel_search_open')): ?><?php if ($this->session->userdata('counsel_search_open')=='field'): ?> active<?php endif ?><?php endif ?>"  href="#"><?php echo _('Field Search') ?></a></li>
        </ul>
        <div class="float-right buttons">
          <i class="material-icons"><?php if ($this->session->userdata('counsel_search_open')): ?>keyboard_arrow_up<?php else: ?>keyboard_arrow_down<?php endif ?></i>
        </div>
      </div>
      <div class="card-body" <?php if (empty($this->session->userdata('counsel_search_open'))): ?> style="display:none"<?php endif ?>>
        <article class="card-block"<?php if($this->session->userdata('counsel_search_open')): ?><?php if($this->session->userdata('counsel_search_open')!='default'): ?> style="display:none"<?php endif ?><?php endif ?>>
          <div class="row">
            <?php include 'search_form_default.php'; ?>
          </div>
        </article>
        <article class="card-block"<?php if ($this->session->userdata('counsel_search_open')): ?><?php if ($this->session->userdata('counsel_search_open')!='field'): ?> style="display:none"<?php endif ?><?php else: ?> style="display:none"<?php endif ?>>
          <div class="row">
            <?php include 'search_form_field.php'; ?>
          </div>
        </article>
      </div>
    </div>
  </div>
</div>

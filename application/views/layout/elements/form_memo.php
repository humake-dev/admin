<div class="card">
  <div class="card-header" style="cursor: pointer;">
    <ul class="nav nav-pills card-header-pills">
      <li class="nav-item"><a class="nav-link" href="#"><?php echo _('Memo') ?></a></li>
    </ul>
    <div class="float-right buttons">
      <i class="material-icons">keyboard_arrow_down</i>
    </div>
  </div>
  <div class="card-body" style="display:none">
    <div class="card-block">
      <div class="row">
        <div class="col-12 form-group">
        <?php
          if (isset($data['content']['content'])) {
            $memo_value=$data['content']['content'];
          } else {
            $memo_value=set_value('content');
          }
          $memo_attr = array(
            'name' => 'content',
            'id'   => 'e_memo',
            'value' => $memo_value,
            'rows'  => 3,
            'class' => 'form-control'
          );
          echo form_textarea($memo_attr);
        
        ?>
        </div>
      </div>
    </div>
  </div>
</div>
<div class="card">
  <div class="card-body">
    <div class="row">
      <div class="col-12 form-group">
        <label><?php echo _('Change Content') ?></label>
            <?php
          if (isset($data['content']['change_content'])) {
            $change_memo_value=$data['content']['change_content'];
          } else {
            $change_memo_value=set_value('change_content');
          }
          $change_memo_attr = array(
            'name' => 'change_content',
            'id'   => 'change_memo',
            'value' => $change_memo_value,
            'rows'  => 3,
            'class' => 'form-control'
          );

          if($this->session->userdata('role_id')!=1) {
            $change_memo_attr['required']='required';
          }

          echo form_textarea($change_memo_attr);
        
        ?>
            </div>
        </div>
    </div>
</div>
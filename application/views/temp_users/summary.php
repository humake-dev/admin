<section id="user_summary" class="row">
  <h2 class="col-12" style="text-indent:-9999px;height:1px;line-height:1px"><?php echo _('User Info'); ?></h2>
  <div class="col-12">
    <article class="card">
      <div class="card-header">
        <h3><?php echo _('Memo'); ?></h3>
        <?php if ($this->session->userdata('branch_id')): ?>
        <?php if ($this->Acl->has_permission('users', 'write')): ?>
          <?php echo anchor('temp-user-contents/add?temp_user_id='.$data['content']['id'], '<i class="material-icons">add</i>', array('id' => 'add-user-memo', 'class' => 'btn-modal more2')); ?>
        <?php endif; ?>
        <?php endif; ?>
        <a href="/temp-users/memo/<?php echo $data['content']['id']; ?><?php echo $params; ?>" title="<?php echo _('More'); ?>" class="more"><i class="material-icons">redo</i></a>
      </div>
      <div class="card-body" <?php if (empty($other_data['memo']['total'])): ?>style="min-height:10px"<?php endif; ?>>
          <div class="row">
            <?php if (isset($other_data)): ?>
            <div class="col-12">
              <?php if (empty($other_data['memo']['total'])): ?>
              <p><?php echo _('Not Inserted Memo'); ?></p>
              <?php else: ?>
              <?php foreach ($other_data['memo']['list'] as $index => $memo): ?>
              <div style="margin-bottom:20px">
                <?php echo anchor('temp-user-contents/view/'.$memo['id'], nl2br($memo['content']), array('class' => 'btn-modal more')); ?>
                (<?php echo $memo['updated_at']; ?>)
              </div>
              <?php endforeach; ?>
              <?php endif; ?>
            </div>
        <?php else: ?>
            -
        <?php endif; ?>
          </div>
      </div>
    </article>
</div>
</section>

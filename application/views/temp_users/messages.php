<div id="users" class="container">
  <div class="row">
    <?php include __DIR__.DIRECTORY_SEPARATOR.'aside.php'; ?>
    <div class="col-12 col-lg-8 col-xxl-9 user_sub">
      <?php if (empty($data['content'])): ?>
      <?php echo $Layout->element('home/not_found.php'); ?>
      <?php else: ?>
      <?php include __DIR__.DIRECTORY_SEPARATOR.'nav.php'; ?>
      <div class="row">
        <div class="col-12">
          <article class="card">
            <h3 class="col-12 card-header"><?php echo _('Message'); ?></h3>
            <div class="col-12">
              <ul class="float-right sl-bnt-group">
                <li></li>
              </ul>
            </div>
            <div class="card-body">
            <table class="table table-striped">
              <colgroup>
      					<col style="width:120px" />
      					<col  />
      					<col  />
      					<col style="width:160px" />
      					<col  />
      					<col  style="width:100px" />
      				</colgroup>
      				<thead>
      					<tr class="thead-default">
      						<th><?php echo _('Type'); ?></th>
      						<th><?php echo _('Image'); ?></th>
      						<th><?php echo _('Title'); ?></th>
      						<th><?php echo _('Content'); ?></th>
      						<th><?php echo _('Created At'); ?></th>
      						<th><?php echo _('Manage'); ?></th>
      					</tr>
      				</thead>
      				<tbody>
            <?php if ($data['list']['total']): ?>
            <?php foreach ($data['list']['list'] as $value): ?>
              <tr>
    						<td><?php echo $value['type']; ?></td>
                <td>
    							<?php if (empty($value['picture_url'])): ?>
    							<?php echo _('Not Inserted'); ?>
    							<?php else: ?>
    							<img src="<?php echo getPhotoPath($value['picture_url'], 'message'); ?>" alt="" />
    							<?php endif; ?>
    						</td>
    						<td><?php echo $value['title']; ?></td>
                <td><?php echo anchor('message-contents/view/'.$value['id'], _('Show Content'), array('class' => 'btn btn-secondary btn-modal')); ?></td>
    						<td><?php echo $value['created_at']; ?></td>
                <td><?php echo anchor('messages/delete/'.$value['id'], _('Delete'), array('class' => 'btn btn-danger btn-delete-confirm')); ?></td>
    					</tr>
    					<?php endforeach; ?>
            <?php else: ?>
            <tr>
              <td colspan="6" class="text-center"><?php echo _('No Data'); ?></td>
            </tr>
          <?php endif; ?>
                </tbody>
              </table>
            </div>
          </article>
        </div>
      </div>
      <?php endif; ?>
    </div>
  </div>
</div>

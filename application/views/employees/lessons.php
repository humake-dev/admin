<div id="employees" class="container">
  <div class="row">
    <?php include __DIR__.DIRECTORY_SEPARATOR.'aside.php'; ?>
    <div class="col-12 col-lg-8 col-xl-9">
      <?php echo $Layout->Element('employees/nav'); ?>
      <section class="row">
        <h2 class="col-12"></h2>
        <article class="col-12">
          <div class="card">
            <div class="card-body">
              <table class="table">
                <colgroup>
                  <col />
                  <col />
                  <col />
                  <?php if (!empty($common_data['branch']['use_access_card'])): ?>                  
                  <col />
                  <?php endif; ?>
                  <col />
                  <col />
                </colgroup>
                <thead class="thead-default">
                  <tr>
                    <th><?php echo _('Increment Number'); ?></th>
                    <th><?php echo _('Course Name'); ?></th>
                    <th><?php echo _('User Name'); ?></th>
                    <?php if (!empty($common_data['branch']['use_access_card'])): ?>
                    <th><?php echo _('Access Card No'); ?></th>
                    <?php endif; ?>
                    <th><?php echo _('Amount received'); ?></th>
                    <th><?php echo _('Date of execution'); ?></th>
                  </tr>
                </thead>
                <tbody>
                  <?php if ($data['total']): ?>
          				<?php foreach ($data['list'] as $index => $value): ?>
                  <tr>
                    <td><?php echo number_format($data['total'] - ($data['page']) - $index); ?></td>
                    <td><?php echo $value['course_name']; ?></td>
                    <td><?php echo $value['user_name']; ?></td>
                    <?php if (!empty($common_data['branch']['use_access_card'])): ?>
                    <td><?php echo $value['card_no']; ?></td>
                    <?php endif; ?>
                    <td><?php echo number_format($value['fee']); ?><?php echo _('Currency'); ?></td>
                    <td><?php echo $value['execute_date']; ?></td>
                  </tr>
                  <?php endforeach; ?>
                  <?php else: ?>
                  <tr>
                    <td colspan="5"><?php echo _('No Data'); ?></td>
                  </tr>
                  <?php endif; ?>
                </tbody>
              </table>
            </div>
          </div>
        </article>
      </section>
    </div>
  </div>
</div>

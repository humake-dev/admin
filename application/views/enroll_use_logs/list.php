<div class="row">
    <div class="col-12">
        <h2 class="float-left" style="text-indent:-9999px"><?php echo _('Enroll Use Log List') ?></h2>
        <div class="float-left">
            <p class="summary">
                <span id="list_count" style="display:none"><?php echo $data['total'] ?></span>
                <?php echo sprintf(_('There Are %d Enroll Use Log'), $data['total']) ?>
            </p>
        </div>
    </div>
    <article class="col-12">
        <table id="enroll_use_list" class="table table-bordered table-striped table-hover">
            <colgroup>
                <col/>
                <?php if (empty($data['user'])): ?>
                <col/>
                <?php endif ?>
                <col/>
                <col/>
                <col/>
                <col/>
                <?php if($this->session->userdata('role_id')<3): ?>
                <col style="width:150px"/>
                <?php endif ?>
            </colgroup>
            <thead class="thead-default">
            <tr>
                <th><?php echo _('Course Name') ?></th>
                <?php if (empty($data['user'])): ?>
                <th><?php echo _('User Name') ?></th>
                <?php endif ?>
                <th><?php echo _('Start Time') ?></th>
                <th><?php echo _('End Time') ?></th>
                <th><?php echo _('Complete Approve At') ?></th>
                <th><?php echo _('Commission') ?></th>
                <?php if($this->session->userdata('role_id')<3): ?>
                <th class="text-center"><?php echo _('Manage') ?></th>
                <?php endif ?>
            </tr>
            </thead>
            <tbody>
            <?php if (empty($data['total'])): ?>
                <tr>
                    <td colspan="<?php if($this->session->userdata('role_id')<3): ?>8<?php else: ?>7<?php endif ?>"><?php echo _('No Data') ?></td>
                </tr>
            <?php else: ?>
                <?php foreach ($data['list'] as $index => $value): ?>
                    <tr>
                        <td><?php echo $value['course_name'] ?></td>
                        <?php if (empty($data['user'])): ?>
                        <td><?php echo $value['user_name'] ?></td>
                        <?php endif ?>
                        <td><?php echo get_dt_format($value['start_time'], $search_data['timezone'], 'Y-m-d H:i') ?></td>
                        <td><?php echo get_dt_format($value['end_time'], $search_data['timezone'], 'Y-m-d H:i') ?></td>
                        <td><?php echo get_dt_format($value['complete_at'], $search_data['timezone'], 'Y-m-d H:i') ?></td>
                        <td><?php echo number_format($value['commission']) ?><?php echo _('Currency') ?></td>
                        <?php if($this->session->userdata('role_id')<3): ?>
                        <td class="text-center">
                            <?php echo anchor('enroll-use-logs/edit/' . $value['id'], _('Edit'), array('class' => 'btn btn-secondary')) ?>
                            <?php echo anchor('enroll-use-logs/delete/' . $value['id'], _('Delete'), array('class' => 'btn btn-danger btn-delete-confirm')) ?>
                        </td>
                        <?php endif ?>
                    </tr>
                <?php endforeach ?>
            <?php endif ?>
            </tbody>
        </table>
        <?php echo $this->pagination->create_links() ?>
    </article>
</div>

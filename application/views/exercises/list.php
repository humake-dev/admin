<div class="col-12 col-xxl-9 list">
  <div class="row">
    <div class="col-12">
      <h2 class="float-left"><?php echo _('Exercise List') ?></h2>
      <div class="float-right">
        <p class="summary">
          <span id="list_count" style="display:none"><?php echo $data['total'] ?></span>
          <?php echo sprintf(_('There Are %d Exercise'),$data['total']) ?>
        </p>
      </div>
    </div>
    <article class="col-12">
      <table id="training_prepare_list" class="table table-bordered table-hover">
        <colgroup>
          <col style="width:116px">
          <col />
          <col />
          <col class="d-none d-md-table-cell">
          <col class="d-none d-lg-table-cell">
          <col class="d-none d-lg-table-cell">
        </colgroup>
        <thead class="thead-default">
          <tr>
            <th><?php echo _('Category') ?></th>
            <th><?php echo _('Title') ?></th>
            <th style="width:110px"><?php echo _('Image') ?></th>
            <th class="d-none d-md-table-cell" style="width:150px"><?php echo _('Enable') ?></th>
            <th class="d-none d-lg-table-cell" style="width:150px"><?php echo _('Created At') ?></th>
            <th class="d-none d-lg-table-cell text-center" style="width:150px"><?php echo _('Manage') ?></th>
          </tr>
        </thead>
        <tbody>
          <?php if ($data['total']): ?>
          <?php foreach ($data['list'] as $index=>$value):
              $page_param='';

              if($this->input->get('category_id')) {
                if(empty($page_param)) {
                  $page_param='?category_id='.$this->input->get('category_id');
                } else {
                  $page_param.='&category_id='.$$this->input->get('category_id');
                }
              }

              if($this->input->get('page')) {
                if(empty($page_param)) {
                  $page_param='?page='.$this->input->get('page');
                } else {
                  $page_param.='&page='.$this->input->get('page');
                }
              }
            ?>
            <tr<?php if (isset($data['content'])): ?><?php if ($data['content']['id']==$value['id']): ?> class="table-primary"<?php endif ?><?php endif ?>>
              <td><?php echo $value['category_name'] ?></td>
              <td><?php echo anchor('exercises/view/'.$value['id'], $value['title']) ?></td>
              <td>
                <?php if(empty($value['picture_url'])): ?>
                <?php echo _('Not Inserted') ?>
                <?php else: ?>
                <?php
                  $pictures=explode(',',$value['picture_url']);
                  foreach($pictures as $picture):
                  $picture_s=explode('::',$picture);
                ?>
                  <form action="/exercise-pictures/delete/<?php echo $picture_s[0] ?>">
                    <div>
                      <img src="<?php echo getPhotoPath('exercise', $this->session->userdata('branch_id'), $picture_s[1], 'small') ?>" />
                    </div>
                    <input type="submit"  value="삭제" class="btn btn-danger">
                  </form>
                <?php endforeach ?>
                <?php endif ?>

              </td>
              <td class="d-none d-md-table-cell"><?php echo change_enable($value['enable']) ?></td>
              <td class="d-none d-lg-table-cell"><?php echo $value['created_at'] ?></td>
              <td class="d-none d-lg-table-cell text-center">
                <?php echo anchor('exercises/edit/'.$value['id'].$page_param, _('Edit'), array('class'=>'btn btn-secondary')) ?>
                <?php echo anchor('exercises/delete/'.$value['id'], _('Delete'), array('class'=>'btn btn-danger btn-delete-confirm')) ?>
              </td>
            </tr>
          <?php endforeach ?>
          <?php else: ?>
          <tr>
            <td colspan="6"><?php echo _('No Data') ?></td>
          </tr>
          <?php endif ?>
        </tbody>
      </table>
      <?php echo $this -> pagination -> create_links() ?>
    </article>
    <div class="col-12">
      <?php echo anchor('exercises/add', _('Add'), array('class'=>'btn btn-primary hidden-xxl')) ?>
    </div>
  </div>
</div>

<div class="col-12">
  <nav class="sub_nav">
    <ul class="nav nav-pills">
      <?php
        $top_f_class='nav-link';
        if(empty($data['category']['current_id'])) {
          $top_f_class.=' active';
        }
      ?>
    <!-- <li class="nav-item"><?php echo anchor('exercises',_('All'), array('class'=>$top_f_class)) ?></li> -->
    <?php if($data['category']['total']): ?>
    <?php foreach ($data['category']['list'] as $index=>$value): ?>
    <?php
      $top_class='nav-link';
        if(isset($data['category']['current_id'])) {
        if (intval($data['category']['current_id'])==intval($value['id'])) {
          $top_class.=' active';
        }
      }
    ?>
    <li class="nav-item"><?php echo anchor('exercises?category_id='.$value['id'], $value['title'], array('class'=>$top_class)) ?></li>
    <?php endforeach ?>
    <?php endif ?>
    </ul>
  </nav>
</div>

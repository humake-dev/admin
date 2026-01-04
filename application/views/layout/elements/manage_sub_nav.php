<ul class="nav nav-pills card-header-pills">
  <?php if ($search_data['date']==date('Y-m-d')): ?>
  <li class="nav-item"><?php echo anchor($this->router->fetch_class().'?type=auto', '자동처방', array('class'=>get_nav_class('auto', $this->input->get('type'), 'auto'))) ?></li>
  <li class="nav-item"><?php echo anchor($this->router->fetch_class().'?type=manual', '수동처방', array('class'=>get_nav_class('manual', $this->input->get('type'), 'auto'))) ?></li>
  <?php else: ?>
    <li class="nav-item"><?php echo anchor($this->router->fetch_class().'?type=auto&amp;date='.$search_data['date'], '자동처방', array('class'=>get_nav_class('auto', $this->input->get('type'), 'auto'))) ?></li>
    <li class="nav-item"><?php echo anchor($this->router->fetch_class().'?type=manual&amp;date='.$search_data['date'], '수동처방', array('class'=>get_nav_class('manual', $this->input->get('type'), 'auto'))) ?></li>
  <?php endif ?>
</ul>

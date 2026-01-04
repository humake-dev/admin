<!-- header -->
<header>
    <div id="header" class="header navbar navbar-default navbar-fixed-top">
        <div class="container">
            <h1 class="navbar-header">
                <?php echo anchor('/', _('Main Title')); ?>
            </h1>
            <ul class="nav navbar-right">
                <li class="dropdown navbar-user">
                    <?php echo anchor('logout', '<i class="material-icons" style="font-size:35px;vertical-align:middle">highlight_off</i>', ['title' => _('Log Out'), 'class' => 'float-right']); ?>
                    <a href="javascript:;" class="dropdown-toggle float-right" data-toggle="dropdown">
  							<span class="d-none d-sm-inline"><?php echo sprintf(_('Welcome %s'), $this->session->userdata('admin_name')); ?>
                  (
                  <?php if (empty($this->session->userdata('center_id'))): ?>
                      <?php if ($this->session->userdata('role_id')==1): ?>
                      <?php echo $common_data['branch']['center_name']; ?>
                      -
                      <?php endif ?>
                      <?php if ($this->session->userdata('branch_id')): ?>
                          <?php echo $this->session->userdata('branch_name'); ?>
                      <?php endif; ?>
                  <?php else: ?>
                      <?php if ($this->session->userdata('branch_id')): ?>
                        <?php if (!empty($common_data['branch']['center_name'])): ?>
                        <?php echo $common_data['branch']['center_name']; ?>
                        <?php endif ?>
                        -
                          <?php if ($this->session->userdata('branch_id')): ?>
                              <?php echo $this->session->userdata('branch_name'); ?>
                          <?php endif; ?>
                      <?php else: ?>
                          <?php echo 'Humake ' . _('Total View'); ?>
                      <?php endif; ?>
                  <?php endif; ?>
                  )
                  </span>
                        <?php if ($this->session->userdata('admin_picture')):
                            if ($this->session->userdata('admin_branch_id')) {
                                $p_branch_id = $this->session->userdata('admin_branch_id');
                            } else {
                                $p_branch_id = $common_data['branch']['id'];
                            }
                            ?>
                            <img src="<?php echo getPhotoPath('employee', $p_branch_id, $this->session->userdata('admin_picture'), 'small'); ?>"
                                 alt=""/>
                        <?php else: ?>
                            <i class="material-icons" style="font-size:35px;vertical-align:middle">account_circle</i>
                        <?php endif; ?>
                    </a>
                    <?php if (isset($common_data['branch'])): ?>
                        <?php if ($common_data['branch']['use_admin_ac']): ?>
                            <?php echo anchor('admins/barcode/' . $this->session->userdata('admin_id'), '<i class="material-icons" style="font-size:35px;vertical-align:middle">view_headline</i>', ['title' => _('barcode'), 'class' => 'float-right d-inline d-sm-none']); ?>
                        <?php endif; ?>
                    <?php endif; ?>
                    <ul class="dropdown-menu animated fadeInLeft">
                        <li class="arrow"></li>
                        <?php if ($this->Acl->has_permission('branch_changes')): ?>
                            <?php if (isset($common_data['branch_list'])): ?>
                                <?php if ($common_data['branch_list']['total']): ?>
                                    <?php if ($this->session->userdata('branch_id')): ?>
                                        <li><?php echo anchor('branches/view-center', _('Total View'), ['title' => _('Total View'), 'class' => 'nav-link']); ?></li>
                                    <?php endif; ?>
                                    <?php foreach ($common_data['branch_list']['list'] as $branch): ?>
                                        <li<?php if ($this->session->userdata('branch_id') == $branch['id']): ?> class="active"<?php endif; ?>>
                                            <?php
                                            if ($this->session->userdata('role_id') == 1) {
                                                $branch_title = $branch['center_name'] . '-' . $branch['title'];
                                            } else {
                                                $branch_title = $branch['title'];
                                            }
                                            ?>
                                            <?php echo anchor('branches/change/' . $branch['id'], $branch_title, ['title' => _('Branch') . ' ' . $branch['title'] . ' ' . _('View'), 'class' => 'nav-link']); ?>
                                        </li>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            <?php endif; ?>
                            <li class="divider"></li>
                        <?php endif; ?>
                        <li><?php echo anchor('admins/edit', _('Edit My Profile')); ?></li>
                        <li><?php echo anchor('admins/my-profit', _('My Profit')); ?></li>
                        <?php if (isset($common_data['branch'])): ?>
                            <?php if ($common_data['branch']['use_admin_ac']): ?>
                                <li><?php echo anchor('admins/barcode/' . $this->session->userdata('admin_id'), _('barcode')); ?></li>
                            <?php endif; ?>
                        <?php endif; ?>
                    </ul>
                    <ul class="dropdown-menu animated fadeInLeft">
                        <li class="arrow"></li>
                    </ul>
                </li>
            </ul>
            <!-- end header navigation right -->
        </div>
    </div>

    <div id="top-menu" class="top-menu">
        <nav class="container">

            <ul class="nav">
                <?php if ($this->Acl->has_permission('users')): ?>
                    <li class="nav-item has-sub dropdown<?php if (in_array($this->router->fetch_class(), ['home', 'users', 'entrances', 'searches', 'enrolls', 'enroll_transfer', 'user_coupons', 'black_lists'])): ?> active<?php endif; ?>">
                        <?php echo anchor('/', '<i class="material-icons">group</i><span>' . _('User Manage') . '</span>', ['title' => _('User Manage Description'), 'class' => 'nav-link  dropdown-toggle']); ?>
                        <ul class="sub-menu">
                            <li<?php if (in_array($this->router->fetch_class(), ['home', 'users'])): ?> class="active"<?php endif; ?>><?php echo anchor('/', '<i class="material-icons">group</i><span>' . _('User Manage') . '</span>', ['title' => _('User Manage Description'), 'class' => 'nav-link']); ?></li>
                            <li<?php if ($this->router->fetch_class() == 'entrances'): ?> class="active"<?php endif; ?>><?php echo anchor('entrances', '<i class="material-icons">compare_arrows</i><span>' . _('User Entrance') . '</span>', ['title' => _('User Entrance Description'), 'class' => 'nav-link']); ?></li>
                            <li<?php if ($this->router->fetch_class() == 'searches'): ?> class="active"<?php endif; ?>><?php echo anchor('searches', '<i class="material-icons">search</i><span>' . _('User Search') . '</span>', ['title' => _('User Search Description'), 'class' => 'nav-link']); ?></li>
                        </ul>
                    </li>
                <?php endif; ?>

                <?php if ($this->session->userdata('branch_id')): ?>
                    <?php if ($this->Acl->has_permission('reservations') and $this->Acl->has_permission('counsels')): ?>
                        <li class="nav-item has-sub dropdown<?php if (in_array($this->router->fetch_class(), ['reservations', 'counsels', 'trainer_actives'])): ?> active<?php endif; ?>">
                            <?php echo anchor('reservations', '<i class="material-icons">event_available</i><span>' . _('Reservation Manage') . '</span>', ['title' => _('Reservation Manage Description'), 'class' => 'nav-link dropdown-toggle']); ?>
                            <ul class="sub-menu">
                                <li<?php if ($this->router->fetch_class() == 'reservations'): ?> class="active"<?php endif; ?>><?php echo anchor('reservations', '<i class="material-icons">event_available</i><span>' . _('Reservation Manage') . '</span>', ['title' => _('Reservation Manage Description'), 'class' => 'nav-link']); ?></li>
                                <li<?php if ($this->router->fetch_class() == 'counsels'): ?> class="active"<?php endif; ?>><?php echo anchor('counsels', '<i class="material-icons">description</i><span>' . _('Counsel Manage') . '</span>', ['title' => _('Counsel Manage Description'), 'class' => 'nav-link']); ?></li>
                                <li<?php if ($this->router->fetch_class() == 'trainer_actives'): ?> class="active"<?php endif; ?>><?php echo anchor('trainer-actives', '<i class="material-icons">description</i><span>' . _('Trainer Active Manage') . '</span>', ['title' => _('Trainer Active Manage Description'), 'class' => 'nav-link']); ?></li>
                            </ul>
                        </li>
                    <?php else: ?>
                        <?php if ($this->Acl->has_permission('reservations')): ?>
                        <li class="nav-item has-sub dropdown<?php if (in_array($this->router->fetch_class(), ['reservations', 'trainer_actives'])): ?> active<?php endif; ?>">
                            <?php echo anchor('reservations', '<i class="material-icons">event_available</i><span>' . _('Reservation Manage') . '</span>', ['title' => _('Reservation Manage Description'), 'class' => 'nav-link dropdown-toggle']); ?>
                            <ul class="sub-menu">
                                <li<?php if ($this->router->fetch_class() == 'reservations'): ?> class="active"<?php endif; ?>><?php echo anchor('reservations', '<i class="material-icons">event_available</i><span>' . _('Reservation Manage') . '</span>', ['title' => _('Reservation Manage Description'), 'class' => 'nav-link']); ?></li>
                                <li<?php if ($this->router->fetch_class() == 'trainer_actives'): ?> class="active"<?php endif; ?>><?php echo anchor('trainer-actives', '<i class="material-icons">description</i><span>' . _('Trainer Active Manage') . '</span>', ['title' => _('Trainer Active Manage Description'), 'class' => 'nav-link']); ?></li>
                            </ul>
                        </li>        
                        <?php endif; ?>
                        <?php if ($this->Acl->has_permission('counsels')): ?>
                            <li class="nav-item<?php if ($this->router->fetch_class() == 'counsels'): ?> active<?php endif; ?>">
                                <?php echo anchor('counsels', '<i class="material-icons">description</i><span>' . _('Counsel Manage') . '</span>', ['title' => _('Counsel Manage Description'), 'class' => 'nav-link']); ?>
                            </li>
                        <?php endif; ?>
                    <?php endif; ?>

                    <?php if ($this->Acl->has_permission('facilities') and $this->Acl->has_permission('rents')): ?>
                        <li class="nav-item has-sub dropdown<?php if (in_array($this->router->fetch_class(), ['rents', 'facilities', 'rent_transfers'])): ?> active<?php endif; ?>">
                            <?php if (isset($common_data['facility_menu_id'])): ?>
                                <?php echo anchor('rents', '<i class="material-icons">storage</i><span>' . _('Rent Manage') . '</span>', ['title' => _('Rent Manage Description'), 'class' => 'nav-link dropdown-toggle']); ?>
                            <?php else: ?>
                                <?php echo anchor('facilities', '<i class="material-icons">storage</i><span>' . _('Facility Manage') . '</span>', ['title' => _('Facility Manage Description'), 'class' => 'nav-link dropdown-toggle']); ?>
                            <?php endif; ?>
                            <ul class="sub-menu">
                                <?php if (isset($common_data['facility_menu_id'])): ?>
                                    <li<?php if ($this->router->fetch_class() == 'rents'): ?> class="active"<?php endif; ?>><?php echo anchor('rents', '<i class="material-icons">storage</i><span>' . _('Rent Manage') . '</span>', ['title' => _('Rent Manage Description'), 'class' => 'nav-link']); ?></li>
                                <?php endif; ?>
                                <li<?php if ($this->router->fetch_class() == 'facilities'): ?> class="active"<?php endif; ?>><?php echo anchor('facilities', '<i class="material-icons">dns</i><span>' . _('Facility Manage') . '</span>', ['title' => _('Facility Manage Description'), 'class' => 'nav-link']); ?></li>
                            </ul>
                        </li>
                    <?php else: ?>
                        <?php if ($this->Acl->has_permission('facilities')): ?>
                            <li class="nav-item<?php if ($this->router->fetch_class() == 'facilities'): ?> active<?php endif; ?>">
                                <?php echo anchor('facilities', '<i class="material-icons">dns</i><span>' . _('Facility Manage') . '</span>', ['title' => _('Facility Manage Description'), 'class' => 'nav-link']); ?>
                            </li>
                        <?php endif; ?>
                        <?php if ($this->Acl->has_permission('rents')): ?>
                            <li class="nav-item<?php if ($this->router->fetch_class() == 'rents'): ?> active<?php endif; ?>">
                                <?php echo anchor('rents', '<i class="material-icons">storage</i><span>' . _('Rent Manage') . '</span>', ['title' => _('Rent Manage Description'), 'class' => 'nav-link']); ?>
                            </li>
                        <?php endif; ?>
                    <?php endif; ?>


                    <?php if ($this->Acl->has_permission('rent_sws')): ?>
                        <li class="nav-item<?php if ($this->router->fetch_class() == 'rent_sws'): ?> active<?php endif; ?>">
                            <?php echo anchor('rent-sws', '<i class="material-icons">storage</i><span>' . _('Sport Wear Rent Manage') . '</span>', ['title' => _('Rent Manage Description'), 'class' => 'nav-link']); ?>
                        </li>
                    <?php endif; ?>

                    <?php if ($this->Acl->has_permission('course_categories') and $this->Acl->has_permission('courses')): ?>
                        <li class="nav-item has-sub dropdown<?php if (in_array($this->router->fetch_class(), ['courses', 'course_categories', 'course_class_groups', 'course_classes'])): ?> active<?php endif; ?>">
                            <?php echo anchor('courses', '<i class="material-icons">fitness_center</i><span>' . _('Course Manage') . '</span>', ['title' => _('Course Manage'), 'class' => 'nav-link dropdown-toggle']); ?>
                            <ul class="sub-menu">
                                <li<?php if ($this->router->fetch_class() == 'courses'): ?> class="active"<?php endif; ?>><?php echo anchor('courses', '<i class="material-icons">fitness_center</i><span>' . _('Course Manage') . '</span>', ['title' => _('Course Manage'), 'class' => 'nav-link']); ?></li>
                                <li<?php if ($this->router->fetch_class() == 'course_categories'): ?> class="active"<?php endif; ?>><?php echo anchor('course-categories', '<i class="material-icons">fitness_center</i><span>' . _('Course Category Manage') . '</span>', ['title' => _('Course Category Manage Description'), 'class' => 'nav-link']); ?></li>
                            </ul>
                        </li>
                    <?php else: ?>
                        <?php if ($this->Acl->has_permission('course_categories')): ?>
                            <li class="nav-item<?php if ($this->router->fetch_class() == 'course_categories'): ?> active<?php endif; ?>">
                                <?php echo anchor('course-categories', '<i class="material-icons">fitness_center</i><span>' . _('Course Category Manage') . '</span>', ['title' => _('Course Category Manage Description'), 'class' => 'nav-link']); ?>
                            </li>
                        <?php endif; ?>
                        <?php if ($this->Acl->has_permission('courses')): ?>
                            <li class="nav-item has-sub dropdown<?php if (in_array($this->router->fetch_class(), ['courses', 'course_class_groups', 'course_classes'])): ?> active<?php endif; ?>">
                                <?php echo anchor('courses', '<i class="material-icons">fitness_center</i><span>' . _('Course Manage') . '</span>', ['title' => _('Course Manage'), 'class' => 'nav-link dropdown-toggle']); ?>
                                <ul class="sub-menu">
                                    <li<?php if ($this->router->fetch_class() == 'courses'): ?> class="active"<?php endif; ?>><?php echo anchor('courses', '<i class="material-icons">fitness_center</i><span>' . _('Course Manage') . '</span>', ['title' => _('Course Manage'), 'class' => 'nav-link']); ?></li>

                                </ul>
                            </li>
                        <?php endif; ?>
                    <?php endif; ?>


                <?php endif; ?>



                <?php if ($this->Acl->has_permission('analyses') and $this->Acl->has_permission('accounts')): ?>
                    <li class="nav-item has-sub dropdown<?php if (in_array($this->router->fetch_class(), ['accounts', 'account_employees', 'order_days', 'account_others', 'order_users', 'account_users', 'account_products', 'analyses', 'branch_payments', 'search_ys'])): ?> active<?php endif; ?>">
                        <?php echo anchor('accounts', '<i class="material-icons">attach_money</i><span>' . _('Accounts Infomation') . '</span>', ['title' => _('Account Analysis Description'), 'class' => 'nav-link dropdown-toggle']); ?>
                        <ul class="sub-menu">
                            <li<?php if (in_array($this->router->fetch_class(), ['accounts', 'account_employees', 'order_days', 'account_others', 'order_users', 'account_users', 'account_products', 'analyses', 'branch_payments', 'search_ys'])): ?> class="active"<?php endif; ?>><?php echo anchor('accounts', '<i class="material-icons">attach_money</i><span>' . _('Account Infomation') . '</span>', ['title' => _('Account Analysis Description'), 'class' => 'nav-link']); ?></li>
                            <li<?php if ($this->router->fetch_class() == 'analyses' and $this->router->fetch_method() == 'index'): ?> class="active"<?php endif; ?>><?php echo anchor('analyses', '<i class="material-icons">insert_chart</i><span>' . _('Account Analysis') . '</span>', ['title' => _('Account Analysis Description'), 'class' => 'nav-link']); ?></li>
                        </ul>
                    </li>
                <?php else: ?>
                    <?php if ($this->Acl->has_permission('accounts')): ?>
                        <li class="nav-item<?php if ($this->router->fetch_class() == 'accounts'): ?> active<?php endif; ?>">
                            <?php echo anchor('accounts', '<i class="material-icons">attach_money</i><span>' . _('Account Infomation') . '</span>', ['title' => _('Account Infomation'), 'class' => 'nav-link']); ?>
                        </li>
                    <?php endif; ?>
                    <?php if ($this->Acl->has_permission('analyses')): ?>
                        <li class="nav-item<?php if ($this->router->fetch_class() == 'analyses' and $this->router->fetch_method() == 'index'): ?> active<?php endif; ?>">
                            <?php echo anchor('analyses', '<i class="material-icons">insert_chart</i><span>' . _('Account Analysis') . '</span>', ['title' => _('Account Analysis Description'), 'class' => 'nav-link']); ?>
                        </li>
                    <?php endif; ?>
                <?php endif; ?>


                <?php if ($this->session->userdata('branch_id')): ?>
                    <?php if ($this->Acl->has_permission('messages')): ?>
                        <li class="nav-item has-sub dropdown<?php if (in_array($this->router->fetch_class(), ['messages', 'message_prepares', 'message_analyses', 'message_points'])): ?> active<?php endif; ?>">
                            <?php echo anchor('messages', '<i class="material-icons">mail</i><span>' . _('Message Manage') . '</span>', ['title' => _('Message Manage Description'), 'class' => 'nav-link dropdown-toggle']); ?>
                            <ul class="sub-menu">
                                <li<?php if ($this->router->fetch_class() == 'messages'): ?> class="active"<?php endif; ?>><?php echo anchor('messages', '<i class="material-icons">mail</i><span>' . _('Message Manage') . '</span>', ['title' => _('Message Manage Description'), 'class' => 'nav-link']); ?></li>                            
                                <li<?php if ($this->router->fetch_class() == 'message_prepares'): ?> class="active"<?php endif; ?>><?php echo anchor('message-prepares', '<i class="material-icons">mail</i><span>' . _('Prepared Message Manage') . '</span>', ['title' => _('Prepared Message Manage Description'), 'class' => 'nav-link']); ?></li>
                                <?php if (($this->session->userdata('role_id') < 3) or in_array($this->session->userdata('admin_uid'),array('humake01','humake02','humake03'))): ?>
                                    <li<?php if (in_array($this->router->fetch_class(), ['message_points', 'message_analyses'])): ?> class="active"<?php endif; ?>><?php echo anchor('message-points', '<i class="material-icons">mail</i><span>' . _('Message Point Manage') . '</span>', ['title' => _('Message Point Description'), 'class' => 'nav-link']); ?></li>
                                <?php endif; ?>
                            </ul>
                        </li>
                    <?php endif; ?>
                <?php endif; ?>
                <?php if ($this->Acl->has_permission('employees') or $this->Acl->has_permission('branches') or $this->Acl->has_permission('notices') or $this->Acl->has_permission('product_categories') or $this->Acl->has_permission('products')): ?>
                    <li class="nav-item has-sub dropdown<?php if (in_array($this->router->fetch_class(), ['employees', 'notices', 'error_reports', 'product_categories', 'products', 'branches', 'centers', 'permissions', 'roles', 'role_permissions', 'admin_permissions', 'jobs', 'visit_routes', 'order_edit_logs', 'counsel_edit_logs', 'enroll_pts'])): ?> active<?php endif; ?>">
                        <?php if ($this->Acl->has_permission('employees')): ?>
                            <?php echo anchor('employees', '<i class="material-icons">build</i><span>' . _('Admin Menu') . '</span>', ['title' => _('Admin Menu Description'), 'class' => 'nav-link dropdown-toggle']); ?>
                        <?php else: ?>
                            <?php if ($this->Acl->has_permission('notices')): ?>
                                <?php echo anchor('notices', '<i class="material-icons">view_headline</i><span>' . _('Notice Manage') . '</span>', ['title' => _('Notice Manage Description'), 'class' => 'nav-link dropdown-toggle']); ?>
                            <?php endif; ?>
                        <?php endif; ?>
                        <ul class="sub-menu">
                            <?php if ($this->Acl->has_permission('employees')): ?>
                                <li<?php if ($this->router->fetch_class() == 'employees'): ?> class="active"<?php endif; ?>>
                                    <?php echo anchor('employees', '<i class="material-icons">account_box</i><span>' . _('Employee Manage') . '</span>', ['title' => _('Employee Manage Description'), 'class' => 'nav-link']); ?>
                                </li>
                            <?php endif; ?>
                            <?php if ($this->Acl->has_permission('notices')): ?>
                                <li<?php if ($this->router->fetch_class() == 'notices'): ?> class="active"<?php endif; ?>><?php echo anchor('notices', '<i class="material-icons">view_headline</i><span>' . _('Notice Manage') . '</span>', ['title' => _('Notice Manage Description'), 'class' => 'nav-link']); ?></li>
                            <?php endif; ?>

                            <li<?php if ($this->router->fetch_class() == 'error_reports'): ?> class="active"<?php endif; ?>><?php echo anchor('error-reports', '<i class="material-icons">view_headline</i><span>' . _('Error Report') . '</span>', ['title' => _('Error Report Description'), 'class' => 'nav-link']); ?></li>


                            <li<?php if ($this->router->fetch_class() == 'counsel_requests'): ?> class="active"<?php endif; ?>><?php echo anchor('counsel_requests' , '<i class="material-icons">call_split</i><span>' . _('Counsel Request') . '</span>', ['title' => _('Counsel Request Description'), 'class' => 'nav-link']); ?></li>
                            <li<?php if ($this->router->fetch_class() == 'user_stop_requests'): ?> class="active"<?php endif; ?>><?php echo anchor('user_stop_requests' , '<i class="material-icons">call_split</i><span>' . _('User Stop Request') . '</span>', ['title' => _('User Stop Request Description'), 'class' => 'nav-link']); ?></li>

                            <?php if ($this->Acl->has_permission('products') or $this->Acl->has_permission('product_categories')): ?>
                                <?php if ($this->Acl->has_permission('products')): ?>
                                    <li<?php if ($this->router->fetch_class() == 'products'): ?> class="active"<?php endif; ?>><?php echo anchor('products', '<i class="material-icons">local_grocery_store</i><span>' . _('Product Manage') . '</span>', ['title' => _('Product Manage Description'), 'class' => 'nav-link']); ?></li>
                                <?php endif; ?>
                                <?php if ($this->Acl->has_permission('product_categories')): ?>
                                    <li<?php if ($this->router->fetch_class() == 'product_categories'): ?> class="active"<?php endif; ?>><?php echo anchor('product_categories', '<i class="material-icons">local_grocery_store</i><span>' . _('Product Category Manage') . '</span>', ['title' => _('Product Category Manage Description'), 'class' => 'nav-link']); ?></li>
                                <?php endif; ?>
                            <?php endif; ?>


                            <?php if ($this->Acl->has_permission('branches')): ?>
                                <?php if ($this->Acl->has_permission('centers')): ?>
                                    <li<?php if ($this->router->fetch_class() == 'branches'): ?> class="active"<?php endif; ?>><?php echo anchor('branches', '<i class="material-icons">call_split</i><span>' . _('Branch Manage') . '</span>', ['title' => _('Branch Manage Description'), 'class' => 'nav-link']); ?></li>
                                <?php else: ?>
                                    <li<?php if ($this->router->fetch_class() == 'branches'): ?> class="active"<?php endif; ?>><?php echo anchor('branches/view/' . $this->session->userdata('branch_id'), '<i class="material-icons">call_split</i><span>' . _('Branch Manage') . '</span>', ['title' => _('Branch Manage Description'), 'class' => 'nav-link']); ?></li>
                                <?php endif; ?>
                            <?php endif; ?>

                            <?php if ($this->session->userdata('center_id')): ?>
                                <?php if ($this->session->userdata('role_id') == 1): ?>
                                    <li<?php if ($this->router->fetch_class() == 'centers'): ?> class="active"<?php endif; ?>><?php echo anchor('centers', '<i class="material-icons">device_hub</i><span>' . _('Center Manage') . '</span>', ['title' => _('Center Manage Description'), 'class' => 'nav-link']); ?></li>
                                    <li<?php if ($this->router->fetch_class() == 'permissions'): ?> class="active"<?php endif; ?>><?php echo anchor('permissions', '<i class="material-icons">device_hub</i><span>' . _('Permission Manage') . '</span>', ['title' => _('Permission Manage Description'), 'class' => 'nav-link']); ?></li>
                                    <li<?php if ($this->router->fetch_class() == 'roles'): ?> class="active"<?php endif; ?>><?php echo anchor('roles', '<i class="material-icons">device_hub</i><span>' . _('Role Manage') . '</span>', ['title' => _('Role Manage Description'), 'class' => 'nav-link']); ?></li>
                                    <li<?php if ($this->router->fetch_class() == 'role_permissions'): ?> class="active"<?php endif; ?>><?php echo anchor('role-permissions', '<i class="material-icons">device_hub</i><span>' . _('Role-Permission Manage') . '</span>', ['title' => _('Role-Permission Manage Description'), 'class' => 'nav-link']); ?></li>
                                    <li<?php if ($this->router->fetch_class() == 'admin_permissions'): ?> class="active"<?php endif; ?>><?php echo anchor('admin-permissions', '<i class="material-icons">device_hub</i><span>' . _('Admin-Permission Manage') . '</span>', ['title' => _('Admin-Permission Manage Description'), 'class' => 'nav-link']); ?></li>
                                <?php else: ?>
                                    <li<?php if ($this->router->fetch_class() == 'jobs'): ?> class="active"<?php endif; ?>><?php echo anchor('jobs', '<i class="material-icons">device_hub</i><span>' . _('Job Manage') . '</span>', ['title' => _('Job Description'), 'class' => 'nav-link']); ?></li>
                                    <li<?php if ($this->router->fetch_class() == 'visit_routes'): ?> class="active"<?php endif; ?>><?php echo anchor('visit-routes', '<i class="material-icons">device_hub</i><span>' . _('Visit Route Manage') . '</span>', ['title' => _('Visit Route Description'), 'class' => 'nav-link']); ?></li>
                                    <li<?php if ($this->router->fetch_class() == 'centers'): ?> class="active"<?php endif; ?>><?php echo anchor('centers', '<i class="material-icons">device_hub</i><span>' . _('Center Manage') . '</span>', ['title' => _('Center Manage Description'), 'class' => 'nav-link']); ?></li>
                                <?php endif; ?>
                            <?php endif; ?>

                            <?php if ($this->session->userdata('role_id') <= 5): ?>
                                <li<?php if ($this->router->fetch_class() == 'order_block_excels'): ?> class="active"<?php endif; ?>><?php echo anchor('order-block-excels', '<i class="material-icons">fitness_center</i><span>' . _('Order Block Excel') . '</span>', ['title' => _('Order Block Exxel Description'), 'class' => 'nav-link']); ?></li>
                                <li<?php if ($this->router->fetch_class() == 'enroll_pts'): ?> class="active"<?php endif; ?>><?php echo anchor('enroll-pts', '<i class="material-icons">fitness_center</i><span>' . _('Enroll PT') . '</span>', ['title' => _('Enroll PT Manage Description'), 'class' => 'nav-link']); ?></li>
                                <li<?php if ($this->router->fetch_class() == 'no_fcs'): ?> class="active"<?php endif; ?>><?php echo anchor('no-fcs', '<i class="material-icons">fitness_center</i><span>' . _('No Fc') . '</span>', ['title' => _('No Fc Manage Description'), 'class' => 'nav-link']); ?></li>
                                <li<?php if ($this->router->fetch_class() == 'order_edit_logs'): ?> class="active"<?php endif; ?>><?php echo anchor('order-edit-logs', '<i class="material-icons">device_hub</i><span>' . _('Order Edit Log') . '</span>', ['title' => _('Order Edit Log Description'), 'class' => 'nav-link']); ?></li>
                                <li<?php if ($this->router->fetch_class() == 'account_edit_logs'): ?> class="active"<?php endif; ?>><?php echo anchor('account-edit-logs', '<i class="material-icons">device_hub</i><span>' . _('Account Edit Log') . '</span>', ['title' => _('Account Edit Log Description'), 'class' => 'nav-link']); ?></li>
                                <li<?php if ($this->router->fetch_class() == 'counsel_edit_logs'): ?> class="active"<?php endif; ?>><?php echo anchor('counsel-edit-logs', '<i class="material-icons">device_hub</i><span>' . _('Counsel Edit Log') . '</span>', ['title' => _('Counsel Edit Log Description'), 'class' => 'nav-link']); ?></li>    
                            <?php endif; ?>
                        </ul>
                    </li>
                <?php endif; ?>
            </ul>
        </nav>
    </div>
</header>
<!-- //header -->

<div style="width:100%">
    <nav class="sub_nav">
        <ul>
            <li <?php if ($type == 'event'): ?>class="curr"<?php endif ?>><a
                        href="search.php?type=event"><?php echo _('Search by Period') ?></a></li>
            <li <?php if ($type == 'status'): ?>class="curr"<?php endif ?>><a
                        href="search.php?type=status"><?php echo _('Search by Status') ?></a></li>
            <li <?php if ($type == 'field'): ?>class="curr"<?php endif ?>><a
                        href="search.php?type=field"><?php echo _('Search by Item') ?></a></li>
        </ul>
    </nav>
    <?php include __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'inc' . DIRECTORY_SEPARATOR . 'message.php' ?>
    <article class="regist_w">
        <div class="title_a">
            <h1 class="title"><?php echo _('User Search') ?></h1>
        </div>
        <form action="" id="formSearchMember" class="search_form">
            <input type="hidden" name="type" value="<?php echo $type ?>"/>
            <div class="search">
                <?php if ($type == 'event'): ?>
                    <div class="row">
                        <label for="search_type_group"><?php echo _('Check classification') ?></label>
                        <select id="search_type_group" name="search_type_group" style="margin-right:20px;">
                            <option value="status1"<?php if (isset($search_type_group)): ?><?php if ($search_type_group == 'status1'): ?> selected="selected"<?php endif ?><?php else: ?> selected="selected"<?php endif ?>><?php echo _('All') ?></option>
                            <option value="status2"<?php if (isset($search_type_group)): ?><?php if ($search_type_group == 'status2'): ?> selected="selected"<?php endif ?><?php endif ?>><?php echo _('New Registration') ?></option>
                            <option value="status3"<?php if (isset($search_type_group)): ?><?php if ($search_type_group == 'status3'): ?> selected="selected"<?php endif ?><?php endif ?>><?php echo _('Registration') ?></option>
                            <option value="status4"<?php if (isset($search_type_group)): ?><?php if ($search_type_group == 'status4'): ?> selected="selected"<?php endif ?><?php endif ?>><?php echo _('Attendance') ?></option>
                            <option value="status5"<?php if (isset($search_type_group)): ?><?php if ($search_type_group == 'status5'): ?> selected="selected"<?php endif ?><?php endif ?>><?php echo _('Enroll Start') ?></option>
                            <option value="status6"<?php if (isset($search_type_group)): ?><?php if ($search_type_group == 'status6'): ?> selected="selected"<?php endif ?><?php endif ?>><?php echo _('Enroll Finish') ?></option>
                            <option value="status7"<?php if (isset($search_type_group)): ?><?php if ($search_type_group == 'status7'): ?> selected="selected"<?php endif ?><?php endif ?>><?php echo _('Rent Start') ?></option>
                            <option value="status8"<?php if (isset($search_type_group)): ?><?php if ($search_type_group == 'status8'): ?> selected="selected"<?php endif ?><?php endif ?>><?php echo _('Rent Finish') ?></option>
                        </select>
                    </div>
                    <div class="row">
                        <label for="start_date" class="title"><?php echo _('Check Period') ?></label>
                        <div style="float:left;position:relative;width:165px">
                            <input type="text" id="start_date" name="start_date"
                                   class="datepicker"<?php if (isset($display_start_date)): ?> value="<?php echo $display_start_date ?>"<?php endif ?> />
                        </div>
                        <span style="display:block;float:left">~</span>
                        <div style="float:left;position:relative;width:165px">
                            <input type="text" id="end_date" name="end_date"
                                   class="datepicker"<?php if (isset($display_end_date)): ?> value="<?php echo $display_end_date ?>"<?php endif ?> />
                        </div>
                        <div style="float:left;margin-left:50px">
                            <label><input type="radio" name="date_p"
                                          value="0"<?php if ($date_p == 0): ?> checked="checked"<?php endif ?> /><?php echo _('Today') ?>
                            </label>
                            <label><input type="radio" name="date_p"
                                          value="7"<?php if ($date_p == 7): ?> checked="checked"<?php endif ?> /><?php echo _('Period Week') ?>
                            </label>
                            <label><input type="radio" name="date_p"
                                          value="30"<?php if ($date_p == 30): ?> checked="checked"<?php endif ?> /><?php echo _('A month') ?>
                            </label>
                            <label><input type="radio" name="date_p"
                                          value="90"<?php if ($date_p == 90): ?> checked="checked"<?php endif ?> /><?php echo _('Three month') ?>
                            </label>
                            <label><input type="radio" name="date_p"
                                          value="180"<?php if ($date_p == 180): ?> checked="checked"<?php endif ?> /><?php echo _('Half a year') ?>
                            </label>
                            <label><input type="radio" name="date_p"
                                          value="365"<?php if ($date_p == 365): ?> checked="checked"<?php endif ?> /><?php echo _('One Year') ?>
                            </label>
                            <label><input type="radio" name="date_p"
                                          value="all"<?php if (!strcmp($date_p, 'all')): ?> checked="checked"<?php endif ?> /><?php echo _('The whole period') ?>
                            </label>
                        </div>
                    </div>
                    <input type="submit" class="btn_search" value="검색"/><?php if ($search): ?><a href="search.php"
                                                                                                 style="margin-left:20px"><?php echo _('Turn off Search') ?></a><?php endif ?>
                <?php elseif ($type == 'status'): ?>
                    <div class="row">
                        <label><input type="radio" name="status_type"
                                      value="course"<?php if ($status_type == 'course'): ?> checked="checked"<?php endif ?> /><?php echo _('By course') ?>
                        </label>
                        <label><input type="radio" name="status_type"
                                      value="payment"<?php if ($status_type == 'payment'): ?> checked="checked"<?php endif ?> /><?php echo _('By payment status') ?>
                        </label>
                    </div>
                    <div class="row">
                        <select id="status_course_select" name="course_id"
                                style="margin-right:20px;<?php if ($status_type == 'payment'): ?>display:none;<?php endif ?>"
                                <?php if ($status_type == 'payment'): ?>disabled="disabled"<?php endif ?>>
                            <?php if ($status_count): ?>
                                <?php foreach ($status_list as $value): ?>
                                    <option value="<?php echo $value['course_idx'] ?>"<?php if (isset($course_idx)): ?><?php if ($course_idx == $value['course_idx']): ?> selected="selected"<?php endif ?><?php endif ?>><?php echo $value['course_name'] ?></option>
                                <?php endforeach ?>
                            <?php endif ?>
                        </select>
                        <select id="status_payment_select" name="payment_id"
                                style="margin-right:20px;<?php if ($status_type == 'course'): ?>display:none;<?php endif ?>"
                                <?php if ($status_type == 'course'): ?>disabled="disabled"<?php endif ?>>
                            <option value="1"<?php if ($payment_idx == 1): ?> selected="selected"<?php endif ?>><?php echo _('In full') ?></option>
                            <option value="2"<?php if ($payment_idx == 2): ?> selected="selected"<?php endif ?>><?php echo _('Unpaid') ?></option>
                            <option value="3"<?php if ($payment_idx == 3): ?> selected="selected"<?php endif ?>><?php echo _('Refund') ?></option>
                            <option value="4"<?php if ($payment_idx == 4): ?> selected="selected"<?php endif ?>><?php echo _('Transfer positive number') ?></option>
                            <option value="5"<?php if ($payment_idx == 5): ?> selected="selected"<?php endif ?>><?php echo _('Cash payment') ?></option>
                            <option value="6"<?php if ($payment_idx == 6): ?> selected="selected"<?php endif ?>><?php echo _('Credit card payment') ?></option>
                        </select>
                        <input type="submit" class="btn_search" value="검색"/><?php if ($search): ?><a
                                href="search.php?type=status"
                                style="margin-left:20px"><?php echo _('Turn off Search') ?></a><?php endif ?>
                    </div>
                <?php else: ?>
                    <div class="row" style="clear:both;width:100%;padding-top:10px">
                        <label for="search_type"><?php echo _('Search Field') ?></label>
                        <select id="search_type" name="search_type" style="margin-right:20px;">
                            <option value="name"<?php if ($search): ?><?php if ($search_type == 'name'): ?> selected="selected"<?php endif ?><?php endif ?>><?php echo _('User Name') ?></option>
                            <option value="card_no"<?php if ($search): ?><?php if ($search_type == 'card_no'): ?> selected="selected"<?php endif ?><?php endif ?>><?php echo _('Member code') ?></option>
                            <option value="phone"<?php if ($search): ?><?php if ($search_type == 'phone'): ?> selected="selected"<?php endif ?><?php endif ?>><?php echo _('Phone') ?></option>
                        </select>
                        <label for="search_word"><?php echo _('Search Word') ?></label>
                        <input type="text" id="search_word"
                               name="search_word"<?php if ($search): ?> value="<?php echo $search_word ?>"<?php endif ?>
                               required="required"/>
                        <input type="submit" class="btn_search" value="검색"/><?php if ($search): ?><a
                                href="search.php?type=field"
                                style="margin-left:20px"><?php echo _('Turn off Search') ?></a><?php endif ?>
                    </div>
                <?php endif ?>
            </div>
            <div style="clear:both;float:left;padding:5px 0;width:100%">
                <?php echo sprintf(_('There Are %d User'), $list_count) ?>
                <p style="float:right;margin:0">
                    <select id="perpage" name="perpage">
                        <option value="5"<?php if ($perPage == 5): ?> selected="selected"<?php endif ?>><?php echo _('View Five') ?></option>
                        <option value="10"<?php if ($perPage == 10): ?> selected="selected"<?php endif ?>><?php echo _('View Ten') ?></option>
                        <option value="20"<?php if ($perPage == 20): ?> selected="selected"<?php endif ?>><?php echo _('View Twenty') ?></option>
                        <option value="30"<?php if ($perPage == 30): ?> selected="selected"<?php endif ?>><?php echo _('View Thirty') ?></option>
                    </select>
                </p>
            </div>
        </form>
    </article>
    <article class="list_w">
        <div class="sc_box" id="member_list">
            <table class="standard box nowrap" style="margin-top:20px">
                <colgroup>
                    <col style="width:50px"/>
                    <col style="width:90px"/>
                    <col style="width:90px"/>
                    <col style="width:50px"/>
                    <col/>
                    <col/>
                    <col/>
                    <col/>
                    <col/>
                    <col/>
                    <col/>
                    <col style="width:90px"/>
                    <col style="width:90px"/>
                </colgroup>
                <thead>
                <tr>
                    <th><input id="check_all" type="checkbox"/></th>
                    <th><?php echo _('User Name') ?></th>
                    <th><?php echo _('Member code') ?></th>
                    <th><?php echo _('Gender') ?></th>
                    <th><?php echo _('Birthday') ?></th>
                    <th><?php echo _('Phone') ?></th>
                    <th><?php echo _('Registed Date') ?></th>
                    <th><?php echo _('Course Contents') ?></th>
                    <th><?php echo _('User Trainer') ?></th>
                    <th><?php echo _('User FC') ?></th>
                    <th><?php echo _('Total PT Count') ?>/<?php echo _('Use Quantity') ?>
                        /<?php echo _('Remaining number') ?></th>
                    <th><?php echo _('Amount received') ?></th>
                    <th><?php echo _('Status') ?></th>
                </tr>
                </thead>
                <tbody>
                <?php if ($data['total']): ?>
                    <?php foreach ($data['list'] as $index => $value): ?>
                        <tr>
                            <td>
                                <input name="id[]" value="<?php echo $value['idx'] ?>" type="checkbox">
                            </td>
                            <td><?php echo $value['name'] ?></td>
                            <td><?php echo $value['card_no'] ?></td>
                            <td><?php
                                if (isset($value['gender'])) {
                                    if ($value['gender'] == 'M') {
                                        echo '남';
                                    }

                                    if ($value['gender'] == 'F') {
                                        echo '여';
                                    }
                                }
                                ?></td>
                            <td><?php if (empty($value['birthday'])): ?>-<?php else: ?><?php echo $value['birthday'] ?><?php endif ?></td>
                            <td class="phone"><?php echo $value['phone'] ?></td>
                            <td><?php echo date('Y-m-d', strtotime($value['regtime'])) ?></td>
                            <td><?php if (empty($value['course_name'])): ?>-<?php else: ?><?php echo $value['course_name'] ?><?php endif ?></td>
                            <td><?php if (empty($value['trainer'])): ?>-<?php else: ?><?php echo $value['trainer'] ?><?php endif ?></td>
                            <td><?php if (empty($value['fc'])): ?>-<?php else: ?><?php echo $value['fc'] ?><?php endif ?></td>
                            <td><?php if (empty($value['pt_count'])): ?>-<?php else: ?><?php echo $value['pt_count'] ?><?php endif ?></td>
                            <td><?php if (empty($value['pay_total'])): ?>-<?php else: ?><?php echo number_format($value['pay_total']) ?><?php endif ?></td>
                            <td>
                                <?php if ($value['payment_status'] == 'Unpaid'): ?>
                                    <span style="color:red"><?php echo _('Unpaid') ?></span>
                                <?php else: ?>
                                    <span><?php echo _('In full') ?></span>
                                <?php endif ?>
                            </td>
                        </tr>
                    <?php endforeach ?>
                <?php else: ?>
                    <tr>
                        <td colspan="13"><?php echo _('No such member') ?></td>
                    </tr>
                <?php endif ?>
                </tbody>
            </table>
            <div class="sl_pagination">
                <?php
                if ($list_count) {
                    echo $paginator;
                }
                ?>
            </div>
        </div>

        <ul class="members_input">
            <li><input type="hidden"/><span style="font-weight:bold"><?php echo _('None') ?></span></li>
        </ul>
        <ul style="float:left;width:100%;padding:10px 0 20px">
            <li><a href="/message/new.php?type=sms" target="_blank" class="btn_popup" title="메세지 보내기"
                   style="margin-left:0"><img src="/resource/images/common/btn_file_download.png"
                                              alt=""><?php echo _('Send SMS') ?></a></li>
            <li><a href="/message/new.php?type=push" target="_blank" class="btn_popup" title="메세지 보내기"
                   style="margin-left:10px"><img src="/resource/images/common/btn_file_download.png"
                                                 alt=""><?php echo _('Send Push') ?></a></li>
        </ul>
    </article>
</div>

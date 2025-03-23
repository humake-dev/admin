<div id="employees" class="container">
<input type="hidden" id="is_fc" value="<?php if (empty($data['content']['is_fc'])): ?>0<?php else: ?>1<?php endif; ?>">
    <div class="row">
        <?php include __DIR__.DIRECTORY_SEPARATOR.'aside.php'; ?>
        <div class="col-12 col-lg-7 col-xl-8 col-xxl-9">
            <?php echo $Layout->Element('employees/nav'); ?>
            <div class="row">
            <h2 class="col-12"></h2>
            <article id="employee_user_search" class="col-12">
                <div class="card">
                    <div class="card-header">
                    <h3>검색</h3>
                    <div class="float-right buttons">
                        <i class="material-icons"><?php if ($this->session->userdata('employee_search_open')): ?>keyboard_arrow_up<?php else: ?>keyboard_arrow_down<?php endif ?></i>
                    </div>
                    </div>
                    <div class="card-body"<?php if (!$this->session->userdata('employee_search_open')): ?> style="display:none"<?php endif ?>>
                    <?php echo form_open('',array('method'=>'get')); ?>
                    <div class="form-row">
                            <div class="col-12 col-lg-3 form-group">
                            <label for="s_status"><?php echo _('Status'); ?></label>
    <?php
      $options = ['' => _('All'), 'available' => _('Available'), 'expired' => _('Expired')];

      $select = $this->input->get('status');
      echo form_dropdown('status', $options, $select, ['id' => 's_status', 'class' => 'form-control']);
    ?>
                            </div>
                            <div class="col-12 col-lg-3 form-group">          
                            <label for="s_period"><?php echo _('Period'); ?></label>
                                    <?php
           $options = ['' => _('All'),'-' => '-', '1' => _('Under Period 3Month'), '3' => _('More Period 3Month').'~'._('Under Period 6Month'), '6' => _('More Period 6Month').'~'._('Under Period Year'), '12' => _('More Period Year').'~'._('Under Period 2Year'), '24' => _('More Period 2Year')];
           $p_select = $this->input->get('period');
           echo form_dropdown('period', $options, $p_select, ['id' => 's_period', 'class' => 'form-control']);
                                ?>                           
                            </div>
                        </div>
                        <input type=submit value="<?php echo _('Search'); ?>" class="btn btn-primary">
                        <?php echo form_close(); ?>
                    </div>
                </div>
            </article>
            <article class="col-12">
                <div class="card">
                    <div class="card-body">
                    <?php echo form_open('',array('id'=>'eu_search_form')); ?>
                        <input type="hidden" id="employee_user_count" value="<?php echo $data['total']; ?>"/>
                        <h2 class="float-left"><?php echo _('Employee User List'); ?></h2>
                        <div class="float-right">
                            <p class="summary">
                                <span id="list_count" style="display:none"><?php echo $data['total']; ?></span>
                                <?php echo sprintf(_('There Are %d Employee Users'), $data['total']); ?>
                            </p>
                        </div>
                        <table id="employee_user_table" class="table">
                            <colgroup>
                                <col>
                                <col>
                                <col>
                                <col>
                                <col style="width:165px">
                                <col style="width:50px">
                            </colgroup>
                            <thead class="thead-default">
                            <tr>
                                <th><?php echo _('Increment Number'); ?></th>
                                <th><?php echo _('User Name'); ?> / <?php echo _('Phone'); ?></th>
                                <th class="text-right"><?php echo _('Status'); ?></th>
                                <th class="text-right"><?php echo _('Period'); ?></th>
                                <th class="text-center"><?php echo _('Transaction Date'); ?></th>
                                <th class="text-right"><input id="check_all" name="all" value="1" type="checkbox"<?php if(!empty($data['all'])): ?> checked="checked"<?php endif ?>></th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php if (empty($data['total'])): ?>
                                <tr>
                                    <td colspan="8"><?php echo _('No Data'); ?></td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($data['list'] as $index => $value):
                                        $insert_quantity = '-';

                                        if(!empty($value['period'])) {
                                            $insert_quantity = $value['period'];
                                        }

                                            if(empty($value['available_count'])) {
                                                $status = '<span class="text-warning">'._('Expired').'</span>';
                                            } else {
                                                $status = '<span class="text-success">'._('Available').'</span>';
                                            }

                                ?>
                                    <tr>
                                        <td><?php echo number_format($data['total'] - ($data['page']) - $index); ?></td>
                                        <td><?php echo anchor('/view/'.$value['id'], $value['name'].' / '. get_hyphen_phone($value['phone']), ['target' => '_blank']); ?></td>
                                        <td class="text-right"><?php echo $status; ?></td>
                                        <td class="text-right"><?php echo $insert_quantity; ?></td>
                                        <td class="text-right"><?php echo $value['transaction_date'] ?></td>
                                        <td class="text-right">
                                        <label style="display:block">
                                        <input name="user_id[]" value="<?php echo $value['id']; ?>" type="checkbox"<?php if(!empty($data['all'])): ?> checked="checked"<?php endif ?>>
                                        </label>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                            </tbody>
                        </table>
                        <div class="form-row">
                            <?php if(!empty($data['search']) and empty($data['total'])): ?>   

                            <?php else: ?>
                                <div class="form-group col-6 col-lg-4">
                                    <label id="no_select_label"<?php if(!empty($data['all'])): ?> style="display:none"<?php endif ?>>
                                        회원을 선택해주세요
                                    </label>
                                    <label id="select_label"<?php if(empty($data['all'])): ?> style="display:none"<?php endif ?>>
                                        선택된 <span id="eu_total"><?php if(empty($data['all'])): ?>0<?php else: ?><?php echo $data['total'] ?><?php endif ?></span>명을
                                    </label>
                                    <?php
                                    $select = set_value('change_fc_id', '');

                                    if ($data['content']['is_trainer']) {
                                        $options = ['' => _('Not Change'), 0 => _('Set To Not Insert')];
                                    } else {
                                        $options = [0 => _('Set To Not Insert')];
                                    }

                                    if ($data['fc']['total']) {
                                        foreach ($data['fc']['list'] as $value) {
                                            if ($data['content']['id'] == $value['id']) {
                                                continue;
                                            }
                                            $options[$value['id']] = sprintf(_('Users Fc Change To %s'), $value['name']);
                                        }
                                    }

                                    echo form_dropdown('change_fc_id', $options, $select, ['class' => 'form-control']);
                                    ?>
                                </div>
                            <input type="hidden" id="eu_search" name="eu_search" value="<?php if(empty($data['search'])): ?>0<?php else: ?>1<?php endif ?>">
                            <div id="eu_select">
                            </div>       
                            <div id="eu_unselect">
                            </div>
                            <div class="form-group col-6 col-lg-8 text-right">
                                <br />
                                
                                <label class="">
                                <select id="select-oe" name="select_oe" class="form-control" <?php if($data['total']<=10): ?> style="display:none"<?php endif ?>>
                                    <option value=""<?php if($data['total']<=10): ?> selected="selected"<?php endif ?>>미선택</option>
                                    <option value="odd"<?php if(!empty($data['select_oe'])): ?><?php if($data['select_oe']=='odd'): ?> selected="selected"<?php endif ?><?php endif ?>>홀수 페이지 선택</option>
                                    <option value="even"<?php if(!empty($data['select_oe'])): ?><?php if($data['select_oe']=='even'): ?> selected="selected"<?php endif ?><?php endif ?>>짝수 페이지 선택</option>
                                </select>
                                </label>
                                
                            <label class=""><input id="check_real_all" name="real_all" value="1" type="checkbox"<?php if(!empty($data['real_all'])): ?> checked="checked"<?php endif ?>> 전체선택</label>
                            </div>
                            <?php if(!empty($data['search'])): ?>
                            <input type="hidden" name="status" value="<?php echo $this->input->get_post('status') ?>">
                            <input type="hidden" name="period" value="<?php echo $this->input->get_post('period') ?>">                            
                            <?php endif ?>
                            <div class="form-group col-12">
                                <input type=submit value="<?php echo _('Change'); ?>" class="btn btn-primary">
                            </div>
                        </div>
                        <?php endif ?>
                        <div class="sl_pagination"></div>
                        <?php echo form_close(); ?>
                    </div>
                </div>
            </article>
            </div>
        </div>
    </div>
</div>

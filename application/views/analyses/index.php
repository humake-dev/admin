<div id="analyses" class="container">
    <div class="row">
        <div class="col-12">
            <ul class="nav nav-pills">
                <li class="nav-item"><?php echo anchor('analyses?type=period', _('By Period'), array('class' => get_nav_class('period', $this->input->get('type'), 'period'))); ?></li>
                <li class="nav-item"><?php echo anchor('analyses?type=month', _('Monthly'), array('class' => get_nav_class('month', $this->input->get('type'), 'period'))); ?></li>   
            </ul>
        </div>
        <div class="col-12 col-md-6 col-xl-4">
            <article class="card">
                <div class="card-header"><?php echo _('Setting search criteria'); ?></div>
                    <div class="card-body">
                        <div class="top-body">
                            <div class="choice_period"<?php if ($data['type'] == 'month'): ?> style="min-height:160px;display:none"<?php endif; ?>>
                                <?php echo form_open('analyses', array('method' => 'get')); ?>
                                    <input type="hidden" name="type" value="period" />
                                    <div class="form-group">
                                        <label for="start_date"><?php echo _('Check Period'); ?></label>
                                        <div class="form-row">
                                            <div class="col-12">
                                            <div class="form-check form-check-inline">
  <label class="form-check-label">
    <input class="form-check-input" type="radio" name="date_p" value="0" <?php echo set_radio('date_p', '0'); ?>> <?php echo _('Today'); ?>
  </label>
</div>
<div class="form-check form-check-inline">
  <label class="form-check-label">
    <input class="form-check-input" type="radio" name="date_p" value="7" <?php echo set_radio('date_p', '7'); ?>> <?php echo _('Period Week'); ?>
  </label>
</div>
<div class="form-check form-check-inline">
  <label class="form-check-label">
    <input class="form-check-input" type="radio" name="date_p" value="30" <?php echo set_radio('date_p', '30', true); ?>> <?php echo _('A month'); ?>
  </label>
</div>
<div class="form-check form-check-inline">
  <label class="form-check-label">
    <input class="form-check-input" type="radio" name="date_p" value="90" <?php echo set_radio('date_p', '90'); ?>> <?php echo _('Three month'); ?>
  </label>
</div>
<div class="form-check form-check-inline">
  <label class="form-check-label">
    <input class="form-check-input" type="radio" name="date_p" value="180" <?php echo set_radio('date_p', '180'); ?>> <?php echo _('Half a year'); ?>
  </label>
</div>
<div class="form-check form-check-inline">
  <label class="form-check-label">
    <input class="form-check-input" type="radio" name="date_p" value="365" <?php echo set_radio('date_p', '365'); ?>> <?php echo _('One Year'); ?>
  </label>
</div>
<div class="form-check form-check-inline">
  <label class="form-check-label">
    <input class="form-check-input" type="radio" name="date_p" value="all"<?php echo set_radio('date_p', 'all'); ?>> <?php echo _('The whole period'); ?>
  </label>
</div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <div class="input-group-prepend input-daterange">
                                            <?php

                                            if (isset($data['default_start_date'])) {
                                                $start_date_value = $data['default_start_date'];
                                            } else {
                                                $start_date_value = $data['start_date'];
                                            }

                                            echo form_input(array(
                                                'name' => 'start_date',
                                                'value' => $start_date_value,
                                                'id' => 'a_start_date',
                                                'class' => 'form-control datepicker',
                                            ));
                                            ?>
                                            <div class="input-group-text">~</div>
                                            <?php

                                            if (isset($data['default_end_date'])) {
                                                $end_date_value = $data['default_end_date'];
                                            } else {
                                                $end_date_value = $data['end_date'];
                                            }

                                            echo form_input(array(
                                                'name' => 'end_date',
                                                'value' => $end_date_value,
                                                'id' => 'a_end_date',
                                                'class' => 'form-control datepicker',
                                            ));
                                            ?>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <?php echo form_submit('', _('Search'), array('class' => 'btn btn-primary')); ?>
                                    </div>
                                <?php echo form_close(); ?>
                            </div>
                            <div class="row choice_month"<?php if ($data['type'] == 'period'): ?> style="min-height:160px;display:none"<?php endif; ?>>
                                <?php
                                echo form_open('analyses', array('method' => 'get', 'class' => 'col-12 col-md-4  text-right'));
                                $check_date_obj = new DateTime('now', $search_data['timezone']);
                                $a = $check_date_obj->diff(new DateTime($data['year'].'-'.$data['month'], $search_data['timezone']));
                                if ($a->format('%r%m')):
                                ?>
                                    <input type="hidden" name="type" value="month" />
                                    <input type="hidden" name="year" value="<?php if (isset($data['prev_year'])): ?><?php echo $data['prev_year']; ?><?php endif; ?>" />
                                    <input type="hidden" name="month" value="<?php if (isset($data['prev_month'])): ?><?php echo $data['prev_month']; ?><?php endif; ?>" />
                                    <input type="submit" value="&lt;" class="btn prev" />
                                <?php endif; ?>
                                <?php echo form_close(); ?>
                                <div class="col-12 col-md-4 text-center">
                                    <strong class="month"><?php echo $data['year']._('Year'); ?> <?php echo $data['month']._('Month'); ?></strong>
                                </div>
                                <?php
                                $now_obj = new DateTime('now', $search_data['timezone']);
                                $check_date_obj = new DateTime($data['year'].'-'.$data['month'], $search_data['timezone']);
                                $a = $check_date_obj->diff($data['date_obj']);
                                if ($a->format('%r%m') >= 0 and $check_date_obj->format('Y-m') != $now_obj->format('Y-m')):
                                echo form_open('analyses', array('method' => 'get', 'class' => 'col-12 col-md-4'));
                                ?>
                                    <input type="hidden" name="type" value="month" />
                                    <input type="hidden" name="year" value="<?php if (isset($data['next_year'])): ?><?php echo $data['next_year']; ?><?php endif; ?>" />
                                    <input type="hidden" name="month" value="<?php if (isset($data['next_month'])): ?><?php echo $data['next_month']; ?><?php endif; ?>" />
                                    <input type="submit" value="&gt;" class="btn next" />
                                <?php echo form_close(); ?>
                                <?php endif; ?>
                            </div>
                          </div>
                        </div>
                      </article>
      </div>

      <div class="col-12 col-md-6 col-xl-4">
				<article class="card">
					<div class="card-header">
						<h2>GYM</h2>
            <!-- <?php echo anchor('', '+ '._('More'), array('class' => 'more')); ?> -->
					</div>
					<div class="card-body">
            <div class="top-body">
						<dl>
              <dt><?php if ($this->input->get('type') == 'month'): ?><?php echo $data['month']._('Month'); ?><?php endif; ?> 매출</dt>
							<dd><?php echo number_format($data['total_sales']); ?><?php echo _('Currency'); ?></dd>
						</dl>
						<dl>
							<dt><?php if ($this->input->get('type') == 'month'): ?><?php echo $data['month']._('Month'); ?><?php endif; ?>회원권 등록인원(유료)</dt>
							<dd><?php echo number_format($data['total_primary']); ?><?php echo _('Count People'); ?><!--(수업 1 / OT 0) --></dd>
						</dl>
						<dl>
							<dt><?php if ($this->input->get('type') == 'month'): ?><?php echo $data['month']._('Month'); ?><?php endif; ?>PT등록인원(유료)</dt>
							<dd><?php echo number_format($data['total_pts']); ?><?php echo _('Count People'); ?><!--(진행 1 / 휴먼 0)--></dd>
						</dl>
          </div>
					</div>
				</article>
      </div>
      
      <div class="col-12 col-md-6 col-xl-4">
        <article class="card">
					<div class="card-header">
						<h2>3개월간 월별 매출</h2>
            <!-- <?php echo anchor('', '+ '._('More'), array('class' => 'more')); ?> -->
					</div>
					<div class="card-body">
          <?php if ($data['sales_count']['total']): ?>
					<div id="columnchart_values" class="chart-body"></div>
          <?php else: ?>
          <div class="chart-body"><?php echo _('No Data'); ?></div>
          <?php endif; ?>
				</div>
      </article>
    </div>
    <div class="col-12 col-md-6 col-xl-4">
      <article class="card">
        <div class="card-header">
						<h2>회원권 등록비율</h2>
            <!-- <?php echo anchor('', '+ '._('More'), array('class' => 'more')); ?> -->
					</div>
					<div class="card-body">
            <?php if ($data['new_re_ratio']['total']): ?>
							<div id="donutchart2" class="chart-body"></div>
            <?php else: ?>
              <div class="chart-body"><?php echo _('No Data'); ?></div>
            <?php endif; ?>
				</div>
      </article>
    </div>
      <div class="col-12 col-md-6 col-xl-4">
      <article class="card">
        <div class="card-header">
						<h2>등록구분</h2>
            <!-- <?php echo anchor('', '+ '._('More'), array('class' => 'more')); ?> -->
					</div>
					<div class="card-body">
            <?php if ($data['course_count']['total']): ?>
							<div id="donutchart4" class="chart-body"></div>
            <?php else: ?>
              <div class="chart-body"><?php echo _('No Data'); ?></div>
            <?php endif; ?>
					</div>
        </article>
      </div>

      <div class="col-12 col-md-6 col-xl-4">
      <article class="card">
        <div class="card-header">
						<h2>결제형태별 액수</h2>
            <!-- <?php echo anchor('', '+ '._('More'), array('class' => 'more')); ?> -->
					</div>
					<div class="card-body">
            <?php if ($data['payment_type']['total']): ?>
            <div id="donutchart3" class="chart-body"></div>
            <?php else: ?>
              <div class="chart-body"><?php echo _('No Data'); ?></div>
            <?php endif; ?>
					</div>
        </article>
      </div>
      <div class="col-12 col-md-6 col-xl-4">
        <article class="card">
          <div class="card-header">
						<h2>3개월간 월별입장회원</h2>
            <!-- <?php echo anchor('', '+ '._('More'), array('class' => 'more')); ?> -->
					</div>
					<div class="card-body">
            <?php if ($data['entrance_month']['total']): ?>
            <div id="columnchart_values2" class="chart-body"></div>
            <?php else: ?>
              <div class="chart-body"><?php echo _('No Data'); ?></div>
            <?php endif; ?>
        </div>
      </article>
    </div>
      <div class="col-12 col-md-6 col-xl-4">
      <article class="card">
        <div class="card-header">
						<h2>신규회원권 등록자 연령비율</h2>
            <!-- <?php echo anchor('', '+ '._('More'), array('class' => 'more')); ?> -->
					</div>
					<div class="card-body">
            <?php if ($data['age_count']['total']): ?>
							<div id="donutchart1" class="chart-body"></div>
            <?php else: ?>
              <div class="chart-body"><?php echo _('No Data'); ?></div>
            <?php endif; ?>
					</div>
        </article>
      </div>
      <div class="col-12 col-md-6 col-xl-4">
        <article class="card">
          <div class="card-header">
						<h2>신규회원권 등록자 성비</h2>
            <!-- <?php echo anchor('', '+ '._('More'), array('class' => 'more')); ?> -->
					</div>
					<div class="card-body">
            <?php if ($data['gender_count']['total']): ?>
							<div id="donutchart" class="chart-body"></div>
            <?php else: ?>
              <div class="chart-body"><?php echo _('No Data'); ?></div>
            <?php endif; ?>
					</div>
        </article>
      </div>
</div>
</div>
<script>

<?php if ($data['new_re_ratio']['total']): ?>
var course_new_re_ratio=[
  ['등록유형', '구성'],
  <?php foreach ($data['new_re_ratio']['result'] as $value): ?>
   ['<?php echo $value['resist_type']; ?>',<?php echo $value['count']; ?>],
 <?php endforeach; ?>
]
<?php endif; ?>

<?php if ($data['sales_count']['total']): ?>
var sales_data= [
   ['<?php echo _('Month'); ?>', '매출',{ role: "style" }],
   <?php foreach ($data['sales_count']['result'] as $value): ?>
 	  ['<?php echo $value['month']; ?>',<?php echo $value['sales']; ?>,"color:blue"],
 	<?php endforeach; ?>
];
<?php endif; ?>

<?php if ($data['course_count']['total']): ?>
var course_data=[
  ["강의", "신청자수", { role: "style" } ],
	<?php foreach ($data['course_count']['result'] as $value): ?>
	  ['<?php echo $value['title']; ?>',<?php echo $value['count']; ?>,'red'],
	<?php endforeach; ?>
];
<?php endif; ?>

<?php if ($data['payment_type']['total']): ?>
var payment_type_data=[
  ['결제', '결제액', { role: "style" } ],
  <?php foreach ($data['payment_type']['result'] as $value):
    if ($value['count'] == 0) {
        continue;
    }

  ?>
    <?php if ($value['count'] < 0) {
      $pt_color = 'red';
  } else {
      $pt_color = 'blue';
  }
    ?>
	  ['<?php echo $value['payment_type']; ?>',<?php echo $value['count']; ?>,'<?php echo $pt_color; ?>'],
	<?php endforeach; ?>
];
<?php endif; ?>

<?php if ($data['entrance_month']['total']): ?>
var entrance_month_data=[
  ['<?php echo _('Month'); ?>', "입장회원", { role: "style" } ],
	<?php foreach ($data['entrance_month']['result'] as $value): ?>
	  ['<?php echo $value['entrance']; ?>',<?php echo $value['count']; ?>,'blue'],
	<?php endforeach; ?>
];
<?php endif; ?>

<?php if ($data['age_count']['total']): ?>
var age_data=[
  ['연령대', '구성'],
	<?php foreach ($data['age_count']['result'] as $value): ?>
	  ['<?php echo $value['age_group']; ?>',<?php echo $value['count']; ?>],
	<?php endforeach; ?>
];
<?php endif; ?>

<?php if ($data['gender_count']['total']): ?>
var gender_data=[
  ['성별', '구성'],
<?php foreach ($data['gender_count']['result'] as $value): ?>
  ['<?php echo $value['gender']; ?>',<?php echo $value['count']; ?>],
<?php endforeach; ?>
];
<?php endif; ?>


</script>

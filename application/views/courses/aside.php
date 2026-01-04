<aside id="course_aside" class="col-12 col-md-5 col-lg-4 col-xl-3">
	<h2><?php echo _('Course') ?></h2>
	<?php if(empty($data['category']['total'])): ?>
	<p><?php echo _('No Data') ?>	
	<?php else: ?>	
	<?php foreach ($data['category']['list'] as $index=>$category): ?>
	<article class="card">
		<h3 class="card-header"><a href="/courses?category_id=<?php echo $category['id'] ?>"> <?php echo $category['title'] ?></a></h3>
		<div class="card-body<?php if ($data['category']['current_id'] == $category['id']): ?> show<?php else: ?> hide<?php endif ?>">		
		<?php if ($data['category']['current_id'] == $category['id']): ?>	
		<?php if ($data['total']): ?>
		<ul class="list-group">
			<?php foreach ($data['list'] as $value): ?>
			<li class="list-group-item<?php if (isset($data['content'])): ?><?php if ($data['content']['id']==$value['id']): ?> active<?php endif ?><?php endif ?>">
			<?php echo anchor('courses/edit/'.$value['id'], $value['title']) ?>
			</li>
			<?php endforeach ?>
		</ul>
		<?php endif ?>
		<?php echo form_open('courses/add') ?>
			<input type="hidden" name="product_category_id" value="<?php echo $category['id']?>">
			<div class="input-group">
				<input type="text" name="title" value="<?php echo set_value('title') ?>" class="form-control" placeholder="신규 강습명을 입력해주세요." />
				<span class="input-group-btn">
					<input type="submit" class="btn btn-primary" value="<?php echo _('Submit') ?>" />
				</span>
			</div>
		<?php echo form_close() ?>			
		<?php endif ?>
		</div>	
	</article>
	<?php endforeach ?>
	<?php endif ?>
</aside>


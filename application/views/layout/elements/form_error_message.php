<div id="sl_messages" class="col-12">
	<?php if(validation_errors() != false): ?>
	<div class="alert alert-danger" role="alert"><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
	<?php echo validation_errors() ?>
	</div>
	<?php endif ?>
	<?php echo $Layout->element('message') ?>
</div>

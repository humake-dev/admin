<?php echo doctype('html5')."\n"; ?>
<html lang="ko">
<head>
<?php echo meta($meta); ?>
<title><?php echo $title_for_layout; ?></title>
<link href="<?php echo $this->config->item('img_file_path'); ?>favicon.ico" type="image/x-icon" rel="shortcut icon">
<?php echo $style_for_layout; ?>
<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
<meta name="author" content="Sleeping-Lion">
</head>
<body>
  	<?php echo $Layout->element('header'); ?>
  	<div id="mom">
  		<div id="main">
        <div id="sub_main">
          <div class="container">
          <div class="row">
            <?php echo $Layout->Element('message'); ?>
          </div>
          </div>
  			     <?php echo $contents_for_layout; ?>
        </div>
  		</div>
  	</div>
  	<?php echo $Layout->element('footer'); ?>
    <div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-hidden="true">
    </div>

    <div aria-live="polite" aria-atomic="true" style="position:absolute;top:80px;right:0">
  <!-- Position it -->
  <div style="position: absolute; top: 0; right: 0;min-width:300px">

    <div id="default-toast" class="toast" role="alert" aria-live="assertive" aria-atomic="true" data-delay="6500">
  <div class="toast-header">
    <strong class="mr-auto"><?php echo _('Attendance'); ?></strong>
    <small class="text-muted"></small>
    <button type="button" class="ml-2 mb-1 close" data-dismiss="toast" aria-label="Close">
      <span aria-hidden="true">&times;</span>
    </button>
  </div>
  <div class="toast-body">
    <?php echo anchor('/entrances', '', array('class' => 'text')); ?>
  </div>
</div>
</div>
</div>
  	<?php echo "\n".$script_for_layout."\n"; ?>
  </body>
</html>

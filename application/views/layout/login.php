<?php echo doctype('html5')."\n" ?>
<html lang="ko">
<head>
  <?php echo meta($meta); ?>
  <title><?php echo $title_for_layout; ?></title>
  <link href="<?php echo $this->config->item('img_file_path') ?>favicon.ico" type="image/x-icon" rel="shortcut icon"/>
  <?php echo $style_for_layout ?>
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <meta name="author" content="Sleeping-Lion" />
  <body id="login_page"> 
  <div id="mom">
    <div id="main" class="container-fluid">
      <div class="row">
        <?php echo $contents_for_layout ?>
      </div>
    </div>
  </div>
  <?php echo $Layout->element('footer') ?>
  <?php echo "\n".$script_for_layout."\n" ?>
</body>
</html>

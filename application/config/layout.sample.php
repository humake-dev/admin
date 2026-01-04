<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}
/*
 * config file for layout.
 * @location ./application/config/layout.php
 */



/*
 * javascripts files folder url path
 * this will be added before javascript filenames
 * $config['js_file_path'] = '/js';
 */
if(ENVIRONMENT=='production') {
  $config['js_file_path'] = 'https://d35dfeqve1uqoo.cloudfront.net/javascripts/';
} else {
  $config['js_file_path'] = base_url().'assets/javascripts/';
}




/*
 * css files folder url path
 * this will be added before css filenames
 * $config['css_file_path'] = '/css';
 */
if(ENVIRONMENT=='production') {
  $config['css_file_path'] = 'https://d35dfeqve1uqoo.cloudfront.net/stylesheets/';
} else {
  $config['css_file_path'] = base_url().'assets/stylesheets/';
}


if(ENVIRONMENT=='production') {
  $config['img_file_path'] = 'https://d35dfeqve1uqoo.cloudfront.net/images/';
} else {
  $config['img_file_path'] = base_url().'assets/images/';
}

/*
 * layout folder path.
 * it must be under view folder
 */
$config['layout_folder'] = 'layout';


/*
 * elements are fractions of layout saved in defferent php files
 * it must be under layout folder
 */
$config['element_folder'] = 'elements';


/*
 * default layout configaration.
 *
 * 'meta'                   = meta for layout. See html helper meta() for details
 * 'title_for_layout'   = If auto_title set to false this will use as title
 * 'title_separator'    = for auto_tile
 * 'layout'                 = name of the default layout
 *
 */
$config['default'] = array(
    'meta' => array(
        array(
            'name' => 'Content-type',
            'content' => 'text/html; charset=utf-8',
            'type' => 'equiv'
        )
    ),
    'title_for_layout' => 'Myfit erp',
    'title_separator'=>'|',
    'layout' => 'default',
);

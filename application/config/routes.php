<?php

defined('BASEPATH') or exit('No direct script access allowed');

/*
| -------------------------------------------------------------------------
| URI ROUTING
| -------------------------------------------------------------------------
| This file lets you re-map URI requests to specific controller functions.
|
| Typically there is a one-to-one relationship between a URL string
| and its corresponding controller class/method. The segments in a
| URL normally follow this pattern:
|
|	example.com/class/method/id/
|
| In some instances, however, you may want to remap this relationship
| so that a different class/function is called than the one
| corresponding to the URL.
|
| Please see the user guide for complete details:
|
|	https://codeigniter.com/user_guide/general/routing.html
|
| -------------------------------------------------------------------------
| RESERVED ROUTES
| -------------------------------------------------------------------------
|
| There are three reserved routes:
|
|	$route['default_controller'] = 'welcome';
|
| This route indicates which controller class should be loaded if the
| URI contains no data. In the above example, the "welcome" class
| would be loaded.
|
|	$route['404_override'] = 'errors/page_missing';
|
| This route will tell the Router which controller/method to use if those
| provided in the URL cannot be matched to a valid route.
|
$route['translate_uri_dashes'] = FALSE;
|
| This is not exactly a route, but allows you to automatically route
| controller and method names that contain dashes. '-' isn't a valid
| class or method name character, so it requires translation.
| When you set this option to TRUE, it will replace ALL dashes in the
| controller and method URI segments.
|
| Examples:	my-controller/index	-> my_controller/index
|		my-controller/my-method	-> my_controller/my_method
*/
$route['default_controller'] = 'home';
$route['404_override'] = '';
$route['translate_uri_dashes'] = true;

$route['view/(:num)'] = 'home/view/$1';

$route['enrolls/transfer/(:num)'] = 'enroll_transfers/add/$1';
$route['enrolls/stop/(:num)'] = 'enroll_stops/add/$1';
$route['enrolls/resume/(:num)'] = 'enroll_stops/resume/$1';

$route['rents/transfer/(:num)'] = 'rent_transfers/add/$1';
$route['rents/stop/(:num)'] = 'rent_stops/add/$1';
$route['rents/resume/(:num)'] = 'rent_stops/resume/$1';

$route['rent-sws/transfer/(:num)'] = 'rent_sw_transfers/add/$1';
$route['rent-sws/stop/(:num)'] = 'rent_sw_stops/add/$1';
$route['rent-sws/resume/(:num)'] = 'rent_sw_stops/resume/$1';

$route['orders/resume/(:num)'] = 'order_stops/resume/$1';
$route['orders/end/(:num)'] = 'order_ends/add/$1';

$route['users/stop/(:num)'] = 'user_stops/add/$1';
$route['users/resume/(:num)'] = 'user_stops/resume/$1';
$route['users/transfer/(:num)'] = 'user_transfers/add/$1';

$route['branches/change/(:num)'] = 'branch_changes/change/$1';

$route['accounts/view-deleted/([a-zA-Z]+)'] = 'accounts/view_deleted/$1';

$route['logout'] = 'login/logout';

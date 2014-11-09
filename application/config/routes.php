<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
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
|	http://codeigniter.com/user_guide/general/routing.html
|
| -------------------------------------------------------------------------
| RESERVED ROUTES
| -------------------------------------------------------------------------
|
| There area two reserved routes:
|
|	$route['default_controller'] = 'welcome';
|
| This route indicates which controller class should be loaded if the
| URI contains no data. In the above example, the "welcome" class
| would be loaded.
|
|	$route['404_override'] = 'errors/page_missing';
|
| This route will tell the Router what URI segments to use if those provided
| in the URL cannot be matched to a valid route.
|

 *  */
$route['vynosy/organizace/(:any)/(:num)'] = "org/revenues/$1/$2";
$route['vynosy/organizace/(:any)'] = "org/revenues/$1";
$route['naklady/organizace/(:any)/(:num)'] = "org/costs/$1/$2";
$route['naklady/organizace/(:any)'] = "org/costs/$1";
$route['organizace/(:any)/(:num)'] = "org/view/$1/$2";
$route['organizace/(:any)'] = "org/index/$1";

$route['polozka/mesto/(:any)/(:num)/(:num)'] = "city/itemClass/$1/$2/$3";
$route['polozka/mesto/(:any)/(:num)'] = "city/itemClass/$1/$2";
$route['paragraf/mesto/(:any)/(:num)/(:num)'] = "city/paragraphGroup/$1/$2/$3";
$route['paragraf/mesto/(:any)/(:num)'] = "city/paragraphGroup/$1/$2";
$route['prijmy/mesto/(:any)/(:num)'] = "city/revenues/$1/$2";
$route['prijmy/mesto/(:any)'] = "city/revenues/$1";
$route['vydaje/mesto/(:any)/(:num)'] = "city/costs/$1/$2";
$route['vydaje/mesto/(:any)'] = "city/costs/$1";
$route['mesto/(:any)/(:num)'] = "city/view/$1/$2";
$route['mesto/(:any)'] = "city/index/$1";

$route['admin/vzhled/(:any)'] = "admin/appearance/colors/$1";
$route['admin/logo/(:any)'] = "admin/appearance/logo/$1";
$route['admin/zakladni/(:any)'] = "admin/settings/index/$1";

$route['angular/(:any)'] = "../angular/index.html";

$route['default_controller'] = "welcome";
$route['404_override'] = '';


/* End of file routes.php */
/* Location: ./application/config/routes.php */
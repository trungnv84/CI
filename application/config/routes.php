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
*/

$route['default_controller'] = "home";
$route['404_override'] = '';

$route['(?i)thumb_(:any)_(:num)/(:any)(.jpg|.jpeg|.png|.gif)(:any)?'] = 'thumb/index';

################################################################################################

$route['mua-:any-p(:num)(' . REWRITE_SUFFIX . ')?'] = 'Product_Controller/addCart/$1';
$route[':any-cp(:num)([-/]trang-(:num))?(' . REWRITE_SUFFIX . ')?'] = 'Product_Controller/listing/$1/$3';
$route[':any-p(:num)(' . REWRITE_SUFFIX . ')?'] = 'Product_Controller/detail/$1';

$route[':any-ca(:num)([-/]trang-(:num))?(' . REWRITE_SUFFIX . ')?'] = 'Article_Controller/listing/$1/$3';
$route[':any-a(:num)(' . REWRITE_SUFFIX . ')?'] = 'Article_Controller/detail/$1';

$route['gio-hang(' . REWRITE_SUFFIX . ')?'] = 'Product_Controller/cart';
$route['mua-hang(' . REWRITE_SUFFIX . ')?'] = 'Product_Controller/customer';

$route['dang-nhap(' . REWRITE_SUFFIX . ')?'] = 'User_Controller/login';
$route['dang-xuat(' . REWRITE_SUFFIX . ')?'] = 'User_Controller/logout';
$route['dang-ky(' . REWRITE_SUFFIX . ')?'] = 'User_Controller/register';

################################################################################################

$route['admin/login'] = 'User_Controller/adminLogin';
$route['admin/logout'] = 'User_Controller/adminLogout';

$route['admin/user(/(:num))?'] = 'User_Controller/manager$1';
$route['admin/addUser'] = 'User_Controller/add';
$route['admin/editUser'] = 'User_Controller/edit';
$route['admin/saveUser'] = 'User_Controller/save';
$route['admin/saveUserAndClose'] = 'User_Controller/saveAndClose';
$route['admin/saveUserAndAdd'] = 'User_Controller/saveAndAdd';
$route['admin/publishUser'] = 'User_Controller/publish';
$route['admin/unpublishUser'] = 'User_Controller/unpublish';
$route['admin/deleteUser'] = 'User_Controller/delete';

$route['admin/category(/(:num))?(/(:num))?'] = 'Category_Controller/manager$1$2';
$route['admin/addCategory(/(:num))?'] = 'Category_Controller/add$1';
$route['admin/editCategory(/(:num))?'] = 'Category_Controller/edit$1';
$route['admin/saveCategory(/(:num))?'] = 'Category_Controller/save$1';
$route['admin/saveCategoryAndClose(/(:num))?'] = 'Category_Controller/saveAndClose$1';
$route['admin/saveCategoryAndAdd(/(:num))?'] = 'Category_Controller/saveAndAdd$1';
$route['admin/publishCategory(/(:num))?'] = 'Category_Controller/publish$1';
$route['admin/unpublishCategory(/(:num))?'] = 'Category_Controller/unpublish$1';
$route['admin/deleteCategory(/(:num))?'] = 'Category_Controller/delete$1';
$route['admin/reorderCategory(/(:num))?'] = 'Category_Controller/reorder$1';

$route['admin/product(/(:num))?'] = 'Product_Controller/manager$1';
$route['admin/addProduct'] = 'Product_Controller/add';
$route['admin/editProduct'] = 'Product_Controller/edit';
$route['admin/saveProduct'] = 'Product_Controller/save';
$route['admin/saveProductAndClose'] = 'Product_Controller/saveAndClose';
$route['admin/saveProductAndAdd'] = 'Product_Controller/saveAndAdd';
$route['admin/publishProduct'] = 'Product_Controller/publish';
$route['admin/unpublishProduct'] = 'Product_Controller/unpublish';
$route['admin/deleteProduct'] = 'Product_Controller/delete';
$route['admin/reorderProduct'] = 'Product_Controller/reorder';
$route['admin/branchProduct'] = 'Product_Controller/branch';

$route['admin/article(/(:num))?'] = 'Article_Controller/manager$1';
$route['admin/addArticle'] = 'Article_Controller/add';
$route['admin/editArticle'] = 'Article_Controller/edit';
$route['admin/saveArticle'] = 'Article_Controller/save';
$route['admin/saveArticleAndClose'] = 'Article_Controller/saveAndClose';
$route['admin/saveArticleAndAdd'] = 'Article_Controller/saveAndAdd';
$route['admin/publishArticle'] = 'Article_Controller/publish';
$route['admin/unpublishArticle'] = 'Article_Controller/unpublish';
$route['admin/deleteArticle'] = 'Article_Controller/delete';
//$route['admin/reorderArticle'] = 'Article_Controller/reorder';
$route['admin/branchArticle'] = 'Article_Controller/branch';

$route['admin/banner(/(:num))?'] = 'Banner_Controller/manager$1';
$route['admin/addBanner'] = 'Banner_Controller/add';
$route['admin/editBanner'] = 'Banner_Controller/edit';
$route['admin/saveBanner'] = 'Banner_Controller/save';
$route['admin/saveBannerAndClose'] = 'Banner_Controller/saveAndClose';
$route['admin/saveBannerAndAdd'] = 'Banner_Controller/saveAndAdd';
$route['admin/publishBanner'] = 'Banner_Controller/publish';
$route['admin/unpublishBanner'] = 'Banner_Controller/unpublish';
$route['admin/deleteBanner'] = 'Banner_Controller/delete';
$route['admin/reorderBanner'] = 'Banner_Controller/reorder';
$route['admin/branchBanner'] = 'Banner_Controller/branch';

$route['admin/config(/(:num))?'] = 'Config_Controller/manager$1';
$route['admin/editConfig'] = 'Config_Controller/edit';
$route['admin/saveConfig'] = 'Config_Controller/save';
$route['admin/saveConfigAndClose'] = 'Config_Controller/saveAndClose';

$route['application/third_party/tinymce/jscripts/tiny_mce/plugins/imagemanager/(:any)'] = 'tinymce/imagemanager';

/* End of file routes.php */
/* Location: ./application/config/routes.php */

$_file = __DIR__ . DS . DOMAIN_ALIAS . DS . 'routes.php';
if (file_exists($_file)) require_once $_file;
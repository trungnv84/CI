<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');

date_default_timezone_set('Asia/Bangkok');

define('DS', DIRECTORY_SEPARATOR);

define('DOMAIN_ALIAS', preg_replace('/\W/', '_', isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : $_SERVER['SERVER_NAME']));

define('SYSFOLDER', $system_path);

define('APPFOLDER', $application_folder);

define('TIME_NOW', time());

$_file = __DIR__ . DS . DOMAIN_ALIAS . DS . 'constants.php';
if (file_exists($_file)) require_once $_file;

/*
|--------------------------------------------------------------------------
| File and Directory Modes
|--------------------------------------------------------------------------
|
| These prefs are used when checking and setting modes when working
| with the file system.  The defaults are fine on servers with proper
| security, but you may wish (or even need) to change the values in
| certain environments (Apache running a separate process for each
| user, PHP under CGI with Apache suEXEC, etc.).  Octal values should
| always be used to set the mode correctly.
|
*/
define('FILE_READ_MODE', 0644);
define('FILE_WRITE_MODE', 0666);
define('DIR_READ_MODE', 0755);
define('DIR_WRITE_MODE', 0777);

/*
|--------------------------------------------------------------------------
| File Stream Modes
|--------------------------------------------------------------------------
|
| These modes are used when working with fopen()/popen()
|
*/

define('FOPEN_READ', 'rb');
define('FOPEN_READ_WRITE', 'r+b');
define('FOPEN_WRITE_CREATE_DESTRUCTIVE', 'wb'); 		// truncates existing file data, use with care
define('FOPEN_READ_WRITE_CREATE_DESTRUCTIVE', 'w+b'); 	// truncates existing file data, use with care
define('FOPEN_WRITE_CREATE', 'ab');
define('FOPEN_READ_WRITE_CREATE', 'a+b');
define('FOPEN_WRITE_CREATE_STRICT', 'xb');
define('FOPEN_READ_WRITE_CREATE_STRICT', 'x+b');

/*############################################################*/

if(!defined('SITE_NAME')) define('SITE_NAME', 'Điện hoa');

if(!defined('SITE_KEYWORDS')) define('SITE_KEYWORDS', 'Điện hoa');

if(!defined('SITE_DESCRIPTION')) define('SITE_DESCRIPTION', 'Điện hoa');

if(!defined('USER_NEED_ACTIVE')) define('USER_NEED_ACTIVE', TRUE);

if(!defined('LOGIN_AFTER_REGISTER')) define('LOGIN_AFTER_REGISTER', TRUE);

if(!defined('FROM_EMAIL_REGISTER')) define('FROM_EMAIL_REGISTER', 'admin@domain.abc');

if(!defined('FROM_NAME_REGISTER')) define('FROM_NAME_REGISTER', 'domain.abc');

if(!defined('DEFAULT_THEME')) define('DEFAULT_THEME', 'default');

if(!defined('CACHE_VIEW')) define('CACHE_VIEW', FALSE);	//zzz chua lam xong cache //Cho phep sua cai nay bang cau hinh???

if(!defined('ASSETS_OPTIMIZATION')) define('ASSETS_OPTIMIZATION', 15);

if(!defined('ASSETS_VERSION')) define('ASSETS_VERSION', '1');

if(!defined('REWRITE_SUFFIX')) define('REWRITE_SUFFIX', '.html');

if(!defined('USE_SESSION_TOKEN')) define('USE_SESSION_TOKEN', TRUE);

if(!defined('SESSION_TOKEN_NAME')) define('SESSION_TOKEN_NAME', '_token');

if(!defined('CAPTCHA_FOR_REGISTER')) define('CAPTCHA_FOR_REGISTER', FALSE);

if(!defined('CAPTCHA_FOR_LOGIN')) define('CAPTCHA_FOR_LOGIN', FALSE);
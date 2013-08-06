<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/*
|--------------------------------------------------------------------------
| Default email config
|--------------------------------------------------------------------------
|
| Set default email config
|
*/

$config['protocol'] = 'mail';
$config['mailpath'] = '/usr/sbin/sendmail';
$config['mailtype'] = 'html';
$config['charset'] = 'utf-8';
$config['wordwrap'] = false;

$config['protocol']			= 'smtp';	// mail/sendmail/smtp
$config['smtp_host']		= 'smtp.gmail.com';		// SMTP Server.  Example: mail.earthlink.net
$config['smtp_user']		= 'trungnguyenvan@vccorp.vn';		// SMTP Username
$config['smtp_pass']		= 'alskdjdjskal123';		// SMTP Password
$config['smtp_port']		= '465';		// SMTP Port
$config['smtp_crypto']		= 'ssl';		// SMTP Encryption. Can be null, tls or ssl.


/* End of file email.php */
/* Location: ./application/config/email.php */

$_file = __DIR__ . DS . DOMAIN_ALIAS . DS . 'email.php';
if (file_exists($_file)) require_once $_file;
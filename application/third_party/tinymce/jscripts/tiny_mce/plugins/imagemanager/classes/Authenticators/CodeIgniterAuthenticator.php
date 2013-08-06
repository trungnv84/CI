<?php
/**
 * This class handles MCImageManager CodeIgniterAuthenticator stuff.
 *
 * @package CodeIgniterAuthenticator
 */
class Moxiecode_CodeIgniterAuthenticator extends Moxiecode_ManagerPlugin {
	/**#@+
	 * @access public
	 */

	/**
	 * CodeIgniterAuthenciator contructor.
	 */
    /*function __construct() {
        $this->Moxiecode_CodeIgniterAuthenticator();
    }

	function Moxiecode_CodeIgniterAuthenticator() {
	}*/

	/**
	 * Gets called on a authenication request. This method should check sessions or simmilar to
	 * verify that the user has access to the backend.
	 *
	 * This method should return true if the current request is authenicated or false if it's not.
	 *
	 * @param ManagerEngine $man ManagerEngine reference that the plugin is assigned to.
	 * @return bool true/false if the user is authenticated.
	 */
	function onAuthenticate(&$man) {

        global $CodeIgniter;

        //print_r($CodeIgniter);

        if($CodeIgniter->user->hasPermit('admin_login')) {

            $config =& $man->getConfig();

            // Switch path
            /*$config['filesystem.path'] = $_SESSION[$pathKey];*/

            // Switch root
            if(defined('SITE_NAME') && SITE_NAME)
                $config['filesystem.rootpath'] .= DS . SITE_NAME;
            if($config['filesystem.rootpath'] && !is_dir($config['filesystem.rootpath']))
				mkdir($config['filesystem.rootpath'], 0777, true);

            $user = $CodeIgniter->user->userName();
            foreach ($config as $key => $value) {
                // Skip replaceing {$user} in true/false stuff
                if ($value === true || $value === false)
                    continue;

                $value = str_replace('${user}', $user, $value);
                $config[$key] = $value;
            }

            return true;

        } else return false;

	}
}

// Add plugin to MCManager
$man->registerPlugin("CodeIgniterAuthenticator", new Moxiecode_CodeIgniterAuthenticator());
?>
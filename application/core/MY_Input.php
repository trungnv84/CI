<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class MY_Input extends CI_Input {

	function _clean_input_keys($str)
	{
		/*if (!defined('ALLOW_ALL_KEY') && ! preg_match("/^[a-z0-9:_\/-]+$/i", $str))
		{
			exit('Disallowed Key Characters.');
		}*/

		// Clean UTF-8 if supported
		if (UTF8_ENABLED === TRUE)
		{
			$str = $this->uni->clean_string($str);
		}

		return $str;
	}

}

/* End of file Input.php */
/* Location: ./system/core/Input.php */


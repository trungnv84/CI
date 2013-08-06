<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');
/**
 * CodeIgniter Extra
 *
 * An open source application development framework for PHP 5.1.6 or newer
 *
 * @package        CodeIgniter Extra
 * @author        Nguyễn Văn Trung
 * @copyright    Copyright (c) 1984 - 2012, Nguyễn Văn Trung.
 * @license        commercial
 * @link        http://trungnv.com
 * @since        Version 1.0
 * @filesource
 */

// ------------------------------------------------------------------------

/**
 * Shopping Cart Class
 *
 * @package        CodeIgniter
 * @subpackage    Libraries
 * @category    CI_Session
 * @author        Nguyễn Văn Trung
 * @link        http://codeigniter.com/user_guide/libraries/cart.html
 */
//require_once BASEPATH . 'libraries' . DS . 'Session.php';
class MY_Session extends CI_Session
{

    private $sess_write = false;

    /**
     * Session Constructor
     *
     * The constructor runs the session routines automatically
     * whenever the class is instantiated.
     */
    public function __construct($params = array())
    {
        parent::__construct($params);
        register_shutdown_function('MY_Session::_sess_write', $this);
    }

    // --------------------------------------------------------------------

    /**
     * Add or change data in the "userdata" array
     *
     * @access	public
     * @param	mixed
     * @param	string
     * @return	void
     */
    function set_userdata($newdata = array(), $newval = '')
    {
        if (is_string($newdata))
        {
            $newdata = array($newdata => $newval);
        }

        if (count($newdata) > 0)
        {
            foreach ($newdata as $key => $val)
            {
                $this->userdata[$key] = $val;
            }
        }

        $this->sess_write = true;
    }

    // --------------------------------------------------------------------

    /**
     * Delete a session variable from the "userdata" array
     *
     * @access	array
     * @return	void
     */
    function unset_userdata($newdata = array())
    {
        if (is_string($newdata))
        {
            $newdata = array($newdata => '');
        }

        if (count($newdata) > 0)
        {
            foreach ($newdata as $key => $val)
            {
                unset($this->userdata[$key]);
            }
        }

        $this->sess_write = true;
    }

    public function sess_write()
    {
        if($this->sess_write) {
            $this->sess_write = false;
            parent::sess_write();
        }
    }

    public static function _sess_write($session)
    {
        $session->sess_write();
    }
}
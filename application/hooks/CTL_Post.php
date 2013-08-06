<?php
class CTL_Post
{
    public function CtlPost()
    {
        $CI =& get_instance();
        if(isset($CI->session))
            $CI->session->sess_write();
    }
}
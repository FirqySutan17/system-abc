<?php
defined('BASEPATH') OR exit('No direct script access allowed');

function has_permission($key)
{
    $CI =& get_instance();

    if (!isset($CI->session)) {
        return false;
    }

    $permissions = $CI->session->userdata('permissions');

    if (!is_array($permissions)) {
        return false;
    }

    return in_array($key, $permissions, true);
}
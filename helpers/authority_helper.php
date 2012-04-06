<?php if ( ! defined('BASEPATH')) exit ('No direct script access allowed');

if ( ! function_exists('can') && ! function_exists('cannot'))
{
    function can($action, $resource, $resource_val = null)
    {
        return Authority::can($action, $resource, $resource_val);
    }

    function cannot($action, $resource, $resource_val = null)
    {
        return Authority::cannot($action, $resource, $resource_val);
    }
}

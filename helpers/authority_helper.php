<?php if ( ! defined('BASEPATH')) exit ('No direct script access allowed');

if ( ! function_exists('can') && ! function_exists('cannot'))
{
    function can($action, $resource)
    {
        return Authority::can($action, $resource);
    }

    function cannot($action, $resource)
    {
        return ! cannot($action, $resource);
    }
}

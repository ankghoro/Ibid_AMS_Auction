<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

if (!function_exists('base_domain'))
{
    function base_domain($base_url)
    {
        $explode = explode( '.', str_replace(["http://","/"], "",$base_url));
        $index = count($explode) - 1;
        $base_domain = '.'.$explode[$index-1].'.'.$explode[$index];
        return $base_domain;
    }   
}

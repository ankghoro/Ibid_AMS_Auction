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

if (!function_exists('indonesian_date'))
{
    function indonesian_date($timestamp = '', $date_format = 'Y m d', $suffix = 'WIB')
    {
       $months = array (
           1 =>   'Januari',
           'Februari',
           'Maret',
           'April',
           'Mei',
           'Juni',
           'Juli',
           'Agustus',
           'September',
           'Oktober',
           'November',
           'Desember'
       );
       $date_format = preg_replace ("/S/", "", $date_format);
       $date = date ($date_format, strtotime($timestamp));
       $split = explode(' ', $date);
       $reformdate = $split[2] . ' ' . $months[ (int)$split[1] ] . ' ' . $split[0];
       return $reformdate;
    }   
}
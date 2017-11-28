<?php
defined('BASEPATH') OR exit('No direct script access allowed');

if ( ! function_exists('load_header'))
{
	function load_header()
	{
		return array(
			'assets_url'      => '//'.$_SERVER['SERVER_NAME'] . '/assets/'
			);
	}

	function load_menu() 
	{
		$data['menu']  = [
			'Stock' => [
					'icon' => 'fa-car',
					'link' => 'javascript:void(0);',
				],
			'Schedule' => [
					'icon' => 'fa-calendar',
					'link' => '/',
				],
			];

		return $data;
	}
}
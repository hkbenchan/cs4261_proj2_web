<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * CodeIgniter
 *
 * An open source application development framework for PHP 5.1.6 or newer
 *
 * @package		CodeIgniter
 * @author		ExpressionEngine Dev Team
 * @copyright	Copyright (c) 2008 - 2011, EllisLab, Inc.
 * @license		http://codeigniter.com/user_guide/license.html
 * @link		http://codeigniter.com
 * @since		Version 1.0
 * @filesource
 */

// ------------------------------------------------------------------------

/**
 * CodeIgniter Fetch Info Helpers
 *
 * @package		CodeIgniter
 * @subpackage	Helpers
 * @category	Helpers
 * @author		Ben Chan 
 * @link		
 */

// ------------------------------------------------------------------------

/**
 * Reload
 *
 * Lets you reload the data from api and write them to cache, boosting the performance
 *
 * @access	public
 * @param	string
 * @return	boolean	depends on fetching operation
 */
if ( ! function_exists('fetch_rotten_tomato'))
{
	function fetch_rotten_tomato($item = 0, $page_limit = 20, $page_no = 1)
	{
		$rotten_tomato_index = array(
			0 => '',
			1 => 'movie_box',
			2 => 'movie_theaters',
			3 => 'movie_opening',
			4 => 'movie_upcoming',
			5 => 'dvd_top_rent',
			6 => 'dvd_current',
			7 => 'dvd_new',
			8 => 'dvd_upcoming',
			9 => '',
		);
		
		$rotten_tomato_key = 'hzbubqr3gxev62fg8p3pwts6';
		
		if (!is_numeric($item))
			return false;
			
		$item = $rotten_tomato_index[(int)$item];
		$CI =& get_instance();

		$CI->load->spark('curl/1.2.1');
		if ( $item == '' )
			return false;
		
		elseif ( $item == 'movie_box')
		{
			
			$result = $CI->curl->simple_get("http://api.rottentomatoes.com/api/public/v1.0/lists/movies/box_office.json",
			array(
				"limit" => $page_limit,
				"country" => "us",
				"apikey" => $rotten_tomato_key,
			));
			
		}
		elseif ( $item == 'movie_theaters' )
		{
			$result = $CI->curl->simple_get("http://api.rottentomatoes.com/api/public/v1.0/lists/movies/in_theaters.json",
			array(
				"page_limit" => $page_limit,
				"page" => $page_no,
				"country" => "us",
				"apikey" => $rotten_tomato_key,
			));
			
		}
		elseif ( $item == 'movie_opening')
		{
			$result = $CI->curl->simple_get("http://api.rottentomatoes.com/api/public/v1.0/lists/movies/opening.json",
			array(
				"limit" => $page_limit,
				"country" => "us",
				"apikey" => $rotten_tomato_key,
			));
			
		}
		
		
		if ($result == false) { return false; }
		// store it somewhere on the cache
		return json_decode($result, true);
	}
}


/* End of file fetchinfo_helper.php */
/* Location: ./application/helpers/fetchinfo_helper.php */
<?php
// This file is not called from WordPress. We don't like that.
! defined( 'ABSPATH' ) and exit;

/**
 * CSS Parser
 *
 * @package   wpthms
 * @author		wpthms
 * @copyright	Copyright ( c ) wpthms
 * @since     1.0
 */
class YT_CSS_Parser {
	
	protected $cssstr;
	/**
	* Constructor function for PHP5
	*
	*/
	public function __construct()  	{
	   $this->css = array();
	   $this->cssstr = "";
	}

/**
    * Parses an entire CSS file
    *
    * @param mixed $filename CSS File to parse
    */
	public function parse_file($file_name)
	{
		$fh = yt_file_open( $file_name, "r") or die( "Error opening file $file_name" );
		$css_str = yt_file_read($fh, filesize($file_name));
		yt_file_close($fh);
		return( $this->parse_css( $css_str ) );
	}

	/**
    * Parses a CSS string
    *
    * @param string $css_str CSS to parse
    */
	public function parse_css($css_str)
	{
		$this->cssstr = $css_str;
		$this->result = array();

		
		$css_str = trim(preg_replace('/\s*\{[^}]*\}/', '', $css_str));
		
		preg_match_all('/(?<=\.)(.*?)(?=\:before)/',$css_str,$match);
		$this->result = isset($match[0]) ? $match[0] : '';
		
		return $this->result;
		
	}
	
	public function get_string_between($string, $start, $end){
		$string = " ".$string;
		$ini = strpos($string,$start);
		if ($ini == 0) return "";
		$ini += strlen($start);
		$len = strpos($string,$end,$ini) - $ini;
		return substr($string,$ini,$len);
	}


}
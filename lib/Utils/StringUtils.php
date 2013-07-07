<?php

namespace lib\Utils;

class StringUtils {

	/*public static function removeAccents($str, $from_enc = 'auto')
	{
		$str = mb_convert_encoding($str, 'HTML-ENTITIES', $from_enc);
		$str = preg_replace(
			array('/&szlig;/','/&(..)lig;/','/&([aouAOU])uml;/','/&(.)[^;]*;/'),
			array('ss',"$1","$1".'e',"$1"), $str);
		return $str;
	} */  
	
	public static function slugify($str)
	{
		$str = mb_convert_encoding($str, 'ascii', 'auto');
		$str = preg_replace(array('/[^\w\s-+]/', '/[\s-_+]+/', '/^-|-$/'),
							array('', '-', ''), $str);
		return $str;
	}

	public static function truncate($str, $length, $append = '…')
	{
		if(strlen($str) > $length) {
			return substr($str, 0, $length).$append;
		} else {
			return $str;
		}
	}
	
	public static function make_html_paragraphs($str)
	{
		$str = trim($str, " \r\n");
		return preg_replace(array('/\r\n/', '/\n{2,}/', '/\n/', '#</p><p>#', '/^/', '/$/'),
							array("\n", '</p><p>', "<br/>\n", "</p>\n<p>", '<p>', '</p>'), $str);
	}
}
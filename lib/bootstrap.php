<?php

use lib\Main;

define('BASEPATH', dirname(__DIR__));
//const BASEPATH = dirname(__DIR__);

function __autoload($class)
{
    is_file( $file = BASEPATH.DIRECTORY_SEPARATOR.str_replace('\\', DIRECTORY_SEPARATOR, $class).'.php')
		and require_once($file);
}

function main()
{
	$config = array_merge(
		parse_ini_file(BASEPATH.'/lib/Dependency_Injection/default_config.ini', false),
		parse_ini_file(BASEPATH.'/app/config.ini.php', false)
	);
	
	switch ($config['debug.display_errors']) {
		case 'always':
		case ('localhost' == $config['debug.display_errors'] && ('localhost' == $_SERVER['SERVER_NAME'] || '127.0.0.1' == $_SERVER['SERVER_NAME'])):
			ini_set('display_errors', 1);
			break;
		
		case 'never':
		default:
			ini_set('display_errors', 0);
			break;
	}
	error_reporting((int)$config['debug.error_reporting']);
	
	if ($config['debug.log_file']) {
		ini_set('log_errors', 1);
		ini_set('error_log', BASEPATH.DIRECTORY_SEPARATOR.$config['debug.log_file']);
	} else {
		ini_set('log_errors', 0);
	}
	
	if ($config['environment.timezone']) {
		date_default_timezone_set($config['environment.timezone']);	
	}

	new Main($config);
}
main();

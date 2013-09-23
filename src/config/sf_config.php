<?php

$config['db'] = 'scriptureforge';

if (!defined('SF_DATABASE')) {
	define('SF_DATABASE', $config['db']);
}

if (!defined('RAPUMA_BASE_PATH')) {
	define('RAPUMA_BASE_PATH', '/var/lib/rapuma/work/');
}

?>

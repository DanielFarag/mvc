<?php
function dd($array){
	echo '<pre>';
	print_r($array);
	echo '</pre>';
}
define("APP_PATH",dirname(dirname(__FILE__)));

require APP_PATH . '/vendor/autoload.php';

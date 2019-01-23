<?php
function dd($array){
	echo '<pre>';
	print_r($array);
	echo '</pre>';
}
define("APP_PATH",dirname(dirname(__FILE__)));
require APP_PATH . '/vendor/autoload.php';
$database = new Framework\Database([
	'type'=>'mysql',
	'options'=>[
		'host'=>'localhost',
		'database'=>'test',
		'username'=>'root',
		'password'=>''
	]
]);
$mysql=$database->initialize()->connect();
Framework\Registry::set('database',$mysql);
class Books extends Framework\Model{
	/**
	* @readwrite
	* @column
	* @primary
	* @type autonumber
	*/
	protected $_id;
	
	/**
	* @readwrite
	* @column
	* @type text
	*/
	protected $_title;
	
	/**
	* @readwrite
	* @column
	* @type text
	*/
	protected $_iSBN;
	
	/**
	* @readwrite
	* @column
	* @type text
	*/
	protected $_publisher;
};
$model = new Books([
	'connector'=>Framework\Registry::get('database'),
]);
$model->iSBN='123-4567-890-11';
$model->title='My New Title';
$model->publisher=2;
dd($model->save());
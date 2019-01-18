<?php
namespace Framework;

class Path{
	private static $main=['app','caches','config','database','framework','public','storage'];
	private function __construct(){}
	private function __close(){}
	
	public static function __callStatic($dir,$params){
		if(!in_array($dir,self::$main)){
			throw new \Exception("Directory Not Found");
		}
		$path=implode($params,DIRECTORY_SEPARATOR);
		return APP_PATH.DIRECTORY_SEPARATOR.$dir.DIRECTORY_SEPARATOR.$path;
	}
}
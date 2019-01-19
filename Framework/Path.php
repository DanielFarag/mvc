<?php
namespace Framework;

class Path{
	private static $directories;
	private function __construct(){}
	private function __close(){}
	
	public static function __callStatic($needle,$params){
		if(empty(self::$directories)){
			self::$directories = array_diff(scandir(APP_PATH),['.','..','.git']);
		}
		$found = false;
		array_map(function($dir) use (&$needle,&$found){
			if(is_dir(APP_PATH.DIRECTORY_SEPARATOR.$dir) && strtolower($needle) == strtolower($dir)){
				$found = true;
				$needle = $dir;
			}
		},self::$directories);
		if(!$found){
			throw new \Exception("Directory Not Found");
		}
		
		$path=implode($params,DIRECTORY_SEPARATOR);
		
		return APP_PATH.DIRECTORY_SEPARATOR.$needle.DIRECTORY_SEPARATOR.$path;
	}
}
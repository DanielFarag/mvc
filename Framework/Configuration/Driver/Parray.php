<?php
namespace Framework\Configuration\Driver;
use Framework\Configuration\Driver;
use Framework\StringMethods;
use Framework\ArrayMethods;
class Parray extends Driver{
	public function parse(String $path){
		
		if(!file_exists($path)){
			throw new \Exception("$path doesn't exist");
		} 
		
		if(count(StringMethods::match($path,'\.php'))<=0){
			throw new \Exception("$path is not a .php file asd");
		}
		if(empty($this->_parsed[$path])){
			ob_start();
			$array = require($path);
			ob_end_clean(); // Don't display any echos.
			if(!is_array($array)){
				throw new \Exception("$path should return an key\value array");
			}
			$this->_parsed[$path]=ArrayMethods::toObject($array);
		}
		return $this->_parsed[$path];
	}
}
<?php 
namespace Framework\Configuration\Driver;
use Framework\Configuration\Driver;
use Framework\StringMethods;
class Json extends Driver{
	
	public function parse(String $path){
		if(!file_exists($path)){
			throw new \Exception("$path doesn't exist");
		} 

		if(count(StringMethods::match($path,'\.json$'))<=0){
			throw new \Exception("$path it's not a .ini file asd");
		}
		
		if(empty($this->_parsed[$path])){
			$file = file_get_contents($path);
			if(empty($file)) $file='{}';
			$this->_parsed[$path]= json_decode($file);
		}
		
		return $this->_parsed[$path];
	}
}
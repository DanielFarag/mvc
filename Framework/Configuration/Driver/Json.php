<?php 
namespace Framework\Configuration\Driver;
use Framework\Configuration\Driver;
use Framework\StringMethods;
class Json extends Driver{
	
	public function parse(String $path){
		
		parent::CheckFileExtension($path);
		
		if(empty($this->_parsed[$path])){
			$file = file_get_contents($path);
			if(empty($file)) $file='{}';
			$this->_parsed[$path]= json_decode($file);
		}
		
		return $this->_parsed[$path];
	}
}
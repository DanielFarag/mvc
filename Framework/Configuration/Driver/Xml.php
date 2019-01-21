<?php 
namespace Framework\Configuration\Driver;
use Framework\Configuration\Driver;
class Xml extends Driver{
	
	public function parse(String $path){
		parent::CheckFileExtension($path);
		if(empty($this->_parsed[$path])){
			$this->_parsed[$path]=simplexml_load_file($path);
		}
		return $this->_parsed[$path];
	}
}
<?php
namespace Framework\Configuration\Driver;
use Framework\Configuration\Driver;
use Framework\Base;
use Framework\StringMethods;
use Framework\ArrayMethods;
class Ini extends Driver{
	public function parse($path){
		
		if(!file_exists($path)){
			throw new \Exception("$path doesn't exist");
		} 
		if(count(StringMethods::match($path,'\.ini$'))<=0){
			throw new \Exception("$path it's not a .ini file asd");
		}
		if(!isset($this->parsed[$path])){
			$config = [];
			$iniArray = parse_ini_file($path);
			foreach ($iniArray as $key => $value){
				$config = $this->_pair($config, $key, $value);
			}
			$this->_parsed[$path] = ArrayMethods::toObject($config);
		}
		return $this->_parsed[$path];
	}
	private function _pair($config,$key,$value){
		if(strstr($key,'.')){
			$parts = explode('.',$key,2);
			if(empty($config[$parts[0]])){
				$config[$parts[0]]=[];
			}
			$config[$parts[0]] = $this->_pair($config[$parts[0]],$parts[1],$value);
		}else{
			$config[$key] = $value;
		}
		return $config;
	}
}
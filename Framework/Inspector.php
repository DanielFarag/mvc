<?php 
namespace Framework;

use Framework\StringMethods;

use Framework\ArrayMethods;

class Inspector{
	
	protected $_class;
	protected $_properties;
	protected $_methods;
	
	protected $_meta = [
		"class" => null,
		"properties"=>null,
		"methods"=>null
	];
	
	public function __construct($class){
		$this->_class = $class;
	}
	
	public function getClassProperties(){
		if(!isset($this->_properties)){
			$reflection = new \ReflectionClass($this->_class);
			$properties = $reflection->getProperties();
			foreach($properties as $property){
				$this->_properties[]=$property->getName();
			}
		}
		return $this->_properties;
	}
	public function getClassMethods(){
		if(!isset($this->_methods)){
			$reflection = new \ReflectionClass($this->_class);
			$methods = $reflection->getMethods();
			foreach($methods as $method){
				$this->_methods[]=$method->getName();
			}
		}
		return $this->_methods;
	}
	
	
	public function getClassMeta(){
		if(!isset($this->_meta['class'])){
			$reflection  = new \ReflectionClass($this->_class);
			$comment = $reflection->getDocComment();
			if(!empty($comment)){
				$this->_meta['class']= $this->_parse($comment);
			}else{
				$this->_meta['class']=null;
			}
		}
		return $this->_meta['class'];
	}
	public function getPropertyMeta($property){
		if(!isset($this->_meta['properties'][$property])){
			$reflection = new \ReflectionProperty($this->_class,$property);
			$comment = $reflection->getDocComment();
			if(!empty($comment)){
				$this->_meta['properties'][$property]= $this->_parse($comment);
			}else{
				$this->_meta['properties'][$property]= null;
			}
		}
		return $this->_meta['properties'][$property];
	}
	public function getMethodMeta($method){
		if(!isset($this->_meta['methods'][$method])){
			$reflection = new \ReflectionMethod($this->_class,$method);
			$comment = $reflection->getDocComment();
			if(!empty($comment)){
				$this->_meta['methods'][$method]= $this->_parse($comment);
			}else{
				$this->_meta['methods'][$method]= null;
			}
		}
		return $this->_meta['methods'][$method];
	}
	
	protected function _parse($comment){
		$meta= [];
		$pattern = '(@[a-zA-Z0-9]+\s*[a-zA-Z0-9, ()_\-]*)';
		$matches = StringMethods::match($comment,$pattern);
		if($matches){
			foreach($matches as $match){
				$parts=ArrayMethods::clean(ArrayMethods::trim(StringMethods::split($match,"[\s]",2)));
				$meta[$parts[0]]=true;
				if(count($parts)>1){
					$meta[$parts[0]]=ArrayMethods::clean(ArrayMethods::trim(StringMethods::split($parts[1],"[,|\s]")));
				}
			}
		}
		return $meta;
	}
}
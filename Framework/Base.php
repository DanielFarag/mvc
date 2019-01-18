<?php
namespace Framework;

use Framework\Inspector;
use Framework\StringMethods;

class Base{
	
	private $_inspector;

	public function __construct($options = []){
		// Inspect the current class
		$this->_inspector = new Inspector($this);
		// Assign options inserted thought constructor to the protected properties into the class
		// By using a magic method __set(called as public $this->property ) && __call(called as function $this->setProperty()))
		if(is_array($options) || is_object($options)){
			foreach($options as $option=>$value){
				$key = ucfirst($option);
				$method = "set{$key}";
				$this->$method($value);
			}
		}
	}
	public function __call($name,$arguments){
		// Check if inspector is defined in order to be able to inspect the comment of each property (read,write or both)
		if(empty($this->_inspector)){
			throw new \Exception('Call parent::__construct !');
		}
		
		$getMatches = StringMethods::match($name,"^get([a-zA-Z0-9]+$)");
		// If the subclass is trying to get the property's value by calling($object->getProperty() || $this->property)
		if(count($getMatches)>0){
			$normalized = lcfirst($getMatches[0]);
			$property = "_{$normalized}";
			if(property_exists($this,$property)){
				$meta = $this->_inspector->getPropertyMeta($property);

				if(empty($meta['@readwrite']) && empty($meta['@read'])){
					throw new \Exception("getExceptionForWriteOnly");
				}
				if(isset($this->$property)){
					return $this->$property;
				}
				return null;
			}
		}
		
		$setMatches = StringMethods::match($name,'^set([a-zA-Z0-9]+$)');
		// If the subclass is trying to set a value to property be calling ($object->setProperty(value) || $this->property = value)
		if(count($setMatches)>0){
			$normalized = lcfirst($setMatches[0]);
			$property = "_{$normalized}";
			if(property_exists($this,$property)){
				$meta = $this->_inspector->getPropertyMeta($property);
				if(empty($meta['@readwrite']) && empty($meta['@write'])){
					throw new \Exception("getExceptionForReadOnly");
				}
				$this->{$property} = $arguments[0];
				return $this;
			}
		}
		throw new \Exception("getExceptionForImplementation");
	}
	public function __get($name){
		$function="get".ucfirst($name);
		return $this->$function();
	}
	public function __set($name,$value){
		$function = "set".ucfirst($name);
		return $this->$function($value);
	}
}
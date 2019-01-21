<?php
namespace Framework\Configuration;
use Framework\Base;
use Framework\StringMethods;

abstract class Driver extends Base{
	/**
	*	@readwrite
	*/
	protected $_parsed;
	
	/**
	*	@readwrite
	*/
	protected $_extension;
	
	/**
	*	@readwrite
	*/
	protected $_options;
	
	public function CheckFileExtension(String $path){
		if(!file_exists($path)){
			throw new \Exception("$path doesn't exist");
		} 

		if(count(StringMethods::match($path,"\.{$this->extension}$"))<=0){
			throw new \Exception("$path it's not a .{$this->extension} file asd");
		}
	}
	
	abstract public function parse(String $path);
}
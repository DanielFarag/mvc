<?php
namespace Framework\Configuration;
use Framework\Base;

abstract class Driver extends Base{
	/**
	*	@readwrite
	*/
	protected $_parsed;
	
	/**
	*	@readwrite
	*/
	protected $_options;
	
	public function initialize(){
		return $this;
	}
	
	abstract public function parse(String $path);
}
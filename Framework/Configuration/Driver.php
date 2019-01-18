<?php
namespace Framework\Configuration;
use Framework\Base;

class Driver extends Base{
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
}
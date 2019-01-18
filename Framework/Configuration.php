<?php
namespace Framework;
class Configuration extends Base{
	/**
	*	@readwrite
	*/
	protected $_type;
	
	/**
	*	@readwrite
	*/
	protected $_options;
	
	public function initialize(){
		if(!$this->type){
			throw new \Exception("Invalid Type");
		}
		switch($this->type){
			case "ini":
				return new Configuration\Driver\Ini(['options'=>$this->options]);
			break;
			default:
				throw new \Exception("Invalid Type");
			break;
		}
	}
} 
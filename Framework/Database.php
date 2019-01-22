<?php 
namespace Framework;

class Database extends Base{
	/**
	* @readwrite
	*/
	protected $_type;
	
	/**
	* @readwrite
	*/
	protected $_options;
	
	
	
	public function initialize(){
		if(!$this->type){
			throw new \Exception("Invalid type");
		}
		switch($this->type){
			case "mysql":
				return new Database\Connector\Mysql($this->options);
			break;
			default:
				throw new \Exception("Invalid Type");
			break;
		}
	}
}
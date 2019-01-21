<?php
namespace Framework;
use Framework\Configuration\Driver;
class Configuration extends Base{
	/**
	*	@readwrite
	*/
	protected $_type;
	
	/**
	*	@readwrite
	*/
	protected $_extension;
	
	/**
	*	@readwrite
	*/
	protected $_options;
	
	public function initialize():Driver{
		if(!$this->type){
			throw new \Exception("Invalid Type");
		}
		switch($this->type){
			case "ini":
				return new Configuration\Driver\Ini(['extension'=>'ini','options'=>$this->options]);
			break;
			case "array":
				return new Configuration\Driver\Parray(['extension'=>'php','options'=>$this->options]);
			break;
			case "json":
				return new Configuration\Driver\Json(['extension'=>'json','options'=>$this->options]);
			break;
			case "xml":
				return new Configuration\Driver\Xml(['extension'=>'xml','options'=>$this->options]);
			break;
			default:
				throw new \Exception("Invalid Type");
			break;
		}
	}
} 
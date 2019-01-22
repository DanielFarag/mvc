<?php
namespace Framework;
use Framework\ArrayMethods;
class Request extends Base{
	/**
	* @readwrite
	*/
	protected $_host;
	
	/**
	* @readwrite
	*/
	protected $_agent;
	
	/**
	* @readwrite
	*/
	protected $_accept;
	
	/**
	* @readwrite
	*/
	protected $_serverName;	
	
	/**
	* @readwrite
	*/
	protected $_remoteAddr;
	
	/**
	* @readwrite
	*/
	protected $_scriptFilename;
	
	/**
	* @readwrite
	*/
	protected $_scriptName;
	
	/**
	* @readwrite
	*/
	protected $_redirectUrl;
	
	/**
	* @readwrite
	*/
	protected $_queryString;
	
	/**
	* @readwrite
	*/
	protected $_requestMethod;
	
	/**
	* @readwrite
	*/
	protected $_requestUrl;
	
	/**
	* @readwrite
	*/
	protected $_contentType;

	/**
	* @readwrite
	*/
	protected $_header;
	
	/*
	* Set all necessary variables
	*/
	public function initialize(){
		parent::__construct([
			'host'=>$this->key('HTTP_HOST'),
			'agent'=>$this->key('HTTP_USER_AGENT'),
			'serverName'=>$this->key('SERVER_NAME'),
			'remoteAddr'=>$this->key('REMOTE_ADDR'),
			'scriptFilename'=>$this->key('SCRIPT_FILENAME'),
			'scriptName'=>$this->key('SCRIPT_NAME'),
			'redirectUrl'=>$this->key('REDIRECT_URL'),
			'queryString'=>$this->key('QUERY_STRING'),
			'requestMethod'=>$this->key('REQUEST_METHOD'),
			'requestUrl'=>$this->key('REQUEST_URI'),
			'header'=>function(){
				if(function_exists('apache_request_headers')){
					return ArrayMethods::toObject(apache_request_headers());
				}
				return new \stdClass;
			},
			'accept'=>function(){
				return $this->header('Accept');
			},
			'contentType'=>function(){
				return $this->header('Content-Type');
			},
		]);
	}
	private function key(String $key,$default = null){
		if(!empty($_SERVER[$key])){
			return $_SERVER[$key];
		}
		return $default;
	}

	/*
	* check the value of Accept parameter in header
	*/
	public function Expect(String $formate){
		switch($formate){
			case 'json':
				return $this->header('Accept') == "application/json";
			case 'xml':
				return $this->header('Accept') == "application/xml";
			default:
				throw new \Exception("{$formate} isn't a valid formate");
		}
	}
	
	/*
	* Retrieve data from $_GET global variable
	*/
	public function query($key=null,$default = null){
		if($key!=null){
			if(!empty($_GET[$key])) $default = $_GET[$key];
		}else{
			$default = $_GET;
		}
		if(is_array($default)){
			$default = ArrayMethods::toObject($default);
		}
		return $default;
	}
	
	/*
	* Retrieve data from $_POST global variable
	*/
	public function post($key=null,$default = null){
		if($key!=null){
			if(!empty($_POST[$key])) $default = $_POST[$key];
		}else{
			$default = $_POST;
		}
		
		if(is_array($default)){
			$default = ArrayMethods::toObject($default);
		}
		return $default;
	}
	
	/*
	* Retrieve data from the header of the request 
	*/
	public function header($key=null,$default = null){
		if($key!=null){
			if(!empty($this->header->{$key})) $default = $this->header->{$key};
		}else{
			$default = $this->header;
		}
		
		if(is_array($default)){
			$default = ArrayMethods::toObject($default);
		}
		return $default;
	}
}




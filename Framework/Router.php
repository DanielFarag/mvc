<?php 
namespace Framework;
use Framework\Base as Base;
use Framework\Registry as Registry;
use Framework\Inspector as Inspector;
class Router extends Base{
	/**
	* @readwrite
	*/
	protected $_url;
	/**
	* @readwrite
	*/
	protected $_extension;
	/**
	* @read
	*/
	protected $_controller;
	/**
	* @read
	*/
	protected $_action;
	
	protected $_routes = [];
	

	public function addRoute($route){
		$this->_routes[] = $route;
		return $this;
	}
	
	public function removeRoute($route){
		foreach ($this->_routes as $i => $stored){
			if ($stored == $route){
				unset($this->_routes[$i]);
			}
		}
		return $this;
	}
	
	public function getRoutes(){
		$list = [];
		foreach ($this->_routes as $route){
			$list[$route->pattern] = get_class($route);
		}
		return $list;
	}
	public function dispatch(){
		$url = $this->url;
		$parameters = [];
		$controller = "index";
		$action = "index";
		foreach ($this->_routes as $route){
			if ($route->matches($url)){
				$controller = $route->controller;
				$action = $route->action;
				$parameters = $route->parameters;
				$this->_pass($controller, $action, $parameters);
				return;
			}
		}
		
		// If the current url has not Route Handler Object, retrive Controller name,Method name and parameters from url
		$parts = explode("/", trim($url, "/"));
		if (sizeof($parts) > 0){
			$controller = $parts[0];
			if (sizeof($parts) >= 2){
				$action = $parts[1];
				$parameters = array_slice($parts, 2);
			}
		}
		$this->_pass($controller, $action, $parameters);
	}
	protected function _pass($controller,$action,$parameters = []){
		$name = ucfirst($controller);
		$this->_controller = $controller;
		$this->_action = $action;
		/**
		* Check if Controller And Method handler are defined
		*/
		try{
			$instance  = new $name(["parameters" => $parameters]);
			Registry::set("controller",$instance);
		}catch(\Exception $ex){
			throw new \Exception("Controller {$name} not found");
		}
		if(!method_exists($instance,$action)){
			$instance->willRenderLayoutView = false;
			$instance->willRenderActionView = false;
			throw new \Exception("Action {$action} not found");
		}
		
		
		/*
		* Inspect the handler method to retrive any metaData
		*/
		$inspector = new Inspector($instance);
		$methodMeta = $inspector->getMethodMeta($action);
		
		/**
		* Allow only public function to be defined and Route Handler Method
		*/
		if(!empty($methodMeta['@protected']) || !empty($methodMeta['@private'])){
			throw new \Exception("Action {$action} not found");
		}
		
		/**
		* Create a hook method to get fired when a method has @before ...methods @after ...methods ActionHooks
		*/
		$hooks =function ($meta,$type) use ($inspector,$instance){
			if(isset($meta[$type])){
				$run = [];
				foreach($meta[$type] as $method){
					$hookMeta = $inspector->getMethodMeta($method);
					// Handle @once meta: ignore it if it's been fired and has @once meta
					if(in_array($methodm,$run)&&!empty($hookMeta['@once'])){
						continue;
					}
					
					$instance->$method();
					$run[]=$method;
				}
			}
		};
		
		$hooks($methodMeta,"@before");
		call_user_func_array([$instance,$action], is_array($parameters)?$parameters:[]);
		$hooks($methodMeta,"@after");
		Registry::erase("controller");
	}
	
}
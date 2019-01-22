<?php
namespace Framework\Router\Route;
use Framework\Router\Route;
use Framework\StringMethods;
use Framework\ArrayMethods;

class Simple extends Route{
	public function matches(String $url){
		
		$pattern = $this->pattern;
		
		// Get Keys without (:)
		StringMethods::matchAll($pattern,':([a-zA-Z0-9]+)',$matches);
		if(count($matches)>0 && count($matches[0]) && count($matches[1])){
			$keys = $matches[1];
		}else{
			// If there is't any dynamic parameter (:par) in url, return true if url is identical to the pattern 
			return StringMethods::match($url,$pattern);
		}
		
		// replace :par formate to RegularExpression Formate
		$pattern =  StringMethods::replace($pattern,'(:[a-zA-Z0-9]+)','([a-zA-Z0-9-_]+)');
		
		// Get Values for  the $keys from current url by using the generated $pattern then store it into $values
		StringMethods::matchAll($url,$pattern,$values);
		
		if(count($values) &&count($values[0])&&count($values[1])){
			unset($values[0]); // All Matches string
			$derived = array_combine($keys,ArrayMethods::flatten($values));
			$this->parameters = array_merge($this->parameters,$derived);
			return true;
		}
		return false;
	}
}
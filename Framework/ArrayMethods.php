<?php

namespace Framework;

class ArrayMethods{
	private function __construct(){}
	private function __clone(){}
	
	public static function clean(Array $array){
		return array_filter($array,function($item){
			return !empty($item);
		});
	}
	public static function trim(Array $array){
		return array_map(function($item){
			return trim($item);
		},$array);
	}
	public static function toObject(Array $array){
		$results = new \stdClass;
		foreach($array as $key=>$value){
			if(is_array($value)){
				$results->{$key}= self::toObject($value);
			}else{
				$results->{$key} = $value;
			}
		}
		return $results;
	}
	public static function flatten(Array $array,Array $return = []):Array{

		foreach ($array as $key => $value){
			if(is_array($value)|| is_object($value)){
				$return = self::flatten($value,$return);
			}else{
				$return[] =$value;
			}
		}
		return $return;
	}
}
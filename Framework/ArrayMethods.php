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
}
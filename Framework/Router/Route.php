<?php
namespace Framework\Router;

use Framework\Base;

class Route extends Base{
	/**
	* @readwrite
	*/
	protected $_pattern;
	
	/**
	* @readwrite
	*/
	protected $_controller;
	
	/**
	* @readwrite
	*/
	protected $_action;
	
	/**
	* @readwrite
	*/
	protected $_method;
	
	/**
	* @readwrite
	*/
	protected $_parameters = [];
}
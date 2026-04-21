<?php
class auto_load_page{

public function  __construct(){}
//
public static function config_db(){
	return new mysqli("localhost","root","","orbitcms");
	}
private static $page;
private static $cot;
public static function page_name_($page_name){
	self::$page = $page_name;
	}
public static function context_page($context){
	self::$cot=$context;
	}
public static function pag_name_(){
	return self::$page;
	}
	
}



?>
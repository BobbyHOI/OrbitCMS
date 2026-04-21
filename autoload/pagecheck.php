<?php
include "../autoload/autoload.php";
class page_check_ extends auto_load_page{
	
public static function check_expire_date($key_date){
	if($key_date!=''){
		return $key_date;
		}
		else{
			exit("invalid date checking");
			}
	}
public static function controller($key,$pagename){
	$db = parent::config_db()->query("call sp_validation('$key','$pagename')");
	$row = $db->fetch_assoc();
	parent::page_name_($row['page_text']);
	}
	
	
	}
//page_check_::controller('zxvxbxnxnxnxmcmcmcmkdkdlle54hshjs','index');
//echo auto_load_page::pag_name_();

?>
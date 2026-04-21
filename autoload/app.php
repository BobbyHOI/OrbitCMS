<?php
include "../autoload/pagecheck.php";
if(isset($_GET['page_index'])){
	page_check_::controller($_GET['page_index'],$_GET['get_index']);
	echo auto_load_page::pag_name_();
	}
	else{
	echo "Invalid licenceKey";
		}

?>
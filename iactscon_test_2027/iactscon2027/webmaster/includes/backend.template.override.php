<?php
function page_header($headerTitle,$fullscreen=false,$recievedAmount=0){
	
	global $cfg, $mycms;
	webmaster_page_header($headerTitle,$fullscreen,$recievedAmount);
}

function breadCumDefn($headerDisplay=""){
    
	global $cfg, $mycms;
	
	webmaster_breadCumDefn($headerDisplay);
}

function page_footer($scr='&nbsp;'){
	
	global $cfg, $mycms;
	
	webmaster_page_footer($scr);
	$mycms->sql_close();
}
?>
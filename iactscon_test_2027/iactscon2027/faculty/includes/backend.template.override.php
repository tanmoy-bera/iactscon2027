<?php
function page_header($HeaderTitle){
	
	global $cfg, $mycms;
	webmaster_page_header($HeaderTitle);
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
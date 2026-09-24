<?
include_once('includes/init.php');

$act=@$_REQUEST['act']; 
switch($act){

/*============= GENERATE CITY LIST ===============*/
case'GetCityIndex':

	$value           = $_REQUEST['value'];
	
	$CityControlName = $_REQUEST['CityControlName'];
	$CityPlaceHolder = $_REQUEST['CityPlaceHolder'];
	
	$str  = "<select name='".$CityControlName."' id='".$CityControlName."' class='forminputelement' style='width:90%;'>";
	$str .= "<option value='' >-- Select City --</option>";	
	
	$sqlCity['QUERY']  = "SELECT * FROM "._DB_COMN_CITIES_." 
	                     WHERE `state_id`='".$value."'  
					  ORDER BY `city_name`";
	$resultCity  = $mycms->sql_select($sqlCity);
	if($resultCity) {
	foreach($resultCity as $key=>$row) {
			
	  $str.="<option value=$row[ct_id]>$row[city_name]</option>";
		 
	}}
	$str.="</select>|||||".$CityPlaceHolder;
	echo $str;
exit();
break;

/*============ GENERATE STATE LIST ==============*/
case'GetStateIndex':

    $value            = $_REQUEST['value'];
	$StateControlName = $_REQUEST['StateControlName'];
	$StatePlaceHolder = $_REQUEST['StatePlaceHolder'];
	
	$CityControlName  = $_REQUEST['CityControlName'];
	$CityPlaceHolder  = $_REQUEST['CityPlaceHolder'];
	
    $str  = "<select name='".$StateControlName."' id='".$StateControlName."' class='forminputelement' onchange=\"getCityList(this.value,'$CityControlName','$CityPlaceHolder')\" style='width:90%;'>";
	$str .= "<option value=''>-- Select State --</option>";	
	
	$sqlState['QUERY'] = "SELECT * FROM "._DB_COMN_STATE_."WHERE `country_id`='".$value."'  ORDER BY `state_name`";
	$resultState  = $mycms->sql_select($sqlState);
	if($resultState) {
	foreach($resultState as $key=>$row) {
			
	  $str.="<option value=$row[st_id]>$row[state_name]</option>";
		 
	}}
	$str  .= "</select>|||||".$StatePlaceHolder;
	echo $str;
exit();
break;
}
?>




<?php
include_once("includes/frontend.init.php");
include_once("includes/function.invoice.php");
include_once('lib/pdfcrowd.php');
$userId        = addslashes(trim($_REQUEST['user_id']));

$sqlFetchDelegate1           =  array();
  $sqlFetchDelegate1['QUERY']      = "  SELECT * 
                        FROM "._DB_USER_REGISTRATION_." 
                       WHERE `id` = ?
                         AND `status` = ?";
  $sqlFetchDelegate1['PARAM'][]  = array('FILD' => 'id',    'DATA' =>$userId,  'TYP' => 's');
  $sqlFetchDelegate1['PARAM'][]  = array('FILD' => 'status',  'DATA' =>'A',  'TYP' => 's');                        
  $resultDelegate1         = $mycms->sql_select($sqlFetchDelegate1);
  
  if($resultDelegate1)
  {
    $rowDelegate1 = $resultDelegate1[0];
  }

 //print_r($rowDelegate1);

 $userName = $rowDelegate1['user_first_name']." ".$rowDelegate1['user_middle_name']." ".$rowDelegate1['user_last_name'];

$array = $rowDelegate1['tags'];
$var = (explode(",",$array));

if($rowDelegate1['tags']!='' && sizeof($var)>0)
{
  //print_r($var);
  foreach($var as $key=>$val)
  {
      
      if($val =='National Faculty' || $val =='International Faculty')
      {
        $img = _BASE_URL_.'images/Faculty_Certificate_Artwork.jpg';
      }
     
  }
}
else
{
  $img = _BASE_URL_.'images/Delegate_Certificate_Artwork.jpg';
}


?>
<!DOCTYPE html>
<html>
<head>
<meta name="viewport" content="width=device-width, initial-scale=1">
<style>
.container {
  position: relative;
  text-align: center;
  color: white;
}
.container img {
    position: absolute;
    top: 0;
    right: 0;
    left: 0;
    bottom: 0;
    width: 100%;
    height: 100%;
    object-fit: contain;
}
.bottom-left {
  position: absolute;
  bottom: 8px;
  left: 16px;
}

.top-left {
  position: absolute;
  top: 8px;
  left: 16px;
}

.top-right {
  position: absolute;
  top: 8px;
  right: 16px;
}

.bottom-right {
  position: absolute;
  bottom: 8px;
  right: 16px;
}

.centered {
    position: relative;
    color: #064e79;
    font-size: 45px;
    font-weight: 600;
    padding-top: 21%;
    padding-bottom: 41%;
}


</style>
</head>
<body>
<div class="container aa">
  <img src="<?php echo $img; ?>" style="width: 100%;">
  <div class="centered"><?php echo $userName; ?></div>
  <div class="">
</div>
<script>
    window.print();
    </script>
</body>
</html> 
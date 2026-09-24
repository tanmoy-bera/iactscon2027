<?php
	include_once('includes/init.php');

		
	$userId             = addslashes(trim($_REQUEST['id']));
	
	// FETCH USER DETAILS
	$sqlFetchDelegate1				   =	array();
	$sqlFetchDelegate1['QUERY']		   = "  SELECT * 
											  FROM "._DB_USER_REGISTRATION_." 
											 WHERE `id` = ?
											   AND `status` = ?";
	$sqlFetchDelegate1['PARAM'][]  = array('FILD' => 'id',  	'DATA' =>$userId,  'TYP' => 's');
	$sqlFetchDelegate1['PARAM'][]  = array('FILD' => 'status',  'DATA' =>'A',  'TYP' => 's');											   
	$resultDelegate1    	   = $mycms->sql_select($sqlFetchDelegate1);

	//echo '<pre>'; print_r($sqlFetchDelegate1);
	
	if($resultDelegate1)
	{
		$rowDelegate1 = $resultDelegate1[0];
	}
	
	
	$user_full_name = str_replace(".","",trim($rowDelegate1['user_full_name']));
			$user_full_name = str_replace("..","",$user_full_name);
	
	/*if(strlen($user_full_name)>17)	
	{
		//echo 12;
		$user_full_name = str_replace(".","",trim($rowDelegate1['user_first_name']))." ".str_replace(".","",trim($rowDelegate1['user_middle_name']))."<br> ".str_replace(".","",trim($rowDelegate1['user_last_name']));
			$user_full_name = str_replace("..","",$user_full_name);
	}*/	

	if (strlen($user_full_name) > 23) {
    		$user_full_name = wordwrap($user_full_name, 23, "<br>", true);
   			
		}
			
	

	// $printName = userNameShortage($userId, 15);
	$printName = $user_full_name;
	$nameFontSize = '26px';
	if(strlen($printName)>=19)
	{ 
		$nameFontSize = '25px';
	}
	//print_r($rowDelegate1['user_first_name']);

	//Check dalegate Faculty or not
	$checkfaculty				   =	array();
	$checkfaculty['QUERY']   = " SELECT * 
								  FROM "._DB_SP_PARTICIPANT_DETAILS_." 
								 WHERE `participant_registration_id` = ?
								   AND `status` = ?";
	$checkfaculty['PARAM'][]  = array('FILD' => 'participant_registration_id',  	'DATA' =>$rowDelegate1['user_registration_id'],  'TYP' => 's');	
	$checkfaculty['PARAM'][]  = array('FILD' => 'status',  							'DATA' =>'A',  									 'TYP' => 's');							   
	$resultFaculty  = $mycms->sql_select($checkfaculty);
	
	$hasMasterClass = array();
	$hasPostConClass = array();
	
	$sqlWorkshopQueryset				=	array();
	$sqlWorkshopQueryset['QUERY']       = "  SELECT  workshop_request.delegate_id AS workshop_request_delegate_id,
													 workshop_request.workshop_id AS workshop_request_workshop_id,
													 workshop_request.payment_status AS workshop_request_payment_status,
													 workshop_request.refference_invoice_id AS workshop_request_refference_invoice_id,	
												
													 workshop.id AS workshop_id,
													 workshop.classification_title AS workshop_classification_title,
													 workshop.type AS clsf_type, workshop.workshop_description AS workshop_description,
													 
													 invoice.id AS invoice_id,
													 invoice.delegate_id AS invoice_delegate_id,
													 invoice.payment_status AS invoice_payment_status,
													 invoice.status AS invoice_status
						 
												FROM "._DB_REQUEST_WORKSHOP_." AS workshop_request
										
									 LEFT OUTER JOIN "._DB_INVOICE_." invoice
												  ON workshop_request.refference_invoice_id = invoice.id
							
									 LEFT OUTER JOIN "._DB_WORKSHOP_CLASSIFICATION_." workshop
												  ON workshop_request.workshop_id = workshop.id
										  
											   WHERE workshop_request.delegate_id = '".$userId."'
												 AND invoice.status ='A'";
	
	$resultWorkshopUser        	= $mycms->sql_select($sqlWorkshopQueryset);
	$workshopId 				= array();
	
	foreach($resultWorkshopUser as $key=>$val)
	{
		if($val['workshop_request_workshop_id'] != '5')
		{
			$workshopId[] = $val['workshop_request_workshop_id'];
			
			if($val['clsf_type'] == 'MASTER CLASS')
			{
				$hasMasterClass[] = $val['workshop_classification_title'].' - '.$val['workshop_description'];
			}
			
			if($val['clsf_type'] == 'POST-CONFERENCE')
			{
				$hasPostConClass[] = $val['workshop_classification_title'];
			}
		}
	}
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>::Delegate Icard::</title>

<script src="<?=_BASE_URL_?>js/adminPanel/all.js"></script>
<script src="<?=_BASE_URL_?>js/table/stupidtable.js"></script>
<script src="<?=_BASE_URL_?>js/graph/dx.chartjs.js"></script>
<script src="<?=_BASE_URL_?>js/graph/globalize.min.js"></script>
<script src="<?=_BASE_URL_?>js/graph/knockout-2.2.1.js"></script>

<!-- fonts -->
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Funnel+Display:wght@500&display=swap" rel="stylesheet">
<!-- fonts end -->
</head>
<body style="margin:0; padding:0;">
<script type="text/javascript" language="javascript">
	$(document).ready(function(){	
	  window.print();
	   
	   setTimeout(function()
	   {
		//window.close();
		window.open('', '_self', '');
		//window.close();
		},1500);
	  
	});
	</script>
	<!--background-image:url(delegatebg.jpg);-->	
	<?php 
		if($_REQUEST['show']=='OLD'){	
	?>	
<div style="width:500px; height:340px;  position:relative; background-repeat: no-repeat; 
				background-size: 100%; background-position: left top; margin-left: 19%; margin-top: 43%;">
		<p style="margin: 0px; top:22px; left:73px; font-size: 28px; position:absolute;  width: 70%;
				  text-align: center; font-weight: 600; color: rgb(0, 0, 0);   font-family: 'Funnel Display', sans-serif; font-optical-sizing: auto; font-style: normal;">
	<?php
		if($rowDelegate1['user_type']=="DELEGATE")
		{
			echo ucwords(strtolower(trim($printName)));
		}
		?>
		</p>	
		<div style="position: absolute; bottom: 175px; right: 10px;">
			<img src="<?=in_array(2,$workshopId)?"images/Delegate.png":"images/emptywhit.png"?>" alt="">
		</div>
		<div style="position: absolute; bottom: 125px; right: 10px;">
			<img src="<?=in_array(1,$workshopId)?"images/Delegate.png":"images/emptywhit.png"?>" alt="">
		</div>
	
		
		<!--<div style="bottom:35px; left:10px; position:absolute; text-align: center;   width: 49%;">
		<?=generateBarcode($rowDelegate1['user_registration_id']);?><br />
		<span style="font-size:12px";><?=$rowDelegate1['user_registration_id']?></span>
		</div>-->
</div>
	<?php 
		}
		else
		{	
	?>	
	<div style="width:500px; height:340px;  position:relative; background-repeat: no-repeat; 
				background-size: 100%; background-position: left top; margin-left: 19%; margin-top: 43%;">
		<p style="margin: 0px; top:22px; left:73px; font-size: <?=$nameFontSize?>; text-transform: capitalize; position:absolute;  width: 70%;
				  text-align: center; font-weight: 600; color: rgb(0, 0, 0); font-family: 'Funnel Display', sans-serif; font-optical-sizing: auto; font-style: normal;">
	<?php
		/*if($rowDelegate1['user_type']=="DELEGATE")
		{
			if($rowDelegate1['user_first_name']=='EXHIBITOR' || $rowDelegate1['user_first_name']=='MR EXHIBITOR')
			{
				echo ucwords(strtolower(trim($printName)));
			}
			else
			{
				echo ucwords(strtolower(trim($printName)));
			}
			
		}*/
		echo ucwords(strtolower(trim($printName)));
		?>
		</p>	
		<div style="bottom:200px; left:124px; position:absolute; text-align: center;transform:rotate(0deg); width: 49%;">
		<?=generateBarcode($rowDelegate1['user_registration_id']);?><br />
		<span style="font-size:12px; font-family: monospace;"><?=$rowDelegate1['user_registration_id']?></span>
		<?
		if(sizeof($hasMasterClass)>0 || sizeof($hasPostConClass)>0)
		{
		?>
		<br /><span style="font-size:10px; border-top: thin solid #CCCCCC;";>
		<?
				if(sizeof($hasPostConClass)>0)
				{
					echo implode(', ',$hasPostConClass).'<br/>';
				}
		?>
			</span>
		<?
		}
		?>
		<br/><span style="font-size:10px; border-top: thin solid #CCCCCC;";>
		</span>
		</div>
</div>
	<?php 
		}	
	
	
		
	?>
</body>
</html>
<?php
function generateBarcode($registrationId)
{
	global $cfg, $mycms;
?>
  <? if($cfg['CARD_PRINT_OPTION']=='qr'){
	 $qrCodeUrl = "https://api.qrserver.com/v1/create-qr-code/?size=100x100&data=" . urlencode($registrationId);
    
    ?>
    <img src="<?= $qrCodeUrl ?>" 
         id="qrCodeImage" 
         title="<?= $registrationId ?>" 
         width="120" 
         height="120" 
         style="display:block; margin:0 auto;"
         alt="QR Code"/>
	 <? }else{?>
	 <img src="<?=_BASE_URL_."lib/barcode/code/image.php?text=".$registrationId?>" id="barcodeImage" 
	 title="<?=$registrationId?>" width="190px" height="40"/>
	<?} ?>
<?php
}
?>
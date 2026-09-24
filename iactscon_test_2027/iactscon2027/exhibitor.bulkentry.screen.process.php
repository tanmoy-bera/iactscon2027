<?php
	include_once('includes/frontend.init.php');
	include_once("includes/function.frontend.registration.php");
	include_once("includes/function.edit.php");
	include_once("includes/function.registration.php");
	include_once('includes/function.delegate.php');
	include_once('includes/function.invoice.php');
	include_once('includes/function.accompany.php');
	include_once('includes/function.workshop.php');
	include_once('includes/function.registration.php');
	include_once('includes/function.messaging.php');
	include_once('includes/function.accommodation.php');
	include_once("includes/function.dinner.php");
	include_once('includes/function.delegate.php');
	include_once('includes/function.invoice.php');
	include_once('includes/function.accompany.php');
	include_once('includes/function.workshop.php');
	include_once('includes/function.registration.php');
	include_once('includes/function.messaging.php');
	include_once('includes/function.accommodation.php');
	include_once("includes/function.dinner.php");
	include_once('includes/function.delegate.php');
	include_once('includes/function.invoice.php');
	include_once('includes/function.accompany.php');
	include_once('includes/function.workshop.php');
	include_once('includes/function.registration.php');
	include_once('includes/function.messaging.php');
	
	$act = $_REQUEST['act'];
	
	switch($act)
	{
		case'bulkInsert':
			if(isset($_REQUEST['DRAFT']) && trim($_REQUEST['DRAFT']) == 'Y')
			{
				dataDraftProcess();
			}
			elseif(isset($_REQUEST['FORCED']) && trim($_REQUEST['FORCED']) == 'Y')
			{
				dataForcedCompleteProcess();
			}
			else
			{
				dataUploadProcess();
			}
			exit();
			break;
		case'downloadExhibitorExcel':
			bulkRegistrationErrorDownload($cfg, $mycms);
			exit();
			break;
	}
	
	function dataUploadProcess()
	{
		global $mycms, $cfg;
		
		/*echo '<pre>';
		print_r($_REQUEST);
		echo '</pre>';
		die();*/
		
		$lastInsertedSessionDetails = $_REQUEST['bulkUploadSessionId'];
		$registration_cutoff		= $_REQUEST['cutoffId'];
		$exhibitor_company_id		= $_REQUEST['exhibitorId'];
		
		foreach($_REQUEST['user_email'] as $key=>$fN)
		{
			$email_id						= addslashes(trim($_REQUEST['user_email'][$key]));
			$membership_number				= addslashes(trim($_REQUEST['membership_number'][$key]));
			$title							= addslashes(trim($_REQUEST['title'][$key]));
			$first_name						= addslashes(trim($_REQUEST['user_first_name'][$key]));
			$middle_name					= addslashes(trim($_REQUEST['middle_name'][$key]));
			$last_name						= addslashes(trim($_REQUEST['user_last_name'][$key]));
			$mobile_isd_code				= addslashes(trim($_REQUEST['mobile_isd_code'][$key]));
			$mobile_no						= addslashes(trim($_REQUEST['user_mobile'][$key]));
			$address						= addslashes(trim($_REQUEST['address'][$key]));
			$country						= addslashes(trim($_REQUEST['user_country'][$key]));
			$state							= addslashes(trim($_REQUEST['user_state'][$key]));
			$city							= addslashes(trim($_REQUEST['city'][$key]));
			$pincode						= addslashes(trim($_REQUEST['pincode'][$key]));
			$gender							= addslashes(trim($_REQUEST['gender'][$key]));
			$department						= addslashes(trim($_REQUEST['department'][$key]));
			$institute_name					= addslashes(trim($_REQUEST['institute_name'][$key]));
			$food_preference				= addslashes(trim($_REQUEST['food_preference'][$key]));
			
			$registration_classification_id = addslashes(trim($_REQUEST['registration_classification_id'][$key]));
			
			$banquet_dinner					= addslashes(trim($_REQUEST['dinner_value'][$key]));
			
			$pWORKSHOP						= array();
			if($_REQUEST['masterClassId'][$key]!='')
			{
				$pWORKSHOP []                    = addslashes(trim($_REQUEST['masterClassId'][$key]));
			}
			if($_REQUEST['workshopId'][$key]!='')
			{
				$pWORKSHOP []                    = addslashes(trim($_REQUEST['workshopId'][$key]));
			}
			if($_REQUEST['postConference'][$key]!='')
			{
				$pWORKSHOP []                    = addslashes(trim($_REQUEST['postConference'][$key]));
			}
			
			$WORKSHOP						 = implode(',',$pWORKSHOP);
			
			$Accompany_1_Name               = addslashes(trim($_REQUEST['accompanyNameAdd'][$key][0]));
			$Accompany_1_Age                = addslashes(trim($_REQUEST['accompanyAge'][$key][0]));
			$Accompany_1_Food_Preference    = addslashes(trim($_REQUEST['accompanyFoodChoice'][$key][0]));
			$Accompany_1_Banquet_Dinner     = addslashes(trim($_REQUEST['accompanyDinnerValue'][$key][0]));
				
			$Accompany_2_Name               = addslashes(trim($_REQUEST['accompanyNameAdd'][$key][1]));
			$Accompany_2_Age                = addslashes(trim($_REQUEST['accompanyAge'][$key][1]));
			$Accompany_2_Food_Preference 	= addslashes(trim($_REQUEST['accompanyFoodChoice'][$key][1]));
			$Accompany_2_Banquet_Dinner		= addslashes(trim($_REQUEST['accompanyDinnerValue'][$key][1]));
			
			$Accompany_3_Name				= addslashes(trim($_REQUEST['accompanyNameAdd'][$key][2]));
			$Accompany_3_Age				= addslashes(trim($_REQUEST['accompanyAge'][$key][2]));
			$Accompany_3_Food_Preference	= addslashes(trim($_REQUEST['accompanyFoodChoice'][$key][2]));
			$Accompany_3_Banquet_Dinner		= addslashes(trim($_REQUEST['accompanyDinnerValue'][$key][2]));
			
			$Accompany_4_Name				= addslashes(trim($_REQUEST['accompanyNameAdd'][$key][3]));
			$Accompany_4_Age				= addslashes(trim($_REQUEST['accompanyAge'][$key][3]));
			$Accompany_4_Food_Preference	= addslashes(trim($_REQUEST['accompanyFoodChoice'][$key][3]));
			$Accompany_4_Banquet_Dinner		= addslashes(trim($_REQUEST['accompanyDinnerValue'][$key][3]));
			
			$sqlInsertExcel					= array();
			$sqlInsertExcel['QUERY']		= " INSERT INTO  "._DB_BLUK_REGISTRATION_DATA_." 
														SET session_id						= '".$lastInsertedSessionDetails."',
															user_email_id					= '".$email_id."',
															membership_number				= '".$membership_number."',
															user_title						= '".$title."',
															user_first_name					= '".$first_name."',
															user_middle_name				= '".$middle_name."',
															user_last_name					= '".$last_name."',
															user_mobile_isd_code			= '".$mobile_isd_code."',
															user_mobile_no					= '".$mobile_no."',
															user_address					= '".$address."',
															user_country					= '".$country."',
															user_state						= '".$state."',
															user_city						= '".$city."',
															user_pincode					= '".$pincode."',
															user_gender						= '".$gender."',
															user_depertment					= '".$department."',
															user_institute_name				= '".$institute_name."',
															user_food_preference			= '".$food_preference."',
															iis_banquet						= '".$banquet_dinner."',
															workshop						= '".$WORKSHOP."',
															accompany1_name					= '".$Accompany_1_Name."',
															accompany1_age					= '".$Accompany_1_Age."',
															accompany1_food_preference		= '".$Accompany_1_Food_Preference."',
															accompany1_is_banquet			= '".$Accompany_1_Banquet_Dinner."',
															accompany2_name					= '".$Accompany_2_Name."',
															accompany2_age					= '".$Accompany_2_Age."',
															accompany2_food_preference		= '".$Accompany_2_Food_Preference."',
															accompany2_is_banquet			= '".$Accompany_2_Banquet_Dinner."',
															accompany3_name					= '".$Accompany_3_Name."',
															accompany3_age					= '".$Accompany_3_Age."',
															accompany3_food_preference		= '".$Accompany_3_Food_Preference."',
															accompany3_is_banquet			= '".$Accompany_3_Banquet_Dinner."',
															accompany4_name					= '".$Accompany_4_Name."',
															accompany4_age					= '".$Accompany_4_Age."',
															accompany4_food_preference		= '".$Accompany_4_Food_Preference."',
															accompany4_is_banquet			= '".$Accompany_4_Banquet_Dinner."',
															registration_classification_id	= '".$registration_classification_id."',
															registration_tariff_cutoff_id	= '".$registration_cutoff."',
															exhibitor_company_id			= '".$exhibitor_company_id."',
															payment_mode					= '".$payment_mode."'";
			$mycms->sql_insert($sqlInsertExcel);
		}
		
		$sqlInsertIntoSessionDetails			= array();
		$sqlInsertIntoSessionDetails['QUERY']	= " UPDATE "._DB_BLUK_REGISTRATION_SESSION_." 
													   SET `uploadedDateTime` = '".date("Y-m-d h:i:s")."',
													   	   `status` = 'A'
													 WHERE `id` = '".$lastInsertedSessionDetails."'";
		$lastInsertedSessionDetails	= $mycms->sql_update($sqlInsertIntoSessionDetails);
?>
		<center>
		<form action="<?=_BASE_URL_?>exhibitor.bulkentry.notification.php" method="post" name="srchProcessFrm">	
			<input type="hidden" name="Submissionid" value="<?=$lastInsertedSessionDetails?>" />			
			<h5 align="center">Running Upload Process<br />Please Wait</h5>
			<img src="<?=_BASE_URL_?>images/PaymentPreloader.gif"/><br/>
			<h3 align="center">Please do not click 'back' or 'refresh' button or close the browser window.</h3>
			<br/>
			<hr />
		</form>
		</center>
		<script  type="text/javascript">
		document.srchProcessFrm.submit();
		</script>
<?
		exit();
	}
	
	function dataForcedCompleteProcess()
	{
		global $mycms, $cfg;
		
		$lastInsertedSessionDetails = $_REQUEST['bulkUploadSessionId'];
		
		$sqlfetchUpload			 = array();
		$sqlfetchUpload['QUERY'] = "SELECT * FROM "._DB_BLUK_REGISTRATION_SESSION_."  WHERE id = '".$lastInsertedSessionDetails."'"; 
		$resfetchUpload    		 = $mycms->sql_select($sqlfetchUpload);
		
		$data 					 = $resfetchUpload[0]['draft_data'];
		$dataValue				 = unserialize($data);
		
		foreach($dataValue as $key=>$value)
		{
			$_REQUEST[$key] = $value;
		}	
		
		dataUploadProcess();
		
		exit();
	}
	
	function dataDraftProcess()
	{
		global $mycms, $cfg;
		
		$lastInsertedSessionDetails = $_REQUEST['bulkUploadSessionId'];
		
		$sqlInsertIntoSessionDetails 			= array();
		$sqlInsertIntoSessionDetails['QUERY']	= " UPDATE "._DB_BLUK_REGISTRATION_SESSION_." 
													   SET `draft_data` = '".addslashes(serialize($_REQUEST))."'
													 WHERE `id` 		= '".$lastInsertedSessionDetails."'";
		$lastInsertedSessionDetails	= $mycms->sql_update($sqlInsertIntoSessionDetails);
		exit();
	}
	
	function bulkRegistrationErrorDownload($cfg, $mycms)
	{
		ini_set('max_execution_time', 1000);
		header("Pragma: public");
		header("Expires: 0");
		header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
		header("Content-Type: application/octet-stream");
		header('Content-Type: application/vnd.ms-excel');
		header("Content-Type: application/download");
		header("Content-Disposition: attachment;filename=uploadError".time().".xls");
		
		$sessionId = $_REQUEST['Submissionid'];
		
		$sqlfetchFAL 			= array();
		$sqlfetchFAL['QUERY'] = "  SELECT *
									 FROM "._DB_BLUK_REGISTRATION_DATA_." 
									WHERE session_id = '".$sessionId."'"; 
		$resfetchFAL    = $mycms->sql_select($sqlfetchFAL);
		?>
		<table width="100%" border="1">
			<tr class="theader" style="background:#FFFF00; font-weight:bold;">
				<td width="30" align="center" rowspan="2">SL</td>
				<td width="200" align="center" rowspan="2">email id</td>
				<td width="200" align="center" rowspan="2">first name</td>
				<td width="200" align="center" rowspan="2">last name</td>				
				<td width="200" align="center" rowspan="2">mobile no</td>
				<td width="200" align="center" rowspan="2">country</td>
				<td width="200" align="center" rowspan="2">state</td>
				<td width="200" align="center" rowspan="2">banquet dinner</td>				
				<td width="200" align="center">WORKSHOP</td>
				<td align="center" colspan="3">Accompany 1</td>
				<td align="center" colspan="3">Accompany 2</td>
				<td align="center" colspan="3">Accompany 3</td>
				<td align="center" colspan="3">Accompany 4</td>
			</tr>
			
			<tr class="theader" style="background:#FFFF00;">				
				<td align="center" valign="top">Urogynaecology, CTG & Obstetretics Skills, Infertility & Reproductive Endocrinology, Fetal Medicine, None, RCOG USG TOT Plus</td>
				<td width="200" align="center" valign="top">Name</td>
				<td width="200" align="center" valign="top">Food Preference (Veg, NONVEG)</td>
				<td width="200" align="center" valign="top">Banquet Dinner   (YES,NO)</td>
				<td width="200" align="center" valign="top">Name</td>
				<td width="200" align="center" valign="top">Food Preference (Veg, NONVEG)</td>
				<td width="200" align="center" valign="top">Banquet Dinner   (YES,NO)</td>
				<td width="200" align="center" valign="top">Name</td>
				<td width="200" align="center" valign="top">Food Preference (Veg, NONVEG)</td>
				<td width="200" align="center" valign="top">Banquet Dinner   (YES,NO)</td>
				<td width="200" align="center" valign="top">Name</td>
				<td width="200" align="center" valign="top">Food Preference (Veg, NONVEG)</td>
				<td width="200" align="center" valign="top">Banquet Dinner   (YES,NO)</td>	
			</tr>
			
		<?
		$counter = 0;
		foreach($resfetchFAL as $k=>$rowFAL)
		{
			$counter++;
		?>
			<tr>
				<td align="center" valign="top"><?=$counter?></td>
				<td align="center" valign="top"><?=$rowFAL['user_email_id']?></td>
				<td align="center" valign="top"><?=$rowFAL['user_first_name']?></td>
				<td align="center" valign="top"><?=$rowFAL['user_last_name']?></td>	
				<td align="center" valign="top"><?=$rowFAL['user_mobile_no']?></td>
				<td align="center" valign="top"><?=$rowFAL['user_country']?></td>
				<td align="center" valign="top"><?=$rowFAL['user_state']?></td>
				<td align="center" valign="top"><?=($rowFAL['is_banquet']!='')?'YES':'NO'?></td>
				<td align="center" valign="top"><?=$rowFAL['workshop']?></td>
				<td align="center" valign="top"><?=$rowFAL['accompany1_name']?></td>
				<td align="center" valign="top"><?=$rowFAL['accompany1_food_preference']?></td>
				<td align="center" valign="top"><?=($rowFAL['accompany1_name']!='')?(($rowFAL['accompany1_is_banquet']!='')?'YES':'NO'):""?></td>
				<td align="center" valign="top"><?=$rowFAL['accompany2_name']?></td>
				<td align="center" valign="top"><?=$rowFAL['accompany2_food_preference']?></td>
				<td align="center" valign="top"><?=($rowFAL['accompany2_name']!='')?(($rowFAL['accompany2_is_banquet']!='')?'YES':'NO'):""?></td>
				<td align="center" valign="top"><?=$rowFAL['accompany3_name']?></td>
				<td align="center" valign="top"><?=$rowFAL['accompany3_food_preference']?></td>
				<td align="center" valign="top"><?=($rowFAL['accompany3_name']!='')?(($rowFAL['accompany3_is_banquet']!='')?'YES':'NO'):""?></td>
				<td align="center" valign="top"><?=$rowFAL['accompany4_name']?></td>
				<td align="center" valign="top"><?=$rowFAL['accompany4_food_preference']?></td>
				<td align="center" valign="top"><?=($rowFAL['accompany4_name']!='')?(($rowFAL['accompany4_is_banquet']!='')?'YES':'NO'):""?></td>	
			</tr>
		<?php
				
		}
		?>
		<tr>
			<td align="left" colspan="8">
				<b>Excel Download Date and Time : <?=date('d/m/Y h:i A')?>	</b>											
			</td>
		</tr> 	
		</table>
		<?
	}
?>
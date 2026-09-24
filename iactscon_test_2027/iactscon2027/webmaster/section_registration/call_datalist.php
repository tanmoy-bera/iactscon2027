<?php
	include_once('includes/init.php');
	
	include_once('../../includes/function.delegate.php');
	
	if($show=='trash' || $show=='viewtrash')
	{
		page_header("Trash");
	}
	else if($show=='cancelRequests' || $show=='viewforCancelRequests')
	{
		page_header("Cancel Request");
	}
	else if($show=='canceledList' || $show=='viewCanceledRequests')
	{
		page_header("Conference Unregister");
	}
	else
	{
		page_header("Detailed Call Record Listing");
	}
	
	$indexVal          = 1;
	$pageKey           = "_pgn".$indexVal."_";
	
	$pageKeyVal        = ($_REQUEST[$pageKey]=="")?0:$_REQUEST[$pageKey];
	
	@$searchString     = "";
	$searchArray       = array();
	
	$searchArray[$pageKey]                     = $pageKeyVal;
	$searchArray['src_email_id']               = addslashes(trim($_REQUEST['src_email_id']));
	$searchArray['src_access_key']             = addslashes(trim($_REQUEST['src_access_key'],'#'));
	$searchArray['src_mobile_no']              = addslashes(trim($_REQUEST['src_mobile_no']));
	$searchArray['src_user_first_name']        = addslashes(trim($_REQUEST['src_user_first_name']));
	$searchArray['src_user_middle_name']       = addslashes(trim($_REQUEST['src_user_middle_name']));
	$searchArray['src_invoice_no']     		   = addslashes(trim($_REQUEST['src_invoice_no']));
	$searchArray['src_slip_no']       		   = addslashes(trim($_REQUEST['src_slip_no'],'##'));
	$searchArray['src_registration_mode']      = addslashes(trim($_REQUEST['src_registration_mode']));
	$searchArray['src_user_last_name']         = addslashes(trim($_REQUEST['src_user_last_name']));
	$searchArray['src_atom_transaction_ids']   = addslashes(trim($_REQUEST['src_atom_transaction_ids']));
	$searchArray['src_transaction_ids']        = addslashes(trim($_REQUEST['src_transaction_ids']));
	$searchArray['src_conf_reg_category']      = addslashes(trim($_REQUEST['src_conf_reg_category']));
	$searchArray['src_reg_category']           = addslashes(trim($_REQUEST['src_reg_category']));
	$searchArray['src_registration_id']        = addslashes(trim($_REQUEST['src_registration_id']));
	
	foreach($searchArray as $searchKey=>$searchVal)
	{
		if($searchVal!="")
		{
			$searchString .= "&".$searchKey."=".$searchVal;
		}
	}
	javaScriptDefinedValue();
?>	
	<script type="text/javascript" language="javascript" src="scripts/registration.tariff.js"></script>
	<script type="text/javascript" language="javascript" src="scripts/general_registration_new.js"></script>
	<script type="text/javascript" language="javascript" src="scripts/CountryStateCityRetriver.js"></script>
	<script type="text/javascript" language="javascript" src="../../js/returnData.process.js"></script>
	<script type="text/javascript" language="javascript" src="scripts/workshop_registration.js"></script>
	<script type="text/javascript" language="javascript" src="scripts/registration.js"></script>	
	<style>
		.paymentDtls{
			width: 50%;
			float: left;
		}
		.paidStatus{
			width: 50%;
			color: green;
		}
		.unpaidStatus{
			width: 50%;
			color: red;
		}
		.paymentArea{
			background-color:#1e7bac;
			border-bottom:2px solid #DB5600;
			border-radius: 5px;
			padding:12px;
			font-size:22px;
			font-weight:bold;
			color:#000;
			margin:15px 0 0 0;
		}
	</style>
	<div class="container">
		<?php 
			switch($show){
				
				// VIEW GENERAL REGISTRATION WINDOW
									
				default:	
				
					viewAllGeneralRegistration($cfg, $mycms);
					break;
					
			} 
		?>
	</div>
	<style>
	.popup_form
		{
			display: none;
			background-color: #FFF;
			width: 54%;
			position: fixed;
			top: 122px;
			left: 45%;
			height: 40%;
			margin-left: -204px;
			padding: 1px;
			z-index: 14;
			box-shadow: 2px 2px 20px rgba(0, 0, 0, 1);
			border-radius: 15px;
		
		}
	.popup_check_data_form
		{
			display: none;
			background-color: #FFF;
			width: 54%;
			position: fixed;
			top: 122px;
			left: 45%;
			height: 40%;
			margin-left: -204px;
			padding: 1px;
			z-index: 14;
			box-shadow: 2px 2px 20px rgba(0, 0, 0, 1);
			border-radius: 15px;
		
		}
	</style>
	<script>
	function openDetailData(delegateId)
	{
		//alert(delegateId);
		$("#popup_form").fadeIn("slow");
		$("#delegateId").val(delegateId);
		
	}
	function openToCheckData(delegateId)
	{
			console.log("http://localhost/kasscon/dev/developer/webmaster/section_travel/pickup_users.process.php?act=CheckUsers&delegateId="+delegateId);
			$.ajax({
					type: "POST",
					url: 'pickup_users.process.php',
					data: 'act=CheckUsers&delegateId='+delegateId,
					dataType: 'html',
					async: false,
					success:function(returnMessage)
					{
						$('#popup_check_data_form').html(returnMessage);
						$("#popup_check_data_form").fadeIn("slow");
					
					}
			});
			
			 //openrofileDetailsPopUp();
			//$("#popup_profile_full_details").show(3000);
			

	}
	function closePopup()
	{
		$("#popup_form").fadeOut("slow");
	}
	</script>
	
	<div class="popup_form" id="popup_form">
			<form action="pickup_users.process.php" name="nameUpdatePopup" id="nameUpdatePopup" method="post" onSubmit="return formNameValidation();">
				<input type="hidden" name="act" value="add" />	
				<input type="hidden" name="delegateId" id="delegateId">
				<table width="100%" class="tborder">
				<tr>
					<td colspan="4" class="tcat" style="background: cadetblue;">
						<span style="float:left" >Name Update</span>
						<span class="close" forType="tsearchTool" onClick="closePopup()">&times;</span>
					</td>
				</tr>
				<tr class="tcat">
					<td width="30" align="center" data-sort="int">From</td>
					<td align="left" width="140" align="center">To</td>
					<td align="left" width="240">Driver Details</td>
					<td align="left" width="140">Pick & Drop DateTime</td>
				</tr>
					
					<tr>
						<td width="22%" align="left" valign="top"><input type="text" name="user_source_location" id="user_source_location" style="width:95%; text-transform:uppercase;" value=""/></td>
						<td width="22%" align="left" valign="top"><input type="text" name="user_destination_location" id="user_destination_location" style="width:95%; text-transform:uppercase;" value=""/></td>
						<td align="center" valign="top" width="22%">
						<select name="driver_id" id="driver_id" style="width:100%;">
							<option value="">-Select Category-</option>
							<?php
							$sqlFetchDriverData				 =	array();
							$sqlFetchDriverData['QUERY']	 = "SELECT `id`,`driver_name`,`driver_phone_no` FROM "._DB_MASTER_CAR_DETAILS_." WHERE status = 'A' ";
							$resultDriverData	 = $mycms->sql_select($sqlFetchDriverData);			
							
							
							if($resultDriverData)
							{
								foreach($resultDriverData as $key=>$rowDriverData) 
								{
								?>
									<option value="<?=$rowDriverData['id']?>" >
									<?=($rowDriverData['driver_name'])?> <&nbsp;<?=($rowDriverData['driver_phone_no'])?> >
									</option>
								<?php
								}
							}
							?>
						</select>
						</td>
						<td valign="top">
							<input type="text" name="pickupDate" id="pickupDate" value=""  readonly="readonly" rel="tcal" style="width:35%" placeholder="Choose Pickup Date" />&nbsp; -&nbsp; 
								
								<span rel="timeChooser">
									<input type="text" name="pickupTimeHour" id="pickupTimeHour" specific="hour" value="" style="width:15%;  text-align:center;" placeholder="HH" /> : 
									 
									<input type="text" name="pickupTimeMin" id="pickupTimeMin" specific="min" value="" style="width:15%;   text-align:center" placeholder="MM" />
								</span>
								<br><br>
							<input type="text" name="dropOffDate" id="dropOffDate" value=""  readonly="readonly" rel="tcal" style="width:35%" placeholder="Choose Dropoff Date" />&nbsp; -&nbsp; 
								
								<span rel="timeChooser">
									<input type="text" name="dropOffTimeHour" id="dropOffTimeHour" specific="hour" value="" style="width:15%;  text-align:center;" placeholder="HH" /> : 
									 
									<input type="text" name="dropOffTimeMin" id="dropOffTimeMin" specific="min" value="" style="width:15%;   text-align:center" placeholder="MM" />
								</span>
								<br><br>
							<input type="submit" name="submitData" id="submitData" class="btn btn-small btn-blue" align="right" style="margin-left:180px;">
						</td>
					</tr>
					
					<tr>
						<td colspan="4"></td>
					</tr>
				</table>		
			</form>
		</div>
		
	<div class="popup_check_data_form" id="popup_check_data_form"></div>
<?php

	page_footer();
	
	
	/****************************************************************************/
	/*                      SHOW ALL GENERAL REGISTRATION                       */
	/****************************************************************************/
	
	function viewAllGeneralRegistration($cfg, $mycms)
	{
		global $searchArray, $searchString;
		
		$loggedUserId		= $mycms->getLoggedUserId();
		//$access				= buttonAccess($loggedUserId);
		// FETCHING LOGGED USER DETAILS
		$sqlSystemUser				=	array();
		$sqlSystemUser['QUERY']      = "SELECT * FROM "._DB_CONF_USER_." 
		                              	 WHERE `a_id` = '".$loggedUserId."'";
									   
		$resultSystemUser   = $mycms->sql_select($sqlSystemUser);
		$rowSystemUser      = $resultSystemUser[0];
		
		$searchApplication  = 0;
		
		$rowFetchUser       = getUserDetails($_REQUEST['delegateId']);
		
	?>
		<script>
				$(document).ready(function(){
					
					arrivalSelection();
					depertureSelection();
					$("#arivalDate").datepicker({
						 dateFormat :"yy-mm-dd",
						 minDate:'2018-05-18',
						 maxDate:'2018-05-21',
						 changeMonth: true,
						 changeYear: true
					});
					
					$("input[operationMode=arrivalThrough]").change(function(){
						arrivalSelection();
					});
					
					$("#departureDate").datepicker({
						 dateFormat :"yy-mm-dd",
						  minDate:'2018-05-18',
						  maxDate:'2018-05-21',
						 changeMonth: true,
						 changeYear: true
					});
					
					$("input[operationMode=departureThrough]").change(function(){
						depertureSelection();
					});
					initiateTimeChooser();
				});
				
				function arrivalSelection()
				{
					$("input[name=arivalToCity]").attr('checked',false);
					
					if($("input[operationMode=arrivalThrough]:checked").val()=="FLIGHT"){
						$("span[use=viaFlightAp],tr[use=viaFlightAp]").show();
						$("span[use=viaTrainAp],tr[use=viaTrainAp]").hide();
					}
					else{
						$("span[use=viaTrainAp],tr[use=viaTrainAp]").show();
						$("span[use=viaFlightAp],tr[use=viaFlightAp]").hide();
						
					}
				}
				
				function showDriverDetails(DriverId)
				{ 
					
					console.log("http://localhost/kasscon/dev/developer/webmaster/section_travel/registration.process.php?act=DriverDetails&DriverId="+DriverId);
					$.ajax({
						type: "POST",
						url: "registration.process.php",
						data: "act=DriverDetails&DriverId="+DriverId,
						dataType: "json",
						async: false,
						success: function(JSONObject){
							
							$("#departure_driver_no").val(JSONObject.DRIVER_NUMBER);
							$("#departure_car_name").val(JSONObject.CAR_NAME);
							$("#departure_car_no").val(JSONObject.CAR_NUMBER);
						}
					});	
				}
				function showArrivalDriverDetails(DriverId)
				{ 
					
					console.log("http://localhost/kasscon/dev/developer/webmaster/section_travel/registration.process.php?act=ArrivalDriverDetails&DriverId="+DriverId);
					$.ajax({
						type: "POST",
						url: "registration.process.php",
						data: "act=ArrivalDriverDetails&DriverId="+DriverId,
						dataType: "json",
						async: false,
						success: function(JSONObject){
							
							$("#arrival_driver_no").val(JSONObject.DRIVER_NUMBER);
							$("#arrival_car_name").val(JSONObject.CAR_NAME);
							$("#arrival_car_no").val(JSONObject.CAR_NUMBER);
						}
					});	
				}
				function depertureSelection()
				{
					$("input[name=departureToCity]").attr('checked',false);
					
					if($("input[operationMode=departureThrough]:checked").val()=="FLIGHT"){
						$("span[use=viaFlightDp],tr[use=viaFlightDp]").show();
						$("span[use=viaTrainDp],tr[use=viaTrainDp]").hide();
					}
					else{
						$("span[use=viaTrainDp],tr[use=viaTrainDp]").show();
						$("span[use=viaFlightDp],tr[use=viaFlightDp]").hide();
					}
				}
				
			</script>
			
		<table width="100%" class="tborder" align="center"> 	 				
			<tr>
				<td colspan="2" style="margin:0px; padding:0px;">					
					<table width="100%">
						<tr class="thighlight">
							<td colspan="4" align="left">User Details</td>
						</tr>
						<tr>
							<td width="20%" align="left">Name:</td>
							<td width="30%" align="left">
								<?=strtoupper($rowFetchUser['user_full_name'])?> 
								
							</td>
							<td width="20%" align="left">Registration Type:</td>
							<td width="30%" align="left"><span style="color:<?=$rowFetchUser['registration_request'] =='GENERAL'?'#339900':'#cc0000'?>;"><b><?=$rowFetchUser['registration_request']?></b></span></td>
						</tr>
						<tr>
							<td align="left">Registration Id:</td>
							<td align="left">	
							<?php
							if($rowFetchUser['registration_payment_status']!="UNPAID")
							{
								echo $rowFetchUser['user_registration_id'];
							}
							else
							{
								echo "-";
							}
							?>
							</td>
							<td align="left">Unique Sequence:</td>
							<td align="left">
							<?php
							if($rowFetchUser['registration_payment_status']!="UNPAID")
							{
								echo $rowFetchUser['user_unique_sequence'];
							}
							else
							{
								echo "-";
							}
							?></td>
						</tr>
						<tr>
							<td align="left">Mobile:</td>
							<td align="left"><?=$rowFetchUser['user_mobile_isd_code']." - ".$rowFetchUser['user_mobile_no']?></td>
							<td align="left">Email Id:</td>
							<td align="left"><?=$rowFetchUser['user_email_id']?></td>
						</tr>
						<tr>
							<td align="left">Registration Mode:</td>
							<td align="left"><?=strtoupper($rowFetchUser['registration_mode'])?></td>
							<td align="left">Registration Date:</td>
							<td align="left"><?=date('d/m/Y h:i A', strtotime($rowFetchUser['conf_reg_date']))?></td>
						</tr>
					</table>
												
					<table width="100%" shortData="on" >
						<thead>
							<tr class="thighlight">
								<td colspan="5" align="left">Counications</td>
							</tr>
							<tr class="theader">
								<td width="30" align="center" data-sort="int">Sl No</th>
								<td align="left" width="140">Attended By</th>
								<!--<td width="110" align="left">Registration Type</th>
								<td width="180" align="left">Registration Details</th>-->
								<td width="280" align="center" >Call Date Time</th>
								<td  align="left" width="240">Discussion Details</th>
								<td width="300" align="center" >Subject</td>	
							</tr>
						</thead>
						<tbody>
								<? 
								$counter = 0;
								$sqlfetchPickdropdata			=	array();
								$sqlfetchPickdropdata['QUERY'] ="SELECT *, 
																		( CASE WHEN calldetails.logged_user_id = '-1'
																			   THEN 'Submitted by user'
																			   ELSE user.username
																		  END) AS LoggedUserName 
																   FROM "._DB_USER_CALLDETAILS_." calldetails
															 INNER JOIN "._DB_USER_REGISTRATION_." delegate
																	 ON calldetails.delegate_id=delegate.id
														LEFT OUTER JOIN "._DB_CONF_USER_." user
																	 ON calldetails.logged_user_id=user.a_id
																  WHERE `delegate_id`='".$_REQUEST['delegateId']."'
															   ORDER BY calldetails.id ASC"; 
								
								$resfetchPickdropdata    = $mycms->sql_select($sqlfetchPickdropdata);
								
								if($resfetchPickdropdata)
								{
									foreach($resfetchPickdropdata as $key=>$val)
									{
										$counter ++;
										if($val['LoggedUserName']!='')
										{
								?>
									<tr class="tlisting" bgcolor="<?=$color?>">
										<td align="center" valign="top"><?=$counter + ($_REQUEST['_pgn1_']*10)?></td>
										<td align="left" valign="top"><?=$val['LoggedUserName']?>
										</td>
										
										<td align="center" valign="top">
										<?=$val['call_date']?>&nbsp;&nbsp;
										<?=$val['call_time']?>
										</td>	
										<td align="left" valign="top">	
										<?=$val['call_contents']?>	
										<td align="center" valign="top">
										<?=$val['call_subject']?></td>
										</td>
									</tr>
								<?
										}
									}
								}
								else
								{
								?>
									<td></td>
									<td align="center" valign="center" colspan="3">
									No call records ..
									</td>
								<?php
								}
							?>
						</tbody>
					</table>
				</td>
			</tr>			
			<tr class="tfooter">
				<td colspan="2">
					<span class="paginationRecDisplay"><?=$mycms->paginateRecInfo(1)?></span>
					<span class="paginationDisplay"><?=$mycms->paginate(1,'pagination')?></span>
				</td>
			</tr>	
		</table>
		
		<div class="overlay" id="fade_popup" onClick="closeProfileDetailsPopUp()"></div>
		<div class="popup_form" id="popup_profile_full_details"></div>
	<?php
	}
	
	
?>


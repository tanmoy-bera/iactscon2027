<?php
	include_once('includes/init.php');
	include_once('../../includes/function.delegate.php');
	include_once('../../includes/function.registration.php');
	ini_set('max_execution_time', 1000);
	header("Pragma: public");
	header("Expires: 0");
	header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
	header("Content-Type: application/octet-stream");
	header('Content-Type: application/vnd.ms-excel');
	header("Content-Type: application/download");
	header("Content-Disposition: attachment;filename=userexcelreport".time().".xls"); 
	
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
	$searchArray['src_slip_no']       		   = addslashes(trim($_REQUEST['src_slip_no']));
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
	
?>

<table border="1">
		<tr>
			<td colspan="28" align="left"><h4 style="color:#000000;">NORMAL REPORT</h4></td>
		</tr>
		<tr class="theader">
			<td align="center"><b>Sl No</b></td>
			<td align="left"><b>Name</b></td>
			<td align="center"><b>Mobile No</b></td>
			<td align="center"><b>Email Id</b></td>
			<td align="center"><b>Unique Sequence No</b></td>
			<td align="center"><b>Registration Id</b></td>
			<td align="center"><b>Registration Type</b></td>
			<td align="left"><b>City</b></td>
			<td align="left"><b>State</b></td>
			<td align="left"><b>Country</b></td>
			
			<td align="left"><b>Conference Registration Cutoff</b></td>
			<td align="left"><b>Registration Mode</b></td>
			<td align="left"><b>Registration Date</b></td>
			<td align="left"><b>Payment Mode</b></td>
			<td align="left"><b>Transaction No</b></td>
			<td align="center"><b>Conference Payment Status</b></td>
			
			<td align="left"><b>Workshop Registration</b></td>
			<td align="left"><b>Registration Mode</b></td>
			<td align="center"><b>Registration Payment Status</b></td>
			
		</tr>
			<!--<td align="left"><b>Workshop Registration</b></td>
			<td align="left"><b>Registration Mode</b></td>
			<td align="center"><b>Registration Payment Status</b></td>
			
			<td align="left"><b>Workshop Registration</b></td>
			<td align="left"><b>Registration Mode</b></td>
			<td align="center"><b>Registration Payment Status</b></td>
			
			<td align="left"><b>Workshop Registration</b></td>
			<td align="left"><b>Registration Mode</b></td>
			<td align="center"><b>Registration Payment Status</b></td>-->
		
		
		
		<?php
		@$searchCondition     	   = "";
		//$searchCondition     	  .= " AND delegate.isRegistration = 'Y'";		
		//$sqlFetchUser            = generalRegistrationTransIdsQuerySet("", $searchCondition);		
		$alterCondition = "";
		//$alterCondition = "AND delegate.user_email_id NOT LIKE '%@encoder%'";
		$idArr = registrationFullDetailsQuerySet("","",$alterCondition, "serach");
		
		if($idArr)
		{
			foreach($idArr as $i=>$id) 
			{
				 set_time_limit (0);
				$Slcounter           		  = $Slcounter + 1;
				$rowFetchUser = getUserDetails($id);
				// USER REGISTRATION STATUS
				$regPaymentStatus         = array();
				//$regPaymentStatus         = conferenceRegistrationPaymentStatus($rowFetchUser['id']);
				
				
					$registration_status 		=  "REGISTERED";
					$registration_status_color 	=  "#009900";
				
				$sqlFetchConferenceInvoice = array();	
				$sqlFetchConferenceInvoice['QUERY']    = "SELECT * 
										     		   		FROM "._DB_INVOICE_."
													 	    WHERE delegate_id = '".$rowFetchUser['id']."'
													        AND (service_type = 'DELEGATE_CONFERENCE_REGISTRATION'
														    OR service_type = 'DELEGATE_RESIDENTIAL_REGISTRATION')";
				$resFetchConferenceInvoice	  =	$mycms->sql_select($sqlFetchConferenceInvoice);	
				
				$regOnlinePaymentDetails 		  = "-";
				$regOnlinereference 		  	  = "-";
				$regPaymentDetails 		  = "-";
				$regReference 		  	  = "-";
				
				if($resFetchConferenceInvoice)
				{
					$rowFetchConferenceInvoice = $resFetchConferenceInvoice[0];
					
					     $sqlFetchConferencePayment = array();
						 $sqlFetchConferencePayment['QUERY']    = "SELECT * 
														   				FROM "._DB_PAYMENT_." 
														  				WHERE `slip_id` = '".$rowFetchConferenceInvoice['slip_id']."'
																		AND `delegate_id` = '".$rowFetchUser['id']."' ";
						$resFetchConferencePayment    = $mycms->sql_select($sqlFetchConferencePayment);
						$rowFetchConferencePayment	  = $resFetchConferencePayment[0];
												
						$regPaymentDetails		 	  = strtoupper($rowFetchConferencePayment['payment_mode']);
						if($regPaymentDetails == 'ONLINE')
						{
						$regReference 				  = "".$rowFetchConferencePayment['atom_atom_transaction_id'];
						}
						else
						{
						$regReference		 		  = getOflinePayDetail4Excel($rowFetchConferencePayment);
						}
										
				}
				
				$cutoffTitle = getRegistrationCutoff($rowFetchUser['registration_tariff_cutoff_id']);
				
				?>
		
				<tr class="tlisting">
					<td align="center" valign="top" for="Sl No"><?=$Slcounter?></td>
					<td align="left" valign="top"  for="Name">
						<?=$rowFetchUser['user_title'].' '.$rowFetchUser['user_first_name'].' '.$rowFetchUser['user_middle_name'].' '.$rowFetchUser['user_last_name']?>
					</td>
					<td align="center" valign="top" for="Mobile No"><?=$rowFetchUser['user_mobile_isd_code']?> <?=$rowFetchUser['user_mobile_no']?></td>
					<td align="center" valign="top" for="Email Id"><?=$rowFetchUser['user_email_id']?></td>
					<td align="center" valign="top" for="Unique Sequence No">
						<?	if($rowFetchUser['registration_payment_status']=="PAID" 
							   || $rowFetchUser['registration_payment_status']=="COMPLIMENTARY"
							   || $rowFetchUser['registration_payment_status']=="ZERO_VALUE")
							{
								echo strtoupper($rowFetchUser['user_unique_sequence']);
								echo "<br />";
							}
							else
							{
								echo "-";
							}
						?>
					</td>
					<td align="center" valign="top" for="Registration Id">
					<?php
							if($rowFetchUser['registration_payment_status']=="PAID" 
							   || $rowFetchUser['registration_payment_status']=="COMPLIMENTARY"
							   || $rowFetchUser['registration_payment_status']=="ZERO_VALUE")
							{
								echo $rowFetchUser['user_registration_id'];
								echo "<br />";
							}
							else
							{
								echo "-";
								echo "<br />";
							}
					?>
					</td>
					<td align="center" valign="top" for="Registration Type">
					<?php
						if($rowFetchUser['isRegistration']=="Y")
						{
							echo getRegClsfName($rowFetchUser['registration_classification_id']);
							
						}
					?>
					</td>
					<td align="center" valign="top" for="City"><?=$rowFetchUser['user_city']?></td>
					<td align="center" valign="top" for="State"><?=$rowFetchUser['state_name']?></td>
					<td align="center" valign="top" for="Country"><?=$rowFetchUser['country_name']?></td>									
					<td align="left" valign="top" for="Conference Registration Cutoff"><?=getCutoffName($rowFetchUser['registration_tariff_cutoff_id'])?></td>
					<td align="center" valign="top" for="Registration Mode"><?=$rowFetchUser['registration_mode']?></td>	
					<td align="left" valign="top" for="RegistrationMode"><?=date('d/m/Y h:i A', strtotime($rowFetchUser['created_dateTime']))?></td>	
					<td align="center" valign="top" for="Payment Mode">
					<?php					
						echo $regPaymentDetails;
					?>
					</td>
					<td align="center" valign="top" for="Transaction No"><?=$regReference?></td>				
					<td align="center" valign="top" for="Conference Payment Status"><?=$rowFetchUser['registration_payment_status']?></td>
					<?				
						
					// WORKSHOP STATUS
					$workshopPaymentStatus    = array();
					//$workshopPaymentStatus    = workshopRegistrationPaymentStatus($rowFetchUser['id']);
					
					 $sqlFetchWorkshop['QUERY']        = "SELECT 
					 											workshopBooking.*,workshopTariff.classification_title,
																workshopBooking.booking_mode,invoiceDtls.payment_status
																									   
												  				FROM "._DB_REQUEST_WORKSHOP_." workshopBooking
												  
															    INNER JOIN "._DB_TARIFF_CUTOFF_." tariffCutoff 
																ON workshopBooking.tariff_cutoff_id = tariffCutoff.id 
											
																INNER JOIN "._DB_WORKSHOP_CLASSIFICATION_." workshopTariff 
																ON workshopBooking.workshop_id = workshopTariff.id 
																	
																INNER JOIN "._DB_INVOICE_." AS invoiceDtls
																ON workshopBooking.refference_invoice_id = invoiceDtls.id
													 			AND invoiceDtls.status = 'A'
													 
												 				WHERE workshopBooking.delegate_id = '".$rowFetchUser['id']."' 
												   				AND invoiceDtls.service_type = 'DELEGATE_WORKSHOP_REGISTRATION'
												   				AND workshopBooking.status = 'A' ";	
												  											  
												 
					$resultWorkshoparray     = $mycms->sql_select($sqlFetchWorkshop);
					/*echo "<pre>";
					print_r($resultWorkshoparray);
					echo "</pre>";*/
					
					$rowWorkshop 			 = $resultWorkshoparray[0];
					
					$workshopOnlinePaymentDetails 		  = "-";
					$workshopOnlinereference 		  	  = "-";
					$workshopOfflinePaymentDetails 		  = "-";
					$workshopOfflinereference 		  	  = "-";
				
					
					
					//$workshopCutoffTitle = getRegistrationCutoff($rowWorkshop['tariff_cutoff_id']);
					if($resultWorkshoparray){
						$counter = 0;
						 
						foreach($resultWorkshoparray as $key=> $rowWorkshop)
						{
							
						?>
										
						<td align="center" valign="top" for="Workshop Registration"><?=$rowWorkshop['classification_title']?></td>
						
						<td align="center" valign="top" for="Workshop Registration Mode"><?=$rowWorkshop['booking_mode']?></td>
						<td align="center" valign="top" for="Workshop Payment Status"><?=$rowWorkshop['payment_status']?></td>
						<?
							$counter ++;
							$count = $counter;
						}
						}
					?>
				</tr>
		<?php
			
			}
		} 
		else 
		{
		?>
			<tr>
				<td colspan="8" align="center">
					<span class="mandatory">No Record Present.</span>												
				</td>
			</tr>  
		<?php 
		} 
		?>
		<tr>
			<td align="left">
				<h3>Excel Download Date and Time : <?=date('d/m/Y h:i A')?>	</h3>											
			</td>
	  	</tr> 
	</table>
<?
	exit();
	
	function getOflinePayDetail4Excel($rowPayment)
	{
		global $mycms, $cfg;
		
		$payDetails = "-";
		switch($rowPayment['payment_mode'])
		{
			case "Cash" :
				$payDetails 		  	  = "-";
				break;
			case "Cheque" :
				$payDetails 		  	  = "Cheque No. :".$rowPayment['cheque_number'];
				break;
			case "Draft" :
				$payDetails 		  	  = "Draft No. :".$rowPayment['draft_number'];
				break;
			case "NEFT" :
				$payDetails 		  	  = "UTR No. :".$rowPayment['neft_transaction_no'];
				break;
			case "RTGS" :
				$payDetails 		  	  = "UTR No. :".$rowPayment['rtgs_transaction_no'];
				break;
		}
		return $payDetails;
	}
	
	function getRegistrationCutoff($cutoffId)
	{
		$cutoffTitle = '';
		switch($rowFetchUser['registration_tariff_cutoff_id'])
		{
			case "1" :
				$cutoffTitle = "Early Bird";
				break;
			case "2" :
				$cutoffTitle = "2nd Cut-off";
				break;
			case "3" :
				$cutoffTitle = "3rd Cut-off";
				break;
			case "4" :
				$cutoffTitle = "Spot";
				break;
		}
		return $cutoffTitle;
	}
?>

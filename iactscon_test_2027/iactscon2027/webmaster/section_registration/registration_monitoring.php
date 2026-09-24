<?php

	include_once('includes/init.php');
	include_once("../../includes/function.php");
	include_once("../../includes/function.workshop.php");
	page_header("Monitoring Window");
	
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
	$searchArray['src_reg_date']               = addslashes(trim($_REQUEST['src_reg_date']));
	$searchArray['src_reg_month']              = addslashes(trim($_REQUEST['src_reg_month']));
	$searchArray['src_country_id']             = addslashes(trim($_REQUEST['src_country_id']));
	$searchArray['src_state_id']               = addslashes(trim($_REQUEST['src_state_id']));
	$searchArray['c']              			   = addslashes(trim($_REQUEST['c']));
	
	foreach($searchArray as $searchKey=>$searchVal)
	{
		if($searchVal!="")
		{
			$searchString .= "&".$searchKey."=".$searchVal;
		}
	}
?>
	<script type="text/javascript" language="javascript" src="scripts/CountryStateCityRetriver.js"></script>
	<script type="text/javascript" language="javascript" src="scripts/registration.js"></script>
	<style>
		.tborder .ttextlarge{font-size:20px; font-family:Arial, Helvetica, sans-serif;}
		.tborder .tdpadding {padding: 8px 0 8px 15px;}
	</style>
	<div class="container">
		<?php 
			switch($show){
				
				// REGISTRATION TIME RELETEATED
				case'registration':
				
					monitaringGeneralRegistration($cfg, $mycms);
					break;
					
				case'view_registration':
					
					viewGeneralRegistration($cfg, $mycms);
					break;
					
				case'view_delegate':
					
					delegateViewFormTemplate($cfg, $mycms);
					break;
				// LOCATION RELETED	
				
				case'registration_location':
				
					monitaringGeneralRegistrationByLocation($cfg, $mycms);
					break;
					
				case'registration_state':
				
					monitaringGeneralRegistrationByState($cfg, $mycms);
					break;
					
				case'view_registration_location':
					
					viewGeneralRegistration($cfg, $mycms);
					break;
					
				case'view_delegate_location':
					
					delegateViewFormTemplate($cfg, $mycms);
					break;
					
				case'print_invoice':
				
					$requestPage  = "registration_monitoring.php"; 
					$processPage  = "monitoring.process.php"; 
					
					printInvoiceTemplate($requestPage, $processPage);	
					break;
					
			  case'view_monthwise_report':
					
					monitaringGeneralRegistrationMonthwise($cfg, $mycms);
					break;	
						
				// SHOW ALL GENERAL REGISTRATION WINDOW
				default:	
				
					break;
			} 
		?>
	</div>
<?php

	page_footer();
	
	/****************************************************************************/
	/*                      SHOW ALL GENERAL REGISTRATION                       */
	/****************************************************************************/
	function monitaringGeneralRegistrationMonthwise($cfg, $mycms)
	{
		global $searchArray, $searchString;
		
		$loggedUserId		= $mycms->getLoggedUserId();
		
		// FETCHING LOGGED USER DETAILS
		$loggedUserId = array();
		$sqlSystemUser['QUERY']      = "SELECT * FROM "._DB_CONF_USER_." 
		                               WHERE `a_id` = ? ";
									   
		$sqlSystemUser['PARAM'][]  = array('FILD' => 'a_id',  'DATA' => $loggedUserId  ,  'TYP' => 's');

		$resultSystemUser   = $mycms->sql_select($sqlSystemUser);
		$rowSystemUser      = $resultSystemUser[0];
		
		$searchApplication  = 0;
		
		
	?>
			<form name="frmSearch" method="post" action="monitoring.process.php" onSubmit="return FormValidator.validate(this);">
			<input type="hidden" name="act" value="search_month" />
			<table width="100%" class="tborder" align="center">	
				<tr>
					<td class="tcat" colspan="2" align="left">
						<span style="float:left">Registration Reports</span>
						<span class="tsearchTool" forType="tsearchTool"></span>
					</td>
				</tr>
				<tr>
					<td colspan="2" style="margin:0px; padding:0px;">
						
						<div class="tsearch" style="display:block;">
							
						</div>			
								
						<table width="70%" shortData="on" style="margin: 15px 0 15px 15px;" class="tborder" >
							<tbody>
								<tr>
									<td align="left" class="" style="border-bottom:1px solid #000; color:#FF3300;"><b>MONTH</b></td>
									<td align="center" class="" style="border-bottom:1px solid #000; color:#FF3300;"><b>PAID</b></td>
									<td align="center" class="" style="border-bottom:1px solid #000; color:#FF3300;"><b>COMPLIMENTARY</b></td>
									<td align="center" class="" style="border-bottom:1px solid #000; color:#FF3300;"><b>UNPAID</b></td>
									<td align="center" class="" style="border-bottom:1px solid #000; color:#FF3300;"><b>TOTAL</b></td>
									<td align="center" class="" style="border-bottom:1px solid #000; color:#FF3300;"><b>ACTION</b></td>
								</tr>
								<?php
								@$searchCondition       = "";
								
								if($_REQUEST['src_reg_month']!='')
								{
									
									 $searchCondition   .= " AND EXTRACT(MONTH FROM delegate.created_dateTime) = '".$_REQUEST['src_reg_month']."'";
								}
								$sqlFetchUserRegDate = array();
								$sqlFetchUserRegDate['QUERY']  = "	SELECT DISTINCT(EXTRACT(MONTH FROM delegate.created_dateTime)) AS regDate,
																DATE(delegate.created_dateTime)
															  FROM "._DB_USER_REGISTRATION_." delegate 
															 WHERE delegate.user_type = ?
															   AND delegate.status = ? AND delegate.operational_area != ?
															   AND delegate.isRegistration = ? 
															   AND delegate.isConference = ?
															   AND user_email_id NOT LIKE '%@encoder%'
															    ".$searchCondition."
														  ORDER BY DATE(delegate.created_dateTime) DESC";
							
									
								$sqlFetchUserRegDate['PARAM'][]  = array('FILD' => 'delegate.user_type',  'DATA' => 'DELEGATE'  ,  'TYP' => 's');
								$sqlFetchUserRegDate['PARAM'][]  = array('FILD' => 'delegate.status',  'DATA' => 'A'  ,  'TYP' => 's');
								$sqlFetchUserRegDate['PARAM'][]  = array('FILD' => 'delegate.operational_area',  'DATA' => 'EXHIBITOR'  ,  'TYP' => 's');
								$sqlFetchUserRegDate['PARAM'][]  = array('FILD' => 'delegate.isRegistration',  'DATA' => 'Y'  ,  'TYP' => 's');
								$sqlFetchUserRegDate['PARAM'][]  = array('FILD' => 'delegate.isConference',  'DATA' => 'Y'  ,  'TYP' => 's');
								


								$resultFetchUserRegDate = $mycms->sql_select($sqlFetchUserRegDate);	
								
								if($resultFetchUserRegDate)
								{
									
									foreach($resultFetchUserRegDate as $i=>$rowFetchDate) 
									{
										$counter             = $counter + 1;
										
								?>
										<tr class="tlisting">
											<td align="left"  class="ttextlarge tdpadding" >
												<?
												$monthNum  = $rowFetchDate['regDate'];
												$dateObj   = DateTime::createFromFormat('!m', $monthNum);
												$monthName = $dateObj->format('F');
												echo $monthName;
												?> 
											</td>
											<td align="center"  class="ttextlarge" width="70" >
												<?php
												    $sqlFetchPaidUserCount = array();
													$sqlFetchPaidUserCount['QUERY'] = "	SELECT COUNT(`id`) AS regUser 
																				  FROM  "._DB_USER_REGISTRATION_."  
																				 WHERE user_type = ?
																				   AND status = ? 
																				   AND operational_area != ?
																				   AND isRegistration = ? 
																				   AND isConference = ?
																				   AND user_email_id NOT LIKE '%@encoder%'
																				   AND registration_payment_status = ?
																				   AND EXTRACT(MONTH FROM created_dateTime) = '".$rowFetchDate['regDate']."'";

													$sqlFetchPaidUserCount['PARAM'][]  = array('FILD' => 'user_type',  'DATA' => 'DELEGATE'  ,  'TYP' => 's');
													$sqlFetchPaidUserCount['PARAM'][]  = array('FILD' => 'status',  'DATA' => 'A'  ,  'TYP' => 's');
													$sqlFetchPaidUserCount['PARAM'][]  = array('FILD' => 'operational_area',  'DATA' => 'EXHIBITOR'  ,  'TYP' => 's');
													$sqlFetchPaidUserCount['PARAM'][]  = array('FILD' => 'isRegistration',  'DATA' => 'Y'  ,  'TYP' => 's');
													$sqlFetchPaidUserCount['PARAM'][]  = array('FILD' => 'isConference',  'DATA' => 'Y'  ,  'TYP' => 's');
													$sqlFetchPaidUserCount['PARAM'][]  = array('FILD' => 'registration_payment_status',  'DATA' => 'PAID'  ,  'TYP' => 's');							   
												
													$resultFetchPaidUserCount = $mycms->sql_select($sqlFetchPaidUserCount);
													echo '<span style="color:green;">'.$totalRegPaidUser	  = $resultFetchPaidUserCount[0]['regUser'].'</span>';
													$totalPaidDelegate     = $totalPaidDelegate + $totalRegPaidUser;
												?>
												</td>
												<td align="center"  class="ttextlarge" width="70" >
												<?php
												    $sqlFetchComplementaryUserCount = array();
													$sqlFetchComplementaryUserCount['QUERY'] = "	SELECT COUNT(`id`) AS regUser 
																						  FROM  "._DB_USER_REGISTRATION_."  
																						 WHERE user_type = ?
																						   AND status = ? 
																						   AND operational_area != ?
																						   AND isRegistration = ? 
																				 		   AND isConference = ?
																				  		   AND user_email_id NOT LIKE '%@encoder%'
																						   AND registration_payment_status = ?
																						   AND EXTRACT(MONTH FROM created_dateTime) = '".$rowFetchDate['regDate']."'";


												    $sqlFetchComplementaryUserCount['PARAM'][]  = array('FILD' => 'user_type',  'DATA' => 'DELEGATE'  ,  'TYP' => 's');
												    $sqlFetchComplementaryUserCount['PARAM'][]  = array('FILD' => 'status',  'DATA' => 'A'  ,  'TYP' => 's');
													$sqlFetchComplementaryUserCount['PARAM'][]  = array('FILD' => 'operational_area',  'DATA' => 'EXHIBITOR'  ,  'TYP' => 's');
													$sqlFetchComplementaryUserCount['PARAM'][]  = array('FILD' => 'isRegistration',  'DATA' => 'Y'  ,  'TYP' => 's');
													$sqlFetchComplementaryUserCount['PARAM'][]  = array('FILD' => 'isConference',  'DATA' => 'Y'  ,  'TYP' => 's');
													$sqlFetchComplementaryUserCount['PARAM'][]  = array('FILD' => 'registration_payment_status',  'DATA' => 'COMPLIMENTARY'  ,  'TYP' => 's');									   
												
													$resultFetchComplementaryUserCount = $mycms->sql_select($sqlFetchComplementaryUserCount);
													echo '<span style="color:#2196F3;">'.$totalRegComplementaryUser	  = $resultFetchComplementaryUserCount[0]['regUser'].'</span>';
													$totalRegComplementaryDelegate     = $totalRegComplementaryDelegate + $totalRegComplementaryUser;
												?>
												</td>
												<td align="center"  class="ttextlarge" width="70" >
												<?php
												    $sqlFetchUnpaidUserCount = array();
													$sqlFetchUnpaidUserCount['QUERY']  	= "	SELECT COUNT(`id`) AS regUser 
																				  FROM  "._DB_USER_REGISTRATION_."  
																				 WHERE user_type = ?
																				   AND status = ? 
																				   AND operational_area != ?
																				   AND isRegistration = ? 
																				   AND isConference = ?
																				   AND registration_payment_status = ?
																				   AND EXTRACT(MONTH FROM created_dateTime) = '".$rowFetchDate['regDate']."'";

													$sqlFetchUnpaidUserCount['PARAM'][]  = array('FILD' => 'user_type',  'DATA' => 'DELEGATE'  ,  'TYP' => 's');
													$sqlFetchUnpaidUserCount['PARAM'][]  = array('FILD' => 'status',  'DATA' => 'A'  ,  'TYP' => 's');
													$sqlFetchUnpaidUserCount['PARAM'][]  = array('FILD' => 'operational_area',  'DATA' => 'EXHIBITOR'  ,  'TYP' => 's');
													$sqlFetchUnpaidUserCount['PARAM'][]  = array('FILD' => 'isRegistration',  'DATA' => 'Y'  ,  'TYP' => 's');
													$sqlFetchUnpaidUserCount['PARAM'][]  = array('FILD' => 'isConference',  'DATA' => 'Y'  ,  'TYP' => 's');
													$sqlFetchUnpaidUserCount['PARAM'][]  = array('FILD' => 'registration_payment_status',  'DATA' => 'UNPAID'  ,  'TYP' => 's');									   
																			   
												
													$resultFetchUnpaidUserCount = $mycms->sql_select($sqlFetchUnpaidUserCount);
													echo '<span style="color:red;">'.$totalUnpaidRegUser	  = $resultFetchUnpaidUserCount[0]['regUser'].'</span>';
													$totalUnpaidRegDelegate     = $totalUnpaidRegDelegate + $totalUnpaidRegUser;
												?>
												</td>
												<td align="center"  class="ttextlarge" width="70" >
												<?php
												    $sqlFetchUserCount = array();
													$sqlFetchUserCount['QUERY']  	= "	SELECT COUNT(`id`) AS regUser 

																				  FROM  "._DB_USER_REGISTRATION_."  
																				 WHERE user_type = ?
																				   AND status = ? 
																				   AND operational_area != ?
																				   AND isRegistration = ?
																				   AND isConference = ?
																				   AND user_email_id NOT LIKE '%@encoder%'
																				   AND EXTRACT(MONTH FROM created_dateTime) = '".$rowFetchDate['regDate']."'";

													$sqlFetchUserCount['PARAM'][]  = array('FILD' => 'user_type',  'DATA' => 'DELEGATE'  ,  'TYP' => 's');
													$sqlFetchUserCount['PARAM'][]  = array('FILD' => 'status',  'DATA' => 'A'  ,  'TYP' => 's');
													$sqlFetchUserCount['PARAM'][]  = array('FILD' => 'operational_area',  'DATA' => 'EXHIBITOR'  ,  'TYP' => 's');
													$sqlFetchUserCount['PARAM'][]  = array('FILD' => 'isRegistration',  'DATA' => 'Y'  ,  'TYP' => 's');
													$sqlFetchUserCount['PARAM'][]  = array('FILD' => 'isConference',  'DATA' => 'Y'  ,  'TYP' => 's');
													
												
													$resultFetchUserCount = $mycms->sql_select($sqlFetchUserCount);
													echo '<span style="color:black;">'.$totalRegUser	  = $resultFetchUserCount[0]['regUser'].'</span>';
													$totalRegDelegate     = $totalRegDelegate + $totalRegUser;
												?>
											</td>
											<td align="center" class="ttextlarge" width="100" >
												<a href="registration_monitoring.php?show=view_registration&src_reg_month=<?=$rowFetchDate['regDate']?>&c=1" target="_blank">
												<span title="View" class="icon-eye" /></a>
											</td>
										</tr>
								<?php
									}
									if($_REQUEST['src_reg_date']=='')
									{	
								?>
										<tr>
											<td align="left" class="ttextlarge" style="border-top:1px solid #000;">Total Delegate</td>
											<td align="center" class="ttextlarge" style="border-top:1px solid #000;">
											<span style="color:green;"><?=$totalPaidDelegate?></span>
											</td>
											<td align="center" class="ttextlarge" style="border-top:1px solid #000;">
											<span style="color:#2196F3;"><?=$totalRegComplementaryDelegate?></span>
											</td>
											<td align="center" class="ttextlarge" style="border-top:1px solid #000;">
											<span style="color:red;"><?=$totalUnpaidRegDelegate?></span>
											</td>
											<td align="center" class="ttextlarge" style="border-top:1px solid #000;">
											<span style="color:black;"><?=$totalRegDelegate?></span>
											</td>
											<td align="left" class="ttextlarge" style="border-top:1px solid #000;"></td>
										</tr>
								<?php
									}	
								} 
								else 
								{
								?>
									<tr>
										<td colspan="6" align="center">
											<span class="mandatory">No Record Present.</span>												
										</td>
									</tr>  
								<?php 
								} 
								?>
							</tbody>
						</table>
						
					</td>
				</tr>		
			</table>
		</form>
	<?php
	}
	 
	/****************************************************************************/
	/*                      SHOW ALL GENERAL REGISTRATION                       */
	/****************************************************************************/
	function monitaringGeneralRegistration($cfg, $mycms)
	{
		global $searchArray, $searchString;
		
		$loggedUserId		= $mycms->getLoggedUserId();
		
		// FETCHING LOGGED USER DETAILS
		$sqlSystemUser = array();
		$sqlSystemUser['QUERY']      = "SELECT * FROM "._DB_CONF_USER_." 
		                               WHERE `a_id` =  ? ";
		$sqlSystemUser['PARAM'][]  = array('FILD' => 'a_id',  'DATA' =>$loggedUserId ,  'TYP' => 's');
									   
		$resultSystemUser   = $mycms->sql_select($sqlSystemUser);
		$rowSystemUser      = $resultSystemUser[0];
		
		$searchApplication  = 0;
		$sqlFetchUserRegDate = array();
		$sqlFetchUserRegDate['QUERY']  = "	SELECT DISTINCT(EXTRACT(MONTH FROM delegate.created_dateTime)) AS regDate,
																DATE(delegate.created_dateTime)
															  FROM "._DB_USER_REGISTRATION_." delegate 
															 WHERE delegate.user_type = 'DELEGATE'
															   AND delegate.status = 'A' 
															   AND delegate.operational_area != 'EXHIBITOR'
															   AND delegate.isRegistration = 'Y' 
															   AND delegate.isConference = 'Y'
															   AND user_email_id NOT LIKE '%@encoder%'
															   AND delegate.status = 'A' ".$searchCondition."
														  ORDER BY DATE(delegate.created_dateTime) ASC";
														  					
		$resultFetchUserRegDate = $mycms->sql_select($sqlFetchUserRegDate);	
		
		
	?>
			<form name="frmSearch" method="post" action="monitoring.process.php" onSubmit="return FormValidator.validate(this);">
			<input type="hidden" name="act" value="search_date" />
			
			<style>
			/* Style the tab */
			div.tab {
				overflow: hidden;
				border: 1px solid #ccc;
				background-color:#333333;  
			}
			
			/* Style the links inside the tab */
			div.tab a {
				float: left;
				display: block;
				color: white;
				text-align: center;
				padding: 14px 16px;
				text-decoration: none;
				transition: 0.3s;
				font-size: 17px;
			}
			
			div.tab a:hover {
			background-color:#999999;       //   #009933; 					//#999999;
			}
			/* Create an active/current tablink class */
			div.tab a.active {
				background-color:#00CC66; 
			}
		</style>
			<table width="100%" class="tborder" align="center" >	
				<tr>
					<td class="tcat" colspan="2" align="left">
						<span style="float:left">Registration Reports</span>
						<span class="tsearchTool" forType="tsearchTool"></span>
					</td>
				</tr>
				<tr>
					<td colspan="2" style="margin:0px; padding:0px;">
						
						<div class="tsearch" style="display:block;">
							
							<table width="70%" style="margin: 15px 0 15px 15px; border: solid 1px #D5D5D5;">
								<tr>
									<td align="left" width="150" class="tdpadding"><strong>Registration Date:</strong></td>
									<td align="left" width="250" class="tdpadding">
										<input type="date" name="src_reg_date" id="src_reg_date" 
										 style="width:90%; text-transform:uppercase;" value="<?=$_REQUEST['src_reg_date']?>" />
									</td>
									<td align="right" width="150" >
										<?php
										// echo  $_REQUEST['src_reg_date'];
										if($_REQUEST['src_reg_date']!='')
										{
										?>
											<br/>
											<input type="button" name="clearBttn" id="clearBttn" class="btn btn-small btn-red" value="Clear" onclick="window.location.href='registration_monitoring.php?show=registration'">
										<?php
										}
										?>
										<input type="submit" name="goSearch" value="Search" 
										 class="btn btn-small btn-blue" style="margin: 0 30px 0 0;"/>
									</td>
								</tr>
							</table>
						</div>			
								
						<table width="70%" shortData="on" style="margin: 15px 0 15px 15px;" class="tborder" >
							<tbody>
								<tr>
									<td align="left" class="" style="border-bottom:1px solid #000;">Date</td>
									<td align="center" class="" style="border-bottom:1px solid #000;">Paid</td>
									<td align="center" class="" style="border-bottom:1px solid #000;">Complemantary</td>
									<td align="center" class="" style="border-bottom:1px solid #000;">Unpaid</td>
									<td align="center" class="" style="border-bottom:1px solid #000;">Total</td>
									<td align="center" class="" style="border-bottom:1px solid #000;">Action</td>
								</tr>
								<?php
								@$searchCondition       = "";
								//$searchCondition   .= " AND DATE(delegate.created_dateTime) = '".$_REQUEST['src_reg_date']."'";
								if($_REQUEST['src_reg_date']!='')
								{
									$searchCondition   .= " AND DATE(delegate.created_dateTime) = '".$_REQUEST['src_reg_date']."'";
								}
								if($_REQUEST['src_reg_month']!='')
								{
									$searchCondition   .= " AND MONTH(delegate.created_dateTime) = '".$_REQUEST['src_reg_month']."'";
								}

								$sqlFetchUserRegDate = array();
								$sqlFetchUserRegDate['QUERY']  = "	SELECT DISTINCT DATE(delegate.created_dateTime) AS regDate
															  FROM "._DB_USER_REGISTRATION_." delegate 
															 WHERE delegate.user_type = ?
															   AND delegate.status = ? AND delegate.operational_area != ?
															   AND delegate.isRegistration = ?
															   AND delegate.isConference = ?
															   
															   AND user_email_id NOT LIKE '%@encoder%'
															    ".$searchCondition."
														  ORDER BY DATE(delegate.created_dateTime) DESC";


							     $sqlFetchUserRegDate['PARAM'][]  = array('FILD' => 'delegate.user_type',  'DATA' => 'DELEGATE',  'TYP' => 's');	
								 $sqlFetchUserRegDate['PARAM'][]  = array('FILD' => 'delegate.status',  'DATA' => 'A' ,  'TYP' => 's');
								 $sqlFetchUserRegDate['PARAM'][]  = array('FILD' => 'delegate.operational_area',  'DATA' => 'EXHIBITOR' ,  'TYP' => 's');
								 $sqlFetchUserRegDate['PARAM'][]  = array('FILD' => 'delegate.isRegistration',  'DATA' => 'Y' ,  'TYP' => 's');
								 $sqlFetchUserRegDate['PARAM'][]  = array('FILD' => 'delegate.isConference',  'DATA' => 'Y' ,  'TYP' => 's');						  
							
								$resultFetchUserRegDate = $mycms->sql_select($sqlFetchUserRegDate);	
								
								if($resultFetchUserRegDate)
								{
									
									foreach($resultFetchUserRegDate as $i=>$rowFetchDate) 
									{
										
										$counter             = $counter + 1;
										
								?>
										<tr class="tlisting">
											<td align="left"  class="ttextlarge tdpadding" >
												<?php
												$date = date_create($rowFetchDate['regDate']);
												echo date_format($date,"d F, Y");
												?> 
											</td>
											<td align="center"  class="ttextlarge" width="70" >
												<?php
	                                                $sqlFetchPaidUserCount = array();											
													$sqlFetchPaidUserCount['QUERY'] = "	SELECT COUNT(`id`) AS regUser 
																				  FROM  "._DB_USER_REGISTRATION_."  
																				 WHERE user_type = ?
																				   AND status = ? 
																				   AND operational_area != ?
																				   AND isRegistration = ? 
																				   AND isConference = ?
																				   AND user_email_id NOT LIKE '%@encoder%'
																				   AND registration_payment_status = ?
																				   AND DATE(created_dateTime) = '".$rowFetchDate['regDate']."'";
												
													$sqlFetchPaidUserCount['PARAM'][]  = array('FILD' => 'user_type',  'DATA' => 'DELEGATE',  'TYP' => 's');	
													$sqlFetchPaidUserCount['PARAM'][]  = array('FILD' => 'status',  'DATA' => 'A' ,  'TYP' => 's');
													$sqlFetchPaidUserCount['PARAM'][]  = array('FILD' => 'operational_area',  'DATA' => 'EXHIBITOR' ,  'TYP' => 's');
													$sqlFetchPaidUserCount['PARAM'][]  = array('FILD' => 'isRegistration',  'DATA' => 'Y' ,  'TYP' => 's');
													$sqlFetchPaidUserCount['PARAM'][]  = array('FILD' => 'isConference',  'DATA' => 'Y' ,  'TYP' => 's');
													$sqlFetchPaidUserCount['PARAM'][]  = array('FILD' => 'registration_payment_status',  'DATA' => 'PAID' ,  'TYP' => 's');
													
													$resultFetchPaidUserCount = $mycms->sql_select($sqlFetchPaidUserCount);
													echo '<span style="color:green;">'.$totalRegPaidUser	  = $resultFetchPaidUserCount[0]['regUser'].'</span>';
													$totalPaidDelegate     = $totalPaidDelegate + $totalRegPaidUser;
												?>
												</td>
												<td align="center"  class="ttextlarge" width="70" >
												<?php

													$sqlFetchComplementaryUserCount = array();
													$sqlFetchComplementaryUserCount['QUERY'] = "	SELECT COUNT(`id`) AS regUser 
																						  FROM  "._DB_USER_REGISTRATION_."  
																						 WHERE user_type = ?
																						   AND status = ? 
																						   AND operational_area != ?
																						   AND isRegistration = ? 
																				 		   AND isConference = ?
																				  		   AND user_email_id NOT LIKE '%@encoder%'
																						   AND registration_payment_status = ?
																						   AND DATE(created_dateTime) = '".$rowFetchDate['regDate']."'";
												


													$sqlFetchComplementaryUserCount['PARAM'][]  = array('FILD' => 'user_type',  'DATA' => 'DELEGATE',  'TYP' => 's');	
													$sqlFetchComplementaryUserCount['PARAM'][]  = array('FILD' => 'status',  'DATA' => 'A' ,  'TYP' => 's');
													$sqlFetchComplementaryUserCount['PARAM'][]  = array('FILD' => 'operational_area',  'DATA' => 'EXHIBITOR' ,  'TYP' => 's');
													$sqlFetchComplementaryUserCount['PARAM'][]  = array('FILD' => 'isRegistration',  'DATA' => 'Y' ,  'TYP' => 's');
												    $sqlFetchComplementaryUserCount['PARAM'][]  = array('FILD' => 'isConference',  'DATA' => 'Y' ,  'TYP' => 's');
													$sqlFetchComplementaryUserCount['PARAM'][]  = array('FILD' => 'registration_payment_status',  'DATA' => 'COMPLIMENTARY' ,  'TYP' => 's');


													$resultFetchComplementaryUserCount = $mycms->sql_select($sqlFetchComplementaryUserCount);
													//echo $totalRegUser	  = $resultFetchUserCount[0]['regUser'];
													echo '<span style="color:#2196F3;">'.$totalRegComplementaryUser	  = $resultFetchComplementaryUserCount[0]['regUser'].'</span>';
													$totalRegComplementaryDelegate     = $totalRegComplementaryDelegate + $totalRegComplementaryUser;
												?>
												</td>
												<td align="center"  class="ttextlarge" width="70" >
												<?php
												    $sqlFetchUnpaidUserCount = array();
													$sqlFetchUnpaidUserCount['QUERY']  	= "	SELECT COUNT(`id`) AS regUser 
																				  FROM  "._DB_USER_REGISTRATION_."  
																				 WHERE user_type = ?
																				   AND status = ? 
																				   AND operational_area != ?
																				   AND isRegistration = ? 
																				   AND isConference = ?
																				   AND user_email_id NOT LIKE '%@encoder%'
																				   AND registration_payment_status = ?
																				   AND DATE(created_dateTime) = '".$rowFetchDate['regDate']."'";
												
													$sqlFetchUnpaidUserCount['PARAM'][]  = array('FILD' => 'user_type',  'DATA' => 'DELEGATE',  'TYP' => 's');	
													$sqlFetchUnpaidUserCount['PARAM'][]  = array('FILD' => 'status',  'DATA' => 'A' ,  'TYP' => 's');
													$sqlFetchUnpaidUserCount['PARAM'][]  = array('FILD' => 'operational_area',  'DATA' => 'EXHIBITOR' ,  'TYP' => 's');
													$sqlFetchUnpaidUserCount['PARAM'][]  = array('FILD' => 'isRegistration',  'DATA' => 'Y' ,  'TYP' => 's');
													$sqlFetchUnpaidUserCount['PARAM'][]  = array('FILD' => 'isConference',  'DATA' => 'Y' ,  'TYP' => 's');
													$sqlFetchUnpaidUserCount['PARAM'][]  = array('FILD' => 'registration_payment_status',  'DATA' => 'UNPAID' ,  'TYP' => 's');
							   
							   

													$resultFetchUnpaidUserCount = $mycms->sql_select($sqlFetchUnpaidUserCount);
												//echo $totalUnpaidRegUser	  = $resultFetchUnpaidUserCount[0]['regUser'];
													echo '<span style="color:red;">'.$totalUnpaidRegUser	  = $resultFetchUnpaidUserCount[0]['regUser'].'</span>';
													$totalUnpaidRegDelegate     = $totalUnpaidRegDelegate + $totalUnpaidRegUser;
												?>
												</td>
												<td align="center"  class="ttextlarge" width="70" >
												<?php
													$sqlFetchUserCount = array();												
													$sqlFetchUserCount['QUERY']  	= "	SELECT COUNT(`id`) AS regUser 
																				  FROM  "._DB_USER_REGISTRATION_."  
																				 WHERE user_type = ?
																				   AND status = ? 
																				   AND operational_area != ?
																				   AND isRegistration = ?
																				   AND isConference = ?
																				   AND user_email_id NOT LIKE '%@encoder%'
																				   AND DATE(created_dateTime) = '".$rowFetchDate['regDate']."'";


													$sqlFetchUserCount['PARAM'][]  = array('FILD' => 'user_type',  'DATA' => 'DELEGATE',  'TYP' => 's');	
													$sqlFetchUserCount['PARAM'][]  = array('FILD' => 'status',  'DATA' => 'A' ,  'TYP' => 's');
													$sqlFetchUserCount['PARAM'][]  = array('FILD' => 'operational_area',  'DATA' => 'EXHIBITOR' ,  'TYP' => 's');
													$sqlFetchUserCount['PARAM'][]  = array('FILD' => 'isRegistration',  'DATA' => 'Y' ,  'TYP' => 's');
													$sqlFetchUserCount['PARAM'][]  = array('FILD' => 'isConference',  'DATA' => 'Y' ,  'TYP' => 's');
													
															  
															  

												
													$resultFetchUserCount = $mycms->sql_select($sqlFetchUserCount);
													//$totalRegUser	  = $resultFetchUserCount[0]['regUser'];
													echo '<span style="color:black;">'.$totalRegUser	  = $resultFetchUserCount[0]['regUser'].'</span>';
													//echo '<span>Total : '.$totalRegUser	  = $resultFetchUserCount[0]['regUser'].'</span>';
													$totalRegDelegate     = $totalRegDelegate + $totalRegUser;
												?>
											</td>
											<td align="center" class="ttextlarge" width="100" >
												<a href="registration_monitoring.php?show=view_registration&src_reg_date=<?=$rowFetchDate['regDate']?>&c=1" target="_blank">
												<span title="View" class="icon-eye" /></a>
											</td>
										</tr>
								<?php
									}
									if($_REQUEST['src_reg_date']=='')
									{	
								?>
										<tr>
											<td align="left" class="ttextlarge" style="border-top:1px solid #000;">Total Delegate</td>
											<td align="center" class="ttextlarge" style="border-top:1px solid #000;">
											<span style="color:green;"><?=$totalPaidDelegate?></span>
											</td>
											<td align="center" class="ttextlarge" style="border-top:1px solid #000;">
											<span style="color:#2196F3;"><?=$totalRegComplementaryDelegate?></span>
											</td>
											<td align="center" class="ttextlarge" style="border-top:1px solid #000;">
											<span style="color:red;"><?=$totalUnpaidRegDelegate?></span>
											</td>
											<td align="center" class="ttextlarge" style="border-top:1px solid #000;">
											<span style="color:black;"><?=$totalRegDelegate?></span>
											</td>
											<td align="left" class="ttextlarge" style="border-top:1px solid #000;"></td>
										</tr>
								<?php
									}	
								} 
								else 
								{
								?>
									<tr>
										<td colspan="6" align="center">
											<span class="mandatory">No Record Present.</span>												
										</td>
									</tr>  
								<?php 
								} 
								?>
							</tbody>
						</table>
						
					</td>
				</tr>		
			</table>
		</form>
	<?php
	}
	
	function monitaringGeneralRegistrationByLocation($cfg, $mycms)
	{
		global $searchArray, $searchString;
		
		$loggedUserId		= $mycms->getLoggedUserId();
		
	?>
			<form name="frmSearch" method="post" action="monitoring.process.php" onSubmit="return FormValidator.validate(this);">
			<input type="hidden" name="act" value="search_location" />
			<table width="100%" class="tborder" align="center">	
				<tr>
					<td class="tcat" colspan="2" align="left">
						<span style="float:left">Registration Reports</span>
						<span class="tsearchTool" forType="tsearchTool"></span>
					</td>
				</tr>
				<tr>
					<td colspan="2" style="margin:0px; padding:0px;">
						
						<div class="tsearch" style="display:block;">
							<table width="70%" style="margin: 15px 0 15px 15px; border: solid 1px #D5D5D5;">
								<tr>
									<td align="left" width="150" class="tdpadding"><strong>Country:</strong></td>
									<td align="left" width="250" class="tdpadding">
									
										<select name="src_country_id" id="src_country_id"
												style="width:90%;" value="<?=$_REQUEST['src_country_id']?>" />
										 	<option value="">-- Country Name --</option>
										 <?php
											$sql = array();
											$sql['QUERY'] = "SELECT * FROM "._DB_COMN_COUNTRY_." WHERE `status` = 'A'  ORDER BY country_name ASC";
												$result	 = $mycms->sql_select($sql);	
												foreach($result as $i=>$row) 
												{
												?>
										 		<option value="<?=$row['country_id']?>" <?=($row['country_id']==$_REQUEST['src_country_id']?'selected="selected"':'')?> ><?=$row['country_name']?></option>
												<?php
												}
												?>
											<option value="0">Not Recorded</option>
										 </select>
									</td>
									<td align="right" width="150">
										<?php 
										if($_REQUEST['src_country_id']!='')
										{
										?>
											<input type="button" name="clearBttn" id="clearBttn" class="btn btn-small btn-red" value="Clear" onclick="window.location.href='registration_monitoring.php?show=registration_location'">
										<?php
										}
										?>
										<input type="submit" name="goSearch" value="Search" 
										 class="btn btn-small btn-blue" style="margin: 0 30px 0 0;"/>
									</td>
								</tr>
							</table>
						</div>			
								
						<table width="70%" shortData="on" style="margin: 15px 0 15px 15px;" class="tborder" >
							<tbody>
								<tr>
									<td align="left" class="" style="border-bottom:1px solid #000;">Country Name</td>
									<td align="center" class="" style="border-bottom:1px solid #000;">Paid</td>
									<td align="center" class="" style="border-bottom:1px solid #000;">Complemantary</td>
									<td align="center" class="" style="border-bottom:1px solid #000;">Unpaid</td>
									<td align="center" class="" style="border-bottom:1px solid #000;">Total</td>
									<td align="center" class="" style="border-bottom:1px solid #000;">Action</td>
								</tr>
								<?php
								@$searchCondition       = "";
								
								if($_REQUEST['src_country_id']!='')
								{
									$searchCondition   .= " AND delegate.user_country_id = '".$_REQUEST['src_country_id']."'";
								}
								
								$sqlFetchUser = array();
								$sqlFetchUser['QUERY']		  = "  SELECT DISTINCT delegate.user_country_id AS countryId,
																				   IFNULL(country.country_name,'NOT RECORDED') AS countryName
																			  FROM "._DB_USER_REGISTRATION_." delegate 
																   LEFT OUTER JOIN "._DB_COMN_COUNTRY_." country
																				ON delegate.user_country_id = country.country_id
																			 WHERE delegate.status = ?
																			   AND operational_area IS NOT NULL ".$searchCondition."
																		  ORDER BY ( CASE WHEN country.country_name IS NULL THEN 'ZZZZZZZZ' ELSE country.country_name END) ASC";

								$sqlFetchUser['PARAM'][]  = array('FILD' => 'delegate.status',  			'DATA' => 'A',  		'TYP' => 's');
								
								$resultFetchUser	 = $mycms->sql_select($sqlFetchUser);	
								
								if($resultFetchUser)
								{ 
									foreach($resultFetchUser as $i=>$rowFetch) 
									{
										
										$counter             = $counter + 1;
										
										$sqlFetchPaidUserCount = array();
										$sqlFetchPaidUserCount['QUERY'] = "	SELECT COUNT(*) AS regUser 
																			  FROM  "._DB_USER_REGISTRATION_."  
																			 WHERE user_type = ?
																			   AND status = ?
																			   AND operational_area NOT IN ('EXHIBITOR')
																			   AND isRegistration = ?
																			   AND isConference = ?
																			   AND registration_payment_status = ?
																			   AND user_country_id = '".$rowFetch['countryId']."'";
																	   
										$sqlFetchPaidUserCount['PARAM'][]  = array('FILD' => 'user_type',  					'DATA' =>'DELEGATE',    'TYP' => 's');
										$sqlFetchPaidUserCount['PARAM'][]  = array('FILD' => 'status',  					'DATA' =>'A',  			'TYP' => 's');
										$sqlFetchPaidUserCount['PARAM'][]  = array('FILD' => 'isRegistration',  			'DATA' =>'Y',  'TYP' => 's');
										$sqlFetchPaidUserCount['PARAM'][]  = array('FILD' => 'isConference',  				'DATA' =>'Y',  'TYP' => 's');
										$sqlFetchPaidUserCount['PARAM'][]  = array('FILD' => 'registration_payment_status', 'DATA' => 'PAID',  'TYP' => 's');
										
										$resultFetchPaidUserCount 			= $mycms->sql_select($sqlFetchPaidUserCount);										
										$totalRegPaidUser	  				= $resultFetchPaidUserCount[0]['regUser'];
										$totalPaidDelegate     				= $totalPaidDelegate + $totalRegPaidUser;
										
										
										$sqlFetchComplementaryUserCount = array();
										$sqlFetchComplementaryUserCount['QUERY'] 	= "	SELECT COUNT(*) AS regUser 
																						  FROM "._DB_USER_REGISTRATION_."  
																						 WHERE user_type = ?
																						   AND status = ?
																						   AND operational_area NOT IN ('EXHIBITOR')
																						   AND isRegistration = ?
																						   AND isConference = ?
																						   AND registration_payment_status = ?
																						   AND user_country_id = '".$rowFetch['countryId']."'";
																	   
										$sqlFetchComplementaryUserCount['PARAM'][]  = array('FILD' => 'user_type',  					'DATA' =>'DELEGATE',    		'TYP' => 's');
										$sqlFetchComplementaryUserCount['PARAM'][]  = array('FILD' => 'status',  						'DATA' =>'A',  					'TYP' => 's');
										$sqlFetchComplementaryUserCount['PARAM'][]  = array('FILD' => 'isRegistration',  				'DATA' =>'Y',  					'TYP' => 's');
										$sqlFetchComplementaryUserCount['PARAM'][]  = array('FILD' => 'isConference',  					'DATA' =>'Y',  					'TYP' => 's');
										$sqlFetchComplementaryUserCount['PARAM'][]  = array('FILD' => 'registration_payment_status', 	'DATA' => 'COMPLIMENTARY',  	'TYP' => 's');
										
										$resultFetchComplementaryUserCount 			= $mycms->sql_select($sqlFetchComplementaryUserCount);										
										$totalRegComplementaryUser	  				= $resultFetchComplementaryUserCount[0]['regUser'];
										$totalRegComplementaryDelegate     			= $totalRegComplementaryDelegate + $totalRegComplementaryUser;
										
										
										$sqlFetchUnpaidUserCount = array();
										$sqlFetchUnpaidUserCount['QUERY'] 	= "	SELECT COUNT(*) AS regUser 
																				  FROM "._DB_USER_REGISTRATION_."  
																				 WHERE user_type = ?
																				   AND status = ?
																				   AND operational_area NOT IN ('EXHIBITOR')
																				   AND isRegistration = ?
																				   AND isConference = ?
																				   AND registration_payment_status = ?
																				   AND user_country_id = '".$rowFetch['countryId']."'";
																	   
										$sqlFetchUnpaidUserCount['PARAM'][]  = array('FILD' => 'user_type',  					'DATA' =>'DELEGATE',    		'TYP' => 's');
										$sqlFetchUnpaidUserCount['PARAM'][]  = array('FILD' => 'status',  						'DATA' =>'A',  					'TYP' => 's');
										$sqlFetchUnpaidUserCount['PARAM'][]  = array('FILD' => 'isRegistration',  				'DATA' =>'Y',  					'TYP' => 's');
										$sqlFetchUnpaidUserCount['PARAM'][]  = array('FILD' => 'isConference',  					'DATA' =>'Y',  					'TYP' => 's');
										$sqlFetchUnpaidUserCount['PARAM'][]  = array('FILD' => 'registration_payment_status', 	'DATA' => 'UNPAID',  			'TYP' => 's');
										
										$resultFetchUnpaidUserCount 		 = $mycms->sql_select($sqlFetchUnpaidUserCount);
										$totalUnpaidRegUser	  				 = $resultFetchUnpaidUserCount[0]['regUser'];
										$totalUnpaidRegDelegate     		 = $totalUnpaidRegDelegate + $totalUnpaidRegUser;
										
										$sqlFetchUserCount = array();
										$sqlFetchUserCount['QUERY'] 	= "	SELECT COUNT(*) AS regUser 
																				  FROM "._DB_USER_REGISTRATION_."  
																				 WHERE user_type = ?
																				   AND status = ?
																				   AND operational_area NOT IN ('EXHIBITOR')
																				   AND isRegistration = ?
																				   AND isConference = ?
																				   AND user_country_id = '".$rowFetch['countryId']."'";
																	   
										$sqlFetchUserCount['PARAM'][]  = array('FILD' => 'user_type',  						'DATA' =>'DELEGATE',    		'TYP' => 's');
										$sqlFetchUserCount['PARAM'][]  = array('FILD' => 'status',  						'DATA' =>'A',  					'TYP' => 's');
										$sqlFetchUserCount['PARAM'][]  = array('FILD' => 'isRegistration',  				'DATA' =>'Y',  					'TYP' => 's');
										$sqlFetchUserCount['PARAM'][]  = array('FILD' => 'isConference',  					'DATA' =>'Y',  					'TYP' => 's');
										
										$resultFetchUserCount 		   = $mycms->sql_select($sqlFetchUserCount);
										$totalRegUser	  			   = $resultFetchUserCount[0]['regUser'];
										$totalRegDelegate     		   = $totalRegDelegate + $totalRegUser;
								
								?>
										<tr class="tlisting">
											<td align="left"  class="ttextlarge tdpadding" >
												<?php
												echo $rowFetch['countryName'].'['.$rowFetch['countryId'].']';
												?> 
											</td>
											<td align="center"  class="ttextlarge" width="70" >
												<?php
													echo '<span style="color:green;">'.$totalRegPaidUser.'</span>';
												?>
												</td>
												<td align="center"  class="ttextlarge" width="70" >
												<?php
													echo '<span style="color:#2196F3;">'.$totalRegComplementaryUser.'</span>';
												?>
												</td>
												<td align="center"  class="ttextlarge" width="70" >
												<?php
													echo '<span style="color:red;">'.$totalUnpaidRegDelegate.'</span>';
												?>
												</td>
												
												
												<td align="center"  class="ttextlarge" width="70" >
												<?php
													echo '<span style="color:black;">'.$totalRegUser.'</span>';
												?>
											</td>
											<td align="center" class="ttextlarge" width="100" >
												<a href="registration_monitoring.php?show=registration_state&src_country_id=<?=$rowFetch['countryId']?>" target="_blank">
												<span title="View" class="icon-eye" /></a>
											</td>
										</tr>
								<?php
									}
									
									if($_REQUEST['src_country_id']=='')
									{	
								?>
										<tr>
											<td align="left" class="ttextlarge" style="border-top:1px solid #000;">Total Registered Delegate</td>
											<td align="center" class="ttextlarge" style="border-top:1px solid #000;">
												<span style="color:green;"><?=$totalPaidDelegate?></span>
											</td>
											<td align="center" class="ttextlarge" style="border-top:1px solid #000;">
												<span style="color:#2196F3;"><?=$totalRegComplementaryDelegate?></span>
											</td>
											<td align="center" class="ttextlarge" style="border-top:1px solid #000;">
												<span style="color:red;"><?=$totalUnpaidRegDelegate?></span>
											</td>
											<td align="center" class="ttextlarge" style="border-top:1px solid #000;">
												<span style="color:black;"><?=$totalRegDelegate?></span>
											</td>
											<td align="left" class="ttextlarge" style="border-top:1px solid #000;"></td>
										</tr>
								<?php
									}	
								} 
								else 
								{
								?>
									<tr>
										<td colspan="6" align="center">
											<span class="mandatory">No Record Present.</span>												
										</td>
									</tr>  
								<?php 
								} 
								?>
							</tbody>
						</table>
						
					</td>
				</tr>		
			</table>
		</form>
	<?php
	}
	
	function monitaringGeneralRegistrationByState($cfg, $mycms)
	{
		global $searchArray, $searchString;
		
		$loggedUserId		= $mycms->getLoggedUserId();
		
		// FETCHING LOGGED USER DETAILS
		$sqlSystemUser = array();
		$sqlSystemUser['QUERY']    = "SELECT * FROM "._DB_CONF_USER_." 
		                               WHERE `a_id` = ? ";
			
		$sqlSystemUser['PARAM'][]  = array('FILD' => 'a_id',  'DATA' => $loggedUserId,  'TYP' => 's');							   

		$resultSystemUser   = $mycms->sql_select($sqlSystemUser);
		$rowSystemUser      = $resultSystemUser[0];
		
		$searchApplication  = 0;
		
		
	?>
			<form name="frmSearch" method="post" action="monitoring.process.php" onSubmit="return FormValidator.validate(this);">
			<input type="hidden" name="act" value="search_state" />
			<input type="hidden" name="c" value="<?=$_REQUEST['c']?>" />
			<input type="hidden" name="src_country_id" value="<?=$_REQUEST['src_country_id']?>" />
			<table width="100%" class="tborder" align="center">	
				<tr>
					<td class="tcat" colspan="2" align="left">
						<span style="float:left">Registration Reports</span>
						<span class="tsearchTool" forType="tsearchTool"></span>
					</td>
				</tr>
				<tr>
					<td colspan="2" style="margin:0px; padding:0px;">
						
						<div class="tsearch" style="display:block;">
							<table width="70%" style="margin: 15px 0 15px 15px; border: solid 1px #D5D5D5;">
								<tr>
									<td align="left" width="150" class="tdpadding"><strong>State Name:</strong></td>
									<td align="left" width="250" class="tdpadding">
									
										<select name="src_state_id" id="src_state_id"
										 style="width:90%;" value="<?=$_REQUEST['src_state_id']?>" />
										 <option value="">-- State Name --</option>
										 <?php
												$sql['QUERY'] = "SELECT * FROM "._DB_COMN_STATE_." WHERE `status` = 'A' AND `country_id` = '".
												$_REQUEST['src_country_id']."' ORDER BY state_name ASC";
												$result	 = $mycms->sql_select($sql);	
												foreach($result as $i=>$row) 
												{
												?>
										 		<option value="<?=$row['st_id']?>" <?=($row['st_id']==$_REQUEST['src_state_id']?'selected="selected"':'')?> ><?=$row['state_name']?></option>
												<?php
												}
												?>
										 </select>
									</td>
									<td align="right" width="150">
										<?php 
										if($_REQUEST['src_state_id']!='')
										{
										?>
											<input type="button" name="clearBttn" id="clearBttn" class="btn btn-small btn-red" value="Clear" onclick="window.location.href='registration_monitoring.php?show=registration_state&src_country_id=<?=$_REQUEST['src_country_id']?>'">
										<?php
										}
										?>
										<input type="submit" name="goSearch" value="Search" 
										 class="btn btn-small btn-blue" style="margin: 0 30px 0 0;"/>
									</td>
								</tr>
							</table>
						</div>			
								
						<table width="70%" shortData="on" style="margin: 15px 0 15px 15px;" class="tborder" >
							<tbody>
								<tr>
									<td align="left" class="" style="border-bottom:1px solid #000;">State Name</td>
									<td align="center" class="" style="border-bottom:1px solid #000;">Paid</td>
									<td align="center" class="" style="border-bottom:1px solid #000;">Complemantary</td>
									<td align="center" class="" style="border-bottom:1px solid #000;">Unpaid</td>
									<td align="center" class="" style="border-bottom:1px solid #000;">Total</td>
									<td align="center" class="" style="border-bottom:1px solid #000;">Action</td>
								</tr>
								<?php
								@$searchCondition       = "";
								
								if($_REQUEST['src_state_id']!='')
								{
									$searchCondition   .= " AND delegate.user_state_id = '".$_REQUEST['src_state_id']."'";
								}
								
								$sqlFetchUser = array();	
								$sqlFetchUser['QUERY']		  = "	SELECT DISTINCT delegate.user_state_id AS stateId,
								 								   delegate.user_country_id AS countryId,
																   state.state_name AS stateName
															  FROM "._DB_USER_REGISTRATION_." delegate 
												   LEFT OUTER JOIN "._DB_COMN_STATE_." state
										  						ON delegate.user_state_id = state.st_id
															 WHERE delegate.user_type = ?
															   AND delegate.status = ? 
															   AND delegate.user_email_id NOT LIKE '%@encoder%'
															   AND delegate.operational_area != ?
															   AND delegate.isRegistration = ?
															   AND delegate.isConference = ?
															   AND delegate.user_country_id = '".$_REQUEST['src_country_id']."'
															   		".$searchCondition."
														  ORDER BY state.state_name ASC";
							
								$sqlFetchUser['PARAM'][]  = array('FILD' => 'delegate.user_type',  'DATA' => 'DELEGATE',  'TYP' => 's');	
								$sqlFetchUser['PARAM'][]  = array('FILD' => 'delegate.status',  'DATA' => 'A' ,  'TYP' => 's');
								$sqlFetchUser['PARAM'][]  = array('FILD' => 'delegate.operational_area',  'DATA' => 'EXHIBITOR' ,  'TYP' => 's');
								$sqlFetchUser['PARAM'][]  = array('FILD' => 'delegate.isRegistration',  'DATA' => 'Y' ,  'TYP' => 's');
								$sqlFetchUser['PARAM'][]  = array('FILD' => 'delegate.isConference',  'DATA' => 'Y' ,  'TYP' => 's');

								$resultFetchUser	 = $mycms->sql_select($sqlFetchUser);	
								
								if($resultFetchUser)
								{
									
									foreach($resultFetchUser as $i=>$rowFetch) 
									{
										
										$counter             = $counter + 1;
										
								?>
										<tr class="tlisting">
											<td align="left"  class="ttextlarge tdpadding" >
												<?php
												echo $rowFetch['stateName'] ;
												?> 
											</td>
											<td align="center"  class="ttextlarge" width="70" >
												<?php
													$sqlFetchPaidUserCount = array();												
													$sqlFetchPaidUserCount['QUERY'] = "	SELECT COUNT(`id`) AS regUser 
																				  FROM  "._DB_USER_REGISTRATION_."  
																				 WHERE user_type = ?
																				   AND status = ? 
															  					   AND user_email_id NOT LIKE '%@encoder%'
																				   AND operational_area != ?
																				   AND isRegistration = ?
																				   AND isConference = ?
																				   AND registration_payment_status = ?
																				   AND user_state_id = '".$rowFetch['stateId']."'";

													$sqlFetchPaidUserCount['PARAM'][]  = array('FILD' => 'user_type',  'DATA' => 'DELEGATE',  'TYP' => 's');	
												    $sqlFetchPaidUserCount['PARAM'][]  = array('FILD' => 'status',  'DATA' => 'A' ,  'TYP' => 's');
													$sqlFetchPaidUserCount['PARAM'][]  = array('FILD' => 'operational_area',  'DATA' => 'EXHIBITOR' ,  'TYP' => 's');
													$sqlFetchPaidUserCount['PARAM'][]  = array('FILD' => 'isRegistration',  'DATA' => 'Y' ,  'TYP' => 's');
													$sqlFetchPaidUserCount['PARAM'][]  = array('FILD' => 'isConference',  'DATA' => 'Y' ,  'TYP' => 's');							   
													$sqlFetchPaidUserCount['PARAM'][]  = array('FILD' => 'registration_payment_status',  'DATA' => 'PAID' ,  'TYP' => 's');	

													$resultFetchPaidUserCount = $mycms->sql_select($sqlFetchPaidUserCount);
													echo '<span style="color:green;">'.$totalRegPaidUser	  = $resultFetchPaidUserCount[0]['regUser'].'</span>';
													$totalPaidDelegate     = $totalPaidDelegate + $totalRegPaidUser;
												?>
												</td>
												<td align="center"  class="ttextlarge" width="70" >
												<?php
											        $sqlFetchComplementaryUserCount = array();	
													$sqlFetchComplementaryUserCount['QUERY'] = "	SELECT COUNT(`id`) AS regUser 
																						  FROM  "._DB_USER_REGISTRATION_."  
																						 WHERE user_type = ?
																						   AND status = ? 
															  					 		   AND user_email_id NOT LIKE '%@encoder%' 
																						   AND operational_area != ?
																						   AND isRegistration = ?
																				  		   AND isConference = ?
																						   AND registration_payment_status = ?
																						   AND user_state_id = '".$rowFetch['stateId']."'";

													$sqlFetchComplementaryUserCount['PARAM'][]  = array('FILD' => 'user_type',  'DATA' => 'DELEGATE',  'TYP' => 's');	
													$sqlFetchComplementaryUserCount['PARAM'][]  = array('FILD' => 'status',  'DATA' => 'A' ,  'TYP' => 's');
												    $sqlFetchComplementaryUserCount['PARAM'][]  = array('FILD' => 'operational_area',  'DATA' => 'EXHIBITOR' ,  'TYP' => 's');
													$sqlFetchComplementaryUserCount['PARAM'][]  = array('FILD' => 'isRegistration',  'DATA' => 'Y' ,  'TYP' => 's');
													$sqlFetchComplementaryUserCount['PARAM'][]  = array('FILD' => 'isConference',  'DATA' => 'Y' ,  'TYP' => 's');							   
												    $sqlFetchComplementaryUserCount['PARAM'][]  = array('FILD' => 'registration_payment_status',  'DATA' => 'COMPLIMENTARY' ,  'TYP' => 's');									   
												
													$resultFetchComplementaryUserCount = $mycms->sql_select($sqlFetchComplementaryUserCount);
													//echo $totalRegUser	  = $resultFetchUserCount[0]['regUser'];
													echo '<span style="color:#2196F3;">'.$totalRegComplementaryUser	  = $resultFetchComplementaryUserCount[0]['regUser'].'</span>';
													$totalRegComplementaryDelegate     = $totalRegComplementaryDelegate + $totalRegComplementaryUser;
												?>
												</td>
												<td align="center"  class="ttextlarge" width="70" >
												<?php
													$sqlFetchUnpaidUserCount = array();												
													$sqlFetchUnpaidUserCount['QUERY']  	= "	SELECT COUNT(`id`) AS regUser 
																				  FROM  "._DB_USER_REGISTRATION_."  
																				 WHERE user_type = ?
																				   AND status = ? 
															  					   AND user_email_id NOT LIKE '%@encoder%'
																				   AND operational_area != ?
																				   AND isRegistration = ?
																				   AND isConference = ?
																				   AND registration_payment_status = ?
																				   AND user_state_id = '".$rowFetch['stateId']."'";
												

													$sqlFetchUnpaidUserCount['PARAM'][]  = array('FILD' => 'user_type',  'DATA' => 'DELEGATE',  'TYP' => 's');	
													$sqlFetchUnpaidUserCount['PARAM'][]  = array('FILD' => 'status',  'DATA' => 'A' ,  'TYP' => 's');
													$sqlFetchUnpaidUserCount['PARAM'][]  = array('FILD' => 'operational_area',  'DATA' => 'EXHIBITOR' ,  'TYP' => 's');
												    $sqlFetchUnpaidUserCount['PARAM'][]  = array('FILD' => 'isRegistration',  'DATA' => 'Y' ,  'TYP' => 's');
													$sqlFetchUnpaidUserCount['PARAM'][]  = array('FILD' => 'isConference',  'DATA' => 'Y' ,  'TYP' => 's');							   
													$sqlFetchUnpaidUserCount['PARAM'][]  = array('FILD' => 'registration_payment_status',  'DATA' => 'UNPAID' ,  'TYP' => 's');									   
																			   



													$resultFetchUnpaidUserCount = $mycms->sql_select($sqlFetchUnpaidUserCount);
												//echo $totalUnpaidRegUser	  = $resultFetchUnpaidUserCount[0]['regUser'];
													echo '<span style="color:red;">'.$totalUnpaidRegUser	  = $resultFetchUnpaidUserCount[0]['regUser'].'</span>';
													$totalUnpaidRegDelegate     = $totalUnpaidRegDelegate + $totalUnpaidRegUser;
												?>
												</td>
												<td align="center"  class="ttextlarge" width="70" >
												<?php
													$sqlFetchUserCount = array();						
													$sqlFetchUserCount['QUERY']  	= "	SELECT COUNT(`id`) AS regUser 
																				  FROM  "._DB_USER_REGISTRATION_."  
																				 WHERE user_type = ?
																				   AND status = ? 
															  					   AND user_email_id NOT LIKE '%@encoder%'
																				   AND operational_area != ?
																				   AND isRegistration = ?
																				   AND isConference = ?
																				   AND user_state_id = '".$rowFetch['stateId']."'";


																				   
												    $sqlFetchUserCount['PARAM'][]  = array('FILD' => 'user_type',  'DATA' => 'DELEGATE',  'TYP' => 's');	
													$sqlFetchUserCount['PARAM'][]  = array('FILD' => 'status',  'DATA' => 'A' ,  'TYP' => 's');
													$sqlFetchUserCount['PARAM'][]  = array('FILD' => 'operational_area',  'DATA' => 'EXHIBITOR' ,  'TYP' => 's');
													$sqlFetchUserCount['PARAM'][]  = array('FILD' => 'isRegistration',  'DATA' => 'Y' ,  'TYP' => 's');
													$sqlFetchUserCount['PARAM'][]  = array('FILD' => 'isConference',  'DATA' => 'Y' ,  'TYP' => 's');							   
																						   
																																																 
												
													$resultFetchUserCount = $mycms->sql_select($sqlFetchUserCount);
													//$totalRegUser	  = $resultFetchUserCount[0]['regUser'];
													echo '<span style="color:black;">'.$totalRegUser	  = $resultFetchUserCount[0]['regUser'].'</span>';
													//echo '<span>Total : '.$totalRegUser	  = $resultFetchUserCount[0]['regUser'].'</span>';
													$totalRegDelegate     = $totalRegDelegate + $totalRegUser;
												?>
											</td>
											<td align="left" class="ttextlarge" width="70" >
												<a href="registration_monitoring.php?show=view_registration&src_country_id=<?=$rowFetch['countryId']?>&src_state_id=<?=$rowFetch['stateId']?>&c=2" target="_blank">
												<span title="View" class="icon-eye" /></a>
											</td>
										</tr>
								<?php
									}
									if($_REQUEST['src_state_id']=='')
									{	
								?>
										<tr>
											<td align="left" class="ttextlarge" style="border-top:1px solid #000;">Total Registered Delegate</td>
											<td align="center" class="ttextlarge" style="border-top:1px solid #000;">
											
												<span style="color:green;"><?=$totalPaidDelegate?></span>
												
											</td>
											<td align="center" class="ttextlarge" style="border-top:1px solid #000;">
												<span style="color:#2196F3;"><?=$totalRegComplementaryDelegate?></span>
											</td>
											<td align="center" class="ttextlarge" style="border-top:1px solid #000;">
												<span style="color:red;"><?=$totalUnpaidRegDelegate?></span>
											
											</td>
											<td align="center" class="ttextlarge" style="border-top:1px solid #000;">
												<span style="color:black;"><?=$totalRegDelegate?></span>
											
											</td>
											
											<td align="left" class="ttextlarge" style="border-top:1px solid #000;"></td>
										</tr>
								<?php
									}	
								} 
								else 
								{
								?>
									<tr>
										<td colspan="6" align="center">
											<span class="mandatory">No Record Present.</span>												
										</td>
									</tr>  
								<?php 
								} 
								?>
							</tbody>
						</table>
						
					</td>
				</tr>		
			</table>
		</form>
	<?php
	}
	
	function viewGeneralRegistration_Old_One($cfg, $mycms)
	{
		global $searchArray, $searchString;
		
		$loggedUserId		= $mycms->getLoggedUserId();
		
		// FETCHING LOGGED USER DETAILS
		$sqlSystemUser['QUERY']      = "SELECT * FROM "._DB_CONF_USER_." 
		                               WHERE `a_id` = '".$loggedUserId."'";
									   
		$resultSystemUser   = $mycms->sql_select($sqlSystemUser);
		$rowSystemUser      = $resultSystemUser[0];
		
		$searchApplication  = 0;
		
		
	?>
		<form name="frmSearch" method="post" action="monitoring.process.php" onSubmit="return FormValidator.validate(this);">
			<input type="hidden" name="act" value="search_registration" />
			<input type="hidden" name="c" value="<?=$_REQUEST['c']?>" />
			<table width="100%" class="tborder" align="center">	
				<tr>
					<td class="tcat" colspan="2" align="left">
						<span style="float:left">General Registration</span>
						<span class="tsearchTool" forType="tsearchTool"></span>
					</td>
				</tr>
				<tr>
					<td colspan="2" style="margin:0px; padding:0px;">
						<div class="tsearch" style="display:block;">
							<table width="100%">
								<tr>
									<td align="left" width="150">User Name:</td>
									<td align="left" width="250">
										<input type="text" name="src_user_first_name" id="src_user_first_name" 
										 style="width:90%; text-transform:uppercase;" value="<?=$_REQUEST['src_user_first_name']?>" />
									</td>
									<td align="left" width="150">Unique Sequence:</td>
									<td align="left" width="250">
										<input type="text" name="src_access_key" id="src_access_key" 
									 	 style="width:90%; text-transform:uppercase;" value="<?=$_REQUEST['src_access_key']?>" />
									</td>
									<td align="right" rowspan="5">
										<?php 
										searchStatus();
										?>
										<input type="submit" name="goSearch" value="Search" 
										 class="btn btn-small btn-blue" />
									</td>
								</tr>
								<tr>
									<td align="left">Mobile No:</td>
									<td align="left">
										<input type="text" name="src_mobile_no" id="src_mobile_no" 
										 style="width:90%; text-transform:uppercase;" value="<?=$_REQUEST['src_mobile_no']?>" />
									</td>
									<td align="left">Email Id:</td>
									<td align="left">
										<input type="text" name="src_email_id" id="src_email_id" 
									 	 style="width:90%;" value="<?=$_REQUEST['src_email_id']?>" />
									</td>
								</tr>
								<tr>
									<td align="left">Conference Reg. Category:</td>
									<td align="left">
										<select name="src_conf_reg_category" id="src_conf_reg_category" style="width:96%;">
											<option value="">-- Select Category --</option>
											<?php
											$sqlFetchClassification['QUERY']	 = "SELECT `classification_title`,`id`,`currency`,`type` FROM "._DB_REGISTRATION_CLASSIFICATION_." WHERE status = 'A' AND `id`  NOT IN (3,6)";
											$resultClassification	 = $mycms->sql_select($sqlFetchClassification);			
											
											
											if($resultClassification)
											{
												foreach($resultClassification as $key=>$rowClassification) 
												{
												?>
													<option value="<?=$rowClassification['id']?>" <?=($rowClassification['id']==trim($_REQUEST['src_conf_reg_category']))?'selected="selected"':''?>>
													<?
														if($rowClassification['type']=="DELEGATE")
														{
															echo "CONFERENCE - ".$rowClassification['classification_title'];
														}
														if($rowClassification['type']=="COMBO")
														{
															echo "Residential Registration - ".$rowClassification['classification_title'];
														}
													?>
													</option>
												<?php
												}
											}
											?>
										</select>
									</td>
									<td align="left">Registration Id:</td>
									<td align="left">
										<input type="text" name="src_registration_id" id="src_registration_id" 
										 style="width:90%; text-transform:uppercase;" value="<?=$_REQUEST['src_registration_id']?>" />
									</td>
								</tr>
								<tr>
									<td align="left">Invoice No:</td>
									<td align="left">
										<input type="text" name="src_invoice_no" id="src_invoice_no" 
										 style="width:90%; text-transform:uppercase;" value="<?=$_REQUEST['src_invoice_no']?>" />
									</td>
									<td align="left">Slip No:</td>
									<td align="left">
										<input type="text" name="src_slip_no" id="src_slip_no" 
									 	 style="width:90%;" value="<?=$_REQUEST['src_slip_no']?>" />
									</td>
								</tr>
								<tr>
									<td align="left">Registration Mode:</td>
									<td align="left">
									
										<select name="src_registration_mode" id="src_registration_mode" style="width:96%;">
											<option value="">-- Select Mode --</option>
											<option value="ONLINE" <?=(trim($_REQUEST['src_registration_mode']=="ONLINE"))?'selected="selected"':''?>>ONLINE</option>
											<option value="OFFLINE" <?=(trim($_REQUEST['src_registration_mode']=="OFFLINE"))?'selected="selected"':''?>>OFFLINE</option>
										</select>
										
									</td>
									<td align="left">Transaction Id:</td>
									<td align="left">
										<input type="text" name="src_transaction_ids" id="src_transaction_ids" 
									 	 style="width:90%;" value="<?=$_REQUEST['src_transaction_ids']?>" />
									</td>
								</tr>
							</table>
						</div>		
						<table width="100%" shortData="on" >
							<thead>
								<tr class="tcat" style="font-size:12px;">
									<th width="60" align="center" data-sort="int">Sl No</th>
									<th align="left">Name & Contact</th>
									<th width="190" align="center" data-sort="int">Unique Sequence No</th>
									<th width="200" align="left">Registration Type</th>
									<th width="130" align="left">Registration Details</th>
									<th width="250" align="left">Service Taken</th>
									<th width="90" align="center">Action</th>
								</tr>
							</thead>
							<tbody>
								<?php
								@$searchCondition       = "";
								$searchCondition       .= " AND delegate.operational_area != 'EXHIBITOR'
														    AND delegate.isRegistration = 'Y'
															AND delegate.isConference = 'Y'
															AND delegate.status = 'A'
															";
								
								if($_REQUEST['src_email_id']!='')
								{
									$searchCondition   .= " AND delegate.user_email_id LIKE '%".$_REQUEST['src_email_id']."%'";
								}
								if($_REQUEST['src_access_key']!='')
								{
									$searchCondition   .= " AND delegate.user_unique_sequence LIKE '%".$_REQUEST['src_access_key']."%'";
								}
								if($_REQUEST['src_mobile_no']!='')
								{
									$searchCondition   .= " AND delegate.user_mobile_no LIKE '%".$_REQUEST['src_mobile_no']."%'";
								}
								if($_REQUEST['src_user_first_name']!='')
								{
									$searchCondition   .= " AND delegate.user_first_name LIKE '%".$_REQUEST['src_user_first_name']."%'";
								}
								if($_REQUEST['src_user_middle_name']!='')
								{
									$searchCondition   .= " AND delegate.user_middle_name LIKE '%".$_REQUEST['src_user_middle_name']."%'";
								}
								if($_REQUEST['src_user_last_name']!='')
								{
									$searchCondition   .= " AND delegate.user_last_name LIKE '%".$_REQUEST['src_user_last_name']."%'";
								}
								if($_REQUEST['src_transaction_ids']!='')
								{
									$searchApplication	= 1;
									$searchCondition   .= " AND LOCATE('".$_REQUEST['src_transaction_ids']."', totalInvoicePayment.atomTransactionIds) > 0";
								}
								if($_REQUEST['src_atom_transaction_ids']!='')
								{
									$searchApplication	= 1;
									$searchCondition   .= " AND LOCATE('".$_REQUEST['src_atom_transaction_ids']."', totalInvoicePayment.atomAtomTransactionIds) > 0";
								}
								if($_REQUEST['src_conf_reg_category']!='')
								{
									$searchCondition   .= " AND delegate.registration_classification_id = '".$_REQUEST['src_conf_reg_category']."'";
								}
								if($_REQUEST['src_registration_id']!='')
								{
									$searchCondition   .= " AND (delegate.user_registration_id LIKE '%".$_REQUEST['src_registration_id']."%' 
									                             AND (delegate.registration_payment_status = 'ZERO_VALUE' 
															          OR delegate.registration_payment_status = 'COMPLIMENTARY'
																	  OR delegate.registration_payment_status = 'PAID'))";
								}
								
								if($_REQUEST['src_reg_date']!='')
								{
									$searchCondition   .= " AND DATE(delegate.created_dateTime) = '".$_REQUEST['src_reg_date']."'";
								}
								
								
								if($_REQUEST['src_country_id']!='')
								{
									$searchCondition   .= " AND delegate.user_country_id = '".$_REQUEST['src_country_id']."'";
								}
								
								if($_REQUEST['src_state_id']!='')
								{
									$searchCondition   .= " AND delegate.user_state_id = '".$_REQUEST['src_state_id']."'";
								}
								$sqlFetchUser       = "";
													
								$sqlFetchUser    	  = registrationDetailsQuerySet("", $searchCondition,"");
								
								$resultFetchUser      = $mycms->pagination(1, $sqlFetchUser, 10, $restrt);	
								
								
								if($resultFetchUser)
								{
									
									foreach($resultFetchUser as $i=>$rowFetchUser) 
									{
										$status =true;
										$counter             = $counter + 1;
										$color ="#FFFFFF";
										if($rowFetchUser['account_status']=="UNREGISTERED")
										{
											$color ="#FFCCCC";
											$status =false;
										}
										
										$totalAccompanyCount = 0;
										//$totalAccompanyCount = getTotalAccompanyCount($rowFetchUser['id']);
								?>
										<tr class="tlisting" bgcolor="<?=$color?>">
											<td align="center" valign="top"><?=$counter + ($_REQUEST['_pgn1_']*10)?></td>
											<td align="left" valign="top">
												<?=strtoupper($rowFetchUser['user_title'])?> 
												<?=strtoupper($rowFetchUser['user_first_name'])?> 
												<?=strtoupper($rowFetchUser['user_middle_name'])?> 
												<?=strtoupper($rowFetchUser['user_last_name'])?>

												<br />
												<?=$rowFetchUser['user_mobile_isd_code'].$rowFetchUser['user_mobile_no']?>
												<br />
												<?=$rowFetchUser['user_email_id']?>
												<br />
												<font style="color:#0033FF;"><?= $rowFetchUser['tags']?></font>
												<?php
												if($rowFetchUser['account_status']=="UNREGISTERED")
												{
												?>
													<br><span style="color:#D41000; font-weight:bold;">Unregistered</span>
												<?php
												}
												else
												{
												
													$isaccomodationstatus['QUERY']	=  "SELECT * FROM "._DB_USER_UNREGISTER_REQUEST_."
																		 WHERE `delegate_id` 	= '".$rowFetchUser['id'] ."'
																		   AND `request_status` IS NULL";
													//$fetchstatus		    =	$mycms->sql_select($isaccomodationstatus);
												   if($fetchstatus)
													{
													echo ($rowFetchUser['totalUnregisterRequest']>0)?'<br><span style="color:#D41000">REQUEST TO UNREGISTER</span>':'';
													}
												}
												?>
											</td>
											<td align="center" valign="top"><?	if($rowFetchUser['registration_payment_status']=="PAID" 
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
												?></td>
											<td align="left" valign="top">
												<?php
												if($rowFetchUser['isRegistration']=="Y")
												{
													echo $rowFetchUser['classification_title'];
													echo "<br />";
													echo $rowFetchUser['cutoffTitle'];
												}
												?>
											</td>
											<td align="left" valign="top">
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
												<?=date('d/m/Y h:i A', strtotime($rowFetchUser['created_dateTime']))?>
											</td>
											
											
											
											<td align="left" valign="top">
												<?
												$paymentStatus = registrationPaymentStatus($rowFetchUser['id'],'RESIDENTIAL');
												echo $paymentStatus['TITLE'];
												$paymentStatus = registrationPaymentStatus($rowFetchUser['id'],'CONFERENCE');
												echo $paymentStatus['TITLE'];
												$paymentStatus = registrationPaymentStatus($rowFetchUser['id'],'WORKSHOP');
												echo $paymentStatus['TITLE'];
												$paymentStatus = registrationPaymentStatus($rowFetchUser['id'],'ACCOMPANY');
												echo $paymentStatus['TITLE'];
												$paymentStatus = registrationPaymentStatus($rowFetchUser['id'],'ACCOMMODATION');
												echo $paymentStatus['TITLE'];
												$paymentStatus = registrationPaymentStatus($rowFetchUser['id'],'TOUR');
												echo $paymentStatus['TITLE'];
												?>
												
												<!--<span class="paymentDtls">Workshop:</span><span class="paidStatus">PAID</span>
												<span class="paymentDtls">Accompany:<span></span></span>
												<span class="paymentDtls">Accommodation:<span></span></span>
												<span class="paymentDtls">Tour:<span></span></span>-->
											</td>
											<td align="center">
												<a href="registration_monitoring.php?show=view_delegate&id=<?=$rowFetchUser['id']?><?=$searchString?>">
												<span title="View" class="icon-eye" /></a>
											</td>
										</tr>
								<?php
									}
								} 
								else 
								{
								?>
									<tr>
										<td colspan="7" align="center">
											<span class="mandatory">No Record Present.</span>												
										</td>
									</tr>  
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
		</form>
	<?php
	}
	
	function viewGeneralRegistration($cfg, $mycms)
	{
		include_once("../../includes/function.delegate.php");
		include_once("../../includes/function.registration.php");
		include_once("../../includes/function.invoice.php");
		global $searchArray, $searchString;
		
		$loggedUserId		= $mycms->getLoggedUserId();
		
		// FETCHING LOGGED USER DETAILS
		$loggedUserId = array();
		$sqlSystemUser['QUERY']      = "SELECT * FROM "._DB_CONF_USER_." 
									   WHERE `a_id` = ? ";
									   
	    $sqlSystemUser['PARAM'][]  = array('FILD' => 'a_id',  'DATA' => $loggedUserId ,  'TYP' => 's');	
															   
									   
		$resultSystemUser   = $mycms->sql_select($sqlSystemUser);
		$rowSystemUser      = $resultSystemUser[0];
		
		$searchApplication  = 0;
		
		
	?>
		<script>
		$(document).ready(function(){
			$("td[use=registrationDetailsList]").attr("dataStat","noDisplay");
			loadUserDetails();
		});
		
		function loadUserDetails()
		{
			if($("td[use=registrationDetailsList][dataStat=noDisplay]").length > 0)
			{
				var detailsTd = $("td[use=registrationDetailsList][dataStat=noDisplay]").first();
				var userId = $(detailsTd).attr("userId");
				
				var param = "act=registrationList&id="+userId;
				$.ajax({
					  url: "registration.process.php",
					  type: "POST",
					  data: param,
					  dataType: "html",
					  success: function(data){
						 $(detailsTd).html(data);
						 $(detailsTd).attr("dataStat","Display");
						 loadUserDetails();
					  }
				   }
				);
			}
		}
		</script>
		<form name="frmSearch" method="post" action="monitoring.process.php" onSubmit="return FormValidator.validate(this);">
			<input type="hidden" name="act" value="search_registration" />
			<input type="hidden" name="c" value="<?=$_REQUEST['c']?>" />
			<table width="100%" class="tborder" align="center">	
				<tr>
					<td class="tcat" colspan="2" align="left">
						<span style="float:left">General Registration</span>
						<span class="tsearchTool" forType="tsearchTool"></span>
					</td>
				</tr>
				<tr>
					<td colspan="2" style="margin:0px; padding:0px;">
									
						
						<div class="tsearch" style="display:block;">
							<table width="100%">
								<tr>
									<td align="left" width="150">User Name:</td>
									<td align="left" width="250">
										<input type="text" name="src_user_first_name" id="src_user_first_name" 
										 style="width:90%; text-transform:uppercase;" value="<?=$_REQUEST['src_user_first_name']?>" />
									</td>
									<td align="left" width="150">Unique Sequence:</td>
									<td align="left" width="250">
										<input type="text" name="src_access_key" id="src_access_key" 
									 	 style="width:90%; text-transform:uppercase;" value="<?=$_REQUEST['src_access_key']?>" />
									</td>
									<td align="right" rowspan="5">
										<?php 
										if($_REQUEST['c']== 1)
										{
											$string = "&c=1&src_reg_date=".$_REQUEST['src_reg_date'];
										?>
										<input type="hidden" name="src_reg_date" value="<?=$_REQUEST['src_reg_date']?>" />
										<?php
										}
										if($_REQUEST['c']== 2)
										{
											$string = "&c=2&src_country_id=".$_REQUEST['src_country_id']."&src_state_id=".$_REQUEST['src_state_id'];
										?>
										<input type="hidden" name="src_country_id" value="<?=$_REQUEST['src_country_id']?>" />
										<input type="hidden" name="src_state_id" value="<?=$_REQUEST['src_state_id']?>" />
										<?php
										}
										searchStatus('?show=view_registration'.$string);
										?>
										<input type="submit" name="goSearch" value="Search" 
										 class="btn btn-small btn-blue" />
									</td>
								</tr>
								<tr>
									<td align="left">Mobile No:</td>
									<td align="left">
										<input type="text" name="src_mobile_no" id="src_mobile_no" 
										 style="width:90%; text-transform:uppercase;" value="<?=$_REQUEST['src_mobile_no']?>" />
									</td>
									<td align="left">Email Id:</td>
									<td align="left">
										<input type="text" name="src_email_id" id="src_email_id" 
									 	 style="width:90%;" value="<?=$_REQUEST['src_email_id']?>" />
									</td>
								</tr>
								<tr>
									<td align="left">Conference Reg. Category:</td>
									<td align="left">
										<select name="src_conf_reg_category" id="src_conf_reg_category" style="width:96%;">
											<option value="">-- Select Category --</option>
											<?php
											$sqlFetchClassification['QUERY']	 = "SELECT `classification_title`,`id`,`currency`,`type` FROM "._DB_REGISTRATION_CLASSIFICATION_." WHERE status = 'A' AND `id`  NOT IN (3,6)";
											$resultClassification	 = $mycms->sql_select($sqlFetchClassification);			
											
											
											if($resultClassification)
											{
												foreach($resultClassification as $key=>$rowClassification) 
												{
												?>
													<option value="<?=$rowClassification['id']?>" <?=($rowClassification['id']==trim($_REQUEST['src_conf_reg_category']))?'selected="selected"':''?>>
													<?
														if($rowClassification['type']=="DELEGATE")
														{
															echo "CONFERENCE - ".$rowClassification['classification_title'];
														}
														if($rowClassification['type']=="COMBO")
														{
															echo "Residential Registration - ".$rowClassification['classification_title'];
														}
													?>
													</option>
												<?php
												}
											}
											?>
										</select>
									</td>
									<td align="left">Registration Id:</td>
									<td align="left">
										<input type="text" name="src_registration_id" id="src_registration_id" 
										 style="width:90%; text-transform:uppercase;" value="<?=$_REQUEST['src_registration_id']?>" />
									</td>
								</tr>
								<tr>
									<td align="left">Invoice No:</td>
									<td align="left">
										<input type="text" name="src_invoice_no" id="src_invoice_no" 
										 style="width:90%; text-transform:uppercase;" value="<?=$_REQUEST['src_invoice_no']?>" />
									</td>
									<td align="left">Slip No:</td>
									<td align="left">
										<input type="text" name="src_slip_no" id="src_slip_no" 
									 	 style="width:90%;" value="<?=$_REQUEST['src_slip_no']?>" />
									</td>
								</tr>
								<tr>
									<td align="left">Registration Mode:</td>
									<td align="left">
									
										<select name="src_registration_mode" id="src_registration_mode" style="width:96%;">
											<option value="">-- Select Mode --</option>
											<option value="ONLINE" <?=(trim($_REQUEST['src_registration_mode']=="ONLINE"))?'selected="selected"':''?>>ONLINE</option>
											<option value="OFFLINE" <?=(trim($_REQUEST['src_registration_mode']=="OFFLINE"))?'selected="selected"':''?>>OFFLINE</option>
										</select>
										
									</td>
									<td align="left">Transaction Id:</td>
									<td align="left">
										<input type="text" name="src_transaction_ids" id="src_transaction_ids" 
									 	 style="width:90%;" value="<?=$_REQUEST['src_transaction_ids']?>" />
									</td>
								</tr>
								
								
							</table>
						</div>		
						
						
						<table width="100%" shortData="on" >
							<thead>
								<tr class="theader">
									<td width="40" align="center" data-sort="int">Sl No</th>
									<td align="left">Name & Contact</th>
									<td width="110" align="left">Registration Type</th>
									<td width="130" align="left">Registration Details</th>
									<td width="450" align="center">Service Dtls</th>
									<td width="90" align="center">Action</th>
								</tr>
							</thead>
							<tbody>
								<?php
								
								$alterCondition = "";
															
								$alterCondition .= " AND delegate.user_email_id NOT LIKE '%@encoder%'";
								
								if($_REQUEST['src_reg_date']!='')
								{
									$alterCondition   .= " AND DATE(delegate.created_dateTime) = '".$_REQUEST['src_reg_date']."'";
								}
								if($_REQUEST['src_reg_month']!='')
								{
									$alterCondition   .= " AND EXTRACT(MONTH FROM delegate.created_dateTime) = '".$_REQUEST['src_reg_month']."'";
								}
								
								
								if($_REQUEST['src_country_id']!='')
								{
									$alterCondition   .= " AND delegate.user_country_id = '".$_REQUEST['src_country_id']."'";
								}
								
								if($_REQUEST['src_state_id']!='')
								{
									$alterCondition   .= " AND delegate.user_state_id = '".$_REQUEST['src_state_id']."'";
								}
								
							   
								$sqlFetchUser         = "";
								
								$idArr = getAllDelegates("","",$alterCondition);
								
								
								if($idArr)
								{
									
									foreach($idArr as $i=>$id) 
									{
										$status = true;
										$rowFetchUser = getUserDetails($id);
										$counter      = $counter + 1;
										$color = "#FFFFFF";
										if($rowFetchUser['account_status']=="UNREGISTERED")
										{
											$color ="#FFCCCC";
											$status =false;
										}
										
										$totalAccompanyCount = 0;
										//$totalAccompanyCount = getTotalAccompanyCount($rowFetchUser['id']);
										//echo ($rowFetchUser['user_mobile_isd_code']);
								?>
										<tr class="tlisting" bgcolor="<?=$color?>">
											<td align="center" valign="top"><?=$counter + ($_REQUEST['_pgn1_']*10)?></td>
											<td align="left" valign="top">
												<?=strtoupper($rowFetchUser['user_title'])?> 
												<?=strtoupper($rowFetchUser['user_first_name'])?> 
												<?=strtoupper($rowFetchUser['user_middle_name'])?> 
												<?=strtoupper($rowFetchUser['user_last_name'])?>

												<br />
												<?=$rowFetchUser['user_mobile_isd_code'].$rowFetchUser['user_mobile_no']?>
												<br />
												<?=$rowFetchUser['user_email_id']?>
												<br />
												<font style="color:#0033FF;"><?= $rowFetchUser['tags']?></font>
												<?php
												if($rowFetchUser['account_status']=="UNREGISTERED")
												{
												?>
													<br><span style="color:#D41000; font-weight:bold;">Unregistered</span>
												<?php
												}
												
												?>
											</td>
											
											<td align="left" valign="top">
												<?php
												if($rowFetchUser['isRegistration']=="Y")
												{
													echo getRegClsfName($rowFetchUser['registration_classification_id']);
													echo "<br />";
													echo getCutoffName($rowFetchUser['registration_tariff_cutoff_id']);
												}
												?>
											</td>
											<td align="left" valign="top">
												<?php
												if($rowFetchUser['registration_payment_status']=="PAID" 
												   || $rowFetchUser['registration_payment_status']=="COMPLIMENTARY"
												   || $rowFetchUser['registration_payment_status']=="ZERO_VALUE")
												{
													echo "Reg Id : ".$rowFetchUser['user_registration_id'];
													echo "<br />";
												}
												else
												{
													echo "-";
													echo "<br />";
												}
												
												if($rowFetchUser['registration_payment_status']=="PAID" 
												   || $rowFetchUser['registration_payment_status']=="COMPLIMENTARY"
												   || $rowFetchUser['registration_payment_status']=="ZERO_VALUE")
												{
													echo "Us No : ".strtoupper($rowFetchUser['user_unique_sequence']);
													echo "<br />";
												}
												else
												{
													echo "-";
													echo "<br />";
												}
												?>
												<?=date('d/m/Y h:i A', strtotime($rowFetchUser['created_dateTime']))?>
											</td>
											<td align="left" valign="top" use="registrationDetailsList" userId="<?=$rowFetchUser['id']?>">
												<img src="<?=_BASE_URL_?>css/adminPanel/<?=$cfg['THEME']?>/images/loaders/facebook.gif" />
											</td>
											<!--<td align="center" valign="top">Payment</td-->
											<td align="center" valign="top">
												<a onclick="openDetailsPopup(<?=$rowFetchUser['id']?>);"><span title="View" class="icon-eye" /></a>
												<a href="registration.php?show=invoice&id=<?=$rowFetchUser['id']?>"><span title="Invoice" class="icon-book"/></a>
											</td>
										</tr>
								<?php
									}
								} 
								else 
								{
								?>
									<tr>
										<td colspan="7" align="center">
											<span class="mandatory">No Record Present.</span>												
										</td>
									</tr>  
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
		</form>
		
		<div class="overlay" id="fade_popup" onclick="closeProfileDetailsPopUp()"></div>
		<div class="popup_form" id="popup_profile_full_details" style="width:90%;"></div>
	<?php
	}
	
	function delegateViewFormTemplate($requestPage, $processPage, $showDeleted=false)
	{
		global $cfg, $mycms, $searchArray, $searchString;
		
		$delegateId               = addslashes(trim($_REQUEST['id']));
		
		if($showDeleted)
		{
		
			$sqlFetchUser             = registrationDetailsQuerySet($delegateId);
		}
		else
		{
			$sqlFetchUser             = registrationDetailsQuerySet($delegateId); 
		}
		$resultFetchUser          = $mycms->sql_select($sqlFetchUser);
		$rowFetchUser             = $resultFetchUser[0]; 
		
		$loggedUserId		      = $mycms->getLoggedUserId();		
		// FETCHING LOGGED USER DETAILS
		$sqlSystemUser['QUERY']      	  = "SELECT * FROM "._DB_CONF_USER_." 
		                                     WHERE `a_id` = '".$loggedUserId."'";
									   
		$resultSystemUser         = $mycms->sql_select($sqlSystemUser);
		$rowSystemUser            = $resultSystemUser[0];
	?>
		<table width="100%" align="center" class="tborder"> 
			<thead> 
				<tr> 
					<td colspan="2" align="left" class="tcat">
						<span style="float:left; background-color:#FFFFFF; width: 160px; height: 160px;">
							<?php
							@$userImage        = "";
							
							if($rowFetchUser['user_image']!="" && file_exists("../../".$cfg['USER.PROFILE.IMAGE'].$rowFetchUser['user_image']))
							{
								$userImage     = $cfg['BASE_URL'].$cfg['USER.PROFILE.IMAGE'].$rowFetchUser['user_image'];
							}
							else
							{
								$userImage     = $cfg['DIR_CM_IMAGES']."noUserCircularImage.jpg";
							}
							?>
							<img src="<?=$userImage?>" width="160" height="160" />
						</span>
						<span style="float:left; margin-top:18px; margin-left:20px;">
							<br />
							<span style="font-size:36px;">
								<?=strtoupper($rowFetchUser['user_first_name'])?> 
								<?=strtoupper($rowFetchUser['user_middle_name'])?> 
								<?=strtoupper($rowFetchUser['user_last_name'])?>
							</span>
							<br />
							<br />
							<span style="font-weight:normal;font-size: 13px;"><b>Mobile Number:</b> <?=$rowFetchUser['user_mobile_isd_code'].$rowFetchUser['user_mobile_no']?></span>
							<br />
							<span style="font-weight:normal;font-size: 13px;"><b>Email Id:</b> <?=$rowFetchUser['user_email_id']?></span>
							<br />
							
						</span>
					</td> 
				</tr> 
			</thead> 
			<tbody>
				<tr>
					<td colspan="2" style="margin:0px; padding:0px;">  
					
						<table width="100%">
							<tr class="thighlight">
								<td colspan="4" align="left">Login Details</td>
							</tr>
							<tr >
								<td width="20%" align="left" valign="top">Email Id:</td>
								<td width="30%" align="left" valign="top"><?=$rowFetchUser['user_email_id']?></td>
								<td width="20%" align="left" valign="top">Unique Sequence:</td>
								<td width="30%" align="left" valign="top" >
									<?	
									if($rowFetchUser['registration_payment_status']=="PAID" 
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
							</tr>
							<tr>
								<td align="left">Registration Id:</td>
								<td align="left">
								<?php
								
								
								if($rowFetchUser['registration_payment_status']=="PAID" 
								   || $rowFetchUser['registration_payment_status']=="COMPLIMENTARY" 
								   || $rowFetchUser['registration_payment_status']=="ZERO_VALUE" 
								   || $rowFetchUser['workshop_payment_status']=="PAID" 
								   || $rowFetchUser['workshop_payment_status']=="COMPLIMENTARY"
								   || $rowFetchUser['workshop_payment_status']=="ZERO_VALUE")
								{
									echo $rowFetchUser['user_registration_id'];
								}
								else
								{
									echo "-";
								}
								?>
								</td>
								<td align="left">&nbsp;</td>
								<td align="left" >&nbsp;</td>
							</tr>
						</table>
						
						
						
						<table width="100%">
								<tr class="thighlight">
									<td colspan="8" align="left"> 
									<?=ucwords(strtolower($rowFetchUser['user_full_name']))?> Invoice
									</td>
								</tr>
								<tr class="theader" >
									<td width="" align="center">Sl No</td>
									<td align="left"  width="">Invoice No</td>
									<td align="left"  width="">PV No</td>
									<td width="" align="center">Invoice Mode</td>
									<td align="center">Invoice For</td>
									<td width="" align="center">Invoice Date</td>
									<td width="" align="right">Invoice Amount</td>
									<td width="" align="center">Payment Status</td>
									<!--<td width="70" align="center">Action</td>-->
								</tr>
								<?php
								$invoiceCounter                 = 0;
								$sqlFetchInvoice                = invoiceDetailsQuerySet("",$delegateId,"");
																
								$resultFetchInvoice             = $mycms->sql_select($sqlFetchInvoice);
								
								
								if($resultFetchInvoice)
								{
									foreach($resultFetchInvoice as $key=>$rowFetchInvoice)
									{
										$invoiceCounter++;
										$slip = getInvoice($rowFetchInvoice['slip_id']);
										
										
										$thisUserDetails = getUserDetails($rowFetchInvoice['delegate_id']);
										$type			 = "";
										if($rowFetchInvoice['service_type'] == "DELEGATE_CONFERENCE_REGISTRATION")
										{
											$type = "CONFERENCE REGISTRATION OF ".$thisUserDetails['user_full_name'];
										}
										if($rowFetchInvoice['service_type'] == "DELEGATE_WORKSHOP_REGISTRATION")
										{
											$workShopDetails = getWorkshopDetails($rowFetchInvoice['refference_id']);
											$type =  getWorkshopName($workShopDetails['workshop_id'])." REGISTRATION ".$thisUserDetails['user_full_name'];
										}
										if($rowFetchInvoice['service_type'] == "ACCOMPANY_CONFERENCE_REGISTRATION")
										{
											$type = "ACCOMPANY REGISTRATION OF ".$thisUserDetails['user_full_name'];
										}
										if($rowFetchInvoice['service_type'] == "DELEGATE_RESIDENTIAL_REGISTRATION")
										{
											$type = "RESIDENTIAL REGISTRATION OF ".$thisUserDetails['user_full_name'];
										}
										if($rowFetchInvoice['service_type'] == "DELEGATE_ACCOMMODATION_REQUEST")
										{
											$type = "ACCOMMODATION REGISTRATION OF ".$thisUserDetails['user_full_name'];
										}
										if($rowFetchInvoice['service_type'] == "DELEGATE_TOUR_REQUEST")
										{
											$tourDetails = getTourDetails($rowFetchInvoice['refference_id']);
											
											$type = getTourName($tourDetails['package_id'])." REGISTRATION OF ".$thisUserDetails['user_full_name'];
										}
										
										$styleColor = 'background: rgb(204, 229, 204);';
										$isCancel	= 'NO';
										if($rowFetchInvoice['status'] =='C')
										{
											$styleColor = 'background: rgb(255, 204, 204);';
											$isCancel	= 'YES';
											$type = "Invoice Cancelled";
										}
								?>
										<tr class="tlisting" <?=$styleColor?>>
											<td align="center" bgcolor="#CCE5CC"><?=$invoiceCounter?></td>
											<td align="left" bgcolor="#CCE5CC"><?=$rowFetchInvoice['invoice_number']?></td>
											<td align="left" bgcolor="#CCE5CC"><?=$slip['slip_number']?></td>
											<td align="center" bgcolor="#CCE5CC"><?=$rowFetchInvoice['invoice_mode']?></td>
											<td align="left" bgcolor="#CCE5CC"><?=$type?></td>
											<td align="center" bgcolor="#CCE5CC"><?=setDateTimeFormat2($rowFetchInvoice['invoice_date'], "D")?></td>
											<td align="right" bgcolor="#CCE5CC">
											<?=$rowFetchInvoice['currency']?> <?=number_format($rowFetchInvoice['service_roundoff_price'],2)?>
											</td>
											<td align="center" bgcolor="#CCE5CC">
												<?php
												if($rowFetchInvoice['payment_status']=="COMPLIMENTARY")
												{
												?>
													<span style="color:#5E8A26;">Complementary</span>
												<?php
												}
												elseif($rowFetchInvoice['payment_status']=="ZERO_VALUE")
												{
												?>
													<span style="color:#009900;">Zero Value	</span>
												<?php
												}
												else if($rowFetchInvoice['payment_status']=="PAID")
												{
												?>
													<span style="color:#5E8A26;">Paid</span>
												<?php		
												}
												else if($rowFetchInvoice['payment_status']=="UNPAID")
												{
												?>
													<span style="color:#C70505;">UNPAID</span>
												<?php		
												}
												?>
											</td>
											
										</tr>
								<?php
									}
								}
								?>
							</table>
						
						<table width="100%">
							<tr class="thighlight">
								<td colspan="4" align="left">User Details</td>
							</tr>
							<tr>
								<td width="20%" align="left">Name:</td>
								<td width="30%" align="left">
									<?=strtoupper($rowFetchUser['user_first_name'])?> 
									<?=strtoupper($rowFetchUser['user_middle_name'])?> 
									<?=strtoupper($rowFetchUser['user_last_name'])?>
								</td>
								<td width="20%" align="left"></td>
								<td width="30%" align="left"></td>
							</tr>
							<tr>
								<td align="left">Address:</td>
								<td rowspan="4" align="left" valign="top"><?=strtoupper($rowFetchUser['user_address'])?></td>
								<td align="left">Country:</td>
								<td align="left"><?=strtoupper($rowFetchUser['country_name'])?></td>
							</tr>
							<tr>
								<td align="left">&nbsp;</td>
								<td align="left">State:</td>
								<td align="left"><?=strtoupper($rowFetchUser['state_name'])?></td>
							</tr>
							<tr>
								<td align="left"></td>
								<td align="left">City:</td>
								<td align="left"><?=strtoupper($rowFetchUser['user_city_id'])?></td>
							</tr>
							<tr>
								<td align="left"></td>
								<td align="left">Postal Code:</td>
								<td align="left"><?=strtoupper($rowFetchUser['user_pincode'])?></td>
							</tr>
							<tr>
								<td align="left">Mobile:</td>
								<td align="left"><?=$rowFetchUser['user_mobile_isd_code'].$rowFetchUser['user_mobile_no']?></td>
								<td align="left">Phone No:</td>
								<td align="left"><?=$rowFetchUser['user_phone_no']?></td>
							</tr>
						</table>
						
						
						
						<?php
						if($rowFetchUser['isRegistration']=="Y")
						{
						?>
							<table width="100%">
								<tr class="thighlight">
									<td colspan="4" align="left">Registration Tariff</td>
								</tr>
								<tr>
									<td width="20%" align="left" valign="top">Tariff Classification:</td>
									<td width="30%" align="left" valign="top"><?=$rowFetchUser['classification_title']?></td>
									<td width="20%" align="left" valign="top">Tariff Cut Off:</td>
									<td width="30%" align="left" valign="top"><?=$rowFetchUser['cutoffTitle']?></td>
								</tr>
							</table>
						
							
							
							
							<table width="100%">
							<tr class="thighlight">
								<td colspan="3" align="left">Workshop Registration Details</td>
							</tr>
							<?
							$resultWorkshopDtls 	= getWorkshopDetailsOfDelegate($delegateId);
							//echo "<pre>"; print_r($resultAccommodationDtls); echo "<pre>";
							if($resultWorkshopDtls)
							{
								
							?>
							<tr class="theader">
								<td align="left">Workshop Name</td>
								<td align="left" width="400" >Booking Cut-off</td>
								<td align="center" width="150">Payment Status</td>
							</tr>	
							<?
								$cutoff = "";
								foreach($resultWorkshopDtls as $keyWorkshopDtls=>$rowWorkshopDtls)
								{
								?>
								<tr>
									<td align="left"><?=getWorkshopName($rowWorkshopDtls['workshop_id'])?></td>
									<td align="left"><?=getCutoffName($rowWorkshopDtls['tariff_cutoff_id'])?></td>
									<td align="center">
										<?
										if($rowWorkshopDtls['payment_status'] == 'UNPAID')
										{
											echo '<span class="unpaidStatus">UNPAID</span>';
										}
										else if($rowWorkshopDtls['payment_status'] == 'PAID')
										{
											echo '<span class="paidStatus">PAID</span>';
										}
										else if($rowWorkshopDtls['payment_status'] == 'ZERO_VALUE')
										{
											echo '<span class="paidStatus">ZERO VALUE</span>';
										}
										?>
									</td>
								</tr>
								<?
								}
							}
							else
							{
							?>
							<tr>
								<td colspan="3" align="center">
									<span class="mandatory">No Record Present.</span>
								</td>
							</tr>
							<?
							}
						?>
						</table>
							
							<table width="100%">			
							<tr class="thighlight">
								<td colspan="7" align="left">Payment Details</td>
							</tr>
							<tr class="theader">
								<td width="50" align="center">Sl No</td>
								<td width="100" align="left">Invoice No</td>
								<td width="100" align="center">Payment Mode</td>
								<td width="100" align="center">Payment Date</td>
								<td width="100" align="right">Payment Amt.</td>
								<td width="300" align="left">Description</td>
								<td align="left">Remarks</td>
							</tr>
							<?php
							$paymentCounter       = 0;
							$sqlFetchPayment['QUERY']      = "SELECT slip.slip_number,
															payment.* 
													   
													   FROM "._DB_SLIP_." slip
												 
												 INNER JOIN "._DB_PAYMENT_." payment
														 ON slip.id = payment.slip_id  
												 
													  WHERE slip.delegate_id = '".$rowFetchUser['id']."'
														AND payment.status = 'A'  
													 
												   ORDER BY payment.payment_date DESC";
												   
							$resultFetchPayment   = $mycms->sql_select($sqlFetchPayment);
							if($resultFetchPayment)
							{
								foreach($resultFetchPayment as $key=>$rowFetchPayment)
								{
									$paymentCounter++;
									
									$paymentDescription     = "-";
									
									if($rowFetchPayment['payment_mode']=="Cash")
									{
										$paymentDescription = "Paid by <b>Cash</b>. Date of Deposit: <b>".setDateTimeFormat2($rowFetchPayment['cash_deposit_date'], "D")."</b>.";
									}
									if($rowFetchPayment['payment_mode']=="Cheque")
									{
										$paymentDescription = "Paid by <b>Cheque</b>. Cheque Number: <b>".$rowFetchPayment['cheque_number']."</b>.<br>
															   Cheque Date: <b>".setDateTimeFormat2($rowFetchPayment['cheque_date'], "D")."</b>.
															   Cheque Drawn Bank: <b>".$rowFetchPayment['cheque_drawn_bank']."</b>.";
									}
									if($rowFetchPayment['payment_mode']=="Draft")
									{
										$paymentDescription = "Paid by <b>Draft</b>. Draft Number: <b>".$rowFetchPayment['draft_number']."</b>.<br>
															   Draft Date: <b>".setDateTimeFormat2($rowFetchPayment['draft_date'], "D")."</b>.
															   Draft Drawn Bank: <b>".$rowFetchPayment['draft_drawn_bank']."</b>.";
									}
									if($rowFetchPayment['payment_mode']=="NEFT")
									{
										$paymentDescription = "Paid by <b>NEFT</b>. NEFT Transaction Number: <b>".$rowFetchPayment['neft_transaction_no']."</b>.<br>
															   Transaction Date: <b>".setDateTimeFormat2($rowFetchPayment['neft_date'], "D")."</b>.
															   Transaction Bank: <b>".$rowFetchPayment['neft_bank_name']."</b>.";
									}
									if($rowFetchPayment['payment_mode']=="RTGS")
									{
										$paymentDescription = "Paid by <b>RTGS</b>. RTGS Transaction Number: <b>".$rowFetchPayment['rtgs_transaction_no']."</b>.<br>
															   Transaction Date: <b>".setDateTimeFormat2($rowFetchPayment['rtgs_date'], "D")."</b>.
															   Transaction Bank: <b>".$rowFetchPayment['rtgs_bank_name']."</b>.";
									}
									if($rowFetchPayment['payment_mode']=="Online" && $rowFetchPayment['online_transaction_gateway']=="ATOM")
									{
										$paymentDescription = "Online Transaction Gateway: <b>Atom</b>. 
															   Atom Bank Transaction Id: <b>".$rowFetchPayment['atom_bank_transaction_id']."</b>.<br>
															   Atom Transaction Id: <b>".$rowFetchPayment['atom_atom_transaction_id']."</b>. 
															   Transaction Card Number: <b>".$rowFetchPayment['atom_transaction_card_no']."</b>.";
									}
							?>
									<tr class="tlisting">
										<td align="center" valign="top"><?=$paymentCounter?></td>
										<td align="left" valign="top"><?=$rowFetchPayment['slip_number']?></td>
										<td align="center" valign="top"><?=$rowFetchPayment['payment_mode']?></td>
										<td align="center" valign="top"><?=setDateTimeFormat2($rowFetchPayment['payment_date'], "D")?></td>
										<td align="right" valign="top"><?=$rowFetchPayment['amount']?></td>
										<td align="left" valign="top"><?=$paymentDescription?></td>
										<td align="left" valign="top"><?=$rowFetchPayment['remarks']?></td>
									</tr>
							<?php
								}
							}
							else
							{
							?>
								<tr>
									<td align="center" colspan="6">
										<span class="mandatory">No Record(s) Found !!</span>
									</td>
								</tr>
							<?php
							}
							?>	
						</table>
						<?php
						}
						?>
					</td>
				</tr>
				<tr>
					<td width="52%"></td>
					<td align="left">
						<input type="button" name="Back" id="Back" value="Back" 
						 class="btn btn-small btn-red" onClick="location.href='registration_monitoring.php?show=view_registration<?=$searchString?>';"/>
					</td>
				</tr>
				<tr>
					<td colspan="2" class="tfooter">&nbsp;</td>
				</tr>
			</tbody> 
		</table>
	<?php
	}
	
	function fullDeletedRegistrationDetailsCompressedQuerySet($delegateId="", $searchCondition="")
	{
		global $cfg, $mycms;
		
		$sqlBigJoin                = "SET OPTION SQL_BIG_SELECTS = 1";
		mysql_query($sqlBigJoin);
		
		$filterCondition           = "";
		
		if($delegateId != "")
		{
			$filterCondition      .= " AND delegate.id = '".$delegateId ."'";
		}
		
		$sqlDelegateQueryset['QUERY']       = "SELECT delegate.*,
		                                     
											 country.country_name,
											 state.state_name,
											 
											 tariffCutoff.cutoff_title AS cutoffTitle,
											 registrationClassification.classification_title,
											 
											 IFNULL(totalUnregisterRequest, 0) AS totalUnregisterRequest 
					 
										FROM "._DB_USER_REGISTRATION_." delegate 
										
							 LEFT OUTER JOIN "._DB_REGISTRATION_CLASSIFICATION_." AS registrationClassification
									      ON delegate.registration_classification_id = registrationClassification.id
										 
							 LEFT OUTER JOIN "._DB_TARIFF_CUTOFF_." AS tariffCutoff
								          ON delegate.registration_tariff_cutoff_id = tariffCutoff.id
							 
							 LEFT OUTER JOIN "._DB_COMN_COUNTRY_." country
										  ON delegate.user_country_id = country.country_id
												 
							 LEFT OUTER JOIN "._DB_COMN_STATE_." state
										  ON delegate.user_state_id = state.st_id
							 
							 LEFT OUTER JOIN (
							 
							 						SELECT COUNT(*) AS totalUnregisterRequest,  
													       delegate_id
													
													  FROM "._DB_USER_UNREGISTER_REQUEST_."
													 WHERE `status` = 'A'    
													   
												  GROUP BY `delegate_id`  
							 				 
											 ) unregisterRequest
										  ON delegate.id = unregisterRequest.delegate_id 
							 			
									   WHERE delegate.user_type = 'DELEGATE'
									     AND delegate.status = 'FD' ".$filterCondition." ".$searchCondition." 
											  
									ORDER BY created_dateTime DESC, delegate.id DESC"; 
		
		return $sqlDelegateQueryset;
	}
?>


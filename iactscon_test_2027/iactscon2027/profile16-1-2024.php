<?php
include_once("includes/frontend.init.php"); 
include_once("includes/function.registration.php");
include_once("includes/function.delegate.php");
include_once("includes/function.invoice.php");
include_once("includes/function.workshop.php");
include_once("includes/function.dinner.php");
include_once("includes/function.accompany.php");
include_once("includes/function.abstract.php");
include_once('includes/function.accommodation.php'); 

if(strtolower($_SERVER['HTTP_HOST'])=='localhost' || $_SESSION['SHOW']=='YES')
{
//
}
else
{
	///header("location: https://www.ruedakolkata.com/aiccrcog2019_ver0/profile.php");
}

$loginDetails 	 = login_session_control();
$delegateId 	 = $loginDetails['DELEGATE_ID'];
$rowUserDetails  = getUserDetails($delegateId);
$invoiceList 	 = getConferenceContents($delegateId);	
$currentCutoffId = getTariffCutoffId();

$offline_payments = json_decode($cfg['PAYMENT.METHOD']);

//echo '<pre>'; print_r($offline_payments);

//print_r($loginDetails);die;

// abstract related work by weavers stat
$operate		 = false;

if( $cfg['ABSTRACT.SUBMIT.LASTDATE'] >= date('Y-m-d') ){
	$operate = true;
}
// abstract related work by weavers end

	$sqlHeader    =   array();
	$sqlHeader['QUERY'] = "SELECT * FROM "._DB_EMAIL_SETTING_." 
	                        WHERE `status`='A' order by id desc limit 1";
	 //$sql['PARAM'][]  =   array('FILD' => 'status' ,           'DATA' => 'A' ,                   'TYP' => 's');                    
	$resultHeader = $mycms->sql_select($sqlHeader);
	$rowHeader  = $resultHeader[0];

	$header_image = _BASE_URL_.$cfg['EMAIL.HEADER.FOOTER.IMAGE'].$rowHeader['header_image'];

 $act = $_REQUEST['act']; 
switch($act)
	{
		case'paymentSetInit': 
			$slip_id = $_REQUEST['slip_id'];
			$delegateId = $_REQUEST['delegate_id'];
			$mode = $_REQUEST['mode'];
			if(!empty($slip_id) && !empty($delegateId) && !empty($mode)){
				global  $cfg, $mycms;
				$mycms->setSession('LOGGED.USER.ID',$delegateId);
			?>
				<center>
				    <form action="<?=_BASE_URL_?>payment.retry.php" method="post" name="loginUnpaidOnlineFrm" style="display: none;">   <!--registration.process.php-->
				        <input type="hidden" id="slip_id" name="slip_id" value="<?=$slip_id?>" />
				        <input type="hidden" id="delegate_id" name="delegate_id" value="<?=$delegateId?>" />
				        <input type="hidden" name="act" value="paymentSet" />
				        <input type="hidden" name="mode" value="<?=$mode?>" />
				        <h5 align="center">Processing Payment Mode<br />Please Wait</h5>
				        <img src="<?=_BASE_URL_?>images/PaymentPreloader.gif"/><br/>
				        <h3 align="center">Please do not click 'back' or 'refresh' button or close the browser window.</h3>
				        <br/>
				        <hr />
				    </form>
				</center>
				<script  type="text/javascript">
				 document.loginUnpaidOnlineFrm.submit();
				</script>
		<?	
			}
			exit();
		break;
	}

	
?>
<!DOCTYPE html>
<html lang="en">
	<head>
		<meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <link rel="icon" href="image/favicon.png" type="favicon">
		<title>:: Profile | <?php echo $cfg['EMAIL_CONF_NAME'];?> ::</title>
		
<?php
		setTemplateStyleSheet();
		setTemplateBasicJS();
		backButtonOffJS();
?>
		<link rel="stylesheet" type="text/css" href="<?=_DIR_CM_CSS_."website/"?>roboto_css.css" />
		<link rel="stylesheet" type="text/css" href="<?=_DIR_CM_CSS_."website/"?>profile_css.php?link_color=005e82" />
        <link rel="stylesheet" type="text/css" href="<?=_DIR_CM_CSS_."website/"?>all.css" />
	</head>
	<style type="text/css">
		.strikethrough{
			text-decoration: line-through;
			color:#AD3D2D;
		}
	</style>
	<body> 
		<div class="container-fluied" style="height:102vh;">
			<div class="container">
				<div class="row">		
					 <input type="hidden" name="gst_flag" id="gst_flag" value="<?php echo $cfg['GST.FLAG']; ?>">					
					<div class="col-xs-11 profileright-section" style="width: 89%;">
						<div class="col-xs-12 welcome" style="padding-right: 0;display:none;">
							<h3 class="pull-right">Welcome, <?=$rowUserDetails['user_full_name']?> <button type="button" class="btn btn-primary" onClick="window.location.href='<?=_BASE_URL_?>login.process.php?action=logout'">Log Out</button></h3>
						</div>
						
						<div class="col-sm-4 col-xs-12 profile-right-section" >
							<div class="col-xs-12" style="padding: 0;"><img src="<?php echo $header_image; ?>" style="width: 100%"></div>
							<div class="col-xs-12" style="margin-top: -90px; padding: 0; display:block;">
								<div style="margin:0 auto; width:34%;">
									<img src="<?=_BASE_URL_?>images/nopic.png" style="width: 100%; border: thick solid #005e82;">
								</div>
								<p style="text-align:center; font-size: 18px; font-weight: bold; color: #005e82; padding:8px 0px;"><?=$rowUserDetails['user_full_name']?> </p>
							</div>
							<?							
							if($rowUserDetails['registration_payment_status']!="UNPAID" || $rowUserDetails['registration_request'] == 'ABSTRACT')
							{
								$regDateDisplay = $mycms->cDate('d/m/Y',$invoiceList[$delegateId]['REGISTRATION'][$delegateId]['USER']['conf_reg_date']);
								$regIdDisp 		= $rowUserDetails['user_registration_id'];
								$uniqueSqDisp 	= $rowUserDetails['user_unique_sequence'];
								$emailDisp 		= $rowUserDetails['user_email_id'];
								$mobileDisp 	= $rowUserDetails['user_mobile_no'];
								
								if($rowUserDetails['registration_request'] == 'ABSTRACT')
								{
									$regDateDisplay = "-";
									$regIdDisp 		= "Provisional ";				
								}
							}
							else
							{
								$regDateDisplay = "-";
								$regIdDisp 		= "-";
								$uniqueSqDisp 	= "-";
								$emailDisp 		= $rowUserDetails['user_email_id'];
								$mobileDisp 	= $rowUserDetails['user_mobile_no'];
							}

							?>
							
							<div class="reg col-xs-12">
								<p>Reg Date : <i><?=$regDateDisplay?></i></p>
								<p>Reg ID : <i><?=$regIdDisp?></i></p>
								<p>US No : <i><?=$uniqueSqDisp?></i></p> 
								<p>Email ID : <i><?=$emailDisp?></i></p>
								<p style="margin-bottom: 0;">Mobile No : <i><?=$mobileDisp?></i></p>
							</div>
							<!-- <div class="links">
								<div class="arrow_box"><p style="margin-bottom: 0" >Quick Links</p></div>
								<div class="quick-links">
									<a href="<?=_BASE_URL_?>terms.php" target="_blank">Terms & Conditions</a>, 
									<a href="<?=_BASE_URL_?>privacy.php" target="_blank">Privacy Policy</a>, 
									<a href="<?=_BASE_URL_?>cancellation.php" target="_blank">Cancellation & Refund Policy</a>,<br/>
									
								</div>
							</div> -->
							<!--<div class="queryform">
								<div class="arrow_box"><p style="margin-bottom: 0" >Send Your Query</p></div>
								<div class="quick-links">
									<form use="query_form">
										<input type="hidden" name="action" value="message_admin" />
										<input type="hidden" name="delegateId" value="<?=$delegateId?>" />
										<textarea name="user_text" style="width: 100%; height: 150px;" required placeholder="Query / Suggestions"></textarea>
										<button class="pull-right" type="submit">SEND</button>
									</form>
								</div>
							</div>-->
							
						</div>
						
						<div class="col-sm-8 col-xs-12 profile-center-section" >						
							<div class="col-xs-12" style="padding: 0; display:none;" operationMode="bannerDisplay"><img src="<?=_BASE_URL_?>images/RegistrationBanner_1920_995.jpg" style="width: 100%"></div>
							
							<?
								
								profileSummary($delegateId, $rowUserDetails, $invoiceList, $currentCutoffId);
								
								profileConferenceRegistrationDetails($delegateId, $rowUserDetails, $invoiceList, $currentCutoffId);
								
								profileWorkshopDetails($delegateId, $rowUserDetails, $invoiceList, $currentCutoffId);
								
								profileWorkshopAdd($delegateId, $rowUserDetails, $invoiceList, $currentCutoffId);
								profileAccompanyAdd($delegateId, $rowUserDetails, $invoiceList, $currentCutoffId);
								
								profileAccompanyDetails($delegateId, $rowUserDetails, $invoiceList, $currentCutoffId);
								profileAccompanyAddBanquet($delegateId, $rowUserDetails, $invoiceList, $currentCutoffId);

								profileDinnerAdd($delegateId, $rowUserDetails, $invoiceList, $currentCutoffId);

								//profileBanquetDinnerDetails($delegateId, $rowUserDetails, $invoiceList, $currentCutoffId);

								profileAbstractSubmissionDetails($delegateId);

								profileAccommodationDetails($delegateId,$rowUserDetails, $invoiceList, $currentCutoffId);

								profileAccommodationAdds($delegateId,$rowUserDetails, $invoiceList, $currentCutoffId);

								profileAccommodationAddRoom($delegateId, $rowUserDetails, $invoiceList, $currentCutoffId);
							?>							
									
							<div class="menu-center-section col-xs-12" style="display: none;">
								<h5>Conference INVOICEoice Details of DR NEERAJ DUGAR</h5>
								<table class="table">
									<tbody>
										<tr>
											<td class="border-color">INVOICEoice</td>
											<td class="border-color">Date: 27/02/2019 
												<a class="print" style="padding: 5px 15px; margin-left: 2px">Print INVOICE</a> 
												<a class="print" style="padding: 5px 15px; ">Download INVOICE</a></td>
										</tr>
										<tr>
											<td>PV No.</td>
											<td>##SLIP110319-000617 <a class="cancel-bttn">Download Payment Voucher</a></td>
										</tr>
										<tr>
											<td>INVOICEoice Amount</td>
											<td>INR 5100.00</td>
										</tr>
										<tr>
											<td>Payment Mode</td>
											<td>Online</td>
										</tr>
										 
									</tbody>
								</table>
							</div>
							
							<div class="menu-center-section col-xs-12" style="display: none;">
								<h5>Post Conference Workshop</h5>
								<table class="table">
									<tbody>
										<tr>
											<td>Payment Mode <span class="star">*</span></td>
											<td>
												<input type="radio" style="width: auto; height: auto"> veg
												<input type="radio" style="width: auto; height: auto"> veg
											</td>
										</tr>
										<tr>
											<td style="background: #ccc; color: black;" colspan="2">Amount: 0.00</td>
											
										</tr>
										<tr>
											<td></td>
											<td> <input type="submit" style="width: auto; height: auto" value="update Profile" class="submit"></td>
										</tr>
										
										 
									</tbody>
								</table>
							</div>
							
							<div class="menu-center-section col-xs-12" style="display: none;">
								<h5>Add Accompanying Person</h5>
								<table class="table">
									<tbody>
										<tr>
											<td colspan="2" class="sub-head border-color"> Fill-up Details 1</td>
											
										</tr>
										<tr>
											<td>Name <span class="star">*</span></td>
											<td><input type="text" style="width: 100%"></td>
										</tr>
									   <tr>
											<td>Gala Dinner</td>
										   <td>
											   <div class="radio" style="border: 0px solid #2cb8f4;
																				border-radius: 0px;
																				background: transparent;
																				padding: 0px 0px;
																				box-shadow: none;
																			   margin-top: 0;">
														<label class="container-box" style="width: 20%; float: left">One
															<input type="checkbox" checked="checked" name="radio">
															<span class="checkmark"></span>
														 </label>
													</div>
										   </td>
										</tr>
										<tr>
											<td>Food Preference</td>
											<td>
												<div class="radio" style="border: 0px solid #2cb8f4;
																				border-radius: 0px;
																				background: transparent;
																				padding: 0px 0px;
																				box-shadow: none;
																			   margin-top: 0;">
														<label class="container-box" style="width: 20%; float: left">One
															<input type="radio" checked="checked" name="radio">
															<span class="checkmark"></span>
														 </label>
														 <label class="container-box" style="width: 20%; float: left">Two
															 <input type="radio" name="radio">
															 <span class="checkmark"></span>
														 </label>
													</div>
											</td>
										</tr>
										<tr>
											<td colspan="2" style="padding: 10px"><input type="submit" style="width: 100%; height: auto" value="Add More Accompanying Person" class="submit"></td>
										</tr>
										<tr>
											<td>Payment Mode <span class="star">*</span></td>
											<td>
												<div class="radio" style="border: 0px solid #2cb8f4;
																				border-radius: 0px;
																				background: transparent;
																				padding: 0px 0px;
																				box-shadow: none;
																			   margin-top: 0;">
														<label class="container-box" style="width: 20%; float: left">One
															<input type="radio" checked="checked" name="radio">
															<span class="checkmark"></span>
														 </label>
														 <label class="container-box" style="width: 20%; float: left">Two
															 <input type="radio" name="radio">
															 <span class="checkmark"></span>
														 </label>
												
												
													</div>
											</td>
										</tr>
										<tr>
											<td></td>
											<td> <input type="submit" style="width: auto; height: auto" value="Next" class="submit"></td>
										</tr>
										
										 
									</tbody>
								</table>
							</div>
							
							
							<div class="menu-center-section col-xs-12" style="display: none;">
								<h5>Accompanying Person Registration Details</h5>
								<table class="table">
									<tbody>
										<tr>
											<td class="border-color">Registered</td>
											<td class="border-color">Date: 12/03/2019 </td>
										</tr>
										<tr>
											<td>Accompanying Person Registration Id</td>
											<td>RCOG19-4261-1727-1728 <a><input type="submit" style="width: auto; height: auto;" value="update Profile" class="submit pull-right"></a></td>
										</tr>
										<tr>
											<td>Accompanying Person Name</td>
											<td>DSCV</td>
										</tr>
										<tr>
											<td colspan="2"><a class="cancel-bttn">Request for workshop change</a></td>
											
										</tr>
										
										 
									</tbody>
								</table>
							</div> 
							
							<div class="menu-center-section col-xs-12" style="display: none;">
								<h5>Accompanying Person INVOICEoice Details</h5>
								<table class="table">
									<tbody>
										<tr>
											<td class="border-color">INVOICEoice</td>
											<td class="border-color">Date: 27/02/2019 </td>
										</tr>
										<tr>
											<td>PV No.</td>
											<td>##SLIP110319-000617 <a class="cancel-bttn">Download Payment Voucher</a></td>
										</tr>
										<tr>
											<td>Accompanying Person Name</td>
											<td>DSCV</td>
										</tr>
										<tr>
											<td>INVOICEoice Amount</td>
											<td>INR 5100.00</td>
										</tr>
										<tr>
											<td>Payment Mode</td>
											<td>Online</td>
										</tr>
										 
									</tbody>
								</table>
							</div>
							
							<div class="menu-center-section col-xs-12" style="display: none;">
								<h5>Workshop Registration Details Of DR SWARNENDU ENCODERS</h5>
								<table class="table">
									<tbody>
										<tr>
											<td class="border-color">Workshop</td>
											<td class="border-color">INVOICEoice Date: 12/03/2019 </td>
										</tr>
										<tr>
											<td>Workshop Name</td>
											<td>Master Class - Urogynaecology  <a class="cancel-bttn">Request for workshop change</a></td>
										</tr>
										<tr>
											<td>Workshop Status</td>
											<td>Registered</td>
										</tr>
										
										 
									</tbody>
								</table>
							</div>
							
						    <div class="menu-center-section col-xs-12" style="display: none;">
								<h5> User Profile Details</h5>
								<table class="table">
									<tbody>
										<tr>
											<td class="border-color">Registered Email Id</td>
											<td class="border-color">neerajdugar03@yahoo.co.in</td>
										</tr>
										<tr>
											<td>Registered Mobile</td>
											<td>+91 9830402092</td>
										</tr>
										<tr>
											<td>Unique Sequence</td>
											<td>#24950555</td>
										</tr>
										<tr>
											<td>Registration Id</td>
											<td>KLOLIC19-4272-0555</td>
										</tr>
										<tr>
											<td>Address</td>
											<td>UJAAS THE CONDOVILLE ,69 S.K DEB ROAD, LAKE TOWN, BLOCK-15,FLAT-301</td>
										</tr>
										<tr>
											<td>Country</td>
											<td>INDIA</td>
										</tr>
										<tr>
											<td>State</td>
											<td>WEST BENGAL</td>
										</tr>
										<tr>
											<td>Pincode</td>
											<td>700048</td>
										</tr>
										 
									</tbody>
								</table>
							</div>
							
							<div class="menu-center-section col-xs-12" style="display: none;">
								<h5>Update User Details</h5>
								 <form action="#" id="" name="">
									 <table class="table">
										<tbody>
											<tr>
												<td class="border-color">Registered Email Id</td>
												<td class="border-color">wdcdc@gmail.com</td>
											</tr>
											<tr>
												<td>Registered Mobile</td>
												<td>+91 9876543210</td>
											</tr>
											<tr>
												<td>Unique Sequence</td>
												<td>#16960290</td>
											</tr>
										   
											 <tr>
												<td>Registration Id</td>
												<td>KLOLIC19-9651-0290</td>
											</tr>
											<tr>
												<td>Address <span class="star">*</span></td>
												<td><textarea style="width: 100%; height: 30px"></textarea></td>
											</tr>
											<tr>
												<td>Country <span class="star">*</span></td>
												<td >
													<select style="width: 100%; height: 30px">
														<option value="0">Dr.</option>
													</select>
												</td>
											</tr>
											<tr>
												<td>State <span class="star">*</span></td>
												<td >
													<select style="width: 100%; height: 30px">
														<option value="0">Dr.</option>
													</select>
												</td>
											</tr>
											<tr>
												<td>City <span class="star">*</span></td>
												<td><input type="text" name=""></td>
											</tr>
											 <tr>
												<td>Postal Code <span class="star">*</span></td>
												<td><input type="number" name=""></td>
											</tr>
											<tr>
												<td>Food Preference <span class="star">*</span></td>
												<td >
													 <div class="radio" style="border: 0px solid #2cb8f4;
																				border-radius: 0px;
																				background: transparent;
																				padding: 0px 0px;
																				box-shadow: none;
																			   margin-top: 0;">
														<label class="container-box" style="width: 20%; float: left">One
															<input type="radio" checked="checked" name="radio">
															<span class="checkmark"></span>
														 </label>
														 <label class="container-box" style="width: 20%; float: left">Two
															 <input type="radio" name="radio">
															 <span class="checkmark"></span>
														 </label>
												
												
													</div>
												</td>
											</tr>
											<tr>
												<td> </td>
												<td >
													<input type="submit" style="width: auto; height: auto" value="update Profile" class="submit"> 
													
												</td>
											</tr>
	
	
										</tbody>
									 </table>
									</form>
						
								</div>
											
					
							<div class="menu-center-section col-xs-12" style="display: none;">
								<h5>Workshop Registration Details Of DR SWARNENDU ENCODERS</h5>
								<table class="table">
									<tbody>
										<tr>
											<td class="border-color">SL</td>
											<td class="border-color">Submission Code</td>
											<td class="border-color">Title</td>
											<td class="border-color">Topic</td>
											<td class="border-color">Status</td>
											<td class="border-color">Action</td>
										</tr>
										<tr>
											<td>1</td>
											<td>00980087</td>
											<td>ASCASCSACASCASCASCASC</td>
											<td>BASIC SCIENCE RESEARCH</td>
											<td>Active</td>
											<td>icon</td>
										</tr>
										
										
										 
									</tbody>
								</table>
							</div>
							
							<div class="menu-center-section col-xs-12" style="display: none;">
								<h5>Update User Details</h5>
								<form>
									<table class="table">
										<tbody>
											<tr><td colspan="2" class="sub-head border-color">Submission Details</td></tr>
											<tr>
												<td>Submission Code:</td>
												<td>44370062</td>
											</tr>
											<tr>
												<td>Submission Code:</td>
												<td>44370062</td>
											</tr>
											<tr>
												<td colspan="2" class="sub-head border-color">Author & Presenter</td>
											</tr>
											<tr>
												<td>Name</td>
												<td>DR M ENCODERS</td>
											</tr>
											<tr>
												<td>Mobile No</td>
												<td>+91 9051437550</td>
											</tr>
											<tr>
												<td>Email-id</td>
												<td>monalisa.m@encoders.co.in</td>
											</tr>
											<tr>
												<td>Country</td>
												<td>INDIA</td>
											</tr>
											<tr>
												<td>City</td>
												<td>UTTARPARA</td>
											</tr>
											<tr>
												<td>Institute Name	</td>
												<td><input type="text"></td>
											</tr>
											<tr>
												<td>Institute Name	</td>
												<td><input type="text"></td>
											</tr>
											<tr>
												<td colspan="2" class="sub-head border-color">Author & Presenter</td>
											</tr>
											<tr>
												<td>Name</td>
												<td>DR M ENCODERS</td>
											</tr>
											<tr>
												<td>Mobile No</td>
												<td>+91 9051437550</td>
											</tr>
											<tr>
												<td>Email-id</td>
												<td>monalisa.m@encoders.co.in</td>
											</tr>
											<tr>
												<td>Country</td>
												<td>INDIA</td>
											</tr>
											<tr>
												<td>City</td>
												<td>UTTARPARA</td>
											</tr>
											<tr>
												<td>Institute Name	</td>
												<td><input type="text"></td>
											</tr>
											<tr>
												<td>Institute Name	</td>
												<td><input type="text"></td>
											</tr>
										</tbody>
									</table>
								</form>
							</div>
						</div>						
						
						
					</div>
					<?php
					leftCommonMenu("Profile");
					?>	
				</div>
			</div>
		</div>
		<script>
		$(document).ready(function(){
			$('form[use=query_form]').on('submit',(function(e) {
				e.preventDefault();
				
				var obj = $(this);
				
				var dataValue = $(this).serialize();
				console.log(dataValue);	
				$.ajax({
					type : 'POST',
					data: dataValue,
					url : "profile_update.process.php",
					success: function(data){
						console.log(data);
						try
						{						
							var datavalue = JSON.parse(data);
						}	
						catch(e){}
												
						alert("Your Message is sent.");
						
						$(obj).find("textarea").val('');
					}
				}).fail(function() {
					alert("Something wrong!! Please try again later.");
				});
			}));
		});
		</script>
	</body>
</html>
<?
function profileSummary($delegateId, $rowUserDetails, $invoiceList, $currentCutoffId)
{
	global $mycms, $cfg, $operate;

	if(!empty($mycms->getSession('PAYMENT_PROCESS_FAILED')))
	{ 
	?>
		<script type="text/javascript">
			alert(<?=$mycms->getSession('PAYMENT_PROCESS_FAILED')?>)
		</script>
	<?
	}
	$mycms->removeSession('PAYMENT_PROCESS_FAILED');	
	$conferenceInvoiceDetails 	= reset($invoiceList[$delegateId]['REGISTRATION']);
	$registrationInvoiceAmount	= $conferenceInvoiceDetails['INVOICE']['currency'].' '.$conferenceInvoiceDetails['INVOICE']['service_roundoff_price'];	

	//echo '<pre>'; print_r($conferenceInvoiceDetails);

	
	$workshopDetails 			= $invoiceList[$delegateId]['WORKSHOP'];
	
	$accompanyDtlsArr 			= $invoiceList[$delegateId]['ACCOMPANY'];
	
	$delgDinner					= getDinnerDetailsOfDelegate($delegateId);
	$dinnerDtls					= array();
	if($delgDinner && !empty($delgDinner))
	{
		$dinnerDtls[$delegateId] 				= $delgDinner;
		$dinnerDtls[$delegateId]['INVOICE'] 	= getInvoiceDetails($delgDinner['refference_invoice_id']);
		$dinnerDtls[$delegateId]['USER'] 		= $rowUserDetails;
	}
	$dinnerDtlsAccm					= array();
	foreach($accompanyDtlsArr as $key=>$accompanyFullDtls)
	{
		$accomDtlsForDinnr 		  							= $accompanyFullDtls['ROW_DETAIL'];
		$accompDinnrDet										= getDinnerDetailsOfDelegate($accomDtlsForDinnr['id']);
		
		if(!empty($accompDinnrDet))
		{				
			$dinnerDtlsAccm[$accomDtlsForDinnr['id']] 				= $accompDinnrDet;
			$dinnerDtlsAccm[$accomDtlsForDinnr['id']]['INVOICE'] 	= getInvoiceDetails($accompDinnrDet['refference_invoice_id']);
			$dinnerDtlsAccm[$accomDtlsForDinnr['id']]['USER'] 		= $accomDtlsForDinnr;
			
			//$dinnerDtls[$accomDtlsForDinnr['id']] 					= $accompDinnrDet;
			//$dinnerDtls[$accomDtlsForDinnr['id']]['INVOICE'] 		= getInvoiceDetails($accompDinnrDet['refference_invoice_id']);
			//$dinnerDtls[$accomDtlsForDinnr['id']]['USER'] 			= $accomDtlsForDinnr;
		}
	}
	

	/* Abstract submission details start */

	//$abstract_details = delegateAbstractDetails($delegateId);
	//$abstract_details = delegateAbstractDetailsSummery($delegateId);

	$abstract_details = delegateAbstractDetailsSummeryWithoutTopic($delegateId);
	
	/* Abstract submission details end */
//echo '<pre>'; print_r($conferenceInvoiceDetails);
?>
<script>
	$(document).ready(function(){
		setInterval( function(){ setTheClock(); }, 1000 );//triggerTopBlinker();
		countdown(year,month,day,hour,minute);
	});
	
	function setTheClock()
	{
		var today=new Date();
		var todayy=today.getYear();
		if (todayy < 1000) { 
			todayy+=1900; 
		}
		var todaym=today.getMonth();
		var todayd=today.getDate();
		var todayh=today.getHours();
		var todaymin=today.getMinutes();
		var todaysec=today.getSeconds();
		
		var theClock = $("td[use=theClock]");
		$(theClock).find("span[use=day]").text( (todayd<10)?('0'+todayd):todayd );
		$(theClock).find("span[use=month]").text( (todaym<10)?('0'+todaym):todaym );
		$(theClock).find("span[use=year]").text( (todayy<10)?('0'+todayy):todayy );
		$(theClock).find("span[use=hour]").text( (todayh<10)?('0'+todayh):todayh );
		$(theClock).find("span[use=min]").text( (todaymin<10)?('0'+todaymin):todaymin );
		$(theClock).find("span[use=sec]").text( (todaysec<10)?('0'+todaysec):todaysec );
	}
	
	//  Change the items below to create your countdown target date and announcement once the target date and time are reached.  
	var current="";     			 //enter what you want the script to display when the target date and time are reached, limit to 20 characters
	var year= 2019;      			 //Enter the count down target date YEAR
	var month= 9;     				 //Enter the count down target date MONTH
	var day= 5;       				 //Enter the count down target date DAY
	var hour=23;          			 //Enter the count down target date HOUR (24 hour clock)
	var minute=59;        			 //Enter the count down target date MINUTE
	var tz=5.5;            			 //Offset for your timezone in hours from UTC (see http://wwp.greenwichmeantime.com/index.htm to find the timezone offset for your location)
	
	// DO NOT CHANGE THE CODE BELOW! 
	var montharray=new Array("Jan","Feb","Mar","Apr","May","Jun","Jul","Aug","Sep","Oct","Nov","Dec");	
	function countdown(yr,m,d,hr,min){
		theyear=yr;themonth=m;theday=d;thehour=hr;theminute=min;
		var today=new Date();
		var todayy=today.getYear();
		if (todayy < 1000) { 
			todayy+=1900; 
		}
		var todaym=today.getMonth();
		var todayd=today.getDate();
		var todayh=today.getHours();
		var todaymin=today.getMinutes();
		var todaysec=today.getSeconds();
		var todaystring1=montharray[todaym]+" "+todayd+", "+todayy+" "+todayh+":"+todaymin+":"+todaysec;
		var todaystring=Date.parse(todaystring1)+(tz*1000*60*60);
		var futurestring1=(montharray[m-1]+" "+d+", "+yr+" "+hr+":"+min);
		var futurestring=Date.parse(futurestring1)-(today.getTimezoneOffset()*(1000*60));
		var dd=futurestring-todaystring;
		var dday=Math.floor(dd/(60*60*1000*24)*1);
		var dhour=Math.floor((dd%(60*60*1000*24))/(60*60*1000)*1);
		var dmin=Math.floor(((dd%(60*60*1000*24))%(60*60*1000))/(60*1000)*1);
		var dsec=Math.floor((((dd%(60*60*1000*24))%(60*60*1000))%(60*1000))/1000*1);
		
		var theClock = $("td[use=theCountDown]");
		
		if(dday<=0&&dhour<=0&&dmin<=0&&dsec<=0){
			
			$(theClock).find("span[use=day]").text('00');
			$(theClock).find("span[use=hour]").text('00');
			$(theClock).find("span[use=min]").text('00');
			$(theClock).find("span[use=sec]").text('00');
			return;
		}
		else 
		{
			$(theClock).find("span[use=day]").text((dday<10)?('0'+dday):dday);
			$(theClock).find("span[use=hour]").text((dhour<10)?('0'+dhour):dhour);
			$(theClock).find("span[use=min]").text((dmin<10)?('0'+dmin):dmin);
			$(theClock).find("span[use=sec]").text((dsec<10)?('0'+dsec):dsec);
			setTimeout("countdown(theyear,themonth,theday,thehour,theminute)",1000);
		}
	}	
	
	function openProfileDataBlock(obj,what)
	{
		//$("div[operationMode=bannerDisplay]").hide();
		$("div[operationMode=profileData]").hide();
		$("div[operationMode=profileData][operationData='"+what+"']").slideDown();
	}
	
	function returnToSummaryBlock(obj,what='')
	{
		var parent = $(obj).parent().closest("div[operationMode=profileData]");
		
		$(parent).hide();
		//$("div[operationMode=profileData][operationData=registrationSummary]").slideDown();

		
		if(what!='' && what == 'workshopAdd'){
			$("div[operationMode=profileData][operationData=workshopAdd]").slideDown();	
			$('#frmAddWorkshopfromProfile table').show();
			$("div[use=registrationPayment]").hide();
			$('button[sequenceNo=1]').parent().show();
			$('h5[sequenceNo=1]').show();
			$("div[use=complimentrayWorkshop]").hide();

			var novemberWorkshop = $("input[type=checkbox][operationMode=workshopId_nov]:checked").length;
			var decemberWorkshop = $("input[type=checkbox][operationMode=workshopId]:checked").length;
            if(novemberWorkshop > 0 || decemberWorkshop > 0){
				$('button[sequenceno=1]').parent().show();
			}else{
				$('button[sequenceno=1]').parent().hide();
			}

			//$("input[type=checkbox][operationMode=workshopId]").prop("checked",false); 
			//$("input[type=checkbox][operationMode=workshopId]").attr("chkStatus","false");
		}else{
			$("div[operationMode=profileData][operationData=registrationSummary]").slideDown();	
		}
		
		
	}
    
    function returnToSummaryBlockBanquet(obj,what='')
	{
		var parent = $(obj).parent().closest("div[operationMode=profileData]");
		
		$(parent).hide();
		//$("div[operationMode=profileData][operationData=registrationSummary]").slideDown();

		
		if(what!='' && what == 'workshopAdd'){
			$("div[operationMode=profileData][operationData=workshopAdd]").slideDown();	
			$('#frmAddWorkshopfromProfile table').show();
			$("div[use=registrationPayment]").hide();
			$('button[sequenceNo=1]').parent().show();
			$('h5[sequenceNo=1]').show();
			$("div[use=complimentrayWorkshop]").hide();

			var novemberWorkshop = $("input[type=checkbox][operationMode=workshopId_nov]:checked").length;
			var decemberWorkshop = $("input[type=checkbox][operationMode=workshopId]:checked").length;
            if(novemberWorkshop > 0 || decemberWorkshop > 0){
				$('button[sequenceno=1]').parent().show();
			}else{
				$('button[sequenceno=1]').parent().hide();
			}

			//$("input[type=checkbox][operationMode=workshopId]").prop("checked",false); 
			//$("input[type=checkbox][operationMode=workshopId]").attr("chkStatus","false");
		}else{
			$("div[operationMode=profileData][operationData=registrationSummary]").slideDown();	
		}
	}
	
	function returnToSummaryBlockDinner(obj,what='')
	{
		var parent = $(obj).parent().closest("div[operationMode=profileData]");
		
		$(parent).hide();
		//$("div[operationMode=profileData][operationData=registrationSummary]").slideDown();

		
		if(what!='' && what == 'dinnerAdd'){
			$("div[operationMode=profileData][operationData=dinnerAdd]").slideDown();	
			$('#frmAddDinnerfromProfile table').show();
			$("div[use=registrationPayment]").hide();
			$('button[sequenceNos=11]').parent().show();
			$('h5[sequenceNos=11]').show();
			$("div[use=complimentrayWorkshop]").hide();

			var novemberWorkshop = $("input[type=checkbox][operationMode=dinnerId_nov]:checked").length;
			var decemberWorkshop = $("input[type=checkbox][operationMode=dinnerId]:checked").length;
            if(novemberWorkshop > 0 || decemberWorkshop > 0){
				$('button[sequencenos=11]').parent().show();
			}else{
				$('button[sequencenos=11]').parent().hide();
			}

			//$("input[type=checkbox][operationMode=workshopId]").prop("checked",false); 
			//$("input[type=checkbox][operationMode=workshopId]").attr("chkStatus","false");
		}else{
			$("div[operationMode=profileData][operationData=registrationSummary]").slideDown();	
		}
		
		
	}
	
	function triggerTopBlinker()
	{
		var color = ['#005e82','#2cb8f4'];
		var bgcolor = ['#2cb8f4','#005e82'];
		var today=new Date();
		var todaysec=today.getSeconds();
		
		$.each($("div[use=BlinkerDiv]"),function(){
			var indx =  todaysec%2;
			$(this).css('color',color[indx]);
			$(this).css('background',bgcolor[indx]);
		});
	}
</script>
<div class="col-xs-12 das-bord-section" style="display: block; padding: 0; margin-top: 8px;" operationMode="profileData" operationData="registrationSummary">
	<? 
		// check if the login user has already submitted abstract
		$sql  			  = array();
		$sql['QUERY']     = " SELECT * 
								FROM "._DB_ABSTRACT_REQUEST_." 
							   WHERE `status` = ?
								 AND `applicant_id` = ?";
								
		$sql['PARAM'][]   = array('FILD' => 'status',         'DATA' =>'A',          'TYP' => 's');
		$sql['PARAM'][]   = array('FILD' => 'applicant_id',   'DATA' =>$delegateId, 'TYP' => 's');
		$resultAbstractType = $mycms->sql_select($sql);
		
		if(!$resultAbstractType && $operate) {  
	?>	
	<div class="col-xs-12" style="margin:8px 0 10px 0; padding:10px 5px !important;  font-size:18px; color:#fff; background:#005e82;" use='BlinkerDiv'>
		Abstract / Case Study Submission ends on <?=$mycms->cDate('d/m/Y', $cfg['CASEREPORT.SUBMIT.LASTDATE'])?>.... <a href="<?=_BASE_URL_?>abstract.user.entrypoint.php" style="float:right; color:#FFFFFF;">CLICK HERE TO SUBMIT</a> 
		<!-- Submission Opening Soon... -->
	</div>
	<? } ?>
	<div class="col-xs-12" style="padding-left:0px !important;  padding-right:0px !important;">
		<table class="table">
			<tr style="display:none;">
				<td class="date cn-date">
					<table class="table" style="background: transparent; margin-bottom: 0;">
						<tr>
							<td class="reg_date" colspan="2" 
								style="font-size: 20px; padding: 13px; text-align: center; border: 0; vertical-align: middle">
								CONFERENCE DATE<br><?=$cfg['EMAIL_CONF_HELD_FROM']?>
							</td>
						</tr>
						<tr>
							<td class="today" style="font-size: 16px; padding: 13px; border: 0; vertical-align: middle" use="theClock">
								TODAY<br>
								<span class="today_date">
									<span use="day">00</span>/<span use="month">00</span>/<span use="year">0000</span>
								</span>
								<br>
								<span class="today_time">
									<span use="hour">00</span>:<span use="min">00</span>:<span use="sec">00</span>
								</span>
							</td>
							<td class="temp" style="font-size: 16px; padding: 13px; text-align: right; border: 0; vertical-align: middle" use="theCountDown">
								<span class="today_date">
									<span use="day">00</span> Days
								</span>
								<br>
								<span class="today_time">
									<span use="hour">00</span> Hrs <span use="min">00</span> Mins <span use="sec">00</span> Secs
								</span>
								<br/> LEFT
							</td>
						</tr>
					</table>
				</td>
			</tr>
			<tr>
				<td  style="border: 0; vertical-align: middle; padding: 0 0 8px 0;">
					<table class="tableCard" style="margin-bottom: 0">
						<tr>
							<td rowspan="<?=intval(sizeof($dinnerDtls))+2?>" style="font-size:42px; text-align:center; vertical-align: top; padding: 10px 10px;" width="10%">
								<i class="fas fa-file-signature" style="color:#005e82;"></i>
							</td>
							<td colspan="2" class="reg_cat" style="border: 0; padding: 10px 5px; font-size: 18px; font-weight: bold">
								REGISTRATION
								<!-- <i class="fas fa-info-circle pull-right" style="cursor:pointer;" onClick="openProfileDataBlock(this,'registrationDetails');"></i> -->
							</td>
						</tr>
						<tr style="vertical-align: initial;">
							<td class="reg_name" style="border: 0; padding: 0px 5px 10px 5px; font-size: 16px">
								<?=$invoiceList[$delegateId]['REGISTRATION'][$delegateId]['REG_DETAIL']?>
							</td>
							<!-- <td class="left-btn">
								<a href="pdf.download.invoice.php?user_id=<?=$delegateId?>&invoice_id=<?=$conferenceInvoiceDetails['INVOICE']['id']?>" target="_blank" style="background: #005e82; border-radius: 20px; padding: 5px 15px; margin-right: 10px; color: #ffffff; ">INVOICE</a>
							</td> -->
							<td class="left-btn">
								<?php
								 if(!empty($conferenceInvoiceDetails['INVOICE']['id']) && $conferenceInvoiceDetails['INVOICE']['id']>0)
								 {
								?>
								<a href="pdf.download.invoice.php?user_id=<?=$delegateId?>&invoice_id=<?=$conferenceInvoiceDetails['INVOICE']['id']?>" target="_blank" style="background: #005e82; border-radius: 20px; padding: 5px 15px; margin-right: 10px; color: #ffffff; ">INVOICE</a>
								<?php
								}
								else
								{

									?>
									 <a href="<?=_BASE_URL_?>registration.tariff.php?abstractDelegateId=<?=$delegateId?>" target="_blank" style="background: #005e82; border-radius: 20px; padding: 5px 15px; margin-right: 10px; color: #ffffff; ">REGISTER NOW</a>
									<?php
								}
								?>
							</td>
						</tr>
						<?
						if(sizeof($dinnerDtls)>0)
						{

						?>
						<tr>
							<td class="reg_name" style="border: 0; padding: 0px 5px 10px 5px; font-size: 16px">
								GALA-DINNER 
							</td>
						</tr>
						<?
						}
						?>
						<?php /*?>
						<tr>
							<td class="reg_price" colspan="2" style="border: 0; padding: 2px 0 13px 13px; font-size: 18px">
								<span style="font-style: italic">
								<?
								if($conferenceInvoiceDetails['INVOICE']['payment_status'] !='COMPLIMENTARY' && $conferenceInvoiceDetails['INVOICE']['payment_status'] !='ZERO_VALUE')
								{
									echo $registrationInvoiceAmount;
								}
								else if($conferenceInvoiceDetails['INVOICE']['payment_status']=='COMPLIMENTARY')
								{
									echo 'COMPLIMENTARY';
								}
								else if($conferenceInvoiceDetails['INVOICE']['payment_status']=='ZERO_VALUE')
								{
									echo 'ZERO VALUE';
								}
								?>
								</span>
							</td>
						</tr>
						<tr>
							<td class="reg_print" colspan="2" style="border: 0; padding: 13px;">
								<?
								if($conferenceInvoiceDetails['INVOICE']['payment_status']!='COMPLIMENTARY' && $conferenceInvoiceDetails['INVOICE']['payment_status']!='UNPAID' && $conferenceInvoiceDetails['INVOICE']['payment_status']!='ZERO_VALUE')
								{
								?>
								<a style="background: white; border-radius: 20px; padding: 5px 15px; margin-right: 10px;" href="pdf.download.invoice.php?user_id=<?=$delegateId?>&invoice_id=<?=$conferenceInvoiceDetails['INVOICE']['id']?>" >INVOICE</a>
								<?
								}
								?>
								<!--<a>CANCEL</a>-->
							</td>
						</tr>
						<?php */?>
					</table>
				</td>
			</tr>
			<?
			if($workshopDetails)
			{
				$wrksp_Cnt = 0;
			?>
			<tr>
				<td style="border: 0; vertical-align: bottom; padding: 8px 0;">
					<table class="tableCard" style="margin-bottom: 0">
						<tr>
							<td rowspan="4" style="font-size:42px; text-align:center; vertical-align: top; padding: 10px 10px;" width="10%">
								<img src="<?=_BASE_URL_?>images/training-128.png" style="width: 100%;">
							</td>
							<td colspan="4" class="reg_cat" style="border: 0; padding: 10px 5px 10px 5px; font-size: 18px; font-weight: bold">
								WORKSHOPS
								<!-- <i class="fas fa-info-circle pull-right" style="cursor:pointer;" onClick="openProfileDataBlock(this,'workshopDetails');"></i> -->
							</td>
						</tr>
			<?
				
				$existingWorkShops = array();
				foreach($workshopDetails as $key => $rowWorkshopDetails	)
				{
					
					if($rowWorkshopDetails && $rowWorkshopDetails['INVOICE']['status']=='A')
					{
						
						$existingWorkShops[] = $rowWorkshopDetails['ROW_DETAIL']['type']; 
						$workshopStatus = true;	
						$wrksp_Cnt++;	
						
						if( $rowWorkshopDetails['INVOICE']['service_roundoff_price'] > 0)
						{
							$workshopAmountDisp = $rowWorkshopDetails['INVOICE']['currency'].' '. $rowWorkshopDetails['INVOICE']['service_roundoff_price'];
						}	
						else
						{
							$workshopAmountDisp = 'Included in package';
						}	

						
						if(!empty($rowWorkshopDetails['ROW_DETAIL']['workshop_date']))
						{
							$workshop_date = '('.$rowWorkshopDetails['ROW_DETAIL']['workshop_date'].')';
						}
						else
						{
							$workshop_date = '';
						}
						
			?>
						<? 
						/* <tr><td class="workshop_cat_name" colspan="2" style="border: 0; padding: 0px 5px 10px 5px;"><?=$rowWorkshopDetails['REG_DETAIL']?></td></tr> */
						if(!empty($rowWorkshopDetails['ROW_DETAIL']))
						{
						?>
						<tr>
							<td class="workshop_cat_name" colspan="3" style="border: 0; padding: 0px 5px 10px 5px;"><?=$rowWorkshopDetails['REG_DETAIL']?> <?=$workshop_date?></td>
							<? /* <td class="workshop_cat_price"  style="border: 0; padding: 13px" width="45%">
								<span style="font-style: italic" ><?=$workshopAmountDisp?></span>
							</td> */ ?>
							<td class="workshop_cat_print left-btn" style="border: 0; padding: 13px 0px;text-align: right">
							<?
								if($rowWorkshopDetails['INVOICE']['service_roundoff_price']>0)
								{
								
							?>
								<a href="pdf.download.invoice.php?user_id=<?=$delegateId?>&invoice_id=<?=$rowWorkshopDetails['INVOICE']['id']?>" target="_blank" style="background: #005e82; border-radius: 20px; padding: 5px 15px; margin-right: 5px; color: #ffffff; ">INVOICE</a>
								<?php
                                         if($rowWorkshopDetails['INVOICE']['payment_status']=='UNPAID' && $rowWorkshopDetails['INVOICE']['invoice_mode']=='ONLINE')
                                         {
                                        ?>
                                            <a href="payment.retry.php?slip_id=<?=$rowWorkshopDetails['INVOICE']['slip_id']?>" target="_blank" style="background: #00825e; border-radius: 20px;  margin-right: 10px; color: #ffffff; display: inline-block; margin-top: 6px;">PAY NOW</a>
                                       
                                        <?php
                                        
                                        }
							
								}
							?>
							</td>
						</tr>
						<?php
						}
						 /*?>
						<tr>
							<td class="workshop_cat_price"  style="border: 0; padding: 13px" width="45%">
								<span style="font-style: italic" ><?=$workshopAmountDisp?></span>
							</td>
							<td class="workshop_cat_print" style="border: 0; padding: 13px;text-align: right">
							<?
								if($rowWorkshopDetails['INVOICE']['service_roundoff_price']>0)
								{
							?>
								<a href="pdf.download.invoice.php?user_id=<?=$delegateId?>&invoice_id=<?=$rowWorkshopDetails['INVOICE']['id']?>" style="background: white; border-radius: 20px; padding: 5px 15px; margin-right: 10px; ">INVOICE</a>
							<?
								}
							?>
								<!--<a>CANCEL</a>-->
							</td>
						</tr>
						<tr ><td style="border: 0; padding: 0" colspan="2"><hr style="margin: 0; color: white"></td></tr>
						<?php */?>
			<?
					}
				}

				// check if user has registered to all type of workshops (MASTER CLASS & WORKSHOP)
				$condition = '';
				if(count($existingWorkShops) > 0)
				{
				    $condition .= 'AND type NOT IN("' . implode('", "', $existingWorkShops) . '")';
				}

				$sqlWorkshop = array();
				$sqlWorkshop['QUERY'] = "SELECT count(*) as remainingWorkshop
				                            FROM  "._DB_WORKSHOP_CLASSIFICATION_." 
				                            WHERE `status` = ? ".$condition;
				$sqlWorkshop['PARAM'][]   = array('FILD' => 'status',  'DATA' => 'A', 'TYP' => 's');

				$resWorkshop = $mycms->sql_select($sqlWorkshop,false);

				if($resWorkshop[0]['remainingWorkshop'] != 0)
				{
			?>
					<!-- <tr>
						<td></td>
						<td style="text-align:center; padding: 5px;">
							<h5 class="smallhed addCategory" style="text-align: center;padding: 5px; color: #ffffff; cursor:pointer; background-color: #005e82;     width: fit-content;" use="addCategory" linkId="dinner_add" onclick="addWorkShopFromProfile(this,'workshopAdd')">APPLY FOR ANOTHER WORKSHOP</h5>
						</td>
					</tr> -->
				<?
				}
				?>
					</table>
				</td>
			</tr>
			<?
			}
			// workshop related from profile work by weavers start
			//-----------close workshop--------------//
			else{

				 $workshopDetailsArray 	 = getAllWorkshopTariffs($currentCutoffId);
				//echo count($workshopDetailsArray);
				if(count($workshopDetailsArray)>0)
				{
			?>
				 <tr>
					<td style="border: 0; vertical-align: bottom; padding: 8px 0;">
						<table class="tableCard" style="margin-bottom: 0">
							<tr>
								<td style="font-size:42px; text-align:center; vertical-align: top; padding: 10px 10px;" width="10%">
									<img src="<?=_BASE_URL_?>images/training-128.png" style="width: 100%;">
								</td>
								<td class="reg_cat" style="border: 0; padding: 10px 5px 10px 5px; font-size: 18px; font-weight: bold">
									WORKSHOPS
								</td>
							</tr>
							<tr>
								<td></td>
								<td><span style="font-size:large;">You haven't applied for workshop</span>	</td>
							</tr>
							<tr>
								<td></td>
								<td>
									<h5 class="smallhed addCategory" style="text-align: center;padding: 5px; color: #ffffff; cursor:pointer; background-color: #005e82; width: fit-content;" use="addCategory" linkId="wrokshop_add" onclick="addWorkShopFromProfile(this,'workshopAdd')">APPLY FOR WORKSHOP</h5>
								</td>
							</tr>
						</table>
					</td>
											
				</tr> 
			<? 
				}
			}
			// workshop related from profile work by weavers end
			if(sizeof($dinnerDtls)>0)
			{
			?>	
			<tr>
				 <td class="gala_dinner" style="border: 0; vertical-align: middle; padding: 0">
					<table class="table" style=" margin-bottom: 0">
			<?
				$dinrCount = 0;
				foreach($dinnerDtls as $uId=>$dinnerDetails)
				{
					$dinrCount++;
					if($dinrCount%2 == 1)
					{
						$dinrClass 	= "gala_dinner_accompany";
						$dinrBG 	= "#2393c3";
						$dinrInvBG 	= "#27a5db";
					}
					else
					{
						$dinrClass 	= "gala_dinner_workshop";
						$dinrBG 	= "#27a5db";
						$dinrInvBG 	= "#2393c3";
					}
					
					$dinrInvAmtDisp = 'Included in Package';
					$hasInvoice		= false;
					if($dinnerDetails['INVOICE']['service_type'] == 'DELEGATE_DINNER_REQUEST')
					{
						$dinrInvAmtDisp = $dinnerDetails['INVOICE']['currency'].' '.$dinnerDetails['INVOICE']['service_roundoff_price'];
						$hasInvoice 	= true;
					}
					
					
			?>
						<tr>
							<td class="<?=$dinrClass?>" colspan="2" style="background: <?=$dinrBG?>; border: 0">
								<table class="table" style="background: transparent; margin-bottom: 0">
									<tr>
										<td class="gala_dinner_accompany_name" colspan="2" style="border: 0 ; font-size: 18px; color: white; font-weight: bold; padding: 13px 0 0 13px">
											BANQUET-DINNER 
											<!-- <i class="fas fa-info-circle pull-right" style="cursor:pointer;" onClick="openProfileDataBlock(this,'dinnerDetails');"></i> -->
										</td>
									</tr>
									<tr>
										<td class="gala_dinner_accompany_name" colspan="2" style="border: 0 ; font-size: 18px; color: white; font-weight: bold; padding: 13px 0 0 13px">
											<span style="font-weight:normal;"><?=$dinnerDetails['USER']['user_full_name']?></span>
										</td>
									</tr>
									<tr>
										<td class="gala_dinner_accompany_price" colspan="2"  style="border: 0 ; font-size: 16px; color: white; padding: 0px 0 13px 13px">
											<span style="font-style: italic"><?=$dinrInvAmtDisp?></span>
											
										</td>
									</tr>
									<tr>
										<td class="gala_dinner_accompany_print"  style="border: 0">
										<?
										if($hasInvoice)
										{
										?>
											<a href="pdf.download.invoice.php?user_id=<?=$delegateId?>&invoice_id=<?=$dinnerDetails['INVOICE']['id']?>" style="background: <?=$dinrInvBG?>; border-radius: 20px; padding: 5px 15px; margin-right: 10px; color white ">INVOICE</a>
										<?
										}
										if($dinnerDetails['INVOICE']['payment_status']=='UNPAID' && $dinnerDetails['INVOICE']['invoice_mode']=='ONLINE')
										{
											?>
												<a href="payment.retry.php?slip_id=<?=$dinnerDetails['INVOICE']['slip_id']?>" style="background: <?=$dinrInvBG?>; border-radius: 20px; padding: 5px 15px; margin-right: 10px; color white ">PAY NOW</a>
											<?php
										}
										?>
										
											<!--a style="color: white">CANCEL</a-->
										</td>
									</tr>
								</table>
							</td>
						</tr>
						<tr><td style="border: 0; padding: 1px"></td></tr>
			<?
				}
			?>	
					</table>
				</td>
			</tr>
			<?
			}
			else
			{

				$dinnerTariffArray   = getAllDinnerTarrifDetails($currentCutoffId);
				//print_r($dinnerTariffArray);
				if(count($dinnerTariffArray)>0)
				{
				?>
				  <tr>
					<td style="border: 0; vertical-align: bottom; padding: 8px 0;">
						<table class="tableCard" style="margin-bottom: 0">
							<tr>
								<td style="font-size:42px; text-align:center; vertical-align: top; padding: 10px 10px;" width="10%">
									<img src="<?=_BASE_URL_?>images/training-128.png" style="width: 100%;">
								</td>
								<td class="reg_cat" style="border: 0; padding: 10px 5px 10px 5px; font-size: 18px; font-weight: bold">
									BANQUET DINNER
								</td>
							</tr>
							<tr>
								<td></td>
								<td><span style="font-size:large;">You haven’t applied for dinner</span>	</td>
							</tr>
							<tr>
								<td></td>
								<td>
									<h5 class="smallhed addCategory" style="text-align: center;padding: 5px; color: #ffffff; cursor:pointer; background-color: #005e82; width: fit-content;" use="addCategory" linkId="dinner_add" onclick="addDinnerFromProfile(this,'dinnerAdd')">APPLY FOR BANQUET DINNER</h5>
								</td>
							</tr>
						</table>
					</td>
											
				</tr> 
				<?php
				}
			}
			
			//echo '<pre>'; print_r($invoiceList[$delegateId]['ACCOMMODATION']);
			//if(isset($invoiceList[$delegateId]['REGISTRATION'][$delegateId]['COMBO-ACCOMODATION']))
			if(isset($invoiceList[$delegateId]['ACCOMMODATION']))
			{
				//echo '<pre>'; print_r($invoiceList[$delegateId]['ACCOMMODATION']['ROW_DETAIL']);

				global $mycms, $cfg;

				$sqlGetAccommodation   = array();
				 $sqlGetAccommodation['QUERY']	= "SELECT req.id,req.refference_invoice_id,req.user_id,req.accompany_name,req.accommodation_details,req.hotel_id,req.package_id,req.checkin_date,req.checkout_date,req.booking_quantity,R.room_id, req.rooms_no FROM "._DB_REQUEST_ACCOMMODATION_." req INNER JOIN  "._DB_MASTER_ROOM_." R ON req.id = R.request_accommodation_id WHERE req.`user_id` = '".trim($delegateId)."' AND req.`status`='A' AND R.`status`='A' ORDER BY R.room_id ASC, req.created_dateTime ASC";	

				$resultGetAccommodation = $mycms->sql_select($sqlGetAccommodation);	
				$roomsArr = array();
				$countAccoDetils = 0;
				$countAccoDetilsInnr= '';
				 $countAccoDetils+=count($roomsArr);
				foreach ($resultGetAccommodation as $key => $value) {
					$roomsArr[$value['room_id']][] = $value;

					$countAccoDetilsInnr=count($value);
				}
				
				//echo '<pre>'; print_r($roomsArr);	
				//echo $countAccoDetilsInnr;
				//echo $countAccoDetils + $countAccoDetilsInnr;
			?>
			<tr>
				<td  style="border: 0; vertical-align: bottom; padding: 8px 0;">
					<table class="tableCard">
						<tr>
							<td rowspan="<?=intval($countAccoDetilsInnr)+1?>"style="font-size:42px; text-align:center; vertical-align: top; padding: 10px 10px;" width="10%">
								<i class="fas fa-hotel" style="color:#005e82;"></i>
							</td>
							<td colspan="2" style="border: 0; padding: 10px 5px; font-size: 18px; font-weight: bold">
								ACCOMMODATION
								<!--i class="fas fa-info-circle pull-right" style="cursor:pointer;"></i-->
								<!-- <i class="fas fa-info-circle pull-right" style="cursor:pointer;" onClick="openProfileDataBlock(this,'accommodationDetails');"></i> -->
							</td>
						</tr>
						<?
						//echo '<pre>'; print_r($invoiceList[$delegateId]['ACCOMMODATION']);
						 $accommodation_room = '';
						 $count = 0;
						 
						 $hotel_id = '';
						 $distinctArray = [];
						 foreach ($roomsArr as $key => $accommodationRow) {
						 	?>
						 	 <tr><td style="border: 0; padding: 10px 5px; font-size: 18px; font-weight: bold">Room <?=$key?></td></tr>
						 	<?php
						 	
							 	foreach ($accommodationRow as $key => $value) 
							 	{
							 		//print_r($value);
								 		$getAccommodationMaxRoom = getAccommodationMaxRoom($delegateId);

								 		 if(!empty($value['rooms_no']))
											 {
											 	 $accommodations_room = $value['rooms_no'];

											 }
											 else
											 {
											 	$accommodations_room = 1;
											 }

											  $hotel_id =  $value['hotel_id'];
											  $hotel_name = getHotelNameByID($hotel_id);

										     if (!in_array($accommodations_room, $distinctArray)) {
											        $distinctArray[] = $accommodations_room;
											    }

											 $getPackageNameById =  getPackageNameById($value['package_id']);  
											 $total_stays = getDaysBetweenDates($value['checkin_date'],$value['checkout_date']);
											 $total_stay = $getPackageNameById." for ".$total_stays; 

											 ?>
											 <tr>
												<td class="resi_pkg_hotel" style="border: 0; padding: 10px 5px; font-size: 16px; padding-top: 0 ">
													<span style="font-style: italic;font-size: 18px;"><?=(!empty($total_stay)?str_replace("+","",$total_stay).' at ':'').$hotel_name?></span>
													<br>
													<h5>
														<span><sup>*</sup>Check In -</span>
														<span style="color:#e32925;">DATE: <?=$mycms->cDate('d/m/Y',$value['checkin_date'])?></span>
														
														<br>
														<span><sup>*</sup>Check Out -</span>
														<span style="color:#e32925;">DATE: <?=$mycms->cDate('d/m/Y',$value['checkout_date'])?></span>
														 
													</h5>
												</td>
												<td class="left-btn" style="border: 0; text-align: right; vertical-align:middle !important;">
													<a href="pdf.download.invoice.php?user_id=<?=$delegateId?>&invoice_id=<?=$value['refference_invoice_id']?>" target="_blank" style="background: #005e82; border-radius: 20px; padding: 5px 15px; margin-right: 10px; color: #ffffff; display: inline-block; ">INVOICE</a>
												
												</td>

																		
											</tr>
											 <?php
							 		
							 	}
						 }

						

					?>
					<tr><td>
						
					<?php
					
					  foreach ($distinctArray as $key => $value) {
					  	//echo $value;
					  	//echo $hotel_id;
					  	 $minCheckInDateByHotelID = minCheckInDateByHotelID($hotel_id);
						 $maxCheckInDateByHotelID = maxCheckOutDateByHotelID($hotel_id);
						 $getNightValidate = getNightValidate($value, $minCheckInDateByHotelID, $maxCheckInDateByHotelID, $delegateId, $count);
					  }
						if($getNightValidate<3)
						{
							?>
							<h5 class="smallhed addCategory" style="text-align: center;padding: 5px; color: #ffffff; cursor:pointer; background-color: #005e82;width: fit-content;margin: 6px;border-radius: 6px;white-space: nowrap;" use="addCategory" linkId="wrokshop_add" onclick="addAccommodationFromProfile(this,'accommodationAdd')">ADD MORE NIGHTS</h5>
							<?php
						}
						//echo $accommodation_room;
						if($getAccommodationMaxRoom<3)	
						{
						?>		
							

							<h5 class="smallhed addCategory" style="text-align: center;padding: 5px; color: #ffffff; cursor:pointer; background-color: #005e82;width: fit-content;margin: 6px;border-radius: 6px;white-space: nowrap;" use="addCategory" linkId="wrokshop_add" onclick="addAccommodationRoomFromProfile(this,'accommodationAddRoom')">ADD MORE ROOMS</h5>
							
						<?php
						}
						?>	

						</td></tr>
					</table>
				</td>
			</tr>
			<?
			}else{
			?>
			 	
				<tr>
					<td style="border: 0; vertical-align: bottom; padding: 8px 0;"> 
						<table class="tableCard">
							<tr>
								<td style="font-size:42px; text-align:center; vertical-align: top; padding: 10px 10px;" width="10%">
									<i class="fas fa-hotel" style="color:#005e82;"></i>
								</td>
								<td style="border: 0; padding: 10px 5px; font-size: 18px; font-weight: bold">
									ACCOMMODATION
								</td>
							</tr>
							<tr>
								<td></td>
								<td>
									<span style="font-size:large;">You don't have any accommodation</span>
								</td>
							</tr>
							<tr>
								<td></td>
								<td>
									<h5 class="smallhed addCategory" style="text-align: center;padding: 5px; color: #ffffff; cursor:pointer; background-color: #005e82;width: fit-content;" use="addCategory" linkId="wrokshop_add" onclick="addAccommodationFromProfile(this,'accommodationAdd')">ADD ACCOMODATION</h5>
								</td>
							</tr>
						</table>
					</td>						
				</tr>
			<?
			}
			// accommodation related work  by weavers end
			if(sizeof($accompanyDtlsArr)>0)
			{ 
			?>
			<tr>
				<td  style="border: 0; vertical-align: bottom; padding: 8px 0;">
					<table class="tableCard" style="margin-bottom: 0;">
						<tr>
							<!-- <td style="font-size:42px; text-align:center; vertical-align: top; padding: 10px 10px;" width="10%">
								<i class="fas fa-users" style="color:#005e82;"></i>
							</td> -->
							<td  class="reg_cat" style="border: 0; padding: 10px 5px 10px 5px; font-size: 18px; font-weight: bold; width: 100%;">
								<span style="font-size:42px; text-align:center; vertical-align: top; padding: 10px 10px;"><i class="fas fa-users" style="color:#005e82;"></i></span> ACCOMPANY DETAILS 
								<!-- <i class="fas fa-info-circle pull-right" style="cursor:pointer;"></i> -->
								<i class="fas fa-info-circle pull-right" style="cursor:pointer;" onClick="openProfileDataBlock(this,'accompanyDetails');"></i>
							</td>
						</tr>
			<?
				//echo '<pre>'; print_r(expression)
				 $numItems = count($accompanyDtlsArr);
				$i = 0;
				foreach($accompanyDtlsArr as $key=>$accompanyFullDtls)
				{
					//echo $key;
					//echo '<pre>'; print_r($accompanyFullDtls);
					$accompanyDtls 		  = $accompanyFullDtls['ROW_DETAIL'];
					
					$accompanySlipDtls 	  = $accompanyFullDtls['SLIP_DETAILS'];
					$accompanInvoiceDtls  = $accompanyFullDtls['INVOICE'];
					$accompanyPaymentDtls = $accompanyFullDtls['SLIP_PAYMENT'];
					$dataVal 			  = $accompanInvoiceDtls['currency'].' '.$accompanInvoiceDtls['service_roundoff_price'];

					$dinnerDetails	= $dinnerDtlsAccm[$accompanyDtls['id']];
			?>
						<tr>
							
									<?php
									 if(++$i === $numItems ) {

									 ?>
									 <!-- <td colspan="1" style="border: 0; padding:0 "></td>  -->
									 <?php
										}
									 ?>


									<td class="accompany_name"  style="border: 0;  padding: 2px 17px; font-size: 16px; padding-top: 0"><?=strtoupper($accompanyDtls['user_full_name'])?></td>
									<td class="accompany_print left-btn" style="border: 0; padding: 13px 0px;text-align: right">
										<? /* ?><a href="pdf.download.invoice.php?user_id=<?=$delegateId?>&invoice_id=<?=$accompanInvoiceDtls['id']?>" style="background: #2393c3; border-radius: 20px; padding: 5px 15px; margin-right: 10px; color: white ">INVOICE</a> <? */ ?>
										<a href="pdf.download.invoice.php?user_id=<?=$delegateId?>&invoice_id=<?=$accompanInvoiceDtls['id']?>" target="_blank" style="background: #005e82; border-radius: 20px; padding: 5px 15px; margin-right: 10px; color: #ffffff; display: inline-block; ">INVOICE</a>
										<?php
										if($accompanyPaymentDtls['payment_status']=='UNPAID' && $accompanyPaymentDtls['slip_invoice_mode']=='ONLINE')
										 {
										?>
											<a href="payment.retry.php?slip_id=<?=$accompanyPaymentDtls['slip_id']?>" target="_blank" style="background: #00825e; border-radius: 20px;  margin-right: 10px; color: #ffffff; display: inline-block; margin-top: 6px;">PAY NOW</a>
										<!--<a>CANCEL</a>-->
										<?php
										}
										if(empty($dinnerDetails))
										{
										?>

											<!-- <a href="javascript:void(0)" target="_blank" style="background: #005e82; border-radius: 20px; padding: 5px 15px; margin-right: 10px; color: #ffffff; display: inline-block; " onclick="addAccompanyFromProfileBanquet(this,'accompanyAddBanquet')">APPLY BANQUET</a> -->

											<h5 class="smallhed addCategory"
						                    style="text-align: center;padding: 5px; color: #ffffff; cursor:pointer; background-color: #005e82;width: fit-content;border-radius: 20px;"
						                    use="addCategory" linkId="accompany_add" onclick="addAccompanyFromProfileBanquet(this,'accompanyAddBanquet','<?php echo $accompanyDtls['id']; ?>')">APPLY BANQUET</h5>
										<?php
										}
										?>
									</td>
									
				<?
					
										
					if(!empty($dinnerDetails))
					{
						$dinrInvAmtDisp = 'Included in Package';
						$hasInvoice		= false;
						if($dinnerDetails['INVOICE']['service_type'] == 'DELEGATE_DINNER_REQUEST')
						{
							$dinrInvAmtDisp = $dinnerDetails['INVOICE']['currency'].' '.$dinnerDetails['INVOICE']['service_roundoff_price'];
							$hasInvoice 	= true;
						}
				?>
									
									<tr>
										<!-- <td  style="border: 0; padding:0 "></td> -->
										<td class="accompany_price" style="border: 0; padding: 2px 17px">
											BANQUET 
											
										</td>
										<?php ?><td class="accompany_print" style="border: 0; padding: 2px 17px; text-align: right; vertical-align:middle !important;">
											<?
													if($hasInvoice)
													{
											?>	
																		<a href="pdf.download.invoice.php?user_id=<?=$delegateId?>&invoice_id=<?=$dinnerDetails['INVOICE']['id']?>" style="background: #2393c3; border-radius: 20px; padding: 5px 15px; margin-right: 10px; color: white ">INVOICE</a>
																		<!--<a>CANCEL</a>-->
											<?
													}
													if($dinnerDetails['INVOICE']['payment_status']=='UNPAID' && $dinnerDetails['INVOICE']['invoice_mode']=='ONLINE')
													{

											?>
													<a href="payment.retry.php?slip_id=<?=$dinnerDetails['INVOICE']['slip_id']?>" target="_blank" style="background: #00825e; border-radius: 20px;  margin-right: 10px; color: #ffffff; display: inline-block; margin-top: 6px;padding: 7px;">PAY NOW</a>
													<?php
													}
													?>
																	</td>
									</tr>	
				<?
					}
				?>
								<!-- </table> -->
							<!-- </td> -->
						</tr>
						<!--<tr><td style="border: 0; padding: 0" colspan="2"><hr style="margin: 0"></td></tr> -->
			<?
				}
			?>
			 <tr>
            <td style="padding: 2px 17px;"> <h5 class="smallhed addCategory"
                    style="text-align: center;padding: 5px; color: #ffffff; cursor:pointer; background-color: #005e82;width: fit-content;"
                    use="addCategory" linkId="accompany_add" onclick="addAccompanyFromProfile(this,'accompanyAdd')">ADD
                    ACCOMPANY</h5></td>
            <td>
               
            </td>
        </tr>
					</table>
				</td>
			</tr>

			<?
			}
			else
			{
				 $registrationAccompanyAmount 	= getCutoffTariffAmnt($currentCutoffId);
				 if(!empty($registrationAccompanyAmount) && $registrationAccompanyAmount>0)
				 {
				?>
				   <tr>
			            <td style="border: 0; vertical-align: bottom; padding: 8px 0;">
			                <table class="tableCard">
			                    <tr>
			                        <td style="font-size:42px; text-align:center; vertical-align: top; padding: 10px 10px;"
			                            width="10%">
			                            <i class="fas fa-users" style="color:#005e82;"></i>
			                        </td>
			                        <td style="border: 0; padding: 10px 5px; font-size: 18px; font-weight: bold">
			                            ACCOMPANY DETAILS
			                        </td>
			                    </tr>
			                    <tr>
			                        <td></td>
			                        <td>
			                            <span style="font-size:large;">You haven't added any accompany yet.</span>
			                        </td>
			                    </tr>
			                    <tr>
			                        <td></td>
			                        <td>
			                            <h5 class="smallhed addCategory"
			                                style="text-align: center;padding: 5px; color: #ffffff; cursor:pointer; background-color: #005e82;width: fit-content;"
			                                use="addCategory" linkId="accompany_add"
			                                onclick="addAccompanyFromProfile(this,'accompanyAdd')">ADD ACCOMPANY</h5>
			                        </td>
			                    </tr>
			                </table>
			            </td>
			        </tr>
				<?php
				}
			}
			?>
			<!-- Abstract submission details start -->
			<?
			//echo '<pre>'; print_r($abstract_details);
			 if(!empty($abstract_details) && count($abstract_details) > 0){ ?>
			<!-- <tr>
				<td style="border: 0; vertical-align: middle; padding: 0 0 8px 0;">
					<table class="tableCard" style="margin-bottom: 0">
						<tbody>
							<tr>
								<td rowspan="2" style="font-size:42px; text-align:center; vertical-align: top; padding: 10px 10px;" width="10%">
									<i class="fas fa-book-medical" willBlink='Y' style="color:#005e82;" ></i>
								</td>
								<td colspan="2" class="reg_cat" style="border: 0; padding: 10px 5px; font-size: 18px; font-weight: bold">
									<?=strtoupper($abstract_details['abstract_category'])?> <?=$abstract_details['abstract_parent_type']?> DETAILS
									<i class="fas fa-info-circle pull-right" style="cursor:pointer;" onclick="openProfileDataBlock(this,'abstractDetails');"></i>
								</td>
							</tr>
							<tr style="vertical-align: initial;">
								<td class="reg_name" style="border: 0; padding: 0px 5px 10px 5px; font-size: 16px">
									Topic: <b><?=$abstract_details['abstract_topic']?></b> <br> Submission Code: <b><?=$abstract_details['abstract_submition_code']?></b>							
								</td>
								<? if( $cfg['ABSTRACT.EDIT.LASTDATE'] >= date('Y-m-d') ) {?>
								<td class="left-btn">
									<a href="abstract.user.edit.php?user_id=<?=$delegateId?>&abstract_id=<?=$abstract_details['id']?>" target="" style="background: #005e82; border-radius: 20px; padding: 5px 15px; margin-right: 10px; color: #ffffff; ">EDIT</a>
								</td>
								<? } ?>
							</tr>
						</tbody>
					</table>
				</td>
			</tr> -->
			 <tr>
                        <td style="border: 0; vertical-align: middle; padding: 0 0 8px 0;">
                            <table class="tableCard" style="margin-bottom: 0">

                                <tbody>
                                    <?php
                                    //echo count($abstract_details);
                                    // echo '<pre>'; print_r($abstract_details);
                                    foreach ($abstract_details as $key => $value) {
                                    ?>
                                        <tr>
                                            <td rowspan="2" style="font-size:42px; text-align:center; vertical-align: top; padding: 10px 10px;" width="10%">
                                                <i class="fas fa-book-medical" willBlink='Y' style="color:#005e82;" ></i>
                                            </td>
                                            <td colspan="2" class="reg_cat" style="border: 0; padding: 10px 5px; font-size: 18px; font-weight: bold">
                                               <!--  <?=strtoupper($value['abstract_category'])?> <?=$value['abstract_parent_type']?> DETAILS -->
                                               ABSTRACT DETAILS
                                            </td>
                                        </tr>
                                        <tr style="vertical-align: initial;">
                                            
                                            <td class="reg_name" style="border: 0; padding: 0px 5px 10px 5px; font-size: 16px">
                                                

                                                 Submission Code: <b><?=$value['abstract_submition_code']?></b>                         
                                            </td>

                                            <?

                                             if( $cfg['ABSTRACT.EDIT.LASTDATE'] >= date('Y-m-d') ) {?>
                                                <td class="left-btn">
                                                    <a href="abstract.user.edit.php?user_id=<?=$delegateId?>&abstract_id=<?=$value['id']?>" target="" style="background: #035b6a; border-radius: 20px; padding: 5px 38px; margin-right: 10px; color: #ffffff; ">VIEW AND EDIT</a>
                                                </td>
                                                

                                            <? } ?>
                                        </tr>

                                    <?php
                                    }

                                    if(count($abstract_details)<100)
                                    {
                                    ?>
                                    <tr><td class="left-btn"><a href="<?=_BASE_URL_?>abstract.user.entrypoint.php" style="background: #035b6a; border-radius: 20px; padding: 5px 0px; margin: 0 0 20px 23px; color: #ffffff; ">Add New</a></td></tr>
                                    <?php
                                    }
                                    ?>
                                </tbody>
                            </table>
                        </td>
                    </tr>
			<? 
				if($_SESSION['absedmsg'] != '')
				{
			?>
				<script type="text/javascript">
				alert("<?=$_SESSION['absedmsg']?>")
				</script>
			<?
				}
				unset($_SESSION["absedmsg"]);
			} ?>
			<!-- Abstract submission details end -->
		</table>
	</div>
</div>	
<?
}

function profileConferenceRegistrationDetails($delegateId, $rowUserDetails, $invoiceList, $currentCutoffId)
{
	global $mycms, $cfg;

	
	$conferenceInvoiceDetails = reset($invoiceList[$delegateId]['REGISTRATION']);
?>
<div class="menu-center-section col-xs-12" style="display:none" operationMode="profileData" operationData="registrationDetails">
	<h5>
	Conference Registration Details of <?=$invoiceList[$delegateId]['REGISTRATION'][$delegateId]['USER']['user_full_name']?>
	<i class="fas fa-backward pull-right" style="cursor:pointer;" onClick="returnToSummaryBlock(this);"></i>
	</h5>
	<table class="table">
		<tbody>
			<tr>
				<td class="" width="30%" style="color:#308b64;">Registered</td>
				<td class="" style="color:#308b64;">Date: <?=$mycms->cDate('d/m/Y', $invoiceList[$delegateId]['REGISTRATION'][$delegateId]['USER']['conf_reg_date'])?></td>
			</tr>
			<tr>
				<td>Registration Category</td>
				<td>
					<?=$invoiceList[$delegateId]['REGISTRATION'][$delegateId]['REG_DETAIL']?> 
					<!--a class="cancel-bttn">Request for cancellation</a-->
				</td>
			</tr>
			<tr>
				<td>Registration Id</td>
				<td><?=$invoiceList[$delegateId]['REGISTRATION'][$delegateId]['USER']['user_registration_id']?></td>
			</tr>
			<tr>
				<td>Unique Sequence</td>
				<td><?=$invoiceList[$delegateId]['REGISTRATION'][$delegateId]['USER']['user_unique_sequence']?></td>
			</tr>
			<tr>
				<td>Registered E-mail id</td>
				<td><?=$invoiceList[$delegateId]['REGISTRATION'][$delegateId]['USER']['user_email_id']?></td>
			</tr>
			<tr>
				<td>Registered Mobile No</td>
				<td><?=$invoiceList[$delegateId]['REGISTRATION'][$delegateId]['USER']['user_mobile_no']?></td>
			</tr>
		</tbody>
	</table>
</div>
<?php
}

/// WORKSHOP
function profileWorkshopDetails($delegateId, $rowUserDetails, $invoiceList, $currentCutoffId)
{
	global $mycms, $cfg;
	
	$conferenceInvoiceDetails = reset($invoiceList[$delegateId]['REGISTRATION']);	

?>
<div class="menu-center-section col-xs-12" style="display:none" operationMode="profileData" operationData="workshopDetails">
	<h5>
		Workshop Registration Details<!-- Of <?=$rowUserDetails['user_full_name']?>-->
		<i class="fas fa-backward pull-right" style="cursor:pointer;" onClick="returnToSummaryBlock(this);"></i>
	</h5>
	<?	
	$workshopDetails = $invoiceList[$delegateId]['WORKSHOP'];
	
	?>
	<table class="table">
	<?
	if($workshopDetails)
	{	
		$wrksp_Cnt = 0;
	
		foreach($workshopDetails as $key => $rowWorkshopDetails	)
		{
			if($rowWorkshopDetails && $rowWorkshopDetails['INVOICE']['status']=='A')
			{
				$workshopStatus = true;	
				$wrksp_Cnt++;	
				// workshop related work by weavers start
				$slip_id = $rowWorkshopDetails['SLIP_DETAILS']['id'];	
				$delegateId = $rowWorkshopDetails['SLIP_DETAILS']['delegate_id'];
				$mode = $rowWorkshopDetails['SLIP_DETAILS']['invoice_mode'];
				//print_r($rowWorkshopDetails);
				// workshop related work by weavers end

				if(!empty($rowWorkshopDetails['ROW_DETAIL']['workshop_date']))
				{	
					$workshop_date = '('.$rowWorkshopDetails['ROW_DETAIL']['workshop_date'].')';
				}
				else
				{
					$workshop_date = '';	
				}

				if(!empty($rowWorkshopDetails['ROW_DETAIL']))
				{
			?>
				
				<tr>
					<th colspan="2">
						<?=$rowWorkshopDetails['REG_DETAIL']?> <?=$workshop_date?>
						<!--a operationMode="proceedPayment" style="float:right; border: 1px solid #e4181f; padding: 5px; color: #e4181f;" onClick="openWorkshopChangePopup('<?=$rowWorkshopDetails['INVOICE']['id']?>')">Request for workshop change</a-->
					</th>
				</tr>
				<tr>
					<td width="30%" style="color:#308b64;">
						 Registered on
					</td>
					<td style="color:#308b64;"><?=date('d/m/Y', strtotime($rowWorkshopDetails['INVOICE']['invoice_date']))?>
					</td>
				</tr>
				<tr>
					<td>Workshop Status </td>
					<td>
					<strong style="color:<?=(($rowWorkshopDetails['INVOICE']['payment_status']=='PAID' || $rowWorkshopDetails['INVOICE']['payment_status']=='ZERO_VALUE' || $rowWorkshopDetails['INVOICE']['payment_status']=='COMPLIMENTARY') ?'#00a200':'red')?>;"><?=($rowWorkshopDetails['INVOICE']['payment_status']!= 'UNPAID')?'Registered':'Incomplete'?></strong>
					<!-- workshop related work by weavers start -->
					<? if($rowWorkshopDetails['INVOICE']['payment_status'] == 'UNPAID' && $rowWorkshopDetails['INVOICE']['invoice_mode'] != 'OFFLINE'){ ?>
							<form action="<?=_BASE_URL_?>profile.php" method="post" name="loginUnpaidOnlineFrmInit">
								<input type="hidden" id="slip_id" name="slip_id" value="<?=$slip_id?>" />
								<input type="hidden" id="delegate_id" name="delegate_id" value="<?=$delegateId?>" />
								<input type="hidden" name="act" value="paymentSetInit" />
								<input type="hidden" name="mode" value="<?=$mode?>" />
								<!-- <span><a href="javascript:void(0);" onclick="paymentProcess()" title="Complete Payment" style="border: 1px solid #e27926; padding: 5px; color: #e27926; text-decoration:none;font-size:large; float:right;">Complete Now</a></span> -->
								<input type="submit" name="sumit" value="Complete Now" style="background-color: #005e82; padding: 5px; color: #ffffff; text-decoration:none;font-size:large; float:right; height: auto;">
							</form>
					<?  } ?>
					<!-- workshop related work by weavers end -->
					</td>
				</tr>
			<?
				}
			}
		}												
	}
	if($workshopStatus == false)
	{
	?>
		<tr>
			<td><span style="color:#842267; font-size:large;">You don't have booked any workshop</span>	</td>						
		</tr>
	<?
	}
?>
	</table>
</div>	
<?
}

function profileDinnerAdd($delegateId, $rowUserDetails, $invoiceList, $currentCutoffId)
{
	global $mycms, $cfg;
	
	$currentCutoffId 			= getTariffCutoffId();
	
	$conferenceInvoiceDetails 	= reset($invoiceList[$delegateId]['REGISTRATION']);	

	$conferenceDetails 			= reset($invoiceList[$delegateId]['REGISTRATION']);	
	
	$workshopSessionDetails 	= $invoiceList[$delegateId]['WORKSHOP'];
	$workshopPaymentStatus 		= "NO";
	$workshopSessionStatus 		= "0";		// paid					
	$workshoSessionTitle 		= "";
	
	
	
	if($workshopSessionDetails)
	{		
		// workshop related work by weavers start
		$slipIDs = array();	 // define as array	
		$existingWorkshopIds = array();
		$existingWorkShops = array();		

		// workshop related work by weavers end
		foreach($workshopSessionDetails as $key => $rowWorkshopCount)
		{							
			$rawData 		= $rowWorkshopCount['ROW_DETAIL'];
			$invoiceData 	= $rowWorkshopCount['INVOICE'];
			$slipData 		= $rowWorkshopCount['SLIP_DETAILS'];
			$paymentData 	= $rowWorkshopCount['SLIP_PAYMENT'];
			$UserData 		= $rowWorkshopCount['USER'];
			
			// workshop related work by weavers start
			$slipIDs[] = $slipData['id']; 
			$existingWorkshopIds[] = $rawData['id'];
			$existingWorkShops[] = $rawData['type']; 	

			// workshop related work by weavers end
			
			if($invoiceData['status']=='A')
			{
				$paymentStatus = $invoiceData['payment_status'];
				

				//if($rawData['type'] == 'POSTCONFERENCE')
				// workshop related work by weavers start
				if($rawData['type'] == 'MASTER CLASS' || $rawData['type'] == 'WORKSHOP')
				// workshop related work by weavers end
				{
					if($paymentStatus=='UNPAID')
					{
						$workshopSessionStatus = 'UNPAID';	
					}
					else if($paymentStatus=='PAID')
					{
						$workshopSessionStatus = 'PAID';	
						$workshoSessionTitle = $rowWorkshopSessionDetails['REG_DETAIL'];
					}								
				}
			}
		}					
	}
		
	if($workshopSessionStatus == 'PAID')
	{
		$workshopPaymentStatus = "YES";
	}
?>
<div class="menu-center-section col-xs-12" style="display:none" operationMode="profileData" operationData="dinnerAdd">
<?	
	$workshopSessionStatus = 'Paid';
	if($workshopSessionStatus == 'UNPAID')
	{
	?>
	<?php
	}
	else
	{						
	?>	
		<h5 sequenceNos='11'> 
		Register to Dinner
		<i class="fas fa-backward pull-right" style="cursor:pointer;" onClick="returnToSummaryBlockDinner(this);"></i>
		</h5>
		<form name="frmAddDinnerfromProfile" id="frmAddDinnerfromProfile" action="<?=$cfg['BASE_URL']?>registration.process.php" method="post">
			<input type="hidden" name="act" value="add_dinner_profile" />
			<input type="hidden" id="cutoff_id" name="cutoff_id" value="<?=$currentCutoffId?>" cutoffid="<?=$currentCutoffId?>" />
			<input type="hidden" name="delegateClasfId" value="<?=$rowUserDetails['registration_classification_id']?>" />								
			<input type="hidden" id="registrationRequest" name="registrationRequest" value="<?=$rowUserDetails['registration_request']?>" />
										
			<!--<input type="hidden" name="workshopAmount" id="workshopAmount" value="<?=($registration_tariff_cutoff_id==1)?'3000':'5000'?>" />-->
			<div class="" actAs='fieldContainer'>
			<table class="table">
				<? 
				
					$currentCutoffId 			= getTariffCutoffId();	
					
					$conferenceTariffArray   	= getAllRegistrationTariffs($currentCutoffId);
					$workshopDetDisplay 	 	= getAllWorkshopTariffs($currentCutoffId);

					
					$condition = '';
					if(count($existingWorkShops) > 0)
					{
						$condition .= 'AND type NOT IN("' . implode('", "', $existingWorkShops) . '")';
					}

					$sqlWorkshop = array();
					$sqlWorkshop['QUERY'] = "SELECT id,type
											 	FROM  "._DB_WORKSHOP_CLASSIFICATION_." 
												WHERE `status` = ? ".$condition;
					$sqlWorkshop['PARAM'][]   = array('FILD' => 'status',  'DATA' => 'A', 'TYP' => 's');

					$resWorkshop = $mycms->sql_select($sqlWorkshop,false);

					$availabel_workshop_ids = array_map(function($item) {
					    return $item['id'];
					}, $resWorkshop);
					
					$workshopCountArr 		 	= totalWorkshopCountReport();	
					
					$workshopDisplay			= array();
					
					$dinnerTariffArray   = getAllDinnerTarrifDetails($currentCutoffId);
					//print_r($dinnerTariffArray);

					if(!empty($dinnerTariffArray))
					{	
						
						//$workshopDetailsArray = $workshopDisplay['POST-CONFERENCE'];	
						// workshop related work by weavers start
						if($dinnerTariffArray)
						{
							$workshopDetailsArray = $workshopDisplay['MASTER CLASS'];

					?>	
						<tr>
							<td colspan="2" style="text-align:center;">
								<div class="container-box masterClass" use="accordianL1TriggerDiv" useSpec1="stage2">
									<i><b>Banquet Dinner</b></i>
								</div>
							</td>
						</tr>
						<?	
						
						// workshop related work by weavers end
							foreach($dinnerTariffArray as $keyDinner=>$dinnerValue)
							{	
								

										// disable workshop if tariff amount is 0 and user registration classification type is not member by weavers end										
							?>
										<tr>
											<td>
												<div class="radio" style="border: 0px solid #2cb8f4; border-radius: 0px; background: transparent; padding: 0px 0px; box-shadow: none; margin-top: 0;">
													<label class="container-box" style="width: 100%; float: left; <?=$disable_label?>">
														<?=$dinnerValue[$currentCutoffId]['DINNER_TITTLE']?>
														<input type="checkbox" name="dinner_value[]" id="dinner_id_<?=$dinnerValue[$currentCutoffId]['ID'].'_'.$keyRegClasf?>"  
															   value="<?=$dinnerValue[$currentCutoffId]['ID']?>" <?=$style?>  dinnerName="<?=$rowRegClasf['WORKSHOP_NAME']?>" operationMode="dinnerId"  amount="<?=$dinnerValue[$currentCutoffId]['AMOUNT']?>" currency="<?=$rowRegClasf['CURRENCY']?>" registrationClassfId="<?=$keyRegClasf?>" invoiceTitle="<?=$dinnerValue[$currentCutoffId]['DINNER_TITTLE']?>" <?=$disable_radio?> />
														<span class="checkmark"></span>
													 </label>
												</div>
											</td>
											<td align="right"><?=$dinnerValue[$currentCutoffId]['AMOUNT']?></td>
										</tr>
							<?
								/*	}
								}*/
							}
						}
						
						// workshop related work by weavers end
					}
				
				?>
			</table>
				<!-- // workshop related from profile work by weavers start -->
				<div class="alert alert-danger" callFor='alert' style="margin-top: 10px;">Please choose a proper option.</div>
				<div class="text-center pull-right" style="margin-top:10px; display:none;">
					<button type="button" class="submit" sequenceNos='11' use='nextButton' onclick="showPaymentSectionDinner(this)">Next</button>
				</div>
				<!-- // workshop related from profile work by weavers end -->
			</div>	
			
			<?
			//if($workshopPaymentStatus == "NO")
			//{
			?>
			<!-- // workshop related from profile work by weavers start -->
			<div use="registrationPaymentOption" style="display:none;" class="rightPanel_payment">
                <div class="link" use="rightAccordianL1TriggerDiv">PAYMENT OPTION</div>
                <ul class="submenu" style="display: none">
                    <li>
						<div class="col-xs-12 form-group" actAs='fieldContainer'>
							<div class="radio">
								<label class="select-lable" >Payment Options</label>
								<label class="container-box">
									CREDIT / DEBIT CARD / ONLINE PAYMENT
									<input type="radio" name="registrationMode" value="ONLINE" operationMode="registrationMode" use='tariffPaymentMode' for="CC">
									<span class="checkmark"></span>
								</label>
								<div class="rightPanel_payment_CC" style="display:none; padding-left:20px;" for="CC" use='payRules'>
									Payment will be accepted only through VISA <img src="<?=_BASE_URL_?>images/visa_card_cion.png">  
									MASTER CARD <img src="<?=_BASE_URL_?>images/master_card_cion.png">
									RuPay <img src="<?=_BASE_URL_?>images/rupay_card_icon.png">
									Maestro <img src="<?=_BASE_URL_?>images/maestro_icon.png">
									MasterCard SecureCode <img src="<?=_BASE_URL_?>images/master_secured_icon.png">
									<!-- Diners Club International <img src="<?=_BASE_URL_?>images/dinner_club_icon.png"> -->
									<!-- American Express Card <img src="<?=_BASE_URL_?>images/american_express_card_cion.png"><br> -->
									For any query, please write at <?=$cfg['EMAIL_CONF_EMAIL_US']?> or call at <?=$cfg['EMAIL_CONF_CONTACT_US']?><br><br> 
								</div>
								
								<label class="container-box">
									CHEQUE
									<input type="radio" name="registrationMode" value="OFFLINE" operationMode="registrationMode" use='tariffPaymentMode' for="CHQ">
									<span class="checkmark"></span>
								</label>
								<div class="rightPanel_payment_CHQ" style="display:none; padding-left:39px;" for="CHQ" use='payRules'>
									<?=$cfg['cheque_info']?>
								</div>
								
								<label class="container-box">
									DEMAND DRAFT
									<input type="radio" name="registrationMode" value="OFFLINE" operationMode="registrationMode" use='tariffPaymentMode' for="DD">
									<span class="checkmark"></span>
								</label>
								<div class="rightPanel_payment_DD" style="display:none; padding-left:39px;" for="DD" use='payRules'>
									<?=$cfg['draft_info']?>	 														
								</div>														
												
								<label class="container-box">
									NEFT / RTGS / BANK TRANSFER / NET BANKING
									<input type="radio" name="registrationMode" value="OFFLINE" operationMode="registrationMode" use='tariffPaymentMode' for="WIRE">
									<span class="checkmark"></span>
								</label>
								<div class="rightPanel_payment_NEFT" style="display:none; padding-left:39px;" for="WIRE" use='payRules'>															
									<?=$cfg['neft_info']?>
								</div>
												
								<label class="container-box">
									CASH PAYMENT
									<input type="radio" name="registrationMode" value="OFFLINE" operationMode="registrationMode" use='tariffPaymentMode' for="CASH">
									<span class="checkmark"></span>
								</label>
								<div class="rightPanel_payment_CASH" style="display:none; padding-left:39px;" for="CASH" use='payRules'>															
									Payment can be sent by money order to the ISAR 24 Secretariat. 
									Direct deposition will not be accepted.<br>
									For any query, please write at <?=$cfg['EMAIL_CONF_EMAIL_US']?> or call at <?=$cfg['EMAIL_CONF_CONTACT_US']?><br><br> 
								</div>	
							</div>
							<div class = "alert alert-danger" callFor='alert'>Please choose a proper option.</div>
						</div>  
						<div class=" col-xs-2 text-center pull-right">
							<button type="button" class="submit" use='nextButton'>Next</button>
						</div>          
						<div class="clearfix"></div>
                    </li>
                </ul>
            </div>

			<div use="registrationPayment" id="registrationPaymentDinner" style="display:none;" class="rightPanel_payment">
                
                <h5 sequenceNo='2'>
					<i class="fas fa-backward pull-right" style="cursor:pointer;" onClick="returnToSummaryBlockDinner(this,'dinnerAdd');"></i>
				</h5>
				<div class="col-xs-12 form-group">	
					<i class="pull-left"><b>NOTE:</b> Rates are inclusive of all Taxes.</i>
				</div> 
				<div use="totalAmount" class="col-xs-12 totalAmount pull-right" style="padding: 0;  display:none;">
					<table class="table bill" use="totalAmountTable">
						<thead>
							<tr>
								<th>DETAIL</th>
								<th align="right" style="text-align:right;">AMOUNT (INR)</th>
							</tr>
						</thead>
						<tbody></tbody>
						<tfoot>
							<tr style="display:none;" use='rowCloneable'>
								<td>&bull; &nbsp <span use="invTitle">Something</span></td>
								<td align="right"><span use="amount">0.00</span></td>
							</tr>
							<tr>
								<td>Total</td>
								<td align="right"><span use='totalAmount'>0.00</span></td>
							</tr>
						</tfoot>
					</table>
				</div>
									
				<div class="col-xs-12 form-group" use="offlinePaymentOptionChoice" id="offlinePaymentOptionChoiceDinner" actAs='fieldContainer'>
					<div class="checkbox">
						<label class="select-lable" >Payment Option</label>
						<div>
							<label class="container-box" style="float:left; margin-right:30px;">Online Payment
							  <input type="radio" name="payment_mode" use="payment_mode_select" value="Card" for="Card" paymentMode='ONLINE'>
							  <span class="checkmark"></span>
							</label>
							<?php
								$offline_payments = json_decode($cfg['PAYMENT.METHOD']);

			                    if (in_array("Cheque", $offline_payments))
			                    {
			                    ?>
									<label class="container-box" style="float:left; margin-right:30px;">Cheque
									  <input type="radio" name="payment_mode" use="payment_mode_select" value="Cheque" for="Cheque" paymentMode='OFFLINE' profileFrom="<?=$id;?>">
									  <span class="checkmark"></span>
									</label>
								<?php
				                   }
				                   if (in_array("Draft", $offline_payments))
				                   {
				                   ?> 
									<label class="container-box" style="float:left; margin-right:30px;">Draft
									  <input type="radio" name="payment_mode" use="payment_mode_select" value="Draft" for="Draft" paymentMode='OFFLINE' profileFrom="<?=$id;?>">
									  <span class="checkmark"></span>
									</label>
								<?php
				                   }
				                   if (in_array("Neft", $offline_payments))
				                   {
				                   ?>	
									<label class="container-box" style="float:left; margin-right:30px;">NEFT
									  <input type="radio" name="payment_mode" use="payment_mode_select" value="NEFT" for="NEFT" paymentMode='OFFLINE' profileFrom="<?=$id;?>">
									  <span class="checkmark"></span>
									</label>
								 <?php
			                    }
			                   if (in_array("Rtgs", $offline_payments))
			                   {
			                    ?>
									<label class="container-box" style="float:left; margin-right:30px;">RTGS
									  <input type="radio" name="payment_mode" use="payment_mode_select" value="RTGS" for="RTGS" paymentMode='OFFLINE' profileFrom="<?=$id;?>">
									  <span class="checkmark"></span>
									</label>
							  <?php
			                   }
			                   if (in_array("Cash", $offline_payments))
			                   {
			                   ?> 	
									<label class="container-box" style="float:left; margin-right:30px;">Cash
									  <input type="radio" name="payment_mode" use="payment_mode_select" value="Cash" for="Cash" paymentMode='OFFLINE' profileFrom="<?=$id;?>">
									  <span class="checkmark"></span>
									</label>
							   <?php
			                   }
			                   if (in_array("Upi", $offline_payments))
			                   {
			                   ?> 	
								<label class="container-box" style="float:left; margin-right:30px;">UPI
								  <input type="radio" name="payment_mode" use="payment_mode_select" value="UPI" for="UPI" paymentMode='OFFLINE' profileFrom="<?=$id;?>">
								  <span class="checkmark"></span>
								</label>
							  <?php
							  }
							  ?>
							&nbsp;
						</div>																				
					</div>
					<div class = "alert alert-danger" id="payment_error_msg" callFor='alert' style="display: none;">Please choose a proper option.</div>
				</div>
				<? /* // commented as per feedback on 09.09.2022
				<div class="col-xs-12 form-group" style="display:none;" use="offlinePaymentOption" for="Card" actAs='fieldContainer'>
					<div class="checkbox">
						<label class="select-lable" >Card Type</label>
						<div>
							<!-- <label class="container-box" style="float:left; margin-right:30px;">
							  <img src="<?=_BASE_URL_?>images/international_globe.png" height="20px;">
							  International Card
							  <input type="radio" name="card_mode" use="card_mode_select" value="International">
							  <span class="checkmark"></span>											   
							</label> -->
							<label class="container-box" style="float:left; margin-right:30px;">
							  <img src="<?=_BASE_URL_?>images/india_globe.png" height="20px;">
							  Indian Card
							  <input type="radio" name="card_mode" use="card_mode_select" value="Indian">
							  <span class="checkmark"></span>
							</label>
							&nbsp;
						</div>																				
					</div>
					<div class = "alert alert-danger" callFor='alert'>Please choose a proper option.</div>
				</div>
				*/ ?>
				<div class="col-xs-12 form-group input-material" style="display:none;" use="offlinePaymentOption" for="Card">
					Payment will be accepted only through VISA <img src="<?=_BASE_URL_?>images/visa_card_cion.png">  
					MASTER CARD <img src="<?=_BASE_URL_?>images/master_card_cion.png">
					RuPay <img src="<?=_BASE_URL_?>images/rupay_card_icon.png">
					Maestro <img src="<?=_BASE_URL_?>images/maestro_icon.png">
					MasterCard SecureCode <img src="<?=_BASE_URL_?>images/master_secured_icon.png">
					<!-- Diners Club International <img src="<?=_BASE_URL_?>images/dinner_club_icon.png"> -->
					<!-- American Express Card <img src="<?=_BASE_URL_?>images/american_express_card_cion.png"> --><br>
					For any query, please write at <?=$cfg['EMAIL_CONF_EMAIL_US']?> or call at <?=$cfg['EMAIL_CONF_CONTACT_US']?><br><br> 
				</div>
				
				<div class="col-xs-6 form-group input-material" style="display:none;" use="offlinePaymentOption" for="Cash" actAs='fieldContainer'>
					<label for="user_first_name">Money Order Sent Date</label>
					<input type="date" class="form-control" name="cash_deposit_date" id="cash_deposit_date" max="<?=$mycms->cDate("Y-m-d")?>" min="<?=$mycms->cDate("Y-m-d","-6 Months")?>">
					<div class = "alert alert-danger" callFor='alert'>Please choose a proper option.</div>
				</div>
				<div class="col-xs-12 form-group input-material" style="display:none;" use="offlinePaymentOption" for="Cash">
					<?=$cfg['cash_info']?>
				</div>
				<!-- UPI Payment Option Added By Weavers start -->

				<div class="col-xs-12 form-group input-material">
				    <div class="col-xs-6 form-group input-material" style="display:none;" use="offlinePaymentOption" for="UPI" actAs='fieldContainer'>

				        <label for="txn_no">UPI Transaction ID</label>
				        <input type="text" class="form-control" name="txn_no" id="txn_no">
				        <div class = "alert alert-danger" callFor='alert'>Please choose a proper option.</div>
				    </div>
				    <div class="col-xs-6 form-group input-material" style="display:none;" use="offlinePaymentOption" for="UPI" actAs='fieldContainer'>
				        <label for="txn_no">UPI Payment Date</label>
				        <input type="date" class="form-control" name="upi_date" id="upi_date" max="<?=$mycms->cDate("Y-m-d")?>" min="<?=$mycms->cDate("Y-m-d","-6 Months")?>">
				        <div class = "alert alert-danger" callFor='alert'>Please choose a proper option.</div>
				    </div> 
				</div>  
				<!-- UPI Payment Option Added By Weavers end -->
			
				<div class="col-xs-12 form-group input-material" style="display:none;" use="offlinePaymentOption" for="Cheque" actAs='fieldContainer'>
					<label for="user_first_name">Cheque No</label>
					<input type="text" class="form-control" name="cheque_number" id="cheque_number">
					<div class = "alert alert-danger" callFor='alert'>Please enter a proper value.</div>
				</div>
				<div class="col-xs-6 form-group input-material" style="display:none;" use="offlinePaymentOption" for="Cheque" actAs='fieldContainer'>
					<label for="user_first_name">Drawee Bank</label>
					<input type="text" class="form-control" name="cheque_drawn_bank" id="cheque_drawn_bank">
					<div class = "alert alert-danger" callFor='alert'>Please enter a proper value.</div>
				</div>
				<div class="col-xs-6 form-group input-material" style="display:none;" use="offlinePaymentOption" for="Cheque" actAs='fieldContainer'>
					<label for="user_first_name">Cheque Date</label>
					<input type="date" class="form-control" name="cheque_date" id="cheque_date" max="<?=$mycms->cDate("Y-m-d")?>" min="<?=$mycms->cDate("Y-m-d","-6 Months")?>">
					<div class = "alert alert-danger" callFor='alert'>Please choose a proper option.</div>
				</div>
				<div class="col-xs-12 form-group input-material" style="display:none;" use="offlinePaymentOption" for="Cheque">
					<?=$cfg['cheque_info']?>
				</div>
			
				<div class="col-xs-12 form-group input-material" style="display:none;" use="offlinePaymentOption" for="Draft" actAs='fieldContainer'>
					<label for="user_first_name">Draft No</label>
					<input type="text" class="form-control" name="draft_number" id="draft_number">
					<div class = "alert alert-danger" callFor='alert'>Please enter a proper value.</div>
				</div>
				<div class="col-xs-6 form-group input-material" style="display:none;" use="offlinePaymentOption" for="Draft" actAs='fieldContainer'>
					<label for="user_first_name">Drawee Bank</label>
					<input type="text" class="form-control" name="draft_drawn_bank" id="draft_drawn_bank">
					<div class = "alert alert-danger" callFor='alert'>Please enter a proper value.</div>
				</div>
				<div class="col-xs-6 form-group input-material" style="display:none;" use="offlinePaymentOption" for="Draft" actAs='fieldContainer'>
					<label for="user_first_name">Draft Date</label>
					<input type="date" class="form-control" name="draft_date" id="draft_date" max="<?=$mycms->cDate("Y-m-d")?>" min="<?=$mycms->cDate("Y-m-d","-6 Months")?>">
					<div class = "alert alert-danger" callFor='alert'>Please choose a proper option.</div>
				</div>
				<div class="col-xs-12 form-group input-material" style="display:none;" use="offlinePaymentOption" for="Draft">
					<?=$cfg['draft_info']?>	
				</div>
			
				<div class="col-xs-12 form-group input-material" style="display:none;" use="offlinePaymentOption" for="NEFT" actAs='fieldContainer'>
					<label for="user_first_name">Transaction Id</label>
					<input type="text" class="form-control" name="neft_transaction_no" id="neft_transaction_no">
					<div class = "alert alert-danger" callFor='alert'>Please enter a proper value.</div>
				</div>
				<div class="col-xs-6 form-group input-material" style="display:none;" use="offlinePaymentOption" for="NEFT" actAs='fieldContainer'>
					<label for="user_first_name">Drawee Bank</label>
					<input type="text" class="form-control" name="neft_bank_name" id="neft_bank_name">
					<div class = "alert alert-danger" callFor='alert'>Please enter a proper value.</div>
				</div>
				<div class="col-xs-6 form-group input-material" style="display:none;" use="offlinePaymentOption" for="NEFT" actAs='fieldContainer'>
					<label for="user_first_name">Date</label>
					<input type="date" class="form-control" name="neft_date" id="neft_date" max="<?=$mycms->cDate("Y-m-d")?>" min="<?=$mycms->cDate("Y-m-d","-6 Months")?>">
					<div class = "alert alert-danger" callFor='alert'>Please choose a proper option.</div>
				</div>
				<div class="col-xs-12 form-group input-material" style="display:none;" use="offlinePaymentOption" for="NEFT">
					<?=$cfg['neft_info']?>
				</div>
				<div class="col-xs-12 form-group input-material" style="display:none;" use="offlinePaymentOption" for="UPI">
				   <?=$cfg['upi_info']?>
				</div>
			
				<div class="col-xs-12 form-group input-material" style="display:none;" use="offlinePaymentOption" for="RTGS" actAs='fieldContainer'>
					<label for="user_first_name">Transaction Id</label>
					<input type="text" class="form-control" name="rtgs_transaction_no" id="rtgs_transaction_no">
					<div class = "alert alert-danger" callFor='alert'>Please enter a proper value.</div>
				</div>
				<div class="col-xs-6 form-group input-material" style="display:none;" use="offlinePaymentOption" for="RTGS" actAs='fieldContainer'>
					<label for="user_first_name">Drawee Bank</label>
					<input type="text" class="form-control" name="rtgs_bank_name" id="rtgs_bank_name">
					<div class = "alert alert-danger" callFor='alert'>Please enter a proper value.</div>
				</div>
				<div class="col-xs-6 form-group input-material" style="display:none;" use="offlinePaymentOption" for="RTGS" actAs='fieldContainer'>
					<label for="user_first_name">Date</label>
					<input type="date" class="form-control" name="rtgs_date" id="rtgs_date" max="<?=$mycms->cDate("Y-m-d")?>" min="<?=$mycms->cDate("Y-m-d","-6 Months")?>">
					<div class = "alert alert-danger" callFor='alert'>Please choose a proper option.</div>
				</div>
				<div class="col-xs-12 form-group input-material" style="display:none;" use="offlinePaymentOption" for="RTGS">
					<?=$cfg['neft_info']?>
				</div>
			
				<div class="col-xs-12 form-group" use="conditionOption" actAs='fieldContainer'>
					<div class="checkbox">
						<label class="container-box"> 
							By Clicking, you hereby agree to receive all promotional SMS and e-mails related to ISAR 24. To unsubscribe, send us a mail at isarbbsr2024@gmail.com.
							<input type="checkbox" name="acceptance1" value="acceptance1" required>
							<span class="checkmark"></span>
						</label>
					</div>
					<div class = "alert alert-danger" callFor='alert'>Please choose option.</div>	
					<div class="checkbox">
						<label class="container-box">
							I have read and clearly understood the 
							<a href="<?=_BASE_URL_?>terms.php" title="Click to View 'Terms &amp; Conditions'" target="_blank" class="anclink">Terms &amp; Conditions</a> 
							and 
							<a href="<?=_BASE_URL_?>cancellation.php" title="Click to View 'Cancellation &amp; Refund Policy'" target="_blank" class="anclink">Cancellation &amp; Refund Policy</a> 
							and agree with the same. 
							<input type="checkbox" name="acceptance2" value="acceptance2" required>
							<span class="checkmark"></span>
						</label>										
					</div>
					<div class = "alert alert-danger" callFor='alert'>Please choose option.</div>
				</div> 
			 
				<div class=" col-xs-12 text-center pull-right">
					<button type="submit" class="submit" sequenceNo='5' use='nextButton'>Proceed to Pay</button>
				</div>      
				<div class="clearfix"></div>
			</div>

			<div use="complimentrayWorkshop" style="display:none; margin-top: 15px;" class="rightPanel_payment">
				<div class=" col-xs-12 text-center pull-right">
					<button type="submit" class="submit" sequenceNo='3' use='nextButton'>Complete Registration Process</button>
				</div>      
				<div class="clearfix"></div>
			</div>

			<div use="registrationProcess" style="display:none; text-align:center;" class="rightPanel_payment">
				<img src="<?=_BASE_URL_?>images/PaymentPreloader.gif"/>
			</div>
			<!-- // workshop related from profile work by weavers end -->
			<?php /*?>
			<table class="table">
				<tr>
					<td width="40%">											
						Payment Mode <span style="color:#FF0000;">*</span>
					</td>
					<td>
						<input name="registrationMode" operationmode="registrationMode" id="registrationMode_online" value="ONLINE" type="radio" required > Online &nbsp;&nbsp;
						<input name="registrationMode" operationmode="registrationMode" id="registrationMode_offline" value="OFFLINE" type="radio" checked="checked"> Offline
					</td>
				</tr>
				<tr>
					<th align="right" style="text-decoration:none; background: rgb(204, 204, 204); border: 1px solid rgb(204, 204, 204);" colspan="2" >
						<div class="reg_total_amnt">
							Amount: <?=getRegistrationCurrency($delagateCatagory)?> <span id="ammount">0.00</span>
						</div>
					</th>
				</tr>
				<tr>
					<td></td>
					<td>
						<input type="submit" value="Next" class="btn btn-warning viobtn slow"  />
					</td>
				</tr>
			</table>
			<?php */?>
			
			<?
			//}
			?>
		</form>	
		<script>
			
		$(document).ready(function(){
			/*$("div[use=profileWorkshopAdd]").find("input[type=checkbox][operationMode=workshopId]").click(function(){
				
				$("div[use=profileWorkshopAdd]").find("input[type=checkbox][operationMode=workshopId]").prop("checked",false);
				$(this).prop("checked",true);
				
				calculateTotalWorkshoAmountInProfile();
			});

			$("form[name=frmAddWorkshopfromProfile]").submit(function(e){			
				var checkedWrkObj = $("div[use=profileWorkshopAdd]").find("input[type=checkbox][operationMode=workshopId]:checked");
				if($(checkedWrkObj).length == 0)
				{
					alert("Select a Workshop.");
					$("div[use=profileWorkshopAdd]").find("input[type=checkbox][operationMode=workshopId]").focus();
					e.preventDefault();
					return false;
				}
				
				var checkedWrkObj = $("div[use=profileWorkshopAdd]").find("input[type=radio][operationMode=registrationMode]:checked");
				if($(checkedWrkObj).length == 0)
				{
					alert("Select a Payment Mode.");
					$("div[use=profileWorkshopAdd]").find("input[type=radio][operationMode=registrationMode]").focus();
					e.preventDefault();
					return false;
				}
			});*/

			// workshop related work for profile by weavers start
			var novemberWorkshop = $("input[type=checkbox][operationMode=workshopId_nov]:checked").length;
			var decemberWorkshop = $("input[type=checkbox][operationMode=dinnerId]:checked").length;
            if(novemberWorkshop > 0 || decemberWorkshop > 0){
				$('button[sequencenos=11]').parent().show();
			}else{
				$('button[sequencenos=11]').parent().hide();
			}


			$("input[type=checkbox][operationMode=dinnerId]").each(function(){
                $(this).click(function(){   
                    
                    var currChkbxStatus = $(this).attr("chkStatus");

                    var novemberWorkshop = $("input[type=checkbox][operationMode=dinnerId_nov]:checked").length;
					var decemberWorkshop = $("input[type=checkbox][operationMode=dinnerId]:checked").length;
                    if(novemberWorkshop > 0 || decemberWorkshop > 0){
						$('button[sequencenos=11]').parent().show();
						popDownAlert();
					}else{
						$('button[sequencenos=11]').parent().hide();
					}
                    
                    $("input[type=checkbox][operationMode=dinnerId]").prop("checked",false);  
                    $("input[type=checkbox][operationMode=dinnerId]").attr("chkStatus","false");
                    
                    if(currChkbxStatus=="true")
                    {
                        $(this).prop("checked",false);  
                        $(this).attr("chkStatus","false");
                    }
                    else
                    {   
                        $(this).prop("checked",true);   
                        $(this).attr("chkStatus","true");   
                    }
                    
                    calculateTotalDinnerAmountInProfile();
                });
            });

            
			$("input[type=checkbox][operationMode=dinnerId_nov]").each(function(){
				$(this).click(function(){	
					
					var currChkbxStatus = $(this).attr("chkStatus");

					var novemberWorkshop = $("input[type=checkbox][operationMode=dinnerId_nov]:checked").length;
					var decemberWorkshop = $("input[type=checkbox][operationMode=dinnerId]:checked").length;
                    if(novemberWorkshop > 0 || decemberWorkshop > 0){
						$('button[sequencenos=11]').parent().show();
						popDownAlert();
					}else{
						$('button[sequencenos=11]').parent().hide();
					}
					
					$("input[type=checkbox][operationMode=dinnerId_nov]").prop("checked",false);	
					$("input[type=checkbox][operationMode=dinnerId_nov]").attr("chkStatus","false");
					
					if(currChkbxStatus=="true")
					{
						$(this).prop("checked",false);	
						$(this).attr("chkStatus","false");
					}
					else
					{	
						$(this).prop("checked",true);	
						$(this).attr("chkStatus","true");	
					}
					
					calculateTotalDinnerAmountInProfile();
				});
			});
			// workshop related work for profile by weavers end
			
		});
		
		
		function calculateTotalDinnerAmountInProfile()
		{	
			// workshop related work for profile by weavers start
			
			var totalAmount = 0;
		    var totTable    = $('div[use=registrationPayment]').find("table[use=totalAmountTable]");
		    $(totTable).children("tbody").find("tr").remove();

		    var novemberWorkshop = $("input[type=checkbox][operationMode=dinnerId_nov]:checked").length;
			var decemberWorkshop = $("input[type=checkbox][operationMode=dinnerId]:checked").length;
		    //$.each($("input[type=checkbox][operationMode=workshopId_nov]:checked,input[type=checkbox][operationMode=workshopId]:checked"),function(){
		    $.each($("div[operationData=dinnerAdd] input[type=checkbox]:checked,div[operationData=dinnerAdd] input[type=radio]:checked"),function(){
		        var attr = $(this).attr('amount');
		        
		        if (typeof attr !== typeof undefined && attr !== false) 
		        {
		            var amt     = parseFloat(attr); 
		            totalAmount = totalAmount+amt;
		            
		            var attrReg = $(this).attr('operationMode');
		            
		            var isMastCls  = false;
		            if (typeof attrReg !== typeof undefined && attrReg !== false && attrReg === 'workshopId') 
		            {
		                isMastCls = true;
		            }

		            var isNovWorkshop = false;
		            if(typeof attrReg !== typeof undefined && attrReg !== false && attrReg === 'workshopId_nov')
		            {
		                isNovWorkshop = true;
		            }

		            var cloneIt = false;
		            var amtAlterTxt = 'Complimentary';
		           
		            if(amt > 0)
		            {
		                cloneIt = true;
		            }
		            else if(isMastCls || isNovWorkshop)
		            {
		                cloneIt = true;
		                amtAlterTxt = 'Included in Registration'
		            }
		            
		            if(cloneIt)
		            {                           
		                var cloned  = $(totTable).children("tfoot").find("tr[use=rowCloneable]").first().clone();                                                   
		                $(cloned).attr("use","rowCloned");
		                $(cloned).find("span[use=invTitle]").text($.trim($(this).attr('invoiceTitle')));                                    
		                $(cloned).find("span[use=amount]").text((amt>0)?(amt).toFixed(2):amtAlterTxt);
		                $(cloned).show();
		                $(totTable).children("tbody").append(cloned);
		            }
		        }
		       
		        //console.log($(this))
		        if($(this).attr('use')=='payment_mode_select')
		        //if($(this).attr('paymentMode')=='ONLINE' && $(this).attr('use')=='payment_mode_select')
		        {
		            if($(this).val()=='Card')
		            //if($(this).val()=='Card')
		            {
		                var internetHandling = <?=$cfg['INTERNET.HANDLING.PERCENTAGE']?>;
		                var internetAmount = (totalAmount*internetHandling)/100;
		                totalAmount = totalAmount+internetAmount;
		                
		                console.log(">>amt123"+internetAmount+' ==> '+totalAmount);
		                
		                var cloned  = $(totTable).children("tfoot").find("tr[use=rowCloneable]").first().clone();   
		                                
		                $(cloned).attr("use","rowCloned");
		                $(cloned).find("span[use=invTitle]").text("Internet Handling Charge");
		                $(cloned).find("span[use=amount]").text((internetAmount).toFixed(2));
		                $(cloned).show();
		                $(totTable).children("tbody").append(cloned);
		            }
		        }
		        
		    });
		    
		    totalAmount = Math.round(totalAmount,0);
		    
		    $(totTable).children("tfoot").find("span[use=totalAmount]").text((totalAmount).toFixed(2));
		    //$("div[use=totalAmount]").find("span[use=totalAmount]").text((totalAmount).toFixed(2));
		    //$("div[use=totalAmount]").find("span[use=totalAmount]").attr('theAmount',totalAmount);
		    //$("div[use=totalAmount]").show();
		    //$("table[use=totalAmountTable]").show();

		    $("div[use=registrationPayment] div[use=totalAmount]").find("span[use=totalAmount]").text((totalAmount).toFixed(2));
		    $("div[use=registrationPayment] div[use=totalAmount]").find("span[use=totalAmount]").attr('theAmount',totalAmount);
		    $("div[use=registrationPayment] div[use=totalAmount]").show();
		    $("div[use=registrationPayment] table[use=totalAmountTable]").show();

		    // workshop related work for profile by weavers end
		}
		</script>	
	<?
	}
?>
</div>
<?					
}

function profileWorkshopAdd($delegateId, $rowUserDetails, $invoiceList, $currentCutoffId)
{
	global $mycms, $cfg;
	
	$currentCutoffId 			= getTariffCutoffId();
	
	$conferenceInvoiceDetails 	= reset($invoiceList[$delegateId]['REGISTRATION']);	

	$conferenceDetails 			= reset($invoiceList[$delegateId]['REGISTRATION']);	
	
	$workshopSessionDetails 	= $invoiceList[$delegateId]['WORKSHOP'];
	$workshopPaymentStatus 		= "NO";
	$workshopSessionStatus 		= "0";		// paid					
	$workshoSessionTitle 		= "";
	
	
	
	if($workshopSessionDetails)
	{		
		// workshop related work by weavers start
		$slipIDs = array();	 // define as array	
		$existingWorkshopIds = array();
		$existingWorkShops = array();		

		// workshop related work by weavers end
		foreach($workshopSessionDetails as $key => $rowWorkshopCount)
		{							
			$rawData 		= $rowWorkshopCount['ROW_DETAIL'];
			$invoiceData 	= $rowWorkshopCount['INVOICE'];
			$slipData 		= $rowWorkshopCount['SLIP_DETAILS'];
			$paymentData 	= $rowWorkshopCount['SLIP_PAYMENT'];
			$UserData 		= $rowWorkshopCount['USER'];
			
			// workshop related work by weavers start
			$slipIDs[] = $slipData['id']; 
			$existingWorkshopIds[] = $rawData['id'];
			$existingWorkShops[] = $rawData['type']; 	

			// workshop related work by weavers end
			
			if($invoiceData['status']=='A')
			{
				$paymentStatus = $invoiceData['payment_status'];
				

				//if($rawData['type'] == 'POSTCONFERENCE')
				// workshop related work by weavers start
				if($rawData['type'] == 'MASTER CLASS' || $rawData['type'] == 'WORKSHOP')
				// workshop related work by weavers end
				{
					if($paymentStatus=='UNPAID')
					{
						$workshopSessionStatus = 'UNPAID';	
					}
					else if($paymentStatus=='PAID')
					{
						$workshopSessionStatus = 'PAID';	
						$workshoSessionTitle = $rowWorkshopSessionDetails['REG_DETAIL'];
					}								
				}
			}
		}					
	}
		
	if($workshopSessionStatus == 'PAID')
	{
		$workshopPaymentStatus = "YES";
	}
?>
<div class="menu-center-section col-xs-12" style="display:none" operationMode="profileData" operationData="workshopAdd">
<?	
	if($workshopSessionStatus == 'UNPAID')
	{
	?>
	<h5>
		Previous Workshop Registration Incomplete.
		<i class="fas fa-backward pull-right" style="cursor:pointer;" onClick="returnToSummaryBlock(this);"></i>
	</h5>
	<table class="table">
		<tr>
	<?
		if($currentCutoffId > 0)
		{
	?>
		<th>
			<!--<span><a title="Complete Payment" style="border: 1px solid #e27926; padding: 5px; color: #e27926; text-decoration:none;font-size:large; float:right;"  onclick="$('div[use=menuContent]').hide(); $('div[use=menuContent][divId=registration_workshop_invoice]').show();">Complete Now</a></span>-->
			<!-- workshop related from profile work by weavers start -->
			<span><a title="Complete Payment" style="background-color: #005e82;padding: 5px; color: #ffffff; text-decoration:none;font-size:large; float:right;cursor: pointer;"  onclick="openProfileDataBlock(this,'workshopDetails');">Go Back To Workshop Details And Complete Now</a></span>
			<!-- // workshop related from profile work by weavers end -->
			
		</th>									
	<?
		}
		else
		{
	?>
		<th>
			<span style="margin:3px; color:#0000FF; font-size:smaller">Online Registration is closed. <br />Please Contact the Conference Secretariat for further assistance </span>
		</th>		
	<?
		}
	?>
		</tr>
	</table>
	<?
	}
	else
	{						
	?>	
		<h5 sequenceNo='1'> 
		Register to Workshop
		<i class="fas fa-backward pull-right" style="cursor:pointer;" onClick="returnToSummaryBlock(this);"></i>
		</h5>
		<form name="frmAddWorkshopfromProfile" id="frmAddWorkshopfromProfile" action="<?=$cfg['BASE_URL']?>registration.process.php" method="post">
			<input type="hidden" name="act" value="add_workshop" />
			<input type="hidden" id="cutoff_id" name="cutoff_id" value="<?=$currentCutoffId?>" cutoffid="<?=$currentCutoffId?>" />
			<input type="hidden" name="delegateClasfId" value="<?=$rowUserDetails['registration_classification_id']?>" />								
			<input type="hidden" id="registrationRequest" name="registrationRequest" value="<?=$rowUserDetails['registration_request']?>" />
										
			<!--<input type="hidden" name="workshopAmount" id="workshopAmount" value="<?=($registration_tariff_cutoff_id==1)?'3000':'5000'?>" />-->
			<div class="" actAs='fieldContainer'>
			<table class="table">
				<? 
				if(false && $workshopSessionStatus=='PAID')
				{

				?>
				<tr >
					<td width="75%" style="padding-left:50px;">
						  <?=$workshoSessionTitle;?>
					</td>
					<td style="color:#00a200;" >
						Registered
					</td>
				</tr>
				<?
				}
				else
				{
					$currentCutoffId 			= getTariffCutoffId();	
					
					$conferenceTariffArray   	= getAllRegistrationTariffs($currentCutoffId);
					$workshopDetDisplay 	 	= getAllWorkshopTariffs($currentCutoffId);

					
					$condition = '';
					if(count($existingWorkShops) > 0)
					{
						$condition .= 'AND type NOT IN("' . implode('", "', $existingWorkShops) . '")';
					}

					$sqlWorkshop = array();
					$sqlWorkshop['QUERY'] = "SELECT id,type
											 	FROM  "._DB_WORKSHOP_CLASSIFICATION_." 
												WHERE `status` = ? ".$condition;
					$sqlWorkshop['PARAM'][]   = array('FILD' => 'status',  'DATA' => 'A', 'TYP' => 's');

					$resWorkshop = $mycms->sql_select($sqlWorkshop,false);

					$availabel_workshop_ids = array_map(function($item) {
					    return $item['id'];
					}, $resWorkshop);
					
					$workshopCountArr 		 	= totalWorkshopCountReport();	
					
					$workshopDisplay			= array();
					$workshoDefinedDate = '';
					foreach($workshopDetDisplay as $workshopClassfId=>$workshopDet)
					{

						
						// workshop related work by weavers start (remove workshops from list which are already registered, only condition added)
						//if(!in_array($workshopClassfId, $existingWorkshopIds)){
						
						if(in_array($workshopClassfId, $availabel_workshop_ids)){
							$theWorkshop = $workshopDet[$rowUserDetails['registration_classification_id']];
							$workshopDisplay[$theWorkshop['WORKSHOP_TYPE']][$workshopClassfId] = $workshopDet;	

							//echo '<pre>'; print_r($workshopDet[$workshopClassfId]['WORKSHOP_DATE']);
							 $workshoDefinedDate = $workshopDet[$workshopClassfId]['WORKSHOP_DATE'];
						}
						// workshop related work by weavers end (remove workshops from list which are already registered, only condition added)
					}
					

					if(!empty($workshopDisplay))
					{	
						
						//$workshopDetailsArray = $workshopDisplay['POST-CONFERENCE'];	
						// workshop related work by weavers start
						if(isset($workshopDisplay['MASTER CLASS']) && sizeof($workshopDisplay['MASTER CLASS']) > 0)
						{
							$workshopDetailsArray = $workshopDisplay['MASTER CLASS'];

					?>	
						<tr>
							<td colspan="2" style="text-align:center;">
								<div class="container-box masterClass" use="accordianL1TriggerDiv" useSpec1="stage2">
									<i><b>WORKSHOP (<?=$workshoDefinedDate?>)</b></i>
								</div>
							</td>
						</tr>
						<?	
						// workshop related work by weavers end
							foreach($workshopDetailsArray as $keyWorkshopclsf=>$rowWorkshopclsf)
							{	
								foreach($rowWorkshopclsf as $keyRegClasf=>$rowRegClasf)
								{
									if($keyRegClasf==$rowUserDetails['registration_classification_id'])
									{
										//$workshopCount = $workshopCountArr[$keyWorkshopclsf]['TOTAL_LEFT_SIT'];
										$workshopCount = getWorkshopClassificationSeatLimit($rowRegClasf['WORKSHOP_ID']);
									
										if($workshopCount<1)
										{
											 $style = 'disabled="disabled"';
											 $span	= '<span class="tooltiptext">No More Seat Available For This Workshop</span>';
											 $spanCss = 'style="cursor:not-allowed;"';
										}
										else
										{
											$style = '';
											$span	= '';
											$spanCss = '';
										} 
										
										$workshopRateDisplay = $rowRegClasf['CURRENCY'].'&nbsp'.$rowRegClasf[$rowRegClasf['CURRENCY']];
										if($rowRegClasf[$rowRegClasf['CURRENCY']]==0 && $rowRegClasf['WORKSHOP_ID']!=5)
										{
											$workshopRateDisplay = "Included in Registration";
										}
										else if( $rowRegClasf['WORKSHOP_ID'] == 5)
										{
											$workshopRateDisplay = "";
										}

										// disable workshop if tariff amount is 0 and user registration classification type is not member by weavers start

										if($rowUserDetails['registration_classification_id'] != 1 && $rowRegClasf[$rowRegClasf['CURRENCY']] == 0)
										{
											$disable_radio = 'disabled="disabled"';
											$disable_label = 'cursor: not-allowed;';
										}
										else
										{
											$disable_radio = '';
											$disable_label = '';
										}

										// disable workshop if tariff amount is 0 and user registration classification type is not member by weavers end										
							?>
										<tr>
											<td>
												<div class="radio" style="border: 0px solid #2cb8f4; border-radius: 0px; background: transparent; padding: 0px 0px; box-shadow: none; margin-top: 0;">
													<label class="container-box" style="width: 100%; float: left; <?=$disable_label?>">
														<?=$rowRegClasf['WORKSHOP_NAME']?>
														<input type="checkbox" name="workshop_id[]" id="workshop_id_<?=$keyWorkshopclsf.'_'.$keyRegClasf?>"  
															   value="<?=$rowRegClasf['WORKSHOP_ID']?>" <?=$style?>  workshopName="<?=$rowRegClasf['WORKSHOP_NAME']?>" operationMode="workshopId"  amount="<?=$rowRegClasf[$rowRegClasf['CURRENCY']]?>" currency="<?=$rowRegClasf['CURRENCY']?>" registrationClassfId="<?=$keyRegClasf?>" invoiceTitle="<?=$rowRegClasf['WORKSHOP_NAME']?>" <?=$disable_radio?> />
														<span class="checkmark" <?=$spanCss?>></span>
													 </label>
												</div>
											</td>
											<td align="right"><?=$workshopRateDisplay?></td>
										</tr>
							<?
									}
								}
							}
						}
						// workshop related work by weavers start
						if (isset($workshopDisplay['WORKSHOP']) && sizeof($workshopDisplay['WORKSHOP']) > 0) 
						{
							$workshopDetailsArray = $workshopDisplay['WORKSHOP'];
						?>
						<tr>
							<td colspan="2" style="text-align:center;">
								<div class="container-box masterClass" use="accordianL1TriggerDiv" useSpec1="stage2">
									<i><b>WORKSHOP (<?=$workshoDefinedDate?>)</b></i>
								</div>
							</td>
						</tr>
						<?	
							foreach($workshopDetailsArray as $keyWorkshopclsf=>$rowWorkshopclsf)
							{	
								foreach($rowWorkshopclsf as $keyRegClasf=>$rowRegClasf)
								{
									if($keyRegClasf==$rowUserDetails['registration_classification_id'])
									{
										//$workshopCount = $workshopCountArr[$keyWorkshopclsf]['TOTAL_LEFT_SIT'];
										$workshopCount = getWorkshopClassificationSeatLimit($rowRegClasf['WORKSHOP_ID']);
									
										if($workshopCount<1)
										{
											 $style = 'disabled="disabled"';
											 $span	= '<span class="tooltiptext">No More Seat Available For This Workshop</span>';
										}
										else
										{
											$style = '';
											$span	= '';
										} 
										
										$workshopRateDisplay = $rowRegClasf['CURRENCY'].'&nbsp'.$rowRegClasf[$rowRegClasf['CURRENCY']];
										if($rowRegClasf[$rowRegClasf['CURRENCY']]==0 && $rowRegClasf['WORKSHOP_ID']!=5)
										{
											$workshopRateDisplay = "Included in Registration";
										}
										else if( $rowRegClasf['WORKSHOP_ID'] == 5)
										{
											$workshopRateDisplay = "";
										}

										// disable workshop if tariff amount is 0 and user registration classification type is not member by weavers start

										if($rowUserDetails['registration_classification_id'] != 1 && $rowRegClasf[$rowRegClasf['CURRENCY']] == 0)
										{
											$disable_radio = 'disabled="disabled"';
											$disable_label = 'cursor: not-allowed;';
										}
										else
										{
											$disable_radio = '';
											$disable_label = '';
										}

										// disable workshop if tariff amount is 0 and user registration classification type is not member by weavers end

							?>
										<tr>
											<td>
												<div class="radio" style="border: 0px solid #2cb8f4; border-radius: 0px; background: transparent; padding: 0px 0px; box-shadow: none; margin-top: 0;">
													<label class="container-box" style="width: 100%; float: left; <?=$disable_label?>">
														<?=$rowRegClasf['WORKSHOP_NAME']?>
														<input type="checkbox" name="workshop_id[]" id="workshop_id_<?=$keyWorkshopclsf.'_'.$keyRegClasf?>"  
															   value="<?=$rowRegClasf['WORKSHOP_ID']?>" <?=$style?>  workshopName="<?=$rowRegClasf['WORKSHOP_NAME']?>"operationMode="workshopId_nov"  amount="<?=$rowRegClasf[$rowRegClasf['CURRENCY']]?>" currency="<?=$rowRegClasf['CURRENCY']?>" registrationClassfId="<?=$keyRegClasf?>" invoiceTitle="<?=$rowRegClasf['WORKSHOP_NAME']?>" <?=$disable_radio?>/>
														<span class="checkmark"></span>
													 </label>
												</div>
											</td>
											<td align="right"><?=$workshopRateDisplay?></td>
										</tr>
							<?
									}
								}
							}	
						}
						// workshop related work by weavers end
					}
				}
				?>
			</table>
				<!-- // workshop related from profile work by weavers start -->
				<div class="alert alert-danger" callFor='alert' style="margin-top: 10px;">Please choose a proper option.</div>
				<div class="text-center pull-right" style="margin-top:10px; display:none;">
					<button type="button" class="submit" sequenceNo='1' use='nextButton' onclick="showPaymentSection(this)">Next</button>
				</div>
				<!-- // workshop related from profile work by weavers end -->
			</div>	
			
			<?
			//if($workshopPaymentStatus == "NO")
			//{
			?>
			<!-- // workshop related from profile work by weavers start -->
			<div use="registrationPaymentOption" style="display:none;" class="rightPanel_payment">
                <div class="link" use="rightAccordianL1TriggerDiv">PAYMENT OPTION</div>
                <ul class="submenu" style="display: none">
                    <li>
						<div class="col-xs-12 form-group" actAs='fieldContainer'>
							<div class="radio">
								<label class="select-lable" >Payment Options</label>
								<label class="container-box">
									CREDIT / DEBIT CARD / ONLINE PAYMENT
									<input type="radio" name="registrationMode" value="ONLINE" operationMode="registrationMode" use='tariffPaymentMode' for="CC">
									<span class="checkmark"></span>
								</label>
								<div class="rightPanel_payment_CC" style="display:none; padding-left:20px;" for="CC" use='payRules'>
									Payment will be accepted only through VISA <img src="<?=_BASE_URL_?>images/visa_card_cion.png">  
									MASTER CARD <img src="<?=_BASE_URL_?>images/master_card_cion.png">
									RuPay <img src="<?=_BASE_URL_?>images/rupay_card_icon.png">
									Maestro <img src="<?=_BASE_URL_?>images/maestro_icon.png">
									MasterCard SecureCode <img src="<?=_BASE_URL_?>images/master_secured_icon.png">
									<!-- Diners Club International <img src="<?=_BASE_URL_?>images/dinner_club_icon.png"> -->
									<!-- American Express Card <img src="<?=_BASE_URL_?>images/american_express_card_cion.png"><br> -->
									For any query, please write at <?=$cfg['EMAIL_CONF_EMAIL_US']?> or call at <?=$cfg['EMAIL_CONF_CONTACT_US']?><br><br> 
								</div>
								
								<label class="container-box">
									CHEQUE
									<input type="radio" name="registrationMode" value="OFFLINE" operationMode="registrationMode" use='tariffPaymentMode' for="CHQ">
									<span class="checkmark"></span>
								</label>
								<div class="rightPanel_payment_CHQ" style="display:none; padding-left:39px;" for="CHQ" use='payRules'>
									<?=$cfg['cheque_info']?>
								</div>
								
								<label class="container-box">
									DEMAND DRAFT
									<input type="radio" name="registrationMode" value="OFFLINE" operationMode="registrationMode" use='tariffPaymentMode' for="DD">
									<span class="checkmark"></span>
								</label>
								<div class="rightPanel_payment_DD" style="display:none; padding-left:39px;" for="DD" use='payRules'>
									<?=$cfg['draft_info']?>													
								</div>														
												
								<label class="container-box">
									NEFT / RTGS / BANK TRANSFER / NET BANKING
									<input type="radio" name="registrationMode" value="OFFLINE" operationMode="registrationMode" use='tariffPaymentMode' for="WIRE">
									<span class="checkmark"></span>
								</label>
								<div class="rightPanel_payment_NEFT" style="display:none; padding-left:39px;" for="WIRE" use='payRules'>															
									<?=$cfg['neft_info']?>
								</div>
												
								<label class="container-box">
									CASH PAYMENT
									<input type="radio" name="registrationMode" value="OFFLINE" operationMode="registrationMode" use='tariffPaymentMode' for="CASH">
									<span class="checkmark"></span>
								</label>
								<div class="rightPanel_payment_CASH" style="display:none; padding-left:39px;" for="CASH" use='payRules'>															
									<?=$cfg['cash_info']?>
								</div>	
							</div>
							<div class = "alert alert-danger" callFor='alert'>Please choose a proper option.</div>
						</div>  
						<div class=" col-xs-2 text-center pull-right">
							<button type="button" class="submit" use='nextButton'>Next</button>
						</div>          
						<div class="clearfix"></div>
                    </li>
                </ul>
            </div>

			<div use="registrationPayment" id="registrationPaymentWorkshop" style="display:none;" class="rightPanel_payment">
                
                <h5 sequenceNo='2'>
					<i class="fas fa-backward pull-right" style="cursor:pointer;" onClick="returnToSummaryBlock(this,'workshopAdd');"></i>
				</h5>
				<div class="col-xs-12 form-group">	
					<i class="pull-left"><b>NOTE:</b> Rates are inclusive of all Taxes.</i>
				</div> 
				<div use="totalAmount" class="col-xs-12 totalAmount pull-right" style="padding: 0;  display:none;">
					<table class="table bill" use="totalAmountTable">
						<thead>
							<tr>
								<th>DETAIL</th>
								<th align="right" style="text-align:right;">AMOUNT (INR)</th>
							</tr>
						</thead>
						<tbody></tbody>
						<tfoot>
							<tr style="display:none;" use='rowCloneable'>
								<td>&bull; &nbsp <span use="invTitle">Something</span></td>
								<td align="right"><span use="amount">0.00</span></td>
							</tr>
							<tr>
								<td>Total</td>
								<td align="right"><span use='totalAmount'>0.00</span></td>
							</tr>
						</tfoot>
					</table>
				</div>
									
				<div class="col-xs-12 form-group" use="offlinePaymentOptionChoice" actAs='fieldContainer'>
					<div class="checkbox">
						<label class="select-lable" >Payment Option</label>
						<div>
							<label class="container-box" style="float:left; margin-right:30px;">Online Payment
							  <input type="radio" name="payment_mode" use="payment_mode_select" value="Card" for="Card" paymentMode='ONLINE'>
							  <span class="checkmark"></span>
							</label>
							<?php
								$offline_payments = json_decode($cfg['PAYMENT.METHOD']);

			                    if (in_array("Cheque", $offline_payments))
			                    {
			                    ?>
									<label class="container-box" style="float:left; margin-right:30px;">Cheque
									  <input type="radio" name="payment_mode" use="payment_mode_select" value="Cheque" for="Cheque" paymentMode='OFFLINE' profileFrom="<?=$id;?>">
									  <span class="checkmark"></span>
									</label>
								<?php
				                   }
				                   if (in_array("Draft", $offline_payments))
				                   {
				                   ?> 
									<label class="container-box" style="float:left; margin-right:30px;">Draft
									  <input type="radio" name="payment_mode" use="payment_mode_select" value="Draft" for="Draft" paymentMode='OFFLINE' profileFrom="<?=$id;?>">
									  <span class="checkmark"></span>
									</label>
								<?php
				                   }
				                   if (in_array("Neft", $offline_payments))
				                   {
				                   ?>	
									<label class="container-box" style="float:left; margin-right:30px;">NEFT
									  <input type="radio" name="payment_mode" use="payment_mode_select" value="NEFT" for="NEFT" paymentMode='OFFLINE' profileFrom="<?=$id;?>">
									  <span class="checkmark"></span>
									</label>
								 <?php
			                    }
			                   if (in_array("Rtgs", $offline_payments))
			                   {
			                    ?>
									<label class="container-box" style="float:left; margin-right:30px;">RTGS
									  <input type="radio" name="payment_mode" use="payment_mode_select" value="RTGS" for="RTGS" paymentMode='OFFLINE' profileFrom="<?=$id;?>">
									  <span class="checkmark"></span>
									</label>
							  <?php
			                   }
			                   if (in_array("Cash", $offline_payments))
			                   {
			                   ?> 	
									<label class="container-box" style="float:left; margin-right:30px;">Cash
									  <input type="radio" name="payment_mode" use="payment_mode_select" value="Cash" for="Cash" paymentMode='OFFLINE' profileFrom="<?=$id;?>">
									  <span class="checkmark"></span>
									</label>
							   <?php
			                   }
			                   if (in_array("Upi", $offline_payments))
			                   {
			                   ?> 	
								<label class="container-box" style="float:left; margin-right:30px;">UPI
								  <input type="radio" name="payment_mode" use="payment_mode_select" value="UPI" for="UPI" paymentMode='OFFLINE' profileFrom="<?=$id;?>">
								  <span class="checkmark"></span>
								</label>
							  <?php
							  }
							  ?>
							&nbsp;
						</div>																				
					</div>
					<div class = "alert alert-danger" callFor='alert'>Please choose a proper option.</div>
				</div>
				<? /* // commented as per feedback on 09.09.2022
				<div class="col-xs-12 form-group" style="display:none;" use="offlinePaymentOption" for="Card" actAs='fieldContainer'>
					<div class="checkbox">
						<label class="select-lable" >Card Type</label>
						<div>
							<!-- <label class="container-box" style="float:left; margin-right:30px;">
							  <img src="<?=_BASE_URL_?>images/international_globe.png" height="20px;">
							  International Card
							  <input type="radio" name="card_mode" use="card_mode_select" value="International">
							  <span class="checkmark"></span>											   
							</label> -->
							<label class="container-box" style="float:left; margin-right:30px;">
							  <img src="<?=_BASE_URL_?>images/india_globe.png" height="20px;">
							  Indian Card
							  <input type="radio" name="card_mode" use="card_mode_select" value="Indian">
							  <span class="checkmark"></span>
							</label>
							&nbsp;
						</div>																				
					</div>
					<div class = "alert alert-danger" callFor='alert'>Please choose a proper option.</div>
				</div>
				*/ ?>
				<div class="col-xs-12 form-group input-material" style="display:none;" use="offlinePaymentOption" for="Card">
					Payment will be accepted only through VISA <img src="<?=_BASE_URL_?>images/visa_card_cion.png">  
					MASTER CARD <img src="<?=_BASE_URL_?>images/master_card_cion.png">
					RuPay <img src="<?=_BASE_URL_?>images/rupay_card_icon.png">
					Maestro <img src="<?=_BASE_URL_?>images/maestro_icon.png">
					MasterCard SecureCode <img src="<?=_BASE_URL_?>images/master_secured_icon.png">
					<!-- Diners Club International <img src="<?=_BASE_URL_?>images/dinner_club_icon.png"> -->
					<!-- American Express Card <img src="<?=_BASE_URL_?>images/american_express_card_cion.png"> --><br>
					For any query, please write at <?=$cfg['EMAIL_CONF_EMAIL_US']?> or call at <?=$cfg['EMAIL_CONF_CONTACT_US']?><br><br> 
				</div>
				
				<div class="col-xs-6 form-group input-material" style="display:none;" use="offlinePaymentOption" for="Cash" actAs='fieldContainer'>
					<label for="user_first_name">Money Order Sent Date</label>
					<input type="date" class="form-control" name="cash_deposit_date" id="cash_deposit_date" max="<?=$mycms->cDate("Y-m-d")?>" min="<?=$mycms->cDate("Y-m-d","-6 Months")?>">
					<div class = "alert alert-danger" callFor='alert'>Please choose a proper option.</div>
				</div>
				<div class="col-xs-12 form-group input-material" style="display:none;" use="offlinePaymentOption" for="Cash">
					<?=$cfg['cash_info']?>
				</div>
				<!-- UPI Payment Option Added By Weavers start -->

				<div class="col-xs-12 form-group input-material">
				    <div class="col-xs-6 form-group input-material" style="display:none;" use="offlinePaymentOption" for="UPI" actAs='fieldContainer'>

				        <label for="txn_no">UPI Transaction ID</label>
				        <input type="text" class="form-control" name="txn_no" id="txn_no">
				        <div class = "alert alert-danger" callFor='alert'>Please choose a proper option.</div>
				    </div>
				    <div class="col-xs-6 form-group input-material" style="display:none;" use="offlinePaymentOption" for="UPI" actAs='fieldContainer'>
				        <label for="txn_no">UPI Payment Date</label>
				        <input type="date" class="form-control" name="upi_date" id="upi_date" max="<?=$mycms->cDate("Y-m-d")?>" min="<?=$mycms->cDate("Y-m-d","-6 Months")?>">
				        <div class = "alert alert-danger" callFor='alert'>Please choose a proper option.</div>
				    </div> 
				</div>  
				<!-- UPI Payment Option Added By Weavers end -->
			
				<div class="col-xs-12 form-group input-material" style="display:none;" use="offlinePaymentOption" for="Cheque" actAs='fieldContainer'>
					<label for="user_first_name">Cheque No</label>
					<input type="text" class="form-control" name="cheque_number" id="cheque_number">
					<div class = "alert alert-danger" callFor='alert'>Please enter a proper value.</div>
				</div>
				<div class="col-xs-6 form-group input-material" style="display:none;" use="offlinePaymentOption" for="Cheque" actAs='fieldContainer'>
					<label for="user_first_name">Drawee Bank</label>
					<input type="text" class="form-control" name="cheque_drawn_bank" id="cheque_drawn_bank">
					<div class = "alert alert-danger" callFor='alert'>Please enter a proper value.</div>
				</div>
				<div class="col-xs-6 form-group input-material" style="display:none;" use="offlinePaymentOption" for="Cheque" actAs='fieldContainer'>
					<label for="user_first_name">Cheque Date</label>
					<input type="date" class="form-control" name="cheque_date" id="cheque_date" max="<?=$mycms->cDate("Y-m-d")?>" min="<?=$mycms->cDate("Y-m-d","-6 Months")?>">
					<div class = "alert alert-danger" callFor='alert'>Please choose a proper option.</div>
				</div>
				<div class="col-xs-12 form-group input-material" style="display:none;" use="offlinePaymentOption" for="Cheque">
					<?=$cfg['cheque_info']?>
				</div>
			
				<div class="col-xs-12 form-group input-material" style="display:none;" use="offlinePaymentOption" for="Draft" actAs='fieldContainer'>
					<label for="user_first_name">Draft No</label>
					<input type="text" class="form-control" name="draft_number" id="draft_number">
					<div class = "alert alert-danger" callFor='alert'>Please enter a proper value.</div>
				</div>
				<div class="col-xs-6 form-group input-material" style="display:none;" use="offlinePaymentOption" for="Draft" actAs='fieldContainer'>
					<label for="user_first_name">Drawee Bank</label>
					<input type="text" class="form-control" name="draft_drawn_bank" id="draft_drawn_bank">
					<div class = "alert alert-danger" callFor='alert'>Please enter a proper value.</div>
				</div>
				<div class="col-xs-6 form-group input-material" style="display:none;" use="offlinePaymentOption" for="Draft" actAs='fieldContainer'>
					<label for="user_first_name">Draft Date</label>
					<input type="date" class="form-control" name="draft_date" id="draft_date" max="<?=$mycms->cDate("Y-m-d")?>" min="<?=$mycms->cDate("Y-m-d","-6 Months")?>">
					<div class = "alert alert-danger" callFor='alert'>Please choose a proper option.</div>
				</div>
				<div class="col-xs-12 form-group input-material" style="display:none;" use="offlinePaymentOption" for="Draft">
					<?=$cfg['draft_info']?>	
				</div>
			
				<div class="col-xs-12 form-group input-material" style="display:none;" use="offlinePaymentOption" for="NEFT" actAs='fieldContainer'>
					<label for="user_first_name">Transaction Id</label>
					<input type="text" class="form-control" name="neft_transaction_no" id="neft_transaction_no">
					<div class = "alert alert-danger" callFor='alert'>Please enter a proper value.</div>
				</div>
				<div class="col-xs-6 form-group input-material" style="display:none;" use="offlinePaymentOption" for="NEFT" actAs='fieldContainer'>
					<label for="user_first_name">Drawee Bank</label>
					<input type="text" class="form-control" name="neft_bank_name" id="neft_bank_name">
					<div class = "alert alert-danger" callFor='alert'>Please enter a proper value.</div>
				</div>
				<div class="col-xs-6 form-group input-material" style="display:none;" use="offlinePaymentOption" for="NEFT" actAs='fieldContainer'>
					<label for="user_first_name">Date</label>
					<input type="date" class="form-control" name="neft_date" id="neft_date" max="<?=$mycms->cDate("Y-m-d")?>" min="<?=$mycms->cDate("Y-m-d","-6 Months")?>">
					<div class = "alert alert-danger" callFor='alert'>Please choose a proper option.</div>
				</div>
				<div class="col-xs-12 form-group input-material" style="display:none;" use="offlinePaymentOption" for="NEFT">
					<?=$cfg['neft_info']?>
				</div>
				<div class="col-xs-12 form-group input-material" style="display:none;" use="offlinePaymentOption" for="UPI">
				   <?=$cfg['upi_info']?>
				</div>
			
				<div class="col-xs-12 form-group input-material" style="display:none;" use="offlinePaymentOption" for="RTGS" actAs='fieldContainer'>
					<label for="user_first_name">Transaction Id</label>
					<input type="text" class="form-control" name="rtgs_transaction_no" id="rtgs_transaction_no">
					<div class = "alert alert-danger" callFor='alert'>Please enter a proper value.</div>
				</div>
				<div class="col-xs-6 form-group input-material" style="display:none;" use="offlinePaymentOption" for="RTGS" actAs='fieldContainer'>
					<label for="user_first_name">Drawee Bank</label>
					<input type="text" class="form-control" name="rtgs_bank_name" id="rtgs_bank_name">
					<div class = "alert alert-danger" callFor='alert'>Please enter a proper value.</div>
				</div>
				<div class="col-xs-6 form-group input-material" style="display:none;" use="offlinePaymentOption" for="RTGS" actAs='fieldContainer'>
					<label for="user_first_name">Date</label>
					<input type="date" class="form-control" name="rtgs_date" id="rtgs_date" max="<?=$mycms->cDate("Y-m-d")?>" min="<?=$mycms->cDate("Y-m-d","-6 Months")?>">
					<div class = "alert alert-danger" callFor='alert'>Please choose a proper option.</div>
				</div>
				<div class="col-xs-12 form-group input-material" style="display:none;" use="offlinePaymentOption" for="RTGS">
					<?=$cfg['neft_info']?>
				</div>
			
				<div class="col-xs-12 form-group" use="conditionOption" actAs='fieldContainer'>
					<div class="checkbox">
						<label class="container-box"> 
							By Clicking, you hereby agree to receive all promotional SMS and e-mails related to ISAR 24. To unsubscribe, send us a mail at isarbbsr2024@gmail.com.
							<input type="checkbox" name="acceptance1" value="acceptance1" required>
							<span class="checkmark"></span>
						</label>
					</div>
					<div class = "alert alert-danger" callFor='alert'>Please choose option.</div>	
					<div class="checkbox">
						<label class="container-box">
							I have read and clearly understood the 
							<a href="<?=_BASE_URL_?>terms.php" title="Click to View 'Terms &amp; Conditions'" target="_blank" class="anclink">Terms &amp; Conditions</a> 
							and 
							<a href="<?=_BASE_URL_?>cancellation.php" title="Click to View 'Cancellation &amp; Refund Policy'" target="_blank" class="anclink">Cancellation &amp; Refund Policy</a> 
							and agree with the same. 
							<input type="checkbox" name="acceptance2" value="acceptance2" required>
							<span class="checkmark"></span>
						</label>										
					</div>
					<div class = "alert alert-danger" callFor='alert'>Please choose option.</div>
				</div> 
			 
				<div class=" col-xs-12 text-center pull-right">
					<button type="submit" class="submit" sequenceNo='2' use='nextButton'>Proceed to Pay</button>
				</div>      
				<div class="clearfix"></div>
			</div>

			<div use="complimentrayWorkshop" style="display:none; margin-top: 15px;" class="rightPanel_payment">
				<div class=" col-xs-12 text-center pull-right">
					<button type="submit" class="submit" sequenceNo='3' use='nextButton'>Complete Registration Process</button>
				</div>      
				<div class="clearfix"></div>
			</div>

			<div use="registrationProcess" style="display:none; text-align:center;" class="rightPanel_payment">
				<img src="<?=_BASE_URL_?>images/PaymentPreloader.gif"/>
			</div>
			<!-- // workshop related from profile work by weavers end -->
			<?php /*?>
			<table class="table">
				<tr>
					<td width="40%">											
						Payment Mode <span style="color:#FF0000;">*</span>
					</td>
					<td>
						<input name="registrationMode" operationmode="registrationMode" id="registrationMode_online" value="ONLINE" type="radio" required > Online &nbsp;&nbsp;
						<input name="registrationMode" operationmode="registrationMode" id="registrationMode_offline" value="OFFLINE" type="radio" checked="checked"> Offline
					</td>
				</tr>
				<tr>
					<th align="right" style="text-decoration:none; background: rgb(204, 204, 204); border: 1px solid rgb(204, 204, 204);" colspan="2" >
						<div class="reg_total_amnt">
							Amount: <?=getRegistrationCurrency($delagateCatagory)?> <span id="ammount">0.00</span>
						</div>
					</th>
				</tr>
				<tr>
					<td></td>
					<td>
						<input type="submit" value="Next" class="btn btn-warning viobtn slow"  />
					</td>
				</tr>
			</table>
			<?php */?>
			
			<?
			//}
			?>
		</form>	
		<script>
			
		$(document).ready(function(){
			/*$("div[use=profileWorkshopAdd]").find("input[type=checkbox][operationMode=workshopId]").click(function(){
				
				$("div[use=profileWorkshopAdd]").find("input[type=checkbox][operationMode=workshopId]").prop("checked",false);
				$(this).prop("checked",true);
				
				calculateTotalWorkshoAmountInProfile();
			});

			$("form[name=frmAddWorkshopfromProfile]").submit(function(e){			
				var checkedWrkObj = $("div[use=profileWorkshopAdd]").find("input[type=checkbox][operationMode=workshopId]:checked");
				if($(checkedWrkObj).length == 0)
				{
					alert("Select a Workshop.");
					$("div[use=profileWorkshopAdd]").find("input[type=checkbox][operationMode=workshopId]").focus();
					e.preventDefault();
					return false;
				}
				
				var checkedWrkObj = $("div[use=profileWorkshopAdd]").find("input[type=radio][operationMode=registrationMode]:checked");
				if($(checkedWrkObj).length == 0)
				{
					alert("Select a Payment Mode.");
					$("div[use=profileWorkshopAdd]").find("input[type=radio][operationMode=registrationMode]").focus();
					e.preventDefault();
					return false;
				}
			});*/

			// workshop related work for profile by weavers start
			var novemberWorkshop = $("input[type=checkbox][operationMode=workshopId_nov]:checked").length;
			var decemberWorkshop = $("input[type=checkbox][operationMode=workshopId]:checked").length;
            if(novemberWorkshop > 0 || decemberWorkshop > 0){
				$('button[sequenceno=1]').parent().show();
			}else{
				$('button[sequenceno=1]').parent().hide();
			}


			$("input[type=checkbox][operationMode=workshopId]").each(function(){
                $(this).click(function(){   
                    
                    var currChkbxStatus = $(this).attr("chkStatus");

                    var novemberWorkshop = $("input[type=checkbox][operationMode=workshopId_nov]:checked").length;
					var decemberWorkshop = $("input[type=checkbox][operationMode=workshopId]:checked").length;
                    if(novemberWorkshop > 0 || decemberWorkshop > 0){
						$('button[sequenceno=1]').parent().show();
						popDownAlert();
					}else{
						$('button[sequenceno=1]').parent().hide();
					}
                    
                    $("input[type=checkbox][operationMode=workshopId]").prop("checked",false);  
                    $("input[type=checkbox][operationMode=workshopId]").attr("chkStatus","false");
                    
                    if(currChkbxStatus=="true")
                    {
                        $(this).prop("checked",false);  
                        $(this).attr("chkStatus","false");
                    }
                    else
                    {   
                        $(this).prop("checked",true);   
                        $(this).attr("chkStatus","true");   
                    }
                    
                    calculateTotalWorkshoAmountInProfile();
                });
            });

            
			$("input[type=checkbox][operationMode=workshopId_nov]").each(function(){
				$(this).click(function(){	
					
					var currChkbxStatus = $(this).attr("chkStatus");

					var novemberWorkshop = $("input[type=checkbox][operationMode=workshopId_nov]:checked").length;
					var decemberWorkshop = $("input[type=checkbox][operationMode=workshopId]:checked").length;
                    if(novemberWorkshop > 0 || decemberWorkshop > 0){
						$('button[sequenceno=1]').parent().show();
						popDownAlert();
					}else{
						$('button[sequenceno=1]').parent().hide();
					}
					
					$("input[type=checkbox][operationMode=workshopId_nov]").prop("checked",false);	
					$("input[type=checkbox][operationMode=workshopId_nov]").attr("chkStatus","false");
					
					if(currChkbxStatus=="true")
					{
						$(this).prop("checked",false);	
						$(this).attr("chkStatus","false");
					}
					else
					{	
						$(this).prop("checked",true);	
						$(this).attr("chkStatus","true");	
					}
					
					calculateTotalWorkshoAmountInProfile();
				});
			});
			// workshop related work for profile by weavers end
			
		});
		
		
		function calculateTotalWorkshoAmountInProfile()
		{	
			// workshop related work for profile by weavers start
			
			var totalAmount = 0;
		    var totTable    = $('div[use=registrationPayment]').find("table[use=totalAmountTable]");
		    $(totTable).children("tbody").find("tr").remove();

		    var novemberWorkshop = $("input[type=checkbox][operationMode=workshopId_nov]:checked").length;
			var decemberWorkshop = $("input[type=checkbox][operationMode=workshopId]:checked").length;
		    //$.each($("input[type=checkbox][operationMode=workshopId_nov]:checked,input[type=checkbox][operationMode=workshopId]:checked"),function(){
		    $.each($("div[operationData=workshopAdd] input[type=checkbox]:checked,div[operationData=workshopAdd] input[type=radio]:checked"),function(){
		        var attr = $(this).attr('amount');
		        
		        if (typeof attr !== typeof undefined && attr !== false) 
		        {
		            var amt     = parseFloat(attr); 
		            var gst_flag = $('#gst_flag').val();
				      
			            if(gst_flag==1)
	                    {
	                    	var cgstP = <?=$cfg['INT.CGST']?>;
	                    	var cgstAmnt = (amt * cgstP) / 100;

	                   		var sgstP = <?=$cfg['INT.SGST']?>;
	                    	var sgstAmnt = (amt * sgstP) / 100;

	                    	var totalGst = cgstAmnt + sgstAmnt;
	                    	var totalGstAmount = cgstAmnt + sgstAmnt + amt;
	                    	totalAmount = parseFloat(totalAmount) + parseFloat(totalGstAmount);
	                    }
	                    else
	                    {
	                    	totalAmount = parseFloat(totalAmount) + parseFloat(amt);
	                    }
		            
		            var attrReg = $(this).attr('operationMode');
		            
		            var isMastCls  = false;
		            if (typeof attrReg !== typeof undefined && attrReg !== false && attrReg === 'workshopId') 
		            {
		                isMastCls = true;
		            }

		            var isNovWorkshop = false;
		            if(typeof attrReg !== typeof undefined && attrReg !== false && attrReg === 'workshopId_nov')
		            {
		                isNovWorkshop = true;
		            }

		            var cloneIt = false;
		            var amtAlterTxt = 'Complimentary';
		           
		            if(amt > 0)
		            {
		                cloneIt = true;
		            }
		            else if(isMastCls || isNovWorkshop)
		            {
		                cloneIt = true;
		                amtAlterTxt = 'Included in Registration'
		            }
		            
		            if(cloneIt)
		            {                           
		                var cloned  = $(totTable).children("tfoot").find("tr[use=rowCloneable]").first().clone();                                                   
		                $(cloned).attr("use","rowCloned");
		                $(cloned).find("span[use=invTitle]").text($.trim($(this).attr('invoiceTitle')));                                    
		                $(cloned).find("span[use=amount]").text((amt>0)?(amt).toFixed(2):amtAlterTxt);
		                $(cloned).show();
		                $(totTable).children("tbody").append(cloned);
		            }
		            if(gst_flag==1)
                        {
                        	
	                        if (cloneIt) {

	                            var cgstP = <?=$cfg['INT.CGST']?>;
	                            var cgstAmnt = (amt * cgstP) / 100;

	                            var sgstP = <?=$cfg['INT.SGST']?>;
	                            var sgstAmnt = (amt * sgstP) / 100;

	                            var totalGst = cgstAmnt + sgstAmnt;
	                            var totalGstAmount = cgstAmnt + sgstAmnt + amt;


	                            var cloned = $(totTable).children("tfoot").find("tr[use=rowCloneable]").first()
	                                .clone();
	                            $(cloned).attr("use", "rowCloned");
	                            $(cloned).find("span[use=invTitle]").text("GST 18%");
	                            $(cloned).find("span[use=amount]").text((totalGst).toFixed(2));
	                            $(cloned).show();
	                            $(totTable).children("tbody").append(cloned);
	                        }
	                    }
		        }
		        //console.log($(this))
		        if($(this).attr('operationMode')=='registrationMode' && $(this).attr('use')=='tariffPaymentMode')
		        //if($(this).attr('paymentMode')=='ONLINE' && $(this).attr('use')=='payment_mode_select')
		        {
		            if($(this).val()=='ONLINE')
		            //if($(this).val()=='Card')
		            {
		                var internetHandling = <?=$cfg['INTERNET.HANDLING.PERCENTAGE']?>;
		                var internetAmount = (totalAmount*internetHandling)/100;
		                totalAmount = totalAmount+internetAmount;
		                
		                console.log(">>amt123"+internetAmount+' ==> '+totalAmount);
		                
		                var cloned  = $(totTable).children("tfoot").find("tr[use=rowCloneable]").first().clone();   
		                                
		                $(cloned).attr("use","rowCloned");
		                $(cloned).find("span[use=invTitle]").text("Internet Handling Charge");
		                $(cloned).find("span[use=amount]").text((internetAmount).toFixed(2));
		                $(cloned).show();
		                $(totTable).children("tbody").append(cloned);
		            }
		        }
		        
		    });
		    
		    totalAmount = Math.round(totalAmount,0);
		    
		    $(totTable).children("tfoot").find("span[use=totalAmount]").text((totalAmount).toFixed(2));
		    //$("div[use=totalAmount]").find("span[use=totalAmount]").text((totalAmount).toFixed(2));
		    //$("div[use=totalAmount]").find("span[use=totalAmount]").attr('theAmount',totalAmount);
		    //$("div[use=totalAmount]").show();
		    //$("table[use=totalAmountTable]").show();

		    $("div[use=registrationPayment] div[use=totalAmount]").find("span[use=totalAmount]").text((totalAmount).toFixed(2));
		    $("div[use=registrationPayment] div[use=totalAmount]").find("span[use=totalAmount]").attr('theAmount',totalAmount);
		    $("div[use=registrationPayment] div[use=totalAmount]").show();
		    $("div[use=registrationPayment] table[use=totalAmountTable]").show();

		    // workshop related work for profile by weavers end
		}
		</script>	
	<?
	}
?>
</div>
<?					
}


// ACCOMPANY DETAILS
function profileAccompanyDetails($delegateId, $rowUserDetails, $invoiceList, $currentCutoffId)
{
	global $mycms, $cfg;
	
	$conferenceInvoiceDetails = reset($invoiceList[$delegateId]['REGISTRATION']);
	$accompanyDtlsArr 			= $invoiceList[$delegateId]['ACCOMPANY'];
	
	if(count($accompanyDtlsArr) > 0){
?>
	<div class="menu-center-section col-xs-12" style="display:none" operationMode="profileData" operationData="accompanyDetails">
		<h5>
		Accompany Registration Details of <?=$invoiceList[$delegateId]['REGISTRATION'][$delegateId]['USER']['user_full_name']?>
		<i class="fas fa-backward pull-right" style="cursor:pointer;" onClick="returnToSummaryBlock(this);"></i>
		</h5>
		<table class="table table-bordered">
			<tbody>
				<? foreach ($accompanyDtlsArr as $key => $value) { 
					
					$accompany_full_name = $value['ROW_DETAIL']['user_full_name'];
					$accompany_registration_id = $value['ROW_DETAIL']['user_registration_id'];
					$accompany_food_preference = $value['ROW_DETAIL']['user_food_preference'];
					$accompany_registration_date = $value['ROW_DETAIL']['conf_reg_date'];
					$accompany_invoice_status = $value['INVOICE']['status'];
					$accompany_payment_status = $value['INVOICE']['payment_status'];
					if($accompany_invoice_status == 'A' && $accompany_payment_status == 'PAID'){
				?>
						<tr>
							<td class="" width="30%" style="color:#308b64;">Registered</td>
							<td class="" style="color:#308b64;">Date: <?=$mycms->cDate('d/m/Y', $accompany_registration_date)?></td>
						</tr>
						<tr>
							<td>Accompany Name</td>
							<td><?=$accompany_full_name?></td>
						</tr>
						<tr>
							<td>Accompany Registration ID</td>
							<td><?=$accompany_registration_id?></td>
						</tr>
						<tr>
							<td>Accompany Food Preference</td>
							<td><?=$accompany_food_preference?></td>
						</tr>
				<? } 
				} ?>
				
			</tbody>
		</table>
	</div>
<?php
	}
}

// ACCOMPANY ADD START

function profileAccompanyAdd($delegateId, $rowUserDetails, $invoiceList, $currentCutoffId)
{
    $conferenceTariffArray   = getAllRegistrationTariffs($currentCutoffId);
    $accompanyCatagory      = 1; // accompany persion registration fees set to the cutoff value of 'Member' registration classification type 
    //$registrationAmount   = $conferenceTariffArray[$accompanyCatagory]['AMOUNT'];
    $registrationAmount     = getCutoffTariffAmnt($currentCutoffId);
    $registrationCurrency   = $conferenceTariffArray[$accompanyCatagory]['CURRENCY'];
?>
	<div class="menu-center-section col-xs-12" style="display:none" operationMode="profileData"
	    operationData="accompanyAdd">
	    <h5>
	        Add Accompany
	        <i class="fas fa-backward pull-right" style="cursor:pointer;" onClick="returnToSummaryBlock(this);"></i>
	    </h5>
	    <form name="frmAddAccompanyfromProfile" id="frmAddAccompanyfromProfile"
	        action="<?=$cfg['BASE_URL']?>registration.process.php" method="post">

	        <input type="hidden" id="cutoff_id" name="cutoff_id" value="<?=$currentCutoffId?>"
	            cutoffid="<?=$currentCutoffId?>" />
	        <input type="hidden" name="accompanyClasfId" value="<?=$accompanyCatagory?>" />
	        <input type="hidden" name="act" value="add_accompany" />
	        <input type="hidden" name="registration_request" id="registration_request" value="GENERAL" />
	        <input type="hidden" name="accompanyTariffAmount" id="accompanyTariffAmount" value="<?=$registrationAmount?>" />
	        <div class="" actAs='fieldContainer'>
	            <div class="col-xs-12 form-group ">
	                <div class="checkbox">
	                    <label class="select-lable">Number of Accompanying Person(s)</label>
	                    <div>
	                        <label class="container-box" style="float:left; margin-right:20px;">None
	                            <input type="radio" name="accompanyCount" use="accompanyCountSelect" value="0"
	                                amount="<?=0?>" invoiceTitle="Accompanying Person" checked="checked" required>
	                            <span class="checkmark"></span>
	                        </label>
	                        <label class="container-box" style="float:left; margin-right:20px;">One
	                            <input type="radio" name="accompanyCount" use="accompanyCountSelect" value="1"
	                                amount="<?=floatval($registrationAmount)*1?>" invoiceTitle="Accompanying - 1 Person">
	                            <span class="checkmark"></span>
	                        </label>
	                        <label class="container-box" style="float:left; margin-right:20px;">Two
	                            <input type="radio" name="accompanyCount" use="accompanyCountSelect" value="2"
	                                amount="<?=floatval($registrationAmount)*2?>" invoiceTitle="Accompanying - 2 Person">
	                            <span class="checkmark"></span>
	                        </label>
	                        <label class="container-box" style="float:left; margin-right:20px;">Three
	                            <input type="radio" name="accompanyCount" use="accompanyCountSelect" value="3"
	                                amount="<?=floatval($registrationAmount)*3?>" invoiceTitle="Accompanying - 3 Person">
	                            <span class="checkmark"></span>
	                        </label>
	                        <label class="container-box" style="float:left; margin-right:20px;">Four
	                            <input type="radio" name="accompanyCount" use="accompanyCountSelect" value="4"
	                                amount="<?=floatval($registrationAmount)*4?>" invoiceTitle="Accompanying - 4 Person">
	                            <span class="checkmark"></span>
	                        </label>
	                        <i class="itemPrice pull-right">
	                            <?
	                                    if(floatval($registrationAmount)>0)
	                                    {
	                                            echo '@ '.$registrationCurrency.' '.number_format($registrationAmount,2);
	                                    }
	                                                                
	                            ?>
	                        </i>
	                        &nbsp;
	                    </div>
	                </div>
	            </div>
	            <div class="col-xs-12" use="accompanyDetails" index="1" style="display:none;">
	                <h4>ACCOMPANY 1</h4>
	                <div class="col-xs-12 form-group input-material" actAs='fieldContainer'>
	                    <label for="accompany_name_add_1">Name</label>
	                    <input type="text" class="form-control" name="accompany_name_add[0]" id="accompany_name_add_1"
	                        style="text-transform:uppercase;">
	                    <input type="hidden" name="accompany_selected_add[0]" value="0" />
	                    <div class="alert alert-danger" callFor='alert'>Please enter a proper value.</div>
	                </div>
	                <div class="col-xs-8 form-group" actAs='fieldContainer'>
						<div class="checkbox">
							<label class="select-lable" >BANQUET</label>
							<?php
							$dinnerTariffArray   = getAllDinnerTarrifDetails($currentCutoffId);
							foreach($dinnerTariffArray as $keyDinner=>$dinnerValue)
							{
							?>
								<label class="container-box">
									<i class="itemTitle left-i"><?=$dinnerValue[$currentCutoffId]['DINNER_TITTLE']?></i>
									<i class="itemPrice right-i pull-right"> 
								<?php
								if(floatval($dinnerValue[$currentCutoffId]['AMOUNT'])>0)
								{
									echo $registrationCurrency.' '.number_format($dinnerValue[$currentCutoffId]['AMOUNT'],2);
								}

								?>
								</i>
									<input type="checkbox" name="dinner_value[0]" id="dinner_value" 
									value="<?=$dinnerValue[$currentCutoffId]['ID']?>" 
									operationMode="dinner"  
									amount="<?=$dinnerValue[$currentCutoffId]['AMOUNT']?>"
									invoiceTitle="<?=$dinnerValue[$currentCutoffId]['DINNER_TITTLE']?> - Accompany 1" onclick="calculateTotalAmountInProfile('accompanyAdd')"/>
									<span class="checkmark"></span>
									</label>
							<?php
							}
							?>						
						</div>
						<div class = "alert alert-danger" callFor='alert'>Please choose a proper option.</div>
					</div>
	                <div class="col-xs-12 form-group" actAs='fieldContainer'>
	                    <div class="checkbox">
	                        <div>
	                            <label class="container-box" style="float:left; margin-right:20px;">Veg
	                                <input type="radio" groupName="accompany_food_choice" name="accompany_food_choice[0]"
	                                    id="accompany_food_1_veg" value="VEG">
	                                <span class="checkmark"></span>
	                            </label>
	                            <label class="container-box" style="float:left; margin-right:20px;">Non-Veg
	                                <input type="radio" groupName="accompany_food_choice" name="accompany_food_choice[0]"
	                                    id="accompany_food_1_nonveg" value="NON_VEG">
	                                <span class="checkmark"></span>
	                            </label>
	                            &nbsp;
	                        </div>
	                        <label class="select-lable">Food Preference</label>
	                    </div>
	                    <div class="alert alert-danger" callFor='alert'>Please choose a proper option.</div>
	                </div>
	            </div>
	            <div class="col-xs-12" use="accompanyDetails" index="2" style="display:none;">
	                <h4>ACCOMPANY 2</h4>
	                <div class="col-xs-12 form-group input-material" actAs='fieldContainer'>
	                    <label for="accompany_name_add_2">Name</label>
	                    <input type="text" class="form-control" name="accompany_name_add[1]" id="accompany_name_add_2"
	                        style="text-transform:uppercase;">
	                    <input type="hidden" name="accompany_selected_add[1]" value="1" />
	                    <div class="alert alert-danger" callFor='alert'>Please enter a proper value.</div>
	                </div>
	                <div class="col-xs-8 form-group" actAs='fieldContainer'>
						<div class="checkbox">
							<label class="select-lable" >BANQUET</label>
							<?php
							$dinnerTariffArray   = getAllDinnerTarrifDetails($currentCutoffId);
							foreach($dinnerTariffArray as $keyDinner=>$dinnerValue)
							{
							?>
								<label class="container-box">
									<i class="itemTitle left-i"><?=$dinnerValue[$currentCutoffId]['DINNER_TITTLE']?></i>
									<i class="itemPrice right-i pull-right"> 
								<?php
								if(floatval($dinnerValue[$currentCutoffId]['AMOUNT'])>0)
								{
									echo $registrationCurrency.' '.number_format($dinnerValue[$currentCutoffId]['AMOUNT'],2);
								}

								?>
								</i>
									<input type="checkbox" name="dinner_value[1]" id="dinner_value" 
									value="<?=$dinnerValue[$currentCutoffId]['ID']?>" 
									operationMode="dinner"  
									amount="<?=$dinnerValue[$currentCutoffId]['AMOUNT']?>"
									invoiceTitle="<?=$dinnerValue[$currentCutoffId]['DINNER_TITTLE']?> - Accompany 2" onclick="calculateTotalAmountInProfile('accompanyAdd')"/>
									<span class="checkmark"></span>
									</label>
							<?php
							}
							?>						
						</div>
						<div class = "alert alert-danger" callFor='alert'>Please choose a proper option.</div>
					</div>
	                <div class="col-xs-12 form-group" actAs='fieldContainer'>
	                    <div class="checkbox">
	                        <div>
	                            <label class="container-box" style="float:left; margin-right:20px;">Veg
	                                <input type="radio" groupName="accompany_food_choice" name="accompany_food_choice[1]"
	                                    id="accompany_food_1_veg" value="VEG">
	                                <span class="checkmark"></span>
	                            </label>
	                            <label class="container-box" style="float:left; margin-right:20px;">Non-Veg
	                                <input type="radio" groupName="accompany_food_choice" name="accompany_food_choice[1]"
	                                    id="accompany_food_1_nonveg" value="NON_VEG">
	                                <span class="checkmark"></span>
	                            </label>
	                            &nbsp;
	                        </div>
	                        <label class="select-lable">Food Preference</label>
	                    </div>
	                    <div class="alert alert-danger" callFor='alert'>Please choose a proper option.</div>
	                </div>
	            </div>
	            <div class="col-xs-12" use="accompanyDetails" index="3" style="display:none;">
	                <h4>ACCOMPANY 3</h4>
	                <div class="col-xs-12 form-group input-material" actAs='fieldContainer'>
	                    <label for="accompany_name_add_3">Name</label>
	                    <input type="text" class="form-control" name="accompany_name_add[2]" id="accompany_name_add_3"
	                        style="text-transform:uppercase;">
	                    <input type="hidden" name="accompany_selected_add[2]" value="2" />
	                    <div class="alert alert-danger" callFor='alert'>Please enter a proper value.</div>
	                </div>
	                <div class="col-xs-8 form-group" actAs='fieldContainer'>
						<div class="checkbox">
							<label class="select-lable" >BANQUET</label>
							<?php
							$dinnerTariffArray   = getAllDinnerTarrifDetails($currentCutoffId);
							foreach($dinnerTariffArray as $keyDinner=>$dinnerValue)
							{
							?>
								<label class="container-box">
									<i class="itemTitle left-i"><?=$dinnerValue[$currentCutoffId]['DINNER_TITTLE']?></i>
									<i class="itemPrice right-i pull-right"> 
								<?php
								if(floatval($dinnerValue[$currentCutoffId]['AMOUNT'])>0)
								{
									echo $registrationCurrency.' '.number_format($dinnerValue[$currentCutoffId]['AMOUNT'],2);
								}

								?>
								</i>
									<input type="checkbox" name="dinner_value[2]" id="dinner_value" 
									value="<?=$dinnerValue[$currentCutoffId]['ID']?>" 
									operationMode="dinner"  
									amount="<?=$dinnerValue[$currentCutoffId]['AMOUNT']?>"
									invoiceTitle="<?=$dinnerValue[$currentCutoffId]['DINNER_TITTLE']?> - Accompany 3" onclick="calculateTotalAmountInProfile('accompanyAdd')"/>
									<span class="checkmark"></span>
									</label>
							<?php
							}
							?>						
						</div>
						<div class = "alert alert-danger" callFor='alert'>Please choose a proper option.</div>
					</div>
	                <div class="col-xs-12 form-group" actAs='fieldContainer'>
	                    <div class="checkbox">
	                        <div>
	                            <label class="container-box" style="float:left; margin-right:20px;">Veg
	                                <input type="radio" groupName="accompany_food_choice" name="accompany_food_choice[2]"
	                                    id="accompany_food_3_veg" value="VEG">
	                                <span class="checkmark"></span>
	                            </label>
	                            <label class="container-box" style="float:left; margin-right:20px;">Non-Veg
	                                <input type="radio" groupName="accompany_food_choice" name="accompany_food_choice[2]"
	                                    id="accompany_food_3_nonveg" value="NON_VEG">
	                                <span class="checkmark"></span>
	                            </label>
	                            &nbsp;
	                        </div>
	                        <label class="select-lable">Food Preference</label>
	                    </div>
	                    <div class="alert alert-danger" callFor='alert'>Please choose a proper option.</div>
	                </div>
	            </div>
	            <div class="col-xs-12" use="accompanyDetails" index="4" style="display:none;">
	                <h4>ACCOMPANY 4</h4>
	                <div class="col-xs-12 form-group input-material" actAs='fieldContainer'>
	                    <label for="accompany_name_add_4">Name</label>
	                    <input type="text" class="form-control" name="accompany_name_add[3]" id="accompany_name_add_4"
	                        style="text-transform:uppercase;">
	                    <input type="hidden" name="accompany_selected_add[3]" value="3" />
	                    <div class="alert alert-danger" callFor='alert'>Please enter a proper value.</div>
	                </div>
	                <div class="col-xs-8 form-group" actAs='fieldContainer'>
						<div class="checkbox">
							<label class="select-lable" >BANQUET</label>
							<?php
							$dinnerTariffArray   = getAllDinnerTarrifDetails($currentCutoffId);
							foreach($dinnerTariffArray as $keyDinner=>$dinnerValue)
							{
							?>
								<label class="container-box">
									<i class="itemTitle left-i"><?=$dinnerValue[$currentCutoffId]['DINNER_TITTLE']?></i>
									<i class="itemPrice right-i pull-right"> 
								<?php
								if(floatval($dinnerValue[$currentCutoffId]['AMOUNT'])>0)
								{
									echo $registrationCurrency.' '.number_format($dinnerValue[$currentCutoffId]['AMOUNT'],2);
								}

								?>
								</i>
									<input type="checkbox" name="dinner_value[3]" id="dinner_value" 
									value="<?=$dinnerValue[$currentCutoffId]['ID']?>" 
									operationMode="dinner"  
									amount="<?=$dinnerValue[$currentCutoffId]['AMOUNT']?>"
									invoiceTitle="<?=$dinnerValue[$currentCutoffId]['DINNER_TITTLE']?> - Accompany 4" onclick="calculateTotalAmountInProfile('accompanyAdd')"/>
									<span class="checkmark"></span>
									</label>
							<?php
							}
							?>						
						</div>
						<div class = "alert alert-danger" callFor='alert'>Please choose a proper option.</div>
					</div>
	                <div class="col-xs-12 form-group" actAs='fieldContainer'>
	                    <div class="checkbox">
	                        <div>
	                            <label class="container-box" style="float:left; margin-right:20px;">Veg
	                                <input type="radio" groupName="accompany_food_choice" name="accompany_food_choice[3]"
	                                    id="accompany_food_4_veg" value="VEG">
	                                <span class="checkmark"></span>
	                            </label>
	                            <label class="container-box" style="float:left; margin-right:20px;">Non-Veg
	                                <input type="radio" groupName="accompany_food_choice" name="accompany_food_choice[3]"
	                                    id="accompany_food_4_nonveg" value="NON_VEG">
	                                <span class="checkmark"></span>
	                            </label>
	                            &nbsp;
	                        </div>
	                        <label class="select-lable">Food Preference</label>
	                    </div>
	                    <div class="alert alert-danger" callFor='alert'>Please choose a proper option.</div>
	                </div>
	            </div>
	            <div class="alert alert-danger" callFor='alert' style="margin-top: 10px;">Please choose a proper option.
	            </div>
	            <div class="text-center pull-right accom-submit" style="margin-top:10px; display:none;">
	                <button type="button" class="submit" onclick="validateRegistrationAccompanyDetails(this)">Next</button>
	            </div>
	            <?
	                    paymentProcess('accopanyAddPayment');
	                
	                ?>
	        </div>
	    </form>
	</div>
<?php
}

// ACCOMPANY ADD END

function profileAccommodationAddRoom($delegateId, $rowUserDetails, $invoiceList, $currentCutoffId)
{
	
	global  $cfg, $mycms;

    $conferenceTariffArray   = getAllRegistrationTariffs($currentCutoffId);
    $accompanyCatagory      = 1; // accompany persion registration fees set to the cutoff value of 'Member' registration classification type 
    //$registrationAmount   = $conferenceTariffArray[$accompanyCatagory]['AMOUNT'];
    $registrationAmount     = getCutoffTariffAmnt($currentCutoffId);
    $registrationCurrency   = $conferenceTariffArray[$accompanyCatagory]['CURRENCY'];
    $getExistingAccommodationList = getTotalAccommodationWithoutCombo($delegateId);
    
    //echo '<pre>'; print_r($getExistingAccommodationList[0]['hotel_id']);

   
     $getAccommodationMaxRoom = getAccommodationMaxRoom($delegateId);
?>
	<div class="menu-center-section col-xs-12" style="display:none" operationMode="profileData"
	    operationData="accommodationAddRoom">
	    <h5>
	        Add Room
	        <i class="fas fa-backward pull-right" style="cursor:pointer;" onClick="returnToSummaryBlock(this);"></i>
	    </h5>
	    <form name="frmAddAccommodationRoom" id="frmAddAccommodationRoom"
	        action="<?=$cfg['BASE_URL']?>registration.process.php" method="post">

		     <input type="hidden" name="act" value="add_accommodationfrom_profile" />
			<input type="hidden" id="cutoff_id" name="cutoff_id" value="<?=$currentCutoffId?>" cutoffid="<?=$currentCutoffId?>" />
			<input type="hidden" name="delegateClasfId" value="<?=$rowUserDetails['registration_classification_id']?>" />
			<input type="hidden" id="delegate_id" name="delegate_id" value="<?=$delegateId?>" />								
			<input type="hidden" id="accommodation_pkg_id" name="accommodation_pkg_id" value="" />								
			<input type="hidden" id="accommodation_details" name="accommodation_details" value="" />
			<input type="hidden" name="addRoom" value="1">	
	        <div class="accommodation-hotels" actAs='fieldContainer'>
	        	<?php
				$sql_hotel  =   array();
				$sql_hotel['QUERY'] =   "SELECT * 
				                            FROM "._DB_MASTER_HOTEL_."
				                            WHERE `status`  =       ?
				                            ORDER BY `id` ASC";
				                                
				$sql_hotel['PARAM'][]   =   array('FILD' => 'status' ,      'DATA' => 'A' ,         'TYP' => 's');
				        
				$res_hotel=$mycms->sql_select($sql_hotel);

				
				 $totalCount = 3 - $getAccommodationMaxRoom;

				if($totalCount>0)
				{
			?>
					<div class="col-md-6" style="padding-bottom: 13px !important;"><label class="accom-title"><b>Choose Hotel :</b></label>
	                    <select operationmode="hotel_select_acco_room_id" name="accommodationHotel" id="hotel_select_acco_room_id" style="color:#767676 !important;height: 38px !important;float: left;">
	                        <option value="">Choose hotel</option>
	                        <?php
	                        
	                        foreach ($res_hotel as $key => $value) {

	                        	 if($getExistingAccommodationList[0]['hotel_id']==$value['id'])
	                        	 {
	                            ?>
	                              <option value="<?php echo $value['id'] ?>"><?php echo $value['hotel_name'] ?></option>
	                            <?php
	                        	}
	                         } 
	                        ?>
	                    </select> 
	                    <div class="alert alert-danger" callFor='alert' id="hotelErr" style="display: none;">Please enter a proper value.</div>
	                </div>
	                <div class="col-md-6">
	                	<label class="accom-title"><b>Choose Rooms:</b></label>
	                	<select name="accommodation_room" id="accommodation_room" style="color:#767676 !important;height: 38px !important;float: left;">
	                		<?php

	                		 for($i=1;$i<=$totalCount;$i++)
	                		 {
	                		?>
	                			<option value="<?=$i?>"><?=$i?></option>
	                		<?php
	                		}
	                		?>
	                	</select>
	                </div>
	                <div class="col-md-12" id="packageDiv" class="packageDiv" style="color: #fff; " style="display: none;">
	                    
	                </div>

	                 <div class="col-md-12" id="clonedDivContainer" class="packageDiv" style="color: #fff; ">
	                 </div>
	            
					<input type="hidden" id="newAccodays" name="noofdays" value="1">
		            
		            <?
		                    paymentProcess('accommodationAddRoom');
		                
		           ?>

		     <?php
		      }
		      else
		      {
		      	echo "No rooms available here";
		      }
		     ?>      
	        </div>
	    </form>
	</div>
<?php

}

function profileAccompanyAddBanquet($delegateId, $rowUserDetails, $invoiceList, $currentCutoffId)
{
    $conferenceTariffArray   = getAllRegistrationTariffs($currentCutoffId);
    $accompanyCatagory      = 1; // accompany persion registration fees set to the cutoff value of 'Member' registration classification type 
    //$registrationAmount   = $conferenceTariffArray[$accompanyCatagory]['AMOUNT'];
    $registrationAmount     = getCutoffTariffAmnt($currentCutoffId);
    $registrationCurrency   = $conferenceTariffArray[$accompanyCatagory]['CURRENCY'];
?>
<div class="menu-center-section col-xs-12" style="display:none" operationMode="profileData"
    operationData="accompanyAddBanquet">
    <h5>
        Add Banquet
        <i class="fas fa-backward pull-right" style="cursor:pointer;" onClick="returnToSummaryBlockBanquet(this);"></i>
    </h5>
    <form name="frmAddAccompanyBanquetfromProfile" id="frmAddAccompanyBanquetfromProfile"
        action="<?=$cfg['BASE_URL']?>registration.process.php" method="post">

        <input type="hidden" id="cutoff_id" name="cutoff_id" value="<?=$currentCutoffId?>"
            cutoffid="<?=$currentCutoffId?>" />
        <input type="hidden" name="accompanyClasfId" value="<?=$accompanyCatagory?>" />
        <input type="hidden" name="act" value="add_accompany_banquet" />
        <input type="hidden" name="registration_request" id="registration_request" value="GENERAL" />
        <input type="hidden" name="accompanyTariffAmount" id="accompanyTariffAmount" value="<?=$registrationAmount?>" />
         <input type="hidden" name="accompanyID" id="accompanyID"  />

        <div class="" actAs='fieldContainer'>
            
            <div class="col-xs-12" use="accompanyDetailsBanquet" index="1" style="display:block;">
                
                <div class="col-xs-8 form-group" actAs='fieldContainer'>
					<div class="checkbox">
						<label class="select-lable" >BANQUET</label>
						<?php
						$dinnerTariffArray   = getAllDinnerTarrifDetails($currentCutoffId);
						foreach($dinnerTariffArray as $keyDinner=>$dinnerValue)
						{
						?>
							<label class="container-box">
								<i class="itemTitle left-i"><?=$dinnerValue[$currentCutoffId]['DINNER_TITTLE']?></i>
								<i class="itemPrice right-i pull-right"> 
							<?php
							if(floatval($dinnerValue[$currentCutoffId]['AMOUNT'])>0)
							{
								echo $registrationCurrency.' '.number_format($dinnerValue[$currentCutoffId]['AMOUNT'],2);
							}

							?>
							</i>
								<input type="checkbox" name="dinner_value[0]" id="dinner_value_accompany" 
								value="<?=$dinnerValue[$currentCutoffId]['ID']?>" 
								operationMode="dinner"  
								amount="<?=$dinnerValue[$currentCutoffId]['AMOUNT']?>"
								invoiceTitle="<?=$dinnerValue[$currentCutoffId]['DINNER_TITTLE']?> - Accompany 1"/>
								<span class="checkmark"></span>
								</label>
						<?php
						}
						?>						
					</div>
					<div class = "alert alert-danger" callFor='alert' id="error_dinner">Please choose a proper option.</div>
				</div>
               
            </div>
          
            <div class="text-center pull-right accom-submit1" style="margin-top:10px;">
                <button type="button" class="submit" onclick="validateRegistrationAccompanyDetailsBanquet(this)">Next</button>
            </div>
            <?
                    paymentProcess('accompanyBanquet');
                
                ?>
        </div>
    </form>
</div>
<?php
}


/// DINNER
function profileBanquetDinnerDetails($delegateId, $rowUserDetails, $invoiceList, $currentCutoffId)
{
	
	global $mycms, $cfg;
	
	$conferenceInvoiceDetails = reset($invoiceList[$delegateId]['REGISTRATION']);	
?>
<div class="menu-center-section col-xs-12" style="display:none" operationMode="profileData" operationData="dinnerDetails">
	<h5>
		Gala Dinner Details of<?=$rowUserDetails['user_full_name']?>
		<i class="fas fa-backward pull-right" style="cursor:pointer;" onClick="returnToSummaryBlock(this);"></i>
	</h5>	
		<table class="table">
			<tr>
				<td width="50%">Gala Dinner Applicant</td>
				<td width="50%"><?=$rowUserDetails['user_full_name']?></td>
				</tr>
				<tr>
				<td width="50%">Registration Status </td>
				<td width="50%">
				<strong style="color:<?=(($dinnersInvoiceDtls['payment_status']=='PAID' || $dinnersInvoiceDtls['payment_status']=='ZERO_VALUE') ?'#00a200':'red')?>;"><?=($dinnersInvoiceDtls['INVOICE']['payment_status']!= 'UNPAID')?'Registered':'Incomplete'?></strong>
				<?
				if($invoiceList[$delegateId]['OPR_TYPE']=='DELEGATE')
				{
				?>
				<a operationMode="proceedPayment" style="float:right; border: 1px solid #e4181f; padding: 5px; color: #e4181f;" onClick="openEditNamePopup('<?=$dinnersInvoiceDtls['id']?>')">Request for cancellation</a>
				<?
				}
				?>
				</td>
				</tr>
				<tr>
				<td width="50%">Gala Dinner</td>
				<td width="50%" style="color:#0000FF;"><b>&nbsp;TAKEN</b></td>
				</tr>
		</table>
</div>
<?
}

// ABSTRACT DETAILS

function profileAbstractSubmissionDetails($delegateId)
{
	global $mycms, $cfg;
	
	//$abstract_details = delegateAbstractDetails($delegateId);
	$rowUserDetails = getUserDetails($delegateId);
	
?>
<div class="menu-center-section col-xs-12" style="display:none" operationMode="profileData" operationData="abstractDetails">
	<h5>
	<?=$abstract_details['RAW']['abstract_category']?> <?=ucwords(strtolower($abstract_details['RAW']['abstract_parent_type']))?> Details of <?=ucwords(strtolower($rowUserDetails['user_full_name']))?>
	<i class="fas fa-backward pull-right" style="cursor:pointer;" onClick="returnToSummaryBlock(this);"></i>
	</h5>
	<table class="table">
		<tbody>
			<tr>
				<td class="" width="30%" style="color:#308b64;">Registered</td>
				<td class="" style="color:#308b64;">Date: <?=$mycms->cDate('d/m/Y', $abstract_details['SUBMISSION_DATE'])?></td>
			</tr>
			<tr>
				<td>Category</td>
				<td>
					<?=$abstract_details['RAW']['abstract_category']?> 
					<!--a class="cancel-bttn">Request for cancellation</a-->
				</td>
			</tr>
			<tr>
				<td>Submission Code</td>
				<td><?=$abstract_details['RAW']['abstract_submition_code']?></td>
			</tr>
			<tr>
				<td>Topic</td>
				<td><?=$abstract_details['TOPIC']?></td>
			</tr>
			<tr>
				<td>Abstract Title</td>
				<td><?=$abstract_details['CONTENT']['TITLE']?></td>
			</tr>
			<tr>
				<td>Author Name</td>
				<td><?=$abstract_details['RAW']['abstract_author_name']?></td>
			</tr>
			<tr>
				<td>Author Department</td>
				<td><?=$abstract_details['RAW']['abstract_author_department']?></td>
			</tr>
			<tr>
				<td>Author Institute</td>
				<td><?=$abstract_details['RAW']['abstract_author_institute_name']?></td>
			</tr>
			<tr>
				<td>Introduction</td>
				<td><?=$abstract_details['RAW']['abstract_background']?></td>
			</tr>
			<? if(!empty($abstract_details['RAW']['abstract_description'])) {?>
			<tr>
				<td>Description</td>
				<td><?=$abstract_details['RAW']['abstract_description']?></td>
			</tr>
			<? } ?>
			<? if(!empty($abstract_details['RAW']['abstract_background_aims'])) {?>
			<tr>
				<td>Aims and Objectives</td>
				<td><?=$abstract_details['RAW']['abstract_background_aims']?></td>
			</tr>
			<? } ?>
			<? if(!empty($abstract_details['RAW']['abstract_material_methods'])) {?>
			<tr>
				<td>Materials and Methods</td>
				<td><?=$abstract_details['RAW']['abstract_material_methods']?></td>
			</tr>
			<? } ?>
			<? if(!empty($abstract_details['RAW']['abstract_results'])) {?>
			<tr>
				<td>Results</td>
				<td><?=$abstract_details['RAW']['abstract_results']?></td>
			</tr>
			<? } ?>
			<? if(!empty($abstract_details['RAW']['abstract_conclution'])) {?>
			<tr>
				<td>Conclusions / Discussion</td>
				<td><?=$abstract_details['RAW']['abstract_conclution']?></td>
			</tr>
			<? } ?>
			<? if(!empty($abstract_details['CONTENT']['FILE_NAME'])) {?>
			<tr>
				<td>File</td>
				<td><?=$abstract_details['CONTENT']['FILE_NAME']?></td>
			</tr>
			<? } ?>
		</tbody>
	</table>
</div>
<?php
}

// ACCOMMODATION DETAILS 

function profileAccommodationDetails($delegateId, $rowUserDetails, $invoiceList, $currentCutoffId)
{
	global $mycms, $cfg;
	
	$conferenceInvoiceDetails = reset($invoiceList[$delegateId]['REGISTRATION']);
	$accommdationDtlsArr 			= $invoiceList[$delegateId]['ACCOMMODATION'];
	//echo '<pre>'; print_r($invoiceList);
	if(count($accommdationDtlsArr) > 0){
	?>
	<div class="menu-center-section col-xs-12" style="display:none" operationMode="profileData" operationData="accommodationDetails">
		<h5>
		Accommodation Details of <?=$invoiceList[$delegateId]['REGISTRATION'][$delegateId]['USER']['user_full_name']?>
		<i class="fas fa-backward pull-right" style="cursor:pointer;" onClick="returnToSummaryBlock(this);"></i>
		</h5>
		<table class="table table-bordered">
			<tbody>
				<? foreach ($accommdationDtlsArr as $key => $value) { 
					$hotel_name = '';
				    $total_stay = '';
				    if(!empty($value['ROW_DETAIL']['accommodation_details'])){
				        $accommodation_details_arr = explode('@', $value['ROW_DETAIL']['accommodation_details']);
				        $hotel_name = $accommodation_details_arr[1];
				        $temp = explode('-', $accommodation_details_arr[0]);
				        $total_stay = $temp[1];
				        //$total_stay = str_replace('Stay', '', $total_stay);
				    }
					$accommodation_invoice_status = $value['ROW_DETAIL']['status'];
					$accommodation_payment_status = $value['ROW_DETAIL']['payment_status'];
					$accommodation_checking_date = $value['ROW_DETAIL']['checkin_date'];
					$accommodation_checkout_date = $value['ROW_DETAIL']['checkout_date'];
					
					$slip_id = $value['SLIP_DETAILS']['id'];
					$mode = $value['ROW_DETAIL']['booking_mode'];
				?>
						<tr>
							<td width="30%">Checking Date</td>
							<td><?=$mycms->cDate('d/m/Y', $accommodation_checking_date)?></td>
						</tr>
						<tr>
							<td width="30%">Checkout Date</td>
							<td><?=$mycms->cDate('d/m/Y', $accommodation_checkout_date)?></td>
						</tr>
						<tr>
							<td>Hotel Name</td>
							<td><?=$hotel_name?></td>
						</tr>
						<tr>
							<td>Total Night Stay</td>
							<td><?=$total_stay?></td>
						</tr>
						
						<tr>
							<td width="30%">
								 Registered on
							</td>
							<td><?=date('d/m/Y', strtotime($value['INVOICE']['invoice_date']))?></td>
						</tr>
						<tr>
							<td>Accommodation Status</td>
							<td>
								<strong style="color:<?=(($accommodation_payment_status=='PAID' || $accommodation_payment_status=='ZERO_VALUE') ?'#00a200':'red')?>;"><?=($accommodation_payment_status!= 'UNPAID')?'Registered':'Incomplete'?></strong>
								<? if($value['INVOICE']['payment_status'] == 'UNPAID' && $value['INVOICE']['invoice_mode'] != 'OFFLINE'){ ?>
										<form action="<?=_BASE_URL_?>profile.php" method="post">
											<input type="hidden" id="slip_id" name="slip_id" value="<?=$slip_id?>" />
											<input type="hidden" id="delegate_id" name="delegate_id" value="<?=$delegateId?>" />
											<input type="hidden" name="act" value="paymentSetInit" />
											<input type="hidden" name="mode" value="<?=$mode?>" />
											<input type="submit" name="sumit" value="Complete Now" style="background-color: #005e82; padding: 5px; color: #ffffff; text-decoration:none;font-size:large; float:right; height: auto;">
										</form>
								<?  } ?>
							</td>
						</tr>
						
				<?  
				} ?>
				
			</tbody>
		</table>
	</div>
<?	}
	
}

// ACCOMMODATION ADD
function profileAccommodationAdds($delegateId, $rowUserDetails, $invoiceList, $currentCutoffId)
{
	global $mycms, $cfg;
	//echo 1212;
	if($delegateId > 0 && $currentCutoffId > 0){
		$rowaccommodation_deatils = accommodationList($currentCutoffId);
		$total_accmmo_count = getTotalAccommodationCount($delegateId);

		$getExistingAccommodationList = getTotalAccommodationWithoutCombo($delegateId);
		$userRec = getUserDetails($delegateId);
		//echo '<pre>'; print_r($rowUserDetails['accommodation_room']);
		//echo "<pre>"; print_r($invoiceList[$delegateId]['REGISTRATION'][$delegateId]['USER']['accDateCombo']); echo "</pre>";
		if(count($rowaccommodation_deatils) > 0){
			//$hotel_details = array_unique(array_column($rowaccommodation_deatils, 'HOTEL_NAME','HOTEL_ID'));
			$hotel_details = [];
			foreach ($rowaccommodation_deatils as $key => $value) {
				 $packageID = $value['ACCOMMODATION_PACKAGE_ID'];
				if (!isset($hotel_details[$packageID])) {
				    $hotel_details[$packageID] = $value;
				}
			}
			$hotel_details = array_values($hotel_details);

			//echo '<pre>'; print_r($hotel_details);
			$max_number_of_days = max(array_column($rowaccommodation_deatils, 'DAYS'));
			
			$accommodationDetails = array();
			$hotel_aval_days = array();

			//echo '<pre>'; print_r($rowaccommodation_deatils);

			foreach ($rowaccommodation_deatils as $key => $value) {
				// code...
				$accommodationDetails[$value['HOTEL_ID']][] = $value;
				$hotel_aval_days[$value['HOTEL_ID']][] = $value['DAYS'];
			}
			$comboCheckIn = '';
			$comboCheckOut = '';
			if(isset($invoiceList[$delegateId]['REGISTRATION'][$delegateId]['USER']['accDateCombo']))
			{
				$comboAcco = $invoiceList[$delegateId]['REGISTRATION'][$delegateId]['USER']['accDateCombo'];
				$explodeCombo = explode("-",$comboAcco);
				//print_r($explodeCombo);
				 $comboCheckIn = $explodeCombo[0];
				 $comboCheckOut = $explodeCombo[1];
				 $comboHotelID = $explodeCombo[2];
			}
			//echo '<pre>'; print_r($accommodationDetails);
		?>
		<div class="menu-center-section col-xs-12" style="display:none;" operationMode="profileData" operationData="accommodationAdd">
			<h5>
			<?php
			if(!empty($getExistingAccommodationList[0]['hotel_id']) && $getExistingAccommodationList[0]['hotel_id']>0)
			{
				echo 'Add Nights';
			}
			else
			{
				echo 'Add Accommodation';
			}
			?>	
			
			<i class="fas fa-backward pull-right" style="cursor:pointer;" onClick="returnToSummaryBlock(this);"></i>
			</h5>
			<form name="frmAddAccommodationfromProfile" id="frmAddAccommodationfromProfile" action="<?=$cfg['BASE_URL']?>registration.process.php" method="post">
				<input type="hidden" name="act" value="add_accommodationfrom_profile" />
				<input type="hidden" id="cutoff_id" name="cutoff_id" value="<?=$currentCutoffId?>" cutoffid="<?=$currentCutoffId?>" />
				<input type="hidden" name="delegateClasfId" value="<?=$rowUserDetails['registration_classification_id']?>" />
				<input type="hidden" id="delegate_id" name="delegate_id" value="<?=$delegateId?>" />								
				<input type="hidden" id="accommodation_pkg_id" name="accommodation_pkg_id" value="" />								
				<input type="hidden" id="accommodation_details" name="accommodation_details" value="" />								
				<div class="accommodation-hotels" actAs='accommodationFieldContainer'>
					<?

						if(count($hotel_details) > 0 && count($accommodationDetails) > 0)
						{
							
							 $checkinIdArray = array();
							 $checkoutIdArray = array();
							 $packageArray = array();
								 if(count($getExistingAccommodationList)>0)
								 {
								 		
									 	foreach ($getExistingAccommodationList as $key => $value) {
									 		array_push($packageArray,$value['package_id']);
										 	//echo 'hoteelID='.$value['hotel_id'];
										 	//echo 'checkin_date='.$value['checkin_date'];
										 	//echo 'checkout_date='.$value['checkout_date'];

										     $diff = strtotime($value['checkout_date']) - strtotime($value['checkin_date']);
  										     $days = abs(round($diff / 86400));

  										    if($days>1)
  										    {
  										    	//echo 'hotelID='. $value['hotel_id'];
  										    	//echo 'checkin_date='. $value['checkin_date'];
  										    	//echo 'checkout_date='. $value['checkout_date'];
  										    	$getAllCheckInId = getVariableCheckInId($value['hotel_id'], $value['checkin_date'], $value['checkout_date']);
  										    	//print_r($getAllCheckInId);
  										    	$getAllCheckOutId = getVariableCheckOutId($value['hotel_id'], $value['checkin_date'], $value['checkout_date']);
  										    }
  										    else
  										    {
  										    	$getAllCheckInId = getAllCheckInId($value['hotel_id'], $value['checkin_date']);
  										    	$getAllCheckOutId = getAllCheckOutId($value['hotel_id'], $value['checkout_date']);
  										    }

										 	foreach ($getAllCheckInId as $key => $value) {
										 		array_push($checkinIdArray,$value['id']);

										 	}

										 	foreach ($getAllCheckOutId as $key => $value) {

										 		array_push($checkoutIdArray,$value['id']);

										 		
										 	}

									 	
								 		
									 }
								 }

								
								if(!empty($userRec['isCombo']) && $userRec['isCombo']=='Y')
								{   
									//echo 1212;
									$accDateCombo = $userRec['accDateCombo'];
									$explodeComboDate = explode('-',$accDateCombo);
									
									$checkInCombo = array($explodeComboDate[0],$explodeComboDate[1]);
									foreach($hotel_details as $hotel_id=>$hotel_detail)
									 {
									 	if($hotel_id==$comboHotelID)
									 	{
											$aval_days = array_unique($hotel_aval_days[$hotel_id]);
											$min_amount = array_column($accommodationDetails[$hotel_id],'AMOUNT');


											?>
												
												<input type="hidden" name="accommodationHotel"  value="<?=$hotel_id?>">
											<?
										}	
									}
									?>
									<input type="hidden"  name="noofdays" value="1">
									<?php	
									//echo '<pre>'; print_r($accommodationDetails);
									foreach($accommodationDetails as $hotelId=>$rowAccommodation)
									{
										//echo '<pre>'; print_r($rowAccommodation);


										if($hotelId==$comboHotelID)
									 	{

									 		//echo '<pre>'; print_r($rowAccommodation);
									 		$temp = array_unique(array_column($rowAccommodation, 'CHECKIN_DATE','CHECKOUT_DATE'));
									 		//echo '<pre>'; print_r($temp);

									 		$uniqueArray = [];

											foreach ($rowAccommodation as $item) {
											    /*$hotelId = $item['CHECKIN_DATE_ID'];
											    if (!isset($uniqueArray[$hotelId])) {
											        $uniqueArray[$hotelId] = $item;
											    }*/
											    if(count($packageArray)>0)
												{	
													if (in_array($item['ACCOMMODATION_PACKAGE_ID'], $packageArray))
													{	
														//echo 12;
													    $hotelId = $item['CHECKIN_DATE_ID'];
													    if (!isset($uniqueArray[$hotelId])) {
													        $uniqueArray[$hotelId] = $item;
													    }
													}
												}	
												else{
													$hotelId = $item['CHECKIN_DATE_ID'];
												    if (!isset($uniqueArray[$hotelId])) {
												        $uniqueArray[$hotelId] = $item;
												    }
												} 
											}
											$uniqueArray = array_values($uniqueArray); // Re-index the array

											//echo '<pre>'; print_r($uniqueArray);
											?>
												<div class="col-xs-12" style="display:block;padding: 0;" use="<?=$hotelId?>" operetionMode="checkInCheckOutTr">
													<div class="col-xs-6 form-group" actAs='fieldContainer'>
														<div class="radio">
															<label class="select-lable" >CHECK-IN DATE</label>
															<?
																foreach($uniqueArray as $seq=>$accPackDet)
																{

																	if($accPackDet['DAYS']==1)
													 				{
																		
																		 $ShareState = $accPackDet['DAYS']." Night Stay";
																		//echo 'checkin='. $accPackDet['CHECKIN_DATE_ID'];
																		//echo 'checkout='. $accPackDet['CHECKOUT_DATE_ID'];
																		if(!empty($comboCheckIn) && ($comboCheckIn==$accPackDet['CHECKIN_DATE_ID']) && ($comboCheckOut==$accPackDet['CHECKOUT_DATE_ID']))
																		{
																			$checked = 'checked="checked"';
																		}
																		else
																		{
																			$checked = '';
																		}

																		
																		//print_r($checkinIdArray);
																		if(count($checkinIdArray)>0)
																		{
																			if (in_array($accPackDet['CHECKIN_DATE_ID'], $checkinIdArray))
																			{
																				 $checkinCheck = 'checked="checked"';
																				 $checkinDisabled = 'disabled="disabled"';
																			}
																			else
																			{
																				$checkinCheck = '';
																				$checkinDisabled = '';
																			}
																		}
																		


																		?>
																			<label class="container-box" style="display:block;"><?=$accPackDet['CHECKIN_DATE']?>
																				<input type="checkbox" operetionMode="checkInCheckOut_<?=$hotelId.'_'.$accPackDet['DAYS']?>" use="accoStartDate" id="accDate_<?=$accPackDet['ACCOMMODATION_PACKAGE_ID'].'_'.$accPackDet['HOTEL_ID'].'_'.$accPackDet['DAYS'].'_'.$currentCutoffId?>"  name="accDate[]" value="<?=$accPackDet['CHECKIN_DATE_ID'].'-'.$accPackDet['CHECKOUT_DATE_ID'].'-'.$accPackDet['HOTEL_ID']?>" checkoutDate="<?=$accPackDet['CHECKOUT_DATE_ID'].'_'.$accPackDet['ACCOMMODATION_TARIFF_ID']?>" accommoPkg="<?=$accPackDet['ACCOMMODATION_PACKAGE_ID']?>" amount="<?=$accPackDet['AMOUNT']?>"
																						invoiceTitle="Residential Package - <?=$StayName?>-<?=$ShareState?>@<?=$accPackDet['HOTEL_NAME']?>" onClick="showChekinChekoutDate(this);" class="accoStartDate" <?=$checkinCheck?> <?=$checkinDisabled?>>
																				<span class="checkmark"></span>
																			</label>
																			 <input type="hidden" name="checkTotalCount" id="checkTotalCount" value="<?=$seq?>">
																		<?
																	}	
																}
															?>
														</div>

														<div class = "alert alert-danger" callFor='alert'>Please select a proper option.</div>
													</div>	
													<div class="col-xs-6 form-group" style="" actAs='fieldContainer'>
														<div class="radio">
															<label class="select-lable" >CHECK-OUT DATE</label>
																<?
																	foreach($uniqueArray as $seq=>$accPackDet)
																	{
																		if($accPackDet['DAYS']==1)
													 					{

													 						if(count($checkoutIdArray)>0)
																			{
																				if (in_array($accPackDet['CHECKOUT_DATE_ID'], $checkoutIdArray))
																				{
																					 $checkinCheck = 'checked="checked"';
																					 $checkinDisabled = 'disabled="disabled"';
																				}
																				else
																				{
																					$checkinCheck = '';
																					$checkinDisabled = '';
																				}
																			}
																	?>
																		<label class="container-box" style="display:block;"><?=$accPackDet['CHECKOUT_DATE']?>
																			<input type="checkbox" operetionMode="checkInCheckOut_<?=$hotelId.'_'.$accPackDet['DAYS']?>" value="<?=$accPackDet['CHECKOUT_DATE_ID'].'_'.$accPackDet['ACCOMMODATION_TARIFF_ID']?>" use="accoEndDate" disabled="disabled" <?=$checkinCheck?>>
																			<span class="checkmark"></span>
																		</label>
																<?	
																		}
																	}
																?>
														</div>
														<div class = "alert alert-danger" callFor='alert'>Please select a proper option.</div>
													</div>
												</div>
											<?php	
										}
									}
								}
								else
								{
										if(!empty($getExistingAccommodationList[0]['hotel_id']) && $getExistingAccommodationList[0]['hotel_id']>0)
										{

											
											   
												
												 $arr_hotel = array();	
												
												  $getAccommodationMaxRoom = getAccommodationMaxRoom($delegateId);

												 $single_hotelID = $getExistingAccommodationList[0]['hotel_id'];
												 if(!empty($single_hotelID) && $single_hotelID>0)
												 {
												 	$styless = 'none';
												 }
												 else
												 {
												 	$styless = 'block';
												 }
												?>
												<div class="col-xs-12" style="display:none;" actAs='fieldContainer' operetionMode="checkInCheckOutHotel">
														<div class="radio">
															<label class="select-lable" >CHOOSE HOTEL</label>
															<?
															//echo '<pre>'; print_r($hotel_details);
																foreach($hotel_details as $hotel_id=>$hotel_detail)
																{
																	$aval_days = array_unique($hotel_aval_days[$hotel_id]);
																	 $min_amount = $hotel_detail['AMOUNT'];

															?>
																	<label class="container-box"><?=$hotel_detail?> @ <?=number_format($min_amount)?> Per Night
																		<input type="radio" accommoAvlDy="<?=implode(',',$aval_days)?>" use="accommoHotel" id="accmmoH_<?=$hotel_detail['HOTEL_ID']?>"  name="accommodationHotel" value="<?=$hotel_detail['HOTEL_ID']?>">
																		<span class="checkmark"></span>
																	</label>
															<?
																}
															?>
														</div>
													<div class="alert alert-danger" callFor='alert'>Please select a proper option.</div>
												</div>
												<input type="hidden" name="accommodationHotel" value="<?=$single_hotelID?>">
												<input type="hidden" name="noofdays" value="1">
												<div class="col-xs-12" style="display:none;" actAs='fieldContainer' operetionMode="checkInCheckOutHotelDays">
													<div class="radio">
														<label class="select-lable">PLEASE CHOOSE</label>
														<div class="radio-holder" style="display: flex;align-items: center;">
														<?
															for($i=1;$i<=$max_number_of_days;$i++)
															{

														?>
																<label class="container-box" style="margin-right: 15px;">
																	<input type="radio" hotelValue="" use="accommoTotalDays" name="noofdays" value="<?=$i?>"><?=$i.' Nights Stay'?>
																	<span class="checkmark" style="top: 1px;left: 4px;"></span>
																</label>
														<?
															}
														?>
														</div>
													</div>
													<div class = "alert alert-danger" callFor='alert'>Please select a proper option.</div>
												</div> 

												 <input type="hidden" name="getAccommodationMaxRoom" id="getAccommodationMaxRoom" value="<?=$getAccommodationMaxRoom?>">
											<?
													//echo '<pre>'; print_r($accommodationDetails);
												
													foreach($accommodationDetails as $hotel_idd=>$rowAccommodation)
													{ 
														//echo 'single_hotelID='.$single_hotelID."<br>";
														//echo 'hotel_idd='.$hotel_idd;
														 //echo '<pre>'; print_r($rowAccommodation);
															
															//echo '<pre>'; print_r($rowAccommodation);
														
															$uniqueArray1 = [];
															//echo '<pre>'; print_r($rowAccommodation);
																foreach ($rowAccommodation as $item) {
																	//print_r($item['ACCOMMODATION_PACKAGE_ID']);
																	if(count($packageArray)>0)
																	{	
																		if (in_array($item['ACCOMMODATION_PACKAGE_ID'], $packageArray))
																		{	
																			//echo 12;
																		    $hotelId = $item['CHECKIN_DATE_ID'];
																		    if (!isset($uniqueArray1[$hotelId])) {
																		        $uniqueArray1[$hotelId] = $item;
																		    }
																		}
																	}	
																	else{
																		$hotelId = $item['CHECKIN_DATE_ID'];
																	    if (!isset($uniqueArray1[$hotelId])) {
																	        $uniqueArray1[$hotelId] = $item;
																	    }
																	}    
																}
															$uniqueArray1 = array_values($uniqueArray1);

															//echo 'hotelID='. $k;

															//echo '<pre>'; print_r($uniqueArray1);
															
															if($single_hotelID==$hotel_idd)
															{

																//echo '<pre>'; print_r($uniqueArray1);
																//echo $getAccommodationMaxRoom;
																
																for($i=1;$i<=$getAccommodationMaxRoom;$i++)
																{
																	
																	// $packagePrice = 300;
																		
																		//$checkinIdArray = getVariableCombinedCheckInId($i, $delegateId, );

																		//echo '<pre>' print_r($uniqueArray1);

																	$sqlDetails['QUERY'] = "SELECT DISTINCT(AR.package_id) AS package_id FROM "._DB_REQUEST_ACCOMMODATION_." AR INNER JOIN "._DB_MASTER_ROOM_." R ON AR.id=R.request_accommodation_id WHERE R.`room_id` = '".$i."' AND AR.status='A' AND R.status='A' AND AR.user_id='".$delegateId."' ";
																	//print_r($sqlDetails);
																	$resDetails = $mycms->sql_select($sqlDetails);

																	$existPackageID = $resDetails[0]['package_id'];
	

															?>
																		<input type="checkbox" name="package_night_id" id="package_night_id<?=$i?>" checked="" style="position: absolute;left: -9999px;opacity: 0;clip-path: inset(50%);">
																        <div class="col-xs-12"><b>ROOM <?=$i?></b></div>

																		<div class="col-xs-12" style="display:block;padding: 0;" use="<?=$hotel_idd?>" operetionMode="checkInCheckOutTr">
																			<div class="col-xs-6 form-group" actAs='fieldContainer'>
																				<div class="radio">
																					<label class="select-lable" >CHECK-IN DATE</label>
																					<select name="accomodation_package_checkin_ids[]" id="accomodation_package_checkin_id<?=$i?>" style="color:#767676 !important; height: 38px !important; width: 235px !important;">
																						<option value="" selected="">Select Checkin Date</option>
																					<?
																					    
																						foreach($uniqueArray1 as $seq=>$accPackDet)
																						{

																							$packagePrice = getAccommodationPackageDetails($accPackDet['CUTOFF_ID'], $hotel_idd, $existPackageID);

																							

																							$checkinIdArray = getAllCombinationCheckInID($i, $delegateId, $accPackDet['HOTEL_ID']);
																							
																							$ShareState = $accPackDet['DAYS']." Night Stay";
																							
																							if(!empty($comboCheckIn) && ($comboCheckIn==$accPackDet['CHECKIN_DATE_ID']) && ($comboCheckOut==$accPackDet['CHECKOUT_DATE_ID']))
																							{
																								$checked = 'checked="checked"';
																							}
																							else
																							{
																								$checked = '';
																							}
																							
																							if (in_array($accPackDet['CHECKIN_DATE_ID'], $checkinIdArray))
																							{
																								 $checkinCheck = 'checked="checked"';
																								 $checkinDisabled = 'disabled="disabled"';
																								 $css = 'strikethrough';
																							}
																							else
																							{
																								$checkinCheck = '';
																								$checkinDisabled = '';
																								 $css = '';
																							}


																						$packageName = getPackageNameById($existPackageID);

																						$StayName = $packageName;
																					?>
																						
																						<option value="<?=$accPackDet['CHECKIN_DATE_ID'].'/'.$accPackDet['CHECKIN_DATE'].'/'.$i?>"  invoiceTitle="Residential Package - <?=$StayName?>-<?=$ShareState?>@<?=$accPackDet['HOTEL_NAME']?>" class="<?=$css?>" package_id="<?=$accPackDet['ACCOMMODATION_PACKAGE_ID']?>" <?=$checkinDisabled?> ><?=$accPackDet['CHECKIN_DATE']?></option>

																						

																					<?
																						}
																					?>
																					</select>
																					 <input type="hidden" name="totalCount" id="totalCount" value="0">

																				</div>
																				<div class = "alert alert-danger" callFor='alert'>Please select a proper option.</div>
																			</div>	
																			<div class="col-xs-6 form-group" style="" actAs='fieldContainer'>
																				<div class="radio">
																					<label class="select-lable" >CHECK-OUT DATE</label>
																					<select operationmode="accomodation_package_checkout_id" name="accomodation_package_checkout_ids[]" class="accomodation_package_checkout_id" id="accomodation_package_checkout_id<?=$i?>" style="color:#767676 !important; height: 38px !important;margin-inline: 3px;width: 235px !important;" onchange="get_checkout_val_room_night(this,'<?=$i?>')" accommodation="checkout">
                	 																	<option value="">Select Check Out Date</option>
																						<?
																							foreach($uniqueArray1 as $seq=>$accPackDet)
																							{

																								
																								$checkoutIdArray = getAllCombinationCheckOutID($i, $delegateId, $accPackDet['HOTEL_ID']);
																								if(count($checkoutIdArray)>0)
																									{
																										if (in_array($accPackDet['CHECKOUT_DATE_ID'], $checkoutIdArray))
																										{
																											 $checkinCheck = 'checked="checked"';
																											 $checkinDisabled = 'disabled="disabled"';
																											  $css = 'strikethrough';
																										}
																										else
																										{
																											$checkinCheck = '';
																											$checkinDisabled = '';
																											 $css = '';
																										}
																									}


																						?>
																								
																								<option value="<?=$accPackDet['CHECKIN_DATE_ID'].'/'.$accPackDet['CHECKOUT_DATE'].'/'.$i?>" amount="<?=$packagePrice[0]['inr_amount']?>" class="<?=$css; ?>" invoiceTitle="Residential Package - <?=$StayName?>-<?=$ShareState?>@<?=$accPackDet['HOTEL_NAME']?>" package_id="<?=$accPackDet['ACCOMMODATION_PACKAGE_ID']?>" <?=$checkinDisabled?>><?=$accPackDet['CHECKOUT_DATE']?></option>
																								
																						<?	
																							}
																						?>
																					</select>
																				</div>
																				<div class = "alert alert-danger" callFor='alert'>Please select a proper option.</div>
																			</div>
																		</div>
															<?
																}
															}
														

													}

										}
									else{
													$sql_hotel  =   array();
													$sql_hotel['QUERY'] =   "SELECT * 
													                            FROM "._DB_MASTER_HOTEL_."
													                            WHERE `status`  =       ?
													                            ORDER BY `id` ASC";
													                                
													$sql_hotel['PARAM'][]   =   array('FILD' => 'status' ,      'DATA' => 'A' ,         'TYP' => 's');
													        
													$res_hotel=$mycms->sql_select($sql_hotel);

													$sql_room  =   array();
													$sql_room['QUERY'] =   "SELECT * 
													                            FROM "._DB_MASTER_ROOM_."
													                            WHERE `status`  =       ?
													                            ORDER BY `id` ASC";
													                                
													$sql_room['PARAM'][]   =   array('FILD' => 'status' ,      'DATA' => 'A' ,         'TYP' => 's');
													        
													$res_room=$mycms->sql_select($sql_room);

														?>
														<div class="col-md-6" style="padding-bottom: 13px !important;"><label class="accom-title"><b>Choose Hotel :</b></label>
								                            <select operationmode="hotel_select_acco_id" name="accommodationHotel" id="hotel_select_acco_id" style="color:#767676 !important;height: 38px !important;float: left;">
								                                <option value="">Choose hotel</option>
								                                <?php
								                                
								                                foreach ($res_hotel as $key => $value) {
								                                    ?>
								                                    <option value="<?php echo $value['id'] ?>"><?php echo $value['hotel_name'] ?></option>
								                                    <?php
								                                 } 
								                                ?>
								                            </select> 
								                            <div class="alert alert-danger" callFor='alert' id="hotelErr" style="display: none;">Please enter a proper value.</div>
								                        </div>
								                        <div class="col-md-6">
								                        	<label class="accom-title"><b>Choose Rooms:</b></label>
								                        	<select name="accommodation_room" id="accommodation_room" style="color:#767676 !important;height: 38px !important;float: left;">
								                        		<?php
								                        		 for($i=1;$i<=3;$i++)
								                        		 {
								                        		?>
								                        			<option value="<?=$i?>"><?=$i?></option>
								                        		<?php
								                        		}
								                        		?>
								                        	</select>
								                        </div>
								                        <div class="col-md-12" id="packageDiv" class="packageDiv" style="color: #fff; " style="display: none;">
								                            
								                        </div>
								                    
														<input type="hidden" id="newAccodays" name="noofdays" value="1">
											<?php
											}		
								}		
						}
					?>
				</div>
				<?
					paymentProcess('accommodationAdd');
				?>
			</form>
		</div>
		<?
			}
	}
}


// GENERAL PAYMENT OPTION

function paymentProcess($id="")
{ 
	global $cfg, $mycms;
?>
	<div use="registrationPaymentOptionWrap" style="display:none;" class="rightPanel_payment">
        <div class="link" use="rightAccordianL1TriggerDiv">PAYMENT OPTION</div>
        <ul class="submenu" style="display: none">
            <li>
				<div class="col-xs-12 form-group" actAs='fieldContainer'>
					<div class="radio">
						<label class="select-lable" >Payment Options</label>
						<label class="container-box">
							CREDIT / DEBIT CARD / ONLINE PAYMENT
							<input type="radio" name="registrationModePaymemt" value="ONLINE" operationMode="registrationModePaymemt" use='tariffPaymentModePaymemt' for="CC">
							<span class="checkmark"></span>
						</label>
						<div class="rightPanel_payment_CC" style="display:none; padding-left:20px;" for="CC" use='payRules'>
							Payment will be accepted only through VISA <img src="<?=_BASE_URL_?>images/visa_card_cion.png">  
							MASTER CARD <img src="<?=_BASE_URL_?>images/master_card_cion.png">
							RuPay <img src="<?=_BASE_URL_?>images/rupay_card_icon.png">
							Maestro <img src="<?=_BASE_URL_?>images/maestro_icon.png">
							MasterCard SecureCode <img src="<?=_BASE_URL_?>images/master_secured_icon.png">
							<!-- Diners Club International <img src="<?=_BASE_URL_?>images/dinner_club_icon.png"> -->
							<!-- American Express Card <img src="<?=_BASE_URL_?>images/american_express_card_cion.png"><br> -->
							For any query, please write at <?=$cfg['EMAIL_CONF_EMAIL_US']?> or call at <?=$cfg['EMAIL_CONF_CONTACT_US']?><br><br> 
						</div>
						
						<label class="container-box">
							CHEQUE
							<input type="radio" name="registrationModePaymemt" value="OFFLINE" operationMode="registrationModePaymemt" use='tariffPaymentModePaymemt' for="CHQ">
							<span class="checkmark"></span>
						</label>
						<div class="rightPanel_payment_CHQ" style="display:none; padding-left:39px;" for="CHQ" use='payRules'>
							<?=$cfg['cheque_info']?>
						</div>
						
						<label class="container-box">
							DEMAND DRAFT
							<input type="radio" name="registrationModePaymemt" value="OFFLINE" operationMode="registrationModePaymemt" use='tariffPaymentModePaymemt' for="DD">
							<span class="checkmark"></span>
						</label>
						<div class="rightPanel_payment_DD" style="display:none; padding-left:39px;" for="DD" use='payRules'>
							<?=$cfg['draft_info']?>														
						</div>														
										
						<label class="container-box">
							NEFT / RTGS / BANK TRANSFER / NET BANKING
							<input type="radio" name="registrationModePaymemt" value="OFFLINE" operationMode="registrationModePaymemt" use='tariffPaymentModePaymemt' for="WIRE">
							<span class="checkmark"></span>
						</label>
						<div class="rightPanel_payment_NEFT" style="display:none; padding-left:39px;" for="WIRE" use='payRules'>															
							<?=$cfg['neft_info']?>
						</div>
										
						<label class="container-box">
							CASH PAYMENT
							<input type="radio" name="registrationModePaymemt" value="OFFLINE" operationMode="registrationModePaymemt" use='tariffPaymentModePaymemt' for="CASH">
							<span class="checkmark"></span>
						</label>
						<div class="rightPanel_payment_CASH" style="display:none; padding-left:39px;" for="CASH" use='payRules'>															
							Payment can be sent by money order to the ISAR 24 Secretariat. 
							Direct deposition will not be accepted.<br>
							For any query, please write at <?=$cfg['EMAIL_CONF_EMAIL_US']?> or call at <?=$cfg['EMAIL_CONF_CONTACT_US']?><br><br> 
						</div>	
					</div>
					<div class = "alert alert-danger" callFor='alert'>Please choose a proper option.</div>
				</div>  
				<div class=" col-xs-2 text-center pull-right">
					<button type="button" class="submit" use='nextButton'>Next</button>
				</div>          
				<div class="clearfix"></div>
            </li>
        </ul>
    </div>

	<div use="registrationPaymentWrap" id="<?php echo $id; ?>" style="display:none;" class="rightPanel_payment">
        
		<div use="totalAmount" class="col-xs-12 totalAmount pull-right" style="display:none;">
			<table class="table bill" use="totalAmountTable">
				<thead>
					<tr>
						<th>DETAIL</th>
						<th align="right" style="text-align:right;">AMOUNT (INR)</th>
					</tr>
				</thead>
				<tbody></tbody>
				<tfoot>
					<tr style="display:none;" use='rowCloneable'>
						<td>&bull; &nbsp <span use="invTitle">Something</span></td>
						<td align="right"><span use="amount">0.00</span></td>
					</tr>
					<tr>
						<td>Total</td>
						<td align="right"><span use='totalAmount'>0.00</span></td>
					</tr>
				</tfoot>
			</table>
		</div>
		<div class="col-xs-12 form-group">	
			<i class="pull-left"><b>NOTE:</b> Rates are inclusive of all Taxes.</i>
		</div>


							
		<div class="col-xs-12 form-group" use="offlinePaymentOptionChoiceWrap" actAs='fieldContainer'>
			<div class="checkbox">
				<label class="select-lable" >Payment Option</label>
				<div>
					<label class="container-box" style="float:left; margin-right:30px;">Online Payment
					  <input type="radio" name="payment_mode" use="payment_mode_select" value="Card" for="Card" paymentMode='ONLINE' profileFrom="<?=$id;?>">
					  <span class="checkmark"></span>
					</label>
					<?php
					$offline_payments = json_decode($cfg['PAYMENT.METHOD']);

                    if (in_array("Cheque", $offline_payments))
                    {
                    ?>
						<label class="container-box" style="float:left; margin-right:30px;">Cheque
						  <input type="radio" name="payment_mode" use="payment_mode_select" value="Cheque" for="Cheque" paymentMode='OFFLINE' profileFrom="<?=$id;?>">
						  <span class="checkmark"></span>
						</label>
					<?php
	                   }
	                   if (in_array("Draft", $offline_payments))
	                   {
	                   ?> 
						<label class="container-box" style="float:left; margin-right:30px;">Draft
						  <input type="radio" name="payment_mode" use="payment_mode_select" value="Draft" for="Draft" paymentMode='OFFLINE' profileFrom="<?=$id;?>">
						  <span class="checkmark"></span>
						</label>
					<?php
	                   }
	                   if (in_array("Neft", $offline_payments))
	                   {
	                   ?>	
						<label class="container-box" style="float:left; margin-right:30px;">NEFT
						  <input type="radio" name="payment_mode" use="payment_mode_select" value="NEFT" for="NEFT" paymentMode='OFFLINE' profileFrom="<?=$id;?>">
						  <span class="checkmark"></span>
						</label>
					 <?php
                    }
                   if (in_array("Rtgs", $offline_payments))
                   {
                    ?>
						<label class="container-box" style="float:left; margin-right:30px;">RTGS
						  <input type="radio" name="payment_mode" use="payment_mode_select" value="RTGS" for="RTGS" paymentMode='OFFLINE' profileFrom="<?=$id;?>">
						  <span class="checkmark"></span>
						</label>
				  <?php
                   }
                   if (in_array("Cash", $offline_payments))
                   {
                   ?> 	
						<label class="container-box" style="float:left; margin-right:30px;">Cash
						  <input type="radio" name="payment_mode" use="payment_mode_select" value="Cash" for="Cash" paymentMode='OFFLINE' profileFrom="<?=$id;?>">
						  <span class="checkmark"></span>
						</label>
				   <?php
                   }
                   if (in_array("Upi", $offline_payments))
                   {
                   ?> 	
					<label class="container-box" style="float:left; margin-right:30px;">UPI
					  <input type="radio" name="payment_mode" use="payment_mode_select" value="UPI" for="UPI" paymentMode='OFFLINE' profileFrom="<?=$id;?>">
					  <span class="checkmark"></span>
					</label>
				  <?php
				  }
				  ?>	
					&nbsp;
				</div>																				
			</div>
			<div class = "alert alert-danger" callFor='alert' id="error_payment_mode_process">Please choose a proper option.</div>
		</div>
		<? /* commented as per the feedback on 09.09.2022
		<div class="col-xs-12 form-group" style="display:none;" use="offlinePaymentOptionWrap" for="Card" actAs='fieldContainer'>
			<div class="checkbox">
				<label class="select-lable" >Card Type</label>
				<div>
					<!-- <label class="container-box" style="float:left; margin-right:30px;">
					  <img src="<?=_BASE_URL_?>images/international_globe.png" height="20px;">
					  International Card
					  <input type="radio" name="card_mode" use="card_mode_select" value="International">
					  <span class="checkmark"></span>											   
					</label> -->
					<label class="container-box" style="float:left; margin-right:30px;">
					  <img src="<?=_BASE_URL_?>images/india_globe.png" height="20px;">
					  Indian Card
					  <input type="radio" name="card_mode" use="card_mode_select" value="Indian">
					  <span class="checkmark"></span>
					</label>
					&nbsp;
				</div>																				
			</div>
			<div class = "alert alert-danger" callFor='alert'>Please choose a proper option.</div>
		</div>
		*/ ?>
		<div class="col-xs-12 form-group input-material" style="display:none;" use="offlinePaymentOptionWrap" for="Card">
			Payment will be accepted only through VISA <img src="<?=_BASE_URL_?>images/visa_card_cion.png">  
			MASTER CARD <img src="<?=_BASE_URL_?>images/master_card_cion.png">
			RuPay <img src="<?=_BASE_URL_?>images/rupay_card_icon.png">
			Maestro <img src="<?=_BASE_URL_?>images/maestro_icon.png">
			MasterCard SecureCode <img src="<?=_BASE_URL_?>images/master_secured_icon.png">
			<!-- Diners Club International <img src="<?=_BASE_URL_?>images/dinner_club_icon.png"> -->
			<!-- American Express Card <img src="<?=_BASE_URL_?>images/american_express_card_cion.png"> --><br>
			For any query, please write at <?=$cfg['EMAIL_CONF_EMAIL_US']?> or call at <?=$cfg['EMAIL_CONF_CONTACT_US']?><br><br> 
		</div>
		
		<div class="col-xs-6 form-group input-material" style="display:none;" use="offlinePaymentOptionWrap" for="Cash" actAs='fieldContainer'>
			<label for="user_first_name">Money Order Sent Date</label>
			<input type="date" class="form-control" name="cash_deposit_date" id="cash_deposit_date" max="<?=$mycms->cDate("Y-m-d")?>" min="<?=$mycms->cDate("Y-m-d","-6 Months")?>">
			<div class = "alert alert-danger" callFor='alert'>Please choose a proper option.</div>
		</div>
		<div class="col-xs-12 form-group input-material" style="display:none;" use="offlinePaymentOptionWrap" for="Cash">
			<?=$cfg['cash_info']?>
		</div>
		
		<div class="col-xs-12 form-group input-material">
		    <div class="col-xs-6 form-group input-material" style="display:none;" use="offlinePaymentOptionWrap" for="UPI" actAs='fieldContainer'>

		        <label for="txn_no">UPI Transaction ID</label>
		        <input type="text" class="form-control" name="txn_no" id="txn_no">
		        <div class = "alert alert-danger" callFor='alert'>Please choose a proper option.</div>
		    </div>
		    <div class="col-xs-6 form-group input-material" style="display:none;" use="offlinePaymentOptionWrap" for="UPI" actAs='fieldContainer'>
		        <label for="txn_no">UPI Payment Date</label>
		        <input type="date" class="form-control" name="upi_date" id="upi_date" max="<?=$mycms->cDate("Y-m-d")?>" min="<?=$mycms->cDate("Y-m-d","-6 Months")?>">
		        <div class = "alert alert-danger" callFor='alert'>Please choose a proper option.</div>
		    </div> 
		</div>  
		<!-- UPI Payment Option Added By Weavers end -->
	
		<div class="col-xs-12 form-group input-material" style="display:none;" use="offlinePaymentOptionWrap" for="Cheque" actAs='fieldContainer'>
			<label for="user_first_name">Cheque No</label>
			<input type="text" class="form-control" name="cheque_number" id="cheque_number">
			<div class = "alert alert-danger" callFor='alert'>Please enter a proper value.</div>
		</div>
		<div class="col-xs-6 form-group input-material" style="display:none;" use="offlinePaymentOptionWrap" for="Cheque" actAs='fieldContainer'>
			<label for="user_first_name">Drawee Bank</label>
			<input type="text" class="form-control" name="cheque_drawn_bank" id="cheque_drawn_bank">
			<div class = "alert alert-danger" callFor='alert'>Please enter a proper value.</div>
		</div>
		<div class="col-xs-6 form-group input-material" style="display:none;" use="offlinePaymentOptionWrap" for="Cheque" actAs='fieldContainer'>
			<label for="user_first_name">Cheque Date</label>
			<input type="date" class="form-control" name="cheque_date" id="cheque_date" max="<?=$mycms->cDate("Y-m-d")?>" min="<?=$mycms->cDate("Y-m-d","-6 Months")?>">
			<div class = "alert alert-danger" callFor='alert'>Please choose a proper option.</div>
		</div>
		<div class="col-xs-12 form-group input-material" style="display:none;" use="offlinePaymentOptionWrap" for="Cheque">
			<?=$cfg['cheque_info']?>
		</div>
	
		<div class="col-xs-12 form-group input-material" style="display:none;" use="offlinePaymentOptionWrap" for="Draft" actAs='fieldContainer'>
			<label for="user_first_name">Draft No</label>
			<input type="text" class="form-control" name="draft_number" id="draft_number">
			<div class = "alert alert-danger" callFor='alert'>Please enter a proper value.</div>
		</div>
		<div class="col-xs-6 form-group input-material" style="display:none;" use="offlinePaymentOptionWrap" for="Draft" actAs='fieldContainer'>
			<label for="user_first_name">Drawee Bank</label>
			<input type="text" class="form-control" name="draft_drawn_bank" id="draft_drawn_bank">
			<div class = "alert alert-danger" callFor='alert'>Please enter a proper value.</div>
		</div>
		<div class="col-xs-6 form-group input-material" style="display:none;" use="offlinePaymentOptionWrap" for="Draft" actAs='fieldContainer'>
			<label for="user_first_name">Draft Date</label>
			<input type="date" class="form-control" name="draft_date" id="draft_date" max="<?=$mycms->cDate("Y-m-d")?>" min="<?=$mycms->cDate("Y-m-d","-6 Months")?>">
			<div class = "alert alert-danger" callFor='alert'>Please choose a proper option.</div>
		</div>
		<div class="col-xs-12 form-group input-material" style="display:none;" use="offlinePaymentOptionWrap" for="Draft">
			<?=$cfg['draft_info']?>	
		</div>
	
		<div class="col-xs-12 form-group input-material" style="display:none;" use="offlinePaymentOptionWrap" for="NEFT" actAs='fieldContainer'>
			<label for="user_first_name">Transaction Id</label>
			<input type="text" class="form-control" name="neft_transaction_no" id="neft_transaction_no">
			<div class = "alert alert-danger" callFor='alert'>Please enter a proper value.</div>
		</div>
		<div class="col-xs-6 form-group input-material" style="display:none;" use="offlinePaymentOptionWrap" for="NEFT" actAs='fieldContainer'>
			<label for="user_first_name">Drawee Bank</label>
			<input type="text" class="form-control" name="neft_bank_name" id="neft_bank_name">
			<div class = "alert alert-danger" callFor='alert'>Please enter a proper value.</div>
		</div>
		<div class="col-xs-6 form-group input-material" style="display:none;" use="offlinePaymentOptionWrap" for="NEFT" actAs='fieldContainer'>
			<label for="user_first_name">Date</label>
			<input type="date" class="form-control" name="neft_date" id="neft_date" max="<?=$mycms->cDate("Y-m-d")?>" min="<?=$mycms->cDate("Y-m-d","-6 Months")?>">
			<div class = "alert alert-danger" callFor='alert'>Please choose a proper option.</div>
		</div>
		<div class="col-xs-12 form-group input-material" style="display:none;" use="offlinePaymentOptionWrap" for="NEFT">
			<?=$cfg['neft_info']?>
		</div>
		<div class="col-xs-12 form-group input-material" style="display:none;" use="offlinePaymentOptionWrap" for="UPI" >
			<?=$cfg['upi_info']?>
		</div>
	
		<div class="col-xs-12 form-group input-material" style="display:none;" use="offlinePaymentOptionWrap" for="RTGS" actAs='fieldContainer'>
			<label for="user_first_name">Transaction Id</label>
			<input type="text" class="form-control" name="rtgs_transaction_no" id="rtgs_transaction_no">
			<div class = "alert alert-danger" callFor='alert'>Please enter a proper value.</div>
		</div>
		<div class="col-xs-6 form-group input-material" style="display:none;" use="offlinePaymentOptionWrap" for="RTGS" actAs='fieldContainer'>
			<label for="user_first_name">Drawee Bank</label>
			<input type="text" class="form-control" name="rtgs_bank_name" id="rtgs_bank_name">
			<div class = "alert alert-danger" callFor='alert'>Please enter a proper value.</div>
		</div>
		<div class="col-xs-6 form-group input-material" style="display:none;" use="offlinePaymentOptionWrap" for="RTGS" actAs='fieldContainer'>
			<label for="user_first_name">Date</label>
			<input type="date" class="form-control" name="rtgs_date" id="rtgs_date" max="<?=$mycms->cDate("Y-m-d")?>" min="<?=$mycms->cDate("Y-m-d","-6 Months")?>">
			<div class = "alert alert-danger" callFor='alert'>Please choose a proper option.</div>
		</div>
		<div class="col-xs-12 form-group input-material" style="display:none;" use="offlinePaymentOptionWrap" for="RTGS">
			<?=$cfg['neft_info']?>
		</div>
	
		<div class="col-xs-12 form-group" use="conditionOption" actAs='fieldContainer'>
			<div class="checkbox">
				<label class="container-box"> 
					By Clicking, you hereby agree to receive all promotional SMS and e-mails related to ISAR 24. To unsubscribe, send us a mail at isarbbsr2024@gmail.com.
					<input type="checkbox" name="acceptance1" value="acceptance1" required>
					<span class="checkmark"></span>
				</label>
			</div>
			<div class = "alert alert-danger" callFor='alert'>Please choose option.</div>	
			<div class="checkbox">
				<label class="container-box">
					I have read and clearly understood the 
					<a href="<?=_BASE_URL_?>terms.php" title="Click to View 'Terms &amp; Conditions'" target="_blank" class="anclink">Terms &amp; Conditions</a> 
					and 
					<a href="<?=_BASE_URL_?>cancellation.php" title="Click to View 'Cancellation &amp; Refund Policy'" target="_blank" class="anclink">Cancellation &amp; Refund Policy</a> 
					and agree with the same. 
					<input type="checkbox" name="acceptance2" value="acceptance2" required>
					<span class="checkmark"></span>
				</label>										
			</div>
			<div class = "alert alert-danger" callFor='alert'>Please choose option.</div>
		</div> 
	 
		<div class=" col-xs-12 text-center pull-right">
			<button type="submit" class="submit" operationMode='paymentSubmit'>Proceed to Pay</button>
		</div>      
		<div class="clearfix"></div>
	</div>
	<div use="registrationProcess" style="display:none; text-align:center;" class="rightPanel_payment">
		<img src="<?=_BASE_URL_?>images/PaymentPreloader.gif"/>
	</div>

	<script type="text/javascript">
		/*function calculateTotalAmountInProfile(section)
		{	
			// accommodation related work for profile by weavers start
			
			var totalAmount = 0;
		    //var totTable    = $("div[use=registrationPaymentWrap]").find("table[use=totalAmountTable]");
		    var totTable    = $("div[use=registrationPaymentWrap] table[use=totalAmountTable]");
		    $(totTable).children("tbody").find("tr").remove();
		   
		    //$.each($("input[type=checkbox][operationMode=workshopId_nov]:checked,input[type=checkbox][operationMode=workshopId]:checked"),function(){
		    $.each($("div[operationData="+section+"] input[type=checkbox]:checked,div[operationData="+section+"] input[type=radio]:checked"),function(){
		        var attr = $(this).attr('amount');
		        
		        if (typeof attr !== typeof undefined && attr !== false) 
		        {
		            var amt     = parseFloat(attr); 
		            totalAmount = totalAmount+amt;
		            var attrReg = $(this).attr('operationMode');
		            var cloneIt = false;
		            var amtAlterTxt = 'Included in Registration';
		            console.log('totalAmount==>'+totalAmount)
		            if(amt > 0)
		            {
		                cloneIt = true;
		            }
		            
		            if(cloneIt)
		            {                           
		                var cloned  = $(totTable).children("tfoot").find("tr[use=rowCloneable]").first().clone();                                                   
		                $(cloned).attr("use","rowCloned");
		                $(cloned).find("span[use=invTitle]").text($.trim($(this).attr('invoiceTitle')));                                    
		                $(cloned).find("span[use=amount]").text((amt>0)?(amt).toFixed(2):amtAlterTxt);
		                $(cloned).show();
		                $(totTable).children("tbody").append(cloned);
		            }
		        }
		        //console.log($(this))
		        if($(this).attr('operationMode')=='registrationModePaymemt' && $(this).attr('use')=='tariffPaymentModePaymemt')
		       	//if($(this).attr('paymentMode')=='ONLINE' && $(this).attr('use')=='payment_mode_select')
		        {
		            if($(this).val()=='ONLINE')
		            //if($(this).val()=='Card')
		            {
		                var internetHandling = <?=$cfg['INTERNET.HANDLING.PERCENTAGE']?>;
		                var internetAmount = (totalAmount*internetHandling)/100;
		                totalAmount = totalAmount+internetAmount;
		                
		                console.log(">>inter"+internetHandling+' ==> '+internetHandling);
		                
		                var cloned  = $(totTable).children("tfoot").find("tr[use=rowCloneable]").first().clone();   
		                                
		                $(cloned).attr("use","rowCloned");
		                $(cloned).find("span[use=invTitle]").text("Internet Handling Charge");
		                $(cloned).find("span[use=amount]").text((internetAmount).toFixed(2));
		                $(cloned).show();
		                $(totTable).children("tbody").append(cloned);
		            }
		        }
		        
		    });
		    
		    totalAmount = Math.round(totalAmount,0);
		    
		    $(totTable).children("tfoot").find("span[use=totalAmount]").text((totalAmount).toFixed(2));
		    //$("div[use=totalAmount]").find("span[use=totalAmount]").text((totalAmount).toFixed(2));
		    //$("div[use=totalAmount]").find("span[use=totalAmount]").attr('theAmount',totalAmount);
		    //$("div[use=totalAmount]").show();
		    //$("table[use=totalAmountTable]").show();
		    
		    $("div[use=registrationPaymentWrap] div[use=totalAmount]").find("span[use=totalAmount]").text((totalAmount).toFixed(2));
		    $("div[use=registrationPaymentWrap] div[use=totalAmount]").find("span[use=totalAmount]").attr('theAmount',totalAmount);
		    $("div[use=registrationPaymentWrap] div[use=totalAmount]").show();
		    $("div[use=registrationPaymentWrap] table[use=totalAmountTable]").show();
		    // accommodation related work for profile by weavers end
		}*/

		function calculateTotalAmountInProfile(section,days="")
		{	
			// accommodation related work for profile by weavers start
			//alert(section);
			var totalAmount = 0;
		    //var totTable    = $("div[use=registrationPaymentWrap]").find("table[use=totalAmountTable]");
		    var totTable    = $("div[use=registrationPaymentWrap] table[use=totalAmountTable]");
		    $(totTable).children("tbody").find("tr").remove();

		    var checkInVal= $('#accomodation_package_checkin_id').val();
			var checkOutVal= $('#accomodation_package_checkout_id').val();	

			
			 if(typeof checkOutVal !== 'undefined' && typeof checkInVal !== 'undefined')
			 {
			 	const checkInArray = checkInVal.split("/");
				var checkInID = checkInArray[0];
				var checkInDate = checkInArray[1];

				const checkOutArray = checkOutVal.split("/");
				var checkOutID = checkOutArray[0];
				var checkOutDate = checkOutArray[1];

				var date1 = new Date(checkInDate);
				var date2 = new Date(checkOutDate);

				// Calculate the difference in milliseconds
				var differenceMs = Math.abs(date2 - date1);
				var accommodation_room = $('#accommodation_room').val();
				 if (typeof accommodation_room !== typeof undefined && accommodation_room !== false && !isNaN(accommodation_room)) 
				 {
				 	var roomQty = accommodation_room;
				 }
				 else
				 {
				 	var roomQty = 1;
				 }

				// Convert the difference to days
				var differenceDays = Math.ceil(differenceMs / (1000 * 60 * 60 * 24));
			 }

		  
		   
		    //$.each($("input[type=checkbox][operationMode=workshopId_nov]:checked,input[type=checkbox][operationMode=workshopId]:checked"),function(){
		    $.each($("div[operationData="+section+"] input[type=checkbox]:checked,div[operationData="+section+"] input[type=radio]:checked"),function(index, element){
		    		
		    		 console.log('key='+index);
			    	if (!$(this).prop('disabled')) 
			    	{
				        var attr = $(this).attr('amount');

				       // alert('atr='+attr);
				        if(differenceDays!='' && differenceDays>0)
				        {
				        	  attr = parseFloat(attr)*parseInt(differenceDays)*parseInt(roomQty);
				        }
				        
				        if (typeof attr !== typeof undefined && attr !== false && !isNaN(attr)) 
				        {
				            var amt     = parseFloat(attr); 
				           
				            var gst_flag = $('#gst_flag').val();
				            if(gst_flag==1)
		                    {
		                    	var cgstP = <?=$cfg['INT.CGST']?>;
		                    	var cgstAmnt = (amt * cgstP) / 100;

		                   		var sgstP = <?=$cfg['INT.SGST']?>;
		                    	var sgstAmnt = (amt * sgstP) / 100;

		                    	var totalGst = cgstAmnt + sgstAmnt;
		                    	var totalGstAmount = cgstAmnt + sgstAmnt + amt;
		                    	totalAmount = parseFloat(totalAmount) + parseFloat(totalGstAmount);
		                    }
		                    else
		                    {
		                    	totalAmount = parseFloat(totalAmount) + parseFloat(amt);
		                    }

		                    //alert(totalAmount);
				            var attrReg = $(this).attr('operationMode');
				            var cloneIt = false;
				            var amtAlterTxt = 'Included in Registration';
				            console.log('totalAmount==>'+totalAmount)
				            if(amt > 0)
				            {
				                cloneIt = true;
				            }
				            
					            if(cloneIt)
					            {                           
					                var cloned  = $(totTable).children("tfoot").find("tr[use=rowCloneable]").first().clone();                                                   
					                $(cloned).attr("use","rowCloned");
					                $(cloned).find("span[use=invTitle]").text($.trim($(this).attr('invoiceTitle')));                                    
					                $(cloned).find("span[use=amount]").text((amt>0)?(amt).toFixed(2):amtAlterTxt);
					                $(cloned).show();
					                $(totTable).children("tbody").append(cloned);
					            }

				            	if(gst_flag==1)
		                        {
			                        if (cloneIt) {

			                            var cgstP = <?=$cfg['INT.CGST']?>;
			                            var cgstAmnt = (amt * cgstP) / 100;

			                            var sgstP = <?=$cfg['INT.SGST']?>;
			                            var sgstAmnt = (amt * sgstP) / 100;

			                            var totalGst = cgstAmnt + sgstAmnt;
			                            var totalGstAmount = cgstAmnt + sgstAmnt + amt;


			                            var cloned = $(totTable).children("tfoot").find("tr[use=rowCloneable]").first()
			                                .clone();
			                            $(cloned).attr("use", "rowCloned");
			                            $(cloned).find("span[use=invTitle]").text("GST 18%");
			                            $(cloned).find("span[use=amount]").text((totalGst).toFixed(2));
			                            $(cloned).show();
			                            $(totTable).children("tbody").append(cloned);
			                        }
			                    }
				        }
				       
				       
				        //alert(totalAmount);
				         if($(this).val()=='Card')
					   	 {

					    			var internetHandling = <?=$cfg['INTERNET.HANDLING.PERCENTAGE']?>;
					                var internetAmount = (totalAmount*internetHandling)/100;
					                totalAmount = totalAmount+internetAmount;
					                
					                console.log(">>inter"+internetHandling+' ==> '+internetHandling);
					                
					                var cloned  = $(totTable).children("tfoot").find("tr[use=rowCloneable]").first().clone();   
					                                
					                $(cloned).attr("use","rowCloned");
					                $(cloned).find("span[use=invTitle]").text("Internet Handling Charge");
					                $(cloned).find("span[use=amount]").text((internetAmount).toFixed(2));
					                $(cloned).show();
					                $(totTable).children("tbody").append(cloned);
					    } 
					    
			    	}


			    	
			   		 if($(this).attr('operationMode')=='registrationModePaymemt' && $(this).attr('use')=='tariffPaymentModePaymemt')
				       	//if($(this).attr('paymentMode')=='ONLINE' && $(this).attr('use')=='payment_mode_select')
				        {
				        	//alert(12);
				            if($(this).val()=='ONLINE')
				            //if($(this).val()=='Card')
				            {

				                /*var internetHandling = <?=$cfg['INTERNET.HANDLING.PERCENTAGE']?>;
				                var internetAmount = (totalAmount*internetHandling)/100;
				                totalAmount = totalAmount+internetAmount;
				                
				                console.log(">>inter"+internetHandling+' ==> '+internetHandling);
				                
				                var cloned  = $(totTable).children("tfoot").find("tr[use=rowCloneable]").first().clone();   
				                                
				                $(cloned).attr("use","rowCloned");
				                $(cloned).find("span[use=invTitle]").text("Internet Handling Charge");
				                $(cloned).find("span[use=amount]").text((internetAmount).toFixed(2));
				                $(cloned).show();
				                $(totTable).children("tbody").append(cloned);*/
				            }
				        }
				       // alert(totalAmount);
				      	


		        
		    });
		    
		    totalAmount = Math.round(totalAmount,0);
		    
		    $(totTable).children("tfoot").find("span[use=totalAmount]").text((totalAmount).toFixed(2));
		    //$("div[use=totalAmount]").find("span[use=totalAmount]").text((totalAmount).toFixed(2));
		    //$("div[use=totalAmount]").find("span[use=totalAmount]").attr('theAmount',totalAmount);
		    //$("div[use=totalAmount]").show();
		    //$("table[use=totalAmountTable]").show();
		    
		    $("div[use=registrationPaymentWrap] div[use=totalAmount]").find("span[use=totalAmount]").text((totalAmount).toFixed(2));
		    $("div[use=registrationPaymentWrap] div[use=totalAmount]").find("span[use=totalAmount]").attr('theAmount',totalAmount);
		    $("div[use=registrationPaymentWrap] div[use=totalAmount]").show();
		    $("div[use=registrationPaymentWrap] table[use=totalAmountTable]").show();
		    // accommodation related work for profile by weavers end
		}

		function calculateTotalAmountInProfileAccommodationRoom(section,days="")
		{	
			// accommodation related work for profile by weavers start
			//alert(section);
			var totalAmount = 0;
		    //var totTable    = $("div[use=registrationPaymentWrap]").find("table[use=totalAmountTable]");
		    var totTable    = $("div[use=registrationPaymentWrap] table[use=totalAmountTable]");
		    $(totTable).children("tbody").find("tr").remove();

		    

		  
		   
		    //$.each($("input[type=checkbox][operationMode=workshopId_nov]:checked,input[type=checkbox][operationMode=workshopId]:checked"),function(){
			$.each($("div[operationData="+section+"] input[type=radio]:checked, div[operationData="+section+"] .accomodation_package_checkout_id option:selected"),function(index, element){
			    		 
			    		var incrementKey = index + 1;
			    		var checkInVal= $('#accomodation_package_checkin_id'+incrementKey).val();
						var checkOutVal= $('#accomodation_package_checkout_id'+incrementKey).val();	

						var attr = $(this).attr('amount');

					   //  alert($(this).val());
						
					 if(typeof checkOutVal !== 'undefined' && typeof checkInVal !== 'undefined')
					 {

							 	//alert(checkOutVal);
							 	const checkInArray = checkInVal.split("/");
								var checkInID = checkInArray[0];
								var checkInDate = checkInArray[1];

								const checkOutArray = checkOutVal.split("/");
								var checkOutID = checkOutArray[0];
								var checkOutDate = checkOutArray[1];

								var date1 = new Date(checkInDate);
								var date2 = new Date(checkOutDate);

								// Calculate the difference in milliseconds
								var differenceMs = Math.abs(date2 - date1);
								var accommodation_room = $('#accommodation_room').val();
								 if (typeof accommodation_room !== typeof undefined && accommodation_room !== false && !isNaN(accommodation_room)) 
								 {
								 	var roomQty = accommodation_room;
								 }
								 else
								 {
								 	var roomQty = 1;
								 }

									// Convert the difference to days
								var differenceDays = Math.ceil(differenceMs / (1000 * 60 * 60 * 24));


								if (!$(this).prop('disabled')) 
						    	{
								        var attr = $(this).attr('amount');

								       // alert('atr='+attr);
								        if(differenceDays!='' && differenceDays>0)
								        {
								        	  attr = parseFloat(attr)*parseInt(differenceDays)*parseInt(roomQty);
								        }
							        
								        if (typeof attr !== typeof undefined && attr !== false && !isNaN(attr)) 
								        {
								            var amt     = parseFloat(attr); 
								           	//alert(amt);
								            var gst_flag = $('#gst_flag').val();
								            if(gst_flag==1)
						                    {
						                    	var cgstP = <?=$cfg['INT.CGST']?>;
						                    	var cgstAmnt = (amt * cgstP) / 100;

						                   		var sgstP = <?=$cfg['INT.SGST']?>;
						                    	var sgstAmnt = (amt * sgstP) / 100;

						                    	var totalGst = cgstAmnt + sgstAmnt;
						                    	var totalGstAmount = cgstAmnt + sgstAmnt + amt;
						                    	totalAmount = parseFloat(totalAmount) + parseFloat(totalGstAmount);
						                    }
						                    else
						                    {
						                    	totalAmount = parseFloat(totalAmount) + parseFloat(amt);
						                    }

						                    //alert(totalAmount);
								            var attrReg = $(this).attr('operationMode');
								            var cloneIt = false;
								            var amtAlterTxt = 'Included in Registration';
								            console.log('totalAmount==>'+totalAmount)
								            if(amt > 0)
								            {
								                cloneIt = true;
								            }
								            
									            if(cloneIt)
									            {                           
									                var cloned  = $(totTable).children("tfoot").find("tr[use=rowCloneable]").first().clone();                                                   
									                $(cloned).attr("use","rowCloned");
									                $(cloned).find("span[use=invTitle]").text($.trim($(this).attr('invoiceTitle')));                                    
									                $(cloned).find("span[use=amount]").text((amt>0)?(amt).toFixed(2):amtAlterTxt);
									                $(cloned).show();
									                $(totTable).children("tbody").append(cloned);
									            }

								            	if(gst_flag==1)
						                        {
							                        if (cloneIt) {

							                            var cgstP = <?=$cfg['INT.CGST']?>;
							                            var cgstAmnt = (amt * cgstP) / 100;

							                            var sgstP = <?=$cfg['INT.SGST']?>;
							                            var sgstAmnt = (amt * sgstP) / 100;

							                            var totalGst = cgstAmnt + sgstAmnt;
							                            var totalGstAmount = cgstAmnt + sgstAmnt + amt;


							                            var cloned = $(totTable).children("tfoot").find("tr[use=rowCloneable]").first()
							                                .clone();
							                            $(cloned).attr("use", "rowCloned");
							                            $(cloned).find("span[use=invTitle]").text("GST 18%");
							                            $(cloned).find("span[use=amount]").text((totalGst).toFixed(2));
							                            $(cloned).show();
							                            $(totTable).children("tbody").append(cloned);
							                        }
							                    }
								        }
							       
							       
							      //  alert($(this).val());
								       
								    
						    	}

					    	 


				    	
					   		   if($(this).attr('operationMode')=='registrationModePaymemt' && $(this).attr('use')=='tariffPaymentModePaymemt')
						       	//if($(this).attr('paymentMode')=='ONLINE' && $(this).attr('use')=='payment_mode_select')
						        {
						        	//alert(12);
						            if($(this).val()=='ONLINE')
						            //if($(this).val()=='Card')
						            {

						                /*var internetHandling = <?=$cfg['INTERNET.HANDLING.PERCENTAGE']?>;
						                var internetAmount = (totalAmount*internetHandling)/100;
						                totalAmount = totalAmount+internetAmount;
						                
						                console.log(">>inter"+internetHandling+' ==> '+internetHandling);
						                
						                var cloned  = $(totTable).children("tfoot").find("tr[use=rowCloneable]").first().clone();   
						                                
						                $(cloned).attr("use","rowCloned");
						                $(cloned).find("span[use=invTitle]").text("Internet Handling Charge");
						                $(cloned).find("span[use=amount]").text((internetAmount).toFixed(2));
						                $(cloned).show();
						                $(totTable).children("tbody").append(cloned);*/
						            }
						        }
				  	 }


				  	 if($(this).val()=='Card')
				   	 {

				    			var internetHandling = <?=$cfg['INTERNET.HANDLING.PERCENTAGE']?>;
				                var internetAmount = (totalAmount*internetHandling)/100;
				                totalAmount = totalAmount+internetAmount;
				                
				                console.log(">>inter"+internetHandling+' ==> '+internetHandling);
				                
				                var cloned  = $(totTable).children("tfoot").find("tr[use=rowCloneable]").first().clone();   
				                                
				                $(cloned).attr("use","rowCloned");
				                $(cloned).find("span[use=invTitle]").text("Internet Handling Charge");
				                $(cloned).find("span[use=amount]").text((internetAmount).toFixed(2));
				                $(cloned).show();
				                $(totTable).children("tbody").append(cloned);
				    } 

				    	
			  });
		    
		    totalAmount = Math.round(totalAmount,0);
		    
		    $(totTable).children("tfoot").find("span[use=totalAmount]").text((totalAmount).toFixed(2));
		    //$("div[use=totalAmount]").find("span[use=totalAmount]").text((totalAmount).toFixed(2));
		    //$("div[use=totalAmount]").find("span[use=totalAmount]").attr('theAmount',totalAmount);
		    //$("div[use=totalAmount]").show();
		    //$("table[use=totalAmountTable]").show();
		    
		    $("div[use=registrationPaymentWrap] div[use=totalAmount]").find("span[use=totalAmount]").text((totalAmount).toFixed(2));
		    $("div[use=registrationPaymentWrap] div[use=totalAmount]").find("span[use=totalAmount]").attr('theAmount',totalAmount);
		    $("div[use=registrationPaymentWrap] div[use=totalAmount]").show();
		    $("div[use=registrationPaymentWrap] table[use=totalAmountTable]").show();
		    // accommodation related work for profile by weavers end
		}
	</script>
<?

}

?>
<script type="text/javascript">
	// workshop related work for profile by weavers start
	$(document).ready(function() {
		popDownAlert();
	})

	function addWorkShopFromProfile(obj,what){
		$("div[operationMode=profileData]").hide();
		$("div[operationMode=profileData][operationData='"+what+"']").slideDown();
		$('#frmAddWorkshopfromProfile table').show();
	}

	function addDinnerFromProfile(obj,what)
	{
		$("div[operationMode=profileData]").hide();
		$("div[operationMode=profileData][operationData='"+what+"']").slideDown();
		$('#frmAddDinnerfromProfile table').show();
	}

	function showPaymentSection(obj) {

		var novemberWorkshop = $("input[type=checkbox][operationMode=workshopId_nov]:checked").length;
		var decemberWorkshop = $("input[type=checkbox][operationMode=workshopId]:checked").length;
        if(novemberWorkshop > 0 || decemberWorkshop > 0){
			popDownAlert();
			$("div[operationMode=profileData]").hide();
			$("div[operationMode=profileData][operationData='workshopAdd']").slideDown();
			$('h5[sequenceNo=1]').hide();
			$('#frmAddWorkshopfromProfile table').hide();
			$(obj).parent().hide();
			$("div[use=registrationPayment]").show();
		}else{
			popoverAlert(obj);
		}
		calculateTotalWorkshoAmountInProfile();

		//
		var workshopAmount = $("table[use=totalAmountTable]").find("td span[use=totalAmount]").attr('theamount');
		if(parseFloat(workshopAmount) == 0)
		{
			$("div[use=registrationPayment]").find("div[use=offlinePaymentOptionChoice]").hide();
			$("div[use=registrationPayment]").find("div[use=conditionOption]").hide();
			$("div[use=registrationPayment]").find("div[use=conditionOption] input[type=checkbox]").prop('required',false);
			$("div[use=registrationPayment]").find("button[use=nextButton]").parent().hide();
			$("div[use=complimentrayWorkshop]").show();
		}
		else{
			$("div[use=registrationPayment]").find("div[use=offlinePaymentOptionChoice]").show();
			$("div[use=registrationPayment]").find("div[use=conditionOption]").show();
			$("div[use=registrationPayment]").find("div[use=conditionOption] input[type=checkbox]").prop('required',true);
			$("div[use=registrationPayment]").find("button[use=nextButton]").parent().show();
			$("div[use=complimentrayWorkshop]").hide();
		}

	}

	function showPaymentSectionDinner(obj){
		var novemberWorkshop = $("input[type=checkbox][operationMode=dinnerId_nov]:checked").length;
		var decemberWorkshop = $("input[type=checkbox][operationMode=dinnerId]:checked").length;
        if(novemberWorkshop > 0 || decemberWorkshop > 0){
			popDownAlert();
			$("div[operationMode=profileData]").hide();
			$("div[operationMode=profileData][operationData='dinnerAdd']").slideDown();
			$('h5[sequenceNos=11]').hide();
			$('#frmAddDinnerfromProfile table').hide();
			$(obj).parent().hide();
			$("div[use=registrationPayment]").show();
		}else{
			popoverAlert(obj);
		}
		calculateTotalDinnerAmountInProfile();

		//
		var workshopAmount = $("table[use=totalAmountTable]").find("td span[use=totalAmount]").attr('theamount');
		if(parseFloat(workshopAmount) == 0)
		{
			$("div[use=registrationPayment]").find("div[use=offlinePaymentOptionChoice]").hide();
			$("div[use=registrationPayment]").find("div[use=conditionOption]").hide();
			$("div[use=registrationPayment]").find("div[use=conditionOption] input[type=checkbox]").prop('required',false);
			$("div[use=registrationPayment]").find("button[use=nextButton]").parent().hide();
			$("div[use=complimentrayWorkshop]").show();
		}
		else{
			$("div[use=registrationPayment]").find("div[use=offlinePaymentOptionChoice]").show();
			$("div[use=registrationPayment]").find("div[use=conditionOption]").show();
			$("div[use=registrationPayment]").find("div[use=conditionOption] input[type=checkbox]").prop('required',true);
			$("div[use=registrationPayment]").find("button[use=nextButton]").parent().show();
			$("div[use=complimentrayWorkshop]").hide();
		}
	}

		
	function popoverAlert(obj, msg)
	{
		var parent 		= $(obj).parent().closest("div[actAs=fieldContainer]");
		var alertObj 	= $(parent).children("div[callFor=alert]");
		
		var attr = $(alertObj).attr('defaultAlert');
		if (typeof attr === typeof undefined || attr === false) 
		{
			$(alertObj).attr('defaultAlert', $(alertObj).text());
			$(alertObj).click(function(){
				popDownAlert(this);
			});
		}						
		
		if(typeof msg !== typeof undefined && $.trim(msg) !== '')
		{
			$(alertObj).text(msg);
		}
		else
		{
			$(alertObj).text($(alertObj).attr('defaultAlert'));
		}
		
		$(alertObj).show();
	}
	
	function popDownAlert(obj)
	{
		if(typeof obj === typeof undefined)
		{
			$("div[callFor=alert]").hide();
		}
		else
		{
			$(obj).hide();
		}
	}
	$("button[use=nextButton][sequenceno='2']").click(function(evnt){
		try{

			returnVal = validateRegistrationPayment();
			if(!returnVal) {
				return false;
				evnt.preventDefault();
			}else{
				//evnt.preventDefault();
				//alert('Form submitting.....')
			}
		}
		catch(e)
		{
			console.log('ERROR : '+e.message);
			evnt.preventDefault();
		}
	})

	$("button[use=nextButton][sequenceno='5']").click(function(evnt){
		try{

			returnVal = validateRegistrationPaymentDinner();
			if(!returnVal) {
				return false;
				evnt.preventDefault();
			}else{
				//evnt.preventDefault();
				//alert('Form submitting.....')
			}
		}
		catch(e)
		{
			console.log('ERROR : '+e.message);
			evnt.preventDefault();
		}
	})
	

	$("input[type=radio][use=tariffPaymentMode]").click(function(){
	    var forPay = $(this).attr("for");
	    $("div[use=payRules]").hide();
	    $("div[use=payRules][for='"+forPay+"']").slideDown();
	    
	    $("div[use=offlinePaymentOptionChoice]").hide();
	    $("div[use=offlinePaymentOption]").hide();
	    
	    $("div[use=offlinePaymentOptionChoice]").find("input[type=radio]").prop("checked",false);
	    $("div[use=offlinePaymentOption]").find("input[type=text]").val('');
	    $("div[use=offlinePaymentOption]").find("input[type=date]").val('');
	    
	    if($(this).val()=='OFFLINE')
	    {
	        $("div[use=offlinePaymentOptionChoice]").slideDown();
	        if(forPay == 'CHQ')
	        {
	            $("div[use=offlinePaymentOptionChoice]").find("input[type=radio][use=payment_mode_select][for=Cheque]").trigger("click");
	        }
	        else if(forPay == 'DD')
	        {
	            $("div[use=offlinePaymentOptionChoice]").find("input[type=radio][use=payment_mode_select][for=Draft]").trigger("click");
	        }
	        else if(forPay == 'WIRE')
	        {
	            $("div[use=offlinePaymentOptionChoice]").find("input[type=radio][use=payment_mode_select][for=NEFT]").trigger("click");
	        }
	        else if(forPay == 'CASH')
	        {
	            //$("div[use=offlinePaymentOptionChoice]").find("input[type=radio][use=payment_mode_select][for=Cash]").prop("checked",true);
	            $("div[use=offlinePaymentOptionChoice]").find("input[type=radio][use=payment_mode_select][for=Cash]").trigger("click");
	        }
	    }
	}); 


	$("div[use=offlinePaymentOptionChoice]").find("input[type=radio]").click(function(){							
		var forPay = $(this).attr("for");
		$("div[use=offlinePaymentOption]").hide();
		$("div[use=offlinePaymentOption][for='"+forPay+"']").slideDown();
		
		var paymentMode = $(this).attr("paymentMode");							
		$("input[type=radio][use=tariffPaymentMode]").prop("checked",false);
		$("input[type=radio][use=tariffPaymentMode][value='"+paymentMode+"']").first().prop("checked",true);	
		calculateTotalWorkshoAmountInProfile();			
	});

	function validateRegistrationPayment() {
	 
	    var theAmount               = parseFloat($("div[use=totalAmount]").find("span[use=totalAmount]").attr('theAmount'));
	    if(theAmount > 0)
	    {
	        var paymentModeObj          = $("div[use=registrationPaymentOption]").find("input[type=radio][name=registrationMode]:checked");
	        var paymentMode             = $(paymentModeObj).val();
	        var paymentOptionCheckedOb  = $("div[use=registrationPayment]").find("input[type=radio][name=payment_mode]:checked");
	        //alert(paymentMode);
	        if($(paymentOptionCheckedOb).length == 0)
	        {
	            var paymentOptionObj    = $("div[use=registrationPayment]").find("input[type=radio][name=payment_mode]").first();
	            console.log('paymentOptionCheckedOb>>NOT SELECTED');
	            popoverAlert(paymentOptionObj);
	            $(paymentOptionObj).focus();
	            return false;
	        }
	        if(paymentMode=='OFFLINE')
	        {   
	            var returnVal               = true;
	            var paymentOptionChecked    = $(paymentOptionCheckedOb).attr("for");
	            $.each($("div[id=registrationPaymentWorkshop]").find("div[use=offlinePaymentOption][for='"+paymentOptionChecked+"']"),function(){
	                var thiObj = $(this);
	                $.each($(thiObj).find("input[type=text],input[type=date]"),function(i,validateObj){
	                    if($.trim($(validateObj).val())=='')
	                    {
	                        console.log('pay details>>BLANK');
	                        popoverAlert(validateObj);
	                        $(validateObj).focus();
	                        returnVal = false;
	                        return false;
	                    }
	                }); 
	                if(!returnVal) return false;    
	            });
	            if(!returnVal) return false;    
	        }
	        else if(paymentMode=='ONLINE')
	        {
	            var returnVal               = true;
	            var paymentOptionChecked    = $(paymentOptionCheckedOb).attr("for");
	            $.each($("div[use=registrationPayment]").find("div[use=offlinePaymentOption][for='"+paymentOptionChecked+"']"),function(){
	                var thiObj = $(this);
	                $.each($(thiObj).find("input[type=text],input[type=date]"),function(i,validateObj){
	                    if($.trim($(validateObj).val())=='')
	                    {
	                        console.log('pay details>>BLANK');
	                        popoverAlert(validateObj);
	                        $(validateObj).focus();
	                        returnVal = false;
	                        return false;
	                    }
	                }); 
	                $.each($(thiObj).find("input[type=radio],input[type=checkbox]"),function(i,validateObj){
	                    var name        = $(validateObj).attr("name")
	                    var type        = $(validateObj).attr("type")
	                    var parent      = $(validateObj).parent().closest("div[actAs=fieldContainer]");
	                    var checkedObj  = $(parent).find("input[type='"+type+"'][name='"+name+"']:checked");
	                    if($(checkedObj).length == 0)
	                    {
	                        var checkedOptionObj    = $(parent).find("input[type='"+type+"'][name='"+name+"']").first();                                                                
	                        console.log('checkedOptionObj>>NOT SELECTED');
	                        popoverAlert(checkedOptionObj);
	                        $(checkedOptionObj).focus();
	                        return false;
	                    }
	                }); 
	                if(!returnVal) return false;    
	            });
	            if(!returnVal) return false;    
	        }                       
	        if(!$("div[use=registrationPayment]").find("input[type=checkbox][name=acceptance1]").is(":checked"))
	        {
	            var valOb1 = $("div[use=registrationPayment]").find("input[type=checkbox][name=acceptance1]");
	            popoverAlert(valOb1);
	            $(valOb1).focus();
	            return false;
	        }
	        if(!$("div[use=registrationPayment]").find("input[type=checkbox][name=acceptance2]").is(":checked"))
	        {
	            var valOb2 = $("div[use=registrationPayment]").find("input[type=checkbox][name=acceptance2]");
	            popoverAlert(valOb2);
	            $(valOb2).focus();
	            return false;
	        }
	    }
	    else
	    {
	         $("div[use=registrationPayment]").find("input[type=radio][name=payment_mode]").last().prop("checked",true);
	    }
	    return true;
	}

	/*function validateRegistrationPaymentDinner() {
	 
	    var theAmount = parseFloat($("div[use=totalAmount]").find("span[use=totalAmount]").attr('theAmount'));
	    if(theAmount > 0)
	    {
	        var paymentModeObj          = $("div[id=registrationPaymentDinner]").find("input[type=radio][name=payment_mode]:checked");
	        var paymentMode             = $(paymentModeObj).val();
	        alert(paymentMode);
	       
	        var paymentOptionCheckedOb  = $("div[id=registrationPaymentDinner]").find("input[type=radio][name=payment_mode]:checked");
	        if($(paymentOptionCheckedOb).length == 0)
	        {
	            var paymentOptionObj    = $("div[use=registrationPayment]").find("input[type=radio][name=payment_mode]").first();
	            console.log('paymentOptionCheckedOb>>NOT SELECTED');
	            //popoverAlert(paymentOptionObj);
	            $('#payment_error_msg').show();
	            $(paymentOptionObj).focus();
	            return false;
	        }
	        alert(paymentMode);
	        if(paymentMode=='Cheque' || paymentMode=='Draft' || paymentMode=='NEFT' || paymentMode=='RTGS' || paymentMode=='Cash' || paymentMode=='UPI')
	        {   
	            var returnVal               = true;
	            var paymentOptionChecked    = $(paymentOptionCheckedOb).attr("for");
	            $.each($("div[id=registrationPaymentDinner]").find("div[use=offlinePaymentOption][for='"+paymentOptionChecked+"']"),function(){
	                var thiObj = $(this);
	                $.each($(thiObj).find("input[type=text],input[type=date]"),function(i,validateObj){
	                    if($.trim($(validateObj).val())=='')
	                    {
	                        console.log('pay details>>BLANK');
	                        popoverAlert(validateObj);
	                        $(validateObj).focus();
	                        returnVal = false;
	                        return false;
	                    }
	                }); 
	                if(!returnVal) return false;    
	            });
	            if(!returnVal) return false;    
	        }
	        else if(paymentMode=='Card')
	        {
	            var returnVal               = true;
	            
	            if(!returnVal) return false;    
	        }                       
	        if(!$("div[use=registrationPayment]").find("input[type=checkbox][name=acceptance1]").is(":checked"))
	        {
	            var valOb1 = $("div[use=registrationPayment]").find("input[type=checkbox][name=acceptance1]");
	            popoverAlert(valOb1);
	            $(valOb1).focus();
	            return false;
	        }
	        if(!$("div[use=registrationPayment]").find("input[type=checkbox][name=acceptance2]").is(":checked"))
	        {
	            var valOb2 = $("div[use=registrationPayment]").find("input[type=checkbox][name=acceptance2]");
	            popoverAlert(valOb2);
	            $(valOb2).focus();
	            return false;
	        }
	    }
	    else
	    {
	         $("div[use=registrationPayment]").find("input[type=radio][name=payment_mode]").last().prop("checked",true);
	    }
	    return true;
	}*/

	function validateRegistrationPaymentDinner() {

	 
	    var theAmount = parseFloat($("div[id=registrationPaymentDinner]").find("span[use=totalAmount]").attr('theAmount'));
	    //alert(theAmount);
	    if(theAmount > 0)
	    {
	        var paymentModeObj          = $("div[id=registrationPaymentDinner]").find("input[type=radio][name=payment_mode]:checked");
	        var paymentMode             = $(paymentModeObj).val();
	        //alert(paymentMode);
	       
	        var paymentOptionCheckedOb  = $("div[id=registrationPaymentDinner]").find("input[type=radio][name=payment_mode]:checked");
	        if($(paymentOptionCheckedOb).length == 0)
	        {
	            var paymentOptionObj    = $("div[use=registrationPayment]").find("input[type=radio][name=payment_mode]").first();
	            console.log('paymentOptionCheckedOb>>NOT SELECTED');
	            //popoverAlert(paymentOptionObj);
	            $('#payment_error_msg').show();
	            $(paymentOptionObj).focus();
	            return false;
	        }
	       // alert(paymentMode)
	        if(paymentMode=='Cheque' || paymentMode=='Draft' || paymentMode=='NEFT' || paymentMode=='RTGS' || paymentMode=='Cash' || paymentMode=='UPI')
	        {   
	            var returnVal               = true;
	            var paymentOptionChecked    = $(paymentOptionCheckedOb).attr("for");
	            $.each($("div[id=registrationPaymentDinner]").find("div[use=offlinePaymentOption][for='"+paymentOptionChecked+"']"),function(){
	                var thiObj = $(this);
	                $.each($(thiObj).find("input[type=text],input[type=date]"),function(i,validateObj){
	                    if($.trim($(validateObj).val())=='')
	                    {
	                        console.log('pay details>>BLANK');
	                        popoverAlert(validateObj);
	                        $(validateObj).focus();
	                        returnVal = false;
	                        return false;
	                    }
	                }); 
	                if(!returnVal) return false;    
	            });
	            if(!returnVal) return false;    
	        }
	        else if(paymentMode=='Card')
	        {
	            var returnVal               = true;
	            
	            if(!returnVal) return false;    
	        }                       
	        if(!$("div[use=registrationPayment]").find("input[type=checkbox][name=acceptance1]").is(":checked"))
	        {
	            var valOb1 = $("div[use=registrationPayment]").find("input[type=checkbox][name=acceptance1]");
	            popoverAlert(valOb1);
	            $(valOb1).focus();
	            return false;
	        }
	        if(!$("div[use=registrationPayment]").find("input[type=checkbox][name=acceptance2]").is(":checked"))
	        {
	            var valOb2 = $("div[use=registrationPayment]").find("input[type=checkbox][name=acceptance2]");
	            popoverAlert(valOb2);
	            $(valOb2).focus();
	            return false;
	        }
	    }
	    else
	    {
	         $("div[use=registrationPayment]").find("input[type=radio][name=payment_mode]").last().prop("checked",true);
	    }
	    return true;
	}


	

	$("div[operationData=workshopAdd] input[type=checkbox],div[operationData=workshopAdd] input[type=radio]").click(function(){
		calculateTotalWorkshoAmountInProfile();
		//calculateTotalAmountInProfile();
	});

	$("div[operationData=dinnerAdd] input[type=checkbox],div[operationData=dinnerAdd] input[type=radio]").click(function(){
		calculateTotalDinnerAmountInProfile();
		//calculateTotalAmountInProfile();
	});

	// workshop related work for profile by weavers end

	/* Accompany related work start */
function addAccompanyFromProfile(obj, what) {
    $("div[operationMode=profileData]").hide();
    $("div[operationMode=profileData][operationData='" + what + "']").slideDown();
}

function addAccompanyFromProfileBanquet(obj, what,accom_id)
{
	$("div[operationMode=profileData]").hide();
    $("div[operationMode=profileData][operationData='" + what + "']").slideDown();
    $('#accompanyID').val(accom_id);
}

$("input[type=radio][use=accompanyCountSelect]").click(function() {
    var count = parseInt($(this).val());
    var haveCount = $("div[use=accompanyDetails]").length;
    for (var i = 1; i <= count; i++) {
        $("div[use=accompanyDetails][index='" + i + "']").slideDown();

    }
    for (var j = (count + 1); j <= haveCount; j++) {
        var accomDiv = $("div[use=accompanyDetails][index='" + j + "']");
        $(accomDiv).slideUp();
        $(accomDiv).find("input[type=text]").val('');
        $(accomDiv).find("input[type=radio]").prop('checked', false);
        $(accomDiv).find("input[type=checkbox]").prop('checked', false);

    }
    calculateTotalAmountInProfile('accompanyAdd');
    if (count > 0) {
        $('.accom-submit').show();
    } else {
        $('.accom-submit').hide();
    }
});

$("input[type=radio][use=accommodationCountSelect]").click(function() {
    var count = parseInt($(this).val());
    var haveCount = $("div[use=accommodationDetails]").length;
    for (var i = 1; i <= count; i++) {
        $("div[use=accommodationDetails][index='" + i + "']").slideDown();

    }
    for (var j = (count + 1); j <= haveCount; j++) {
        var accomDiv = $("div[use=accommodationDetails][index='" + j + "']");
        $(accomDiv).slideUp();
        $(accomDiv).find("input[type=text]").val('');
        $(accomDiv).find("input[type=radio]").prop('checked', false);
        $(accomDiv).find("input[type=checkbox]").prop('checked', false);

    }
    calculateTotalAmountInProfile('accommodationAddRoom');
    if (count > 0) {
        $('.accom-submit').show();
    } else {
        $('.accom-submit').hide();
    }
});

$("input[type=radio][use=accompanyCountSelect]").click(function() {
    var count = parseInt($(this).val());
    var haveCount = $("div[use=accompanyDetails]").length;
    for (var i = 1; i <= count; i++) {
        $("div[use=accompanyDetails][index='" + i + "']").slideDown();
    }
    for (var j = (count + 1); j <= haveCount; j++) {
        var accomDiv = $("div[use=accompanyDetails][index='" + j + "']");
        $(accomDiv).slideUp();
        $(accomDiv).find("input[type=text]").val('');
        $(accomDiv).find("input[type=radio]").prop('checked', false);
        $(accomDiv).find("input[type=checkbox]").prop('checked', false);
    }
    calculateTotalAmountInProfile('accompanyAdd');
    if (count > 0) {
        $('.accom-submit').show();
    } else {
        $('.accom-submit').hide();
    }
});

function validateRegistrationAccompanyDetails(obj) {
    popDownAlert();
    var countObj = $(obj).parent().parent().find("input[type=radio][name=accompanyCount]:checked");
    var count = $(countObj).val();
    var returnVal = true;
    for (var i = 0; i < count; i++) {
        var accomDiv = $("div[use=accompanyDetails][index='" + (i + 1) + "']");

        var accomPanyNameObj = $(accomDiv).find("input[name='accompany_name_add[" + i + "]']");
        var accomPanyName = $(accomPanyNameObj).val();

        var accomPanyFoodObj = $(accomDiv).find("input[name='accompany_food_choice[" + i + "]']");
        var accomPanyFood = '';
        if (accomPanyFoodObj.is(":checked")) {
            accomPanyFood = $(accomPanyFoodObj).val();
        }


        if ($.trim(accomPanyName) == '') {
            console.log('accomPanyName>>BLANK');
            $(accomPanyNameObj).focus();
            popoverAlert(accomPanyNameObj);
            returnVal = false;
            return false;
        }
        console.log(accomPanyFood)
        if ($.trim(accomPanyFood) == '') {
            console.log('accomPanyName>>BLANK1');
            $(accomPanyFoodObj).focus();
            popoverAlert(accomPanyFoodObj);
            returnVal = false;
            return false;
        }
    }
    calculateTotalAmountInProfile('accompanyAdd');
    if (returnVal) {
        $('#frmAddAccompanyfromProfile').find('div[use=registrationPaymentWrap]').show()
    } else {
        $('#frmAddAccompanyfromProfile').find('div[use=registrationPaymentWrap]').hide()
    }
    return true;
}


function validateRegistrationAccompanyDetailsBanquet(obj) {
    popDownAlert();
    var returnVal = true;
  	//var accomDiv = $("#dinner_value_accompany").prop('checked', true);
  	var chkPassport = document.getElementById("dinner_value_accompany");
        if (chkPassport.checked) {
        	returnVal = true;
        }
        else
        {
        	$('#error_dinner').show();
        	returnVal = false;
        }

  	//var accomPanyNameObj = $(accomDiv).find("input[type=checkbox]");
  	
    
   
    calculateTotalAmountInProfile('accompanyAddBanquet');
    if (returnVal) {

        $('#frmAddAccompanyBanquetfromProfile').find('div[use=registrationPaymentWrap]').show()
    } else {
        $('#frmAddAccompanyBanquetfromProfile').find('div[use=registrationPaymentWrap]').hide()
    }
    return true;
}


$('#frmAddAccompanyBanquetfromProfile').submit(function(evnt) {
    popDownAlert();
    
     var paymentModeObj = $("div[use=registrationPaymentOptionWrap]").find(
            "input[type=radio][name=registrationModePaymemt]:checked");
    var paymentMode = $(paymentModeObj).val();
   var paymentOptionCheckedOb = $("div[use=registrationPaymentWrap]").find(
            "input[type=radio][name=payment_mode]:checked");
    console.log('paymentMode='+paymentMode);    
        if ($(paymentOptionCheckedOb).length == 0) {
            var paymentOptionObj = $("div[id=accompanyBanquet]").find("input[type=radio][name=payment_mode]")
                .first();
                 
              $('#error_payment_mode_process').show();
            console.log('paymentOptionCheckedOb>>NOT SELECTED',paymentOptionObj);
           
            popoverAlert(paymentOptionObj);
            $(paymentOptionObj).focus();
            return false;
        }
        //console.log('paymentmode='+paymentMode);
        
    if (paymentMode == 'OFFLINE') {

        var returnVal = true;
        var paymentOptionChecked = $(paymentOptionCheckedOb).attr("for");
        console.log('paymentoption='+paymentOptionChecked);
      $.each($("div[id=accompanyBanquet]").find("div[use=offlinePaymentOptionWrap][for='" +
            paymentOptionChecked + "']"), function() {
            var thiObj = $(this);
            console.log(thiObj);
            $.each($(thiObj).find("input[type=text],input[type=date]"), function(i, validateObj) {
                if ($.trim($(validateObj).val()) == '') {
                    console.log('pay details>>BLANK');
                    popoverAlert(validateObj);
                    $(validateObj).focus();
                    
                    returnVal = false;
                   // return false;
                  
                }
            });
            if (!returnVal) return false;
        });

        if (!returnVal) return false;
    }
    else if (paymentMode == 'ONLINE') {
        var returnVal = true;
    }

    try {
      
        var nextButton = $('.accom-submit1').find('button');
        var validateReturnVal = validateRegistrationAccompanyDetailsBanquet(nextButton);
        
        if (!returnVal || !validateReturnVal) {
            return false;
            evnt.preventDefault();
        } else {
            
        }
    } catch (e) {
        console.log('ERROR : ' + e.message);
        evnt.preventDefault();
    }
})


$('#frmAddAccommodationRoom').submit(function(evnt) {
    popDownAlert();
    
     var paymentModeObj = $("div[use=registrationPaymentOptionWrap]").find(
            "input[type=radio][name=registrationModePaymemt]:checked");
    var paymentMode = $(paymentModeObj).val();
   var paymentOptionCheckedOb = $("div[use=registrationPaymentWrap]").find(
            "input[type=radio][name=payment_mode]:checked");
    console.log('paymentMode='+paymentMode);    
        if ($(paymentOptionCheckedOb).length == 0) {
            var paymentOptionObj = $("div[id=accommodationAddRoom]").find("input[type=radio][name=payment_mode]")
                .first();
                 
              $('#error_payment_mode_process').show();
            console.log('paymentOptionCheckedOb>>NOT SELECTED',paymentOptionObj);
           
            popoverAlert(paymentOptionObj);
            $(paymentOptionObj).focus();
            return false;
        }
        //console.log('paymentmode='+paymentMode);
        
    if (paymentMode == 'OFFLINE') {

        var returnVal = true;
        var paymentOptionChecked = $(paymentOptionCheckedOb).attr("for");
        console.log('paymentoption='+paymentOptionChecked);
      $.each($("div[id=accommodationAddRoom]").find("div[use=offlinePaymentOptionWrap][for='" +
            paymentOptionChecked + "']"), function() {
            var thiObj = $(this);
            console.log(thiObj);
            $.each($(thiObj).find("input[type=text],input[type=date]"), function(i, validateObj) {
                if ($.trim($(validateObj).val()) == '') {
                    console.log('pay details>>BLANK');
                    popoverAlert(validateObj);
                    $(validateObj).focus();
                    
                    returnVal = false;
                   // return false;
                  
                }
            });
            if (!returnVal) return false;
        });

        if (!returnVal) return false;
    }
    else if (paymentMode == 'ONLINE') {
        var returnVal = true;
    }

    try {
      
        var nextButton = $('.accom-submit1').find('button');
        var validateReturnVal = validateRegistrationAccompanyDetailsBanquet(nextButton);
        
        if (!returnVal || !validateReturnVal) {
            return false;
            evnt.preventDefault();
        } else {
            
        }
    } catch (e) {
        console.log('ERROR : ' + e.message);
        evnt.preventDefault();
    }
})

/*$('#frmAddAccompanyfromProfile').submit(function(evnt) {
    popDownAlert();
     var paymentModeObj = $("div[use=registrationPaymentOptionWrap]").find(
            "input[type=radio][name=registrationModePaymemt]:checked");
    var paymentMode = $(paymentModeObj).val();
   var paymentOptionCheckedOb = $("div[use=registrationPaymentWrap]").find(
            "input[type=radio][name=payment_mode]:checked");
    console.log('paymentMode='+paymentMode);    
        if ($(paymentOptionCheckedOb).length == 0) {
            var paymentOptionObj = $("div[use=registrationPaymentWrap]").find("input[type=radio][name=payment_mode]")
                .first();
              $('#error_payment_mode').css("display", "block");
            console.log('paymentOptionCheckedOb>>NOT SELECTED',paymentOptionObj);
           
            popoverAlert(paymentOptionObj);
            $(paymentOptionObj).focus();
            return false;
        }
        //console.log('paymentmode='+paymentMode);
    if (paymentMode == 'OFFLINE') {

        var returnVal = true;
        var paymentOptionChecked = $(paymentOptionCheckedOb).attr("for");
        console.log('paymentoption='+paymentOptionChecked);
      $.each($("div[id=registrationPaymentWrapAcmp]").find("div[use=offlinePaymentOptionWrap][for='" +
            paymentOptionChecked + "']"), function() {
            var thiObj = $(this);
            console.log(thiObj);
            $.each($(thiObj).find("input[type=text],input[type=date]"), function(i, validateObj) {
                if ($.trim($(validateObj).val()) == '') {
                    console.log('pay details>>BLANK');
                    popoverAlert(validateObj);
                    $(validateObj).focus();
                    
                    returnVal = false;
                   // return false;
                  
                }
            });
            if (!returnVal) return false;
        });

        if (!returnVal) return false;
    }
    else if (paymentMode == 'ONLINE') {
        var returnVal = true;
    }

    try {
       // var returnVal = validatePayment();
        var nextButton = $('.accom-submit').find('button');
        var validateReturnVal = validateRegistrationAccompanyDetails(nextButton);
        //console.log('form submitted--->'+validateReturnVal);
        if (!returnVal || !validateReturnVal) {
            return false;
            evnt.preventDefault();
        } else {
            //evnt.preventDefault();
            //alert('Form submitting.....')
        }
    } catch (e) {
        console.log('ERROR : ' + e.message);
        evnt.preventDefault();
    }
})*/
$('#frmAddAccompanyfromProfile').submit(function(evnt) {
    popDownAlert();
    
     var paymentModeObj = $("div[use=registrationPaymentOptionWrap]").find(
            "input[type=radio][name=registrationModePaymemt]:checked");
    var paymentMode = $(paymentModeObj).val();
   var paymentOptionCheckedOb = $("div[use=registrationPaymentWrap]").find(
            "input[type=radio][name=payment_mode]:checked");
    console.log('paymentMode='+paymentMode);    
        if ($(paymentOptionCheckedOb).length == 0) {
            var paymentOptionObj = $("div[id=accopanyAddPayment]").find("input[type=radio][name=payment_mode]")
                .first();
                 
              $('#error_payment_mode_process').show();
            console.log('paymentOptionCheckedOb>>NOT SELECTED',paymentOptionObj);
           
            popoverAlert(paymentOptionObj);
            $(paymentOptionObj).focus();
            return false;
        }
        //console.log('paymentmode='+paymentMode);
        
    if (paymentMode == 'OFFLINE') {

        var returnVal = true;
        var paymentOptionChecked = $(paymentOptionCheckedOb).attr("for");
        console.log('paymentoption='+paymentOptionChecked);
      $.each($("div[id=accopanyAddPayment]").find("div[use=offlinePaymentOptionWrap][for='" +
            paymentOptionChecked + "']"), function() {
            var thiObj = $(this);
            console.log(thiObj);
            $.each($(thiObj).find("input[type=text],input[type=date]"), function(i, validateObj) {
                if ($.trim($(validateObj).val()) == '') {
                    console.log('pay details>>BLANK');
                    popoverAlert(validateObj);
                    $(validateObj).focus();
                    
                    returnVal = false;
                   // return false;
                  
                }
            });
            if (!returnVal) return false;
        });

        if (!returnVal) return false;
    }
    else if (paymentMode == 'ONLINE') {
        var returnVal = true;
    }

    try {
      
        var nextButton = $('.accom-submit1').find('button');
       // var validateReturnVal = validateRegistrationAccompanyDetailsBanquet(nextButton);
        
        if (!returnVal) {
            return false;
            evnt.preventDefault();
        } else {
            
        }
    } catch (e) {
        console.log('ERROR : ' + e.message);
        evnt.preventDefault();
    }
})


	function addAccommodationFromProfile(obj,what) {

		$("div[operationMode=profileData]").hide();
		$("div[operationMode=profileData][operationData='"+what+"']").slideDown();
		$('#frmAddAccommodationfromProfile table').show();
	}

	function addAccommodationRoomFromProfile(obj,what) {

		$("div[operationMode=profileData]").hide();
		$("div[operationMode=profileData][operationData='"+what+"']").slideDown();
		$('#frmAddAccommodationRoom table').show();
	}

	$(document).ready(function() {
      var total = 0; // Initialize the counter variable

      // Attach a change event handler to the checkboxes
      $('.accoStartDate').change(function() {
        // Get the value of the changed checkbox
        var value = parseInt($(this).val());

        // Check if the checkbox is checked or unchecked and update the counter accordingly
        if ($(this).is(':checked')) {
          total += 1;
        } else {
          total -= 1;
        }

        // Update the total display
       
        if(total>0)
        {

        }
        else
        {
        	$(this).closest("form").find("div[use=registrationPaymentWrap]").hide();
        }
        
      });
    });

	function showChekinChekoutDate(obj)
	{
		popDownAlert();

		var totalCount = $('#checkTotalCount').val();
		var total = 0;
		
		//$("input[type=radio][use=accoStartDate]").prop("checked",false);
		//$("input[type=checkbox][use=accoStartDate]").prop("disabled",true);

		//var count = $('obj:checked').length;
		var count = $(obj).filter(':checked').length;
		console.log('count='+count);
		

		if( $(obj).is(':checked') ){

          total += 1;
        } else {
        	//alert('not checked');
         total -= 1;
        }

        //alert(total);

        //return false;

		var parent 			= $(obj).parent().closest("div[operetionMode=checkInCheckOutTr]");
		var checkoutDate 	= $(obj).attr("checkoutDate");

		console.log('val='+totalCount);
		if( $(obj).is(':checked') ){

			  //  $(obj).prop("checked",true);

			//$("input[type=checkbox][use=accoStartDate]").not(obj).prop('disabled', true);

			//$(obj).siblings().attr("disabled", $(obj).prop("checked",true));
			
									
			
			$("input[type=radio][use=accoEndDate]").prop("checked",false);
			$(parent).find("input[use=accoEndDate][value='"+checkoutDate+"']").prop("checked",true);
			//console.log('val='+$.trim($(obj).val()));

			
			if($.trim($(obj).val()) != "")
			{
				
				$(obj).closest("form").find("div[use=registrationPaymentWrap]").slideDown();
				var accommoPkgID = $.trim($(obj).attr('accommoPkg'));
				var accommodation_details = $.trim($(obj).attr('invoiceTitle'));

				$('#accommodation_pkg_id').val(accommoPkgID);
				$('#accommodation_details').val(accommodation_details);
				// payment related script start
				$(obj).closest("form").find("div[use=registrationPaymentWrap] input[type=radio]").prop('checked', false);
				$(obj).closest("form").find("div[use=registrationPaymentOptionWrap] input[type=radio]").prop('checked', false);
				calculateTotalAmountInProfile('accommodationAdd');
				// payment related script end
			}
		}
		else{

			//alert($.trim($(obj).val()));
			
		   //$(obj).closest("form").find("div[use=registrationPaymentWrap]").hide();
		   
		   $("input[type=radio][use=accoEndDate]").prop("checked",false);
		   $(parent).find("input[use=accoEndDate][value='"+checkoutDate+"']").prop("checked",false);
		   calculateTotalAmountInProfile('accommodationAdd');
		}

		return false;

		
		
	}

	$("div[operetionMode=checkInCheckOutHotel]").find("input[type=radio]").click(function(){

		var hotelID = $(this).val();
		var avalDays = $(this).attr('accommoAvlDy').split(",");

		$("div[operetionMode=checkInCheckOutTr]").find("input[type=radio]").prop('checked', false);

		$("div[operetionMode=checkInCheckOutHotelDays]").find("input[type=radio]").prop('checked', false);
		$("div[operetionMode=checkInCheckOutHotelDays]").find("input[type=radio]").prop("disabled",true).parent().hide();
		if($.trim(hotelID) == '' && avalDays.length == '')
		{
			popoverAlert($(this));
			return false;
		}
		
		$.each(avalDays,function(i){
		   $("div[operetionMode=checkInCheckOutHotelDays]").find("input[type=radio][value=" + avalDays[i] + "]").prop("disabled",false).parent().show();
		});
		$("div[operetionMode=checkInCheckOutHotelDays]").find("input[type=radio]").attr('hotelValue',hotelID);
		$("div[operetionMode=checkInCheckOutHotelDays]").hide()
		$("div[operetionMode=checkInCheckOutTr]").hide();	
		$("div[operetionMode=checkInCheckOutTr]").find("input[type=radio]").parent().hide();
		$(this).closest("form").find("div[use=registrationPaymentWrap]").hide();

		$(this).closest("form").find("div[use=registrationPaymentWrap]").hide();
		if(hotelID>0)
		{
			$("div[operetionMode=checkInCheckOutTr][use='"+hotelID+"']").show();
			$("div[operetionMode=checkInCheckOutTr]").find("input[type=radio][operetionmode='checkInCheckOut_"+hotelID+"_1']").parent().show();
		}
		
		//calculateTotalAmountInProfile();												
	});

	$("div[operetionMode=checkInCheckOutHotelDays]").find("input[type=radio]").click(function(){	
		var numberOfDays = $(this).val();
		var hotelID = $(this).attr('hotelValue');
		$("div[operetionMode=checkInCheckOutTr]").find("input[type=radio]").parent().hide();
		if($.trim(numberOfDays) == '' && $.trim(hotelID) == ''){
			popoverAlert($(this));
			return false;
		}
		$("div[operetionMode=checkInCheckOutTr][use='"+hotelID+"']").show();

		$(this).closest("form").find("div[use=registrationPaymentWrap]").hide();
		$("div[operetionMode=checkInCheckOutTr]").find("input[type=radio][operetionmode='checkInCheckOut_"+hotelID+"_"+numberOfDays+"']").parent().show();
		//calculateTotalAmountInProfile();
	})


	$('#frmAddAccommodationfromProfile').submit(function(event){

		popDownAlert();
		var paymentModeObj = $("div[use=registrationPaymentOptionWrap]").find(
		"input[type=radio][name=registrationModePaymemt]:checked");
		var paymentMode = $(paymentModeObj).val();
		var paymentOptionCheckedOb = $("div[use=registrationPaymentWrap]").find(
		"input[type=radio][name=payment_mode]:checked");
		console.log('paymentMode='+paymentMode);    

		if ($(paymentOptionCheckedOb).length == 0) {
			var paymentOptionObj = $("div[id=accommodationAdd]").find("input[type=radio][name=payment_mode]")
			    .first();
			     
			  $('#error_payment_mode_process').show();
			console.log('paymentOptionCheckedOb>>NOT SELECTED',paymentOptionObj);

			popoverAlert(paymentOptionObj);
			$(paymentOptionObj).focus();
			return false;
		}
		//console.log('paymentmode='+paymentMode);

		if (paymentMode == 'OFFLINE') {

			var returnVal = true;
			var paymentOptionChecked = $(paymentOptionCheckedOb).attr("for");
			console.log('paymentoption='+paymentOptionChecked);
			$.each($("div[id=accommodationAdd]").find("div[use=offlinePaymentOptionWrap][for='" +
			paymentOptionChecked + "']"), function() {
			var thiObj = $(this);
			console.log(thiObj);
			$.each($(thiObj).find("input[type=text],input[type=date]"), function(i, validateObj) {
			    if ($.trim($(validateObj).val()) == '') {
			        console.log('pay details>>BLANK');
			        popoverAlert(validateObj);
			        $(validateObj).focus();
			        
			        returnVal = false;
			       // return false;
			      
			    }
			});
			if (!returnVal) return false;
			});

			if (!returnVal) return false;
		}
		else if (paymentMode == 'ONLINE') {
			var returnVal = true;
		}
		try{
			//returnVal = validatePayment();
			if(!returnVal) {
				return false;
				evnt.preventDefault();
			}else{
				//evnt.preventDefault();
				//alert('Form submitting.....')
			}
		}
		catch(e)
		{
			console.log('ERROR : '+e.message);
			evnt.preventDefault();
		}
	})

	// payment related script start


	$("input[type=radio][use=tariffPaymentModePaymemt]").click(function(){
	    var forPay = $(this).attr("for");
	    $("div[use=payRules]").hide();
	    $("div[use=payRules][for='"+forPay+"']").slideDown();
	    
	    $("div[use=offlinePaymentOptionChoiceWrap]").hide();
	    $("div[use=offlinePaymentOptionWrap]").hide();
	    
	    $("div[use=offlinePaymentOptionChoiceWrap]").find("input[type=radio]").prop("checked",false);
	    $("div[use=offlinePaymentOptionWrap]").find("input[type=text]").val('');
	    $("div[use=offlinePaymentOptionWrap]").find("input[type=date]").val('');
	    
	    if($(this).val()=='OFFLINE')
	    {
	        $("div[use=offlinePaymentOptionChoiceWrap]").slideDown();
	        if(forPay == 'CHQ')
	        {
	            $("div[use=offlinePaymentOptionChoiceWrap]").find("input[type=radio][use=payment_mode_select][for=Cheque]").trigger("click");
	        }
	        else if(forPay == 'DD')
	        {
	            $("div[use=offlinePaymentOptionChoiceWrap]").find("input[type=radio][use=payment_mode_select][for=Draft]").trigger("click");
	        }
	        else if(forPay == 'WIRE')
	        {
	            $("div[use=offlinePaymentOptionChoiceWrap]").find("input[type=radio][use=payment_mode_select][for=NEFT]").trigger("click");
	        }
	        else if(forPay == 'CASH')
	        {
	            //$("div[use=offlinePaymentOptionChoice]").find("input[type=radio][use=payment_mode_select][for=Cash]").prop("checked",true);
	            $("div[use=offlinePaymentOptionChoiceWrap]").find("input[type=radio][use=payment_mode_select][for=Cash]").trigger("click");
	        }
	    }
	});

	$("div[use=offlinePaymentOptionChoiceWrap]").find("input[type=radio]").click(function(){
	//alert(12);	
		
		//alert(differenceDays);
					
		var forPay = $(this).attr("for");
		var profileFrom = $(this).attr("profileFrom");
		//alert(profileFrom);
		$("div[use=offlinePaymentOptionWrap]").hide();
		$("div[use=offlinePaymentOptionWrap][for='"+forPay+"']").slideDown();
		
		var paymentMode = $(this).attr("paymentMode");							
		$("input[type=radio][use=tariffPaymentModePaymemt]").prop("checked",false);
		$("input[type=radio][use=tariffPaymentModePaymemt][value='"+paymentMode+"']").first().prop("checked",true);	

		

		if(profileFrom=='accommodationAdd')
		{

			calculateTotalAmountInProfileAccommodationRoom('accommodationAdd');
			
		}
		else if(profileFrom=='initial')
		{
			calculateTotalAmountInProfile('accommodationAdd');  
		}
		else if(profileFrom=='accommodationAddRoom')
		{
			calculateTotalAmountInProfile('accommodationAddRoom'); 
		}
		else if(profileFrom==='accopanyAddPayment')
		{
			calculateTotalAmountInProfile('accompanyAdd');
		}
			
	});
	


	function validatePayment() {
		popDownAlert();
	    //var theAmount               = parseFloat($("div[use=totalAmount]").find("span[use=totalAmount]").attr('theAmount'));
	    var theAmount               = parseFloat($("div[use=registrationPaymentWrap] div[use=totalAmount]").find("span[use=totalAmount]").attr('theAmount'));
	    if(theAmount > 0)
	    {
	        var paymentModeObj          = $("div[use=registrationPaymentOptionWrap]").find("input[type=radio][name=registrationModePaymemt]:checked");
	        var paymentMode             = $(paymentModeObj).val();
	        var paymentOptionCheckedOb  = $("div[use=registrationPaymentWrap]").find("input[type=radio][name=payment_mode]:checked");
	        if($(paymentOptionCheckedOb).length == 0)
	        {
	            var paymentOptionObj    = $("div[use=registrationPaymentWrap]").find("input[type=radio][name=payment_mode]").first();
	            console.log('paymentOptionCheckedOb>>NOT SELECTED');
	            popoverAlert(paymentOptionObj);
	            $(paymentOptionObj).focus();
	            return false;
	        }
	        if(paymentMode=='OFFLINE')
	        {   
	            var returnVal               = true;
	            var paymentOptionChecked    = $(paymentOptionCheckedOb).attr("for");
	            $.each($("div[use=registrationPaymentWrap]").find("div[use=offlinePaymentOptionWrap][for='"+paymentOptionChecked+"']"),function(){
	                var thiObj = $(this);
	                $.each($(thiObj).find("input[type=text],input[type=date]"),function(i,validateObj){
	                    if($.trim($(validateObj).val())=='')
	                    {
	                        console.log('pay details>>BLANK');
	                        popoverAlert(validateObj);
	                        $(validateObj).focus();
	                        returnVal = false;
	                        return false;
	                    }
	                }); 
	                if(!returnVal) return false;    
	            });
	            if(!returnVal) return false;    
	        }
	        else if(paymentMode=='ONLINE')
	        {
	            var returnVal               = true;
	            var paymentOptionChecked    = $(paymentOptionCheckedOb).attr("for");
	            $.each($("div[use=registrationPaymentWrap]").find("div[use=offlinePaymentOptionWrap][for='"+paymentOptionChecked+"']"),function(){
	                var thiObj = $(this);
	                $.each($(thiObj).find("input[type=text],input[type=date]"),function(i,validateObj){
	                    if($.trim($(validateObj).val())=='')
	                    {
	                        console.log('pay details>>BLANK');
	                        popoverAlert(validateObj);
	                        $(validateObj).focus();
	                        returnVal = false;
	                        return false;
	                    }
	                }); 
	                $.each($(thiObj).find("input[type=radio],input[type=checkbox]"),function(i,validateObj){
	                    var name        = $(validateObj).attr("name")
	                    console.log('name-->'+name)
	                    var type        = $(validateObj).attr("type")
	                    var parent      = $(validateObj).parent().closest("div[actAs=fieldContainer]");
	                    var checkedObj  = $(parent).find("input[type='"+type+"'][name='"+name+"']:checked");
	                    if($(checkedObj).length == 0)
	                    {
	                        var checkedOptionObj    = $(parent).find("input[type='"+type+"'][name='"+name+"']").first();                                                                
	                        console.log('checkedOptionObj>>NOT SELECTED');
	                        popoverAlert(checkedOptionObj);
	                        $(checkedOptionObj).focus();
	                        returnVal = false;
	                        return false;
	                    }
	                }); 
	                if(!returnVal) return false;    
	            });
	            if(!returnVal) return false;    
	        }    
	        
	        if(!$("div[use=registrationPaymentWrap]").find("input[type=checkbox][name=acceptance1]").is(":checked"))
	        {
	            var valOb1 = $("div[use=registrationPaymentWrap]").find("input[type=checkbox][name=acceptance1]");
	            console.log(valOb1);
	            popoverAlert(valOb1);
	            $(valOb1).focus();
	            returnVal = false;
	            return false;
	        }
	        
	        if(!$("div[use=registrationPaymentWrap]").find("input[type=checkbox][name=acceptance2]").is(":checked"))
	        {
	            var valOb2 = $("div[use=registrationPaymentWrap]").find("input[type=checkbox][name=acceptance2]");
	            console.log(valOb2);
	            popoverAlert(valOb2);
	            $(valOb2).focus();
	            returnVal = false;
	            return false;
	        }
	         if(!returnVal) return false;  
	    }
	    else
	    {
	         $("div[use=registrationPaymentWrap]").find("input[type=radio][name=payment_mode]").last().prop("checked",true);
	    }
	    return true;
	}
	// payment related script end

$('#hotel_select_acco_id').on('change',function(){
         var hotel_id=$(this).val();
         
         if(hotel_id!='')
         {
           // alert(21);
            //$('.packageDiv').hide();
            $.ajax({
                    type: "POST",
                    url: "returnData.process.php",
                    data: "act=getAllPackageProfile&hotel_id="+hotel_id,
                    dataType: "html",
                    async: false,
                    success: function(JSONObject){
                        // console.log(JSONObject);
                        if(JSONObject)
                        {
                           $('#packageDiv').html(JSONObject);
                           $('#packageDiv').show(); 
                           $('#accommodation').hide();
                        }
                        else
                        {
                            $('#packageDiv').html('');
                            $('#packageDiv').hide(); 
                        }
                        
                    }
             });
         }
         else
         {
            
            $('#packageDiv').html('');
           $('#packageDiv').hide(); 
         }
         
        
     }); 

$('#hotel_select_acco_room_id').on('change',function(){
         var hotel_id=$(this).val();
         var delegateId = '<?=$delegateId ?>';
         
         if(hotel_id!='')
         {
           // alert(21);
            //$('.packageDiv').hide();
            $.ajax({
                    type: "POST",
                    url: "returnData.process.php",
                    data: "act=getAllPackageProfileRoom&hotel_id="+hotel_id+"&delegateId="+delegateId,
                    dataType: "html",
                    async: false,
                    success: function(JSONObject){
                        // console.log(JSONObject);
                        if(JSONObject)
                        {
                           $('#packageDiv').html(JSONObject);
                           $('#packageDiv').show(); 
                           $('#accommodation').hide();
                        }
                        else
                        {
                            $('#packageDiv').html('');
                            $('#packageDiv').hide(); 
                        }
                        
                    }
             });
         }
         else
         {
            
            $('#packageDiv').html('');
           $('#packageDiv').hide(); 
         }
         
        
     }); 



 function get_checkout_val(val)
    {
       
        var checkInVal= $('#accomodation_package_checkin_id').val();
        var checkOutVal= val;
        const checkInArray = checkInVal.split("/");
        var checkInID = checkInArray[0];
        var checkInDate = checkInArray[1];

        const checkOutArray = checkOutVal.split("/");
        var checkOutID = checkOutArray[0];
        var checkOutDate = checkOutArray[1];

        var date1 = new Date(checkInDate);
        var date2 = new Date(checkOutDate);

        // Calculate the difference in milliseconds
        var differenceMs = Math.abs(date2 - date1);

        // Convert the difference to days
        var differenceDays = Math.ceil(differenceMs / (1000 * 60 * 60 * 24));

        var totalAmount = 0;
        var totTable = $("table[use=totalAmountTable]");
        $(totTable).children("tbody").find("tr").remove();
        var gst_flag = $('#gst_flag').val();
        var cloneIt = false;
        $("input[type=radio][value=Card]").attr('profilefrom',"initial");
           $("input[name=package_id]").each( function () {
                  if($(this).prop('checked') == true)
                  {
                        var packageID = $(this).val();
                        var amount = ($(this).attr('amount'));
                        var amountIncludedDay = parseFloat(amount)*parseInt(differenceDays);
                        //alert('amount='+amount+'amountIncludedDay'+amountIncludedDay)
                        calculateTotalAmountInProfile('accommodationAdd',differenceDays);

                        $('#newAccodays').val(differenceDays);

                        //$('#accommodation').show();
                        $('#accommodationAdd').show();
                   
                }
           });

                
    } 

 function get_checkout_val_room(val)
    {
      
        var checkInVal= $('#accomodation_package_checkin_id').val();
        var checkOutVal= val;
        const checkInArray = checkInVal.split("/");
        var checkInID = checkInArray[0];
        var checkInDate = checkInArray[1];

        const checkOutArray = checkOutVal.split("/");
        var checkOutID = checkOutArray[0];
        var checkOutDate = checkOutArray[1];

        var date1 = new Date(checkInDate);
        var date2 = new Date(checkOutDate);

        // Calculate the difference in milliseconds
        var differenceMs = Math.abs(date2 - date1);

        // Convert the difference to days
        var differenceDays = Math.ceil(differenceMs / (1000 * 60 * 60 * 24));
       
        var totalAmount = 0;
        var totTable = $("table[use=totalAmountTable]");
        $(totTable).children("tbody").find("tr").remove();
        var gst_flag = $('#gst_flag').val();
        var cloneIt = false;
           $("input[name=package_id]").each( function () {
                  if($(this).prop('checked') == true)
                  {
                        var packageID = $(this).val();
                        var amount = ($(this).attr('amount'));
                        var amountIncludedDay = parseFloat(amount)*parseInt(differenceDays);
                        //alert('amount='+amount+'amountIncludedDay'+amountIncludedDay)
                        calculateTotalAmountInProfile('accommodationAddRoom',differenceDays);

                        $('#newAccodays').val(differenceDays);

                        $('#accommodationAddRoom').show();
                   
                }
           });

                
    }  

 function get_checkout_val_room_night(obj,countVal)
    {
      
        var accomodation_package_checkin_id = $('#accomodation_package_checkin_id'+countVal).val();
        //alert(accomodation_package_checkin_id)
        if(accomodation_package_checkin_id=='')
        {
        	alert('Please choose check in date first');
        	$('#accomodation_package_checkout_id'+countVal).val("");
        }
        else
        {
	    	 var selectedOption = $(obj).find('option:selected');
	         var amount = selectedOption.attr('amount');
	         var package_id = selectedOption.attr('package_id');
	         var i;
	         var title = selectedOption.attr('invoicetitle');
	         var value = $(obj).val();
	         var getAccommodationMaxRoom = $('#getAccommodationMaxRoom').val();
	        // alert(value+'amnt='+amount+'title='+title+'room='+getAccommodationMaxRoom);
	         for(i=1;i<=getAccommodationMaxRoom;i++)
	         {
	         	$('#package_night_id'+i).attr('amount',amount);
	         	$('#package_night_id'+i).attr('invoiceTitle',title);
	         	$('#package_night_id'+i).val(package_id);
	         }
	         calculateTotalAmountInProfileAccommodationRoom('accommodationAdd');
	         $('#accommodationAdd').show();
        }
       	 
          /* $("input[type=checkbox]:checked").each( function (index) {
                 

                  		var incrementKey = index+1;
                        var packageID = $(this).val();
                        var amount = ($(this).attr('amount'));
                        alert('amnt='+amount);

                        $('#package_night_id'+incrementKey).attr('amount',amount);
                        //$('#package_night_id1'+incrementKey).attr('amount',amount);
                        
                        //var amountIncludedDay = parseFloat(amount)*parseInt(differenceDays);
                        //alert('amount='+amount+'amountIncludedDay'+amountIncludedDay)
                        calculateTotalAmountInProfileAccommodationRoom('accommodationAdd');

                      //  $('#newAccodays').val(differenceDays);

                       // $('#accommodationAddRoom').show();
                   
                
           });*/

                
    }      
    
    
function get_checkin_val(val)
 {
    if(typeof val !== 'undefined' && val!='')
    {
       var checkOutVal= $('#accomodation_package_checkout_id').val(""); 

       calculateTotalAmountInProfile('accommodationAdd');
       $('#accommodation').hide();
    }
 }      

  function getPackageVal(val)
  {
    if(typeof val !== 'undefined' && val!='')
    {
    	$('#accommodation_pkg_id').val(val);
        var checkInVal= $('#accomodation_package_checkin_id').val("");
        var checkOutVal= $('#accomodation_package_checkout_id').val("");
         calculateTotalAmountInProfile('accommodationAdd');
         $('#accommodation').hide();
    }
  } 

function getPackageValRoom(val)
{
    if(typeof val !== 'undefined' && val!='')
    {
    	$('#accommodation_pkg_id').val(val);
        var checkInVal= $('#accomodation_package_checkin_id').val("");
        var checkOutVal= $('#accomodation_package_checkout_id').val("");
         calculateTotalAmountInProfile('accommodationAddRoom');
         $('#accommodationAddRoom').hide();
    }
}       

 
</script>
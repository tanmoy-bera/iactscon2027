<?php
	include_once('includes/init.php');
	
	page_header("Edit User Details");
	
	$searchArray['src_email_id']               = addslashes(trim($_REQUEST['src_email_id']));
	$searchArray['src_access_key']             = addslashes(trim($_REQUEST['src_access_key']));
	$searchArray['src_mobile_no']              = addslashes(trim($_REQUEST['src_mobile_no']));
	$searchArray['src_user_first_name']        = addslashes(trim($_REQUEST['src_user_first_name']));
	$searchArray['src_user_middle_name']       = addslashes(trim($_REQUEST['src_user_middle_name']));
	$searchArray['src_user_last_name']         = addslashes(trim($_REQUEST['src_user_last_name']));
	$searchArray['src_registration_id']        = addslashes(trim($_REQUEST['src_registration_id']));
	$searchArray['src_user_full_name']         = addslashes(trim($_REQUEST['src_user_full_name']));
	
	foreach($searchArray as $searchKey=>$searchVal)
	{
		if($searchVal!="")
		{
			$searchString .= "&".$searchKey."=".$searchVal;
		}
	}
	//javaScriptDefinedValue();
?>
	<style>
		.tborder td{
			font-size:16px;
		}
		
		.save {
				    background-color:#FFCC99;
    color: #fff;
    font-size: 13px;
    width: 38px;
    height: 16px;
    padding: 10px 10px 10px 10px;
    text-align: center;
    text-decoration: none;
    border-radius: 12px;
    display: inline-block;
		}
		
		.popup_form
		{
			display: none;
			background-color: #FFF;
			width: 54%;
			position: fixed;
			top: 122px;
			left: 45%;
			height: 25%;
			margin-left: -204px;
			padding: 15px;
			z-index: 14;
			box-shadow: 2px 2px 20px rgba(0, 0, 0, 1);
			border-radius: 15px;
		
		}
		
		
		.formControl{ 
			text-transform:uppercase;
			font-family:Arial, Helvetica, sans-serif;
			font-size:18px;
		}
		.successMessage {
			font-size:25px; 
			margin-bottom:10px; 
			font-weight:bold;
			color:#006600;
			margin:10px 0 10px 0;
		}
		.errorMessage {
			font-size:76px; 
			margin-bottom:10px; 
			font-weight:bold;
			color:#D41000;
			margin:10px 0 10px 0;
		}
		.tborder .ttextlarge2{
			font-size: 20px;
   			border-radius: 10px; 
			font-family:Arial, Helvetica, sans-serif;
			font-weight:bold;
		}
		.print {
			background-color: #006600;
			color: white;
			font-size: 10px;
			width: 65px;
			padding: 10px 10px 10px 10px;
			text-align: center;
			text-decoration: none;
			border-radius: 20px;
			display: inline-block;
		}
		.kit {
			background-color: #350303;
			color: #9E9E9E;
			font-size: 10px;
			width: 65px;
			padding: 10px 10px 10px 10px;
			text-align: center;
			text-decoration: none;
			border-radius: 20px;
			display: inline-block;
		}
	</style>
	<script type="text/javascript" language="javascript" src="../../js/returnData.process.js"></script>
	<script language="javascript">
	
		$(document).ready(function(){
			$('#new_email_id').blur(function(){
				checkEmailValidation('#new_email_id');
			});
			
			$("#src_user_full_name").focus();
	
			$("#src_registration_id").keypress(function(event) {
				
				if (event.which == 13) {
					event.preventDefault();
					window.location="edit_userDetails.php?m=2&src_registration_id="+$(this).val();
				}
				
				
			});
			$("#src_access_key").keypress(function(event) {
				
				if (event.which == 13) {
					event.preventDefault();
					window.location="edit_userDetails.php?m=2&src_access_key="+$(this).val();
				}
				
			});
			$("#src_mobile_no").keypress(function(event) {
				if (event.which == 13) {
					event.preventDefault();
					window.location="edit_userDetails.php?m=2&src_mobile_no="+$(this).val();
				}
			
			});
			$("#src_user_full_name").keypress(function(event) {
			
				if (event.which == 13) {
					event.preventDefault();
					window.location="edit_userDetails.php?m=2&src_user_full_name="+$(this).val();
				}
			
			
			});
		});
		
		function openEditNamePopup(id,user_title,firstname,middlename,lastname)
		{
			$("#edit_name_popup_form").fadeIn("slow");
			$('#delegateId').val(id);
			$('#user_title').val(user_title);
			$('#user_first_name').val(firstname);
			$('#user_middle_name').val(middlename);
			$('#user_last_name').val(lastname);
			
			$('#email_status').val("");
		}
		
		function closeEditNamePopup()
		{
			$("#edit_name_popup_form").fadeOut("slow"); 
		}
		
		function openEditEmailPopup(id,email)
		{
			$("#edit_email_popup_form").fadeIn("slow");
			$('#user_id').val(id);
			$('#new_email_id').val(email);
			$('#old_email_id').val(email);
			$('#email_status').val("");
		}
		
		function closeEditEmailPopup()
		{
			$("#edit_email_popup_form").fadeOut("slow"); 
		}
		
		function openAccompanyEditNamePopup(id,full_name,age,food_pref)
		{
			$("#edit_accompany_name_popup_form").fadeIn("slow");
			
			$('#accompanyId').val(id);
			$('#user_full_name').val(full_name);
			$('#accompany_edit_age').val(age);
			$('#accompany_edit_food_veg').prop('checked',false);
			$('#accompany_edit_food_nonveg').prop('checked',false);
			if(food_pref=='VEG')
			{
				$('#accompany_edit_food_veg').prop('checked',true);
			}
			if(food_pref=='NON_VEG')
			{
				$('#accompany_edit_food_nonveg').prop('checked',true);
			}
			
		}
		
		
		function closeAccompanyEditNamePopup()
		{
			$("#edit_accompany_name_popup_form").fadeOut("slow"); 
		}
		
		
		function openEditSmsPopup(id,isd,mobile)
		{
			$("#edit_mobile_popup_form").fadeIn("slow");
			$('#delegate_id').val(id);
			$('#new_isd_code').val(isd);
			$('#old_isd_code').val(isd);
			$('#new_mobile_no').val(mobile);
			$('#old_mobile_no').val(mobile);
			//$('#mobile_div').val("");
		}
		
		function closeEditSmsPopup()
		{
			$("#edit_mobile_popup_form").fadeOut("slow"); 
		}
		
		function checkMobileValidation(mobileControl){
			var user_mobile_no = $(mobileControl).val();
			$('#mobile_id_validation').val('');
			if(user_mobile_no!=""){
				$.ajax({
						type: "POST",
						url: "edit_userDetails.process.php",
						data: "act=getMobileValidation&mobile="+user_mobile_no,
						dataType: "text",
						async: false,
						success:function(returnMessage){
							console.log('http://localhost/imsos/dev/developer/webmaster/section_registration/edit_userDetails.process.php?act=getMobileValidation&mobile='+user_mobile_no);
							returnMessage = returnMessage.trim();
							if(returnMessage == 'IN_USE')
							{
								//$('#email_div').html('<span style="color:#FF0000">Email Id Already In Use</span>');
								var successContent   = '<div style="color:#FF0000";font-size:16px; text-align:center;">This Mobile No Already Registered.</div>';
								
								$('#mobile_div').html(successContent);
							}
							else
							{
								$('#mobile_div').html('<span style="color:#009933">Available</span>');
							}
							$('#mobile_id_validation').val(returnMessage);
						}
						
						
				});
			}
		}
		function checkEmailValidation(emailControl)
		{
							   
			var user_email    = $(emailControl).val();
			
			$('#email_id_validation').val("");
			$('#email_status').html("");
			
			if(user_email!="")
			{
				if(regularExpressionEmail.test($(emailControl).val())==false)
				{
					$('#email_status').html('<span style="color:#D41000">Invalid Email Id</span>');
					$('#email_id_validation').val('INVALID');
					return false;
				}
				else
				{
					$.ajax({
								type: "POST",
								url: 'edit_userDetails.process.php',
								data: 'act=getEmailValidation&email='+user_email,
								dataType: 'text',
								async: false,
								success:function(returnMessage)
								{
								console.log('http://localhost/imsos/dev/developer/webmaster/section_registration/edit_userDetails.process.php?act=getEmailValidation&email='+user_email);
									returnMessage = returnMessage.trim();
									if(returnMessage == 'IN_USE')
									{
										//$('#email_div').html('<span style="color:#FF0000">Email Id Already In Use</span>');
										var successContent   = '<div style="font-size:16px; text-align:center;">This Email ID Already Registered.</div>';
											successContent  += '<div style="color:#FF0000; font-size:15px; text-align:center;">Please Use Another Email ID.</div>';
										
										$('#email_status').html(successContent);
									}
									else
									{
										$('#email_status').html('<span style="color:#009933">Available</span>');
									}
									$('#email_id_validation').val(returnMessage);
								}
					});
				}
			}
			
	}
	
		function formEmailValidation()
		{
			if($('#email_id_validation').val()=="IN_USE"){
				
				$('#new_email_id').focus();
				$('#new_email_id').css('border-color','#D41000');
				alert("Email Id Already In Use");
				return false;
			}
			
			if(fieldNotEmpty('#new_email_id', "Please Enter Valid Email Id") == false){ 
				return false; 
			}
			if(fieldShouldEmailValidate('#new_email_id', "Please Provide Valid Email Id") == false){ 
				return false; 
			}
		}
		
		function formMobileValidation()
		{
			if(fieldNotEmpty('#new_mobile_no,#new_isd_code', "Please Enter Mobile No") == false){ 
				return false; 
			}
			if(isNaN($('#new_mobile_no').val()))
			{
				$('#new_mobile_no').focus();
				$('#new_mobile_no').css('border-color','#D41000');
				$('#new_mobile_no').val("");
				alert("Invalid Mobile Number");
				return false;
			}
			var mobile_no = $('#new_mobile_no').val();
			if( mobile_no.length > 10 || mobile_no.length < 10 )
			{
				alert("Mobile Number must be 10 digits");
				return false;
			}	
		}
		
		function formNameValidation()
		{
			if(fieldNotEmpty('#user_first_name', "Please First Name") == false){ 
				return false; 
			}
			//if(fieldNotEmpty('#user_last_name', "Please Last Name") == false){ 
				//return false; 
			//}
			return true;			
		}	
		
		function formAccompanyNameValidation()
		{
			if(fieldNotEmpty('#user_full_name', "Please Name") == false){ 
				return false; 
			}
			return true;	
		}

	</script>
		
	<div class="container">
		<?php 
		switch($show){
		
			default:
				
				trackIdCardDistributionWindow($cfg, $mycms); 
				break;
		}
		?>
	</div>
	<?php
	page_footer();
	
	/*******************************************************************************/
	/*                           SPOT DELEGATE REGISTRATION                        */
	/*******************************************************************************/
	function trackIdCardDistributionWindow($cfg, $mycms)
	{
		global $searchArray, $searchString;
		include_once('../../includes/function.accompany.php');
		$searchStatus         = 0;
		
		foreach($searchArray as $searchKey=>$searchVal)
		{
			if($searchVal!="")
			{
				$searchStatus = 1;
			}
		}
	?>
		<form name="frmSpotIdCardSearch" id="frmSpotIdCardSearch" action="edit_userDetails.process.php" method="post">
			<input type="hidden" name="act" value="searchDelegateEdit" />
			
			<table width="100%" class="tborder">
				<tr>
					<td class="tcat">Edit Screen</td>
				</tr>
				<tr>
					<td style="margin:0px; padding:0px;">
						
						<table width="100%">
							<tr>
								<td colspan="4" class="thighlight" align="left">Filter Delegate</td>
							</tr>
							<tr>
								<td align="left" width="20%">Registration ID :</td>
								<td align="left" width="30%">
									<input type="text" id="src_registration_id" name="src_registration_id" class="ttextlarge2" style="width:90%; padding:6px;" />
								</td>
								<td align="left" width="20%">Unique Sequence :</td>
								<td align="left" width="30%">
									<input type="text" id="src_access_key" name="src_access_key" placeholder="#" class="ttextlarge2" style="width:90%; padding:6px;" />
								</td>
							</tr>
							<tr>
								<td align="left" width="20%">User Full Name :</td>
								<td align="left" width="30%">
									<input type="text" id="src_user_full_name" name="src_user_full_name" class="ttextlarge2" style="width:90%; padding:6px;" />
								</td>
								<td align="left" width="20%">Mobile No :</td>
								<td align="left" width="30%">
									<input type="text" id="src_mobile_no" name="src_mobile_no" placeholder="" class="ttextlarge2" style="width:90%; padding:6px;" />
								</td>
							</tr>
							<tr>
							<tr>
								<td align="left" width="20%">&nbsp;</td>
								<td align="left" width="30%">&nbsp;</td>
								<td align="left" width="20%">&nbsp;</td>
								<td align="right" width="30%">
								<?php
									if($_REQUEST['m'])
									{
								?>
									<input type="button" id="clear" name="clear" class="btn btn-small btn-red"  value="Clear" style=" padding:6px; margin-right: 18px; width: 90px;" onclick="window.location='edit_userDetails.php'" />
								<?php
									}
									else
									{
										echo "&nbsp;";
									}
								?>
								</td>
							</tr>
						</table>
						<?php
						
						?>
							<table width="100%">
								
								<thead>
								<tr class="theader">
									<td width="5%" align="center">Sl No</td>
									<td align="left">Name & Contact</td>
									<td width="25%" align="left">Registration Details</td>
									<td width="35%" align="center">Action</td>
								</tr>
								</thead>
								
								<?php
								$counter              = 0;
								
								@$searchCondition     = "";
								$searchCondition     .= " AND delegate.registration_request NOT IN('EXHIBITOR','GUEST') 
														  AND delegate.isRegistration = 'Y'";
								
								if($_REQUEST['src_email_id']!='')
								{
									$searchCondition .= " AND delegate.user_email_id LIKE '%".$_REQUEST['src_email_id']."%'";
								}
								if($_REQUEST['src_access_key']!='')
								{
									$searchCondition .= " AND delegate.user_unique_sequence LIKE '%".$_REQUEST['src_access_key']."%'"; 
								}
								if($_REQUEST['src_mobile_no']!='')
								{
									$searchCondition .= " AND delegate.user_mobile_no LIKE '%".$_REQUEST['src_mobile_no']."%'";
								}
								if($_REQUEST['src_user_full_name']!='')
								{
									$searchCondition   .= " AND delegate.user_full_name LIKE '%".$_REQUEST['src_user_full_name']."%'";
								}
								if($_REQUEST['src_user_first_name']!='')
								{
									$searchCondition .= " AND delegate.user_first_name LIKE '%".$_REQUEST['src_user_first_name']."%'";
								}
								if($_REQUEST['src_user_middle_name']!='')
								{
									$searchCondition .= " AND delegate.user_middle_name LIKE '%".$_REQUEST['src_user_middle_name']."%'";
								}
								if($_REQUEST['src_user_last_name']!='')
								{
									$searchCondition .= " AND delegate.user_last_name LIKE '%".$_REQUEST['src_user_last_name']."%'";
								}
								if($_REQUEST['src_registration_id']!='')
								{
									$searchCondition .= " AND ( delegate.user_registration_id LIKE '%".$_REQUEST['src_registration_id']."%' OR delegate.id IN (SELECT `refference_delegate_id` FROM "._DB_USER_REGISTRATION_." WHERE user_registration_id LIKE '%".$_REQUEST['src_registration_id']."%'))";
								}
								//$sqlFetchUser     = registrationDetailsCompressedQuerySet("", $searchCondition);
								$sqlFetchDelegate['QUERY']	  = "SELECT delegate.*
																   FROM "._DB_USER_REGISTRATION_." AS delegate 
																  WHERE delegate.status = ? 
																    AND delegate.user_type = ?
																	 ".$searchCondition."
															   ORDER BY delegate.id DESC";
								$sqlFetchDelegate['PARAM'][]   = array('FILD' => 'delegate.status',             'DATA' =>'A',   	   'TYP' => 's');	
								$sqlFetchDelegate['PARAM'][]   = array('FILD' => 'delegate.user_type',          'DATA' =>'DELEGATE',   'TYP' => 's');						 
								
								$resultDelegate      = $mycms->sql_select_paginated(1, $sqlFetchDelegate, 10, $restrt);	
								if($resultDelegate)
								{
									foreach($resultDelegate as $i=>$rowDelegate) 
									{
										$counter      = $counter + 1; 
								 ?>
								 		<tr class="tlisting">
											<td align="center" valign="top"><?=$counter + ($_REQUEST['_pgn1_']*10)?></td>
											<td align="left" valign="top"> 
												<?=str_replace('..','.',strtoupper($rowDelegate['user_full_name']))?>  
												<br />
												<?=$rowDelegate['user_mobile_isd_code'].$rowDelegate['user_mobile_no']?>
												<br />
												<?=$rowDelegate['user_email_id']?>
												<br />
												<span style="color:#0000FF"><?=str_replace(',',', ',$rowDelegate['tags'])?></span>
											
											</td>
											<td align="left" valign="top">
												<?php
												if($rowDelegate['registration_payment_status'] != "UNPAID")
												{
													echo $rowDelegate['user_unique_sequence'].'<br />';  
												}
												else
												{
													echo "-".'<br />';
												}
												?>
												
												<?php
												if($rowDelegate['registration_payment_status'] != "UNPAID")
												{
													echo $rowDelegate['user_registration_id'];
												}
												else
												{
													echo "-";
												}
												?>
											</td>
											<td align="center">
												
												<a operationMode="ProfileDetails" 
													onclick="openEditNamePopup(<?=$rowDelegate['id']?>,'<?=$rowDelegate['user_title']?>','<?=$rowDelegate['user_first_name']?>','<?=$rowDelegate['user_middle_name']?>','<?=$rowDelegate['user_last_name']?>')" class="btn btn-small btn-blue">Edit Name</a> 
													
												<a operationMode="ProfileDetails" 
													onclick="openEditEmailPopup(<?=$rowDelegate['id']?>,'<?=$rowDelegate['user_email_id']?>')" class="btn btn-small btn-blue">Edit Email</a>
													
												<a operationMode="ProfileDetails" 
													onclick="openEditSmsPopup(<?=$rowDelegate['id']?>,'<?=$rowDelegate['user_mobile_isd_code']?>','<?=$rowDelegate['user_mobile_no']?>')" class="btn btn-small btn-blue">Edit Phone No</a>
													
												<a href="editUserDetails.php?delegateId=<?=$rowDelegate['id']?>" class="btn btn-small btn-blue" target="_blank">Edit Other Details</a>
											</td>
										</tr>
											<?php
												$showAccompanyCount = getTotalAccompanyCount($rowDelegate['id']);
								 
												if($showAccompanyCount!=0)
												{
													$sqlaccompany 		   = array();
													$sqlaccompany['QUERY'] = "SELECT * 
																				FROM "._DB_USER_REGISTRATION_." 
																			    WHERE user_type = ? 
																				  AND status = ? 
																				  AND refference_delegate_id= ?";
																				  
													$sqlaccompany['PARAM'][]   = array('FILD' => 'user_type',             			   'DATA' =>'ACCOMPANY',   		'TYP' => 's');
													$sqlaccompany['PARAM'][]   = array('FILD' => 'status',             				   'DATA' =>'A',   				'TYP' => 's');
													$sqlaccompany['PARAM'][]   = array('FILD' => 'refference_delegate_id',             'DATA' =>$rowDelegate['id'], 'TYP' => 's');
														
													$RESFetchaccompany	= $mycms->sql_select($sqlaccompany);
													$rowFetchaccompany		= $RESFetchaccompany[0];
														foreach($RESFetchaccompany as $key=>$rowFetchAccompany) 
														{
											?>
														<tr class="tlisting">
														<td align="left" valign="top"></td>
														<td align="left" valign="top"> 
															<?=str_replace('..','.',strtoupper($rowFetchAccompany['user_full_name']))?>(<?=str_replace('..','.',strtoupper($rowDelegate['user_full_name']))?>)
															<br />
															<?=$rowFetchAccompany['user_mobile_isd_code'].$rowFetchAccompany['user_mobile_no']?>
															<br />
															<?=$rowFetchAccompany['user_email_id']?>
															<br />
															<span style="color:#0000FF"><?=str_replace(',',', ',$rowFetchAccompany['tags'])?></span>
														
														</td>
														<td align="left" valign="top">
															<?php
															if($rowDelegate['registration_payment_status'] != "UNPAID")
															{
																echo $rowFetchAccompany['user_registration_id'];
															}
															else
															{
																echo "-";
															}
															?>
														</td>
														<td align="center">															
															<a operationMode="ProfileDetails" onclick="openAccompanyEditNamePopup(<?=$rowFetchAccompany['id']?>,'<?=$rowFetchAccompany['user_full_name']?>','<?=$rowFetchAccompany['user_age']?>','<?=$rowFetchAccompany['user_food_preference']?>')" class="btn btn-small btn-blue">Edit Deatils</a> 
														</td>
													</tr>
											<?php           
														}
												}
									}
								}
								else
								{
								?>
									<tr>
										<td colspan="6" align="center">
											<span class="mandatory" style="font-size:16px;">No Result Found !!</span>
										</td>
									</tr>
								<?php
								}
								?>
							</table>
						<?php
						
						?>
						
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
		
		<div class="popup_form" id="edit_email_popup_form">
			<form action="edit_userDetails.process.php" name="emailUpdatePopup" id="emailUpdatePopup" method="post" onsubmit="return formEmailValidation();">
				<input type="hidden" name="act" id="email_update" value="email_update" />
				<input type="hidden" name="user_id" id="user_id" />
				<input type="hidden" name="search_string" id="search_string" value="<?=$searchString?>"/>
				<input type="hidden" name="email_id_validation" id="email_id_validation" value="" />
				<input type="hidden" name="old_email_id" id="old_email_id" value="" />
				<table width="100%" class="tborder">
					<tr>
						<td colspan="4" class="tcat">
							<span style="float:left">Email Update</span>
							<span class="close" forType="tsearchTool" onclick="closeEditEmailPopup()">&times;</span>
						</td>
					</tr>
					<tr>
						<td width="15%" align="left">Email Id:</td>
						<td width="35%" align="right"><input type="text" id="new_email_id" name="new_email_id" style="width:100%" /></td>
						<td colspan="2" align="left"><div id="email_status"></div></td>
					</tr>
					<tr>
						<td align="left">&nbsp;</td>
						<td align="left">&nbsp;</td>
						<td align="left">&nbsp;</td>
						<td align="left">&nbsp;</td>
					</tr>
					<tr>
						<td align="left">&nbsp;</td>
						<td align="right"><input type="submit" class="ticket ticket-important" value="Update" /></td>
						<td align="left">&nbsp;</td>
						<td align="left">&nbsp;</td>
					</tr>
					<tr>
						<td colspan="4"></td>
					</tr>
				</table>		
			</form>
		</div>
		
		<div class="popup_form" id="edit_mobile_popup_form">
			<form action="edit_userDetails.process.php" name="mobileUpdatePopup" id="mobileUpdatePopup" method="post" onsubmit="return formMobileValidation();">
				<input type="hidden" name="act" id="mobile_update" value="mobile_update" />
				<input type="hidden" name="delegate_id" id="delegate_id"/>
				<input type="hidden" name="old_isd_code" id="old_isd_code" value="" />
				<input type="hidden" name="old_mobile_no" id="old_mobile_no" value="" />				
				<input type="hidden" name="mobile_id_validation" id="mobile_id_validation" value="" />				
				<input type="hidden" name="search_string" id="search_string" value="<?=$searchString?>"/>
				<table width="100%" class="tborder">
					<tr>
						<td colspan="4" class="tcat">
							<span style="float:left">Mobile No Update</span>
							<span class="close" forType="tsearchTool" onclick="closeEditSmsPopup()">&times;</span>
						</td>
					</tr>
					<tr>
						<td width="20%" align="left">Mobile No:</td>
						<td width="10%" align="right">
							<input type="text" id="new_isd_code" name="new_isd_code" id="isd_validation" forType="new_isd_code" style="width:30%"/>
						</td>
						<td width="30%" align="left">
							<input type="text" id="new_mobile_no" name="new_mobile_no" forType="new_mobile_no"/> 
							<input type="hidden" name="mobile_validation" id="mobile_validation" />
						</td>
						<td><div id="mobile_div"></div></td>
						
					</tr>
					<tr>
						<td align="left">&nbsp;</td>
						<td align="left">&nbsp;</td>
						<td align="left">&nbsp;</td>
						<td align="left">&nbsp;</td>
					</tr>
					<tr>
						<td align="left">&nbsp;</td>
						<td align="right"><input type="submit" class="ticket ticket-important" value="Update" style="margin-right: 20px;" /></td>
						<td align="left">&nbsp;</td>
						<td align="right">&nbsp;</td>
					</tr>
					<tr>
						<td colspan="4"></td>
					</tr>
				</table>		
			</form>
		</div>
		
		<div class="popup_form" id="edit_name_popup_form">
			<form action="edit_userDetails.process.php" name="nameUpdatePopup" id="nameUpdatePopup" method="post" onsubmit="return formNameValidation();">
				<input type="hidden" name="act" id="name_update" value="name_update" />
				<input type="hidden" name="delegateId" id="delegateId" value=""/>
				<input type="hidden" name="search_string" id="search_string" value="<?=$searchString?>"/>
				<table width="100%" class="tborder">
					<tr>
						<td colspan="5" class="tcat">
							<span style="float:left">Name Update</span>
							<span class="close" forType="tsearchTool" onclick="closeEditNamePopup()">&times;</span>
						</td>
					</tr>
					<tr>
						<td width="20%" align="left">Delegate Name:</td>
						<td width="22%" align="left"><input type="text" name="user_first_name" id="user_first_name" style="width:95%; text-transform:uppercase;" value="<?=$_REQUEST['user_full_name']?>"/></td>
						<td width="22%" align="left"><input type="text" name="user_middle_name" id="user_middle_name" style="width:95%; text-transform:uppercase;" value=""/></td>
						<td width="22%" align="left"><input type="text" name="user_last_name" id="user_last_name" style="width:95%; text-transform:uppercase;" value=""/></td>
					</tr>
					<tr>
						<td align="left">&nbsp;</td>
						<td align="left">&nbsp;</td>
						<td align="left">&nbsp;</td>
						<td align="left">&nbsp;</td>
						<td align="left">&nbsp;</td>
					</tr>
					<tr>
						<td align="left">&nbsp;</td>
						<td align="left">&nbsp;</td>
						<td align="right">&nbsp;</td>
						<td align="left">&nbsp;</td>
						<td align="left"><input type="submit" class="ticket ticket-important" value="Update" style="margin-right: 20px;" /></td>
					</tr>
					<tr>
						<td colspan="5"></td>
					</tr>
				</table>		
			</form>
		</div>
		
		<div class="popup_form" id="edit_accompany_name_popup_form">
			<form action="edit_userDetails.process.php" name="nameUpdatePopup" id="nameUpdatePopup" method="post" onsubmit="return formAccompanyNameValidation();">
				<input type="hidden" name="act" id="accomapny_name_update" value="accomapny_name_update" />
				<input type="hidden" name="accompanyId" id="accompanyId" value=""/>
				<input type="hidden" name="search_string" id="search_string" value="<?=$searchString?>"/>
				<table width="100%" class="tborder">
					<tr>
						<td colspan="4" class="tcat">
							<span style="float:left">Name Update</span>
							<span class="close" forType="tsearchTool" onclick="closeAccompanyEditNamePopup()">&times;</span>
						</td>
					</tr>
					<tr>
						<td width="20%" align="left">Accompany Name:</td>
						<td width="22%" align="left"><input type="text" name="user_full_name" id="user_full_name" style="width:95%; text-transform:uppercase;" value="<?=$_REQUEST['user_full_name']?>"/></td>
						<td width="22%" align="left">Age</td>
						<td width="22%" align="left">
							<input type="text" name="accompany_edit_age" id="accompany_edit_age" value="<?=$_REQUEST['accompany_edit_age']?>" style="width:95%; text-transform:uppercase;"/>
						</td>
					</tr>
					<tr>
						<td width="22%" align="left">Food Preference</td>
						<td width="22%" align="left" colspan="3">
							<input type="radio" name="accompany_edit_food_preference" id="accompany_edit_food_veg" value="VEG" <?=$_REQUEST['accompany_edit_food_preference']=='VEG'?'checked=checked':''?> /> Veg 
							<input type="radio" name="accompany_edit_food_preference" id="accompany_edit_food_nonveg" value="NON_VEG" <?=$_REQUEST['accompany_edit_food_preference']=='NON_VEG'?'checked=checked':''?> /> Non-veg 
						</td>
					</tr>
					<tr>
						<td align="left">&nbsp;</td>
						<td align="left">&nbsp;</td>
						<td align="right">&nbsp;</td>
						<td align="left"><input type="submit" class="ticket ticket-important" value="Update" style="margin-right: 10px; float:right" /></td>
					</tr>
					<tr>
						<td colspan="4"></td>
					</tr>
				</table>		
			</form>
		</div>
	<?php
	}
	?>
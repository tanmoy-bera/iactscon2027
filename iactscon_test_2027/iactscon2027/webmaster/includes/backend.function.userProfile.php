<?php
	/**************************************************************/
	/*                  LOGGED USER PROFILE WINDOW                */
	/**************************************************************/
	function viewUserProfile($cfg, $mycms)
	{    
		global $searchArray, $searchString;
		
		$sqlEmployee['QUERY'] = "SELECT DISTINCT empUser.*,
										
										empRole.roleName    
								   
								   FROM ".$cfg['DB.CONF.USER']." empUser 
									 
							 INNER JOIN ".$cfg['DB.CONF.ROLE']." empRole 
									 ON empRole.roleId = empUser.priviledge_role  
									 
								  WHERE empUser.a_id = '".$mycms->getLoggedUserId()."'";
		
		$resultEmployee = $mycms->sql_select($sqlEmployee);
		$rowEmployee    = $resultEmployee[0];
	?>
		<script language="javascript">
			/**********************************************************/
			/*                REDIRECTION TO EDIT UNIT                */
			/**********************************************************/
			function openEditScreen(displayDiv, editDiv)
			{
				$("#"+displayDiv).css('display','none');
				$("#"+editDiv).css('display','block');
			}
			
			/**********************************************************/
			/*                  SUBMIT DESIRED FORM                   */
			/**********************************************************/
			function submitForm(formId)
			{
				$("#"+formId).submit();
			}
			
			/*********************************************************/
			/*                     OPEN POPUP                        */
			/*********************************************************/
			function openPopUp(fadeDiv, formDiv)
			{
				$("#"+fadeDiv).fadeIn("fast");
				$("#"+formDiv).fadeIn("fast");
			}
			
			/*********************************************************/
			/*                     CLOSE POPUP                       */
			/*********************************************************/
			function closePopUp(fadeDiv, formDiv)
			{
				$("#"+fadeDiv).fadeOut("fast");
				$("#"+formDiv).fadeOut("fast");
			}
		</script>
		<table width="100%" align="center" class="tborder"> 
			<thead> 
				<tr> 
					<td colspan="2" align="left" class="tcat" valign="middle">
						<span style="float:left; border-radius: 500px; background-color:#FFFFFF; width: 80px; height: 80px;">
							<?php
							@$employeeImage = "";
							if($rowEmployee['image']!="" && file_exists("../../".$cfg['USER.IMAGES'].$rowEmployee['image']))
							{
								$employeeImage = "../../".$cfg['USER.IMAGES'].$rowEmployee['image'];
							}
							else
							{
								$employeeImage = $cfg['DIR_CM_IMAGES']."noUserCircularImage.jpg";
							}
							?>
							<img src="<?=$employeeImage?>" width="80" style="border-radius: 500px;" />
						</span>
						<span style="float:left; margin-top:13px; margin-left:20px;">
							<span>Welcome</span>
							<br />
							<span style="font-size:24px;"><?=$rowEmployee['name']?></span>
							<br />
							<span style="font-weight:normal;font-size: 11px; cursor:pointer;" onclick="openPopUp('fade_popup','form_popup')">Change Image</span>
						</span>
					</td> 
				</tr> 
			</thead> 
			<tbody>  
				<tr>
					<td colspan="2" style="margin:0px; padding:0px;">
					
						<div id="printContainer">
							<table width="100%" cellpadding="6" cellspacing="1">
								<tr>
									<td width="50%" style="margin:0px; padding:0px;" valign="top">
										
										<?php
										/**********************************************************/
										/*                LOGIN DETAILS DISPLAY SCREEN            */
										/**********************************************************/
										?>
										<table width="100%" class="tborder">
											<tr class="thighlight">
												<td colspan="2">Login Details</td>
											</tr>
											<tr>
												<td height="27" width="40%" align="left">Username:</td>
												<td width="60%" align="left"><?=$rowEmployee['username']?></td>
											</tr>
											<tr>
												<td height="27" align="left">Access Role:</td>
												<td align="left"><?=$rowEmployee['roleName']?></td>
											</tr>
										</table>
										
										<?php
										/**********************************************************/
										/*           EMPLOYEE PERSONAL DETAILS DISPLAY SCREEN     */
										/**********************************************************/
										?>
										<div id="employeePersonalDetailsDisplay">
											<table width="100%" class="tborder">
												<tr class="thighlight">
													<td colspan="2">
														<span style="float:left">Personal Details</span>
														<span class="icon-pen-alt2" style="float:right; cursor:pointer;" 
														 onclick="openEditScreen('employeePersonalDetailsDisplay','employeePersonalDetailsEdit')"></span>
													</td>
												</tr>
												<tr>
													<td height="27" width="40%" align="left">Name:</td>
													<td width="60%" align="left"><?=$rowEmployee['name']?></td>
												</tr>
												<tr>
													<td height="27" align="left">Email Id:</td>
													<td align="left"><?=$rowEmployee['email']?></td>
												</tr>
												<tr>
													<td height="27" align="left">Phone No:</td>
													<td align="left"><?=$rowEmployee['phone_no']?></td>
												</tr>
												<tr>
													<td height="27" align="left">Mobile No:</td>
													<td align="left"><?=$rowEmployee['mobile_no']?></td>
												</tr>
											</table>
										</div>
										
										<?php
										/**********************************************************/
										/*            EMPLOYEE PERSONAL DETAILS EDIT SCREEN       */
										/**********************************************************/
										?>
										<div id="employeePersonalDetailsEdit" style="display:none;">
											<form name="frmEmployeePersonalDetails" id="frmEmployeePersonalDetails" action="userProfile.process.php" method="post">
												<input type="hidden" name="act" value="updatePersonalDetails" />
												<table width="100%" class="tborder">
													<tr class="thighlight">
														<td colspan="2">
															<span style="float:left">Personal Details</span>
															<span class="icon-folder-fill" style="float:right; cursor:pointer;" 
															 onclick="submitForm('frmEmployeePersonalDetails')"></span>
														</td>
													</tr>
													<tr>
														<td height="27" width="40%" align="left">Name:</td>
														<td width="60%" align="left">
															<input name="employee_name" id="employee_name" value="<?=$rowEmployee['name']?>" 
															 style="width:90%;" />
														</td>
													</tr>
													<tr>
														<td height="27" align="left">Email Id:</td>
														<td align="left">
															<input name="employee_email_id" id="employee_email_id" value="<?=$rowEmployee['email']?>" 
															 style="width:90%;" />
														</td>
													</tr>
													<tr>
														<td height="27" align="left">Phone No:</td>
														<td align="left">
															<input name="employee_phone_no" id="employee_phone_no" value="<?=$rowEmployee['phone_no']?>" 
															 style="width:90%;" />
														</td>
													</tr>
													<tr>
														<td height="27" align="left">Mobile No:</td>
														<td align="left">
															<input name="employee_mobile_no" id="employee_mobile_no" value="<?=$rowEmployee['mobile_no']?>" 
															 style="width:90%;" />
														</td>
													</tr>
												</table>
											</form>
										</div>
										
									</td>
									<td></td>
								</tr>
							</table>
						</div>
					
					</td>
				</tr>
				<tr class="tfooter">
					<td colspan="2">&nbsp;</td>
				</tr>
			</tbody> 
		</table>
		
		<div class="overlay" id="fade_popup"></div>
		<div class="popup_form" id="form_popup">
			<form action="userProfile.process.php" name="frmChangeUserImage" id="frmChangeUserImage" enctype="multipart/form-data" method="post">
				<input type="hidden" name="act" value="changeUserImage" />
				<table width="100%" class="tborder">
					<tr>
						<td class="tcat">
							<span style="float:left">Change User Image</span>
							<span class="close" forType="tsearchTool" 
							 onclick="closePopUp('fade_popup','form_popup')">&times;</span>
						</td>
					</tr>
					<tr>
						<td align="center">
							<span style="border-radius: 500px; background-color:#FFFFFF; width: 90px; height: 90px;">
								<?php
								@$employeeImage = "";
								if($rowEmployee['image']!="" && file_exists("../../".$cfg['USER.IMAGES'].$rowEmployee['image']))
								{
									$employeeImage = "../../".$cfg['USER.IMAGES'].$rowEmployee['image'];
								}
								else
								{
									$employeeImage = $cfg['DIR_CM_IMAGES']."noUserCircularImage.jpg";
								}
								?>
								<img src="<?=$employeeImage?>" width="90" height="90" style="border-radius: 500px;" />
							</span>
						</td>
					</tr>
					<tr>
						<td align="center">
							<span class="mandatory">Please upload 90px X 90px for better visual display.</span>
						</td>
					</tr>
					<tr>
						<td align="center">
							<input type="file" name="user_image" id="user_image" />
						</td>
					</tr>
					<tr>
						<td align="center">
							<input type="submit" name="Save" id="Save" value="Save" class="btn btn-small btn-blue">
						</td>
					</tr>
				</table>
			</form>
		</div>
	<?php
	}
	
	/**************************************************************/
	/*                 UPDATE PERSONAL DETAILS PROCESS            */
	/**************************************************************/
	function updatePersonalDetailsProcess()
	{
		global $cfg, $mycms;
		
		$loggedUserID                     = $mycms->getLoggedUserId(); 
		$employee_name                    = addslashes($_REQUEST['employee_name']);
		$employee_email_id                = addslashes($_REQUEST['employee_email_id']);
		$employee_phone_no                = addslashes($_REQUEST['employee_phone_no']);
		$employee_mobile_no               = addslashes($_REQUEST['employee_mobile_no']);
		
		$sqlUpdate			              = "UPDATE ".$cfg['DB.CONF.USER']." 
												SET `name` = '".$employee_name."',
													`email` = '".$employee_email_id."',
													`phone_no` = '".$employee_phone_no."',
													`mobile_no` = '".$employee_mobile_no."', 
													`modified_by` = '".$loggedUserID."',
													`modified_ip` = '".$_SERVER['REMOTE_ADDR']."',
													`modified_sessionId` = '".session_id()."',
													`modified_dateTime` = '".date('Y-m-d H:i:s')."'
											  WHERE `a_id` = '".$loggedUserID."'";
		$mycms->sql_update($sqlUpdate);	
	}
	
	/**************************************************************/
	/*                    UPDATE USER IMAGE PROCESS               */
	/**************************************************************/
	function updateUserImageProcess()
	{
		global $cfg, $mycms;
		
		$loggedUserID                   = $mycms->getLoggedUserId(); 
		$userImage                      = $_FILES['user_image']['name'];
		$userImageTempFile              = $_FILES['user_image']['tmp_name'];
		$userImageFileName              = $loggedUserID."_".time().strstr($userImage,'.');
		
		if($userImageTempFile!="")
		{
			$userImagePath              = "../../".$cfg['USER.IMAGES'].$userImageFileName;
			
			chmod($userImagePath, 0777);
			copy($userImageTempFile, $userImagePath);
			chmod($userImagePath, 0777);
	
			$sqlUserImage = "UPDATE ".$cfg['DB.CONF.USER']." 
								SET `image`='".$userImageFileName."' 
							  WHERE `a_id`='".$loggedUserID."'";
			$mycms->sql_update($sqlUserImage);
		}
	}
?>
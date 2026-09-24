<?php
	include_once('includes/init.php');
	page_header("User Profile");
?>
    <script language="javascript" src="scripts/CountryStateCityRetriver.js"></script>
	<div class="container">
		<?php 
			switch($show){						
				
				/***************************************************************************/
				/* Name:       viewUserProfile                                             */
				/* Location:   root/includes/function.userProfile.php                      */
				/* Parameters:                                                             */
				/***************************************************************************/
				default:	
				
					viewUserProfile($cfg, $mycms); 
					break;				
			} 
		?>
	</div>
<?php
	page_footer();
	
	function viewUserProfile($cfg, $mycms)
	{
		global $cfg, $mycms;
		
		$facultyId                 = $mycms->getLoggedUserId();
		
		$sqlFacultyDetails         = facultyDetailsQueryset($facultyId, "");
		$resultFacultyDetails      = $mycms->sql_select($sqlFacultyDetails);	
		$rowFacultyDetails         = $resultFacultyDetails[0];
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
						<span style=" border-radius: 500px; background-color:#FFFFFF; width: 80px; height: 80px;">
							<?php
							@$employeeImage = "";
							if($rowFacultyDetails['faculty_profile_image']!="" && file_exists("../../".$cfg['FACULTY.PROFILE.IMAGE'].$rowFacultyDetails['faculty_profile_image']))
							{
								$employeeImage = "../../".$cfg['FACULTY.PROFILE.IMAGE'].$rowFacultyDetails['faculty_profile_image'];
							}
							else
							{
								$employeeImage = $cfg['DIR_CM_IMAGES']."noUserCircularImage.jpg";
							}
							?>
							<img src="<?=$employeeImage?>" width="80" style="border-radius: 500px;" />
						</span>
						<span style=" margin-top:13px; margin-left:20px;">
							<span>Welcome</span>
							<br />
							<span style="font-size:24px;"><?=$rowFacultyDetails['faculty_full_name']?></span>
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
												<td width="60%" align="left"><?=$rowFacultyDetails['faculty_login_username']?></td>
											</tr>
											<tr>
												<td height="27" align="left">Access Role:</td>
												<td align="left"><?=$rowFacultyDetails['faculty_role']?></td>
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
													<td height="27" width="40%" align="left">Title:</td>
													<td width="60%" align="left"><?=$rowFacultyDetails['faculty_title']?></td>
												</tr>
												<tr>
													<td height="27" align="left">First Name:</td>
													<td align="left"><?=$rowFacultyDetails['faculty_first_name']?></td>
												</tr>
												<tr>
													<td height="27" align="left">Middle Name:</td>
													<td align="left"><?=$rowFacultyDetails['faculty_middle_name']?></td>
												</tr>
												<tr>
													<td height="27" align="left">Last Name:</td>
													<td align="left"><?=$rowFacultyDetails['faculty_last_name']?></td>
												</tr>
												<tr>
													<td height="27" align="left">Designation:</td>
													<td align="left"><?=$rowFacultyDetails['faculty_designation']?></td>
												</tr>
												<tr>
													<td height="27" align="left">Institution Name:</td>
													<td align="left"><?=$rowFacultyDetails['faculty_institution_name']?></td>
												</tr>
												<tr>
													<td height="27" align="left">Specification:</td>
													<td align="left"><?=$rowFacultyDetails['faculty_specification']?></td>
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
														<td height="27" width="40%" align="left">Title:</td>
														<td width="60%" align="left">
															<select name="faculty_title_edit" id="faculty_title_edit" style="width:90%; text-transform:uppercase;">
																<option value="">-- Select Title --</option>
																<option value="Dr" <?=($rowFacultyDetails['faculty_title']=="DR")?'selected="selected"':''?>>Dr.</option>
																<option value="Prof" <?=($rowFacultyDetails['faculty_title']=="PROF")?'selected="selected"':''?>>Prof.</option>
																<option value="Mr" <?=($rowFacultyDetails['faculty_title']=="MR")?'selected="selected"':''?>>Mr.</option>
																<option value="Ms" <?=($rowFacultyDetails['faculty_title']=="MS")?'selected="selected"':''?>>Ms.</option>
																<option value="Mrs" <?=($rowFacultyDetails['faculty_title']=="MRS")?'selected="selected"':''?>>Mrs.</option>
															</select>
														</td>
													</tr>
													<tr>
														<td height="27" align="left">First Name:</td>
														<td align="left">
															<input type="text" name="faculty_first_name_edit" id="faculty_first_name_edit" 
									 						 style="width:90%; text-transform:uppercase;" value="<?=$rowFacultyDetails['faculty_first_name']?>" />
														</td>
													</tr>
													<tr>
														<td height="27" align="left">Middle Name:</td>
														<td align="left">
															<input type="text" name="faculty_middle_name_edit" id="faculty_middle_name_edit" 
															 style="width:90%; text-transform:uppercase;" value="<?=$rowFacultyDetails['faculty_middle_name']?>" />
														</td>
													</tr>
													<tr>
														<td height="27" align="left">Last Name:</td>
														<td align="left">
															<input type="text" name="faculty_last_name_edit" id="faculty_last_name_edit" 
															 style="width:90%; text-transform:uppercase;" value="<?=$rowFacultyDetails['faculty_last_name']?>" />
														</td>
													</tr>
													<tr>
														<td height="27" align="left">Designation:</td>
														<td align="left">
															<input type="text" name="faculty_designation_edit" id="faculty_designation_edit" 
															 style="width:90%; text-transform:uppercase;" value="<?=$rowFacultyDetails['faculty_designation']?>" />
														</td>
													</tr>
													<tr>
														<td height="27" align="left">Institution Name:</td>
														<td align="left">
															<input type="text" name="faculty_institution_name_edit" id="faculty_institution_name_edit" 
															 style="width:90%; text-transform:uppercase;" value="<?=$rowFacultyDetails['faculty_institution_name']?>" />
														</td>
													</tr>
													<tr>
														<td height="27" align="left">Specification:</td>
														<td align="left">
															<input type="text" name="faculty_specification_edit" id="faculty_specification_edit" 
															 style="width:90%; text-transform:uppercase;" value="<?=$rowFacultyDetails['faculty_specification']?>" />
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
								@$employeeImage    = "";
								if($rowFacultyDetails['faculty_profile_image']!="" && file_exists("../../".$cfg['FACULTY.PROFILE.IMAGE'].$rowFacultyDetails['faculty_profile_image']))
								{
									$employeeImage = "../../".$cfg['FACULTY.PROFILE.IMAGE'].$rowFacultyDetails['faculty_profile_image'];
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
							<input type="file" name="faculty_image" id="faculty_image" />
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
?>
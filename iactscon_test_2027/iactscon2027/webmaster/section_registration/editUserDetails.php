<?
include_once('includes/init.php');
	
	page_header("Edit User Basic Details");
	
	$searchArray['src_email_id']               = addslashes(trim($_REQUEST['src_email_id']));
	$searchArray['src_access_key']             = addslashes(trim($_REQUEST['src_access_key']));
	$searchArray['src_mobile_no']              = addslashes(trim($_REQUEST['src_mobile_no']));
	$searchArray['src_user_first_name']        = addslashes(trim($_REQUEST['src_user_first_name']));
	$searchArray['src_user_middle_name']       = addslashes(trim($_REQUEST['src_user_middle_name']));
	$searchArray['src_user_last_name']         = addslashes(trim($_REQUEST['src_user_last_name']));
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
	
	<script type="text/javascript" language="javascript" src="<?=_BASE_URL_?>js/website/returnData.process.js"></script>
	
		
	<div class="container">
		<?php 
		switch($show){
		
			default:
				
				viewDelegateRegistrationDetails($cfg, $mycms); 
				break;
		}
		?>
	</div>
	<?php
	page_footer();
	
function viewDelegateRegistrationDetails($delegateId)
{
 global $cfg, $mycms;
 include_once('../../includes/function.delegate.php');
$delegateId 			= $_REQUEST['delegateId'];
$sqlFetchUser           = registrationDetailsQuery($delegateId); 
$rowFetchUser           = $sqlFetchUser;

$loggedUserId			= $mycms->getLoggedUserId();

// FETCHING LOGGED USER DETAILS
$sqlSystemUser 					=	array();
$sqlSystemUser['QUERY']      	= "SELECT * 
									 FROM "._DB_CONF_USER_." 
								    WHERE `a_id` = '".$loggedUserId."'";
							   
$resultSystemUser   	= $mycms->sql_select($sqlSystemUser);
$rowSystemUser      	= $resultSystemUser[0];
?>

<div>
	<table width="100%" class="tborder">
		<tr>
			<td class="tcat">
				<span style="float:left">User Profile</span>
				
			</td>
		</tr>
	</table>
</div>

<table width="100%" align="center" class="tborder"> 
	<form name="frmUpdateProfile" id="frmUpdateProfile" action="edit_userDetails.process.php" method="post">
	<input type="hidden" name="act" value="update_profile" autocomplete="off">
	<input type="hidden" name="delegate_id" id="delegate_id" value="<?=$delegateId?>" />	 
		<tbody>
			<tr>
				<td  style="margin:0px; padding:0px;">  
				
					<table width="100%">
						<tr class="thighlight">
							<td colspan="4" align="left">User Details</td>
						</tr>
						<tr>
							<td width="20%" align="left">Name:</td>
							<td width="30%" align="left">
								<?=strtoupper($rowFetchUser['user_full_name'])?> 
								
							</td>
							<td align="left" width="20%">Member Type:</td>
							<td align="left" width="30%"><?=$rowFetchUser['classification_title']?></td>
						</tr>
						<tr>
							<td align="left">Country:</td>
							<td align="left">
							<select name="user_country" id="user_country" class="drpdwn" style="text-transform:uppercase;width:70%" forType="country">
									<option value="">-- Select Country --</option>
									<?php
									$rowFetchUser['user_country_id'];
									$sqlFetchCountry 	=	array();
									$sqlFetchCountry['QUERY']    = "SELECT * 
																	  FROM "._DB_COMN_COUNTRY_." 
																	  WHERE `status` = 'A' 
																	ORDER BY `country_name` ASC";
									$resultFetchCountry = $mycms->sql_select($sqlFetchCountry);
									if($resultFetchCountry)
									{
										foreach($resultFetchCountry as $keyCountry=>$rowFetchCountry)
										{
									?>
											<option value="<?=$rowFetchCountry['country_id']?>" <?=($rowFetchCountry['country_id']==$rowFetchUser['user_country_id'])?'selected="selected"':''?>><?=$rowFetchCountry['country_name']?></option>
									<?php
										}
									}
									?>
							</select>
							</td>
							<td align="left">State:</td>
							<td align="left">
							<select name="user_state" id="user_state" class="drpdwn" style="text-transform:uppercase;width:70%" forType="state">
									<option value="">-- Select Country First --</option>
									<?php
									$sqlFetchState 	=	array();
									$sqlFetchState['QUERY']    = "SELECT * 
																	FROM "._DB_COMN_STATE_." 
																	 WHERE `status` = 'A' 
																	   AND `country_id` = '".$rowFetchUser['user_country_id']."'
																  ORDER BY `state_name`";
															  
									$resultState      = $mycms->sql_select($sqlFetchState);
									if($resultState)
									{
										foreach($resultState as $i=>$rowState)
										{
									?>
											<option value="<?=$rowState['st_id']?>" <?=($rowState['st_id']==$rowFetchUser['user_state_id'])?'selected="selected"':''?>><?=$rowState['state_name']?></option>
									<?php
										}
									}
									?>
							</select>
							</td>
						</tr>
						<tr>
							<td align="left">Address:</td>
							<td align="left">
								<textarea name="user_address" id="user_address" style="text-transform:uppercase; width:70%" class="txt_fld" autocomplete="off"><?=$rowFetchUser['user_address']?></textarea>
							</td>
							<td align="left">Pin Code:</td>
							<td align="left"><input type="text" name="user_postal_code" id="user_postal_code" value="<?=$rowFetchUser['user_pincode']?>" class="fld" style="text-transform:uppercase;width:70%" /> </td>
						</tr>
						<tr>
							<td align="left">City:</td>
							<td align="left">
								<input type="text" name="user_city" id="user_city" value="<?=$rowFetchUser['user_city']?>" class="fld" style="text-transform:uppercase;width:70%" /> 
							</td>
							<td >Food Preference</td>
							<td >
								<input type="radio" groupName="user_food_choice"  name="user_food_preference" id="user_food_veg" value="VEG" <?=(($rowFetchUser['user_food_preference']=='VEG')?'checked=checked':'');?> /> Veg 
								&nbsp;
								<input type="radio" groupName="user_food_choice" name="user_food_preference" id="user_food_nonveg" value="NON_VEG"  <?=(($rowFetchUser['user_food_preference']=='NON_VEG')?'checked=checked':'');?> /> Non-veg 
							</td>
						</tr>
						<tr>
							<td align="right" colspan="4"><input type="submit" value="Update Details" class="btn btn-small btn-blue"  /></td>
						</tr>
					</table>
					
				</td>
			</tr>
			
		</tbody> 
	</form>
</table>
		

<?php
}
?>
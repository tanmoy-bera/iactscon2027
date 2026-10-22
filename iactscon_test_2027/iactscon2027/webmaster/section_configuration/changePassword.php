<?php
    include_once('includes/init.php');
	include_once('../../includes/function.changePassword.php');
	
	switch($action){
	
		case md5("changepass"):
		
			changePasswordProcess();
			break; 
	}  

	page_header("Change Password");
?>
	<div class="container">
		<?php
			changePasswordFormWindow();
		?>
	</div>
<?php 
    page_footer();
?> 
<?php
	/******************************************************/
	/*               CHANGE PASSWORD FORM WINDOW          */
	/******************************************************/
	function changePasswordFormWindow()
	{
		global $cfg, $mycms;
	?>
	<script>
		$(document).ready(function(){
			$('#new_password').change(function(){
				$('#confirm_password').val('');
				$('#errorPassword').hide();			
			});
			$('#confirm_password').change(function(){					
				var new_pass = $('#new_password').val();	
				var new_conf_pass = $('#confirm_password').val();	
				if(new_pass != new_conf_pass){
					$('#errorPassword').show();
				}
				else{
					$('#errorPassword').hide();
				}
			});
		});
	</script>
		<form name="frm_changpass" action="" method="post" onsubmit="return onSubmitAction();"> 
			<input type="hidden" name="act" value="<?=md5("changepass")?>" />
			<input type="hidden" name="id" value="<?=$mycms->getLoggedUserId()?>" />		
			<table width="50%" class="tborder"> 
				<thead> 
					<tr> 
						<td colspan="2" align="left" class="tcat">Change Password</td> 
					</tr> 
				</thead> 
				<tbody>
					<tr>
						<td colspan="2" align="center" style="margin:0px; padding:0px;">
							
							<table width="100%">
								<tr>
									<td width="40%" align="left">Username</td>
									<td width="60%" align="left"><?=$mycms->getLoggedUserName()?></td>
								</tr>
								<tr>
									<td align="left">Existing Password</td>
									<td align="left">
										<input name="old_password" type="password" id="old_password" label="Existing Password" required="">
									</td>
								</tr>
								<tr>
									<td align="left">New Password</td>
									<td align="left">
										<input name="new_password" type="password" id="new_password" label="New Password" required="" ">
									</td>
								</tr>
								<tr>
									<td align="left">Confirm Password</td>
									<td align="left">
										<input name="confirm_password" type="password" id="confirm_password" label="Confirm Password" required="" />
										<span id="errorPassword" style="float:right; color:#FF0000; display: none;">Password mismatch</span>
									</td>
								</tr>
							</table>
							
						</td>
					</tr>
					<tr>
						<td width="40%"></td>
						<td align="left">
							<input type="reset" name="resetButton" id="resetButton" value="Reset" class="btn btn-small btn-red" /></a>
							&nbsp;
							<input type="submit" name="SaveButton" id="SaveButton" value="Change Password" class="btn btn-small btn-blue" />
						</td>
					</tr>
					<tr>
						<td colspan="2"></td>
					</tr>
					<tr class="tfooter">
						<td colspan="2">&nbsp;</td>
					</tr>
				</tbody>
			</table>
		</form>
	<?php
	}
	?>
	<?php
	/******************************************************/
	/*                 CHANGE PASSWORD PROCESS            */
	/******************************************************/
	function changePasswordProcess()
	{
		global $cfg, $mycms;
		
		if(isset($_REQUEST['id']) && ($_REQUEST['id']==$mycms->getLoggedUserId()))
		{
			$old_password = md5($_REQUEST['old_password']);
			$new_password = md5($_REQUEST['new_password']);
			
			$user_check_query['QUERY'] = "SELECT * FROM "._DB_CONF_USER_." 
			                               WHERE `password` ='".$old_password."' 
										     AND `a_id`='".$mycms->getLoggedUserId()."'";
			
			$change_pass['QUERY']   = "UPDATE "._DB_CONF_USER_." 
				                      	  SET `password` = '".$new_password."', 
											   `modified_by`='".$mycms->getLoggedUserId()."',
										       `modified_ip`='".$_SERVER['REMOTE_ADDR']."',
										       `modified_sessionId`='".session_id()."',
										       `modified_dateTime`='".date('Y-m-d H:i:s')."'
								     	 WHERE `a_id` = '".$mycms->getLoggedUserId()."' ";
							
			$user_check_result = $mycms->sql_select($user_check_query);
			
			if ($user_check_result)
			{					
				$change_result = $mycms->sql_update($change_pass);
				$mycms->redirect('changePassword.php?m=Password Changed Successfully');
				exit();
			}
			else
			{
				$msg = "Please enter correct existing password.";
			}
		}
	}
?>

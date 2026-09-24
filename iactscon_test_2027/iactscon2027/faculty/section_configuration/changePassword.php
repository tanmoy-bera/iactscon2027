<?php
    include_once('includes/init.php');

	page_header("Change Password");
?>
	<script language="javascript" src="scripts/changePassword.js"></script>
	<div class="container">
		<form name="frmChangePassword" id="frmChangePassword" action="changePassword.process.php" method="post"> 
			<input type="hidden" name="act" value="<?=md5("changepass")?>" />		
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
									<td width="30%" align="left">Username</td>
									<td width="70%" align="left"><?=$mycms->getLoggedUserName()?></td>
								</tr>
								<tr>
									<td align="left">Existing Password</td>
									<td align="left">
										<input type="password" name="old_password" id="old_password" />
									</td>
								</tr>
								<tr>
									<td align="left">New Password</td>
									<td align="left">
										<input type="password" name="new_password" id="new_password" />
									</td>
								</tr>
								<tr>
									<td align="left">Confirm Password</td>
									<td align="left">
										<input type="password" name="confirm_password" id="confirm_password" />
									</td>
								</tr>
							</table>
							
						</td>
					</tr>
					<tr>
						<td width="30%"></td>
						<td align="left">
							<input type="reset" name="resetButton" id="resetButton" value="Reset" 
							 class="btn btn-small btn-red" />
							
							&nbsp;
							
							<input type="submit" name="SaveButton" id="SaveButton" value="Change Password" 
							 class="btn btn-small btn-blue" />
						</td>
					</tr>
					<tr class="tfooter">
						<td colspan="2">&nbsp;</td>
					</tr>
				</tbody>
			</table>
		</form>
	</div>
<?php 
    page_footer();
?> 


$(document).ready(function(){
	$("#frmChangePassword").submit(function(){
		if(changePasswordFormValidation()==false)
		{
			return false;
		}
		else
		{
			return true;
		}
	});
});

function changePasswordFormValidation()
{
	var oldPassword           = $("#old_password").val();
	var newPassword           = $("#new_password").val();
	var confirmPassword       = $("#confirm_password").val();
	
	if(oldPassword=="")
	{
		alert("Please Enter Existing Password.");
		$("#old_password").focus();
		return false;
	}
	if(oldPassword!=getLoggedUserPassword())
	{
		alert("Incorrect Existing Password.");
		$("#old_password").focus();
		$("#old_password").val("");
		return false;
	}
	if(newPassword=="")
	{
		alert("Please Enter New Password.");
		$("#new_password").focus();
		return false;
	}
	if(oldPassword==newPassword)
	{
		alert("Attempting Same Password.");
		$("#new_password").focus();
		$("#new_password").val("");
		return false;
	}
	if(confirmPassword=="")
	{
		alert("Please Confirm New Password.");
		$("#new_password").focus();
		return false;
	}
	if(newPassword!=confirmPassword)
	{
		alert("Please Confirm Your Password Correctly.");
		$("#confirm_password").focus();
		$("#confirm_password").val("");
		return false;
	}
}

function getLoggedUserPassword()
{
	var oldPassword  = "";
	
	$.ajax({
				type: "POST",
				url: 'changePassword.process.php',
				data: 'act=loggedUserPassword',
				dataType: 'json',
				async: false,
				success: function(JSONObject)
				{
					oldPassword  = JSONObject.GET_LOGGED_PASSWORD;
					oldPassword  = oldPassword.trim();
				}
		  });
	
	return oldPassword;
}
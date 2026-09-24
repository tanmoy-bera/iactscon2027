/****************************************************/
/*            OPEN FORGET PASSWORD POPUP            */
/****************************************************/
function openForgetPasswordPopup()
{
	resetForm();
	$("div[fortype=loginPanel]").css("display","none");
	$("div[fortype=forgetPasswordPanel]").css("display","block");
}

/****************************************************/
/*                OPEN LOGIN POPUP                  */
/****************************************************/
function openLoginPopup()
{
	resetForm();
	$("div[fortype=loginPanel]").css("display","block");
	$("div[fortype=forgetPasswordPanel]").css("display","none");
}

function resetForm()
{
	$("input").each(function(){
		$("#uname").css("border-color","#CCC");
	});
	$("#loginMessageBox").text("");
	$("#forgetPasswordMessageBox").text("");
}

/***************************************************/
/*               LOGIN FORM VALIDATOR              */
/***************************************************/
function loginFormValidator()
{
	var uname   = $("#uname").val();
	var pass    = $("#pass").val();
	
	if(uname=="")
	{
		$("#uname").focus();
		$("#uname").css("border-color","#D41000");
		$("#loginMessageBox").text("");
		$("#loginMessageBox").text("Please Provide Username");
		return false;
	}
	if(pass=="")
	{
		$("#pass").focus();
		$("#pass").css("border-color","#D41000");
		$("#loginMessageBox").text("");
		$("#loginMessageBox").text("Please Provide Password");
		return false;
	}
	var md5password 	=	CryptoJS.MD5(pass);
	$("#pass").val(md5password);
	return true;
}

function emailRetriver()
{
	var user_email   = $("#user_email").val();
	if(user_email=="")
	{
		$("#user_email").focus();
		$("#user_email").css("border-color","#D41000");
		$("#forgetPasswordMessageBox").text("");
		$("#forgetPasswordMessageBox").text("Please Provide Email Id");
		return false;
	}
	else
	{
		$.ajax({
					type: "POST",
					url: "login.php",
					data: 'act=sendPassword&user_email='+user_email,
					dataType: "text",
					async:false,
					success: function(message){
						$("#forgetPasswordMessageBox").text("");
						$("#forgetPasswordMessageBox").text("Your Login Details Been Send");
					}
              });
	}
}
$(document).ready(function(){
	
	$("a[operationMode=changeProfileImage]").click(function(){
		$("#user_profile_image").click();
		$("#user_profile_image").change(function() { 
			
			var fileExtension = ['jpeg', 'jpg', 'png'];

			if($.inArray($('#user_profile_image').val().split('.').pop().toLowerCase(), fileExtension) == -1 
			   && $('#user_profile_image').val()!="") 
			{
				alert("Only .jpeg, .jpg and .png formats are allowed.");
				
				$('#user_profile_image').val("");
				$('#user_profile_image').focus();
				
				return false;
			}
			else
			{
				if($('#user_profile_image').val()!="")
				{
					readURL(this);
				}
				else
				{
					return false;
				}
			}
			
		});
	});
	
	try
	{
	initiateTimeChooser();
	} catch(e){}
});

function readURL(input) 
{
	if(input.files && input.files[0]) 
	{
		var reader    = new FileReader();
		reader.onload = function (e){
			
			$('#userProfileImagePreview').attr('src', e.target.result);
		}
		
		reader.readAsDataURL(input.files[0]);
	}
}

function disableTextStatus(id)
{
	if($('#date_id_'+id).prop('checked'))
	{
		$('input[use=avalable_'+id+']').attr("disabled",false);
	}
	else
	{
		$('input[use=avalable_'+id+']').attr("disabled",true);
	}
}

function namePrint() {
    var first_name = $('#participantFirstName').val() || '';
    var middle_name = $('#participantMiddleName').val() || '';
    var last_name = $('#participantLastName').val() || '';

    var name = first_name + " " + middle_name + " " + last_name;
    
    var res = name.toUpperCase();
    
    $('#pername').text(res);
}


function jsUcfirst(text) 
{
	var parts = text.split(' '),
		len = parts.length,
		i, words = [];
	for (i = 0; i < len; i++) {
		var part = parts[i];
		var first = part[0].toUpperCase();
		var rest = part.substring(1, part.length);
		var word = first + rest;
		words.push(word);
		}
	return words.join(' ');
}

function validateEmailAddress(obj,id)
{
	var email = $.trim($(obj).val());
    var parent = $(obj).closest('.frm_grp'); // or $(obj).parent()
	$(parent).find('img[use=processingIcon]').show();
	$(parent).find('span').hide();
	setTimeout( function(){		
		$.ajax({
			type: "POST",
			url: jsWemaster_BASE_URL+'manage_participant.process.php',
			data:'act=getEmailValidationStatus&email='+email+'&id='+id,
			dataType: "json",
			async: true,
			success: function(jsonObject){						
				if(jsonObject.STATUS == 'IN_USE')
				{
					$(parent).find('span[use=Duplicate]').show();
					$(parent).find('input[name=emailValidate]').val('N');
				}
				else if(jsonObject.STATUS == 'AVAILABLE')
				{
					$(parent).find('span[use=Registered]').show();
					$(parent).find('input[name=emailValidate]').val('Y');
						$('#participantMobile').val(jsonObject.MOBILE_NO);
						$('#participantFirstName').val(jsonObject.FIRST_NAME);
						$('#participantMiddleName').val(jsonObject.MIDDLE_NAME);
						$('#participantLastName').val(jsonObject.LAST_NAME);
						let title = jsonObject.TITLE;

						if (title) {
							title = title.charAt(0).toUpperCase() + title.slice(1).toLowerCase();
						}

						$('#participantTitle').val(title);	
					// $(parent).find('span[use=Duplicate]').show();
					// $(parent).find('input[name=emailValidate]').val('N');
				}
				else if(jsonObject.STATUS == 'NOT_FOUND')
				{
					$(parent).find('span[use=unRegistered]').show();
					$(parent).find('input[name=emailValidate]').val('Y');
				}				
				$(parent).find('img[use=processingIcon]').hide();
			}
	  });
	},500);
}

function validateAlternativeEmailAddress(obj,id)
{
	var email = $.trim($(obj).val());
    var parent = $(obj).closest('.frm_grp'); // or $(obj).parent()
	$(parent).find('img[use=processingIcon]').show();
	$(parent).find('span').hide();
	setTimeout( function(){		
		$.ajax({
			type: "POST",
			url: "manage_participant.process.php",
			data:'act=getEmailValidationStatus&email='+email+'&id='+id,
			dataType: "json",
			async: true,
			success: function(jsonObject){						
				if(jsonObject.STATUS == 'IN_USE')
				{
					$(parent).find('span[use=Duplicate]').show();
					$(parent).find('input[name=participantAlternativeEmail]').val('');
				}
				else if(jsonObject.STATUS == 'AVAILABLE')
				{
					$(parent).find('span[use=Registered]').show();
					// $(parent).find('span[use=Duplicate]').show();
					$(parent).find('input[name=participantAlternativeEmail]').val('');
				}
				else if(jsonObject.STATUS == 'NOT_FOUND')
				{
					$(parent).find('span[use=unRegistered]').show();
					// $(parent).find('input[name=participantAlternativeEmail]').val('');
				}				
				$(parent).find('img[use=processingIcon]').hide();
			}
	  });
	},500);
}


function validateFacultyMobileNumber(obj,id)
{
	var mobile = $.trim($(obj).val());
    var parent = $(obj).closest('.frm_grp'); // or $(obj).parent()
	$(parent).find('img[use=processingIcon]').show();
	$(parent).find('span').hide();
	setTimeout( function(){		
		$.ajax({
			type: "POST",
			url: "manage_participant.process.php",
			data:'act=getMobileValidationStatus&mobile='+mobile+'&id='+id,
			dataType: "json",
			async: true,
			success: function(jsonObject){						
				if(jsonObject.STATUS == 'IN_USE')
				{
					$(parent).find('span[use=Duplicate]').show();
					$(parent).find('input[name=mobileValidate]').val('N');
				}
				else if(jsonObject.STATUS == 'AVAILABLE')
				{
					$(parent).find('span[use=Registered]').show();
					$(parent).find('input[name=mobileValidate]').val('Y');
					    // $('#participantEmail').val(jsonObject.EMAIL_ID);
						$('#participantFirstName').val(jsonObject.FIRST_NAME);
						$('#participantMiddleName').val(jsonObject.MIDDLE_NAME);
						$('#participantLastName').val(jsonObject.LAST_NAME);
						let title = jsonObject.TITLE;

						if (title) {
							title = title.charAt(0).toUpperCase() + title.slice(1).toLowerCase();
						}

						$('#participantTitle').val(title);	
					// $(parent).find('span[use=Duplicate]').show();
					// $(parent).find('input[name=emailValidate]').val('N');
				}
				else if(jsonObject.STATUS == 'NOT_FOUND')
				{
					$(parent).find('span[use=unRegistered]').show();
					$(parent).find('input[name=mobileValidate]').val('Y');
				}				
				$(parent).find('img[use=processingIcon]').hide();
			}
	  });
	},500);
}


function manageParticipantValidation()
{
	var accessVerrification = true;
	
	var emailValidate = $("#emailValidate").val();
	var mobileValidate = $("#mobileValidate").val();

	if(emailValidate!='Y')
	{
		alert("Please use some other email.");
		return false;
	}

	if(mobileValidate!='Y')
	{
		alert("Please use some other mobile number.");
		return false;
	}

	return accessVerrification;
}

function manageParticipantValidationEdit()
{
	var accessVerrification = true;
	
	var emailValidate = $("#emailValidateEdit").val();
	var mobileValidate = $("#mobileValidateEdit").val();

	if(emailValidate!='Y')
	{
		alert("Please use some other email.");
		return false;
	}

	if(mobileValidate!='Y')
	{
		alert("Please use some other mobile number.");
		return false;
	}

	return accessVerrification;
}

function deleteDocument(docUniqName,tr,docName)
{
	if(confirm('Do you want to delete '+docName+' ???'))
	{
		$.ajax({
				type: "POST",
				url: 'additional_data.process.php',
				data: 'act=deleteDocument&docUniqName='+docUniqName, //+'&docName='+docName,
				dataType: 'text',
				async: false,
				success: function(JSONObject){
					$('tr[use='+tr+']').remove();
				}
		  });
	}
	else
	{
		return false;
	}
}

/*
function regIdValidation()
{
	var regId = $('#participantRegistrationId').val();
	console.log("http://localhost/icgst/dev/developer/webmaster/section_scientific_program/additional_data.process.php?act=regIdvalidation&regId="+regId);
	$.ajax({
					type: "POST",
					url: 'additional_data.process.php',
					data: 'act=regIdvalidation&regId='+regId,
					dataType: 'json',
					async: false,
					success: function(JSONObject){
						
						$("#regIdStatus").text(JSONObject.MESSAGE);
						$("#registrationIdStatus").val(JSONObject.MESSAGE);
						$("#participantName").val(JSONObject.NAME);
						$('#pername').text(JSONObject.NAME);
						$("#participantIntstitute").val(JSONObject.INSTITUTE);
						$("#participantEmail").val(JSONObject.EMAIL_ID);
						$("#participantMobile").val(JSONObject.MOBILE_NO);
						$("#participantid").val(JSONObject.ID);
						$("#regIdStatus").css('color',JSONObject.COLOR);
						
					}
			  });
}
*/
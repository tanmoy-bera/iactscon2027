/**************************************************************/
/*                  COUNTRY AVAILABILITY ADD                  */
/**************************************************************/
function countryAvailabilityAdd(getVal)
{
	if(getVal!="")
	{
		$.ajax({
					type: "POST",
					url: jsSectionBaseURL+"manage_country.process.php",
					data: 'act=countryAvailability&getVal='+getVal,
					dataType: "text",
					async:false,
					success: function(msg)
					{
						if(msg == 0)
						{
							$("#chk_country_add").val(2);
						}
						else
						{
							$("#chk_country_add").val(1);
						}
					}
				});
	}
	else
	{
		$("#chk_country_add").val(0);
	}
}

/**************************************************************/
/*                  COUNTRY AVAILABILITY EDIT                 */
/**************************************************************/
function countryAvailabilityEdit(getVal)
{
	var countryValOld = $("#country_name_or").val();
	
	if(getVal!="")
	{
		if(getVal==countryValOld)
		{
			$("#chk_country_edit").val(3);
			$("#same_edit").css('display','inline');
			$("#exsists_edit").css('display','none');
			$("#notexsists_edit").css('display','none');
		}
		else
		{
			$.ajax({
					type: "POST",
					url: jsSectionBaseURL+"manage_country.process.php",
					data: 'act=countryAvailability&getVal='+getVal,
					dataType: "text",
					async:false,
					success: function(msg)
					{
						if(msg == 0)
						{
							$("#chk_country_edit").val(2);
						}
						else
						{
							$("#chk_country_edit").val(1);
						}
					}
				});
		}
	}
	else
	{
		$("#chk_country_edit").val(0);
	}
}

/**************************************************************/
/*                        FORM VALIDATOR                      */
/**************************************************************/
function formValidator(formType)
{
	$(document).ready(function(){
		$("input, select, textarea").each(function(){
			$(this).css("border-color","#CCC");
		});
	});
	
	var country_abbreviation = $("#country_abbreviation_"+formType);
	var country_name         = $("#country_name_"+formType);
	
	if(fieldNotEmpty(country_abbreviation,'Please Enter Country Abbreviation')==false){ return false }
	if(fieldNotEmpty(country_name,'Please Enter Country Name')==false){ return false }
}

$(document).ready(function(){
	
	
		
		$("#headerImage").change(function() { 
			
			var fileExtension = ['jpeg', 'jpg', 'png','gif'];

			if($.inArray($('#headerImage').val().split('.').pop().toLowerCase(), fileExtension) == -1 
			   && $('#headerImage').val()!="") 
			{
				alert("Only .jpeg, .jpg, .gif and .png formats are allowed.");
				
				$('#headerImage').val("");
				$('#headerImage').focus();
				
				return false;
			}
			else
			{
				if($('#headerImage').val()!="")
				{
					readURL(this);
				}
				else
				{
					return false;
				}
			}
			
		});


		$("#footerImage").change(function() { 
			
			var fileExtension = ['jpeg', 'jpg', 'png', 'gif'];

			if($.inArray($('#footerImage').val().split('.').pop().toLowerCase(), fileExtension) == -1 
			   && $('#footerImage').val()!="") 
			{
				alert("Only .jpeg, .jpg, .gif and .png formats are allowed.");
				
				$('#footerImage').val("");
				$('#footerImage').focus();
				
				return false;
			}
			else
			{
				if($('#footerImage').val()!="")
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
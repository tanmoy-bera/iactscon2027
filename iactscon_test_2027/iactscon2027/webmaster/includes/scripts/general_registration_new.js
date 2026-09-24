$(document).ready(function(){
	
	$("#user_country").change(function(){
		countryId  = $(this).val();
		
		generateSateListFromCity(countryId);
	});
});

function generateSateListFromCity(countryId)
{
	if(countryId!=""){
		
		$.ajax({
					type: "POST",
					url: "registration.process.php",
					data: "act=generateStateList&countryId="+countryId,
					dataType: "html",
					async: false,
					success: function(JSONObject){
						$("#user_state").html(JSONObject);
						$("#user_state").removeAttr("disabled");
					}
		});
	}else{
		$("#user_state").html('<option value="">-- Select Country First --</option>');
		$("#user_state").attr("disabled","disabled");
	}
}


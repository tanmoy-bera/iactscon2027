$(document).ready(function(){
	
	$("#yes").click(function(){
		$("table[use=notification]").hide();
		$("table[use=loder]").show();
		setTimeout(detailsInsert, 500);		
	});
});

function requiredDataDumpingMethod(additionalString, jsBaseURL, jsSectionBaseURL)
{
	var returnValue;
	console.log(jsSectionBaseURL+'excel.abstract.generator.php?'+additionalString);
	
	$.ajax({
					type: "POST",
					url: jsSectionBaseURL+'excel.abstract.generator.php',
					data: additionalString,
					dataType: 'json',
					async: false,
					cache: false,
					success: function(JSONObject){
					
						var totalDataInserted = JSONObject.TOTAL_DATA_INSERTED;
						var totalDataFound    = JSONObject.TOTAL_DATA_FOUND;
						
						returnValue           = JSONObject.TOTAL_DATA_FOUND;
						
						var displayText       = "";
						
						if(totalDataFound>0){
							
							$("#generate").css("display", "none");
							
							if(totalDataInserted==1){
							
								displayText   = "Please wait, "+totalDataInserted+" record prepared form delegate list.";
							}
							else if(totalDataInserted>1){
							
								displayText   = "Please wait, "+totalDataInserted+" records prepared form delegate list.";
							}
						}
						else{
							
							$("#generate").css("display", "block");
							$("#generate").val("Generate And Download Excel Again");
							displayText       = "Please wait while redirecting to download screen";
						}
						
						$("#displayScreen").text("");
						$("#displayScreen").text(displayText);
					}
		  });
	
	return returnValue;
}

function detailsInsert()
{	
	$("table[use=loder]").show();
	var div = $("div[use=data]");
	console.log('http://localhost/isarcon/dev/developer/webmaster/section_registration/representative_listing.process.php?act=insertData');
	var returnValue = 0;
	$.ajax({
				type: "POST",
				url: 'representative_listing.process.php',
				data: 'act=insertData',
				dataType: 'json',
				async: false,
				cache: false,
				success: function(JSONObject){
					console.log(JSONObject);
					var AVAILABLE      = JSONObject.AVAILABLE;
					var NOT_AVAILABLE  = JSONObject.NOT_AVAILABLE;
					var excel  = JSONObject.EXCEL_NAME;
					
					//display:none;
					var tr = '<div><div style="width: 100%; text-align: left; float: left;" align="center">Suecessfully Inserted: '+AVAILABLE+' record(s).</div><div style="width: 100%; float: left; text-align: left;" align="center">Failed: '+NOT_AVAILABLE+' record(s)       [ Note: Check duplicate email Id and mobile number ]</div></div>';
					$('#excelName').val(excel);
					console.log(tr);
					$(div).after(tr);
					setTimeout(function(){ 
											$("div[use=data]").remove();
											$("table[use=loder]").hide();
											$("table[use=insert]").show();
										}, 4000);
					
				}
		  });
}




function bulkUploadValidation()
{
	if(fieldNotEmpty('#cutoff_id_add', "Please select cutoff") == false){ 		
		
		return false; 
	}
	
	var registration_tariff = $("input[type=radio][operationMode=registration_tariff]:checked").length;
	
	if(registration_tariff < 1 ){ 
		$('input[type=radio][operationMode=registration_tariff]').css('outline-color', '#D41000');
        $('input[type=radio][operationMode=registration_tariff]').css('outline-style', 'solid');
        $('input[type=radio][operationMode=registration_tariff]').css('outline-width', 'thin');
		$("html, body").animate({ scrollTop: 50 }, 1000);
		cssAlert('tr[use=registration_tariff]','Please Select Registration Classification');			
		
		return false;
	}	
	
	return true;
	
}
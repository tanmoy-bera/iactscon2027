$(document).ready(function(){
	
	
	$("input[type=checkbox][operationmode=dinner_value]").each(function(){
				$(this).click(function(){
					if($(this).is(":checked")){
						$(this).prop("checked",true);
						calculateTotalAmountNew();
					}
					else
					{ 
						calculateTotalAmountNew();
						$(this).prop("checked",false);
					}
				});
	});
	$("input[type=radio][operationMode=registration_type]").click(function(){
		calculateTotalAmountNew();																   
	});
	$("input[type=checkbox][operationmode=discountCheckbox]").each(function(){
		$(this).click(function(){
			if($(this).is(":checked")){
				$(this).prop("checked",true);
				calculateTotalAmountNew();
			}
			else
			{ 
				calculateTotalAmountNew();
				$(this).prop("checked",false);
			}
		});
	});
	
	$("input[type=text][operationMode=discountAmount]").keyup(function(){
		var serviceType = $('input[type=hidden][id=type]').attr('value');		
		
		if(serviceType == 'Dinner')
		{
			calculateTotalAmountNew();
		}
	});

	
});


function validateDinnerFrom(form)
{	
	var sessionStatus = false;
	
	$("input[type=checkbox][operationMode=dinner_value]").each(function(){																			   
		if($(this).is(":checked"))
		{
			sessionStatus = true;
		}																			   
	});
	

	
	if(sessionStatus == false)
	{
		alert("Please select dinner");
		return false;
	}
	else
	{
		var discount = $("input[type=checkbox][operationMode=discountCheckbox]:checked").length;
		if(discount>0)
		{		
			if(fieldNotEmpty('#discountAmount', "Please Enter discount amount") == false){ 	
				return false;
			}	
			
			var discountAmount = $("input[type=text][operationMode=discountAmount]");		
			if(isNaN((discountAmount).val()))
			{
				alert('Enter Discount Amount correctly');
				$(discountAmount).focus();
				sessionStatus = false;
				return false;
			}
			
			var total = parseFloat($("#amount").text());	
			if(total <  parseFloat((discountAmount).val()))
			{
				alert('Enter Discount Amount correctly');
				$(discountAmount).focus();
				sessionStatus = false;
				return false;
			}
		}
	}
	return sessionStatus;;
}

function calculateTotalAmountNew()
{
	console.log("calling calculateTotalAmountNew");
	var cutOffId	= $("#cutoff_id_add").val();
	console.log(cutOffId);
	$("#amount2").text("");
	$("#amount2").text("0.00");
	if(cutOffId=="")
	{
		$("input[type=checkbox][operationmode=dinner_value]").prop("checked",false);
		//cssAlert("#cutoff_id_add","Please Select Cutoff First");
		alert("Please Select Cutoff First");
		$("#cutoff_id_add").css('border-color','#822169');	
	}
	else
	{ 	
		var regType = $("input[type=radio][operationMode=registration_type]:checked").val();
		
		if(regType=='GENERAL')
		{ 
			var totalSum        = 0;
			$("input[type=checkbox][operationmode=dinner_value]").each(function(){	
				if($(this).is(":checked")){					
					if($(this).val().trim()!=""){	
						var parent = $(this).parent().closest("tr");
						var dinnerAmmount = $(parent).find("td[use=dinnerRate][cutoff='"+cutOffId+"']").attr("rate");		
						console.log(dinnerAmmount);
						totalSum    = parseFloat(totalSum) + parseFloat(dinnerAmmount);
					}
				}				
			});
			
			console.log('length>>'+$("input[type=checkbox][operationMode=dinner_choice]").length);
			
			$("input[type=checkbox][operationMode=dinner_choice]").each(function(){	
				console.log($(this).attr('operationMode'));
				if($(this).is(":checked")){	
					console.log('CHECKED');
						
					//var parent = $(this).parent().closest("tr");						
					var dinnerAmmount =  $("input[use=storedTariffAmount][cutoffId='"+cutOffId+"']").val();
					//var dinnerAmmount = $(parent).find("td[use=dinnerRate][cutoff='"+cutOffId+"']").attr("rate");		
					console.log(dinnerAmmount);
					totalSum    = parseFloat(totalSum) + parseFloat(dinnerAmmount);
					
				}				
			});
			
			console.log(totalSum);
			
			var discountAmount = 0;
			
			var discount = $("input[type=checkbox][operationMode=discountCheckbox]:checked").length;
			if(!isNaN(discount) && discount>0)
			{		
				var discountAmount = $("input[type=text][operationMode=discountAmount]").val();
				if(isNaN(discountAmount) || $.trim(discountAmount) == '')
				{
					discountAmount = 0;
				}
			}
			else
			{
					discountAmount = 0;
			}
			
			console.log(discountAmount);
			
			totalSum    = parseFloat(totalSum) - parseFloat(discountAmount);
			
			$("#amount2").text("");
			$("#amount2").text(totalSum.toFixed(2));
			
			console.log(totalSum);
			
			$("#amount").text("");
			$("#amount").text(totalSum.toFixed(2));
			//$("#amount").attr('BasicAmount',totalBasicSum);
		}
		if(regType=='ZERO_VALUE')
		{
			$("#amount2").text("");
			$("#amount2").text("0.00");
			
			$("#amount").text("");
			$("#amount").text("0.00");
		}
	}
}


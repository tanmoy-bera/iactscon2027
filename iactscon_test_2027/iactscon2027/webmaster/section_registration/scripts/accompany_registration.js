$(document).ready(function(){
	try
	{
		addMoreAccompany('addMoreAccompany_placeholder_add','addMoreAccompany_template');
	}
	catch(e)
	{
	
	}
	$('a[operationMode="removeRow"]').first().css('display','none');
	//calculateAccompanyAmount();
	var regType = $("input[type=radio][operationMode=registration_type]:checked").val();
	if(regType=='GENERAL')
	{
		$("div[operationMode=paymentTermsSetUnit]").show();
	}
	if(regType=='ZERO_VALUE')
	{
		$("div[operationMode=paymentTermsSetUnit]").hide();
	}
	
	$("input[type=text][operationMode=discountAmount]").keyup(function(){
		var serviceType = $('input[type=hidden][id=type]').attr('value');		
		//alert(serviceType);
		if(serviceType == 'Accompny')
		{
			calculateAccompanyAmount();
		}														
	});
    
    $("input[type=checkbox][groupName='dinner_value[]']").click(function(){
		calculateAccompanyAmount();								
	});
	
});


/************************************************************************/
/*                          ADD MORE TEMPLATE                           */
/************************************************************************/
function addMoreTemplate(placeholderDiv, templateDiv, maxCount)
{
	var totalTables     = parseInt($('#'+placeholderDiv+' div[operationMode=accompanyPerson]').length);
	var nextCounter     = totalTables + 1;
	var getTemplate     = $("#"+templateDiv).clone();
	var modifiedTemp    = getTemplate.html().replace(/\#COUNTER/g,nextCounter);
	
	$('#'+placeholderDiv).append(modifiedTemp);
	
	removeTableRow();
	
	var maxCounter = 4;
	if(!(typeof maxCount === 'undefined' || maxCount === null)){
		maxCounter = 4-parseInt(maxCount);
	}
	
	if(nextCounter>=maxCounter)
	{
		$('a[operationMode=addAccompanyButton]').css("display","none");
	}
	$("input[operationMode=accompany_name], input[operationMode=accompany_age], input[operationMode=accompany_relationship]").blur(function(){
		
		calculateAccompanyAmount();
	});
	
	$("input[operationMode=accompany_name], input[operationMode=accompany_age], input[operationMode=accompany_relationship]").keyup(function(){		
		calculateAccompanyAmount();
	});
	
	//calculateAccompanyAmount();
	
}

/************************************************************************/
/*                           ADD MORE ACCOMPANY                         */
/************************************************************************/
function addMoreAccompany(placeholderDiv, templateDiv, maxCount)
{
	//alert("OKKKKKK");
	addMoreTemplate(placeholderDiv, templateDiv, maxCount);
	calculateAccompanyAmount();
}

/************************************************************************/
/*                           REMOVE TABLE ROW                           */
/************************************************************************/
function removeTableRow()
{
	$("a[operationMode=removeRow]").click(function(){
		
		var sequenceBy = $(this).attr("sequenceBy");
		
		$("div[operationMode=accompanyPerson][sequenceBy="+sequenceBy+"]").remove();
		
		var totalTables  = parseInt($('#addMoreAccompany_placeholder_add table').length);	
		var nextCounter  = totalTables + 1;
		calculateAccompanyAmount();
		if(nextCounter<5)
		{
			$('a[operationMode=addAccompanyButton]').css("display","block");
		}
		
		
	});
}

function accompanyFromValidatior(form)
{
	//var form = $('#frmApplyForAccompany');
	
	if(fieldNotEmpty('#cutoff_id_add', "Please Chose Cutoff") == false){ 
		
		status = false;
		return false;
	}
	
	var accompanyList =  $(form).find("#addMoreAccompany_placeholder_add").find('div[operationmode=accompanyPerson]');
	
	
	var isAllAccompanyValidated = true;
	var status = true;
	
	$.each($(accompanyList), function(){
		var accompany_name = $(this).find("input[type=text][operationMode=accompany_name]");
		
		if($(accompany_name).val()=='')
		{
			//alert('Enter Accompany Name.....');
			alert('Enter Accompany Name.....');
			isAllAccompanyValidated = false;
			status = false;
			return false;
		}
	});
	
	/*****************************************ACCOMPANY DISCOUNT VALIDATION********************************************/	
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
			status = false;
			return false;
		}
		
		var total = parseFloat($("#amount").attr('BasicAmount'));	
		
		if(total <=  parseFloat((discountAmount).val()))
		{
			alert('Enter Discount Amount correctly');
			$(discountAmount).focus();
			status = false;
			return false;
		}
	}	
	
	if(!isAllAccompanyValidated){
		status = false;
		return false;
	}
	if(!validatePaymentTermsSubmission(form)){
		status = false;
		return false;
	}
	
	return status;
}
//var dinamt = {dinnerAmount:0 }    

function calculateAccompanyAmountOld()
{
	var totalSum        = 0;
	
	//$("#amount").text("0.00");
	$("input[operationMode=accompany_name]").each(function(){		
		if($(this).val().trim()!=""){
			if($("input[name=type]").val()== 'Accompny')
			{
				var cutoffId			  = $("#cutoff_id_add").val();
				var accompanyTariffAmount = $("#accompanyTariffAmount"+cutoffId).val();
				if(isNaN(accompanyTariffAmount))
				{
					accompanyTariffAmount = 0;
				}
			}
			else
			{
				var accompanyTariffAmount = $("#accompanyTariffAmount").val();
			}			
			
			var tot = parseFloat($("#amount").text())
						
			$(this).parent().closest('table').find("input[type=checkbox][operationMode=dinner]").each(function(){
				if($(this).is(":checked")){
					var dinnerAmount   = 3000;						
					var tot = parseFloat($("#amount").text())
					var totalSum    = parseFloat(tot) + parseFloat(dinnerAmount);
					$("#amount").text("");
					$("#amount").text(totalSum.toFixed(2));	
					alert(totalSum);
				}
			});	
		}
	});
	
	var discount = $("input[type=checkbox][operationMode=discountCheckbox]:checked").length;
	
	if(discount > 0)
	{
		var amount = $("input[type=text][operationMode=discountAmount]").val();
		//alert(amount);
		if(isNaN(amount) || (amount == ''))
		{
			amount = 0;
		}
		if(totalSum < parseFloat(amount))
		{
			$("input[type=text][operationMode=discountAmount]").val('');
			if(fieldNotEmpty('#discountAmount', "Discount must be less than Total amount") == false){ 
			}
		}	
		else
		{
			totalSum -= parseFloat(amount);
		}
	}
	
}

function calculateAccompanyAmount()
{
	console.log("calling calculateAccompanyAmount @ sec_reg\accopany_registration.js");
	
	var totalSum        = 0;
	var totalBasicSum   = 0;
	
	$("input[operationMode=accompany_name]").each(function(){
		if($(this).val().trim()!=""){
            var parent 	  = $(this).parent().closest("form");
            var tarriffAmount = 0;	
			var tarrifBasicAmount =0;
			console.log($("#accompanyTariffAmount").length);
			if($("#accompanyTariffAmount").length>0)
			{
				tarriffAmount = parseFloat($("#accompanyTariffAmount").val()); 
				tarrifBasicAmount = parseFloat($("#accompanyTariffAmount").attr('BasicAmount')); 
			}
			else
			{
				var cutOffObj = $(parent).find("select[name=cutoff_id_add]");
				var cutoff	  = $(cutoff_id_add).val();				
				tarriffAmount = parseFloat($("#accompanyTariffAmount"+cutoff).val());
				tarrifBasicAmount = parseFloat($("#accompanyTariffAmount"+cutoff).attr('BasicAmount')); 
			}
             console.log("#1>>"+totalSum);
            if($(parent).find("input[type=checkbox][groupName='dinner_value[]']").is(":checked"))
            {
               var dinnerObj =  $(parent).find("input[type=checkbox][groupName='dinner_value[]']:checked");
               //var dinnerAmount = parseFloat($(dinnerObj).attr("ammount"));
			   var dinnerAmount = parseFloat($("#dinnerTariffAmount").val())
               totalSum    = parseFloat(totalSum) + dinnerAmount;
            }
            console.log("#2>>"+totalSum);
			
			totalSum    = parseFloat(totalSum) + tarriffAmount;
			totalBasicSum = parseFloat(totalBasicSum) + tarrifBasicAmount;
		}
	});
	
	 console.log("#3>>"+totalSum);

	var discount = $("input[type=checkbox][operationMode=discountCheckbox]:checked").length;
	if(!isNaN(discount) && discount>0)
	{		
		var discountAmount = $("input[type=text][operationMode=discountAmount]").val();
	}
	else
	{
			discountAmount = 0;
	}
	totalSum    = parseFloat(totalSum) - parseFloat(discountAmount);
	
	console.log("#4>>"+totalSum);
	
	$("#amount").text("");
	$("#amount").text(totalSum.toFixed(2));
	$("#amount").attr('BasicAmount',totalBasicSum);
	//$("#amount").text("");
	//$("#amount").text(totalSum.toFixed(2));
}

function setAccompanyTariff(event)
{
	var selectElement = event.target;
   var cutoffId = selectElement.value;

   if(cutoffId!=='')
   {

   	$("input[operationMode=accompany_name]").each(function(){		
		 $(this).val("");
	});
   	$('#amount').text("0.00");
   	$.ajax({
					  type: "POST",
                 url: 'registration.process.php',
              
						data: {act:'getAccompanyCutoffAmnt',cutoffId:cutoffId},
                
                 async: false,
						success: function(data){
						$('#accompanyTariffAmount').val(data);
					}
				});
   }
    
}
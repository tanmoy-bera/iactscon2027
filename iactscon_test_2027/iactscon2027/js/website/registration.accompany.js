
$(document).ready(function(){
	try
	{
		addMoreTemplate('addMoreAccompany_placeholder','addMoreAccompany_template');
	}
	catch(e){}
	$("input[type=checkbox][groupName=accompany_food_choice]").each(function(){
		$(this).click(function(){	
			$("input[type=checkbox][groupName=accompany_food_choice]").prop("checked",false);
			if($(this).prop("checked")=="true")
			{
				$(this).prop("checked",true);
			}
			else
			{
				$(this).prop("checked",true);
							
			}
		});
	});
	
});

function formAccompany()
{
	var form = $('#accmForm');
	
	var accompanyList =  $(form).find("#addMoreAccompany_placeholder").find('div[operationmode=accompanyPerson]');
	
	
	var isAllAccompanyValidated = true;
	var status = true;
	$.each($(accompanyList), function(){
		
		var accompany_name = $(this).find("input[type=text][operationMode=accompany_name]");
		if($(accompany_name).val()=='')
		{
			alert("Enter Accompanying Person Name");
			//cssAlert(accompany_name,'Enter Accompanying Person Name');
			isAllAccompanyValidated = false;
			status = false;
			return false;
		}
		
		var accompany_food_choice = $(this).find("input[type=radio][groupName=accompany_food_choice]:checked");
	});
	if(!isAllAccompanyValidated){
		status = false;
		return false;
	}
	
	return status;
}
/**********************************************************************/
/*                         ADD MORE TEMPLATE                          */
/**********************************************************************/
function addMoreTemplate(placeholderDiv, templateDiv,addAccompanyNumber=4)
{	
	var totalTables  = parseInt($('#'+placeholderDiv+' div[operationMode=accompanyPerson]').length);
	var clasfId 	 = 2;
	var nextCounter  = totalTables + 1;
	var getTemplate  = $("#"+templateDiv).clone();
	var modifiedTemp = getTemplate.html().replace(/\#COUNTER/g,nextCounter);
	$('#'+placeholderDiv).append(modifiedTemp);
	removeTableRow();
	var maxLimit = addAccompanyNumber;	//addAccompanyNumber;
	
	//alert(maxLimit);
	if(nextCounter>=maxLimit)
	{
		$('a[operationMode=addAccompanyButton]').css("display","none");
	}
	else
	{
		$('a[operationMode=addAccompanyButton]').css("display","block");
	}
	$('a[operationMode=removeRow][sequenceBy=1]').css("display","none");
	$('div[class=dvdr][sequenceBy=1]').css("width","93%");
	
	$("input[operationMode=accompany_name]").keyup(function(){
		
		calculateTotalAmount();
	});
	
	$("input[type=checkbox][operationMode=dinner_choice]").click(function(){
		//$("input[type=radio][operationMode=dinner_choice]").prop("checked",false);
		if($(this).is(':checked'))
		{
			calculateTotalAmount();
		}
		else
		{
			calculateTotalAmount();
		}
	});
}

/**********************************************************************/
/*                         ADD MORE ACCOMPANY                         */
/**********************************************************************/
function addMoreAccompany(placeholderDiv, templateDiv,addAccompanyNumber)
{	
	addMoreTemplate(placeholderDiv, templateDiv,addAccompanyNumber);
	//calculateTotalAmount();
}

/**********************************************************************/
/*                               REMOVE ROW                           */
/**********************************************************************/
function removeTableRow()
{
	$("a[operationMode=removeRow]").each(function(){
		$(this).click(function(){
			
			var sequenceBy = $(this).attr("sequenceBy");
			$("div[operationMode=accompanyPerson][sequenceBy="+sequenceBy+"]").remove();
			calculateTotalAmount();
			
			var totalTables  = parseInt($('#addMoreAccompany_placeholder div[operationMode=accompanyPerson]').length);	
			var nextCounter  = totalTables + 1;
			if(nextCounter<4)
			{
				$('a[operationMode=addAccompanyButton]').css("display","inline");
			}
		});
	});
}

/**********************************************************************/
/*                         Choose Dinner Option                       */
/**********************************************************************/
function dinnerChoice(obj)
{
	var parent = $(obj).parent().closest('tr');
	if($(obj).val() == 'YES' )
	{
		$(parent).find("input[type=checkbox][operationMode=dinner]").prop('checked', true);
	}
	else
	{
		$(parent).find("input[type=checkbox][operationMode=dinner]").prop('checked', false);
	}
	calculateTotalAmount();
}

/**********************************************************************/
/*                        CALCULATE TOTAL AMOUNT                      */
/**********************************************************************/
function calculateTotalAmount()
{
	var currency  			   = $("input[type=hidden][name=accompanyClasfId]").attr('currency');
	var totalSum        = 0;
	$("input[operationMode=accompany_name]").each(function(){
		if($(this).val().trim()!=""){
			console.log($("#accompanyTariffAmount").val());
			totalSum    = parseFloat(totalSum) + parseFloat($("#accompanyTariffAmount").val());
		}
	});
	if(totalSum)
	{
		$("span[id=registrationCurrency]").text(currency);
		$("span[id=ammount]").text(totalSum.toFixed(2));
	}
	else
	{
		$("span[id=registrationCurrency]").text(currency);
		$("span[id=ammount]").text('0.00');
	}
	$("input[type=checkbox][operationMode=dinner_choice]").each(function(){
		if($(this).is(":checked")){
			var dinnerTd = $(this).val();
			var dinner = $(this).attr('ammount');
		}
		else
		{
			var dinner = 0;
		}
		var tot = parseFloat($("span[id=ammount]").text())
		totalSum    = parseFloat(tot) + parseFloat(dinner);
		$("span[id=registrationCurrency]").text(currency);
		$("span[id=ammount]").text(totalSum.toFixed(2));
	});	
	
}

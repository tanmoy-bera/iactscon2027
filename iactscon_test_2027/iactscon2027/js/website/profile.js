
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
/*                         ADD MORE TEMPLATE                          */
/**********************************************************************/
function addMoreTemplate(placeholderDiv, templateDiv,noAccompany)
{
	
	var totalTables  = parseInt($('#'+placeholderDiv+' div[operationMode=accompanyPerson]').length);
	var clasfId 	 = $('#delegateClasfId').val();
	var nextCounter  = totalTables + 1;
	var getTemplate  = $("#"+templateDiv).clone();
	var modifiedTemp = getTemplate.html().replace(/\#COUNTER/g,nextCounter);;
	$('#'+placeholderDiv).append(modifiedTemp);
	removeTableRow();
	var maxLimit = noAccompany;
	if(nextCounter>=maxLimit)
	{
		$('a[operationMode=addAccompanyButton]').css("display","none");
	}
	$('a[operationMode=removeRow][sequenceBy=1]').css("display","none");
	$('div[class=dvdr][sequenceBy=1]').css("width","93%");
	
	$("input[operationMode=accompany_name], input[operationMode=accompany_age], input[operationMode=accompany_relationship]").blur(function(){
		
		calculateTotalAmount();
	});
	
	$("input[operationMode=accompany_name], input[operationMode=accompany_age], input[operationMode=accompany_relationship]").keyup(function(){
		
		calculateTotalAmount();
	});
}

/**********************************************************************/
/*                         ADD MORE ACCOMPANY                         */
/**********************************************************************/
function addMoreAccompany(placeholderDiv, templateDiv,noAccompany)
{
	addMoreTemplate(placeholderDiv, templateDiv,noAccompany);
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
			if(nextCounter<5)
			{
				$('a[operationMode=addAccompanyButton]').css("display","inline");
			}
			//alert(nextCounter);
			
			$('a[operationMode=removeRow][sequenceBy=1]').css("display","none");
			$('div[class=dvdr][sequenceBy=1]').css("width","93%");
			
			
		});
	});
}

function calculateTotalAmount()
{
	var totalSum        = 0;
	
	$("input[operationMode=accompany_name]").each(function(){
		
		if($(this).val().trim()!=""){
			console.log($("#accompanyTariffAmount").val());
			totalSum    = parseFloat(totalSum) + parseFloat($("#accompanyTariffAmount").val());
			
		}
	});
	
	$("#amount").text("");
	$("#amount").text(totalSum.toFixed(2));
}

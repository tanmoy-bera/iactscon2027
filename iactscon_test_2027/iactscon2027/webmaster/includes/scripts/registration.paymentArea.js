$(document).ready(function(){
	try
	{
		addMoreTemplate('addMorePayment_placeholder','addMorePayment_template');
	}
	catch(e){}
});

/**********************************************************************/
/*                         ADD MORE TEMPLATE                          */
/**********************************************************************/
function addMoreTemplate(placeholderDiv, templateDiv,addAccompanyNumber=10)
{		
	var totalTables  = parseInt($('#'+placeholderDiv+' div[operationMode=paymentRow]').length);
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
		$('a[operationMode=addPaymentButton]').css("display","none");
	}
	else
	{
		$('a[operationMode=addPaymentButton]').css("display","block");
	}
	$('a[operationMode=removePaymentRow]').first().css("display","none");
	$('div[class=dvdr][sequenceBy=1]').css("width","93%");
	
}

/**********************************************************************/
/*                         ADD MORE ACCOMPANY                         */
/**********************************************************************/
function addMorePayment(placeholderDiv, templateDiv,addAccompanyNumber)
{	
	addMoreTemplate(placeholderDiv, templateDiv,addAccompanyNumber);
}

/**********************************************************************/
/*                               REMOVE ROW                           */
/**********************************************************************/
function removeTableRow()
{
	$("a[operationMode=removePaymentRow]").each(function(){
		$(this).click(function(){
			
			var sequenceBy = $(this).attr("sequenceBy");
			$("div[operationMode=paymentRow][sequenceBy="+sequenceBy+"]").remove();
			
			var totalTables  = parseInt($('#addMorePayment_placeholder div[operationMode=paymentRow]').length);	
			var nextCounter  = totalTables + 1;
			if(nextCounter<4)
			{
				$('a[operationMode=addPaymentButton]').css("display","inline");
			}
		});
	});
}

function multiPaymentValidation(form)
{
	try
	{
		var isAllPaymentValidated = true;
		var status = true;
		
		var paymentList =  $(form).find("#addMorePayment_placeholder").find('table[use=thePaymentTable]');
		
		
		$.each($(paymentList),function(){
			var table = $(this);
			var amount = $(table).find("input[type=number][operationMode=amount]");
			if($(amount).val()=='')
			{
				$(amount).focus();
				alert('Enter Amount.....');
				isAllPaymentValidated = false;
				status = false;
				return false;
			}
			
			var paymode = $(table).find("select[use=payment_mode]");
			
			if($(paymode).val()=='Cash')
			{ 
				var cash_deposit_date = $(table).find("input[type=text][use=cash_deposit_date]");
				if($(cash_deposit_date).val()=='')
				{
					$(cash_deposit_date).focus();
					alert('Select Cash Deposit Date');
					isAllPaymentValidated = false;
					status = false;
					return false;
				}
			} 
			else if($(paymode).val()=='Cheque')
			{
				var cheque_number = $(table).find("input[type=number][use=cheque_number]");
				if($(cheque_number).val()=='')
				{
					$(cheque_number).focus();
					alert('Enter Cheque No. Only Numeric Value');
					isAllPaymentValidated = false;
					status = false;
					return false;
				}
				
				var cheque_drawn_bank = $(table).find("input[type=text][use=cheque_drawn_bank]");
				if($(cheque_drawn_bank).val()=='')
				{
					$(cheque_drawn_bank).focus();
					alert('Enter Drawee Bank');
					isAllPaymentValidated = false;
					status = false;
					return false;
				}
				
				var cheque_date = $(table).find("input[type=text][use=cheque_date]");
				if($(cheque_date).val()=='')
				{
					$(cheque_date).focus();
					alert('Enter Cheque Date');
					isAllPaymentValidated = false;
					status = false;
					return false;
				}
			}
			else if($(paymode).val()=='Draft')
			{
				var draft_number = $(table).find("input[type=text][use=draft_number]");
				if($(draft_number).val()=='')
				{
					$(draft_number).focus();
					alert('Enter Draft No');
					isAllPaymentValidated = false;
					status = false;
					return false;
				}
				
				var draft_drawn_bank = $(table).find("input[type=text][use=draft_drawn_bank]");
				if($(draft_drawn_bank).val()=='')
				{
					$(draft_drawn_bank).focus();
					alert('Enter Drawee Bank');
					isAllPaymentValidated = false;
					status = false;
					return false;
				}
				
				var draft_date = $(table).find("input[type=text][use=draft_date]");
				if($(draft_date).val()=='')
				{
					$(draft_date).focus();
					alert('Enter Draft Date');
					isAllPaymentValidated = false;
					status = false;
					return false;
				}
			}
			else if($(paymode).val()=='NEFT')
			{
				var neft_bank_name = $(table).find("input[type=text][use=neft_bank_name]");
				if($(neft_bank_name).val()=='')
				{
					$(neft_bank_name).focus();
					alert('Enter Drawee Bank');
					isAllPaymentValidated = false;
					status = false;
					return false;
				}
				
				var neft_date = $(table).find("input[type=text][use=neft_date]");
				if($(neft_date).val()=='')
				{
					$(neft_date).focus();
					alert('Enter Date');
					isAllPaymentValidated = false;
					status = false;
					return false;
				}
				
				var neft_transaction_no = $(table).find("input[type=text][use=neft_transaction_no]");
				if($(neft_transaction_no).val()=='')
				{
					$(neft_transaction_no).focus();
					alert('Enter Transaction Id');
					isAllPaymentValidated = false;
					status = false;
					return false;
				}
			}
			else if($(paymode).val()=='RTGS')
			{    
				var rtgs_bank_name = $(table).find("input[type=text][use=rtgs_bank_name]");   
				if($(rtgs_bank_name).val()=='')
				{
					$(rtgs_bank_name).focus();
					alert('Enter Drawee Bank');
					isAllPaymentValidated = false;
					status = false;
					return false;
				}
				
				var rtgs_date = $(table).find("input[type=text][use=rtgs_date]");
				if($(rtgs_date).val()=='')
				{
					$(rtgs_date).focus();
					alert('Enter Date');
					isAllPaymentValidated = false;
					status = false;
					return false;
				}
				
				var rtgs_transaction_no = $(table).find("input[type=text][use=rtgs_transaction_no]");
				if($(rtgs_transaction_no).val()=='')
				{
					$(rtgs_transaction_no).focus();
					alert('Enter Transaction Id');
					isAllPaymentValidated = false;
					status = false;
					return false;
				}
			}
			
			else if($(paymode).val()=='CARD')
			{ 
				var card_number = $(table).find("input[type=number][use=card_number]");   //alert($(card_number).val());
				if($(card_number).val()=='')
				{
					$(card_number).focus();
					alert('Enter Card No. Only Numeric Value');
					isAllPaymentValidated = false;
					status = false;
					return false;
				}
				
				var card_date = $(table).find("input[type=text][use=card_date]");
				if($(card_date).val()=='')
				{
					$(card_date).focus();
					alert('Enter Card Date');
					isAllPaymentValidated = false;
					status = false;
					return false;
				}
			}
			
			
		});
		
		
		if(!isAllPaymentValidated){
			status = false;
			return false;
		}
		return status;
	} 
	catch(e)
	{
		console.log(e.message);
	}
}

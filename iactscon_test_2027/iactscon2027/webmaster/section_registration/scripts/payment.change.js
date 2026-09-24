$(document).ready(function(){
	paymentModeChooser();
	$("input[type=radio][operationMode=registrationMode]").change(function(){
		paymentModeChooser();
	});
});

function paymentModeChooser()
{
	var paymentMode = $("input[type=radio][operationMode=registrationMode]:checked").val();
	if(paymentMode=='ONLINE')
	{
		$("tr[use=onlineTr]").show();
		$("tr[use=offlineTr]").hide();
	}
	else if(paymentMode=='OFFLINE')
	{
		$("tr[use=onlineTr]").hide();
		$("tr[use=offlineTr]").show();
	}
}

function fromValidation(ob)
{
	if(validatePaymentTermsSubmission(ob)==true)
	{
		return true;	
	}
	else
	{
		return false;	
	}
}
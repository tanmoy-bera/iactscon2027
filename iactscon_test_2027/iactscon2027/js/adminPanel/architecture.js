/**************************************************************/
/*                  MULTI TASK OPERATIONAL PROCESS            */
/**************************************************************/
function multiTaskValidation(processPage, searchString)
{ 
	var operationMode    = $("#multiOperationSelector").val();
	var operationArray   = new Array();
	var arrayIndex       = 0;
	 
	var act              = "";
	if(operationMode=="")
	{
		alert('Please Select An Operation');
		return false;
	}
	else
	{
		$("input[name=checkvalue]").each(function() { 
			  if($(this).is(':checked')) {
				  var oppValue = $(this).val();
				  operationArray[arrayIndex++] = oppValue;
			  }
		});
		switch(operationMode)
		{
			case'remove': 
			  
				if(confirm('Do you want to remove the selected records')==false) 
				{
					return false;
				}
				break;
		}
		// REDIRECTIONAL OPERATION
		window.location.href = processPage+"?&act="+operationMode+"&id="+operationArray+searchString;
		return true;
	}
}

/**************************************************************/
/*               MULTI TASK SELECT OPERATION PROCESS          */
/**************************************************************/
function checkall()
{
	$("input[name=checkvalue]").each(function() { 
		  if($("#check_all").is(':checked')) {
			  $(this).prop('checked', true);
		  }
		  else {
			  $(this).prop('checked', false);
		  }
	});
}

/**************************************************************/
/*                       OPEN SEARCH FORM                     */
/**************************************************************/
$(document).ready(function(){
	$(".tsearchTool").click(function(){
		$(".tsearch").slideToggle("slow");
	});
});
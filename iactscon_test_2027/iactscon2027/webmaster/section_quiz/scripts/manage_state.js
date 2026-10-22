$(document).ready(function(){
	
	// ADD MORE TEMPLATE PROCESS
	$("a[operationMode=addMoreStateLink]").click(function(){
		addMoreTemplateProcess();
	});
 	
	// ADD FORM VALIDATION PROCESS
	$("#frmInsertState").submit(function(){
		if(addFormValidationProcess()==0)
		{
			return false;
		}
	});
	
	// EDIT FORM VALIDATION PROCESS
	$("#frmUpdateState").submit(function(){
		if(addFormValidationProcess()==0)
		{
			return false;
		}
	});
});

/*********************************************************************/
/*                     ADD MORE TEMPLATE PROCESS                     */
/*********************************************************************/
function addMoreTemplateProcess()
{
	var totalRows      = parseInt($('#addMoreStatePlaceholder tr[forType=parent]').length);
	var nextCounter    = totalRows;
	var getTemplate    = $("#addMoreStateTemplate tbody:first").html();
	var modifiedTemp   = getTemplate.replace(/\#COUNTER/g,nextCounter);
	
	$('#addMoreStatePlaceholder tr[forType=parent]:last').before(modifiedTemp);
}

function addFormValidationProcess()
{
	var accessValidation  = 1;
	
	if(fieldNotEmpty("#country_id_add", "Please Select Country")==false){ 
		
		accessValidation  = 0;
		return false 
	}
	
	return accessValidation;
}

function editFormValidationProcess()
{
	var accessValidation  = 1;
	
	if(fieldNotEmpty("#country_id_edit", "Please Select Country")==false){ 
		
		accessValidation  = 0;
		return false 
	}
	if(fieldNotEmpty("#state_name_edit", "Please Enter State")==false){ 
		
		accessValidation  = 0;
		return false 
	}
	
	return accessValidation;
}
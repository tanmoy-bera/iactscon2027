$(document).ready(function(){

	$("#frmInsert").submit(function(){
		
		var accessValidation = insertFormValidation();
		
		if(accessValidation==1){
			
			return false;
		}
	});
	
	$("#frmUpdate").submit(function(){
		
		var accessValidation = updateFormValidation();
		
		if(accessValidation==1){
			
			return false;
		}
	});
})

function insertFormValidation(){
	
	var accessValidation = 0;
	
	if(fieldNotEmpty('#hotel_name_add', "Please Enter Hotel Name") == false){ 
		
		accessValidation = 1;
	}
	
	return accessValidation;
}

function updateFormValidation(){
	
	var accessValidation = 0;
	
	if(fieldNotEmpty('#hotel_name_update', "Please Enter Hotel Name") == false){ 
		
		accessValidation = 1;
	}
	
	return accessValidation;
}
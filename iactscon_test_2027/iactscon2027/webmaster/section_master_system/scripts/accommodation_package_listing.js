$(document).ready(function(){

	$("#frmInsert").submit(function(){
		
		var accessValidation = insertFormValidation();
		
		if(accessValidation==false){
			
			return false;
		}
	});
	
	$("#frmUpdate").submit(function(){
		
		var accessValidation = updateFormValidation();
		
		if(accessValidation==false){
			
			return false;
		}
	});
})

function insertFormValidation(){
	
	if(fieldNotEmpty('#hotel_id_add', "Please Select Hotel") == false){ 
		
		accessValidation = 1;
		return false;
	}
	if(fieldNotEmpty('#hotel_room_type_add', "Please Enter Package Name") == false){ 
		
		accessValidation = 1;
		return false;
	}
}

function updateFormValidation(){
	
	if(fieldNotEmpty('#hotel_id_update', "Please Select Hotel") == false){ 
		
		accessValidation = 1;
		return false;
	}
	if(fieldNotEmpty('#hotel_room_type_update', "Please Enter Package Name") == false){ 
		
		accessValidation = 1;
		return false;
	}
}
// JavaScript Document
	function after_validation(form){	
		try {				
			var idVal = "";
			if($(form).has("#id").length) idVal = "&id="+$(form).find("#id").val();
			var secNm = "&sectionName="+$(form).find("#sectionName").val();
			var param = "act=isPresent"+secNm+idVal;
			var isUniqueName = false;			
			$.ajax({
				  url: "section.process.php",
				  global: false,
				  type: "POST",
				  data: param,
				  dataType: "json",
				  async:false,
				  success: function(data){
				  	if(data.status=='0'){
						isUniqueName = true;
					} else {
						alert("Section Name already Present in Database");								
						$(form).find("#sectionName").focus();
					}
				  }
			   }
			);
			return isUniqueName;
		} catch (e) {
			printStackTrace(e);
			return false;
		} 
		return true;
	}

 function getSequence(form){
		try {	
			var idVal = "";
			if($(form).has("#id").length) idVal = "&id="+$(form).find("#id").val();
			var param = "act=getSeq"+idVal;
			$.ajax({
					  url: "section.process.php",
					  type: "POST",
					  data: param,
					  dataType: "html",
					  success: function(data){
						$(form).find("#pgSeq").empty();
						$(form).find("#pgSeq").append(data);
					  }
				   }
				);
		} catch (e) {
			printStackTrace(e);
			return false;
		} 
	}	
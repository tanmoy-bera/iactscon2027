// JavaScript Document
	function after_validation(form){	
		try {				
			var idVal = "";
			if($(form).has("#id").length) idVal = "&id="+$(form).find("#id").val();
			var secId = "&sectionId="+$(form).find("#sectionId").val();
			var modNm = "&moduleName="+$(form).find("#moduleName").val();
			var param = "act=isPresent"+modNm+secId+idVal;
			var isUniqueName = false;			
			$.ajax({
				  url: "module.process.php",
				  global: false,
				  type: "POST",
				  data: param,
				  dataType: "json",
				  async:false,
				  success: function(data){
					if(data.status=='0'){
						isUniqueName = true;
					} else {
						alert("Module name already Present in Database");								
						$(form).find("#moduleName").focus();
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
			var secId = "&sectionId="+$(form).find("#sectionId").val();
			var param = "act=getSeq"+secId+idVal;
			$.ajax({
					  url: "module.process.php",
					  type: "POST",
					  data: param,
					  dataType: "html",
					  success: function(data){
						$(form).find("#modSeq").empty();
						$(form).find("#modSeq").append(data);
					  }
				   }
				);
		} catch (e) {
			printStackTrace(e);
			return false;
		} 
	}	
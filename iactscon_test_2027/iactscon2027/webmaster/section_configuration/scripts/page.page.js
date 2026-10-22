// JavaScript Document
	function getModule(form){
		try {	
			var secId = "&sectionId="+$(form).find("#sectionId").val();
			var param = "act=getModule"+secId;
			$.ajax({
					  url: "page.process.php",
					  type: "POST",
					  data: param,
					  dataType: "html",
					  success: function(data){
						$(form).find("#pgMod").empty();
						$(form).find("#pgMod").append(data);
					  }
				   }
				);
		} catch (e) {
			printStackTrace(e);
			return false;
		} 
	}	
	
	function getSequence(form){
		try {	
			var idVal = "";
			if($(form).has("#id").length) idVal = "&id="+$(form).find("#id").val();
			var modId = "&moduleId="+$(form).find("#moduleId").val();
			var param = "act=getSeq"+modId+idVal;
			$.ajax({
					  url: "page.process.php",
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
	
	function after_validation(form){	
		try {				
			var idVal = "";
			if($(form).has("#id").length) idVal = "&id="+$(form).find("#id").val();
			var modId = "&moduleId="+$(form).find("#moduleId").val();
			var pgNm = "&pageName="+$(form).find("#pageName").val();
			var flNm = "&fileName="+$(form).find("#fileName").val();
			var param = "act=isNamePresent"+pgNm+modId+idVal;
			var isUniqueName = false;	
			$.ajax({
				  url: "page.process.php",
				  global: false,
				  type: "POST",
				  data: param,
				  dataType: "json",
				  async:false,
				  success: function(data){
					if(data.status=='0'){
						isUniqueName = true;
					} else {
						alert("Page name already Present in Database");								
						$(form).find("#pageName").focus();
					}
				  }
			   }
			);
			if(!isUniqueName) return false; 
			else isUniqueName = false;		
			param = "act=isFilePresent"+pgNm+modId+idVal;
			$.ajax({
				  url: "page.process.php",
				  global: false,
				  type: "POST",
				  data: param,
				  dataType: "json",
				  async:false,
				  success: function(data){
					if(data.status=='0'){
						isUniqueName = true;
					} else {
						alert("Page name already Present in Database");								
						$(form).find("#fileName").focus();
					}
				  }
			   }
			);
			if(!isUniqueName) return false; 
			else return true;
		} catch (e) {
			printStackTrace(e);
			return false;
		} 
		return true;
	}
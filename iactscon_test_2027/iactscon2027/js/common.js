	function int2Float(fld,format){	
		try{
			var val = trim(fld.value);
			if(val.length==0) {return true;}
			if(isNaN(val)) {return true;}
			
			var valSign = '';
			if( startsWith(val,"+") || startsWith(val,"-") ){
				val = trim( val.substring( 1, val.length ) ); 
				valSign = trim( val.substring( 0, 1 ) ); 
			}	
			
			var paramErr = "Invalid Validation Parameter for int2Float in field "+((fld.id)?fld.id:fld.name);
			var param = trim(format);
			
			var intV = 0;
			var decV = 0;
			
			if(param.length>0){	
				if(param.indexOf(',')<0){
					throw new Error(paramErr);
				}else{
					var params = param.split(",");
					if(params.length!=2){
						throw new Error(paramErr);
					}				
					if(isNaN(params[0])||params[0].indexOf('.')>=0||parseInt(params[0])<1){
						throw new Error(paramErr);
					}else{
						intV = parseInt(params[0]);
					}				
					if(isNaN(params[1])||params[1].indexOf('.')>=0||parseInt(params[1])<1){
						throw new Error(paramErr);
					}else{
						decV = parseInt(params[1]);
					}				
				}
			}
			
			var vals = val.split(".");
			if(vals.length > 2){
				return true;
			}else if(vals.length == 2){
				if(vals[1].length>decV){
					vals[1] =  trim( vals[1].substring( 0, decV ));
				} else {
					for(var i=vals[1].length;  i<decV; i++ ){
						vals[1] += '0';
					}
				}
			}else{
				vals[1] = '0';
				for(var i=1;  i<decV; i++ ){
					vals[1] += '0';
				}
			}
			
			var fVal = valSign+vals[0]+'.'+vals[1];
			fld.value = fVal;
		} catch (e){
			printStackTrace(e);
			return false;
		}
		return true;	
	}
	
	/****************************************************/
	/*                  CLEAR ON ZERO                   */
	/****************************************************/
	function clearOnZero()
	{						   
		$("input[forType=pricing]").each(function() { 
			$(this).focusin(function(){
				if(!isNaN($(this).val()) && parseFloat($(this).val())==0.00)
				{
					$(this).val("");
				}
			});
		});	
	}
	
	/****************************************************/
	/*                   SET TO ZERO                    */
	/****************************************************/
	function zeroOnBlank()
	{
		$("input[forType=pricing]").each(function() {
			$(this).focusout(function(){
				if(isNaN($(this).val()) || $(this).val()==""){
					$(this).val($(this).attr("format"));
				}
			});
		});	
	}
	
	/**************************************************************/
	/*                       REGULAR EXPRESSION                   */
	/**************************************************************/
	var regularExpressionDecimal = /^\d+\.?\d*$/;
	var regularExpressionEmail   = /^([A-Za-z0-9_\-\.])+\@([A-Za-z0-9_\-\.])+\.([A-Za-z]{2,4})$/;
	var regularExpressionInteger = /^[0-9]+$/;
	
	/**************************************************************/
	/*                        FIELD NON EMPTY                     */
	/**************************************************************/
	function fieldNotEmpty(control,message)
	{
		if($(control).length > 0 && $.trim($(control).val())=="")
		{
			var div = '<div class="alertBox" use="alert">'+message+'</div>';
			$(control).focus();
			$(control).css('border-color','#D41000');
			$(control).after(div);
			alert(message);
			setTimeout(function(){$('div[use=alert]').fadeOut(1000);}, 2000);
			setTimeout(function(){$('div[use=alert]').remove();}, 2500);
			
			return false;
		}
		else
		{
			$(control).css('border','');
		}
	}
		
	/**************************************************************/
	/*              FIELD INTEGER VALIDATE FUNCTION               */
	/**************************************************************/
	function fieldShouldIntegerValidate(control,message)
	{
		if(regularExpressionInteger.test($(control).val())==false && $(control).length)
		{
			cssAlert(control,message);
			return false;
		}
		else
		{
			$(control).css('border','');
		}
	} 
	
	/**************************************************************/
	/*              FIELD DECIMAL VALIDATE FUNCTION               */
	/**************************************************************/
	function fieldShouldDecimalValidate(control,message)
	{
		if(regularExpressionDecimal.test($(control).val())==false && $(control).length)
		{
			cssAlert(control,message);
			return false;
		}
		else
		{
			$(control).css('border','');
		}
	} 
	
	/**************************************************************/
	/*               FIELD EMAIL VALIDATE FUNCTION                */
	/**************************************************************/
	function fieldShouldEmailValidate(control,message)
	{
		if(regularExpressionEmail.test($(control).val())==false && $(control).length)
		{
			cssAlert(control,message);
			return false;
		}
		else
		{
			$(control).css('border','');
		}
	}
	
	function cssAlert(control,message)
	{
		id = $(control).attr('id');
		var div = '<div class="alertMainDiv" use="alert"><div class="alertSubDiv"><div style="height:75%; margin-bottom: 30px;">'+message+'</div><div><input type="button" value="OK" class="alertBtn"  onclick="closeAlertDiv(\''+id+'\');" /></div></div></div>';
		setTimeout(function(){$('div[use=alert]').fadeOut(1000);}, 2000);
		$('input').css('border-color','');
		$(control).focus();
		$(control).css('border-color','#D41000');
		$("body").after(div);
		$('input[type=button][class=alertBtn]').focus();		
	}
	
	function closeAlertDiv(id)
	{
		$('div[use=alert]').fadeOut(500);
		$('#'+id).focus();
		setTimeout(function(){$('div[use=alert]').remove();}, 1000);
	}
	
	function countWords(stringValue)
	{
		s = stringValue;
		s = s.replace(/(^\s*)|(\s*$)/gi,"");
		s = s.replace(/[ ]{2,}/gi," ");
		s = s.replace(/\n /,"\n");
		
		return s.split(' ').length;
	}
	
	/**************************************************************/
	/*                       JQUERY CALLENDER                     */
	/**************************************************************/
	function number2Text(s)
	{
		// American Numbering System
		var th     = ['', 'Thousand', 'Million', 'Billion', 'Trillion'];
		var dg     = ['Zero', 'One', 'Two', 'Three', 'Four', 'Five', 'Six', 'Seven', 'Eight', 'Nine'];
		var tn     = ['Ten', 'Eleven', 'Twelve', 'Thirteen', 'Fourteen', 'Fifteen', 'Sixteen', 'Seventeen', 'Eighteen', 'Nineteen'];	
		var tw     = ['Twenty', 'Thirty', 'Forty', 'Fifty', 'Sixty', 'Seventy', 'Eighty', 'Ninety'];
	
		s          = s.toString();
		s          = s.replace(/[\, ]/g, '');
		if (s != parseFloat(s)) 
		{
			return 'not a number';
		}
		
		var x      = s.indexOf('.');
		if(x == -1) 
		{
			x = s.length;
		}
		if(x > 15) 
		{
			return 'too big';
		}
		var n      = s.split('');
		var str    = '';
		var sk     = 0;
		for(var i=0; i<x; i++) 
		{
			if((x-i)%3==2) 
			{
				if(n[i]=='1') 
				{
					str += tn[Number(n[i + 1])] + ' ';
					i++;
					sk   = 1;
				} 
				else if(n[i] != 0) 
				{
					str += tw[n[i] - 2] + ' ';
					sk   = 1;
				}
			} 
			else if(n[i] != 0) 
			{
				str += dg[n[i]] + ' ';
				if ((x - i) % 3 == 0) str += 'Hundred ';
				sk = 1;
			}
			if((x - i) % 3 == 1) 
			{
				if (sk) str += th[(x - i - 1) / 3] + ' ';
				sk = 0;
			}
		}
		
		return str.replace(/\s+/g, ' ');
	}
	
	$(document).ready(function(){
		
		$("input[type=checkbox][groupName]").click(function(){			
			var groupName = $(this).attr("groupName");			
			if(groupName!=""){				
				if($(this).is(":checked")){					
					$("input[type=checkbox][groupName="+groupName+"]").prop("checked",false);	
					$(this).prop("checked",true);
				}
				else
				{					
					$(this).prop("checked",false);
				}
			}
		});	
		
		$("input[isMandatory=Y]").blur(function(){			
			if($(this).val().trim()==""){				
				$("input[isMandatory=Y], select[isMandatory=Y]").css('border-color','#CCC');
				$(this).css('border-color','#D41000');
			}
		});
		
		$("select[isMandatory=Y]").change(function(){			
			if($(this).val().trim()==""){				
				$("input[isMandatory=Y], select[isMandatory=Y]").css('border-color','#CCC');
				$(this).css('border-color','#D41000');
			}
		});
	});
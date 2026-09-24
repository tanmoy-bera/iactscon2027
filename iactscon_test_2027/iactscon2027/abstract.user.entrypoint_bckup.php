<?php
include_once("includes/frontend.init.php"); 
include_once("includes/function.registration.php");
include_once("includes/function.delegate.php");
include_once("includes/function.invoice.php");
include_once("includes/function.workshop.php");
include_once("includes/function.dinner.php");
include_once("includes/function.accompany.php");

?>
<!DOCTYPE html>
<html lang="en">
	<head>
		<meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <link rel="icon" href="image/favicon.png" type="favicon">
		<title>:: Abstract | <?=$cfg['EMAIL_CONF_NAME'];?> ::</title>
<?php
		setTemplateStyleSheet();
		setTemplateBasicJS();
		backButtonOffJS();
?>
		<link rel="stylesheet" type="text/css" href="<?=_DIR_CM_CSS_."website/"?>abstract.user.entrypoint_css.php?background_color=<?=$cfg['background_color']?>" />
		<link rel="stylesheet" type="text/css" href="<?=_DIR_CM_CSS_."website/"?>roboto_css.css" />
		<script src="<?=_BASE_URL_?>js/website/returnData.process.js"></script>
		<!-- <script src="<?=_BASE_URL_?>js/website/abstract_request.js"></script> -->
		
	</head>
<?php
	$loginDetails 	 = login_session_control(false);
	$delegateId 	 = $loginDetails['DELEGATE_ID'];
	
	$operate 		 = false;
	
	if(isset($_REQUEST['TOKEN']) && trim($_REQUEST['TOKEN']) != '')
	{
		$token = unserialize(base64_decode($_REQUEST['TOKEN']));
		if(is_array($token) && sizeof($token) > 0)
		{
			foreach($token as $key=>$val)
			{
				$_REQUEST[$key] = $val;
			}
		}
	}
	
	if($_SESSION['PROCEED_2_ABSTRACT']=='OK')
	{
		$_REQUEST['PROCEED']='OK';
		$_REQUEST['EXPIRY']=$_SESSION['PROCEED_EXPIRY'];
	}


	$sqlHeader 	=	array();
	$sqlHeader['QUERY'] = "SELECT * FROM "._DB_EMAIL_SETTING_." 
											WHERE `status`='A' order by id desc limit 1";
					 //$sql['PARAM'][]	=	array('FILD' => 'status' ,     		 'DATA' => 'A' ,       	           'TYP' => 's');					 
	$resultHeader = $mycms->sql_select($sqlHeader);
	$rowHeader    		 = $resultHeader[0];

	$header_image = _BASE_URL_.$cfg['EMAIL.HEADER.FOOTER.IMAGE'].$rowHeader['logo_image'];
	if($rowHeader['logo_image']!='')
	{
		$emailHeader  = $header_image;
	}
	
?>
	<body>       
		<form name="abstractRequestForm" id="abstractRequestForm" 
			  action="<?=_BASE_URL_?>abstract_request.process.php" method="post" enctype="multipart/form-data"> <!-- onSubmit="return abstractRequestFormValidation(this);" -->
		<input type="hidden" name="act" value="abstractSubmission" />
		<input type="hidden" name="applicantId" id="applicantId" value="<?=$delegateId?>" />
		<input type="hidden" name="report_data" id="report_data" value="Abstract" />		
        <div class="col-xs-4 left-container-box regTarrif_leftPanel" style="position:relative;">
            <div class="col-xs-4 logo">
               <a href="<?=_BASE_URL_?>"> <img src="<?=$emailHeader?>" alt="logo" style="width: 100%;"></a>              
            </div>
            <div class="col-xs-7 col-xs-offset-1 timer" style="padding: 0">
				<div>
                    <h5 class="cutoffNameTop">Submit</h5>
                    <h4 style="color: white" class="cutoffName">ABSTRACT</h4>
                </div>
                <div class="timeLeftWrapper">
					<div style="font-size: 20px;" class="timeLeft">
                        <div class="col-xs-3 timeLeftDays">
                            <p style="margin-bottom: 0; color: white; font-style: italic;"><span id="dday">000</span> <sub style="bottom: 0; font-size: 12px;">DAYS</sub></p>
						</div>
                        <div class="col-xs-3 timeLeftHours">
                            <p style="margin-bottom: 0;color: white; font-style: italic;"><span id="dhour">00</span> <sub style="bottom: 0; font-size: 12px;">HRS.</sub></p>
						</div>
                        <div class="col-xs-3 timeLeftMinutes">
                            <p style="margin-bottom: 0;color: white; font-style: italic;"><span id="dmin">00</span> <sub style="bottom: 0; font-size: 12px;">MIN.</sub></p>
						</div>
                        <div class="col-xs-3 timeLeftSeconds">
                            <p style="margin-bottom: 0;color: white; font-style: italic;"><span id="dsec">00</span> <sub style="bottom: 0; font-size: 12px;">SEC.</sub></p>
						</div>
                    </div>
                </div>
				<?	
				$endDate		  	  = $cfg['ABSTRACT.SUBMIT.LASTDATE'] ;
				$dateArr		   	  = explode("-",$endDate);
				?>
				<script>
				//  Change the items below to create your countdown target date and announcement once the target date and time are reached.  
				var current="";     			 //enter what you want the script to display when the target date and time are reached, limit to 20 characters
				var year= <?=$dateArr[0]?>;      //Enter the count down target date YEAR
				var month= <?=$dateArr[1]?>;     //Enter the count down target date MONTH
				var day= <?=$dateArr[2]?>;       //>Enter the count down target date DAY
				var hour=23;          			 //Enter the count down target date HOUR (24 hour clock)
				var minute=59;        			 //Enter the count down target date MINUTE
				var tz=5.5;            			 //Offset for your timezone in hours from UTC (see http://wwp.greenwichmeantime.com/index.htm to find the timezone offset for your location)
				
				// DO NOT CHANGE THE CODE BELOW! 
				var montharray=new Array("Jan","Feb","Mar","Apr","May","Jun","Jul","Aug","Sep","Oct","Nov","Dec");
				
				function countdown(yr,m,d,hr,min){
					theyear=yr;themonth=m;theday=d;thehour=hr;theminute=min;
					var today=new Date();
					var todayy=today.getYear();
					if (todayy < 1000) { 
						todayy+=1900; 
					}
					var todaym=today.getMonth();
					var todayd=today.getDate();
					var todayh=today.getHours();
					var todaymin=today.getMinutes();
					var todaysec=today.getSeconds();
					var todaystring1=montharray[todaym]+" "+todayd+", "+todayy+" "+todayh+":"+todaymin+":"+todaysec;
					var todaystring=Date.parse(todaystring1)+(tz*1000*60*60);
					var futurestring1=(montharray[m-1]+" "+d+", "+yr+" "+hr+":"+min);
					var futurestring=Date.parse(futurestring1)-(today.getTimezoneOffset()*(1000*60));
					var dd=futurestring-todaystring;
					var dday=Math.floor(dd/(60*60*1000*24)*1);
					var dhour=Math.floor((dd%(60*60*1000*24))/(60*60*1000)*1);
					var dmin=Math.floor(((dd%(60*60*1000*24))%(60*60*1000))/(60*1000)*1);
					var dsec=Math.floor((((dd%(60*60*1000*24))%(60*60*1000))%(60*1000))/1000*1);
					if(dday<=0&&dhour<=0&&dmin<=0&&dsec<=0){
						document.getElementById('dday').style.display="none";
						document.getElementById('dhour').style.display="none";
						document.getElementById('dmin').style.display="none";
						document.getElementById('dsec').style.display="none";
						return;
					}
					else 
					{
						document.getElementById('dday').innerHTML=(dday<10)?('0'+dday):dday;
						document.getElementById('dhour').innerHTML=(dhour<10)?('0'+dhour):dhour;
						document.getElementById('dmin').innerHTML=(dmin<10)?('0'+dmin):dmin;
						document.getElementById('dsec').innerHTML=(dsec<10)?('0'+dsec):dsec;
						setTimeout("countdown(theyear,themonth,theday,thehour,theminute)",1000);
					}
				}
				countdown(year,month,day,hour,minute);
				</script>
            </div>
            
            <div class="col-xs-12 menu" style="padding: 0">
<?		
		//if($cfg['ABSTRACT.SUBMIT.LASTDATE'] >= date('Y-m-d') && ( $_REQUEST['PROCEED']==='OK' && $_REQUEST['EXPIRY']>= date('Y-m-d')) )
		if( $cfg['ABSTRACT.SUBMIT.LASTDATE'] >= date('Y-m-d') )
		{
			
			$_SESSION['PROCEED_2_ABSTRACT'] = 'OK';
			$_SESSION['PROCEED_EXPIRY'] = $_REQUEST['EXPIRY'];
			
			$operate			  = true;
			$resultAbstractType	  = false;
			if($delegateId != '')
			{
				$rowUserDetails   = getUserDetails($delegateId);
				$invoiceList 	  = getConferenceContents($delegateId);	
				$currentCutoffId  = getTariffCutoffId();
				
				$sql  			  = array();
				$sql['QUERY']     = " SELECT * 
										FROM "._DB_ABSTRACT_REQUEST_." 
									   WHERE `status` = ?
										 AND `applicant_id` = ?";
										 //AND `abstract_child_type` IN ('Oral','Poster')
										
				$sql['PARAM'][]   = array('FILD' => 'status',         'DATA' =>'A',          'TYP' => 's');
				$sql['PARAM'][]   = array('FILD' => 'applicant_id',   'DATA' =>$delegateId , 'TYP' => 's');
				$resultAbstractType = $mycms->sql_select($sql);

				$abstractCatArray = array();
				foreach ($resultAbstractType as $key => $cat_val) {
					//echo $cat_val['abstract_cat'];
					array_push($abstractCatArray,trim($cat_val['abstract_cat']));
				}

				//echo '<pre>'; print_r($resultAbstractType);
			}

			//echo count($resultAbstractType);
?>
                <ul use="leftAccordion" class="accordion">
                	<li use='leftAccordionAbstractCategory'>
					 	<div class="link masterClass" use="accordianL1TriggerDiv" useSpec1="stage2" style="font-weight: 600; text-decoration: underline;">SUBMISSION CATEGORY</div>
						<ul class="submenu" style="display:block;">
							<?
							/*if(!$resultAbstractType)
							{*/
							if(count($resultAbstractType)<10)
							{	
								$sqlAbstractTopic			  =	array();
								$sqlAbstractTopic['QUERY']    = "SELECT * FROM "._DB_ABSTRACT_TOPIC_CATEGORY_." 
																  WHERE `status` IN ('A')
															   ORDER BY `category` ASC";
								
								
								$resultAbstractTopic = $mycms->sql_select($sqlAbstractTopic);


								$sqlAbstractSubmission			  =	array();
								$sqlAbstractSubmission['QUERY']    = "SELECT * FROM "._DB_ABSTRACT_SUBMISSION_." 
																  WHERE `status` IN ('A')
															   ORDER BY `category` ASC";
								
								
								$resultAbstractSubmission = $mycms->sql_select($sqlAbstractSubmission);

								$sqlAbstractPresentation			 =	array();
								$sqlAbstractPresentation['QUERY']    = "SELECT * FROM "._DB_ABSTRACT_PRESENTATION_." 
																  WHERE `status` ='A'
															   ORDER BY `id` ASC";
								
									//$sqlAbstractTopic['PARAM'][]  = array('FILD' => 'status', 'DATA' =>'A',  'TYP' => 's');
									$resultAbstractPresentation = $mycms->sql_select($sqlAbstractPresentation);
									//print_r($resultAbstractPresentation);
								//foreach ($cfg['ABSTRACT_SUBMISSION_CATEGORY'] as $key => $value) {

								//print_r($resultAbstractTopic);
								foreach ($resultAbstractTopic as $key => $value) {
										//echo 'exist='.$resultAbstractType[0]['abstract_cat'];
										
										/*if($value['category']==$resultAbstractType[0]['abstract_cat'])
										{*/
										if (in_array(trim($value['id']), $abstractCatArray))
 										 {
 										 	
											$checked ='checked';
											$disabled ='disabled';
										}
										else
										{

											$checked ='';
											$disabled ='';
										}
									?>
									<li use="abstractCategoryRadio">
										<a>
											<label class="container-box menu-container-box">
												<i class="itemTitle"><?=$value['category'];?></i> 
												<input type="radio" name="abstract_category" value="<?=$value['id']?>" 
													   relatedCategoryType="<?=$value?>"
													   onclick="abstractTypeChange(this,'<?=$value['id']?>','<?php echo implode(",", json_decode($value['category_fields'])); ?>')"   required/>									
												<span class="checkmark"></span>
											</label>
										</a>
									</li>
								<?	
								}
							}
							?>
						</ul>					
					</li>
					<li use='leftAccordionSubmissionType' class="abs-submission-type" style="display:none;">
						<div class="link masterClass" use="accordianL1TriggerDiv" useSpec1="stage2" style="font-weight: 600; text-decoration: underline;"></div>
						<ul class="submenu" style="display:block;padding-left: 23px;">
							<?
							/*if(!$resultAbstractType)
							{*/
							if(count($resultAbstractType)<10)
							{	
								foreach ($resultAbstractSubmission as $key => $value) { 
								?>
									<li use="submissionTypeRadio" class="submissionTypeRadio<?=$value['category']?>"  style="display:none;">
										<a>
											<label class="container-box menu-container-box">  
												<i class="itemTitle"><?=$value['abstract_submission']?></i> 	
												<input type="radio" name="abstract_parent_type" value="<?=$value['id']?>" 
													   titleWordCountLimit="<?=$cfg['ABSTRACT.TITLE.WORD.LIMIT']?>" 
													   contentWordCountLimit="<?=$cfg['ABSTRACT.FREE.PAPER.SESSION.WORD.LIMIT']?>"
													   relatedSubmissionSubType="" onclick="abstractSubmissionType(this,'<?php echo $value['category']; ?>','<?php echo $value['id']; ?>')"
													   disabled="disabled" 
													    />	
												<span class="checkmark"></span>
											</label>
										</a>
									</li>
								<?	
								}
							}
							?>
						</ul>					
					</li>
					<li use='leftAccordionSubmissionType' class="abs-sub-submission-type" style="display:none;">
						<div class="link conferenceRegistration" use="accordianL1TriggerDiv" useSpec1="stage1" style="font-weight: 600; text-decoration: underline;"></div>
						<ul class="submenu" style="display:block;padding-left: 50px;">
							<?
							/*if(!$resultAbstractType)
							{*/
							if(count($resultAbstractType)<10)
							{	
								foreach ($resultAbstractPresentation as $key => $value) { 

									//echo '<pre>'; print_r($resultAbstractPresentation);
								?>
									<li use="submissionSubTypeRadio" class="submissionSubTypeRadio_<?=$value['category_id']?>_<?=$value['submission_id']?>" style="display:none;">
										<a>
											<label class="container-box menu-container-box">
												<i class="itemTitle"><?=$value['abstract_presentation']?></i> 
												<input type="radio" name="abstract_child_type" value="<?=$value['id']?>" 
													   relatedSubmissionType="" onclick="submissionTypeFieldsSet(this, '<?=$value['category_id']?>','<?=$value['submission_id']?>')" />									
												<span class="checkmark"></span>
											</label>
										</a>
									</li>
								<?
								}
							}
							?>
						</ul>
					</li>
                </ul>
				
				<script>
					$(document).ready(function(){

						//$("ul[use=rightAccordion]").find("li[use=abstractPresenterDetails] ul").slideUp();
						$.each($("ul[use=leftAccordion]").children("li"), function(i,level1){	
							$(level1).attr("parent","0");						
							$(level1).attr("level","1");
							$(level1).attr("sequence",(i+1));
							$(level1).children("div[use=accordianL1TriggerDiv]").attr("level","1");
							$(level1).children("div[use=accordianL1TriggerDiv]").attr("sequence",(i+1));
							$.each($(level1).children("ul").children("li"), function(j,level2){
								$(level2).attr("parent",(i+1));						
								$(level2).attr("level","2");
								$(level2).attr("sequence",(j+1));								
								$(level2).children("div[use=accordianL2TriggerDiv]").attr("level","2");
								$(level2).children("div[use=accordianL2TriggerDiv]").attr("sequence",(j+1));
								
								$.each($(level2).children("ul").children("li"), function(k,level3){	
									$(level3).attr("parent",(j+1));						
									$(level3).attr("level","3");
									$(level3).attr("sequence",(k+1));									
									$(level3).children("div[use=accordianL3TriggerDiv]").attr("level","3");
									$(level3).children("div[use=accordianL3TriggerDiv]").attr("sequence",(k+1));
								});
							});							
						});	
						$.each($("ul[use=rightAccordion]").children("li"), function(i,level){	
							$(level).attr("sequence",(i+1));							
							$(level).children("div[use=rightAccordianTriggerDiv]").attr("sequence",(i+1));
							$(level).children("ul").attr("displayStatus",'N');
							$(level).children("ul").attr("level",'1');
							$(level).find("button[use=nextButton]").attr("sequence",(i+1));
							$(level).find("button[use=nextButton]").attr("goto",(i+1+1));
							$(level).children("div[use=rightAccordianL1TriggerDiv]").click(function(){	
								var seq = $(this).attr("sequence");
								var divParnt = $(this).parent().closest("li");
								var target = $(level).children("ul");
								if($(target).attr("displayStatus")=='N')
								{
									$("ul[use=rightAccordion]").find("ul[level=1]").slideUp();
									$("ul[use=rightAccordion]").find("ul[level=1]").attr("displayStatus",'N');
									$(level).children("ul").slideDown();
									$(level).children("ul").attr("displayStatus",'Y');
								}
							});

							var sequence = $("ul[use=rightAccordion]").find("ul[level=1]").parent().attr('sequence');
							if(sequence > 1){
								$("ul[use=rightAccordion]").find("ul[level=1]").parent().hide();
							}

							$(level).find("button[use=nextButton]").click(function(){
								var thisSeq = $(this).attr('sequence');
								var nextSeq = $(this).attr('goto');
								

								if(validateOnNextButton(thisSeq))
								{
									
									var thisLi = $("ul[use=rightAccordion]").children("li[sequence='"+thisSeq+"']");
									$(thisLi).children("ul").slideUp();
									$(thisLi).children("ul").attr("displayStatus",'N');
									var nextLi = $("ul[use=rightAccordion]").children("li[sequence='"+nextSeq+"']");
									$(nextLi).show();
									$(nextLi).children("ul").slideDown();
									$(nextLi).children("ul").attr("displayStatus",'Y');
								}
							});			
						});	
						$("li[use=abstractCoAuthors]").find("input[type=radio][use=coAuthorCountSelect]").click(function(){							
							var count = parseInt($(this).val());			
							var container = $("li[use=abstractCoAuthors]").find("div[use=coAutherPlacement]");
							var existing = $(container).find("div[use=coAuthorContainerPlaced]").length;
							
							if(existing > count)
							{
								var diff = existing - count;
								for(var i=0; i<parseInt(diff); i++)
								{
									try
									{
										$(container).find("div[use=coAuthorContainerPlaced]").last().remove();
									}
									catch(e){}
								}
							}
							else
							{
								var diff = count - existing;								
								for(var i=0; i<parseInt(diff); i++)
								{
									try
									{
										var cloned = $("div[use=coAuthorContainer]").clone();
										console.log('cloned>>'+cloned);
										$(cloned).attr("use","coAuthorContainerPlaced");
										$(container).append(cloned);
										$(cloned).show();
									}
									catch(e){
										console.log('problem>>'+e.message());
									}
								}
							}
							
							$.each($(container).find("div[use=coAuthorContainerPlaced]"),function(i,obj){
								$(obj).find("span[use=coAuthorSrl]").text(i+1);
							});		

								

									$('.institute').keypress(function(event) {
	    
								    
								    var inputValue = event.which;
								    if (!(inputValue >= 65 && inputValue <= 90) && !(inputValue >= 97 && inputValue <= 122) && !(inputValue == 32)) { 
									      event.preventDefault(); 
									    }

								  });

								  $('.department').keypress(function(event) {
	    
								    
								    var inputValue = event.which;
								    if (!(inputValue >= 65 && inputValue <= 90) && !(inputValue >= 97 && inputValue <= 122) && !(inputValue == 32)) { 
									      event.preventDefault(); 
									    }

								  });		    	

								  			
						});	
						$("textarea[checkFor=wordCount]").keyup(function(){		
							wordLimitCounter(this);
						});
						$("textarea[checkFor=wordCount]").blur(function(){		
							wordLimitCounter(this);
						});

						$("#abstract_author_phone_no").blur(function(e) {
							var phone_no = $.trim($(this).val());
						   	if (validatePhone(phone_no)) {
						      popDownAlert();
						   	}
						   	else {
						       popoverAlert(this)
						   	}
						});
						$('input[name^="abstract_coauthor_phone_no"]').each(function() { 
							$(this).blur(function(){
								var phone_no = $.trim($(this).val());
								if (validatePhone(phone_no)) {
								    popDownAlert();
							   	}
							   	else {
							   	    popoverAlert(this)
							   	}
							})
						});


						// prevent to paste content if any html tags are found
						$('textarea').bind("paste",function(e) {
		                    let paste = (event.clipboardData || window.clipboardData).getData('text');
		                    paste = paste.toUpperCase();
		                    if(/<(br|basefont|hr|input|source|frame|param|area|meta|!--|col|link|option|base|img|wbr|!DOCTYPE|a|abbr|acronym|address|applet|article|aside|audio|b|bdi|bdo|big|blockquote|body|button|canvas|caption|center|cite|code|colgroup|command|datalist|dd|del|details|dfn|dialog|dir|div|dl|dt|em|embed|fieldset|figcaption|figure|font|footer|form|frameset|head|header|hgroup|h1|h2|h3|h4|h5|h6|html|i|iframe|ins|kbd|keygen|label|legend|li|map|mark|menu|meter|nav|noframes|noscript|object|ol|optgroup|output|p|pre|progress|q|rp|rt|ruby|s|samp|script|section|select|small|span|strike|strong|style|sub|summary|sup|table|tbody|td|textarea|tfoot|th|thead|time|title|tr|track|tt|u|ul|var|video).*?>|<(video).*?<\/\2>/i.test(paste) == true) {
		                            alert('Unable to paste, Special character and symbols are not allowed.Please remove those tags and try again.');
		                            e.preventDefault();
		                    }
		                });
					});
					// document ready end


					function validatePhone(phoneNumber) {
					   
					    var filter = /^[0-9-+]+$/;
					    
					    if (filter.test(phoneNumber) && phoneNumber.length === 10) {
					        return true;
					    }
					    else {
					        return false;
					    }
					}
					
					function validateOnNextButton(seq)
					{
						popDownAlert();
						
						var returnVal 	= true;
						var thisLi 		= $("ul[use=rightAccordion]").children("li[sequence='"+seq+"']");
						var thisLiUse 	= $(thisLi).attr("use");
						
						console.clear();
						console.log('initial>>'+returnVal);
						
						$.each($(thisLi).find("input[type=text],input[type=email],input[type=tel],input[type=number],input[type=date],textarea"),function(){
							//var attr = $(this).attr('required');
							var abstractCategory = localStorage.getItem('subCat');
							var abstractSubmissionType = localStorage.getItem('subType');


							if( !$(this).hasClass('hideitem') ){
								var attr = $(this).prop("required", true);
								console.log(attr);
								if (typeof attr !== typeof undefined && attr !== false) {
									console.log('hasReq>>'+$(this).attr('name'));
									if($.trim($(this).val())=='')
									{
										alert(12);
										$(this).focus();
										popoverAlert(this);
										returnVal = false;
										return false;
									}
								}
							}
							
							
						});	
						console.log('text>>'+returnVal);
						if(!returnVal) return false;
						
						$.each($(thisLi).find("select"),function(){
								
							//var attr = $(this).prop("required", true);
							var attr = $( this ).attr( 'required' );
							console.log('ATTRR'+attr)
							if (typeof attr !== typeof undefined && attr !== false) {
																								
								if($.trim($(this).val())=='')
								{
									$(this).focus();
									popoverAlert(this);
									returnVal = false;
									return false;
								}
							}
						});	
						console.log('select>>'+returnVal);	
						if(!returnVal) return false;		
							
						$.each($(thisLi).find("input[type=radio]"),function(){
							var hasRequired = false;
							//var attr = $(this).attr('required');
							var attr = $(this).prop("required", true);
							if (typeof attr !== typeof undefined && attr !== false) 
							{
								console.log('hasReq>>'+$(this).attr('name'));
								hasRequired = true;
							}
							if(hasRequired)
							{
								var name = $(this).attr("name");
								
								if($("input[type=radio][name='"+name+"']:checked").length == 0)
								{
									
									popoverAlert(this);
									returnVal = false;
									return false;
								}
							}
						});


						var phone_no = $(thisLi).find("input[usefor=mobile]").val();
						console.log('phone_no-->'+phone_no)
						if (typeof phone_no !== typeof undefined && phone_no !== false) 
						{
						   	if (!validatePhone(phone_no)) {
						      	popoverAlert($(thisLi).find("input[usefor=mobile]"))
						      	returnVal = false;
						      	return false;
						   	}
					   	}
					   	
						console.log('radio>>'+returnVal);	
						if(!returnVal) return false;	
						
						if(thisLiUse=='abstractAuthorDetails')
						{
							returnVal = validateAbstractAuthorDetails(thisLi);
							console.log('abstractAuthorDetails>>'+returnVal);	
							if(!returnVal) return false;		
						}
						else if(thisLiUse=='abstractCoAuthors')
						{
							returnVal = validateAbstractCoAuthors(thisLi);
							console.log('abstractCoAuthors>>'+returnVal);		
							if(!returnVal) return false;	
						}
						else if(thisLiUse=='abstractDetails')
						{
							returnVal = validateAbstractDetails(thisLi);
							console.log('abstractDetails>>'+returnVal);	
							if(!returnVal) return false;		
						}
						else if(thisLiUse=='abstractNominations')
						{
							returnVal = validateAbstractNominations(thisLi);
							console.log('abstractNominations>>'+returnVal);	
							if(!returnVal) return false;		
						}
						else if(thisLiUse=='abstractPresenterDetails')
						{
							returnVal = validatePresenterDetails(thisLi);
							console.log('abstractPresenterDetails>>'+returnVal);	
							if(!returnVal) return false;		
						}
						console.log('final>>'+returnVal);						
						return returnVal;
					}


					
					function submissionTypeFieldsSet(obj, cat_id, subcat_id)
					{
						var submissionType  			= $(obj).val();	
						var subsubcat_id  			= $(obj).val();	
						// set data in localstorage

						//alert(submissionType);
						localStorage.setItem("subSType", submissionType);

						var submissionCategoryContainer	= $("li[use=submissionTypeRadio]");
						var submissionSubTypeObj		= $(submissionCategoryContainer).find("input[type=radio]:checked");
						var submissionSubType  			= $(submissionSubTypeObj).val();
						$("ul[use=rightAccordion]").find("ul[level=1]").slideUp();
						$("ul[use=rightAccordion]").find("ul[level=1]").attr("displayStatus",'N');	
						
						if(submissionSubType != '' && submissionSubType != undefined){
							
							$(obj).attr('relatedSubmissionSubType',submissionSubType)
							enableAllFileds($("li[use=abstractPresenterDetails]"));
							$("li[use=abstractPresenterDetails]").find("button[use=nextButton]").show();
							$("li[use=abstractAuthorDetails]").find("button[use=nextButton]").show();
							//$("ul[use=rightAccordion]").find("ul[level=1]").slideDown();
							//$("ul[use=rightAccordion]").find("ul[level=1]").attr("displayStatus",'Y');
							$("ul[use=rightAccordion]").find("li[use=abstractPresenterDetails] ul").slideDown();
							
						
						}else{

							$(obj).attr('relatedSubmissionSubType','')
							//disableAllFileds($("li[use=abstractPresenterDetails]"));
							//$("li[use=abstractPresenterDetails]").find("button[use=nextButton]").hide();
							//$("li[use=abstractAuthorDetails]").find("button[use=nextButton]").hide();
							//$("ul[use=rightAccordion]").find("ul[level=1]").slideUp();
							//$("ul[use=rightAccordion]").find("ul[level=1]").attr("displayStatus",'N');
							$("ul[use=rightAccordion]").find("li[use=abstractPresenterDetails] ul").slideDown();
						}

						if(cat_id!='' && subcat_id!='' && subsubcat_id!='')
						{
							//alert('cat'+cat_id+'subcat_id'+subcat_id+'subsubcat_id'+subsubcat_id)
							$.ajax({
								type: "POST",
								url: "abstract_request.process.php",
								data: {action:'generateSubSubTopic',cat_id:cat_id,subcat_id:subcat_id,subsubcat_id:subsubcat_id},
								dataType: "html",
								async: false,
								success: function(JSONObject){
									console.log(JSONObject);
									$('#abstract_topic_id').html(JSONObject)
									
								},
								error: function (jqXHR, textStatus, errorThrown) {
							          console.log(jqXHR+'--'+textStatus+'--'+errorThrown)
							    }
							});		
						}
					}

					function abstractSubmissionType(obj,cat_id,submission_id) {
						var submissionType  			= $(obj).val();	
						// set data in localstorage
						localStorage.setItem("subType", submissionType);
						//alert('cat='+cat_id+'sub='+submission_id+'submissionType='+submissionType);
						$("li[use=submissionSubTypeRadio]").hide();
						
						if(submissionType === 'Case Report'){
							$('.submissionSubTypeRadio_'+cat_id+'_'+submission_id).show();

							$('#abstract_background_aims').parent().hide();
							$('#abstract_background_aims').addClass('hideitem')
							$('#abstract_description').parent().show();
							$('#abstract_description').removeClass('hideitem')
							$('#abstract_material_methods').parent().hide();
							$('#abstract_material_methods').addClass('hideitem')
							$('#abstract_results').parent().hide();
							$('#abstract_results').addClass('hideitem')
						}else{
							$('.submissionSubTypeRadio_'+cat_id+'_'+submission_id).show();

							$('#abstract_background_aims').parent().show();
							$('#abstract_background_aims').removeClass('hideitem')
							$('#abstract_description').parent().hide();
							$('#abstract_description').addClass('hideitem')
							$('#abstract_material_methods').parent().show();
							$('#abstract_material_methods').addClass('hideitem')
							$('#abstract_material_methods').removeClass('hideitem')
							$('#abstract_results').parent().show();
							$('#abstract_results').removeClass('hideitem')
						}

						var submissionCategoryContainer	= $("li[use=submissionSubTypeRadio]");
						var submissionSubTypeObj		= $(submissionCategoryContainer).find("input[type=radio]:checked");
						var submissionSubType  			= $(submissionSubTypeObj).val();	
						$("ul[use=rightAccordion]").find("ul[level=1]").slideUp();
						$("ul[use=rightAccordion]").find("ul[level=1]").attr("displayStatus",'N');
						
						if(abstractSubmissionType != '' && submissionSubType != undefined){
							
							$(obj).attr('relatedSubmissionType',submissionSubType)
							enableAllFileds($("li[use=abstractPresenterDetails]"));
							$("li[use=abstractPresenterDetails]").find("button[use=nextButton]").show();
							$("li[use=abstractAuthorDetails]").find("button[use=nextButton]").show();
							//$("ul[use=rightAccordion]").find("ul[level=1]").slideDown();
							//$("ul[use=rightAccordion]").find("ul[level=1]").attr("displayStatus",'Y');
							$("ul[use=rightAccordion]").find("li[use=abstractPresenterDetails] ul").slideDown();
						
						}else{
							
							$(obj).attr('relatedSubmissionType','')
							//disableAllFileds($("li[use=abstractPresenterDetails]"));
							//$("li[use=abstractPresenterDetails]").find("button[use=nextButton]").hide();
							//$("li[use=abstractAuthorDetails]").find("button[use=nextButton]").hide();
							//$("ul[use=rightAccordion]").find("ul[level=1]").slideUp();
							//$("ul[use=rightAccordion]").find("ul[level=1]").attr("displayStatus",'N');
							$("ul[use=rightAccordion]").find("li[use=abstractPresenterDetails] ul").slideDown();
						}


						if(cat_id!='' && submission_id!='')
						{
							$.ajax({
								type: "POST",
								url: "abstract_request.process.php",
								data: {action:'generateCatSubTopic',cat_id:cat_id,submission_id:submission_id},
								dataType: "html",
								async: false,
								success: function(JSONObject){
									if(JSONObject.trim()=='empty')
									{
										$('#topicDetails').hide();
										//document.getElementById("abstract_topic_id").required = false;
										$('#abstract_topic_id').attr('required', false); 
									}
									else
									{
										$('#topicDetails').show();
										$('#abstract_topic_id').html(JSONObject)
									}
									
								},
								error: function (jqXHR, textStatus, errorThrown) {
							          console.log(jqXHR+'--'+textStatus+'--'+errorThrown)
							    }
							});
						}
					}
					
					function abstractTypeChange(obj,cat_id, fieldArr)
					{		
						var submissionSubType = $(obj).val();
						//alert(cat_id);
						fieldArr = fieldArr.split(",");
  						//alert(fieldArr.length);
						//console.log(fieldArr.length);
						// set data in localstorage
						$('.commn-absfields').hide();
						localStorage.setItem("subCat", submissionSubType);
						$('#abstract_description').addClass('hideitem')

						$("ul[use=rightAccordion]").find("ul[level=1]").slideUp();
						$("ul[use=rightAccordion]").find("ul[level=1]").attr("displayStatus",'N');	
						
						$("li[use=submissionTypeRadio]").hide();
						$("li[use=submissionSubTypeRadio]").hide();

						enableAllFileds($("li[use=abstractPresenterDetails]"));
						$("li[use=abstractPresenterDetails]").find("button[use=nextButton]").show();
						//$("li[use=abstractAuthorDetails]").find("button[use=nextButton]").show();
						
						// enable submission type and sub-submission type for Free Paper Abstract Category 

						//alert(submissionSubType);
						if(submissionSubType === "Free Paper"){
							$('.submissionTypeRadio'+cat_id).show();
							var submissionTypeContainer	= $("li[use=leftAccordionSubmissionType]");						
							$(submissionTypeContainer).find("input[type=radio]").prop("disabled",false);
							$(submissionTypeContainer).find("input[type=radio]").prop("checked",false);
							
							$('.abs-submission-type').show();
							$('.abs-sub-submission-type').show();
							$("ul[use=rightAccordion]").find("li[use=abstractPresenterDetails] ul").slideDown();
							//disableAllFileds($("li[use=abstractPresenterDetails]"));
							//$("li[use=abstractPresenterDetails]").find("button[use=nextButton]").hide();
							$("li[use=abstractAuthorDetails]").find("button[use=nextButton]").show();

							$('#upload_abstract_file').parent().show();
							$('#upload_abstract_file').addClass('hideitem')


						}
						if(submissionSubType === "Poster Presentation"){
							$('#topicDetails').hide();
						}
						else{
							//alert(12);
							$('.submissionTypeRadio'+cat_id).show();
							var submissionTypeContainer	= $("li[use=leftAccordionSubmissionType]");						
							$(submissionTypeContainer).find("input[type=radio]").prop("disabled",false);
							$(submissionTypeContainer).find("input[type=radio]").prop("checked",false);

							$('.abs-submission-type').show();
							$('.abs-sub-submission-type').show();

							//disableAllFileds($("li[use=abstractPresenterDetails]"));
							$("ul[use=rightAccordion]").find("li[use=abstractPresenterDetails] ul").slideDown();
							//$("li[use=abstractPresenterDetails]").find("button[use=nextButton]").hide();
							$("li[use=abstractAuthorDetails]").find("button[use=nextButton]").show();

							$('#upload_abstract_file').parent().show();
							$('#upload_abstract_file').removeClass('hideitem')
							

							localStorage.setItem("subSType", '');
							localStorage.setItem("subType", '');

							$('#topicDetails').show();

							
							for(var i=0;i<fieldArr.length;i++)
							{

								//alert(fieldArr[i]);
								$('#fields_'+fieldArr[i]).show();
								$('#fieldVal_'+fieldArr[i]).removeClass("hideitem");
							}
						}

						if(submissionSubType !== '')
						{
							
							
							$.ajax({
								type: "POST",
								url: "abstract_request.process.php",
								data: {action:'generateTopic',topic:cat_id},
								dataType: "html",
								async: false,
								success: function(JSONObject){
									if(JSONObject)
									{
										if(JSONObject.trim()=='empty')
										{
											$('#topicDetails').hide();
											//document.getElementById("abstract_topic_id").required = false;
											$('#abstract_topic_id').attr('required', false); 
										}
										else
										{
											$('#topicDetails').show();
											$('#abstract_topic_id').html(JSONObject)
										}
									}
									
									
								},
								error: function (jqXHR, textStatus, errorThrown) {
					                  console.log(jqXHR+'--'+textStatus+'--'+errorThrown)
					            }
							});



							$.ajax({
								type: "POST",
								url: "abstract_request.process.php",
								data: {action:'checkSubCat',cat_id:cat_id,delegateId:'<?php echo $delegateId; ?>'},
								dataType: "html",
								async: false,
								success: function(JSONObject){
									if(JSONObject)
									{
										
									}
									
									
								},
								error: function (jqXHR, textStatus, errorThrown) {
					                  console.log(jqXHR+'--'+textStatus+'--'+errorThrown)
					            }
							});
						}
					}
					
					function validateAbstractAuthorDetails(obj)
					{
						return true;
					}
					
					function validateAbstractCoAuthors(obj)
					{
						return true;
					}
					
					function validateAbstractDetails(obj)
					{
						// remove all html tag
						$('textarea').each(function(i,evt){
		                    var message = jQuery(this).val();
		                    if(/<(br|basefont|hr|input|source|frame|param|area|meta|!--|col|link|option|base|img|wbr|!DOCTYPE|a|abbr|acronym|address|applet|article|aside|audio|b|bdi|bdo|big|blockquote|body|button|canvas|caption|center|cite|code|colgroup|command|datalist|dd|del|details|dfn|dialog|dir|div|dl|dt|em|embed|fieldset|figcaption|figure|font|footer|form|frameset|head|header|hgroup|h1|h2|h3|h4|h5|h6|html|i|iframe|ins|kbd|keygen|label|legend|li|map|mark|menu|meter|nav|noframes|noscript|object|ol|optgroup|output|p|pre|progress|q|rp|rt|ruby|s|samp|script|section|select|small|span|strike|strong|style|sub|summary|sup|table|tbody|td|textarea|tfoot|th|thead|time|title|tr|track|tt|u|ul|var|video).*?>|<(video).*?<\/\2>/i.test(message) == true) {

		                        alert('Special character and symbols are not allowed.Please check and remove those otherwise system will automatically remove those tags..');
		                        //event.preventDefault();

		                        message= message.replace(/<(br|basefont|hr|input|source|frame|param|area|meta|!--|col|link|option|base|img|wbr|!DOCTYPE|a|abbr|acronym|address|applet|article|aside|audio|b|bdi|bdo|big|blockquote|body|button|canvas|caption|center|cite|code|colgroup|command|datalist|dd|del|details|dfn|dialog|dir|div|dl|dt|em|embed|fieldset|figcaption|figure|font|footer|form|frameset|head|header|hgroup|h1|h2|h3|h4|h5|h6|html|i|iframe|ins|kbd|keygen|label|legend|li|map|mark|menu|meter|nav|noframes|noscript|object|ol|optgroup|output|p|pre|progress|q|rp|rt|ruby|s|samp|script|section|select|small|span|strike|strong|style|sub|summary|sup|table|tbody|td|textarea|tfoot|th|thead|time|title|tr|track|tt|u|ul|var|video).*?|<(video).*?<\/\2>|".*">.*?|<\/(.*).*?>/g,''); 
		                    }
		                    
		                    $(this).val(message)
		                }) 
						return true;
					}
					
					function validateAbstractNominations(obj)
					{
						return true;
					}
					
					function validatePresenterDetails(obj)
					{
						return true;
					}
							
					function replicateEntry(src,traget)
					{
						$(traget).val($(src).val());
					}
										
					/*function chooseNomination(obj)
					{
						var parent = $("li[use=abstractNominations]");
						
						$(parent).find("label[use=abstractAward]").hide();						
						$(parent).find("input[type=radio][use=award]").prop("checked",false);
						$(parent).find("input[type=radio][use=award]").prop("required",false);
												
						$(parent).find("label[use=abstractAward][topicId='"+$(obj).val()+"']").show();
						$(parent).find("label[use=abstractAward][topicId='"+$(obj).val()+"']").find("input[type=radio][use=award]").prop("required",true);
					}*/
					
					function effectOnAwardClick(obj)
					{
						var parent =  $("li[use=abstractNominations]");
						
						if($(obj).is(":checked"))
						{
							$(parent).find("input[type=radio][use=award]").prop("checked",false);
							$(parent).find("input[type=radio][use=award][value=N]").prop("checked",true);
							$(obj).prop("checked",true);
						}
						else
						{
							$(parent).find("input[type=radio][use=award]").prop("checked",false);
						}
					}
										
					function setAsPresenter(obj)
					{
						
						
						var author_name = $.trim($('#abstract_presenter_name').val());
						var author_country_id = $.trim($('#abstract_presenter_country_id').val());
						var author_state_id = $.trim($('#abstract_presenter_state_id').val());
						var author_city = $.trim($('#abstract_presenter_city').val());
						var author_mobile = $.trim($('#abstract_presenter_mobile').val());
						var author_institute_name = $.trim($('#abstract_presenter_institute_name').val());
						var author_department = $.trim($('#abstract_presenter_department').val());

						if($(obj).prop('checked')){
							
							// set author name
							if(author_name != ''){
								$('#abstract_author_name').val(author_name)
							}
							
							// set author country
							if(author_country_id != ''){
								$('#abstract_author_country').val(author_country_id)
								generateStateOptionList($('#abstract_author_country'));
							}
							
							// set author state
							if(author_state_id != ''){
								$('#abstract_author_state_id').val(author_state_id)
							}

							// set author city
							if(author_city != ''){
								$('#abstract_author_city').val(author_city)
							}

							// set author mobile
							if(author_mobile != ''){
								$('#abstract_author_phone_no').val(author_mobile);
							}

							// set author institute
							if(author_institute_name != ''){
								$('#abstract_author_institute_name').val(author_institute_name)
							}
							
							// set author department
							if(author_department != ''){
								$('#abstract_author_department').val(author_department)
							}

							$('#isPresenter').val('Y')

						}
						else{
							
							// set author state
							$('#abstract_author_name').val('')

							// set author country
							$('#abstract_author_country').val('')

							// set author state
							$('#abstract_author_state_id').val('')

							// set author city
							$('#abstract_author_city').val('')

							// set author mobile
							$('#abstract_author_phone_no').val('');

							// set author institute
							$('#abstract_author_institute_name').val('')

							// set author department
							$('#abstract_author_department').val('')
						}
					}
					
					function wordLimitCounter(obj)
					{
						var totalWordCount  = 0; 
						var totalCharacterCount  = 0;
						var parent 			= $("li[use=abstractDetails]");
						var wordCount 		= parseInt($(obj).attr("wordcount"));	

						var group 			= $(obj).attr("spreadInGroup");
						var showWordCount 	= $(parent).find("span[use='"+$(obj).attr("displayText")+"']");
						var wordLimit		= parseInt($(showWordCount).attr('limit'));
						var count 			= wordLimit;
						var totalCharacter = '';

						var word_type = '<?=$cfg['ABSTRACT.TOTAL.WORD.TYPE']?>';
						
						//console.log(count);
						$(parent).find("textarea[spreadInGroup='"+group+"']").each(function(){												   
							if($(this).val()!="")
							{
								 

								
						
									totalWordCount  = parseInt(totalWordCount) + parseInt(countWords($(this).val()));
									totalCharacterCount = parseInt(totalCharacterCount) + parseInt($(this).val().length);
									if($("textarea[spreadInGroup='"+group+"']").length > 1){
										count = wordLimit-totalWordCount

										countCharacter = parseInt(wordLimit)-parseInt(totalCharacterCount);

										totalCharacter+=$(this).val();

										//console.log('countCharacter='+countCharacter);
										
									}

									

			            			if(word_type=='word')
			            			{
			            				if(totalWordCount > wordLimit)
										{
											// prevent max word
				            				//$(obj).val(truncateWords($.trim($(obj).val()),wordLimit));
				            				$(showWordCount).css("color","#D41000");
				            				$(this).val(truncateWords($.trim($(this).val()),count));
				            			}else{
				            				$(showWordCount).css("color","");
			            				}	
			            			}
			            			else
			            			{
			            				if(totalCharacterCount > wordLimit)
										{
											console.log(1212);
											console.log('totalCharacter='+totalCharacterCount+'wordLimit='+wordLimit);
											
				            				$(showWordCount).css("color","#D41000");
				            				//$(this).val(truncateCharacters($.trim($(this).val()),countCharacter));
				            				if($(this).val().length>=wordLimit)
				            				{
				            					$(this).val($(this).val().substring($(this).val(), wordLimit));
				            				}
				            				else if(countCharacter<wordLimit)
				            				{
				            					
				            					$(this).val($(this).val().substring(0, countCharacter));
				            					//$(this).val("");
				            				}
				            				else
				            				{
				            					//alert(2);
				            					$(this).val("");
				            				}
				            				
				            			}else{
				            				$(showWordCount).css("color","");
				            			}
			            			}

								
							}
						});
						
						$(showWordCount).find("span[use=total_word_entered]").text("");

						if(word_type=='word')
		            	{
		            		
		            		$(showWordCount).find("span[use=total_word_entered]").text(totalWordCount);
		            	}
		            	else
		            	{
		            		$(showWordCount).find("span[use=total_word_entered]").text(totalCharacterCount);
		            	}
						


						
						/*if(totalWordCount > wordLimit)
						{
							$(showWordCount).css("color","#D41000");
						}
						else
						{
							$(showWordCount).css("color","");
						}*/
					}


					function truncateWords(str, no_words) {
					    //return str.split(" ").splice(0,no_words).join(" ");
					     const words = str.split(' ');

						  if (no_words >= words.length) {
						    return str;
						  }

						  const truncated = words.slice(0, no_words);
						  return `${truncated.join(' ')}`;
					}


					function truncateCharacters(str, no_words) {
					    //return str.split(" ").splice(0,no_words).join(" ");
					     const words = str.length;

						  if (no_words >= words.length) {
						  	console.log('str='+str);
						    return str;
						  }

						  //const truncated = words.slice(0, no_words);
						  //return `${truncated.join(' ')}`;
					}
					
					function countWords(stringValue)
					{
						//console.log("Length="+stringValue.length);
						s = stringValue;
						s = s.replace(/(^\s*)|(\s*$)/gi,"");
						s = s.replace(/[ ]{2,}/gi," ");
						s = s.replace(/\n /,"\n");
						
						return s.split(' ').length;
					}
										
					function popoverAlert(obj, msg)
					{
						var parent 		= $(obj).parent().closest("div[actAs=fieldContainer]");
						var alertObj 	= $(parent).children("div[callFor=alert]");
						
						var attr = $(alertObj).attr('defaultAlert');
						if (typeof attr === typeof undefined || attr === false) 
						{
							$(alertObj).attr('defaultAlert', $(alertObj).text());
							$(alertObj).click(function(){
								popDownAlert(this);
							});
						}						
						
						if(typeof msg !== typeof undefined && $.trim(msg) !== '')
						{
							$(alertObj).text(msg);
						}
						else
						{
							$(alertObj).text($(alertObj).attr('defaultAlert'));
						}
						
						$(alertObj).show();
					}
					
					function popDownAlert(obj)
					{
						if(typeof obj === typeof undefined)
						{
							$("div[callFor=alert]").hide();
						}
						else
						{
							$(obj).hide();
						}
					}
					
					function enableAllFileds(obj)
					{
						$.each($(obj).find("input,select,textarea"),function(){
							var attr = $(this).attr('disabled');
							if (typeof attr !== typeof undefined && attr !== false) {
								$(this).prop("disabled",false);
								$(this).attr("wasDisabled",'Y');
								$(obj).find(".dis_abled").attr("hasDisableCSS","Y");
								$(obj).find(".dis_abled").removeClass("dis_abled");
							}
						});
					}
					
					function disableAllFileds(obj)
					{
						$.each($(obj).find("input,select,textarea"),function(){
							if($(this).attr("wasDisabled")=='Y')
							{
								$(this).prop("disabled",true);
								$(obj).find("span[hasDisableCSS=Y],div[hasDisableCSS=Y]").addClass("dis_abled");
							}
						});
					}
					
					var SLICE_SIZE = 500;

					function fileChangeHandler(targetObj,callbackFunction)
					{
						var sliceSize = SLICE_SIZE;
						var target = targetObj;
						var parent = $(target).parent().closest("div");
						var fileName = $(target).val();
						if(fileName != ""){ 
							var uploader = {
											index			: "",
											reader 			: {},
											file 			: {},
											slice_size 		: (sliceSize * 1024),
											sessionId		: $(parent).find("input[type=hidden][name=sessionId]").val(),
											dynamicFileName : "",
											target			: {},
											targetParent	: {},
											garndParent		: {},
											filenameTranP	: {},
											filenameOrgnP	: {},
											uploadProgrOb	: {},
											uploadProgrIm	: {},
											size_done		: 0,
											percent_done	: 0,
											nounce			: ""
										};
														
							if( start_upload(targetObj, uploader, callbackFunction) == false )
							{
								return false;
							}
							else
							{
								// $('#uploadModal').modal({
								// 	backdrop: 'static',
								// 	keyboard: false,
								// 	show	: true
								// });
								$('li[use=registrationProcess]').show();
							}
						}
					}
					
					function start_upload( target, uploader, callbackFunction )
					{
						var d = new Date();				
						uploader.index = d.getTime();
						
						uploader.reader = new FileReader();				
						//file = document.querySelector( '#upload_case_file' ).files[0];
						uploader.file = $(target).prop('files')[0];
						
						var maxSize = $(target).attr("allowedSize");
						
						if((uploader.file.size/1024)/1024 > parseInt(maxSize))
						{
							alert("Upload file within "+maxSize+" MB");
							$('#upload_abstract_file').val('');
							return false;
						}	
						
						var fileTypes = $(target).attr("allowedFileTypes");	
						var fArray = fileTypes.split(',');
						var typeMatch = false;
						for(var i = 0; i < fArray.length; i++)
						{
							if(uploader.file.name.endsWith($.trim(fArray[i])))
							{
								typeMatch = true;
								break;
							}
						}
						
						if(!typeMatch)
						{
							alert("Upload "+fileTypes+" files only");
							$('#upload_abstract_file').val('')
							
							//$(target).parent().closest("div").find("div[use=progress]").css('width','0%');
            				return false;
						}
						
						
						// createDynamicFileName
						uploader.dynamicFileName = uploader.sessionId+d.getTime()+'_'+uploader.file.name;	
						uploader.target			 = target;
						uploader.garndParent	 = $(target).parent().closest("li");
						uploader.targetParent	 = $(target).parent().closest("div");
						uploader.filenameTranP	 = $(uploader.targetParent).find("input[type=hidden][use=upload_temp_fileName]");	
						uploader.filenameOrgnP	 = $(uploader.targetParent).find("input[type=hidden][use=upload_original_fileName]");
						//uploader.uploadProgrOb	 = $(uploader.targetParent).find("div[use=progressbar]");		
						uploader.targetForm	 	 = $(target).parent().closest("form");
						uploader.nounce			 = 'E'+uploader.sessionId+d.getTime()+'x';
										
						console.log('::'+uploader.index+':: start upload');
						
						$(uploader.target).prop("disabled",true);
						
						$(uploader.garndParent).find("button[use=nextButton]").attr('isDisplay','N');
						$(uploader.garndParent).find("button[use=nextButton]").hide();
						$(uploader.garndParent).find("span[use=preSubmitProcess]").show();
						
						//$(uploader.uploadProgrOb).find('div[use=progress]').css('width','0%');
						//$(uploader.uploadProgrOb).show();	
						
						upload_file( 0, uploader, callbackFunction);
						
					}
					
					function upload_file( start, uploader, callbackFunction) 
					{
						var next_slice = start + uploader.slice_size + 1;
						
						var blob = uploader.file.slice( start, next_slice );
						
						console.log('::'+uploader.index+':: upload file with ==> '+start);
						
						uploader.reader.onloadend = function( event ) {											
							if ( event.target.readyState !== FileReader.DONE ) {
								return;
							}
							//alert(jsBASE_URL);
							console.log('::'+uploader.index+':: upload file to ==> chunked-file-uploader.php');
							$.ajax({
								url: jsBASE_URL+'chunked-file-uploader.php',
								type: 'POST',
								dataType: 'text',
								cache: false,
								data: {						
									action: 'uploadCaseReportFile',
									file_data: event.target.result,
									file: uploader.dynamicFileName,
									file_type: uploader.file.type,
									nonce: uploader.nounce
								},
								beforeSend: function(){
									$('li[use=registrationProcess]').show();					            },
								error: function( jqXHR, textStatus, errorThrown ) {
									console.log( jqXHR, textStatus, errorThrown );
								},
								success: function( data ) {
									console.log('::'+uploader.index+':: upload file response ==> '+data);
									uploader.size_done = start + uploader.slice_size;
									uploader.percent_done = Math.floor( ( uploader.size_done / uploader.file.size ) * 100 );
									
									if(uploader.percent_done > 100)
									{
										uploader.percent_done = 100;
									}
									
									console.log('::'+uploader.index+':: upload file progress ==> '+uploader.size_done+' bytes ['+uploader.percent_done+'%]');
									
									//$(uploader.uploadProgrOb).find('div[use=progress]').css('width',uploader.percent_done+'%');
													
									if ( next_slice < uploader.file.size ) 
									{
										// Update upload progress
										//$( '#upload_case_file_msg' ).html( 'Uploading File - ' + percent_done + '%' );								
										console.log('::'+uploader.index+':: upload file continue ==> '+next_slice+' ===>'+uploader.percent_done+'% done');
										
										// More to upload, call function recursively
										upload_file( next_slice, uploader );								
									} 
									else 
									{
										// Update upload progress
										//$( '#upload_case_file_msg' ).html( 'Upload Complete!' );
										
										console.log('::'+uploader.index+':: upload file END');
										
										$(uploader.filenameTranP).val(uploader.dynamicFileName);
										$(uploader.filenameOrgnP).val(uploader.file.name);
										
										$(uploader.target).prop("disabled",false);
										$(uploader.garndParent).find("span[use=preSubmitProcess]").hide();
										$(uploader.garndParent).find("button[use=nextButton]").attr('isDisplay','Y');
										$(uploader.garndParent).find("button[use=nextButton]").show();
										
										try
										{
											$('li[use=registrationProcess]').hide();	
											alert("File upload complete. \nYou can submit now.");		
											callbackFunction();
										}
										catch(e)
										{
											console.log('::'+uploader.index+':: upload file ENDED callback fail ==>'+e.message);
										}
									}
								}
							} );
						};
					
						uploader.reader.readAsDataURL( blob );
					}

					function generateStateOptionList(obj, callback)
					{
						//var parent 		= $(obj).parent().closest("table");  // close for abstract submission from the profile
						var parent 		= $(obj).parent().closest(".com-country-state");
						
						var countryId	= $(obj).val();
						//console.log(jsBASE_URL+'abstract_request.process.php?act=getStateOptionList&countryId='+countryId);
						
						$.ajax({
							type: "POST",
							url: jsBASE_URL+'abstract_request.process.php',
							data: 'act=getStateOptionList&countryId='+countryId,
							dataType: 'html',
							async: false,
							success: function(returnMessage)
							{
								//console.log(returnMessage+' country-state-data')
								if(returnMessage!='')
								{
									$(parent).find("select[operationMode=stateControl]").html("");
									$(parent).find("select[operationMode=stateControl]").html(returnMessage);
								}
								
								try
								{
									callback();
								}
								catch(e){}
							}
						});
					}

					$("form[name=abstractRequestForm]").submit(function(evnt){							
							console.log('triggered submit @ ');
							$.each($("ul[use=rightAccordion]").children("li"), function(i,level){
								try{
									var seq = $(this).attr("sequence");
									if(!validateOnNextButton(seq))
									{
										console.log('ERROR @ '+seq);
										evnt.preventDefault();
									}
								}
								catch(e)
								{
									console.log('ERROR : '+e.message);
									evnt.preventDefault();
								}
							});
						});					
				</script>
<?
		}
?>
            </div>
            <?php
          
           // if($delegateId>0 && $resultAbstractType)
           // {
            ?>
            <div class="col-xs-12 menu-links">                
               <!--  <p><a href="profile.php" target="_blank"><i class="fas fa-caret-right"></i>&nbsp&nbsp&nbsp Rules and Regulations</a></p>   -->            
                <p class="menu-login"><a href="<?=_BASE_URL_?>profile.php">BACK TO MY PROFILE</a></p>
            </div> 
            <?php
        	//}
            ?>
			
			<br/><br/>           
        </div>        
        <? //if(!$resultAbstractType){ 
        if(count($resultAbstractType)<10)
		{	
        	?>
	        <div class="col-xs-8 right-container regTarrif_rightPanel">
	            <div class="col-xs-12 col-xs-offset-0" style="padding: 0; margin-top: 15px;">                
					<ul use="rightAccordion" class="accordion" style="padding:0 0 0  0px; margin: 0; ">
						<!-- Abstract Presenter Details Start -->
	                    <li use="abstractPresenterDetails" class="rightPanel_userDetails">
	                        <div class="link" use="rightAccordianL1TriggerDiv">ABSTRACT PRESENTER DETAILS</div>
	                        <ul class="submenu" style="display: block"  displayStatus="Y">
	                            <li>
									<div class="col-xs-12 form-group input-material dis_abled">
										<label for="user_mobile">Email-id</label>
										<input type="text" name="abstract_presenter_email" id="abstract_presenter_email" class="form-control" style="text-transform:lowercase;" 
											   usefor="email" value="<?=$rowUserDetails['user_email_id']?>" 
											   readonly="true">
									</div>
									<div class="col-xs-12 form-group input-material dis_abled">
										<label for="user_email_id">Name</label>
										<input type="text" name="abstract_presenter_name" id="abstract_presenter_name" class="form-control" style="text-transform:uppercase;" 
											   usefor="name" value="<?=$rowUserDetails['user_full_name']?>"
											   readonly="true">
									</div>
																	
									<div class="col-xs-6 form-group input-material dis_abled">
										<label for="abstract_presenter_country">Country</label>
										<?php
										$sqlFetchCountry			 =	array();
										$sqlFetchCountry['QUERY']    = "SELECT * FROM "._DB_COMN_COUNTRY_." 
																	   WHERE `status` = ?
																		 AND `country_id` = ?";
																		 
										$sqlFetchCountry['PARAM'][]   = array('FILD' => 'status',  	   'DATA' =>'A',  'TYP' => 's');
										$sqlFetchCountry['PARAM'][]   = array('FILD' => 'country_id',  'DATA' =>$rowUserDetails['user_country_id'],  'TYP' => 's');								 
																	
										$resultFetchCountry = $mycms->sql_select($sqlFetchCountry);
										$rowCountry            = $resultFetchCountry[0];
										?>
										<input type="hidden" name="abstract_presenter_country_id" id="abstract_presenter_country_id" value="<?=$rowUserDetails['user_country_id']?>" />
										<input type="text" name="abstract_presenter_country" id="abstract_presenter_country" class="form-control" style="text-transform:uppercase;" 
											   usefor="country-display" value="<?=$rowCountry['country_name']?>" 
											   readonly="true">
									</div>
									<div class="col-xs-6 form-group input-material dis_abled">
										<label for="abstract_presenter_state">State</label>
										<?php
										$sqlFetchState			 =	array();
										$sqlFetchState['QUERY']    = "SELECT * FROM "._DB_COMN_STATE_." 
																	   WHERE `status` = ? 
																		 AND `st_id` = ?";
										$sqlFetchState['PARAM'][]   = array('FILD' => 'status',  	   'DATA' =>'A',  'TYP' => 's');
										$sqlFetchState['PARAM'][]   = array('FILD' => 'st_id',    'DATA' =>$rowUserDetails['user_state_id'],  'TYP' => 's');
																	
										$resultFetchState = $mycms->sql_select($sqlFetchState);
										$rowState           = $resultFetchState[0];
										?>
										<input type="hidden" name="abstract_presenter_state_id" id="abstract_presenter_state_id" value="<?=$rowUserDetails['user_state_id']?>" />
										<input type="text" name="abstract_presenter_state" id="abstract_presenter_state" class="form-control" style="text-transform:uppercase;" 
											   usefor="country-display" value="<?=strtoupper($rowState['state_name'])?>" 
											   readonly="true">
									</div>
									<div class="col-xs-6 form-group input-material dis_abled">
										<label for="user_mobile">City</label>
										<input type="text" name="abstract_presenter_city" id="abstract_presenter_city" class="form-control" style="text-transform:uppercase;" 
											   usefor="country-display" value="<?=strtoupper($rowUserDetails['user_city'])?>" 
											   readonly="true">
									</div>
									
									<div class="col-xs-6 form-group input-material dis_abled">
										<label for="user_mobile">Mobile</label>
										<input type="text" name="abstract_presenter_mobile" id="abstract_presenter_mobile" class="form-control" style="text-transform:uppercase;" 
											   usefor="mobile" value="<?=$rowUserDetails['user_mobile_no']?>" 
											   readonly="true">
									</div>
									
	                        		<div class="col-xs-6 form-group input-material dis_abled" actAs='fieldContainer'>
										<label for="user_first_name">Institute Name</label>
										<input type="text" class="form-control" style="text-transform:uppercase;" 
											   name="abstract_presenter_institute_name"  id="abstract_presenter_institute_name" usefor="instituteName"
											   value="<?=$rowUserDetails['user_institute_name']?>" 
											   disabled="disabled" required>
										<div class = "alert alert-danger" callFor='alert'>Please enter a proper value.</div>
									</div>
									<div class="col-xs-6 form-group input-material dis_abled" actAs='fieldContainer'>
										<label for="user_last_name">Department</label>
										<input type="text" class="form-control" style="text-transform:uppercase;"  
											   name="abstract_presenter_department"  id="abstract_presenter_department" usefor="department"
											   value="<?=$rowUserDetails['user_department']?>"
											   disabled="disabled" required>
										<div class = "alert alert-danger" callFor='alert'>Please enter a proper value.</div>
									</div>
									
									<div class=" col-xs-2 text-center pull-right">
										<button type="button" class="submit" use='nextButton' style="display:none;">Next</button>
									</div>

	        						<div class="clearfix"></div>
	                            </li>
	                        </ul>
	                    </li>
						<!-- Abstract Presenter Details End -->
						<!-- Author Start -->
						<?
							
							$same_user_array = array();
							$same_user_array['name'] = trim($rowUserDetails['user_full_name']);;
							$same_user_array['country_id'] = trim($rowUserDetails['user_country_id']);
							$same_user_array['state_id'] = trim($rowUserDetails['user_state_id']);
							$same_user_array['city'] = trim($rowUserDetails['user_city']);
							$same_user_array['mobile'] = trim($rowUserDetails['user_mobile_no']);
							//echo json_encode($same_user_array);
						?>
						<li use="abstractAuthorDetails" style="display:none;" class="rightPanel_userDetails">
	                        <div class="link" use="rightAccordianL1TriggerDiv">AUTHOR</div>
	                        <ul class="submenu" style="display: none"  displayStatus="Y">
	                            <li>
									<div class="com-country-state">
										<div class="col-xs-12 form-group input-material" actAs='fieldContainer'>
											<label for="user_email_id">Name</label>
											<input type="text" name="abstract_author_name" id="abstract_author_name" class="form-control" style="text-transform:uppercase;" 
												   usefor="name" value="" required>
											<div class = "alert alert-danger" callFor='alert'>Please enter a proper value.</div>
										</div>
										
										<div class="col-xs-6 form-group input-material" actAs='fieldContainer'>
											<label for="abstract_author_country">Country</label>
											<select class="form-control select" name="abstract_author_country" id="abstract_author_country" usefor="country"
												style="text-transform:uppercase;padding-top:6px;" 
												operationmode="countryControl"
												onChange="generateStateOptionList(this);"> 
												<option value="">-- Select Country --</option>
												<?php
												$sqlFetchCountry			 =	array();
												$sqlFetchCountry['QUERY']    = "SELECT * FROM "._DB_COMN_COUNTRY_." 
																			   WHERE `status` = ? 
																			ORDER BY `country_name` ASC";
												$sqlFetchCountry['PARAM'][]   = array('FILD' => 'status',  	   'DATA' =>'A',  'TYP' => 's');							
												$resultFetchCountry = $mycms->sql_select($sqlFetchCountry);
												if($resultFetchCountry)
												{
													foreach($resultFetchCountry as $keyCountry=>$rowFetchCountry)
													{
												?>
														<option value="<?=$rowFetchCountry['country_id']?>"><?=$rowFetchCountry['country_name']?></option>
												<?php
													}
												}
												?>
											</select>
											
											<div class = "alert alert-danger" callFor='alert'>Please enter a proper value.</div>
										</div>
										
										<div class="col-xs-6 form-group input-material" actAs='fieldContainer'>
											<label for="abstract_author_state_id">State</label>
											<select class="form-control select" name="abstract_author_state_id" id="abstract_author_state_id" usefor="state" 
														style="text-transform:uppercase;padding-top:6px;" 
														operationMode="stateControl">
													<option value="">-- Select Country First --</option>
													
											</select>
											<div class = "alert alert-danger" callFor='alert'>Please enter a proper value.</div>
										</div>
										<div class="col-xs-6 form-group input-material" actAs='fieldContainer'>
											<label for="user_city">City</label>
											<input type="text" name="abstract_author_city" id="abstract_author_city" class="form-control" style="text-transform:uppercase;" 
												   usefor="city" value="" required>
											<div class = "alert alert-danger" callFor='alert'>Please enter a proper value.</div>
										</div>
										<div class="col-xs-6 form-group input-material" actAs='fieldContainer'>
											<label for="user_mobile">Mobile</label>
											<input type="text" name="abstract_author_phone_no" id="abstract_author_phone_no" class="form-control" style="text-transform:uppercase;" usefor="mobile" value="" onkeypress="return isNumber(event)">
											<div class = "alert alert-danger" callFor='alert'>Please enter a proper value.</div>
										</div>
										
										<div class="col-xs-6 form-group input-material" actAs='fieldContainer'>
											<label for="user_first_name">Institute Name</label>
											<input type="text" class="form-control" style="text-transform:uppercase;" 
												   name="abstract_author_institute_name"  id="abstract_author_institute_name" usefor="instituteName"
												   value="">
											<div class = "alert alert-danger" callFor='alert'>Please enter a proper value.</div>
										</div>
										<? /*
										<div class="col-xs-6 form-group input-material dis_abled" actAs='fieldContainer'>
											<label for="user_first_name">Institute Name</label>
											<input type="text" class="form-control" style="text-transform:uppercase;" 
												   name="abstract_author_institute_name"  id="abstract_presenter_institute_name" usefor="instituteName"
												   value="<?=$rowUserDetails['user_institute_name']?>"
												   onkeyup="replicateEntry(this,$(this).parent().closest('li').find('input[id=abstract_author_institute_name]'));" 
												   disabled="disabled" required>
											<div class = "alert alert-danger" callFor='alert'>Please enter a proper value.</div>
										</div>
										*/ ?>
										<div class="col-xs-6 form-group input-material" actAs='fieldContainer'>
											<label for="user_last_name">Department</label>
											<input type="text" class="form-control" style="text-transform:uppercase;"  
												   name="abstract_author_department"  id="abstract_author_department" usefor="department"
												   value="" required>
											<div class = "alert alert-danger" callFor='alert'>Please enter a proper value.</div>
										</div>
										<? /*
										<div class="col-xs-6 form-group input-material dis_abled" actAs='fieldContainer'>
											<label for="user_last_name">Department</label>
											<input type="text" class="form-control" style="text-transform:uppercase;"  
												   name="abstract_presenter_department"  id="abstract_presenter_department" usefor="department"
												   value="<?=$rowUserDetails['user_department']?>"
												   onkeyup="replicateEntry(this,$(this).parent().closest('li').find('input[id=abstract_author_department]'))"
												   disabled="disabled" required>
											<div class = "alert alert-danger" callFor='alert'>Please enter a proper value.</div>
										</div>
										*/ ?>
										<!-- will be presenting -->
										<div class="col-xs-12 form-group">
											<div class="checkbox">
												<div>
													<label class="container-box" style="float:left; margin-right:20px;">Author and Presenter is the same person
													  <input type="checkbox" name="willBePresenter" use="willBePresenter" 
															 onclick="setAsPresenter(this)">
													  <span class="checkmark"></span>
													</label>
													&nbsp;
												</div>																				
											</div>
											<input type="hidden" id="isPresenter" name="isPresenter" value="">
										</div>
										
										<div class=" col-xs-2 text-center pull-right">
											<button type="button" class="submit" use='nextButton' style="display:none;">Next</button>
										</div>   
													 
										<div class="clearfix"></div>
									</div>
	                            </li>
	                        </ul>
	                    </li>
						<!-- Author End -->
						<li use="abstractCoAuthors" style="display:none;" class="rightPanel_accompany">						
							<input type="hidden" name="accompanyClasfId" value="<?=$accompanyCatagory?>" />
							<input type="hidden" name="accompanyTariffAmount" id="accompanyTariffAmount" value="<?=$registrationAmount?>" />
	                        <div class="link" use="rightAccordianL1TriggerDiv">CO-AUTHORS</div>
	                        <ul class="submenu" style="display: none">
	                            <li>
									<div class="col-xs-12 form-group ">
										<div class="checkbox">
											<label class="select-lable" >Number of Co-Authors(s)</label>
											<div>
												<label class="container-box" style="float:left; margin-right:20px;">None
												  <input type="radio" name="coAuthorCount" use="coAuthorCountSelect" value="0" checked="checked" required>
												  <span class="checkmark"></span>
												</label>
												<label class="container-box" style="float:left; margin-right:20px;">One
												  <input type="radio" name="coAuthorCount" use="coAuthorCountSelect" value="1">
												  <span class="checkmark"></span>
												</label>
												<label class="container-box" style="float:left; margin-right:20px;">Two
												  <input type="radio" name="coAuthorCount" use="coAuthorCountSelect" value="2">
												  <span class="checkmark"></span>
												</label>
												<label class="container-box" style="float:left; margin-right:20px;">Three
												  <input type="radio" name="coAuthorCount" use="coAuthorCountSelect" value="3">
												  <span class="checkmark"></span>
												</label>
												<label class="container-box" style="float:left; margin-right:20px;">Four
												  <input type="radio" name="coAuthorCount" use="coAuthorCountSelect" value="4">
												  <span class="checkmark"></span>
												</label>
												&nbsp;
											</div>																				
										</div>
									</div>
									                        		
									<div class="col-xs-12" use="coAutherPlacement">
										
									</div>
									                
									<div class=" col-xs-2 text-center pull-right">
										<button type="button" class="submit" use='nextButton'>Next</button>
									</div>                
	        						<div class="clearfix"></div>
	                            </li>
	                        </ul>
	                    </li>
						
						<li use="abstractDetails" style="display:none;" class="rightPanel_payment">
	                        <div class="link" use="rightAccordianL1TriggerDiv">ABSTRACT DETAILS</div>
	                        <ul class="submenu" style="display: none">
	                            <li>
									 <div class="col-xs-12 form-group input-material" actAs='fieldContainer' id="topicDetails">
										<label for="user_mobile">Topic</label>
										<select name="abstract_topic_id" id="abstract_topic_id"
												class="form-control select" style="text-transform:uppercase; height:60px;"  required="required"
												 > 
											<option value="">-- Select Topic --</option>
											<?php
											$sqlAbstractTopic			  =	array();
											$sqlAbstractTopic['QUERY']    = "SELECT * FROM "._DB_ABSTRACT_TOPIC_." 
																			  WHERE `status` = ? 
																		   ORDER BY `id` ASC";
											
											$sqlAbstractTopic['PARAM'][]  = array('FILD' => 'status', 'DATA' =>'A',  'TYP' => 's');
											$resultAbstractTopic = $mycms->sql_select($sqlAbstractTopic);
											
											if($resultAbstractTopic)
											{
												foreach($resultAbstractTopic as $keyAbstractTopic=>$rowAbstractTopic)
												{
											?>
											<option value="<?=$rowAbstractTopic['id']?>"><?=$rowAbstractTopic['abstract_topic']?></option>
											<?php
												}
											}
										?>
										</select>
										<div class = "alert alert-danger" callFor='alert'>Please choose a proper option.</div>
									</div> 
									<?php
									    $sqlAbstractFields			  =	array();
										$sqlAbstractFields['QUERY']    = "SELECT * FROM "._DB_ABSTRACT_FIELDS_." 
																		  WHERE `status` = ?";
										
										$sqlAbstractFields['PARAM'][]  = array('FILD' => 'status', 'DATA' =>'A',  'TYP' => 's');
										
										$resultAbstractFields = $mycms->sql_select($sqlAbstractFields);

										
									?>
									
									<div class="col-xs-12 form-group input-material" actAs='fieldContainer' relatedSubmissionType="" relatedSubmissionSubType="">
										<label for="user_mobile">Title</label>
										<textarea class="form-control" 
												  name="abstract_title" id="abstract_title" 
												  checkFor="wordCount" spreadInGroup="abstractTitle" 
												  displayText="abstract_title_word_count" 
												  style="text-transform:uppercase;" <?php if($cfg['ABSTRACT.TITLE.WORD.TYPE']=='word'){ ?> maxlength="<?=$cfg['ABSTRACT.TITLE.WORD.LIMIT']?>"<?php } ?> required></textarea>
										<div class = "alert alert-success" style="display:block;">
											<span use="abstract_title_word_count" limit="<?=$cfg['ABSTRACT.TITLE.WORD.LIMIT']?>">
												<span use="total_word_entered">0</span> / 
												<span use="total_word_limit"><?=$cfg['ABSTRACT.TITLE.WORD.LIMIT']?></span>
												<!-- <span style="color: #D41000;">(Title should be within <?=$cfg['ABSTRACT.TITLE.WORD.LIMIT']?> <?=$cfg['ABSTRACT.TOTAL.WORD.TYPE']?>.)</span> -->
												<span style="color: #D41000;">(Title should be within <?=$cfg['ABSTRACT.TITLE.WORD.LIMIT']?> <?=$cfg['ABSTRACT.TITLE.WORD.TYPE']?>s )</span>
											</span>
										</div>
										<div class = "alert alert-danger" callFor='alert'>Please enter a proper value.</div>
									</div>
									<?php
									foreach ($resultAbstractFields as $key => $value) 
									{
										
									
									?>
										<div class="col-xs-12 form-group input-material commn-absfields" id="fields_<?=$value['id']?>" actAs='fieldContainer' relatedSubmissionType="" relatedSubmissionSubType="" style="display: none;">
											<label for="user_mobile"><?=$value['display_name']?></label>
											<textarea class="form-control hideitem" 
													  name="<?=$value['field_key']?>[]" id="fieldVal_<?=$value['id']?>" 
													  checkFor="wordCount" spreadInGroup="abstractContent" 
													  displayText="abstract_total_word_display" word_type="<?=$cfg['ABSTRACT.TOTAL.WORD.TYPE']?>"
													  ></textarea>
											<div class = "alert alert-danger" callFor='alert'>Please enter a proper value.</div>
										</div>
									<?php
									}	
									?>	
									
									<!-- <div class="col-xs-12 form-group input-material" actAs='fieldContainer' relatedSubmissionType="" relatedSubmissionSubType="">
										<label for="user_mobile">Aims &amp; Objective</label>
										<textarea class="form-control" 
												  name="abstract_background_aims" id="abstract_background_aims"
												  checkFor="wordCount" spreadInGroup="abstractContent" 
												  displayText="abstract_total_word_display" word_type="<?=$cfg['ABSTRACT.TOTAL.WORD.TYPE']?>"></textarea>
										<div class = "alert alert-danger" callFor='alert'>Please enter a proper value.</div>
									</div> 
									
									<div class="col-xs-12 form-group input-material" style="display:none;" actAs='fieldContainer' relatedSubmissionType="" relatedSubmissionSubType="">
										<label for="user_mobile">Description</label>
										<textarea class="form-control" 
												  name="abstract_description" id="abstract_description" 
												  checkFor="wordCount" spreadInGroup="abstractContent" 
												  displayText="abstract_total_word_display" word_type="<?=$cfg['ABSTRACT.TOTAL.WORD.TYPE']?>"
												  ></textarea>
										<div class = "alert alert-danger" callFor='alert'>Please enter a proper value.</div>
									</div>

									<div class="col-xs-12 form-group input-material" actAs='fieldContainer' relatedSubmissionType="" relatedSubmissionSubType="">
										<label for="user_mobile">Materials &amp; Methods</label>
										<textarea class="form-control" 
												  name="abstract_material_methods" id="abstract_material_methods" 
												  checkFor="wordCount" spreadInGroup="abstractContent" 
												  displayText="abstract_total_word_display" word_type="<?=$cfg['ABSTRACT.TOTAL.WORD.TYPE']?>"></textarea>
										<div class = "alert alert-danger" callFor='alert'>Please enter a proper value.</div>
									</div>
									
									<div class="col-xs-12 form-group input-material" actAs='fieldContainer' relatedSubmissionType="" relatedSubmissionSubType="">
										<label for="user_mobile">Results</label>
										<textarea class="form-control" 
												  name="abstract_results" id="abstract_results" 
												  checkFor="wordCount" spreadInGroup="abstractContent" 
												  displayText="abstract_total_word_display" word_type="<?=$cfg['ABSTRACT.TOTAL.WORD.TYPE']?>"
												  ></textarea>
										<div class = "alert alert-danger" callFor='alert'>Please enter a proper value.</div>
									</div>
									
									<div class="col-xs-12 form-group input-material" actAs='fieldContainer' relatedSubmissionType="" relatedSubmissionSubType="">
										<label for="user_mobile">Conclusion</label>
										<textarea class="form-control" 
												  name="abstract_conclusion" id="abstract_conclusion" 
												  checkFor="wordCount" spreadInGroup="abstractContent" 
												  displayText="abstract_total_word_display" word_type="<?=$cfg['ABSTRACT.TOTAL.WORD.TYPE']?>"></textarea>
										<div class = "alert alert-danger" callFor='alert'>Please enter a proper value.</div>
									</div>

									<div class="col-xs-12 form-group input-material" actAs='fieldContainer' relatedSubmissionType="" relatedSubmissionSubType="">
										<label for="user_mobile">References (If Any)</label>
										<textarea class="form-control" 
												  name="abstract_references" checkFor="wordCount" spreadInGroup="abstractContent" displayText="abstract_total_word_display"  id="abstract_references" word_type="<?=$cfg['ABSTRACT.TOTAL.WORD.TYPE']?>" 
												 ></textarea>
										<div class = "alert alert-danger" callFor='alert'>Please enter a proper value.</div>
									</div> -->

									<!-- <div class="col-xs-12 form-group input-material" style="display:none;" use='abstractFile' actAs='fieldContainer' relatedSubmissionType="" relatedSubmissionSubType="">
										<label for="user_mobile">Upload File</label>									
										<input type="hidden" name="sessionId" use="sessionId" value="<?=session_id()?>" />											
										<input type="hidden" name="upload_temp_doc_fileName" use="upload_temp_fileName" />
										<input type="hidden" name="upload_original_doc_fileName" use="upload_original_fileName"/>	
										<input type="file" name="upload_abstract_file" id="upload_abstract_file" 
											   onChange="fileChangeHandler(this)" class="form-control" 
											   allowedSize = '10' allowedFileTypes='pdf'
											   style="width:100%; height:66px;">
										<div class = "alert alert-success" style="display:block;">
										The FULL AWARD PAPER should be in the style of JOURNAL OF NEONATOLOGY <a href="https://journals.sagepub.com/author-instructions/NNT" target="_blank">(https://journals.sagepub.com/author-instructions/NNT)</a>
										</div>
										
										<div class = "alert alert-danger" callFor='alert'>Please choose a proper value.</div>
									</div> -->
									
									<div class="col-xs-12 form-group ">
										<div class="checkbox">
											<label class="select-lable" >Total Word Count</label>
											<span use="abstract_total_word_display" limit="<?=$cfg['ABSTRACT.FREE.PAPER.SESSION.WORD.LIMIT']?>">
												<span use="total_word_entered">0</span> / 
												<span use="total_word_limit"><?=$cfg['ABSTRACT.FREE.PAPER.SESSION.WORD.LIMIT']?></span>
												<span style="color: #D41000;">(Total <?=$cfg['ABSTRACT.FREE.PAPER.SESSION.WORD.LIMIT']?> are <?=$cfg['ABSTRACT.TOTAL.WORD.TYPE']?>s allowed.)</span>
											</span>
										</div>
									</div>
									
									<!--
									<div class="col-xs-12 form-group input-material" style="display:none;" use='videoTr' actAs='fieldContainer' relatedSubmissionType="ABSTRACT,CASEREPORT" relatedSubmissionSubType="VIDEO">
										<label for="user_mobile">Upload Video</label>									
										<input type="hidden" name="sessionId" use="sessionId" value="<?=session_id()?>" />											
										<input type="hidden" name="upload_temp_vdo_fileName" use="upload_temp_fileName" />
										<input type="hidden" name="upload_original_vdo_fileName" use="upload_original_fileName"/>	
										<input type="file" name="upload_abstract_video" id="upload_abstract_video" 
											   onChange="fileChangeHandler(this)" class="form-control" 
											   allowedSize = '40' allowedFileTypes='mp4'
											   style="width:100%; height:66px;">
										<div class = "alert alert-success" style="display:block;">
										Size not more than 40MB; File type:&nbsp; .mp4
										</div>
										<div class="progress" use="progressbar">
										  <div class="progress-bar" role="progressbar" aria-valuenow="70" aria-valuemin="0" aria-valuemax="100" style="width:0%" use="progress"></div>
										</div>
										<div class = "alert alert-danger" callFor='alert'>Please choose a proper value.</div>
									</div>
									-->
																	
									<div class=" col-xs-2 text-center pull-right">
										<!-- <button type="button" class="submit" use='nextButton'>Next</button> -->
										<button type="submit" class="submit" use='nextButton'>Submit</button>
										<span use="preSubmitProcess" style="display:none;">UPLOADING <img src="<?=_BASE_URL_?>images/loadinfo.net.gif" height="10px"/></span>
									</div>          
	        						<div class="clearfix"></div>
	                            </li> 
	                        </ul>
	                    </li>
						<?/*
						<li use="abstractNominations" style="display:none;" class="rightPanel_payment">
	                        <div class="link" use="rightAccordianL1TriggerDiv">NOMINATIONS</div>
	                        <ul class="submenu" style="display: none">
	                            <li>
									<div class="col-xs-12 form-group ">
										<div class="checkbox">
											<label class="select-lable" >Nominate this Abstract / Case Study</label>
											<div>
												<?
												$sqlAbstractAward			  =	array();
												$sqlAbstractAward['QUERY']    = "SELECT * FROM "._DB_AWARD_MASTER_." 
																				  WHERE `status` = ?";														
												$sqlAbstractAward['PARAM'][]  = array('FILD' => 'status', 'DATA' =>'A',  'TYP' => 's');
												$resultAbstractAward = $mycms->sql_select($sqlAbstractAward);
												foreach($resultAbstractAward  as $k=>$rowAbstractAward)
												{
												?>
												<label class="container-box" use='abstractAward1' topicId1="<?=$rowAbstractAward['related_topic_id']?>">
													<?=$rowAbstractAward['award_description']?>
													<input type="radio" 
														   use='award' name="award_request[<?=$rowAbstractAward['id']?>]" 
														   topicId="<?=$rowAbstractAward['related_topic_id']?>" 
														   value="Y" 
														   onclick="effectOnAwardClick(this);" >											  
													<span class="checkmark"></span>
												</label>
												<input type="radio" 
													   use='award' name="award_request[<?=$rowAbstractAward['id']?>]" 
													   topicId="<?=$rowAbstractAward['related_topic_id']?>" 
													   value="N" style="display:none;">
												<?
												}
												?>
											</div>																				
										</div>
									</div>
									 
									<div class=" col-xs-12 text-center pull-right">
										<button type="submit" class="submit" use='nextButton'>Submit</button>
									</div>      
	        						<div class="clearfix"></div>
	                            </li>
	                        </ul>
	                    </li>
	                 	*/?>
						
						<li use="registrationProcess" style="display:none; text-align:center;" class="rightPanel_payment">
							<img src="<?=_BASE_URL_?>images/PaymentPreloader.gif"/>
						</li>
	                </ul>      
	            </div>
	       	</div>
       	<? } ?>
	   	</form>
		
		<div use="coAuthorContainer" style="display:none;" class="com-country-state">
			<div class="col-xs-12 form-group input-material" style="margin-bottom: -20px;">&nbsp;</div>
			<h4>CO-AUTHOR <span use='coAuthorSrl'></span></h4>
			<div class="col-xs-6 form-group input-material" actAs='fieldContainer'>
				<label for="user_email_id">Name</label>
				<input type="hidden" name="abstract_coauthor_id[]" defaultName="abstract_coauthor_id"  usefor="id"/>
				<input type="text" class="form-control" style="text-transform:uppercase;" 
					   name="abstract_coauthor_name[]" defaultName="abstract_coauthor_name"  usefor="name"
					   value="" required>
				<div class = "alert alert-danger" callFor='alert'>Please enter a proper value.</div>
			</div>
			
			<div class="col-xs-6 form-group input-material" actAs='fieldContainer'>
				<label for="user_mobile">Mobile</label>
				<input type="text" class="form-control" style="text-transform:uppercase;" 
					   name="abstract_coauthor_phone_no[]" defaultName="abstract_coauthor_phone_no" usefor="mobile" required onkeypress="return isNumber(event)">
				<div class = "alert alert-danger" callFor='alert'>Please enter a proper value.</div>
			</div>
											
			<div class="col-xs-6 form-group input-material" actAs='fieldContainer'>
				<label for="user_mobile">Country</label>
				<select name="abstract_coauthor_country[]" defaultName="abstract_coauthor_country" usefor="country"
						class="form-control" style="text-transform:uppercase;padding: 6px 10px;height: 43px;" 
						operationMode="countryControl"
						onChange="generateStateOptionList(this);">
					<option value="">-- Select Country --</option>
					<?php
					$sqlFetchCountry			 =	array();
					$sqlFetchCountry['QUERY']    = "SELECT * FROM "._DB_COMN_COUNTRY_." 
													 WHERE `status` = ? 
												  ORDER BY `country_name` ASC";
					$sqlFetchCountry['PARAM'][]   = array('FILD' => 'status',  	   'DATA' =>'A',  'TYP' => 's');							
					$resultFetchCountry = $mycms->sql_select($sqlFetchCountry);
					if($resultFetchCountry)
					{
						foreach($resultFetchCountry as $keyCountry=>$rowFetchCountry)
						{
					?>
					<option value="<?=$rowFetchCountry['country_id']?>"><?=$rowFetchCountry['country_name']?></option>
					<?php
						}
					}
					?>
				</select>
				<div class = "alert alert-danger" callFor='alert'>Please select a proper option.</div>
			</div>
			<div class="col-xs-6 form-group input-material" actAs='fieldContainer'>
				<label for="user_mobile">State</label>
				<select name="abstract_coauthor_state[]" defaultName="abstract_coauthor_state" usefor="state"
						class="form-control" style="text-transform:uppercase;padding: 6px 10px;height: 43px;" 
						operationMode="stateControl">
					<option value="">-- Select Country First --</option>
				</select>
				<div class = "alert alert-danger" callFor='alert'>Please select a proper option.</div>
			</div>
			
			<div class="col-xs-6 form-group input-material" actAs='fieldContainer'>
				<label for="user_first_name">Institute Name</label>
				<input type="text" class="form-control institute" style="text-transform:uppercase;" 
					   name="abstract_coauthor_institute_name[]" defaultName="abstract_coauthor_institute_name" usefor="instituteName"
					   value="" required>
				<div class = "alert alert-danger" callFor='alert'>Please enter a proper value.</div>
			</div>
			<div class="col-xs-6 form-group input-material" actAs='fieldContainer'>
				<label for="user_last_name">Department</label>
				<input type="text" class="form-control department" style="text-transform:uppercase;"  
					   name="abstract_coauthor_department[]" defaultName="abstract_coauthor_department" usefor="department"
					   value="" required>
				<div class = "alert alert-danger" callFor='alert'>Please enter a proper value.</div>
			</div> 
			<!--
			<div class="col-xs-12 form-group">
				<div class="checkbox dis_abled">
					<div>
						<label class="container-box" style="float:left; margin-right:20px;">will be presenting
						  <input type="checkbox" name="willBePresenter" use="willBePresenter" 
								 onclick="setAsPresenter(this)">
						  <span class="checkmark"></span>
						</label>
						&nbsp;
					</div>																				
				</div>
			</div>
			-->
		</div> 
		
		<div id="uploadModal" class="modal fade" role="dialog">
		  <div class="modal-dialog">
			<!-- Modal content-->
			<form name="registrationCheckoutrForm" id="registrationCheckoutrForm" method="post">
				<div class="modal-content">
					<div class="modal-header">
						<div class="log"><h3>File Uploader</h3></div>
					</div>
					
					<div class="modal_subHead"><h2><span>Uploading File</span></h2></div>
					
					<div class="col-xs-10 profileright-section">							
						<div class="login-user" style="margin-top: 25px;">
							<img src="<?=_BASE_URL_?>images/PaymentPreloader.gif"/>
						</div>	
					</div>	
					<div class="modal-footer"></div>
				</div>
			</form>
		  </div>
		</div>
<?
		if(!$operate)
		{
?>
		<div id="expiredModal" class="modal fade" role="dialog">
		  <div class="modal-dialog">
			<!-- Modal content-->
				<div class="modal-content">
					<div class="modal-header">
						<div class="log"><h3>SUBMISSION OPENING SOON</h3></div>
					</div>
					
					<div class="modal_subHead"><h2><span>Abstract / Free Paper Submission Opening Soon</span></h2></div>
					
					<div class="col-xs-10 profileright-section">							
						<div class="login-user" style="margin-top: 25px;">
							<button type="button" use='submitBtn' onClick="window.location.href='profile.php';">Back</button>
						</div>	
					</div>	
					<div class="modal-footer"></div>
				</div>
		  </div>
		</div>
		
		<script>
		$(document).ready(function(){
			$('#expiredModal').modal({
				backdrop: 'static',
				keyboard: false,
				show	: true
			});
		});	
		</script>
<?
		}
		
		if($delegateId == '' && $operate)
		{
?>		
	  	<div id="emailModal" class="modal fade" role="dialog">
		  <div class="modal-dialog">
			<!-- Modal content-->
			<form name="registrationCheckoutrForm" id="registrationCheckoutrForm" method="post">
				<div class="modal-content">
					<div class="modal-header">
						<div class="log"><h3>Welcome to Abstract Submission</h3></div>

					</div>
					
					<div class="modal_subHead"><h2><span>Please enter your email</span></h2></div>
					
					<div class="col-xs-10 profileright-section">							
						<div class="login-user" style="margin-top: 25px;" actAs='fieldContainer'>
							<h4>
								<input type="email" 
									   name="user_email_id" id="user_email_id" value="" 
									   style="text-transform:lowercase;"/>
							</h4>
							<div class = "alert alert-danger" callFor='alert'>Please enter a proper value.</div>
						</div>		
						<div class="bttn" style="margin-top: 25px;" use='submitBlock'>
							<button type="submit" use='submitBtn'>Submit</button>
							<a href="<?=_BASE_URL_?>">Back</a>
							<img src="<?=_BASE_URL_?>images/loadinfo.net.gif" use='loader' alt="processing" style="width: 20px; display:none;">
						</div>
					</div>	
					<div class="modal-footer"></div>
				</div>
			</form>
		  </div>
		</div>
		
		<div id="loginModal" class="modal fade" role="dialog">
		  <div class="modal-dialog">
			<!-- Modal content-->
			<form name="loginForm" id="loginForm" method="post" action="abstract.user.entrypoint.process.php">
				<input type="hidden" name="act" value="enter" />
				<div class="modal-content">
					<div class="modal-header">
						<div class="log"><h3>YOU ARE REGISTERED</h3></div>
					</div>
					
					<div class="modal_subHead"><h2><span>Your Unique Sequence is sent to your Registered Mail ID.</span></h2></div>
					
					<div class="col-xs-12 profileright-section">							
						<div class="login-user" style="margin-top: 25px;"><h4><input type="email" name="email" id="email" value="" style="text-transform:lowercase; border:0px;" readonly="" /></h4></div>		
						<div class="login-user" style="margin-top: 5px;"><h4><input type="text" name="user_otp" id="user_otp" value="#" required /></h4></div>			   
						<div class = "alert alert-danger" callFor='alert'>OTP does not match.</div>
						<div class="bttn" style="margin-top: 25px;"><button type="submit" >Proceed</button></div>
					</div>				
					<div class="modal-footer"></div>
				</div>
			</form>
		  </div>
		</div>		
		
		<div id="registerModal" class="modal fade" role="dialog">
		  <div class="modal-dialog">
			<!-- Modal content-->
			<?
				// REGISTRATION RELATED OPERATION
				$registrationClassificationId   = $mycms->getSession('CLSF_ID_FRONT');						
				$registrationCutoffId  	        = $mycms->getSession('CUTOFF_ID_FRONT');
				$registrationMode	            = $mycms->getSession('REGISTRATION_MODE');
			?>
			<form name="absRegisterForm" id="absRegisterForm" method="post" action="<?=_BASE_URL_?>abstract.user.entrypoint.process.php" enctype="multipart/form-data">
				<input type="hidden" name="act" value="step1" />
				<input type="hidden" name="reg_area" value="FRONT" />
				<input type="hidden" name="otp_id" id="otp_id" value=""/>
				<input type="hidden" name="registration_request" id="registration_request" value="ABSTRACT" />
				<input type="hidden" name="registration_cutoff" id="registration_cutoff" value="<?=$registrationCutoffId?>" />
				<input type="hidden" name="registration_classification_id[]" id="registration_classification_id" value="<?=$registrationClassificationId?>" />
				<input type="hidden" name="registrationMode" id="registrationMode" value="<?=$registrationMode?>" />
				<div class="modal-content">
					<div class="modal-header">
						<div class="log"><h3>YOUR PROFILE</h3></div>
					</div>
					
					<div class="col-xs-12 profileright-section">
						<div class="login-user" style="margin-top: 25px;">							
							<div class="col-xs-6 form-group input-material" actAs='fieldContainer'>
								<label for="user_email_id">E-mail</label>
								<input type="email" style="text-transform:lowercase;" class="form-control" name="user_email_id"  id="user_email_id" readonly="" required>
								<div class = "alert alert-danger" callFor='alert'>Please enter a proper value.</div>
							</div>
							
							<div class="col-xs-4 form-group input-material" actAs='fieldContainer'>
								<label for="user_mobile">Mobile</label>
								<input type="text" class="form-control" name="user_mobile" id="user_mobile" onkeypress="return isNumber(event)" required>
								<div class = "alert alert-danger" callFor='alert'>Please enter a proper value.</div>
							</div>
							<div class=" col-xs-2 form-group input-material" use="mobileProcessing">
								<img src="<?=_BASE_URL_?>images/loadinfo.net.gif" use='loader' alt="processing" style="width: 20px; display:none;">
								<span class="alert alert-success" use='mobileAvailability' state='available' style="display:none;">Available</span>
								<span class="alert alert-danger" use='mobileAvailability' state='used' style="display:none;">Already in use</span>
								<input type="hidden" name="user_mobile_validated" id="user_mobile_validated" value="N" />
							</div>
							
							<div class="col-xs-12 form-group" actAs='fieldContainer'>
								<div class="checkbox">
									<label class="select-lable" >Title</label>
									<div>
										<label class="container-box" style="float:left; margin-right:20px;">Dr
										  <input type="radio" name="user_initial_title" value="Dr" required>
										  <span class="checkmark"></span>
										</label>
										<label class="container-box" style="float:left; margin-right:20px;">Prof
										  <input type="radio" name="user_initial_title" value="Prof" >
										  <span class="checkmark"></span>
										</label>
										<label class="container-box" style="float:left; margin-right:20px;">Mr
										  <input type="radio" name="user_initial_title" value="Mr" >
										  <span class="checkmark"></span>
										</label>
										<label class="container-box" style="float:left; margin-right:20px;">Ms
										  <input type="radio" name="user_initial_title" value="Ms" >
										  <span class="checkmark"></span>
										</label>
										<label class="container-box" style="float:left; margin-right:20px;">Mrs
										  <input type="radio" name="user_initial_title" value="Mrs" >
										  <span class="checkmark"></span>
										</label>
										&nbsp;
									</div>
								</div>
								<div class = "alert alert-danger" callFor='alert'>Please select a proper option.</div>
							</div>
							<div class="col-xs-6 form-group input-material" actAs='fieldContainer'>
								<label for="user_first_name">First Name</label>
								<input type="text" class="form-control" name="user_first_name"  id="user_first_name" style="text-transform:uppercase;" required>
								<div class = "alert alert-danger" callFor='alert'>Please enter a proper value.</div>
							</div>
							<div class="col-xs-6 form-group input-material" actAs='fieldContainer'>
								<label for="user_last_name">Last Name</label>
								<input type="text" class="form-control" name="user_last_name"  id="user_last_name" style="text-transform:uppercase;" required>
								<div class = "alert alert-danger" callFor='alert'>Please enter a proper value.</div>
							</div>
														
							<div class="col-xs-6 form-group" actAs='fieldContainer'>
								<label class="select-lable">Country</label>
								<select class="form-control select" name="user_country" id="user_country" forType="country" style="text-transform:uppercase;"  required>
									<option value="">-- Select Country --</option>
									<?php
									$sqlFetchCountry   = array();
									$sqlFetchCountry['QUERY']    = "SELECT * FROM "._DB_COMN_COUNTRY_." 
																	   WHERE `status` =? 
																	ORDER BY `country_name` ASC";
																	
									$sqlFetchCountry['PARAM'][]   = array('FILD' => 'status', 'DATA' =>'A', 'TYP' => 's');	
									
									$resultFetchCountry = $mycms->sql_select($sqlFetchCountry);
									if($resultFetchCountry)
									{
										foreach($resultFetchCountry as $keyCountry=>$rowFetchCountry)
										{
									?>
											<option value="<?=$rowFetchCountry['country_id']?>"><?=$rowFetchCountry['country_name']?></option>
									<?php
										}
									}
									?>
								</select>
								<div class = "alert alert-danger" callFor='alert'>Please select a proper option.</div>
							</div>
							<div class="col-xs-6 form-group" actAs='fieldContainer'>
								<label class="select-lable">State</label>
								<select class="form-control select" name="user_state" id="user_state" forType="state" style="text-transform:uppercase;" required>
									<option value="">-- Select Country First --</option>
								</select>
								<div class = "alert alert-danger" callFor='alert'>Please select a proper option.</div>
							</div>
							<div class="col-xs-6 form-group input-material" actAs='fieldContainer'>
								<label for="user_city">City</label>
								<input type="text" class="form-control" name="user_city" id="user_city" value="" style="text-transform:uppercase;" required>
								<div class = "alert alert-danger" callFor='alert'>Please enter a proper value.</div>
							</div>
							<div class="col-xs-6 form-group input-material" actAs='fieldContainer'>
								<label for="user_postal_code">Postal Code</label>
								<input type="text" class="form-control" name="user_postal_code" id="user_postal_code" onkeypress="return isNumber(event)" required>
								<div class = "alert alert-danger" callFor='alert'>Please enter a proper value.</div>
							</div>
							
							<div class="clearfix"></div>
						</div>
						<div class="bttn" style="margin-top: 25px;" use='submitBlock'>
							<button type="submit" use='submitBtn'>Submit</button>
							<img src="<?=_BASE_URL_?>images/loadinfo.net.gif" use='loader' alt="processing" style="width: 20px; display:none;">
						</div>	
					</div>
												
					<div class="modal-footer"></div>
				</div>
			</form>
		  </div>
		</div>
		
		<script>

		$(document).ready(function(){
			$('#emailModal').modal({
				backdrop: 'static',
				keyboard: false,
				show	: true
			});
			
			$('#loginModal').modal({
				backdrop: 'static',
				keyboard: false,
				show	: false
			});
			
			$('#registerModal').modal({
				backdrop: 'static',
				keyboard: false,
				show	: false
			});
			
			$('#registerModal').find("input[id=user_mobile]").blur(function(){
				checkMobileNo(this);
			});	

			

			
			$("form[name=registrationCheckoutrForm]").submit(function(e){			
				e.preventDefault();																
				
				var formObj 			= $(this);
				var parent 				= $(formObj).parent().closest("div[role=dialog]");
				var emailIdObj 			= $(formObj).find("#user_email_id");
				var emailId 			= $.trim($(emailIdObj).val());
				var submitObjContainer  = $(parent).find("div[use=submitBlock]");
				
				if(emailId != '')
				{
					var filter = /^([\w-\.]+)@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.)|(([\w-]+\.)+))([a-zA-Z]{2,4}|[0-9]{1,3})(\]?)$/;
					if (filter.test(emailId)) 
					{
						$(submitObjContainer).find("button").hide();
						$(submitObjContainer).find("img[use=loader]").show();
						
						console.log(jsBASE_URL+'returnData.process.php?act=getEmailValidation&email='+emailId);
									
						setTimeout(function(){
						   $.ajax({
									type: "POST",
									url: jsBASE_URL+'returnData.process.php',
									data: 'act=getEmailValidationStatusAbstract&email='+emailId,
									dataType: 'json',
									async: false,
									success:function(JSONObject)
									{
										console.log(JSONObject);
										
										if (JSONObject.STATUS == 'NOT_AVAILABLE')
										{
											var registerDiv = $("#registerModal");
											try
											{
												var JSONObjectData = JSONObject.DATA;																
												$(registerDiv).find('#email_div').html('');
												$(registerDiv).find('#user_first_name').val(JSONObjectData.FIRST_NAME);
												$(registerDiv).find('#user_middle_name').val(JSONObjectData.MIDDLE_NAME);
												$(registerDiv).find('#user_last_name').val(JSONObjectData.LAST_NAME);
												$(registerDiv).find('#user_mobile').val(JSONObjectData.MOBILE_NO);
												$(registerDiv).find('#user_usd_code').val(JSONObjectData.MOBILE_ISD_CODE);
												checkMobileValidation(JSONObjectData.MOBILE_NO,jBaseUrl)
												$(registerDiv).find('#user_phone_no').val(JSONObjectData.PHONE_NO);
												$(registerDiv).find('#user_address').val(JSONObjectData.ADDRESS);
												$(registerDiv).find('#user_city').val(JSONObjectData.CITY);
												$(registerDiv).find('#user_postal_code').val(JSONObjectData.PIN_CODE);
												
												$(registerDiv).find('#user_country').val(JSONObjectData.COUNTRY_ID);											
												$(registerDiv).find('#user_country').trigger("change");
												$(registerDiv).find('#user_state').val(JSONObjectData.STATE_ID);
											} 
											catch (e)
											{
												$(registerDiv).find('#email_div').html('');
												$(registerDiv).find('input[type=text]').val('');
												$(registerDiv).find("input[type=checkbox]").prop("checked",false);
												$(registerDiv).find("input[type=checkbox]").prop("checked",false);
												$(registerDiv).find('#user_country').val('');											
												$(registerDiv).find('#user_country').trigger("change");
											}	
												
											$(registerDiv).find("#user_email_id").val(emailId);	
																						
											var regClassIdVal = $.trim($(registerDiv).find("#regClassId").val());
											
											$('#emailModal').modal('hide');
											$('#registerModal').modal('show');
										}
										else
										{											
											$.ajax({
												type: "POST",
												url: jsBASE_URL+'abstract.user.entrypoint.process.php',
												data: 'act=triggerOTPSMS&id='+JSONObject.ID,
												dataType: 'text',
												async: false,
												success:function(dataObj)
												{
													$("#loginModal").find("#email").val(emailId);	
													$("#loginModal").find("#user_otp").attr("passkey", JSONObject.UNIQUE_SEQUENCE);
													$('#emailModal').modal('hide');
													$('#loginModal').modal('show');
												}
											});
										}
										
										$(submitObjContainer).find("img[use=loaderImg]").hide();
									}
								});
						 },500);
				 	}
					else
					{
						popoverAlert(emailIdObj);					 	
					}
				}
				else
				{
					popoverAlert(emailIdObj);					 	
				}						
			});
			
			$("form[name=loginForm]").submit(function(e){	
				var formObj 			= $(this);
				var OTPObj 				= $(formObj).find("#user_otp");
				var otpVal 				= $.trim($(OTPObj).val());
				var otpPass 			= $.trim($(OTPObj).attr("passkey"));
				
				$(formObj).find("div[callFor=alert]").hide();
				
				if(otpVal != otpPass)
				{
					$(formObj).find("div[callFor=alert]").show();
					e.preventDefault();		
					return false;	
				}
				return true;								
			});
			
			$("form[name=absRegisterForm]").submit(function(e){			
				e.preventDefault();																
				
				var formObj 			= $(this);
				var parent 				= $(formObj).parent().closest("div[role=dialog]");
				var returnVal 			= true;
				var submitObjContainer  = $(parent).find("div[use=submitBlock]");
				
				//console.clear();
				console.log('initial>>'+returnVal);
				
				$.each($(parent).find("input[type=text],input[type=email],input[type=tel],input[type=number],input[type=date],textarea"),function(){
					var attr = $(this).attr('required');
					if (typeof attr !== typeof undefined && attr !== false) {
						console.log('hasReq>>'+$(this).attr('name'));
						if($.trim($(this).val())=='')
						{
							$(this).focus();
							popoverAlert(this);
							returnVal = false;
							return false;
						}
					}
				});	
				console.log('text>>'+returnVal);
				if(!returnVal) return false;
				
				$.each($(parent).find("select"),function(){
					var attr = $(this).attr('required');
					if (typeof attr !== typeof undefined && attr !== false) {
						console.log('hasReq>>'+$(this).attr('name'));																
						if($.trim($(this).val())=='')
						{
							$(this).focus();
							popoverAlert(this);
							returnVal = false;
							return false;
						}
					}
				});	
				console.log('select>>'+returnVal);	
				if(!returnVal) return false;		
					
				$.each($(parent).find("input[type=radio]"),function(){
					var hasRequired = false;
					var attr = $(this).attr('required');
					if (typeof attr !== typeof undefined && attr !== false) 
					{
						console.log('hasReq>>'+$(this).attr('name'));
						hasRequired = true;
					}
					if(hasRequired)
					{
						var name = $(this).attr("name");
						if($("input[type=radio][name='"+name+"']:checked").length == 0)
						{
							popoverAlert(this);
							returnVal = false;
							return false;
						}
					}
				});
				console.log('radio>>'+returnVal);	
				if(!returnVal) return false;
				
				/*if($(parent).find("input[id=user_mobile_validated]").val()!="Y")
				{
					popoverAlert($(parent).find("input[name=user_mobile]"));
					return false;
				}*/
				
				$(submitObjContainer).find("button").hide();
				$(submitObjContainer).find("img[use=loader]").show();
				
				console.log($(formObj).attr('action'));
				
				setTimeout(function(){
				   $.ajax({
						type: "POST",
						url: $(formObj).attr('action'),
						data: $(formObj).serialize(),
						dataType: 'json',
						async: false,
						success:function(JSONObject)
						{
							console.log(JSONObject);
							window.location.href=JSONObject.REDIRECT;
						}
					});
				},500);
			});
		});		
		
		function checkMobileNo(mobileObj)
		{
			popDownAlert();
			
			var mobile = $(mobileObj).val();
			var parent = $('#registerModal');
			
			$(parent).find("div[use=mobileProcessing]").find("span[use=mobileAvailability]").hide();
			$(parent).find("div[use=mobileProcessing]").find("img[use=loader]").show();
			$(parent).find("input[id=user_mobile_validated]").val("N");
			
			if(mobile!="")
			{
				if(isNaN(mobile) || mobile.toString().length != 10)
				{
					popoverAlert(mobileObj);					 	
					$(parent).find("div[use=mobileProcessing]").find("span[use=mobileAvailability]").hide();	
					$(parent).find("div[use=mobileProcessing]").find("img[use=loader]").hide();				
				}
				else
				{
					console.log(jsBASE_URL+'returnData.process.php?act=getMobileValidation&mobile='+mobile);
					setTimeout(function(){
						$.ajax({
							type: "POST",
							url: jsBASE_URL+'returnData.process.php',
							data: 'act=getMobileValidation&mobile='+mobile,
							dataType: 'text',
							async: false,
							success:function(returnMessage)
							{
								$(parent).find("div[use=mobileProcessing]").find("span[use=mobileAvailability]").hide();	
								$(parent).find("div[use=mobileProcessing]").find("img[use=loader]").hide();				
								returnMessage = returnMessage.trim();
								if(returnMessage == 'IN_USE')
								{
									popoverAlert(mobileObj,"Mobile no. is already in use.");					 	
									$(parent).find("input[id=user_mobile_validated]").val("N");
								}
								else
								{
									$(parent).find("div[use=mobileProcessing]").find("span[use=mobileAvailability][state=available]").show();
									$(parent).find("div[use=mobileProcessing]").find("input[id=user_mobile_validated]").val("Y");
									console.log('>>'+$(parent).find("div[use=mobileProcessing]").find("input[name=user_mobile_validated]").val());
								}
							}
						});
					},500);
				}
			}	
			else
			{
				popoverAlert(mobileObj);	
				$(parent).find("div[use=mobileProcessing]").find("img[use=loader]").hide();								 	
			}
		}
		</script>
<?
		}
?>
		<script>
			$(document).ready(function(){
				setInterval( function(){
					var windowHieght = $( document ).height();
					$(".left-container-box").css('min-height',windowHieght+'px');
				}, 1000);
			});

	$('#abstract_presenter_institute_name').keypress(function(event) {
	    
	    //var val = $(this).val().replace(/[^a-zA-Z]/g, '');
	    
	    var inputValue = event.which;
	    if (!(inputValue >= 65 && inputValue <= 90) && !(inputValue >= 97 && inputValue <= 122) && !(inputValue == 32)) { 
		      event.preventDefault(); 
		    }

	  });

	

	$('#abstract_presenter_department').keypress(function(event) {
	    
	    //var val = $(this).val().replace(/[^a-zA-Z]/g, '');
	    
	    var inputValue = event.which;
	    if (!(inputValue >= 65 && inputValue <= 90) && !(inputValue >= 97 && inputValue <= 122) && !(inputValue == 32)) { 
		      event.preventDefault(); 
		    }

	  });

	$('#abstract_title').on('keyup', function() {
	    console.log('abstarct='+this.value.length);
	    jQuery('#abstract_total_word_entered').text(this.value.length);
	});

	$('#abstract_author_city').keypress(function(event) {
	    
	    //var val = $(this).val().replace(/[^a-zA-Z]/g, '');
	    
	    var inputValue = event.which;
	    if (!(inputValue >= 65 && inputValue <= 90) && !(inputValue >= 97 && inputValue <= 122) && !(inputValue == 32)) { 
		      event.preventDefault(); 
		    }

	  });

	

	$('#abstract_author_institute_name').keypress(function(event) {
	    
	    //var val = $(this).val().replace(/[^a-zA-Z]/g, '');
	    
	    var inputValue = event.which;
	    if (!(inputValue >= 65 && inputValue <= 90) && !(inputValue >= 97 && inputValue <= 122) && !(inputValue == 32)) { 
		      event.preventDefault(); 
		    }

	  });

	

	$('#abstract_author_department').keypress(function(event) {
	    
	    //var val = $(this).val().replace(/[^a-zA-Z]/g, '');
	    
	    var inputValue = event.which;
	    if (!(inputValue >= 65 && inputValue <= 90) && !(inputValue >= 97 && inputValue <= 122) && !(inputValue == 32)) { 
		      event.preventDefault(); 
		    }

	  });

	$('#user_city').keypress(function(event) {
	    
	    //var val = $(this).val().replace(/[^a-zA-Z]/g, '');
	    
	    var inputValue = event.which;
	    if (!(inputValue >= 65 && inputValue <= 90) && !(inputValue >= 97 && inputValue <= 122) && !(inputValue == 32)) { 
		      event.preventDefault(); 
		    }

	  });


	function isNumber(evt)
	{
	    evt = (evt) ? evt : window.event;
	    var charCode = (evt.which) ? evt.which : evt.keyCode;
	    if (charCode > 31 && (charCode < 48 || charCode > 57)) {
	    return false;
	    }
	    return true;
	} 	
	</script>
	</body>
	
</html>
<?php
	
?>
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
	$operate		 = false;

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
			  action="<?=_BASE_URL_?>abstract_request.process.php" method="post" enctypeX="multipart/form-data"> 
		<input type="hidden" name="act" value="abstractUpdate" />
		<input type="hidden" name="applicantId" id="applicantId" value="<?=$delegateId?>" />
		<input type="hidden" name="report_data" id="report_data" value="Abstract" />		
        <div class="col-xs-4 left-container-box regTarrif_leftPanel" style="position:relative;">
            <div class="col-xs-4 logo">
                <img src="<?=$emailHeader?>" alt="logo" style="width: 100%;">              
            </div>
            <div class="col-xs-7 col-xs-offset-1 timer" style="padding: 0">
				<div>
                    <h5 class="cutoffNameTop">Edit</h5>
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
				$endDate		  	  = $cfg['ABSTRACT.EDIT.LASTDATE'] ;
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
		if( $cfg['ABSTRACT.EDIT.LASTDATE'] >= date('Y-m-d') && $_GET['abstract_id'] !== '' && $_GET['user_id'] !== '' )
		{
			
			//$resultAbstractType	  = false;
			$operate = true;
			if($delegateId != '')
			{
				$rowUserDetails   = getUserDetails($delegateId);
								
				$sql  			  = array();
				$sql['QUERY']     = " SELECT * 
										FROM "._DB_ABSTRACT_REQUEST_." 
									   WHERE `status` = ?
										 AND `applicant_id` = ? AND id = ?";
										 //AND `abstract_child_type` IN ('Oral','Poster')
										
				$sql['PARAM'][]   = array('FILD' => 'status',         'DATA' =>'A',          'TYP' => 's');
				$sql['PARAM'][]   = array('FILD' => 'applicant_id',   'DATA' => $delegateId?$delegateId:trim($_GET['user_id']) , 'TYP' => 's');
				$sql['PARAM'][]   = array('FILD' => 'id',   'DATA' =>trim($_GET['abstract_id']) , 'TYP' => 's');
				$resultAbstractType = $mycms->sql_select($sql);

				//echo '<pre>'; print_r($resultAbstractType);

			}
		}

		//print_r($cfg['ABSTRACT_SUBMISSION_CATEGORY']);

		$sqlAbstractTopic			  =	array();
								$sqlAbstractTopic['QUERY']    = "SELECT * FROM "._DB_ABSTRACT_TOPIC_CATEGORY_." 
																  WHERE `status` IN ('A')
															   ORDER BY `category` ASC";
								
		$resultAbstractTopic = $mycms->sql_select($sqlAbstractTopic);

		$sqlAbstractSubmission			  =	array();
		$sqlAbstractSubmission['QUERY']    = "SELECT * FROM "._DB_ABSTRACT_SUBMISSION_." 
										  WHERE `status` IN ('A') AND category='".$resultAbstractType[0]['abstract_cat']."'
									   ORDER BY `category` ASC";
		
		
		$resultAbstractSubmission = $mycms->sql_select($sqlAbstractSubmission);

		//echo '<pre>'; print_r($resultAbstractType[0]);
?>
			<? if($resultAbstractType && $operate) {?>	
				<ul use="leftAccordion" class="accordion">
	            	<li use='leftAccordionAbstractCategory'>
					 	<div class="link masterClass" use="accordianL1TriggerDiv" useSpec1="stage2" style="font-weight: 600; text-decoration: underline;">SUBMISSION CATEGORY</div>
						<ul class="submenu" style="display:block;">
							<? 
								//foreach ($cfg['ABSTRACT_SUBMISSION_CATEGORY'] as $key => $value) {
								foreach ($resultAbstractTopic as $key => $value) {
									?>
									<li use="abstractCategoryRadio">
										<a>
											<label class="container-box menu-container-box">
												<i class="itemTitle"><?=$value['category']?></i> 
												<input type="radio" name="abstract_category" value="<?=$value?>" 
													   relatedCategoryType="<?=$value?>" <?= $value['id'] == $resultAbstractType[0]['abstract_cat'] ? "checked" : "" ?> disabled readonly required/>									
												<span class="checkmark"></span>
											</label>
										</a>
									</li>
								<?	
								}
							?>
						</ul>					
					</li>
					<? //if(trim($resultAbstractType[0]['abstract_category']) === "Free Paper") {?>
					<li use='leftAccordionSubmissionType' class="abs-submission-type">
						<div class="link masterClass" use="accordianL1TriggerDiv" useSpec1="stage2" style="font-weight: 600; text-decoration: underline;"></div>
						<ul class="submenu" style="display:block;padding-left: 23px;">
							<?
							
								foreach ($resultAbstractSubmission as $key => $value) { 
								?>
									<li use="submissionTypeRadio">
										<a>
											<label class="container-box menu-container-box">  
												<i class="itemTitle"><?=$value['abstract_submission']?></i> 	
												<input type="radio" name="abstract_parent_type" value="<?=$value['id']?>" 
													   titleWordCountLimit="<?=$cfg['ABSTRACT.TITLE.WORD.LIMIT']?>" 
													   contentWordCountLimit="<?=$cfg['ABSTRACT.FREE.PAPER.SESSION.WORD.LIMIT']?>"
													   relatedSubmissionSubType=""   readonly
													   disabled="disabled" 
													   <?= strtoupper($value['id']) == trim($resultAbstractType[0]['abstract_parent_type']) ? "checked" : "" ?>
													   required />	
												<span class="checkmark"></span>
											</label>
										</a>
									</li>
								<?	
								}
							?>
						</ul>					
					</li>
					<!-- <li use='leftAccordionSubmissionType' class="abs-sub-submission-type">
						<div class="link conferenceRegistration" use="accordianL1TriggerDiv" useSpec1="stage1" style="font-weight: 600; text-decoration: underline;">PRESENTATION TYPE</div>
						<ul class="submenu" style="display:block;">
							<?
								foreach ($cfg['ABSTRACT_SUB_SUBMISSION_TYPE'] as $key => $value) { 
								?>
									<li use="submissionSubTypeRadio">
										<a>
											<label class="container-box menu-container-box">
												<i class="itemTitle"><?=$value?></i> 
												<input type="radio" name="abstract_child_type" value="<?=$value?>" 
													   relatedSubmissionType="" disabled  <?= strtoupper($value) == trim($resultAbstractType[0]['abstract_child_type']) ? "checked" : "" ?> readonly required/>									
												<span class="checkmark"></span>
											</label>
										</a>
									</li>
								<?
								}
							?>
						</ul>
					</li> -->
					<? //} ?>
	            </ul>
	        <? } ?>
            </div>
            
            <div class="col-xs-12 menu-links">                
               <!--  <p><a href="profile.php" target="_blank"><i class="fas fa-caret-right"></i>&nbsp&nbsp&nbsp Rules and Regulations</a></p> -->              
                <p class="menu-login"><a href="<?=_BASE_URL_?>profile.php">BACK TO MY PROFILE</a></p>
            </div> 
			
			<br/><br/>           
        </div>
        <? if($resultAbstractType && $operate){ ?>
        	<input type="hidden" name="abstract_id" value="<?=$resultAbstractType[0]['id']?>">
	        <div class="col-xs-8 right-container regTarrif_rightPanel">
	            <div class="col-xs-12 col-xs-offset-0" style="padding: 0; margin-top: 15px;">                
					<ul use="rightAccordion" class="accordion" style="padding:0 0 0  0px; margin: 0; ">
						<!-- Abstract Presenter Details Start -->
	                    <li use="abstractPresenterDetails" class="rightPanel_userDetails">
	                        <div class="link" use="rightAccordianL1TriggerDiv">ABSTRACT PRESENTER DETAILS</div>
	                        <ul class="submenu" style="display: block"  displayStatus="Y">
	                            <li>
									<div class="col-xs-12 form-group input-material">
										<label for="user_mobile">Email-id</label>
										<input type="text" name="abstract_presenter_email" id="abstract_presenter_email" class="form-control" style="text-transform:lowercase;" 
											   usefor="email" value="<?=$rowUserDetails['user_email_id']?>" 
											   readonly="true" disabled>
									</div>
									<div class="col-xs-12 form-group input-material">
										<label for="user_email_id">Name</label>
										<input type="text" name="abstract_presenter_name" id="abstract_presenter_name" class="form-control" style="text-transform:uppercase;" 
											   usefor="name" value="<?=$rowUserDetails['user_full_name']?>"
											   readonly="true" disabled>
									</div>
																	
									<div class="col-xs-6 form-group input-material">
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
											   readonly="true" disabled>
									</div>
									<div class="col-xs-6 form-group input-material">
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
											   readonly="true" disabled>
									</div>
									<div class="col-xs-6 form-group input-material">
										<label for="user_mobile">City</label>
										<input type="text" name="abstract_presenter_city" id="abstract_presenter_city" class="form-control" style="text-transform:uppercase;" 
											   usefor="country-display" value="<?=strtoupper($rowUserDetails['user_city'])?>" 
											   readonly="true" disabled>
									</div>
									
									<div class="col-xs-6 form-group input-material">
										<label for="user_mobile">Mobile</label>
										<input type="text" name="abstract_presenter_mobile" id="abstract_presenter_mobile" class="form-control" style="text-transform:uppercase;" 
											   usefor="mobile" value="<?=$rowUserDetails['user_mobile_no']?>" 
											   readonly="true" disabled>
									</div>
									
	                        		<div class="col-xs-6 form-group input-material" actAs='fieldContainer'>
										<label for="user_first_name">Institute Name</label>
										<input type="text" class="form-control institute" style="text-transform:uppercase;" 
											   name="abstract_presenter_institute_name"  id="abstract_presenter_institute_name" usefor="instituteName"
											   value="<?=$rowUserDetails['user_institute_name']?>" 
											   disabled="disabled" required readonly>
										<div class = "alert alert-danger" callFor='alert'>Please enter a proper value.</div>
									</div>
									<div class="col-xs-6 form-group input-material" actAs='fieldContainer'>
										<label for="user_last_name">Department</label>
										<input type="text" class="form-control" style="text-transform:uppercase;"  
											   name="abstract_presenter_department"  id="abstract_presenter_department" usefor="department"
											   value="<?=$rowUserDetails['user_department']?>"
											   disabled="disabled" required readonly> 
										<div class = "alert alert-danger" callFor='alert'>Please enter a proper value.</div>
									</div>
									<!-- 
									<div class=" col-xs-2 text-center pull-right">
										<button type="button" class="submit" use='nextButton' style="display:none;">Next</button>
									</div> -->

	        						<div class="clearfix"></div>
	                            </li>
	                        </ul>
	                    </li>
						<!-- Abstract Presenter Details End -->
						<!-- Author Start -->
						
						<li use="abstractAuthorDetails" class="rightPanel_userDetails">
	                        <div class="link" use="rightAccordianL1TriggerDiv">AUTHOR</div>
	                        <ul class="submenu" displayStatus="Y">
	                            <li>
									<div class="com-country-state">
										<div class="col-xs-12 form-group input-material" actAs='fieldContainer'>
											<label for="user_email_id">Name</label>
											<input type="text" name="abstract_author_name" id="abstract_author_name" class="form-control" style="text-transform:uppercase;" 
												   usefor="name" value="<?=$resultAbstractType[0]['abstract_author_name']?>" disabled readonly required>
										</div>
										
										<div class="col-xs-6 form-group input-material" actAs='fieldContainer'>
											<label for="abstract_author_country">Country</label>
											<?php
												$sqlFetchCountry			 =	array();
												$sqlFetchCountry['QUERY']    = "SELECT * FROM "._DB_COMN_COUNTRY_." 
																			   WHERE `status` = ?
																				 AND `country_id` = ?";
																				 
												$sqlFetchCountry['PARAM'][]   = array('FILD' => 'status',  	   'DATA' =>'A',  'TYP' => 's');
												$sqlFetchCountry['PARAM'][]   = array('FILD' => 'country_id',  'DATA' =>$resultAbstractType[0]['abstract_author_country_id'],  'TYP' => 's');								 
																			
												$resultFetchCountry = $mycms->sql_select($sqlFetchCountry);
												$rowCountry            = $resultFetchCountry[0];
												?>
											<input type="text" name="abstract_author_country" id="abstract_author_country" class="form-control" style="text-transform:uppercase;" 
													   usefor="country-display" value="<?=$rowCountry['country_name']?>" 
													   readonly="true" disabled>
										</div>
										
										<div class="col-xs-6 form-group input-material" actAs='fieldContainer'>
											<label for="abstract_author_state_id">State</label>
											<?
												$sqlFetchState			 =	array();
												$sqlFetchState['QUERY']    = "SELECT * FROM "._DB_COMN_STATE_." 
																			   WHERE `status` = ? 
																				 AND `st_id` = ?";
												$sqlFetchState['PARAM'][]   = array('FILD' => 'status',  	   'DATA' =>'A',  'TYP' => 's');
												$sqlFetchState['PARAM'][]   = array('FILD' => 'st_id',    'DATA' =>$resultAbstractType[0]['abstract_author_state_id'],  'TYP' => 's');
																			
												$resultFetchState = $mycms->sql_select($sqlFetchState);
												$rowState           = $resultFetchState[0];
											?>
										<input type="text" name="abstract_author_state" id="abstract_author_state" class="form-control" style="text-transform:uppercase;" 
											   usefor="country-display" value="<?=strtoupper($rowState['state_name'])?>" readonly disabled>
										</div>
										<div class="col-xs-6 form-group input-material" actAs='fieldContainer'>
											<label for="user_city">City</label>
											<input type="text" name="abstract_author_city" id="abstract_author_city" class="form-control" style="text-transform:lowercase;" 
												   usefor="city" value="<?=$resultAbstractType[0]['abstract_author_city']?>" disabled readonly required>
										</div>
										<div class="col-xs-6 form-group input-material" actAs='fieldContainer'>
											<label for="user_mobile">Mobile</label>
											<input type="text" name="abstract_author_phone_no" id="abstract_author_phone_no" class="form-control" style="text-transform:uppercase;" usefor="mobile" value="<?=$resultAbstractType[0]['abstract_author_phone_no']?>" readonly disabled>
										</div>
										
										<div class="col-xs-6 form-group input-material" actAs='fieldContainer'>
											<label for="user_first_name">Institute Name</label>
											<input type="text" class="form-control institute" style="text-transform:uppercase;" 
												   name="abstract_author_institute_name"  id="abstract_author_institute_name" usefor="instituteName"
												   value="<?=$resultAbstractType[0]['abstract_author_institute_name']?>" readonly disabled>
										</div>
										<div class="col-xs-6 form-group input-material" actAs='fieldContainer'>
											<label for="user_last_name">Department</label>
											<input type="text" class="form-control" style="text-transform:uppercase;"  
												   name="abstract_author_department"  id="abstract_author_department" usefor="department"
												   value="<?=$resultAbstractType[0]['abstract_author_department']?>" disabled readonly required>
										</div>
										<!-- will be presenting -->
										<div class="col-xs-12 form-group">
											<div class="checkbox">
												<div>
													<label class="container-box" style="float:left; margin-right:20px;">Author and Presenter is the same person
													  <input type="checkbox" name="willBePresenter" use="willBePresenter" <?=$resultAbstractType[0]['isPresenter']=='Y'?"checked":""?> readonly disabled>
													  <span class="checkmark"></span>
													</label>
													&nbsp;
												</div>																				
											</div>
										</div>
										
										<!-- <div class=" col-xs-2 text-center pull-right">
											<button type="button" class="submit" use='nextButton' style="display:none;">Next</button>
										</div>   --> 
													 
										<div class="clearfix"></div>
									</div>
	                            </li>
	                        </ul>
	                    </li>
						<!-- Author End -->
						<?php 			
							$sqlCoAuthorDtls  = array();				
							$sqlCoAuthorDtls['QUERY']  =  "SELECT * 
															FROM  "._DB_ABSTRACT_COAUTHOR_."
														   WHERE `abstract_id` = '".$resultAbstractType[0]['id']."' 
															 AND `status` = 'A'";												
							$coAuthorDetails  =	$mycms->sql_select($sqlCoAuthorDtls);
							$coAuthor_counter =	$mycms->sql_numrows($coAuthorDetails);	
						?>
						<li use="abstractCoAuthors" class="rightPanel_accompany">						
	                        <div class="link" use="rightAccordianL1TriggerDiv">CO-AUTHORS</div>
	                        <ul class="submenu">
	                            <li>
									<div class="col-xs-12 form-group ">
										<div class="checkbox">
											<label class="select-lable" >Number of Co-Authors(s)</label>
											<div>
												<label class="container-box" style="float:left; margin-right:20px;">None
												  <input type="radio" name="coAuthorCount" use="coAuthorCountSelect" value="0" <?=$coAuthor_counter == 0 ? "checked" : "checked"?> disabled readonly required>
												  <span class="checkmark"></span>
												</label>
												<label class="container-box" style="float:left; margin-right:20px;">One
												  <input type="radio" name="coAuthorCount" use="coAuthorCountSelect" value="1" <?=$coAuthor_counter == 1 ? "checked" : ""?>  required>
												  <span class="checkmark"></span>
												</label>
												<label class="container-box" style="float:left; margin-right:20px;">Two
												  <input type="radio" name="coAuthorCount" use="coAuthorCountSelect" value="2" <?=$coAuthor_counter == 2 ? "checked" : ""?>  required>
												  <span class="checkmark"></span>
												</label>
												<label class="container-box" style="float:left; margin-right:20px;">Three
												  <input type="radio" name="coAuthorCount" use="coAuthorCountSelect" value="3" <?=$coAuthor_counter == 3 ? "checked" : ""?> required>
												  <span class="checkmark"></span>
												</label>
												<label class="container-box" style="float:left; margin-right:20px;">Four
												  <input type="radio" name="coAuthorCount" use="coAuthorCountSelect" value="4" <?=$coAuthor_counter == 4 ? "checked" : ""?>  required>
												  <span class="checkmark"></span>
												</label>
												&nbsp;
											</div>																				
										</div>
									</div>
									                        		
									<div class="col-xs-12" use="coAutherPlacement">
										<input type="hidden" name="existing_co_count" id="existing_co_count" value="<?=$coAuthor_counter?>">
										<? 
										if($coAuthor_counter > 0){ 
											$count = 0;
											foreach ($coAuthorDetails as $key => $value) {
												$coAuthor_counter-- ;
												$count++;
										?>
											<div use="coAuthorContainer" class="com-country-state">
												<div class="col-xs-12 form-group input-material" style="margin-bottom: -20px;">&nbsp;</div>
												<h4>CO-AUTHOR <span use='coAuthorSrl'></span></h4>
												<div class="col-xs-6 form-group input-material" actAs='fieldContainer'>
													<label for="user_email_id">Name</label>
													<input type="hidden" name="abstract_coauthor_id[]" defaultName="abstract_coauthor_id"  usefor="id"/>
													<input type="text" class="form-control" style="text-transform:uppercase;" 
														   name="abstract_coauthor_name[]" defaultName="abstract_coauthor_name"  usefor="name"
														   value="<?=$value['abstract_coauthor_name']?>"   >
												</div>
												
												<div class="col-xs-6 form-group input-material" actAs='fieldContainer'>
													<label for="user_mobile">Mobile</label>
													<input type="text" class="form-control" style="text-transform:uppercase;" 
														   name="abstract_coauthor_phone_no[]" value="<?=$value['abstract_coauthor_phone_no']?>" defaultName="abstract_coauthor_phone_no" usefor="mobile"  onkeypress="return isNumber(event)" >
												</div>
																				
												<div class="col-xs-6 form-group input-material" actAs='fieldContainer'>
													<label for="user_mobile">Country</label>
													<?php
														$sqlFetchCountry			 =	array();
														$sqlFetchCountry['QUERY']    = "SELECT * FROM "._DB_COMN_COUNTRY_." 
																					   WHERE `status` = ?
																						 AND `country_id` = ?";
																						 
														$sqlFetchCountry['PARAM'][]   = array('FILD' => 'status',  	   'DATA' =>'A',  'TYP' => 's');
														$sqlFetchCountry['PARAM'][]   = array('FILD' => 'country_id',  'DATA' =>$value['abstract_coauthor_country_id'],  'TYP' => 's');								 
																					
														$resultFetchCountry = $mycms->sql_select($sqlFetchCountry);
														$rowCountry            = $resultFetchCountry[0];
														?>
													<!-- <input type="text" name="abstract_coauthor_country[]" defaultName="abstract_coauthor_country" usefor="country"class="form-control" style="text-transform:uppercase;" value="<?=$rowCountry['country_name']?>"  > -->
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
													<option value="<?=$rowFetchCountry['country_id']?>"<?php if($value['abstract_coauthor_country_id']==$rowFetchCountry['country_id']){ echo 'selected'; } ?>><?=$rowFetchCountry['country_name']?></option>
													<?php
														}
													}
													?>
												</select>
												</div>
												<div class="col-xs-6 form-group input-material" actAs='fieldContainer'>
													<label for="user_mobile">State</label>
													<?
														$sqlFetchState			 =	array();
														$sqlFetchState['QUERY']    = "SELECT * FROM "._DB_COMN_STATE_." 
																					   WHERE `status` = ? 
																						 AND `st_id` = ?";
														$sqlFetchState['PARAM'][]   = array('FILD' => 'status',  	   'DATA' =>'A',  'TYP' => 's');
														$sqlFetchState['PARAM'][]   = array('FILD' => 'st_id',    'DATA' =>$value['abstract_coauthor_state_id'],  'TYP' => 's');
																					
														$resultFetchState = $mycms->sql_select($sqlFetchState);
														$rowState           = $resultFetchState[0];
													?>
													<!-- <input type="text" name="abstract_coauthor_state[]" defaultName="abstract_coauthor_state" usefor="state" class="form-control" style="text-transform:uppercase;" value="<?=strtoupper($rowState['state_name'])?>" > -->
													<select name="abstract_coauthor_state[]" defaultName="abstract_coauthor_state" usefor="state"
															class="form-control" style="text-transform:uppercase;padding: 6px 10px;height: 43px;" 
															operationMode="stateControl">
														<option value="<?=$value['abstract_coauthor_state_id']?>"><?=strtoupper($rowState['state_name'])?></option>
													</select>
												</div>
												
												<div class="col-xs-6 form-group input-material" actAs='fieldContainer'>
													<label for="user_first_name">Institute Name</label>
													<input type="text" class="form-control institute" style="text-transform:uppercase;" 
														   name="abstract_coauthor_institute_name[]" defaultName="abstract_coauthor_institute_name" usefor="instituteName"
														   value="<?=$value['abstract_coauthor_institute_name']?>" >
												</div>
												<div class="col-xs-6 form-group input-material" actAs='fieldContainer'>
													<label for="user_last_name">Department</label>
													<input type="text" class="form-control department" style="text-transform:uppercase;"  
														   name="abstract_coauthor_department[]" defaultName="abstract_coauthor_department" usefor="department"
														   value="<?=$value['abstract_coauthor_department']?>" >
												</div> 
											</div>
										<? }
										} ?> 
									</div>

								<div use="coAuthorContainerEmpty" style="display:none;" class="com-country-state">
									<div class="col-xs-12 form-group input-material" style="margin-bottom: -20px;">&nbsp;</div>
									<h4>CO-AUTHOR <span use='coAuthorSrl'></span></h4>
									<div class="col-xs-6 form-group input-material" actAs='fieldContainer'>
										<label for="user_email_id">Name</label>
										<input type="hidden" name="abstract_coauthor_id[]" defaultName="abstract_coauthor_id"  usefor="id"/>
										<input type="text" class="form-control" style="text-transform:uppercase;" 
											   name="abstract_coauthor_name[]" defaultName="abstract_coauthor_name"  usefor="name"
											   value="" >
										<div class = "alert alert-danger" callFor='alert'>Please enter a proper value.</div>
									</div>
									
									<div class="col-xs-6 form-group input-material" actAs='fieldContainer'>
										<label for="user_mobile">Mobile</label>
										<input type="text" class="form-control" style="text-transform:uppercase;" 
											   name="abstract_coauthor_phone_no[]" defaultName="abstract_coauthor_phone_no" usefor="mobile"  onkeypress="return isNumber(event)">
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
											   value="" >
										<div class = "alert alert-danger" callFor='alert'>Please enter a proper value.</div>
									</div>
									<div class="col-xs-6 form-group input-material" actAs='fieldContainer'>
										<label for="user_last_name">Department</label>
										<input type="text" class="form-control department" style="text-transform:uppercase;"  
											   name="abstract_coauthor_department[]" defaultName="abstract_coauthor_department" usefor="department"
											   value="" >
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
									<!-- <div class=" col-xs-2 text-center pull-right">
										<button type="button" class="submit" use='nextButton'>Next</button>
									</div>  -->               
	        						<div class="clearfix"></div>
	                            </li>
	                        </ul>
	                    </li>


						
						<li use="abstractDetails" class="rightPanel_payment">
	                        <div class="link" use="rightAccordianL1TriggerDiv">ABSTRACT DETAILS</div>
	                        <ul class="submenu">
	                            <li>
	                            	<?php
	                            	 if(!empty($resultAbstractType[0]['abstract_cat']) )
	                            	 {

	                            	 	if(!empty($resultAbstractType[0]['abstract_topic_id']) && $resultAbstractType[0]['abstract_topic_id']>0)
	                            	 	{
	                            	?>
											<div class="col-xs-12 form-group input-material" actAs='fieldContainer'>
												<label for="user_mobile">Topic</label>
												<select name="abstract_topic_id" id="abstract_topic_id"
														class="form-control select" style="text-transform:uppercase; height:60px; " 
														 required disabled> 
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
													<option value="<?=$rowAbstractTopic['id']?>" <?=$rowAbstractTopic['id'] == $resultAbstractType[0]['abstract_topic_id'] ? "selected":"" ?> ><?=$rowAbstractTopic['abstract_topic']?></option>
													<?php
														}
													}
												?>
												</select>
												<div class = "alert alert-danger" callFor='alert'>Please choose a proper option.</div>
											</div> 
									<?php
										}
									}

									 if($cfg['ABSTRACT.TITLE.WORD.TYPE']=='character')
									 {
									 	$abstract_title_length = strlen(trim($resultAbstractType[0]['abstract_title']));
									 }
									 else
									 {
									 	$abstract_title_length = str_word_count(trim($resultAbstractType[0]['abstract_title']));
									 }
									?>
									
									<div class="col-xs-12 form-group input-material" actAs='fieldContainer' relatedSubmissionType="" relatedSubmissionSubType="">
										<label for="user_mobile">Title</label>
										<textarea class="form-control" 
												  name="abstract_title" id="abstract_title" 
												  checkFor="wordCount" spreadInGroup="abstractTitle" 
												  displayText="abstract_title_word_count"
												  style="text-transform:uppercase;" required><?=$resultAbstractType[0]['abstract_title']?></textarea>
										<div class = "alert alert-success" style="display:block;">
											<span use="abstract_title_word_count" limit="<?=$cfg['ABSTRACT.TITLE.WORD.LIMIT']?>">
												<span use="total_word_entered"><?=$abstract_title_length;?></span> / 
												<span use="total_word_limit"><?=$cfg['ABSTRACT.TITLE.WORD.LIMIT']?></span>
												<span style="color: #D41000;">(Title should be within <?=$cfg['ABSTRACT.TITLE.WORD.LIMIT']?> <?=$cfg['ABSTRACT.TITLE.WORD.TYPE']?>s.)</span>
											</span>
										</div>
										<div class = "alert alert-danger" callFor='alert'>Please enter a proper value.</div> 
									</div>
									<?
										$total_count_word = 0;
										if(trim($resultAbstractType[0]['abstract_category']) === "Free Paper" && strtoupper(trim($resultAbstractType[0]['abstract_parent_type']) === "CASE REPORT")){
											$intro_words = str_word_count(trim($resultAbstractType[0]['abstract_background']));
											$desc_words = str_word_count(trim($resultAbstractType[0]['abstract_description']));
											$conclution_words = str_word_count(trim($resultAbstractType[0]['abstract_conclution']));
											$total_count_word = $intro_words+$desc_words+$conclution_words;
										}else{
											$intro_words = str_word_count(trim($resultAbstractType[0]['abstract_background']));
											$aims_obj_words = str_word_count(trim($resultAbstractType[0]['abstract_background_aims']));
											$material_wrods = str_word_count(trim($resultAbstractType[0]['abstract_material_methods']));
											$results_wrods = str_word_count(trim($resultAbstractType[0]['abstract_results']));
											$conclution_words = str_word_count(trim($resultAbstractType[0]['abstract_conclution']));
											$total_count_word = $intro_words+$aims_obj_words+$material_wrods+$results_wrods+$conclution_words;
										}
									?>

									<?php
									    $sqlAbstractFields			  =	array();
										$sqlAbstractFields['QUERY']    = "SELECT * FROM "._DB_ABSTRACT_FIELDS_." 
																		  WHERE `status` = ?";
										
										$sqlAbstractFields['PARAM'][]  = array('FILD' => 'status', 'DATA' =>'A',  'TYP' => 's');
										
										$resultAbstractFields = $mycms->sql_select($sqlAbstractFields);

									foreach ($resultAbstractFields as $key => $value) 
									{	

										
										 $sqlAbstractFieldsVal			  =	array();
										 $sqlAbstractFieldsVal['QUERY']    = "SELECT COUNT(*) AS COUNTDATA FROM "._DB_ABSTRACT_REQUEST_." 
																		  WHERE ".$value['field_key']."!='NULL' AND id='".$resultAbstractType[0]['id']."'";

										$resultAbstractFieldsVal = $mycms->sql_select($sqlAbstractFieldsVal);								  
										//echo '<pre>'; print_r( $resultAbstractFieldsVal[0]['COUNTDATA']);

										if($resultAbstractFieldsVal[0]['COUNTDATA']>0)
										{


									?>
										<div class="col-xs-12 form-group input-material" actAs='fieldContainer' relatedSubmissionType="" relatedSubmissionSubType="">
											<label for="user_mobile"><?=$value['display_name']?></label>
											<textarea class="form-control" 
													  name="<?=$value['field_key']?>[]" id="fieldVal_<?=$value['id']?>" 
													  checkFor="wordCount" spreadInGroup="abstractContent" 
													  displayText="abstract_total_word_display"
													  ><?=$resultAbstractType[0][$value['field_key']]?></textarea>
											<div class = "alert alert-danger" callFor='alert'>Please enter a proper value.</div>
										</div>
									<?php
										}
										
									}	
									?>
									
									<!-- <div class="col-xs-12 form-group input-material" actAs='fieldContainer' relatedSubmissionType="" relatedSubmissionSubType="">
										<label for="user_mobile">Aims &amp; Objective</label>
										<textarea class="form-control" 
												  name="abstract_background_aims" id="abstract_background_aims"
												  checkFor="wordCount" spreadInGroup="abstractContent" 
												  displayText="abstract_total_word_display"><?=$resultAbstractType[0]['abstract_background_aims']?></textarea>
										<div class = "alert alert-danger" callFor='alert'>Please enter a proper value.</div>
									</div> 
									
									<div class="col-xs-12 form-group input-material" actAs='fieldContainer' relatedSubmissionType="" relatedSubmissionSubType="">
										<label for="user_mobile">Description</label>
										<textarea class="form-control" 
												  name="abstract_description" id="abstract_description" 
												  checkFor="wordCount" spreadInGroup="abstractContent" 
												  displayText="abstract_total_word_display"
												  ><?=$resultAbstractType[0]['abstract_description']?></textarea>
										<div class = "alert alert-danger" callFor='alert'>Please enter a proper value.</div>
									</div>
									
									
									<div class="col-xs-12 form-group input-material" actAs='fieldContainer' relatedSubmissionType="" relatedSubmissionSubType="">
										<label for="user_mobile">Materials &amp; Methods</label>
										<textarea class="form-control" 
												  name="abstract_material_methods" id="abstract_material_methods" 
												  checkFor="wordCount" spreadInGroup="abstractContent" 
												  displayText="abstract_total_word_display"><?=$resultAbstractType[0]['abstract_material_methods']?></textarea>
										<div class = "alert alert-danger" callFor='alert'>Please enter a proper value.</div>
									</div>
									
									<div class="col-xs-12 form-group input-material" actAs='fieldContainer' relatedSubmissionType="" relatedSubmissionSubType="">
										<label for="user_mobile">Results</label>
										<textarea class="form-control" 
												  name="abstract_results" id="abstract_results" 
												  checkFor="wordCount" spreadInGroup="abstractContent" 
												  displayText="abstract_total_word_display"
												  ><?=$resultAbstractType[0]['abstract_results']?></textarea>
										<div class = "alert alert-danger" callFor='alert'>Please enter a proper value.</div>
									</div>
									
									<div class="col-xs-12 form-group input-material" actAs='fieldContainer' relatedSubmissionType="" relatedSubmissionSubType="">
										<label for="user_mobile">Conclusion</label>
										<textarea class="form-control" 
												  name="abstract_conclusion" id="abstract_conclusion" 
												  checkFor="wordCount" spreadInGroup="abstractContent" 
												  displayText="abstract_total_word_display"><?=$resultAbstractType[0]['abstract_conclution']?></textarea>
										<div class = "alert alert-danger" callFor='alert'>Please enter a proper value.</div>
									</div> -->
									
									<? if(trim($resultAbstractType[0]['abstract_category']) === "Award Paper"){ ?>
									<input type="hidden" name="existing_abstract_file" value="<?=$resultAbstractType[0]['abstract_file']?>" />
									<input type="hidden" name="existing_abstract_file_name" value="<?=$resultAbstractType[0]['abstract_original_file_name']?>" />
									<div class="col-xs-12 form-group input-material" use='abstractFile' actAs='fieldContainer' relatedSubmissionType="" relatedSubmissionSubType="">
										<label for="user_mobile">Upload File</label>									
										<input type="hidden" name="sessionId" use="sessionId" value="<?=session_id()?>" />											
										<input type="hidden" name="upload_temp_doc_fileName" use="upload_temp_fileName" />
										<input type="hidden" name="upload_original_doc_fileName" use="upload_original_fileName"/>	
										<input type="file" name="upload_abstract_file" id="upload_abstract_file" 
											   onChange="fileChangeHandler(this)" class="form-control" 
											   allowedSize = '10' allowedFileTypes='pdf'
											   style="width:100%; height:66px;">
										<? if(!empty($resultAbstractType[0]['abstract_file']) && !empty($resultAbstractType[0]['abstract_original_file_name'])) {?>
											<div class="alert alert-info" style="display:block;">
												<span>Previous uploaded file <strong><a href="<?=$cfg['FILES.ABSTRACT.REQUEST'].$resultAbstractType[0]['abstract_file']?>" target="_blank" style="color: #2a7454;"><?=$resultAbstractType[0]['abstract_original_file_name']?></a></strong></span>
											</div>
										<? } ?>
										<div class = "alert alert-success" style="display:block;">
										The FULL AWARD PAPER should be in the style of JOURNAL OF NEONATOLOGY <a href="https://journals.sagepub.com/author-instructions/NNT" target="_blank">(https://journals.sagepub.com/author-instructions/NNT)</a>
										</div>
										<!--<div class="progress" use="progressbar">
										  <div class="progress-bar" role="progressbar" aria-valuenow="70" aria-valuemin="0" aria-valuemax="100" style="width:0%" use="progress"></div>
										</div> -->
										<div class = "alert alert-danger" callFor='alert'>Please choose a proper value.</div>
									</div>
									<? } ?>
									
									<div class="col-xs-12 form-group ">
										<div class="checkbox">
											<label class="select-lable" >Total Word Count</label>
											<span use="abstract_total_word_display" limit="<?=$cfg['ABSTRACT.FREE.PAPER.SESSION.WORD.LIMIT']?>">
												<span use="total_word_entered"><?=$total_count_word?></span> / 
												<span use="total_word_limit"><?=$cfg['ABSTRACT.FREE.PAPER.SESSION.WORD.LIMIT']?></span>
												<span style="color: #D41000;">(Total <?=$cfg['ABSTRACT.FREE.PAPER.SESSION.WORD.LIMIT']?> are <?=$cfg['ABSTRACT.TOTAL.WORD.TYPE']?>s allowed.)</span>
											</span>
										</div>
									</div>
									<div class=" col-xs-2 text-center pull-right">
										<!-- <button type="button" class="submit" use='nextButton'>Next</button> -->
										<button type="submit" class="submit" use='nextButton'>Submit</button>
										<span use="preSubmitProcess" style="display:none;">UPLOADING <img src="<?=_BASE_URL_?>images/loadinfo.net.gif" height="10px"/></span>
									</div>          
	        						<div class="clearfix"></div>
	                            </li> 
	                        </ul>
	                    </li>
						<li use="registrationProcess" style="display:none; text-align:center;" class="rightPanel_payment">
							<img src="<?=_BASE_URL_?>images/PaymentPreloader.gif"/>
						</li>
	                </ul>      
	            </div>
	       	</div>
       	<? } ?>        
	   	</form>
	   	<?
		if(!$operate)
		{
		?>
		<div id="expiredModal" class="modal fade" role="dialog">
		  <div class="modal-dialog">
			<!-- Modal content-->
				<div class="modal-content">
					<div class="modal-header">
						<div class="log"><h3>ABSTRACT EDIT DATE PASSED</h3></div>
					</div>
					
					<div class="modal_subHead"><h2><span>You can't edit this abstract/case report anymore because last date of edit date is passed.</span></h2></div>
					
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
		?>
		<script>
		$(document).ready(function(){
			$('.regTarrif_rightPanel .submenu').css({'display':'block'})
			setInterval( function(){
				var windowHieght = $( document ).height();
				$(".left-container-box").css('min-height',windowHieght+'px');
			}, 1000);

			$('textarea').bind("paste",function(e) {
                let paste = (event.clipboardData || window.clipboardData).getData('text');
                paste = paste.toUpperCase();
                if(/<(br|basefont|hr|input|source|frame|param|area|meta|!--|col|link|option|base|img|wbr|!DOCTYPE|a|abbr|acronym|address|applet|article|aside|audio|b|bdi|bdo|big|blockquote|body|button|canvas|caption|center|cite|code|colgroup|command|datalist|dd|del|details|dfn|dialog|dir|div|dl|dt|em|embed|fieldset|figcaption|figure|font|footer|form|frameset|head|header|hgroup|h1|h2|h3|h4|h5|h6|html|i|iframe|ins|kbd|keygen|label|legend|li|map|mark|menu|meter|nav|noframes|noscript|object|ol|optgroup|output|p|pre|progress|q|rp|rt|ruby|s|samp|script|section|select|small|span|strike|strong|style|sub|summary|sup|table|tbody|td|textarea|tfoot|th|thead|time|title|tr|track|tt|u|ul|var|video).*?>|<(video).*?<\/\2>/i.test(paste) == true) {
                        alert('Unable to paste, Special character and symbols are not allowed.Please remove those tags and try again.');
                        e.preventDefault();
                }
            });
            // remove all html tag
			
            $("textarea[checkFor=wordCount]").keyup(function(){		
				wordLimitCounter(this);
			});
			$("textarea[checkFor=wordCount]").blur(function(){		
				wordLimitCounter(this);
			});

			/*$('.institute').on('keyup', function() {
	    	
		    // Get textbox value and remove non-alphabet characters
		    var val = $(this).val().replace(/[^a-zA-Z]/g, '');
		    // Set textbox value to cleaned-up value
		    $(this).val(val);
		  }); 
*/

		$('.institute').bind('keyup blur',function(){ 
    var node = $(this);
    node.val(node.val().replace(/[^a-z]/g,'') ); }
);
		  $('.department').on('keyup', function() {
		    // Get textbox value and remove non-alphabet characters
		    var val = $(this).val().replace(/[^a-zA-Z]/g, '');
		    // Set textbox value to cleaned-up value
		    $(this).val(val);
		  }); 


		});
		// document ready function end


		$("form[name=abstractRequestForm]").submit(function(evnt){
			var returnStatus = true;							
			$('textarea').each(function(i,evt){
                var message = $(this).val();
                var totalWords = 0;

                var parent 	= $("li[use=abstractDetails]");
                var group 	= $(this).attr("spreadInGroup");
                var showWordCount 	= $(parent).find("span[use='"+$(this).attr("displayText")+"']");
                $("div[use=coAuthorContainerEmpty]").remove();

                if($.trim(message) == ''){
                	popoverAlert($(this));
					returnStatus = false;
					return false;
                }

                if($(this).attr('name') == "abstract_title")
                {
                	totalWords = countWords(message)
                	if(totalWords > 30){
                		$(parent).find("textarea[spreadInGroup='"+group+"']")
                		$(showWordCount).find("span[use=total_word_entered]").text("");
						$(showWordCount).find("span[use=total_word_entered]").text(totalWordCount);
                		returnStatus = false;
						return false;
                	}
                }else{
                	totalWords = countWords(message)
                	if(totalWords > 250){
                		$(parent).find("textarea[spreadInGroup='"+group+"']")
                		$(showWordCount).find("span[use=total_word_entered]").text("");
						$(showWordCount).find("span[use=total_word_entered]").text(totalWordCount);
                		returnStatus = false;
						return false;
                	}
                }

                if(/<(br|basefont|hr|input|source|frame|param|area|meta|!--|col|link|option|base|img|wbr|!DOCTYPE|a|abbr|acronym|address|applet|article|aside|audio|b|bdi|bdo|big|blockquote|body|button|canvas|caption|center|cite|code|colgroup|command|datalist|dd|del|details|dfn|dialog|dir|div|dl|dt|em|embed|fieldset|figcaption|figure|font|footer|form|frameset|head|header|hgroup|h1|h2|h3|h4|h5|h6|html|i|iframe|ins|kbd|keygen|label|legend|li|map|mark|menu|meter|nav|noframes|noscript|object|ol|optgroup|output|p|pre|progress|q|rp|rt|ruby|s|samp|script|section|select|small|span|strike|strong|style|sub|summary|sup|table|tbody|td|textarea|tfoot|th|thead|time|title|tr|track|tt|u|ul|var|video).*?>|<(video).*?<\/\2>/i.test(message) == true) {

                    alert('Special character and symbols are not allowed.Please check and remove those otherwise system will automatically remove those tags..');
                    //event.preventDefault();

                    message= message.replace(/<(br|basefont|hr|input|source|frame|param|area|meta|!--|col|link|option|base|img|wbr|!DOCTYPE|a|abbr|acronym|address|applet|article|aside|audio|b|bdi|bdo|big|blockquote|body|button|canvas|caption|center|cite|code|colgroup|command|datalist|dd|del|details|dfn|dialog|dir|div|dl|dt|em|embed|fieldset|figcaption|figure|font|footer|form|frameset|head|header|hgroup|h1|h2|h3|h4|h5|h6|html|i|iframe|ins|kbd|keygen|label|legend|li|map|mark|menu|meter|nav|noframes|noscript|object|ol|optgroup|output|p|pre|progress|q|rp|rt|ruby|s|samp|script|section|select|small|span|strike|strong|style|sub|summary|sup|table|tbody|td|textarea|tfoot|th|thead|time|title|tr|track|tt|u|ul|var|video).*?|<(video).*?<\/\2>|".*">.*?|<\/(.*).*?>/g,''); 
                }
                
                $(this).val(message)
            })

            try{
				
				if(!returnStatus)
				{
					console.log('return status @ '+returnStatus);
					evnt.preventDefault();
				}
			}
			catch(e)
			{
				console.log('ERROR : '+e.message);
				evnt.preventDefault();
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



		/*function wordLimitCounter(obj)
		{
			var totalWordCount  = 0; 
			
			var parent 			= $("li[use=abstractDetails]");
			var wordCount 		= parseInt($(obj).attr("wordcount"));	
			var group 			= $(obj).attr("spreadInGroup");
			var showWordCount 	= $(parent).find("span[use='"+$(obj).attr("displayText")+"']");
			var wordLimit		= parseInt($(showWordCount).attr('limit'));
			var count 			= wordLimit
			$(parent).find("textarea[spreadInGroup='"+group+"']").each(function(){												   
				if($(this).val()!="")
				{
					totalWordCount  = parseInt(totalWordCount) + parseInt(countWords($(this).val()));
					if($("textarea[spreadInGroup='"+group+"']").length > 1){
						count = wordLimit-totalWordCount
					}
					if(totalWordCount > wordLimit)
					{
						
        				$(showWordCount).css("color","#D41000");
        				$(this).val(truncateWords($.trim($(this).val()),count));
        			}else{
        				$(showWordCount).css("color","");
        			}
				}
			});
			
			$(showWordCount).find("span[use=total_word_entered]").text("");
			$(showWordCount).find("span[use=total_word_entered]").text(totalWordCount);
			
			
		}


		function truncateWords(str, no_words) {
		    
		     const words = str.split(' ');

			  if (no_words >= words.length) {
			    return str;
			  }

			  const truncated = words.slice(0, no_words);
			  return `${truncated.join(' ')}`;
		}
		
		function countWords(stringValue)
		{
			s = stringValue;
			s = s.replace(/(^\s*)|(\s*$)/gi,"");
			s = s.replace(/[ ]{2,}/gi," ");
			s = s.replace(/\n /,"\n");
			
			return s.split(' ').length;
		}*/

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
				//alert(word_type);
				$(parent).find("textarea[spreadInGroup='"+group+"']").each(function(){												   
					if($(this).val()!="")
					{
						 
							totalWordCount  = parseInt(totalWordCount) + parseInt(countWords($(this).val()));
							totalCharacterCount = parseInt(totalCharacterCount) + parseInt($(this).val().length);
							if($("textarea[spreadInGroup='"+group+"']").length > 1){
								count = wordLimit-totalWordCount

								countCharacter = parseInt(wordLimit) - parseInt(totalCharacterCount);

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
					$('li[use=registrationProcess]').show();
				}
			}
		}

		$("li[use=abstractCoAuthors]").find("input[type=radio][use=coAuthorCountSelect]").click(function(){		
									
			var count = parseInt($(this).val());			
			var container = $("li[use=abstractCoAuthors]").find("div[use=coAutherPlacement]");
			var existing = $(container).find("div[use=coAuthorContainerPlaced]").length;
			var allexisting = $('#existing_co_count').val();

			//alert('count='+count+'existng='+existing);
			if(existing > count)
			{
				var diff = existing - count - allexisting;

				var diffss = existing - count;
				//alert(diffss);

				for(var i=0; i<parseInt(diffss); i++)
				{
					try
					{
						//alert(12);
						//$(container).find("div[use=coAuthorContainerPlaced]").css("background-color", "yellow");
						$(container).find("div[use=coAuthorContainerPlaced]").last().remove();
					}
					catch(e){
						console.log('problem>>'+e.message());
					}
				}
			}
			else
			{

				var diff = count - existing - allexisting;								
				for(var i=0; i<parseInt(diff); i++)
				{
					try
					{
						var cloned = $("div[use=coAuthorContainerEmpty]").clone();
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
			
			/*$.each($(container).find("div[use=coAuthorContainerPlaced]"),function(i,obj){
				$(obj).find("span[use=coAuthorSrl]").text(i+1);
			});	*/		

			$('.institute').on('keyup', function() {
									
			    // Get textbox value and remove non-alphabet characters
			    var val = $(this).val().replace(/[^a-zA-Z]/g, '');
			    // Set textbox value to cleaned-up value
			    $(this).val(val);
			  });
			
			$('.department').on('keyup', function() {
				
			    // Get textbox value and remove non-alphabet characters
			    var val = $(this).val().replace(/[^a-zA-Z]/g, '');
			    // Set textbox value to cleaned-up value
			    $(this).val(val);
			  }); 			
		});	

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
		</script>
	</body>
</html>
<?php
	
?>
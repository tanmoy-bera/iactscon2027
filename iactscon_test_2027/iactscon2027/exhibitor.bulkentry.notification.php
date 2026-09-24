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
		<title>:: BULK ENTRY | AICC RCOG 2019 ::</title>
<?php
		setTemplateStyleSheet();
		setTemplateBasicJS();
		backButtonOffJS();
?>
		<link rel="stylesheet" type="text/css" href="<?=_DIR_CM_CSS_."website/"?>abstract.user.entrypoint.css" />
		<link rel="stylesheet" type="text/css" href="<?=_DIR_CM_CSS_."website/"?>roboto_css.css" />
	</head>
<?php
		$Submissionid 	= addslashes(trim($_REQUEST['Submissionid']));
		
		$sqlfetchUpload			 = array();
		$sqlfetchUpload['QUERY'] = "SELECT *  FROM "._DB_BLUK_REGISTRATION_SESSION_."  WHERE id = '".$Submissionid."'"; 
		$resfetchUpload    		 = $mycms->sql_select($sqlfetchUpload);
		$rowfetchUpload			 = $resfetchUpload[0];
		
		$exhibitor_company_id 	 		 = $rowfetchUpload['exhibitor_company_id'];
		$registration_tariff_cutoff_id 	 = $rowfetchUpload['registration_tariff_cutoff_id'];
		
		$TOKEN = base64_encode(serialize(array('exhibitorId'=>$exhibitor_company_id,'cutoffid'=>$registration_tariff_cutoff_id)));
		
		$sqlQuery 		= array();
		$sqlQuery['QUERY']  = " SELECT COUNT(*) AS cont
								  FROM "._DB_BLUK_REGISTRATION_DATA_." 
								 WHERE session_id = '".$Submissionid."'"; 
		$resfetchALL    	= $mycms->sql_select($sqlQuery);
		$rowALL 			= $resfetchALL[0];
		
?>
	<body>
        <div class="col-xs-2 left-container-box regTarrif_leftPanel" style="position:relative;">
            <div class="col-xs-4 logo">
                <img src="<?=_BASE_URL_?>images/logo_white.png" alt="logo" style="width: 100%;">              
            </div>
            <div class="col-xs-7 col-xs-offset-1 timer" style="padding: 0">
				<div>
                    <h5 class="cutoffNameTop">BULK</h5>
                    <h4 style="color: white" class="cutoffName">USER ENTRY</h4>
                </div>
            </div>	
            <div class="col-xs-12 menu" style="padding: 0">
                <ul use="leftAccordion" class="accordion" style="border-left: 5px solid #0078b3">
				  <li use='leftAccordionAbstractCategory'>
				 	<div class="link masterClass" use="accordianL1TriggerDiv">DIRECTIVES</div>
					<p style="color:#FFFFFF; padding-left:20px; padding-right:10px; text-align:left; font-size:smaller;">
					Contrary to popular belief, Lorem Ipsum is not simply random text. It has roots in a piece of classical Latin literature from 45 BC, making it over 2000 years old. Richard McClintock, a Latin professor at Hampden-Sydney College in Virginia, looked up one of the more obscure Latin words, consectetur, from a Lorem Ipsum passage, and going through the cites of the word in classical literature, discovered the undoubtable source. Lorem Ipsum comes from sections 1.10.32 and 1.10.33 of "de Finibus Bonorum et Malorum" (The Extremes of Good and Evil) by Cicero, written in 45 BC. This book is a treatise on the theory of ethics, very popular during the Renaissance. The first line of Lorem Ipsum, "Lorem ipsum dolor sit amet..", comes from a line in section 1.10.32.
					</p>
				  </li>
                </ul>
            </div>
			
			<div class="col-xs-12 menu-links">   
				 <p class="menu-login" style="position:fixed; bottom: 20px;"><a href="<?=_BASE_URL_?>exhibitor.bulkentry.screen.php?TOKEN=<?=$TOKEN?>">ENTER ANOTHER SET</a></p>
            </div> 
			
			<br/><br/>           
        </div>        
        
        <div class="col-xs-10 right-container regTarrif_rightPanel">
            <div class="col-xs-12 col-xs-offset-0" style="padding: 0; margin-top: 15px;">                
				<div class="col-xs-11 profileright-section" style="width: 85%">
                    <div class="log"><h1>UPLOAD SUCCESSFUL</h1></div>
                    <div class="summery"><h3>Your <b>Registration Data</b> has been submitted successfully.</h3></div>                    
                    <div class="paynow-msg" style="margin-top: 25px;">
						<h5>Submission Reference No. :  <?=$Submissionid?></h5>
						<h5>Total Records : <?=$rowALL['cont']?></h5>
                    </div>
					<div class="paynow-msg postal" style="margin-top: 15px;">
						<h5><i>You will contacted by the Conference Registration Office very soon with further information.</i></h5>
					</div>
                    <div class="bttn" style="margin-top: 25px;"><button onClick="window.location.href='exhibitor.bulkentry.screen.process.php?act=downloadExhibitorExcel&Submissionid=<?=$Submissionid?>'">Download The Record Excel</button></div>
                </div>
            </div>
       	</div>
		
		<script>
			$(document).ready(function(){
				setInterval( function(){
					var windowHieght = $( document ).height();
					$(".left-container-box").css('min-height',windowHieght+'px');
				}, 1000);
			});
		</script>
	</body>
	
</html>
<?php
	
?>
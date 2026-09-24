<?php
include_once("includes/frontend.init.php"); 
include_once("includes/function.registration.php");
include_once("includes/function.delegate.php");
include_once("includes/function.workshop.php");
?>
<!DOCTYPE html>
<html lang="en">
	<head>
		<meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <link rel="icon" href="image/favicon.png" type="favicon">
		<title>:: Abstract Submission | AICC RCOG 2019 ::</title>
		
<?php
		setTemplateStyleSheet();
		setTemplateBasicJS();
		backButtonOffJS();
?>
		<link rel="stylesheet" type="text/css" href="<?=_DIR_CM_CSS_."website/"?>roboto_css.css" />
		<link rel="stylesheet" type="text/css" href="<?=_DIR_CM_CSS_."website/"?>abstract.submission.notification.css" />
        <link rel="stylesheet" type="text/css" href="<?=_DIR_CM_CSS_."website/"?>all.css" />
	</head>
	<body> 
		<div class="container-fluied">
        <div class="container">
            <div class="row">                
				<?
				leftCommonMenu();
				
				$id = addslashes(trim($_REQUEST['Submissionid']));
				$sqlQuery = array();
				$sqlQuery['QUERY']     = "  SELECT abstractRequest.*,
												   abstractTopic.abstract_topic AS abstract_topic												  														  
														
											  FROM "._DB_ABSTRACT_REQUEST_." abstractRequest 																			  
											  
								   LEFT OUTER JOIN "._DB_ABSTRACT_TOPIC_." abstractTopic 
												ON abstractRequest.abstract_topic_id = abstractTopic.id 
																																		
											 WHERE abstractRequest.abstract_submition_code = ?";							
				$sqlQuery['PARAM'][]   	= array('FILD' => 'abstract_submition_code',    'DATA' =>$id,     'TYP' => 's');
				
				$abstractDetails    	= $mycms->sql_select($sqlQuery, false);
				$rowAbstractDetails		= $abstractDetails[0];	
				
				$rowUserDetails 		= getUserDetails($rowAbstractDetails['applicant_id']);	
				
				//echo '<pre>'; print_r($rowAbstractDetails);
				
				$abstract_background_aims_words 	= explode(' ',trim($rowAbstractDetails['abstract_background_aims']));
				$abstract_material_methods_words 	= explode(' ',trim($rowAbstractDetails['abstract_material_methods']));
				$abstract_results_words 			= explode(' ',trim($rowAbstractDetails['abstract_results']));
				$abstract_conclution_words 			= explode(' ',trim($rowAbstractDetails['abstract_conclution']));
				$abstract_references_words 			= explode(' ',trim($rowAbstractDetails['abstract_references']));
				$abstract_background_words 			= explode(' ',trim($rowAbstractDetails['abstract_background']));
				$abstract_description_words 		= explode(' ',trim($rowAbstractDetails['abstract_description']));
				
				$totalWordCount						= count($abstract_background_aims_words)
													  +count($abstract_material_methods_words)
													  +count($abstract_results_words)
													  +count($abstract_conclution_words)
													  +count($abstract_references_words)
													  +count($abstract_background_words)
													  +count($abstract_description_words);
				$total_count_word = 0;
				if(trim($rowAbstractDetails['abstract_category']) === "Free Paper" && strtoupper(trim($rowAbstractDetails['abstract_parent_type']) === "CASE REPORT")){
					$intro_words = str_word_count(trim($rowAbstractDetails['abstract_background']));
					$desc_words = str_word_count(trim($rowAbstractDetails['abstract_description']));
					$conclution_words = str_word_count(trim($rowAbstractDetails['abstract_conclution']));
					$total_count_word = $intro_words+$desc_words+$conclution_words;
				}else{
					$intro_words = str_word_count(trim($rowAbstractDetails['abstract_background']));
					$aims_obj_words = str_word_count(trim($rowAbstractDetails['abstract_background_aims']));
					$material_wrods = str_word_count(trim($rowAbstractDetails['abstract_material_methods']));
					$results_wrods = str_word_count(trim($rowAbstractDetails['abstract_results']));
					$conclution_words = str_word_count(trim($rowAbstractDetails['abstract_conclution']));
					$total_count_word = $intro_words+$aims_obj_words+$material_wrods+$results_wrods+$conclution_words;
				}
				//echo 'total:-'.$total_count_word;
				$sql				  	= array();
				$sql['QUERY']         	= "   SELECT mast.award_name 
												FROM "._DB_AWARD_REQUEST_." req
										  INNER JOIN "._DB_AWARD_MASTER_." mast
												  ON mast.`id` = req.`award_id`
											   WHERE req.`applicant_id` = '".$rowAbstractDetails['applicant_id']."'
												 AND req.`submission_id` = '".$rowAbstractDetails['id']."'
												 AND req.`status`= 'A'";													   
				$resultUsertAward 	  	= $mycms->sql_select($sql, false);
				$nominations 		  	= array();
				//echo '<!-- nomination --'; print_r($sql); echo '-->';
				foreach($resultUsertAward as $kk=>$row)
				{
					$nominations[] = $row['award_name'];
				}
				
				if(empty($nominations))
				{
					$nominations[] = 'None';
				}
				
				?>
                <div class="col-xs-11 profileright-section" style="width: 85%">
                	<div class="banner-wrap">
	            		<?
		                    $sql    =   array();
		                    $sql['QUERY'] = "SELECT * FROM "._DB_EMAIL_SETTING_." 
		                                     WHERE `status`='A' order by id desc limit 1";
		                                        
		                    $result = $mycms->sql_select($sql);
		                    $row             = $result[0];

		                    $header_Image = _BASE_URL_.$cfg['EMAIL.HEADER.FOOTER.IMAGE'].$row['header_image'];
                       
                    	?>
                        <img src="<?=$header_Image;?>" alt="" width="1000" height="200">
                    </div>
                    <div class="log-wrap"><h1>SUBMISSION SUCCESSFUL</h1></div>
                    <div class="t-c">
                    <div class="summery"><h3>Your <b><?=($rowAbstractDetails['abstract_parent_type']=='CASE REPORT')?'Case Report':'Abstract'?></b> has been submitted successfully.</h3></div>                    
                    <div class="paynow-msg" style="margin-top: 25px;">
						<h5>Submission Reference No. :  <?=$rowAbstractDetails['abstract_submition_code']?></h5>
						<!-- <h5>Topic : <?=$rowAbstractDetails['abstract_topic']?></h5> -->
						<h5>Title : <?=$rowAbstractDetails['abstract_title']?></h5>
						<h5>Word Count : <?=$total_count_word?></h5>
						<? /*<h5>Nominations : <?=implode(', ',$nominations)?><br/></h5> */ ?>
                    </div>
					<div class="paynow-msg postal" style="margin-top: 15px;">
						<h5><i>You will receive selection/rejection intimation at your registered e-mail id by <?=($rowAbstractDetails['abstract_parent_type']=='CCASE REPORT')?$mycms->cDate('d/m/Y',$cfg['CASEREPORT.CONFIRMATION.DATE']):$mycms->cDate('d/m/Y',$cfg['ABSTRACT.CONFIRMATION.DATE'])?>.</i></h5>
						<?
						if($rowUserDetails['registration_request']=='ABSTRACT')
						{
						?>
						<h6>Conference Registration is mandatory for paper presenters.</h6>
						<?
						}
						?>
					</div>
                    <div class="bttn" style="margin-top: 25px;"><button onClick="window.location.href='profile.php?Submissionid=<?=$id?>&menuId=<?=($rowAbstractDetails['abstract_parent_type']=='CASE REPORT')?'casereport_details':'abstract_details'?>'" style="background:#7f8080">Proceed to your Profile</button></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
	</body>
</html>
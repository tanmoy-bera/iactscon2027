<?php
	function page_header($headerTitle)
	{
		global $cfg, $mycms;
	?>
		<!doctype html>
			<html class="no-js" lang="en">
				<head>
					<title>:: <?=$cfg['APP_NAME']?> :: <?=$headerTitle?></title>
					<link href="<?=_BASE_URL_?>images/fav.png" rel="shortcut icon">
					<meta charset="utf-8" />
					<meta name="description" content="" />
					<meta name="author" content="" />		
					<meta name="viewport" content="width=device-width, initial-scale=1.0">
					<?php
					if($mycms->getPageName()=="login.php" && $cfg['SECTION'] == "Login")
					{
					?>
						<link rel="stylesheet" href="<?=_BASE_URL_?>css/adminPanel/<?=$cfg['THEME']?>/reset.css" type="text/css" media="screen" title="no title" />
						<link rel="stylesheet" href="<?=_BASE_URL_?>css/adminPanel/<?=$cfg['THEME']?>/text.css" type="text/css" media="screen" title="no title" />
						<link rel="stylesheet" href="<?=_BASE_URL_?>css/adminPanel/<?=$cfg['THEME']?>/buttons.css" type="text/css" media="screen" title="no title" />
						<link rel="stylesheet" href="<?=_BASE_URL_?>css/adminPanel/<?=$cfg['THEME']?>/theme-default.css" type="text/css" media="screen" title="no title" />
						<link rel="stylesheet" href="<?=_BASE_URL_?>css/adminPanel/<?=$cfg['THEME']?>/login.css" type="text/css" media="screen" title="no title" />
					<?php
					}
					else
					{
					?>
						<link rel="stylesheet" href="<?=_BASE_URL_?>css/adminPanel/<?=$cfg['THEME']?>/style.css" type="text/css" />
					<?php
					}
					?>
					<script language="javascript" src="<?=_BASE_URL_?>js/jquery.js"></script>
					<script language="javascript" src="<?=_BASE_URL_?>js/adminPanel/architecture.js"></script>
					<script language="javascript" src="<?=_BASE_URL_?>js/print.js"></script>
					<script language="javascript" src="<?=_BASE_URL_?>js/excelReportGenerator.js"></script>
					<script language="javascript" src="<?=_BASE_URL_?>js/common.js"></script>
					<script language="javascript" type="text/javascript">
						function closeApplication()
						{
							var param = "act=<?=md5('logout')?>";
							$.ajax({
								  url: "<?=$cfg['DOMAIN_URL']?>section_login/login.php",
								  type: "POST",
								  data: param,
								  dataType: "html",
								  success: function(data){
									 location.href="admin.php";
								  }
							   }
							);
						}
						$(document).ready(function() {
							$('div[forType=messageDiv]').delay(10000).fadeOut('slow');
						});
					</script>
				</head>
				<body>
					<?php
						if($mycms->getPageName()!="login.php")
						{
					?>
							<div id="wrapper">
								
								<div id="header">
									<h1 style="background-image:url(<?=_BASE_URL_?>images/logo_small.png)">
										<a href="<?=_BASE_URL_.getDomainUrl($cfg['DOMAIN_TAG'])?>"><?=getDomainName($cfg['DOMAIN_TAG'])?></a>
									</h1>		
									<a href="javascript:;" id="reveal-nav">
										<span class="reveal-bar"></span>
										<span class="reveal-bar"></span>
										<span class="reveal-bar"></span>
									</a>
								</div>
								<?php
								faculty_leftbar_content();
								faculty_topNav_content();
								?>
								<div id="content">		
				
									<div id="contentHeader">
										<h1><?=$headerTitle?></h1>
									</div>
					<?php
						}
					?>
	<?php
	}
	
	function dashboard_page_header($headerTitle)
	{
		global $cfg, $mycms;
	?>
		<!doctype html>
			<html class="no-js" lang="en">
				<head>
					<title>:: <?=$cfg['APP_NAME']?> :: <?=$headerTitle?></title>
					<link href="<?=_BASE_URL_?>images/fav.png" rel="shortcut icon">
					<meta charset="utf-8" />
					<meta name="description" content="" />
					<meta name="author" content="" />		
					<meta name="viewport" content="width=device-width, initial-scale=1.0">
					<?php
					if($mycms->getPageName()=="login.php" && $cfg['SECTION'] == "Login")
					{
					?>
						<link rel="stylesheet" href="<?=_BASE_URL_?>css/adminPanel/<?=$cfg['THEME']?>/reset.css" type="text/css" media="screen" title="no title" />
						<link rel="stylesheet" href="<?=_BASE_URL_?>css/adminPanel/<?=$cfg['THEME']?>/text.css" type="text/css" media="screen" title="no title" />
						<link rel="stylesheet" href="<?=_BASE_URL_?>css/adminPanel/<?=$cfg['THEME']?>/buttons.css" type="text/css" media="screen" title="no title" />
						<link rel="stylesheet" href="<?=_BASE_URL_?>css/adminPanel/<?=$cfg['THEME']?>/theme-default.css" type="text/css" media="screen" title="no title" />
						<link rel="stylesheet" href="<?=_BASE_URL_?>css/adminPanel/<?=$cfg['THEME']?>/login.css" type="text/css" media="screen" title="no title" />
					<?php
					}
					else
					{
					?>
						<link rel="stylesheet" href="<?=_BASE_URL_?>css/adminPanel/<?=$cfg['THEME']?>/style.css" type="text/css" />
					<?php
					}
					?>
					<script language="javascript" src="<?=_BASE_URL_?>js/jquery.js"></script>
					<script language="javascript" src="<?=_BASE_URL_?>js/adminPanel/architecture.js"></script>
					<script language="javascript" src="<?=_BASE_URL_?>js/print.js"></script>
					<script language="javascript" src="<?=_BASE_URL_?>js/excelReportGenerator.js"></script>
					<script language="javascript" src="<?=_BASE_URL_?>js/common.js"></script>
					<script language="javascript" type="text/javascript">
						function closeApplication()
						{
							var param = "act=<?=md5('logout')?>";
							$.ajax({
								  url: "<?=$cfg['DOMAIN_URL']?>section_login/login.php",
								  type: "POST",
								  data: param,
								  dataType: "html",
								  success: function(data){
									 location.href="admin.php";
								  }
							   }
							);
						}
						$(document).ready(function() {
							$('div[forType=messageDiv]').delay(10000).fadeOut('slow');
						});
					</script>
				</head>
				<body>
					<?php
						if($mycms->getPageName()!="login.php")
						{
					?>
							<div id="wrapper">
								
								<div id="header">
									<h1 style="background-image:url(<?=_BASE_URL_?>images/logo_small.png)">
										<a href="<?=_BASE_URL_.getDomainUrl($cfg['DOMAIN_TAG'])?>"><?=getDomainName($cfg['DOMAIN_TAG'])?></a>
									</h1>		
									<a href="javascript:;" id="reveal-nav">
										<span class="reveal-bar"></span>
										<span class="reveal-bar"></span>
										<span class="reveal-bar"></span>
									</a>
								</div>
								<?php
								faculty_leftbar_content();
								faculty_topNav_content();
								?>
								<div id="content" style="min-height:500px;">
					<?php
						}
					?>
	<?php
	}

	function breadCumDefn($headerDisplay="")
	{
		global $cfg, $mycms;
		webmaster_breadCumDefn($headerDisplay);
	}
	
	/**********************************************************/
	/*                  FACULTY LEFT BAR METHOD               */
	/**********************************************************/	
	function faculty_leftbar_content()
	{
		global $cfg, $mycms;
		$loggedUserID = $mycms->getLoggedUserId();
		$sqlAbstractTopic			  =	array();
		$sqlAbstractTopic['QUERY']    = "SELECT * FROM "._DB_ABSTRACT_TOPIC_CATEGORY_." 
										  WHERE `status`='A'
									   ORDER BY `id` ASC";
		
		//$sqlAbstractTopic['PARAM'][]  = array('FILD' => 'status', 'DATA' =>'A',  'TYP' => 's');
		$resultAbstractTopic = $mycms->sql_select($sqlAbstractTopic);
		//echo '<pre>'; print_r($resultAbstractTopic);
	?>
		<div id="sidebar">
			<ul id="mainNav">
				<li id="navDashboard" class="nav active">
					<span class="icon-home"></span>
					<a href="<?=$cfg['DOMAIN_URL']?>">Reviewer's Panel</a>
				</li>
				<?php
				$counterWebPageSection = 0;
				$result                = getFacultySectionArray();
				?>
			
				<?php
				foreach($resultAbstractTopic as $k=>$val)
				{
					//echo '<pre>'; print_r($resultAbstractTopic);
					$sqlSubcat			  =	array();
					$sqlSubcat['QUERY']    = "SELECT * FROM "._DB_ABSTRACT_SUBMISSION_." 
										  WHERE `status`='A' AND category='".$val['id']."'
									   ORDER BY `id` ASC";

					$resultSubcat = $mycms->sql_select($sqlSubcat);	

					$sqlCountAbstract			  =	array();
					$sqlCountAbstract['QUERY']    = "SELECT COUNT(*) COUNTDATA FROM "._DB_ABSTRACT_REQUEST_."  
													  WHERE `status` = 'A' AND abstract_cat='".$val['id']."'
												  AND id IN (SELECT abstract_id FROM "._DB_ABSTRACT_ALLOTMENT_." WHERE review_user_id = '".$loggedUserID."')";
					
						
						$resultCountAbstract = $mycms->sql_select($sqlCountAbstract);
					
					//echo '<pre>'; print_r($sqlCountAbstract);			   
				?>

					<li class="nav" id="catPanel" onclick="getSub('<?=$val['id']?>')"><span><a href="../section_abstract/all_abstract.free_paper.php?cat=<?=$val['id']?>"><?=$val['category']?><strong> (<?=$resultCountAbstract[0]['COUNTDATA']?></strong>)</a></span></li>
				<?php
					if(count($resultSubcat)>0)
					{
						foreach ($resultSubcat as $key => $value) {
							?>
							<!--  <li class="nav subcat" id="subcatPanel<?=$val['id']?>" style="margin-left: 20px;"><span><a href="../section_abstract/all_abstract.free_paper.php?category=<?=$value['category']?>&sub=<?=$value['id']?>"><?=$value['abstract_submission']?></a></span></li> -->
							<?php
						}
					}
				}
				?>

				<!-- <li class="nav"><span><a href="../section_abstract/all_abstract.free_paper.php?goto=poster">Review Poster</a></span></li>
				<li class="nav"><span><a href="../section_abstract/all_abstract.free_paper.php?goto=video">Review Video</a></span></li> -->
				
			</ul>
		</div>
	<?php
	}
	
	/**********************************************************/
	/*              WEBMASTER TOP NAVIGATION METHOD           */
	/**********************************************************/
	function faculty_topNav_content()
	{
		global $cfg, $mycms;
	?>
		<div id="topNav">
			<ul>
				<li>
					<a href="#menuProfile" class="menu"><?=$mycms->getLoggedUserName()?></a>
					<div id="menuProfile" class="menu-container menu-dropdown">
						<div class="menu-content"style="min-height:35px;">
							<ul class="">
								<li><a href="<?=$cfg['DOMAIN_URL']?>section_configuration/changePassword.php">Change Password</a></li>
							</ul>
						</div>
					</div>
				</li>
				<li><a onClick="confirm('Do you realy want to Logout?')&&closeApplication()">Logout</a></li>
			</ul>
		</div>
	<?php
	}
	
	function page_footer($scr='&nbsp;')
	{
		global $cfg, $mycms;
		webmaster_page_footer($scr);
	}
	/**********************************************************/
	/*               WEBMASTER PAGE FOOTER METHOD             */
	/**********************************************************/		
	function webmaster_page_footer1($scr='&nbsp;')
	{
		global $cfg, $mycms;
	
		if($mycms->getPageName()!="login.php")
		{
	?>
					</div>
				</div>
				<div id="footer">
					<span style="">This system is running on ENWI architecture</span>
					<span style="float:right;"><?=$cfg['COPYRIGHT']?></span>
				</div>
				<style>
					.notify-defult { background-color: #dff0d8; color: #468847; border-color: #d6e9c6; }
				</style>
	<?php
		}
		$notificationClass = "";
		if($_REQUEST['m']!="")
		{
				if($_REQUEST['m']==1 || $_REQUEST['m']==2)
				{
					$notificationClass = "notify-success";
				}
				else if($_REQUEST['m']==3 || $_REQUEST['m']==0)
				{
					$notificationClass = "notify-error";
				}
				else if($_REQUEST['m']==5)
				{
					$notificationClass = "notify-info";
				}
				else
				{
					$notificationClass = "notify-defult";
				}
	?>
				<div class="notify <?=$notificationClass?>" forType="messageDiv">
					<p><?=$mycms->getDisplayMessage()?></p>
				</div>
	<?php
		}
	?>
				<script src="<?=_BASE_URL_?>js/adminPanel/all.js"></script>
				<script language="javascript" src="<?=_BASE_URL_?>js/adminPanel/architecture.js"></script>
				<script language="javascript" src="<?=_BASE_URL_?>js/common.js"></script>
				<script language="javascript" type="text/javascript">
					function closeApplication()
					{
						var param = "act=<?=md5('logout')?>";
						$.ajax({
							  url: "<?=$cfg['DOMAIN_URL']?>section_login/login.php",
							  type: "POST",
							  data: param,
							  dataType: "html",
							  success: function(data){
								 location.href="admin.php";
							  }
						   }
						);
					}
					
					$(document).ready(function() {
						$('div[forType=messageDiv]').delay(4000).fadeOut('slow','linear');
						$('input,textarea').attr('autocomplete','off');
					});
					
					var jsBASE_URL	= "<?=_BASE_URL_?>";
					var CFG = { BASE_URL : "<?=_BASE_URL_?>" };
				</script>
			</body>
		</html>
	<?php
	}
?>
<?php
	/**********************************************************/
	/*               WEBMASTER PAGE HEADER METHOD             */
	/**********************************************************/
	function javaScriptDefinedValue()
	{
		global $cfg, $mycms;
	?>
		<script language="javascript">
			var jsBASE_URL	= "<?=_BASE_URL_?>";
			var jsWemaster_BASE_URL	= "<?=_BASE_URL_?>webmaster/";
			var CFG = { BASE_URL : "<?=_BASE_URL_?>" };
		</script>
	<?php
	}

	function webmaster_page_header($headerTitle,$fullscreen=false)
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
					<script language="javascript" src="<?=_BASE_URL_?>js/common.js"></script>
					<? javaScriptDefinedValue() ?>
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
									  location.href = "<?=$cfg['DOMAIN_URL']?>dashboard.php";
								  }
							   }
							);
						}
						
						function MeasureConnectionSpeedtest() 
						{
							var imageAddr = "<?=_BASE_URL_?>css/adminPanel/<?=$cfg['THEME']?>/images/connectionChecker.jpg"; 
							var downloadSize = 17356;									
							var startTime, endTime;
							var download = new Image();
							
							download.onload = function () {
								endTime = (new Date()).getTime();
								showResults();
							}
							
							download.onerror = function (err, msg) {
							}
							
							startTime = (new Date()).getTime();
							var cacheBuster = "?nnn=" + startTime;
							download.src = imageAddr + cacheBuster;
							
							function showResults() {
								var duration = (endTime - startTime) / 1000;
								var bitsLoaded = downloadSize * 8;
								var speedBps = (bitsLoaded / duration);													
								var speedKbps = (speedBps / 1024);
								var speedMbps = (speedKbps / 1024);													
								var msg = (speedBps).toFixed(2) + " bps"
								if(speedBps>1126)  msg = (speedKbps).toFixed(2) + " kbps"
								if(speedKbps>1126)  msg = (speedMbps).toFixed(2) + " mbps"
								
								//$("span[popupRelatedSubObj=networkSpeed]").html("@"+msg);
							}
						} 
						
						function redirectionOfLink(obj)
						{
							var eLink = $(obj).attr("ehref");
							$("#content").find("#contentHeader").hide();
							$("#content").find(".container").hide();	
							$("#content").find("#menuRedirectionLoader").show();		
							window.location.href = eLink;
						}
						
						$(document).ready(function() {
							MeasureConnectionSpeedtest();
							setInterval(function(){MeasureConnectionSpeedtest();},300000);
							$('div[forType=messageDiv]').delay(4000).fadeOut('slow','linear');
							$('input,textarea').attr('autocomplete','off');
							$("div[use=loder]").hide();
							$("#content").find("#menuRedirectionLoader").hide();	
							$("#content").find(".container").show();
						});		
					</script>
				</head>
				<body>
					<?php
						if($cfg['LIFE_CYCLE']=='DEV')
						{
					?>
						<div style="background:#FF0000; color:#FFFF00; font-weight:bold; width:100%; z-index:99999999;"><marquee style="font-weight:bold; color:#FFFF00; font-size:11px;">This is a Development Area. Nothing is recorded.</marquee></div>
					<?php
						}
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
									<? webmaster_topNav_content();?>
								</div>
								<?php
									webmaster_leftbar_content();
								?>
								<div id="content">	
									<div id="contentHeader">
										<h1>
										<?=$headerTitle?>
										<?
										if($fullscreen)
										{
										?>
											
											<span status="OFF" class="btn btn btn-red" style="float:right; margin-right:30px; margin-bottom:10px; padding:5px 10px;" onClick="toggleLeftMenu(this);">Off</span>
											<span style="float:right; font-family:Arial, Helvetica, sans-serif;">Menu&nbsp;</span>
											<script>
											$(document).ready(function(){
												$("#content").attr('left_margn' ,$("#content").css("margin-left"));
												$("#content").css("margin-left","0px");
												$("#sidebar").hide();
											});
											
											function toggleLeftMenu(obj)
											{
												if($(obj).attr("status") == 'ON')
												{
													$("#content").css("margin-left","0px");
													$("#sidebar").hide();
													$(obj).attr("status","OFF")
													$(obj).removeAttr("class");
													$(obj).removeClass("btn btn btn-red").addClass("btn btn btn-red");
													$(obj).html("Off");
												}
												else
												{
													$("#content").css("margin-left",$("#content").attr('left_margn'));
													$("#sidebar").show();
													$(obj).attr("status","ON")
													$(obj).removeAttr("class");
													$(obj).removeClass("btn btn btn-blue").addClass("btn btn btn-blue");
													$(obj).html("On");
												}
											}
											</script>
										<?
										}
										?>
										</h1>
									</div>
					<?php
						}
					?>
	<?php
	}

	function webmaster_page_header_without_left_bar($headerTitle)
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
									<? webmaster_topNav_content();?>
								</div>
								<div id="contents">	
									<div id="contentHeader">
										<h1><?=$headerTitle?></h1>
									</div>
					<?php
					}
					?>
	<?php
	}
		
	/**********************************************************/
	/*               WEBMASTER PAGE FOOTER METHOD             */
	/**********************************************************/		
	function webmaster_page_footer($scr='&nbsp;')
	{
		global $cfg, $mycms;
	
		if($mycms->getPageName()!="login.php")
		{
	?>
					<div id="menuRedirectionLoader" use="menuRedirectionLoader">		
						<div>				
							<img src="<?=_BASE_URL_?>css/adminPanel/<?=$cfg['THEME']?>/images/loaders/facebook.gif" /><br/>
							<span popupRelatedSubObj="processingArea">...LOADING...</span><br/>
							<span popupRelatedSubObj="networkSpeed"></span>
						</div>
					</div>
				</div>
				<div id="footer">
					<span style="float:left;">This system is running on ENWI architecture</span>
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
				<div id="onFormSubmitOvrelay" popupRelatedObj="formSubmit"></div>
				<div id="onFormSubmitLoader" popupRelatedObj="formSubmitDetails">
					<!-- <div>
						<img src="<?=_BASE_URL_?>css/adminPanel/<?=$cfg['THEME']?>/images/loaders/big-roller.gif" /><br/>
						<span popupRelatedSubObj="processingArea">...PROCESSING...</span><br>
						<span popupRelatedSubObj="networkSpeed"></span>
					</div> -->
				</div>
				<script type="text/javascript">
					function onSubmitAction(preAction)
					{
						try{
							var preAct = true;
							try{ preAct = preAction(); } catch(error){}
							if(preAct)
							{
								$("div[popupRelatedObj=formSubmit]").toggle();
								$("div[popupRelatedObj=formSubmitDetails]").toggle();
							}
							return preAct;
						} catch(error){console.log(error);}
					}
				</script>				
				<script type="text/javascript" src="<?=_BASE_URL_?>js/adminPanel/all.js"></script>
			</body>
		</html>
	<?php
	}
	
	/**********************************************************/
	/*                WEBMASTER LEFT BAR METHOD               */
	/**********************************************************/	
	function webmaster_leftbar_content()
	{
		global $cfg, $mycms;
	?>		
		<div id="sidebar">
			<ul id="mainNav">
				<li id="navDashboard" class="nav active">
					<span class="icon-home"></span>
					<a href="<?=$cfg['DOMAIN_URL']?>"><?=getDomainName($cfg['DOMAIN_TAG'])?></a>
				</li>
				<?php
				$counterWebPageSection = 0;
				$result                = getSectionArray();
					
				foreach($result as $code=>$rowWebPageSection)
				{
				?>
					<li class="nav">
						<span class="<?=$rowWebPageSection['sectionImg']?>"></span>
						<a href="javascript:void(null);"><?=$rowWebPageSection['sectionName']?></a>				
						<ul class="subNav" <?=($rowWebPageSection['sectionTag']==$cfg['SECTION'])?"style='display: block;'":""?>>
						<?php

							foreach($rowWebPageSection['module'] as $keyModule=>$rowWebPageModule)
							{
						?>
								<li><a href="javascript:void(null);"><?=$rowWebPageModule['moduleName']?></a>
									<ul>
										<?php
											foreach($rowWebPageModule['page'] as $keyPage=>$rowWebPages)
											{
										?>
												<li><a href="<?=$cfg['DOMAIN_URL'].$rowWebPageSection['sectionPath'].$rowWebPages['pageFileName']?>"><?=$rowWebPages['pageName']?></a></li>
										<?php
											}
										?>
									</ul>
								</li>					
						<?php
							}
						?>
						</ul>
					</li>
				<?php
				}
				?>
			</ul>
		</div>
	<?php
	}
	
	/**********************************************************/
	/*              WEBMASTER TOP NAVIGATION METHOD           */
	/**********************************************************/
	function webmaster_topNav_content()
	{
		global $cfg, $mycms;
	?>
		<div id="topNav">
			<ul>
				<li>
					<a href="#menuProfile" class="menu"><?=$mycms->getLoggedUserName()?></a>
					<div id="menuProfile" class="menu-container menu-dropdown">
						<div class="menu-content">
							<ul class="">
								<li><a href="<?=$cfg['DOMAIN_URL']?>section_configuration/changePassword.php">Change Password</a></li>
								<li><a href="<?=$cfg['DOMAIN_URL']?>section_configuration/help.php">Help</a></li>
							</ul>
						</div>
					</div>
				</li>
				<?php
				if($cfg['DOMAIN_SCOPE']=="INTERNAL")
				{
				?>
					<li>
						<a href="#menuDomains" class="menu"><?=getDomainName($cfg['DOMAIN_TAG'])?></a>
						<?php
						$sqlSelectDomain['QUERY'] = "SELECT * FROM "._DB_CONF_DOMAIN_." 
													  WHERE `scope` = 'INTERNAL'";	
						
						$resultDomain    = $mycms->sql_select($sqlSelectDomain);
						$maxDomainCount  = $mycms->sql_numrows($resultDomain);
						if($maxDomainCount > 1)
						{
						?>
							<div id="menuDomains" class="menu-container menu-dropdown">
								<div class="menu-content">
									<ul class="">
									<?php
									foreach($resultDomain as $keyDomain=>$rowDomain)
									{
									?>
										<li><a href="<?=_BASE_URL_.$rowDomain['path']?>"><?=$rowDomain['name']?></a></li>
									<?php
									}
									?>
									</ul>
								</div>
							</div>
						<?php
						}
						?>
					</li>
				<?php
				}
				?>
				<li><a onClick="confirm('Do you realy want to Logout?')&&closeApplication()">Logout</a></li>
			</ul>
		</div>
	<?php
	}
	
	/**********************************************************/
	/*              WEBMASTER NOT ELLIGIBLE DISPLAY           */
	/**********************************************************/
	function webmaster_notElligible_display()
	{
		global $cfg, $mycms;	
	?>
		<div id="error-wrapper">
			<img src="<?=_DIR_CM_IMAGES_?>noAccess.png" height="200" width="200">
			<div id="error-code">Unauthorized Access</div>
			<div id="error-message">
				Sorry, you have no authority to access this page!
			</div>
			<div id="error-actions">
				<a href="<?=_BASE_URL_.getDomainUrl($cfg['DOMAIN_TAG'])?>" class="btn btn- btn-primary">Back to <?=getDomainName($cfg['DOMAIN_TAG'])?></a>
			</div>	
		</div>
	<?php		
	}
	
	function backButtonOffJS()
	{ 
		if(strtolower($_SERVER['HTTP_HOST'])=='localhost')
		{
			return;
		}
	?>
		<script language="javascript">
			$(document).bind('contextmenu', function (e) {
				e.preventDefault();
			});
		</script>
		
		<script type = "text/javascript" >
			history.pushState(null, null, '<?=basename($_SERVER['REQUEST_URI'])?>');
			window.addEventListener('popstate', function(event) {
				history.pushState(null, null, '<?=basename($_SERVER['REQUEST_URI'])?>');
			});
		</script>
	<?php 
	}
?>
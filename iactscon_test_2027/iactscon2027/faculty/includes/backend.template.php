<?php
	/**********************************************************/
	/*               WEBMASTER PAGE HEADER METHOD             */
	/**********************************************************/
	function webmaster_page_header($headerTitle)
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
						  <link href="<?= _DIR_CM_CSS_ . "website/" ?>custom-style.css?>" rel="stylesheet" type="text/css">
					<?php
					}
					?>
				</head>
				<body>
					<?php
						if($mycms->getPageName()!="login.php")
						{
					?>
					?>
	<?php
	}
	
	

	/**********************************************************/
	/*                WEBMASTER BREAD CUM METHOD              */
	/**********************************************************/	
	function webmaster_breadCumDefn($headerDisplay="")
	{
		global $cfg, $mycms;
	
	}
	
	
	
	/**********************************************************/
	/*                WEBMASTER LEFT BAR METHOD               */
	/**********************************************************/	
	function webmaster_leftbar_content()
	{
		global $cfg, $mycms;
	?>
	<div class="dash_lt">
		<div id="sidebar" class="menu_wrap">
			<ul id="mainNav" class="main_menu">
				<li id="navDashboard" class="main_menu_li nav active">
					<!-- <span class="icon-home"></span> -->
					<a class="non_sub" href="<?=$cfg['DOMAIN_URL']?>"><span class="menu_icon"><i class="fa-solid fa-house"></i></span>
					<span class="menu_txt_wrap"><span class="menu_txt"><?=getDomainName($cfg['DOMAIN_TAG'])?></span></span></a>
					
				</li>
				<?php
				$counterWebPageSection = 0;
				$result                = getSectionArray();
				
		
				foreach($result as $code=>$rowWebPageSection)
				{
				?>
					<li class="main_menu_li nav">
						<span class="<?=$rowWebPageSection['sectionImg']?>"></span>
						<a class="has-sub" href="javascript:;"><?=$rowWebPageSection['sectionName']?></a>	
								
						<ul class="subNav" <?=($rowWebPageSection['sectionTag']==$cfg['SECTION'])?"style='display: block;'":""?>>
						<?php
							foreach($rowWebPageSection['module'] as $keyModule=>$rowWebPageModule)
							{
						?>
								<li><a href="javascript:;"><?=$rowWebPageModule['moduleName']?></a>
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
		<div class="dash_lt_btm">
						MANAGED BY <img style="width: 88px; margin-left: 6px;" src="https://ruedakolkata.com/natcon_2025/webmaster/rueda_logo.png" alt="">
					</div>
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
								<li><a href="<?=$cfg['DOMAIN_URL']?>section_configuration/userProfile.php">Edit Profile</a></li>
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
	/*             WEBMASTER QUICK NAVIGATION METHOD          */
	/**********************************************************/
	function webmaster_quickNav_content()
	{
		global $cfg, $mycms;
?>	
		<div id="quickNav">
				<ul>
<?
			$bDay           = date('m-d');				
			$dateCondition = " AND delegate.user_dob LIKE '%-".$bDay."'";		
			if($resultFetchUser)
			{
				$staricon = "iconRed-star";
			}
			else
			{
				$staricon = "icon-star";
			}
?>
				<li class="quickNavMail">
						<a href="<?=_BASE_URL_?>webmaster/section_communication/birthDay.user_list.php" class="menu" onClick="window.location.href='<?=_BASE_URL_?>webmaster/section_communication/birthDay.user_list.php'"><span class="<?=$staricon?>"></span></a>
				</li>
			</ul>		
		</div>
<?
	}
	
	/**********************************************************/
	/*              WEBMASTER NOT ELLIGIBLE DISPLAY           */
	/**********************************************************/
	function webmaster_notElligible_display()
	{
		global $cfg, $mycms;	
	?>
		<div id="error-wrapper">
			<img src="<?=$cfg['DIR_CM_IMAGES']?>noAccess.png" height="200" width="200">
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
	
	/**********************************************************/
	/*              backButtonOffJS				           */
	/**********************************************************/
	function backButtonOffJS()
	{ 
		if(strtolower($_SERVER['HTTP_HOST'])=='localhost')
		{
			//return;
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
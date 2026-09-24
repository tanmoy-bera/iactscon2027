<?php
	function javaScriptDefinedValue()
	{
		global $cfg, $mycms;
	?>
		<script language="javascript">
			var jsBASE_URL	= "<?=_BASE_URL_?>";
			var CFG = { BASE_URL : "<?=_BASE_URL_?>" };
		</script>
	<?php
	}
	 
	function setTemplateStyleSheet()
	{
		global $cfg, $mycms;
	?>
		<meta name="viewport" content="width=device-width, initial-scale=1"> 
		<!-- <link rel="stylesheet" type="text/css" href="<?=_BASE_URL_."util/"?>fontawesome.v5.7.2/css/all.css" />
		<link rel="stylesheet" type="text/css" href="<?=_BASE_URL_."util/"?>bootstrap.3.3.7/css/bootstrap.min.css"  />-->
		<!-- <link rel="stylesheet" type="text/css" href="<?=_DIR_CM_CSS_."website/"?>input-material_css.php?link_color=<?=$cfg['link_color']?>" /> -->
        <link rel="stylesheet" type="text/css" href="<?=_DIR_CM_CSS_."website/"?>fronted.template.css" />
		<link rel="stylesheet" type="text/css" href="<?=_DIR_CM_CSS_."website/"?>all.css" />
	<?php
	}
		
	function setTemplateBasicJS()
	{
		global $cfg, $mycms;
		
		javaScriptDefinedValue();
	?>
		<script type="text/javascript" src="<?=_DIR_CM_JSCRIPT_."website/include/"?>jquery.3.3.1.min.js"></script>
		<!-- <script type="text/javascript" src="<?=_BASE_URL_."util/"?>bootstrap.3.3.7/js/bootstrap.min.js"></script> -->			
	<?php
	}
	
	function setTemplateAdditionalJS()
	{ 
		global  $cfg, $mycms;
	}
	
	function setProfileJS()
	{ 
		global  $cfg, $mycms;
	?>
		<script type="text/javascript" src="<?=_DIR_CM_JSCRIPT_."website/include/"?>jquery.3.3.1.min.js"></script>
		<!-- <script type="text/javascript" src="<?=_BASE_URL_."util/"?>bootstrap.3.3.7/js/bootstrap.min.js"></script> -->	
		<script type="text/javascript" src="<?=_DIR_CM_JSCRIPT_."website/"?>returnData.process.js"></script>
	<?
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
	
	function leftCommonMenu($usedAt='Others')
	{
		global  $cfg, $mycms;
		
		$delegateId 	= $mycms->getSession('LOGGED.USER.ID');
		
		// check if the login user has already submitted abstract
		if($delegateId != ''){
			//$resultAbstractType   = false;
		    $sql              = array();
		    $sql['QUERY']     = " SELECT * 
		                            FROM "._DB_ABSTRACT_REQUEST_." 
		                           WHERE `status` = ?
		                             AND `applicant_id` = ?";
		                            
		    $sql['PARAM'][]   = array('FILD' => 'status',         'DATA' =>'A',          'TYP' => 's');
		    $sql['PARAM'][]   = array('FILD' => 'applicant_id',   'DATA' =>$delegateId, 'TYP' => 's');
		    $resultAbstractType = $mycms->sql_select($sql);
		    
		}

		$sqlLogo 	=	array();
					$sqlLogo['QUERY'] = "SELECT * FROM "._DB_EMAIL_SETTING_." 
											WHERE `status`='A' order by id desc limit 1";
					 //$sql['PARAM'][]	=	array('FILD' => 'status' ,     		 'DATA' => 'A' ,       	           'TYP' => 's');					 
		$resultLogo = $mycms->sql_select($sqlLogo);
		$rowLogo    		 = $resultLogo[0];

		 $logo_image = _BASE_URL_.$cfg['EMAIL.HEADER.FOOTER.IMAGE'].$rowLogo['logo_image'];
		if($rowLogo['logo_image']!='')
		{
			$emailLogo  = $logo_image;
		}
		
		if($usedAt=="Profile")
		{


?>
	<div class="col-xs-1 profile-left-section" style="width: 11%;position: sticky;top: 0; padding: 0 9px 0 0px;">
		<!-- <div style="margin-top: 24px;"><a href="<?=_WEBSITE_BASE_URL_?>"><img src="<?=_BASE_URL_?>images/logocopy.png" alt="logo" class="right-side-logo"></a></div> -->
		<div style="margin-top: 24px;"><a href="<?=_WEBSITE_BASE_URL_?>"><img src="<?php echo $emailLogo; ?>" alt="logo" class="right-side-logo"></a></div>

<?php
			if($delegateId=='')
			{
?>
		<div><a href="<?=_BASE_URL_?>registration.tariff.php"><i class="fas fa-user-plus"></i><br>Register</a></div>		
		<div><a href="<?=_BASE_URL_?>login.php"><i class="fas fa-sign-in-alt"></i><br>Login</a></div>
<?
			}
?>		<? if(!$resultAbstractType && $cfg['ABSTRACT.SUBMIT.LASTDATE'] >= date('Y-m-d')) {?>
			<div><a href="<?=_BASE_URL_?>abstract.user.entrypoint.php"><i class="fas fa-book-medical" willBlink='Y' ></i><br>Abstract / Case Study Submission</a></div>
		<? } ?>
		<div><a href="<?=_BASE_URL_?>login.process.php?action=logout"><i class="fas fa-sign-out-alt"></i><br>Logout</a></div>
	</div>
<?
		}
		else
		{
?>
	<div class="col-xs-1 profile-left-section">
		<!-- <div style="margin-top: 24px;"><a href="<?=_WEBSITE_BASE_URL_?>"><img src="<?=_BASE_URL_?>images/logocopy.png" alt="logo" class="right-side-logo"></a></div> -->
		<div style="margin-top: 24px;"><a href="<?=_WEBSITE_BASE_URL_?>"><img src="<?php echo $emailLogo; ?>" alt="logo" class="right-side-logo"></a></div>

<?
			if($delegateId!='')
			{
?>		
		<div><a href="<?=_BASE_URL_?>profile.php"><i class="fas fa-user-md"></i><br>Profile</a></div>
<?
			}
			else
			{
?>
		<div><a href="<?=_BASE_URL_?>registration.tariff.php"><i class="fas fa-user-plus"></i><br>Register</a></div>	
<?
				if($usedAt!="Login")
				{
?>	
		<div><a href="<?=_BASE_URL_?>login.php"><i class="fas fa-sign-in-alt"></i><br>Login</a></div>
<?
				}
			}
?>
		<div><a href="<?=_BASE_URL_?>terms.php"><i class="fas fa-file-signature"></i><br> Terms & Conditions</a></div>
		<div><a href="<?=_BASE_URL_?>privacy.php"><i class="fas fa-shield-alt"></i><br>Privacy Policy</a></div>
		<div><a href="<?=_BASE_URL_?>cancellation.php"><i class="fas fa-undo-alt"></i><br>Cancellation & Refund Policy</a></div>
		 <? if( $cfg['ABSTRACT.EDIT.LASTDATE'] >= date('Y-m-d') && $delegateId=='') {?>
			<div><a href="<?=_BASE_URL_?>abstract.user.entrypoint.php"><i class="fas fa-undo-alt"></i><br>Click to abstract submission</a></div>
		<?php
		} 
		?>
		<!-- general footer start -->
		<div class="reg_footer common-footer">
			<div class="reg_footer_sec">
				<p>&copy;RUEDA. All rights reserved.</p>
				<a href="#"><img src="<?=_BASE_URL_?>images/rueda.png" alt="rueda"></a>
				<p>Conference Manager</p>
			</div>
		</div>
		<!-- general footer end -->
	</div>
	
<?
		}
?>
		<script>
			$(document).ready(function(){
				setInterval( function(){
					var color = ['#0078b3','#2cb8f4'];
					var today=new Date();
					var todaysec=today.getSeconds();
					
					$.each($("i[willBlink=Y]"),function(){
						var indx =  todaysec%2;
						//$(this).css('color',color[indx]);
					});
				},1000);
			});
		</script>
<?
	}
?>

        
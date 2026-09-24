<?php
function page_header($headerTitle)
{
	global $cfg, $mycms;
?>
	<!doctype html>
	<html class="no-js" lang="en">

	<head>
		<title>:: <?= $cfg['APP_NAME'] ?> :: <?= $headerTitle ?></title>
		<link href="<?= _BASE_URL_ ?>images/fav.png" rel="shortcut icon">
		<meta charset="utf-8" />
		<meta name="description" content="" />
		<meta name="author" content="" />
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
		<?php
		if ($mycms->getPageName() == "login.php" && $cfg['SECTION'] == "Login") {
		?>
			<link rel='stylesheet' href='https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.1/css/bootstrap.min.css'>
			<link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.7.0/css/all.css" integrity="sha384-lZN37f5QGtY3VHgisS14W3ExzMWZxybE1SJSEsQp9S+oqd12jhcu+A56Ebc1zFSJ" crossorigin="anonymous">
			<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css" integrity="sha512-Kc323vGBEqzTmouAECnVceyQqyqdsSiqLQISBL29aUW4U/M7pSPA/gEUZQqv1cwx4OnYxTxve5UMg5GT6L4JJg==" crossorigin="anonymous" referrerpolicy="no-referrer" />
			<link href="https://cdn.jsdelivr.net/gh/hung1001/font-awesome-pro@4cac1a6/css/all.css" rel="stylesheet" type="text/css" />
			<link rel="stylesheet" href="<?= _BASE_URL_ ?>webmaster/css/style.css" type="text/css" />
		<?php
		} else {
		?>
			<link rel="stylesheet" type="text/css" href="https://ruedakolkata.com/natcon2025/util/fontawesome.v5.7.2/css/all.css" />
			<link rel="stylesheet" type="text/css" href="https://ruedakolkata.com/natcon2025/css/website/input-material_css.php?link_color=" />
			<link rel="stylesheet" type="text/css" href="https://ruedakolkata.com/natcon2025/util/bootstrap.3.3.7/css/bootstrap.min.css" />
			<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.3.0/font/bootstrap-icons.css">
			<link rel="stylesheet" type="text/css" href="<?= _BASE_URL_ ?>faculty/css/review_style.css" />
			<link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.7.0/css/all.css" integrity="sha384-lZN37f5QGtY3VHgisS14W3ExzMWZxybE1SJSEsQp9S+oqd12jhcu+A56Ebc1zFSJ" crossorigin="anonymous">
		<?php
		}
		?>
		<script language="javascript" src="<?= _BASE_URL_ ?>js/jquery.js"></script>
		<script language="javascript" src="<?= _BASE_URL_ ?>js/adminPanel/architecture.js"></script>
		<script language="javascript" src="<?= _BASE_URL_ ?>js/print.js"></script>
		<script language="javascript" src="<?= _BASE_URL_ ?>js/excelReportGenerator.js"></script>
		<script language="javascript" src="<?= _BASE_URL_ ?>js/common.js"></script>
		<script language="javascript" type="text/javascript">
			function closeApplication() {
				var param = "act=<?= md5('logout') ?>";
				$.ajax({
					url: "<?= $cfg['DOMAIN_URL'] ?>section_login/login.php",
					type: "POST",
					data: param,
					dataType: "html",
					success: function(data) {
						location.href = "admin.php";
					}
				});
			}
			$(document).ready(function() {
				$('div[forType=messageDiv]').delay(10000).fadeOut('slow');
			});
		</script>
	</head>

	<body>
		<?php
		if ($mycms->getPageName() != "login.php") {
		?>
			<header>
				<h2>Reviewer Dashboard</h2>
				<div class="header_right">
					<h3><?= $mycms->getLoggedUserName() ?><span>Reviewer</span></h3>
					<a href="" onClick="confirm('Do you realy want to Logout?')&&closeApplication()"><i class="fas fa-power-off"></i></a>
				</div>
			</header>
			<?php
			// faculty_leftbar_content(); //

			?>

		<?php
		}
		?>
	<?php
}

function faculty_topNav_content()
{
	global $cfg, $mycms;
	$loggedUserID = $mycms->getLoggedUserId();
	$sqlAbstractTopic			  =	array();
	$sqlAbstractTopic['QUERY']    = "SELECT * FROM " . _DB_ABSTRACT_TOPIC_CATEGORY_ . " 
										  WHERE `status`='A'
									   ORDER BY `id` ASC";
									   
	$resultAbstractTopic = $mycms->sql_select($sqlAbstractTopic);
	?>

		<div class="review_btn_wrap">
			<?php
			$img_arr[0] = _BASE_URL_.'images/3rd-.apng'; //research.apng
			$img_arr[1] = _BASE_URL_.'images/case-report.apng';
					foreach ($resultAbstractTopic as $k => $val) {
						//echo '<pre>'; print_r($resultAbstractTopic);
						$sqlSubcat			  =	array();
						$sqlSubcat['QUERY']    = "SELECT * FROM " . _DB_ABSTRACT_SUBMISSION_ . " 
										  WHERE `status`='A' AND category='" . $val['id'] . "'
									   ORDER BY `id` ASC";

						$resultSubcat = $mycms->sql_select($sqlSubcat);

						$sqlCountAbstract			  =	array();
						$sqlCountAbstract['QUERY']    = "SELECT COUNT(*) COUNTDATA FROM " . _DB_ABSTRACT_REQUEST_ . "  
													  WHERE `status` = 'A' AND abstract_cat='" . $val['id'] . "'
												  AND id IN (SELECT abstract_id FROM " . _DB_ABSTRACT_ALLOTMENT_ . " WHERE review_user_id = '" . $loggedUserID . "')";


						$resultCountAbstract = $mycms->sql_select($sqlCountAbstract);

						//echo '<pre>'; print_r($sqlCountAbstract);			   
					?>
				<button onclick="window.location.href='../section_abstract/admin.php?cat=<?= $val['id'] ?>'" class="<?= $_REQUEST['cat'] == $val['id'] ? 'active' : '' ?>" data-tab="research"><img src="<?= $img_arr[$k]?>"  alt=""/><span><b><?= $val['category'] ?></b><i><?= $resultCountAbstract[0]['COUNTDATA'] ?> Templates</i></span></button>
			<?php } ?>
		</div>

	<?php
}

/**********************************************************/
					/*                  FACULTY LEFT BAR METHOD               */
/**********************************************************/
function faculty_leftbar_content()
{
	global $cfg, $mycms;
	$loggedUserID = $mycms->getLoggedUserId();
	$sqlAbstractTopic			  =	array();
	$sqlAbstractTopic['QUERY']    = "SELECT * FROM " . _DB_ABSTRACT_TOPIC_CATEGORY_ . " 
										  WHERE `status`='A'
									   ORDER BY `id` ASC";

	//$sqlAbstractTopic['PARAM'][]  = array('FILD' => 'status', 'DATA' =>'A',  'TYP' => 's');
	$resultAbstractTopic = $mycms->sql_select($sqlAbstractTopic);
	//echo '<pre>'; print_r($resultAbstractTopic);
	?>
		<div class="dash_lt">
			<div id="sidebar" class="menu_wrap">
				<ul id="mainNav" class="main_menu">
					<li id="navDashboard" class="main_menu_li nav active">
						<a class="non_sub" href="<?= $cfg['DOMAIN_URL'] ?>"><span class="menu_icon"><i class="fa-solid fa-house"></i></span>
							<span class="menu_txt_wrap"><span class="menu_txt">Reviewer's Panel</span></span></a>
					</li>
					<?php
					$counterWebPageSection = 0;
					$result                = getFacultySectionArray();
					?>

					<?php
					foreach ($resultAbstractTopic as $k => $val) {
						//echo '<pre>'; print_r($resultAbstractTopic);
						$sqlSubcat			  =	array();
						$sqlSubcat['QUERY']    = "SELECT * FROM " . _DB_ABSTRACT_SUBMISSION_ . " 
										  WHERE `status`='A' AND category='" . $val['id'] . "'
									   ORDER BY `id` ASC";

						$resultSubcat = $mycms->sql_select($sqlSubcat);

						$sqlCountAbstract			  =	array();
						$sqlCountAbstract['QUERY']    = "SELECT COUNT(*) COUNTDATA FROM " . _DB_ABSTRACT_REQUEST_ . "  
													  WHERE `status` = 'A' AND abstract_cat='" . $val['id'] . "'
												  AND id IN (SELECT abstract_id FROM " . _DB_ABSTRACT_ALLOTMENT_ . " WHERE review_user_id = '" . $loggedUserID . "')";


						$resultCountAbstract = $mycms->sql_select($sqlCountAbstract);

						//echo '<pre>'; print_r($sqlCountAbstract);			   
					?>

						<li class="nav main_menu_li" id="catPanel" onclick="getSub('<?= $val['id'] ?>')">

							<a class="non_sub" href="../section_abstract/all_abstract.free_paper.php?cat=<?= $val['id'] ?>">
								<span class="menu_icon"><i class="fa-solid fa-house"></i></span>
								<span class="menu_txt_wrap"><span class="menu_txt"><?= $val['category'] ?><strong> (<?= $resultCountAbstract[0]['COUNTDATA'] ?></strong>)</span></span></a>

						</li>
						<?php
						if (count($resultSubcat) > 0) {
							foreach ($resultSubcat as $key => $value) {
						?>
								<!--  <li class="nav subcat" id="subcatPanel<?= $val['id'] ?>" style="margin-left: 20px;"><span><a href="../section_abstract/all_abstract.free_paper.php?category=<?= $value['category'] ?>&sub=<?= $value['id'] ?>"><?= $value['abstract_submission'] ?></a></span></li> -->
					<?php
							}
						}
					}
					?>

					<!-- <li class="nav"><span><a href="../section_abstract/all_abstract.free_paper.php?goto=poster">Review Poster</a></span></li>
				<li class="nav"><span><a href="../section_abstract/all_abstract.free_paper.php?goto=video">Review Video</a></span></li> -->

				</ul>
			</div>
			<div class="dash_lt_btm">
				MANAGED BY <img style="width: 88px; margin-left: 6px;" src="https://ruedakolkata.com/natcon_2025/webmaster/rueda_logo.png" alt="">
			</div>
		</div>
	<?php
}

function dashboard_page_header($headerTitle)
{
	global $cfg, $mycms;
	?>
		<!doctype html>
		<html class="no-js" lang="en">

		<head>
			<title>:: <?= $cfg['APP_NAME'] ?> :: <?= $headerTitle ?></title>
			<link href="<?= _BASE_URL_ ?>images/fav.png" rel="shortcut icon">
			<meta charset="utf-8" />
			<meta name="description" content="" />
			<meta name="author" content="" />
			<meta name="viewport" content="width=device-width, initial-scale=1.0">
			<?php
			if ($mycms->getPageName() == "login.php" && $cfg['SECTION'] == "Login") {
			?>
				<link rel='stylesheet' href='https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.1/css/bootstrap.min.css'>
				<link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.7.0/css/all.css" integrity="sha384-lZN37f5QGtY3VHgisS14W3ExzMWZxybE1SJSEsQp9S+oqd12jhcu+A56Ebc1zFSJ" crossorigin="anonymous">
				<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css" integrity="sha512-Kc323vGBEqzTmouAECnVceyQqyqdsSiqLQISBL29aUW4U/M7pSPA/gEUZQqv1cwx4OnYxTxve5UMg5GT6L4JJg==" crossorigin="anonymous" referrerpolicy="no-referrer" />
				<link href="https://cdn.jsdelivr.net/gh/hung1001/font-awesome-pro@4cac1a6/css/all.css" rel="stylesheet" type="text/css" />
				<link rel="stylesheet" href="<?= _BASE_URL_ ?>webmaster/css/style.css" type="text/css" />
			<?php
			} else {
			?>
				<link rel='stylesheet' href='https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.1/css/bootstrap.min.css'>
				<link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.7.0/css/all.css" integrity="sha384-lZN37f5QGtY3VHgisS14W3ExzMWZxybE1SJSEsQp9S+oqd12jhcu+A56Ebc1zFSJ" crossorigin="anonymous">
				<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css" integrity="sha512-Kc323vGBEqzTmouAECnVceyQqyqdsSiqLQISBL29aUW4U/M7pSPA/gEUZQqv1cwx4OnYxTxve5UMg5GT6L4JJg==" crossorigin="anonymous" referrerpolicy="no-referrer" />
				<link href="https://cdn.jsdelivr.net/gh/hung1001/font-awesome-pro@4cac1a6/css/all.css" rel="stylesheet" type="text/css" />
				<link rel="stylesheet" href="<?= _BASE_URL_ ?>webmaster/css/style.css" type="text/css" />
			<?php
			}
			?>
			<script language="javascript" src="<?= _BASE_URL_ ?>js/jquery.js"></script>
			<script language="javascript" src="<?= _BASE_URL_ ?>js/adminPanel/architecture.js"></script>
			<script language="javascript" src="<?= _BASE_URL_ ?>js/print.js"></script>
			<script language="javascript" src="<?= _BASE_URL_ ?>js/excelReportGenerator.js"></script>
			<script language="javascript" src="<?= _BASE_URL_ ?>js/common.js"></script>
			<script language="javascript" type="text/javascript">
				function closeApplication() {
					var param = "act=<?= md5('logout') ?>";
					$.ajax({
						url: "<?= $cfg['DOMAIN_URL'] ?>section_login/login.php",
						type: "POST",
						data: param,
						dataType: "html",
						success: function(data) {
							location.href = "admin.php";
						}
					});
				}
				$(document).ready(function() {
					$('div[forType=messageDiv]').delay(10000).fadeOut('slow');
				});
			</script>
		</head>

		<body>
			<?php
			if ($mycms->getPageName() != "login.php") {
			?>
				<div id="wrapper" class="dash_wrap">
					<div class="header_wrap" id="contentHeaderX">
						<div class="logo_wrap">
							<img src="<?= _BASE_URL_ ?>images/logo_small.png" alt="">
							<a href="javascript:void(0)" class="menu-btn"><i class="fa-solid fa-bars"></i></a>
						</div>
						<style>
							.prf_head {
								display: flex;
								align-items: center;
								gap: 8px;
							}

							.prf_head span {
								width: 40px;
								height: 40px;
								border: 1px solid #ffffffba;
								border-radius: 50px;

							}

							.prf_head span img {
								width: 100%;
								height: 100%;
								object-fit: cover;
							}
						</style>
						<div class="head-rt">
							<h3>Admin Panel</h3>
							<div style="display: flex;align-items: center;gap: 20px;">
								<?php
								faculty_topNav_content();
								?>
							</div>
						</div>
					</div>

					<?php
					// faculty_leftbar_content();
					?>
					<div id="content" class="dash_rt">
					<?php
				}
					?>
					<?php
				}

				function breadCumDefn($headerDisplay = "")
				{
					global $cfg, $mycms;
					webmaster_breadCumDefn($headerDisplay);
				}



				/**********************************************************/
					/*              WEBMASTER TOP NAVIGATION METHOD           */
				/**********************************************************/


				function page_footer($scr = '&nbsp;')
				{
					global $cfg, $mycms;
					// webmaster_page_footer($scr);
				}
				/**********************************************************/
						/*               WEBMASTER PAGE FOOTER METHOD             */
				/**********************************************************/
				function webmaster_page_footer1($scr = '&nbsp;')
				{
					global $cfg, $mycms;

					if ($mycms->getPageName() != "login.php") {
					?>
					</div>
				</div>
				<!-- <div id="footer">
							<span style="">This system is running on ENWI architecture</span>
							<span style="float:right;"><?= $cfg['COPYRIGHT'] ?></span>
						</div> -->
				<style>
					.notify-defult {
						background-color: #dff0d8;
						color: #468847;
						border-color: #d6e9c6;
					}
				</style>
			<?php
					}
					$notificationClass = "";
					if ($_REQUEST['m'] != "") {
						if ($_REQUEST['m'] == 1 || $_REQUEST['m'] == 2) {
							$notificationClass = "notify-success";
						} else if ($_REQUEST['m'] == 3 || $_REQUEST['m'] == 0) {
							$notificationClass = "notify-error";
						} else if ($_REQUEST['m'] == 5) {
							$notificationClass = "notify-info";
						} else {
							$notificationClass = "notify-defult";
						}
			?>
				<div class="notify <?= $notificationClass ?>" forType="messageDiv">
					<p><?= $mycms->getDisplayMessage() ?></p>
				</div>
			<?php
					}
			?>
			<script src="<?= _BASE_URL_ ?>js/adminPanel/all.js"></script>
			<script language="javascript" src="<?= _BASE_URL_ ?>js/adminPanel/architecture.js"></script>
			<script language="javascript" src="<?= _BASE_URL_ ?>js/common.js"></script>
			<script language="javascript" type="text/javascript">
				function closeApplication() {
					var param = "act=<?= md5('logout') ?>";
					$.ajax({
						url: "<?= $cfg['DOMAIN_URL'] ?>section_login/login.php",
						type: "POST",
						data: param,
						dataType: "html",
						success: function(data) {
							location.href = "admin.php?cat=1";
						}
					});
				}

				$(document).ready(function() {
					$('div[forType=messageDiv]').delay(4000).fadeOut('slow', 'linear');
					$('input,textarea').attr('autocomplete', 'off');
				});

				var jsBASE_URL = "<?= _BASE_URL_ ?>";
				var CFG = {
					BASE_URL: "<?= _BASE_URL_ ?>"
				};
			</script>
		</body>

		</html>
	<?php
				}
	?>
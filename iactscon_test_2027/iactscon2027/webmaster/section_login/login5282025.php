<?php
include_once('includes/init.php');
page_header("Login");

if (isset($_GET['process']) && $_GET['process'] != '') {
	$accessKey				        = $mycms->decoded($_GET['process']);
	$accessKeyArray                	= explode("####", $accessKey);

	$action					        = $accessKeyArray[0];
	$_REQUEST['allow']              = $accessKeyArray[1];
}

switch ($action) {
	/***********************************************************/
	/*                     LOGIN OPERATION                     */
	/***********************************************************/
	case md5("login"):

		admin_loginProcess();
		break;

		/***********************************************************/
		/*                     LOGOUT OPERATION                    */
	/***********************************************************/
	case md5("logout"):
		$mycms->logout("login.php" . (isset($_REQUEST['m']) ? ('?m=' . $_REQUEST['m']) : ''));
		break;

	case md5("prelogin"):

		admin_preLoginProcess();
		break;

	case md5("loginScreen"):
		loginScreen($cfg, $mycms);
		break;

	case 'sendPassword':
		$user_email        = $_REQUEST['user_email'];
		$sqlFetchUser		=	array();
		$sqlFetchUser['QUERY']      = " SELECT * FROM " . _DB_CONF_USER_ . " 
										             WHERE `email` = ?,
												     LIMIT 0, 1";
		$sqlFetchUser['PARAM'][]	=	array('FILD' => 'email', 	  'DATA' => $user_email,             'TYP' => 's');
		$resultFetchUser   = $mycms->sql_select($sqlFetchUser);
		if ($resultFetchUser) {
			$rowFetchUser  = $resultFetchUser[0];
			$messageString = "";
			$messageString = "<table width='100%' border='0'>
									<tr>
									<td>
									Dear<br />
									" . $rowFetchUser['name'] . "<br /><br />
									Following is your login details.<br /><br />
									Username: " . $rowFetchUser['username'] . "<br />
									Password: " . $mycms->decoded($rowFetchUser['password']) . "<br />
									Url: <a href='" . $cfg['BASE_URL'] . "webmaster'>" . $cfg['BASE_URL'] . "/webmaster</a><br />	
									</td>
									</tr>
									<tr>
									<td align='right'>
									Thanking You<br />
									" . $cfg['ADMIN_NAME'] . "<br />
									Date: " . date('d/m/Y') . "<br />	
									</td>
									</tr>
								  </table>";
			$messageString = trim($messageString);
			$mycms->send_mail($rowFetchUser['name'], $user_email, "DO NOT REPLY", $messageString);
		}
		break;

	default:
		preloginScreen($cfg, $mycms);
		break;
}

page_footer();

function loginScreen($cfg, $mycms)
{
?>
	<script language="javascript" src="scripts/md5.js?x=<?= date('Ymdhis') ?>"></script>
	<script language="javascript" src="scripts/login.js?x=<?= date('Ymdhis') ?>"></script>

	<?php include_once("includes/header.php"); ?>

	<div class="login_body">
		<?php
		$sqlSuccessImg    =   array();
		$sqlSuccessImg['QUERY'] = "SELECT * FROM " . _DB_LANDING_FLYER_IMAGE_ . " 
							 WHERE title='Webmaster Background' ";

		$resultSuccessImg      = $mycms->sql_select($sqlSuccessImg);
		$resultSuccessImg = _BASE_URL_ . $cfg['EMAIL.HEADER.FOOTER.IMAGE'] . $resultSuccessImg[0]['image']; ?>

		
		<?php if (pathinfo($resultSuccessImg, PATHINFO_EXTENSION) != 'mp4') { ?>
			<img src="<?php echo $resultSuccessImg; ?>">
		<?php } else { ?>
			<video autoplay="" loop="" playsinline="" muted="">
				<source src="<?= $resultSuccessImg ?>" type="video/mp4">
			</video>

		<?php } ?>
		<div class="login_body_lft">
			<h4><?php echo $cfg['FULL_CONF_NAME']; ?></h4>
			<h2> <?php echo $cfg['EMAIL_CONF_NAME']; ?></h2>
			<p><i class="fal fa-calendar mr-2"></i> <?= date('j M Y', strtotime($cfg['CONF_START_DATE'])) . " - " . date('j M Y', strtotime($cfg['CONF_END_DATE'])) ?><br>
				<i class="fal fa-hotel mr-2"></i> <?= $cfg['EMAIL_CONF_VENUE'] ?>
			</p>
		</div>
		<div class="login_body_rt">
			<form name="frm_login" action="login.php" onsubmit="return loginFormValidator()" method="post">
				<input id="act" type="hidden" value='<?= md5("login") ?>' name="act">
				<input id="usrTyps" type="hidden" value='D,A,U' name="usrTyps">
				<h2 class="mb-5">Login</h2>
				<div class="input-field">
					<input type="text" name="uname" value="<?= $_REQUEST['allow'] ?>" id="uname" required="">
					<label> Username </label>
				</div>
				<div class="input-field">
					<input type="password" name="pass" value="" id="pass" required="">
					<label> Password </label>
				</div>

				<input type="submit" class="wirzaButton" value="Login">
				<span id="loginMessageBox" style="float:right; color:#FF0000;"><?= $_REQUEST['msg'] ?></span>
			</form>
		</div>
	</div>
	<?php include_once("includes/footer.php"); ?>

<?php
}

function preloginScreen($cfg, $mycms)
{
?>
	<script language="javascript" src="scripts/md5.js?x=<?= date('Ymdhis') ?>"></script>
	<script language="javascript" src="scripts/login.js?x=<?= date('YmdHis') ?>"></script>


	<?php include_once("includes/header.php"); ?>

	<div class="login_body">
		<?php
		$sqlSuccessImg    =   array();
		$sqlSuccessImg['QUERY'] = "SELECT * FROM " . _DB_LANDING_FLYER_IMAGE_ . " 
							 WHERE title='Webmaster Background' ";

		$resultSuccessImg      = $mycms->sql_select($sqlSuccessImg);
		$resultSuccessImg = _BASE_URL_ . $cfg['EMAIL.HEADER.FOOTER.IMAGE'] . $resultSuccessImg[0]['image']; ?>

		<!-- <video autoplay="" loop="" playsinline="" muted="">
			<source src="<?= $resultSuccessImg ?>" type="video/mp4">
		</video> -->
		<?php if (pathinfo($resultSuccessImg, PATHINFO_EXTENSION) != 'mp4') { ?>
			<img src="<?php echo $resultSuccessImg; ?>">
		<?php } else { ?>
			<video autoplay="" loop="" playsinline="" muted="">
				<source src="<?= $resultSuccessImg ?>" type="video/mp4">
			</video>

		<?php } ?>
		<div class="login_body_lft">
			<h4><?php echo $cfg['FULL_CONF_NAME']; ?></h4>
			<h2> <?php echo $cfg['EMAIL_CONF_NAME']; ?></h2>
			<p><i class="fal fa-calendar mr-2"></i> <?= date('j M Y', strtotime($cfg['CONF_START_DATE'])) . " - " . date('j M Y', strtotime($cfg['CONF_END_DATE'])) ?><br>
				<i class="fal fa-hotel mr-2"></i> <?= $cfg['EMAIL_CONF_VENUE'] ?>
			</p>
		</div>
		<div class="login_body_rt">
			<form name="frm_login" action="login.php" onsubmit="return loginFormValidator()" method="post">
				<input id="act" type="hidden" value='<?= md5("prelogin") ?>' name="act">
				<input id="usrTyps" type="hidden" value='D,A,U' name="usrTyps">
				<h2 class="mb-5">Login</h2>
				<div class="input-field">
					<input type="text" name="uname" value="<?= $_REQUEST['allow'] ?>" id="uname" onblur='$("#loginMessageBox").html("");'>
					<label> Username</label>
				</div>

				<input type="submit" class="wirzaButton" value="Proceed">
				<span id="loginMessageBox" style="float:right; color:#FF0000; font-weight:bold;"><?= $_REQUEST['msg'] ?></span>
			</form>
		</div>
	</div>
	<?php include_once("includes/footer.php"); ?>
<?php
}
?>
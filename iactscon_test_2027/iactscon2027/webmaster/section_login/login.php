<?php
include_once("../../webmaster/includes/source.php");
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
		loginScreen($cfg, $mycms);
		break;
}

page_footer();

function loginScreen($cfg, $mycms)
{
?>
	<script language="javascript" src="scripts/md5.js?x=<?= date('Ymdhis') ?>"></script>
	<script language="javascript" src="scripts/login.js?x=<?= date('Ymdhis') ?>"></script>

	<?php // include_once("includes/header.php"); 
	?>
	<link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.7.0/css/all.css" integrity="sha384-lZN37f5QGtY3VHgisS14W3ExzMWZxybE1SJSEsQp9S+oqd12jhcu+A56Ebc1zFSJ" crossorigin="anonymous">
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css" integrity="sha512-Kc323vGBEqzTmouAECnVceyQqyqdsSiqLQISBL29aUW4U/M7pSPA/gEUZQqv1cwx4OnYxTxve5UMg5GT6L4JJg==" crossorigin="anonymous" referrerpolicy="no-referrer" />
	<link href="https://cdn.jsdelivr.net/gh/hung1001/font-awesome-pro@4cac1a6/css/all.css" rel="stylesheet" type="text/css" />
	<link rel="stylesheet" href="../css/style.css">
	<link rel="stylesheet" href="../css/responcive.css">
	<body>
		<?php
		$sqlSuccessImg    =   array();
		$sqlSuccessImg['QUERY'] = "SELECT * FROM " . _DB_LANDING_FLYER_IMAGE_ . " 
							 WHERE title='Webmaster Background' ";

		$resultSuccessImg      = $mycms->sql_select($sqlSuccessImg);
		$resultSuccessImg = _BASE_URL_ . $cfg['EMAIL.HEADER.FOOTER.IMAGE'] . $resultSuccessImg[0]['image']; 
		?>

		
	  <div class="login_wrap">
        <div class="login_left">
			<?php if (pathinfo($resultSuccessImg, PATHINFO_EXTENSION) != 'mp4') { ?>
				<img class="login-back" src="<?php echo $resultSuccessImg; ?>">
			<?php } else { ?>
				<video autoplay="" loop="" playsinline="" muted="">
					<source src="<?= $resultSuccessImg ?>" type="video/mp4">
				</video>

			<?php } ?>        
	   </div>
        <div class="login_right">
			<div class="login_right_top">
                <h2><?php echo $cfg['EMAIL_CONF_NAME']; ?></span></h2>
				<p><?=$cfg['FULL_CONF_NAME']?></p>
				<ol>
					<li>
						<span class=""><i class='fal fa-map-marker-alt'></i></span>
						<h6>
							<n>venue</n>
							<g><?= $cfg['EMAIL_CONF_VENUE'] ?></g>
						</h6>
					</li>
					<li>
						<span class=""><i class='fal fa-calendar'></i></span>
						<h6>
							<n>date</n>
							<g><?= date('j M Y', strtotime($cfg['CONF_START_DATE'])) . " - " . date('j M Y', strtotime($cfg['CONF_END_DATE'])) ?></g>
						</h6>
					</li>
				</ol>
	         </div>
			<form name="frm_login"  class="login_right_bottom"  action="login.php" onsubmit="return loginFormValidator()" method="post">
			<h4>Admin Control Terminal</h4>
			<input id="act" type="hidden" value='<?= md5("login") ?>' name="act">
			<input id="usrTyps" type="hidden" value='D,A,U' name="usrTyps">
            <div class="form_grid">
                <div class="login_frm_wrap span_4">
                    <label>User</label>
                    <div>
                        <i class="fal fa-envelope"></i> <input type="text"  name="uname" value="<?= $_REQUEST['allow'] ?>" id="uname" required="" placeholder="Enter Your User Name" autocomplete="off">
                    </div>
                </div>
                <div class="login_frm_wrap span_4">
                    <label>Password</label>
                <div class="password-wrapper">
					<i class="fal fa-lock"></i>
					
					<input type="password" name="pass" id="pass" required autocomplete="off">

					<button type="button" id="togglePassword">
						<i class="fal fa-eye-slash"></i>
					</button>
				</div>
                </div>
                <div class="registration_btn_wrap span_4">
                <button type="submit" value="Login" class="mi-1  w-100">Secure Login<i class="fal fa-sign-in ml-2"></i></button>
            </div>
	      </form>
            </div>
            
        </div>
    </div>
	</div>
	<?php // include_once("includes/footer.php"); 
	?>

<?php
}

function preloginScreen($cfg, $mycms)
{
?>
	<script language="javascript" src="scripts/md5.js?x=<?= date('Ymdhis') ?>"></script>
	<script language="javascript" src="scripts/login.js?x=<?= date('YmdHis') ?>"></script>
	<link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.7.0/css/all.css" integrity="sha384-lZN37f5QGtY3VHgisS14W3ExzMWZxybE1SJSEsQp9S+oqd12jhcu+A56Ebc1zFSJ" crossorigin="anonymous">
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css" integrity="sha512-Kc323vGBEqzTmouAECnVceyQqyqdsSiqLQISBL29aUW4U/M7pSPA/gEUZQqv1cwx4OnYxTxve5UMg5GT6L4JJg==" crossorigin="anonymous" referrerpolicy="no-referrer" />
	<link href="https://cdn.jsdelivr.net/gh/hung1001/font-awesome-pro@4cac1a6/css/all.css" rel="stylesheet" type="text/css" />
	<link rel="stylesheet" href="../css/style.css">
	<link rel="stylesheet" href="../css/responcive.css">

	<body>
		<?php // include_once("includes/header.php"); 
		?>
		<?php
		$sqlSuccessImg    =   array();
		$sqlSuccessImg['QUERY'] = "SELECT * FROM " . _DB_LANDING_FLYER_IMAGE_ . " 
							 WHERE title='Webmaster Background' ";

		$resultSuccessImg      = $mycms->sql_select($sqlSuccessImg);
		$resultSuccessImg = _BASE_URL_ . $cfg['EMAIL.HEADER.FOOTER.IMAGE'] . $resultSuccessImg[0]['image']; ?>

		<!-- <video autoplay="" loop="" playsinline="" muted="">
			<source src="<?= $resultSuccessImg ?>" type="video/mp4">
		</video> -->
		
      <div class="login_wrap">
        <!-- <div class="login_left">
       <?php if (pathinfo($resultSuccessImg, PATHINFO_EXTENSION) != 'mp4') { ?>
			<img class="login-back" src="<?php echo $resultSuccessImg; ?>">
		<?php } else { ?>
			<video autoplay="" loop="" playsinline="" muted="">
				<source src="<?= $resultSuccessImg ?>" type="video/mp4">
			</video>

		<?php } ?>        </div> -->
        <div class="login_right">
			<div class="login_right_top">
				<h2><?php echo $cfg['EMAIL_CONF_NAME']; ?></span></h2>
				<p>73rd Annual Conference of the Indian Association of Cardiovascular-Thoracic Surgeons(IACTS).</p>
				<ol>
					<li>
						<span class=""><i class='fal fa-map-marker-alt'></i></span>
						<h6>
							<n>venue</n>
							<g><?= $cfg['EMAIL_CONF_VENUE'] ?></g>
						</h6>
					</li>
					<li>
						<span class=""><i class='fal fa-calendar'></i></span>
						<h6>
							<n>date</n>
							<g><?= date('j M Y', strtotime($cfg['CONF_START_DATE'])) . " - " . date('j M Y', strtotime($cfg['CONF_END_DATE'])) ?></g>
						</h6>
					</li>
				</ol>
	        </div>
			<form name="frm_login" class="login_right_bottom" action="login.php" onsubmit="return loginFormValidator()" method="post">
				<h4>Admin Control Terminal</h4>
			    <input id="act" type="hidden" value='<?= md5("prelogin") ?>' name="act">
				<input id="usrTyps" type="hidden" value='D,A,U' name="usrTyps">
            <div class="form_grid" >
                <div class="login_frm_wrap span_4">
                    <label>User</label>
                    <div>
                        <i class="fal fa-envelope"></i> <input type="text" placeholder="doctor@hospital.com" name="uname" value="<?= $_REQUEST['allow'] ?>" id="uname" onblur='$("#loginMessageBox").html("");'>
                    </div>
                </div>
              
                <div class="registration_btn_wrap span_4">
                <button type="submit"  value="Proceed" class="mi-1  w-100">Secure Login<i class="fal fa-sign-in ml-2"></i></button>
            </div>
	        </form>
            </div>
            
        </div>
    </div>
		<!-- <div class="login_body_rt">
			<div>
				<h4><?php echo $cfg['FULL_CONF_NAME']; ?></h4>
				<h2> <?php echo $cfg['EMAIL_CONF_NAME']; ?></h2>
				<p><i class="fal fa-calendar mr-2"></i> <?= date('j M Y', strtotime($cfg['CONF_START_DATE'])) . " - " . date('j M Y', strtotime($cfg['CONF_END_DATE'])) ?><br>
					<i class="fal fa-hotel mr-2"></i> <?= $cfg['EMAIL_CONF_VENUE'] ?>
				</p>
			</div>
			<form name="frm_login" action="login.php" onsubmit="return loginFormValidator()" method="post">
				<input id="act" type="hidden" value='<?= md5("prelogin") ?>' name="act">
				<input id="usrTyps" type="hidden" value='D,A,U' name="usrTyps">
				<h2 class="mb-5">Login</h2>
				<div class="input-field">
					<input type="text" name="uname" value="<?= $_REQUEST['allow'] ?>" id="uname" onblur='$("#loginMessageBox").html("");'>
					<label>Username</label>
				</div>
				<input type="submit" class="wirzaButton" value="Proceed">
				<span id="loginMessageBox" style="float:right; color:#FF0000; font-weight:bold;"><?= $_REQUEST['msg'] ?></span>
			</form>
			<header>
				<p>
					<a href=""><span><i class="fas fa-phone-alt"></i></span><?= $cfg['EMAIL_CONF_CONTACT_US'] ?></a>
				</p>
			</header>
		</div> -->
		<?php  include_once("../../webmaster/includes/js-source.php");?>

	</body>
	<?php // include_once("includes/footer.php"); 
	?>
<?php
}
?>
<script>
	let vh = window.innerHeight * 0.01;
	document.documentElement.style.setProperty('--vh', `${vh}px`);
</script>
<script>
    const toggleBtn = document.getElementById("togglePassword");
    const passwordInput = document.getElementById("pass");
    const icon = toggleBtn.querySelector("i");

    toggleBtn.addEventListener("click", function () {
        if (passwordInput.type === "password") {
            passwordInput.type = "text";
            icon.classList.remove("fa-eye-slash");
            icon.classList.add("fa-eye");
        } else {
            passwordInput.type = "password";
            icon.classList.remove("fa-eye");
            icon.classList.add("fa-eye-slash");
        }
    });
</script>
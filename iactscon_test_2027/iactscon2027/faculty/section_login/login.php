<?php
include_once("../../webmaster/includes/source.php");
include_once('includes/init.php');
page_header("");

switch ($action) {
		/***********************************************************/
		/*                     LOGIN OPERATION                     */
		/***********************************************************/
	case md5("login"):
     
		faculty_login_process();
		break;

		/***********************************************************/
		/*                     LOGOUT OPERATION                    */
		/***********************************************************/
	case md5("logout"):

		$mycms->logout("login.php" . (isset($_REQUEST['m']) ? ('?m=' . $_REQUEST['m']) : ''));
		break;
		
}
?>
<script language="javascript" src="scripts/login.js"></script>
	<body>
		<?php
		$sqlSuccessImg    =   array();
		$sqlSuccessImg['QUERY'] = "SELECT * FROM " . _DB_LANDING_FLYER_IMAGE_ . " 
							 WHERE title='Webmaster Background' ";

		$resultSuccessImg      = $mycms->sql_select($sqlSuccessImg);
		$resultSuccessImg = _BASE_URL_ . $cfg['EMAIL.HEADER.FOOTER.IMAGE'] . $resultSuccessImg[0]['image']; 

		$sqlMSG   =  array();
		$sqlMSG['QUERY']    = "SELECT * FROM " . _DB_COMPANY_INFORMATION_ . " 
					WHERE `id` = 1";
		$result       = $mycms->sql_select($sqlMSG);
		$companyInfo =  $result[0];
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
                <h2><?php echo $companyInfo['company_conf_name']; ?></span></h2>
				<p><?php echo $companyInfo['company_conf_full_name']; ?></p>
				<ol>
					<li>
						<span class=""><i class='fal fa-map-marker-alt'></i></span>
						<h6>
							<n>venue</n>
							<g><?= $companyInfo['company_conf_venue'] ?></g>
						</h6>
					</li>
					<li>
						<span class=""><i class='fal fa-calendar'></i></span>
						<h6>
							<n>date</n>
							<g><?= date('j M Y', strtotime($companyInfo['conf_start_date'])) . " - " . date('j M Y', strtotime($companyInfo['conf_end_date'])) ?></g>
						</h6>
					</li>
				</ol>
	         </div>
			<form name="frm_login"  class="login_right_bottom"  action="login.php" onsubmit="return loginFormValidator()" method="post">
			<h4>Review Login</h4>
			<input id="act" type="hidden" value='<?= md5("login") ?>' name="act">
			<input id="usrTyps" type="hidden" value='D,A,U' name="usrTyps">
            <div class="form_grid">
                <div class="login_frm_wrap span_4">
                    <label>User</label>
                    <div>
                        <i class="fal fa-envelope"></i> <input type="text"  name="username" value="" id="username" required placeholder="Enter Your User Name" autocomplete="off">
                    </div>
                </div>
                <div class="login_frm_wrap span_4">
                    <label>Password</label>
                <div class="password-wrapper">
					<i class="fal fa-lock"></i>
					
					<input type="password" name="password" id="password" required autocomplete="off">

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
     <?php  include_once("../../webmaster/includes/js-source.php");?>

</body>
<!-- <div id="login" class="login_body">

		<img class="login-back" src="https://ruedakolkata.com/natcon2025/uploads/EMAIL.HEADER.FOOTER.IMAGE/WEBMASTER_BACKGROUND_0047_250528140101.jpeg">

	<div id="login_panel" class="login_body_rt" fortype="loginPanel">
		<div>
			<h4></h4>
			<h2> NATCON 2025</h2>
			<p><i class="fal fa-calendar mr-2"></i> 19 Dec 2025 - 21 Dec 2025<br>
				<i class="fal fa-hotel mr-2"></i> The Westin</p>
		</div>
		<form name="frm_login" action="login.php" onsubmit="return loginFormValidator()" method="post">
			<input type="hidden" name="act" value="<?= md5("login") ?>" />
			<input type="hidden" name="user_type" value="Faculty" />
			<h2 class="mb-5">Review Login</h2>
			<div class="input-field">
				<input type="text" name="username" id="username" value="" />
				<label>Username</label>
			</div>
			<div class="input-field">
				<input type="password" name="password" id="password" value="" />
				<label for="password">Password</label>
			</div>
			<input type="submit" class="wirzaButton" value="Login">
			<span id="loginMessageBox" style="float:right; color:#FF0000;"><?= $_REQUEST['msg'] ?></span>
			
		</form>
	</div>

	<div id="login_panel" fortype="forgetPasswordPanel" style="display:none;">
		<div class="login_fields">
			<div class="field">
				<label for="email">Email Id</label>
				<input type="text" name="user_email" value="" id="user_email" placeholder="email id" />
			</div>

			<div class="field" style="height:47px;">
				<label for="username"><small><a href="javascript:;" onclick="openLoginPopup()">Back To Login</a></small></small></label>
			</div>
		</div>
		<div class="login_actions">
			<input type="button" class="btn btn-primary" value="Send" onclick="emailRetriver()">
			<span id="forgetPasswordMessageBox" style="float:right; color:#FF0000;"><?= $_REQUEST['msg'] ?></span>
		</div>
	</div>

</div> -->
<?php
page_footer();
?>
<script>
	let vh = window.innerHeight * 0.01;
	document.documentElement.style.setProperty('--vh', `${vh}px`);
</script>
<script>
    const toggleBtn = document.getElementById("togglePassword");
    const passwordInput = document.getElementById("password");
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
<?php include_once("includes/source.php"); ?>
<?php   
	header("Location: section_login/");
?>
<body>

    <div class="login_wrap">
        <div class="login_left">
            <img src="https://ruedakolkata.com/natcon2025/uploads/EMAIL.HEADER.FOOTER.IMAGE/WEBMASTER_BACKGROUND_0047_250528140101.jpeg">
        </div>
        <div class="login_right">
            <h2>NATCON <span>2025</span></h2>
            <p>The 80th National Conference on Tuberculosis & Chest Diseases.</p>
            <ol>
                <li>
                    <span class="badge_default"><?php address(); ?></span>
                    <h6>
                        <n>venue</n>
                        <g>Biswa Bangla Centre</g>
                    </h6>
                </li>
                <li>
                    <span class="badge_default"><?php calendar(); ?></span>
                    <h6>
                        <n>date</n>
                        <g>Dec 19 - 21, 2025</g>
                    </h6>
                </li>
            </ol>
            <h4>Admin Control Terminal</h4>
            <div class="form_grid">
                <div class="login_frm_wrap span_4">
                    <label>User</label>
                    <div>
                        <i class="fal fa-envelope"></i> <input type="email" placeholder="doctor@hospital.com">
                    </div>
                </div>
                <div class="login_frm_wrap span_4">
                    <label>Password</label>
                    <div>
                        <i class="fal fa-lock"></i>
                        <input type="email" placeholder="Password">
                        <button><i class="fal fa-eye-slash"></i></button>
                    </div>
                </div>
                <div class="registration_btn_wrap span_4">
                <button type="submit" onclick="window.location.href='dashboard.php'" class="mi-1 badge_success w-100">Secure Login<i class="fal fa-sign-in ml-2"></i></button>
            </div>
            </div>
            
        </div>
    </div>

</body>
<?php include_once("includes/js-source.php"); ?>

</html>
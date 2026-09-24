<div class="header_user">
    <button class="notification icon_hover badge_danger action-transparent" onclick="$('.user_drop').removeClass('active'); $('.prf_head').removeClass('active'); $('.noti_drop').toggleClass('active'); $('.notification').toggleClass('active');"><span></span><?php bell(); ?></button>
    <hr>
    <a class="prf_head icon_hover badge_dark action-transparent" onclick="$('.user_drop').toggleClass('active'); $('.prf_head').toggleClass('active'); $('.noti_drop').removeClass('active'); $('.notification').removeClass('active');"><span><img  href="#menuProfile"  alt=""></span><?=$mycms->getLoggedUserName()?></a><hr>
    <!-- <div id="menuProfile" class="menu-container menu-dropdown">
        <div class="menu-content">
            <ul class="">
                <li><a href="<?=$cfg['DOMAIN_URL']?>section_configuration/changePassword.php">Change Password</a></li>
                <li><a href="<?=$cfg['DOMAIN_URL']?>section_configuration/help.php">Help</a></li>
            </ul>
        </div>
    </div> -->
    <div class="user_drop">
        <ul>
            <a onClick="confirm('Do you realy want to Logout?')&&closeApplication()" class="icon_hover badge_danger action-transparent"><?php logout(); ?>Log Out</a>
        </ul>
    </div>
    <div class="noti_drop">
        <ul>
            <li>
                <span>
                    <n><?php clock(); ?>16:39</n>
                    <n><?php calendar(); ?>1/12/2025</n>
                </span>
                <p>Manage registrations, track payments, and view participant details.</p>
            </li>
        </ul>
    </div>
</div>
<script language="javascript" type="text/javascript">
    function closeApplication()
    {
        var param = "act=4236a440a662cc8253d7536e5aa17942";

        $.ajax({
                url: "<?=$cfg['DOMAIN_URL']?>section_login/login.php",
                type: "POST",
                data: param,
                dataType: "html",
                success: function(data){
                    location.href="<?=$cfg['DOMAIN_URL']?>section_login/login.php";
                }
            }
        );
    }
</script>
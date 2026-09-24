<div class="header_user">
    <button class="notification icon_hover badge_danger action-transparent" onclick="$('.user_drop').removeClass('active'); $('.prf_head').removeClass('active'); $('.noti_drop').toggleClass('active'); $('.notification').toggleClass('active');"><span></span><?php bell(); ?></button>
    <hr>
    <a class="prf_head icon_hover badge_dark action-transparent" onclick="$('.user_drop').toggleClass('active'); $('.prf_head').toggleClass('active'); $('.noti_drop').removeClass('active'); $('.notification').removeClass('active');"><span><img src="" alt=""></span>endeveloper</a>
    <div class="user_drop">
        <ul>
            <a href="#" class="icon_hover badge_danger action-transparent"><?php logout(); ?>Log Out</a>
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
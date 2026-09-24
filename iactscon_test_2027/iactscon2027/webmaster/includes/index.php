<?php include_once("includes/source.php"); ?>

<body>
    <div class="container position-relative">
        <div class="row">
            <div class="login_head d-none">
                <h6><span>Dec 19-21, 2025</span></h6>
                <!-- <h2>NATCON <span>2025</span></h2> -->
                 
                <p>Kolkata, India</p>
            </div>
            <div class="login_wrap">
                <div class="login_left">
                    <h3 class="con_logo"><img src="images/logo.png" alt=""></h3>
                    <h2>Reshaping <span>Pulmonology.</span></h2>
                    <p>The 80th National Conference on Tuberculosis & Chest Diseases. Join the most prestigious gathering of physicians in the City of Joy.</p>
                    <ol>
                        <li>
                            <span><?php address(); ?></span>
                            <h6>
                                <n>venue</n>
                                <g>Biswa Bangla Centre</g>
                            </h6>
                        </li>
                        <hr>
                        <li>
                            <span><?php calendar(); ?></span>
                            <h6>
                                <n>date</n>
                                <g>Dec 19 - 21, 2025</g>
                            </h6>
                        </li>
                    </ol>
                    <ul id="registration_countdown">
                        <li>
                            <n><span id="days">00</span></n><i>days</i>
                        </li>
                        <hr>
                        <li>
                            <n><span id="hours">00</span></n><i>Hours</i>
                        </li>
                        <hr>
                        <li>
                            <n><span id="minutes">00</span></n><i>Minutes</i>
                        </li>
                        <hr>
                        <li>
                            <n><span id="seconds">00</span></n><i>Seconds</i>
                        </li>
                    </ul>
                    <h3 id="registration_closed" style="display: none;">Registration Closed</h3>
                </div>
                <div class="login_right">
                    <h4>Delegate Access</h4>
                    <p>Secure your spot or manage your submission</p>
                    <div class="login_frm_wrap">
                        <label>Email Address</label>
                        <div>
                            <?php email(); ?>
                            <input type="email" placeholder="doctor@hospital.com">
                            <button><i class="fal fa-long-arrow-right"></i></button>
                        </div>
                    </div>
                    <div class="login_frm_wrap">
                        <label>Code</label>
                        <div>
                            <i class="fal fa-lock"></i>
                            <input type="email" placeholder="Uniq Code">
                            <button><i class="fal fa-eye-slash"></i></button>
                        </div>
                    </div>
                    <div class="login_btn_wrap">
                        <a href="registration.php" class="register"><i class="fal fa-user-plus"></i>Register</a>
                        <a><i class="fal fa-sign-in"></i>Login</a>
                    </div>
                    <a href="#" class="need_help">Need help? Contact Support</a>
                </div>
            </div>

            <div class="speaker_wrap">
                <div class="speaker_head">
                    <h2><span>World Class Faculty</span>Keynote Speakers</h2>
                    <a href="#">View Full Schedule<i class="fal fa-arrow-right"></i></a>
                </div>
                <div class="speaker_owl owl-carousel owl-theme">
                    <div class="item">
                        <span><img src="" alt="">
                            <n><i class="fal fa-award"></i></n>
                        </span>
                        <h4>Prof. R. K. Sharma</h4>
                        <h5>Director</h5>
                        <h6>National Institute of TB & Respiratory Diseases</h6>
                        <p>"Evolution of MDR-TB Treatment Protocols"</p>
                    </div>

                </div>
            </div>
        </div>
    </div>

    <div class="update">
        <span><i class="fal fa-megaphone"></i>Updates</span>
        <marquee behavior="scroll" direction="left">
            <n>NATCON2025 Scheduled: Dec 19-21, 2025</n>
            <?php circle(); ?>
            <n>Early Bird Registration closes soon</n>
            <?php circle(); ?>
            <n>Abstract submission deadline: Oct 30, 2025</n>
            <?php circle(); ?>
            <n>Welcome to Kolkata!</n>
        </marquee>
    </div>
    <div class="float_btn">
        <a><i class="fal fa-comment-alt"></i><span>8100569558</span></a>
        <a><i class="fal fa-comment-alt"></i><span>8100569558</span></a>
        <a><i class="fal fa-comment-alt"></i><span>8100569558</span></a>
        <button><i class="fal fa-comment-alt"></i><i class="fal fa-times"></i></button>
    </div>
</body>
<?php include_once("includes/js-source.php"); ?>
<script>
    $('.owl-carousel').owlCarousel({
        loop: true,
        margin: 10,
        nav: true,
        responsive: {
            0: {
                items: 1
            },
            768: {
                items: 3
            },
            1000: {
                items: 4
            }
        }
    })
    $('.float_btn button').click(function() {
            $('.float_btn').toggleClass('active');
        })
        (function() {
            const second = 1000,
                minute = second * 60,
                hour = minute * 60,
                day = hour * 24;

            //I'm adding this section so I don't have to keep updating this pen every year :-)
            //remove this if you don't need it
            let today = new Date(),
                dd = String(today.getDate()).padStart(2, "0"),
                mm = String(today.getMonth() + 1).padStart(2, "0"),
                yyyy = 2026,
                nextYear = yyyy,
                dayMonth = "04/16/",
                birthday = dayMonth + yyyy;

            today = mm + "/" + dd + "/" + yyyy;
            if (today > birthday) {
                birthday = dayMonth + nextYear;
            }
            //end

            const countDown = new Date(birthday).getTime(),
                x = setInterval(function() {

                    const now = new Date().getTime(),
                        distance = countDown - now;

                    document.getElementById("days").innerText = Math.floor(distance / (day)),
                        document.getElementById("hours").innerText = Math.floor((distance % (day)) / (hour)),
                        document.getElementById("minutes").innerText = Math.floor((distance % (hour)) / (minute)),
                        document.getElementById("seconds").innerText = Math.floor((distance % (minute)) / second);
                    if (distance < 0) {

                        $("#registration_countdown").hide(),
                            $("#registration_closed").show(),
                            $(".register").hide(),
                            clearInterval(x);
                    }
                }, 0)
        }());
</script>

</html>
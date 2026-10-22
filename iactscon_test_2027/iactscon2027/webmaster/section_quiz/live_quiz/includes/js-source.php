<script src="live_quiz/js/jquery.min.js"></script>
<!-- <script src="live_quiz/js/owl.carousel.js"></script> -->
<script src='https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.5.0/Chart.min.js'></script>
<script src='https://unpkg.com/gsap@3/dist/gsap.min.js'></script>
<script src='https://unpkg.com/gsap@3/dist/ScrollTrigger.min.js'></script>
<script src='https://unpkg.com/@studio-freight/lenis@1.0.34/dist/lenis.min.js'></script>
<script src='https://cdn.jsdelivr.net/jquery.slick/1.6.0/slick.min.js'></script>
<script src='https://cdnjs.cloudflare.com/ajax/libs/three.js/r83/three.js'></script>
<script src='https://cdnjs.cloudflare.com/ajax/libs/jquery.imagesloaded/4.1.4/imagesloaded.pkgd.js'></script>
<script src='https://cdnjs.cloudflare.com/ajax/libs/gsap/1.20.4/TweenMax.min.js'></script>
<script src='https://assets.codepen.io/16327/Flip.min.js'></script>
<script src='https://cdn.jsdelivr.net/gh/studio-freight/lenis@1.0.29/bundled/lenis.min.js'></script>
<script src="https://cdn.jsdelivr.net/npm/parvus@2.3.3/dist/js/parvus.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/wow/1.1.2/wow.min.js" integrity="sha512-Eak/29OTpb36LLo2r47IpVzPBLXnAMPAVypbSZiZ4Qkf8p/7S/XRG5xp7OKWPPYfJT6metI+IORkR5G8F900+g==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
<script src="live_quiz/js/main.js"></script>
<script src="live_quiz/js/timer.js"></script>
<script>
    $('.sc_prgm_head p').click(function() {
        var tabId = $(this).attr('data-tab');
        $(".sc_prgm_content").hide().removeClass("active");
        $(".sc_prgm_head p").removeClass("active");
        $('#' + tabId).show().addClass('active');
        $(".sc_prgm_content.active .sc_prgm_box:nth-child(2)").show();
        $(this).addClass("active");
    });
    $('.sc_prgm_tab_btn button').click(function() {
        var tabId = $(this).attr('data-tab');
        $(".sc_prgm_box").hide();
        $(".sc_prgm_tab_btn button").removeClass("active");
        $('#' + tabId).show();
        $(this).addClass("active");
    });
    $('.user').click(function() {
        $('.left_bar').toggleClass('open');
        $('.main_bar').toggleClass('left_bar_open blr');
        $(this).toggleClass('active');

    });
    $('.noti').click(function() {
        $('.right_bar').toggleClass('open');
        $('.main_bar').toggleClass('right_bar_open blr');
        $(this).toggleClass('active');
    });
    // (function() {
    //     const second = 1000,
    //         minute = second * 60,
    //         hour = minute * 60,
    //         day = hour * 24;

    //     //I'm adding this section so I don't have to keep updating this pen every year :-)
    //     //remove this if you don't need it
    //     let today = new Date(),
    //         dd = String(today.getDate()).padStart(2, "0"),
    //         mm = String(today.getMonth() + 1).padStart(2, "0"),
    //         yyyy = today.getFullYear(),
    //         nextYear = yyyy + 1,
    //         dayMonth = "07/21/",
    //         birthday = dayMonth + yyyy;

    //     today = mm + "/" + dd + "/" + yyyy;
    //     if (today > birthday) {
    //         birthday = dayMonth + nextYear;
    //     }
    //     //end

    //     const countDown = new Date(birthday).getTime(),
    //         x = setInterval(function() {

    //             const now = new Date().getTime(),
    //                 distance = countDown - now;

    //             document.getElementById("days").innerText = Math.floor(distance / (day)),
    //                 document.getElementById("hours").innerText = Math.floor((distance % (day)) / (hour)),
    //                 document.getElementById("minutes").innerText = Math.floor((distance % (hour)) / (minute)),
    //                 document.getElementById("seconds").innerText = Math.floor((distance % (minute)) / second);
    //         }, 0)
    // }());

    (function() {
    // Only run if countdown elements exist on this page
    if (!document.getElementById("days")) return;

    const second = 1000,
        minute = second * 60,
        hour = minute * 60,
        day = hour * 24;

    let today = new Date(),
        dd = String(today.getDate()).padStart(2, "0"),
        mm = String(today.getMonth() + 1).padStart(2, "0"),
        yyyy = today.getFullYear(),
        nextYear = yyyy + 1,
        dayMonth = "07/21/",
        birthday = dayMonth + yyyy;

    today = mm + "/" + dd + "/" + yyyy;
    if (today > birthday) {
        birthday = dayMonth + nextYear;
    }

    const countDown = new Date(birthday).getTime(),
        x = setInterval(function() {
            const now = new Date().getTime(),
                distance = countDown - now;

            document.getElementById("days").innerText    = Math.floor(distance / (day));
            document.getElementById("hours").innerText   = Math.floor((distance % (day)) / (hour));
            document.getElementById("minutes").innerText = Math.floor((distance % (hour)) / (minute));
            document.getElementById("seconds").innerText = Math.floor((distance % (minute)) / second);
        }, 1000);
}());
    new WOW().init();

    let vh = window.innerHeight * 0.01;
    document.documentElement.style.setProperty('--vh', `${vh}px`);

    $(window).scroll(function() {
        if ($(window).scrollTop() >= 100) {
            $('header').addClass('fixed-header');
        } else {
            $('header').removeClass('fixed-header');
        }

    });
    $(function() {
        $('.about_toggle_btn a').click(function() {
            var tabId = $(this).attr('data-tab');
            $('.about_toggle_btn a').removeClass('active');
            $('.about_container').removeClass('active').slideUp();
            $(this).addClass('active');
            $('#' + tabId).addClass('active').slideDown();
        });

        $('.has_sub').click(function() {
            var tabId = $(this).attr('data-tab');
            if ($(this).hasClass("active")) {
                $(".sub_menu").hide();
                $(".has_sub").removeClass("active");
                $(".sub_menu_inner_rt_inner").hide().removeClass("open");
                $(".has_sub_inner").removeClass("active");
            } else {
                $(".sub_menu").hide();
                $(".has_sub").removeClass("active");
                $(".sub_menu_inner_rt_inner").hide().removeClass("open");
                $(".has_sub_inner").removeClass("active");
                $('#' + tabId).toggle().toggleClass("open");
                $(this).toggleClass("active");
                $(".sub_menu_inner_rt .sub_menu_inner_rt_inner:first-child").show();
                $(".sub_menu_inner_lt .has_sub_inner:first-child").addClass("active");
            }
        });
        $(".sub_close").click(function() {
            $(".sub_menu").removeClass("open");
            $(".has_sub").removeClass("active");
        });
        // $('.main_menu li a').click(function() {
        //     $(".sub_menu").hide();
        //     $(".has_sub").removeClass("active");
        //     $(".sub_menu_inner_rt_inner").hide();
        //     $(".has_sub_inner").removeClass("active");
        //     $(".sub_menu_inner_rt .sub_menu_inner_rt_inner:first-child").show();
        //     $(".sub_menu_inner_lt .has_sub_inner:first-child").addClass("active");
        // });

        $('.has_sub_inner').hover(function() {
            var tabId = $(this).attr('data-tab');
            $(".sub_menu_inner_rt_inner").hide();
            $(".has_sub_inner").removeClass("active");
            $('#' + tabId).show();
            $(this).toggleClass("active");
        });

        $(".page_inner_link_click[href^='#']").click(function(e) {
            e.preventDefault();
            $(".page_inner_link_click").removeClass("active");
            $(this).addClass("active");
            var position = $($(this).attr("href")).offset().top - 100;

            $("body, html").animate({
                scrollTop: position
            }, 'slow');
        });
    });
    $(document).ready(function() {
        if (screen.width < 960) {
            $('.has_sub_inner').click(function() {
                var tabId = $(this).attr('data-tab');
                $(".sub_menu_inner_rt_inner").removeClass("open");
                $(".has_sub_inner").removeClass("active");
                $('#' + tabId).addClass("open");
                $(this).addClass("active");
                $('.sub_menu_inner_rt').addClass('open');
            });

        }
        $('.faq ul li button').click(function() {
            if ($(this).hasClass("active")) {
                $(".faq ul li p").slideUp();
                $(".faq ul li button").removeClass("active");
            } else {
                $(".faq ul li p").slideUp();
                $(".faq ul li button").removeClass("active");
                $(this).parent().find("p").slideToggle();
                $(this).parent().find("button").toggleClass("active");
            }
        });
        // $('.menu_sub_click').click(function() {
        //     $(".sub_menu_ul").slideUp();
        //     $(".menu_click").removeClass("active");
        // });
    });
    $(".sub_sub_close").click(function() {
        $('.sub_menu_inner_rt').removeClass('open');
        $(".sub_menu_inner_rt_inner").removeClass("open");
        $(".has_sub_inner").removeClass("active");
    });


    function gotoTop(top) {
        $("html, body").animate({
            scrollTop: top + "px"
        }, 'slow');

    };
    $(".click-to-toggle").click(function() {
        $(".fixed-action-btn ul").toggle();
    });
    $(".main-btn-bk").click(function() {
        $(".main_menu").addClass("open");
        $("html, body").addClass("overflow-hidden");
    });
    $(".main_close").click(function() {
        $(".main_menu").removeClass("open");
        $("html, body").removeClass("overflow-hidden");
    });
    $(".nav-link-menu").click(function() {
        $(".main-menu").removeClass("menu-open");
        $("html, body").removeClass("overflow-hidden");
        $(".sub-menu").hide();
    });

    // Get all sections that have an ID defined
    const sections = document.querySelectorAll("section[id]");

    // Add an event listener listening for scroll
    window.addEventListener("scroll", navHighlighter);

    // function navHighlighter() {

    //     // Get current scroll position
    //     let scrollY = window.pageYOffset;

    //     // Now we loop through sections to get height, top and ID values for each
    //     sections.forEach(current => {
    //         const sectionHeight = current.offsetHeight;
    //         const sectionTop = current.offsetTop - 400;
    //         sectionId = current.getAttribute("id");

    //         /*
    //         - If our current scroll position enters the space where current section on screen is, add .active class to corresponding navigation link, else remove it
    //         - To know which link needs an active class, we use sectionId variable we are getting while looping through sections as an selector
    //         */
    //         if (
    //             scrollY > sectionTop &&
    //             scrollY <= sectionTop + sectionHeight
    //         ) {
    //             document.querySelector(".main-menu a[href*=" + sectionId + " ]").classList.add("active");
    //         } else {
    //             document.querySelector(".main-menu a[href*=" + sectionId + " ]").classList.remove("active");
    //         }
    //     });
    // }
    function navHighlighter() {
    let scrollY = window.pageYOffset;
    sections.forEach(current => {
        const sectionHeight = current.offsetHeight;
        const sectionTop = current.offsetTop - 400;
        sectionId = current.getAttribute("id");
        var navLink = document.querySelector(".main-menu a[href*=" + sectionId + "]");
        if (!navLink) return; // ← add this guard
        if (scrollY > sectionTop && scrollY <= sectionTop + sectionHeight) {
            navLink.classList.add("active");
        } else {
            navLink.classList.remove("active");
        }
    });
}
</script>
<script>
    $(document).ready(function() {
        if (screen.width > 960) {
            $('.section_grid').owlCarousel({
                loop: false,
                margin: 50,
                nav: true,
                items: 8,
                autoWidth: false,
                nav: false,
                dots: true,
            });
            $('.core_com_grid').owlCarousel({
                loop: true,
                margin: 20,
                nav: true,
                items: 5,
                nav: true,
                navText: [
                    '<i class="fal fa-arrow-left"></i>',
                    '<i class="fal fa-arrow-right"></i>'
                ],
                dots: false,
            });
            $('.attraction_wrap').owlCarousel({
                loop: false,
                margin: 20,
                nav: true,
                nav: false,
                autoWidth: true,
                items: 3,
                dots: false,

            })
            $('.workshop_wrap').owlCarousel({
                loop: false,
                margin: 20,
                nav: true,
                nav: false,
                autoWidth: true,
                items: 3,
                dots: false,
            })

            $('.landing_ul').owlCarousel({
                loop: false,
                margin: 20,
                nav: true,
                nav: false,
                autoWidth: true,
                items: 3,
                dots: false,
            })
            $('.sc_prgm_head').removeClass('owl-carousel owl-theme');
            $('.com_owl').removeClass('owl-carousel owl-theme');
        }
        if (screen.width < 960) {
            $('.section_grid').removeClass('owl-carousel owl-theme');
            $('.core_com_grid').removeClass('owl-carousel owl-theme');
            $('.attraction_wrap').removeClass('owl-carousel owl-theme');
            $('.workshop_wrap').removeClass('owl-carousel owl-theme')
            $('.landing_ul').removeClass('owl-carousel owl-theme')
            $('.com_owl').owlCarousel({
                loop: true,
                margin: 10,
                nav: true,
                items: 2,
                autoWidth: true,
                nav: false,
                dots: false,
            });

            $('.sc_prgm_head').owlCarousel({
                loop: false,
                margin: 10,
                nav: false,
                items: 1,
                nav: false,
                dots: false,
                autoWidth: true,
            });
        }
    });
    $('.pat_com_grid').owlCarousel({
        loop: true,
        margin: 20,
        nav: true,
        items: 3,
        nav: true,
        navText: [
            '<i class="fal fa-arrow-left"></i>',
            '<i class="fal fa-arrow-right"></i>'
        ],
        dots: false,
        responsive: {
            0: {
                items: 1

            },
            768: {
                items: 2

            },
            1000: {
                items: 3

            }
        }
    });
    $('.page_inner_link_owl').owlCarousel({
        loop: true,
        margin: 40,
        autoWidth: true,
        items: 2,
        dots: false,
        dotsData: false,
        nav: true,

        navText: [
            '<i class="fal fa-angle-left"></i>',
            '<i class="fal fa-angle-right"></i>'
        ],
        mouseDrag: true,
        autoplay: false,
        autoplayTimeout: 8000,
        autoplayHoverPause: true,
        autoplaySpeed: 5000,
        // animateOut: 'fadeOut',
        speed: 8000,

    });

    $('.banner_owl').owlCarousel({
        loop: true,
        margin: 20,
        dots: false,
        dotsData: false,
        nav: false,
        // autoWidth:true,
        // navText: [
        //     '<i class="fal fa-arrow-left"></i>',
        //     '<i class="fal fa-arrow-right"></i>'
        // ],
        mouseDrag: false,
        autoplay: true,
        autoplayTimeout: 8000,
        autoplayHoverPause: true,
        autoplaySpeed: 5000,
        // animateOut: 'fadeOut',
        speed: 8000,
        responsive: {
            0: {
                items: 1,
                dots: true,
                mouseDrag: true,
            },
            768: {
                items: 2,
                dots: true,
                mouseDrag: true,
            },
            1000: {
                items: 4,
                dots: false,
                mouseDrag: false,
            }
        }

    });
    $('.our_specialist_owl').owlCarousel({
        loop: true,
        margin: 20,
        items: 6,
        dots: false,
        dotsData: false,
        nav: true,
        navText: [
            '<i class="fal fa-arrow-left"></i>',
            '<i class="fal fa-arrow-right"></i>'
        ],
        mouseDrag: false,
        autoplay: false,
        autoplayTimeout: 8000,
        autoplayHoverPause: true,
        autoplaySpeed: 5000,
        // animateOut: 'fadeOut',
        speed: 8000,
        responsive: {
            0: {
                items: 2,
                dots: true,
                mouseDrag: true,
            },
            768: {
                items: 4,
                dots: true,
                mouseDrag: true,
            },
            1000: {
                items: 6,
                dots: false,
                mouseDrag: false,
            }
        }

    });
    $('.use_links_owl').owlCarousel({
        loop: false,
        margin: 20,
        items: 7,
        dots: false,
        dotsData: false,
        nav: false,
        navText: [
            '<i class="fal fa-angle-left"></i>',
            '<i class="fal fa-angle-right"></i>'
        ],
        mouseDrag: false,
        autoplay: false,
        autoplayTimeout: 8000,
        autoplayHoverPause: true,
        autoplaySpeed: 5000,
        // animateOut: 'fadeOut',
        speed: 8000,
        responsive: {
            0: {
                items: 2,
                dots: true,
                mouseDrag: true,
            },
            768: {
                items: 4,
                dots: true,
                mouseDrag: true,
            },
            1000: {
                items: 8,
                dots: false,
                mouseDrag: false,
            }
        }

    });

    $('.slider-for').slick({
        slidesToShow: 1,
        slidesToScroll: 1,
        arrows: false,
        asNavFor: '.slider-nav',
        infinite: false,
        autoplay: false,
        autoplaySpeed: 8000,
        arrows: false,
        dots: false,
        fade: true,
        speed: 100,

    });
    $('.slider-nav').slick({
        slidesToScroll: 1,
        asNavFor: '.slider-for',
        dots: false,
        infinite: false,
        focusOnSelect: false,
        slidesPerRow: 1,
        slidesToShow: 1,
        autoplay: false,
        autoplaySpeed: 7000,
        arrows: false,
        dots: true,
        fade: true,
        speed: 100,
        accessibility: true,
        onAfterChange: function(slide, index) {
            console.log("slider-nav change");
            console.log(this.$slides.get(index));
            $('.current-slide').removeClass('current-slide');
            $(this.$slides.get(index)).addClass('current-slide');
        },
        onInit: function(slick) {
            $(slick.$slides.get(0)).addClass('current-slide');
        },
        customPaging: function(slick, index) {
            return '<a>' + (index + 1) + '</a>';
        }
    });
    var oilCanvas = document.getElementById("oilChart");

    Chart.defaults.global.defaultFontFamily = "Lato";
    Chart.defaults.global.defaultFontSize = 18;

    var oilData = {
        labels: [
            "Cat",
            "Dog"
        ],
        datasets: [{
            data: [50, 50],
            backgroundColor: [
                "#002F4F",
                "#006F77"
            ]
        }]
    };

    if (oilCanvas) {
        var pieChart = new Chart(oilCanvas, {
            type: 'pie',
            data: oilData
        });
    }
</script>
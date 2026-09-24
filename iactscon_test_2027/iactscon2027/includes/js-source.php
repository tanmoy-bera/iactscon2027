<script src="js/jquery.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
<script src='https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.2/js/select2.min.js'></script>
<script src="js/owl.carousel.js"></script>
<script>
    $('.hotel_link_owl').owlCarousel({
        loop: false,
        margin: 6,
        autoWidth: true,
        items: 3,
        dots: false,
        dotsData: false,
        nav: true,
        navText: [
            '<i class="fal fa-angle-left"></i>',
            '<i class="fal fa-angle-right"></i>'
        ],
        mouseDrag: false,

    });
    $('.hotel_owl').owlCarousel({
        loop: true,
        margin: 10,
        nav: false,
        dots: false,
        mouseDrag: false,
        autoplay: true,
        autoplayTimeout: 5000,
        autoplayHoverPause: true,
        autoplaySpeed: 4000,
        // animateOut: 'fadeOut',
        speed: 1000,
        items: 1,
    });
    // $('.review_tab .owl-item button').click(function() {
    //     var tabId = $(this).attr('data-tab');
    //     $(".review_right_box").hide();
    //     $(".review_tab button").removeClass("active");
    //     $('#' + tabId).toggle();
    //     $(this).toggleClass('active');
    // });
    $('.hotel_tab_btn').click(function() {
        var tabId = $(this).attr('data-tab');
        $(".hotel_box").hide();
        $(".hotel_tab_btn").removeClass("active");
        $('#' + tabId).toggle();
        $(this).toggleClass('active');
    });
    $('.wrkshp_tab_btn').click(function() {
        var tabId = $(this).attr('data-tab');
        $(".wrkshp_box").hide();
        $(".wrkshp_tab_btn").removeClass("active");
        $('#' + tabId).toggle();
        $(this).toggleClass('active');
    });
    var placeholder = "select";
    $(".mySelect").select2({
        minimumResultsForSearch: 5
    });
    $('.icon_hover').hover(function() {
        $(this).toggleClass('action-transparent');
    });
    $('.icon_hover').hover(function() {
        if ($(this).hasClass("active")) {
            $(this).removeClass('action-transparent');
        }
    });

    $('.dropdown-toggle').click(function() {
        $(this).toggleClass('active');
    });

    $('.has-sub').click(function() {
        if ($(this).hasClass("active")) {
            $(".sub_menu").slideUp();
            $(".has-sub").removeClass("active");
            $(".sub_sub_menu").slideUp();
            $(".sub-has-sub").removeClass("active");
        } else {
            $(".sub_menu").slideUp();
            $(".has-sub").removeClass("active");
            $(".sub_sub_menu").slideUp();
            $(".sub-has-sub").removeClass("active");
            $(this).parent().find(".sub_menu").slideToggle();
            $(this).parent().find(".has-sub").toggleClass("active");
        }
    });
    $('.sub-has-sub').click(function() {
        if ($(this).hasClass("active")) {
            $(".sub_sub_menu").slideUp();
            $(".sub-has-sub").removeClass("active");
        } else {
            $(".sub_sub_menu").slideUp();
            $(".sub-has-sub").removeClass("active");
            $(this).parent().find(".sub_sub_menu").slideToggle();
            $(this).parent().find(".sub-has-sub").toggleClass("active");
        }
    });
    $('.popup_btn').click(function() {
        var tabId = $(this).attr('data-tab');
        $(".popup_wrap").show();
        $('#' + tabId).show();
    });


    var progress = $('body').find('.progress-bar-wrap');
    var speed = 30;
    progress.each(function() {
        var item = $(this).find('.progress-done');
        item.css("width", item.attr("data-progress") + "%");
        item.css("opacity", "1");
        $(this).find('.progress-value n').html(item.attr("data-progress") + '%');

    })
    var counted = 0;
    $(window).scroll(function() {

        var oTop = $('#counter').offset().top - window.innerHeight;
        if (counted == 0 && $(window).scrollTop() > oTop) {
            $('.count').each(function() {
                var $this = $(this),
                    countTo = $this.attr('data-count');
                $({
                    countNum: $this.text()
                }).animate({
                        countNum: countTo
                    },

                    {

                        duration: 2000,
                        easing: 'swing',
                        step: function() {
                            $this.text(Math.floor(this.countNum));
                        },
                        complete: function() {
                            $this.text(this.countNum);
                            //alert('finished');
                        }

                    });
            });
            counted = 1;
        }

    });
</script>
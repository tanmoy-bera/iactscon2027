<script src="../js/jquery.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
<script src='https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.2/js/select2.min.js'></script>
<script>
    // var data = ["Apple", "Banana", "Cherry", "Date", "ElderberriesElderberry"]; // Programatically-generated options array with > 5 options
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

    $('.tracking_analytic_tab button').click(function() {
        var tabId = $(this).attr('data-tab');
        $(".tracking_analytic_box").removeClass("active");
        $(".tracking_analytic_tab button").removeClass("active");
        $('#' + tabId).addClass("active");
        $(this).addClass("active");
    });
    $('.dropdown-toggle').click(function() {
        $(this).toggleClass('active');
    });
    $('.service_detail_btn').click(function() {
        if ($(this).hasClass("active")) {
            $(".service_detail_btn").removeClass('active');
            $(".sub_table_tr").removeClass("active");
            $(".table_wrap tbody tr").removeClass("active");
        } else {
            $(".service_detail_btn").removeClass('active');
            $(".sub_table_tr").removeClass("active");
            $(".table_wrap tbody tr").removeClass("active");
            $(this).parent().parent().next('tr').toggleClass('active');
            $(this).parent().parent().toggleClass('active');
            $(this).toggleClass('active');
        }
    });
    $('.wrkshp_trak').click(function() {
        if ($(this).hasClass("active")) {
            $(".wrkshp_trak").removeClass('active');
            $(".sub_table_tr").removeClass("active");
        } else {
            $(".wrkshp_trak").removeClass('active');
            $(".sub_table_tr").removeClass("active");
            $(this).parent().parent().parent().next('tr').toggleClass('active');
            $(this).toggleClass('active');
        }
    });
    $('.mode_check').click(function() {
        var tabId = $(this).attr('data-tab');
        $(".regi_category").hide();
        $('#' + tabId).show();
    });
    $('.pay_check').click(function() {
        var tabId = $(this).attr('data-tab');
        $(".payment_details").hide();
        $('#' + tabId).show();
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
    // $('.spot_box_bottom_right .drp').click(function() {
    //     if ($(this).hasClass("active")) {
    //         $(".spot_service_break").slideUp();
    //         $(".spot_box_bottom_right .drp").removeClass("active");
    //     } else {
    //         $(".spot_service_break").slideUp();
    //         $(".spot_box_bottom_right .drp").removeClass("active");
    //         $(this).parent().parent().parent().find(".spot_service_break").slideToggle();
    //         $(this).toggleClass("active");
    //     }
    // });
    $('.spot_box_bottom_right .drp').click(function(e) {
        e.preventDefault(); // prevent default anchor behavior

        var $this = $(this);
        var $table = $this.closest('.accm_bottom').next('.accm_tariff');

        if ($this.hasClass("active")) {
            $table.slideUp();
            $this.removeClass("active");
        } else {
            // Close all other tables
            $('.spot_service_break').slideUp();
            $('.spot_box_bottom_right .drp').removeClass("active");

            // Open only this table
            $table.slideDown();
            $this.addClass("active");
        }
    });
    $('.popup-btn').click(function() {
        var tabId = $(this).attr('data-tab');
        $(".pop_up_wrap").show();
        $('#' + tabId).show();
    });
    $('.popup_close').click(function() {
        $(".pop_up_wrap").hide();
        $(".pop_up_body").hide();
    });

    var progress = $('body').find('.progress-bar-wrap');
    var speed = 30;
    progress.each(function() {
        var item = $(this).find('.progress-done');
        item.css("width", item.attr("data-progress") + "%");
        item.css("opacity", "1");
         $(this).find('.progress-value n').html(item.attr("data-progress") + '%');

    })
    // $(document).ready(function() {
    //     progress_bar();
    // });

    // function progress_bar() {
    //     var speed = 30;
    //     var items = $('.regi_data_grid_ul').find('.progress-bar-wrap');

    //     items.each(function() {
    //         var item = $(this).find('.progress-done');
    //         var itemValue = item.attr('data-progress');
    //         var i = 0;
    //         var value = $(this);

    //         var count = setInterval(function() {
    //             if (i <= itemValue) {
    //                 var iStr = i.toString();
    //                 item.css({
    //                     'width': itemValue + '%'
    //                 });
    //                 value.find('.item_value').html(iStr + '%');
    //             } else {
    //                 clearInterval(count);
    //             }
    //             i++;
    //         }, speed);
    //     });
    // }
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
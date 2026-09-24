
/*jQuery(document).ready(function () {
  jQuery('.media-click').click(function () {
    jQuery('.media-bottom a').toggleClass('show');
    jQuery('.media-label').toggleClass('active');
  });

  AOS.init();

  jQuery('#slide1').click(function () {
    jQuery('.sidebar1').addClass('slide-open');
    jQuery('.sidebar2').removeClass('slide-open2');
  });
  // jQuery('#slide2').click(function(){
  //   jQuery('.sidebar2').addClass('slide-open2'); 
  //   jQuery('.sidebar1').removeClass('slide-open'); 
  // })

  jQuery('.close').click(function () {
    jQuery('.sidebar1').removeClass('slide-open');
    jQuery('.sidebar2').removeClass('slide-open2');
  });


  jQuery('.nav-btn').click(function () {
    jQuery('.dashbord-tow-menu').toggleClass('open');
    jQuery('.nav-btn').toggleClass('open');
  });


  jQuery('.cart').click(function () {
    jQuery('.checkout-main-wrap').show();
  });
  jQuery('.close-check').click(function () {
    jQuery('.checkout-main-wrap').hide();
  });
  jQuery('.pay-now').click(function () {
    jQuery('.checkout-main-wrap').show();
  });



  // Create Countdown
  // var Countdown = {

  //   // Backbone-like structure
  //   $el: jQuery('.countdown'),

  //   // Params
  //   countdown_interval: null,
  //   total_seconds: 0,

  //   // Initialize the countdown  
  //   init: function () {

  //     // DOM
  //     this.$ = {
  //       hours: this.$el.find('.bloc-time.hours .figure'),
  //       minutes: this.$el.find('.bloc-time.min .figure'),
  //       seconds: this.$el.find('.bloc-time.sec .figure')
  //     };

  //     // Init countdown values
  //     this.values = {
  //       hours: this.$.hours.parent().attr('data-init-value'),
  //       minutes: this.$.minutes.parent().attr('data-init-value'),
  //       seconds: this.$.seconds.parent().attr('data-init-value'),
  //     };

  //     // Initialize total seconds
  //     this.total_seconds = this.values.hours * 60 * 60 + (this.values.minutes * 60) + this.values.seconds;

  //     // Animate countdown to the end 
  //     this.count();
  //   },

  //   count: function () {

  //     var that = this,
  //       $hour_1 = this.$.hours.eq(0),
  //       $hour_2 = this.$.hours.eq(1),
  //       $min_1 = this.$.minutes.eq(0),
  //       $min_2 = this.$.minutes.eq(1),
  //       $sec_1 = this.$.seconds.eq(0),
  //       $sec_2 = this.$.seconds.eq(1);

  //     this.countdown_interval = setInterval(function () {

  //       if (that.total_seconds > 0) {

  //         --that.values.seconds;

  //         if (that.values.minutes >= 0 && that.values.seconds < 0) {

  //           that.values.seconds = 59;
  //           --that.values.minutes;
  //         }

  //         if (that.values.hours >= 0 && that.values.minutes < 0) {

  //           that.values.minutes = 59;
  //           --that.values.hours;
  //         }

  //         // Update DOM values
  //         // Hours
  //         that.checkHour(that.values.hours, $hour_1, $hour_2);

  //         // Minutes
  //         that.checkHour(that.values.minutes, $min_1, $min_2);

  //         // Seconds
  //         that.checkHour(that.values.seconds, $sec_1, $sec_2);

  //         --that.total_seconds;
  //       }
  //       else {
  //         clearInterval(that.countdown_interval);
  //       }
  //     }, 1000);
  //   },

  //   animateFigure: function ($el, value) {

  //     var that = this,
  //       $top = $el.find('.top'),
  //       $bottom = $el.find('.bottom'),
  //       $back_top = $el.find('.top-back'),
  //       $back_bottom = $el.find('.bottom-back');

  //     // Before we begin, change the back value
  //     $back_top.find('span').html(value);

  //     // Also change the back bottom value
  //     $back_bottom.find('span').html(value);

  //     // Then animate
  //     TweenMax.to($top, 0.8, {
  //       rotationX: '-180deg',
  //       transformPerspective: 300,
  //       ease: Quart.easeOut,
  //       onComplete: function () {

  //         $top.html(value);

  //         $bottom.html(value);

  //         TweenMax.set($top, { rotationX: 0 });
  //       }
  //     });

  //     TweenMax.to($back_top, 0.8, {
  //       rotationX: 0,
  //       transformPerspective: 300,
  //       ease: Quart.easeOut,
  //       clearProps: 'all'
  //     });
  //   },

  //   checkHour: function (value, $el_1, $el_2) {

  //     var val_1 = value.toString().charAt(0),
  //       val_2 = value.toString().charAt(1),
  //       fig_1_value = $el_1.find('.top').html(),
  //       fig_2_value = $el_2.find('.top').html();

  //     if (value >= 10) {

  //       // Animate only if the figure has changed
  //       if (fig_1_value !== val_1) this.animateFigure($el_1, val_1);
  //       if (fig_2_value !== val_2) this.animateFigure($el_2, val_2);
  //     }
  //     else {

  //       // If we are under 10, replace first figure with 0
  //       if (fig_1_value !== '0') this.animateFigure($el_1, 0);
  //       if (fig_2_value !== val_1) this.animateFigure($el_2, val_1);
  //     }
  //   }
  // };

  // // Let's go !
  // Countdown.init();


  


  jQuery('.speaker-slider').slick({
    dots: false,
    nav: true,
    arrows:false,
    infinite: true,
    autoplay: true,
    autoplaySpeed: 3000,
    speed: 300,
    slidesToShow: 4,
    slidesToScroll: 1,
    responsive: [
      {
        breakpoint: 1200,
        settings: {
          slidesToShow: 3,
          slidesToScroll: 3,
          infinite: true,
          dots: true
        }
      },
      {
        breakpoint: 1024,
        settings: {
          slidesToShow: 2,
          slidesToScroll: 3,
          infinite: true,
          dots: true
        }
      },
      {
        breakpoint: 600,
        settings: {
          slidesToShow: 2,
          slidesToScroll: 2
        }
      },
      {
        breakpoint: 480,
        settings: {
          slidesToShow: 1,
          slidesToScroll: 1
        }
      }
    ]
  });

 // jQuery("#tabs").tabs();


  jQuery('#popup-slider').slickLightbox({
    src: 'src',
    itemSelector: '.slider-home-inner-popup img',
    background: 'rgba(0, 0, 0, .8)'
  });

  jQuery('.slider-home-inner').slick({
    dots: false,
    infinite: true,
    autoplay: true,
    autoplaySpeed: 3000,
    speed: 300,
    slidesToShow: 3,
    slidesToScroll: 1,
    responsive: [
      {
        breakpoint: 1024,
        settings: {
          slidesToShow: 3,
          slidesToScroll: 3,
          infinite: true,
          dots: true
        }
      },
      {
        breakpoint: 600,
        settings: {
          slidesToShow: 2,
          slidesToScroll: 2
        }
      },
      {
        breakpoint: 480,
        settings: {
          slidesToShow: 1,
          slidesToScroll: 1
        }
      }
    ]
  });



  jQuery('.multi-hotel-slider').slick({
    dots: false,
    infinite: true,
    autoplay: true,
    autoplaySpeed: 3000,
    speed: 300,
    slidesToShow: 3,
    slidesToScroll: 1,
    responsive: [
      {
        breakpoint: 1024,
        settings: {
          slidesToShow: 3,
          slidesToScroll: 3,
          infinite: true,
          dots: true
        }
      },
      {
        breakpoint: 600,
        settings: {
          slidesToShow: 2,
          slidesToScroll: 1,
        }
      },
      {
        breakpoint: 480,
        settings: {
          slidesToShow: 3,
          slidesToScroll: 1
        }
      }
    ]
  });

  jQuery('.maz-slider').slick({
    dots: false,
    infinite: true,
    autoplay: false,
    autoplaySpeed: 3000,
    slidesToShow: 1,
    slidesToScroll: 1,
    responsive: [
      {
        breakpoint: 1024,
        settings: {
          slidesToShow: 1,
          slidesToScroll: 1,
          infinite: true,
          dots: true
        }
      },
      {
        breakpoint: 992,
        settings: {
          slidesToShow: 2,
          slidesToScroll: 1,
          infinite: true,
          dots: true
        }
      },
      {
        breakpoint: 600,
        settings: {
          slidesToShow: 1,
          slidesToScroll: 1
        }
      },
      {
        breakpoint: 480,
        settings: {
          slidesToShow: 1,
          slidesToScroll: 1
        }
      }
    ]
  });

  jQuery('.profile-menu-slide').slick({
    dots: false,
    infinite: true,
    autoplay: false,
    autoplaySpeed: 3000,
    speed: 300,
    slidesToShow: 1,
    slidesToScroll: 1,
    responsive: [
      {
        breakpoint: 1024,
        settings: {
          slidesToShow: 1,
          slidesToScroll: 1,
          infinite: true,
          dots: true
        }
      },
      {
        breakpoint: 600,
        settings: {
          slidesToShow: 1,
          slidesToScroll: 1
        }
      },
      {
        breakpoint: 480,
        settings: {
          slidesToShow: 1,
          slidesToScroll: 1
        }
      }
    ]
  });

  jQuery('.vertical-slide-list').slick({
    dots: true,
    vertical: true,
    autoplay: true,
    arrows:false,
    slidesToShow: 4,
    slidesToScroll: 1,
    verticalSwiping: true,
    responsive: [
      {
        breakpoint: 992,
        settings: {
          slidesToShow: 2,
          slidesToScroll: 1,
          infinite: true,
          dots: true,
          vertical: false,
          verticalSwiping: false,
        }
      },
      {
        breakpoint: 768,
        settings: {
          slidesToShow: 1,
          slidesToScroll: 1,
          infinite: true,
          dots: true,
          vertical: false,
          verticalSwiping: false,
        }
      },
    ]
  });




  jQuery('.dot-menu').click(function () {
    jQuery('.left-menu').toggleClass('open');
  });


  jQuery(".profile-slider").slick({

    // normal options...
    infinite: true,
    autoplay: true,
    slidesToShow: 3,
    slidesToScroll: 1,
    dots: false,
    arrows: true,
    centerMode: true,

    // the magic
    responsive: [{

      breakpoint: 1200,
      settings: {
        slidesToShow: 1,
        infinite: true,
        centerMode: false,
      }

    }, {

      breakpoint: 767,
      settings: {
        slidesToShow: 1,
        dots: true,
        centerMode: false,
      }

    },  {

      breakpoint: 992,
      settings: {
        slidesToShow: 3,
        dots: true,
        centerMode: false,
      }

    }]
  });


});
*/

jQuery(window).scroll(function () {
  if (jQuery(window).scrollTop() >= 1) {
    jQuery('body').addClass('fixed');
  }
  else {
    jQuery('body').removeClass('fixed');
  }
});

$(document).ready(function () {
    
 
 $('.cpvc').click( function() {
    $("#pvc").toggleClass("someClass");
} );
    
    
    
    
    
    
  $('.media-click').click(function () {
    $('.media-bottom a').toggleClass('show');
    $('.media-label').toggleClass('active');
  });

  AOS.init();

  $('#slide1').click(function () {
    $('.sidebar1').addClass('slide-open');
    $('.sidebar2').removeClass('slide-open2');
  });
  // $('#slide2').click(function(){
  //   $('.sidebar2').addClass('slide-open2'); 
  //   $('.sidebar1').removeClass('slide-open'); 
  // })

  $('.close').click(function () {
    $('.sidebar1').removeClass('slide-open');
    $('.sidebar2').removeClass('slide-open2');
  });


  $('.nav-btn').click(function () {
    $('.dashbord-tow-menu').toggleClass('open');
    $('.nav-btn').toggleClass('open');
  });


  $('.cart').click(function () {
   
    $('#checkout-main-wrap').show();
  });
  $('.close-check').click(function () {
    $('.checkout-main-wrap').hide();
  });
  
  $('.pay-now').click(function () {
    //$('.checkout-main-wrap').show();
  });



  // Create Countdown
  var Countdown = {

    // Backbone-like structure
    $el: $('.countdown'),

    // Params
    countdown_interval: null,
    total_seconds: 0,

    // Initialize the countdown  
    init: function () {

      // DOM
      this.$ = {
        hours: this.$el.find('.bloc-time.hours .figure'),
        minutes: this.$el.find('.bloc-time.min .figure'),
        seconds: this.$el.find('.bloc-time.sec .figure')
      };

      // Init countdown values
      this.values = {
        hours: this.$.hours.parent().attr('data-init-value'),
        minutes: this.$.minutes.parent().attr('data-init-value'),
        seconds: this.$.seconds.parent().attr('data-init-value'),
      };

      // Initialize total seconds
      this.total_seconds = this.values.hours * 60 * 60 + (this.values.minutes * 60) + this.values.seconds;

      // Animate countdown to the end 
      this.count();
    },

    count: function () {

      var that = this,
        $hour_1 = this.$.hours.eq(0),
        $hour_2 = this.$.hours.eq(1),
        $min_1 = this.$.minutes.eq(0),
        $min_2 = this.$.minutes.eq(1),
        $sec_1 = this.$.seconds.eq(0),
        $sec_2 = this.$.seconds.eq(1);

      this.countdown_interval = setInterval(function () {

        if (that.total_seconds > 0) {

          --that.values.seconds;

          if (that.values.minutes >= 0 && that.values.seconds < 0) {

            that.values.seconds = 59;
            --that.values.minutes;
          }

          if (that.values.hours >= 0 && that.values.minutes < 0) {

            that.values.minutes = 59;
            --that.values.hours;
          }

          // Update DOM values
          // Hours
          that.checkHour(that.values.hours, $hour_1, $hour_2);

          // Minutes
          that.checkHour(that.values.minutes, $min_1, $min_2);

          // Seconds
          that.checkHour(that.values.seconds, $sec_1, $sec_2);

          --that.total_seconds;
        }
        else {
          clearInterval(that.countdown_interval);
        }
      }, 1000);
    },

    animateFigure: function ($el, value) {

      var that = this,
        $top = $el.find('.top'),
        $bottom = $el.find('.bottom'),
        $back_top = $el.find('.top-back'),
        $back_bottom = $el.find('.bottom-back');

      // Before we begin, change the back value
      $back_top.find('span').html(value);

      // Also change the back bottom value
      $back_bottom.find('span').html(value);

      // Then animate
      TweenMax.to($top, 0.8, {
        rotationX: '-180deg',
        transformPerspective: 300,
        ease: Quart.easeOut,
        onComplete: function () {

          $top.html(value);

          $bottom.html(value);

          TweenMax.set($top, { rotationX: 0 });
        }
      });

      TweenMax.to($back_top, 0.8, {
        rotationX: 0,
        transformPerspective: 300,
        ease: Quart.easeOut,
        clearProps: 'all'
      });
    },

    checkHour: function (value, $el_1, $el_2) {

      var val_1 = value.toString().charAt(0),
        val_2 = value.toString().charAt(1),
        fig_1_value = $el_1.find('.top').html(),
        fig_2_value = $el_2.find('.top').html();

      if (value >= 10) {

        // Animate only if the figure has changed
        if (fig_1_value !== val_1) this.animateFigure($el_1, val_1);
        if (fig_2_value !== val_2) this.animateFigure($el_2, val_2);
      }
      else {

        // If we are under 10, replace first figure with 0
        if (fig_1_value !== '0') this.animateFigure($el_1, 0);
        if (fig_2_value !== val_1) this.animateFigure($el_2, val_1);
      }
    }
  };

  // Let's go !
  Countdown.init();

  $('.speaker-slider').slick({
    dots: false,
    nav: true,
    arrows:false,
    infinite: true,
    autoplay: true,
    autoplaySpeed: 3000,
    speed: 300,
    slidesToShow: 4,
    slidesToScroll: 1,
    responsive: [
      {
        breakpoint: 1200,
        settings: {
          slidesToShow: 3,
          slidesToScroll: 3,
          infinite: true,
          dots: true
        }
      },
      {
        breakpoint: 1024,
        settings: {
          slidesToShow: 2,
          slidesToScroll: 3,
          infinite: true,
          dots: true
        }
      },
      {
        breakpoint: 600,
        settings: {
          slidesToShow: 2,
          slidesToScroll: 2
        }
      },
      {
        breakpoint: 480,
        settings: {
          slidesToShow: 1,
          slidesToScroll: 1
        }
      }
    ]
  });
    
 
/* jQuery('.carv-slider').slick({
  slidesToShow: 1,
  slidesToScroll: 1,
  arrows: false,
  asNavFor: '.carv-nav',
  autoplay: true,
  autoplaySpeed: 2000
});

jQuery('.carv-nav').slick({
  slidesToShow: 5,
  slidesToScroll: 1,
  asNavFor: '.carv-slider',
  dots: true,
  arrows: false,
  centerMode: true,
  focusOnSelect: true,
  autoplay: true, 
  autoplaySpeed: 2000,
    responsive: [
      {
        breakpoint: 1441,
        settings: {
          slidesToShow: 3,
          slidesToScroll: 1,
          dots: true
        }
      },
      {
        breakpoint: 992,
        settings: {
          slidesToShow: 3,
          slidesToScroll: 1,
          dots: true
        }
      },
        
      {
        breakpoint: 767,
        settings: {
          slidesToShow: 1,
          slidesToScroll: 1,
          dots: true
        }
      }
    ]
});  */ 

/*    
jQuery('.drama-slider').slick({
  slidesToShow: 1,
  slidesToScroll: 1,
  arrows: false,
  fade: true,
  asNavFor: '.drama-nav'
});
jQuery('.drama-nav').slick({
  slidesToShow: 2,
  slidesToScroll: 1,
  asNavFor: '.drama-slider',
  dots: false,
    arrows: true,
  centerMode: true,
  focusOnSelect: true
});*/
    
jQuery('.drama-slider-xxxx').slick({
centerMode: true,
  //centerPadding: '120px',
  arrows: true,
  slidesToShow: 2,
  slidesToScroll: 1,
  infinite: true,
  responsive: [
    {
      breakpoint: 1199,
      settings: {
  dots: true,
        arrows: false,
        //centerPadding: '40px',
        slidesToShow: 1
      }
    }
  ]
  });

  //$("#tabs").tabs();


  $('#popup-slider').slickLightbox({
    src: 'src',
    itemSelector: '.slider-home-inner-popup img',
    background: 'rgba(0, 0, 0, .8)'
  });

  $('.slider-home-inner').slick({
    dots: false,
    infinite: true,
    autoplay: true,
    autoplaySpeed: 3000,
    speed: 300,
    slidesToShow: 3,
    slidesToScroll: 1,
    responsive: [
      {
        breakpoint: 1024,
        settings: {
          slidesToShow: 3,
          slidesToScroll: 3,
          infinite: true,
          dots: true
        }
      },
      {
        breakpoint: 767,
        settings: {
          slidesToShow: 1,
          slidesToScroll: 1
        }
      }
     ]
  });
/*$('.slider-home-inner').slick({
    slidesToShow: 3,
    slidesToScroll: 1,
    responsive: [
      {
        breakpoint: 1024,
        settings: {
          slidesToShow: 3,
          slidesToScroll: 1
        }
      },
      {
        breakpoint: 767,
        settings: {
          slidesToShow: 1,
          slidesToScroll: 1
        }
      }
    ]
  });*/


  $('.multi-hotel-slider').slick({
    dots: false,
    infinite: true,
    autoplay: true,
    autoplaySpeed: 3000,
    speed: 300,
    slidesToShow: 3,
    slidesToScroll: 1,
    responsive: [
      {
        breakpoint: 1024,
        settings: {
          slidesToShow: 3,
          slidesToScroll: 3,
          infinite: true,
          dots: true
        }
      },
      {
        breakpoint: 600,
        settings: {
          slidesToShow: 2,
          slidesToScroll: 1,
        }
      },
      {
        breakpoint: 480,
        settings: {
          slidesToShow: 3,
          slidesToScroll: 1
        }
      }
    ]
  });

  $('.maz-slider').slick({
    dots: false,
    infinite: true,
    autoplay: false,
    autoplaySpeed: 3000,
    slidesToShow: 1,
    slidesToScroll: 1,
    responsive: [
      {
        breakpoint: 1024,
        settings: {
          slidesToShow: 1,
          slidesToScroll: 1,
          infinite: true,
          dots: true
        }
      },
      {
        breakpoint: 992,
        settings: {
          slidesToShow: 1,
          slidesToScroll: 1,
          infinite: true,
          dots: true
        }
      },
      {
        breakpoint: 600,
        settings: {
          slidesToShow: 1,
          slidesToScroll: 1
        }
      },
      {
        breakpoint: 480,
        settings: {
          slidesToShow: 1,
          slidesToScroll: 1
        }
      }
    ]
  });

  $('.profile-menu-slide').slick({
    dots: false,
    infinite: true,
    autoplay: false,
    autoplaySpeed: 3000,
    speed: 300,
    slidesToShow: 1,
    slidesToScroll: 1,
    responsive: [
      {
        breakpoint: 1024,
        settings: {
          slidesToShow: 1,
          slidesToScroll: 1,
          infinite: true,
          dots: true
        }
      },
      {
        breakpoint: 600,
        settings: {
          slidesToShow: 1,
          slidesToScroll: 1
        }
      },
      {
        breakpoint: 480,
        settings: {
          slidesToShow: 1,
          slidesToScroll: 1
        }
      }
    ]
  });

    $('.vertical-slide-list').slick({
      dots: true,
      vertical: true,
      autoplay: true,
      arrows:false,
      slidesToShow: 4,
      slidesToScroll: 1,
      verticalSwiping: true,
      responsive: [
        {
          breakpoint: 992,
          settings: {
            slidesToShow: 2,
            slidesToScroll: 1,
            infinite: true,
            dots: true,
            vertical: false,
            verticalSwiping: false,
          }
        },
        {
          breakpoint: 768,
          settings: {
            slidesToShow: 1,
            slidesToScroll: 1,
            infinite: true,
            dots: true,
            vertical: false,
            verticalSwiping: false,
          }
        },
      ]
    });

    $('.dot-menu').click(function () {
      $('.left-menu').toggleClass('open');
    });


  $(".profile-slider").slick({

    // normal options...
    infinite: true,
    autoplay: true,
    slidesToShow: 3,
    slidesToScroll: 1,
    dots: false,
    arrows: true,
    centerMode: true,

    // the magic
    responsive: [
        {
      breakpoint: 1367,
      settings: {
        slidesToShow: 3,
        infinite: true,
        centerMode: false,
      }

    },
        {
      breakpoint: 1200,
      settings: {
        slidesToShow: 1,
        infinite: true,
        centerMode: false,
      }

    }, {

      breakpoint: 767,
      settings: {
        slidesToShow: 1,
        dots: false,
        centerMode: false,
      }

    },  {

      breakpoint: 992,
      settings: {
        slidesToShow: 3,
        dots: false,
        centerMode: false,
      }

    }]
  });

    
    
  jQuery('.next').on('click', function(){
    
      /*jQuery(this).parent().find(".author-details").each(function(){
      
        jQuery(this).removeClass("full-active");

      })

       jQuery(this).addClass("full-active");*/

     
  });

  jQuery('.prev').on('click', function(){

      var currentActive = $(".author-details.full-active");

        
        currentActive.removeClass("full-active");

       
        var previousElement = currentActive.prev(".author-details");

       
        if (previousElement.length > 0) {
           console.log($(this).attr('type'));
            previousElement.addClass("full-active");
            previousElement.find(".next").show();
            previousElement.find(".prev").show();
            currentActive.find(".next").hide();
            currentActive.find(".prev").hide();
        }

  });
      
  jQuery('.author-fold-1').addClass("full-active");
 
});




function myFunction() {
   
  var element = document.getElementById("site-menu-holder");
  element.classList.toggle("siteMenu-active");
}
//---------------Add developer js-----------------------------//

function isNumber(evt)
{
    evt = (evt) ? evt : window.event;
    var charCode = (evt.which) ? evt.which : evt.keyCode;
    if (charCode > 31 && (charCode < 48 || charCode > 57)) {
    return false;
    }
    return true;
} 
     $('#user_first_name').on('keyup', function() {
    // Get textbox value and remove non-alphabet characters
    var val = $(this).val().replace(/[^a-zA-Z]/g, '');
    // Set textbox value to cleaned-up value
    $(this).val(val);
  });

       $('#user_last_name').on('keyup', function() {
    // Get textbox value and remove non-alphabet characters
    var val = $(this).val().replace(/[^a-zA-Z]/g, '');
    // Set textbox value to cleaned-up value
    $(this).val(val);
  });

  $('#user_city').on('keyup', function() {
    // Get textbox value and remove non-alphabet characters
    var val = $(this).val().replace(/[^a-zA-Z]/g, '');
    // Set textbox value to cleaned-up value
    $(this).val(val);
  });

  function enableAllFileds(obj) {
      $.each($(obj).find("input,select,textarea"), function() {
          var attr = $(this).attr('disabled');
          if (typeof attr !== typeof undefined && attr !== false) {
              $(this).prop("disabled", false);
              $(this).removeClass("disable");
              $(this).closest('.form').removeClass('blur_bw');
              $(this).attr("wasDisabled", 'Y');
              $(obj).find(".dis_abled").attr("hasDisableCSS", "Y");
              $(obj).find(".dis_abled").removeClass("dis_abled");
          }
      });
  }

  function disableAllFileds(obj) {
      $.each($(obj).find("input,select,textarea"), function() {
          if ($(this).attr("wasDisabled") == 'Y') {
              $(this).prop("disabled", true);
              $(obj).find("span[hasDisableCSS=Y],div[hasDisableCSS=Y]").addClass("dis_abled");
          }
      });
  }  
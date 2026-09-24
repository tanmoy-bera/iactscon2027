<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=0, interactive-widget=resizes-content" />
  <link rel="icon" href="<?= _BASE_URL_ ?>images/favicon.png" type="favicon">
  <title>:: <?= $title ?> | <?php echo $cfg['EMAIL_CONF_NAME']; ?> ::</title>


  <style>
    #loading_indicator {
      position: absolute;
      top: 0;
      bottom: 0;
      left: 0;
      right: 0;
      margin: auto;
      border: 10px solid grey;
      border-radius: 50%;
      border-top: 10px solid blue;
      width: 100px;
      height: 100px;
      /*animation: spinIndicator 1s linear infinite;*/
    }

    @keyframes spinIndicator {
      100% {
        transform: rotate(360deg);
      }
    }

    #toast-container>.toast-error {
      background-image: none;
      background-color: #ff4d4d;
      color: #fff;
    }

    .toast-message {
      font-family: Calibri;
    }

    .toast {
      opacity: 1 !important;
    }
  </style>
  <?php
  $randomVersion = mt_rand();
  ?>

  <!-- <link href="<?= _DIR_CM_CSS_ . "website/" ?>bootstrap.min.css" rel="stylesheet"> -->
<!-- 
  <link href="<?= _DIR_CM_CSS_ . "website/" ?>slick.css" rel="stylesheet" type="text/css">
  <link href="<?= _DIR_CM_CSS_ . "website/" ?>slick-theme.css" rel="stylesheet" type="text/css">
  <link href="<?= _DIR_CM_CSS_ . "website/" ?>slick-lightbox.css"
    rel="stylesheet" type="text/css"> -->
  <!-- <link href="css/aos.css" rel="stylesheet" type="text/css"> -->

  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css" rel="stylesheet">

  <!--   <link href="<?= _DIR_CM_CSS_ . "website/" ?>custom.css" rel="stylesheet" type="text/css"> -->
  <!-- <link rel="stylesheet" type="text/css" href="https://ruedakolkata.com/master/css/website/registration.tariff_css.php?background_color=FF5733&link_color=33CAFF" /> -->
<!-- 
  <link href="<?= _DIR_CM_CSS_ . "website/" ?>custom_css.php??v=<?php echo $randomVersion; ?>&lpimg='<?= $cfg['LANDING_PROFILE_IMG'] ?>'&outerBgImg='<?= $cfg['OUTER_BG_IMG'] ?>'&profileImg='<?= $cfg['PROFILE_Back_IMG'] ?>'&color=<?= $cfg['color'] ?>&dark_color=<?= $cfg['dark_color'] ?>&light_color=<?= $cfg['light_color'] ?>" rel="stylesheet" type="text/css">
  <link href="<?= _DIR_CM_CSS_ . "website/" ?>custom-style.css?>" rel="stylesheet" type="text/css"> -->

  <link rel='stylesheet' href='https://cdnjs.cloudflare.com/ajax/libs/Swiper/4.3.5/css/swiper.min.css'>

  <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
  <script src="<?= _BASE_URL_ ?>js/website/jquery-ui.js"></script>
  <script src='<?= _BASE_URL_ ?>js/website/TweenMax.min.js'></script>
  <script src="<?= _BASE_URL_ ?>js/website/bootstrap.bundle.min.js"></script>
  <script src="<?= _BASE_URL_ ?>js/website/slick.min.js"></script>
  <script
    src="https://cdnjs.cloudflare.com/ajax/libs/slick-lightbox/0.2.12/slick-lightbox.min.js"></script>
  <script src="<?= _BASE_URL_ ?>js/website/aos.js"></script>

  <script src="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/owl.carousel.min.js" integrity="sha512-bPs7Ae6pVvhOSiIcyUClR7/q2OAsRiovw4vAkX+zJbw3ShAeeqezq50RIIcIURq7Oa20rW2n2q+fyXBNcU9lrw==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>


  <!-- Toastr JS -->
  <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>

  <script src="<?= _BASE_URL_ ?>js/website/returnData.process.js"></script>
  <script src="<?= _BASE_URL_ ?>js/website/custom.js"></script>


  <!-- new profile 24/10/2024 -->
  <!-- <script src="https://unpkg.com/feather-icons"></script>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/assets/owl.carousel.css" integrity="sha512-UTNP5BXLIptsaj5WdKFrkFov94lDx+eBvbKyoe1YAfjeRPC+gT5kyZ10kOHCfNZqEui1sxmqvodNUx3KbuYI/A==" crossorigin="anonymous" referrerpolicy="no-referrer" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/assets/owl.theme.default.css" integrity="sha512-OTcub78R3msOCtY3Tc6FzeDJ8N9qvQn1Ph49ou13xgA9VsH9+LRxoFU6EqLhW4+PKRfU+/HReXmSZXHEkpYoOA==" crossorigin="anonymous" referrerpolicy="no-referrer" />
  <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.7.0/css/all.css" integrity="sha384-lZN37f5QGtY3VHgisS14W3ExzMWZxybE1SJSEsQp9S+oqd12jhcu+A56Ebc1zFSJ" crossorigin="anonymous">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css" integrity="sha512-Kc323vGBEqzTmouAECnVceyQqyqdsSiqLQISBL29aUW4U/M7pSPA/gEUZQqv1cwx4OnYxTxve5UMg5GT6L4JJg==" crossorigin="anonymous" referrerpolicy="no-referrer" />
  <link href="https://cdn.jsdelivr.net/gh/hung1001/font-awesome-pro@4cac1a6/css/all.css" rel="stylesheet" type="text/css" />



  <link href="<?= _BASE_URL_ ?>css/website/profile.css" rel="stylesheet" type="text/css" />
  <link href="<?= _BASE_URL_ ?>css/website/profile_css" rel="stylesheet" type="text/css" />
  <link href="<?= _BASE_URL_ ?>css/website/profile-responsive.css" rel="stylesheet" type="text/css" /> -->

<!-- =============================== X ============================== -->

</head>
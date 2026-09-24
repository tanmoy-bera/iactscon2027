jQuery(document).ready(function(){
    jQuery(".mobileMenuToggle").click(function(){
        jQuery(".profile-left-section").toggleClass("toggole-menu");
        jQuery(".mobileMenuToggle").toggleClass("menucross");
    });
  });
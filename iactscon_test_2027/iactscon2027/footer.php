 <div class="float-btn">
        <button>
          <i class="fas fa-comment-alt"></i>
        </button>
        <ul class="im-links">
          <?php
          $sqlFooterIcon  = array();
          $sqlFooterIcon['QUERY'] = "SELECT * FROM " . _DB_ICON_SETTING_ . " 
                                  WHERE `status`='A' AND purpose='Footer' order by id ";
          //$sql['PARAM'][]  = array('FILD' => 'status' ,         'DATA' => 'A' ,                   'TYP' => 's');          
          $resultFooterIcon = $mycms->sql_select($sqlFooterIcon);
          foreach ($resultFooterIcon as $k => $val) {

            if ($val['title'] == 'Email') {
              $title = $val['page_link'];
              $href = 'mailto:' . $val['page_link'];
            } else if ($val['title'] == 'Phone') {
              $href = 'tel:+:' . $val['page_link'];
              $title = $val['page_link'];
            } else if ($val['title'] == 'Refund Policy') {
              $title = $val['title'];
              $href = '#';
            } else {
              $title = $val['title'];
              $href = 'javascript:void(0)';
            }
          ?>
            <li class="foot-btn" id="slide1" data-aos="zoom-in" data-aos-delay="200"
              data-aos-duration="600">
              <img src="<?= _BASE_URL_ . $cfg['EMAIL.HEADER.FOOTER.IMAGE']; ?><?= $val['icon'] ?>" alt="" /><a href="<?= $href ?>"><?= $title ?></a>
            </li>
          <?php
          }
          ?>

        </ul>
      </div>
<footer class="main-footer">

      <div class="col-lg-6 foot-left d-none">
        <ul>
            <?php
            $sqlFooterIcon  = array();
            $sqlFooterIcon['QUERY'] = "SELECT * FROM "._DB_ICON_SETTING_." 
                                  WHERE `status`='A' AND purpose='Footer' order by id ";
                       //$sql['PARAM'][]  = array('FILD' => 'status' ,         'DATA' => 'A' ,                   'TYP' => 's');          
            $resultFooterIcon = $mycms->sql_select($sqlFooterIcon);
             foreach($resultFooterIcon as $k=>$val)
             { 

                if($val['title']=='Email')
                {
                  $title = $val['page_link'];
                  $href = 'mailto:'.$val['page_link'];
                }
                else if($val['title']=='Phone')
                {
                  $href = 'tel:+:'.$val['page_link'];
                  $title = $val['page_link'];
                }
                else if($val['title']=='Refund Policy')
                {
                  $title = $val['title'];
                  $href = $val['page_link'];
                }
                else
                {
                  $title = $val['title'];
                  $href = 'javascript:void(0)';
                }
            ?>
              <li class="foot-btn" id="slide1x" data-aos="zoom-in" data-aos-delay="200"
                data-aos-duration="600">
                <img src="<?=_BASE_URL_.$cfg['EMAIL.HEADER.FOOTER.IMAGE']?>/<?=$val['icon']?>" alt="" /><a href="<?=$href?>"><?=$title?></a>
              </li>
            <?php
            }
            ?>
         
        </ul>
      </div>
      <?php
      //Fetching Footer Text
      $sql 	=	array();
      $sql['QUERY'] = "SELECT `display_footer_text` FROM "._DB_COMPANY_INFORMATION_." 
                        WHERE `id`!=''";
      $result 	 = $mycms->sql_select($sql);
      if($result)
			{
      ?>

        <marquee><?=$result[0]['display_footer_text']?>
        </marquee>
      
      <?php } ?>
  
</footer>

<script type="text/javascript">
 $(document).ready(function(){
     $('.drama-nav .slick-prev').click();
    var slide_count = '<?=$hotel_count;?>';
    if(slide_count!='' && slide_count>0)
    {

       jQuery('.carv-slider').slick({
      slidesToShow: 1,
      slidesToScroll: 1,
      arrows: false,
      asNavFor: '.carv-nav',
      autoplay: true,
      autoplaySpeed: 2000
      });

      jQuery('.carv-nav').slick({
        slidesToShow: slide_count,
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
      });

    }
   // alert(slide_count);
      
 }); 
  // let vh = window.innerHeight * 0.01;
  // document.documentElement.style.setProperty('--vh', `${vh}px`);
</script>
   
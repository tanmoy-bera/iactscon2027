<?php
include_once("includes/frontend.init.php"); 
include_once("includes/function.registration.php");
include_once("includes/function.delegate.php");
include_once("includes/function.invoice.php");
include_once("includes/function.workshop.php");
include_once("includes/function.dinner.php");
include_once("includes/function.accompany.php");
include_once("includes/function.abstract.php");
include_once('includes/function.accommodation.php');

$sql    =   array();
                    $sql['QUERY'] = "SELECT * FROM "._DB_EMAIL_SETTING_." 
                                            WHERE `status`='A' order by id desc limit 1";
                     //$sql['PARAM'][]  =   array('FILD' => 'status' ,           'DATA' => 'A' ,                   'TYP' => 's');                    
$result = $mycms->sql_select($sql);
$row             = $result[0];

$header_image = _BASE_URL_.$cfg['EMAIL.HEADER.FOOTER.IMAGE'].$row['logo_image'];
if($row['header_image']!='')
{
    $emailHeader  = $header_image;
}

$sqlIcon  = array();
$sqlIcon['QUERY'] = "SELECT * FROM "._DB_ICON_SETTING_." 
                      WHERE `status`='A' AND `purpose`='Registration' order by id ";
           //$sql['PARAM'][]  = array('FILD' => 'status' ,         'DATA' => 'A' ,                   'TYP' => 's');          
$resultIcon = $mycms->sql_select($sqlIcon);

$title = 'Login'; 
?>
<!DOCTYPE html>
<html>

 <?php 
    setTemplateStyleSheet();
    setTemplateBasicJS();
    backButtonOffJS();
 include_once('header.php'); 

 $cutoffs       = fullCutoffArray();  
 $currentCutoffId  = getTariffCutoffId();

 ?>

<body class="single inner-page">
 <div id ="loading_indicator" style="display:none;"> </div>
  <main>

    <?php include_once('sidebar_icon.php'); ?>

    <section class="main-section">
      <div class="container">
        <div class="inner-greadient-sec">
          <div class="row">

            <div class="col-lg-6 drama-total-holder">
              <div class="cart">
                <img src="images/cart.png" alt="">
              </div>
              <div class="category-head">
                <div class="category-left">
                  <h3><?=$cfg['LOGIN_TITLE']?></h3>
                </div>
                <div class="category-right">
                  <h4><i><?=getCutoffName($currentCutoffId)?></i></h4>
                  <p>till <?php 
                            $endDate = getCutoffEndDate($currentCutoffId);
                            echo date('jS M `y', strtotime($endDate));?> </p>
                </div>
              </div>

              <div class="pd-row">
                
                  <div class="row">
                    <div class="col-lg-12 form mb-3">
                   
                        <label>Registered E-mail id</label>
                        <input type="text" class="form-control" name="user_email_id" id="user_email_id" value="<?=$mycms->getSession('USER.EM')?>" >
                    </div>

                    <div class="col-lg-12 form mb-3">

                     
                        <label>Unique Sequence</label>
                       <input class="form-control" type="text" name="user_unique_sequence" id="user_unique_sequence" value="#" placeholder ="Ex: #0000" onkeypress="return isNumber(event)">
                    </div>

                    <div class="col-lg-12 mb-3 text-center pt-20">
                      <input type="button" value="Login" class="btn next-btn loginBtn" id="loginBtn">

                    </div>
                  </div>

                

                
              </div>
            </div>

            <div class="col-lg-6">

              <div class="logo-section" data-aos="fade-left" data-aos-duration="800">
                
                <div class="site-logo">
                  <?php
                  if($emailHeader)
                  {
                  ?>
                    <a href="#"><img src="<?=$emailHeader?>" alt="" /></a>
                  <?php 
                  }
                  ?>
                </div>
              </div>
                <div class="site-menu-holder" id="site-menu-holder">
                    <button onclick="myFunction()" class="clicknow"><i class="fas fa-chevron-down"></i></button>

             		 <div class="site-menu">

	                      <?php
	                      if($resultIcon)
	                      {
	                          $i=0;
	                          
	                          foreach($resultIcon as $k=>$val)
	                          {

  	                            $i++;
                                // echo '<pre>'; print_r($resultIcon); 
  	                            
  	                            if($i==1)
  	                            {
  	                              $activeclass = '';
  	                            }
  	                            else
  	                            {
  	                              $activeclass = '';
  	                            }

                                if($val['title']=='Registration')
                                {
                                  $url = (($val['title']=='Registration')? _BASE_URL_.'registration.tariff.php':'javascript:void(0)');
                                }
                                if($val['title']=='Abstract')
                                {
                                  $url = (($val['title']=='Abstract')? _BASE_URL_.'abstract.php':'javascript:void(0)');
                                }

                                

                                
	                          ?>

  	                            <a href="<?=$url?>" class="main-menu <?=$activeclass?>" id="item<?=$i;?>">
  	                              <div data-aos="zoom-in" data-aos-delay="100" data-aos-duration="500">
  	                                <img src="<?=_BASE_URL_.$cfg['EMAIL.HEADER.FOOTER.IMAGE'];?><?=$val['icon']?>" alt="" />
  	                                <p><?=$val['title']?></p>
  	                              </div>
  	                            </a>

	                         <?php
	                         
	                         }
	                     }
	                     ?> 

                    </div>
            </div>
            </div>
          </div>
        </div>
      </div>
    </section>

   <?php include_once('footer.php'); ?>


    <div class="checkout-main-wrap">
      <div class="checkout-popup">
        <div class="card-details">
          <div class="card-details-inner">
            <img src="images/payment.jpg" alt="" />

            <div class="card-form">
              <form>
                <div class="row">
                  <div class="col-lg-12">
                    <h6>Payment Details</h6>
                  </div>
                  <div class="col-lg-12"><input type="number" placeholder="Card Number" /></div>
                  <div class="col-lg-4"><input type="number" placeholder="Exp." /></div>
                  <div class="col-lg-4"><input type="number" placeholder="Date" /></div>
                  <div class="col-lg-4"><input type="number" placeholder="CVV" /></div>
                </div>
              </form>
            </div>

            <div class="redio-select">
              <label><input type="radio" name="radio" /> Card</label>
              <label><input type="radio" name="radio" /> UPI</label>
              <label><input type="radio" name="radio" /> Cheque</label>
              <label><input type="radio" name="radio" /> NEFT</label>
            </div>

            <div class="policy-div">
              <ul>
                <li><a href="">Cancellation Policy</a></li>
                <li><a href="">Privacy Policy</a></li>
              </ul>
            </div>

            <div class="righr-btn"><input type="button" class="pay-button" value="Pay Now" /></div>


          </div>
        </div>
        <div class="cart-details">
          <div class="cart-heading">
            <h5>Order Summary</h5>

            <a href="#" class="close-check"><span>&#10005;</span> close</a>
          </div>

          <div class="cart-data-row">
            <table>
              <tbody>
                <tr>
                  <td><img src="images/cat-ic-2.png" alt="" /></td>
                  <td>Live Surgery Workshop <br>1st September</td>
                  <td><span class="price">2000</span></td>
                </tr>
                <tr>
                  <td><img src="images/cat-ic-3.png" alt="" /></td>
                  <td>Accompanying Person 1 <br>Mandira Mudya</td>
                  <td><span class="price">1000</span></td>
                </tr>
              </tbody>
              <tfoot>
                <tr>
                  <td colspan="2"></td>
                  <td><span class="total">Total INR 500</span></td>
                </tr>
              </tfoot>
            </table>
          </div>
        </div>
      </div>
    </div>

  </main>

  <script type="text/javascript">
    $('.loginBtn').click(function(){
      
        var user_email_id = $('#user_email_id').val();
        var user_unique_sequence = $('#user_unique_sequence').val();

        var flag=0;
       
        if(user_email_id=='')
        {
            toastr.error('Please enter the email id', 'Error', {
              "progressBar": true,
              "timeOut": 3000, 
              "showMethod": "slideDown", 
              "hideMethod": "slideUp"    
            });

            flag=1;
            return false;
        }
        if(user_unique_sequence!='')
        {

            var regex = /^#\d+$/;
            if (regex.test(user_unique_sequence)) {
              //console.log("String matches the pattern.");
            } else {
              //console.log("String does not match the pattern.");
              toastr.error('Please enter the unique sequence', 'Error', {
                "progressBar": true,
                "timeOut": 3000, 
                "showMethod": "slideDown", 
                "hideMethod": "slideUp"    
              });

              flag=1;
              return false;
            }
            
        }

        if(user_email_id!='')
        {
            var filter =
              /^([\w-\.]+)@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.)|(([\w-]+\.)+))([a-zA-Z]{2,4}|[0-9]{1,3})(\]?)$/;
            if (!filter.test(user_email_id)) {

                  toastr.error('Please enter valid email id', 'Error', {
                  "progressBar": true,
                  "timeOut": 3000, 
                  "showMethod": "slideDown", 
                  "hideMethod": "slideUp"    
                });

                flag=1;
                return false;

            }  
        }

        if(flag==0)
        {
             $.ajax({
                type: "POST",
                url: jsBASE_URL + 'login.process.php',
                data: 'action=getLoginValidation&user_email_id=' + user_email_id+'&user_unique_sequence='+user_unique_sequence,
                dataType: 'json',
                async: false,
                success: function(JSONObject) {
                    console.log(JSONObject);

                    if(JSONObject.error==400)
                    {
                        if(JSONObject.msg)
                        {
                            toastr.error(JSONObject.msg, 'Error', {
                            "progressBar": true,
                            "timeOut": 5000, 
                            "showMethod": "slideDown", 
                            "hideMethod": "slideUp"    
                           });
                        }
                         
                    }
                    else if(JSONObject.succ==200)
                    {

                      $('#loading_indicator').show();
                      $('#loginBtn').prop('disabled', true);
                      if(JSONObject.msg)
                        {
                            toastr.success(JSONObject.msg, 'Success', {
                                "progressBar": true,
                                "timeOut": 3000, 
                                "showMethod": "slideDown", 
                                "hideMethod": "slideUp"    
                             });
                        }  

                      setTimeout(function () {
                        $('#loading_indicator').hide();
                        $('#loginBtn').prop('disabled', false);
                           window.location.href= jsBASE_URL + 'profile.php';

                        },1500);
                    }

                    
                }
            });
        }

    })
  </script>

</body>

</html>
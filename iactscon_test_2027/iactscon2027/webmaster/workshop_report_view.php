<?
include_once("includes/source.php");
include_once('includes/init.php'); 
include_once(__DIR__ . "/../includes/function.registration.php");
include_once(__DIR__. "/../includes/function.delegate.php");
include_once(__DIR__. "/../includes/function.invoice.php");
include_once(__DIR__. "/../includes/function.workshop.php");
include_once(__DIR__. "/../includes/function.dinner.php");
include_once(__DIR__. "/../includes/function.accompany.php");
include_once(__DIR__. "/../includes/function.accommodation.php");
include_once(__DIR__. "/../includes/function.abstract.php");
$loggedUserID = $mycms->getLoggedUserId();
?>

<body>
    <?php include_once("includes/left-menu.php"); ?>
    <header>
        <h2>Workshop Report</h2>
        <?php include_once("includes/header_right.php"); ?>
    </header>
    <?
        $workshop_id                   = addslashes(trim($_REQUEST['workshopId']));
		$delegateCounter              = 0;
		$sqlFetchWorkshopDetails       = WorkshopReportQuery($workshop_id);
		$resultWorkshopDetails         = $mycms->sql_select($sqlFetchWorkshopDetails);	
		$rowWorkshopDetails            = $resultWorkshopDetails[0];
		$totalSeat					   = ($rowWorkshopDetails['paid_For'] + $rowWorkshopDetails['zerovalue_pay']+$rowWorkshopDetails['complementary_pay']);
	?>
    <div class="body_wrap">
        <div class="page_top_wrap mb-3">
            <div class="page_top_wrap_left">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="#">Workshop</a></li>
                        <li class="breadcrumb-item">Workshop Report</li>
                        <li class="breadcrumb-item active" aria-current="page">Details</li>
                    </ol>
                </nav>
                <h2>Workshop Details Report</h2>
            </div>
            <?php
              $searchCondition = '';
            if(isset($_REQUEST['payStatus']) && $_REQUEST['payStatus'] != '')
            {
                $searchCondition = " AND workshop.payment_status = '".trim($_REQUEST['payStatus'])."' ";
            }
            if($_REQUEST['src_country_name']!="")
            {
                $searchCondition .= "AND delegate.user_country_id = '".$_REQUEST['src_country_name']."%'";
            }
            if($_REQUEST['src_state_name']!="")
            {
                $searchCondition .= "AND delegate.user_state_id = '".$_REQUEST['src_state_name']."'";
            }
            
            
            
            $sqlFetchWorkshopBooking  = array();	
            $sqlFetchWorkshopBooking['QUERY'] 	  = "   SELECT workshop.* ,delegate.id AS delegateId, delegate.user_full_name, delegate.user_unique_sequence, delegate.user_registration_id, delegate.user_mobile_no, delegate.user_email_id,delegate.registration_payment_status,delegate.isRegistration,delegate.registration_classification_id,delegate.membership_number,delegate.registration_tariff_cutoff_id
                                                FROM "._DB_REQUEST_WORKSHOP_." workshop	
                                        INNER JOIN "._DB_USER_REGISTRATION_." delegate
                                                ON delegate.id = workshop.delegate_id
                                        INNER JOIN "._DB_SLIP_." slip  
                                                ON slip.id = workshop.refference_slip_id
                                                WHERE delegate.status = ?
                                                AND workshop.status = ?
                                                AND slip.status = ?
                                                AND workshop.workshop_id = ? 
                                                    ".$searchCondition."
                                            ORDER BY workshop.id DESC";						   
            //echo nl2br($sqlFetchWorkshopBooking);						   
            $sqlFetchWorkshopBooking['PARAM'][]   = array('FILD' => 'delegate.status',            'DATA' =>'A',   'TYP' => 's');
            $sqlFetchWorkshopBooking['PARAM'][]   = array('FILD' => 'workshop.status',            'DATA' =>'A',   'TYP' => 's');
            $sqlFetchWorkshopBooking['PARAM'][]   = array('FILD' => 'slip.status',            	  'DATA' =>'A',   'TYP' => 's');
            $sqlFetchWorkshopBooking['PARAM'][]   = array('FILD' => 'workshop.workshop_id',       'DATA' =>$workshop_id,   'TYP' => 's');		
            $resultWorkshopBooking        = $mycms->sql_select($sqlFetchWorkshopBooking);
            
            ?>
             <div class="page_top_wrap_right">
                <p><?php user(); ?>Total: <b><?= count($resultWorkshopBooking) ?></b></p>
                <a href="workshop_report.php" class="badge_danger"><i class="fal fa-arrow-left"></i>Back</a>
            </div>
        </div>

        <div class="regi_search_wrap mb-3">
            <div class="regi_search">
                <?php search(); ?>
                <input id="SearchInput" placeholder="Search by Name, Email, Mobile, or Reg ID...">
            </div>
            <div class="regi_search_wrap_btn_box">
                <a href="javascript:void(null)" onclick="$('.filter_wrap').slideToggle(); $(this).toggleClass('active');"><?php filter(); ?>Filter</a>
            </div>
        </div>

        <div class="filter_wrap mb-3">
            <form name="frmSearch" method="post" action="workshop_report_view.php?workshopId=<?=$workshop_id?>" >
            <h4 class="filter_heading"><span>Advanced Filtering</span><a class="close_filter" onclick="$('.filter_wrap').slideUp();"><?php close(); ?></a></h4>
            <div class="filter_body">
                <div>
                    <label>Payment Status:</label>
                    <select name="payStatus" id="payStatus" >
                        <option value="">-- Select Payment Status --</option>
                        <option value="PAID" <?=(trim($_REQUEST['payStatus']=="PAID"))?'selected="selected"':''?>>PAID</option>
                        <option value="UNPAID" <?=(trim($_REQUEST['payStatus']=="UNPAID"))?'selected="selected"':''?>>UNPAID</option>
                        <option value="ZERO_VALUE" <?=(trim($_REQUEST['payStatus']=="ZERO_VALUE"))?'selected="selected"':''?>>ZERO_VALUE</option>
                    </select>
                </div>
                <div>
                    <label>Select Country:</label>
                    <select name="src_country_name" id="src_country_name" operationmode="countryControl" fortype="country"  onchange="generateStateOptionList(this);">
                        <option value="">-- Select Country --</option>
                        <?php
                        $sqlCountryList = array();
                        $sqlCountryList['QUERY']    = "SELECT * FROM "._DB_COMN_COUNTRY_." 
                                                    WHERE `status`= ? 
                                                ORDER BY `country_name` ASC";
                        $sqlCountryList['PARAM'][]   = array('FILD' => 'status',            'DATA' =>'A',   'TYP' => 's');						   
                        $resultCountryList = $mycms->sql_select($sqlCountryList);
                        if($resultCountryList)
                        {
                            foreach ($resultCountryList as $i=>$rowCountry)
                            {
                        ?>
                                <option <?=(trim($_REQUEST['src_country_name']==$rowCountry['country_id']))?'selected="selected"':''?> value="<?=$rowCountry['country_id']?>"><?=$rowCountry['country_name']?></option>
                        <?php    
                            }
                        }
                        ?>
                    </select> 
                </div>
                <div>
                    <label>State Name:</label>
                    <select name="src_state_name" id="src_state_name"  style="text-transform:uppercase;" 
                        operationMode="stateControl" fortype="country">
                        <option value="">-- Select Country First --</option>
                    </select>
                </div>
            </div>
            <div class="filter_bottom">
                <button onclick="clearFilters();"><?php reseti(); ?></button>
                <button type="submit">Apply</button>
            </div>
            </form>
        </div>
       
        <div class="workshop_overview_wrap mb-3">
            <h2 class="sub_head">Workshop Overview</h2>
            <ul>
                <li>Workshop Classification<span><?=$rowWorkshopDetails['classification_title']?></span></li>
                <li>Seat Limit<span><?=$rowWorkshopDetails['seat_limit']?></span></li>
                <li>Total Paid Delegate(s)<span><?=$rowWorkshopDetails['paid_For']?></span></li>
                <li>Total Unpaid Delegate(s)<span><?=$rowWorkshopDetails['notPaid_For']?></span></li>
                <li>Total Complementary Delegate(s)<span><?=$rowWorkshopDetails['complementary_pay']?></span></li>
                <li>Total Reg. Delegate(s)<span><?=$rowWorkshopDetails['applied_for']?></span></li>
                <li>Total Zero Value Delegate(s)<span><?=$rowWorkshopDetails['zerovalue_pay']?></span></li>
                <?
								
                if($totalSeat>0)
                {
                ?>
                <li>Seat Left<span><?=($rowWorkshopDetails['seat_limit'] - $totalSeat)?></span></li>
                <?
                }
                ?>
            </ul>
        </div>
        <div class="spot_listing">
            <?php
							
          
            if($resultWorkshopBooking)
            {
                foreach($resultWorkshopBooking as $keyWorkshopBooking=>$rowWorkshopBooking)
                {
                    $delegateCounter++;
                    
                    // USER REGISTRATION PAYMENT STATUS
                    $regPaymentStatus         = array();
                    //$regPaymentStatus         = conferenceRegistrationPaymentStatus($rowWorkshopBooking['delegateId']);
                    
                    // WORKSHOP REGISTRATION PAYMENT STATUS
                    $workshopPaymentStatus    = array();
                    //$workshopPaymentStatus    = workshopRegistrationPaymentStatus($rowWorkshopBooking['delegateId']);
            ?>
            <div class="spot_box">
                <div class="spot_box_top">
                    <div class="spot_name d-flex align-items-start">
                        <div class="regi_img_circle">
                            <!-- <img src="" alt="" class="w-100 h-100"> -->
                            <span><?=$delegateCounter?></span>
                        </div>
                        <div>
                            <div class="regi_name"><?=strtoupper($rowWorkshopBooking['user_full_name'])?></div>
                            <div class="regi_type">
                                <?php
                                    if ($rowWorkshopBooking['isRegistration'] == "Y") {
                                        if (empty(getRegClsfName($rowWorkshopBooking['registration_classification_id']))) {
                                            $regClsName = getRegClsfComboName($rowWorkshopBooking['registration_classification_id']);
                                        } else {
                                            $regClsName = getRegClsfName($rowWorkshopBooking['registration_classification_id']);
                                        }
                                    }
                                    ?>
                                <?php if ($rowWorkshopBooking['membership_number'] != '' || $rowWorkshopBooking['membership_number'] !=0) {
                                    $membership_number = "-" . $rowWorkshopBooking['membership_number'];
                                }else{
                                    $membership_number = '';
                                }
                                ?>
                                <span class="badge_padding badge_primary"><?=$regClsName?><?= $membership_number ?></span>
                                    <?php if (getCutoffName($rowWorkshopBooking['registration_tariff_cutoff_id'])) { ?>
                                    <span class="badge_padding badge_secondary">
                                        <?php
                                        echo '<span style="color: #8ee0f5; text-transform: uppercase;" title="Cutoff">' . getCutoffName($rowWorkshopBooking['registration_tariff_cutoff_id']) . '</span>';

                                        ?></span>
                                <?php } ?>
                            </div>
                            <div class="regi_contact">
                                <span>
                                    <?php call(); ?><?=$rowWorkshopBooking['user_mobile_isd_code'].$rowWorkshopBooking['user_mobile_no']?>
                                </span>
                                <span>
                                    <?php email(); ?><?=$rowWorkshopBooking['user_email_id']?>
                                </span>
                            </div>
                        </div>
                    </div>
                    <div class="spot_details">
                        <div class="spot_details_box">
                            <h5>Reg Number</h5>
                            <p><?=$rowWorkshopBooking['user_registration_id']?></p>
                        </div>
                        <div class="spot_details_box">
                            <h5>Uniq Sequence</h5>
                            <h6><?php qr(); ?><b><?=$rowWorkshopBooking['user_unique_sequence']?></b></h6>
                        </div>
                        <div class="spot_details_box align-items-end">
                            <h5>Payment Status</h5>
                            <? if($rowWorkshopBooking['payment_status']=='PAID')
                                    {
                                    ?>
                                        <span class="pay_status mi-1 badge_padding badge_success w-max-con text-uppercase"><?php paid(); ?><?=$rowWorkshopBooking['payment_status']?></span>

                                    <?
                                    }else if($rowWorkshopBooking['payment_status']=='UNPAID'){
                                        ?>
                                        <span class="pay_status mi-1 badge_padding text_danger w-max-con text-uppercase"><?php unpaid(); ?><?=$rowWorkshopBooking['payment_status']?></span>
                                        <?
                                    }else{
                                        ?>
                                        <span class="pay_status mi-1 badge_padding badge_success w-max-con text-uppercase"><?=$rowWorkshopBooking['payment_status']?></span>
                                        <?
                                    }
                                   ?>
                            <!-- <h6>INR <?=$rowWorkshopBooking['payment_status']?></h6> -->
                        </div>
                    </div>
                </div>
            </div>
            <? } } ?>
          
        </div>
    </div>
    <?php include_once("includes/popup.php"); ?>
</body>
<?php include_once("includes/js-source.php"); ?>

</html>
<script>
function clearFilters() {
    // Reset all form fields
    document.getElementById('payStatus').value = "";
    document.getElementById('src_country_name').value = "";
    
    // Reset state dropdown
    var stateSelect = document.getElementById('src_state_name');
    stateSelect.innerHTML = '<option value="">-- Select Country First --</option>';
    stateSelect.disabled = true;
    
    // Submit the form with empty values
    document.forms['frmSearch'].submit();
}
function generateStateOptionList(obj)
{
    
    var countryId	= $(obj).val();
    console.log(jsBASE_URL+'returnData.process.php?act=generateStateList&countryId='+countryId);
    var forType 	= $(obj).attr("forType");
    if(countryId!='')
    {
        $.ajax({
                    type: "POST",
                    url: jsBASE_URL+'returnData.process.php',
                    data: 'act=generateStateList&countryId='+countryId,
                    dataType: 'html',
                    async: false,
                    success: function(returnMessage)
                    {
                        if(returnMessage!='')
                        {
                            $("select[operationMode=stateControl][forType="+forType+"]").html("");
                            $("select[operationMode=stateControl][forType="+forType+"]").removeAttr("disabled");
                            $("select[operationMode=stateControl][forType="+forType+"]").html(returnMessage);		//	"+forType+"
                        }							
                    }
                });
    }
    else
    {
        $("select[operationMode=stateControl][forType="+forType+"]").html('<option value="">-- Select Country First --</option>');
        $("select[operationMode=stateControl][forType="+forType+"]").attr("disabled","disabled");
    }
//});
}
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('SearchInput');
    const abstracts = document.querySelectorAll('.spot_box');

    searchInput.addEventListener('input', function() {
        const query = this.value.toLowerCase();

        abstracts.forEach(box => {
            const text = box.innerText.toLowerCase();
            if (text.includes(query)) {
                box.style.display = ''; // show
            } else {
                box.style.display = 'none'; // hide
            }
        });
    });
});
</script>
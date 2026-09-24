<?php 
include_once("includes/source.php"); 
include_once('includes/init.php');
include_once('includes/function.workshop.php');
include_once(__DIR__ . "/../includes/function.registration.php");
include_once(__DIR__. "/../includes/function.delegate.php");
include_once(__DIR__. "/../includes/function.invoice.php");
include_once(__DIR__. "/../includes/function.workshop.php");
include_once(__DIR__. "/../includes/function.dinner.php");
include_once(__DIR__. "/../includes/function.accompany.php");
include_once(__DIR__. "/../includes/function.accommodation.php");
include_once(__DIR__. "/../includes/function.abstract.php");
include_once('includes/function.php');
?>
<body>
    <?php include_once("includes/left-menu.php"); ?>
    <header>
        <h2>Abstract Registration</h2>
       <?php include_once("includes/header_right.php"); ?>
    </header>

    <div class="body_wrap">
        <div class="page_top_wrap mb-3">
            <div class="page_top_wrap_left">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="#">Abstract Registration</a></li>
                        <li class="breadcrumb-item"><a href="#">Abstract List</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Submit Abstract</li>
                    </ol>
                </nav>
                <h2>Submit Abstract</h2>
                <!-- <h6>Issue ID cards, kits, and manage on-spot requests.</h6> -->
            </div>
            <!-- <div class="page_top_wrap_right">
                <p><?php printi(); ?>Card Printed: <b>0</b></p>
                <p><?php check(); ?>Delevered: <b>0</b></p>
                <p><?php user(); ?>Total: <b>2</b></p>
            </div> -->
        </div>

        <div class="regi_search_wrap mb-3">
            <div class="regi_search">
                <?php search(); ?>
                <input id="abstractSearchInput" placeholder="Search by Name, Email, Mobile, or Reg ID...">
            </div>
            <div class="regi_search_wrap_btn_box">
                <a href="javascript:void(null)" onclick="$('.filter_wrap').slideToggle(); $(this).toggleClass('active');"><?php filter(); ?>Filter</a>
            </div>
        </div>

        <div class="filter_wrap mb-3">
            <h4 class="filter_heading"><span>Advanced Filtering</span><a class="close_filter" onclick="$('.filter_wrap').slideUp();"><?php close(); ?></a></h4>
            <form name="frmSearch" method="post" action="submit_abstract.php" onSubmit="return FormValidator.validate(this);">
			<input type="hidden" name="act" value="search_registration" />
            <div class="filter_body">
                <div>
                    <label>User Name</label>
                    <input type="text" name="src_user_first_name" id="src_user_first_name"  value="<?= $_REQUEST['src_user_first_name'] ?>" />

                </div>
                <div>
                    <label>Unique Sequence:</label>
                  	<input type="text" name="src_access_key" id="src_access_key"  value="<?= $_REQUEST['src_access_key'] ?>" />

                </div>
                <div>
                    <label>Mobile No:</label>
                    	<input type="text" name="src_mobile_no" id="src_mobile_no"  value="<?= $_REQUEST['src_mobile_no'] ?>" />

                </div>
                <div>
                    <label>Email Id:</label>
					<input type="text" name="src_email_id" id="src_email_id" value="<?= $_REQUEST['src_email_id'] ?>" />

                </div>
            </div>
            <div class="filter_bottom">
                <button type="button" name="" value="Clear"  onClick="window.location.href='submit_abstract.php'"><?php reseti(); ?></button>
                <button type="submit" name="goSearch" value="Search" >Apply</button>
            </div>
          </form>
        </div>
 
        <div class="spot_listing" >
            <?php

                $alterCondition = "";

                if ($_REQUEST['src_access_key'] != '') {
                    $alterCondition   .= " AND registration_list.user_unique_sequence LIKE '%" . $_REQUEST['src_access_key'] . "%'";
                }
                if ($_REQUEST['src_mobile_no'] != '') {
                    $alterCondition   .= " AND registration_list.user_mobile_no LIKE '%" . $_REQUEST['src_mobile_no'] . "%'";
                }
                if ($_REQUEST['src_email_id'] != '') {
                    $alterCondition   .= " AND registration_list.user_email_id LIKE '%" . $_REQUEST['src_email_id'] . "%'";
                }
                if ($_REQUEST['src_user_first_name'] != '') {

                    $alterCondition   .= " AND registration_list.user_full_name LIKE '%" . $_REQUEST['src_user_first_name'] . "%'";
                }

                $counter = 0;
                $indexVal = 1;

                $sqlDetails  = array();
                $sqlDetails['QUERY'] = "SELECT registration_list.* ,country_list.country_name,state_list.state_name 
                                                    FROM " . _DB_USER_REGISTRATION_ . " registration_list
                                        LEFT OUTER JOIN " . _DB_COMN_COUNTRY_ . " country_list
                                                    ON registration_list.user_country_id = country_list . country_id
                                        LEFT OUTER JOIN " . _DB_COMN_STATE_ . " state_list
                                                    ON registration_list.user_state_id = state_list.st_id 
                                                WHERE registration_list.status = 'A'
                                                AND registration_list.user_type = 'DELEGATE' 
                                                AND registration_list.registration_request != 'EXHIBITOR'
                                                AND registration_list.registration_payment_status != 'UNPAID' 
                                                        " . $alterCondition . "
                                                        ORDER BY registration_list.id DESC";
                $resDetails = $mycms->sql_select($sqlDetails);


                if ($resDetails) {
                    foreach ($resDetails as $i => $rowFetchUser) {
                        $status = true;
                        $counter++;
                        $color = "#FFFFFF";

                        if ($rowFetchUser['account_status'] == "UNREGISTERED") {
                            $color  = "#FFFFCA";
                            $status = false;
                        }

                        $totalAccompanyCount = 0;

                ?>
            <div class="spot_box">
                <div class="spot_box_top">
                    <div class="spot_name d-flex align-items-start">
                        <div class="regi_img_circle">
                            <!-- <img src="" alt="" class="w-100 h-100"> -->
                            <span>AM</span>
                        </div>
                        <div>
                            <div class="regi_name"><?= strtoupper($rowFetchUser['user_title']) ?>
											<?= strtoupper($rowFetchUser['user_first_name']) ?>
											<?= strtoupper($rowFetchUser['user_middle_name']) ?>
											<?= strtoupper($rowFetchUser['user_last_name']) ?></div>
                                <?php
											if ($rowFetchUser['isRegistration'] == "Y") {
                                                $userType = getCutoffName($rowFetchUser['registration_tariff_cutoff_id']);
                                    ?>
                            <div class="regi_type">
                                <span class="badge_padding badge_primary"><?= getRegClsfName($rowFetchUser['registration_classification_id']) ?></span>
                                <span class="badge_padding badge_secondary"><?= $userType?></span>
                            </div>
                            <? } ?>
                            <div class="regi_contact">
                                <span>
                                    <?php call(); ?><?= $rowFetchUser['user_mobile_isd_code'] . $rowFetchUser['user_mobile_no'] ?>
                                </span>
                                <span>
                                    <?php email(); ?><?= $rowFetchUser['user_email_id'] ?>
                                </span>
                            </div>
                        </div>
                    </div>
                    <div class="spot_details">
                        <div class="spot_details_box">
                            <h5>Reg Number</h5>
                            	<?php
                                if (
                                    $rowFetchUser['registration_payment_status'] == "PAID"
                                    || $rowFetchUser['registration_payment_status'] == "COMPLIMENTARY"
                                    || $rowFetchUser['registration_payment_status'] == "ZERO_VALUE"
                                ) {
                                    ?>
                            <p><?=$rowFetchUser['user_registration_id']?></p>
                            <!-- <small>NATCON 2025-000334</small> -->

                            <? } ?>
                        </div>
                      
                        <div class="spot_details_box">
                            <h5>Sequence ID</h5>
                              <?
                        if (
                            $rowFetchUser['registration_payment_status'] == "PAID"
                            || $rowFetchUser['registration_payment_status'] == "COMPLIMENTARY"
                            || $rowFetchUser['registration_payment_status'] == "ZERO_VALUE"
                        ) {
                            ?>
                            <h6><?php qr(); ?><b><?=$rowFetchUser['user_unique_sequence']?></b></h6>
                             <? } ?>
                        </div>
                       
                        <div class="spot_details_box">
                            <h5>Date</h5>
                            <h6><?php calendar(); ?><?= date('d/m/Y h:i A', strtotime($rowFetchUser['created_dateTime'])) ?></h6>
                        </div>
                    </div>
                </div>
                <div class="spot_box_bottom justify-content-end">
                    <div class="spot_box_bottom_right">
                        <a  href="javascript:void(null)" class="popup-btn badge_default icon_hover action-transparent addAbstractBtn" data-tab="submitabstract" data-delegate_id="<?= $rowFetchUser['id'] ?>"><?php abstracts(); ?>Submit Abstract</a>
                    </div>
                </div>
               
            </div>
           <? }
            }
           else{
            ?>
            <span class="mandatory" align="center">No Record Present.</span>
            <?
            }
            ?>
        </div>
    </div>
    <?php include_once("includes/popup.php"); ?>
</body>
<?php include_once("includes/js-source.php"); ?>
<script>
    /////////////Abstract submit  start////////////
    $(document).on('click', '.addAbstractBtn', function () {

        let abstractSubmitId = $(this).data('delegate_id');

        $.ajax({
            url: 'includes/popup.php',
            type: 'POST',
            data: { abstractSubmitId: abstractSubmitId },
            success: function (response) {

                $('#submitabstract').html($(response).find('#submitabstract').html());

                // Re-initialize after DOM replacement
                document.body.dataset.accmInit = "0"; // allow re-binding
                initabstractSubmit();
            
            },
            error: function(xhr) {
                console.error('AJAX error', xhr.responseText);
            }
        });
    });
        /////////////Abstract submit  end////////////

</script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('abstractSearchInput');
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
</html>
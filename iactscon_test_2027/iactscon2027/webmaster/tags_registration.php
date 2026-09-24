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

$indexVal          = 1;
$pageKey           = "_pgn" . $indexVal . "_";

$pageKeyVal        = ($_REQUEST[$pageKey] == "") ? 0 : $_REQUEST[$pageKey];

@$searchString     = "";
$searchArray       = array();

$searchArray[$pageKey]                     = $pageKeyVal;
$searchArray['src_email_id']               = addslashes(trim($_REQUEST['src_email_id']));
$searchArray['src_access_key']             = addslashes(trim($_REQUEST['src_access_key'], '#'));
$searchArray['src_mobile_no']              = addslashes(trim($_REQUEST['src_mobile_no']));
$searchArray['src_user_full_name']         = addslashes(trim($_REQUEST['src_user_full_name']));
$searchArray['src_registration_tag']       = addslashes(trim($_REQUEST['src_registration_tag']));
$searchArray['src_atom_transaction_ids']   = trim($_REQUEST['src_atom_transaction_ids']);
$searchArray['src_transaction_ids']        = trim($_REQUEST['src_transaction_ids']);
$searchArray['src_conf_reg_category']      = trim($_REQUEST['src_conf_reg_category']);
$searchArray['src_registration_id']        = trim($_REQUEST['src_registration_id']);

foreach ($searchArray as $searchKey => $searchVal) {
	if ($searchVal != "") {
		$searchString .= "&" . $searchKey . "=" . $searchVal;
	}
}
	$loggedUserId		= $mycms->getLoggedUserId();
?>

<body>
    <?php include_once("includes/left-menu.php"); ?>
    <header>
        <h2>Tag Registration</h2>
        <?php include_once("includes/header_right.php"); ?>
    </header>

    <div class="body_wrap">
        <div class="page_top_wrap mb-3">
            <div class="page_top_wrap_left">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="#">Tag Registration</a></li>
                        <li class="breadcrumb-item"><a href="#">Sc.Programme</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Manage Participant</li>
                    </ol>
                </nav>
                <h2>Tag Registration</h2>
                <h6>Manage participant & tag registration.</h6>
            </div>
            <div class="page_top_wrap_right">
                <?php
                    $sqlSystemUser				 =	array();
                    $sqlSystemUser['QUERY']      = "SELECT * 
                                                        FROM " . _DB_CONF_USER_ . " 
                                                        WHERE `a_id` = ?";
                    $sqlSystemUser['PARAM'][]   = array('FILD' => 'a_id',             'DATA' => $loggedUserId,   	'TYP' => 's');
                    $resultSystemUser   = $mycms->sql_select($sqlSystemUser);
                    $rowSystemUser      = $resultSystemUser[0];

                    $searchApplication  = 0;
                ?>
            </div>
        </div>

        <div class="regi_search_wrap mb-3">
           <!-- <div class="regi_search">
                <?php search(); ?>
                <input id="abstractSearchInput" placeholder="Search by Name, Email, Mobile, or Reg ID...">
            </div> -->
             <div class="regi_search">
                <input id="searchInput" name="q" placeholder="Search by Name, Email, Mobile, or Reg ID..."
                   >
            </div>
            <div class="regi_search_wrap_btn_box">
                <a href="javascript:void(null)" onclick="$('.filter_wrap').slideToggle(); $(this).toggleClass('active');"><?php filter(); ?>Filter</a>
            </div>
        </div>

        <div class="filter_wrap mb-3">
            <h4 class="filter_heading"><span>Advanced Filtering</span><a class="close_filter" onclick="$('.filter_wrap').slideUp();"><?php close(); ?></a></h4>
           <form name="frmSearch" method="post" action="tags_registration.process.php" onSubmit="return FormValidator.validate(this);">
			<input type="hidden" name="act" value="search_registration_tag" />
            <div class="filter_body">
                <div>
                    <label>User Full Name :</label>
                    <input type="text" name="src_user_full_name" id="src_user_full_name"value="<?= $_REQUEST['src_user_full_name'] ?>" />
                </div>

                <div>
                    <label>Unique Sequence:</label>
                    <input type="text" name="src_access_key" id="src_access_key"  value="<?= $_REQUEST['src_access_key'] ?>" />
                </div>
                <div>
                    <label>Email Id :</label>
                     <input type="text" name="src_email_id" id="src_email_id"
										 value="<?= $_REQUEST['src_email_id'] ?>" />                
                </div>
                <div>
                    <label>Mobile No :</label>
                   <input type="text" name="src_mobile_no" id="src_mobile_no"
										text-transform:uppercase;" value="<?= $_REQUEST['src_mobile_no'] ?>" />               
               </div>
                
            </div>
            <div class="filter_bottom">
                <button type="button" name="" value="Clear"  onClick="window.location.href='tags_registration.php?src_access_key=<?= str_replace('#', '', $_REQUEST['src_access_key']) ?>'"><?php reseti(); ?></button>
                <button type="submit" name="goSearch" value="Search">Apply</button>
            </div>
          </form>
        </div>

        <div class="spot_listing" id="spotListing">
            <?
            @$searchCondition       = "";
            $searchCondition       .= " AND delegate.operational_area != 'EXHIBITOR'
                                            AND delegate.registration_request != 'GUEST'
                                            AND delegate.account_status='REGISTERED'
                                            AND delegate.status = 'A'
                                            AND delegate.isRegistration = 'Y'";

            if ($_REQUEST['src_email_id'] != '') {
                $searchCondition   .= " AND delegate.user_email_id LIKE '%" . $_REQUEST['src_email_id'] . "%'";
            }
            if ($_REQUEST['src_access_key'] != '') {
                $searchCondition   .= " AND delegate.user_unique_sequence LIKE '%" . $_REQUEST['src_access_key'] . "%'";
            }
            if ($_REQUEST['src_mobile_no'] != '') {
                $searchCondition   .= " AND delegate.user_mobile_no LIKE '%" . $_REQUEST['src_mobile_no'] . "%'";
            }
            if ($_REQUEST['src_user_full_name'] != '') {
                $searchCondition   .= " AND delegate.user_full_name LIKE '%" . $_REQUEST['src_user_full_name'] . "%'";
            }
            if ($_REQUEST['src_registration_tag'] != '') {
                $searchCondition   .= " AND delegate.tags LIKE '" . $_REQUEST['src_registration_tag'] . "'";
            }
            if ($_REQUEST['src_transaction_ids'] != '') {
                $searchApplication	= 1;
                $searchCondition   .= " AND LOCATE('" . $_REQUEST['src_transaction_ids'] . "', totalInvoicePayment.atomTransactionIds) > 0";
            }
            if ($_REQUEST['src_atom_transaction_ids'] != '') {
                $searchApplication	= 1;
                $searchCondition   .= " AND LOCATE('" . $_REQUEST['src_atom_transaction_ids'] . "', totalInvoicePayment.atomAtomTransactionIds) > 0";
            }
            if ($_REQUEST['src_conf_reg_category'] != '') {
                $searchCondition   .= " AND delegate.registration_classification_id = '" . $_REQUEST['src_conf_reg_category'] . "'";
            }
            if ($_REQUEST['src_registration_id'] != '') {
                $searchCondition   .= " AND (delegate.user_registration_id LIKE '%" . $_REQUEST['src_registration_id'] . "%' 
                                                    AND (delegate.registration_payment_status = 'ZERO_VALUE' 
                                                        OR delegate.registration_payment_status = 'COMPLIMENTARY'
                                                        OR delegate.registration_payment_status = 'PAID'))";
            }
            $sqlFetchUser         = "";

            $idArr = getAllDelegates("", "", $searchCondition);
            if ($idArr) {
                foreach ($idArr as $i => $id) {
                    $status = true;
                    $rowFetchUser = getUserDetails($id);
                    $counter             = $counter + 1;
                    // echo "<pre>";
                    // print_r($rowFetchUser);
                    $totalAccompanyCount = 0;
                    $totalAccompanyCount = getTotalAccompanyCount($rowFetchUser['id']);
                    //if($rowFetchUser['classification_title']=='Delegates'){
                    $sqlClassification    = array();
                    $sqlClassification['QUERY']    = "SELECT * 
                                                                FROM " . _DB_REGISTRATION_CLASSIFICATION_ . " 
                                                                WHERE `status` = ? AND `type` = ? AND `id` = ?";

                    $sqlClassification['PARAM'][]   = array('FILD' => 'status',  'DATA' => 'A',  'TYP' => 's');
                    $sqlClassification['PARAM'][]   = array('FILD' => 'type',  'DATA' => 'DELEGATE',  'TYP' => 's');
                    $sqlClassification['PARAM'][]   = array('FILD' => 'id',  'DATA' => $rowFetchUser['registration_classification_id'],  'TYP' => 's');

                    $resultClassificationTitle = $mycms->sql_select($sqlClassification);
                     
            ?>
            <div class="spot_box">
                <div class="spot_box_top">
                    <div class="spot_name d-flex align-items-start">
                        <div class="regi_img_circle">
                            <!-- <img src="" alt="" class="w-100 h-100"> -->
                            <span><?= $counter + ($_REQUEST['_pgn1_'] * 10) ?></span>
                        </div>
                        <div>
                            <div class="regi_name"><?= strtoupper($rowFetchUser['user_title']) ?>
											<?= strtoupper($rowFetchUser['user_first_name']) ?>
											<?= strtoupper($rowFetchUser['user_middle_name']) ?>
											<?= strtoupper($rowFetchUser['user_last_name']) ?></div>
                            <?php
									if (
										$rowFetchUser['registration_payment_status'] == "PAID"
										|| $rowFetchUser['registration_payment_status'] == "COMPLIMENTARY"
										|| $rowFetchUser['registration_payment_status'] == "ZERO_VALUE"
									) {
                                        ?>
                            <div class="regi_type">
                               
                                <span class="badge_padding badge_primary"><?= $resultClassificationTitle[0]['classification_title'] ?></span>
                                <?php if (getCutoffName($rowFetchUser['registration_tariff_cutoff_id'])) { ?>
                                    <span class="badge_padding badge_secondary">
                                        <?php
                                        echo '<span style="color: #8ee0f5; text-transform: uppercase;" title="Cutoff">' . getCutoffName($rowFetchUser['registration_tariff_cutoff_id']) . '</span>';

                                        ?></span>
                                <?php } ?>
                            </div>
                            <? } else{
                                ?>
                                  <div class="regi_type">
                                     <span class="badge_padding badge_primary" style="color: #FF0000;!important" >Not Registered Yet</span>
                                 </div>
                                <?
                            }
                                ?>
                            <div class="regi_contact">
                                 <?php
									if (
										$rowFetchUser['registration_payment_status'] == "PAID"
										|| $rowFetchUser['registration_payment_status'] == "COMPLIMENTARY"
										|| $rowFetchUser['registration_payment_status'] == "ZERO_VALUE"
									) {
                                        ?>
                                    <span>Reg. Id: <?= $rowFetchUser['user_registration_id'] ?></span>
                                <? } ?>

                                <span>
                                    <?php call(); ?><?= $rowFetchUser['user_mobile_isd_code'] . $rowFetchUser['user_mobile_no'] ?>
                                </span>
                                <span>
                                    <?php email(); ?><?= $rowFetchUser['user_email_id'] ?>
                                </span>
                                <span><?php qr() ?><?=strtoupper($rowFetchUser['user_unique_sequence'])?></span>
                               
                             
                            </div>
                        </div>
                    </div>
                    <div class="spot_details">
                         <div class="spot_details_box">
                             <h5>User Address Details</h5>
                               <div class="regi_contact">
                                <span> Address: <?= $rowFetchUser['user_address'] ?></span>
                                    <span> Country: <?= $rowFetchUser['country_name'] ?></span>
                                    <span> State: <?= $rowFetchUser['state_name'] ?></span>
                                    <span> City: <?= $rowFetchUser['user_city'] ?></span>
                                    <span> Postal Code: <?= $rowFetchUser['user_pincode'] ?></span>
                                </div>
                        </div>
                        <div class="spot_details_box">
                            <h5>Tag Details</h5>
                            <?
                            $array = $rowFetchUser['tags'];
                            //print_r($array);
                            $var = (explode(",", $array));
                            foreach ($var as $key => $val) {
                                $sqlTagListing			  = array();
                                $sqlTagListing['QUERY']  = "SELECT * FROM " . _DB_TAG_MASTER_ . " WHERE `tag_name` = '" . $val . "'";
                                $resultTagListing   	  = $mycms->sql_select($sqlTagListing);
                                $rowTag 		  = $resultTagListing[0];
                                // if ($val == 'IAA Member') {
                            ?>
                            <h6><b><?= $val ?></b></h6>
                            <? } ?>
                        </div>
                        
                        <div class="spot_details_box">
                            <h5>Date</h5>
                            <h6><?php calendar(); ?><?= date('d/m/Y h:i A', strtotime($rowFetchUser['created_dateTime'])) ?></h6>
                        </div>
                    </div>
                </div>
                <div class="spot_box_bottom accm_bottom">
                    <div class="spot_box_bottom_left abstract_result">
                   
                       
                     
                   
                    </div>
                    
                    <div class="spot_box_bottom_right">
            
                        <a href="#" class="drp icon_hover badge_dark action-transparent">Tag<?php down() ?></a>
                    </div>
                </div>
                <div class="accm_tariff spot_service_break">
                    <div class="service_breakdown_wrap mt-0">
                        <h4><?php duser(); ?>Tag Details</h4>
                        <ul class="service_breakdown_wrap_ul">
                           <?php
						
							$tagArray = explode(",", $rowFetchUser['tags']);

							$sqlTagListing			 = array();
							$sqlTagListing = array();
							$sqlTagListing['QUERY'] = "SELECT * FROM " . _DB_TAG_MASTER_ . " WHERE `status` = ?";

							$sqlTagListing['PARAM'][]  = array('FILD' => 'status',  'DATA' => 'A',  'TYP' => 's');

							$resultTagListing   	 = $mycms->sql_select($sqlTagListing);
							$Counter			 	 = 0;
							if ($resultTagListing) {
                                	foreach ($resultTagListing as $keyTagListing => $rowTagListing) {
											$Counter++;
							?>
                            <li>
                                <n>
                                    <j><?php
											echo $rowTagListing['tag_name'];
											?></j>
                                </n>
                                <g>
                                    <label class="toggleswitch ">
                                        <input class="toggleswitch-checkbox reviewer-toggle" type="checkbox"  data-user-id="<?= $rowFetchUser['id'] ?>"  <?= (in_array($rowTagListing['tag_name'], $tagArray)) ? 'checked="checked"' : '' ?> value="<?= $rowTagListing['tag_name'] ?>">
                                        <div class="toggleswitch-switch"></div>
                                    </label>
                                </g>
                            </li>
                           <? } } ?>
                        </ul>
                    </div>
                </div>
            </div>
           <? } }
           else{
            ?>
            <span class="mandatory" align="center">No Record Present.</span>
            <?php
            }
            ?>
        </div>
        <div class="bbp-pagination">
            <div class="bbp-pagination-count"><?= $mycms->paginateRecInfo(1) ?></div>
            <span class="paginationDisplay">
                <div class="pagination"><a><?= $mycms->paginate(1, 'pagination') ?></a></div>
            </span>
        </div>
    </div>
    <?php include_once("includes/popup.php"); ?>
</body>
<?php include_once("includes/js-source.php"); ?>
<script>
   
   $(document).on('change', '.reviewer-toggle', function () {

    let $this = $(this);
    let tagname = $this.val();
    let userId = $this.data('user-id');
    let status = $this.is(':checked') ? 1 : 0;

    // Ask user before submitting
    let confirmMsg = status
        ? "Are you sure you want to tag?"
        : "Are you sure you want to remove this tag?";

    if (!confirm(confirmMsg)) {
        // User clicked Cancel → revert toggle
        $this.prop('checked', !status);
        return; // stop AJAX
    }

    // 🔒 prevent spam clicking
    $this.prop('disabled', true);

    // Proceed with AJAX only if user confirms
    $.ajax({
        type: "POST",
        url: "tags_registration.process.php",
        data: {
            act: 'tag_add',
            user_id: userId,
            tag: tagname,
            status: status
        },
        success: function (res) {
            alert("updated successfully!");
        },
        error: function () {
            alert("Error updating");
            // revert toggle on error
            $this.prop('checked', !status);
        },
        complete: function () {
            $this.prop('disabled', false);
        }
    });


});

</script>
<script>
document.getElementById("searchInput").addEventListener("keyup", function () {
    let filter = this.value.toLowerCase();
    let boxes = document.querySelectorAll("#spotListing .spot_box");

    boxes.forEach(function (box) {
        let text = box.innerText.toLowerCase();

        if (text.includes(filter)) {
            box.style.display = "";
        } else {
            box.style.display = "none";
        }
    });
});
</script>

</html>
<? 
function getCategoryName($id)
{
	global $cfg, $mycms;

	$sqlSelectUser				  			  = array();
	$sqlSelectUser['QUERY']         		  = "SELECT `category` 
												   FROM "._DB_ABSTRACT_TOPIC_CATEGORY_."
												 WHERE `id` = '".$id."' AND status='A'";
												 
	
									 
	$resultSelectUser          = $mycms->sql_select($sqlSelectUser);

	//echo '<pre>'; print_r($resultSelectUser);
	return $resultSelectUser[0]['category'];
}
?>
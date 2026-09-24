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

// $cfg['SECTION_BASE_URL'] = "https://ruedakolkata.com/natcon_25/conference_registration/webmaster/";
?>

<?php
$indexVal          = 1;
$pageKey           = "_pgn" . $indexVal . "_";

$pageKeyVal        = ($_REQUEST[$pageKey] == "") ? 0 : $_REQUEST[$pageKey];

@$searchString     = "";
$searchArray       = array();

$searchArray[$pageKey]                     = $pageKeyVal;



if (isset($_REQUEST['src_registration']) && trim($_REQUEST['src_registration']) != '') {
	$searchArray['src_registration']                   = addslashes(trim($_REQUEST['src_registration']));
}
if (isset($_REQUEST['src_email_id']) && trim($_REQUEST['src_email_id']) != '') {
	$searchArray['src_email_id']                       = addslashes(trim($_REQUEST['src_email_id']));
}
if (isset($_REQUEST['src_access_key']) && trim($_REQUEST['src_access_key']) != '') {
	$searchArray['src_access_key']                     = addslashes(trim($_REQUEST['src_access_key'], '#'));
}
if (isset($_REQUEST['src_mobile_no']) && trim($_REQUEST['src_mobile_no']) != '') {
	$searchArray['src_mobile_no']                      = addslashes(trim($_REQUEST['src_mobile_no']));
}
if (isset($_REQUEST['src_user_first_name']) && trim($_REQUEST['src_user_first_name']) != '') {
	$searchArray['src_user_first_name']                = addslashes(trim($_REQUEST['src_user_first_name']));
}
if (isset($_REQUEST['src_user_middle_name']) && trim($_REQUEST['src_user_middle_name']) != '') {
	$searchArray['src_user_middle_name']               = addslashes(trim($_REQUEST['src_user_middle_name']));
}
if (isset($_REQUEST['src_user_full_name']) && trim($_REQUEST['src_user_full_name']) != '') {
	$searchArray['src_user_full_name']                = addslashes(trim($_REQUEST['src_user_full_name']));
}
if (isset($_REQUEST['src_invoice_no']) && trim($_REQUEST['src_invoice_no']) != '') {
	$searchArray['src_invoice_no']                     = addslashes(trim($_REQUEST['src_invoice_no']));
}
if (isset($_REQUEST['src_slip_no']) && trim($_REQUEST['src_slip_no']) != '') {
	$searchArray['src_slip_no']                        = addslashes(trim($_REQUEST['src_slip_no'], '##'));
}
if (isset($_REQUEST['src_registration_mode']) && trim($_REQUEST['src_registration_mode']) != '') {
	$searchArray['src_registration_mode']              = addslashes(trim($_REQUEST['src_registration_mode']));
}
if (isset($_REQUEST['src_user_last_name']) && trim($_REQUEST['src_user_last_name']) != '') {
	$searchArray['src_user_last_name']                 = addslashes(trim($_REQUEST['src_user_last_name']));
}
if (isset($_REQUEST['src_atom_transaction_ids']) && trim($_REQUEST['src_atom_transaction_ids']) != '') {
	$searchArray['src_atom_transaction_ids']           = addslashes(trim($_REQUEST['src_atom_transaction_ids']));
}
if (isset($_REQUEST['src_transaction_ids']) && trim($_REQUEST['src_transaction_ids']) != '') {
	$searchArray['src_transaction_ids']                = addslashes(trim($_REQUEST['src_transaction_ids']));
}
if (isset($_REQUEST['src_conf_reg_category']) && trim($_REQUEST['src_conf_reg_category']) != '') {
	$searchArray['src_conf_reg_category']              = addslashes(trim($_REQUEST['src_conf_reg_category']));
}
if (isset($_REQUEST['src_reg_category']) && trim($_REQUEST['src_reg_category']) != '') {
	$searchArray['src_reg_category']        		   = addslashes(trim($_REQUEST['src_reg_category']));
}
if (isset($_REQUEST['src_registration_id']) && trim($_REQUEST['src_registration_id']) != '') {
	$searchArray['src_registration_id']                = addslashes(trim($_REQUEST['src_registration_id']));
}
if (isset($_REQUEST['src_workshop_classf']) && trim($_REQUEST['src_workshop_classf']) != '') {
	$searchArray['src_workshop_classf']                = addslashes(trim($_REQUEST['src_workshop_classf']));
}
if (isset($_REQUEST['src_transaction_id']) && trim($_REQUEST['src_transaction_id']) != '') {
	$searchArray['src_transaction_id']                 = addslashes(trim($_REQUEST['src_transaction_id']));
}
if (isset($_REQUEST['src_payment_mode']) && trim($_REQUEST['src_payment_mode']) != '') {
	$searchArray['src_payment_mode']                   = addslashes(trim($_REQUEST['src_payment_mode']));
}
if (isset($_REQUEST['src_payment_status']) && trim($_REQUEST['src_payment_status']) != '') {
	$searchArray['src_payment_status']                 = addslashes(trim($_REQUEST['src_payment_status']));
}
if (isset($_REQUEST['src_accommodation']) && trim($_REQUEST['src_accommodation']) != '') {
	$searchArray['src_accommodation']                  = addslashes(trim($_REQUEST['src_accommodation']));
}
if (isset($_REQUEST['src_mobile_isd_code']) && trim($_REQUEST['src_mobile_isd_code']) != '') {
	$searchArray['src_mobile_isd_code']                = addslashes(trim($_REQUEST['src_mobile_isd_code']));
}
if (isset($_REQUEST['src_registration_type']) && trim($_REQUEST['src_registration_type']) != '') {
	$searchArray['src_registration_type']              = addslashes(trim($_REQUEST['src_registration_type']));
}
if (isset($_REQUEST['src_payment_date']) && trim($_REQUEST['src_payment_date']) != '') {
	$searchArray['src_payment_date']                   = addslashes(trim($_REQUEST['src_payment_date']));
}
if (isset($_REQUEST['src_cancel_invoice_id']) && trim($_REQUEST['src_cancel_invoice_id']) != '') {
	$searchArray['src_cancel_invoice_id']              = addslashes(trim($_REQUEST['src_cancel_invoice_id']));
}
if (isset($_REQUEST['src_transaction_slip_no']) && trim($_REQUEST['src_transaction_slip_no']) != '') {
	$searchArray['src_transaction_slip_no']            = addslashes(trim($_REQUEST['src_transaction_slip_no']));
}
if (isset($_REQUEST['src_registration_from_date']) && trim($_REQUEST['src_registration_from_date']) != '') {
	$searchArray['src_registration_from_date']         = addslashes(trim($_REQUEST['src_registration_from_date']));
}
if (isset($_REQUEST['src_registration_to_date']) && trim($_REQUEST['src_registration_to_date']) != '') {
	$searchArray['src_registration_to_date']           = addslashes(trim($_REQUEST['src_registration_to_date']));
}

if (isset($_REQUEST['src_hasPickup']) && trim($_REQUEST['src_hasPickup']) != '') {
	$searchArray['src_hasPickup']           		   = addslashes(trim($_REQUEST['src_hasPickup']));
}
if (isset($_REQUEST['src_hasDropoff']) && trim($_REQUEST['src_hasDropoff']) != '') {
	$searchArray['src_hasDropoff']           		   = addslashes(trim($_REQUEST['src_hasDropoff']));
}
if (isset($_REQUEST['src_hasTotPlus']) && trim($_REQUEST['src_hasTotPlus']) != '') {
	$searchArray['src_hasTotPlus']           		   = addslashes(trim($_REQUEST['src_hasTotPlus']));
}
if (isset($_REQUEST['src_hasLapSutur']) && trim($_REQUEST['src_hasLapSutur']) != '') {
	$searchArray['src_hasLapSutur']           		   = addslashes(trim($_REQUEST['src_hasLapSutur']));
}
if (isset($_REQUEST['src_has3rd4th']) && trim($_REQUEST['src_has3rd4th']) != '') {
	$searchArray['src_has3rd4th']           		   = addslashes(trim($_REQUEST['src_has3rd4th']));
}
if (isset($_REQUEST['src_hasCerviCancer']) && trim($_REQUEST['src_hasCerviCancer']) != '') {
	$searchArray['src_hasCerviCancer']           	   = addslashes(trim($_REQUEST['src_hasCerviCancer']));
}
if (isset($_REQUEST['src_hasGalaDinner']) && trim($_REQUEST['src_hasGalaDinner']) != '') {
	$searchArray['src_hasGalaDinner']           	   = addslashes(trim($_REQUEST['src_hasGalaDinner']));
}
if (isset($_REQUEST['src_hasAccompany']) && trim($_REQUEST['src_hasAccompany']) != '') {
	$searchArray['src_hasAccompany']           		   = addslashes(trim($_REQUEST['src_hasAccompany']));
}
if (isset($_REQUEST['src_hasAbstract']) && trim($_REQUEST['src_hasAbstract']) != '') {
	$searchArray['src_hasAbstract']           		   = addslashes(trim($_REQUEST['src_hasAbstract']));
}
if (isset($_REQUEST['src_hasAccommodation']) && trim($_REQUEST['src_hasAccommodation']) != '') {
	$searchArray['src_hasAccommodation']           	   = addslashes(trim($_REQUEST['src_hasAccommodation']));
}
if (isset($_REQUEST['src_hasUnpaidInvoice']) && trim($_REQUEST['src_hasUnpaidInvoice']) != '') {
	$searchArray['src_hasUnpaidInvoice']           	   = addslashes(trim($_REQUEST['src_hasUnpaidInvoice']));
}

foreach ($searchArray as $searchKey => $searchVal) {
	if ($searchVal != "") {
		$searchString .= "&" . $searchKey . "=" . $searchVal;
	}
}

?>
<body>
    <?php include_once("includes/left-menu.php"); ?>
    <header>
        <h2>General Registration</h2>
        <?php include_once("includes/header_right.php"); ?>
    </header>

    <div class="body_wrap">
        <div class="page_top_wrap mb-3">
            <div class="page_top_wrap_left">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="#">Home</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Trash Registrations</li>
                    </ol>
                </nav>
                <h2>Participant Overview</h2>
                <h6>Manage registrations, track payments, and view participant details.</h6>
            </div>
        </div>
        <div class="regi_search_wrap mb-3">
            <!-- <div class="regi_search">
                <?php search(); ?>
                <input  id="searchInput"  name="src_global_search"  placeholder="Search by Name, Email, Mobile, or Reg ID...">
                <ul id="searchResults"></ul>
            </div> -->
            <div class="regi_search">
                <input id="searchInput" name="q" placeholder="Search by Name, Email, Mobile, Reg ID or Unique Sequence ID..."
                    value="<?= htmlspecialchars($_GET['q'] ?? '') ?>">
            </div>
            <div class="regi_search_wrap_btn_box">
                <a href="javascript:void(null)" onclick="$('.filter_wrap').slideToggle(); $(this).toggleClass('active');"><?php filter(); ?>Filter</a>
                <!-- <a href="registration.process.php?act=downloadUserListExcel"><?php export(); ?>Export</a>
                <a href="javascript:void(null)" class="popup-btn add" data-tab="newregistartion"><?php add(); ?>New Reg</a> -->
            </div>
        </div>

        <div class="filter_wrap mb-3">
            <h4 class="filter_heading"><span>Advanced Filtering</span><a class="close_filter" onclick="$('.filter_wrap').slideUp();"><?php close(); ?></a></h4>
            <form name="frmSearch" method="post" action="trash_registrations.php" onSubmit="return FormValidator.validate(this);">
			<input type="hidden" name="act" value="search_registration" />
            <div class="filter_body">
                <div class="filter_div">
                    <label>Registration Type</label>
                    <select  name="src_registration_type" id="src_registration_type">
                        <option value="">-- Select Registration type --</option>
                        <option value="GENERAL" <?= (trim($_REQUEST['src_registration_type'] == "GENERAL")) ? 'selected="selected"' : '' ?>>GENERAL</option>
                        <option value="SPOT" <?= (trim($_REQUEST['src_registration_type'] == "SPOT")) ? 'selected="selected"' : '' ?>>SPOT</option>
                    </select>
                </div>
                  <div class="filter_div">
                    <label>Conf. Reg. Category</label>
                	<select name="src_conf_reg_category" id="src_conf_reg_category" >
                        <option value="">-- Select Category --</option>
                        <?php
                        $sqlFetchClassification['QUERY']	 = "SELECT classf.`classification_title`, classf.`id`, classf.`currency`, classf.`type`,  masterHotel.hotel_name
                                                                        FROM " . _DB_REGISTRATION_CLASSIFICATION_ . " classf
                                                            LEFT OUTER JOIN " . _DB_MASTER_HOTEL_ . " masterHotel
                                                                        ON classf.residential_hotel_id = masterHotel.id
                                                                        WHERE classf.status = 'A'
                                                                    ORDER BY classf.type DESC, IFNULL(classf.residential_hotel_id,0) ASC, classf.sequence_by ASC";
                        $resultClassification	 = $mycms->sql_select($sqlFetchClassification);

                        $optgroupName = "";

                        if ($resultClassification) {
                        ?>
                            <optgroup label="Conference Registration">
                                <?
                                foreach ($resultClassification as $key => $rowClassification) {
                                    if ($optgroupName != $rowClassification['hotel_name']) {
                                        $optgroupName = $rowClassification['hotel_name'];
                                        echo "</optgroup><optgroup label='" . "Residential Package - " . $optgroupName . "'>";
                                    }

                                    if ($rowClassification['type'] == "DELEGATE") {
                                        $clasNm = $rowClassification['classification_title'];
                                    } elseif ($rowClassification['type'] == "COMBO") {
                                        $clasNm = str_replace("Residential Package - ", "", $rowClassification['classification_title']);
                                    } else {
                                        $clasNm = $rowClassification['classification_title'];
                                    }

                                    if ($rowClassification['type'] == "COMBO" && $rowClassification['hotel_name'] != '') {
                                        $classfId = $rowClassification['id'] . '-G';
                                    } else {
                                        $classfId = $rowClassification['id'];
                                    }
                                ?>
                                    <option value="<?= $classfId ?>" <?= ($classfId == trim($_REQUEST['src_conf_reg_category'])) ? 'selected="selected"' : '' ?>>
                                        <?= $clasNm ?>
                                    </option>
                                    <?php
                                    if ($rowClassification['type'] == "COMBO" && $rowClassification['hotel_name'] != '') {
                                        $classfId = $rowClassification['id'] . '-I';
                                    ?>
                                        <option value="<?= $classfId ?>" <?= ($classfId == trim($_REQUEST['src_conf_reg_category'])) ? 'selected="selected"' : '' ?>>
                                            <?= $clasNm . ' - Inaugural Offer' ?>
                                        </option>
                                <?php
                                    }
                                }
                                ?>
                            </optgroup>
                        <?php
                        }
                        ?>
                    </select>
                </div>

              
            </div>
            <div class="filter_bottom">
                <button onclick="clearFilters();" ><?php reseti(); ?></button>
                <button type="submit">Apply</button>
            </div>
            </form>
        </div>
        <div class="spot_listing ">
        <?php
            $searchCondition 		= "";
            if (isset($_REQUEST['src_conf_reg_category']) && $_REQUEST['src_conf_reg_category'] != '') {
                $exploded = explode("-", $_REQUEST['src_conf_reg_category']);
                if (sizeof($exploded) == 2) {
                    $_REQUEST['src_conf_reg_category'] = $exploded[0];

                    if ($exploded[1] == 'I') {
                        $searchCondition  .= " AND delegate_id IN ( SELECT id 
                                                                                FROM " . _DB_USER_REGISTRATION_ . " 
                                                                                WHERE status IN ('D')
                                                                                AND user_type = 'DELEGATE'
                                                                                AND operational_area NOT IN ('EXHIBITOR')
                                                                                AND (
                                                                                        isRegistration = 'Y'
                                                                                        OR registration_request = 'ONLYWORKSHOP'
                                                                                        )
                                                                                AND conf_reg_date <=  '2019-01-15 23:59:59' )";
                    } else {
                        $searchCondition  .= " AND delegate_id IN ( SELECT id 
                                                                                FROM " . _DB_USER_REGISTRATION_ . " 
                                                                                WHERE status IN ('D')
                                                                                AND user_type = 'DELEGATE'
                                                                                AND operational_area NOT IN ('EXHIBITOR')
                                                                                AND (
                                                                                        isRegistration = 'Y'
                                                                                        OR registration_request = 'ONLYWORKSHOP'
                                                                                        )
                                                                                AND conf_reg_date >  '2019-01-15 23:59:59' )";
                    }
                }
            }
            if ($_REQUEST['src_user_tags'] != '') {
                $searchCondition   .= " AND delegate_id IN ( SELECT id 
                                                                        FROM " . _DB_USER_REGISTRATION_ . " 
                                                                        WHERE status IN ('D')
                                                                        AND user_type = 'DELEGATE'
                                                                        AND operational_area NOT IN ('EXHIBITOR')
                                                                        AND (
                                                                                        isRegistration = 'Y'
                                                                                        OR registration_request = 'ONLYWORKSHOP'
                                                                                        )
                                                                        AND LOCATE('," . $_REQUEST['src_user_tags'] . ",', CONCAT(',',tags,',') ) > 0 )";
            }
            if ($_REQUEST['src_roles'] != '') {
                $searchCondition   .= " AND delegate_id IN ( SELECT id 
                                                                        FROM " . _DB_USER_REGISTRATION_ . " 
                                                                        WHERE status IN ('D')
                                                                        AND user_type = 'DELEGATE'
                                                                        AND operational_area NOT IN ('EXHIBITOR')
                                                                        AND (
                                                                                        isRegistration = 'Y'
                                                                                        OR registration_request = 'ONLYWORKSHOP'
                                                                                        )
                                                                        AND roles LIKE '%" . $_REQUEST['src_roles'] . "%')";
            }
            if ($_REQUEST['src_email_id'] != '') {
                $searchCondition   .= " AND delegate_id IN ( SELECT id 
                                                                        FROM " . _DB_USER_REGISTRATION_ . " 
                                                                        WHERE status IN ('D')
                                                                        AND user_type = 'DELEGATE'
                                                                        AND operational_area NOT IN ('EXHIBITOR')
                                                                        AND (
                                                                                        isRegistration = 'Y'
                                                                                        OR registration_request = 'ONLYWORKSHOP'
                                                                                        )
                                                                        AND user_email_id LIKE '%" . $_REQUEST['src_email_id'] . "%')";
            }
            if ($_REQUEST['src_access_key'] != '') {
                $searchCondition   .= " AND delegate_id IN ( SELECT id 
                                                                        FROM " . _DB_USER_REGISTRATION_ . " 
                                                                        WHERE status IN ('D')
                                                                        AND user_type = 'DELEGATE'
                                                                        AND operational_area NOT IN ('EXHIBITOR')
                                                                        AND (
                                                                                        isRegistration = 'Y'
                                                                                        OR registration_request = 'ONLYWORKSHOP'
                                                                                        )
                                                                        AND user_unique_sequence LIKE '%" . $_REQUEST['src_access_key'] . "%')";
            }
            if ($_REQUEST['src_mobile_no'] != '') {
                $searchCondition   .= " AND delegate_id IN ( SELECT id 
                                                                        FROM " . _DB_USER_REGISTRATION_ . " 
                                                                        WHERE status IN ('D')
                                                                        AND user_type = 'DELEGATE'
                                                                        AND operational_area NOT IN ('EXHIBITOR')
                                                                        AND (
                                                                                        isRegistration = 'Y'
                                                                                        OR registration_request = 'ONLYWORKSHOP'
                                                                                        )
                                                                        AND user_mobile_no LIKE '%" . $_REQUEST['src_mobile_no'] . "%')";
            }
            if ($_REQUEST['src_user_first_name'] != '') {
                $searchCondition   .= " AND delegate_id IN ( SELECT id 
                                                                        FROM " . _DB_USER_REGISTRATION_ . " 
                                                                        WHERE status IN ('D')
                                                                        AND user_type = 'DELEGATE'
                                                                        AND operational_area NOT IN ('EXHIBITOR')
                                                                        AND (
                                                                                        isRegistration = 'Y'
                                                                                        OR registration_request = 'ONLYWORKSHOP'
                                                                                        )
                                                                        AND (user_first_name  LIKE '%" . $_REQUEST['src_user_first_name'] . "%'
                                                                                OR user_middle_name LIKE '%" . $_REQUEST['src_user_first_name'] . "%'
                                                                                OR user_last_name   LIKE '%" . $_REQUEST['src_user_first_name'] . "%'
                                                                                OR user_full_name LIKE '%" . $_REQUEST['src_user_first_name'] . "%'))";
            }
            if ($_REQUEST['src_payment_mode'] != '') {
                $searchCondition   .= " AND slip_id IN ( SELECT DISTINCT slip_id 
                                                                    FROM " . _DB_PAYMENT_ . " 
                                                                    WHERE status IN ('A')
                                                                        AND payment_mode = '" . $_REQUEST['src_payment_mode'] . "')";
            }
            if ($_REQUEST['src_transaction_id'] != '') {
                $searchCondition   .= " AND ( slip_id IN ( SELECT DISTINCT slip_id 
                                                                        FROM " . _DB_PAYMENT_ . " 
                                                                        WHERE status IN ('A')
                                                                        AND ( atom_atom_transaction_id = '" . $_REQUEST['src_transaction_id'] . "'
                                                                                OR atom_merchant_transaction_id = '" . $_REQUEST['src_transaction_id'] . "'
                                                                                OR atom_bank_transaction_id = '" . $_REQUEST['src_transaction_id'] . "'))
                                                        OR 
                                                        slip_id IN ( SELECT DISTINCT slip_id 
                                                                        FROM " . _DB_PAYMENT_REQUEST_ . " 
                                                                        WHERE status IN ('A')
                                                                        AND transaction_id = '" . $_REQUEST['src_transaction_id'] . "'))";
            }
            if ($_REQUEST['src_registration_mode'] != '') {
                $searchCondition   .= " AND invoice_mode LIKE '%" . $_REQUEST['src_registration_mode'] . "%'";
            }
            if ($_REQUEST['src_user_last_name'] != '') {
                $searchCondition   .= " AND delegate_id IN ( SELECT id 
                                                                        FROM " . _DB_USER_REGISTRATION_ . " 
                                                                        WHERE status IN ('D')
                                                                        AND user_type = 'DELEGATE'
                                                                        AND operational_area NOT IN ('EXHIBITOR')
                                                                        AND (
                                                                                        isRegistration = 'Y'
                                                                                        OR registration_request = 'ONLYWORKSHOP'
                                                                                        )
                                                                        AND delegate.user_last_name LIKE '%" . $_REQUEST['src_user_last_name'] . "%')";
            }
            if ($_REQUEST['src_invoice_no'] != '') {
                $searchCondition   .= " AND invoice_number LIKE '%" . $_REQUEST['src_invoice_no'] . "%'";
            }
            if ($_REQUEST['src_slip_no'] != '') {
                $searchCondition   .= " AND slip_id IN ( SELECT id
                                                                    FROM " . _DB_SLIP_ . " 
                                                                    WHERE status IN ('D')
                                                                    AND slip_number LIKE '%" . $_REQUEST['src_slip_no'] . "%' )";
            }
            if ($_REQUEST['src_transaction_ids'] != '') {
                $searchCondition   .= " AND slip_id IN ( SELECT DISTINCT slip_id 
                                                                    FROM " . _DB_PAYMENT_ . " 
                                                                    WHERE status IN ('A')
                                                                    AND atom_atom_transaction_id LIKE '%" . $_REQUEST['src_transaction_ids'] . "%')";
            }
            if ($_REQUEST['src_transaction_slip_no'] != '') {
                $searchCondition   .= " AND slip_id IN ( SELECT DISTINCT slip_id 
                                                                    FROM " . _DB_PAYMENT_ . " 
                                                                    WHERE status IN ('A')
                                                                    AND (card_transaction_no LIKE '%" . $_REQUEST['src_transaction_slip_no'] . "%'
                                                                            OR rrn_number LIKE '%" . $_REQUEST['src_transaction_slip_no'] . "%'
                                                                            OR cheque_number LIKE '%" . $_REQUEST['src_transaction_slip_no'] . "%'
                                                                            OR draft_number LIKE '%" . $_REQUEST['src_transaction_slip_no'] . "%'
                                                                            OR neft_transaction_no LIKE '%" . $_REQUEST['src_transaction_slip_no'] . "%'
                                                                            OR rtgs_transaction_no LIKE '%" . $_REQUEST['src_transaction_slip_no'] . "%'
                                                                            OR atom_transaction_card_no LIKE '%" . $_REQUEST['src_transaction_slip_no'] . "%'
                                                                            OR atom_bank_transaction_id LIKE '%" . $_REQUEST['src_transaction_slip_no'] . "%'
                                                                            OR atom_atom_transaction_id LIKE '%" . $_REQUEST['src_transaction_slip_no'] . "%'
                                                                            OR remarks LIKE '%" . $_REQUEST['src_transaction_slip_no'] . "%' ))";
            }
            if ($_REQUEST['src_conf_reg_category'] != '') {
                $searchCondition   .= " AND delegate_id IN ( SELECT id 
                                                                        FROM " . _DB_USER_REGISTRATION_ . " 
                                                                        WHERE status IN ('D')
                                                                        AND user_type = 'DELEGATE'
                                                                        AND operational_area NOT IN ('EXHIBITOR')
                                                                        AND (
                                                                                        isRegistration = 'Y'
                                                                                        OR registration_request = 'ONLYWORKSHOP'
                                                                                        )
                                                                        AND registration_classification_id = '" . $_REQUEST['src_conf_reg_category'] . "')";
            }
            if ($_REQUEST['src_reg_category'] != '') {
                if ($_REQUEST['src_reg_category'] == 'Conference') {
                    $searchCondition   .= " AND delegate_id IN ( SELECT id 
                                                                            FROM " . _DB_USER_REGISTRATION_ . " 
                                                                            WHERE status IN ('D')
                                                                                AND user_type = 'DELEGATE'
                                                                                AND operational_area NOT IN ('EXHIBITOR')
                                                                                AND (
                                                                                        isRegistration = 'Y'
                                                                                        OR registration_request = 'ONLYWORKSHOP'
                                                                                        )
                                                                                AND registration_classification_id IN (1,3,4,5,6))";
                } elseif ($_REQUEST['src_reg_category'] == 'Residential') {
                    $searchCondition   .= " AND delegate_id IN ( SELECT id 
                                                                            FROM " . _DB_USER_REGISTRATION_ . " 
                                                                            WHERE status IN ('D')
                                                                                AND user_type = 'DELEGATE'
                                                                                AND operational_area NOT IN ('EXHIBITOR')
                                                                                AND (
                                                                                        isRegistration = 'Y'
                                                                                        OR registration_request = 'ONLYWORKSHOP'
                                                                                        )
                                                                                AND registration_classification_id IN (7,8,9,10,11,12,13,14,15,16,17,18))";
                }
            }
            if ($_REQUEST['src_registration_type'] != '') {
                $searchCondition   .= " AND delegate_id IN ( SELECT id 
                                                                        FROM " . _DB_USER_REGISTRATION_ . " 
                                                                        WHERE status IN ('D')
                                                                        AND user_type = 'DELEGATE'
                                                                        AND operational_area NOT IN ('EXHIBITOR')
                                                                        AND (
                                                                                        isRegistration = 'Y'
                                                                                        OR registration_request = 'ONLYWORKSHOP'
                                                                                        )
                                                                        AND registration_request LIKE '%" . $_REQUEST['src_registration_type'] . "%')";
            }
            if ($_REQUEST['src_registration_id'] != '') {
                $searchCondition   .= " AND delegate_id IN (SELECT id 
                                                                        FROM " . _DB_USER_REGISTRATION_ . " 
                                                                        WHERE status IN ('D')
                                                                        AND user_type = 'DELEGATE'
                                                                        AND operational_area NOT IN ('EXHIBITOR')
                                                                        AND (
                                                                                        isRegistration = 'Y'
                                                                                        OR registration_request = 'ONLYWORKSHOP'
                                                                                        )
                                                                        AND user_registration_id LIKE '%" . $_REQUEST['src_registration_id'] . "' 
                                                                        AND registration_payment_status IN ('ZERO_VALUE', 'COMPLEMENTARY', 'PAID'))";
            }
            if ($_REQUEST['src_payment_status_old'] != '') {
                $searchCondition   .= " AND delegate_id IN (SELECT id 
                                                                        FROM " . _DB_USER_REGISTRATION_ . " 
                                                                        WHERE status IN ('D')
                                                                        AND user_type = 'DELEGATE'
                                                                        AND operational_area NOT IN ('EXHIBITOR')
                                                                        AND (
                                                                                        isRegistration = 'Y'
                                                                                        OR registration_request = 'ONLYWORKSHOP'
                                                                                        )
                                                                        AND user_registration_id LIKE '%" . $_REQUEST['src_registration_id'] . "' 
                                                                        AND registration_payment_status = '" . $_REQUEST['src_payment_status'] . "')";
            }
            if ($_REQUEST['src_workshop_classf'] != '') {
                $id =  trim($_REQUEST['src_workshop_classf']);
                $workshop_id = substr($id, 0, 1);
                $payment_status = substr($id, 1);
                if ($payment_status == "P") {
                    $status = "PAID";
                } else if ($payment_status == "U") {
                    $status = "UNPAID";
                } else if ($payment_status == "C") {
                    $status = "COMPLEMENTARY";
                } else {
                    $status = "ALL";
                }

                if ($status != "ALL") {
                    $searchCondition   .= " AND id IN (   SELECT refference_invoice_id 
                                                                        FROM " . _DB_REQUEST_WORKSHOP_ . " 
                                                                        WHERE status IN ('A')
                                                                        AND workshop_id = '" . $workshop_id . "' 
                                                                        AND status = 'A' 
                                                                        AND payment_status = '" . $status . "')";
                } else {
                    $searchCondition   .= " AND id IN (SELECT refference_invoice_id 
                                                                    FROM " . _DB_REQUEST_WORKSHOP_ . " 
                                                                    WHERE status IN ('A')
                                                                    AND workshop_id = '" . $workshop_id . "' 
                                                                    AND status = 'A' )";
                }
            }
            if ($_REQUEST['src_accommodation'] != '') {
                $searchCondition   .= " AND service_type = 'DELEGATE_ACCOMMODATION_REQUEST' 
                                                    AND payment_status = '" . $_REQUEST['src_accommodation'] . "' 
                                                    AND status = 'A' ";
            }
            if ($_REQUEST['src_country_name'] != "") {
                $searchCondition .= " AND delegate_id IN (  SELECT id 
                                                                        FROM " . _DB_USER_REGISTRATION_ . " 
                                                                        WHERE status IN ('D')
                                                                        AND user_type = 'DELEGATE'
                                                                        AND operational_area NOT IN ('EXHIBITOR')
                                                                        AND (
                                                                                        isRegistration = 'Y'
                                                                                        OR registration_request = 'ONLYWORKSHOP'
                                                                                        )
                                                                        AND user_country_id = '" . $_REQUEST['src_country_name'] . "%')";
            }
            if ($_REQUEST['src_state_name'] != "") {
                $searchCondition .= " AND delegate_id IN (  SELECT id 
                                                                        FROM " . _DB_USER_REGISTRATION_ . " 
                                                                        WHERE status IN ('D')
                                                                        AND user_type = 'DELEGATE'
                                                                        AND operational_area NOT IN ('EXHIBITOR')
                                                                        AND (
                                                                                        isRegistration = 'Y'
                                                                                        OR registration_request = 'ONLYWORKSHOP'
                                                                                        )
                                                                        AND user_state_id = '" . $_REQUEST['src_state_name'] . "')";
            }
            if ($_REQUEST['src_payment_date'] != '') {
                $searchCondition   .= "  AND slip_id IN ( SELECT DISTINCT slip_id 
                                                                    FROM " . _DB_PAYMENT_ . " 
                                                                    WHERE status IN ('A')
                                                                        AND payment_date = '" . $_REQUEST['src_payment_date'] . "')";
            }
            if ($_REQUEST['src_registration_from_date'] != '') {
                $searchCondition   .= " AND delegate_id IN (  SELECT id 
                                                                        FROM " . _DB_USER_REGISTRATION_ . " 
                                                                        WHERE status IN ('D')
                                                                        AND user_type = 'DELEGATE'
                                                                        AND operational_area NOT IN ('EXHIBITOR')
                                                                        AND (
                                                                                        isRegistration = 'Y'
                                                                                        OR registration_request = 'ONLYWORKSHOP'
                                                                                        )
                                                                        AND conf_reg_date BETWEEN  '" . $_REQUEST['src_registration_from_date'] . " 00:00:00'
                                                                        AND '" . $_REQUEST['src_registration_to_date'] . " 23:59:59')";
            }
            if ($_REQUEST['src_cancel_invoice_id'] != '') {
                $searchCondition   .= " AND invoice_number LIKE '%" . $_REQUEST['src_cancel_invoice_id'] . "%' 
                                                    AND status = 'C'";
            }
            if ($_REQUEST['src_hasPickup'] != '') {
                $searchCondition   .= " AND delegate_id IN (SELECT user_id FROM " . _DB_REQUEST_PICKUP_DROPOFF_ . " WHERE pikup_time IS NOT NULL)";
            }
            if ($_REQUEST['src_hasDropoff'] != '') {
                $searchCondition   .= " AND delegate_id IN (SELECT user_id FROM " . _DB_REQUEST_PICKUP_DROPOFF_ . " WHERE dropoff_time IS NOT NULL)";
            }
            if ($_REQUEST['src_hasNotes'] != '') {
                $searchCondition   .= " AND delegate_id IN (   SELECT id 
                                                                            FROM " . _DB_USER_REGISTRATION_ . " 
                                                                            WHERE status IN ('D')
                                                                            AND user_type = 'DELEGATE'
                                                                            AND operational_area NOT IN ('EXHIBITOR')
                                                                            AND (
                                                                                        isRegistration = 'Y'
                                                                                        OR registration_request = 'ONLYWORKSHOP'
                                                                                        )
                                                                            AND TRIM(user_food_preference_in_details) != '')";
            }
            if ($_REQUEST['src_hasPayentTerSetButNotPaid'] != '') {
                $searchCondition   .= " AND slip_id IN (SELECT slip_id	 FROM " . _DB_PAYMENT_ . " payment	WHERE status = 'A' AND payment_status = 'UNPAID')";
            }
            if ($_REQUEST['src_hasUnpaidInvoice'] != '') {
                $searchCondition   .= " AND id IN (SELECT id FROM " . _DB_INVOICE_ . " invoice	WHERE status = 'D' AND payment_status = 'UNPAID')";
            }
            if ($_REQUEST['src_payment_status'] == 'UNPAID') {
                $searchCondition   .= " AND id IN (SELECT id FROM " . _DB_INVOICE_ . " invoice	WHERE status = 'D' AND payment_status = '" . $_REQUEST['src_payment_status'] . "')";

                
            }else if($_REQUEST['src_payment_status'] != ''){
                $searchCondition   .= " AND slip_id IN ( SELECT DISTINCT slip_id 
                                                                    FROM " . _DB_PAYMENT_ . " 
                                                                    WHERE status IN ('A')
                                                                        AND payment_status = '" . $_REQUEST['src_payment_status'] . "')";		
            }
        
                // // Bind :search parameter
                // $searchCondition = ""; // default empty


        if (!empty($_GET['q'])) {
            $q = urldecode(trim($_GET['q']));
            $words = explode(' ', $q);

            $subConditions = [];
            foreach ($words as $word) {
                $word = addslashes($word);

                // Require the word to appear in any of these columns
                $subConditions[] = "(user_full_name LIKE '%$word%' 
                                    OR user_email_id LIKE '%$word%' 
                                    OR user_mobile_no LIKE '%$word%' 
                                    OR user_registration_id LIKE '%$word%'
                                    OR user_unique_sequence LIKE '%$word%')
                                     ";

                
            }

            // Combine with AND between words — each word must appear somewhere
            $searchCondition .= " AND delegate_id IN (
                SELECT id
                FROM " . _DB_USER_REGISTRATION_ . "
                WHERE status IN ('D')
                AND user_type = 'DELEGATE'
                AND operational_area NOT IN ('EXHIBITOR','GUEST')
                AND (isRegistration='Y' OR registration_request='ONLYWORKSHOP')
                AND " . implode(' AND ', $subConditions) . "
            )";
        }
                //echo $searchCondition;

                $sqlDelegateQueryset 			   = array();
                $sqlDelegateQueryset['QUERY']      = "SELECT DISTINCT delegate_id AS delegate_id									 
                                                                FROM " . _DB_INVOICE_ . "
                                                            WHERE status IN ('D') 
                                                                AND delegate_id IN ( SELECT id 
                                                                                        FROM " . _DB_USER_REGISTRATION_ . " 
                                                                                    WHERE status IN ('D')
                                                                                        AND user_type = 'DELEGATE'
                                                                                        AND operational_area NOT IN ('EXHIBITOR','GUEST')
                                                                                        AND (
                                                                                            isRegistration = 'Y'
                                                                                            OR registration_request = 'ONLYWORKSHOP'
                                                                                            
                                                                                            )
                                                                                            
                                                                                        )
                                                            " . $searchCondition . "  ORDER BY delegate_id DESC";

                //$sqlFetchUser         	= "";								
                //$idArr 					= getAllDelegates("","",$alterCondition,'','R001',true);

                $resultFetchUser     	  = $mycms->sql_select_paginated('R001', $sqlDelegateQueryset, 10);

            // echo '<pre>'; print_r($sqlDelegateQueryset);
            $perPage = 10; // IMPORTANT: must match SQL LIMIT

            $pageIndex = isset($_GET['_pgnR001_'])
                ? (int)$_GET['_pgnR001_']
                : 0;

            $offset = $pageIndex * $perPage;
            $counter =0;
            if ($resultFetchUser) //$idArr['IDS']
            {
                
                foreach ($resultFetchUser as $kkl => $idRow) //$idArr['IDS'] as $i=>$id
                {
                    $countertest = $offset + $kkl + 1;
                    $id = $idRow['delegate_id'];

                    $status = true;
                    $rowFetchUser = getDeletedUserDetails($id);
                    $counter      = $countertest;
                    $color = "#FFFFFF";
                    if ($rowFetchUser['account_status'] == "UNREGISTERED") {
                        $color = "#FFCCCC";
                        $status = false;
                    }
                    $totalAccompanyCount = 0;

                    if ($rowFetchUser['user_food_preference'] == 'VEG') {
                        $foodcolor = "#00CC00";
                    } else {
                        $foodcolor = "#FF0000";
                    }

                    $sqlListing	 = array();
                    $sqlListing['QUERY']		 = "SELECT COUNT(*) AS COUNTDATA
                                                    FROM " . _DB_SP_PARTICIPANT_DETAILS_ . " 
                                                    WHERE `participant_delegate_id` = '" . $id . "' AND status='A'";
                    $resultsListing	 = $mycms->sql_select($sqlListing);
                    $existingDetail	 = $resultsListing[0];

                    //print_r($existingDetail['COUNTDATA']);
            ?>
            <div class="spot_box">
                <div class="spot_box_top">
                    <div class="spot_name d-flex align-items-start">
                        <div class="regi_img_circle">
                            <!-- <img src="" alt="" class="w-100 h-100"> -->
                            <span><?= $counter?></span>
                        </div>
                        <div>
                            <div class="regi_name"><?= strtoupper($rowFetchUser['user_full_name']) ?></div>
                           
                            <div class="regi_contact">
                                <span>
                                    <?php call(); ?><?= $rowFetchUser['user_mobile_no'] ?>
                                </span>
                                <span>
                                    <?php email(); ?><?= $rowFetchUser['user_email_id'] ?>
                                </span>
                            </div>
                        </div>
                    </div>
                    <div class="spot_details">
                        <div class="spot_details_box">
                            <h5>Unique Sequence</h5>
                            <h6><?=strtoupper($rowFetchUser['user_unique_sequence'])?></h6>
                        </div>
                         <?php
                            if ($rowFetchUser['isRegistration'] == "Y") {
                                if (empty(getRegClsfName($rowFetchUser['registration_classification_id']))) {
                                    $regClsName = getRegClsfComboName($rowFetchUser['registration_classification_id']);
                                } else {
                                    $regClsName = getRegClsfName($rowFetchUser['registration_classification_id']);
                                }
                            }
                            ?>
                        <?php if ($rowFetchUser['membership_number'] != '' || $rowFetchUser['membership_number'] !=0) {
                            $membership_number = "-" . $rowFetchUser['membership_number'];
                        }else{
                            $membership_number = '';
                        }
                        ?>
                        <div class="spot_details_box">
                            <h5>Registration Type</h5>
                            <h6><?=$regClsName?></h6>
                            <h6><?=$membership_number?></h6>
                        </div>
                        <div class="spot_details_box">
                            <h5>Registration Details</h5>
                            <h6><?= $rowFetchUser['user_registration_id']?></h6>
                            <h6><?= date('d/m/Y h:i A', strtotime($rowFetchUser['created_dateTime'])) ?></h6>
                        </div>
                    </div>
                </div>
                <div class="spot_box_bottom accm_bottom justify-content-end">

                    <div class="spot_box_bottom_right">
                        <a class="icon_hover badge_secondary action-transparent" href="<?= $cfg['SECTION_BASE_URL'] ?>registration.process.php?act=Active&id=<?= $rowFetchUser['id'] ?>"  onclick="return confirm('Do you really want to re-Activate this record ?');"><?php reseti(); ?>Re-Active</a>
                        <a href="javascript:void(0);"  class="icon_hover badge_danger action-transparent delet" onclick="if (confirm('Do you really want to remove this user?')) { 
                                                                            window.location.href='<?= $cfg['SECTION_BASE_URL'] ?>registration.process.php?act=deleteTrash&id=<?= $rowFetchUser['id']; ?>&userType=<?=$rowFetchUser['userType'] ?>'; 
                                                                        }"><?php delete(); ?>Delete</a>
                    </div>
                </div>
                
            </div>
              <?php
                    }
                } else {
                    ?>
                  
                      <span class="mandatory">No Record Present.</span>
                       
                    <?php
                }
                ?>
            <div class="bbp-pagination">
              <div class="bbp-pagination-count"><?= $mycms->paginateRecInfo('R001') ?></div>
                <span class="paginationDisplay">
                    <div class="pagination"><a><?= $mycms->paginate('R001', 'pagination') ?></a></div>
                </span>
            </div>
        </div>

     
    </div>
    <?php include_once("includes/popup.php"); ?>
</body>
<?php include_once("includes/js-source.php"); ?>
<script>
function clearFilters() {
    // Get the form
    var form = document.forms['frmSearch'];

    // Clear selects
    form.src_registration_type.value = "";
    form.src_payment_status.value = "";
    form.src_conf_reg_category.value = "";
    form.src_user_tags.value = "";
    form.src_registration_mode.value = "";

    // Clear date input
    form.querySelector('input[type="date"]').value = "";

    // Optional: clear hidden act value (if you want a clean GET)
    // form.act.value = "";

    // Submit the form to PHP with empty values
    form.submit();
}
      /////////////////////view profile start/////////////////////////
        $(document).on('click', '.viewProfilebtn', function() {
        let userId   = $(this).data('user-id');
        $.ajax({
            url: 'includes/popup.php',
            type: 'POST',
            data: {
                userId: userId,               
            },
        success: function(response) {
        $('#profile').html($(response).find('#profile').html());
        // Then show the popup
        $('#profile').fadeIn();
            },
            error: function(xhr) {
                console.error('AJAX error', xhr.responseText);
            }
        });
        // Trigger the popup-btn functionality
    });
    $(document).on('click', '.editbtn', function() {
        let userId   = $(this).data('user-id');
        $.ajax({
            url: 'includes/popup.php',
            type: 'POST',
            data: {
                edituserId: userId,               
            },
        success: function(response) {
        $('#editregistartion').html($(response).find('#editregistartion').html());
        // Then show the popup
        initEditregistration();

        $('#editregistartion').fadeIn();
            },
            error: function(xhr) {
                console.error('AJAX error', xhr.responseText);
            }
        });
        // Trigger the popup-btn functionality
    });
    ///////////////////view profile edit///////////////////////////
</script>
<script>
// const searchInput = document.getElementById("searchInput");
// let typingTimer;
// const typingDelay = 300;

// searchInput.addEventListener("keypress", function(e) {
//     if (e.key === "Enter") {
//         const query = this.value.trim();
//         const url = new URL(window.location.href);

//         if(query.length > 0){
//             url.searchParams.set('q', query);
//         } else {
//             url.searchParams.delete('q');
//         }
//         url.searchParams.delete('_pgnR001_'); // reset pagination
//         window.location.href = url.toString();
//     }
// });

// searchInput.addEventListener("keydown", () => clearTimeout(typingTimer));

const searchInput = document.getElementById("searchInput");
let typingTimer;
const typingDelay = 800; // wait 0.8s after last keystroke

searchInput.addEventListener("keyup", function() {
    clearTimeout(typingTimer);

    typingTimer = setTimeout(() => {
        const query = this.value.trim();

        const url = new URL(window.location.href);

        if (query.length > 0) {
            url.searchParams.set('q', query);
        } else {
            url.searchParams.delete('q');
        }

        url.searchParams.delete('_pgnR001_'); // reset pagination
        window.location.href = url.toString(); // reload page with new query
    }, typingDelay);
});

searchInput.addEventListener("keydown", () => clearTimeout(typingTimer));
</script>
</html>
<?php

function getDeletedUserDetails($delegateId, $onlyActive = false)
{
	global $cfg, $mycms;

	$condition = "";
	if ($onlyActive) {
		$condition = " AND registration_list.status = 'D'";
	} else {
		$condition = " AND registration_list.status IN ('A','C','D')";
	}

	$sqlDetails	= array();
	$sqlDetails['QUERY'] = "SELECT registration_list.* ,country_list.country_name, state_list.state_name, spParticipant.id AS participantId, spParticipant.participation_type
							  FROM " . _DB_USER_REGISTRATION_ . " registration_list
				   LEFT OUTER JOIN " . _DB_COMN_COUNTRY_ . " country_list
								ON registration_list.user_country_id = country_list . country_id
				   LEFT OUTER JOIN " . _DB_COMN_STATE_ . " state_list
								ON registration_list.user_state_id = state_list.st_id 
				   LEFT OUTER JOIN " . _DB_SP_PARTICIPANT_DETAILS_ . " spParticipant
								ON spParticipant.participant_delegate_id = registration_list.id 
							 WHERE registration_list.id = ? 
								   " . $condition . "";

	$sqlDetails['PARAM'][]	=	array('FILD' => 'registration_list.id', 	  'DATA' => $delegateId,             'TYP' => 's');

	$resDetails          = $mycms->sql_select($sqlDetails);

	if ($resDetails) {
		return $resDetails[0];
	} else {
		return false;
	}
}
?>
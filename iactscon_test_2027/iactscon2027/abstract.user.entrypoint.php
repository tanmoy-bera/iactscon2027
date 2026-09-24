<?php
include_once("includes/frontend.init.php");
include_once("includes/function.registration.php");
include_once("includes/function.delegate.php");
include_once("includes/function.invoice.php");
include_once("includes/function.workshop.php");
include_once("includes/function.dinner.php");
include_once("includes/function.accompany.php");

?>
<!DOCTYPE html>
<html lang="en">

<?php
//setTemplateStyleSheet();
setTemplateBasicJS();
backButtonOffJS();
include_once('header.php');
?>

<?php
$loginDetails 	 = login_session_control();
$delegateId 	 = $loginDetails['DELEGATE_ID'];

$operate 		 = false;

if (isset($_REQUEST['TOKEN']) && trim($_REQUEST['TOKEN']) != '') {
	$token = unserialize(base64_decode($_REQUEST['TOKEN']));
	if (is_array($token) && sizeof($token) > 0) {
		foreach ($token as $key => $val) {
			$_REQUEST[$key] = $val;
		}
	}
}

if ($_SESSION['PROCEED_2_ABSTRACT'] == 'OK') {
	$_REQUEST['PROCEED'] = 'OK';
	$_REQUEST['EXPIRY'] = $_SESSION['PROCEED_EXPIRY'];
}


$sqlHeader 	=	array();
$sqlHeader['QUERY'] = "SELECT * FROM " . _DB_EMAIL_SETTING_ . " 
											WHERE `status`='A' order by id desc limit 1";
//$sql['PARAM'][]	=	array('FILD' => 'status' ,     		 'DATA' => 'A' ,       	           'TYP' => 's');					 
$resultHeader = $mycms->sql_select($sqlHeader);
$rowHeader    		 = $resultHeader[0];

$header_image = _BASE_URL_ . $cfg['EMAIL.HEADER.FOOTER.IMAGE'] . $rowHeader['logo_image'];
if ($rowHeader['logo_image'] != '') {
	$emailHeader  = $header_image;
}

//to get abstract file types
$sqlInfo  = array();
$sqlInfo['QUERY']    = "SELECT * FROM " . _DB_COMPANY_INFORMATION_ . " 
             WHERE `status` = ?";
$sqlInfo['PARAM'][] = array('FILD' => 'status',         'DATA' => 'A',                   'TYP' => 's');
$resultInfo      = $mycms->sql_select($sqlInfo);
$rowInfo         = $resultInfo[0];
$hod_consent_file_types = $rowInfo['hod_consent_file_types']; //JSON array
$abstract_file_types = $rowInfo['abstract_file_types'];

//================== To check all available services =======================
$workshopDetailsArray    = getAllWorkshopTariffs($currentCutoffId);
$registrationAccompanyAmount  = getCutoffTariffAmnt($currentCutoffId);
$delgDinner    = getDinnerDetailsOfDelegate($delegateId);
$dinnerDtls  = array();
if ($delgDinner && !empty($delgDinner)) {
	$dinnerDtls[$delegateId]                = $delgDinner;
	$dinnerDtls[$delegateId]['INVOICE']     = getInvoiceDetails($delgDinner['refference_invoice_id']);
	$dinnerDtls[$delegateId]['USER']        = $rowUserDetails;
}
$sqlFetchHotel      = array();
$sqlFetchHotel['QUERY'] = "SELECT * 
                                     FROM " . _DB_MASTER_HOTEL_ . "
                                    WHERE `status` =  ? ";

$sqlFetchHotel['PARAM'][] = array('FILD' => 'status',    'DATA' => 'A',     'TYP' => 's');
$resultFetchHotel        = $mycms->sql_select($sqlFetchHotel);
$countAcc = ($resultFetchHotel) ? '1' : '0';
// =========================================================================


$operate			  = true;
$resultAbstractType	  = false;
if ($delegateId != '') {
	$rowUserDetails   = getUserDetails($delegateId);
	// echo '<pre>'; print_r($rowUserDetails);die;
	$presenter_email = $rowUserDetails['user_email_id'];
	$invoiceList 	  = getConferenceContents($delegateId);
	$currentCutoffId  = getTariffCutoffId();

	$sql_abs  			  = array();
	$sql_abs['QUERY']     = " SELECT * 
								FROM " . _DB_ABSTRACT_REQUEST_ . " 
							   WHERE `status` = ?
								 AND `applicant_id` = ?";
	//AND `abstract_child_type` IN ('Oral','Poster')

	$sql_abs['PARAM'][]   = array('FILD' => 'status',         'DATA' => 'A',          'TYP' => 's');
	$sql_abs['PARAM'][]   = array('FILD' => 'applicant_id',   'DATA' => $delegateId, 'TYP' => 's');
	$resultAbstractType = $mycms->sql_select($sql_abs);

	$abstractCatArray = array();
	$countAbstract = 0;
	foreach ($resultAbstractType as $key => $cat_val) {

		if ($cat_val['abstract_cat'] == 1) {
			$countAbstract++;
		}

		array_push($abstractCatArray, trim($cat_val['abstract_cat']));
	}
	// echo $countPaper;
	// echo '<pre>'; print_r($abstractCatArray);die;
}
?>

<body class="no-bg cart-sade-bar">
	<style>
		.blur_bw {
			filter: blur(1.5px) grayscale(1);
		}
	</style>

	<main>

		<!-- <div class="cart">
      <img src="<?= _BASE_URL_ ?>images/cart.png" alt="" />
    </div> -->
		<form name="abstractRequestForm" id="abstractRequestForm" action="<?= _BASE_URL_ ?>abstract_request.process.php" method="post" enctype="multipart/form-data">
			<input type="hidden" name="act" value="abstractSubmission" />
			<input type="hidden" name="applicantId" id="applicantId" value="<?= $delegateId ?>" />
			<input type="hidden" name="report_data" id="report_data" value="Abstract" />

			<section class="dashbord-main">
				<div class="custom-dashbord-inner">
					<?php $sql   =  array();
					$sqlIcon['QUERY'] = "SELECT * FROM " . _DB_ICON_SETTING_ . " 
									WHERE `id`!='' AND `purpose`='Profile Side Panel Icon'  AND status IN ('A', 'I') ORDER BY `id`";
					// $sql['PARAM'][]	=	array('FILD' => 'status' ,  'DATA' => 'A' , 'TYP' => 's');					 
					$result    = $mycms->sql_select($sqlIcon);
					if ($result) {
					?>
						<div class="left-menu open">
							<!-- <div class="dot-menu"><img src="<?= _BASE_URL_ ?>images/more.png" alt="" /></div> -->
							<ul>
								<li><a href="<?= _BASE_URL_ ?>profile.php"><img src="<?= _BASE_URL_ ?>images/home.png" alt="" /></a></li>

								<li><a href="<?= _BASE_URL_ ?>profile.php" title="Profile"><img src="<?= _BASE_URL_ ?>images/cat-ic-1.png" alt="" /></a></li>
								<?php if (count($workshopDetailsArray) > 0) { ?>
									<li><a href="<?= _BASE_URL_ . "profile-add.php?section=3" ?>" title="<?= $result[0]['title'] ?>"><img src="<?= $cfg['EMAIL.HEADER.FOOTER.IMAGE'] . $result[0]['icon']; ?>" alt="" /></a></li>
								<?php }
								if (!empty($registrationAccompanyAmount) && $registrationAccompanyAmount > 0) { ?>
									<li><a href="<?= _BASE_URL_ . "profile-add.php?section=4" ?>" title="<?= $result[1]['title'] ?>"><img src="<?= $cfg['EMAIL.HEADER.FOOTER.IMAGE'] . $result[1]['icon']; ?>" alt="" /></a></li>
								<?php }
								if (sizeof($delgDinner) > 0) { ?>
									<li><a href="<?= _BASE_URL_ . "profile-add.php?section=5" ?>" title="<?= $result[2]['title'] ?>"><img src="<?= $cfg['EMAIL.HEADER.FOOTER.IMAGE'] . $result[2]['icon']; ?>" alt="" /></a></li>
								<?php }
								if ($countAcc) { ?>
									<li><a href="<?= _BASE_URL_ . "profile-add.php?section=6" ?>" title="<?= $result[3]['title'] ?>"><img src="<?= $cfg['EMAIL.HEADER.FOOTER.IMAGE'] . $result[3]['icon']; ?>" alt="" /></a></li>
								<?php } ?>
								<li><a href="<?= _BASE_URL_ ?>login.process.php?action=logout" class="btn logout" style="margin-top: 12px;" title="Logout"><img src="<?= _BASE_URL_ ?>images/log-out.png">
									</a> </li>
							</ul>
						</div>
					<?php } ?>

					<div class="accomodation-one">
						<div class="dash-menus">
							<a class="d-lg-none bar-icon"><i class="fas fa-bars"></i><i class="fa-solid fa-arrow-left"></i></a>
							<div class="user-name"><span><?= $rowUserDetails['user_full_name'] ?></span> <img src="<?= _BASE_URL_ ?>images/user.png" alt="" />
							</div>
							<div class="dash-notification"><img src="<?= _BASE_URL_ ?>images/notification.png" alt="" /></div>
							<div class="dash-logo"><a href="#"><img src="<?= $header_image ?>" style="    height: 72px;width: 204px;background: white;padding: 12px;" alt="" /></a></div>
						</div>
						<div class="dr-details-sec">
							<div class="author-details author-fold-1">
								<div class="author-close">
									<div class="close-auth"><!-- <img src="<?= _BASE_URL_ ?>images/auth-close.png" alt="" /> Close --></div>
								</div>

								<div class="auth-scroll">
									<div class="accordion" id="accordionExample">
										<div class="accordion-item" id="authorSection">
											<h2 class="accordion-header" id="headingOne">
												<button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
													<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512"><!--!Font Awesome Free 6.5.2 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2024 Fonticons, Inc.-->
														<path d="M256 512A256 256 0 1 0 256 0a256 256 0 1 0 0 512zM369 209L241 337c-9.4 9.4-24.6 9.4-33.9 0l-64-64c-9.4-9.4-9.4-24.6 0-33.9s24.6-9.4 33.9 0l47 47L335 175c9.4-9.4 24.6-9.4 33.9 0s9.4 24.6 0 33.9z" />
													</svg> Author Details
												</button>
											</h2>
											<div id="collapseOne" class="accordion-collapse collapse show" aria-labelledby="headingOne" data-bs-parent="#accordionExample">
												<div class="accordion-body" id="accordion-body-presenter">

													<input type="hidden" name="presenter_email_id" id="presenter_email_id" value="<?= $rowUserDetails['user_email_id'] ?>">
													<input type="hidden" name="presenter_mobile" id="presenter_mobile" value="<?= $rowUserDetails['user_mobile_no'] ?>">
													<input type="hidden" name="presenter_title" id="presenter_title" value="<?= $rowUserDetails['user_title'] ?>">
													<input type="hidden" name="presenter_first_name" id="presenter_first_name" value="<?= $rowUserDetails['user_first_name'] ?>">
													<input type="hidden" name="presenter_last_name" id="presenter_last_name" value="<?= $rowUserDetails['user_last_name'] ?>">
													<input type="hidden" name="presenter_country" id="presenter_country" value="<?= $rowUserDetails['user_country_id'] ?>">
													<input type="hidden" name="presenter_state" id="presenter_state" value="<?= $rowUserDetails['user_state_id'] ?>">
													<input type="hidden" name="presenter_city" id="presenter_city" value="<?= $rowUserDetails['user_city'] ?>">
													<input type="hidden" name="presenter_pincode" id="presenter_pincode" value="<?= $rowUserDetails['user_pincode'] ?>">
													<input type="hidden" name="presenter_institute" id="presenter_institute" value="<?= $rowUserDetails['user_institute_name'] ?>">
													<input type="hidden" name="presenter_department" id="presenter_department" value="<?= $rowUserDetails['user_department'] ?>">

													<div class="col-xs-12">
														<div class="checkbox" style="padding-top:10px">
															<div>
																<label class="custom-radio" style="float:left; margin-right:20px;">Author and Presenter is the same person
																	<input type="checkbox" name="willBePresenter" use="willBePresenter" id="willBePresenter" onclick="setAsPresenter(this)">
																	<span class="checkmark"></span>
																</label>
																&nbsp;
															</div>
														</div>
														<input type="hidden" id="isPresenter" name="isPresenter" value="">
													</div>
													<div class="form-floating mb-3">
														<label for="floatingInput">E-mail</label>
														<div class="d-flex">
															<span><img src="images/email-R.png" alt=""></span>
															<input type="text" name="abstract_author_email" id="abstract_author_email" class="form-control" style="text-transform:lowercase;" usefor="email" value="" validate="Please enter the email id">
														</div>
													</div>
													<div class="form-floating mb-3">
														<label for="floatingInput">Mobile</label>
														<div class="d-flex">
															<span><img src="images/phone-R.png" alt=""></span>
															<input type="text" class="form-control" id="abstract_author_mobile" name="abstract_author_mobile" placeholder="" validate="Please enter the mobile No" onkeypress="return isNumber(event)" maxlength="10">
														</div>
													</div>
													<div class="form-floating mb-3">
														<label for="floatingInput">Title</label>
														<div class="d-flex">
															<span><img src="images/Name-R.png" alt=""></span>
															<div class="checkbox-wrap">
																<label class="custom-radio"><input type="radio" name="abstract_author_title" value="DR"> Dr.<span class="checkmark"></span> </label>
																<label class="custom-radio"><input type="radio" name="abstract_author_title" value="PROF"> Prof. <span class="checkmark"></label>
																<label class="custom-radio"><input type="radio" name="abstract_author_title" value="MR"> Mr.<span class="checkmark"></label>
																<label class="custom-radio"><input type="radio" name="abstract_author_title" value="MRS"> Mrs.<span class="checkmark"></label>
																<label class="custom-radio"><input type="radio" name="abstract_author_title" value="MS"> Ms.<span class="checkmark"></label>
															</div>
														</div>
													</div>
													<div class="form-floating mb-3">
														<label for="floatingInput">First name</label>
														<div class="d-flex">
															<span><img src="images/Name-R.png" alt=""></span>
															<input type="text" class="form-control" id="abstract_author_first_name" name="abstract_author_first_name" placeholder="" validate="Please enter the first name">
														</div>
													</div>
													<div class="form-floating mb-3">
														<label for="floatingInput">Last Name</label>
														<div class="d-flex">
															<span><img src="images/Name-R.png" alt=""></span>
															<input type="text" class="form-control" id="abstract_author_last_name" name="abstract_author_last_name" placeholder="" validate="Please enter the last name">
														</div>
													</div>
													<div class="form-floating mb-3">
														<label for="floatingSelect">Country</label>
														<div class="d-flex">
															<span><img src="images/country-R.png" alt=""></span>
															<select class="form-control select" name="abstract_author_country" id="abstract_author_country" forType="country" style="text-transform:uppercase;" required validate="Please select country">
																<option value="0">-- Select Country --</option>
																<?php
																$sqlFetchCountry   = array();
																$sqlFetchCountry['QUERY']    = "SELECT * FROM " . _DB_COMN_COUNTRY_ . " 
	                                                         WHERE `status` =? 
	                                                      ORDER BY `country_name` ASC";

																$sqlFetchCountry['PARAM'][]   = array('FILD' => 'status', 'DATA' => 'A', 'TYP' => 's');

																$resultFetchCountry = $mycms->sql_select($sqlFetchCountry);
																if ($resultFetchCountry) {
																	foreach ($resultFetchCountry as $keyCountry => $rowFetchCountry) {

																?>
																		<option value="<?= $rowFetchCountry['country_id'] ?>"><?= $rowFetchCountry['country_name'] ?></option>
																<?php
																	}
																}
																?>
															</select>
														</div>
													</div>
													<div class="form-floating mb-3">
														<label for="floatingSelect">State</label>
														<div class="d-flex">
															<span><img src="images/state-R.png" alt=""></span>
															<?php
															// $sqlFetchState   = array();
															// $sqlFetchState['QUERY']    = "SELECT * FROM " . _DB_COMN_STATE_ . " 
															//  WHERE `status` =? AND `country_id`=?
															// ORDER BY `state_name` ASC";

															// $sqlFetchState['PARAM'][]   = array('FILD' => 'status', 'DATA' => 'A', 'TYP' => 's');
															// $sqlFetchState['PARAM'][]   = array('FILD' => 'country_id', 'DATA' => $rowUserDetails['user_country_id'], 'TYP' => 's');

															// $resultFetchState = $mycms->sql_select($sqlFetchState);

															?>

															<!-- <select class="form-control select" name="abstract_authors_state" id="abstract_presenter_state" use="country" forType="state" style="text-transform:uppercase;" required validate="Please select state"> -->
															<select class="form-control select" name="abstract_author_state" id="abstract_author_state" forType="country" style="text-transform:uppercase;" required validate="Please select state">
																<!-- <option value="0">-- Select State --</option> -->
																<option value="">-- Select Country First --</option>
																<!-- <?php

																		if ($resultFetchState) {
																			foreach ($resultFetchState as $keyState => $rowFetchState) {
																		?>
																		<option value="<?= $rowFetchState['st_id'] ?>"><?= $rowFetchState['state_name'] ?></option>
																<?php
																			}
																		}
																?> -->
															</select>
														</div>

													</div>


													<div class="form-floating mb-3">
														<label for="floatingInput">City</label>
														<div class="d-flex">
															<span><img src="images/city-R.png" alt=""></span>
															<input type="text" class="form-control" id="abstract_author_city" name="abstract_author_city" placeholder="" validate="Please enter the city">
														</div>
													</div>
													<div class="form-floating mb-3">
														<label for="floatingInput">Postal Code</label>
														<div class="d-flex">
															<span><img src="images/postal-R.png" alt=""></span>
															<input type="text" class="form-control" id="abstract_author_pincode" name="abstract_author_pincode" placeholder="" validate="Please enter the postal code">
														</div>
													</div>
													<div class="form-floating mb-3">
														<label for="floatingInput">Institute</label>
														<div class="d-flex">
															<span><img src="images/postal-R.png" alt=""></span>
															<input type="text" class="form-control" use="Institute" id="abstract_author_institute" name="abstract_author_institute" placeholder="" validate="Please enter the institute" autocomplete="nope">
														</div>
													</div>
													<div class="form-floating">
														<label for="floatingInput">Department</label>
														<div class="d-flex">
															<span><img src="images/postal-R.png" alt=""></span>
															<input type="text" class="form-control" use="Department" id="abstract_author_department" name="abstract_author_department" placeholder="" validate="Please enter the Department" autocomplete="nope">
														</div>
													</div>

												</div>
											</div>
										</div>


										<div class="accordion-item" id="coAuthorSection" style="display:none">
											<h2 class="accordion-header" id="headingTwo">
												<button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseTwo" aria-expanded="false" aria-controls="collapseTwo">
													<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512"><!--!Font Awesome Free 6.5.2 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2024 Fonticons, Inc.-->
														<path d="M256 512A256 256 0 1 0 256 0a256 256 0 1 0 0 512zM369 209L241 337c-9.4 9.4-24.6 9.4-33.9 0l-64-64c-9.4-9.4-9.4-24.6 0-33.9s24.6-9.4 33.9 0l47 47L335 175c9.4-9.4 24.6-9.4 33.9 0s9.4 24.6 0 33.9z" />
													</svg> Co-author Details
												</button>
											</h2>
											<div id="collapseTwo" class="accordion-collapse collapse show" aria-labelledby="headingTwo" data-bs-parent="#accordionExample">
												<!-- <div id="allCoauthor"> -->
												<div class="accordion-body accordion" id="accordion-body-coauthor">

													<div class="add_coathor  accordion-item" id="coAuthor_first" style="margin-bottom: 22px;">
														<h5 class="accordion-header" id="ca1">
															<button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseTwo1" aria-expanded="false" aria-controls="collapseTwo1">
																<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512"><!--!Font Awesome Free 6.5.2 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2024 Fonticons, Inc.-->
																	<path d="M256 512A256 256 0 1 0 256 0a256 256 0 1 0 0 512zM369 209L241 337c-9.4 9.4-24.6 9.4-33.9 0l-64-64c-9.4-9.4-9.4-24.6 0-33.9s24.6-9.4 33.9 0l47 47L335 175c9.4-9.4 24.6-9.4 33.9 0s9.4 24.6 0 33.9z" />
																</svg> Co Auther - <span id="coCount">1</span>
															</button>
														</h5>
														<div id="collapseTwo1" class="accordion-collapse collapse show " aria-labelledby="headingTwo1" data-bs-parent="#accordion-body-coauthor">
															<!-- <h5>Co Auther - <span id="coCount">1</span></h5> -->
															<div class="form-floating mb-3">
																<label for="floatingInput">E-mail</label>
																<div class="d-flex">
																	<span><img src="images/email-R.png" alt=""></span>
																	<input type="text" class="form-control coauther_details " use="Email" id="abstract_coauthor_email" name="abstract_coauthor_email[0]" placeholder="" validate="Please enter the coauthor email" autocomplete="nope">
																</div>
															</div>
															<div class="form-floating mb-3">
																<label for="floatingInput">Mobile</label>
																<div class="d-flex">
																	<span><img src="images/phone-R.png" alt=""></span>
																	<input type="text" class="form-control coauther_details " use="Mobile" id="abstract_coauthor_mobile" name="abstract_coauthor_mobile[0]" placeholder="" onkeypress="return isNumber(event)" maxlength="10" validate="Please enter the coauthor mobile" onkeypress="return isNumber(event)" maxlength="10" autocomplete="nope">
																</div>
															</div>
															<div class="form-floating mb-3">
																<label for="floatingInput">Title</label>
																<div class="d-flex">
																	<span><img src="images/Name-R.png" alt=""></span>
																	<div class="checkbox-wrap">
																		<label class="custom-radio"><input type="radio" name="abstract_coauthor_title[0]" value="Dr"> Dr.<span class="checkmark"></label>
																		<label class="custom-radio"><input type="radio" name="abstract_coauthor_title[0]" value="Prof"> Prof.<span class="checkmark"></label>
																		<label class="custom-radio"><input type="radio" name="abstract_coauthor_title[0]" value="Mr"> Mr.<span class="checkmark"></label>
																		<label class="custom-radio"><input type="radio" name="abstract_coauthor_title[0]" value="Mrs"> Mrs.<span class="checkmark"></label>
																		<label class="custom-radio"><input type="radio" name="abstract_coauthor_title[0]" value="Ms"> Ms.<span class="checkmark"></label>
																	</div>
																</div>
															</div>
															<div class="form-floating mb-3">
																<label for="floatingInput">First name</label>
																<div class="d-flex">
																	<span><img src="images/Name-R.png" alt=""></span>
																	<input type="text" class="form-control coauther_details " use="First Name" id="abstract_coauthor_first_name" name="abstract_coauthor_first_name[0]" placeholder="" validate="Please enter the coauthor first name" autocomplete="nope">
																</div>
															</div>
															<div class="form-floating mb-3">
																<label for="floatingInput">Last Name</label>
																<div class="d-flex">
																	<span><img src="images/Name-R.png" alt=""></span>
																	<input type="text" class="form-control coauther_details " use="Last Name" id="abstract_coauthor_last_name" name="abstract_coauthor_last_name[0]" placeholder="" validate="Please enter the coauthor last name" autocomplete="nope">
																</div>
															</div>
															<div class="form-floating mb-3">
																<label for="floatingSelect">Country</label>
																<div class="d-flex">
																	<span><img src="images/country-R.png" alt=""></span>
																	<select class="form-control select coauther_details " use="Country" name="abstract_coauthor_country[0]" id="abstract_coauthor_country" forType="country" style="text-transform:uppercase;" required validate="Please select the coauthor country">
																		<option value="0">-- Select Country --</option>
																		<?php
																		$sqlFetchCountry   = array();
																		$sqlFetchCountry['QUERY']    = "SELECT * FROM " . _DB_COMN_COUNTRY_ . " 
																                         WHERE `status` =? 
																                      ORDER BY `country_name` ASC";

																		$sqlFetchCountry['PARAM'][]   = array('FILD' => 'status', 'DATA' => 'A', 'TYP' => 's');

																		$resultFetchCountry = $mycms->sql_select($sqlFetchCountry);
																		if ($resultFetchCountry) {
																			foreach ($resultFetchCountry as $keyCountry => $rowFetchCountry) {
																		?>
																				<option value="<?= $rowFetchCountry['country_id'] ?>"><?= $rowFetchCountry['country_name'] ?></option>
																		<?php
																			}
																		}
																		?>
																	</select>
																</div>
															</div>
															<div class="form-floating mb-3">
																<label for="floatingSelect">State</label>
																<div class="d-flex">
																	<span><img src="images/state-R.png" alt=""></span>
																	<select class="form-control select coauther_details " use="State" name="abstract_coauthor_state[0]" id="abstract_coauthor_state" forType="state" style="text-transform:uppercase;" required validate="Please select the coauthor state">
																		<option value="0" selected>Select State</option>
																		<?php
																		$sqlFetchState   = array();
																		$sqlFetchState['QUERY']    = "SELECT * FROM " . _DB_COMN_STATE_ . " 
															                         WHERE `status` =? 
															                      ORDER BY `state_name` ASC";

																		$sqlFetchState['PARAM'][]   = array('FILD' => 'status', 'DATA' => 'A', 'TYP' => 's');

																		$resultFetchState = $mycms->sql_select($sqlFetchState);
																		if ($resultFetchState) {
																			foreach ($resultFetchState as $keyState => $rowFetchState) {
																		?>
																				<option value="<?= $rowFetchState['st_id'] ?>"><?= $rowFetchState['state_name'] ?></option>
																		<?php
																			}
																		}
																		?>
																	</select>
																</div>
															</div>
															<div class="form-floating mb-3">
																<label for="floatingInput">City</label>
																<div class="d-flex">
																	<span><img src="images/city-R.png" alt=""></span>
																	<input type="text" class="form-control coauther_details " use="City" id="abstract_coauthor_city" name="abstract_coauthor_city[0]" placeholder="" validate="Please enter the coauthor city" autocomplete="nope">
																</div>
															</div>
															<div class="form-floating mb-3">
																<label for="floatingInput">Postal Code</label>
																<div class="d-flex">
																	<span><img src="images/postal-R.png" alt=""></span>
																	<input type="text" class="form-control coauther_details " use="Postal Code" id="abstract_coauthor_pincode" name="abstract_coauthor_pincode[0]" placeholder="" validate="Please enter the coauthor pincode" onkeypress="return isNumber(event)" autocomplete="nope">
																</div>
															</div>
															<div class="form-floating mb-3">
																<label for="floatingInput">Institute</label>
																<div class="d-flex">
																	<span><img src="images/postal-R.png" alt=""></span>
																	<input type="text" class="form-control coauther_details " use="Institute" id="abstract_coauthor_institute" name="abstract_coauthor_institute[0]" placeholder="" validate="Please enter the coauthor institute" autocomplete="nope">
																</div>
															</div>
															<div class="form-floating">
																<label for="floatingInput">Department</label>
																<div class="d-flex">
																	<span><img src="images/postal-R.png" alt=""></span>
																	<input type="text" class="form-control coauther_details " use="Department" id="abstract_coauthor_department" name="abstract_coauthor_department[0]" placeholder="" validate="Please enter the coauthor Department" autocomplete="nope">
																</div>
															</div>
															<!-- <input type="hidden" name="coAuthorCounts" id="coAuthorCounts" value="1"> -->

														</div>
													</div>
													<button class="btn" id="removeCoauthor1" style="margin-top: 9px;background: #c34747;border:0px solid black; padding: 12px 25px;margin-bottom: 15px;">Delete</button>

													<input type="hidden" name="coAuthorCounts" id="coAuthorCounts" value="1">
													<!-- <button  style="padding-top: 10px;padding-left: 20px;background-color: #eb6666bf;" id="isCoauthor"> Remove </button> -->
													<!-- <button class="delete-coauthor-btn" id="delete_first">Delete</button> -->



												</div>
												<button class="btn" id="add-coauthor-btn" style="margin-top: 9px;">Add More</button>
												<!-- </div> -->

											</div>
										</div>
									</div>
								</div>

								<div class="bottom-btn-wrap">
									<a id="firstAthrBtn" class="btn next-btn"><i class="fa-solid fa-chevron-right"></i></a>
									<a id="firstBtn" class="btn next-btn next" style="display:none"><i class="fa-solid fa-chevron-right"></i></a>

								</div>
							</div>




							<div class="author-details author-fold-2 blur_bw">
								<div class="author-close d-none">
									<div class="close-auth"><!-- <img src="<?= _BASE_URL_ ?>images/auth-close.png" alt="" /> Close --></div>
								</div>

								<div class="auth-scroll">
									<div class="accordion" id="accordionExample2">
										<div class="accordion-item">
											<h2 class="accordion-header" id="headingOne1">
												<button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" id="categoryBtn" data-bs-target="#collapseOne1" aria-expanded="true" aria-controls="collapseOne">
													<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512"><!--!Font Awesome Free 6.5.2 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2024 Fonticons, Inc.-->
														<path d="M256 512A256 256 0 1 0 256 0a256 256 0 1 0 0 512zM369 209L241 337c-9.4 9.4-24.6 9.4-33.9 0l-64-64c-9.4-9.4-9.4-24.6 0-33.9s24.6-9.4 33.9 0l47 47L335 175c9.4-9.4 24.6-9.4 33.9 0s9.4 24.6 0 33.9z" />
													</svg> Submission Category
												</button>
											</h2>

											<div id="collapseOne1" class="accordion-collapse collapse show" aria-labelledby="headingOne1" data-bs-parent="#accordionExample2">
												<div class="accordion-body custom-radio-holder" id="abstract_details">

													<?php
													if (count($resultAbstractType) < 10) {

														$sqlAbstractTopic			  =	array();
														$sqlAbstractTopic['QUERY']    = "SELECT * FROM " . _DB_ABSTRACT_TOPIC_CATEGORY_ . " 
																										  WHERE `status` IN ('A')
																									   ORDER BY `category` ASC";
														$resultAbstractTopic = $mycms->sql_select($sqlAbstractTopic);


														$sqlAbstractSubmission			  =	array();
														$sqlAbstractSubmission['QUERY']    = "SELECT * FROM " . _DB_ABSTRACT_SUBMISSION_ . " 
																										  WHERE `status` IN ('A')
																									   ORDER BY `category` ASC";
														$resultAbstractSubmission = $mycms->sql_select($sqlAbstractSubmission);

														$sqlAbstractPresentation			 =	array();
														$sqlAbstractPresentation['QUERY']    = "SELECT * FROM " . _DB_ABSTRACT_PRESENTATION_ . " 
																										  WHERE `status` ='A'
																									   ORDER BY `id` ASC";
														$resultAbstractPresentation = $mycms->sql_select($sqlAbstractPresentation);




														//echo '<pre>'; print_r($resultAbstractTopic);
														foreach ($resultAbstractTopic as $key => $value) {

															$sqlAbstractSubcat['QUERY']    = "SELECT * FROM " . _DB_AWARD_MASTER_ . " 
																								WHERE `related_category_id`='" . $value['id'] . "' AND `related_topic_id`='' AND
																								`status` = 'A' ORDER BY `id` ASC";

															$resultAbstractSubcat = $mycms->sql_select($sqlAbstractSubcat);
															$nomination_ids = array();
															if ($resultAbstractSubcat) {
																foreach ($resultAbstractSubcat as $key => $nomination) {
																	array_push($nomination_ids, $nomination['id']);
																}
															}
															$nomination_ids = json_encode($nomination_ids);
															// print_r($nomination_ids);

															if (in_array(trim($value['id']), $abstractCatArray)) {
																$checked = 'checked';
																$disabled = 'disabled';
															} else {
																$checked = '';
																$disabled = '';
															}
													?>

															<div class="form-check">
																<label class="custom-radio " for="flexCheckDefault<?= $value['id'] ?>">
																	<input class=" custom-radio trigger-subCategory" type="radio" value="<?= $value['id'] ?>" id="flexCheckDefault<?= $value['id'] ?>" name="abstract_category" onclick="abstractTypeChange(this,'<?= $value['id'] ?>','<?php echo implode(',', json_decode($value['category_fields'])); ?>','<?php echo implode(',', json_decode($nomination_ids)); ?>')" title="<?= $value['category'] ?>">
																	<?= $value['category'] ?><span class="checkmark"></span>
																</label>
															</div>
													<?php

														}
													}

													?>

													<input type="hidden" name="absCatTitle" id="absCatTitle">

												</div>
											</div>
										</div>
										<?php
										if ($resultAbstractSubmission) {
										?>
											<div class="accordion-item">
												<h2 class="accordion-header" id="headingTwo2">
													<button class="accordion-button collapsed" type="button" id="subCatBtn" data-bs-toggle="collapse" data-bs-target="#collapseTwo2SubCat" aria-expanded="false" aria-controls="collapseTwo">
														<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512"><!--!Font Awesome Free 6.5.2 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2024 Fonticons, Inc.-->
															<path d="M256 512A256 256 0 1 0 256 0a256 256 0 1 0 0 512zM369 209L241 337c-9.4 9.4-24.6 9.4-33.9 0l-64-64c-9.4-9.4-9.4-24.6 0-33.9s24.6-9.4 33.9 0l47 47L335 175c9.4-9.4 24.6-9.4 33.9 0s9.4 24.6 0 33.9z" />
														</svg> Submission Sub Category
													</button>
												</h2>
												<div id="collapseTwo2SubCat" class="accordion-collapse collapse " aria-labelledby="headingTwo2" data-bs-parent="#accordionExample2">
													<div class="accordion-body custom-radio-holder" id="abstract_details">

														<?php
														if (count($resultAbstractType) < 10) {
															foreach ($resultAbstractSubmission as $key => $value) {

														?>
																<div class="form-check submissionTypeRadio<?= $value['category'] ?> hidesub" style="display: none;">
																	<label class="form-check-label custom-radio" for="abstract_parent_type<?= $value['id'] ?>">

																		<input class="form-check-input" id="abstract_parent_type<?= $value['id'] ?>" type="radio" name="abstract_parent_type" value="<?= $value['id'] ?>" titleWordCountLimit="<?= $cfg['ABSTRACT.TITLE.WORD.LIMIT'] ?>" contentWordCountLimit="<?= $cfg['ABSTRACT.FREE.PAPER.SESSION.WORD.LIMIT'] ?>" relatedSubmissionSubType="" onclick="abstractSubmissionType(this,'<?php echo $value['category']; ?>','<?php echo $value['id']; ?>')" disabled="disabled" title="<?= $value['abstract_submission'] ?>">
																		<?= $value['abstract_submission'] ?><span class="checkmark"></span>
																	</label>
																</div>

														<?php
															}
														}
														?>
														<input type="hidden" name="abssubCatTitle" id="abssubCatTitle">
													</div>
												</div>
											</div>
										<?php } ?>

									</div>
								</div>

								<div class="bottom-btn-wrap">
									<!-- <a id="secondBtnPrev" class="prev" style="display: none;cursor: pointer;">Previous</a>
									<a id="secondBtn" class="next" style="display: none;cursor: pointer;">Next</a> -->

									<a id="secondBtnPrev" class="btn next-btn prev" style="display: none;"><i class="fa-solid fa-chevron-left"></i></a>
									<a id="secondBtn" class="btn next-btn next" style="display: none;"><i class="fa-solid fa-chevron-right"></i></a>

								</div>
							</div>



							<div class="author-details author-fold-3 blur_bw">
								<div class="author-close d-none"></div>

								<div id="all_abstractDetails" class="auth-scroll">
									<?php
									$sqlAbstractTopic			  =	array();
									$sqlAbstractTopic['QUERY']    = "SELECT * FROM " . _DB_ABSTRACT_TOPIC_ . " 
																											  WHERE `status` = ? 
																										   ORDER BY `id` ASC";

									$sqlAbstractTopic['PARAM'][]  = array('FILD' => 'status', 'DATA' => 'A',  'TYP' => 's');
									$resultAbstractTopic = $mycms->sql_select($sqlAbstractTopic);
									// print_r($resultAbstractTopic);

									if ($resultAbstractTopic) {
									?>
										<div class="choose-topic">
											<h2><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512">
													<path d="M256 512A256 256 0 1 0 256 0a256 256 0 1 0 0 512zM369 209L241 337c-9.4 9.4-24.6 9.4-33.9 0l-64-64c-9.4-9.4-9.4-24.6 0-33.9s24.6-9.4 33.9 0l47 47L335 175c9.4-9.4 24.6-9.4 33.9 0s9.4 24.6 0 33.9z" />
												</svg> Choose Your Topic</h2>
											<div class="choose-drop" id="abstract_details_2">

												<select name="abstract_topic_id" id="abstract_topic_id" class="form-control select" style="text-transform:uppercase; height:60px;" required="required" validate="Please select the topic" onchange="getTopicWiseNomination(this)">
													<option value="">--Topic--</option>
													<?php

													foreach ($resultAbstractTopic as $keyAbstractTopic => $rowAbstractTopic) {
													?>
														<option value="<?= $rowAbstractTopic['id'] ?>"><?= $rowAbstractTopic['abstract_topic'] ?></option>
													<?php
													}

													?>
												</select>

												<?php if ($cfg['ABSTRACT.GUIDELINE.PDF.FLAG'] != 0) { ?>
													<a href="<?= $cfg['ABSTRACT.GUIDELINE.PDF.FLAG'] == '1' ? $cfg['ABSTRACT.GUIDELINE.PDF'] : _BASE_URL_ . "uploads/FILES.ABSTRACT.REQUEST/" . $cfg['ABSTRACT.GUIDELINE.PDF.FILE'] ?>" target="_blank"><img src="<?= _BASE_URL_ ?>images/gide.png" alt=""></a>
												<?php }
												?>


											</div>
										</div>
										<div id="abstract_topic_desc" style="font-style: italic; font-size: 14px;font-weight: bolder;">

										</div>
									<?php } ?>
									<div class="accordion" id="accordionExample3" use="abstractDetails">

										<div class="accordion-item">
											<h2 class="accordion-header" id="headingOne10">
												<button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#collapseOne10" aria-expanded="true" aria-controls="collapseOne">
													<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512"><!--!Font Awesome Free 6.5.2 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2024 Fonticons, Inc.-->
														<path d="M256 512A256 256 0 1 0 256 0a256 256 0 1 0 0 512zM369 209L241 337c-9.4 9.4-24.6 9.4-33.9 0l-64-64c-9.4-9.4-9.4-24.6 0-33.9s24.6-9.4 33.9 0l47 47L335 175c9.4-9.4 24.6-9.4 33.9 0s9.4 24.6 0 33.9z" />
													</svg> Title
												</button>
											</h2>

											<div id="collapseOne10" class="accordion-collapse collapse show" aria-labelledby="headingOne10" data-bs-parent="#accordionExample3">
												<div class="accordion-body p-0" id="abstract_details_2">


													<div class="auth-textarea">
														<textarea class="form-control" name="abstract_title" id="abstract_title" checkFor="wordCount" spreadInGroup="abstractTitle" displayText="abstract_title_word_count" style="text-transform:uppercase;" <?php if ($cfg['ABSTRACT.TITLE.WORD.TYPE'] == 'character') { ?> maxlength="<?= $cfg['ABSTRACT.TITLE.WORD.LIMIT'] ?>" <?php } ?> required validate="Please enter the abstract title"></textarea>

														<span use="abstract_title_word_count" limit="<?= $cfg['ABSTRACT.TITLE.WORD.LIMIT'] ?>">
															<span use="total_word_entered">0</span> /
															<span use="total_word_limit"><?= $cfg['ABSTRACT.TITLE.WORD.LIMIT'] ?></span>
															<!-- <span style="color: #D41000;">(Title should be within <?= $cfg['ABSTRACT.TITLE.WORD.LIMIT'] ?> <?= $cfg['ABSTRACT.TOTAL.WORD.TYPE'] ?>.)</span> -->
															<span style="color: #ffff;">(Title should be within <?= $cfg['ABSTRACT.TITLE.WORD.LIMIT'] ?> <?= $cfg['ABSTRACT.TITLE.WORD.TYPE'] ?>s )</span>
														</span>
													</div>


												</div>
											</div>
										</div>
										<?php
										$sqlAbstractFields			  =	array();
										$sqlAbstractFields['QUERY']    = "SELECT * FROM " . _DB_ABSTRACT_FIELDS_ . " 
																						  WHERE `status` = ?";

										$sqlAbstractFields['PARAM'][]  = array('FILD' => 'status', 'DATA' => 'A',  'TYP' => 's');

										$resultAbstractFields = $mycms->sql_select($sqlAbstractFields);

										$i = 1;
										//echo '<pre>'; print_r($resultAbstractFields);
										foreach ($resultAbstractFields as $key => $value) {

											$msg = "Please enter the " . strtolower($value['display_name']);

										?>

											<div class="accordion-item hideFieldVal" id="fields_<?= $value['id'] ?>" actAs='fieldContainer' relatedSubmissionType="" relatedSubmissionSubType="" style="display: none;">
												<h2 class="accordion-header" id="headingOne1<?= $i ?>">
													<button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#collapseOne1<?= $i ?>" aria-expanded="true" aria-controls="collapseOne">
														<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512"><!--!Font Awesome Free 6.5.2 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2024 Fonticons, Inc.-->
															<path d="M256 512A256 256 0 1 0 256 0a256 256 0 1 0 0 512zM369 209L241 337c-9.4 9.4-24.6 9.4-33.9 0l-64-64c-9.4-9.4-9.4-24.6 0-33.9s24.6-9.4 33.9 0l47 47L335 175c9.4-9.4 24.6-9.4 33.9 0s9.4 24.6 0 33.9z" />
														</svg> <?= $value['display_name'] ?>
													</button>
												</h2>

												<div id="collapseOne1<?= $i ?>" class="accordion-collapse collapse" aria-labelledby="headingOne1<?= $i ?>" data-bs-parent="#accordionExample3">
													<div class="accordion-body p-0" id="abstract_details_2">


														<div class="auth-textarea">
															<textarea class="form-control commn hideitem" name="<?= $value['field_key'] ?>[]" id="fieldVal_<?= $value['id'] ?>" checkFor="wordCount" spreadInGroup="abstractContent" displayText="abstract_total_word_display" word_type="<?= $cfg['ABSTRACT.TOTAL.WORD.TYPE'] ?>" validate="<?= $msg ?>" title="<?= $value['display_name'] ?>"></textarea>
														</div>


													</div>
												</div>
											</div>

										<?php
											$i++;
										}
										if ($resultAbstractFields) {
										?>

											<div class="col-xs-12 form-group" id="wordCountList" style="display: none;">
												<div class="checkbox" style="padding: 1rem 1.25rem !important;">
													<label class="select-lable">Total Word Count</label>
													<span use="abstract_total_word_display" limit="<?= $cfg['ABSTRACT.FREE.PAPER.SESSION.WORD.LIMIT'] ?>">
														<span use="total_word_entered">0</span> /
														<span use="total_word_limit"><?= $cfg['ABSTRACT.FREE.PAPER.SESSION.WORD.LIMIT'] ?></span>
														<span style="color: #ffff;">(Total <?= $cfg['ABSTRACT.FREE.PAPER.SESSION.WORD.LIMIT'] ?> are <?= $cfg['ABSTRACT.TOTAL.WORD.TYPE'] ?>s allowed.)</span>
													</span>
												</div>
											</div>
										<?php
										}
										?>
									</div>
									<!-- <hr><h6>Nomination</h6><hr> -->
									<?php
									$sqlAbstractSubcat    = array();
									$sqlAbstractSubcat['QUERY']    = "SELECT * 
																		  FROM " . _DB_AWARD_MASTER_ . " 
																		 WHERE `status` = ? 
																	  ORDER BY `id` ASC";

									$sqlAbstractSubcat['PARAM'][]   = array('FILD' => 'status',  'DATA' => 'A',  'TYP' => 's');

									$resultAbstractSubcat = $mycms->sql_select($sqlAbstractSubcat);

									if ($resultAbstractSubcat) {
									?>
										<div class="accordion" id="accordianNomination" ishidden="0" style="display:none">
											<div class="accordion-item">
												<h2 class="accordion-header" id="headingOne101">
													<button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#collapseOne101" aria-expanded="true" aria-controls="collapseOne">
														<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512">
															<path d="M256 512A256 256 0 1 0 256 0a256 256 0 1 0 0 512zM369 209L241 337c-9.4 9.4-24.6 9.4-33.9 0l-64-64c-9.4-9.4-9.4-24.6 0-33.9s24.6-9.4 33.9 0l47 47L335 175c9.4-9.4 24.6-9.4 33.9 0s9.4 24.6 0 33.9z" />
														</svg> Bursary Programme
													</button>
												</h2>

												<div id="collapseOne101" class="accordion-collapse collapse show" aria-labelledby="headingOne10" data-bs-parent="#accordianNomination">
													<div class="accordion-body p-0" id="abstract_details_2">

														<div class="custom-radio-holder" id="" style="padding:0px 0px">
															<?php
															foreach ($resultAbstractSubcat as $keyAbstractTopic => $rowAbstractTopic) {
															?>
																<div class="form-check nomination_list" style="padding:0px 0px;display:none" id="nomination_holder_<?= $rowAbstractTopic['id'] ?>">
																	<label class="custom-radio " for="nomination_name_<?= $rowAbstractTopic['id'] ?>">
																		<input class=" custom-radio " type="radio" value="<?= $rowAbstractTopic['id'] ?>" id="nomination_name_<?= $rowAbstractTopic['id'] ?>" name="award_request">
																		<?= "I want to opt for " . $rowAbstractTopic['award_name'] ?><span class="checkmark"></span>
																		<br><br><span><i>
																				It is mandatory for all bursary applicants to REGISTER FOR
																				THE CONFERENCE PRIOR TO THE LAST DATE OF
																				ABSTRACT SUBMISSION otherwise their bursary application
																				will not be considered.</i>
																		</span>
																	</label>

																</div>
															<?php } ?>

														</div>
													</div>
												</div>
											</div>
										</div>
									<?php } ?>
									<script>
										document.querySelectorAll('input[type="radio"][name="award_request"]').forEach(function(radio) {
											radio.addEventListener('click', function() {
												// If the radio was already checked, uncheck it
												if (this.checked && this.dataset.wasChecked === "true") {
													this.checked = false;
													this.dataset.wasChecked = "false";
												} else {
													// If the radio was not checked or clicked for the first time, mark it as checked
													this.dataset.wasChecked = "true";
												}

												// Reset other radios in the group
												document.querySelectorAll('input[name="' + this.name + '"]').forEach(function(otherRadio) {
													if (otherRadio !== radio) {
														otherRadio.dataset.wasChecked = "false";
													}
												});
											});
										});
									</script>

									<div class="auth-file-upload-wrap">
										<!-- <div class="auth-file-upload" id="faculty_upload" style="display: none;">
											<img src="<?= _BASE_URL_ ?>images/uplod.png" alt="" />
											<div class="file-up-dtls">
												<h5>Upload File</h5>
												<input type="hidden" name="abstract_file_types" id="abstract_file_types" value=<?= "pdf", "word" ?> />
												<input type="hidden" name="original_abstract_file_name" id="original_abstract_file_name" use="upload_original_fileName" />
												<input type="hidden" name="temp_abstract_filename" id="temp_abstract_filename">
												<input class="form-control hideitem" type="file" id="formFileAbstract" name="upload_abstract_file" validate="Please upload the abstract file" />
												<?php $type = json_decode($abstract_file_types); ?>
												<h6>PDF | Word</h6>
												<span id=abstractFileNameUploaded></span>

											</div>
										</div> -->

										<?php
										if ($hod_consent_file_types !== 'null') {
										?>
											<div class="auth-file-upload">
												<img src="<?= _BASE_URL_ ?>images/uplod.png" alt="" />
												<input type="hidden" name="sessionId" id="sessionId" use="sessionId" value="<?= session_id() ?>" />
												<div class="file-up-dtls">
													<h5>HOD Consent</h5>

													<input type="hidden" name="hod_consent_file_types" id="hod_consent_file_types" value=<?= $hod_consent_file_types ?> />
													<input type="hidden" name="original_consent_file_name" id="original_consent_file_name" use="upload_original_fileName" />
													<input type="hidden" name="temp_consent_filename" id="temp_consent_filename">
													<input class="form-control" type="file" id="formFileHod" name="upload_consent_abstract_file" validate="Please upload the HOD consent file" />
													<?php $type = json_decode($hod_consent_file_types); ?>

													<h6><?= ($type[0] != '') ? strtoupper($type[0]) . " | " : '' ?><?= ($type[1] != '') ? ucfirst($type[1]) : '' ?><?= ($type[2] != '') ? " | " . ucfirst($type[2]) : '' ?></h6>
													<span id=concentFileNameUploaded></span>
												</div>
											</div>
										<?php }
										if ($abstract_file_types !== 'null') { ?>
											<div class="auth-file-upload">
												<img src="<?= _BASE_URL_ ?>images/uplod.png" alt="" />
												<div class="file-up-dtls">
													<h5>Abstract File</h5>
													<input type="hidden" name="abstract_file_types" id="abstract_file_types" value=<?= $abstract_file_types ?> />
													<input type="hidden" name="original_abstract_file_name" id="original_abstract_file_name" use="upload_original_fileName" />
													<input type="hidden" name="temp_abstract_filename" id="temp_abstract_filename">
													<input class="form-control" type="file" id="formFileAbstract" name="upload_abstract_file" validate="Please upload the abstract file" />
													<?php $type = json_decode($abstract_file_types); ?>
													<h6><?= ($type[0] != '') ? strtoupper($type[0]) . " | " : '' ?><?= ($type[1] != '') ? ucfirst($type[1]) : '' ?><?= ($type[2] != '') ? " | " . ucfirst($type[2]) : '' ?></h6>
													<span id=abstractFileNameUploaded></span>

												</div>
											</div>
										<?php } ?>
									</div>



								</div>

								<div class="bottom-btn-wrap">
									<!-- <text>Lorem Ipsum Lorem Ipsum Lorem Ipsum</text> -->
									<div class="auth-last-btn" style="background: none;"><a href="javascript:void(0)" onclick="openPreview()"><img src="<?= _BASE_URL_ ?>images/eye.png" alt="" /></a></div>
									<a id="thirdBtnPrev" class="btn next-btn prev" type="third" style="display: none;"><i class="fa-solid fa-chevron-left"></i></a>
									<div class="auth-last-btn fix" style="background: none;">
										<!-- <a id="thirdBtnPrev" class="prev" type="third" style="display: none;cursor: pointer;">Previous</a> -->
										<a id="thirdBtn" class="next" style="display: none;cursor: pointer;background: none;">Submit</a>

									</div>
								</div>
							</div>

						</div>

					</div>
				</div>
			</section>
		</form>

	</main>
	<div class="checkout-main-wrap" id="previewModal">
		<div class="checkout-popup add-inclusion" style="max-width: 1100px;padding: 25px 25px;">
			<div class="card-details " style="width: 1000px">
				<div class="" id="details">

					<h4 style="color:#0d3859">Authorship Details</h4>
					<b>Name: </b><span id="author_name"></span><br>
					<b>Mobile: </b><span id="author_mobile"></span><br>
					<b>Institute Name: </b><span id="author_institute_name"></span>&nbsp;&nbsp;&nbsp;
					<b>Department: </b><span id="author_department"></span><br>
					<b>City: </b><span id="author_city"></span>&nbsp;&nbsp;&nbsp;
					<b>State: </b><span id="author_state_id"></span>&nbsp;&nbsp;&nbsp;
					<b>Country: </b><span id="author_country"></span><br>
					<hr>
					<!-- CO-AUTHOR DERAILS -->
					<div id="previewData">
					</div>

					<div id="abstractData">

					</div>

				</div>
				<a class="close-check"><span>&#10005;</span> close</a>

			</div>
		</div>
	</div>

	<script type="text/javascript">
		$('#abstract_author_country').on('change', function() {
			// var selectedValue = $(this).val();  // Get the selected value
			generateStateOptionList($('#abstract_author_country'));

		});



		function setAsPresenter(obj) {
			var presenter_email = $('#presenter_email_id').val();
			var presenter_mobile = $('#presenter_mobile').val();
			var presenter_title = $('#presenter_title').val();
			var presenter_first_name = $('#presenter_first_name').val();
			var presenter_last_name = $('#presenter_last_name').val();
			var presenter_country = $('#presenter_country').val();
			var presenter_state = $('#presenter_state').val();
			var presenter_city = $('#presenter_city').val();
			var presenter_pincode = $('#presenter_pincode').val();
			var presenter_institute = $('#presenter_institute').val();
			var presenter_department = $('#presenter_department').val();
			// alert(presenter_email);


			if ($(obj).prop('checked')) {


				if (presenter_email != '') {
					$('#abstract_author_email').val(presenter_email)
				}

				if (presenter_mobile != '') {
					$('#abstract_author_mobile').val(presenter_mobile);
				}

				if (presenter_title != '') {
					$('#abstract_author_title').val(presenter_mobile);

					$('input[name="abstract_author_title"][value="' + presenter_title + '"]').prop('checked', true);
				}

				if (presenter_first_name != '') {
					$('#abstract_author_first_name').val(presenter_first_name)
				}

				if (presenter_last_name != '') {
					$('#abstract_author_last_name').val(presenter_last_name)
				}

				if (presenter_country != '') {
					$('#abstract_author_country').val(presenter_country);
					generateStateOptionList($('#abstract_author_country'));
				}

				if (presenter_state != '') {
					$('#abstract_author_state').val(presenter_state)
				}

				if (presenter_city != '') {
					$('#abstract_author_city').val(presenter_city)
				}

				if (presenter_pincode != '') {
					$('#abstract_author_pincode').val(presenter_pincode)
				}

				if (presenter_institute != '') {
					$('#abstract_author_institute').val(presenter_institute)
				}

				if (presenter_department != '') {
					$('#abstract_author_department').val(presenter_department)
				}

				$('#isPresenter').val('Y')

			} else {

				// set author state
				$('#abstract_author_email').val('');
				$('#abstract_author_first_name').val('');
				$('#abstract_author_last_name').val('');
				$('input[name="abstract_author_title"]').prop('checked', false);


				// set author country 
				$('#abstract_author_country').val('')

				// set author state
				$('#abstract_author_state').val('')

				// set author city
				$('#abstract_author_city').val('')
				$('#abstract_author_pincode').val('')
				$('#abstract_author_institute').val('')
				$('#abstract_author_department').val('')

				$('#abstract_author_mobile').val('');


			}
		}

		function generateStateOptionList(obj, callback) {
			// var parent = $(obj).parent().closest(".com-country-state");

			var countryId = $(obj).val();

			$.ajax({
				type: "POST",
				url: jsBASE_URL + 'abstract_request.process.php',
				data: 'act=getStateOptionList&countryId=' + countryId,
				dataType: 'html',
				async: false,
				success: function(returnMessage) {
					//console.log(returnMessage+' country-state-data')
					if (returnMessage != '') {
						$('#abstract_author_state').empty();
						$('#abstract_author_state').empty().append(returnMessage);

						// $(parent).find("select[operationMode=stateControl]").html("");
						// $(parent).find("select[operationMode=stateControl]").html(returnMessage);
					}

					try {
						callback();
					} catch (e) {}
				}
			});
		}

		function getArrayFieldValues() {
			var values = [];
			var myObj = {};

			$(".coauther_details").each(function() {
				var fieldValue = $(this).val();
				var usefor = $(this).attr('use');
				if (fieldValue !== '') {
					console.log('key=' + usefor + ',values=' + fieldValue)
					myObj[usefor] = fieldValue;
					/*var keyValueArray = {
		  usefor: fieldValue
		 
		};*/

				}
				//values.push(fieldValue);
			});

			console.log(myObj);
			// return keyValueArray;
		}

		function openPreview() {

			var isPresenter = $("#isPresenter").val();

			// ================ TO GET PRESENTER DETAILS VALUES ==========================
			// var presenter_name =  $("#presenter_first_name").val() + " " + $("#presenter_last_name").val();
			// var presenter_email = $("#presenter_email").val();
			// var presenter_mobile = $("#presenter_mobile").val();
			// var presenter_country = $("#presenter_country option:selected").text();
			// var presenter_state = $("#presenter_state option:selected").text();
			// var presenter_city = $("#presenter_city").val();
			// var presenter_pin = $("#presenter_pincode").val();

			// ================ TO GET AUTHOR DETAILS VALUES ==========================
			var author_name = $("#abstract_author_first_name").val() + " " + $("#abstract_author_last_name").val();
			var author_email = $("#abstract_author_email").val();
			var author_mobile = $("#abstract_author_mobile").val();
			var author_country = $("#abstract_author_country option:selected").text();
			var author_state = $("#abstract_author_state option:selected").text();
			var author_city = $("#abstract_author_city").val();
			var author_pin = $("#abstract_author_pincode").val();

			$('#details').empty();

			var absCatTitle = $('#absCatTitle').val();
			var abssubCatTitle = $('#abssubCatTitle').val();
			var abstract_topic_id = $('#abstract_topic_id').val();
			var abstract_topic_title = $('#abstract_topic_id option:selected').text();
			// var result = abstract_topic_id.split('-');


			var msg = '';
			// ===================== PRESENTER DETAILS =====================
			// msg += "<div style='display:flex;'><div><h4 style='text-align: center;'>Presenter Details</h4>";
			// msg += "<b>Name: </b><span id='presenter_name'>" + presenter_name + "</span><br>";
			// msg += "<b>Email id: </b>" + presenter_email + "<br>";
			// msg += "<b>Mobile: </b>" + presenter_mobile + "<br>";
			// msg += "<b>City: </b><span id=''>" + presenter_city + "</span>&nbsp;&nbsp;&nbsp;";
			// msg += "<b>Postal Code: </b><span id=''>" + presenter_pin + "</span><br>";
			// msg += "<b>State: </b><span id=''>" + presenter_state + "</span>&nbsp;&nbsp;&nbsp;";
			// msg += "<b>Country: </b><span id=''></span>" + presenter_country + "<br></div>";

			// ===================== AUTHOR DETAILS =====================
			msg += "<h4 style='text-align: center;'>Author Details</h4>";
			msg += "<b>Name: </b><span id='presenter_name'>" + author_name + "</span><br>";
			msg += "<b>Email id: </b>" + author_email + "<br>";
			msg += "<b>Mobile: </b>" + author_mobile + "<br>";
			msg += "<b>City: </b><span id=''>" + author_city + "</span>&nbsp;&nbsp;&nbsp;";
			msg += "<b>Postal Code: </b><span id=''>" + author_pin + "</span><br>";
			msg += "<b>State: </b><span id=''>" + author_state + "</span>&nbsp;&nbsp;&nbsp;";
			msg += "<b>Country: </b><span id=''></span>" + author_country + "<br><hr>";

			// ===================== C0-AUTHOR DETAILS =====================
			var totalCoAuthor = $("#coAuthorCounts").val();
			var arrayValues = getArrayFieldValues();
			if (($('#abstract_coauthor_email').val() != "" || $('#abstract_coauthor_first_name').val() != "" || $('#abstract_coauthor_last_name').val() != "") || totalCoAuthor > 1) {
				// if (totalCoAuthor > 0) {
				// msg+='<div class="item">';
				msg += '<h4 style="text-align: center;">Co-Author Details</h4>';
				msg += '<div class="row m-0">';
				var i = 1;
				var values = [];

				var myObj = {};
				var i = 1;
				var k = 0;
				$(".coauther_details").each(function() {
					var fieldValue = $(this).val();
					var usefor = $(this).attr('use');
					console.log("usefor= " + usefor);
					console.log("fieldValue= " + fieldValue);
					if (k % 8 == 0 || k == 0) {
						msg += '<div class="col-md-12"><h5><b style="color:#86f5e2">Co-Author: ' + i + '</b></h5></div>';
					}
					k++;
					if (fieldValue !== '') {
						console.log('key' + usefor + 'values=' + fieldValue);

						console.log("k= " + k);
						myObj[usefor] = fieldValue;
						if (usefor == 'Country' || usefor == 'State') {
							var text = $(this).find("option:selected").text();
							msg += '<div class="col-md-6"><p><strong>' + usefor + ':</strong> ' + text + ' </p></div>';
						} else {
							msg += '<div class="col-md-6"><p><strong>' + usefor + ':</strong> ' + fieldValue + ' </p></div>';
						}
					}
					console.log("k= = " + k);
					// if (k % 8 == 0 && fieldValue !== '') {
					if (k % 8 == 0 && totalCoAuthor > 1) {
						i++;
					}
					console.log("i= = " + i);

				});

				msg += '</div>';
				msg += '</div><hr>';

				// $('#previewData').html(preview);
			}

			// ===================== ABSTRACT DETAILS =====================
			msg += "<h4 style='text-align: center;'>Abstract Details</h4>";
			msg += "<strong>Category: </strong>" + absCatTitle + "<br>";
			if (abssubCatTitle != undefined) {
				msg += "<strong>Sub Category: </strong>" + abssubCatTitle + "<br>";
			}
			// msg += "<strong>Topic: </strong>" + result[1] + "<br>";
			if (abstract_topic_title) {
				msg += "<strong>Topic: </strong>" + abstract_topic_title + "<br>";
			}
			msg += "<strong>Abstract Title: </strong>" + $('#abstract_title').val() + "<br>";
			$('textarea[spreadingroup]').each(function() {
				// Get the value of the textarea
				var textareaValue = $(this).val();
				var displayName = $(this).attr('title');

				if (textareaValue != undefined && textareaValue != '' && displayName != undefined && displayName != '') {

					msg += "<strong>" + displayName + ":</strong><br>";
					msg += textareaValue + "<br>";

				}

				// Do something with the textarea value
				console.log('title=' + displayName + 'val=' + textareaValue);
			});

			var consent_file_name = $('#original_consent_file_name').val();
			var abstract_file_name = $('#original_abstract_file_name').val();

			var tempConsentFileName = $("#temp_consent_filename").val();
			var tempAbstractFileName = $("#temp_abstract_filename").val();
			// alert(tempConsentFileName);
			if (consent_file_name != "" && consent_file_name != undefined) {
				msg += "<hr><strong>HOD Consent: </strong><a href='" + jsBASE_URL + "uploads/FILES.ABSTRACT.REQUEST/TEMP/" + tempConsentFileName + "' download  style='background-color:#8f909142;border-radius: 20px;padding: 3px 8px;' >" + consent_file_name + "</a><br>";
			}
			if (abstract_file_name != "" && abstract_file_name != undefined) {
				msg += "<hr><strong>Abstract File: </strong><a href='" + jsBASE_URL + "uploads/FILES.ABSTRACT.REQUEST/TEMP/" + tempAbstractFileName + "' download style='background-color:#8f909142;border-radius: 20px;padding: 3px 8px;' >" + abstract_file_name + "</a><br>";
			}
			console.log("msg= " + msg);

			$('#details').append(msg);
			// $('#abstractData').html(msg);

			//alert(result[1]);
			$('#abstitle').text($('#abstract_title').val());
			$('#cat').text(absCatTitle);
			$('#subcat').text(abssubCatTitle);
			// $('#topic').text(result[1]);
			$('#topic').text(abstract_topic_title);

			if (absCatTitle != '' /*&& abssubCatTitle != '' */ && msg != '') {
				$('#previewModal').show();
			}

		}

		/*$('#isCoauthor').click(function() {
			$('#allCoauthor').hide();
			// if ($(this).prop('checked')) {
			// 	$('.coauther_details').removeClass('mandatory');
			// }
			// else{
			// 	$('.coauther_details').addClass('mandatory');
			// }
		});*/

		function addCoauthor() {
			var accompanyCount = $('#coAuthorCounts').val();
			console.log("accompanyCount= " + accompanyCount);
			if (accompanyCount == 0) {
				$("#accordion-body-coauthor").show();

				$('#coAuthorCounts').val(1);
			} else {
				console.log("count= " + accompanyCount);
				// $("#coAuthor_first").show();
				var incrementedCount = Number(accompanyCount) + 1;
				$('#coAuthorCounts').val(incrementedCount);

				var accompanyCount = $('#coAuthorCounts').val();

				$("#accompanyCount").val(incrementedCount);
				// $('#coCount').text(incrementedCount);


				var newAccompany = $(".add_coathor:first").clone();
				$('#ca1').click();
				// $('.add_coathor').find('.accordion-button').addClass('collapsed');
				// $('.add_coathor').find('.accordion-collapse').attr('aria-expanded', false);
				$('.add_coathor').find('.accordion-collapse').removeClass('show');



				newAccompany.find('.accordion-button').attr('data-bs-target', '#collapseTwo' + accompanyCount);
				newAccompany.find('.accordion-button').removeClass('collapsed');
				newAccompany.find('.accordion-collapse').attr('id', 'collapseTwo' + accompanyCount);
				newAccompany.find('.accordion-collapse').addClass('show');

				newAccompany.find("span#coCount").text(accompanyCount);
				newAccompany.find("input[type='text']").val(''); // Clear the input field
				newAccompany.find("input[type='radio']").prop("checked", false);
				newAccompany.find("select").val(0);
				newAccompany.removeAttr('id');

				//$("#radioOption1").prop("checked", false);

				var fieldSerializeCount = Number(incrementedCount) - 1;

				//alert(fieldSerializeCount);


				newAccompany.find("input[id='abstract_coauthor_email']").attr("name", "abstract_coauthor_email[" + fieldSerializeCount + "]");
				newAccompany.find("input[id='abstract_coauthor_mobile']").attr("name", "abstract_coauthor_mobile[" + fieldSerializeCount + "]");
				newAccompany.find("input[id='abstract_coauthor_first_name']").attr("name", "abstract_coauthor_first_name[" + fieldSerializeCount + "]");
				newAccompany.find("input[id='abstract_coauthor_last_name']").attr("name", "abstract_coauthor_last_name[" + fieldSerializeCount + "]");
				newAccompany.find("input[id='abstract_coauthor_city']").attr("name", "abstract_coauthor_city[" + fieldSerializeCount + "]");
				newAccompany.find("input[id='abstract_coauthor_pincode']").attr("name", "abstract_coauthor_pincode[" + fieldSerializeCount + "]");
				newAccompany.find("input[id='abstract_coauthor_institute']").attr("name", "abstract_coauthor_institute[" + fieldSerializeCount + "]");
				newAccompany.find("input[id='abstract_coauthor_department']").attr("name", "abstract_coauthor_department[" + fieldSerializeCount + "]");

				newAccompany.find("select#abstract_coauthor_country").attr("name", "abstract_coauthor_country[" + fieldSerializeCount + "]");
				newAccompany.find("select#abstract_coauthor_state").attr("name", "abstract_coauthor_state[" + fieldSerializeCount + "]");


				newAccompany.find("input[type='text']").attr("countindex", fieldSerializeCount);
				newAccompany.find("input[type='radio']").attr("name", "abstract_coauthor_title[" + fieldSerializeCount + "]");

				newAccompany.find("input[type='radio'][name='abstract_coauthor_title[" + fieldSerializeCount + "]']").each(function(index, element) {

					var inputType = $(element).attr("type");
					var inputId = $(element).attr("id");
					//alert(inputId)


				});
				$("#accordion-body-coauthor").append(newAccompany);
				newAccompany.append('<button class="delete-coauthor-btn">Delete</button>');
			}

		}

		$("#add-coauthor-btn").on("click", function(e) {
			e.preventDefault();
			addCoauthor();
		});

		$("#removeCoauthor1").on("click", function(e) {
			e.preventDefault();
			$('#coAuthor_first input').val(''); // Clears text, number, and password input fields
			$('#coAuthor_first input[type="checkbox"], #coAuthor_first input[type="radio"]').prop('checked', false); // Unchecks checkboxes and radio buttons
			$('#coAuthor_first select').val(0); // Resets all dropdowns to default option
			$('#coAuthor_first textarea').val(''); // Clears all textareas

		});

		$("#abstract_topic_id").on("change", function() {
			var id = $("#abstract_topic_id").val();
			$.ajax({
				type: "POST",
				url: "abstract_request.process.php",
				data: {
					act: 'getAbstractDescription',
					id: id
				},
				// dataType: "html",
				// async: false,
				success: function(data) {

					if (data) {
						if (data == 0) {
							$('#abstract_topic_desc').html('');
							$('#abstract_topic_desc').css('padding', '');
						} else {
							$('#abstract_topic_desc').html(data);
							$('#abstract_topic_desc').css('padding', '10px 35px');

						}
					}


				},
				error: function(jqXHR, textStatus, errorThrown) {
					console.log(jqXHR + '--' + textStatus + '--' + errorThrown)
				}
			});

		})

		$("#accordion-body-coauthor").on("click", ".delete-coauthor-btn", function(e) {
			e.preventDefault();

			var id = $(this).attr('id');
			var accompanyCount = $('#coAuthorCounts').val();
			console.log($(this).parent());

			if (accompanyCount == 1) {
				$(this).parent().hide();
				$(this).parent().find('input[type=text], input[type=radio], select').val('');
				$('#coAuthorCounts').val(0);
			} else if (id === 'delete_first') {

				var $nextElement = $(this).parent().next();
				console.log("yes");
				$('#coAuthor_first').hide();
				$('#delete_first').hide();
				$('#coAuthor_first').find('input[type=text], input[type=radio], select').val('');

				$('#coAuthorCounts').val(Number(accompanyCount) - 1);

				//to reset the serial number of next co-authors after deleting present
				while ($nextElement.length > 0) {
					var count = $nextElement.find("span#coCount").text();
					$nextElement.find("span#coCount").text(Number(count) - 1);
					$nextElement = $nextElement.next();
				}

			} else {

				var $nextElement = $(this).parent().next();
				$(this).parent().remove();

				$('#coAuthorCounts').val(Number(accompanyCount) - 1);

				//to reset the serial number of next co-authors after deleting present
				while ($nextElement.length > 0) {
					var count = $nextElement.find("span#coCount").text();
					$nextElement.find("span#coCount").text(Number(count) - 1);
					$nextElement = $nextElement.next();
				}

				var accompanyAmount = $('#accompanyAmount').val();

				var amountIncludedDay = parseFloat(accompanyAmount) * parseInt(Number(accompanyCount) - 1);
				//$('#accompanyAmount').val(amountIncludedDay);

				$("#accompanyCount").attr("amount", amountIncludedDay);
				$("#accompanyCount").val(Number(accompanyCount) - 1);
			}



		});

		//onloading all fields of second and third section are desabled
		$("input[name='abstract_category']").prop('disabled', true);
		$("input[name='abstract_parent_type']").prop('disabled', true);
		$('#all_abstractDetails :input, #all_abstractDetails button').prop('disabled', true);



		$('#firstAthrBtn').click(function() {
			var flag = 0;
			$("div[id='accordion-body-presenter']  input[type='text'], div[id='accordion-body-presenter'] input[type='date'], div[id='accordion-body-presenter'] input[type='radio'], div[id='accordion-body-presenter'] select").each(function() {

				if ($(this).attr('type') === 'radio') {

					if (!$("input[type='radio'][name='abstract_author_title']:checked").length) {

						toastr.error('Please select the title', 'Error', {
							"progressBar": true,
							"timeOut": 5000,
							"showMethod": "slideDown",
							"hideMethod": "slideUp"
						});


						flag = 1;
						return false;
					}
				} else {
					var textBoxValue = $(this).val();
					if (textBoxValue === '') {
						toastr.error($(this).attr('validate'), 'Error', {
							"progressBar": true,
							"timeOut": 5000,
							"showMethod": "slideDown",
							"hideMethod": "slideUp"
						});
						flag = 1;
						return false;
					}
				}

			});

			console.log("flag= " + flag);
			if (flag == 0) {

				$('#collapseOne').removeClass('show');
				$('#coAuthorSection').show();
				$('#firstAthrBtn').hide();
				$('#firstBtn').show();
				//disable 1st section
				// $('#collapseOne :input, #collapseOne button').prop('disabled', true);
			}

		});

		//======================== First next button click function ===========================
		$('#firstBtn').click(function() {

			var flag = 0;

			$("div[id='accordion-body-presenter']  input[type='text'], div[id='accordion-body-presenter'] input[type='date'], div[id='accordion-body-presenter'] input[type='radio'], div[id='accordion-body-presenter'] select").each(function() {

				if ($(this).attr('type') === 'radio') {

					if (!$("input[type='radio'][name='abstract_author_title']:checked").length) {

						toastr.error('Please select the title', 'Error', {
							"progressBar": true,
							"timeOut": 5000,
							"showMethod": "slideDown",
							"hideMethod": "slideUp"
						});


						flag = 1;
						return false;
					}
				} else {
					var textBoxValue = $(this).val();
					if (textBoxValue === '') {
						toastr.error($(this).attr('validate'), 'Error', {
							"progressBar": true,
							"timeOut": 5000,
							"showMethod": "slideDown",
							"hideMethod": "slideUp"
						});
						flag = 1;
						return false;
					}
				}

			});

			//to set co-author fields mandatory
			$('.mandatory').each(function() {
				// Check if the input field is empty
				if ($(this).val().trim() === '') {
					$('#collapseOne').removeClass('show');
					$('#collapseTwo').addClass('show');
					toastr.error($(this).attr('validate'), 'Error', {
						"progressBar": true,
						"timeOut": 5000,
						"showMethod": "slideDown",
						"hideMethod": "slideUp"
					});
					flag = 1;
					return false;
				}

			});

			// $("div[id='accordion-body-coauthor'] input[type='text'][use='Institute'], div[id='accordion-body-coauthor'] input[type='text'][use='Department']").each(function (i) {
			// 	    var textBoxValue = $(this).val();
			// 	    if (textBoxValue === '') {
			// 	        toastr.error($(this).attr('validate'), 'Error', {
			// 	            "progressBar": true,
			// 	            "timeOut": 5000,
			// 	            "showMethod": "slideDown",
			// 	            "hideMethod": "slideUp"
			// 	        });
			// 	        flag = 1;
			// 	        return false; // Stop further iteration on error
			// 	    }
			// 	});

			console.log("flag= " + flag);
			if (flag == 0) {
				$('#collapseOne').removeClass('show');
				$('#collapseTwo').addClass('show');
				//disable 1st section
				$('#collapseTwo :input, #collapseTwo button').prop('disabled', true);
				$('#collapseOne :input, #collapseOne button').prop('disabled', true);
				$('.author-fold-1').addClass('blur_bw');
				//enable second section
				$("input[name='abstract_category']").prop('disabled', false);
				$("input[name='abstract_parent_type']").prop('disabled', false);
				$('.author-fold-2').removeClass('blur_bw');

				//============ FOR IHPBA 2025 (max 2 abstract and disable category by id) ========
				var countAbstract = <?= $countAbstract ?>;
				// if (countAbstract >= 2) {
				// 	$('#flexCheckDefault1').prop('disabled', true); //to disable category id=1

				// 	$('label[for="flexCheckDefault1"]').addClass('blur_bw');
				// }
				// =================================================================
			}
			if (flag == 0) {
				/*$('.author-details').removeClass("full-active");
				jQuery('.author-fold-2').addClass("full-active");*/
				var currentActive = $(".author-details.full-active");
				currentActive.removeClass("full-active");
				var previousElement = currentActive.next(".author-details");

				if (previousElement.length > 0) {
					previousElement.addClass("full-active");
					// previousElement.find(".next").show();
					//currentActive.find(".next").hide();
					// currentActive.find(".prev").hide();
				}
				$('#firstBtn').hide();
				$('#secondBtn').show();
				$('#secondBtnPrev').show();
			}
		});

		//=========================== second next button click function ==================================
		$(document).on("click", "#secondBtn", function() {
			var flag = 0;
			$("div[id='abstract_details'] input[type='radio'], div[id='abstract_details'] select").each(function() {

				if ($(this).attr('type') === 'radio') {
					//alert($(this).attr('name'));
					if ($(this).attr('name') == 'abstract_category') {
						if (!$("input[name='" + $(this).attr('name') + "']:checked").length) {
							toastr.error('Please select category', 'Error', {
								"progressBar": true,
								"timeOut": 3000, // 3 seconds
								"showMethod": "slideDown", // Animation method for showing
								"hideMethod": "slideUp" // Animation method for hiding
							});

							flag = 1;
							return false;
						}
					}

					//const isCheckedFaculty = document.getElementById('flexCheckDefault1').checked;

					if ($(this).attr('name') == 'abstract_parent_type') {
						if ((!$("input[name='" + $(this).attr('name') + "']:checked").length) /* && !isCheckedFaculty*/) {

							toastr.error('Please select submission sub category', 'Error', {
								"progressBar": true,
								"timeOut": 3000, // 3 seconds
								"showMethod": "slideDown", // Animation method for showing
								"hideMethod": "slideUp" // Animation method for hiding
							});

							flag = 1;
							return false;

						}
					}
				}


			});
			console.log("flagAFTER= " + flag);
			if (flag == 0) {

				//alert('succ');
				$('#secondBtn').hide();
				$('#secondBtnPrev').hide();
				$('#thirdBtn').show();
				$('#thirdBtnPrev').show();

				//enable third section
				$('#all_abstractDetails :input, #all_abstractDetails button').prop('disabled', false);
				$('.author-fold-3').removeClass('blur_bw');

				//disable second section
				$("input[name='abstract_category']").prop('disabled', true);
				$("input[name='abstract_parent_type']").prop('disabled', true);
				$('.author-fold-2').addClass('blur_bw');
				// $('#abstract_details_2 input').prop('disabled', true);

				var currentActive = $(".author-details.full-active");
				currentActive.removeClass("full-active");
				var previousElement = currentActive.next(".author-details");

				if (previousElement.length > 0) {
					previousElement.addClass("full-active");
				}
			}

		});

		// after clicking any category radio button Find all radio buttons of sub category and remove their checked state
		$('.trigger-subCategory').click(function() {
			$("input[name='abstract_parent_type']").prop('checked', false);
		});

		//=========================== Third next button click function ==================================
		$('#thirdBtn').click(function() {
			var flag = 0;
			$("div[id='abstract_details_2'] input[type='radio'], div[id='abstract_details_2'] select, div[id='abstract_details_2'] textarea ").each(function(index) { // input[type='file']
				if ($(this).is('select')) {
					if ($.trim($(this).val()) == '') {

						var msg = $(this).attr('validate');
						toastr.error(msg, 'Error', {
							"progressBar": true,
							"timeOut": 3000,
							"showMethod": "slideDown",
							"hideMethod": "slideUp"
						});

						flag = 1;
						return false;

					}
				} else {

					if (!$(this).hasClass('hideitem')) {
						//alert(index);
						if ($.trim($(this).val()) == '') {

							var idVal = $(this).attr('id').split('_');;

							$('#collapseOne1' + idVal[1]).addClass('show');

							var msg = $(this).attr('validate');
							toastr.error(msg, 'Error', {
								"progressBar": true,
								"timeOut": 3000,
								"showMethod": "slideDown",
								"hideMethod": "slideUp"
							});

							flag = 1;
							return false;

						}
					}
				}


			});
			var totalWordEnteredTitle = $('[use="total_word_entered"]:first').text();
			var totalWordLimitTitle = $('[use="total_word_limit"]:first').text();
			var totalWordEnteredabstract = $('[use="total_word_entered"]:eq(1)').text();
			var totalWordLimitAbstract = $('[use="total_word_limit"]:eq(1)').text();
			console.log(totalWordLimitTitle + " , " + totalWordLimitAbstract);
			console.log(totalWordEnteredTitle + " , " + totalWordEnteredabstract);
			if (Number(totalWordEnteredTitle) > Number(totalWordLimitTitle)) {
				toastr.error('Title should be within ' + totalWordLimitTitle + ' words', 'Error', {
					"progressBar": true,
					"timeOut": 3000,
					"showMethod": "slideDown",
					"hideMethod": "slideUp"
				});
				flag = 1;
				return false;

			}
			if (Number(totalWordEnteredabstract) > Number(totalWordLimitAbstract)) {
				toastr.error('Abstract details should be within ' + totalWordLimitAbstract + ' words', 'Error', {
					"progressBar": true,
					"timeOut": 3000,
					"showMethod": "slideDown",
					"hideMethod": "slideUp"
				});
				flag = 1;
				return false;

			}

			if (flag == 0) {
				//alert('succ');
				$("input[name='abstract_category']").prop('disabled', false);
				$("input[name='abstract_parent_type']").prop('disabled', false);
				$('#collapseTwo :input, #collapseTwo button').prop('disabled', false);
				$('#collapseOne :input, #collapseOne button').prop('disabled', false);


				$("#abstractRequestForm").submit();
				$("#thirdBtn").css('pointer-events', 'none');
				$("#thirdBtn").css('filter', 'blur(1px)');
			}

		});


		//=========================== Third PREVIOUS button click function ==================================
		$('#thirdBtnPrev').click(function() {
			//disable third section
			$('#all_abstractDetails :input, #all_abstractDetails button').prop('disabled', true);
			$('.author-fold-3').addClass('blur_bw');
			//enable second section
			$("input[name='abstract_category']").prop('disabled', false);
			$("input[name='abstract_parent_type']").prop('disabled', false);
			$('.author-fold-2').removeClass('blur_bw');

			//============ FOR IHPBA 2025 (max 2 abstract) ========
			var countAbstract = <?= $countAbstract ?>;
			if (countAbstract >= 2) {
				$('#flexCheckDefault1').prop('disabled', true);
				$('#flexCheckDefault1').prop('disabled', true);
			}
			// =================================================================
		})

		//=========================== second PREVIOUS next button click function ==================================
		$('#secondBtnPrev').click(function() {
			//enable 1st section
			$('#firstAthrBtn').hide();
			$('#collapseTwo :input, #collapseTwo button').prop('disabled', false);
			$('#collapseOne :input, #collapseOne button').prop('disabled', false);
			$('.author-fold-1').removeClass('blur_bw');


			//disable second section
			$("input[name='abstract_category']").prop('disabled', true);
			$("input[name='abstract_parent_type']").prop('disabled', true);
			$('.author-fold-2').addClass('blur_bw');
		})

		function getTopicWiseNomination(obj) {
			var topic = $(obj).val();
			var topicId = topic.split('-')[0].trim(); // Extracts id before the first hyphen

			var catId = $('input[name="abstract_category"]:checked').val();
			// alert(catId);

			$.ajax({
				type: "POST",
				url: "abstract_request.process.php",
				data: {
					act: 'getNominationByTopic',
					catId: catId,
					topicId: topicId
				},

				async: false,
				success: function(JSONObject) {
					// alert(JSONObject)
					if (JSONObject) {
						var nominationIdsArray = JSON.parse(JSONObject);
						if (nominationIdsArray.length > 0) {
							for (let i = 0; i < nominationIdsArray.length; i++) {
								$('#accordianNomination').show();
								$('#nomination_holder_' + nominationIdsArray[i]).show();
								$('#nomination_holder_' + nominationIdsArray[i]).addClass('wasActive');

							}
						} else {
							$('.wasActive').hide();
							var ishidden = $('#accordianNomination').attr('ishidden');
							if (ishidden == 0) {
								$('#accordianNomination').hide();
							}

						}

					}


				},
				error: function(jqXHR, textStatus, errorThrown) {
					console.log(jqXHR + '--' + textStatus + '--' + errorThrown)
				}
			});

		}

		function abstractTypeChange(obj, cat_id, fieldArr, nomination_ids) {

			// if (cat_id == 1) {
			// 	$('#faculty_upload').show();
			// 	$('#formFileAbstract').removeClass('hideitem');
			// } else {
			// 	$('#faculty_upload').hide();
			// 	$('#formFileAbstract').addClass('hideitem');

			// }

			if (nomination_ids) {
				for (var i = 0; i < nomination_ids.length; i += 2) {
					$('#accordianNomination').show();
					$('#accordianNomination').attr('ishidden', '1');
					$('.wasActive').hide();
					$('.catActive').hide();
					$('#nomination_holder_' + nomination_ids[i]).show();
					$('#nomination_holder_' + nomination_ids[i]).addClass('catActive');
				}
			} else {
				$('#accordianNomination').hide();
				$('#accordianNomination').attr('ishidden', '0');

				$('.nomination_list').hide();
			}

			var submissionSubType = $(obj).val();

			//alert($(obj).attr('title'));
			fieldArr = fieldArr.split(",");

			$('.commn-absfields').hide();
			localStorage.setItem("subCat", submissionSubType);
			$('#abstract_description').addClass('hideitem');

			$('.hidesub').hide();
			$('.hideFieldVal').hide();

			//alert(submissionSubType);
			// if (submissionSubType === "Free Paper") {

			// 	$('.submissionTypeRadio' + cat_id).show();
			// 	var submissionTypeContainer = $("li[use=leftAccordionSubmissionType]");
			// 	$(submissionTypeContainer).find("input[type=radio]").prop("disabled", false);
			// 	$(submissionTypeContainer).find("input[type=radio]").prop("checked", false);

			// 	$('.abs-submission-type').show();
			// 	$('.abs-sub-submission-type').show();


			// 	$('#upload_abstract_file').parent().show();
			// 	$('#upload_abstract_file').addClass('hideitem')

			// }

			if (submissionSubType === "Poster Presentation") {
				$('#topicDetails').hide();
			} else {

				//alert(cat_id);
				$('#absCatTitle').val($(obj).attr('title'));
				$('.commn').addClass('hideitem');
				$('.submissionTypeRadio' + cat_id).show();
				$('#collapseTwo2SubCat').addClass('show');
				$('#wordCountList').show();
				var submissionTypeContainer = $("div[class=accordion-body]");
				$(submissionTypeContainer).find("input[type=radio]").prop("disabled", false);
				//$(submissionTypeContainer).find("input[type=radio]").prop("checked",false);

				$('.abs-submission-type').show();
				$('.abs-sub-submission-type').show();

				//disableAllFileds($("li[use=abstractPresenterDetails]"));

				// $('#upload_abstract_file').parent().show();
				// $('#upload_abstract_file').removeClass('hideitem')


				localStorage.setItem("subSType", '');
				localStorage.setItem("subType", '');

				$('#topicDetails').show();


				for (var i = 0; i < fieldArr.length; i++) {
					//alert(fieldArr[i]);
					$('#fields_' + fieldArr[i]).show();
					$('#fieldVal_' + fieldArr[i]).removeClass("hideitem");
				}
			}

			if (submissionSubType !== '') {


				$.ajax({
					type: "POST",
					url: "abstract_request.process.php",
					data: {
						action: 'generateTopic',
						topic: cat_id
					},
					dataType: "html",
					async: false,
					success: function(JSONObject) {
						if (JSONObject) {
							if (JSONObject.trim() == 'empty') {
								$('#topicDetails').hide();
								//document.getElementById("abstract_topic_id").required = false;
								$('#abstract_topic_id').html("")
								$('#abstract_topic_id').attr('required', false);
							} else {
								$('#topicDetails').show();
								$('#abstract_topic_id').html(JSONObject)
							}
						}


					},
					error: function(jqXHR, textStatus, errorThrown) {
						console.log(jqXHR + '--' + textStatus + '--' + errorThrown)
					}
				});



				$.ajax({
					type: "POST",
					url: "abstract_request.process.php",
					data: {
						action: 'checkSubCat',
						cat_id: cat_id,
						delegateId: '<?php echo $delegateId; ?>'
					},
					dataType: "html",
					async: false,
					success: function(JSONObject) {
						if (JSONObject) {

						}


					},
					error: function(jqXHR, textStatus, errorThrown) {
						console.log(jqXHR + '--' + textStatus + '--' + errorThrown)
					}
				});
			}
		}

		function abstractSubmissionType(obj, cat_id, submission_id) {
			var submissionType = $(obj).val();
			// set data in localstorage
			localStorage.setItem("subType", submissionType);
			//alert('cat='+cat_id+'sub='+submission_id+'submissionType='+submissionType);
			//$('.hidesub').hide();
			//$('.hideFieldVal').hide();

			if (cat_id != '' && submission_id != '') {

				$('#abssubCatTitle').val($(obj).attr('title'));
				$.ajax({
					type: "POST",
					url: "abstract_request.process.php",
					data: {
						action: 'generateCatSubTopic',
						cat_id: cat_id,
						submission_id: submission_id
					},
					dataType: "html",
					async: false,
					success: function(JSONObject) {
						if (JSONObject.trim() == 'empty') {
							$('#topicDetails').hide();
							//document.getElementById("abstract_topic_id").required = false;
							$('#abstract_topic_id').attr('required', false);
						} else {
							$('#topicDetails').show();
							$('#abstract_topic_id').html(JSONObject)
						}

					},
					error: function(jqXHR, textStatus, errorThrown) {
						console.log(jqXHR + '--' + textStatus + '--' + errorThrown)
					}
				});
			}
		}

		function truncateWords(str, no_words) {
			//return str.split(" ").splice(0,no_words).join(" ");
			const words = str.split(' ');

			if (no_words >= words.length) {
				return str;
			}

			const truncated = words.slice(0, no_words);
			return `${truncated.join(' ')}`;
		}


		function truncateCharacters(str, no_words) {
			//return str.split(" ").splice(0,no_words).join(" ");
			const words = str.length;

			if (no_words >= words.length) {
				console.log('str=' + str);
				return str;
			}

			//const truncated = words.slice(0, no_words);
			//return `${truncated.join(' ')}`;
		}

		function countWords(stringValue) {
			//console.log("Length="+stringValue.length);
			s = stringValue;
			s = s.replace(/(^\s*)|(\s*$)/gi, "");
			s = s.replace(/[ ]{2,}/gi, " ");
			s = s.replace(/\n /, "\n");

			return s.split(' ').length;
		}

		function wordLimitCounter(obj) {
			var totalWordCount = 0;
			var totalCharacterCount = 0;
			var parent = $("div[use=abstractDetails]");
			var wordCount = parseInt($(obj).attr("wordcount"));

			var group = $(obj).attr("spreadInGroup");
			var showWordCount = $(parent).find("span[use='" + $(obj).attr("displayText") + "']");
			var wordLimit = parseInt($(showWordCount).attr('limit'));
			var count = wordLimit;
			var totalCharacter = '';

			var word_type = '<?= $cfg['ABSTRACT.TOTAL.WORD.TYPE'] ?>';

			//console.log(count);
			$(parent).find("textarea[spreadInGroup='" + group + "']").each(function() {
				if ($(this).val() != "") {

					totalWordCount = parseInt(totalWordCount) + parseInt(countWords($(this).val()));
					totalCharacterCount = parseInt(totalCharacterCount) + parseInt($(this).val().length);

					if ($("textarea[spreadInGroup='" + group + "']").length > 1) {

						count = wordLimit - totalWordCount

						countCharacter = parseInt(wordLimit) - parseInt(totalCharacterCount);

						totalCharacter += $(this).val();

						//console.log('countCharacter='+countCharacter);

					}

					if (word_type == 'word') {
						if (totalWordCount > wordLimit) {
							// prevent max word
							//$(obj).val(truncateWords($.trim($(obj).val()),wordLimit));
							$(showWordCount).css("color", "#D41000");
							// $(this).val(truncateWords($.trim($(this).val()), count));
						} else {
							$(showWordCount).css("color", "");
						}
					} else {
						if (totalCharacterCount > wordLimit) {
							console.log(1212);
							console.log('totalCharacter=' + totalCharacterCount + 'wordLimit=' + wordLimit);

							$(showWordCount).css("color", "#D41000");
							//$(this).val(truncateCharacters($.trim($(this).val()),countCharacter));
							if ($(this).val().length >= wordLimit) {
								$(this).val($(this).val().substring($(this).val(), wordLimit));
							} else if (countCharacter < wordLimit) {

								$(this).val($(this).val().substring(0, countCharacter));
								//$(this).val("");
							} else {
								//alert(2);
								$(this).val("");
							}

						} else {
							$(showWordCount).css("color", "");
						}
					}


				}
			});

			$(showWordCount).find("span[use=total_word_entered]").text("");

			if (word_type == 'word') {

				$(showWordCount).find("span[use=total_word_entered]").text(totalWordCount);
			} else {
				$(showWordCount).find("span[use=total_word_entered]").text(totalCharacterCount);
			}



		}

		$("textarea[checkFor=wordCount]").keyup(function() {
			wordLimitCounter(this);
		});
		$("textarea[checkFor=wordCount]").blur(function() {
			wordLimitCounter(this);
		});

		$('#abstract_title').on('keyup', function() {
			console.log('abstarct=' + this.value.length);
			jQuery('#abstract_total_word_entered').text(this.value.length);
		});

		$(document).ready(function() {

			function uploadFile(file, dynamic_name_prefix, prevFile) {

				var formData = new FormData();
				formData.append('file', file);
				formData.append('dynamic_name_prefix', dynamic_name_prefix);
				formData.append('prevFile', prevFile);

				$.ajax({
					url: 'abstract.user.entrypoint.process.php',
					type: 'POST',
					data: formData,
					contentType: false,
					processData: false,
					success: function(response) {
						console.log("RES= " + response);
					},
					error: function(xhr, status, error) {
						console.error("Error: ", error);
					}
				});
			}

			function deleteTempFile(tempFile) {

				var formData = new FormData();
				formData.append('tempFile', tempFile);
				formData.append('delete', "1");

				$.ajax({
					url: 'abstract.user.entrypoint.process.php',
					type: 'POST',
					data: formData,
					contentType: false,
					processData: false,
					success: function(response) {
						console.log("RES= " + response);
					},
					error: function(xhr, status, error) {
						console.error("Error: ", error);
					}
				});
			}

			$('#formFileAbstract').on('change', function() {
				var file = this.files[0];
				var flag = 0;
				if (file) {
					var fileSize = file.size; // in bytes
					var fileType = file.type;

					var validExtensions = new Array();
					var abstract_file_types = $('#abstract_file_types').val();
					if (abstract_file_types.includes("pdf")) {
						validExtensions.push("pdf");
					}
					if (abstract_file_types.includes("image")) {
						validExtensions.push("jpg");
						validExtensions.push("jpeg");
						validExtensions.push("png");
					}
					if (abstract_file_types.includes("word")) {
						validExtensions.push("doc");
						validExtensions.push("docx");
					}
					var jsonString = JSON.stringify(validExtensions).replace(/[\[\]"]/g, '');
					var fileTypeErr = "Only " + jsonString + " files are allowed";
					// var validExtensions = ["doc", "pdf", "docx"]
					var fileName = file.name.split('.').pop();

					console.log(fileName);

					if (fileSize > 5 * 1024 * 1024) {

						var prevFile = $('#temp_abstract_filename').val();
						if (prevFile != '') {
							deleteTempFile(prevFile);
						}
						$('#temp_abstract_filename').val('');
						$('#original_abstract_file_name').val('');
						$('#abstractFileNameUploaded').text('');


						toastr.error('File size exceeds 5MB limit.', 'Error', {
							"progressBar": true,
							"timeOut": 5000,
							"showMethod": "slideDown",
							"hideMethod": "slideUp"
						});

						flag = 1;
						this.value = ''; // Clear the file input
						return;
					}

					if (validExtensions.indexOf(fileName) == -1) {
						var prevFile = $('#temp_abstract_filename').val();
						if (prevFile != '') {
							deleteTempFile(prevFile);
						}
						$('#temp_abstract_filename').val('');
						$('#original_abstract_file_name').val('');
						$('#abstractFileNameUploaded').text('');

						toastr.error(fileTypeErr, 'Error', {
							"progressBar": true,
							"timeOut": 4000,
							"showMethod": "slideDown",
							"hideMethod": "slideUp"
						});

						flag = 1;
						this.value = '';
						return;
					}
					// File is valid
					$('#fileError').text('');
				} else {
					var prevFile = $('#temp_abstract_filename').val();
					if (prevFile != '') {
						deleteTempFile(prevFile);
					}
					$('#temp_abstract_filename').val('');
					$('#original_abstract_file_name').val('');
					$('#abstractFileNameUploaded').text('');


					toastr.error('Please select a file to upload.', 'Error', {
						"progressBar": true,
						"timeOut": 4000,
						"showMethod": "slideDown",
						"hideMethod": "slideUp"
					});
					flag = 1;
				}

				if (flag == 0) {

					var sessionId = $('#sessionId').val();
					var d = new Date();
					var uploadTime = d.getTime();
					var prevFile = $('#temp_abstract_filename').val();
					// createDynamicFileName
					var dynamicFileName = 'ABSTRACT_' + sessionId + uploadTime + '_' + file['name'];
					// alert(dynamicFileName);
					$('#temp_abstract_filename').val(dynamicFileName);
					$('#original_abstract_file_name').val(file['name']);
					$('#abstractFileNameUploaded').text(file['name']);

					uploadFile(file, 'ABSTRACT_' + sessionId + uploadTime, prevFile); // Upload the file
					toastr.success('File uploaded successfully.', 'Success', {
						"progressBar": true,
						"timeOut": 2000,
						"showMethod": "slideDown",
						"hideMethod": "slideUp"
					});

				}


			});


			$('#formFileHod').on('change', function() {
				var file = this.files[0];

				var flag = 0;
				if (file) {
					var fileSize = file.size; // in bytes
					var fileType = file.type;

					var validExtensions = new Array();
					var hod_consent_file_types = $('#hod_consent_file_types').val();
					if (hod_consent_file_types.includes("pdf")) {
						validExtensions.push("pdf");
					}
					if (hod_consent_file_types.includes("image")) {
						validExtensions.push("jpg");
						validExtensions.push("jpeg");
						validExtensions.push("png");
					}
					if (hod_consent_file_types.includes("word")) {
						validExtensions.push("doc");
						validExtensions.push("docx");
					}
					var jsonString = JSON.stringify(validExtensions).replace(/[\[\]"]/g, '');
					var fileTypeErr = "Only " + jsonString + " files are allowed";
					// var validExtensions = ["jpg", "pdf", "jpeg", "gif", "png", "pdf"]
					var fileName = file.name.split('.').pop();

					console.log(fileName);

					if (fileSize > 5 * 1024 * 1024) {
						var prevFile = $('#temp_consent_filename').val();
						if (prevFile != '') {
							deleteTempFile(prevFile);
						}
						$('#temp_consent_filename').val('');
						$('#original_consent_file_name').val('');
						$('#concentFileNameUploaded').text('');


						toastr.error('File size exceeds 5MB limit.', 'Error', {
							"progressBar": true,
							"timeOut": 4000,
							"showMethod": "slideDown",
							"hideMethod": "slideUp"
						});
						flag = 1;
						this.value = ''; // Clear the file input
						return;
					}

					if (validExtensions.indexOf(fileName) == -1) {
						var prevFile = $('#temp_consent_filename').val();
						if (prevFile != '') {
							deleteTempFile(prevFile);
						}
						$('#temp_consent_filename').val('');
						$('#original_consent_file_name').val('');
						$('#concentFileNameUploaded').text('');


						toastr.error(fileTypeErr, 'Error', {
							"progressBar": true,
							"timeOut": 4000,
							"showMethod": "slideDown",
							"hideMethod": "slideUp"
						});

						flag = 1;
						this.value = '';
						return;
					}
					// File is valid
					$('#fileError').text('');
				} else {
					var prevFile = $('#temp_consent_filename').val();
					if (prevFile != '') {
						deleteTempFile(prevFile);
					}
					$('#temp_consent_filename').val('');
					$('#original_consent_file_name').val('');
					$('#concentFileNameUploaded').text('');


					toastr.error('Please select a file to upload.', 'Error', {
						"progressBar": true,
						"timeOut": 4000,
						"showMethod": "slideDown",
						"hideMethod": "slideUp"
					});
					flag = 1;
				}

				if (flag == 0) {
					var sessionId = $('#sessionId').val();
					var d = new Date();
					var uploadTime = d.getTime();
					var prevFile = $('#temp_consent_filename').val();
					// createDynamicFileName
					var dynamicFileName = 'CONSENT_' + sessionId + uploadTime + '_' + file['name'];
					// alert(dynamicFileName);
					$('#temp_consent_filename').val(dynamicFileName);
					$('#original_consent_file_name').val(file['name']);
					$('#concentFileNameUploaded').text(file['name']);

					uploadFile(file, 'CONSENT_' + sessionId + uploadTime, prevFile); // Upload the file

					toastr.success('File uploaded successfully.', 'Success', {
						"progressBar": true,
						"timeOut": 2000,
						"showMethod": "slideDown",
						"hideMethod": "slideUp"
					});
				}
			});
		});
		$(".bar-icon").click(function() {
			$(".left-menu").toggleClass("left-menu-slide");
			$(".bar-icon").toggleClass("bar-icon-slide")
		});
	</script>

</body>

</html>
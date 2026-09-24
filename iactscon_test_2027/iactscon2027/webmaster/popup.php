<?php
include_once('init.php');
include_once("icons.php");
include_once('init.php');
include_once('js-source.php');
$cfg['SECTION_BASE_URL'] = "https://ruedakolkata.com/natcon_25/conference_registration/webmaster/";
$WorkshopId   = isset($_POST['workshop_id']) ? $_POST['workshop_id'] : null;
$RegClassId   = isset($_POST['registration_classification_id']) ? $_POST['registration_classification_id'] : null;
$title   = isset($_POST['title']) ? $_POST['title'] : null;
$classification   = isset($_POST['classification']) ? $_POST['classification'] : null;
$hotelId   = isset($_POST['hotelId']) ? $_POST['hotelId'] : null;
$hotelTarrifId   = isset($_POST['hotelTarrifId']) ? $_POST['hotelTarrifId'] : null;
$packageId   = isset($_POST['packageId']) ? $_POST['packageId'] : null;
$roomId   = isset($_POST['roomId']) ? $_POST['roomId'] : null;
$editHotelId   = isset($_POST['editHotelId']) ? $_POST['editHotelId'] : null;

// // Enable error reporting for debugging
// ini_set('display_errors', 1);
// error_reporting(E_ALL);

// // Safely get POST variables
// $WorkshopId   = isset($_POST['workshop_id']) ? $_POST['workshop_id'] : null;
// $RegClassId   = isset($_POST['registration_classification_id']) ? $_POST['registration_classification_id'] : null;

// // Debugging: check if the data is coming through
// echo "<pre>POST received:\n";
// var_dump($_POST);
// var_dump($WorkshopId, $RegClassId);
?>
<style>
    .previewImage {
    max-width: 200px;
    max-height: 200px;
    display: block;
    border: 2px solid red;
}
    </style>
<div class="pop_up_wrap">
    <div class="pop_up_inner">
        <!-- profile pop up -->
        <div class="pop_up_body" id="profile">
            <div class="profile_pop_up">
                <div class="profile_pop_left">
                    <div class="profile_left_box text-center ">
                        <div class="regi_img_circle m-auto">
                            <!-- <img src="" alt="" class="w-100 h-100"> -->
                            <span>AM</span>
                        </div>
                        <h5>Dr. Asim Kumar Majumdar</h5>
                        <h6>NATCON 2025-1176-0638</h6>
                        <div class="regi_type justify-content-center">
                            <span class="badge_padding badge_primary">Delegate</span>
                            <span class="badge_padding badge_secondary">Early Bird</span>
                        </div>
                    </div>
                    <div class="profile_left_box">
                        <h4>Contact Info</h4>
                        <ul>
                            <li>
                                <?php  ?>
                                <p>
                                    <b>Email</b>
                                    <span>drasim53@gmail.com</span>
                                </p>
                            </li>
                            <li>
                                <?php call(); ?>
                                <p>
                                    <b>Phone</b>
                                    <span>9674833617</span>
                                </p>
                            </li>
                            <li>
                                <?php calendar(); ?>
                                <p>
                                    <b>Registered On</b>
                                    <span>19/11/2025</span>
                                </p>
                            </li>
                        </ul>
                    </div>
                    <div class="profile_left_box">
                        <h4>Address</h4>
                        <ul>
                            <li>
                                <?php address(); ?>
                                <p>
                                    <b>Location</b>
                                    <span>GSVM MEDICAL COLLEGE KANPUR, KANPUR, Uttar Pradesh, 208002, INDIA</span>
                                </p>
                            </li>
                            <li>
                                <i class="fal fa-tags"></i>
                                <p>
                                    <b>Tags</b>
                                    <span>VIP</span>
                                </p>
                            </li>
                        </ul>
                    </div>
                    <div class="profile_left_box">
                        <div class="profile_uniq_sque">
                            Unique Sequence<span>#82680594</span>
                        </div>
                    </div>
                </div>
                <div class="profile_pop_right">
                    <div class="profile_pop_right_heading">
                        <span>Registration Details</span>
                        <p>
                            <a href="javascript:void(null)" class="icon_hover badge_primary action-transparent"><?php export(); ?></a>
                            <a href="javascript:void(null)" class="popup_close icon_hover badge_danger action-transparent"><?php close(); ?></a>
                        </p>
                    </div>
                    <div class="profile_pop_right_body">
                        <ul class="profile_payments_grid_ul">
                            <li>
                                <h6>Total Amount</h6>
                                <h5>₹ 14,000</h5>
                            </li>
                            <li>
                                <h6>Payment Status</h6>
                                <h5><span class="mi-1 badge_padding badge_success d-flex w-max-con text-uppercase"><?php paid(); ?>Paid</span></h5>
                            </li>
                            <li>
                                <h6>Invoice No.</h6>
                                <h5>NATCON 2025-000334</h5>
                                <small class="text-muted">UPI TXN ID:00000001</small>
                            </li>
                        </ul>
                        <div class="service_breakdown_wrap">
                            <h4><?php windowi(); ?>Service Breakdown</h4>
                            <div class="table_wrap">
                                <table>
                                    <thead>
                                        <tr>
                                            <th>Service / Item</th>
                                            <th>Category</th>
                                            <th class="text-right">Amount</th>
                                            <th class="text-right">Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td>
                                                Conference Registration<br>
                                                <small class="text-muted">NATCON 2025-000325</small>
                                            </td>
                                            <td>
                                                Delegate <span class="badge_padding badge_dark">OFFLINE (FRONT)</span>
                                            </td>
                                            <td class="text-right">
                                                ₹ 6,000
                                            </td>
                                            <td class="text-right">
                                                <span class="mi-1 badge_padding badge_success  w-max-con text-uppercase"><?php paid(); ?>Paid</span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                Conference Registration<br>
                                                <small class="text-muted">NATCON 2025-000325</small>
                                            </td>
                                            <td>
                                                Delegate <span class="badge_padding badge_dark">OFFLINE (FRONT)</span>
                                            </td>
                                            <td class="text-right">
                                                ₹ 6,000
                                            </td>
                                            <td class="text-right">
                                                <span class="mi-1 badge_padding badge_success  w-max-con text-uppercase"><?php paid(); ?>Paid</span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                Conference Registration<br>
                                                <small class="text-muted">NATCON 2025-000325</small>
                                            </td>
                                            <td>
                                                Delegate <span class="badge_padding badge_dark">OFFLINE (FRONT)</span>
                                            </td>
                                            <td class="text-right">
                                                ₹ 6,000
                                            </td>
                                            <td class="text-right">
                                                <span class="mi-1 badge_padding badge_success  w-max-con text-uppercase"><?php paid(); ?>Paid</span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                Conference Registration<br>
                                                <small class="text-muted">NATCON 2025-000325</small>
                                            </td>
                                            <td>
                                                Delegate <span class="badge_padding badge_dark">OFFLINE (FRONT)</span>
                                            </td>
                                            <td class="text-right">
                                                ₹ 6,000
                                            </td>
                                            <td class="text-right">
                                                <span class="mi-1 badge_padding badge_success  w-max-con text-uppercase"><?php paid(); ?>Paid</span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                Conference Registration<br>
                                                <small class="text-muted">NATCON 2025-000325</small>
                                            </td>
                                            <td>
                                                Delegate <span class="badge_padding badge_dark">OFFLINE (FRONT)</span>
                                            </td>
                                            <td class="text-right">
                                                ₹ 6,000
                                            </td>
                                            <td class="text-right">
                                                <span class="mi-1 badge_padding badge_success  w-max-con text-uppercase"><?php paid(); ?>Paid</span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                Conference Registration<br>
                                                <small class="text-muted">NATCON 2025-000325</small>
                                            </td>
                                            <td>
                                                Delegate <span class="badge_padding badge_dark">OFFLINE (FRONT)</span>
                                            </td>
                                            <td class="text-right">
                                                ₹ 6,000
                                            </td>
                                            <td class="text-right">
                                                <span class="mi-1 badge_padding badge_success  w-max-con text-uppercase"><?php paid(); ?>Paid</span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                Conference Registration<br>
                                                <small class="text-muted">NATCON 2025-000325</small>
                                            </td>
                                            <td>
                                                Delegate <span class="badge_padding badge_dark">OFFLINE (FRONT)</span>
                                            </td>
                                            <td class="text-right">
                                                ₹ 6,000
                                            </td>
                                            <td class="text-right">
                                                <span class="mi-1 badge_padding badge_success  w-max-con text-uppercase"><?php paid(); ?>Paid</span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                Conference Registration<br>
                                                <small class="text-muted">NATCON 2025-000325</small>
                                            </td>
                                            <td>
                                                Delegate <span class="badge_padding badge_dark">OFFLINE (FRONT)</span>
                                            </td>
                                            <td class="text-right">
                                                ₹ 6,000
                                            </td>
                                            <td class="text-right">
                                                <span class="mi-1 badge_padding badge_success  w-max-con text-uppercase"><?php paid(); ?>Paid</span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                Conference Registration<br>
                                                <small class="text-muted">NATCON 2025-000325</small>
                                            </td>
                                            <td>
                                                Delegate <span class="badge_padding badge_dark">OFFLINE (FRONT)</span>
                                            </td>
                                            <td class="text-right">
                                                ₹ 6,000
                                            </td>
                                            <td class="text-right">
                                                <span class="mi-1 badge_padding badge_success  w-max-con text-uppercase"><?php paid(); ?>Paid</span>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
        <!-- profile pop up -->
        <!-- New Registration pop up -->
        <div class="pop_up_body" id="newregistartion">
            <div class="registration_pop_up">
                <div class="registration-pop_heading">
                    <span>New Registration<i class="ml-1 badge_padding badge_dark">ID: NATCON 2025-1176-0638</i></span>
                    <p>
                        <a href="javascript:void(null)" class="popup_close icon_hover badge_danger action-transparent"><?php close(); ?></a>
                    </p>
                </div>
                <div class="registration-pop_body">
                    <div class="registration-pop_body_box">
                        <div class="registration-pop_body_box_inner">
                            <h4 class="registration-pop_body_box_heading">
                                <span><?php user(); ?>Delegate Profile</span>
                            </h4>
                            <div class="form_grid">
                                <div class="frm_grp span_1">
                                    <p class="frm-head">Title</p>
                                    <input>
                                </div>
                                <div class="frm_grp span_3">
                                    <p class="frm-head">First Name <i class="mandatory">*</i></p>
                                    <input>
                                </div>
                                <div class="frm_grp span_2">
                                    <p class="frm-head">Middle Name</p>
                                    <input>
                                </div>
                                <div class="frm_grp span_2">
                                    <p class="frm-head">Last Name <i class="mandatory">*</i></p>
                                    <input>
                                </div>
                                <div class="frm_grp span_4">
                                    <p class="frm-head">Email Address <i class="mandatory">*</i></p>
                                    <input>
                                </div>
                                <div class="frm_grp span_4">
                                    <p class="frm-head">Mobile Number <i class="mandatory">*</i></p>
                                    <input>
                                </div>
                                <div class="frm_grp span_4">
                                    <p class="frm-head">Institute / Org</p>
                                    <input>
                                </div>
                                <div class="frm_grp span_4">
                                    <p class="frm-head">GST Number</p>
                                    <input>
                                </div>
                                <div class="frm_grp span_2">
                                    <p class="frm-head">Gender</p>
                                    <div class="cus_check_wrap">
                                        <label class="cus_check gender_check">
                                            <input type="radio" name="gender">
                                            <span class="checkmark">Male</span>
                                        </label>
                                        <label class="cus_check gender_check">
                                            <input type="radio" name="gender">
                                            <span class="checkmark">Female</span>
                                        </label>
                                    </div>
                                </div>
                                <div class="frm_grp span_2">
                                    <p class="frm-head">Food</p>
                                    <div class="cus_check_wrap">
                                        <label class="cus_check gender_check">
                                            <input type="radio" name="food">
                                            <span class="checkmark">Veg</span>
                                        </label>
                                        <label class="cus_check gender_check">
                                            <input type="radio" name="food">
                                            <span class="checkmark">Non Veg</span>
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="registration-pop_body_box_inner">
                            <h4 class="registration-pop_body_box_heading">
                                <span><?php address(); ?>Address Details</span>
                            </h4>
                            <div class="form_grid">
                                <div class="frm_grp span_4">
                                    <p class="frm-head">Full Address</p>
                                    <input>
                                </div>
                                <div class="frm_grp span_2">
                                    <p class="frm-head">Country</p>
                                    <select>
                                        <option>Select</option>
                                    </select>
                                </div>
                                <div class="frm_grp span_2">
                                    <p class="frm-head">State</p>
                                    <select>
                                        <option>Select</option>
                                    </select>
                                </div>
                                <div class="frm_grp span_2">
                                    <p class="frm-head">City</p>
                                    <select>
                                        <option>Select</option>
                                    </select>
                                </div>
                                <div class="frm_grp span_2">
                                    <p class="frm-head">Pincode</p>
                                    <input>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="registration-pop_body_box">
                        <div class="registration-pop_body_box_inner">
                            <h4 class="registration-pop_body_box_heading">
                                <span><?php calendar(); ?>Conference Details</span>
                            </h4>
                            <div class="form_grid">
                                <div class="frm_grp span_4">
                                    <p class="frm-head">Registration Mode</p>
                                    <div class="cus_check_wrap">
                                        <label class="cus_check mode_check" data-tab="earlybird">
                                            <input type="radio" name="regimood" checked>
                                            <span class="checkmark">Early Bird</span>
                                        </label>
                                        <label class="cus_check mode_check" data-tab="regular">
                                            <input type="radio" name="regimood">
                                            <span class="checkmark">Regular</span>
                                        </label>
                                        <label class="cus_check mode_check" data-tab="spot">
                                            <input type="radio" name="regimood">
                                            <span class="checkmark">Spot</span>
                                        </label>
                                    </div>
                                </div>
                                <div class="frm_grp span_4">
                                    <p class="frm-head">Select Category</p>
                                    <div class="regi_category" id="earlybird" style="display: block;">
                                        <div class="cus_check_wrap flex-column">
                                            <label class="cus_check category_check">
                                                <input type="radio" name="regicategory" checked>
                                                <span class="checkmark">Early Bird<i>₹ 4000</i></span>
                                            </label>
                                            <label class="cus_check category_check">
                                                <input type="radio" name="regicategory">
                                                <span class="checkmark">PGT<i>₹ 2500</i></span>
                                            </label>
                                            <label class="cus_check category_check">
                                                <input type="radio" name="regicategory">
                                                <span class="checkmark">TB Worker<i>₹ 2000</i></span>
                                            </label>
                                            <label class="cus_check category_check">
                                                <input type="radio" name="regicategory">
                                                <span class="checkmark">Faculty<i>₹ 0</i></span>
                                            </label>
                                            <label class="cus_check category_check">
                                                <input type="radio" name="regicategory">
                                                <span class="checkmark">Student<i>₹ 2000</i></span>
                                            </label>
                                            <label class="cus_check category_check">
                                                <input type="radio" name="regicategory">
                                                <span class="checkmark">Exhibitor<i>₹ 10000</i></span>
                                            </label>
                                        </div>
                                    </div>
                                    <div class="regi_category" id="regular">
                                        <div class="cus_check_wrap flex-column">
                                            <label class="cus_check category_check">
                                                <input type="radio" name="regicategory">
                                                <span class="checkmark">Early Bird<i>₹ 4000</i></span>
                                            </label>
                                            <label class="cus_check category_check">
                                                <input type="radio" name="regicategory">
                                                <span class="checkmark">PGT<i>₹ 2500</i></span>
                                            </label>
                                            <label class="cus_check category_check">
                                                <input type="radio" name="regicategory">
                                                <span class="checkmark">TB Worker<i>₹ 2000</i></span>
                                            </label>
                                            <label class="cus_check category_check">
                                                <input type="radio" name="regicategory">
                                                <span class="checkmark">Faculty<i>₹ 0</i></span>
                                            </label>
                                            <label class="cus_check category_check">
                                                <input type="radio" name="regicategory">
                                                <span class="checkmark">Student<i>₹ 2000</i></span>
                                            </label>
                                            <label class="cus_check category_check">
                                                <input type="radio" name="regicategory">
                                                <span class="checkmark">Exhibitor<i>₹ 10000</i></span>
                                            </label>
                                        </div>
                                    </div>
                                    <div class="regi_category" id="spot">
                                        <div class="cus_check_wrap flex-column">
                                            <label class="cus_check category_check">
                                                <input type="radio" name="regicategory">
                                                <span class="checkmark">Early Bird<i>₹ 4000</i></span>
                                            </label>
                                            <label class="cus_check category_check">
                                                <input type="radio" name="regicategory">
                                                <span class="checkmark">PGT<i>₹ 2500</i></span>
                                            </label>
                                            <label class="cus_check category_check">
                                                <input type="radio" name="regicategory">
                                                <span class="checkmark">TB Worker<i>₹ 2000</i></span>
                                            </label>
                                            <label class="cus_check category_check">
                                                <input type="radio" name="regicategory">
                                                <span class="checkmark">Faculty<i>₹ 0</i></span>
                                            </label>
                                            <label class="cus_check category_check">
                                                <input type="radio" name="regicategory">
                                                <span class="checkmark">Student<i>₹ 2000</i></span>
                                            </label>
                                            <label class="cus_check category_check">
                                                <input type="radio" name="regicategory">
                                                <span class="checkmark">Exhibitor<i>₹ 10000</i></span>
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="registration-pop_body_box_inner">
                            <h4 class="registration-pop_body_box_heading">
                                <span><?php workshop(); ?>Workshops</span>
                                <a class="add mi-1"><?php check(); ?>Select All</a>
                            </h4>
                            <div class="cus_check_wrap flex-column">
                                <label class="cus_check category_check workshop_check">
                                    <input type="radio" name="workshop1">
                                    <span class="checkmark">TB & Critical Care Workshop<i>₹ 4000</i></span>
                                </label>
                                <label class="cus_check category_check workshop_check">
                                    <input type="radio" name="workshop2">
                                    <span class="checkmark">TB & Critical Care Workshop<i>₹ 4000</i></span>
                                </label>
                            </div>
                        </div>
                        <div class="registration-pop_body_box_inner">
                            <h4 class="registration-pop_body_box_heading">
                                <span><?php duser(); ?>Accompanying (1)</span>
                                <a class="add mi-1"><?php add(); ?>Add</a>
                            </h4>
                            <div class="accm_add_wrap">
                                <h6 class="accm_add_empty">No accompanying persons</h6>
                                <div class="accm_add_box">
                                    <div class="form_grid">
                                        <div class="frm_grp span_4">
                                            <input>
                                        </div>
                                        <div class="frm_grp span_2">
                                            <div class="cus_check_wrap">
                                                <label class="cus_check gender_check">
                                                    <input type="radio" name="food">
                                                    <span class="checkmark">Veg</span>
                                                </label>
                                                <label class="cus_check gender_check">
                                                    <input type="radio" name="food">
                                                    <span class="checkmark">Non Veg</span>
                                                </label>
                                            </div>
                                        </div>

                                        <a href="#" class="accm_delet icon_hover badge_danger action-transparent"><?php delete(); ?></a>

                                    </div>
                                </div>
                                <div class="accm_add_box">
                                    <div class="form_grid">
                                        <div class="frm_grp span_4">
                                            <input>
                                        </div>
                                        <div class="frm_grp span_2">
                                            <div class="cus_check_wrap">
                                                <label class="cus_check gender_check">
                                                    <input type="radio" name="food">
                                                    <span class="checkmark">Veg</span>
                                                </label>
                                                <label class="cus_check gender_check">
                                                    <input type="radio" name="food">
                                                    <span class="checkmark">Non Veg</span>
                                                </label>
                                            </div>
                                        </div>
                                        <a href="#" class="accm_delet icon_hover badge_danger action-transparent"><?php delete(); ?></a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="registration-pop_body_box">
                        <div class="registration-pop_body_box_inner">
                            <h4 class="registration-pop_body_box_heading">
                                <span><?php bill(); ?>Bill Summary</span>
                            </h4>
                            <p class="frm-head text_dark d-flex justify-content-between align-items-center">Registration (Regular) <span class="text-white">₹ 4,000</span></p>
                            <p class="frm-head text_dark d-flex justify-content-between align-items-center">Registration (Regular) <span class="text-white">₹ 4,000</span></p>
                            <hr class="my-3" style="height: 0;border-top: 1px dashed currentColor;background: transparent;">
                            <p class="frm-head text_dark d-flex justify-content-between align-items-center">Registration (Regular) <span class="text-white">₹ <input class="dis_input" type="number"></span></p>
                            <hr class="my-3" style="height: 0;border-top: 1px dashed currentColor;background: transparent;">
                            <p class="frm-head text_dark d-flex justify-content-between align-items-center">Subtotal <span class="text-white">₹ 4,000</span></p>
                            <p class="frm-head text_dark d-flex justify-content-between align-items-center">GST (18%) <span class="text-white">₹ 4,000</span></p>
                            <h5 class="regi_total">
                                Total<span>₹ 12,980</span>
                            </h5>
                        </div>
                        <div class="registration-pop_body_box_inner">
                            <h4 class="registration-pop_body_box_heading">
                                <span><?php credit(); ?>Payment Details</span>
                            </h4>
                            <div class="form_grid">
                                <div class="frm_grp span_4">
                                    <p class="frm-head">Payment Mode</p>
                                    <div class="cus_check_wrap">
                                        <label class="cus_check pay_check" data-tab="cash">
                                            <input type="radio" name="paymentmood" checked>
                                            <span class="checkmark">Cash</span>
                                        </label>
                                        <label class="cus_check pay_check" data-tab="upi">
                                            <input type="radio" name="paymentmood">
                                            <span class="checkmark">UPI</span>
                                        </label>
                                        <label class="cus_check pay_check" data-tab="card">
                                            <input type="radio" name="paymentmood">
                                            <span class="checkmark">Card</span>
                                        </label>
                                    </div>
                                </div>
                                <div class="payment_details span_4" id="cash" style="display: block;">
                                    <div class="form_grid">
                                        <div class="frm_grp span_2">
                                            <p class="frm-head">Date</p>
                                            <input type="date">
                                        </div>
                                        <div class="frm_grp span_2">
                                            <p class="frm-head">Amount Paid</p>
                                            <input type="number">
                                        </div>
                                        <div class="frm_grp span_4">
                                            <p class="frm-head">Transaction ID / Ref No</p>
                                            <input>
                                        </div>
                                    </div>
                                </div>
                                <div class="payment_details span_4" id="upi">
                                    <div class="form_grid">
                                        <div class="frm_grp span_2">
                                            <p class="frm-head">Date</p>
                                            <input type="date">
                                        </div>
                                        <div class="frm_grp span_2">
                                            <p class="frm-head">Amount Paid</p>
                                            <input type="number">
                                        </div>
                                        <div class="frm_grp span_4">
                                            <p class="frm-head">Transaction ID / Ref No</p>
                                            <input>
                                        </div>
                                    </div>
                                </div>
                                <div class="payment_details span_4" id="card">
                                    <div class="form_grid">
                                        <div class="frm_grp span_2">
                                            <p class="frm-head">Date</p>
                                            <input type="date">
                                        </div>
                                        <div class="frm_grp span_2">
                                            <p class="frm-head">Amount Paid</p>
                                            <input type="number">
                                        </div>
                                        <div class="frm_grp span_4">
                                            <p class="frm-head">Transaction ID / Ref No</p>
                                            <input>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <h6 class="due_balance mt-3 d-flex justify-content-between align-items-center badge_danger py-2 px-2">Balance Due<span class="text-white mt-0">₹ 12,980</span></h6>
                            <a href="#" class="due_balance badge_info p-2 mt-1 d-flex justify-content-center align-items-center">Auto-Fill Full Amount</a>

                        </div>

                    </div>
                </div>
                <div class="registration-pop_footer">
                    <div class="registration_btn_wrap">
                        <button class="popup_close badge_dark">Cancel</button>
                        <button type="submit" class="mi-1 badge_success"><?php save(); ?>Confirm Registration</button>
                    </div>
                </div>
            </div>
        </div>
        <!-- New Registration pop up -->
        <!-- Edit Registration pop up -->
        <div class="pop_up_body" id="editregistartion">
            <div class="registration_pop_up">
                <div class="registration-pop_heading">
                    <span>Edit Registration<i class="ml-1 badge_padding badge_dark">ID: NATCON 2025-1176-0638</i></span>
                    <p>
                        <a href="javascript:void(null)" class="popup_close icon_hover badge_danger action-transparent"><?php close(); ?></a>
                    </p>
                </div>
                <div class="registration-pop_body">
                    <div class="registration-pop_body_box">
                        <div class="registration-pop_body_box_inner">
                            <h4 class="registration-pop_body_box_heading">
                                <span><?php user(); ?>Delegate Profile</span>
                            </h4>
                            <div class="form_grid">
                                <div class="frm_grp span_1">
                                    <p class="frm-head">Title</p>
                                    <input>
                                </div>
                                <div class="frm_grp span_3">
                                    <p class="frm-head">First Name <i class="mandatory">*</i></p>
                                    <input>
                                </div>
                                <div class="frm_grp span_2">
                                    <p class="frm-head">Middle Name</p>
                                    <input>
                                </div>
                                <div class="frm_grp span_2">
                                    <p class="frm-head">Last Name <i class="mandatory">*</i></p>
                                    <input>
                                </div>
                                <div class="frm_grp span_4">
                                    <p class="frm-head">Email Address <i class="mandatory">*</i></p>
                                    <input>
                                </div>
                                <div class="frm_grp span_4">
                                    <p class="frm-head">Mobile Number <i class="mandatory">*</i></p>
                                    <input>
                                </div>
                                <div class="frm_grp span_4">
                                    <p class="frm-head">Institute / Org</p>
                                    <input>
                                </div>
                                <div class="frm_grp span_4">
                                    <p class="frm-head">GST Number</p>
                                    <input>
                                </div>
                                <div class="frm_grp span_2">
                                    <p class="frm-head">Gender</p>
                                    <div class="cus_check_wrap">
                                        <label class="cus_check gender_check">
                                            <input type="radio" name="gender">
                                            <span class="checkmark">Male</span>
                                        </label>
                                        <label class="cus_check gender_check">
                                            <input type="radio" name="gender">
                                            <span class="checkmark">Female</span>
                                        </label>
                                    </div>
                                </div>
                                <div class="frm_grp span_2">
                                    <p class="frm-head">Food</p>
                                    <div class="cus_check_wrap">
                                        <label class="cus_check gender_check">
                                            <input type="radio" name="food">
                                            <span class="checkmark">Veg</span>
                                        </label>
                                        <label class="cus_check gender_check">
                                            <input type="radio" name="food">
                                            <span class="checkmark">Non Veg</span>
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="registration-pop_body_box_inner">
                            <h4 class="registration-pop_body_box_heading">
                                <span><?php address(); ?>Address Details</span>
                            </h4>
                            <div class="form_grid">
                                <div class="frm_grp span_4">
                                    <p class="frm-head">Full Address</p>
                                    <input>
                                </div>
                                <div class="frm_grp span_2">
                                    <p class="frm-head">Country</p>
                                    <select>
                                        <option>Select</option>
                                    </select>
                                </div>
                                <div class="frm_grp span_2">
                                    <p class="frm-head">State</p>
                                    <select>
                                        <option>Select</option>
                                    </select>
                                </div>
                                <div class="frm_grp span_2">
                                    <p class="frm-head">City</p>
                                    <select>
                                        <option>Select</option>
                                    </select>
                                </div>
                                <div class="frm_grp span_2">
                                    <p class="frm-head">Pincode</p>
                                    <input>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="registration-pop_body_box">
                        <div class="registration-pop_body_box_inner">
                            <h4 class="registration-pop_body_box_heading">
                                <span><?php calendar(); ?>Conference Details</span>
                            </h4>
                            <div class="form_grid">
                                <div class="frm_grp span_4">
                                    <p class="frm-head">Registration Mode</p>
                                    <div class="cus_check_wrap">
                                        <label class="cus_check mode_check" data-tab="earlybird">
                                            <input type="radio" name="regimood" checked>
                                            <span class="checkmark">Early Bird</span>
                                        </label>
                                        <label class="cus_check mode_check" data-tab="regular">
                                            <input type="radio" name="regimood">
                                            <span class="checkmark">Regular</span>
                                        </label>
                                        <label class="cus_check mode_check" data-tab="spot">
                                            <input type="radio" name="regimood">
                                            <span class="checkmark">Spot</span>
                                        </label>
                                    </div>
                                </div>
                                <div class="frm_grp span_4">
                                    <p class="frm-head">Select Category</p>
                                    <div class="regi_category" id="earlybird" style="display: block;">
                                        <div class="cus_check_wrap flex-column">
                                            <label class="cus_check category_check">
                                                <input type="radio" name="regicategory" checked>
                                                <span class="checkmark">Early Bird<i>₹ 4000</i></span>
                                            </label>
                                            <label class="cus_check category_check">
                                                <input type="radio" name="regicategory">
                                                <span class="checkmark">PGT<i>₹ 2500</i></span>
                                            </label>
                                            <label class="cus_check category_check">
                                                <input type="radio" name="regicategory">
                                                <span class="checkmark">TB Worker<i>₹ 2000</i></span>
                                            </label>
                                            <label class="cus_check category_check">
                                                <input type="radio" name="regicategory">
                                                <span class="checkmark">Faculty<i>₹ 0</i></span>
                                            </label>
                                            <label class="cus_check category_check">
                                                <input type="radio" name="regicategory">
                                                <span class="checkmark">Student<i>₹ 2000</i></span>
                                            </label>
                                            <label class="cus_check category_check">
                                                <input type="radio" name="regicategory">
                                                <span class="checkmark">Exhibitor<i>₹ 10000</i></span>
                                            </label>
                                        </div>
                                    </div>
                                    <div class="regi_category" id="regular">
                                        <div class="cus_check_wrap flex-column">
                                            <label class="cus_check category_check">
                                                <input type="radio" name="regicategory">
                                                <span class="checkmark">Early Bird<i>₹ 4000</i></span>
                                            </label>
                                            <label class="cus_check category_check">
                                                <input type="radio" name="regicategory">
                                                <span class="checkmark">PGT<i>₹ 2500</i></span>
                                            </label>
                                            <label class="cus_check category_check">
                                                <input type="radio" name="regicategory">
                                                <span class="checkmark">TB Worker<i>₹ 2000</i></span>
                                            </label>
                                            <label class="cus_check category_check">
                                                <input type="radio" name="regicategory">
                                                <span class="checkmark">Faculty<i>₹ 0</i></span>
                                            </label>
                                            <label class="cus_check category_check">
                                                <input type="radio" name="regicategory">
                                                <span class="checkmark">Student<i>₹ 2000</i></span>
                                            </label>
                                            <label class="cus_check category_check">
                                                <input type="radio" name="regicategory">
                                                <span class="checkmark">Exhibitor<i>₹ 10000</i></span>
                                            </label>
                                        </div>
                                    </div>
                                    <div class="regi_category" id="spot">
                                        <div class="cus_check_wrap flex-column">
                                            <label class="cus_check category_check">
                                                <input type="radio" name="regicategory">
                                                <span class="checkmark">Early Bird<i>₹ 4000</i></span>
                                            </label>
                                            <label class="cus_check category_check">
                                                <input type="radio" name="regicategory">
                                                <span class="checkmark">PGT<i>₹ 2500</i></span>
                                            </label>
                                            <label class="cus_check category_check">
                                                <input type="radio" name="regicategory">
                                                <span class="checkmark">TB Worker<i>₹ 2000</i></span>
                                            </label>
                                            <label class="cus_check category_check">
                                                <input type="radio" name="regicategory">
                                                <span class="checkmark">Faculty<i>₹ 0</i></span>
                                            </label>
                                            <label class="cus_check category_check">
                                                <input type="radio" name="regicategory">
                                                <span class="checkmark">Student<i>₹ 2000</i></span>
                                            </label>
                                            <label class="cus_check category_check">
                                                <input type="radio" name="regicategory">
                                                <span class="checkmark">Exhibitor<i>₹ 10000</i></span>
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="registration-pop_body_box_inner">
                            <h4 class="registration-pop_body_box_heading">
                                <span><?php workshop(); ?>Workshops</span>
                                <a class="add mi-1"><?php check(); ?>Select All</a>
                            </h4>
                            <div class="cus_check_wrap flex-column">
                                <label class="cus_check category_check workshop_check">
                                    <input type="radio" name="workshop1">
                                    <span class="checkmark">TB & Critical Care Workshop<i>₹ 4000</i></span>
                                </label>
                                <label class="cus_check category_check workshop_check">
                                    <input type="radio" name="workshop2">
                                    <span class="checkmark">TB & Critical Care Workshop<i>₹ 4000</i></span>
                                </label>
                            </div>
                        </div>
                        <div class="registration-pop_body_box_inner">
                            <h4 class="registration-pop_body_box_heading">
                                <span><?php duser(); ?>Accompanying (1)</span>
                                <a class="add mi-1"><?php add(); ?>Add</a>
                            </h4>
                            <div class="accm_add_wrap">
                                <h6 class="accm_add_empty">No accompanying persons</h6>
                                <div class="accm_add_box">
                                    <div class="form_grid">
                                        <div class="frm_grp span_4">
                                            <input>
                                        </div>
                                        <div class="frm_grp span_2">
                                            <div class="cus_check_wrap">
                                                <label class="cus_check gender_check">
                                                    <input type="radio" name="food">
                                                    <span class="checkmark">Veg</span>
                                                </label>
                                                <label class="cus_check gender_check">
                                                    <input type="radio" name="food">
                                                    <span class="checkmark">Non Veg</span>
                                                </label>
                                            </div>
                                        </div>

                                        <a href="#" class="accm_delet icon_hover badge_danger action-transparent"><?php delete(); ?></a>

                                    </div>
                                </div>
                                <div class="accm_add_box">
                                    <div class="form_grid">
                                        <div class="frm_grp span_4">
                                            <input>
                                        </div>
                                        <div class="frm_grp span_2">
                                            <div class="cus_check_wrap">
                                                <label class="cus_check gender_check">
                                                    <input type="radio" name="food">
                                                    <span class="checkmark">Veg</span>
                                                </label>
                                                <label class="cus_check gender_check">
                                                    <input type="radio" name="food">
                                                    <span class="checkmark">Non Veg</span>
                                                </label>
                                            </div>
                                        </div>

                                        <a href="#" class="accm_delet icon_hover badge_danger action-transparent"><?php delete(); ?></a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="registration-pop_body_box">
                        <div class="registration-pop_body_box_inner">
                            <h4 class="registration-pop_body_box_heading">
                                <span><?php bill(); ?>Bill Summary</span>
                            </h4>
                            <p class="frm-head text_dark d-flex justify-content-between align-items-center">Registration (Regular) <span class="text-white">₹ 4,000</span></p>
                            <p class="frm-head text_dark d-flex justify-content-between align-items-center">Registration (Regular) <span class="text-white">₹ 4,000</span></p>
                            <hr class="my-3" style="    height: 0;border-top: 1px dashed currentColor;background: transparent;">
                            <p class="frm-head text_dark d-flex justify-content-between align-items-center">Registration (Regular) <span class="text-white">₹ <input class="dis_input" type="number"></span></p>
                            <hr class="my-3" style="    height: 0;border-top: 1px dashed currentColor;background: transparent;">
                            <p class="frm-head text_dark d-flex justify-content-between align-items-center">Subtotal <span class="text-white">₹ 4,000</span></p>
                            <p class="frm-head text_dark d-flex justify-content-between align-items-center">GST (18%) <span class="text-white">₹ 4,000</span></p>
                            <h5 class="regi_total">
                                Total<span>₹ 12,980</span>
                            </h5>
                        </div>
                        <div class="registration-pop_body_box_inner">
                            <h4 class="registration-pop_body_box_heading">
                                <span><?php credit(); ?>Payment Details</span>
                            </h4>
                            <div class="form_grid">
                                <div class="frm_grp span_4">
                                    <p class="frm-head">Payment Mode</p>
                                    <div class="cus_check_wrap">
                                        <label class="cus_check pay_check" data-tab="cash">
                                            <input type="radio" name="paymentmood" checked>
                                            <span class="checkmark">Cash</span>
                                        </label>
                                        <label class="cus_check pay_check" data-tab="upi">
                                            <input type="radio" name="paymentmood">
                                            <span class="checkmark">UPI</span>
                                        </label>
                                        <label class="cus_check pay_check" data-tab="card">
                                            <input type="radio" name="paymentmood">
                                            <span class="checkmark">Card</span>
                                        </label>
                                    </div>
                                </div>
                                <div class="payment_details span_4" id="cash" style="display: block;">
                                    <div class="form_grid">
                                        <div class="frm_grp span_2">
                                            <p class="frm-head">Date</p>
                                            <input type="date">
                                        </div>
                                        <div class="frm_grp span_2">
                                            <p class="frm-head">Amount Paid</p>
                                            <input type="number">
                                        </div>
                                        <div class="frm_grp span_4">
                                            <p class="frm-head">Transaction ID / Ref No</p>
                                            <input>
                                        </div>
                                    </div>
                                </div>
                                <div class="payment_details span_4" id="upi">
                                    <div class="form_grid">
                                        <div class="frm_grp span_2">
                                            <p class="frm-head">Date</p>
                                            <input type="date">
                                        </div>
                                        <div class="frm_grp span_2">
                                            <p class="frm-head">Amount Paid</p>
                                            <input type="number">
                                        </div>
                                        <div class="frm_grp span_4">
                                            <p class="frm-head">Transaction ID / Ref No</p>
                                            <input>
                                        </div>
                                    </div>
                                </div>
                                <div class="payment_details span_4" id="card">
                                    <div class="form_grid">
                                        <div class="frm_grp span_2">
                                            <p class="frm-head">Date</p>
                                            <input type="date">
                                        </div>
                                        <div class="frm_grp span_2">
                                            <p class="frm-head">Amount Paid</p>
                                            <input type="number">
                                        </div>
                                        <div class="frm_grp span_4">
                                            <p class="frm-head">Transaction ID / Ref No</p>
                                            <input>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <h6 class="due_balance mt-3 d-flex justify-content-between align-items-center badge_danger py-2 px-2">Balance Due<span class="text-white mt-0">₹ 12,980</span></h6>
                            <a href="#" class="due_balance badge_info p-2 mt-1 d-flex justify-content-center align-items-center">Auto-Fill Full Amount</a>
                        </div>
                    </div>
                </div>
                <div class="registration-pop_footer">
                    <div class="registration_btn_wrap">
                        <button class="popup_close badge_dark">Cancel</button>
                        <button type="submit" class="mi-1 badge_success"><?php save(); ?>Update Registration</button>
                    </div>
                </div>
            </div>
        </div>
        <!-- Edit Registration pop up -->
        <!-- New accommodation booking pop up -->
        <div class="pop_up_body" id="newbooking">
            <div class="registration_pop_up">
                <div class="registration-pop_heading">
                    <span>New Hotel Booking</span>
                    <p>
                        <a href="javascript:void(null)" class="popup_close icon_hover badge_danger action-transparent"><?php close(); ?></a>
                    </p>
                </div>
                <div class="registration-pop_body">
                    <div class="registration-pop_body_box">
                        <div class="registration-pop_body_box_inner p-0 bg-transparent border-0">
                            <div class="form_grid">
                                <div class="frm_grp span_4">
                                    <p class="frm-head">Guest Name</p>
                                    <input>
                                </div>
                                <div class="frm_grp span_4">
                                    <p class="frm-head">Select Hotel</p>
                                    <select></select>
                                </div>
                                <div class="frm_grp span_2">
                                    <p class="frm-head">Check In</p>
                                    <input type="date">
                                </div>
                                <div class="frm_grp span_2">
                                    <p class="frm-head">Check Out</p>
                                    <input type="date">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="registration-pop_footer">
                    <div class="registration_btn_wrap">
                        <button class="popup_close badge_dark">Cancel</button>
                        <button type="submit" class="mi-1 badge_success"><?php save(); ?>Confirm Booking</button>
                    </div>
                </div>
            </div>
        </div>
        <!-- New accommodation booking pop up -->
        <!-- New session pop up -->
        <div class="pop_up_body " id="newsession">
            <div class="registration_pop_up">
                <div class="registration-pop_heading">
                    <span>Compose Session</span>
                    <p>
                        <a href="javascript:void(null)" class="popup_close icon_hover badge_danger action-transparent"><?php close(); ?></a>
                    </p>
                </div>
                <div class="registration-pop_body">
                    <div class="registration-pop_body_box">
                        <div class="registration-pop_body_box_inner p-0 bg-transparent border-0">
                            <h5 class="registration-pop_body_box_heading"><span>Session Details</span></h5>
                            <div class="form_grid">
                                <div class="frm_grp span_2">
                                    <p class="frm-head">Session Title</p>
                                    <input>
                                </div>
                                <div class="frm_grp span_2">
                                    <p class="frm-head">Session Date</p>
                                    <input type="date">
                                </div>
                                <div class="frm_grp span_2">
                                    <p class="frm-head">Start Time</p>
                                    <input type="time">
                                </div>
                                <div class="frm_grp span_2">
                                    <p class="frm-head">End Time</p>
                                    <input type="time">
                                </div>
                                <div class="frm_grp span_2">
                                    <p class="frm-head">Session Hall</p>
                                    <select></select>
                                </div>
                                <div class="frm_grp span_2">
                                    <p class="frm-head">Ref Color</p>
                                    <select></select>
                                </div>
                                <div class="frm_grp span_2">
                                    <p class="frm-head">Session Type</p>
                                    <select></select>
                                </div>
                                <div class="frm_grp span_2">
                                    <p class="frm-head">Session Chairpersons</p>
                                    <input date>
                                </div>
                            </div>
                        </div>
                        <div class="registration-pop_body_box_inner p-0 bg-transparent border-0">
                            <h5 class="registration-pop_body_box_heading"><span>Talks / Sub-Sessions</span><a class="add mi-1"><?php add() ?>Add</a></h5>
                            <div class="accm_add_wrap talk_sub">
                                <h6 class="accm_add_empty">No talks added yet.</h6>
                                <div class="accm_add_box">
                                    <div class="form_grid">
                                        <div class="frm_grp span_2">
                                            <p class="frm-head sub-frm-head">Duration</p>
                                            <input>
                                        </div>
                                        <div class="frm_grp span_7">
                                            <p class="frm-head sub-frm-head">Topic Title</p>
                                            <input>
                                        </div>
                                        <div class="frm_grp span_5">
                                            <p class="frm-head sub-frm-head">Speaker</p>
                                            <input>
                                        </div>
                                        <div class="frm_grp span_1">
                                            <a href="#" class="accm_delet icon_hover badge_danger action-transparent"><?php delete() ?></a>
                                        </div>
                                    </div>
                                </div>
                                <div class="accm_add_box">
                                    <div class="form_grid">
                                        <div class="frm_grp span_2">
                                            <p class="frm-head sub-frm-head">Duration</p>
                                            <input>
                                        </div>
                                        <div class="frm_grp span_7">
                                            <p class="frm-head sub-frm-head">Topic Title</p>
                                            <input>
                                        </div>
                                        <div class="frm_grp span_5">
                                            <p class="frm-head sub-frm-head">Speaker</p>
                                            <input>
                                        </div>
                                        <div class="frm_grp span_1">
                                            <a href="#" class="accm_delet icon_hover badge_danger action-transparent"><?php delete() ?></a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="registration-pop_footer">
                    <div class="registration_btn_wrap">
                        <button class="popup_close badge_dark">Cancel</button>
                        <button type="submit" class="mi-1 badge_success"><?php save(); ?>Compose Session</button>
                    </div>
                </div>
            </div>
        </div>
        <!-- New session pop up -->
        <!-- edit session pop up -->
        <div class="pop_up_body " id="editsession">
            <div class="registration_pop_up">
                <div class="registration-pop_heading">
                    <span>Edit Session</span>
                    <p>
                        <a href="javascript:void(null)" class="popup_close icon_hover badge_danger action-transparent"><?php close(); ?></a>
                    </p>
                </div>
                <div class="registration-pop_body">
                    <div class="registration-pop_body_box">
                        <div class="registration-pop_body_box_inner p-0 bg-transparent border-0">
                            <h5 class="registration-pop_body_box_heading"><span>Session Details</span></h5>
                            <div class="form_grid">
                                <div class="frm_grp span_2">
                                    <p class="frm-head">Session Title</p>
                                    <input>
                                </div>
                                <div class="frm_grp span_2">
                                    <p class="frm-head">Session Date</p>
                                    <input type="date">
                                </div>
                                <div class="frm_grp span_2">
                                    <p class="frm-head">Start Time</p>
                                    <input type="time">
                                </div>
                                <div class="frm_grp span_2">
                                    <p class="frm-head">End Time</p>
                                    <input type="time">
                                </div>
                                <div class="frm_grp span_2">
                                    <p class="frm-head">Session Hall</p>
                                    <select></select>
                                </div>
                                <div class="frm_grp span_2">
                                    <p class="frm-head">Ref Color</p>
                                    <select></select>
                                </div>
                                <div class="frm_grp span_2">
                                    <p class="frm-head">Session Type</p>
                                    <select></select>
                                </div>
                                <div class="frm_grp span_2">
                                    <p class="frm-head">Session Chairpersons</p>
                                    <input date>
                                </div>
                            </div>
                        </div>
                        <div class="registration-pop_body_box_inner p-0 bg-transparent border-0">
                            <h5 class="registration-pop_body_box_heading"><span>Talks / Sub-Sessions</span><a class="add mi-1"><?php add() ?>Add</a></h5>
                            <div class="accm_add_wrap talk_sub">
                                <h6 class="accm_add_empty">No talks added yet.</h6>
                                <div class="accm_add_box">
                                    <div class="form_grid">
                                        <div class="frm_grp span_2">
                                            <p class="frm-head sub-frm-head">Duration</p>
                                            <input>
                                        </div>
                                        <div class="frm_grp span_7">
                                            <p class="frm-head sub-frm-head">Topic Title</p>
                                            <input>
                                        </div>
                                        <div class="frm_grp span_5">
                                            <p class="frm-head sub-frm-head">Speaker</p>
                                            <input>
                                        </div>
                                        <div class="frm_grp span_1">
                                            <a href="#" class="accm_delet icon_hover badge_danger action-transparent"><?php delete() ?></a>
                                        </div>
                                    </div>
                                </div>
                                <div class="accm_add_box">
                                    <div class="form_grid">
                                        <div class="frm_grp span_2">
                                            <p class="frm-head sub-frm-head">Duration</p>
                                            <input>
                                        </div>
                                        <div class="frm_grp span_7">
                                            <p class="frm-head sub-frm-head">Topic Title</p>
                                            <input>
                                        </div>
                                        <div class="frm_grp span_5">
                                            <p class="frm-head sub-frm-head">Speaker</p>
                                            <input>
                                        </div>
                                        <div class="frm_grp span_1">
                                            <a href="#" class="accm_delet icon_hover badge_danger action-transparent"><?php delete() ?></a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
                <div class="registration-pop_footer">
                    <div class="registration_btn_wrap">
                        <button class="popup_close badge_dark">Cancel</button>
                        <button type="submit" class="mi-1 badge_success"><?php save(); ?>Update Session</button>
                    </div>
                </div>
            </div>
        </div>
        <!-- edit session pop up -->
        <!-- New exibitor pop up -->
        <div class="pop_up_body " id="newexibitor">
            <div class="registration_pop_up">
                <div class="registration-pop_heading">
                    <span>Exhibitor Registration</span>
                    <p>
                        <a href="javascript:void(null)" class="popup_close icon_hover badge_danger action-transparent"><?php close(); ?></a>
                    </p>
                </div>
                <div class="registration-pop_body">
                    <div class="registration-pop_body_box">
                        <div class="registration-pop_body_box_inner p-0 bg-transparent border-0">
                            <h5 class="registration-pop_body_box_heading"><span>Exhibitor Details</span></h5>
                            <div class="form_grid">
                                <div class="frm_grp span_2">
                                    <p class="frm-head">Exhibitor Code</p>
                                    <input>
                                </div>
                                <div class="frm_grp span_2">
                                    <p class="frm-head">Company Name</p>
                                    <input>
                                </div>
                                <div class="frm_grp span_2">
                                    <p class="frm-head">Company Address</p>
                                    <input>
                                </div>
                                <div class="frm_grp span_2">
                                    <p class="frm-head">Country</p>
                                    <select></select>
                                </div>
                                <div class="frm_grp span_2">
                                    <p class="frm-head">State</p>
                                    <select></select>
                                </div>
                                <div class="frm_grp span_2">
                                    <p class="frm-head">City</p>
                                    <select></select>
                                </div>
                                <div class="frm_grp span_2">
                                    <p class="frm-head">Partnership Type</p>
                                    <select></select>
                                </div>
                                <div class="frm_grp span_2">
                                    <p class="frm-head">Remarks / Reference</p>
                                    <select></select>
                                </div>
                                <div class="frm_grp span_2">
                                    <p class="frm-head">Company GSTIN No</p>
                                    <select></select>
                                </div>
                                <div class="frm_grp span_2">
                                    <p class="frm-head">Company PAN No</p>
                                    <select></select>
                                </div>
                            </div>
                        </div>
                        <div class="registration-pop_body_box_inner p-0 bg-transparent border-0">
                            <h5 class="registration-pop_body_box_heading"><span>Contact Persons</span><a class="add mi-1"><?php add() ?>Add</a></h5>
                            <div class="accm_add_wrap exibitor_sub">
                                <h6 class="accm_add_empty">No contact person added yet.</h6>
                                <div class="accm_add_box">
                                    <h5 class="frm_sub_head"><span>Contact Person 1</span><a href="#" class="accm_delet icon_hover badge_danger action-transparent"><?php delete() ?></a></h5>
                                    <div class="form_grid">
                                        <div class="frm_grp span_2">
                                            <p class="frm-head sub-frm-head">First Name</p>
                                            <input>
                                        </div>
                                        <div class="frm_grp span_2">
                                            <p class="frm-head sub-frm-head">Middle Name</p>
                                            <input>
                                        </div>
                                        <div class="frm_grp span_2">
                                            <p class="frm-head sub-frm-head">Last Name</p>
                                            <input>
                                        </div>
                                        <div class="frm_grp span_3">
                                            <p class="frm-head sub-frm-head">Mobile Number</p>
                                            <input>
                                        </div>
                                        <div class="frm_grp span_3">
                                            <p class="frm-head sub-frm-head">Email Id</p>
                                            <input>
                                        </div>
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>
                </div>
                <div class="registration-pop_footer">
                    <div class="registration_btn_wrap">
                        <button class="popup_close badge_dark">Cancel</button>
                        <button type="submit" class="mi-1 badge_success"><?php save(); ?>Register Exibitor</button>
                    </div>
                </div>
            </div>
        </div>
        <!-- New exibitor pop up -->
        <!-- New guest pop up -->
        <div class="pop_up_body " id="newguest">
            <div class="registration_pop_up">
                <div class="registration-pop_heading">
                    <span>Create Guest User</span>
                    <p>
                        <a href="javascript:void(null)" class="popup_close icon_hover badge_danger action-transparent"><?php close(); ?></a>
                    </p>
                </div>
                <div class="registration-pop_body">
                    <div class="registration-pop_body_box">
                        <div class="registration-pop_body_box_inner p-0 bg-transparent border-0">
                            <div class="form_grid">
                                <div class="frm_grp span_2">
                                    <p class="frm-head">Full Name</p>
                                    <input>
                                </div>
                                <div class="frm_grp span_2">
                                    <p class="frm-head">Guest Category</p>
                                    <select></select>
                                </div>
                                <div class="frm_grp span_2">
                                    <p class="frm-head">Mobile Number</p>
                                    <input>
                                </div>
                                <div class="frm_grp span_2">
                                    <p class="frm-head">Email Address</p>
                                    <input>
                                </div>
                                <div class="frm_grp span_4">
                                    <p class="frm-head">Organization / Affiliation</p>
                                    <input>
                                </div>
                                <div class="frm_grp span_4">
                                    <p class="frm-head">Notes / Remarks</p>
                                    <input>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
                <div class="registration-pop_footer">
                    <div class="registration_btn_wrap">
                        <button class="popup_close badge_dark">Cancel</button>
                        <button type="submit" class="mi-1 badge_success"><?php save(); ?>Create User</button>
                    </div>
                </div>
            </div>
        </div>
        <!-- New guest pop up -->
        <!-- Edit guest pop up -->
        <div class="pop_up_body " id="editguest">
            <div class="registration_pop_up">
                <div class="registration-pop_heading">
                    <span>Edit Guest User</span>
                    <p>
                        <a href="javascript:void(null)" class="popup_close icon_hover badge_danger action-transparent"><?php close(); ?></a>
                    </p>
                </div>
                <div class="registration-pop_body">
                    <div class="registration-pop_body_box">
                        <div class="registration-pop_body_box_inner p-0 bg-transparent border-0">
                            <div class="form_grid">
                                <div class="frm_grp span_2">
                                    <p class="frm-head">Full Name</p>
                                    <input>
                                </div>
                                <div class="frm_grp span_2">
                                    <p class="frm-head">Guest Category</p>
                                    <select></select>
                                </div>
                                <div class="frm_grp span_2">
                                    <p class="frm-head">Mobile Number</p>
                                    <input>
                                </div>
                                <div class="frm_grp span_2">
                                    <p class="frm-head">Email Address</p>
                                    <input>
                                </div>
                                <div class="frm_grp span_4">
                                    <p class="frm-head">Organization / Affiliation</p>
                                    <input>
                                </div>
                                <div class="frm_grp span_4">
                                    <p class="frm-head">Notes / Remarks</p>
                                    <input>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
                <div class="registration-pop_footer">
                    <div class="registration_btn_wrap">
                        <button class="popup_close badge_dark">Cancel</button>
                        <button type="submit" class="mi-1 badge_success"><?php save(); ?>Update User</button>
                    </div>
                </div>
            </div>
        </div>
        <!-- Edit guest pop up -->
        <!-- Add registration Cutoff pop up -->
        <div class="pop_up_body" id="addregistrationcutoff">
            <div class="registration_pop_up">
                <div class="registration-pop_heading">
                    <span>Add Registration Cutoff</span>
                    <p>
                        <a href="javascript:void(null)" class="popup_close icon_hover badge_danger action-transparent"><?php close(); ?></a>
                    </p>
                </div>
                <form name="frmtypeadd" method="post" action="<?= $cfg['SECTION_BASE_URL'] ?>manage_cutoff.process.php" id="frmtypeadd" onsubmit="return onSubmitAction();">
                    <input type="hidden" name="act" value="insert" />
                    <input type="hidden" name="chk_country_add" id="chk_country_add" value="0" />
                    <div class="registration-pop_body">
                        <div class="registration-pop_body_box">
                            <div class="registration-pop_body_box_inner p-0 bg-transparent border-0">
                                <div class="form_grid">
                                    <div class="frm_grp span_4">
                                        <p class="frm-head">Registration Cutoff Title</p>
                                        <input type="text" name="workshop_add" id="workshop_add" class="validate[required]" required="" />

                                    </div>
                                    <div class="frm_grp span_2">
                                        <p class="frm-head">Start Date</p>
                                        <input type="date" name="seat_limit_add" id="seat_limit_add" class="validate[required]" onblur="countryAvailabilityAdd(this.value)" rel="splDate" required="" />

                                    </div>
                                    <div class="frm_grp span_2">
                                        <p class="frm-head">End Date</p>
                                        <input type="date" name="workshop_date_add" id="workshop_date_add" rel="splDate" required="" />

                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="registration-pop_footer">
                        <div class="registration_btn_wrap">
                            <button class="popup_close badge_dark">Cancel</button>
                            <button type="submit" class="mi-1 badge_success"><?php save(); ?>Save</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
        <!-- Add registration Cutoff pop up -->
        <!-- edit registration Cutoff pop up -->
        <div class="pop_up_body" id="editregistrationcutoff">
            <div class="registration_pop_up">
                <div class="registration-pop_heading">
                    <span>Edit Registration Cutoff</span>
                    <p>
                        <a href="javascript:void(null)" class="popup_close icon_hover badge_danger action-transparent"><?php close(); ?></a>
                    </p>
                </div>
                <form name="frmtypeadd" method="post" action="<?= $cfg['SECTION_BASE_URL'] ?>manage_cutoff.process.php" id="frmtypeadd" onsubmit="return onSubmitAction();">
                    <input type="hidden" name="act" value="update" />
                    <input type="hidden" name="cutoff_id" id="cutoff_id" />
                    <div class="registration-pop_body">
                        <div class="registration-pop_body_box">
                            <div class="registration-pop_body_box_inner p-0 bg-transparent border-0">
                                <div class="form_grid">
                                    <div class="frm_grp span_4">
                                        <p class="frm-head">Registration Cutoff Title</p>
                                        <input type="text" name="cutoff_title" id="cutoff_title" required />
                                    </div>
                                    <div class="frm_grp span_2">
                                        <p class="frm-head">Start Date</p>
                                        <input type="date" name="start_date" id="start_date" rel="splDate" required />

                                    </div>
                                    <div class="frm_grp span_2">
                                        <p class="frm-head">End Date</p>
                                        <input type="date" name="end_date" id="end_date" rel="splDate" required />
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="registration-pop_footer">
                        <div class="registration_btn_wrap">
                            <button class="popup_close badge_dark">Cancel</button>
                            <button type="submit" class="mi-1 badge_success"><?php save(); ?>Update</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
        <!-- edit registration Cutoff pop up -->
        <!-- Add conference date pop up -->
        <div class="pop_up_body" id="addconferencedate">
            <div class="registration_pop_up">
                <div class="registration-pop_heading">
                    <span>Add Conference Date</span>
                    <p>
                        <a href="javascript:void(null)" class="popup_close icon_hover badge_danger action-transparent"><?php close(); ?></a>
                    </p>
                </div>
                <form name="frmtypeadd" method="post" action="<?= $cfg['SECTION_BASE_URL'] ?>manage_cutoff.process.php" id="frmtypeadd" onsubmit="return onSubmitAction();">
                    <input type="hidden" name="act" value="insertDate" />
                    <input type="hidden" name="chk_country_add" id="chk_country_add" value="0" />
                    <div class="registration-pop_body">
                        <div class="registration-pop_body_box">
                            <div class="registration-pop_body_box_inner p-0 bg-transparent border-0">
                                <div class="form_grid">
                                    <div class="frm_grp span_4">
                                        <p class="frm-head">Conference Date</p>
                                        <input type="date" name="conf_date" id="conf_date" class="validate[required]" required="" />

                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="registration-pop_footer">
                        <div class="registration_btn_wrap">
                            <button class="popup_close badge_dark">Cancel</button>
                            <button type="submit" class="mi-1 badge_success"><?php save(); ?>Save</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
        <!-- Add conference date pop up -->
        <!-- Add workshop Cutoff pop up -->
        <div class="pop_up_body" id="addworkshopcutoff">
            <div class="registration_pop_up">
                <div class="registration-pop_heading">
                    <span>Add Workshop Cutoff</span>
                    <p>
                        <a href="javascript:void(null)" class="popup_close icon_hover badge_danger action-transparent"><?php close(); ?></a>
                    </p>
                </div>
                <form name="frmtypeadd" method="post" action="<?= $cfg['SECTION_BASE_URL'] ?>manage_cutoff.process.php" id="frmtypeadd" onsubmit="return onSubmitAction();">
                    <input type="hidden" name="act" value="insertWorkshop" />
                    <input type="hidden" name="chk_country_add" id="chk_country_add" value="0" />
                    <div class="registration-pop_body">
                        <div class="registration-pop_body_box">
                            <div class="registration-pop_body_box_inner p-0 bg-transparent border-0">
                                <div class="form_grid">
                                    <div class="frm_grp span_4">
                                        <p class="frm-head">Workshop Cutoff Title</p>
                                        <input type="text" name="workshop_add" id="workshop_add" class="validate[required]" required="" />

                                    </div>
                                    <div class="frm_grp span_2">
                                        <p class="frm-head">Start Date</p>
                                        <input type="date" name="seat_limit_add" id="seat_limit_add" class="validate[required]" onblur="countryAvailabilityAdd(this.value)" rel="splDate" required="" />
                                    </div>
                                    <div class="frm_grp span_2">
                                        <p class="frm-head">End Date</p>
                                        <input type="date" name="workshop_date_add" id="workshop_date_add" rel="splDate" required="" />
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="registration-pop_footer">
                        <div class="registration_btn_wrap">
                            <button class="popup_close badge_dark">Cancel</button>
                            <button type="submit" class="mi-1 badge_success"><?php save(); ?>Save</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
        <!-- Add workshop Cutoff pop up -->
        <!-- edit workshop Cutoff pop up -->
        <div class="pop_up_body" id="editworkshopcutoff">
            <div class="registration_pop_up">
                <div class="registration-pop_heading">
                    <span>Edit Workshop Cutoff</span>
                    <p>
                        <a href="javascript:void(null)" class="popup_close icon_hover badge_danger action-transparent"><?php close(); ?></a>
                    </p>

                </div>
                <form name="frmtypeadd" method="post" action="<?= $cfg['SECTION_BASE_URL'] ?>manage_cutoff.process.php" id="frmtypeadd" onsubmit="return onSubmitAction();">
                    <input type="hidden" name="act" value="updateWorkshop" />
                    <input type="hidden" name="cutoff_id" id="cutoff_id_workshp" />
                    <div class="registration-pop_body">
                        <div class="registration-pop_body_box">
                            <div class="registration-pop_body_box_inner p-0 bg-transparent border-0">
                                <div class="form_grid">
                                    <div class="frm_grp span_4">
                                        <p class="frm-head">Workshop Cutoff Title</p>
                                        <input type="text" name="cutoff_title" id="cutoff_title_workshp" required />

                                    </div>
                                    <div class="frm_grp span_2">
                                        <p class="frm-head">Start Date</p>
                                        <input type="date" name="start_date" id="start_date_workshp" rel="splDate" required />

                                    </div>
                                    <div class="frm_grp span_2">
                                        <p class="frm-head">End Date</p>
                                        <input type="date" name="end_date" id="end_date_workshp" rel="splDate" required />

                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="registration-pop_footer">
                        <div class="registration_btn_wrap">
                            <button class="popup_close badge_dark">Cancel</button>
                            <button type="submit" class="mi-1 badge_success"><?php save(); ?>Update</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
        <!-- edit workshop Cutoff pop up -->
        <!-- Add workshop type pop up -->
        <div class="pop_up_body" id="addworkshoptype">
            <div class="registration_pop_up">
                <div class="registration-pop_heading">
                    <span>Add Workshop Type</span>
                    <p>
                        <a href="javascript:void(null)" class="popup_close icon_hover badge_danger action-transparent"><?php close(); ?></a>
                    </p>
                </div>
                <div class="registration-pop_body">
                    <div class="registration-pop_body_box">
                        <div class="registration-pop_body_box_inner p-0 bg-transparent border-0">
                            <div class="form_grid">
                                <div class="frm_grp span_4">
                                    <p class="frm-head">Workshop Type</p>
                                    <input>
                                </div>
                                <div class="frm_grp span_2">
                                    <p class="frm-head">Status</p>
                                    <div class="cus_check_wrap">
                                        <label class="cus_check gender_check">
                                            <input type="radio" name="workshopstatus">
                                            <span class="checkmark">Active</span>
                                        </label>
                                        <label class="cus_check gender_check">
                                            <input type="radio" name="workshopstatus">
                                            <span class="checkmark">Inactive</span>
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="registration-pop_footer">
                    <div class="registration_btn_wrap">
                        <button class="popup_close badge_dark">Cancel</button>
                        <button type="submit" class="mi-1 badge_success"><?php save(); ?>Save</button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Add workshop type pop up -->
        <!-- Add workshop pop up -->
        <form name="frmtypeadd"
            id="frmtypeadd"
            method="post"
            action="https://ruedakolkata.com/natcon_25/conference_registration/webmaster/manage_workshop.process.php"
            onsubmit="return onSubmitAction();">

            <input type="hidden" name="act" value="insert">
            <input type="hidden" name="chk_country_add" id="chk_country_add" value="0">

            <?php foreach ($searchArray as $key => $val) { ?>
                <input type="hidden" name="<?= $key ?>" value="<?= $val ?>">
            <?php  } ?>

            <div class="pop_up_body" id="addworkshop">
                <div class="registration_pop_up">
                    <div class="registration-pop_heading">
                        <span>Add Workshop</span>
                        <p>
                            <a href="javascript:void(null)" class="popup_close icon_hover badge_danger action-transparent"><?php close(); ?></a>
                        </p>
                    </div>
                    <div class="registration-pop_body">
                        <div class="registration-pop_body_box">
                            <div class="registration-pop_body_box_inner p-0 bg-transparent border-0">
                                <div class="form_grid">
                                    <div class="frm_grp span_4">
                                        <p class="frm-head">Workshop Title <span class="mandatory">*</span></p>
                                        <input type="text" name="workshop_add" id="workshop_add"
                                            class="validate[required]" required="" />
                                    </div>
                                    <div class="frm_grp span_4">
                                        <p class="frm-head">Workshop Type <span class="mandatory">*</span></p>
                                        <select name="workshop_type" required="">
                                            <option value="">--select type--</option>
                                            <option value="MASTER CLASS">MASTER CLASS</option>
                                            <option value="WORKSHOP">WORKSHOP</option>
                                            <option value="NORMAL">NORMAL</option>
                                            <option value="BREAKFAST">BREAKFAST</option>
                                            <option value="CADAVER">CADAVER</option>
                                        </select>
                                    </div>
                                    <div class="frm_grp span_3">
                                        <p class="frm-head">Venue <span class="mandatory">*</span></p>
                                        <input type="text" name="venue" id="venue"
                                            required="" />
                                    </div>
                                    <div class="frm_grp span_1">
                                        <p class="frm-head">Seat Limit <span class="mandatory">*</span></p>
                                        <input type="text" name="seat_limit_add" id="seat_limit_add"
                                            class="validate[required]" required="" />
                                    </div>
                                    <div class="frm_grp span_2">
                                        <p class="frm-head">WorkShop Date</p>
                                        <input type="date" name="workshop_date_add" id="workshop_date_add" rel="splDate" required="" />
                                    </div>
                                    <div class="frm_grp span_2">
                                        <p class="frm-head">Status <span class="mandatory">*</span></p>
                                        <div class="cus_check_wrap">
                                            <label class="cus_check gender_check">
                                                <input type="radio" name="workshopstatus" value="A">
                                                <span class="checkmark">Active</span>
                                            </label>
                                            <label class="cus_check gender_check">
                                                <input type="radio" name="workshopstatus" value="I">
                                                <span class="checkmark">Inactive</span>
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="registration-pop_footer">
                        <div class="registration_btn_wrap">
                            <button class="popup_close badge_dark">Cancel</button>
                            <button type="submit" class="mi-1 badge_success"><?php save(); ?>Save</button>
                        </div>
                    </div>
                </div>
            </div>
        </form>
        <!-- Add workshop pop up -->
        <!-- Edit workshop pop up -->
        <div class="pop_up_body" id="editworkshop">
            <div class="registration_pop_up">
                <div class="registration-pop_heading">
                    <span>Edit Workshop</span>
                    <p>
                        <a href="javascript:void(0)" class="popup_close icon_hover badge_danger action-transparent"><?php close(); ?></a>
                    </p>
                </div>

                <!-- Form for editing -->
                <form name="frmtypeadd" method="post" action="https://ruedakolkata.com/natcon_25/conference_registration/webmaster/manage_workshop.process.php" id="frmtypeadd">
                    <input type="hidden" name="act" value="update" />
                    <input type="hidden" name="workshop_id" id="workshop_id" />
                    <div class="registration-pop_body">
                        <div class="registration-pop_body_box">
                            <div class="registration-pop_body_box_inner p-0 bg-transparent border-0">
                                <div class="form_grid">
                                    <div class="frm_grp span_4">
                                        <p class="frm-head">Workshop Title</p>
                                        <input type="text" name="workshop_Edit" id="workshop_Edit" class="validate[required]" required />
                                    </div>
                                    <div class="frm_grp span_4">
                                        <p class="frm-head">Workshop Type</p>
                                        <select id="workshop_type" name="workshop_type" required>
                                            <option value="MASTER CLASS">MASTER CLASS</option>
                                            <option value="WORKSHOP">WORKSHOP</option>
                                            <option value="NORMAL">NORMAL</option>
                                            <option value="BREAKFAST">BREAKFAST</option>
                                            <option value="CADAVER">CADAVER</option>
                                        </select>
                                    </div>
                                    <div class="frm_grp span_3">
                                        <p class="frm-head">Venue</p>
                                        <input type="text" name="venueEdit" id="venueEdit" required />
                                    </div>
                                    <div class="frm_grp span_1">
                                        <p class="frm-head">Seat Limit</p>
                                        <input type="text" name="seat_limit_Edit" id="seat_limit_Edit"
                                            class="validate[required]" required />
                                    </div>
                                    <div class="frm_grp span_2">
                                        <p class="frm-head">Workshop Date</p>
                                        <input type="date" name="workshop_date_Edit" id="workshop_date_Edit" rel="splDate" required />
                                    </div>
                                    <div class="frm_grp span_2">
                                        <p class="frm-head">Status</p>
                                        <div class="cus_check_wrap">
                                            <label class="cus_check gender_check">
                                                <input type="radio" id="edit_status_active" name="workshopstatus" value="A">
                                                <span class="checkmark">Active</span>
                                            </label>
                                            <label class="cus_check gender_check">
                                                <input type="radio" id="edit_status_inactive" name="workshopstatus" value="I">
                                                <span class="checkmark">Inactive</span>
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="registration-pop_footer">
                        <div class="registration_btn_wrap">
                            <button class="popup_close badge_dark">Cancel</button>
                            <button type="submit" class="mi-1 badge_success"><?php save(); ?> Update</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
        <!-- Edit workshop pop up -->
        <!-- Edit workshop tariff pop up -->
        <div class="pop_up_body" id="editworkshoptariff">
            <div class="registration_pop_up">
                <div class="registration-pop_heading">
                    <span>Update Workshop Tariff</span>
                    <p>
                        <a href="javascript:void(0)" class="popup_close icon_hover badge_danger action-transparent"><?php close(); ?></a>
                    </p>
                </div>
                <form name="frmTariffEdit" id="frmTariffEdit" method="post" action="workshop_tariff.process.php" onsubmit="return onSubmitAction();">
                    <input type="hidden" name="act" id="act" value="update" />
                    <input type="hidden" name="workshop_classification_id" value="<?= $WorkshopId ?>" />
                    <input type="hidden" name="registration_classification_id" value="<?= $RegClassId ?>" />

                    <div class="registration-pop_body">
                        <div class="registration-pop_body_box">
                            <div class="registration-pop_body_box_inner p-0 bg-transparent border-0">
                                <div class="form_grid">
                                    <div class="frm_grp span_2">
                                        <p class="frm-head">Workshop Title</p>
                                        <p class="typed_data"><?= $title ?></p>
                                    </div>
                                    <div class="frm_grp span_2">
                                        <p class="frm-head">Workshop Type</p>
                                        <p class="typed_data"><?= $classification ?></p>
                                    </div>
                                    <?
                                    $sql    =    array();
                                    $sql['QUERY'] = "SELECT cutoff.cutoff_title,cutoff.id 
                                                    FROM " . _DB_WORKSHOP_CUTOFF_ . " cutoff
                                                    WHERE status = ?";
                                    $sql['PARAM'][]  = array('FILD' => 'status',  'DATA' => 'A',  'TYP' => 's');
                                    $res = $mycms->sql_select($sql);

                                    foreach ($res as $key => $title) {
                                        $rowsTariffAmount = [
                                            'inr_amount' => '',
                                            'usd_amount' => ''
                                        ];
                                    ?>
                                        <div class="registration-pop_body_box_inner span_2">

                                            <h4 class="registration-pop_body_box_heading">
                                                <span><?= strip_tags($title['cutoff_title']) ?></span>
                                            </h4>
                                            <?


                                            $sqlFetchTariffAmount1 = array();
                                            $sqlFetchTariffAmount1['QUERY'] = "SELECT * 
                                                                        FROM " . _DB_TARIFF_WORKSHOP_ . " 
                                                                        WHERE `workshop_id` = ? 
                                                                        AND `tariff_cutoff_id` = ?
                                                                        AND `registration_classification_id` = ?";
                                            $sqlFetchTariffAmount1['PARAM'][] = array('FILD' => 'workshop_id', 'DATA' => $WorkshopId, 'TYP' => 's');
                                            $sqlFetchTariffAmount1['PARAM'][] = array('FILD' => 'tariff_cutoff_id', 'DATA' => $title['id'], 'TYP' => 's');
                                            $sqlFetchTariffAmount1['PARAM'][] = array('FILD' => 'registration_classification_id', 'DATA' => $RegClassId, 'TYP' => 's');
                                            $resultFetchTariffAmount1 = $mycms->sql_select($sqlFetchTariffAmount1);
                                            $sqlRegClasf      = array();

                                            $sqlRegClasf['QUERY']    = "SELECT `classification_title`,`id`,`currency` 
                                                                    FROM " . _DB_REGISTRATION_CLASSIFICATION_ . " WHERE `id` = '" . $RegClassId . "'";
                                            $resRegClasf            = $mycms->sql_select($sqlRegClasf);

                                            if (!empty($resultFetchTariffAmount1)) {
                                                $rowsTariffAmount = $resultFetchTariffAmount1[0];
                                                // print_r($rowsTariffAmount);
                                            ?>
                                            <?
                                            } else {

                                                $rowsTariffAmount = [
                                                    'inr_amount' => '0.00',
                                                    'usd_amount' => '0.00',
                                                ];
                                            }

                                            $currency = !empty($resRegClasf) ? $resRegClasf[0]['currency'] : '';
                                            ?>

                                            <input type="hidden" name="tariff_cutoff_id_edit[]" id="tariff_cutoff_id_edit_<?= $title['id'] ?>" value="<?= $title['id'] ?>" />
                                            <input type="hidden" class="currencyClass" name="currency[<?= $title['id'] ?>]" id="currency_<?= $title['id'] ?>" value="<?= $currency ?>" />
                                            <div class="form_grid">
                                                <div class="frm_grp span_2">
                                                    <p class="frm-head">INR</p>
                                                    <input value="<?= $rowsTariffAmount['inr_amount'] ?>" name="tariff_inr_cutoff_id_edit[<?= $title['id'] ?>]" id="tariff_inr_first_cutoff_id_edit_<?= $title['id'] ?>">
                                                </div>
                                                <div class="frm_grp span_2">
                                                    <p class="frm-head">USD</p>
                                                    <input value="<?= $rowsTariffAmount['usd_amount'] ?>" name="tariff_usd_cutoff_id_edit[<?= $title['id'] ?>]" id="tariff_usd_first_cutoff_id_edit_<?= $title['id'] ?>">
                                                </div>
                                            </div>
                                        </div>
                                    <?
                                    }
                                    ?>

                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="registration-pop_footer">
                        <div class="registration_btn_wrap">
                            <button class="popup_close badge_dark">Cancel</button>
                            <button type="submit" class="mi-1 badge_success">Update</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
        <!-- Edit workshop tariff pop up -->
       <!-- New hotel pop up -->
        <div class="pop_up_body" id="newhotel">
            <div class="registration_pop_up">
                <div class="registration-pop_heading">
                    <span>Add Hotel</span>
                    <p>
                        <a href="javascript:void(null)" class="popup_close icon_hover badge_danger action-transparent"><?php close(); ?></a>
                    </p>
                </div>
                <form name="frmInsert" id="frmInsert" action="<?= $cfg['SECTION_BASE_URL'] ?>hotel_listing.process.php" method="post" enctype="multipart/form-data" onsubmit="return onSubmitAction();">
	         	    <input type="hidden" name="act" value="insert" />
                    <div class="registration-pop_body">
                        <div class="registration-pop_body_box">
                            <div class="registration-pop_body_box_inner">
                                <h4 class="registration-pop_body_box_heading">
                                    <span>Hotel Details</span>
                                </h4>
                                <div class="form_grid">
                                    <div class="frm_grp span_3">
                                        <p class="frm-head">Hotel Name</p>
                                        <input name="hotel_name_add" id="hotel_name_add" required="" />

                                    </div>
                                    <div class="frm_grp span_1">
                                        <p class="frm-head">Ratings</p>
                                         <select  name="hotelRatings">
                                            <option value="">Select</option>
                                            <option value="1">1</option>
                                            <option value="2">2</option>
                                            <option value="3">3</option>
                                            <option value="4">4</option>
                                            <option value="5">5</option>

                                        </select>
                                    </div>
                                    <div class="frm_grp span_2">
                                        <p class="frm-head">Hotel Phone No</p>
                                        <input type="number" name="hotel_phone_add" id="hotel_phone_add" required="" />

                                    </div>
                                    <div class="frm_grp span_2">
                                        <p class="frm-head">Distance From Venue (Km)</p>
                                        <input type="number" name="distance_from_venue_add" id="distance_from_venue_add" required="" />
                                    </div>
                                    <div class="frm_grp span_2">
                                        <p class="frm-head">Check In Date</p>
                                        <input type="date" name="check_in" id="check_in" required="" onchange="updateSeatLimits();" />

                                    </div>
                                    <div class="frm_grp span_2">
                                        <p class="frm-head">Check Out Date</p>
                                        <input type="date" name="check_out" id="check_out" required="" onchange="updateSeatLimits();" />
                                    </div>
                                    <div class="frm_grp span_4">
                                        <p class="frm-head">Address</p>
                                        <input  name="hotel_address_add" id="hotel_address_add"  required="">
                                    </div>
                                </div>
                            </div>
                            <div class="registration-pop_body_box_inner" id="packageForm_1">
                                <h4 class="registration-pop_body_box_heading">
                                    <span>Packages</span>
                                    <a class="add mi-1" id="add_package_1"><?php add(); ?>Add</a>
                                </h4>
                                <div class="accm_add_wrap" id="accm_add_wrap_1">
                                    <h6 class="accm_add_empty" id="accm_add_empty_1">No Package Available</h6>
                                    <div class="accm_add_box" id="package_box" style="display:none;">
                                        <div class="form_grid">
                                            <div class="frm_grp span_4">
                                                <p class="frm-head">Package Name</p>
                                                <input name="package_name[]">
                                            </div>

                                            <a href="#" onclick="hidePackageBox(); return false;" class="accm_delet icon_hover badge_danger action-transparent"><?php delete(); ?></a>
                                        </div>
                                    </div>
                                  
                                </div>
                            </div>
                        </div>
                        <div class="registration-pop_body_box">
                            <div class="registration-pop_body_box_inner"  id="aminityForm_1">
                                <h4 class="registration-pop_body_box_heading">
                                    <span>Aminites</span>
                                    <a class="add mi-1" id="add_aminity_1"><?php add(); ?>Add</a>
                                </h4>
                                <div class="accm_add_wrap"  id="accm_add_wrap_2">
                                    <h6 class="accm_add_empty"  id="accm_add_empty_2">No Aminity Available</h6>
                                    <div class="accm_add_box" id="aminity_box" style="display:none;">
                                        <div class="form_grid">
                                            <div class="frm_grp span_3">
                                                <p class="frm-head">Aminity Name</p>
                                                <input name="accessories_name[]">
                                            </div>
                                           <div class="frm_grp span_1 iconBox">
                                                <p class="frm-head">Icon</p>

                                                <input type="file"
                                                    name="accessories_icon[]"
                                                    class="icon_input"
                                                    accept="image/*"
                                                    style="display:none;">

                                                <label class="frm-image uploadIcon">
                                                    <?php upload() ?>
                                                </label>

                                                <div class="frm_img_preview" style="display:none;">
                                                    <img class="iconPreview"
                                                        src="https://ruedakolkata.com/natcon2025/uploads/EMAIL.HEADER.FOOTER.IMAGE/HOTELICON_0_0001_250107125112.png">
                                                    <button type="button" class="removeIcon">
                                                        <?php delete() ?>
                                                    </button>
                                                </div>
                                            </div>

                                            <a href="#" class="accm_delet icon_hover badge_danger action-transparent"><?php delete(); ?></a>

                                        </div>
                                    </div>
                                  
                                </div>
                            </div>
                            <div class="registration-pop_body_box_inner" id="RoomForm_1">
                                <h4 class="registration-pop_body_box_heading">
                                    <span>Room Type</span>
                                    <a class="add mi-1" id="add_room_1"><?php add(); ?>Add</a>
                                </h4>
                                <div class="accm_add_wrap" id="accm_add_wrap_3">
                                    <h6 class="accm_add_empty" id="accm_add_empty_3">No Room Type Available</h6>
                                    <div class="accm_add_box" id="room_box" style="display:none;">
                                        <div class="form_grid">
                                            <div class="frm_grp span_2">
                                                <p class="frm-head">Room Type</p>
                                                <input name="room_type[]">
                                            </div>
                                            <div class="frm_grp span_2 roomImageBox">
                                                <p class="frm-head">Room Image</p>

                                                <input type="file"
                                                    name="room_type_image[]"
                                                    class="room_input"
                                                    accept="image/*"
                                                    style="display:none;">

                                                <label class="frm-image uploadRoomImage">
                                                    <?php upload() ?>
                                                </label>

                                                <div class="frm_img_preview" style="display:none;">
                                                    <img class="roomPreview"
                                                        src="https://ruedakolkata.com/natcon2025/uploads/EMAIL.HEADER.FOOTER.IMAGE/HOTELICON_0_0001_250107125112.png">
                                                    <button type="button" class="removeRoomImage">
                                                        <?php delete() ?>
                                                    </button>
                                                </div>
                                            </div>
                                            <a href="#" class="accm_delet icon_hover badge_danger action-transparent"><?php delete(); ?></a>
                                        </div>
                                    </div>
                                  
                                </div>
                            </div>
                        </div>
                        <div class="registration-pop_body_box">
                            <div class="registration-pop_body_box_inner">
                                <h4 class="registration-pop_body_box_heading">
                                    <span>Seat Limits</span>
                                </h4>
                                <div class="form_grid">
                                    <div class="registration-pop_body_box_inner span_4">
                                        <div class="form_grid" id="seat_limits_container">
                                            <div class="frm_grp span_2">
                                                <p class="frm-head">Date</p>
                                                <p class="typed_data"></p>
                                            </div>
                                            <div class="frm_grp span_2">
                                                <p class="frm-head">Seat</p>
                                                <input  name="seat_limit[]" >
                                            </div>
                                        </div>
                                    </div>
                                
                                </div>
                            </div>
                            <div class="registration-pop_body_box_inner">
                                <h4 class="registration-pop_body_box_heading">
                                    <span>Hotel Images</span>
                                </h4>
                                <div class="com_info_branding_wrap form_grid g_2">
                                  <div class="com_info_branding_box addBox">
                                        <div class="branding_image_preview">
                                            <img class="previewImage" src="images/Banner KTC BG.png" alt="">
                                            <button class="removeImage" type="button"><i class="fal fa-trash-alt"></i></button>
                                        </div>
                                        <div class="branding_image_upload">
                                            <input type="file" name="slider_image[]" class="webmaster_background" style="display:none" accept="image/*">
                                            <label for="webmaster_background" class="uploadLabel"><span><?php upload() ?></span></label>
                                        </div>
                                    </div>
                                    <div class="com_info_branding_box addBox">
                                        <div class="branding_image_preview">
                                            <img class="previewImage" src="images/Banner KTC BG.png" alt="">
                                            <button class="removeImage"><i class="fal fa-trash-alt"></i></button>
                                        </div>
                                        <div class="branding_image_upload" >
                                            <input style="display: none;"name="slider_image[]"  class="webmaster_background" type="file"  accept="image/*">
                                            <label for="webmaster_background" class="uploadLabel">
                                                <span><?php upload() ?></span>
                                            </label>
                                        </div>
                                    </div>
                                    <div class="com_info_branding_box addBox">
                                        <div class="branding_image_preview">
                                            <img class="previewImage" src="images/Banner KTC BG.png" alt="">
                                            <button class="removeImage"><i class="fal fa-trash-alt"></i></button>
                                        </div>
                                        <div class="branding_image_upload">
                                            <input style="display: none;" name="slider_image[]" class="webmaster_background" type="file"  accept="image/*">
                                            <label for="webmaster_background" class="uploadLabel">
                                                <span><?php upload() ?></span>
                                            </label>
                                        </div>
                                    </div>
                                    <div class="com_info_branding_box addBox">
                                        <div class="branding_image_preview">
                                            <img class="previewImage" src="images/Banner KTC BG.png" alt="">
                                            <button class="removeImage"><i class="fal fa-trash-alt"></i></button>
                                        </div>
                                        <div class="branding_image_upload">
                                            <input style="display: none;" name="slider_image[]" class="webmaster_background" type="file"  accept="image/*">
                                            <label for="webmaster_background" class="uploadLabel">
                                                <span><?php upload() ?></span>
                                            </label>
                                        </div>
                                    </div>
                                    <div class="com_info_branding_box addBox">
                                        <div class="branding_image_preview">
                                            <img class="previewImage" src="images/Banner KTC BG.png" alt="">
                                            <button class="removeImage"><i class="fal fa-trash-alt"></i></button>
                                        </div>
                                        <div class="branding_image_upload">
                                            <input style="display: none;" name="slider_image[]" class="webmaster_background" type="file"  accept="image/*">
                                            <label for="webmaster_background" class="uploadLabel">
                                                <span><?php upload() ?></span>
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>
                    <div class="registration-pop_footer">
                        <div class="registration_btn_wrap">
                            <button class="popup_close badge_dark">Cancel</button>
                            <button type="submit" class="mi-1 badge_success"><?php save(); ?>Add Hotel</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
        <!-- New hotel pop up -->
          <!-- <div class="pop_up_body" id="edithotel">
            <div class="registration_pop_up">
                <div class="registration-pop_heading">
                    <span>Edit Hotel</span>
                    <p>
                        <a href="javascript:void(null)" class="popup_close icon_hover badge_danger action-transparent"><?php close(); ?></a>
                    </p>
                </div>
                <form name="frmInsert" id="frmInsert" action="<?= $cfg['SECTION_BASE_URL'] ?>hotel_listing.process.php" method="post" enctype="multipart/form-data" onsubmit="return onSubmitAction();">
	         	    <input type="hidden" name="act" value="insert" />
                    <div class="registration-pop_body">
                        <div class="registration-pop_body_box">
                            <div class="registration-pop_body_box_inner">
                                <h4 class="registration-pop_body_box_heading">
                                    <span>Hotel Details</span>
                                </h4>
                                <div class="form_grid">
                                    <div class="frm_grp span_3">
                                        <p class="frm-head">Hotel Name</p>
                                        <input name="hotel_name_add" id="hotel_name_add" required="" />

                                    </div>
                                    <div class="frm_grp span_1">
                                        <p class="frm-head">Ratings</p>
                                         <select  name="hotelRatings">
                                            <option value="">Select</option>
                                            <option value="1">1</option>
                                            <option value="2">2</option>
                                            <option value="3">3</option>
                                            <option value="4">4</option>
                                            <option value="5">5</option>

                                        </select>
                                    </div>
                                    <div class="frm_grp span_2">
                                        <p class="frm-head">Hotel Phone No</p>
                                        <input type="number" name="hotel_phone_add" id="hotel_phone_add" required="" />

                                    </div>
                                    <div class="frm_grp span_2">
                                        <p class="frm-head">Distance From Venue (Km)</p>
                                        <input type="number" name="distance_from_venue_add" id="distance_from_venue_add" required="" />
                                    </div>
                                    <div class="frm_grp span_2">
                                        <p class="frm-head">Check In Date</p>
                                        <input type="date" name="check_in" id="check_in" required="" onchange="updateSeatLimits();" />

                                    </div>
                                    <div class="frm_grp span_2">
                                        <p class="frm-head">Check Out Date</p>
                                        <input type="date" name="check_out" id="check_out" required="" onchange="updateSeatLimits();" />
                                    </div>
                                    <div class="frm_grp span_4">
                                        <p class="frm-head">Address</p>
                                        <input  name="hotel_address_add" id="hotel_address_add"  required="">
                                    </div>
                                </div>
                            </div>
                            <div class="registration-pop_body_box_inner" id="packageForm_1">
                                <h4 class="registration-pop_body_box_heading">
                                    <span>Packages</span>
                                    <a class="add mi-1" id="add_package_1"><?php add(); ?>Add</a>
                                </h4>
                                <div class="accm_add_wrap" id="accm_add_wrap_1">
                                    <h6 class="accm_add_empty" id="accm_add_empty_1">No Package Available</h6>
                                    <div class="accm_add_box" id="package_box" style="display:none;">
                                        <div class="form_grid">
                                            <div class="frm_grp span_4">
                                                <p class="frm-head">Package Name</p>
                                                <input name="package_name[]">
                                            </div>

                                            <a href="#" onclick="hidePackageBox(); return false;" class="accm_delet icon_hover badge_danger action-transparent"><?php delete(); ?></a>
                                        </div>
                                    </div>
                                  
                                </div>
                            </div>
                        </div>
                        <div class="registration-pop_body_box">
                            <div class="registration-pop_body_box_inner"  id="aminityForm_1">
                                <h4 class="registration-pop_body_box_heading">
                                    <span>Aminites</span>
                                    <a class="add mi-1" id="add_aminity_1"><?php add(); ?>Add</a>
                                </h4>
                                <div class="accm_add_wrap"  id="accm_add_wrap_2">
                                    <h6 class="accm_add_empty"  id="accm_add_empty_2">No Aminity Available</h6>
                                    <div class="accm_add_box" id="aminity_box" style="display:none;">
                                        <div class="form_grid">
                                            <div class="frm_grp span_3">
                                                <p class="frm-head">Aminity Name</p>
                                                <input name="accessories_name[]">
                                            </div>
                                           <div class="frm_grp span_1 iconBox">
                                                <p class="frm-head">Icon</p>

                                                <input type="file"
                                                    name="accessories_icon[]"
                                                    class="icon_input"
                                                    accept="image/*"
                                                    style="display:none;">

                                                <label class="frm-image uploadIcon">
                                                    <?php upload() ?>
                                                </label>

                                                <div class="frm_img_preview" style="display:none;">
                                                    <img class="iconPreview"
                                                        src="https://ruedakolkata.com/natcon2025/uploads/EMAIL.HEADER.FOOTER.IMAGE/HOTELICON_0_0001_250107125112.png">
                                                    <button type="button" class="removeIcon">
                                                        <?php delete() ?>
                                                    </button>
                                                </div>
                                            </div>

                                            <a href="#" class="accm_delet icon_hover badge_danger action-transparent"><?php delete(); ?></a>

                                        </div>
                                    </div>
                                  
                                </div>
                            </div>
                            <div class="registration-pop_body_box_inner" id="RoomForm_1">
                                <h4 class="registration-pop_body_box_heading">
                                    <span>Room Type</span>
                                    <a class="add mi-1" id="add_room_1"><?php add(); ?>Add</a>
                                </h4>
                                <div class="accm_add_wrap" id="accm_add_wrap_3">
                                    <h6 class="accm_add_empty" id="accm_add_empty_3">No Room Type Available</h6>
                                    <div class="accm_add_box" id="room_box" style="display:none;">
                                        <div class="form_grid">
                                            <div class="frm_grp span_2">
                                                <p class="frm-head">Room Type</p>
                                                <input name="room_type[]">
                                            </div>
                                            <div class="frm_grp span_2 roomImageBox">
                                                <p class="frm-head">Room Image</p>

                                                <input type="file"
                                                    name="room_type_image[]"
                                                    class="room_input"
                                                    accept="image/*"
                                                    style="display:none;">

                                                <label class="frm-image uploadRoomImage">
                                                    <?php upload() ?>
                                                </label>

                                                <div class="frm_img_preview" style="display:none;">
                                                    <img class="roomPreview"
                                                        src="https://ruedakolkata.com/natcon2025/uploads/EMAIL.HEADER.FOOTER.IMAGE/HOTELICON_0_0001_250107125112.png">
                                                    <button type="button" class="removeRoomImage">
                                                        <?php delete() ?>
                                                    </button>
                                                </div>
                                            </div>
                                            <a href="#" class="accm_delet icon_hover badge_danger action-transparent"><?php delete(); ?></a>
                                        </div>
                                    </div>
                                   
                                </div>
                            </div>
                        </div>
                        <div class="registration-pop_body_box">
                            <div class="registration-pop_body_box_inner">
                                <h4 class="registration-pop_body_box_heading">
                                    <span>Seat Limits</span>
                                </h4>
                                <div class="form_grid">
                                    <div class="registration-pop_body_box_inner span_4">
                                        <div class="form_grid" id="seat_limits_container">
                                            <div class="frm_grp span_2">
                                                <p class="frm-head">Date</p>
                                                <p class="typed_data"></p>
                                            </div>
                                            <div class="frm_grp span_2">
                                                <p class="frm-head">Seat</p>
                                                <input  name="seat_limit[]" >
                                            </div>
                                        </div>
                                    </div>
                                
                                </div>
                            </div>
                            <div class="registration-pop_body_box_inner">
                                <h4 class="registration-pop_body_box_heading">
                                    <span>Hotel Images</span>
                                </h4>
                                <div class="com_info_branding_wrap form_grid g_2">
                                  <div class="com_info_branding_box addBox">
                                        <div class="branding_image_preview">
                                            <img class="previewImage" src="images/Banner KTC BG.png" alt="">
                                            <button class="removeImage" type="button"><i class="fal fa-trash-alt"></i></button>
                                        </div>
                                        <div class="branding_image_upload">
                                            <input type="file" name="slider_image[]" class="webmaster_background" style="display:none" accept="image/*">
                                            <label for="webmaster_background" class="uploadLabel"><span><?php upload() ?></span></label>
                                        </div>
                                    </div>
                                    <div class="com_info_branding_box addBox">
                                        <div class="branding_image_preview">
                                            <img class="previewImage" src="images/Banner KTC BG.png" alt="">
                                            <button class="removeImage"><i class="fal fa-trash-alt"></i></button>
                                        </div>
                                        <div class="branding_image_upload" >
                                            <input style="display: none;"name="slider_image[]"  class="webmaster_background" type="file"  accept="image/*">
                                            <label for="webmaster_background" class="uploadLabel">
                                                <span><?php upload() ?></span>
                                            </label>
                                        </div>
                                    </div>
                                    <div class="com_info_branding_box addBox">
                                        <div class="branding_image_preview">
                                            <img class="previewImage" src="images/Banner KTC BG.png" alt="">
                                            <button class="removeImage"><i class="fal fa-trash-alt"></i></button>
                                        </div>
                                        <div class="branding_image_upload">
                                            <input style="display: none;" name="slider_image[]" class="webmaster_background" type="file"  accept="image/*">
                                            <label for="webmaster_background" class="uploadLabel">
                                                <span><?php upload() ?></span>
                                            </label>
                                        </div>
                                    </div>
                                    <div class="com_info_branding_box addBox">
                                        <div class="branding_image_preview">
                                            <img class="previewImage" src="images/Banner KTC BG.png" alt="">
                                            <button class="removeImage"><i class="fal fa-trash-alt"></i></button>
                                        </div>
                                        <div class="branding_image_upload">
                                            <input style="display: none;" name="slider_image[]" class="webmaster_background" type="file"  accept="image/*">
                                            <label for="webmaster_background" class="uploadLabel">
                                                <span><?php upload() ?></span>
                                            </label>
                                        </div>
                                    </div>
                                    <div class="com_info_branding_box addBox">
                                        <div class="branding_image_preview">
                                            <img class="previewImage" src="images/Banner KTC BG.png" alt="">
                                            <button class="removeImage"><i class="fal fa-trash-alt"></i></button>
                                        </div>
                                        <div class="branding_image_upload">
                                            <input style="display: none;" name="slider_image[]" class="webmaster_background" type="file"  accept="image/*">
                                            <label for="webmaster_background" class="uploadLabel">
                                                <span><?php upload() ?></span>
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>
                    <div class="registration-pop_footer">
                        <div class="registration_btn_wrap">
                            <button class="popup_close badge_dark">Cancel</button>
                            <button type="submit" class="mi-1 badge_success"><?php save(); ?>Add Hotel</button>
                        </div>
                    </div>
                </form>
            </div>
        </div> -->
        <!-- edit hotel pop up -->
        <div class="pop_up_body" id="edithotel">
            <div class="registration_pop_up">
                <div class="registration-pop_heading">
                    <span>Edit Hotel</span>
                    <p>
                        <a href="javascript:void(null)" class="popup_close icon_hover badge_danger action-transparent"><?php close(); ?></a>
                    </p>
                </div>
                <form name="frmUpdate" id="frmUpdate" action="hotel_listing.process.php" method="post" enctype="multipart/form-data" onsubmit="return onSubmitAction();">
                <input type="hidden" name="act" id="act" value="update" />
                <input type="hidden" name="id" id="id" value="<?= $editHotelId?>" />
                <div class="registration-pop_body">
                    <div class="registration-pop_body_box">
                        <div class="registration-pop_body_box_inner">
                            <h4 class="registration-pop_body_box_heading">
                                <span>Hotel Details</span>
                            </h4>
                              <?php
                                    $sqlFetchHotel				=	array();
                                    $sqlFetchHotel['QUERY']		=	"SELECT * 
                                                                    FROM " . _DB_MASTER_HOTEL_ . "
                                                                    WHERE `id` 		= 	 ? ";

                                    $sqlFetchHotel['PARAM'][]	=	array('FILD' => 'id', 		'DATA' => $editHotelId, 		'TYP' => 's');
                                    $resultFetchHotel    		= $mycms->sql_select($sqlFetchHotel);
                                  	$sql1			=	array();
									$sql1['QUERY'] = "SELECT min(`check_in_date`) AS checkin								
										        		  FROM " . _DB_ACCOMMODATION_CHECKIN_DATE_ . "
										        		  WHERE hotel_id = ?
										        		    AND status != ?";
									$sql1['PARAM'][]	=	array('FILD' => 'hotel_id', 'DATA' => $editHotelId, 'TYP' => 's');
									$sql1['PARAM'][]	=	array('FILD' => 'status', 	 'DATA' => 'D', 						'TYP' => 's');
									$result = $mycms->sql_select($sql1);
									$rowFetchcheckIn           = $result[0];

									$sql2			=	array();
									$sql2['QUERY'] = "SELECT max(`check_out_date`) AS checkout								
										        		  FROM " . _DB_ACCOMMODATION_CHECKOUT_DATE_ . "
										        		  WHERE hotel_id = ?
										        		    AND status != ?";

									$sql2['PARAM'][]	=	array('FILD' => 'hotel_id', 'DATA' => $editHotelId, 'TYP' => 's');
									$sql2['PARAM'][]	=	array('FILD' => 'status', 	 'DATA' => 'D', 						'TYP' => 's');
									$result = $mycms->sql_select($sql2);
									$rowFetchcheckOut           = $result[0];

                            
                                ?>
                            <div class="form_grid">
                                <div class="frm_grp span_3">
                                    <p class="frm-head">Hotel Name <i class="mandatory">*</i></p>
                                    <input name="hotel_name_update" id="hotel_name_update"  value="<?= $resultFetchHotel[0]['hotel_name'] ?>" required />

                                </div>
                               <div class="frm_grp span_1">
                                    <p class="frm-head">Ratings</p>
                                    <select name="hotelRatings">
                                        <option value="">Select</option>
                                        <?php
                                        $ratings = [1, 2, 3, 4, 5];
                                        $selectedRating = $resultFetchHotel[0]['hotelRatings'] ?? '';
                                        foreach ($ratings as $r) {
                                            $selected = ($r == $selectedRating) ? 'selected' : '';
                                            echo "<option value=\"$r\" $selected>$r</option>";
                                        }
                                        ?>
                                    </select>
                                </div>
                                <div class="frm_grp span_2">
                                    <p class="frm-head">Hotel Phone No <i class="mandatory">*</i></p>
                                    <input name="hotel_phone_update" id="hotel_phone_update" value="<?= $resultFetchHotel[0]['hotel_phone_no'] ?>" required />

                                </div>
                                <div class="frm_grp span_2">
                                    <p class="frm-head">Distance From Venue (Km) <i class="mandatory">*</i></p>
                					<input name="distance_from_venue_update" id="distance_from_venue_update" value="<?=$resultFetchHotel[0]['distance_from_venue'] ?>" required />

                                </div>
                                <div class="frm_grp span_2">
                                    <p class="frm-head">Check In Date <i class="mandatory">*</i></p>
                                    <input type="date" name="check_in" id="check_in_Edit"  required="" value="<?= $rowFetchcheckIn['checkin'] ?>"  onchange="updateSeatLimitsEdit();" >
                                </div>
                                <div class="frm_grp span_2">
                                    <p class="frm-head">Check Out Date <i class="mandatory">*</i></p>
                                    <input type="date" name="check_out" id="check_out_Edit" required="" value="<?= $rowFetchcheckOut['checkout'] ?>"  onchange="updateSeatLimitsEdit();" >
                                </div>
                                <div class="frm_grp span_4">
                                    <p class="frm-head">Address <i class="mandatory">*</i></p>
                                    <input name="hotel_address_update" id="hotel_address_update" value="<?=$resultFetchHotel[0]['hotel_address'] ?>"  >
                                </div>
                            </div>
                        </div>
                        <div class="registration-pop_body_box_inner">
                            <h4 class="registration-pop_body_box_heading">
                                <span>Packages</span>
                                <a class="add mi-1" id="edit_package_1"><?php add(); ?>Add</a>
                            </h4>
                            
                            <div class="accm_add_wrap" id="accm_edit_wrap_1">

                                <div class="accm_add_box" id="package_template" style="display:none;">
                                    <div class="form_grid">
                                        <div class="frm_grp span_4">
                                            <p class="frm-head">Package Name</p>
                                            <input name="package_name[]" value="">
                                        </div>

                                        <a href="#" onclick="hidePackageBox(); return false;" class="accm_delet icon_hover badge_danger action-transparent"><?php delete(); ?></a>

                                    </div>
                                </div>
                                 <?php
                                $sqlFetchPack				=	array();
                                $sqlFetchPack['QUERY']		=	"SELECT `package_name`,`id` 
                                                                FROM " . _DB_ACCOMMODATION_PACKAGE_ . "
                                                                WHERE `status` 		= 	 ?
                                                                AND `hotel_id` 		= 	 ? ";

                                $sqlFetchPack['PARAM'][]	=	array('FILD' => 'status', 		'DATA' => 'A', 		'TYP' => 's');
                                $sqlFetchPack['PARAM'][]	=	array('FILD' => 'hotel_id', 		'DATA' => $editHotelId, 		'TYP' => 's');

                                $resultFetchPack    		= $mycms->sql_select($sqlFetchPack);
                            
                                if ($resultFetchPack) {
                                foreach ($resultFetchPack as $keyPack => $rowFetchPack) {
                                ?>
                                <div class="accm_add_box">
                                    <div class="form_grid">
                                        <div class="frm_grp span_4">
                                            <p class="frm-head">Package Name</p>
                                           <input type="hidden" name="package_id[]" value="<?= $rowFetchPack['id'] ?>">
                                            <input name="package_name[]" value="<?= $rowFetchPack['package_name'] ?>">
                                        </div>

                                        <a href="#" class="accm_delet icon_hover badge_danger action-transparent">
                                            <?php delete(); ?>
                                        </a>
                                    </div>
                                </div>
                               <?
                                }
                                }else{
                                   ?>
                                    <h6 class="accm_add_empty" id="accm_edit_empty_1" >No Package Available</h6>
                                   <?
                                
                                }
                                ?>
                            </div>
                        </div>
                    </div>
                    <div class="registration-pop_body_box">
                       <div class="registration-pop_body_box_inner"  >
                                <h4 class="registration-pop_body_box_heading">
                                    <span>Aminites</span>
                                    <a class="add mi-1" id="edit_aminity_1"><?php add(); ?>Add</a>
                                </h4>
                                <div class="accm_add_wrap"  id="accm_edit_wrap_2">
                                    <div class="accm_add_box" id="aminity_box_edit" style="display:none;">
                                        <div class="form_grid">
                                            
                                            <div class="frm_grp span_3">
                                                <p class="frm-head">Aminity Name</p>
                                                <input name="accessories_name[]">
                                            </div>
                                           <div class="frm_grp span_1 iconBox_edit">
                                                <p class="frm-head">Icon</p>

                                                <input type="file"
                                                    name="accessories_icon[]"
                                                    class="icon_input_edit"
                                                    accept="image/*"
                                                    style="display:none;">
                                                    <input type="hidden" name="accessories_id[]" value="<?= $aminity['id'] ?? '' ?>">
                                                    <input type="hidden" name="accessories_exist_icon[]" value="<?= $aminity['accessories_icon'] ?? '' ?>">
                                                <label class="frm-image uploadIcon_edit">
                                                    <?php upload() ?>
                                                </label>

                                                <div class="frm_img_preview" style="display:none;">
                                                    <img class="iconPreview_edit"
                                                        src="https://ruedakolkata.com/natcon2025/uploads/EMAIL.HEADER.FOOTER.IMAGE/HOTELICON_0_0001_250107125112.png">
                                                    <button type="button" class="removeIcon_edit">
                                                        <?php delete() ?>
                                                    </button>
                                                </div>
                                            </div>

                                            <a href="#" class="accm_delet icon_hover badge_danger action-transparent"><?php delete(); ?></a>

                                        </div>
                                    </div>
                                    <?php
                                        $sqlAcc = array();
                                        $sqlAcc['QUERY']    = "SELECT * FROM " . _DB_ACCOMMODATION_ACCESSORIES_ . "  WHERE `hotel_id` = '" . $editHotelId . "' AND status='A' AND purpose='aminity'  ORDER BY `id` ASC";

                                        $queryAcc = $mycms->sql_select($sqlAcc, false);
                                        
                                    if ($queryAcc) { 
                                        foreach ($queryAcc as $keyqueryAcc => $aminity) {
                                         $icon = _BASE_URL_.'uploads/EMAIL.HEADER.FOOTER.IMAGE/' . $aminity['accessories_icon'];
                                    ?>
                                    <div class="accm_add_box">
                                        <div class="form_grid">
                                            <h6 class="accm_add_empty"  style="display:none;" id="accm_edit_empty_2">No Aminity Available</h6>

                                            <div class="frm_grp span_3">
                                                <p class="frm-head">Aminity Name</p>
                                                <input name="accessories_name[]" value="<?= $aminity['accessories_name'] ?>">
                                            </div>
                                           <div class="frm_grp span_1 iconBox_edit">
                                                <p class="frm-head">Icon</p>

                                                <input type="file"
                                                    name="accessories_icon[]"
                                                    class="icon_input_edit"
                                                    accept="image/*"
                                                    style="display:none;">
                                                <input type="hidden" name="accessories_id[]" value="<?= $aminity['id'] ?? '' ?>">
												<input type="hidden" name="accessories_exist_icon[]" value="<?= $aminity['accessories_icon'] ?>">
                                                <label class="frm-image uploadIcon_edit" style="display:none;">
                                                    <?php upload() ?>
                                                </label>

                                                <div class="frm_img_preview" style="display: block;">
                                                    <img class="iconPreview_edit iconPreview"
                                                        src="<?= $icon?>">
                                                    <button type="button" class="removeIcon_edit">
                                                        <?php delete() ?>
                                                    </button>
                                                </div>
                                            </div>

                                            <a href="#" class="accm_delet icon_hover badge_danger action-transparent"><?php delete(); ?></a>

                                        </div>
                                    </div>
                                 <?
                                }
                                }else{
                                   ?>
                                    <h6 class="accm_add_empty"  id="accm_edit_empty_2">No Aminity Available</h6>
                                   <?
                                
                                }
                                ?>
                                </div>
                            </div>
                        <div class="registration-pop_body_box_inner">
                            <h4 class="registration-pop_body_box_heading">
                                <span>Room Type</span>
                                <a class="add mi-1" id="edit_room_1"><?php add(); ?>Add</a>
                            </h4>
                            <div class="accm_add_wrap" id="accm_edit_wrap_3">
                                <div class="accm_add_box"  id="room_box_edit" style="display:none;">
                                    <div class="form_grid">
                                        <h6 class="accm_add_empty"  style="display:none;"  id="accm_edit_empty_3" >No Room Type Available</h6>
                                        <div class="frm_grp span_2">
                                            <p class="frm-head">Room Type</p>
                                           <input name="room_type[]">
                                        </div>
                                        <div class="frm_grp span_2 roomImageBox_edit">
                                            <p class="frm-head">Room Image</p>
                                              <input type="file"
                                                    name="room_type_image[]"
                                                    class="room_input_edit"
                                                    accept="image/*"
                                                    style="display:none;">
                                              <input type="hidden" name="room_exist_icon[]" value=" ">
											 <input type="hidden" name="room_type_id[]" value="">

                                            <label  class="frm-image uploadRoomImage_edit"><?php upload() ?></label>
                                            <div class="frm_img_preview" style="display:none;">
                                                <img class="roomPreview_edit" src="https://ruedakolkata.com/natcon2025/uploads/EMAIL.HEADER.FOOTER.IMAGE/HOTELICON_0_0001_250107125112.png">
                                                <button  type="button" class="removeRoomImage_edit"><?php delete() ?></button>
                                            </div>
                                        </div>
                                        <a href="#" class="accm_delet icon_hover badge_danger action-transparent"><?php delete(); ?></a>
                                    </div>
                                </div>
                                <?php
                                $sqlRoom = array();
                                $sqlRoom['QUERY']    = "SELECT * FROM " . _DB_ACCOMMODATION_ACCESSORIES_ . "  WHERE `hotel_id` = '" . $editHotelId . "' AND status='A' AND purpose='room'  ORDER BY `id` ASC";

                                $queryRoom = $mycms->sql_select($sqlRoom, false);
                                if ($queryRoom) { 
                                    foreach ($queryRoom as $k => $row) {
                                    $icon = _BASE_URL_ . 'uploads/EMAIL.HEADER.FOOTER.IMAGE/' . $row['accessories_icon'];

                                ?>
                                <div class="accm_add_box" >
                                    <div class="form_grid">
                                        <div class="frm_grp span_2">
                                            <p class="frm-head">Room Type</p>
                                           <input name="room_type[]" value="<?= $row['accessories_name'] ?>">
                                        </div>
                                        <div class="frm_grp span_2 roomImageBox_edit" >
                                            <p class="frm-head">Room Image</p>
                                              <input type="file"
                                                    name="room_type_image[]"
                                                    class="room_input_edit"
                                                    accept="image/*"
                                                    style="display:none;">
											 <input type="hidden" name="room_exist_icon[]" value="<?= $row['accessories_icon'] ?>">
											 <input type="hidden" name="room_type_id[]" value="<?= $row['id'] ?>">

                                            <label  class="frm-image uploadRoomImage_edit" style="display:none;"><?php upload() ?></label>
                                            <div class="frm_img_preview" style="display:block;">
                                                <img class="roomPreview_edit" src="<?= $icon ?>">
                                                <button  type="button" class="removeRoomImage_edit"><?php delete() ?></button>
                                            </div>
                                        </div>
                                        <a href="#" class="accm_delet icon_hover badge_danger action-transparent"><?php delete(); ?></a>
                                    </div>
                                </div>
                                 <?
                                }
                                }else{
                                   ?>
                                    <!-- <h6 class="accm_add_empty"  id="accm_edit_empty_3">No Room Type Available</h6> -->
                                   <?
                                
                                }
                                ?>
                            </div>
                        </div>
                    </div>
                    <div class="registration-pop_body_box">
                        <div class="registration-pop_body_box_inner">
                        <h4 class="registration-pop_body_box_heading">
                            <span>Seat Limits</span>
                        </h4>

                        <!-- SINGLE container -->
                        <div id="seat_limits_container_Edit">

                            <?php
                            $sql = array();
                            $sql['QUERY'] = "SELECT *
                                            FROM " . _DB_ACCOMMODATION_CHECKIN_DATE_ . "
                                            WHERE hotel_id = ?
                                            AND status != ?";
                            $sql['PARAM'][] = array('FILD'=>'hotel_id','DATA'=>$editHotelId,'TYP'=>'s');
                            $sql['PARAM'][] = array('FILD'=>'status','DATA'=>'D','TYP'=>'s');
                            $result = $mycms->sql_select($sql);

                            foreach ($result as $rowSeatLimit) {
                            ?>
                            <input type="hidden" name="checkInid[]" id="" value=<?= $rowSeatLimit['id'] ?>>

                                <div class="registration-pop_body_box_inner span_4 seat-row">
                                    <div class="form_grid">
                                        <div class="frm_grp span_2">
                                            <p class="frm-head">Date</p>
                                            <p class="typed_data">
                                                <?= date('d/m/Y', strtotime($rowSeatLimit['check_in_date'])) ?>
                                            </p>
                                            <input type="hidden" name="seat_date[]" value="<?= $rowSeatLimit['check_in_date'] ?>">
                                        </div>

                                        <div class="frm_grp span_2">
                                            <p class="frm-head">Seat</p>
                                            <input type="number"
                                                name="seat_limit[]"
                                                value="<?= $rowSeatLimit['seat_limit'] ?>"
                                                min="0">
                                        </div>
                                    </div>
                                </div>
                            <?php } ?>

                        </div>
                    </div>
                        <div class="registration-pop_body_box_inner">
                            <h4 class="registration-pop_body_box_heading">
                                <span>Hotel Images</span>
                            </h4>
                            <div class="com_info_branding_wrap com_info_branding_wrap1 form_grid g_2">
                           <?php
                                $maxImages = 5;

                                $sqlRoom = [];
                                $sqlRoom['QUERY'] = "
                                    SELECT * 
                                    FROM " . _DB_ACCOMMODATION_ACCESSORIES_ . " 
                                    WHERE hotel_id = '".$editHotelId."' 
                                    AND status = 'A' 
                                    AND purpose = 'slider'
                                  ORDER BY `id` ASC";

                                $querySlider = $mycms->sql_select($sqlRoom, false);
                                $imageCount  = is_array($querySlider) ? count($querySlider) : 0;
                                ?>
                                <!-- EXISTING IMAGES -->
                                <?php if ($imageCount > 0): ?>
                                    <?php foreach ($querySlider as $row): ?>
                                        <?php
                                            $icon = _BASE_URL_ . 'uploads/EMAIL.HEADER.FOOTER.IMAGE/' . $row['accessories_icon'];
                                        ?>
                                        <div class="com_info_branding_box editBox" >
                                            <div class="branding_image_preview" style="display:Block">
                                                <img class="editpreviewImage" src="<?= $icon ?>" alt="Hotel Image">
                                                <button class="editremoveImage" type="button" data-id="<?= $row['id'] ?>">
                                                    <i class="fal fa-trash-alt"></i>
                                                </button>
                                            </div>
                                            <div class="branding_image_upload" style="display:none">
                                                <input style="display: none;"name="slider_image[]"  class="webmaster_background" type="file"  accept="image/*">
                                               	<input type="hidden" name="slider_exist_icon[]" value="<?= $row['accessories_icon'] ?>">
                                                <input type="hidden" name="slider_id[]" value="<?= $row['id'] ?>">
                                                <label for="webmaster_background" class="edituploadLabel">
                                                    <span><?php upload() ?></span>
                                                </label>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                                 <?php
                                    $remaining = $maxImages - $imageCount;
                                    
                                    for ($i = 0; $i < $remaining; $i++) {
                                        ?>
                                        <div class="com_info_branding_box editBox">
                                            <div class="branding_image_preview">
                                                <img class="editpreviewImage" src="images/Banner KTC BG.png" alt="">
                                                <button  type="button"  class="editremoveImage"><i class="fal fa-trash-alt"></i></button>
                                            </div>
                                            <div class="branding_image_upload" >
                                                <input style="display: none;"name="slider_image[]"  class="webmaster_background" type="file"  accept="image/*">
                                                <input type="hidden" name="slider_exist_icon[]" value="">
                                                <input type="hidden" name="slider_id[]" value="">
                                                <label for="webmaster_background" class="edituploadLabel">
                                                    <span><?php upload() ?></span>
                                                </label>
                                            </div>
                                        </div>
                                        <?php
                                    }
                                    ?>
                        
                            </div>
                        </div>

                    </div>
                </div>
                <div class="registration-pop_footer">
                    <div class="registration_btn_wrap">
                        <button class="popup_close badge_dark">Cancel</button>
                        <button type="submit" class="mi-1 badge_success"><?php save(); ?>Update Hotel</button>
                    </div>
                </div>
                </form>
            </div>
        </div>
        <!-- edit hotel pop up -->
        <!-- view hotel pop up -->
        <div class="pop_up_body" id="viewhotel">
            <div class="profile_pop_up">
                <div class="profile_pop_left">
                    <?php
                        $sqlFetchHotel				=	array();
                        $sqlFetchHotel['QUERY']		=	"SELECT * 
                                                        FROM " . _DB_MASTER_HOTEL_ . "
                                                        WHERE `id` 		= 	 ? ";

                        $sqlFetchHotel['PARAM'][]	=	array('FILD' => 'id', 		'DATA' => $hotelId, 		'TYP' => 's');
                        $resultFetchHotel    		= $mycms->sql_select($sqlFetchHotel);

                    ?>
                    <div class="profile_left_box text-center ">
                        <h5><?= $resultFetchHotel[0]['hotel_name'] ?></h5>
                        <h6 class="star fivestar"> <?php
                            $rating = (int)$resultFetchHotel[0]['hotelRatings'];

                            for ($i = 1; $i <= $rating; $i++) {
                                star();
                            }
                            ?></h6>
                        <!-- class name varied like "fivestar" "fourstar" "threestar" -->
                        <div class="regi_type justify-content-center">
                        <?php
                                $sqlFetchPack				=	array();
                                $sqlFetchPack['QUERY']		=	"SELECT `package_name` 
                                                                FROM " . _DB_ACCOMMODATION_PACKAGE_ . "
                                                                WHERE `status` 		= 	 ?
                                                                AND `hotel_id` 		= 	 ? ";

                                $sqlFetchPack['PARAM'][]	=	array('FILD' => 'status', 		'DATA' => 'A', 		'TYP' => 's');
                                $sqlFetchPack['PARAM'][]	=	array('FILD' => 'hotel_id', 		'DATA' => $hotelId, 		'TYP' => 's');

                                $resultFetchPack    		= $mycms->sql_select($sqlFetchPack);
                            
                                if ($resultFetchPack) {
                                foreach ($resultFetchPack as $keyPack => $rowFetchPack) {
                            ?>
                            <span class="badge_padding badge_default"><?= $rowFetchPack['package_name'] ?></span>
                            <?php
                                 }

                               }
                           ?>
                        </div>
                    </div>
                    <div class="profile_left_box">
                        <ul>
                            <li>
                                <?php call(); ?>
                                <p>
                                    <b>Phone</b>
                                    <span><?= $resultFetchHotel[0]['hotel_phone_no'] ?></span>
                                </p>
                            </li>
                             <?php
                                $sql 		    	  =	array();
                                $sql['QUERY'] = "SELECT min(`check_in_date`) AS checkin								
                                                FROM " . _DB_ACCOMMODATION_CHECKIN_DATE_ . "
                                                WHERE hotel_id = ?
                                                    AND status != ?";
                                $sql['PARAM'][]	=	array('FILD' => 'hotel_id', 	'DATA' => $hotelId, 'TYP' => 's');
                                $sql['PARAM'][]	=	array('FILD' => 'status', 		'DATA' => 'D', 				 'TYP' => 's');
                                $result = $mycms->sql_select($sql);
                                $rowFetchcheckIn           = $result[0];

                                $sql 		    	  =	array();
                                $sql['QUERY'] = "SELECT max(`check_out_date`) AS checkout								
                                                FROM " . _DB_ACCOMMODATION_CHECKOUT_DATE_ . "
                                                WHERE hotel_id = ?
                                                    AND status !=?";

                                $sql['PARAM'][]	=	array('FILD' => 'hotel_id', 	'DATA' => $hotelId, 'TYP' => 's');
                                $sql['PARAM'][]	=	array('FILD' => 'status', 		'DATA' => 'D', 				 'TYP' => 's');
                                $result = $mycms->sql_select($sql);
                                $rowFetchcheckOut           = $result[0];

                             ?>
                            <li>
                                <?php calendar(); ?>
                                <p>
                                    <b>Check In Date</b>
                                    <span><?= date('d-m-Y',strtotime($rowFetchcheckIn['checkin'])) ?></span>
                                </p>
                            </li>
                            <li>
                                <?php calendar(); ?>
                                <p>
                                    <b>Check Out Date</b>
                                    <span><?= date('d-m-Y',strtotime($rowFetchcheckOut['checkout'])) ?></span>
                                </p>
                            </li>
                            <li>
                                <?php address(); ?>
                                <p>
                                    <b>Address</b>
                                    <span><?= $resultFetchHotel[0]['hotel_address'] ?></span>
                                </p>
                            </li>
                            <li>
                                <?php address(); ?>
                                <p>
                                    <b>Distance From Venue (Km)</b>
                                    <span><?= $resultFetchHotel[0]['distance_from_venue'] ?></span>
                                </p>
                            </li>
                        </ul>
                    </div>

                </div>
                <div class="profile_pop_right">
                    <div class="profile_pop_right_heading">
                        <span>Hotel Details</span>
                        <p>
                            <!-- <a href="javascript:void(null)" class="icon_hover badge_primary action-transparent"><?php export(); ?></a> -->
                            <a href="javascript:void(null)" class="popup_close icon_hover badge_danger action-transparent"><?php close(); ?></a>
                        </p>
                    </div>
                    <div class="profile_pop_right_body">
                        <ul class="profile_payments_grid_ul">
                            <?php
                            $sqlRoom = array();
                            $sqlRoom['QUERY']    = "SELECT * FROM " . _DB_ACCOMMODATION_ACCESSORIES_ . "  WHERE `hotel_id` = '" . $hotelId . "' AND status='A' AND purpose='slider'  ORDER BY `id` ASC";

                            $querySlider = $mycms->sql_select($sqlRoom, false);
                            foreach ($querySlider as $k => $row) {
                                //echo '<pre>'; print_r($row);

                                $icon = _BASE_URL_ . 'uploads/EMAIL.HEADER.FOOTER.IMAGE/' . $row['accessories_icon'];
                            ?>
                            <li class="p-0 overflow-hidden">
                                <img class="w-25" src="<?= $icon?>">
                                
                            </li>
                             <!-- <li class="p-0 overflow-hidden">
                                <img class="w-100" src="https://ruedakolkata.com/natcon_2025/uploads/EMAIL.HEADER.FOOTER.IMAGE/MAILER_LOGO_0001_250526185918.png">
                            </li>
                            <li class="p-0 overflow-hidden">
                                <img class="w-100" src="https://ruedakolkata.com/natcon_2025/uploads/EMAIL.HEADER.FOOTER.IMAGE/MAILER_LOGO_0001_250526185918.png">
                            </li>
                            <li class="p-0 overflow-hidden">
                                <img class="w-100" src="https://ruedakolkata.com/natcon_2025/uploads/EMAIL.HEADER.FOOTER.IMAGE/MAILER_LOGO_0001_250526185918.png">
                            </li>
                            <li class="p-0 overflow-hidden">
                                <img class="w-100" src="https://ruedakolkata.com/natcon_2025/uploads/EMAIL.HEADER.FOOTER.IMAGE/MAILER_LOGO_0001_250526185918.png">
                            </li> -->
                            <?php
                            }
                            ?>
                           
                        </ul>
                        <div class="service_breakdown_wrap">
                            <h4>Seat Limit</h4>
                            <div class="table_wrap">
                                <table>
                                    <thead>
                                         
                                            <tr>
                                            <?php
                                                $sql 		    	  =	array();
                                                $sql['QUERY'] = "SELECT `check_in_date`,`seat_limit`								
                                                                            FROM " . _DB_ACCOMMODATION_CHECKIN_DATE_ . "
                                                                            WHERE hotel_id = ?
                                                                                AND status != ?";
                                                $sql['PARAM'][]	=	array('FILD' => 'hotel_id', 	'DATA' => $hotelId, 'TYP' => 's');
                                                $sql['PARAM'][]	=	array('FILD' => 'status', 		'DATA' => 'D', 				 'TYP' => 's');
                                                $result = $mycms->sql_select($sql);
                                                // $rowSeatLimit          = $result[0];
                                                foreach ($result as $key => $rowSeatLimit) {
                                                ?>
                                                    <th class="text-center"><?= date('d-m-Y',strtotime($rowSeatLimit['check_in_date'])) ?></th>
                                            <?
                                                }
                                            ?>
                                            </tr>
                                      
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <?php
                                                $sql 		    	  =	array();
                                                $sql['QUERY'] = "SELECT `check_in_date`,`seat_limit`								
                                                                            FROM " . _DB_ACCOMMODATION_CHECKIN_DATE_ . "
                                                                            WHERE hotel_id = ?
                                                                                AND status != ?";
                                                $sql['PARAM'][]	=	array('FILD' => 'hotel_id', 	'DATA' => $hotelId, 'TYP' => 's');
                                                $sql['PARAM'][]	=	array('FILD' => 'status', 		'DATA' => 'D', 				 'TYP' => 's');
                                                $result = $mycms->sql_select($sql);
                                                // $rowSeatLimit          = $result[0];
                                                foreach ($result as $key => $rowSeatLimit) {
                                                ?>
                                                    <td class="text-center"><?= $rowSeatLimit['seat_limit'] ?></td>
                                                <?
                                                  }
                                                ?>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="service_breakdown_wrap">
                            <h4>Aminities</h4>
                            <ul class="accm_ul aminity_ul">
                                <?php
                                    $sqlAcc = array();
                                    $sqlAcc['QUERY']    = "SELECT * FROM " . _DB_ACCOMMODATION_ACCESSORIES_ . "  WHERE `hotel_id` = '" . $hotelId . "' AND status='A' AND purpose='aminity'  ORDER BY `id` ASC";

                                    $queryAcc = $mycms->sql_select($sqlAcc, false);
                                    
                                if ($queryAcc) { 
                                    foreach ($queryAcc as $keyqueryAcc => $aminity) {
                                            $icon = _BASE_URL_.'uploads/EMAIL.HEADER.FOOTER.IMAGE/' . $aminity['accessories_icon'];
                                ?>
                                 <li><img src=<?= $icon?> alt=""><?= $aminity['accessories_name'] ?></li>
                               <?
                                    }
                                }    
                                ?>                              
                            </ul>
                        </div>
                        <div class="service_breakdown_wrap">
                            <h4>Room Type</h4>
                            <ul class="accm_ul room_ul">
                            <?php
                                $sqlRoom = array();
                                $sqlRoom['QUERY']    = "SELECT * FROM " . _DB_ACCOMMODATION_ACCESSORIES_ . "  WHERE `hotel_id` = '" . $hotelId . "' AND status='A' AND purpose='room'  ORDER BY `id` ASC";

                                $queryRoom = $mycms->sql_select($sqlRoom, false);
                                if ($queryRoom) { 
                                    foreach ($queryRoom as $k => $row) {
                                    $icon = _BASE_URL_ . 'uploads/EMAIL.HEADER.FOOTER.IMAGE/' . $row['accessories_icon'];

                                ?>
                                    <li><img src="<?= $icon ?>" alt=""><?= $row['accessories_name'] ?></li>
                                <?php
                                }
                                }
                             ?>
                            </ul>
                        </div>
                    </div>

                </div>
            </div>
        </div>
        <!-- view hotel pop up -->
        <!-- Hotel Tariff pop up -->
          <div class="pop_up_body" id="hoteltariff">
            <div class="registration_pop_up">
                <div class="registration-pop_heading">
                    <span>Accommodation Tariff</span>
                    <p>
                        <a href="javascript:void(null)" class="popup_close icon_hover badge_danger action-transparent"><?php close(); ?></a>
                    </p>
                </div>
                <div class="registration-pop_body">
                    <div class="registration-pop_body_box">
                        <div class="registration-pop_body_box_inner p-0 bg-transparent border-0">
                            <div class="form_grid">
                                <?php
                                  $hotelId		  =	$hotelTarrifId;
                                  $roomId       =$roomId;
                                 
                                    $sqlFetchHotel				=	array();
                                    $sqlFetchHotel['QUERY']		=	"SELECT * 
                                                                    FROM " . _DB_MASTER_HOTEL_ . "
                                                                    WHERE `id` 		= 	 ? ";

                                    $sqlFetchHotel['PARAM'][]	=	array('FILD' => 'id', 		'DATA' => $hotelTarrifId, 		'TYP' => 's');
                                    $resultFetchHotel    		= $mycms->sql_select($sqlFetchHotel);
                                    $sqlFetchPack				=	array();
                                    $sqlFetchPack['QUERY']		=	"SELECT `package_name` 
                                                                    FROM " . _DB_ACCOMMODATION_PACKAGE_ . "
                                                                    WHERE `status` 		= 	 ?
                                                                    AND `hotel_id` 		= 	 ? ";

                                    $sqlFetchPack['PARAM'][]	=	array('FILD' => 'status', 		'DATA' => 'A', 		'TYP' => 's');
                                    $sqlFetchPack['PARAM'][]	=	array('FILD' => 'hotel_id', 		'DATA' => $hotelTarrifId, 		'TYP' => 's');

                                    $resultFetchPack    		= $mycms->sql_select($sqlFetchPack);
                                    $sqlRoom = array();
                                    $sqlRoom['QUERY'] = "SELECT * FROM " . _DB_ACCOMMODATION_ACCESSORIES_ . "  
                                                        WHERE id = ? AND status='A' AND purpose='room' ORDER BY id ASC";
                                    $sqlRoom['PARAM'][] = array('DATA' => $roomId, 'TYP' => 's');
                                    $rooms = $mycms->sql_select($sqlRoom);

                                    ?>
                                <div class="frm_grp span_2">
                                    <p class="frm-head">Hotel Name</p>
                                    <p class="typed_data"><?=$resultFetchHotel[0]['hotel_name']?></p>
                                </div>
                                <div class="frm_grp span_2">
                                    <p class="frm-head">Room Type</p>
                                    <p class="typed_data"><?= $rooms[0]['accessories_name'] ?></p>
                                </div>
                                <div class="accm_add_wrap span_4">
                                       <? 
                
                                        $sql 	=	array();
                                        $sql['QUERY']	=	"SELECT id, cutoff.cutoff_title  
                                                                FROM "._DB_TARIFF_CUTOFF_." cutoff
                                                            WHERE status = ?";
                                        $sql['PARAM'][]	=	array('FILD' => 'status' , 		'DATA' => 'A' , 		'TYP' => 's');					   
                                        $resCutoff=$mycms->sql_select($sql);
                                        $cutOffsArray = array();
                                        
                                        foreach($resCutoff as $key=>$title)
                                        {	
                                            $sqlPackageCheckoutDate	=	array();
                                            // query in tariff table
                                            $sqlPackageCheckoutDate['QUERY'] = "select * 
                                                                                FROM "._DB_TARIFF_ACCOMMODATION_." accomodation
                                                                                WHERE status = ?
                                                                                AND tariff_cutoff_id = ?
                                                                                AND hotel_id = ?
                                                                                AND package_id = ?";
                                            $sqlPackageCheckoutDate['PARAM'][]	=	array('FILD' => 'status' , 			'DATA' => 'A' , 					'TYP' => 's');
                                            $sqlPackageCheckoutDate['PARAM'][]	=	array('FILD' => 'tariff_cutoff_id' ,'DATA' => $title['id'] , 		'TYP' => 's');
                                            $sqlPackageCheckoutDate['PARAM'][]	=	array('FILD' => 'hotel_id' , 		'DATA' => $hotelTarrifId, 		'TYP' => 's');
                                            $sqlPackageCheckoutDate['PARAM'][]	=	array('FILD' => 'package_id' , 		'DATA' => $packageId , 		'TYP' => 's');	
                                            $sqlPackageCheckoutDate['PARAM'][]	=	array('FILD' => 'roomTypeId' , 		'DATA' => $roomId , 		'TYP' => 's');	

                                            $resPackageCheckoutDate = $mycms->sql_select($sqlPackageCheckoutDate);

                                        ?>	
                                    <div class="accm_add_box">
                                        <h4 class="registration-pop_body_box_heading">
                                            <span><?=strip_tags($title['cutoff_title'])?><span>
                                        </h4>
                                        <div class="registration-pop_body p-0">
                                            <?
                                            if($resultFetchPack){
                                              foreach ($resultFetchPack as $keyPack => $rowFetchPack) {
                                             
                                                ?>
                                            <div class="registration-pop_body_box_inner">
                                                <h4 class="registration-pop_body_box_heading">
                                                    <span><?=$rowFetchPack['package_name']?><span>
                                                </h4>
                                                <div class="form_grid">
                                                    <div class="frm_grp span_2">
                                                        <p class="frm-head">Rate/Night(INR)</p>
                                                        <input type="number" name="rate_per_night_inr[<?=$title['id']?>]" class="rate_inr" value="<?=$resPackageCheckoutDate[0]['inr_amount']?>">

                                                    </div>
                                                    <div class="frm_grp span_2">
                                                        <p class="frm-head">Rate/Night(USD)</p>
                                                        <input type="number" name="rate_per_night_usd[<?=$title['id']?>]" class="rate_usd" value="<?=$resPackageCheckoutDate[0]['usd_amount']?>">

                                                    </div>
                                                </div>

                                            </div>
                                            <?
                                                }
                                                }
                                            ?>
                                         
                                        </div>
                                        <div class="table_wrap mt-3">
                                            <table>
                                                <thead>
                                                    <tr>
                                                        <th>Check In Date</th>
                                                        <th>Check Out Date</th>
                                                        <th>Package</th>
                                                        <th>INR Rate</th>
                                                        <th>USD Rate</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                     <?php foreach($resPackageCheckoutDate as $key=>$accomodationTariff){ 
                                                        $packageCheckoutDate 	=	array();
                                                        $packageCheckoutDate['QUERY'] = "SELECT * 
                                                                                        FROM "._DB_ACCOMMODATION_CHECKOUT_DATE_." _DB_ACCOMMODATION_CHECKIN_DATE_
                                                                                        WHERE `id` = ?
                                                                                            AND `status` = ?
                                                                                    ORDER BY check_out_date";
                                                        $packageCheckoutDate['PARAM'][]	=	array('FILD' => 'id' , 		'DATA' => $accomodationTariff['checkout_date_id'] , 	    'TYP' => 's');
                                                        $packageCheckoutDate['PARAM'][]	=	array('FILD' => 'status' , 			'DATA' => 'A' , 			'TYP' => 's');
                                                        $respackageCheckoutDate 		= $mycms->sql_select($packageCheckoutDate);
                                                        $rowpackageCheckoutDate			= $respackageCheckoutDate[0];

                                                        $packageCheckinDate 	=	array();
                                                        $packageCheckinDate['QUERY'] = "SELECT * 
                                                                                        FROM "._DB_ACCOMMODATION_CHECKIN_DATE_." 
                                                                                        WHERE `id` = ?
                                                                                            AND `status` = ?
                                                                                    ORDER BY check_in_date";
                                                        $packageCheckinDate['PARAM'][]	=	array('FILD' => 'id' , 				'DATA' => $accomodationTariff['checkin_date_id'] , 	    'TYP' => 's');
                                                        $packageCheckinDate['PARAM'][]	=	array('FILD' => 'status' , 			'DATA' => 'A' , 			'TYP' => 's');
                                                    
                                                        $respackageCheckinDate 		= $mycms->sql_select($packageCheckinDate);
                                                        $rowpackageCheckinDate			= $respackageCheckinDate[0];
                                                        ?>
                                                    <tr>
                                                        <td rowspan="3">
                                                                <?=$rowpackageCheckinDate['check_in_date']?>
                                                                <input type="hidden"
                                                                    name="checkin_date[<?=$title['id']?>][]"
                                                                    value="<?=$accomodationTariff['checkin_date_id']?>">
                                                            </td>
                                                        <td  rowspan="3">
                                                                <?=$rowpackageCheckoutDate['check_out_date']?>
                                                                <input type="hidden"
                                                                    name="checkout_date[<?=$title['id']?>][]"
                                                                    value="<?=$accomodationTariff['checkout_date_id']?>">
                                                        </td>
                                                        <td>Single</td>
                                                        <td>5000.00</td>
                                                        <td>16.00</td>
                                                    </tr>
                                                    <?php
                                                     }
                                                    ?>
                                                </tbody>
                                            </table>
                                        </div>
                                        <a href="#" class="accm_delet icon_hover badge_primary action-transparent"><i class="fal fa-paper-plane"></i></a>
                                    </div>
                                
                                    <?php
                                        }
                                        ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="registration-pop_footer">
                    <div class="registration_btn_wrap">
                        <button class="popup_close badge_dark">Cancel</button>
                        <button type="submit" class="mi-1 badge_success"><?php save(); ?>Update Tariff</button>
                    </div>
                </div>
            </div>
        </div>
        <div class="pop_up_body" id="hoteltariffOld">
            <div class="registration_pop_up">
                <div class="registration-pop_heading">
                    <span>Package Tariff</span>
                    <p>
                        <a href="javascript:void(null)" class="popup_close icon_hover badge_danger action-transparent"><?php close(); ?></a>
                    </p>
                </div>
                <?
                	$hotelId		  =	$hotelTarrifId;
                    $packageId       =$packageId;
                    // $cutoff_id  	  = $cutoff_id;
                    
            
                    $dates = array();	
                    $dCount = 0;		
                    $packageCheckDate = array();	
                    $packageCheckDate['QUERY'] = "SELECT * FROM "._DB_ACCOMMODATION_CHECKIN_DATE_." 
                                                        WHERE `hotel_id` = ?
                                                            AND `status` = ?
                                                        ORDER BY  check_in_date";
                        $packageCheckDate['PARAM'][]	=	array('FILD' => 'hotel_id' , 		'DATA' => $hotelId , 	'TYP' => 's');
                        $packageCheckDate['PARAM'][]	=	array('FILD' => 'status' , 			'DATA' => 'A' , 		'TYP' => 's');									    
                        $resCheckIns = $mycms->sql_select($packageCheckDate);
                        
                        foreach ($resCheckIns as $key => $rowCheckIn) {
                            $packageCheckoutDate = array();
                            $packageCheckoutDate['QUERY'] = "SELECT *, TIMESTAMPDIFF(DAY,'".$rowCheckIn['check_in_date']."',`check_out_date`) AS dayDiff
                                                            FROM "._DB_ACCOMMODATION_CHECKOUT_DATE_." 
                                                            WHERE `hotel_id` = ?
                                                                AND `status` = ?
                                                                AND `check_out_date` > ?
                                                        ORDER BY check_out_date";
                            $packageCheckoutDate['PARAM'][]	=	array('FILD' => 'hotel_id' , 		'DATA' => $hotelId , 	    'TYP' => 's');
                            $packageCheckoutDate['PARAM'][]	=	array('FILD' => 'status' , 			'DATA' => 'A' , 			'TYP' => 's');
                            $packageCheckoutDate['PARAM'][]	=	array('FILD' => 'check_out_date' ,	'DATA' => $rowCheckIn['check_in_date'] , 			'TYP' => 's');
                            
                            
                            $resCheckOut = $mycms->sql_select($packageCheckoutDate);	
                            //echo '<pre>'; print_r($resCheckOut);
                            foreach ($resCheckOut as $key => $rowCheckOut) {
                                $dates[$dCount]['CHECKIN'] 	  =  $rowCheckIn['check_in_date'];
                                $dates[$dCount]['CHECKINID']  =  $rowCheckIn['id'];
                                $dates[$dCount]['CHECKOUT']   =  $rowCheckOut['check_out_date'];						
                                $dates[$dCount]['CHECKOUTID'] =  $rowCheckOut['id'];
                                $dates[$dCount]['DAYDIFF']    =  $rowCheckOut['dayDiff'];

                                $dCount++;
                            }		

                        }		
            ?>	
        
           
                <form action="accomodation_tariff.process.php" method="post" onsubmit="return onSubmitAction();">
                    <input type="hidden" name="act" value="edit"/>
                    <input type="hidden" name="package_id" value="<?=$packageId?>"/>
                    <input type="hidden" name="hotel_id" value="<?=$hotelTarrifId?>"/>

                    <div class="registration-pop_body">
                        <div class="registration-pop_body_box">
                            <div class="registration-pop_body_box_inner p-0 bg-transparent border-0">
                                <div class="form_grid">
                                    <?php
                                        $sqlFetchHotel				=	array();
                                        $sqlFetchHotel['QUERY']		=	"SELECT * 
                                                                        FROM " . _DB_MASTER_HOTEL_ . "
                                                                        WHERE `id` 		= 	 ? ";

                                        $sqlFetchHotel['PARAM'][]	=	array('FILD' => 'id', 		'DATA' => $hotelTarrifId, 		'TYP' => 's');
                                        $resultFetchHotel    		= $mycms->sql_select($sqlFetchHotel);
                                        $sqlFetchPack				=	array();
                                        $sqlFetchPack['QUERY']		=	"SELECT `package_name` 
                                                                        FROM " . _DB_ACCOMMODATION_PACKAGE_ . "
                                                                        WHERE `status` 		= 	 ?
                                                                        AND `id` 		= 	 ? ";

                                        $sqlFetchPack['PARAM'][]	=	array('FILD' => 'status', 		'DATA' => 'A', 		'TYP' => 's');
                                        $sqlFetchPack['PARAM'][]	=	array('FILD' => 'id', 		'DATA' => $packageId, 		'TYP' => 's');

                                        $resultFetchPack    		= $mycms->sql_select($sqlFetchPack);
                                
                                    ?>
                                    <div class="frm_grp span_4">
                                        <p class="frm-head">Hotel Name</p>
                                        <p class="typed_data"><?=$resultFetchHotel[0]['hotel_name']?></p>
                                    </div>
                                    <div class="frm_grp span_4">
                                        <p class="frm-head">Package Name</p>
                                        <p class="typed_data"><?=$resultFetchPack[0]['package_name']?></p>
                                    </div>
                                    <div class="accm_add_wrap span_4">
                                        <? 
                
                                        $sql 	=	array();
                                        $sql['QUERY']	=	"SELECT id, cutoff.cutoff_title  
                                                                FROM "._DB_TARIFF_CUTOFF_." cutoff
                                                            WHERE status = ?";
                                        $sql['PARAM'][]	=	array('FILD' => 'status' , 		'DATA' => 'A' , 		'TYP' => 's');					   
                                        $resCutoff=$mycms->sql_select($sql);
                                        $cutOffsArray = array();
                                        
                                        foreach($resCutoff as $key=>$title)
                                        {	
                                            $sqlPackageCheckoutDate	=	array();
                                            // query in tariff table
                                            $sqlPackageCheckoutDate['QUERY'] = "select * 
                                                                                FROM "._DB_TARIFF_ACCOMMODATION_." accomodation
                                                                                WHERE status = ?
                                                                                AND tariff_cutoff_id = ?
                                                                                AND hotel_id = ?
                                                                                AND package_id = ?";
                                            $sqlPackageCheckoutDate['PARAM'][]	=	array('FILD' => 'status' , 			'DATA' => 'A' , 					'TYP' => 's');
                                            $sqlPackageCheckoutDate['PARAM'][]	=	array('FILD' => 'tariff_cutoff_id' ,'DATA' => $title['id'] , 		'TYP' => 's');
                                            $sqlPackageCheckoutDate['PARAM'][]	=	array('FILD' => 'hotel_id' , 		'DATA' => $hotelTarrifId, 		'TYP' => 's');
                                            $sqlPackageCheckoutDate['PARAM'][]	=	array('FILD' => 'package_id' , 		'DATA' => $packageId , 		'TYP' => 's');									   
                                            $resPackageCheckoutDate = $mycms->sql_select($sqlPackageCheckoutDate);

                                        ?>	
                                        
                                        <div class="accm_add_box" data-cutoff="<?=$title['id']?>">
                                            <h4 class="registration-pop_body_box_heading">
                                                <span><?=strip_tags($title['cutoff_title'])?><span>
                                            </h4>
                                            <input type="hidden" name="cutoff[<?=$title['id']?>]" value="<?=$title['id']?>"/>

                                            <div class="form_grid">
                                                <div class="frm_grp span_2">
                                                    <p class="frm-head">Rate/Night(INR)</p>
                                                    <input type="number" name="rate_per_night_inr[<?=$title['id']?>]" class="rate_inr" value="<?=$resPackageCheckoutDate[0]['inr_amount']?>">
                                                </div>
                                                <div class="frm_grp span_2">
                                                    <p class="frm-head">Rate/Night(USD)</p>
                                                    <input type="number" name="rate_per_night_usd[<?=$title['id']?>]" class="rate_usd" value="<?=$resPackageCheckoutDate[0]['usd_amount']?>">
                                                </div>
                                                
                                                <a  href="javascript:void(0);"  value="Compose" class="accm_delet icon_hover badge_primary action-transparent composeBtn "><i class="fal fa-paper-plane"></i></a>
                                            </div>
                                            <div class="table_wrap mt-3">
                                                <table use="rateDes">
                                                    <thead>
                                                        <tr>
                                                            <th>Check In Date</th>
                                                            <th>Check Out Date</th>
                                                            <th>INR Rate</th>
                                                            <th>USD Rate</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <?php foreach($resPackageCheckoutDate as $key=>$accomodationTariff){ 
                                                        $packageCheckoutDate 	=	array();
                                                        $packageCheckoutDate['QUERY'] = "SELECT * 
                                                                                        FROM "._DB_ACCOMMODATION_CHECKOUT_DATE_." _DB_ACCOMMODATION_CHECKIN_DATE_
                                                                                        WHERE `id` = ?
                                                                                            AND `status` = ?
                                                                                    ORDER BY check_out_date";
                                                        $packageCheckoutDate['PARAM'][]	=	array('FILD' => 'id' , 		'DATA' => $accomodationTariff['checkout_date_id'] , 	    'TYP' => 's');
                                                        $packageCheckoutDate['PARAM'][]	=	array('FILD' => 'status' , 			'DATA' => 'A' , 			'TYP' => 's');
                                                        $respackageCheckoutDate 		= $mycms->sql_select($packageCheckoutDate);
                                                        $rowpackageCheckoutDate			= $respackageCheckoutDate[0];

                                                        $packageCheckinDate 	=	array();
                                                        $packageCheckinDate['QUERY'] = "SELECT * 
                                                                                        FROM "._DB_ACCOMMODATION_CHECKIN_DATE_." 
                                                                                        WHERE `id` = ?
                                                                                            AND `status` = ?
                                                                                    ORDER BY check_in_date";
                                                        $packageCheckinDate['PARAM'][]	=	array('FILD' => 'id' , 				'DATA' => $accomodationTariff['checkin_date_id'] , 	    'TYP' => 's');
                                                        $packageCheckinDate['PARAM'][]	=	array('FILD' => 'status' , 			'DATA' => 'A' , 			'TYP' => 's');
                                                    
                                                        $respackageCheckinDate 		= $mycms->sql_select($packageCheckinDate);
                                                        $rowpackageCheckinDate			= $respackageCheckinDate[0];
                                                        ?>
                                                        <!-- <tr  use='rateVal'>
                                                            <td><?=$rowpackageCheckinDate['check_in_date']?></td>
                                                            <td><?=$rowpackageCheckoutDate['check_out_date']?></td>
                                                            <td><?=$accomodationTariff['inr_amount']?></td>
                                                            <td><?=$accomodationTariff['usd_amount']?></td>
                                                        </tr> -->
                                                        <tr use="rateVal">
                                                            <td>
                                                                <?=$rowpackageCheckinDate['check_in_date']?>
                                                                <input type="hidden"
                                                                    name="checkin_date[<?=$title['id']?>][]"
                                                                    value="<?=$accomodationTariff['checkin_date_id']?>">
                                                            </td>

                                                            <td>
                                                                <?=$rowpackageCheckoutDate['check_out_date']?>
                                                                <input type="hidden"
                                                                    name="checkout_date[<?=$title['id']?>][]"
                                                                    value="<?=$accomodationTariff['checkout_date_id']?>">
                                                            </td>

                                                            <td>
                                                                <?=$accomodationTariff['inr_amount']?>
                                                                <input type="hidden"
                                                                    name="INRAmt[<?=$title['id']?>][]"
                                                                    value="<?=$accomodationTariff['inr_amount']?>">
                                                            </td>

                                                            <td>
                                                                <?=$accomodationTariff['usd_amount']?>
                                                                <input type="hidden"
                                                                    name="USDAmt[<?=$title['id']?>][]"
                                                                    value="<?=$accomodationTariff['usd_amount']?>">
                                                            </td>
                                                        </tr>
                                                        <? } ?>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                        <?
                                        }
                                        ?>
                                
                                     
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="registration-pop_footer">
                        <div class="registration_btn_wrap">
                            <button class="popup_close badge_dark">Cancel</button>
                            <button type="submit" id="confirmBtn" disabled class="mi-1 badge_success"><?php save(); ?>Confirm Booking</button>
                        </div>
                    </div>
                </form>
                <script>
                        var datesByCutoff = {};
                        <?php
                        foreach($resCutoff as $title) {
                            $cutoffId = $title['id'];
                            $cutoffDates = array();
                            foreach($dates as $vals) {
                                // You might want to filter dates if they belong to this cutoff
                                $cutoffDates[] = [
                                    'checkin'   => $vals['CHECKIN'],
                                    'checkinId' => $vals['CHECKINID'],
                                    'checkout'  => $vals['CHECKOUT'],
                                    'checkoutId'=> $vals['CHECKOUTID'],
                                    'days'      => $vals['DAYDIFF']
                                ];
                            }
                            echo "datesByCutoff[$cutoffId] = " . json_encode($cutoffDates) . ";\n";
                        }
                        ?>
                        console.log(datesByCutoff);
                        </script>
                       <script>
                        $(document).on('click', '.composeBtn', function (e) {
                        e.preventDefault();
                         $('#confirmBtn').prop('disabled', false);

                        let container = $(this).closest('.accm_add_box');
                        let cutoffId = container.data('cutoff');

                        let INRrate = container.find('.rate_inr').val();
                        let USDrate = container.find('.rate_usd').val();
                        let rateTable = container.find("table[use=rateDes]");

                        if (INRrate === "") {
                            alert('Please enter rate!!!');
                            container.find('.rate_inr').focus();
                            return;
                        }

                        rateTable.find("tr[use=rateVal]").remove();
                       var cutoffDates = datesByCutoff[cutoffId] || [];


                        $.each(cutoffDates, function (i, value) {

                            let INRAmount = parseFloat(INRrate) * parseInt(value.days);
                            let USDAmount = parseFloat(USDrate) * parseInt(value.days);

                            let tr = `
                                <tr use="rateVal">
                                    <td>${value.checkin}
                                        <input type="hidden"
                                            name="checkin_date[${cutoffId}][]"
                                            value="${value.checkinId}">
                                    </td>
                                    <td>${value.checkout}
                                        <input type="hidden"
                                            name="checkout_date[${cutoffId}][]"
                                            value="${value.checkoutId}">
                                    </td>
                                    <td>${INRAmount.toFixed(2)}
                                        <input type="hidden"
                                            name="INRAmt[${cutoffId}][]"
                                            value="${INRAmount}">
                                    </td>
                                    <td>${USDAmount.toFixed(2)}
                                        <input type="hidden"
                                            name="USDAmt[${cutoffId}][]"
                                            value="${USDAmount}">
                                    </td>
                                </tr>
                            `;

                            rateTable.append(tr);
                        });

                        rateTable.show();
                    });
                </script>
            </div>
        </div>
        <!-- Hotel Tariff pop up -->
        <!-- New dinner pop up -->
        <div class="pop_up_body" id="adddinner">
            <div class="registration_pop_up">
                <div class="registration-pop_heading">
                    <span>Add Dinner</span>
                    <p>
                        <a href="javascript:void(null)" class="popup_close icon_hover badge_danger action-transparent"><?php close(); ?></a>
                    </p>
                </div>
                <div class="registration-pop_body">
                    <div class="registration-pop_body_box">
                        <div class="registration-pop_body_box_inner p-0 bg-transparent border-0">
                            <div class="form_grid">
                                <div class="frm_grp span_4">
                                    <p class="frm-head">Select Hotel</p>
                                    <select></select>
                                </div>
                                <div class="frm_grp span_2">
                                    <p class="frm-head">Dinner Classification Name</p>
                                    <input>
                                </div>
                                <div class="frm_grp span_2">
                                    <p class="frm-head">Dinner Date</p>
                                    <input type="date">
                                </div>
                                <div class="frm_grp span_4">
                                    <p class="frm-head">Link</p>
                                    <input>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="registration-pop_footer">
                    <div class="registration_btn_wrap">
                        <button class="popup_close badge_dark">Cancel</button>
                        <button type="submit" class="mi-1 badge_success"><?php save(); ?>Save</button>
                    </div>
                </div>
            </div>
        </div>
        <!-- New dinner pop up -->
        <!-- edit dinner pop up -->
        <div class="pop_up_body" id="editdinner">
            <div class="registration_pop_up">
                <div class="registration-pop_heading">
                    <span>Edit Dinner</span>
                    <p>
                        <a href="javascript:void(null)" class="popup_close icon_hover badge_danger action-transparent"><?php close(); ?></a>
                    </p>
                </div>
                <div class="registration-pop_body">
                    <div class="registration-pop_body_box">
                        <div class="registration-pop_body_box_inner p-0 bg-transparent border-0">
                            <div class="form_grid">
                                <div class="frm_grp span_4">
                                    <p class="frm-head">Select Hotel</p>
                                    <select></select>
                                </div>
                                <div class="frm_grp span_2">
                                    <p class="frm-head">Dinner Classification Name</p>
                                    <input>
                                </div>
                                <div class="frm_grp span_2">
                                    <p class="frm-head">Dinner Date</p>
                                    <input type="date">
                                </div>
                                <div class="frm_grp span_4">
                                    <p class="frm-head">Link</p>
                                    <input>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="registration-pop_footer">
                    <div class="registration_btn_wrap">
                        <button class="popup_close badge_dark">Cancel</button>
                        <button type="submit" class="mi-1 badge_success"><?php save(); ?>Update</button>
                    </div>
                </div>
            </div>
        </div>
        <!-- edit dinner pop up -->
        <!-- Add accompany classification pop up -->
        <div class="pop_up_body" id="addaccompany">
            <div class="registration_pop_up">
                <div class="registration-pop_heading">
                    <span>Add Accompany</span>
                    <p>
                        <a href="javascript:void(null)" class="popup_close icon_hover badge_danger action-transparent"><?php close(); ?></a>
                    </p>
                </div>
                <div class="registration-pop_body">
                    <div class="registration-pop_body_box">
                        <div class="registration-pop_body_box_inner p-0 bg-transparent border-0">
                            <div class="form_grid">
                                <div class="frm_grp span_4">
                                    <p class="frm-head">Classification Title</p>
                                    <input>
                                </div>
                                <div class="frm_grp span_4">
                                    <p class="frm-head">Inclusion Lunch Date</p>
                                    <input type="datce">
                                </div>
                                <div class="frm_grp span_3">
                                    <p class="frm-head">Inclusion Conference Dinner Date</p>
                                    <input type="date">
                                </div>
                                <div class="frm_grp span_2">
                                    <p class="frm-head">Entry to Scientific Halls</p>
                                    <div class="cus_check_wrap">
                                        <label class="cus_check gender_check">
                                            <input type="radio" name="workshopstatus">
                                            <span class="checkmark">Yes</span>
                                        </label>
                                        <label class="cus_check gender_check">
                                            <input type="radio" name="workshopstatus">
                                            <span class="checkmark">No</span>
                                        </label>
                                    </div>
                                </div>
                                <div class="frm_grp span_2">
                                    <p class="frm-head">Entry to Exhibition Area</p>
                                    <div class="cus_check_wrap">
                                        <label class="cus_check gender_check">
                                            <input type="radio" name="workshopstatus">
                                            <span class="checkmark">Yes</span>
                                        </label>
                                        <label class="cus_check gender_check">
                                            <input type="radio" name="workshopstatus">
                                            <span class="checkmark">No</span>
                                        </label>
                                    </div>
                                </div>
                                <div class="frm_grp span_2">
                                    <p class="frm-head">Tea/Coffee during the Conference</p>
                                    <div class="cus_check_wrap">
                                        <label class="cus_check gender_check">
                                            <input type="radio" name="workshopstatus">
                                            <span class="checkmark">Yes</span>
                                        </label>
                                        <label class="cus_check gender_check">
                                            <input type="radio" name="workshopstatus">
                                            <span class="checkmark">No</span>
                                        </label>
                                    </div>
                                </div>
                                <div class="frm_grp span_2">
                                    <p class="frm-head">Inclusion Conference Kit</p>
                                    <div class="cus_check_wrap">
                                        <label class="cus_check gender_check">
                                            <input type="radio" name="workshopstatus">
                                            <span class="checkmark">Yes</span>
                                        </label>
                                        <label class="cus_check gender_check">
                                            <input type="radio" name="workshopstatus">
                                            <span class="checkmark">No</span>
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="registration-pop_footer">
                    <div class="registration_btn_wrap">
                        <button class="popup_close badge_dark">Cancel</button>
                        <button type="submit" class="mi-1 badge_success"><?php save(); ?>Save</button>
                    </div>
                </div>
            </div>
        </div>
        <!-- Add accompany classification pop up -->
        <!-- Edit Documents Header/ Footer pop up -->
        <div class="pop_up_body" id="editdocheaderfooter">
            <div class="registration_pop_up">
                <div class="registration-pop_heading">
                    <span>Edit Image</span>
                    <p>
                        <a href="javascript:void(null)" class="popup_close icon_hover badge_danger action-transparent"><?php close(); ?></a>
                    </p>
                </div>
                <div class="registration-pop_body">
                    <div class="registration-pop_body_box">
                        <div class="registration-pop_body_box_inner p-0 bg-transparent border-0">
                            <div class="form_grid">
                                <div class="frm_grp span_2">
                                    <p class="frm-head">Document Header</p>
                                    <input style="display: none;" id="documentheader">
                                    <label for="documentheader" class="frm-image"><i class="fal fa-plus"></i> Image</label>
                                    <div class="frm_img_preview">
                                        <img src="https://ruedakolkata.com/natcon2025/uploads/EMAIL.HEADER.FOOTER.IMAGE/HOTELICON_0_0001_250107125112.png">
                                        <button><i class="fal fa-trash-alt"></i></button>
                                    </div>
                                </div>
                                <div class="frm_grp span_2">
                                    <p class="frm-head">Document Footer</p>
                                    <input style="display: none;" id="documentfooter">
                                    <label for="documentfooter" class="frm-image"><i class="fal fa-plus"></i> Image</label>
                                    <div class="frm_img_preview">
                                        <img src="https://ruedakolkata.com/natcon2025/uploads/EMAIL.HEADER.FOOTER.IMAGE/HOTELICON_0_0001_250107125112.png">
                                        <button><i class="fal fa-trash-alt"></i></button>
                                    </div>
                                </div>
                                <div class="frm_grp span_2">
                                    <p class="frm-head">Mailer Logo</p>
                                    <input style="display: none;" id="mailerlogo">
                                    <label for="mailerlogo" class="frm-image"><i class="fal fa-plus"></i> Image</label>
                                    <div class="frm_img_preview">
                                        <img src="https://ruedakolkata.com/natcon2025/uploads/EMAIL.HEADER.FOOTER.IMAGE/HOTELICON_0_0001_250107125112.png">
                                        <button><i class="fal fa-trash-alt"></i></button>
                                    </div>
                                </div>
                                <div class="frm_grp span_2">
                                    <p class="frm-head">Site Logo</p>
                                    <input style="display: none;" id="sitelogo">
                                    <label for="sitelogo" class="frm-image"><i class="fal fa-plus"></i> Image</label>
                                    <div class="frm_img_preview">
                                        <img src="https://ruedakolkata.com/natcon2025/uploads/EMAIL.HEADER.FOOTER.IMAGE/HOTELICON_0_0001_250107125112.png">
                                        <button><i class="fal fa-trash-alt"></i></button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="registration-pop_footer">
                    <div class="registration_btn_wrap">
                        <button class="popup_close badge_dark">Cancel</button>
                        <button type="submit" class="mi-1 badge_success"><?php save(); ?>Save</button>
                    </div>
                </div>
            </div>
        </div>
        <!-- Edit Documents Header/ Footer pop up -->
        <!-- Edit Scientific Section Mailer Setting pop up -->
        <div class="pop_up_body" id="editscisectionmailer">
            <div class="registration_pop_up">
                <div class="registration-pop_heading">
                    <span>Edit Image</span>
                    <p>
                        <a href="javascript:void(null)" class="popup_close icon_hover badge_danger action-transparent"><?php close(); ?></a>
                    </p>
                </div>
                <div class="registration-pop_body">
                    <div class="registration-pop_body_box">
                        <div class="registration-pop_body_box_inner p-0 bg-transparent border-0">
                            <div class="form_grid">
                                <div class="frm_grp span_2">
                                    <p class="frm-head">Header Image</p>
                                    <input style="display: none;" id="sciheaderimage">
                                    <label for="sciheaderimage" class="frm-image"><i class="fal fa-plus"></i> Image</label>
                                    <div class="frm_img_preview">
                                        <img src="https://ruedakolkata.com/natcon2025/uploads/EMAIL.HEADER.FOOTER.IMAGE/HOTELICON_0_0001_250107125112.png">
                                        <button><i class="fal fa-trash-alt"></i></button>
                                    </div>
                                </div>
                                <div class="frm_grp span_2">
                                    <p class="frm-head">Sidebar</p>
                                    <input style="display: none;" id="scisidebarimage">
                                    <label for="scisidebarimage" class="frm-image"><i class="fal fa-plus"></i> Image</label>
                                    <div class="frm_img_preview">
                                        <img src="https://ruedakolkata.com/natcon2025/uploads/EMAIL.HEADER.FOOTER.IMAGE/HOTELICON_0_0001_250107125112.png">
                                        <button><i class="fal fa-trash-alt"></i></button>
                                    </div>
                                </div>
                                <div class="frm_grp span_2">
                                    <p class="frm-head">Footer Image</p>
                                    <input style="display: none;" id="scifooterimage">
                                    <label for="scifooterimage" class="frm-image"><i class="fal fa-plus"></i> Image</label>
                                    <div class="frm_img_preview">
                                        <img src="https://ruedakolkata.com/natcon2025/uploads/EMAIL.HEADER.FOOTER.IMAGE/HOTELICON_0_0001_250107125112.png">
                                        <button><i class="fal fa-trash-alt"></i></button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="registration-pop_footer">
                    <div class="registration_btn_wrap">
                        <button class="popup_close badge_dark">Cancel</button>
                        <button type="submit" class="mi-1 badge_success"><?php save(); ?>Save</button>
                    </div>
                </div>
            </div>
        </div>
        <!-- Edit Scientific Section Mailer Setting pop up -->
        <!-- Edit Exhibitor Mailer Setting pop up -->
        <div class="pop_up_body" id="editexibitorimage">
            <div class="registration_pop_up">
                <div class="registration-pop_heading">
                    <span>Edit Image</span>
                    <p>
                        <a href="javascript:void(null)" class="popup_close icon_hover badge_danger action-transparent"><?php close(); ?></a>
                    </p>
                </div>
                <div class="registration-pop_body">
                    <div class="registration-pop_body_box">
                        <div class="registration-pop_body_box_inner p-0 bg-transparent border-0">
                            <div class="form_grid">
                                <div class="frm_grp span_2">
                                    <p class="frm-head">Header Image</p>
                                    <input style="display: none;" id="exiheaderimage">
                                    <label for="exiheaderimage" class="frm-image"><i class="fal fa-plus"></i> Image</label>
                                    <div class="frm_img_preview">
                                        <img src="https://ruedakolkata.com/natcon2025/uploads/EMAIL.HEADER.FOOTER.IMAGE/HOTELICON_0_0001_250107125112.png">
                                        <button><i class="fal fa-trash-alt"></i></button>
                                    </div>
                                </div>
                                <div class="frm_grp span_2">
                                    <p class="frm-head">Sidebar</p>
                                    <input style="display: none;" id="exisidebarimage">
                                    <label for="exisidebarimage" class="frm-image"><i class="fal fa-plus"></i> Image</label>
                                    <div class="frm_img_preview">
                                        <img src="https://ruedakolkata.com/natcon2025/uploads/EMAIL.HEADER.FOOTER.IMAGE/HOTELICON_0_0001_250107125112.png">
                                        <button><i class="fal fa-trash-alt"></i></button>
                                    </div>
                                </div>
                                <div class="frm_grp span_2">
                                    <p class="frm-head">Footer Image</p>
                                    <input style="display: none;" id="exifooterimage">
                                    <label for="exifooterimage" class="frm-image"><i class="fal fa-plus"></i> Image</label>
                                    <div class="frm_img_preview">
                                        <img src="https://ruedakolkata.com/natcon2025/uploads/EMAIL.HEADER.FOOTER.IMAGE/HOTELICON_0_0001_250107125112.png">
                                        <button><i class="fal fa-trash-alt"></i></button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="registration-pop_footer">
                    <div class="registration_btn_wrap">
                        <button class="popup_close badge_dark">Cancel</button>
                        <button type="submit" class="mi-1 badge_success"><?php save(); ?>Save</button>
                    </div>
                </div>
            </div>
        </div>
        <!-- Edit Exhibitor Mailer Setting pop up -->
        <!-- Edit Mail Setting Listing pop up -->
        <div class="pop_up_body" id="edimailsettinglisting">
            <div class="registration_pop_up">
                <div class="registration-pop_heading">
                    <span>Edit Mail Setting</span>
                    <p>
                        <a href="javascript:void(null)" class="popup_close icon_hover badge_danger action-transparent"><?php close(); ?></a>
                    </p>
                </div>
                <div class="registration-pop_body">
                    <div class="registration-pop_body_box">
                        <div class="registration-pop_body_box_inner p-0 bg-transparent border-0">
                            <div class="form_grid">
                                <div class="frm_grp span_2">
                                    <p class="frm-head">Mail Title</p>
                                    <input>
                                </div>
                                <div class="frm_grp span_2">
                                    <p class="frm-head">Mail Subject</p>
                                    <input>
                                </div>
                                <div class="frm_grp span_4">
                                    <p class="frm-head">Mail Body</p>
                                    <textarea></textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="registration-pop_footer">
                    <div class="registration_btn_wrap">
                        <button class="popup_close badge_dark">Cancel</button>
                        <button type="submit" class="mi-1 badge_success"><?php save(); ?>Save</button>
                    </div>
                </div>
            </div>
        </div>
        <!-- Edit Mail Setting Listing pop up -->
        <!-- Edit Exhibitor Mail Setting Listing pop up -->
        <div class="pop_up_body" id="ediexibitormailsettinglisting">
            <div class="registration_pop_up">
                <div class="registration-pop_heading">
                    <span>Edit Mail Setting</span>
                    <p>
                        <a href="javascript:void(null)" class="popup_close icon_hover badge_danger action-transparent"><?php close(); ?></a>
                    </p>
                </div>
                <div class="registration-pop_body">
                    <div class="registration-pop_body_box">
                        <div class="registration-pop_body_box_inner p-0 bg-transparent border-0">
                            <div class="form_grid">
                                <div class="frm_grp span_2">
                                    <p class="frm-head">Mail Title</p>
                                    <input>
                                </div>
                                <div class="frm_grp span_2">
                                    <p class="frm-head">Mail Subject</p>
                                    <input>
                                </div>
                                <div class="frm_grp span_4">
                                    <p class="frm-head">Mail Body</p>
                                    <textarea></textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="registration-pop_footer">
                    <div class="registration_btn_wrap">
                        <button class="popup_close badge_dark">Cancel</button>
                        <button type="submit" class="mi-1 badge_success"><?php save(); ?>Save</button>
                    </div>
                </div>
            </div>
        </div>
        <!-- Edit Exhibitor Mail Setting Listing pop up -->
        <!-- Edit Membership Mail Setting Listing pop up -->
        <div class="pop_up_body" id="edimembormailsettinglisting">
            <div class="registration_pop_up">
                <div class="registration-pop_heading">
                    <span>Edit Mail Setting</span>
                    <p>
                        <a href="javascript:void(null)" class="popup_close icon_hover badge_danger action-transparent"><?php close(); ?></a>
                    </p>
                </div>
                <div class="registration-pop_body">
                    <div class="registration-pop_body_box">
                        <div class="registration-pop_body_box_inner p-0 bg-transparent border-0">
                            <div class="form_grid">
                                <div class="frm_grp span_2">
                                    <p class="frm-head">Mail Title</p>
                                    <input>
                                </div>
                                <div class="frm_grp span_2">
                                    <p class="frm-head">Mail Subject</p>
                                    <input>
                                </div>
                                <div class="frm_grp span_4">
                                    <p class="frm-head">Mail Body</p>
                                    <textarea></textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="registration-pop_footer">
                    <div class="registration_btn_wrap">
                        <button class="popup_close badge_dark">Cancel</button>
                        <button type="submit" class="mi-1 badge_success"><?php save(); ?>Save</button>
                    </div>
                </div>
            </div>
        </div>
        <!-- Edit Membership Mail Setting Listing pop up -->
        <!-- New Country pop up -->
        <div class="pop_up_body" id="newcountry">
            <div class="registration_pop_up">
                <div class="registration-pop_heading">
                    <span>New Country</span>
                    <p>
                        <a href="javascript:void(null)" class="popup_close icon_hover badge_danger action-transparent"><?php close(); ?></a>
                    </p>
                </div>
                <div class="registration-pop_body">
                    <div class="registration-pop_body_box">
                        <div class="registration-pop_body_box_inner p-0 bg-transparent border-0">
                            <div class="form_grid">

                                <div class="frm_grp span_4">
                                    <p class="frm-head">Country Name</p>
                                    <input>
                                </div>
                                <div class="frm_grp span_2">
                                    <p class="frm-head">Country Abbreviation</p>
                                    <input>
                                </div>
                                <div class="frm_grp span_2">
                                    <p class="frm-head">Country ISD Code</p>
                                    <input>
                                </div>
                                <div class="frm_grp span_4">
                                    <p class="frm-head">Country Currency</p>
                                    <input>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="registration-pop_footer">
                    <div class="registration_btn_wrap">
                        <button class="popup_close badge_dark">Cancel</button>
                        <button type="submit" class="mi-1 badge_success"><?php save(); ?>Save</button>
                    </div>
                </div>
            </div>
        </div>
        <!-- New Country pop up -->
        <!-- edit Country pop up -->
        <div class="pop_up_body" id="editcountry">
            <div class="registration_pop_up">
                <div class="registration-pop_heading">
                    <span>Edit Country</span>
                    <p>
                        <a href="javascript:void(null)" class="popup_close icon_hover badge_danger action-transparent"><?php close(); ?></a>
                    </p>
                </div>
                <div class="registration-pop_body">
                    <div class="registration-pop_body_box">
                        <div class="registration-pop_body_box_inner p-0 bg-transparent border-0">
                            <div class="form_grid">

                                <div class="frm_grp span_4">
                                    <p class="frm-head">Country Name</p>
                                    <input>
                                </div>
                                <div class="frm_grp span_2">
                                    <p class="frm-head">Country Abbreviation</p>
                                    <input>
                                </div>
                                <div class="frm_grp span_2">
                                    <p class="frm-head">Country ISD Code</p>
                                    <input>
                                </div>
                                <div class="frm_grp span_4">
                                    <p class="frm-head">Country Currency</p>
                                    <input>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="registration-pop_footer">
                    <div class="registration_btn_wrap">
                        <button class="popup_close badge_dark">Cancel</button>
                        <button type="submit" class="mi-1 badge_success"><?php save(); ?>Update</button>
                    </div>
                </div>
            </div>
        </div>
        <!-- edit Country pop up -->
        <!-- New State pop up -->
        <div class="pop_up_body" id="newstate">
            <div class="registration_pop_up">
                <div class="registration-pop_heading">
                    <span>New State</span>
                    <p>
                        <a href="javascript:void(null)" class="popup_close icon_hover badge_danger action-transparent"><?php close(); ?></a>
                    </p>
                </div>
                <div class="registration-pop_body">
                    <div class="registration-pop_body_box">
                        <div class="registration-pop_body_box_inner p-0 bg-transparent border-0">
                            <div class="form_grid">

                                <div class="frm_grp span_4">
                                    <p class="frm-head">Country Name</p>
                                    <select></select>
                                </div>
                                <div class="frm_grp span_4">
                                    <div class="accm_add_box">
                                        <div class="form_grid">
                                            <div class="frm_grp span_4">
                                                <input>
                                            </div>
                                            <div class="frm_grp span_2">
                                                <div class="cus_check_wrap">
                                                    <label class="cus_check gender_check">
                                                        <input type="radio" name="food">
                                                        <span class="checkmark">Veg</span>
                                                    </label>
                                                    <label class="cus_check gender_check">
                                                        <input type="radio" name="food">
                                                        <span class="checkmark">Non Veg</span>
                                                    </label>
                                                </div>
                                            </div>
                                            <div class="frm_grp span_2">
                                                <a href="#" class="accm_delet icon_hover badge_danger action-transparent"><i class="fal fa-trash-alt"></i></a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="registration-pop_footer">
                    <div class="registration_btn_wrap">
                        <button class="popup_close badge_dark">Cancel</button>
                        <button type="submit" class="mi-1 badge_success"><?php save(); ?>Save</button>
                    </div>
                </div>
            </div>
        </div>
        <!-- New State pop up -->
        <!-- edit State pop up -->
        <div class="pop_up_body" id="editstate">
            <div class="registration_pop_up">
                <div class="registration-pop_heading">
                    <span>Edit Country</span>
                    <p>
                        <a href="javascript:void(null)" class="popup_close icon_hover badge_danger action-transparent"><?php close(); ?></a>
                    </p>
                </div>
                <div class="registration-pop_body">
                    <div class="registration-pop_body_box">
                        <div class="registration-pop_body_box_inner p-0 bg-transparent border-0">
                            <div class="form_grid">

                                <div class="frm_grp span_4">
                                    <p class="frm-head">Country Name</p>
                                    <select></select>
                                </div>
                                <div class="frm_grp span_2">
                                    <p class="frm-head">Country Abbreviation</p>
                                    <input>
                                </div>
                                <div class="frm_grp span_2">
                                    <p class="frm-head">Country ISD Code</p>
                                    <input>
                                </div>
                                <div class="frm_grp span_4">
                                    <p class="frm-head">Country Currency</p>
                                    <input>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="registration-pop_footer">
                    <div class="registration_btn_wrap">
                        <button class="popup_close badge_dark">Cancel</button>
                        <button type="submit" class="mi-1 badge_success"><?php save(); ?>Update</button>
                    </div>
                </div>
            </div>
        </div>
        <!-- edit State pop up -->
        <!-- New combo pop up -->
        <div class="pop_up_body" id="newcombo">
            <div class="registration_pop_up">
                <div class="registration-pop_heading">
                    <span>Add Registration Combo Classification</span>
                    <p>
                        <a href="javascript:void(null)" class="popup_close icon_hover badge_danger action-transparent"><?php close(); ?></a>
                    </p>
                </div>
                <div class="registration-pop_body">
                    <div class="registration-pop_body_box">
                        <div class="registration-pop_body_box_inner">
                            <h4 class="registration-pop_body_box_heading">
                                <span>Regsitration Classification</span>
                            </h4>
                            <div class="form_grid">
                                <div class="frm_grp span_2">
                                    <p class="frm-head">Package Name</p>
                                    <input>
                                </div>
                                <div class="frm_grp span_2">
                                    <p class="frm-head">Cutoff <i class="mandatory">*</i></p>
                                    <input>
                                </div>
                                <div class="frm_grp span_2">
                                    <p class="frm-head">Registration Classification</p>
                                    <select></select>
                                </div>
                                <div class="frm_grp span_2">
                                    <p class="frm-head">Registration Price <i class="mandatory">*</i></p>
                                    <input>
                                </div>
                                <div class="frm_grp span_4">
                                    <p class="frm-head">Workshop Price<i class="mandatory">*</i></p>
                                    <input>
                                </div>
                                <div class="frm_grp span_4">
                                    <p class="frm-head">Workshop Classification <i class="mandatory">*</i></p>
                                    <select class="mySelect for" multiple="multiple" style="width: 100%;">
                                        <option>TB & Critical Care Workshop</option>
                                        <option>TB & Critical Care Workshop</option>
                                        <option>TB & Critical Care Workshop</option>
                                        <option>TB & Critical Care Workshop</option>
                                    </select>
                                </div>
                                <div class="frm_grp span_4">
                                    <p class="frm-head">Dinner Price</p>
                                    <input>
                                </div>
                                <div class="frm_grp span_2">
                                    <p class="frm-head">Inclusion Lunch Date</p>
                                    <input type="date">
                                </div>
                                <div class="frm_grp span_2">
                                    <p class="frm-head">Inclusion Conference Dinner Date</p>
                                    <input type="date">
                                </div>
                                <div class="frm_grp span_2">
                                    <p class="frm-head">Entry to Scientific Halls</p>
                                    <div class="cus_check_wrap">
                                        <label class="cus_check gender_check">
                                            <input type="radio" name="food">
                                            <span class="checkmark">Yes</span>
                                        </label>
                                        <label class="cus_check gender_check">
                                            <input type="radio" name="food">
                                            <span class="checkmark">No</span>
                                        </label>
                                    </div>
                                </div>
                                <div class="frm_grp span_2">
                                    <p class="frm-head">Entry to Exhibition Area</p>
                                    <div class="cus_check_wrap">
                                        <label class="cus_check gender_check">
                                            <input type="radio" name="food">
                                            <span class="checkmark">Yes</span>
                                        </label>
                                        <label class="cus_check gender_check">
                                            <input type="radio" name="food">
                                            <span class="checkmark">No</span>
                                        </label>
                                    </div>
                                </div>
                                <div class="frm_grp span_2">
                                    <p class="frm-head">Tea/Coffee during the Conference</p>
                                    <div class="cus_check_wrap">
                                        <label class="cus_check gender_check">
                                            <input type="radio" name="food">
                                            <span class="checkmark">Yes</span>
                                        </label>
                                        <label class="cus_check gender_check">
                                            <input type="radio" name="food">
                                            <span class="checkmark">No</span>
                                        </label>
                                    </div>
                                </div>
                                <div class="frm_grp span_2">
                                    <p class="frm-head">Inclusion Conference Kit</p>
                                    <div class="cus_check_wrap">
                                        <label class="cus_check gender_check">
                                            <input type="radio" name="food">
                                            <span class="checkmark">Yes</span>
                                        </label>
                                        <label class="cus_check gender_check">
                                            <input type="radio" name="food">
                                            <span class="checkmark">No</span>
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>
                    <div class="registration-pop_body_box">
                        <div class="registration-pop_body_box_inner">
                            <h4 class="registration-pop_body_box_heading">
                                <span>Hotel Details</span>
                            </h4>
                            <div class="form_grid">
                                <div class="frm_grp span_2">
                                    <p class="frm-head">Hotel</p>
                                    <select></select>
                                </div>
                                <div class="frm_grp span_2">
                                    <p class="frm-head">Room Type <i class="mandatory">*</i></p>
                                    <input>
                                </div>
                                <div class="frm_grp span_2">
                                    <p class="frm-head">Accommodation Price (Individual)</p>
                                    <input>
                                </div>
                                <div class="frm_grp span_2">
                                    <p class="frm-head">Accommodation Price (Shared)</p>
                                    <input>
                                </div>
                                <div class="frm_grp span_2 accm_add_box pl-0 pt-0 pb-0 bg-transparent">
                                    <p class="frm-head">No. Of Night</p>
                                    <input>
                                    <a href="#" class="accm_delet icon_hover badge_primary action-transparent"><i class="fal fa-paper-plane"></i></a>
                                </div>
                                <div class="frm_grp span_2">
                                    <p class="frm-head">Seat limit</p>
                                    <input>
                                </div>
                                <div class="span_4 accm_add_box badge_padding">
                                    <div class="table_wrap">
                                        <table>
                                            <thead>
                                                <tr>
                                                    <th>Check In Date</th>
                                                    <th>Check Out Date</th>
                                                    <th>INR Rate</th>
                                                    <th>USD Rate</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr>
                                                    <td>2026-01-17</td>
                                                    <td>2026-01-18</td>
                                                    <td>5000.00</td>
                                                    <td>16.00</td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>

                                <div class="frm_grp span_2">
                                    <p class="frm-head">Sequence By</p>
                                    <input>
                                </div>
                                <div class="frm_grp span_2">
                                    <p class="frm-head">Currency</p>
                                    <select></select>
                                </div>
                                <div class="frm_grp span_2">
                                    <p class="frm-head">Round Of price (Individual)</p>
                                    <input>
                                </div>
                                <div class="frm_grp span_2">
                                    <p class="frm-head">Round Of price (Shared)</p>
                                    <input>
                                </div>
                            </div>
                            <h6 class="due_balance mt-3 d-flex justify-content-between align-items-center badge_danger py-2 px-2">Total Price (Individual)<span class="text-white mt-0">₹ 12,980</span></h6>
                            <h6 class="due_balance mt-3 d-flex justify-content-between align-items-center badge_danger py-2 px-2">Total Price (Shared)<span class="text-white mt-0">₹ 12,980</span></h6>
                            <h6 class="due_balance mt-3 d-flex justify-content-between align-items-center badge_success py-2 px-2">Total Round Of Price (Individual)<span class="text-white mt-0">₹ 12,980</span></h6>
                            <h6 class="due_balance mt-3 d-flex justify-content-between align-items-center badge_success py-2 px-2">Total Round Of Price (Shared)<span class="text-white mt-0">₹ 12,980</span></h6>
                        </div>

                    </div>

                </div>
                <div class="registration-pop_footer">
                    <div class="registration_btn_wrap">
                        <button class="popup_close badge_dark">Cancel</button>
                        <button type="submit" class="mi-1 badge_success"><?php save(); ?>Save</button>
                    </div>
                </div>
            </div>
        </div>
        <!-- New combo pop up -->
        <!-- New registration classification pop up -->
        <div class="pop_up_body" id="newregistrationclassification">
            <div class="registration_pop_up">
                <div class="registration-pop_heading">
                    <span>Add Registration Classification</span>
                    <p>
                        <a href="javascript:void(null)" class="popup_close icon_hover badge_danger action-transparent"><?php close(); ?></a>
                    </p>
                </div>
                <div class="registration-pop_body">
                    <div class="registration-pop_body_box">
                        <div class="registration-pop_body_box_inner">
                            <div class="form_grid">
                                <div class="frm_grp span_4">
                                    <p class="frm-head">Classification Title</p>
                                    <input>
                                </div>
                                <div class="frm_grp span_4">
                                    <p class="frm-head">Classification Type <i class="mandatory">*</i></p>
                                    <select></select>
                                </div>
                                <div class="frm_grp span_2">
                                    <p class="frm-head">Seat limit</p>
                                    <input>
                                </div>
                                <div class="frm_grp span_2">
                                    <p class="frm-head">Sequence By <i class="mandatory">*</i></p>
                                    <input>
                                </div>
                                <div class="frm_grp span_2">
                                    <p class="frm-head">Currency<i class="mandatory">*</i></p>
                                    <input>
                                </div>
                                <div class="frm_grp span_2">
                                    <p class="frm-head">Icon</p>
                                    <input style="display: none;" id="roomimage">
                                    <label for="roomimage" class="frm-image"><i class="fal fa-upload"></i></label>
                                    <div class="frm_img_preview">
                                        <img src="https://ruedakolkata.com/natcon2025/uploads/EMAIL.HEADER.FOOTER.IMAGE/HOTELICON_0_0001_250107125112.png">
                                        <button><i class="fal fa-trash-alt"></i></button>
                                    </div>
                                </div>
                                <div class="frm_grp span_2">
                                    <p class="frm-head">Inclusion Lunch Date</p>
                                    <input type="date">
                                </div>
                                <div class="frm_grp span_2">
                                    <p class="frm-head">Inclusion Conference Dinner Date</p>
                                    <input type="date">
                                </div>
                                <div class="frm_grp span_2">
                                    <p class="frm-head">Entry to Scientific Halls</p>
                                    <div class="cus_check_wrap">
                                        <label class="cus_check gender_check">
                                            <input type="radio" name="food">
                                            <span class="checkmark">Yes</span>
                                        </label>
                                        <label class="cus_check gender_check">
                                            <input type="radio" name="food">
                                            <span class="checkmark">No</span>
                                        </label>
                                    </div>
                                </div>
                                <div class="frm_grp span_2">
                                    <p class="frm-head">Entry to Exhibition Area</p>
                                    <div class="cus_check_wrap">
                                        <label class="cus_check gender_check">
                                            <input type="radio" name="food">
                                            <span class="checkmark">Yes</span>
                                        </label>
                                        <label class="cus_check gender_check">
                                            <input type="radio" name="food">
                                            <span class="checkmark">No</span>
                                        </label>
                                    </div>
                                </div>
                                <div class="frm_grp span_2">
                                    <p class="frm-head">Tea/Coffee during the Conference</p>
                                    <div class="cus_check_wrap">
                                        <label class="cus_check gender_check">
                                            <input type="radio" name="food">
                                            <span class="checkmark">Yes</span>
                                        </label>
                                        <label class="cus_check gender_check">
                                            <input type="radio" name="food">
                                            <span class="checkmark">No</span>
                                        </label>
                                    </div>
                                </div>
                                <div class="frm_grp span_2">
                                    <p class="frm-head">Inclusion Conference Kit</p>
                                    <div class="cus_check_wrap">
                                        <label class="cus_check gender_check">
                                            <input type="radio" name="food">
                                            <span class="checkmark">Yes</span>
                                        </label>
                                        <label class="cus_check gender_check">
                                            <input type="radio" name="food">
                                            <span class="checkmark">No</span>
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>

                </div>
                <div class="registration-pop_footer">
                    <div class="registration_btn_wrap">
                        <button class="popup_close badge_dark">Cancel</button>
                        <button type="submit" class="mi-1 badge_success"><?php save(); ?>Save</button>
                    </div>
                </div>
            </div>
        </div>
        <!-- New registration classification pop up -->
    </div>
</div>
<script>
    document.addEventListener("click", function(e) {
        if (e.target.closest(".popup_close")) {
            e.preventDefault(); // stops form submit
            document.getElementById("editworkshoptariff").style.display = "none";
            $(".pop_up_wrap").hide(); // blur appears
            $(".pop_up_body").hide();
        }


    });
    
</script>
<script>
document.addEventListener("DOMContentLoaded", function () {

    const addBtn = document.getElementById("add_package_1");
    const wrapper = document.getElementById("accm_add_wrap_1");
    const empty = document.getElementById("accm_add_empty_1");
    const template = document.getElementById("package_box");

    addBtn.addEventListener("click", function (e) {
        e.preventDefault();

        empty.style.display = "none";

        const clone = template.cloneNode(true);
        clone.style.display = "block";
        clone.removeAttribute("id"); // VERY IMPORTANT

        wrapper.appendChild(clone);
    });

    wrapper.addEventListener("click", function (e) {
        if (e.target.closest(".accm_delet")) {
            e.preventDefault();
            e.target.closest(".accm_add_box").remove();

            if (wrapper.querySelectorAll(".accm_add_box").length === 1) {
                empty.style.display = "block";
            }
        }
    });

});
</script>
<!-- <script>
document.addEventListener("click", function(e) {
    if (e.target.closest("#edit_package_1")) {
        e.preventDefault();
        
        const wrapper1 = document.getElementById('accm_edit_wrap_1');
        
        // FIX the wrapper's display (don't replace it)
        wrapper1.style.cssText = `
            display: block !important;
            visibility: visible !important;
            opacity: 1 !important;
            min-height: 100px !important;
            background: #f8f9fa !important;
            border: 2px solid #dee2e6 !important;
            border-radius: 5px !important;
            padding: 20px !important;
            margin: 10px 0 !important;
        `;
        
        // Create NEW package box with different structure
        const newBox = document.createElement('div');
        newBox.className = 'package-item-visible';
        
        newBox.innerHTML = `
            <div class="package-content" style="
                display: flex !important;
                align-items: flex-start !important;
                justify-content: space-between !important;
                background: white !important;
                padding: 15px !important;
                margin-bottom: 10px !important;
                border: 1px solid #ced4da !important;
                border-radius: 4px !important;
                min-height: 70px !important;
                box-sizing: border-box !important;
            ">
                <div style="flex: 1 !important; margin-right: 15px !important;">
                    <div style="
                        font-weight: bold !important;
                        margin-bottom: 5px !important;
                        color: #495057 !important;
                    ">Package Name</div>
                    <input type="text" 
                           name="package_name[]" 
                           value=""
                           class="package-input"
                           style="
                               width: 100% !important;
                               padding: 8px 12px !important;
                               border: 1px solid #ced4da !important;
                               border-radius: 3px !important;
                               font-size: 14px !important;
                               box-sizing: border-box !important;
                           ">
                </div>
                <button type="button" 
                        class="delete-package-btn"
                        style="
                            background: #dc3545 !important;
                            color: white !important;
                            border: none !important;
                            padding: 8px 16px !important;
                            border-radius: 3px !important;
                            cursor: pointer !important;
                            font-size: 14px !important;
                            align-self: center !important;
                        ">
                    Delete
                </button>
            </div>
        `;
        
        wrapper1.appendChild(newBox);
        
        // Focus on the new input
        setTimeout(() => {
            const input = newBox.querySelector('.package-input');
            if (input) input.focus();
        }, 100);
        
        console.log('✓ Package added. Total packages:', wrapper1.querySelectorAll('.package-item-visible').length);
    }
    
    // Handle delete buttons for NEW packages
    if (e.target.closest('.delete-package-btn')) {
        e.preventDefault();
        const packageItem = e.target.closest('.package-item-visible');
        if (packageItem) {
            packageItem.remove();
            console.log('✓ Package deleted');
        }
    }
    
    // Handle delete buttons for OLD packages (if they exist)
    if (e.target.closest('.accm_delet')) {
        e.preventDefault();
        const packageItem = e.target.closest('.accm_add_box');
        if (packageItem && packageItem.id !== 'package_template') {
            packageItem.remove();
            console.log('✓ Old package deleted');
        }
    }
});
</script> -->
<script>
function initEditHotelPackages() {

    const wrapper1  = document.getElementById("accm_edit_wrap_1");
    const template1 = document.getElementById("package_template");
    const empty1    = document.getElementById("accm_edit_empty_1");

    if (!wrapper1 || !template1) return;

    // prevent multiple bindings
    if (document.body.dataset.accmInit === "1") return;
    document.body.dataset.accmInit = "1";

    document.addEventListener("click", function (e) {

        // ADD
        if (e.target.closest("#edit_package_1")) {
            e.preventDefault();

            if (empty1) empty1.style.display = "none";

            const clone = template1.cloneNode(true);
            clone.style.display = "block";
            clone.removeAttribute("id");

            wrapper1.appendChild(clone);
        }

        // DELETE
        const delBtn = e.target.closest(".accm_delet");
        if (delBtn) {
            e.preventDefault();

            const box = delBtn.closest(".accm_add_box");
            if (box) box.remove();

            if (
                wrapper1.querySelectorAll(".accm_add_box").length === 0 &&
                empty1
            ) {
                empty1.style.display = "block";
            }
        }
    });
}

// initial page load
document.addEventListener("DOMContentLoaded", initEditHotelPackages);
</script>
<script>
document.addEventListener("DOMContentLoaded", function () {

    const addBtn = document.getElementById("add_aminity_1");
    const wrapper = document.getElementById("accm_add_wrap_2");
    const empty = document.getElementById("accm_add_empty_2");
    const template = document.getElementById("aminity_box");

    addBtn.addEventListener("click", function (e) {
        e.preventDefault();

        empty.style.display = "none";

        const clone = template.cloneNode(true);
        clone.style.display = "block";
        clone.removeAttribute("id"); // VERY IMPORTANT

        wrapper.appendChild(clone);
    });

    wrapper.addEventListener("click", function (e) {
        if (e.target.closest(".accm_delet")) {
            e.preventDefault();
            e.target.closest(".accm_add_box").remove();

            if (wrapper.querySelectorAll(".accm_add_box").length === 1) {
                empty.style.display = "block";
            }
        }
    });

});
</script>
<script>
function initEditHotelAminity() {

    const addBtn = document.getElementById("edit_aminity_1");
    const wrapper = document.getElementById("accm_edit_wrap_2");
    const empty = document.getElementById("accm_edit_empty_2");
    const template = document.getElementById("aminity_box_edit");

    addBtn.addEventListener("click", function (e) {
        e.preventDefault();
    console.log(addBtn);
        empty.style.display = "none";

        const clone = template.cloneNode(true);
        clone.style.display = "block";
        clone.removeAttribute("id"); // VERY IMPORTANT

        wrapper.appendChild(clone);
    });

    wrapper.addEventListener("click", function (e) {
        if (e.target.closest(".accm_delet")) {
            e.preventDefault();
            e.target.closest(".accm_add_box").remove();

            if (wrapper.querySelectorAll(".accm_add_box").length === 1) {
                empty.style.display = "block";
            }
        }
    });
    /////////////for Aminities Icon Edit///////////////////////

    document.addEventListener('click', function (e) {

        const box = e.target.closest('.iconBox_edit');
        if (!box) return;

        const input = box.querySelector('.icon_input_edit');
        const previewBox = box.querySelector('.frm_img_preview');
        const previewImg = box.querySelector('.iconPreview_edit');

        // Upload click
        if (e.target.closest('.uploadIcon_edit')) {
            input.click();
            return;
        }

        // Remove icon
        if (e.target.closest('.removeIcon_edit')) {
            input.value = "";
            previewImg.src = "";
            previewBox.style.display = "none";
            box.querySelector('.uploadIcon_edit').style.display = "block";
            return;
        }
    });

    // File change
    document.addEventListener('change', function (e) {
        if (!e.target.classList.contains('icon_input_edit')) return;

        const input = e.target;
        const box = input.closest('.iconBox_edit');
        const previewBox = box.querySelector('.frm_img_preview');
        const previewImg = box.querySelector('.iconPreview_edit');

        const file = input.files[0];
        if (!file || !file.type.startsWith('image/')) {
            alert('Please select an image file');
            input.value = "";
            return;
        }

        const reader = new FileReader();
        reader.onload = function (evt) {
            previewImg.src = evt.target.result;
            previewBox.style.display = "block";
            box.querySelector('.uploadIcon_edit').style.display = "none";
        };
        reader.readAsDataURL(file);
    });
    //////////////////////end////////////////
}

// initial page load
document.addEventListener("DOMContentLoaded", initEditHotelAminity);
</script>
<script>
document.addEventListener("DOMContentLoaded", function () {

    const addBtn = document.getElementById("add_room_1");
    const wrapper = document.getElementById("accm_add_wrap_3");
    const empty = document.getElementById("accm_add_empty_3");
    const template = document.getElementById("room_box");

    addBtn.addEventListener("click", function (e) {
        e.preventDefault();

        empty.style.display = "none";

        const clone = template.cloneNode(true);
        clone.style.display = "block";
        clone.removeAttribute("id"); // VERY IMPORTANT

        wrapper.appendChild(clone);
    });

    wrapper.addEventListener("click", function (e) {
        if (e.target.closest(".accm_delet")) {
            e.preventDefault();
            e.target.closest(".accm_add_box").remove();

            if (wrapper.querySelectorAll(".accm_add_box").length === 1) {
                empty.style.display = "block";
            }
        }
    });

});
function updateSeatLimits() {
    const checkIn = document.getElementById('check_in').value;
    const checkOut = document.getElementById('check_out').value;
    const container = document.getElementById('seat_limits_container');
    
    // Clear previous rows
    container.innerHTML = '';

    if (!checkIn || !checkOut) return;

    const startDate = new Date(checkIn);
    const endDate = new Date(checkOut);

    if (endDate < startDate) {
        alert("Check-out date must be after check-in date!");
        return;
    }

    // Function to format date as dd/mm/yyyy
    function formatDate(date) {
        const day = String(date.getDate()).padStart(2, '0');
        const month = String(date.getMonth() + 1).padStart(2, '0');
        const year = date.getFullYear();
        return `${day}/${month}/${year}`;
    }

    // Loop through all dates
    let currentDate = new Date(startDate);
    while (currentDate < endDate) {
        const row = document.createElement('div');
        row.className = 'registration-pop_body_box_inner span_4';
        row.innerHTML = `
            <div class="form_grid">
                <div class="frm_grp span_2">
                    <p class="frm-head">Date</p>
                    <p class="typed_data">${formatDate(currentDate)}</p>
                </div>
                <div class="frm_grp span_2">
                    <p class="frm-head">Seat</p>
                    <input name="seat_limit[]" type="number" min="0" />
                </div>
            </div>
        `;
        container.appendChild(row);

        // Move to next day
        currentDate.setDate(currentDate.getDate() + 1);
    }
}
//////////////For edit form/////////
function updateSeatLimitsEdit() {
    const checkIn = document.getElementById('check_in_Edit').value;
    const checkOut = document.getElementById('check_out_Edit').value;
    const container = document.getElementById('seat_limits_container_Edit');

    if (!checkIn || !checkOut) return;

    const startDate = new Date(checkIn);
    const endDate = new Date(checkOut);

    if (endDate < startDate) {
        alert("Check-out date must be after check-in date!");
        return;
    }

    container.innerHTML = '';

    function formatDate(date) {
        const d = String(date.getDate()).padStart(2, '0');
        const m = String(date.getMonth() + 1).padStart(2, '0');
        const y = date.getFullYear();
        return `${d}/${m}/${y}`;
    }

    let currentDate = new Date(startDate);

    while (currentDate < endDate) {
        const isoDate = currentDate.toISOString().split('T')[0];

        container.innerHTML += `
            <div class="registration-pop_body_box_inner span_4 seat-row">
                <div class="form_grid">
                    <div class="frm_grp span_2">
                        <p class="frm-head">Date</p>
                        <p class="typed_data">${formatDate(currentDate)}</p>
                        <input type="hidden" name="seat_date[]" value="${isoDate}">
                    </div>
                    <div class="frm_grp span_2">
                        <p class="frm-head">Seat</p>
                        <input name="seat_limit[]" type="number" min="0">
                    </div>
                </div>
            </div>
        `;

        currentDate.setDate(currentDate.getDate() + 1);
    }
}
</script>
<script>
    /////////////for Images///////////////////////

document.addEventListener('DOMContentLoaded', function() {
    const container = document.querySelector('.com_info_branding_wrap');
    if (!container) return;

    // Event delegation for clicks
    container.addEventListener('click', function(e) {
        const box = e.target.closest('.addBox');
        if (!box) return;

        const input = box.querySelector('.webmaster_background');
        const preview = box.querySelector('.previewImage');
    console.log('Input element found:', input); // should log input element

        // Remove button
        if (e.target.closest('.removeImage')) {
            preview.src = "images/Banner KTC BG.png";
            input.value = "";
             box.querySelector('.branding_image_upload').style.display = 'block';
             box.querySelector('.branding_image_preview').style.display = 'none';
            return;
        }

        // Upload label
       if (e.target.closest('.uploadLabel') || e.target.tagName === 'SPAN') {
    input.click();
    return;
}
    });

    // Event delegation for file input changes
   container.addEventListener('change', function(e) {
    const input = e.target;
    if (!input.classList.contains('webmaster_background')) return;

    const box = input.closest('.addBox');
    const preview = box.querySelector('.previewImage');

    if (input.files && input.files[0]) {
        const file = input.files[0];

        if (!file.type.startsWith('image/')) {
            alert('Please select an image file.');
            input.value = "";
            return;
        }

        const reader = new FileReader();

        reader.onload = function(evt) {
            preview.src = evt.target.result; // <- THIS sets the preview
            // console.log('Preview updated for:', file.name);
            // console.log('Ievt.target.result:', evt.target.result); // should log input element
           box.querySelector('.branding_image_upload').style.display = 'none';
           box.querySelector('.branding_image_preview').style.display = 'block';

        };

        reader.readAsDataURL(file); // <- converts File to base64
    }
});

});
/////////////////////end//////////////////////////

/////////////for Aminities Icon///////////////////////
document.addEventListener('DOMContentLoaded', function () {

    document.addEventListener('click', function (e) {

        const box = e.target.closest('.iconBox');
        if (!box) return;

        const input = box.querySelector('.icon_input');
        const previewBox = box.querySelector('.frm_img_preview');
        const previewImg = box.querySelector('.iconPreview');

        // Upload click
        if (e.target.closest('.uploadIcon')) {
            input.click();
            return;
        }

        // Remove icon
        if (e.target.closest('.removeIcon')) {
            input.value = "";
            previewImg.src = "";
            previewBox.style.display = "none";
            box.querySelector('.uploadIcon').style.display = "block";
            return;
        }
    });

    // File change
    document.addEventListener('change', function (e) {
        if (!e.target.classList.contains('icon_input')) return;

        const input = e.target;
        const box = input.closest('.iconBox');
        const previewBox = box.querySelector('.frm_img_preview');
        const previewImg = box.querySelector('.iconPreview');

        const file = input.files[0];
        if (!file || !file.type.startsWith('image/')) {
            alert('Please select an image file');
            input.value = "";
            return;
        }

        const reader = new FileReader();
        reader.onload = function (evt) {
            previewImg.src = evt.target.result;
            previewBox.style.display = "block";
            box.querySelector('.uploadIcon').style.display = "none";
        };
        reader.readAsDataURL(file);
    });

});
////////////////end///////////////////////////////


/////////////for room images///////////////////////

document.addEventListener('DOMContentLoaded', function () {

    document.addEventListener('click', function (e) {

        const box = e.target.closest('.roomImageBox');
        if (!box) return;

        const input = box.querySelector('.room_input');
        const previewBox = box.querySelector('.frm_img_preview');
        const previewImg = box.querySelector('.roomPreview');

        // Upload click
        if (e.target.closest('.uploadRoomImage')) {
            input.click();
            return;
        }

        // Remove icon
        if (e.target.closest('.removeRoomImage')) {
            input.value = "";
            previewImg.src = "";
            previewBox.style.display = "none";
            box.querySelector('.uploadRoomImage').style.display = "block";
            return;
        }
    });

    // File change
    document.addEventListener('change', function (e) {
        if (!e.target.classList.contains('room_input')) return;

        const input = e.target;
        const box = input.closest('.roomImageBox');
        const previewBox = box.querySelector('.frm_img_preview');
        const previewImg = box.querySelector('.roomPreview');

        const file = input.files[0];
        if (!file || !file.type.startsWith('image/')) {
            alert('Please select an image file');
            input.value = "";
            return;
        }

        const reader = new FileReader();
        reader.onload = function (evt) {
            previewImg.src = evt.target.result;
            previewBox.style.display = "block";
            box.querySelector('.uploadRoomImage').style.display = "none";
        };
        reader.readAsDataURL(file);
    });

});
</script>
<script>
function initEditHotelRoomType() {

    const addBtn = document.getElementById("edit_room_1");
    const wrapper = document.getElementById("accm_edit_wrap_3");
    const empty = document.getElementById("accm_edit_empty_3");
    const template = document.getElementById("room_box_edit");

    addBtn.addEventListener("click", function (e) {
        e.preventDefault();

        empty.style.display = "none";

        const clone = template.cloneNode(true);
        clone.style.display = "block";
        clone.removeAttribute("id"); // VERY IMPORTANT

        wrapper.appendChild(clone);
    });

    wrapper.addEventListener("click", function (e) {
        if (e.target.closest(".accm_delet")) {
            e.preventDefault();
            e.target.closest(".accm_add_box").remove();

            if (wrapper.querySelectorAll(".accm_add_box").length === 1) {
                empty.style.display = "block";
            }
        }
    });
  document.addEventListener('click', function (e) {

        const box = e.target.closest('.roomImageBox_edit');
        if (!box) return;

        const input = box.querySelector('.room_input_edit');
        const previewBox = box.querySelector('.frm_img_preview');
        const previewImg = box.querySelector('.roomPreview_edit');

        // Upload click
        if (e.target.closest('.uploadRoomImage_edit')) {
            input.click();
            return;
        }

        // Remove icon
        if (e.target.closest('.removeRoomImage_edit')) {
            input.value = "";
            previewImg.src = "";
            previewBox.style.display = "none";
            box.querySelector('.uploadRoomImage_edit').style.display = "block";
            return;
        }
    });

    // File change
    document.addEventListener('change', function (e) {
        if (!e.target.classList.contains('room_input_edit')) return;

        const input = e.target;
        const box = input.closest('.roomImageBox_edit');
        const previewBox = box.querySelector('.frm_img_preview');
        const previewImg = box.querySelector('.roomPreview_edit');
        console.log(box);
        const file = input.files[0];
        if (!file || !file.type.startsWith('image/')) {
            alert('Please select an image file');
            input.value = "";
            return;
        }

        const reader = new FileReader();
        reader.onload = function (evt) {
            previewImg.src = evt.target.result;
            previewBox.style.display = "block";
            box.querySelector('.uploadRoomImage_edit').style.display = "none";
        };
        reader.readAsDataURL(file);
    });

}

// initial page load
document.addEventListener("DOMContentLoaded", initEditHotelRoomType);
</script>
<script>
function initEditHotelSlider() {

            ///////For slider images/////////
    const container = document.querySelector('.com_info_branding_wrap1');

    if (!container) return;
        console.log('container element found:', container); // should log input element

    // Event delegation for clicks
    container.addEventListener('click', function(e) {
        const box = e.target.closest('.editBox');

        if (!box) return;

        const input = box.querySelector('.webmaster_background');
        const preview = box.querySelector('.editpreviewImage');
        console.log('Input element found:', input); // should log input element

        // Remove button
    //    if (e.target.closest('.editremoveImage')) {
    //        preview.src = "images/Banner KTC BG.png";
    //        input.value = "";
    //        box.querySelector('.branding_image_upload').style.display = 'block';
    //        box.querySelector('.branding_image_preview').style.display = 'none';
    //     return;
    //     }
        if (e.target.closest('.editremoveImage')) {

            preview.src = "images/Banner KTC BG.png";
            input.value = "";

            // 🔥 CLEAR HIDDEN FIELDS (CRITICAL)
            const existIcon = box.querySelector('input[name="slider_exist_icon[]"]');
            const sliderId  = box.querySelector('input[name="slider_id[]"]');

            if (existIcon) existIcon.value = '';
            if (sliderId)  sliderId.value = '';

            box.querySelector('.branding_image_upload').style.display = 'block';
            box.querySelector('.branding_image_preview').style.display = 'none';

            return;
        }
        // Upload label
    if (e.target.closest('.edituploadLabel') || e.target.tagName === 'SPAN') {
    input.click();
    return;
    }
    });

        // Event delegation for file input changes
    container.addEventListener('change', function(e) {
        const input = e.target;
        if (!input.classList.contains('webmaster_background')) return;

        const box = input.closest('.editBox');
        const preview = box.querySelector('.editpreviewImage');

        if (input.files && input.files[0]) {
            const file = input.files[0];

            if (!file.type.startsWith('image/')) {
                alert('Please select an image file.');
                input.value = "";
                return;
            }

            const reader = new FileReader();

            reader.onload = function(evt) {
                preview.src = evt.target.result; // <- THIS sets the preview
                // console.log('Preview updated for:', file.name);
                // console.log('Ievt.target.result:', evt.target.result); // should log input element
            box.querySelector('.branding_image_upload').style.display = 'none';
            box.querySelector('.branding_image_preview').style.display = 'block';

            };

            reader.readAsDataURL(file); // <- converts File to base64
        }
    });
}
// initial page load
document.addEventListener("DOMContentLoaded", initEditHotelSlider);
</script>
<!-- <script>
document.addEventListener("DOMContentLoaded", function () {

    const addBtn = document.getElementById("add_package_1");
    const box = document.getElementById("package_box");
    const empty = document.getElementById("accm_add_empty_1");

    addBtn.addEventListener("click", function (e) {
        e.preventDefault();
        box.style.display = "block";
        empty.style.display = "none";
    });

});

function hidePackageBox() {
    document.getElementById("package_box").style.display = "none";
    document.getElementById("accm_add_empty_1").style.display = "block";
}
</script> -->
<!-- <style>
					.notify-defult { background-color: #dff0d8; color: #468847; border-color: #d6e9c6; }
				</style>
                <div class="notify notify-success" fortype="messageDiv" style="display: none;">
					<p>Data Updated Successfully</p>
				</div> -->
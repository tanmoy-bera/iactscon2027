<?php 
include_once("includes/source.php");
include_once("includes/source.php"); 
include_once('includes/init.php');
include_once('includes/function.workshop.php');
include_once("../../includes/function.registration.php");
include_once('../../includes/function.delegate.php');
include_once('../../includes/function.invoice.php');
include_once('../../includes/function.workshop.php');
include_once('../../includes/function.dinner.php');
include_once('../../includes/function.accompany.php');
include_once('../../includes/function.accommodation.php');
include_once('../../includes/function.abstract.php');
include_once('includes/function.php');
// $cfg['SECTION_BASE_URL'] = "https://ruedakolkata.com/natcon_25/conference_registration/webmaster/";
 ?>

<body>
    <?php include_once("includes/left-menu.php"); ?>
    <header>
        <h2>System Master</h2>
        <?php include_once("includes/header_right.php"); ?>
    </header>
    <div class="body_wrap">
        <div class="page_top_wrap mb-3">
            <div class="page_top_wrap_left">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item active" aria-current="page"><a href="#">System Master</a></li>
                    </ol>
                </nav>
                <h2>Manage System Master</h2>
                <h6>Manage tariff, dates, packages, and classifications.</h6>
            </div>
            <div class="page_top_wrap_right">
                <a href="#" id="saveChanges"  class="badge_success"><i class="fal fa-save"></i>Save Changes</a>
            </div>
        </div>

        <div class="com_info_wrap">
            <div class="com_info_left">
                <h6>System Menu</h6>
                <button data-tab="countdownsetup" class="com_info_left_click icon_hover badge_default active">Countdowns</button>
                <button data-tab="backgroundsetup" class="com_info_left_click icon_hover badge_default action-transparent">Images</button>
                <button data-tab="flyersetup" class="com_info_left_click icon_hover badge_default action-transparent">Flyers</button>
                <button data-tab="iconsetup" class="com_info_left_click icon_hover badge_default action-transparent">Icons</button>
                <button data-tab="facultysetup" class="com_info_left_click icon_hover badge_default action-transparent">Faculties</button>
                <button data-tab="keynotespeakers" class="com_info_left_click icon_hover badge_default action-transparent">Keynote Speakers</button>
                <button data-tab="textsetup" class="com_info_left_click icon_hover badge_default action-transparent">Info Setup</button>
            </div>
            <div class="com_info_right">
                <div class="com_info_box active" id="countdownsetup">
                    <div class="com_info_box_grid">
                        <div class="com_info_box_grid_box">
                            <h5 class="com_info_box_head">
                                <n><span class="text_primary"><?php clock() ?></span> Countdown</n>
                            </h5>
                            <div class="form_grid">
                                <div class="com_info_box_inner span_2">
                                    <h4 class="com_info_box_inner_sub_head"><span>Registration Countdown</span></h4>
                                    <div class="accm_add_box">
                                        <div class="frm_grp">
                                            <input type="date">
                                        </div>
                                        <a href="#" class="accm_delet icon_hover badge_primary action-transparent"><i class="fal fa-paper-plane"></i></a>
                                    </div>
                                    <ul class="countdown" id="registration_countdown">
                                        <li>
                                            <n><span id="days">00</span></n><i>days</i>
                                        </li>
                                        <hr>
                                        <li>
                                            <n><span id="hours">00</span></n><i>Hours</i>
                                        </li>
                                        <hr>
                                        <li>
                                            <n><span id="minutes">00</span></n><i>Minutes</i>
                                        </li>
                                        <hr>
                                        <li>
                                            <n><span id="seconds">00</span></n><i>Seconds</i>
                                        </li>
                                    </ul>
                                </div>
                                <div class="com_info_box_inner span_2">
                                    <h4 class="com_info_box_inner_sub_head"><span>Abstract Countdown</span></h4>
                                    <div class="accm_add_box">
                                        <div class="frm_grp">
                                            <input type="date">
                                        </div>
                                        <a href="#" class="accm_delet icon_hover badge_primary action-transparent"><i class="fal fa-paper-plane"></i></a>
                                    </div>
                                    <ul class="countdown" id="registration_countdown">
                                        <li>
                                            <n><span id="absdays">00</span></n><i>days</i>
                                        </li>
                                        <hr>
                                        <li>
                                            <n><span id="abshours">00</span></n><i>Hours</i>
                                        </li>
                                        <hr>
                                        <li>
                                            <n><span id="absminutes">00</span></n><i>Minutes</i>
                                        </li>
                                        <hr>
                                        <li>
                                            <n><span id="absseconds">00</span></n><i>Seconds</i>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="com_info_box" id="backgroundsetup">
                    <div class="com_info_box_grid">
                        <div class="com_info_box_grid_box">
                            <h5 class="com_info_box_head">
                                <n><span class="text_secondary"><?php branding() ?></span> Images</n>
                            </h5>
                            <div class="form_grid g_2">
                                <div class="com_info_box_inner">
                                    <h4 class="com_info_box_inner_sub_head"><span>Registration Background</span></h4>
                                    <div class="branding_image_preview">
                                        <img src="images/Banner KTC BG.png" alt="">
                                        <button><i class="fal fa-trash-alt"></i></button>
                                    </div>
                                    <div class="branding_image_upload">
                                        <input style="display: none;" id="webmaster_background" type="file">
                                        <label for="webmaster_background">
                                            <span><i class="fal fa-upload"></i></span>
                                            <n>Add Icon</n>
                                            <g>149x149px recommended</g>
                                        </label>
                                    </div>
                                </div>
                                <div class="com_info_box_inner">
                                    <h4 class="com_info_box_inner_sub_head"><span>Profile Background</span></h4>
                                    <div class="branding_image_preview">
                                        <img src="images/Banner KTC BG.png" alt="">
                                        <button><i class="fal fa-trash-alt"></i></button>
                                    </div>
                                    <div class="branding_image_upload">
                                        <input style="display: none;" id="webmaster_background" type="file">
                                        <label for="webmaster_background">
                                            <span><i class="fal fa-upload"></i></span>
                                            <n>Add Icon</n>
                                            <g>149x149px recommended</g>
                                        </label>
                                    </div>
                                </div>
                                <div class="com_info_box_inner">
                                    <h4 class="com_info_box_inner_sub_head"><span>Exibitor Background</span></h4>
                                    <div class="branding_image_preview">
                                        <img src="images/Banner KTC BG.png" alt="">
                                        <button><i class="fal fa-trash-alt"></i></button>
                                    </div>
                                    <div class="branding_image_upload">
                                        <input style="display: none;" id="webmaster_background" type="file">
                                        <label for="webmaster_background">
                                            <span><i class="fal fa-upload"></i></span>
                                            <n>Add Icon</n>
                                            <g>149x149px recommended</g>
                                        </label>
                                    </div>
                                </div>
                                <div class="com_info_box_inner">
                                    <h4 class="com_info_box_inner_sub_head"><span>Abstract Submission Success Image</span></h4>
                                    <div class="branding_image_preview">
                                        <img src="images/Banner KTC BG.png" alt="">
                                        <button><i class="fal fa-trash-alt"></i></button>
                                    </div>
                                    <div class="branding_image_upload">
                                        <input style="display: none;" id="webmaster_background" type="file">
                                        <label for="webmaster_background">
                                            <span><i class="fal fa-upload"></i></span>
                                            <n>Add Icon</n>
                                            <g>149x149px recommended</g>
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="com_info_box" id="flyersetup">
                    <div class="com_info_box_grid">
                        <div class="com_info_box_grid_box">
                            <h5 class="com_info_box_head">
                                <n><span class="text_success"><i class="fal fa-book-open"></i></span> Flyers</n>
                            </h5>
                            <div class="com_info_box_inner span_4">
                                <h4 class="com_info_box_inner_sub_head"><span>Manage Flyers</span><a class="add mi-1"><?php add() ?> Add</a></h4>
                                <div class="form_grid g_3">
                                    <div class="accm_add_box">
                                        <div class="branding_image_preview">
                                            <img src="images/Banner KTC BG.png" alt="">
                                            <button><i class="fal fa-trash-alt"></i></button>
                                        </div>
                                        <div class="branding_image_upload">
                                            <input style="display: none;" id="webmaster_background" type="file">
                                            <label for="webmaster_background">
                                                <span><i class="fal fa-upload"></i></span>
                                                <n>Add Flyer</n>
                                                <g>270x406px recommended</g>
                                            </label>
                                        </div>
                                        <div class="frm_grp mt-3">
                                            <p class="frm-head">Status</p>
                                            <div class="cus_check_wrap">
                                                <label class="cus_check gender_check">
                                                    <input type="radio" name="food">
                                                    <span class="checkmark">Active</span>
                                                </label>
                                                <label class="cus_check gender_check">
                                                    <input type="radio" name="food">
                                                    <span class="checkmark">Inactive</span>
                                                </label>
                                            </div>
                                        </div>
                                        <a href="#" class="accm_delet icon_hover badge_danger action-transparent"><?php delete() ?></a>
                                    </div>
                                    <div class="accm_add_box">
                                        <div class="branding_image_preview">
                                            <img src="images/Banner KTC BG.png" alt="">
                                            <button><i class="fal fa-trash-alt"></i></button>
                                        </div>
                                        <div class="branding_image_upload">
                                            <input style="display: none;" id="webmaster_background" type="file">
                                            <label for="webmaster_background">
                                                <span><i class="fal fa-upload"></i></span>
                                                <n>Add Flyer</n>
                                                <g>270x406px recommended</g>
                                            </label>
                                        </div>
                                        <div class="frm_grp mt-3">
                                            <p class="frm-head">Status</p>
                                            <div class="cus_check_wrap">
                                                <label class="cus_check gender_check">
                                                    <input type="radio" name="food">
                                                    <span class="checkmark">Active</span>
                                                </label>
                                                <label class="cus_check gender_check">
                                                    <input type="radio" name="food">
                                                    <span class="checkmark">Inactive</span>
                                                </label>
                                            </div>
                                        </div>
                                        <a href="#" class="accm_delet icon_hover badge_danger action-transparent"><?php delete() ?></a>
                                    </div>
                                    <div class="accm_add_box">
                                        <div class="branding_image_preview">
                                            <img src="images/Banner KTC BG.png" alt="">
                                            <button><i class="fal fa-trash-alt"></i></button>
                                        </div>
                                        <div class="branding_image_upload">
                                            <input style="display: none;" id="webmaster_background" type="file">
                                            <label for="webmaster_background">
                                                <span><i class="fal fa-upload"></i></span>
                                                <n>Add Flyer</n>
                                                <g>270x406px recommended</g>
                                            </label>
                                        </div>
                                        <div class="frm_grp mt-3">
                                            <p class="frm-head">Status</p>
                                            <div class="cus_check_wrap">
                                                <label class="cus_check gender_check">
                                                    <input type="radio" name="food">
                                                    <span class="checkmark">Active</span>
                                                </label>
                                                <label class="cus_check gender_check">
                                                    <input type="radio" name="food">
                                                    <span class="checkmark">Inactive</span>
                                                </label>
                                            </div>
                                        </div>
                                        <a href="#" class="accm_delet icon_hover badge_danger action-transparent"><?php delete() ?></a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="com_info_box" id="iconsetup">
                    <form name="frmtypeedit" method="post" action="<?= $cfg['SECTION_BASE_URL'] ?>manage_landing_page.process.php" id="frmtypeedit" enctype="multipart/form-data">
                        <input type="hidden" name="act" value="edit_side_icon" />
                     
                    <div class="com_info_box_grid">
                        <div class="com_info_box_grid_box">
                            <h5 class="com_info_box_head">
                                <n><span class="text_info"><i class="fal fa-icons-alt"></i></span> Icons</n>
                            </h5>
                            <div class="com_info_box_inner span_4">
                                <h4 class="com_info_box_inner_sub_head"><span>Landing Page Side Icon</span><a  id="add_side_icon" class="add mi-1"><?php add() ?> Add</a></h4>
                                <div class="form_grid g_3" id="side_icon_wrapper">
                                  <?php

                                        $i = 0;

                                        $sql 	=	array();
                                        $sql['QUERY'] = "SELECT * FROM " . _DB_ICON_SETTING_ . " 
                                                                WHERE `id`!='' AND `purpose`='Side Icon' AND status IN ('A', 'I') ORDER BY `id`";
                                        // $sql['PARAM'][]	=	array('FILD' => 'status' ,  'DATA' => 'A' , 'TYP' => 's');					 
                                        $result 	 = $mycms->sql_select($sql);
                                     if ($result) {
                                        foreach ($result as $index => $row):
                                            $uniqueId = 'icon_' . $row['id'];
                                            $icon_image = '../../' . $cfg['EMAIL.HEADER.FOOTER.IMAGE'] . $row['icon'];
                                    ?>
                                    <div class="accm_add_box side_icon_box">
                                        <input type="hidden" name="id[<?= $index ?>]" value="<?= $row['id'] ?>">

                                        <!-- Preview -->
                                        <div class="branding_image_preview" style="<?= !empty($row['icon']) ? 'display:block;' : 'display:none;' ?>">
                                            <img src="<?= $icon_image ?>" class="icon-preview-img">
                                            <button type="button" class="delete-icon-btn"><?php delete() ?></button>
                                        </div>

                                        <!-- Upload -->
                                        <div class="branding_image_upload" style="<?= empty($row['icon']) ? 'display:block;' : 'display:none;' ?>">
                                            <input type="file" id="<?= $uniqueId ?>" name="headerImage[<?= $index ?>]" class="icon-file-input" style="display:none;">
                                            <label for="<?= $uniqueId ?>">
                                                <span><i class="fal fa-upload"></i></span>
                                                <n>Add Icon</n>
                                                <g>60x60px recommended</g>
                                            </label>
                                        </div>

                                        <!-- Title / Page Link / Status -->
                                        <div class="form_grid mt-3">
                                            <div class="frm_grp span_4">
                                                <p class="frm-head">Title <i class="mandatory">*</i></p>
                                                <input type="text" name="title[<?= $index ?>]" value="<?= $row['title'] ?>">
                                            </div>
                                            <div class="frm_grp span_4">
                                                <p class="frm-head">Page Link <i class="mandatory">*</i></p>
                                                <input type="text" name="page_link[<?= $index ?>]" value="<?= $row['page_link'] ?>">
                                            </div>
                                            <div class="frm_grp span_4">
                                                <p class="frm-head">Status</p>
                                                <div class="cus_check_wrap">
                                                    <label class="cus_check gender_check">
                                                        <input type="radio" name="status[<?= $index ?>]" value="A" <?= ($row['status']=='A')?'checked':'' ?>>
                                                        <span class="checkmark">Active</span>
                                                    </label>
                                                    <label class="cus_check gender_check">
                                                        <input type="radio" name="status[<?= $index ?>]" value="I" <?= ($row['status']=='I')?'checked':'' ?>>
                                                        <span class="checkmark">Inactive</span>
                                                    </label>
                                                </div>
                                            </div>
                                        </div>

                                        <a href="#" class="accm_delet icon_hover badge_danger action-transparent iconremove" data-id="<?= $row['id'] ?>"><?php delete() ?></a>
                                    </div>
                                    <?php endforeach; } ?>
                                </div>
                            </div>
                        </div>
                        <div class="com_info_box_grid_box">
                            <div class="com_info_box_inner span_4">
                                <h4 class="com_info_box_inner_sub_head"><span>Footer Icon</span><a class="add mi-1"><?php add() ?> Add</a></h4>
                                <div class="form_grid g_3">
                                    <div class="accm_add_box">
                                        <div class="branding_image_preview">
                                            <img src="images/Banner KTC BG.png" alt="">
                                            <button><i class="fal fa-trash-alt"></i></button>
                                        </div>
                                        <div class="branding_image_upload">
                                            <input style="display: none;" id="webmaster_background" type="file">
                                            <label for="webmaster_background">
                                                <span><i class="fal fa-upload"></i></span>
                                                <n>Add Icon</n>
                                                <g>149x149px recommended</g>
                                            </label>
                                        </div>
                                        <div class="form_grid mt-3">
                                            <div class="frm_grp span_4">
                                                <p class="frm-head">Title <i class="mandatory">*</i></p>
                                                <input>
                                            </div>
                                            <div class="frm_grp span_4">
                                                <p class="frm-head">Link <i class="mandatory">*</i></p>
                                                <input>
                                            </div>
                                            <div class="frm_grp span_4">
                                                <p class="frm-head">Status</p>
                                                <div class="cus_check_wrap">
                                                    <label class="cus_check gender_check">
                                                        <input type="radio" name="food">
                                                        <span class="checkmark">Active</span>
                                                    </label>
                                                    <label class="cus_check gender_check">
                                                        <input type="radio" name="food">
                                                        <span class="checkmark">Inactive</span>
                                                    </label>
                                                </div>
                                            </div>
                                        </div>
                                        <a href="#" class="accm_delet icon_hover badge_danger action-transparent"><?php delete() ?></a>
                                    </div>
                                    <div class="accm_add_box">
                                        <div class="branding_image_preview">
                                            <img src="images/Banner KTC BG.png" alt="">
                                            <button><i class="fal fa-trash-alt"></i></button>
                                        </div>
                                        <div class="branding_image_upload">
                                            <input style="display: none;" id="webmaster_background" type="file">
                                            <label for="webmaster_background">
                                                <span><i class="fal fa-upload"></i></span>
                                                <n>Add Icon</n>
                                                <g>149x149px recommended</g>
                                            </label>
                                        </div>
                                        <div class="form_grid mt-3">
                                            <div class="frm_grp span_4">
                                                <p class="frm-head">Title <i class="mandatory">*</i></p>
                                                <input>
                                            </div>
                                            <div class="frm_grp span_4">
                                                <p class="frm-head">Link <i class="mandatory">*</i></p>
                                                <input>
                                            </div>
                                            <div class="frm_grp span_4">
                                                <p class="frm-head">Status</p>
                                                <div class="cus_check_wrap">
                                                    <label class="cus_check gender_check">
                                                        <input type="radio" name="food">
                                                        <span class="checkmark">Active</span>
                                                    </label>
                                                    <label class="cus_check gender_check">
                                                        <input type="radio" name="food">
                                                        <span class="checkmark">Inactive</span>
                                                    </label>
                                                </div>
                                            </div>
                                        </div>
                                        <a href="#" class="accm_delet icon_hover badge_danger action-transparent"><?php delete() ?></a>
                                    </div>
                                    <div class="accm_add_box">
                                        <div class="branding_image_preview">
                                            <img src="images/Banner KTC BG.png" alt="">
                                            <button><i class="fal fa-trash-alt"></i></button>
                                        </div>
                                        <div class="branding_image_upload">
                                            <input style="display: none;" id="webmaster_background" type="file">
                                            <label for="webmaster_background">
                                                <span><i class="fal fa-upload"></i></span>
                                                <n>Add Icon</n>
                                                <g>149x149px recommended</g>
                                            </label>
                                        </div>
                                        <div class="form_grid mt-3">
                                            <div class="frm_grp span_4">
                                                <p class="frm-head">Title <i class="mandatory">*</i></p>
                                                <input>
                                            </div>
                                            <div class="frm_grp span_4">
                                                <p class="frm-head">Link <i class="mandatory">*</i></p>
                                                <input>
                                            </div>
                                            <div class="frm_grp span_4">
                                                <p class="frm-head">Status</p>
                                                <div class="cus_check_wrap">
                                                    <label class="cus_check gender_check">
                                                        <input type="radio" name="food">
                                                        <span class="checkmark">Active</span>
                                                    </label>
                                                    <label class="cus_check gender_check">
                                                        <input type="radio" name="food">
                                                        <span class="checkmark">Inactive</span>
                                                    </label>
                                                </div>
                                            </div>
                                        </div>
                                        <a href="#" class="accm_delet icon_hover badge_danger action-transparent"><?php delete() ?></a>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="com_info_box_grid_box">
                            <div class="com_info_box_inner span_4">
                                <h4 class="com_info_box_inner_sub_head"><span>Side Icon</span><a class="add mi-1"><?php add() ?> Add</a></h4>
                                <div class="form_grid g_3">
                                    <div class="accm_add_box">
                                        <div class="branding_image_preview">
                                            <img src="images/Banner KTC BG.png" alt="">
                                            <button><i class="fal fa-trash-alt"></i></button>
                                        </div>
                                        <div class="branding_image_upload">
                                            <input style="display: none;" id="webmaster_background" type="file">
                                            <label for="webmaster_background">
                                                <span><i class="fal fa-upload"></i></span>
                                                <n>Add Icon</n>
                                                <g>149x149px recommended</g>
                                            </label>
                                        </div>
                                        <div class="form_grid mt-3">
                                            <div class="frm_grp span_4">
                                                <p class="frm-head">Title <i class="mandatory">*</i></p>
                                                <input>
                                            </div>

                                        </div>
                                        <a href="#" class="accm_delet icon_hover badge_danger action-transparent"><?php delete() ?></a>
                                    </div>
                                    <div class="accm_add_box">
                                        <div class="branding_image_preview">
                                            <img src="images/Banner KTC BG.png" alt="">
                                            <button><i class="fal fa-trash-alt"></i></button>
                                        </div>
                                        <div class="branding_image_upload">
                                            <input style="display: none;" id="webmaster_background" type="file">
                                            <label for="webmaster_background">
                                                <span><i class="fal fa-upload"></i></span>
                                                <n>Add Icon</n>
                                                <g>149x149px recommended</g>
                                            </label>
                                        </div>
                                        <div class="form_grid mt-3">
                                            <div class="frm_grp span_4">
                                                <p class="frm-head">Title <i class="mandatory">*</i></p>
                                                <input>
                                            </div>
                                        </div>
                                        <a href="#" class="accm_delet icon_hover badge_danger action-transparent"><?php delete() ?></a>
                                    </div>
                                    <div class="accm_add_box">
                                        <div class="branding_image_preview">
                                            <img src="images/Banner KTC BG.png" alt="">
                                            <button><i class="fal fa-trash-alt"></i></button>
                                        </div>
                                        <div class="branding_image_upload">
                                            <input style="display: none;" id="webmaster_background" type="file">
                                            <label for="webmaster_background">
                                                <span><i class="fal fa-upload"></i></span>
                                                <n>Add Icon</n>
                                                <g>149x149px recommended</g>
                                            </label>
                                        </div>
                                        <div class="form_grid mt-3">
                                            <div class="frm_grp span_4">
                                                <p class="frm-head">Title <i class="mandatory">*</i></p>
                                                <input>
                                            </div>
                                        </div>
                                        <a href="#" class="accm_delet icon_hover badge_danger action-transparent"><?php delete() ?></a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                  </form>
                </div>
                <div class="com_info_box" id="facultysetup">
                    <form id="facultyForm" enctype="multipart/form-data" method="post" autocomplete="off" action="<?= _BASE_URL_ ?>webmaster/information-set-up-process.php">
                       <input type="hidden" name="act" value="saveProfile">
                        <div class="com_info_box_grid">
                            <div class="com_info_box_grid_box">
                                <h5 class="com_info_box_head">
                                    <n><span class="text_danger"><?php user() ?></span> Faculties</n>
                                    <a class="add mi-1"  id="add_faculty"><?php add() ?> Add</a>
                                </h5>
                                <div class="com_info_box_inner">
                                    <div class="form_grid g_2"  id="faculty_wrapper">
                                    
                                        <?php
                                            $sql_cal			=	array();
                                            $sql_cal['QUERY']	=	"SELECT * 
                                                                        FROM `rcg_keynote_highlight_speaker`
                                                                        WHERE `status` 	= 		'A'
                                                                        ORDER BY `id` ASC";


                                            $res_cal = $mycms->sql_select($sql_cal);
                                           
                                         	foreach ($res_cal as $key => $rows) {
                                            $icon_image =$cfg['EMAIL.HEADER.FOOTER.IMAGE'] . $rows['profileImage'];
                                            //  print_r($icon_image);
                                        ?>
                                     <div class="accm_add_box faculty_box">

                                        <!-- IMPORTANT hidden ID -->
                                        <input type="hidden" name="speaker_id[]" value="<?= $rows['id'] ?>">

                                        <div class="form_grid g_6">

                                            <div class="frm_grp span_1 iconBox_editclss">
                                                <p class="frm-head">Image</p>

                                                <input type="file" class="icon_input_clss" name="icon[]" hidden>

                                                <label class="frm-image uploadIcon_class"  style="<?= !empty($rows['profileImage']) ? 'display:none;' : 'display:block;' ?>">
                                                    <?php upload() ?>
                                                </label>

                                                <div class="frm_img_preview" 
                                                    style="<?= !empty($rows['profileImage']) ? 'display:block;' : 'display:none;' ?>">

                                                    <img class="iconPreview_clss"
                                                        src="<?= _BASE_URL_ ?><?= $icon_image ?>"
                                                        data-default="<?= _BASE_URL_ ?><?= $icon_image ?>">

                                                    <button type="button" class="removeIcon_clss">
                                                        <?php delete() ?>
                                                    </button>
                                                </div>
                                            </div>

                                            <div class="frm_grp span_4">
                                                <p class="frm-head">Speaker Name *</p>
                                                <input required name="speaker_name[]" value="<?= htmlspecialchars($rows['speaker_name']) ?>">
                                            </div>
                                           <div class="frm_grp span_1">
                                                <p class="frm-head" >Hall *</p>
                                                <input  name="hall[]" required value="<?= htmlspecialchars($rows['hall']) ?>">
                                            </div>
                                            <div class="frm_grp span_3">
                                                <p class="frm-head">Designation</p>
                                                <input name="designation[]" value="<?= htmlspecialchars($rows['designation']) ?>">
                                            </div>
                                             <div class="frm_grp span_3">
                                                <p class="frm-head" >Conference Datetime</i></p>
                                                <input type="datetime-local" name="confDateTime[]"  value="<?=$rows['confDateTime'] ?>">
                                            </div>
                                            <div class="frm_grp span_6">
                                                <p class="frm-head">Work For</p>
                                                <input name="workInfo[]" value="<?= htmlspecialchars($rows['workInfo']) ?>">
                                            </div>

                                            <div class="frm_grp span_6">
                                                <p class="frm-head">Tag Line</p>
                                                <input name="tagLine[]" value="<?= htmlspecialchars($rows['tagLine']) ?>">
                                            </div>

                                        </div>

                                       <a href="#" class="accm_delet removeFaculty badge_danger" data-id="<?= $rows['id'] ?>">
                                            <?php delete() ?>
                                        </a>
                                    </div>
                                        <?php
                                            }
                                            ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                   </form>
                </div>
                <div class="com_info_box" id="keynotespeakers">
                    <div class="com_info_box_grid">
                        <div class="com_info_box_grid_box">
                            <h5 class="com_info_box_head">
                                <n><span class="text_danger">Keynote Speakers</n>
                                <?
                                     $sqlMSG   =  array();
                                    $sqlMSG['QUERY']    = "SELECT * FROM " . _DB_COMPANY_INFORMATION_ . " 
                                                WHERE `id` = 1";
                                    $result       = $mycms->sql_select($sqlMSG);
                                ?>
                                <label class="toggleswitch">
                                    <input 
                                        class="toggleswitch-checkbox" 
                                        type="checkbox" 
                                        data-id="1" <?= $result[0]['speakerStatus'] === 'A' ? 'checked' : '' ?>
                                        data-action="toggleMainStatus"
                                    >
                                    <div class="toggleswitch-switch"></div>
                                </label>
                            </h5>
                           <?php
                                $sql_cal			=	array();
                                $sql_cal['QUERY']	=	"SELECT * 
                                                            FROM `rcg_keynote_highlight_speaker`
                                                            WHERE `status` 	!= 		'D'
                                                            ORDER BY `id` ASC";


                                $res_cal = $mycms->sql_select($sql_cal);
                                
                                foreach ($res_cal as $key => $rows) {
                                $icon_image =$cfg['EMAIL.HEADER.FOOTER.IMAGE'] . $rows['profileImage'];
                                //  print_r($icon_image);
                            ?>
                            <div class="spot_box">
                                <div class="spot_box_top align-items-center">
                                  
                                    <div class="spot_name d-flex align-items-center">
                                      
                                            <? 
                                            if(!empty($rows['profileImage'])){
                                               ?>
                                            <div class="regi_img_circle">
                                               <img src="<?= _BASE_URL_ ?><?= $icon_image ?>" alt="" class="w-100 h-100">
                                             </div>
                                           <?php
                                            } else{
                                                 $nameParts = explode(' ', trim($rows['speaker_name']));
                                                $initials = '';
                                                if (count($nameParts) >= 2) {
                                                    $initials = strtoupper($nameParts[0][0] . $nameParts[1][0]);
                                                } elseif (count($nameParts) === 1) {
                                                    $initials = strtoupper($nameParts[0][0]);
                                                }
                                                ?>
                                                <div class="regi_img_circle">
                                               <span><?= $initials ?></span>
                                               </div>
                                                <?
                                            }
                                           
                                            ?>
                                      
                                        <div>
                                            <div class="regi_name"><?=$rows['speaker_name']?></div>
                                        </div>
                                    </div>
                                    
                                    <div class="spot_details">
                                       <div class="spot_details_box align-items-end">
                                            <label class="toggleswitch">
                                                <input 
                                                    class="toggleswitch-checkbox" 
                                                    type="checkbox" 
                                                    data-id="<?= $rows['id'] ?>" 
                                                    <?= $rows['status'] === 'A' ? 'checked' : '' ?>
                                                >
                                                <div class="toggleswitch-switch"></div>
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <?php
                                }
                                ?>
                        </div>

                    </div>
                </div>
                <div class="com_info_box" id="textsetup">
                    <div class="com_info_box_grid">
                        <div class="com_info_box_grid_box">
                            <h5 class="com_info_box_head">
                                <n><span class="text_danger"><?php invoive() ?></span> Info Setup</n>
                            </h5>
                            <div class="com_info_box_inner span_4">
                                <h4 class="com_info_box_inner_sub_head"><span>Footer Text</span><a class="add mi-1"><?php add() ?> Add</a></h4>
                                <div class="form_grid g_3">
                                    <div class="accm_add_box">
                                        <div class="form_grid">
                                            <div class="frm_grp span_4">
                                                <p class="frm-head">Test <i class="mandatory">*</i></p>
                                                <input>
                                            </div>
                                            <div class="frm_grp span_4">
                                                <p class="frm-head">Status</p>
                                                <div class="cus_check_wrap">
                                                    <label class="cus_check gender_check">
                                                        <input type="radio" name="food">
                                                        <span class="checkmark">Active</span>
                                                    </label>
                                                    <label class="cus_check gender_check">
                                                        <input type="radio" name="food">
                                                        <span class="checkmark">Inactive</span>
                                                    </label>
                                                </div>
                                            </div>
                                        </div>
                                        <a href="#" class="accm_delet icon_hover badge_danger action-transparent"><?php delete() ?></a>
                                    </div>
                                    <div class="accm_add_box">
                                        <div class="form_grid">
                                            <div class="frm_grp span_4">
                                                <p class="frm-head">Test <i class="mandatory">*</i></p>
                                                <input>
                                            </div>
                                            <div class="frm_grp span_4">
                                                <p class="frm-head">Status</p>
                                                <div class="cus_check_wrap">
                                                    <label class="cus_check gender_check">
                                                        <input type="radio" name="food">
                                                        <span class="checkmark">Active</span>
                                                    </label>
                                                    <label class="cus_check gender_check">
                                                        <input type="radio" name="food">
                                                        <span class="checkmark">Inactive</span>
                                                    </label>
                                                </div>
                                            </div>
                                        </div>
                                        <a href="#" class="accm_delet icon_hover badge_danger action-transparent"><?php delete() ?></a>
                                    </div>
                                    <div class="accm_add_box">
                                        <div class="form_grid">
                                            <div class="frm_grp span_4">
                                                <p class="frm-head">Test <i class="mandatory">*</i></p>
                                                <input>
                                            </div>
                                            <div class="frm_grp span_4">
                                                <p class="frm-head">Status</p>
                                                <div class="cus_check_wrap">
                                                    <label class="cus_check gender_check">
                                                        <input type="radio" name="food">
                                                        <span class="checkmark">Active</span>
                                                    </label>
                                                    <label class="cus_check gender_check">
                                                        <input type="radio" name="food">
                                                        <span class="checkmark">Inactive</span>
                                                    </label>
                                                </div>
                                            </div>
                                        </div>
                                        <a href="#" class="accm_delet icon_hover badge_danger action-transparent"><?php delete() ?></a>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="com_info_box_grid_box">
                            <div class="com_info_box_inner span_4">
                                <h4 class="com_info_box_inner_sub_head"><span>Google Map Link</span><a class="add mi-1"></h4>
                                <div class="frm_grp span_">
                                    <input>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php include_once("includes/popup.php"); ?>
</body>
<?php include_once("includes/js-source.php"); ?>
<script>
    $('.com_info_left_click').click(function() {
        var tabId = $(this).attr('data-tab');
        $(".com_info_box").removeClass("active");
        $(".com_info_left_click").removeClass("active").addClass('action-transparent');
        $('#' + tabId).addClass("active");
        $(this).addClass("active").removeClass('action-transparent');
    });
    $('.com_info_box_content_sec_left_click').click(function() {
        var tabId = $(this).attr('data-tab');
        $(".com_info_box_content_sec_right_box").removeClass("active");
        $(".com_info_box_content_sec_left_click").removeClass("active").addClass('action-transparent');
        $('#' + tabId).addClass("active");
        $(this).addClass("active").removeClass('action-transparent');
    });
    (function() {
        const second = 1000,
            minute = second * 60,
            hour = minute * 60,
            day = hour * 24;

        //I'm adding this section so I don't have to keep updating this pen every year :-)
        //remove this if you don't need it
        let today = new Date(),
            dd = String(today.getDate()).padStart(2, "0"),
            mm = String(today.getMonth() + 1).padStart(2, "0"),
            yyyy = 2026,
            nextYear = yyyy,
            dayMonth = "04/16/",
            birthday = dayMonth + yyyy;

        today = mm + "/" + dd + "/" + yyyy;
        if (today > birthday) {
            birthday = dayMonth + nextYear;
        }
        //end

        const countDown = new Date(birthday).getTime(),
            x = setInterval(function() {

                const now = new Date().getTime(),
                    distance = countDown - now;

                document.getElementById("days").innerText = Math.floor(distance / (day)),
                    document.getElementById("hours").innerText = Math.floor((distance % (day)) / (hour)),
                    document.getElementById("minutes").innerText = Math.floor((distance % (hour)) / (minute)),
                    document.getElementById("seconds").innerText = Math.floor((distance % (minute)) / second);
                if (distance < 0) {

                    $("#registration_countdown").hide(),
                        $("#registration_closed").show(),
                        $(".register").hide(),
                        clearInterval(x);
                }
            }, 0)
    }());
    (function() {
        const abssecond = 1000,
            absminute = abssecond * 60,
            abshour = absminute * 60,
            absday = abshour * 24;

        //I'm adding this section so I don't have to keep updating this pen every year :-)
        //remove this if you don't need it
        let abstoday = new Date(),
            absdd = String(abstoday.getDate()).padStart(2, "0"),
            absmm = String(abstoday.getMonth() + 1).padStart(2, "0"),
            absyyyy = 2026,
            absnextYear = absyyyy,
            absdayMonth = "07/14/",
            absbirthday = absdayMonth + absyyyy;

        abstoday = absmm + "/" + absdd + "/" + absyyyy;
        if (abstoday > absbirthday) {
            absbirthday = absdayMonth + absnextYear;
        }
        //end

        const abscountDown = new Date(absbirthday).getTime(),
            absx = setInterval(function() {

                const absnow = new Date().getTime(),
                    absdistance = abscountDown - absnow;

                document.getElementById("absdays").innerText = Math.floor(absdistance / (absday)),
                    document.getElementById("abshours").innerText = Math.floor((absdistance % (absday)) / (abshour)),
                    document.getElementById("absminutes").innerText = Math.floor((absdistance % (abshour)) / (absminute)),
                    document.getElementById("absseconds").innerText = Math.floor((absdistance % (absminute)) / abssecond);
                if (absdistance < 0) {
                    $("#abstract_end").innerText = "Submission Closed";
                    $("#abstract_countdown").hide(),
                        $("#abstract_end").show(),
                        $(".abs_sub").hide(),
                        clearInterval(x);
                }
            }, 0)
    }());
</script>
<script>
    $('#saveChanges').click(function () {

        // Get active tab ID
        var activeTabId = $('.com_info_box.active').attr('id');

        if (!activeTabId) {
            alert("No active tab found.");
            return;
        }

        // Find form inside active tab
        var activeForm = $('#' + activeTabId).find('form');

        if (activeForm.length) {
            activeForm.submit();
        } else {
            alert("No form found inside this tab.");
        }
    });
    $(document).ready(function() {
            // Check if URL has hash
            var hash = window.location.hash.substring(1); // removes '#'
            if(hash) {
                // Trigger the tab click for that hash
                var btn = $('.com_info_left_click[data-tab="' + hash + '"]');
                if(btn.length) {
                    btn.trigger('click');
                }
            }
        });
      ////////////////facultysetup form start//////////
   document.addEventListener('DOMContentLoaded', function () {
    const facultyWrapper = document.getElementById('faculty_wrapper');
    const addBtn = document.getElementById('add_faculty');

    addBtn.addEventListener('click', function (e) {
        e.preventDefault();

        // Create a fresh new box from scratch (not cloning hidden template)
        const newBox = document.createElement('div');
        newBox.classList.add('accm_add_box', 'faculty_box');
        newBox.innerHTML = `
            <input type="hidden" name="speaker_id[]">
            <div class="form_grid g_6">
                <div class="frm_grp span_1 iconBox_editclss">
                    <p class="frm-head">Image</p>
                    <input type="file" class="icon_input_clss" name="icon[]" hidden>
                    <label class="frm-image uploadIcon_class"><?php upload() ?></label>
                    <div class="frm_img_preview" style="display:none;">
                        <img class="iconPreview_clss" src="">
                        <button type="button" class="removeIcon_clss"><?php delete() ?></button>
                    </div>
                </div>
                <div class="frm_grp span_4">
                    <p class="frm-head">Speaker Name *</p>
                    <input name="speaker_name[]" required>
                </div>
                <div class="frm_grp span_1">
                    <p class="frm-head" >Hall *</i></p>
                    <input  name="hall[]" required>
                </div>
                <div class="frm_grp span_3">
                    <p class="frm-head">Designation</p>
                    <input name="designation[]">
                </div>
                 <div class="frm_grp span_3">
                    <p class="frm-head" >Conference Datetime</i></p>
                    <input type="datetime-local" name="confDateTime[]">
                </div>
                <div class="frm_grp span_6">
                    <p class="frm-head">Work For</p>
                    <input name="workInfo[]">
                </div>
                <div class="frm_grp span_6">
                    <p class="frm-head">Tag Line</p>
                    <input name="tagLine[]">
                </div>
            </div>
            <a href="#" class="accm_delet removeFaculty badge_danger"><?php delete() ?></a>
        `;

        // Add file input change listener for image preview
        const fileInput = newBox.querySelector('.icon_input_clss');
        fileInput.addEventListener('change', function () {
            const previewImg = newBox.querySelector('.iconPreview_clss');
            const previewBox = newBox.querySelector('.frm_img_preview');
            const uploadBtn = newBox.querySelector('.uploadIcon_class');
            if (this.files && this.files[0]) {
                const reader = new FileReader();
                reader.onload = function (e) {
                    previewImg.src = e.target.result;
                    previewBox.style.display = 'block';
                    uploadBtn.style.display = 'none';
                }
                reader.readAsDataURL(this.files[0]);
            }
        });

        facultyWrapper.appendChild(newBox);
    });

    // Delete faculty (DB or new)
    document.addEventListener('click', function (e) {
    const btn = e.target.closest('.removeFaculty');
        if (!btn) return;
        e.preventDefault();

        const facultyBox = btn.closest('.faculty_box');
        if (!facultyBox) return;

        const facultyId = btn.dataset.id;

        if (facultyId && facultyId !== '') {
            if (!confirm("Are you sure you want to delete this faculty?")) return;

            fetch('information-set-up-process.php', {
                method: 'POST',
                headers: {'Content-Type': 'application/x-www-form-urlencoded'},
                body: 'act=deleteFaculty&id=' + encodeURIComponent(facultyId)
            })
            .then(res => res.text())
            .then(rawData => {
                // Extract only 'success' from the response, ignore HTML
                const match = rawData.match(/success/); // finds first 'success'
                if (match) {
                    alert('Faculty deleted successfully!');
                    facultyBox.remove();
                }
            })
            .catch(err => {
                console.error(err);
                alert('An error occurred while deleting.');
            });

        } else {
            // Unsaved/new box: just remove
            facultyBox.remove();
        }
    });
   
    document.addEventListener('change', function (e) {
    const toggle = e.target.closest('.toggleswitch-checkbox'); // find the changed toggle
    if (!toggle) return; // ignore unrelated changes

    const itemId = toggle.dataset.id;
    const action = toggle.dataset.action || 'toggleSpeakerStatus'; // default action
    const isActive = toggle.checked ? 'A' : 'I';

    fetch('information-set-up-process.php', {
        method: 'POST',
        headers: {'Content-Type': 'application/x-www-form-urlencoded'},
        body: 'act=' + encodeURIComponent(action) + 
              '&id=' + encodeURIComponent(itemId) + 
              '&status=' + encodeURIComponent(isActive)
    })
    .then(res => res.text())
    .then(rawData => {
        const success = rawData.includes('success');

        if (success) {
            // Optional: different messages based on action
             window.location.hash = 'keynotespeakers';
            if (action === 'toggleMainStatus') {
                alert('Keynote Speaker status updated successfully!');
            } else {
                alert('Speaker status updated successfully!');
                const facultyBox = toggle.closest('.faculty_box');
                if (facultyBox) facultyBox.remove(); // remove box if needed
            }
        } else {
            alert('Failed to update status. Reverting toggle.');
            toggle.checked = !toggle.checked; // revert on failure
            console.error('API response:', rawData);
        }
    })
    .catch(err => {
        console.error(err);
        alert('An error occurred. Reverting toggle.');
        toggle.checked = !toggle.checked; // revert on error
    });
});
    // Image preview for existing boxes
    document.querySelectorAll('.icon_input_clss').forEach(input => {
        input.addEventListener('change', function () {
            const box = this.closest('.iconBox_editclss');
            const previewImg = box.querySelector('.iconPreview_clss');
            const previewBox = box.querySelector('.frm_img_preview');
            const uploadBtn = box.querySelector('.uploadIcon_class');
            if (this.files && this.files[0]) {
                const reader = new FileReader();
                reader.onload = function (e) {
                    previewImg.src = e.target.result;
                    previewBox.style.display = 'block';
                    uploadBtn.style.display = 'none';
                }
                reader.readAsDataURL(this.files[0]);
            }
        });
    });
});
     /////////////////faucalty form end///////////////
     ///////////////Icon start///////////////
document.addEventListener('DOMContentLoaded', function () {
  const addBtn = document.getElementById('add_side_icon');
    const iconWrapper = document.getElementById('side_icon_wrapper');

    addBtn.addEventListener('click', function(e) {
        e.preventDefault();

        const index = document.querySelectorAll('.side_icon_box').length;
        const uniqueId = 'icon_file_' + Date.now();

        const newBox = document.createElement('div');
        newBox.classList.add('accm_add_box', 'side_icon_box');

        newBox.innerHTML = `
            <input type="hidden" name="id[${index}]" value="">

            <div class="branding_image_preview" style="display:none;">
                <img src="" class="icon-preview-img">
                <button type="button" class="delete-icon-btn">Delete</button>
            </div>

            <div class="branding_image_upload" style="display:block;">
                <input type="file" id="${uniqueId}" name="headerImage[${index}]" class="icon-file-input" style="display:none;">
                <label for="${uniqueId}">
                    <span><i class="fal fa-upload"></i></span>
                    <n>Add Icon</n>
                    <g>60x60px recommended</g>
                </label>
            </div>

            <div class="form_grid mt-3">
                <div class="frm_grp span_4">
                    <p class="frm-head">Title <i class="mandatory">*</i></p>
                    <input type="text" name="title[${index}]">
                </div>
                <div class="frm_grp span_4">
                    <p class="frm-head">Page Link <i class="mandatory">*</i></p>
                    <input type="text" name="page_link[${index}]">
                </div>
                <div class="frm_grp span_4">
                    <p class="frm-head">Status</p>
                    <div class="cus_check_wrap">
                        <label class="cus_check gender_check">
                            <input type="radio" name="status[${index}]" value="A" checked>
                            <span class="checkmark">Active</span>
                        </label>
                        <label class="cus_check gender_check">
                            <input type="radio" name="status[${index}]" value="I">
                            <span class="checkmark">Inactive</span>
                        </label>
                    </div>
                </div>
            </div>

            <a href="#" class="accm_delet icon_hover badge_danger action-transparent iconremove"><? delete() ?></a>
        `;

        iconWrapper.appendChild(newBox);
    });


    // Delete faculty (DB or new)
    document.addEventListener('click', function (e) {
    const btn = e.target.closest('.iconremove');
        if (!btn) return;
        e.preventDefault();

        const IconBox = btn.closest('.side_icon_box');
        if (!IconBox) return;

        const IconId = btn.dataset.id;

        if (IconId && IconId !== '') {
            if (!confirm("Are you sure you want to delete this Icon?")) return;

            fetch('manage_landing_page.process.php', {
                method: 'POST',
                headers: {'Content-Type': 'application/x-www-form-urlencoded'},
                body: 'act=Remove&id=' + encodeURIComponent(IconId)
            })
            .then(res => res.text())
            .then(rawData => {
                // Extract only 'success' from the response, ignore HTML
                const match = rawData.match(/success/); // finds first 'success'
                if (match) {
                    alert('Icon deleted successfully!');
                    IconBox.remove();
                }
            })
            .catch(err => {
                console.error(err);
                alert('An error occurred while deleting.');
            });

        } else {
            // Unsaved/new box: just remove
            IconBox.remove();
        }
    });
     // Preview + Delete functionality
    document.addEventListener('change', function(e) {
        if (!e.target.classList.contains('icon-file-input')) return;

        const box = e.target.closest('.side_icon_box');
        const previewBox = box.querySelector('.branding_image_preview');
        const uploadBox = box.querySelector('.branding_image_upload');
        const previewImg = previewBox.querySelector('img');

        if (e.target.files && e.target.files[0]) {
            const reader = new FileReader();
            reader.onload = function(event) {
                previewImg.src = event.target.result;
                previewBox.style.display = 'block';
                uploadBox.style.display = 'none';
            };
            reader.readAsDataURL(e.target.files[0]);
        }
    });

    document.addEventListener('click', function(e) {
        const deleteBtn = e.target.closest('.delete-icon-btn');
        if (!deleteBtn) return;

        const box = deleteBtn.closest('.side_icon_box');
        const previewBox = box.querySelector('.branding_image_preview');
        const uploadBox = box.querySelector('.branding_image_upload');
        const fileInput = box.querySelector('.icon-file-input');
        const previewImg = previewBox.querySelector('img');

        fileInput.value = '';
        previewImg.src = '';
        previewBox.style.display = 'none';
        uploadBox.style.display = 'block';
    });

});
     //////////////icon end/////////////////
</script>
</html>
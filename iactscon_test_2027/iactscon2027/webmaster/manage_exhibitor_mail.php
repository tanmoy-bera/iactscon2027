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
?>

<body>
    <?php include_once("includes/left-menu.php"); ?>
    <header>
        <h2>Exhibitor Mail</h2>
        <?php include_once("includes/header_right.php"); ?>
    </header>
    <script language="javascript" src="<?= _BASE_URL_ ?>JS/jquery.js"></script>
    <script src="https://cdn.ckeditor.com/4.20.1/standard/ckeditor.js"></script>
    <script language="javascript" src="<?= $cfg['SECTION_BASE_URL'] ?>scripts/manage_country.js"></script>
    <script language="javascript" src="<?=_BASE_URL_?>js/adminPanel/architecture.js"></script>
    <div class="body_wrap">
        <div class="page_top_wrap mb-3">
            <div class="page_top_wrap_left">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                         <li class="breadcrumb-item"><a href="#">Master Data</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Manage Exhibitor Mail</li>
                    </ol>
                </nav>
                <h2>Manage Exhibitor Mail</h2>
                <h6>Manage mail titles, and subjects.</h6>
            </div>
        </div>
        <div class="com_info_box_grid">
            <div class="com_info_box_inner">
                <h5 class="com_info_box_inner_sub_head">
                    <span>Mail Setting Listing</span>
                    <a class="add mi-1 popup-btn"  data-tab="addmailsettinglisting"><?php add() ?>Add Template</a>
                </h5>
                <div class="table_wrap">
                    <table>
                        <thead>
                            <tr>
                                <th class="check_select">
                                    <label class="cus_check category_check workshop_check">
                                        <input name="check_all" id="check_all" targetElement="checkBoxListing"
									type="checkbox" onclick="checkall(this);">
                                        <span class="checkmark"></span>
                                    </label>
                                </th>
                                <th class="sl">#</th>
                                <th>Title</th>
                                <th>Mail Subject</th>
                                <th class="action">Status</th>
                                <th class="action">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php

                            $i = 0;

                            $sql 	=	array();
                            $sql['QUERY'] = "SELECT * FROM " . _DB_EMAIL_TEMPLATE_ . " 
                                                        WHERE `id` IN (7,8) ORDER BY id";
                            //$sql['PARAM'][]	=	array('FILD' => 'status' ,     		 'DATA' => 'A' ,       	           'TYP' => 's');					 
                            $result 	 = $mycms->sql_select($sql);
                            if ($result) {
                                foreach ($result as $i => $row) {
                                    @$i++;

                                    $header_image = '../../' . $cfg['EMAIL.HEADER.FOOTER.IMAGE'] . $row['header_image'];
                                    $footer_image = '../../' . $cfg['EMAIL.HEADER.FOOTER.IMAGE'] . $row['footer_image'];
                            ?>
                            <tr>
                                <td class="check_select">
                                    <label class="cus_check category_check workshop_check">
                                        <input type="checkbox" name="checkvalue" id="checkvalue" forType="checkBoxListing" value="<?= $row['id'] ?>">
                                        <span class="checkmark"></span>
                                    </label>
                                </td>
                                <td class="sl"><?= $i + ($_REQUEST['_pgn_'] * 50) ?></td>
                                <td><?php echo $row['title']; ?></td>
                                <td><?php echo $row['subject']; ?></td>
                                <td>
                                    <div class="action_div">
                                        <?php	
                                        if($row['status']=='A'){
                                            ?>
                                            <a href="<?= $cfg['SECTION_BASE_URL'] ?>manage_conference_mail.process.php?act=<?=($row['status']=='A')?'Inactive':'Active'?>&id=<?=$row['id'];?>" class="badge_padding  badge_success w-max-con text-uppercase">Active</a>
                                        <?php	
                                        }else{
                                        ?>
                                            <a href="<?= $cfg['SECTION_BASE_URL'] ?>manage_conference_mail.process.php?act=<?=($row['status']=='A')?'Inactive':'Active'?>&id=<?=$row['id'];?>" class="badge_padding  badge_danger w-max-con text-uppercase">Inactive</a>
                                        <?php	
                                        }
                                        ?>
                                    </div>
                                </td>
                                <td class="action">
                                    <div class="action_div">
                                        <a class="popup-btn icon_hover badge_secondary action-transparent br-5 w-auto edimailsettinglistingBtn" data-tab="edimailsettinglisting" data-id="<?= $row['id'] ?>"><?php edit(); ?></a>
                                        <!-- <a href="#" class="icon_hover badge_danger action-transparent br-5 w-auto"><?php delete(); ?></a> -->
                                        
                                    </div>
                                </td>
                            </tr>
                            <? } } ?>
                        </tbody>
                    </table>
                </div>
                <div class="list-action">
                    <select name="multiOperationSelector" id="multiOperationSelector">
                        <option value="">Choose</option>
                        <option value="Active">Active</option>
                        <option value="Inactive">Inactive</option>
                        <option value="Remove">Remove</option>
                    </select>
                    <button class="submit" name="submit" type="button" onclick="return multiTaskValidation('<?= $cfg['SECTION_BASE_URL'] ?>manage_conference_mail.process.php','<?= $searchString ?>');" >Apply</button>
                </div>
            </div>
            
            <div class="com_info_box_inner">
                <h5 class="com_info_box_inner_sub_head">
                    <span>Exhibitor Mailer Setting</span>
                </h5>
                <div class="table_wrap">
                    <table>
                        <thead>
                            <tr>
                                <th>Sidebar</th>
                                <th>Header Image</th>
                                <th>Footer Image</th>
                                <th class="action">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>
                                    <img class="header_img" src="https://ruedakolkata.com/natcon_2025/uploads/EMAIL.HEADER.FOOTER.IMAGE/MAILER_LOGO_0001_250526185918.png">
                                </td>
                                <td>
                                    <img class="header_img" src="https://ruedakolkata.com/natcon_2025/uploads/EMAIL.HEADER.FOOTER.IMAGE/MAILER_LOGO_0001_250526185918.png">
                                </td>
                                <td>
                                    <img class="header_img" src="https://ruedakolkata.com/natcon_2025/uploads/EMAIL.HEADER.FOOTER.IMAGE/MAILER_LOGO_0001_250526185918.png">
                                </td>
                                <td class="action">
                                    <div class="action_div">
                                        <a class="popup-btn icon_hover badge_secondary action-transparent br-5 w-auto" data-tab="editexibitorimage"><?php edit(); ?></a>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <?php include_once("includes/popup.php"); ?>
</body>
<?php include_once("includes/js-source.php"); ?>


</html>
<script>
  ///////////////////Section Edit///////////////////////////
   $(document).on('click', '.edimailsettinglistingBtn', function () {

    let editTemplateId = $(this).data('id');

    $.ajax({
        url: 'includes/popup.php',
        type: 'POST',
        data: { editTemplateId: editTemplateId },
        success: function (response) {

            $('#edimailsettinglisting').html($(response).find('#edimailsettinglisting').html());

            // Re-initialize after DOM replacement
            initedimailsettinglisting();
               $('#edimailsettinglisting').fadeIn();

        },
        error: function(xhr) {
            console.error('AJAX error', xhr.responseText);
        }
    });

});
       ///////////////////Section edit end///////////////////////////
         ///////////////////Section Edit///////////////////////////
   $(document).on('click', '.editdocheaderfooterBtn', function () {

    let editDocId = $(this).data('id');

    $.ajax({
        url: 'includes/popup.php',
        type: 'POST',
        data: { editDocId: editDocId },
        success: function (response) {

            $('#editdocheaderfooter').html($(response).find('#editdocheaderfooter').html());

            // Re-initialize after DOM replacement
            initeditdocheaderfooter();
               $('#editdocheaderfooter').fadeIn();

        },
        error: function(xhr) {
            console.error('AJAX error', xhr.responseText);
        }
    });

});
       ///////////////////Section edit end///////////////////////////
</script>
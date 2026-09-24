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
        <h2>Mail Settings</h2>
        <?php include_once("includes/header_right.php"); ?>
    </header>

    <div class="body_wrap">
        <div class="page_top_wrap mb-3">
            <div class="page_top_wrap_left">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                         <li class="breadcrumb-item"><a href="#">Master Data</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Manage Mail Settings</li>
                    </ol>
                </nav>
                <h2>Manage Mail Settings</h2>
                <h6>Manage mail header, footer, and sidebar images.</h6>
            </div>
        </div>
        <div class="com_info_box_grid">
            <div class="com_info_box_inner">
                <h5 class="com_info_box_inner_sub_head">
                    <span>Documents Header/ Footer</span>
                </h5>
                <div class="table_wrap">
                    <table>
                        <thead>
                            <tr>
                                <th>Document Header</th>
                                <th>Document Footer</th>
                                <th>Mailer Logo</th>
                                <th>Site logo</th>
                                <th class="action">Status</th>
                                <th class="action">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            	<?php

                                    $i = 0;

                                    $sql 	=	array();
                                    $sql['QUERY'] = "SELECT * FROM " . _DB_EMAIL_SETTING_ . " 
                                                                WHERE `id`!='' ";
                                    //$sql['PARAM'][]	=	array('FILD' => 'status' ,     		 'DATA' => 'A' ,       	           'TYP' => 's');					 
                                    $result 	 = $mycms->sql_select($sql);
                                    if ($result) {
                                        foreach ($result as $i => $row) {
                                            @$i++;

                                            $header_image = _BASE_URL_ . $cfg['EMAIL.HEADER.FOOTER.IMAGE'] . $row['header_image'];
                                            $footer_image =_BASE_URL_ . $cfg['EMAIL.HEADER.FOOTER.IMAGE'] . $row['footer_image'];

                                            $logo_image = _BASE_URL_ . $cfg['EMAIL.HEADER.FOOTER.IMAGE'] . $row['logo_image'];
                                            $mailer_logo =_BASE_URL_ . $cfg['EMAIL.HEADER.FOOTER.IMAGE'] . $row['mailer_logo'];
                                    ?>
                            <tr>
                                <td>
                                    <img class="header_img" src="<?php echo $header_image; ?>">
                                </td>
                                <td>
                                    <img class="header_img" src="<?php echo $footer_image; ?>">
                                </td>
                                <td>
                                    <img class="header_img" src="<?php echo $mailer_logo; ?>">
                                </td>
                                <td>
                                    <img class="header_img" src="<?php echo $logo_image; ?>">
                                </td>
                                <td>
                                    <div class="action_div">
                                          <?php	
                                        if($row['status']=='A'){
                                            ?>
                                            <a href="<?= $cfg['SECTION_BASE_URL'] ?>manage_mail_setting.process.php?act=<?=($row['status']=='A')?'Inactive':'Active'?>&id=<?=$row['id'];?>" class="badge_padding  badge_success w-max-con text-uppercase">Active</a>
                                        <?php	
                                        }else{
                                        ?>
                                            <a href="<?= $cfg['SECTION_BASE_URL'] ?>manage_mail_setting.process.php?act=<?=($row['status']=='A')?'Inactive':'Active'?>&id=<?=$row['id'];?>" class="badge_padding  badge_danger w-max-con text-uppercase">Inactive</a>
                                        <?php	
                                        }
                                        ?>
                
                                    </div>
                                </td>
                                <td class="action">
                                    <div class="action_div">
                                        <a class="popup-btn icon_hover badge_secondary action-transparent br-5 w-auto editdocheaderfooterBtn" data-tab="editdocheaderfooter" data-id="<?=$row['id']?>"><?php edit(); ?></a>
                                    </div>
                                </td>
                            </tr>
                            <? } } ?>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="com_info_box_inner">
                <h5 class="com_info_box_inner_sub_head">
                    <span>Scientific Section Mailer Setting</span>
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
                                        <a class="popup-btn icon_hover badge_secondary action-transparent br-5 w-auto" data-tab="editscisectionmailer"><?php edit(); ?></a>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
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
<?php
include_once("includes/init.php");
$loggedUserID = $mycms->getLoggedUserId();

// $show = isset($_REQUEST['show']) ? $_REQUEST['show'] : 'upload_csv';

page_header("Mail Circulation");
global $cfg, $mycms;

?>
<script src="<?= _BASE_URL_ ?>webmaster/lib/tinymce/tinymce.min.js"></script>
<script>
    tinymce.init({
        selector: '.mailer_layout',
        relative_urls: false,
        remove_script_host: false,
        convert_urls: false,
        width: 1000,
        height: 1400,
        plugins: 'advlist autolink link image lists charmap print preview hr anchor pagebreak ' +
            'searchreplace wordcount visualblocks visualchars code fullscreen insertdatetime media nonbreaking ' +
            'table emoticons template paste help',
        toolbar: 'undo redo | formatselect | bold italic backcolor | ' +
            'alignleft aligncenter alignright alignjustify | ' +
            'bullist numlist outdent indent | removeformat | help | ' +
            'link image media | code preview',
        menubar: 'file edit view insert format tools table help'
    });
</script>
<div class="body_wrap">
    <?php
    switch ($show) {

        /***************** STEP 1 *****************/
        case 'upload_csv':
            $mycms->setSession("CIRCULATION_STEP", 1);
            upload_csv_form($mycms, $cfg);
            break;

        /***************** STEP 2 *****************/
        case 'template_resource':
            template_resource_form($mycms, $cfg);
            break;

        /***************** STEP 3 *****************/
        case 'layout':
            mailer_layout_form($mycms, $cfg);
            break;
        /***************** STEP 4 *****************/
        case 'final':
            final_step($mycms, $cfg);
            break;
        // ==================================
        case 'edit_layout':
            edit_layout_form($mycms, $cfg);
            break;

        case 'edit_database':
            edit_database_form($mycms, $cfg);
            break;

        case 'addImage':
            add_image_resource_form($mycms, $cfg);
            break;

        default:
            mailer_list($mycms, $cfg);
            break;
    }
    ?>
</div>

<?php
page_footer();

/***************** FUNCTIONS *****************/

function upload_csv_form($mycms, $cfg)
{
    $step = $mycms->getSession("CIRCULATION_STEP");
    if ($step != 1) {
        $mycms->redirect('mail_circulation.php');
    }

    $sample_file = _BASE_URL_ . "uploads/MAIL.CIRCULATION.RESOURCES/mailer_sample_data.csv";

    $sqlTable['QUERY'] = "SELECT table_name , table_rows 
                            FROM information_schema.tables
                            WHERE table_schema = 'ruedakol_natcon_2025'
                            AND table_name LIKE 'circulation_%'";
    $resTables =  $mycms->sql_select($sqlTable);

?>
    <div class="body_content_box">
        <form class="con_box-grd row" method="post" action="mail_circulation.process.php" enctype="multipart/form-data">
            <input type="hidden" name="act" value="upload_csv" />
            <div class="form-group">
                <h2>Step 1: Upload CSV & Database Name</h2>
            </div>


            <div class="form-group">
                <label class="frm-head">CSV File <span class="mandatory">*</span>
                    <br>
                    <span style="font-size:13px;color:bisque"><a href="<?= $sample_file ?>" download>Download Sample File</a></span>
                </label>

                <input type="file" id="csv_file" name="csv_file" accept=".csv" style="padding: 10px 16px;width:30%" required />

                &nbsp;&nbsp;

                <select name="existing_db" id="existing_db" style="width:30%">
                    <option value="">-- Choose From Existing --</option>
                    <?php foreach ($resTables as $table) {
                        $tableName = str_replace('circulation_', '', $table['table_name']);
                        $tableName = str_replace('_', ' ', $tableName);
                        $tableName = substr($tableName, 0, -15);

                    ?>
                        <option value="<?= $table['table_name'] ?>" ><?= $tableName . '    (' . $table['table_rows'] . ' emails)' ?></option>
                    <?php } ?>
                </select>

                <input type="hidden" name="isNewDatabase" value="">
            </div>

            <div class="form-group">
                <label class="frm-head">Database Name <span class="mandatory">*</span><br>
                    <span style="font-size:12px;color:bisque"> within 25 alphabets</span></label>
                <input type="text" maxlength="25" name="database_name" id="database_name" required />
            </div>
            <div class="form-group">
                <label class="frm-head">Sender Email</label>
                <input type="email" name="sender_email" />
            </div>
            <div class="form-group">
                <label class="frm-head">Display Name</label>
                <input type="text" name="sender_name" />
            </div>
            <div class="form-group">
                <label class="frm-head">Reply to</label>
                <input type="email" name="reply_to" />
            </div>
            <!-- <div class="form-group">
                <label class="frm-head">Subject</label>
                <input type="text" name="subject" />
            </div> -->
            <div class="form-group">
                <div class="frm-btm-wrap">
                    <button type="submit" class="submit">Upload</button>
                </div>
            </div>
        </form>
    </div>
    <script>
        $(document).ready(function() {

            // When CSV file is uploaded
            $('#csv_file').on('change', function() {
                $('#csv_file').prop('required', true);
                $('#existing_db').prop('required', false);
                $('#database_name').val('');
                $('#database_name').prop('readonly', false);
                if (this.files.length > 0) {
                    $('#existing_db').val(''); // clear dropdown
                    $('#isNewDatabase').val('Y');
                } else {
                    $('#isNewDatabase').val('');
                }
            });

            // When existing option is selected
            $('#existing_db').on('change', function() {
                $('#csv_file').prop('required', false);
                $('#existing_db').prop('required', true);
                if ($(this).val() !== '') {
                    $('#csv_file').val('');

                    $('#isNewDatabase').val('N');
                    $('#database_name').val($(this).find('option:selected').text());
                    $('#database_name').prop('readonly', true);
                } else {
                    $('#isNewDatabase').val('');
                    $('#database_name').val('');
                    $('#database_name').prop('readonly', false);
                }
            });

        });
    </script>
<?php
}

function template_resource_form($mycms, $cfg)
{
    $step = $mycms->getSession("CIRCULATION_STEP");
    // if ($step != 2) {
    //     $mycms->redirect('mail_circulation.php?show=upload_csv');
    // }
?>
    <div class="body_content_box">
        <form class="con_box-grd row" method="post" action="mail_circulation.process.php" enctype="multipart/form-data">
            <input type="hidden" name="act" value="insert_template_resource" />
            <input type="hidden" name="key" value="<?= $_REQUEST['key'] ?>" />
            <div class="form-group">
                <h2>Step 2: Add Template Resource</h2>
            </div>

            <div class="form-group">
                <label class="frm-head">Logo</label>
                <input type="file" style="padding: 10px 16px;" name="logo" />
            </div>

            <div class="form-group">
                <label class="frm-head">Flyer Images (up to 12)</label>
                <div>
                    <?php for ($i = 0; $i < 12; $i++) { ?>
                        <input type="file" style="padding: 10px 16px;" accept="images" name="flyer_images[]" />
                    <?php } ?>
                </div>

            </div>

            <div class="form-group">
                <label class="frm-head">Flyer Link</label>
                <input type="text" name="flyer_link" />
            </div>

            <div class="form-group">
                <label class="frm-head">Icons</label>
                <input type="file" name="icons" style="padding: 10px 16px;" />
            </div>

            <div class="form-group">
                <label class="frm-head">Icon Links</label>
                <input type="text" name="icon_links" />
            </div>

            <div class="form-group">
                <label class="frm-head">Choose Template</label>
                <ul class="cus-check-wrap">
                    <li>
                        <p class="sub-check">
                            <label class="cus-check">ICS <input style="display:none;" type="radio" name="template" value="ICS" tabindex="15" required="" autocomplete="off">
                                <span class="checkmark"></span>
                            </label>
                            <label class="cus-check">IHPBA<input style="display:none;" type="radio" name="template" value="IHPBA" tabindex="16" required="" autocomplete="off">
                                <span class="checkmark"></span>
                            </label>
                            <label class="cus-check">None <input style="display:none;" type="radio" name="template" checked="checked" value="NONE" tabindex="16" required="" autocomplete="off">
                                <span class="checkmark"></span>
                            </label>
                        </p>
                    </li>
                </ul>

            </div>

            <div class="form-group">
                <div class="frm-btm-wrap">
                    <button type="submit" class="submit">Save Resource</button>
                </div>
            </div>
        </form>
        <div class="body_content_box">
        <?php
    }

    function mailer_layout_form($mycms, $cfg)
    {
        // $step = $mycms->getSession("CIRCULATION_STEP");
        // if ($step != 3) {
        //     $mycms->redirect('mail_circulation.php?show=upload_csv');
        // }
        $resource_id = $_REQUEST['resource_id'];

        $sqlresource['QUERY'] = "SELECT * FROM `rcg_mailer_circulation_resources` WHERE id = '" . $resource_id . "' ";

        $res_resource = $mycms->sql_select($sqlresource);
        $row_resource = $res_resource[0];
        $imageUrl = _BASE_URL_ . "uploads/MAIL.CIRCULATION.RESOURCES/";
        $flyer_tr = '';
        if ($row_resource['flyer_link']) {
            $flyer_link = $row_resource['flyer_link'];
        } else {
            $flyer_link = '#';
        }
        $flyer_arr = json_decode($row_resource['flyer_images']);
        // print_r($flyer_arr);
        if (is_array($flyer_arr) && count($flyer_arr) > 0) {
            $flyer_list = '       <style>
            .flyer-gallery {
                display: flex;
                flex-wrap: wrap;
                gap: 15px;
            }
            .flyer-item {
                width: 140px;
                text-align: center;
                font-family: Arial, sans-serif;
            }
            .flyer-item img {
                width: 100%;
                height: auto;
                border: 1px solid #ccc;
                border-radius: 6px;
                transition: transform 0.2s;
            }
            .flyer-item img:hover {
                transform: scale(1.05);
            }
            .copy-btn {
                margin-top: 6px;
                padding: 4px 8px;
                font-size: 12px;
                cursor: pointer;
                border: 1px solid #007bff;
                background-color: #007bff;
                color: white;
                border-radius: 4px;
            }
            .copy-btn:active {
                transform: scale(0.96);
            }
        </style><div class="flyer-gallery" style="display:flex;flex-wrap:wrap;gap:10px;">';

            foreach ($flyer_arr as $rowFlyer) {
                $fullImageUrl = $imageUrl . $rowFlyer;

                $flyer_list .= '
                <div class="flyer-item" style="width:80px;text-align:center;">
                    <a href="' . htmlspecialchars($fullImageUrl) . '" target="_blank">
                        <img src="' . htmlspecialchars($fullImageUrl) . '" alt="Flyer" style="width:100%;height:auto;border:1px solid #ccc;border-radius:5px;">
                    </a>
                    <button type="button" class="copy-btn" data-link="' . htmlspecialchars($fullImageUrl) . '">Copy Link</button>

                </div>
            ';
            }

            $flyer_list .= '</div>';
        }

        if ($row_resource['template'] == 'ICS') {

            $imageUrl = _BASE_URL_ . "/uploads/MAIL.CIRCULATION.RESOURCES/";
            $flyer_tr = '';
            if ($row_resource['flyer_link']) {
                $flyer_link = $row_resource['flyer_link'];
            } else {
                $flyer_link = '#';
            }
            $flyer_arr = json_decode($row_resource['flyer_images']);
            //    echo "<pre>"; print_r($flyer_arr); die;

            foreach ($flyer_arr as $rowFlyer) {
                //    echo "<pre>"; print_r($rowFlyer); die;

                $flyer_tr = '<tr>
                <td colspan="2"
                    style="text-align:center;background:white;padding: 23px 0;border-radius:25px; padding-top: 0;">
                    <a href="' . $flyer_link . '" target="_blank"><img src="' . $imageUrl . $rowFlyer . '" alt="" style="outline: none;
                    text-decoration: none;
                    -ms-interpolation-mode: bicubic;
                    clear: both;
                    display: block !important; width: 100%;"></a>
                </td>
            </tr>';
            }


            // echo  $imageUrl . $rowFlyer ;die;



            $template = '<div style="max-width:800px;margin:auto;font-family:sans-serif;width:100%;background: white;border-radius:25px">

    <table width="100%" cellpadding="0" cellspacing="0" border="0">
        <tbody> 
        <tr>
                <td>
                    <img src="' . $imageUrl . 'icslogo.png"
                        alt="" style="width:190px" class="CToWUd a6T" data-bit="iit" tabindex="0">
                </td>
                <td style="text-align:right;line-height:23px">
                    <b style="font-size:24px">Indian Chest Society</b><br> IMA Annexe Building, South Amabazari Road,
                    Nagpur<br> Ambazari, Nagpur 440010, India<br>
                    <a href="mailto:icsofficeexecutive@gmail.com" target="_blank">icsofficeexecutive@gmail.com</a>
                    <br>
                    <a href="http://www.myicsorg.net" style="color:black;text-decoration:none" target="_blank"
                        data-saferedirecturl="https://www.google.com/url?q=http://www.myicsorg.net&amp;source=gmail&amp;ust=1742104868899000&amp;usg=AOvVaw1vcJ8O1VqXd4pch1u8JkMH">www.myicsorg.net</a>
                </td>
            </tr>
           ' . $flyer_tr . '
            <tr>
                <td colspan="2" style="line-height: 24px;">
                    Dear Members,<br><br>


                   
                    
                    <br><br>
                    Best Regards,
                    <br>
                    <b>Dr. Sundeep Salvi</b> - Election Officer, ICS Election 2025
                    <br>
                    <b>Dr. Raja Dhar</b> - Secretary, ICS.
                </td>
            </tr>

            <tr>
                <td colspan="2" style="text-align:center;background:white;padding: 23px 0;border-radius:25px;">
                    <img src="' . $imageUrl . 'signature.png" alt="" style="outline: none;
                    text-decoration: none;
                    -ms-interpolation-mode: bicubic;
                    clear: both;
                    display: block !important; width: 100%;">
                </td>
            </tr>
            <tr>
                <td colspan="2" style="text-align:center">
                    <a href="https://www.facebook.com/IndianChestSociety" target="_blank"
                        style="text-decoration: none;"><img src="' . $imageUrl . 'fb.png" style="width: 32px;" width="32" alt=""> </a>
                    <a href="https://x.com/ICSNagpur" style="text-decoration: none;" target="_blank"><img src="' . $imageUrl . 'tw.png"
                            style="width: 32px;" width="32" alt=""> </a>
                    <a href="https://www.instagram.com/indianchestsociety/?utm_medium=copy_link" target="_blank"
                        style="text-decoration: none;"><img src="' . $imageUrl . 'in.png" style="width: 32px;" width="32" alt=""> </a>
                    <a href="https://open.spotify.com/show/2Z10zTEfj6Gmz9PZNKtG2e" target="_blank"
                        style="text-decoration: none;"><img src="' . $imageUrl . 'sf.png" style="width: 32px;" width="32" alt=""> </a>
                    <a href="https://www.myicsorg.net/" target="_blank" style="text-decoration: none;"><img src="' . $imageUrl . 'wb.png"
                            style="width: 32px;" width="32" alt=""> </a>
                    <a href="https://www.youtube.com/channel/UC9s4Olz_urKoWdq0E7K7KQA" target="_blank"
                        style="text-decoration: none;"><img src="' . $imageUrl . 'yt.png" style="width: 32px;" width="32" alt=""> </a>
                </td>
            </tr>
            <tr>
                <td colspan="2" style="text-align:center">
                    <img src="' . $imageUrl . 'icslogo.png"
                        alt="" style="width:190px" class="CToWUd a6T" data-bit="iit" tabindex="0">
                </td>
            </tr>
            <tr>
                <td colspan="2" style="text-align:center;line-height:23px">
                    <b style="font-size:24px">Indian Chest Society</b><br> IMA Annexe Building, South Amabazari Road,
                    Nagpur<br> Ambazari, Nagpur 440010, India<br>
                    <a href="http://icsofficeexecutive@gmail.com" style="color:black;text-decoration:none"
                        target="_blank"
                        data-saferedirecturl="https://www.google.com/url?q=http://icsofficeexecutive@gmail.com&amp;source=gmail&amp;ust=1742104868899000&amp;usg=AOvVaw0o5lUjF30k1Rh0BlD6_2OF">icsofficeexecutive@gmail.com</a>
                    <br>
                    <a href="http://www.myicsorg.net" style="color:black;text-decoration:none" target="_blank"
                        data-saferedirecturl="https://www.google.com/url?q=http://www.myicsorg.net&amp;source=gmail&amp;ust=1742104868899000&amp;usg=AOvVaw1vcJ8O1VqXd4pch1u8JkMH">www.myicsorg.net</a>
                </td>
            </tr>
            <tr>
                <td colspan="2" style="text-align:center;padding-top:25px">
                    Copyright © 2021 Indian Chest Society All rights reserved.
                </td>
            </tr>
        </tbody>
    </table>
</div>';
        }
        ?>
            <div class="body_content_box">
                <form class="con_box-grd row" method="post" action="mail_circulation.process.php">
                    <input type="hidden" name="act" value="insert_mailer_layout" />
                    <input type="hidden" name="resource_id" value="<?= $_REQUEST['resource_id'] ?>" />
                    <input type="hidden" name="key" value="<?= $_REQUEST['key'] ?>" />

                    <div class="form-group">
                        <h2>Step 3: Edit Mailer layout</h2>
                    </div>

                    <!-- <div class="form-group">
                        <label class="frm-head">Layout ID</label>
                        <input type="text" name="layout_id" />
                    </div> -->

                    <div class="form-group">
                        <label class="frm-head">Subject</label>
                        <input type="text" name="subject" />
                    </div>

                    <div class="form-group">
                        <label class="frm-head">Uploaded Images</label>
                        <?= $flyer_list; ?>
                    </div>


                    <div class="form-group">
                        <label class="frm-head">HTML Content</label>
                        <textarea name="html" class="mailer_layout"><?= $template ?></textarea>
                    </div>

                    <div class="form-group">
                        <div class="frm-btm-wrap">
                            <button type="submit" class="submit">Save & Next</button>
                        </div>
                    </div>
                </form>
            </div>
        <?php
    }

        ?>

        <script>
            document.querySelectorAll(".copy-btn").forEach(function(btn) {
                btn.addEventListener("click", function() {
                    const link = this.getAttribute("data-link");
                    navigator.clipboard.writeText(link).then(() => {
                        this.innerText = "Copied!";
                        setTimeout(() => {
                            this.innerText = "Copy Link";
                        }, 1500);
                    }).catch(err => {
                        alert("Failed to copy link: " + err);
                    });
                });
            });
        </script>

        <?php
        function final_step($mycms, $cfg)
        {

            // $step = $mycms->getSession("CIRCULATION_STEP");
            // if ($step != 4) {
            //     $mycms->redirect('mail_circulation.php?show=upload_csv');
            // }

            $id = base64_decode($_REQUEST['key']);

            $sqlDetails     =    array();
            $sqlDetails['QUERY']    = "SELECT * FROM `rcg_mailer_circulation_details`
						   WHERE `id` = ?";
            $sqlDetails['PARAM'][]    =    array('FILD' => 'id',              'DATA' => $id,                      'TYP' => 's');
            $resultDetails             = $mycms->sql_select($sqlDetails);
            $rowMailDetails             = $resultDetails[0];

            $sql     =    array();
            $sql['QUERY']    = "SELECT * FROM `rcg_mailer_circulation_layout`
						   WHERE `id` = ?";
            $sql['PARAM'][]    =    array('FILD' => 'id',              'DATA' => $rowMailDetails['layout_id'],                      'TYP' => 's');
            $result             = $mycms->sql_select($sql);
            $row             = $result[0];



            $databaseName = $rowMailDetails['data_table'];
            $totalCountResult = $mycms->sql_select([
                'QUERY' => "SELECT COUNT(*) as 'total' FROM `$databaseName` WHERE `isBlackListed`='N'"
            ]);
            $deactiveCountResult = $mycms->sql_select([
                'QUERY' => "SELECT COUNT(*) as 'Sent' FROM `$databaseName` WHERE `status`='D' AND `isBlackListed`='N'"
            ]);
            $inactiveCountResult = $mycms->sql_select([
                'QUERY' => "SELECT COUNT(*) as 'Pending' FROM `$databaseName` WHERE `status`='D' AND `isBlackListed`='N'"
            ]);
            $activeCountResult = $mycms->sql_select([
                'QUERY' => "SELECT COUNT(*) as 'Active' FROM `$databaseName` WHERE `status`='A' AND `isBlackListed`='N'"
            ]);

        ?>
            <div class="body_content_box">
                <form class="con_box-grd row">
                    <!-- <input type="hidden" name="act" value="insert_mailer_layout" /> -->
                    <div class="form-group">
                        <h2>Step 4: Edit Mailer layout<span><a href="mail_circulation.php"><- Back to List</a></span></h2>

                    </div>
                    <table cellpadding="5px">
                        <tr>
                            <td width="50%" align="left" valign="top">
                                <div class="form-group" style="color: #edcda4; font-weight:bolder">
                                    From: <?= $rowMailDetails['sender_name'] ?> < <?= $rowMailDetails['sender_email'] ?>><br>
                                        <!-- Display Name: <?= $rowMailDetails['sender_name'] ?><br> -->
                                        Reply to: <?= $rowMailDetails['reply_to'] ?><br>
                                        Subject: <?= $row['subject'] ?><br>
                                        Database: <?= $rowMailDetails['data_table'] ?><br>
                                </div>
                                <!-- <div class="form-group">
                                    <label class="frm-head">Subject</label>
                                    <input type="text" name="subject" required />
                                </div> -->
                                <div class="form-group">
                                    <input type="email" name="test_email_id" id="test_email_id" list="email_suggestions" placeholder="Enter a Email Id to send Test Mail" />
                                    <datalist id="email_suggestions">
                                        <option value="production@ruedakolkata.com">production@ruedakolkata.com</option>
                                        <option value="anamul@ruedakolkata.com">anamul@ruedakolkata.com</option>
                                        <option value="rueda@yopmail.com">rueda@yopmail.com</option>
                                        <option value="progya.roy@weavers-web.com">progya.roy@weavers-web.com</option>
                                    </datalist>
                                    &nbsp; &nbsp;<button type="button" mailer_id="<?= $rowMailDetails['id'] ?>" id="send_test_mail" class="btn btn-warning">Send</button>

                                </div>

                                <div style="color: #cee7d9;">
                                    <hr>
                                    <h6 style="background: #b7784b73;padding: 12px;border-radius: 5px;box-shadow: -2px 3px 5px 0px #ffffff6b;"><b>Circulation Status</b></h6>
                                    <br>

                                    <div style="display: flex;justify-content: space-between;">
                                        <div>
                                            Total: <?= $totalCountResult[0]['total'] ?> <br>
                                            Active: <?= $activeCountResult[0]['Active'] ?>
                                            <br>
                                            Sent: <?= $deactiveCountResult[0]['Sent'] . "/" . $totalCountResult[0]['total'] ?>
                                        </div>
                                        <div style="text-align: right;">
                                            <a href="mail_circulation.process.php?act=activeDatabase&table=<?= $databaseName ?>&key=<?= $_REQUEST['key'] ?>" class="btn btn-sm btn-success">Active</a>&nbsp;&nbsp;
                                            <a href="mail_circulation.process.php?act=InactiveDatabase&table=<?= $databaseName ?>&key=<?= $_REQUEST['key'] ?>" class="btn btn-sm btn-danger">Reset</a>&nbsp;&nbsp;
                                            <br><br>
                                            <a href="mail_circulation.php?show=edit_layout&id=<?= $rowMailDetails['layout_id'] ?>&key=<?= $_REQUEST['key'] ?>" class="btn btn-sm btn-info">Edit Template</a>



                                            <a href="mail_circulation.php?show=edit_database&id=<?= $rowMailDetails['id'] ?>&key=<?= $_REQUEST['key'] ?>" class="btn btn-sm btn-secondary">Replace Database</a>

                                        </div>


                                    </div>
                                    <hr>
                                </div>
                                <button type="button" mailer_id="<?= $rowMailDetails['id'] ?>" class="btn btn-warning" id="start_circulation_btn">Start Circulation</button>

                                <div id="progressArea" style="margin-top:20px; display:none;">
                                    <div id="progressBarContainer" style="width:100%; background:#e9ecef; height:25px; border-radius:8px; overflow:hidden;">
                                        <div id="progressBar" style="width:0%; height:25px; background:#007bff; color:#fff; text-align:center;">0%</div>
                                    </div>
                                    <div id="progressStatus" style="margin-top:10px;color: white;"></div>
                                </div>
            </div>

            </td>
            <td width="50%" align="center">
                <div class="form-group" style="padding: 28px 21px;background:white">

                    <?php echo htmlspecialchars_decode($row['html']); ?>
                </div>
            </td>

            </tr>
            </table>
            <div class="form-group">
                <div class="frm-btm-wrap">
                    <!-- <button type="submit" class="btn btn-primary">Start Circulation</button> -->

                </div>
            </div>
            </form>
        </div>
        <script>
            $(function() {
                $('#start_circulation_btn').on('click', function(e) {
                    e.preventDefault();
                    const formData = $(this).serialize();
                    $('#progressArea').show();
                    $('#progressBar').css('width', '0%').text('0%');
                    $('#progressStatus').html('⏳ Starting mail circulation...');

                    sendBatch(formData, 0); // Start from batch 0
                });

                function sendBatch(formData, offset) {
                    var mailer_id = $('#start_circulation_btn').attr('mailer_id');
                    $.ajax({
                        url: 'mail_circulation.process.php',
                        method: 'POST',
                        data: 'act=start_circulation&mailer_id=' + mailer_id + '$offset=' + offset,

                        success: function(response) {
                            const data = JSON.parse(response);
                            const percent = Math.min(100, data.progress);
                            $('#progressBar').css('width', percent + '%').text(percent + '%');
                            $('#progressStatus').html(data.message);

                            if (data.completed) {
                                $('#progressStatus').html('✅ Mailing complete. ' + data.message);
                            } else {
                                // Continue with next batch
                                setTimeout(() => sendBatch(formData, data.next_offset), 1000);
                            }
                        },
                        error: function() {
                            $('#progressStatus').html('❌ Error occurred while sending emails.');
                        }
                    });
                }
            });
        </script>
        <script>
            $(document).on('click', '#send_test_mail', function(e) {
                e.preventDefault();
                var test_email_id = $('#test_email_id').val();
                var mailer_id = $('#send_test_mail').attr('mailer_id');
                if (test_email_id == '') {
                    // alert("Please enter a email-id to send a test mail!");
                } else {
                    var filter =
                        /^([\w-\.]+)@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.)|(([\w-]+\.)+))([a-zA-Z]{2,4}|[0-9]{1,3})(\]?)$/;
                    if (!filter.test(test_email_id)) {
                        alert("Please enter valid email id!");
                        return false;
                    }


                    $.ajax({
                        type: "POST",
                        url: 'mail_circulation.process.php',
                        data: 'act=send_test_mail&mailer_id=' + mailer_id + '&test_email_id=' + test_email_id,
                        // dataType: 'json',
                        async: false,
                        success: function(response) {
                            console.log(response);

                            // if (response == 'Success') {
                            alert("Mail sent!");
                            $('#test_email_id').val('');
                            // }
                            // else if (JSONObject.succ == 200) {

                            //     $('#loading_indicator').show();
                            //     $('#loginBtn').prop('disabled', true);
                            //     if (JSONObject.msg) {

                            //     }

                            //     setTimeout(function() {
                            //         $('#loading_indicator').hide();
                            //         $('#loginBtn').prop('disabled', false);
                            //         window.location.href = jsBASE_URL + 'profile.php';

                            //     }, 1500);
                            // }


                        }
                    });
                }
            })
        </script>
    <?php
        }


        function edit_layout_form($mycms, $cfg)
        {
            // $step = $mycms->getSession("CIRCULATION_STEP");
            // if ($step != 3) {
            //     $mycms->redirect('mail_circulation.php?show=upload_csv');
            // }
            $id = $_REQUEST['id'];

            $sqlLayout['QUERY'] = "SELECT * FROM `rcg_mailer_circulation_layout` WHERE id = '" . $id . "' ";

            $res_Layout = $mycms->sql_select($sqlLayout);
            $html = $res_Layout[0]['html'];

            // RESOURCES

            $sqlresource['QUERY'] = "SELECT * FROM `rcg_mailer_circulation_resources` WHERE id = '" . $res_Layout[0]['resource_id'] . "' ";

            $res_resource = $mycms->sql_select($sqlresource);
            $row_resource = $res_resource[0];
            $imageUrl = _BASE_URL_ . "uploads/MAIL.CIRCULATION.RESOURCES/";

            $flyer_arr = json_decode($row_resource['flyer_images']);
            // print_r($flyer_arr);
            if (is_array($flyer_arr) && count($flyer_arr) > 0) {
                $flyer_list = '       <style>
            .flyer-gallery {
                display: flex;
                flex-wrap: wrap;
                gap: 15px;
            }
            .flyer-item {
                width: 140px;
                text-align: center;
                font-family: Arial, sans-serif;
            }
            .flyer-item img {
                width: 100%;
                height: auto;
                border: 1px solid #ccc;
                border-radius: 6px;
                transition: transform 0.2s;
            }
            .flyer-item img:hover {
                transform: scale(1.05);
            }
            .copy-btn {
                margin-top: 6px;
                padding: 4px 8px;
                font-size: 12px;
                cursor: pointer;
                border: 1px solid #007bff;
                background-color: #007bff;
                color: white;
                border-radius: 4px;
            }
            .copy-btn:active {
                transform: scale(0.96);
            }
        </style><div class="flyer-gallery" style="display:flex;flex-wrap:wrap;gap:10px;">';

                foreach ($flyer_arr as $rowFlyer) {
                    $fullImageUrl = $imageUrl . $rowFlyer;

                    $flyer_list .= '
                <div class="flyer-item" style="width:80px;text-align:center;">
                    <a href="' . htmlspecialchars($fullImageUrl) . '" target="_blank">
                        <img src="' . htmlspecialchars($fullImageUrl) . '" alt="Flyer" style="width:100%;height:auto;border:1px solid #ccc;border-radius:5px;">
                    </a>
                    <button type="button" class="copy-btn" data-link="' . htmlspecialchars($fullImageUrl) . '">Copy Link</button>

                </div>
            ';
                }

                $flyer_list .= '</div>';
            }


    ?>
        <div class="body_content_box">
            <form class="con_box-grd row" method="post" action="mail_circulation.process.php">
                <input type="hidden" name="act" value="edit_mailer_layout" />
                <input type="hidden" name="key" value="<?= $_REQUEST['key'] ?>" />
                <input type="hidden" name="layout_id" value="<?= $id ?>" />

                <div class="form-group">
                    <h2>Edit Template</h2>
                </div>
                <div class="form-group">
                    <label class="frm-head">Subject</label>
                    <input type="text" name="subject" value="<?= $res_Layout[0]['subject'] ?>" />
                </div>
                <div class="form-group">
                    <label class="frm-head">Uploaded Images
                        <br><br> <a href="mail_circulation.php?show=addImage&resource_id=<?= $res_Layout[0]['resource_id'] ?>&id=<?= $id ?>&key=<?= $_REQUEST['key'] ?>" style="color: #b7ff93;"><i class="fal fa-upload"></i>Add Image</a></label>
                    <?= $flyer_list; ?>
                </div>
                <div class="form-group">
                    <label class="frm-head">HTML Content</label>
                    <textarea name="edit_html" class="mailer_layout"><?= $html ?></textarea>
                </div>

                <div class="form-group">
                    <div class="frm-btm-wrap">
                        <a href="mail_circulation.php?show=final&key=<?= $_REQUEST['key'] ?>" class="submit">Back</a>
                        <button type="submit" class="submit">Update</button>
                    </div>
                </div>
            </form>
        </div>
    <?php
        }

        function edit_database_form($mycms, $cfg)
        {
            $sample_file = _BASE_URL_ . "uploads/MAIL.CIRCULATION.RESOURCES/mailer_sample_data.csv";
            $id = $_REQUEST['id'];

            $sqlMailerDetails['QUERY'] = "SELECT * FROM `rcg_mailer_circulation_details` WHERE id = '" . $id . "' ";

            $res_details = $mycms->sql_select($sqlMailerDetails);
            $details = $res_details[0];

    ?>
        <div class="body_content_box">
            <form class="con_box-grd row" method="post" action="mail_circulation.process.php" enctype="multipart/form-data">
                <input type="hidden" name="act" value="edit_database" />
                <input type="hidden" name="key" value="<?= $_REQUEST['key'] ?>" />
                <input type="hidden" name="id" value="<?= $details['id'] ?>" />

                <div class="form-group">
                    <h2>Replace/Edit Database Details</h2>
                </div>


                <div class="form-group">
                    <label class="frm-head">CSV File <span class="mandatory">*</span>
                        <br>
                        <span style="font-size:13px;color:bisque"><a href="<?= $sample_file ?>" download>Download Sample File</a></span>
                    </label>
                    <input type="file" name="csv_file" accept=".csv" style="padding: 10px 16px;" required />
                </div>

                <div class="form-group">
                    <label class="frm-head">New Database Name <span class="mandatory">*</span></label>
                    <input type="text" name="database_name" maxlength="25" required />
                </div>
                <div class="form-group">
                    <label class="frm-head">Sender Email<span class="mandatory">*</span></label>
                    <input type="email" name="sender_email" value="<?= $details['sender_email'] ?>" required />
                </div>
                <div class="form-group">
                    <label class="frm-head">Display Name<span class="mandatory">*</span></label>
                    <input type="text" name="sender_name" value="<?= $details['sender_name'] ?>" required />
                </div>
                <div class="form-group">
                    <label class="frm-head">Reply to</label>
                    <input type="email" name="reply_to" value="<?= $details['reply_to'] ?>" />
                </div>
                <!-- <div class="form-group">
                <label class="frm-head">Subject</label>
                <input type="text" name="subject" />
            </div> -->
                <div class="form-group">
                    <div class="frm-btm-wrap">
                        <a href="mail_circulation.php?show=final&key=<?= $_REQUEST['key'] ?>" class="submit">Back</a>

                        <button type="submit" class="submit">Update</button>
                    </div>
                </div>
            </form>
        </div>
    <?php
        }


        function add_image_resource_form($mycms, $cfg)
        {
            $step = $mycms->getSession("CIRCULATION_STEP");
            // if ($step != 2) {
            //     $mycms->redirect('mail_circulation.php?show=upload_csv');
            // }
    ?>
        <div class="body_content_box">
            <form class="con_box-grd row" method="post" action="mail_circulation.process.php" enctype="multipart/form-data">
                <input type="hidden" name="act" value="add_image" />
                <input type="hidden" name="key" value="<?= $_REQUEST['key'] ?>" />
                <input type="hidden" name="id" value="<?= $_REQUEST['id'] ?>" />
                <input type="hidden" name="resource_id" value="<?= $_REQUEST['resource_id'] ?>" />
                <div class="form-group">
                    <h2>Add Image</h2>
                </div>

                <div class="form-group">
                    <label class="frm-head">Image</label>
                    <input type="file" style="padding: 10px 16px;" name="image" accept="images" />
                </div>

                <div class="form-group">
                    <div class="frm-btm-wrap">
                        <a href="mail_circulation.php?show=edit_layout&id=<?= $_REQUEST['id'] ?>&key=<?= $_REQUEST['key'] ?>" class="submit">Back</a>
                        <button type="submit" class="submit">Upload</button>

                    </div>
                </div>
            </form>
            <div class="body_content_box">
            <?php
        }

        function  mailer_list($mycms, $cfg)
        {
            $sqlDetails     =    array();
            $sqlDetails['QUERY']    = "SELECT * FROM `rcg_mailer_circulation_details`
						   WHERE `status` = ? ORDER by id DESC";
            $sqlDetails['PARAM'][]    =    array('FILD' => 'status',              'DATA' => 'A',                      'TYP' => 's');
            $resultDetails             = $mycms->sql_select($sqlDetails);

            ?>
                <div class="body_content_box">
                    <div class="form-group">
                        <h2>Mailer List</h2>
                    </div>
                    <div class="table_wrap">
                        <table width="100%">
                            <thead>
                                <tr class="theader">
                                    <th class="action">Sl No.</th>
                                    <th width="50%">Details</th>
                                    <th>Database</th>
                                    <!-- <th class="action">Status</th> -->
                                    <th class="action">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php


                                if ($resultDetails) {
                                    foreach ($resultDetails as $key => $rowsl) {

                                        $sqlSubject = array();
                                        $sqlSubject['QUERY']       = "SELECT `subject` FROM `rcg_mailer_circulation_layout`
											
											 WHERE `id` = ?";

                                        $sqlSubject['PARAM'][]   = array('FILD' => 'id',     'DATA' => $rowsl['layout_id'],    'TYP' => 's');
                                        $result_subject =  $mycms->sql_select($sqlSubject);
                                        $subject = $result_subject[0]['subject'];
                                ?>
                                        <tr class="tlisting">
                                            <td class="action"><?= $key + 1 ?></td>
                                            <td><b><?= $subject ?> </b><br>
                                                From: <?= $rowsl['sender_name'] ?> | <?= $rowsl['sender_email'] ?>
                                                <br>Date: <?= date('d/m/Y', strtotime($rowsl['created_dateTime'])) ?>
                                            </td>
                                            <td><?= $rowsl['data_table'] ?></td>
                                            <!-- <td></td> -->

                                            <td class="action">
                                                <a href="mail_circulation.php?show=final&key=<?= base64_encode($rowsl['id']) ?>">
                                                    <span alt="Edit" title="Edit Record" class="icon-pen"></span></a>
                                                <a href="<?= $cfg['SECTION_BASE_URL'] ?>mail_circulation.process.php?act=deleteMailer&id=<?= $rowsl['id'] ?>" onclick="return confirm('Do you really want to delete this Mailer?');">
                                                <span  title="Delete" class="icon-trash-stroke" ></span></a>
                                            </td>
                                        </tr>
                                    <?
                                    }
                                } else {
                                    ?>
                                    <tr>
                                        <td colspan="10" align="center" class="mandatory">No Record Present.</td>
                                    </tr>
                                <?php } ?>
                            </tbody>
                        </table>
                        <div class="bbp-pagination">
                            <div class="bbp-pagination-count"><?= $mycms->paginateRecInfo(1) ?></div>
                            <span class="paginationDisplay"><?= $mycms->paginate(1, 'pagination') ?></span>
                        </div>
                    </div>

                    <a title="Add Mailer" class="stick_add" href="<?= $_SERVER['PHP_SELF'] ?>?show=upload_csv"><i class="fas fa-plus"></i></a>

                </div>
            <?php
        }
            ?>
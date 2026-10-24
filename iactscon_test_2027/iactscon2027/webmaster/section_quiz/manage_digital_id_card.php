<?php
include_once('includes/init.php');

page_header("Digital ID Card Management");

$pageKey = "_pgn_";
$pageKeyVal = ($_REQUEST[$pageKey] == "") ? 0 : $_REQUEST[$pageKey];

@$searchString = "";
$searchArray = array();
$searchArray[$pageKey] = $pageKeyVal;

foreach ($searchArray as $searchKey => $searchVal) {
    $searchString .= "&" . $searchKey . "=" . $searchVal;
}
?>
<script language="javascript" src="<?= _BASE_URL_ ?>JS/jquery.js"></script>
<style>
    .id-card-block {
        display: flex;
        flex-direction: column;
        vertical-align: top;
        width: 220px;
        margin: 0 15px 15px 0;
        padding: 12px;
        border: 1px solid #ddd;
        border-radius: 6px;
        background: #fafafa;
        text-align: center;
    }

    .id-card-block h4 {
        margin: 0 0 10px 0;
        font-size: 13px;
        font-weight: bold;
        min-height: 32px;
    }

    .id-card-block .id-card-image-slot {
        height: 130px;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-bottom: 8px;
    }

    .id-card-block .id-card-image-slot img {
        max-width: 100%;
        max-height: 130px;
        object-fit: cover;
        border: 1px solid #ddd;
        padding: 4px;
        background: #fff;
    }

    .id-card-block .id-card-image-slot .no-image-placeholder {
        width: 100%;
        height: 100%;
        display: flex;
        align-items: center;
        justify-content: center;
        border: 1px dashed #ccc;
        background: #f0f0f0;
        color: #999;
        font-size: 11px;
    }

    .id-card-block input[type="file"] {
        width: 100%;
        font-size: 11px;
        padding: 0px 0px;
        margin-top: auto;
    }

    .id-card-wrapper {
        display: flex;
        flex-wrap: wrap;
        align-items: flex-start;
    }
</style>
<div class="container">
    <?php
    switch ($show) {
        case 'add':
            addDigitalIdCard($cfg, $mycms);
            break;
        case 'edit':
            editDigitalIdCard($cfg, $mycms);
            break;
        case 'view':
            viewDigitalIdCard($cfg, $mycms);
            break;
        default:
            showAllDigitalIdCards($cfg, $mycms);
            break;
    }
    ?>
</div>
<?php
page_footer();

/*============== SHOW ALL DIGITAL ID CARDS =============*/
function showAllDigitalIdCards($cfg, $mycms)
{
    global $searchArray, $searchString;
?>
    <form name="frmDigitalIdCardListing" id="frmDigitalIdCardListing" action="<?= $cfg['SECTION_BASE_URL'] ?>manage_digital_id_card.process.php" method="post">
        <input type="hidden" name="act" id="act" value="search" />
        <input type="hidden" id="excelPrintVal" name="excelPrintVal" />
        <table width="100%" align="center" class="tborder">
            <tr>
                <td colspan="2" align="left" class="tcat">
                    <span style="float:left">Digital ID Card Listing</span>
                    <span class="tsearchTool" forType="tsearchTool"></span>
                </td>
            </tr>
            <tr>
                <td colspan="2" style="margin:0px; padding:0px;">
                    <table width="100%">
                        <tr class="theader">
                            <td width="30" align="center">
                                <input name="check_all" id="check_all" targetElement="checkBoxListing"
                                    type="checkbox" onclick="checkall(this);" />
                            </td>
                            <td width="40" align="center">Sl No</td>
                            <td width="150" align="center">Background Image</td>
                            <td width="150" align="center">CCICON Logo</td>
                            <td width="150" align="center">CONCLOUD Logo</td>
                            <td width="150" align="center">Venue Logo</td>
                            <td width="50" align="center">Status</td>
                            <td width="100" align="center">Created At</td>
                            <td width="100" align="center">Updated At</td>
                            <td width="80" align="center">Action</td>
                        </tr>
                        <?php
                        $con = "";
                        $params = [];

                        $i = 0;

                        $sql = [];
                        $sql['QUERY'] = "
                            SELECT *
                            FROM rcg_digital_id_card
                            WHERE status != 'D'
                            $con
                            ORDER BY created_at DESC
                        ";

                        foreach ($params as $p) {
                            $sql['PARAM'][] = $p;
                        }

                        $result = $mycms->sql_select_paginated($indexVal, $sql, 20, $restrt);

                        if ($result) {
                            foreach ($result as $i => $row) {
                                @$i++;
                        ?>
                                <tr class="tlisting">
                                    <td align="center">
                                        <input type="checkbox" name="checkvalue" id="checkvalue" forType="checkBoxListing"
                                            value="<?= $row['id']; ?>" />
                                    </td>
                                    <td align="center"><?= $i + ($_REQUEST['_pgn_'] * 20) ?></td>
                                    <td align="center">
                                        <?php if (!empty($row['background_image'])) { ?>
                                            <img src="<?= $cfg['SECTION_BASE_URL'] . $row['background_image'] ?>"
                                                alt="Background"
                                                style="max-width: 130px; max-height: 90px; object-fit: cover;" />
                                        <?php } else { ?>
                                            <span class="mandatory">No Image</span>
                                        <?php } ?>
                                    </td>
                                    <td align="center">
                                        <?php if (!empty($row['header_logo_1'])) { ?>
                                            <img src="<?= $cfg['SECTION_BASE_URL'] . $row['header_logo_1'] ?>"
                                                alt="CCICON Logo"
                                                style="max-width: 130px; max-height: 90px; object-fit: cover;" />
                                        <?php } else { ?>
                                            <span class="mandatory">No Image</span>
                                        <?php } ?>
                                    </td>
                                    <td align="center">
                                        <?php if (!empty($row['header_logo_2'])) { ?>
                                            <img src="<?= $cfg['SECTION_BASE_URL'] . $row['header_logo_2'] ?>"
                                                alt="CONCLOUD Logo"
                                                style="max-width: 130px; max-height: 90px; object-fit: cover;" />
                                        <?php } else { ?>
                                            <span class="mandatory">No Image</span>
                                        <?php } ?>
                                    </td>
                                    <td align="center">
                                        <?php if (!empty($row['header_logo_3'])) { ?>
                                            <img src="<?= $cfg['SECTION_BASE_URL'] . $row['header_logo_3'] ?>"
                                                alt="Venue Logo"
                                                style="max-width: 130px; max-height: 90px; object-fit: cover;" />
                                        <?php } else { ?>
                                            <span class="mandatory">No Image</span>
                                        <?php } ?>
                                    </td>
                                    <td align="center">
                                        <?php
                                        if ($row['status'] == 'A') {
                                            echo '<span style="color: green;">Active</span>';
                                        } elseif ($row['status'] == 'I') {
                                            echo '<span style="color: orange;">Inactive</span>';
                                        }
                                        ?>
                                    </td>
                                    <td align="center"><?= date('d-M-Y H:i', strtotime($row['created_at'])) ?></td>
                                    <td align="center"><?= $row['updated_at'] ? date('d-M-Y H:i', strtotime($row['updated_at'])) : '-' ?></td>
                                    <td align="center">
                                        <a href="javascript:void(null);" onClick="redirectionOfLink(this)"
                                            ehref="<?= $_SERVER['PHP_SELF'] ?>?show=edit&id=<?= $row['id'] ?><?= $searchString ?>">
                                            <span src="<?= $cfg['THEME_IMAGE_PATH'] . 'edit.png' ?>" title="Edit" class="icon-pen"></span>
                                        </a>
                                        &nbsp;
                                        <a href="<?= $cfg['SECTION_BASE_URL'] ?>manage_digital_id_card.process.php?act=Remove&id=<?= $row['id'] ?><?= $searchString ?>"
                                            onClick="return confirm('Do you really want to remove this record?');">
                                            <span src="<?= $cfg['THEME_IMAGE_PATH'] . 'drop.png' ?>" title="Remove" class="icon-trash-stroke"></span>
                                        </a>
                                        &nbsp;
                                        <?php if ($row['status'] == 'A') { ?>
                                            <a href="<?= $cfg['SECTION_BASE_URL'] ?>manage_digital_id_card.process.php?act=Inactive&id=<?= $row['id'] ?><?= $searchString ?>">
                                                <span title="Inactivate" class="icon-ban" style="color: orange;"></span>
                                            </a>
                                        <?php } else { ?>
                                            <a href="<?= $cfg['SECTION_BASE_URL'] ?>manage_digital_id_card.process.php?act=Active&id=<?= $row['id'] ?><?= $searchString ?>">
                                                <span title="Activate" class="icon-check-circle" style="color: green;"></span>
                                            </a>
                                        <?php } ?>
                                    </td>
                                </tr>
                            <?php }
                        } else { ?>
                            <tr>
                                <td colspan="10" align="center">
                                    <span class="mandatory">No Record.</span>
                                </td>
                            </tr>
                        <?php } ?>
                    </table>
                </td>
            </tr>

            <tr>
                <td width="50%" align="left">
                    <select name="multiOperationSelector" id="multiOperationSelector">
                        <option value="">Choose an action...</option>
                        <option value="Active">Active</option>
                        <option value="Inactive">Inactive</option>
                        <option value="Remove">Remove</option>
                    </select>
                    <input value="Apply to selected" name="submit" type="button"
                        onclick="return multiTaskValidation('<?= $cfg['SECTION_BASE_URL'] ?>manage_digital_id_card.process.php','<?= $searchString ?>');"
                        class="btn btn-small btn-blue" />
                </td>
                <td width="50%" align="right">
                    <a href="<?= $_SERVER['PHP_SELF'] ?>?show=add<?= $searchString ?>">Create New Record</a>
                </td>
            </tr>
            <tr class="tfooter">
                <td colspan="2">
                    <span class="paginationRecDisplay"><?= $mycms->paginateRecInfo($indexVal) ?></span>
                    <span class="paginationDisplay"><?= $mycms->paginate($indexVal, 'pagination') ?></span>
                </td>
            </tr>
        </table>
    </form>
<?php
}

/*================= ADD DIGITAL ID CARD ===============*/
function addDigitalIdCard($cfg, $mycms)
{
    global $searchArray, $searchString;
?>
    <form name="frmDigitalIdCardAdd" method="post" action="<?= $cfg['SECTION_BASE_URL'] ?>manage_digital_id_card.process.php"
        id="frmDigitalIdCardAdd" onsubmit="return onSubmitAction();" enctype="multipart/form-data">
        <input type="hidden" name="act" value="store" />
        <?php
        foreach ($searchArray as $key => $val) {
        ?>
            <input type="hidden" name="<?= $key ?>" id="<?= $key ?>" value="<?= $val ?>" />
        <?php
        }
        ?>
        <table width="100%" class="tborder">
            <thead>
                <tr>
                    <td colspan="2" align="left" class="tcat">Create Digital ID Card Images</td>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td colspan="2" style="margin:0px; padding:0px;">
                        <table width="100%">
                            <tr class="thighlight">
                                <td align="left">Details</td>
                            </tr>
                            <tr>
                                <td align="left" style="padding: 15px;">
                                    <div class="id-card-wrapper">

                                        <div class="id-card-block">
                                            <h4>Background Image <span class="mandatory">*</span></h4>
                                            <input type="file" name="background_image" id="background_image"
                                                class="validate[required]" required=""
                                                accept="image/jpeg,image/jpg,image/png,image/webp,image/svg+xml,.svg" />
                                            <br><small>JPG, JPEG, PNG, WEBP, SVG</small>
                                        </div>

                                        <div class="id-card-block">
                                            <h4>CCICON Logo <span class="mandatory">*</span></h4>
                                            <input type="file" name="header_logo_1" id="header_logo_1"
                                                class="validate[required]" required=""
                                                accept="image/jpeg,image/jpg,image/png,image/webp,image/svg+xml,.svg" />
                                            <br><small>JPG, JPEG, PNG, WEBP, SVG</small>
                                        </div>

                                        <div class="id-card-block">
                                            <h4>CONCLOUD Logo <span class="mandatory">*</span></h4>
                                            <input type="file" name="header_logo_2" id="header_logo_2"
                                                class="validate[required]" required=""
                                                accept="image/jpeg,image/jpg,image/png,image/webp,image/svg+xml,.svg" />
                                            <br><small>JPG, JPEG, PNG, WEBP, SVG</small>
                                        </div>

                                        <div class="id-card-block">
                                            <h4>Venue Logo <span class="mandatory">*</span></h4>
                                            <input type="file" name="header_logo_3" id="header_logo_3"
                                                class="validate[required]" required=""
                                                accept="image/jpeg,image/jpg,image/png,image/webp,image/svg+xml,.svg" />
                                            <br><small>JPG, JPEG, PNG, WEBP, SVG</small>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
                <tr>
                    <td colspan="2" align="left">
                        <a href="<?= $_SERVER['PHP_SELF'] ?>?pageno=<?= $_REQUEST['pageno'] ?>">
                            <input type="button" name="BackAdd" id="BackAdd" value="Back" class="btn btn-small btn-red" />
                        </a>
                        &nbsp;
                        <input type="submit" name="Save2" id="Save2" value="Save" class="btn btn-small btn-blue" />
                    </td>
                </tr>
                <tr class="tfooter">
                    <td colspan="2">&nbsp;</td>
                </tr>
            </tbody>
        </table>
    </form>
<?php
}

/*================= EDIT DIGITAL ID CARD ==============*/
function editDigitalIdCard($cfg, $mycms)
{
    global $searchArray, $searchString;

    $sql = array();
    $sql['QUERY'] = "SELECT * FROM `rcg_digital_id_card`  
                       WHERE `id` = ?";
    $sql['PARAM'][] = array('FILD' => 'id', 'DATA' => $_REQUEST['id'], 'TYP' => 'i');
    $result = $mycms->sql_select($sql);
    $row = $result[0];
?>
    <form name="frmDigitalIdCardEdit" method="post" action="<?= $cfg['SECTION_BASE_URL'] ?>manage_digital_id_card.process.php"
        id="frmDigitalIdCardEdit" onsubmit="return onSubmitAction();" enctype="multipart/form-data">
        <input type="hidden" name="act" value="edit" />
        <input type="hidden" name="id" id="id" value="<?= $_REQUEST['id'] ?>" />
        <input type="hidden" name="prev_background_image" id="prev_background_image" value="<?= $row['background_image']; ?>" />
        <input type="hidden" name="prev_header_logo_1" id="prev_header_logo_1" value="<?= $row['header_logo_1']; ?>" />
        <input type="hidden" name="prev_header_logo_2" id="prev_header_logo_2" value="<?= $row['header_logo_2']; ?>" />
        <input type="hidden" name="prev_header_logo_3" id="prev_header_logo_3" value="<?= $row['header_logo_3']; ?>" />
        <?php
        foreach ($searchArray as $key => $val) {
        ?>
            <input type="hidden" name="<?= $key ?>" id="<?= $key ?>" value="<?= $val ?>" />
        <?php
        }
        ?>
        <table width="100%" class="tborder">
            <thead>
                <tr>
                    <td colspan="2" align="left" class="tcat">Edit Digital ID Card Images</td>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td colspan="2" style="margin:0px; padding:0px;">
                        <table width="100%">
                            <tr class="thighlight">
                                <td align="left">Details</td>
                            </tr>
                            <tr>
                                <td align="left" style="padding: 15px;">
                                    <div class="id-card-wrapper">

                                        <div class="id-card-block">
                                            <h4>Background Image <span class="mandatory">*</span></h4>
                                            <div class="id-card-image-slot">
                                                <?php if (!empty($row['background_image'])) { ?>
                                                    <img src="<?= $cfg['SECTION_BASE_URL'] . $row['background_image'] ?>" alt="Current Background" />
                                                <?php } else { ?>
                                                    <div class="no-image-placeholder">No Image</div>
                                                <?php } ?>
                                            </div>
                                            <input type="file" name="background_image" id="background_image"
                                                accept="image/jpeg,image/jpg,image/png,image/webp,image/svg+xml,.svg" />
                                            <br><small>JPG, JPEG, PNG, WEBP, SVG</small>
                                        </div>

                                        <div class="id-card-block">
                                            <h4>CCICON Logo <span class="mandatory">*</span></h4>
                                            <div class="id-card-image-slot">
                                                <?php if (!empty($row['header_logo_1'])) { ?>
                                                    <img src="<?= $cfg['SECTION_BASE_URL'] . $row['header_logo_1'] ?>" alt="Current CCICON Logo" />
                                                <?php } else { ?>
                                                    <div class="no-image-placeholder">No Image</div>
                                                <?php } ?>
                                            </div>
                                            <input type="file" name="header_logo_1" id="header_logo_1"
                                                accept="image/jpeg,image/jpg,image/png,image/webp,image/svg+xml,.svg" />
                                            <br><small>JPG, JPEG, PNG, WEBP, SVG</small>
                                        </div>

                                        <div class="id-card-block">
                                            <h4>CONCLOUD Logo <span class="mandatory">*</span></h4>
                                            <div class="id-card-image-slot">
                                                <?php if (!empty($row['header_logo_2'])) { ?>
                                                    <img src="<?= $cfg['SECTION_BASE_URL'] . $row['header_logo_2'] ?>" alt="Current CONCLOUD Logo" />
                                                <?php } else { ?>
                                                    <div class="no-image-placeholder">No Image</div>
                                                <?php } ?>
                                            </div>
                                            <input type="file" name="header_logo_2" id="header_logo_2"
                                                accept="image/jpeg,image/jpg,image/png,image/webp,image/svg+xml,.svg" />
                                            <br><small>JPG, JPEG, PNG, WEBP, SVG</small>
                                        </div>

                                        <div class="id-card-block">
                                            <h4>Venue Logo <span class="mandatory">*</span></h4>
                                            <div class="id-card-image-slot">
                                                <?php if (!empty($row['header_logo_3'])) { ?>
                                                    <img src="<?= $cfg['SECTION_BASE_URL'] . $row['header_logo_3'] ?>" alt="Current Venue Logo" />
                                                <?php } else { ?>
                                                    <div class="no-image-placeholder">No Image</div>
                                                <?php } ?>
                                            </div>
                                            <input type="file" name="header_logo_3" id="header_logo_3"
                                                accept="image/jpeg,image/jpg,image/png,image/webp,image/svg+xml,.svg" />
                                            <br><small>JPG, JPEG, PNG, WEBP, SVG</small>
                                        </div>

                                    </div>
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
                <tr>
                    <td colspan="2" align="left">
                        <a href="<?= $_SERVER['PHP_SELF'] ?>?<?= $searchString ?>">
                            <input type="button" name="BackEdit" id="BackEdit" value="Back" class="btn btn-small btn-red" />
                        </a>
                        &nbsp;
                        <input type="submit" name="Save" id="Save" value="Save" class="btn btn-small btn-blue" />
                    </td>
                </tr>
                <tr class="tfooter">
                    <td colspan="2">&nbsp;</td>
                </tr>
            </tbody>
        </table>
    </form>
<?php
}

/*================= VIEW DIGITAL ID CARD ==============*/
function viewDigitalIdCard($cfg, $mycms)
{
    global $searchArray, $searchString;

    $sql = array();
    $sql['QUERY'] = "SELECT * FROM `rcg_digital_id_card`  
                       WHERE `id` = ?";
    $sql['PARAM'][] = array('FILD' => 'id', 'DATA' => $_REQUEST['id'], 'TYP' => 'i');
    $result = $mycms->sql_select($sql);
    $row = $result[0];
?>
    <table width="100%" class="tborder">
        <thead>
            <tr>
                <td colspan="2" align="left" class="tcat">View Digital ID Card Images</td>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td colspan="2" style="margin:0px; padding:0px;">
                    <table width="100%">
                        <tr class="thighlight">
                            <td align="left">Details</td>
                        </tr>
                        <tr>
                            <td align="left" style="padding: 10px 15px;"><strong>ID:</strong> <?= $row['id'] ?></td>
                        </tr>
                        <tr>
                            <td align="left" style="padding: 15px;">
                                <div class="id-card-wrapper">

                                    <div class="id-card-block">
                                        <h4>Background Image</h4>
                                        <?php if (!empty($row['background_image'])) { ?>
                                            <img src="<?= $cfg['SECTION_BASE_URL'] . $row['background_image'] ?>" alt="Background" />
                                        <?php } else { ?>
                                            <span class="mandatory">No Image</span>
                                        <?php } ?>
                                    </div>

                                    <div class="id-card-block">
                                        <h4>CCICON Logo</h4>
                                        <?php if (!empty($row['header_logo_1'])) { ?>
                                            <img src="<?= $cfg['SECTION_BASE_URL'] . $row['header_logo_1'] ?>" alt="CCICON Logo" />
                                        <?php } else { ?>
                                            <span class="mandatory">No Image</span>
                                        <?php } ?>
                                    </div>

                                    <div class="id-card-block">
                                        <h4>CONCLOUD Logo</h4>
                                        <?php if (!empty($row['header_logo_2'])) { ?>
                                            <img src="<?= $cfg['SECTION_BASE_URL'] . $row['header_logo_2'] ?>" alt="CONCLOUD Logo" />
                                        <?php } else { ?>
                                            <span class="mandatory">No Image</span>
                                        <?php } ?>
                                    </div>

                                    <div class="id-card-block">
                                        <h4>Venue Logo</h4>
                                        <?php if (!empty($row['header_logo_3'])) { ?>
                                            <img src="<?= $cfg['SECTION_BASE_URL'] . $row['header_logo_3'] ?>" alt="Venue Logo" />
                                        <?php } else { ?>
                                            <span class="mandatory">No Image</span>
                                        <?php } ?>
                                    </div>

                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td width="40%" align="left"><strong>Status:</strong></td>
                        </tr>
                        <tr>
                            <td align="left" style="padding: 0 15px 10px;">
                                <?php
                                if ($row['status'] == 'A') {
                                    echo '<span style="color: green;">Active</span>';
                                } elseif ($row['status'] == 'I') {
                                    echo '<span style="color: orange;">Inactive</span>';
                                }
                                ?>
                            </td>
                        </tr>
                        <tr>
                            <td align="left" style="padding: 0 15px 5px;"><strong>Created At:</strong> <?= date('d-M-Y H:i:s', strtotime($row['created_at'])) ?></td>
                        </tr>
                        <tr>
                            <td align="left" style="padding: 0 15px 10px;">
                                <strong>Updated At:</strong> <?= $row['updated_at'] ? date('d-M-Y H:i:s', strtotime($row['updated_at'])) : 'Never' ?>
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
            <tr>
                <td colspan="2" align="left">
                    <a href="<?= $_SERVER['PHP_SELF'] ?>?<?= $searchString ?>">
                        <input type="button" name="Back" id="Back" value="Back" class="btn btn-small btn-red" />
                    </a>
                </td>
            </tr>
            <tr class="tfooter">
                <td colspan="2">&nbsp;</td>
            </tr>
        </tbody>
    </table>
<?php
}
?>
<?php
include_once("../includes/source.php");
include_once('../includes/init.php');
include_once('../includes/function.workshop.php');
?>
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

<body>
    <?php include_once("../includes/left-menu.php"); ?>
    <header>
        <h2>Digital ID Card Management</h2>
        <?php include_once("../includes/header_right.php"); ?>
    </header>
    <div class="body_wrap">
        <?php
        $pageKey    = "_pgn_";
        $pageKeyVal = (empty($_REQUEST[$pageKey])) ? 0 : $_REQUEST[$pageKey];

        $searchString = "";
        $searchArray  = array();
        $searchArray[$pageKey] = $pageKeyVal;

        foreach ($searchArray as $searchKey => $searchVal) {
            $searchString .= "&" . $searchKey . "=" . $searchVal;
        }

        $show = $_REQUEST['show'] ?? '';
        ?>

        <div class="page_top_wrap mb-3">
            <div class="page_top_wrap_left">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="#">Quiz</a></li>
                        <li class="breadcrumb-item active" aria-current="page"><a href="#">Digital ID Card</a></li>
                    </ol>
                </nav>
                <h2>
                    <?php
                    if ($show == 'add') echo 'Create Digital ID Card Images';
                    elseif ($show == 'edit') echo 'Edit Digital ID Card Images';
                    elseif ($show == 'view') echo 'View Digital ID Card Images';
                    else echo 'Digital ID Card Listing';
                    ?>
                </h2>
                <h6>Manage digital ID card background and logo images.</h6>
            </div>
            <?php if ($show == '') { ?>
            <div class="page_top_wrap_right">
                <a href="<?= $_SERVER['PHP_SELF'] ?>?show=add<?= $searchString ?>" class="badge_primary"><i class="fal fa-plus"></i> Create New Record</a>
            </div>
            <?php } ?>
        </div>

        <div class="com_info_wrap">
            <div class="com_info_right" style="width:100%;">
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
        </div>
    </div>
</body>
<?php include_once("../includes/js-source.php"); ?>
<script>
    $(document).on('click', '.deleteIdCardBtn', function () {
        if (confirm('Do you really want to remove this record?')) {
            window.location.href = $(this).data('href');
        }
    });
    $('#applyMultiActionBtn').on('click', function () {
        let action = $('#multiOperationSelector').val();
        let selected = $('.checkBoxListing:checked').map(function () {
            return $(this).val();
        }).get();

        if (action === '') {
            alert('Please choose an action.');
            return;
        }
        if (selected.length === 0) {
            alert('Please select at least one record.');
            return;
        }
        if (confirm(`Are you sure you want to ${action} the selected records?`)) {
            $.ajax({
                url: 'manage_digital_id_card.process.php',
                type: 'POST',
                data: {
                    act: 'MultiAction',
                    action: action,
                    ids: selected
                },
                success: function () {
                    location.reload();
                }
            });
        }
    });
    $('#check_all').on('click', function () {
        $('.checkBoxListing').prop('checked', this.checked);
    });
</script>
</html>
<?php

/*============== SHOW ALL DIGITAL ID CARDS =============*/
function showAllDigitalIdCards($cfg, $mycms)
{
    global $searchArray, $searchString;

    $con    = "";
    $params = [];

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
    ?>
    <form name="frmDigitalIdCardListing" id="frmDigitalIdCardListing" action="manage_digital_id_card.process.php" method="post">
        <input type="hidden" name="act" id="act" value="search" />
        <input type="hidden" id="excelPrintVal" name="excelPrintVal" />

        <div class="com_info_box active" id="idCardListing">
            <div class="com_info_box_grid">
                <div class="com_info_box_grid_box">
                    <h5 class="com_info_box_head">
                        <n><span class="text_primary"><i class="fal fa-id-card"></i></span> Digital ID Card Listing</n>
                    </h5>
                    <div class="com_info_box_inner">
                        <div class="table_wrap">
                            <table>
                                <thead>
                                    <tr>
                                        <th class="sl">
                                            <input name="check_all" id="check_all" type="checkbox" />
                                        </th>
                                        <th class="sl">#</th>
                                        <th>Background Image</th>
                                        <th>CCICON Logo</th>
                                        <th>CONCLOUD Logo</th>
                                        <th>Venue Logo</th>
                                        <th>Status</th>
                                        <th>Created At</th>
                                        <th>Updated At</th>
                                        <th class="action text-right">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    if ($result) {
                                        $i = 0;
                                        foreach ($result as $row) {
                                            $i++;
                                    ?>
                                    <tr>
                                        <td class="sl">
                                            <input type="checkbox" name="checkvalue[]" class="checkBoxListing" value="<?= $row['id']; ?>" />
                                        </td>
                                        <td class="sl"><?= $i + ($_REQUEST['_pgn_'] * 20) ?></td>
                                        <td align="center">
                                            <?php if (!empty($row['background_image'])) { ?>
                                           <img src="<?= $row['background_image'] ?>" alt="Background" style="max-width: 100px; max-height: 70px; object-fit: cover;" />
                                                   
                                            <?php } else { ?>
                                                <span class="mandatory">No Image</span>
                                            <?php } ?>
                                        </td>
                                        <td align="center">
                                            <?php if (!empty($row['header_logo_1'])) { ?>
                                              <img src="<?= $row['header_logo_1'] ?>" alt="CCICON Logo" style="max-width: 100px; max-height: 70px; object-fit: cover;" />
                                                 
                                            <?php } else { ?>
                                                <span class="mandatory">No Image</span>
                                            <?php } ?>
                                        </td>
                                        <td align="center">
                                            <?php if (!empty($row['header_logo_2'])) { ?>
                                              <img src="<?= $row['header_logo_2'] ?>" alt="CONCLOUD Logo" style="max-width: 100px; max-height: 70px; object-fit: cover;" />
     
                                            <?php } else { ?>
                                                <span class="mandatory">No Image</span>
                                            <?php } ?>
                                        </td>
                                        <td align="center">
                                            <?php if (!empty($row['header_logo_3'])) { ?>
                                               <img src="<?= $row['header_logo_3'] ?>" alt="Venue Logo" style="max-width: 100px; max-height: 70px; object-fit: cover;" />
                                            <?php } else { ?>
                                                <span class="mandatory">No Image</span>
                                            <?php } ?>
                                        </td>
                                        <td>
                                            <?php if ($row['status'] == 'A') { ?>
                                                <n class="badge_padding badge_success w-max-con text-uppercase">Active</n>
                                            <?php } elseif ($row['status'] == 'I') { ?>
                                                <n class="badge_padding badge_danger w-max-con text-uppercase">Inactive</n>
                                            <?php } ?>
                                        </td>
                                        <td><?= date('d-M-Y H:i', strtotime($row['created_at'])) ?></td>
                                        <td><?= $row['updated_at'] ? date('d-M-Y H:i', strtotime($row['updated_at'])) : '-' ?></td>
                                        <td class="action">
                                            <div class="action_div dropdown" role="menu" aria-label="Actions">
                                                <button class="icon_hover badge_dark action-transparent dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false"><?php ellips(); ?></button>
                                                <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                                                   <a href="<?= $_SERVER['PHP_SELF'] ?>?show=edit&id=<?= $row['id'] ?><?= $searchString ?>" class="icon_hover badge_secondary action-transparent"><?php edit(); ?>Edit Details</a>
                                                    <?php if ($row['status'] == 'A') { ?>
                                                        <a href="manage_digital_id_card.process.php?act=Inactive&id=<?= $row['id'] ?><?= $searchString ?>" class="icon_hover badge_secondary action-transparent"><i class="fal fa-ban"></i> Inactivate</a>
                                                    <?php } else { ?>
                                                        <a href="manage_digital_id_card.process.php?act=Active&id=<?= $row['id'] ?><?= $searchString ?>" class="icon_hover badge_secondary action-transparent"><i class="fal fa-check-circle"></i> Activate</a>
                                                    <?php } ?>
                                                    <a href="javascript:void(0);" class="deleteIdCardBtn icon_hover badge_danger action-transparent br-5 w-auto" data-href="manage_digital_id_card.process.php?act=Remove&id=<?= $row['id'] ?><?= $searchString ?>"><?php delete(); ?>Remove</a>
                                                </ul>
                                            </div>
                                        </td>
                                    </tr>
                                    <?php
                                        }
                                    } else {
                                    ?>
                                    <tr>
                                        <td colspan="10" align="center">
                                            <span class="mandatory">No Record.</span>
                                        </td>
                                    </tr>
                                    <?php } ?>
                                </tbody>
                            </table>
                        </div>

                        <div class="page_top_wrap mt-3">
                            <div class="page_top_wrap_left">
                                <select name="multiOperationSelector" id="multiOperationSelector">
                                    <option value="">Choose an action...</option>
                                    <option value="Active">Active</option>
                                    <option value="Inactive">Inactive</option>
                                    <option value="Remove">Remove</option>
                                </select>
                                <button type="button" id="applyMultiActionBtn" class="badge_dark">Apply to selected</button>
                            </div>
                        </div>

                        <div class="paginationDisplay">
                            <span class="paginationRecDisplay"><?= $mycms->paginateRecInfo($indexVal) ?></span>
                            <span class="paginationDisplay"><?= $mycms->paginate($indexVal, 'pagination') ?></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
    <?php
}

/*================= ADD DIGITAL ID CARD ===============*/
function addDigitalIdCard($cfg, $mycms)
{
    global $searchArray, $searchString;
    ?>
    <div class="com_info_box active" id="addIdCard">
        <form name="frmDigitalIdCardAdd" method="post" action="manage_digital_id_card.process.php"
            id="frmDigitalIdCardAdd" onsubmit="return onSubmitAction();" enctype="multipart/form-data">
            <input type="hidden" name="act" value="store" />
            <?php
            foreach ($searchArray as $key => $val) {
            ?>
            <input type="hidden" name="<?= $key ?>" id="<?= $key ?>" value="<?= $val ?>" />
            <?php
            }
            ?>
            <div class="com_info_box_grid">
                <div class="com_info_box_grid_box">
                    <h5 class="com_info_box_head">
                        <n><span class="text_success"><i class="fal fa-id-card"></i></span> Details</n>
                        <div class="page_top_wrap_right">
                            <a href="<?= $_SERVER['PHP_SELF'] ?>?<?= ltrim($searchString, '&') ?>" class="badge_dark"><i class="fal fa-arrow-left"></i> Back</a>
                            <button type="submit" id="Save2" name="Save2" class="badge_success"><i class="fal fa-save"></i> Save</button>
                        </div>
                    </h5>
                    <div class="com_info_box_inner">
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
                    </div>
                </div>
            </div>
        </form>
    </div>
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
    <div class="com_info_box active" id="editIdCard">
        <form name="frmDigitalIdCardEdit" method="post" action="manage_digital_id_card.process.php"
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
            <div class="com_info_box_grid">
                <div class="com_info_box_grid_box">
                    <h5 class="com_info_box_head">
                        <n><span class="text_success"><i class="fal fa-id-card"></i></span> Details</n>
                        <div class="page_top_wrap_right">
                            <a href="<?= $_SERVER['PHP_SELF'] ?>?<?= ltrim($searchString, '&') ?>" class="badge_dark"><i class="fal fa-arrow-left"></i> Back</a>
                            <button type="submit" id="Save" name="Save" class="badge_success"><i class="fal fa-save"></i> Save</button>
                        </div>
                    </h5>
                    <div class="com_info_box_inner">
                        <div class="id-card-wrapper">

                            <div class="id-card-block">
                                <h4>Background Image <span class="mandatory">*</span></h4>
                                <div class="id-card-image-slot">
                                    <?php if (!empty($row['background_image'])) { ?>
                                      <img src="<?= $row['background_image'] ?>" alt="Current Background" />
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
                                      <img src="<?= $row['header_logo_1'] ?>" alt="Current CCICON Logo" />
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
                             <img src="<?= $row['header_logo_2'] ?>" alt="Current CONCLOUD Logo" />
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
                                       <img src="<?= $row['header_logo_3'] ?>" alt="Current Venue Logo" />
                                    <?php } else { ?>
                                        <div class="no-image-placeholder">No Image</div>
                                    <?php } ?>
                                </div>
                                <input type="file" name="header_logo_3" id="header_logo_3"
                                    accept="image/jpeg,image/jpg,image/png,image/webp,image/svg+xml,.svg" />
                                <br><small>JPG, JPEG, PNG, WEBP, SVG</small>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
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
    <div class="com_info_box active" id="viewIdCard">
        <div class="com_info_box_grid">
            <div class="com_info_box_grid_box">
                <h5 class="com_info_box_head">
                    <n><span class="text_primary"><i class="fal fa-id-card"></i></span> View Digital ID Card Images</n>
                    <div class="page_top_wrap_right">
                        <a href="<?= $_SERVER['PHP_SELF'] ?>?<?= ltrim($searchString, '&') ?>" class="badge_dark"><i class="fal fa-arrow-left"></i> Back</a>
                    </div>
                </h5>
                <div class="com_info_box_inner">
                    <div class="form_grid g_3 mb-3">
                        <div class="frm_grp">
                            <p class="frm-head">ID</p>
                            <p><?= $row['id'] ?></p>
                        </div>
                        <div class="frm_grp">
                            <p class="frm-head">Status</p>
                            <p>
                                <?php if ($row['status'] == 'A') { ?>
                                    <n class="badge_padding badge_success w-max-con text-uppercase">Active</n>
                                <?php } elseif ($row['status'] == 'I') { ?>
                                    <n class="badge_padding badge_danger w-max-con text-uppercase">Inactive</n>
                                <?php } ?>
                            </p>
                        </div>
                        <div class="frm_grp">
                            <p class="frm-head">Created At</p>
                            <p><?= date('d-M-Y H:i:s', strtotime($row['created_at'])) ?></p>
                        </div>
                        <div class="frm_grp">
                            <p class="frm-head">Updated At</p>
                            <p><?= $row['updated_at'] ? date('d-M-Y H:i:s', strtotime($row['updated_at'])) : 'Never' ?></p>
                        </div>
                    </div>

                    <div class="id-card-wrapper">

                        <div class="id-card-block">
                            <h4>Background Image</h4>
                            <?php if (!empty($row['background_image'])) { ?>
                              <img src="<?= $row['background_image'] ?>" alt="Background" />
                            <?php } else { ?>
                                <span class="mandatory">No Image</span>
                            <?php } ?>
                        </div>

                        <div class="id-card-block">
                            <h4>CCICON Logo</h4>
                            <?php if (!empty($row['header_logo_1'])) { ?>
                               <img src="<?= $row['header_logo_1'] ?>" alt="CCICON Logo" />
                            <?php } else { ?>
                                <span class="mandatory">No Image</span>
                            <?php } ?>
                        </div>

                        <div class="id-card-block">
                            <h4>CONCLOUD Logo</h4>
                            <?php if (!empty($row['header_logo_2'])) { ?>
                             <img src="<?= $row['header_logo_2'] ?>" alt="CONCLOUD Logo" />
                            <?php } else { ?>
                                <span class="mandatory">No Image</span>
                            <?php } ?>
                        </div>

                        <div class="id-card-block">
                            <h4>Venue Logo</h4>
                            <?php if (!empty($row['header_logo_3'])) { ?>
                               <img src="<?= $row['header_logo_3'] ?>" alt="Venue Logo" />
                            <?php } else { ?>
                                <span class="mandatory">No Image</span>
                            <?php } ?>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php
}
?>
<?php
include_once("./includes/source.php");
include_once('./includes/init.php');
include_once('./includes/function.workshop.php');
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

    /* API preview card */
    .api_preview_wrap {
        display: flex;
        flex-wrap: wrap;
        gap: 15px;
        margin-top: 10px;
    }
    .api_preview_box {
        border: 1px solid #ddd;
        border-radius: 6px;
        padding: 10px;
        background: #fff;
        min-width: 200px;
    }
    .api_preview_box h6 {
        margin: 0 0 8px 0;
        font-size: 12px;
        color: #666;
        text-transform: uppercase;
        letter-spacing: .5px;
    }
    .api_preview_box .val {
        font-size: 14px;
        font-weight: 600;
        color: #222;
        word-break: break-all;
    }
</style>

<body>
    <?php include_once("./includes/left-menu.php"); ?>
    <header>
        <h2>ID Card Studio</h2>
        <?php include_once("./includes/header_right.php"); ?>
    </header>
    <div class="body_wrap">
        <?php
        $pageKey    = "_pgn_";
        $pageKeyVal = (empty($_REQUEST[$pageKey])) ? 0 : (int)$_REQUEST[$pageKey];

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
                        <li class="breadcrumb-item"><a href="#">App Setup</a></li>
                        <li class="breadcrumb-item active" aria-current="page"><a href="#">ID Card Studio</a></li>
                    </ol>
                </nav>
                <h2>
                    <?php
                    if ($show == 'add') echo 'Create Digital ID Card';
                    elseif ($show == 'edit') echo 'Edit Digital ID Card';
                    elseif ($show == 'view') echo 'View Digital ID Card';
                    else echo 'Digital ID Card Listing';
                    ?>
                </h2>
                <h6>Manage digital ID card event details, background and logo images.</h6>
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
<?php include_once("./includes/js-source.php"); ?>
<script>
    // The forms call onSubmitAction() on submit. If it is not provided by the shared
    // js files, an undefined function throws and the form never submits - so define a fallback.
    if (typeof window.onSubmitAction !== 'function') {
        window.onSubmitAction = function () { return true; };
    }

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
                    window.location.href = 'manage_digital_id_card.php';
                },
                error: function (xhr) {
                    alert('Request failed (' + xhr.status + '). Please try again.');
                }
            });
        }
    });

    $('#check_all').on('click', function () {
        $('.checkBoxListing').prop('checked', this.checked);
    });

    // End date must not be before start date (server validates this too)
    $('#frmDigitalIdCardAdd, #frmDigitalIdCardEdit').on('submit', function (e) {
        var s = $('#event_start_date').val();
        var en = $('#event_end_date').val();
        if (s && en && en < s) {
            alert('Event end date cannot be before event start date.');
            e.stopImmediatePropagation();
            return false;
        }
    });
    $('#event_start_date').on('change', function () {
        $('#event_end_date').attr('min', $(this).val());
    });
    $('#event_start_date').trigger('change');

    // Live image preview inside the badge canvas
    $(document).on('change', 'input[type="file"]', function () {
        var input = this;
        var slot = $(input).closest('.id-card-block').find('.id-card-image-slot');

        if (input.files && input.files[0]) {
            var reader = new FileReader();
            reader.onload = function (e) {
                slot.html('<img src="' + e.target.result + '" alt="Preview" />');
            };
            reader.readAsDataURL(input.files[0]);
        }
    });
</script>
</html>
<?php

/*============== HELPERS =============*/

// Same format as the API: "10 Dec 2026" / "10 - 12 Dec 2026" / "30 Nov - 02 Dec 2026"
function idCardEventDateLabel($start, $end)
{
    if (empty($start) || strpos($start, '0000') === 0) {
        return '-';
    }
    $s = strtotime($start);
    if (empty($end) || $end === $start || strpos($end, '0000') === 0) {
        return date('d M Y', $s);
    }
    $e = strtotime($end);
    if (date('Y-m', $s) === date('Y-m', $e)) {
        return date('d', $s) . ' - ' . date('d M Y', $e);
    }
    if (date('Y', $s) === date('Y', $e)) {
        return date('d M', $s) . ' - ' . date('d M Y', $e);
    }
    return date('d M Y', $s) . ' - ' . date('d M Y', $e);
}

// Value for <input type="date"> ('' when empty / zero date)
function idCardDateInputVal($v)
{
    return (empty($v) || strpos($v, '0000') === 0) ? '' : substr($v, 0, 10);
}

// Base URL for images (same as API: server_base_url('webmaster/section_quiz/'))
function idCardImageUrl($relativePath)
{
    if (empty($relativePath)) return '';
    if (preg_match('#^https?://#i', $relativePath)) return $relativePath;
    return server_base_url('webmaster/section_quiz/') . ltrim($relativePath, '/');
}

// Loads one non-deleted record by the id in the request (or null)
function idCardLoadRow($mycms)
{
    $sql = array();
    $sql['QUERY'] = "SELECT * FROM `rcg_digital_id_card` WHERE `id` = ? AND `status` != 'D'";
    $sql['PARAM'][] = array('FILD' => 'id', 'DATA' => (int)($_REQUEST['id'] ?? 0), 'TYP' => 'i');
    $result = $mycms->sql_select($sql);
    return (!empty($result[0])) ? $result[0] : null;
}

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
    $pgn = (int)($_REQUEST['_pgn_'] ?? 0);
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
                                        <th>Event Date</th>
                                        <th>Event Location</th>
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
                                            <input type="checkbox" name="checkvalue[]" class="checkBoxListing" value="<?= (int)$row['id']; ?>" />
                                        </td>
                                        <td class="sl"><?= $i + ($pgn * 20) ?></td>
                                        <td><?= htmlspecialchars(idCardEventDateLabel($row['event_start_date'] ?? null, $row['event_end_date'] ?? null)) ?></td>
                                        <td><?= !empty($row['event_location']) ? htmlspecialchars($row['event_location']) : '-' ?></td>
                                        <td align="center">
                                            <?php if (!empty($row['background_image'])) { ?>
                                                <img src="<?= htmlspecialchars(idCardImageUrl($row['background_image'])) ?>" alt="Background" style="max-width: 100px; max-height: 70px; object-fit: cover;" />
                                            <?php } else { ?>
                                                <span class="mandatory">No Image</span>
                                            <?php } ?>
                                        </td>
                                        <td align="center">
                                            <?php if (!empty($row['header_logo_1'])) { ?>
                                                <img src="<?= htmlspecialchars(idCardImageUrl($row['header_logo_1'])) ?>" alt="CCICON Logo" style="max-width: 100px; max-height: 70px; object-fit: cover;" />
                                            <?php } else { ?>
                                                <span class="mandatory">No Image</span>
                                            <?php } ?>
                                        </td>
                                        <td align="center">
                                            <?php if (!empty($row['header_logo_2'])) { ?>
                                                <img src="<?= htmlspecialchars(idCardImageUrl($row['header_logo_2'])) ?>" alt="CONCLOUD Logo" style="max-width: 100px; max-height: 70px; object-fit: cover;" />
                                            <?php } else { ?>
                                                <span class="mandatory">No Image</span>
                                            <?php } ?>
                                        </td>
                                        <td align="center">
                                            <?php if (!empty($row['header_logo_3'])) { ?>
                                                <img src="<?= htmlspecialchars(idCardImageUrl($row['header_logo_3'])) ?>" alt="Venue Logo" style="max-width: 100px; max-height: 70px; object-fit: cover;" />
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
                                        <td><?= !empty($row['created_at']) ? date('d-M-Y H:i', strtotime($row['created_at'])) : '-' ?></td>
                                        <td><?= !empty($row['updated_at']) ? date('d-M-Y H:i', strtotime($row['updated_at'])) : '-' ?></td>
                                        <td class="action">
                                            <div class="action_div dropdown" role="menu" aria-label="Actions">
                                                <button type="button" class="icon_hover badge_dark action-transparent dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false"><?php ellips(); ?></button>
                                                <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                                                    <a href="<?= $_SERVER['PHP_SELF'] ?>?show=view&id=<?= (int)$row['id'] ?><?= $searchString ?>" class="icon_hover badge_secondary action-transparent"><i class="fal fa-eye"></i> View</a>
                                                    <a href="<?= $_SERVER['PHP_SELF'] ?>?show=edit&id=<?= (int)$row['id'] ?><?= $searchString ?>" class="icon_hover badge_secondary action-transparent"><?php edit(); ?>Edit Details</a>
                                                    <?php if ($row['status'] == 'A') { ?>
                                                        <a href="manage_digital_id_card.process.php?act=Inactive&id=<?= (int)$row['id'] ?><?= $searchString ?>" class="icon_hover badge_secondary action-transparent"><i class="fal fa-ban"></i> Inactivate</a>
                                                    <?php } else { ?>
                                                        <a href="manage_digital_id_card.process.php?act=Active&id=<?= (int)$row['id'] ?><?= $searchString ?>" class="icon_hover badge_secondary action-transparent"><i class="fal fa-check-circle"></i> Activate</a>
                                                    <?php } ?>
                                                    <a href="javascript:void(0);" class="deleteIdCardBtn icon_hover badge_danger action-transparent br-5 w-auto" data-href="manage_digital_id_card.process.php?act=Remove&id=<?= (int)$row['id'] ?><?= $searchString ?>"><?php delete(); ?>Remove</a>
                                                </ul>
                                            </div>
                                        </td>
                                    </tr>
                                    <?php
                                        }
                                    } else {
                                    ?>
                                    <tr>
                                        <td colspan="12" align="center">
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
            <input type="hidden" name="<?= $key ?>" id="<?= $key ?>" value="<?= htmlspecialchars($val) ?>" />
            <?php
            }
            ?>

            <div class="venue_grid">

                <!-- LEFT: Live Badge Canvas Preview -->
                <div class="push_noti_right_botttom">
                    <div class="venue_hall_top push_noti_top">
                        <h5>
                            <n><i class="fal fa-portrait"></i>Live Badge Canvas Preview</n>
                        </h5>
                        <span class="badge_secondary">Standard 85x54mm CR80 Badge</span>
                    </div>

                    <div class="id_card_box">
                        <div class="id_card_content">
                            <div class="id_card_content_top">
                                <n>80th IACTSCON 2026 • KOLKATA</n>
                                <l class="badge_default">Delegate PASS</l>
                                <p></p>
                                <h5>Dr. Sarah Williams</h5>
                                <h6>Royal Brompton Hospital, London</h6>
                            </div>
                            <div class="id_card_content_bottom">
                                <h5></h5>
                                <p>Scan for Hall Access & Meal Counter</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- RIGHT: Details form -->
                <div class="id_box_right">
                    <div class="venue_hall_top push_noti_top">
                        <h5>
                            <n><i class="fal fa-sliders-v"></i>Badge Template Configurator</n>
                            <l>Customize header text, category badge color, background gradients & QR positions.</l>
                        </h5>
                        <div class="page_top_wrap_right">
                            <a href="<?= $_SERVER['PHP_SELF'] ?>?<?= ltrim($searchString, '&') ?>" class="badge_dark"><i class="fal fa-arrow-left"></i> Back</a>
                            <button type="submit" id="Save2" name="Save2" class="badge_success"><i class="fal fa-save"></i> Save</button>
                        </div>
                    </div>

                    <div class="form_grid">
                        <div class="frm_grp span_2">
                            <p class="frm-head">Event Start Date <span class="mandatory">*</span></p>
                            <input type="date" name="event_start_date" id="event_start_date"
                                class="validate[required]" required="" />
                        </div>
                        <div class="frm_grp span_2">
                            <p class="frm-head">Event End Date</p>
                            <input type="date" name="event_end_date" id="event_end_date" />
                        </div>
                        <div class="frm_grp span_4">
                            <p class="frm-head">Event Location <span class="mandatory">*</span></p>
                            <input type="text" name="event_location" id="event_location" maxlength="255"
                                class="validate[required]" required="" placeholder="Venue / City" />
                        </div>
                    </div>

                    <div class="id-card-wrapper">

                        <div class="id-card-block">
                            <h4>Background Image <span class="mandatory">*</span></h4>
                            <div class="id-card-image-slot">
                                <div class="no-image-placeholder">No Image</div>
                            </div>
                            <input type="file" name="background_image" id="background_image"
                                class="validate[required]" required=""
                                accept="image/jpeg,image/jpg,image/png,image/webp,image/svg+xml,.svg" />
                            <br><small>JPG, JPEG, PNG, WEBP, SVG</small>
                        </div>

                        <div class="id-card-block">
                            <h4>CCICON Logo <span class="mandatory">*</span></h4>
                            <div class="id-card-image-slot">
                                <div class="no-image-placeholder">No Image</div>
                            </div>
                            <input type="file" name="header_logo_1" id="header_logo_1"
                                class="validate[required]" required=""
                                accept="image/jpeg,image/jpg,image/png,image/webp,image/svg+xml,.svg" />
                            <br><small>JPG, JPEG, PNG, WEBP, SVG</small>
                        </div>

                        <div class="id-card-block">
                            <h4>CONCLOUD Logo <span class="mandatory">*</span></h4>
                            <div class="id-card-image-slot">
                                <div class="no-image-placeholder">No Image</div>
                            </div>
                            <input type="file" name="header_logo_2" id="header_logo_2"
                                class="validate[required]" required=""
                                accept="image/jpeg,image/jpg,image/png,image/webp,image/svg+xml,.svg" />
                            <br><small>JPG, JPEG, PNG, WEBP, SVG</small>
                        </div>

                        <div class="id-card-block">
                            <h4>Venue Logo <span class="mandatory">*</span></h4>
                            <div class="id-card-image-slot">
                                <div class="no-image-placeholder">No Image</div>
                            </div>
                            <input type="file" name="header_logo_3" id="header_logo_3"
                                class="validate[required]" required=""
                                accept="image/jpeg,image/jpg,image/png,image/webp,image/svg+xml,.svg" />
                            <br><small>JPG, JPEG, PNG, WEBP, SVG</small>
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

    $row = idCardLoadRow($mycms);
    if (!$row) {
        echo '<div class="com_info_box active"><span class="mandatory">Record not found.</span> '
           . '<a href="' . $_SERVER['PHP_SELF'] . '?' . ltrim($searchString, '&') . '" class="badge_dark">Back</a></div>';
        return;
    }
    ?>
    <div class="com_info_box active" id="editIdCard">
        <form name="frmDigitalIdCardEdit" method="post" action="manage_digital_id_card.process.php"
            id="frmDigitalIdCardEdit" onsubmit="return onSubmitAction();" enctype="multipart/form-data">
            <input type="hidden" name="act" value="edit" />
            <input type="hidden" name="id" id="id" value="<?= (int)$row['id'] ?>" />
            <input type="hidden" name="prev_background_image" id="prev_background_image" value="<?= htmlspecialchars($row['background_image'] ?? '') ?>" />
            <input type="hidden" name="prev_header_logo_1" id="prev_header_logo_1" value="<?= htmlspecialchars($row['header_logo_1'] ?? '') ?>" />
            <input type="hidden" name="prev_header_logo_2" id="prev_header_logo_2" value="<?= htmlspecialchars($row['header_logo_2'] ?? '') ?>" />
            <input type="hidden" name="prev_header_logo_3" id="prev_header_logo_3" value="<?= htmlspecialchars($row['header_logo_3'] ?? '') ?>" />
            <?php
            foreach ($searchArray as $key => $val) {
            ?>
            <input type="hidden" name="<?= $key ?>" id="<?= $key ?>" value="<?= htmlspecialchars($val) ?>" />
            <?php
            }
            ?>

            <div class="venue_grid">

                <!-- LEFT: Live Badge Canvas Preview -->
                <div class="push_noti_right_botttom">
                    <div class="venue_hall_top push_noti_top">
                        <h5>
                            <n><i class="fal fa-portrait"></i>Live Badge Canvas Preview</n>
                        </h5>
                        <span class="badge_secondary">Standard 85x54mm CR80 Badge</span>
                    </div>

                    <div class="id_card_box">
                        <div class="id_card_content">
                            <div class="id_card_content_top">
                                <n>80th IACTSCON 2026 • KOLKATA</n>
                                <l class="badge_default">Delegate PASS</l>
                                <p></p>
                                <h5>Dr. Sarah Williams</h5>
                                <h6>Royal Brompton Hospital, London</h6>
                            </div>
                            <div class="id_card_content_bottom">
                                <h5></h5>
                                <p>Scan for Hall Access & Meal Counter</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- RIGHT: Details form -->
                <div class="id_box_right">
                    <div class="venue_hall_top push_noti_top">
                        <h5>
                            <n><i class="fal fa-sliders-v"></i>Badge Template Configurator</n>
                            <l>Customize header text, category badge color, background gradients & QR positions.</l>
                        </h5>
                        <div class="page_top_wrap_right">
                            <a href="<?= $_SERVER['PHP_SELF'] ?>?<?= ltrim($searchString, '&') ?>" class="badge_dark"><i class="fal fa-arrow-left"></i> Back</a>
                            <button type="submit" id="Save" name="Save" class="badge_success"><i class="fal fa-save"></i> Save</button>
                        </div>
                    </div>

                    <div class="form_grid">
                        <div class="frm_grp span_2">
                            <p class="frm-head">Event Start Date <span class="mandatory">*</span></p>
                            <input type="date" name="event_start_date" id="event_start_date"
                                class="validate[required]" required=""
                                value="<?= htmlspecialchars(idCardDateInputVal($row['event_start_date'] ?? '')) ?>" />
                        </div>
                        <div class="frm_grp span_2">
                            <p class="frm-head">Event End Date</p>
                            <input type="date" name="event_end_date" id="event_end_date"
                                value="<?= htmlspecialchars(idCardDateInputVal($row['event_end_date'] ?? '')) ?>" />
                        </div>
                        <div class="frm_grp span_4">
                            <p class="frm-head">Event Location <span class="mandatory">*</span></p>
                            <input type="text" name="event_location" id="event_location" maxlength="255"
                                class="validate[required]" required="" placeholder="Venue / City"
                                value="<?= htmlspecialchars($row['event_location'] ?? '') ?>" />
                        </div>
                    </div>

                    <div class="id-card-wrapper">

                        <div class="id-card-block">
                            <h4>Background Image <span class="mandatory">*</span></h4>
                            <div class="id-card-image-slot">
                                <?php if (!empty($row['background_image'])) { ?>
                                    <img src="<?= htmlspecialchars(idCardImageUrl($row['background_image'])) ?>" alt="Current Background" />
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
                                    <img src="<?= htmlspecialchars(idCardImageUrl($row['header_logo_1'])) ?>" alt="Current CCICON Logo" />
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
                                    <img src="<?= htmlspecialchars(idCardImageUrl($row['header_logo_2'])) ?>" alt="Current CONCLOUD Logo" />
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
                                    <img src="<?= htmlspecialchars(idCardImageUrl($row['header_logo_3'])) ?>" alt="Current Venue Logo" />
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
        </form>
    </div>
    <?php
}

/*================= VIEW DIGITAL ID CARD ==============*/
function viewDigitalIdCard($cfg, $mycms)
{
    global $searchArray, $searchString;

    $row = idCardLoadRow($mycms);
    if (!$row) {
        echo '<div class="com_info_box active"><span class="mandatory">Record not found.</span> '
           . '<a href="' . $_SERVER['PHP_SELF'] . '?' . ltrim($searchString, '&') . '" class="badge_dark">Back</a></div>';
        return;
    }
    ?>
    <div class="com_info_box active" id="viewIdCard">
        <div class="venue_grid">

            <!-- LEFT: Live Badge Canvas Preview -->
            <div class="push_noti_right_botttom">
                <div class="venue_hall_top push_noti_top">
                    <h5>
                        <n><i class="fal fa-portrait"></i>Live Badge Canvas Preview</n>
                    </h5>
                    <span class="badge_secondary">Standard 85x54mm CR80 Badge</span>
                </div>

                <div class="id_card_box">
                    <div class="id_card_content">
                        <div class="id_card_content_top">
                            <n>80th IACTSCON 2026 • KOLKATA</n>
                            <l class="badge_default">Delegate PASS</l>
                            <p></p>
                            <h5>Dr. Sarah Williams</h5>
                            <h6>Royal Brompton Hospital, London</h6>
                        </div>
                        <div class="id_card_content_bottom">
                            <h5></h5>
                            <p>Scan for Hall Access & Meal Counter</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- RIGHT: View details -->
            <div class="id_box_right">
                <div class="venue_hall_top push_noti_top">
                    <h5>
                        <n><i class="fal fa-id-card"></i>View Digital ID Card</n>
                        <l>Record details and images.</l>
                    </h5>
                    <div class="page_top_wrap_right">
                        <a href="<?= $_SERVER['PHP_SELF'] ?>?<?= ltrim($searchString, '&') ?>" class="badge_dark"><i class="fal fa-arrow-left"></i> Back</a>
                    </div>
                </div>

                <div class="form_grid">
                    <div class="frm_grp span_2">
                        <p class="frm-head">ID</p>
                        <p><?= (int)$row['id'] ?></p>
                    </div>
                    <div class="frm_grp span_2">
                        <p class="frm-head">Status</p>
                        <p>
                            <?php if ($row['status'] == 'A') { ?>
                                <n class="badge_padding badge_success w-max-con text-uppercase">Active</n>
                            <?php } elseif ($row['status'] == 'I') { ?>
                                <n class="badge_padding badge_danger w-max-con text-uppercase">Inactive</n>
                            <?php } ?>
                        </p>
                    </div>
                    <div class="frm_grp span_2">
                        <p class="frm-head">Event Date</p>
                        <p><?= htmlspecialchars(idCardEventDateLabel($row['event_start_date'] ?? null, $row['event_end_date'] ?? null)) ?></p>
                    </div>
                    <div class="frm_grp span_2">
                        <p class="frm-head">Event Location</p>
                        <p><?= !empty($row['event_location']) ? htmlspecialchars($row['event_location']) : '-' ?></p>
                    </div>
                    <div class="frm_grp span_2">
                        <p class="frm-head">Created At</p>
                        <p><?= !empty($row['created_at']) ? date('d-M-Y H:i:s', strtotime($row['created_at'])) : '-' ?></p>
                    </div>
                    <div class="frm_grp span_2">
                        <p class="frm-head">Updated At</p>
                        <p><?= !empty($row['updated_at']) ? date('d-M-Y H:i:s', strtotime($row['updated_at'])) : 'Never' ?></p>
                    </div>
                </div>

                <div class="id-card-wrapper">

                    <div class="id-card-block">
                        <h4>Background Image</h4>
                        <div class="id-card-image-slot">
                            <?php if (!empty($row['background_image'])) { ?>
                                <img src="<?= htmlspecialchars(idCardImageUrl($row['background_image'])) ?>" alt="Background" />
                            <?php } else { ?>
                                <div class="no-image-placeholder">No Image</div>
                            <?php } ?>
                        </div>
                    </div>

                    <div class="id-card-block">
                        <h4>CCICON Logo</h4>
                        <div class="id-card-image-slot">
                            <?php if (!empty($row['header_logo_1'])) { ?>
                                <img src="<?= htmlspecialchars(idCardImageUrl($row['header_logo_1'])) ?>" alt="CCICON Logo" />
                            <?php } else { ?>
                                <div class="no-image-placeholder">No Image</div>
                            <?php } ?>
                        </div>
                    </div>

                    <div class="id-card-block">
                        <h4>CONCLOUD Logo</h4>
                        <div class="id-card-image-slot">
                            <?php if (!empty($row['header_logo_2'])) { ?>
                                <img src="<?= htmlspecialchars(idCardImageUrl($row['header_logo_2'])) ?>" alt="CONCLOUD Logo" />
                            <?php } else { ?>
                                <div class="no-image-placeholder">No Image</div>
                            <?php } ?>
                        </div>
                    </div>

                    <div class="id-card-block">
                        <h4>Venue Logo</h4>
                        <div class="id-card-image-slot">
                            <?php if (!empty($row['header_logo_3'])) { ?>
                                <img src="<?= htmlspecialchars(idCardImageUrl($row['header_logo_3'])) ?>" alt="Venue Logo" />
                            <?php } else { ?>
                                <div class="no-image-placeholder">No Image</div>
                            <?php } ?>
                        </div>
                    </div>

                </div>

                <!-- API Response Preview (matching format) -->
                <div class="venue_hall_top push_noti_top" style="margin-top:20px;">
                    <h5>
                        <n><i class="fal fa-code"></i>API Response Preview (matching format)</n>
                        <l>Ei format e API theke data ashbe.</l>
                    </h5>
                </div>

                <div class="api_preview_wrap">
                    <div class="api_preview_box">
                        <h6>date</h6>
                        <div class="val"><?= htmlspecialchars(idCardEventDateLabel($row['event_start_date'] ?? null, $row['event_end_date'] ?? null)) ?></div>
                    </div>
                    <div class="api_preview_box">
                        <h6>location</h6>
                        <div class="val"><?= htmlspecialchars($row['event_location'] ?? '-') ?></div>
                    </div>
                    <?php foreach (['background_image', 'header_logo_1', 'header_logo_2', 'header_logo_3'] as $f) { ?>
                    <div class="api_preview_box">
                        <h6><?= $f ?></h6>
                        <div class="val" style="font-size:11px;">
                            <a href="<?= htmlspecialchars(idCardImageUrl($row[$f] ?? '')) ?>" target="_blank"><?= htmlspecialchars(idCardImageUrl($row[$f] ?? '')) ?></a>
                        </div>
                    </div>
                    <?php } ?>
                </div>

            </div>

        </div>
    </div>
    <?php
}
?>
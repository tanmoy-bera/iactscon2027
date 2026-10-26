<?php
include_once("./includes/source.php");
include_once('./includes/init.php');
include_once('./includes/function.workshop.php');
?>

<body>
    <?php include_once("./includes/left-menu.php"); ?>
    <header>
        <h2>Manage Floors</h2>
        <?php include_once("./includes/header_right.php"); ?>
    </header>
    <?php
    // One-time flash alert set by floor_list.process.php
    $flash = $_SESSION['floor_alert'] ?? null;
    unset($_SESSION['floor_alert']);
    ?>
    <style>
        .int_alert {
            position: fixed; top: 20px; right: 20px; z-index: 99999;
            min-width: 280px; max-width: 420px; padding: 14px 18px;
            border-radius: 10px; border: 1px solid;
            display: flex; gap: 14px; align-items: center; justify-content: space-between;
            font-size: 14px; box-shadow: 0 10px 30px rgba(0, 0, 0, .6);
        }
        .int_alert.success { color: #34d399; background: #0f1f1a; border-color: rgba(16, 185, 129, .45); }
        .int_alert.error   { color: #f87171; background: #221212; border-color: rgba(239, 68, 68, .45); }
        .int_alert button  { background: none; border: 0; color: inherit; font-size: 20px; line-height: 1; cursor: pointer; }
        /* all column headings centered */
        .table_wrap table thead th { text-align: center !important; }
        /* Action column: center the "..." button */
        .table_wrap table td.action,
        .table_wrap table th.action { text-align: center !important; }
        .table_wrap table td.action .action_div {
            display: flex !important;
            justify-content: center !important;
            align-items: center;
            float: none !important;
            margin-left: auto !important;
            margin-right: auto !important;
            width: 100%;
        }
    </style>
    <?php if (!empty($flash['message'])) { ?>
    <div class="int_alert <?= htmlspecialchars($flash['type']) ?>" id="floorAlert">
        <span>
            <i class="fal <?= ($flash['type'] === 'error') ? 'fa-exclamation-circle' : 'fa-check-circle' ?>"></i>
            <?= htmlspecialchars($flash['message']) ?>
        </span>
        <button type="button" id="floorAlertClose" aria-label="Close">&times;</button>
    </div>
    <?php } ?>

    <div class="body_wrap">
        <?php
        $pageKey    = "_pgn_";
        $pageKeyVal = (empty($_REQUEST[$pageKey])) ? 0 : $_REQUEST[$pageKey];

        $searchString = "";
        $searchArray  = array();
        $searchArray[$pageKey]        = $pageKeyVal;
        $searchArray['search_name']   = $_REQUEST['search_name'] ?? '';
        $searchArray['search_status'] = $_REQUEST['search_status'] ?? '';

        foreach ($searchArray as $searchKey => $searchVal) {
            $searchString .= "&" . $searchKey . "=" . urlencode($searchVal);
        }

        $show = $_REQUEST['show'] ?? '';
        ?>

        <div class="page_top_wrap mb-3">
            <div class="page_top_wrap_left">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="#">Masters</a></li>
                        <li class="breadcrumb-item active" aria-current="page"><a href="#">Manage Floors</a></li>
                    </ol>
                </nav>
                <h2><?= ($show == 'add') ? 'Create New Floor' : (($show == 'edit') ? 'Edit Floor' : 'Floor Listing') ?></h2>
            </div>
            <?php if ($show == '') { ?>
            <div class="page_top_wrap_right">
                <a href="<?= $_SERVER['PHP_SELF'] ?>?show=add<?= $searchString ?>" class="badge_primary"><i class="fal fa-plus"></i> Create New Floor</a>
            </div>
            <?php } ?>
        </div>

        <div class="com_info_wrap">
            <div class="com_info_right" style="width:100%;">
                <?php
                switch ($show) {
                    case 'add':
                        addFloor($cfg, $mycms);
                        break;
                    case 'edit':
                        editFloor($cfg, $mycms);
                        break;
                    default:
                        showAllFloors($cfg, $mycms);
                        break;
                }
                ?>
            </div>
        </div>
    </div>
</body>
<?php include_once("./includes/js-source.php"); ?>
<script>
    $(document).on('click', '.deleteFloorBtn', function () {
        if (confirm('Do you really want to delete this floor?')) {
            window.location.href = $(this).data('href');
        }
    });

    // ---------- Flash alert: auto hide + close ----------
    $(function () {
        if ($('#floorAlert').length) {
            setTimeout(function () { $('#floorAlert').fadeOut(300); }, 4000);
        }
    });
    $(document).on('click', '#floorAlertClose', function () {
        $('#floorAlert').fadeOut(200);
    });

    // ---------- Select / Deselect all ----------
    $(document).on('click', '#check_all', function () {
        $('.checkBoxListing').prop('checked', this.checked);
    });
    $(document).on('change', '.checkBoxListing', function () {
        $('#check_all').prop('checked', $('.checkBoxListing').length === $('.checkBoxListing:checked').length);
    });

    // ---------- Multi action apply ----------
    $(document).on('click', '#applyMultiActionBtn', function () {
        var action   = $('#multiOperationSelector').val();
        var selected = $('.checkBoxListing:checked').map(function () { return $(this).val(); }).get();

        if (action === '') { alert('Please choose an action.'); return; }
        if (selected.length === 0) { alert('Please select at least one floor.'); return; }

        var label = (action === 'Remove') ? 'remove' : 'mark as ' + action.toLowerCase();
        if (confirm('Are you sure you want to ' + label + ' the selected floors?')) {
            $.ajax({
                url: 'floor_list.process.php',
                type: 'POST',
                data: { act: 'MultiAction', action: action, ids: selected },
                complete: function () { location.reload(); }
            });
        }
    });
</script>
</html>
<?php

/*============== SHOW ALL FLOORS ================*/
function showAllFloors($cfg, $mycms)
{
    global $searchArray, $searchString;

    $con    = "";
    $params = [];

    if (!empty($_REQUEST['search_name'])) {
        $con .= " AND f.`floor_name` LIKE ?";
        $params[] = array('FILD' => 'floor_name', 'DATA' => '%' . $_REQUEST['search_name'] . '%', 'TYP' => 's');
    }
    if (!empty($_REQUEST['search_status']) && in_array($_REQUEST['search_status'], array('A', 'I'), true)) {
        $con .= " AND f.`status` = ?";
        $params[] = array('FILD' => 'status', 'DATA' => $_REQUEST['search_status'], 'TYP' => 's');
    }

    $sql = [];
    $sql['QUERY'] = "
        SELECT f.floor_id, f.floor_name, f.status, f.created_at, f.updated_at
        FROM rcg_floor_master f
        WHERE f.status != 'D'
        $con
        ORDER BY f.created_at DESC, f.floor_id DESC
    ";

    foreach ($params as $p) {
        $sql['PARAM'][] = $p;
    }

    $result = $mycms->sql_select_paginated($indexVal, $sql, 20, $restrt);
    ?>
    <div class="com_info_box active" id="floorListing">
        <div class="com_info_box_grid">
            <div class="com_info_box_grid_box">
                <h5 class="com_info_box_head">
                    <n><span class="text_primary"><i class="fal fa-building"></i></span> Floor Listing</n>
                </h5>
                <div class="com_info_box_inner">

                    <!-- Search / filter -->
                    <form method="get" action="<?= $_SERVER['PHP_SELF'] ?>" class="mb-3">
                        <div class="form_grid g_4">
                            <div class="frm_grp">
                                <p class="frm-head">Floor Name</p>
                                <input type="text" name="search_name" value="<?= htmlspecialchars($_REQUEST['search_name'] ?? '') ?>" />
                            </div>
                            <div class="frm_grp">
                                <p class="frm-head">Status</p>
                                <select name="search_status">
                                    <option value="">All</option>
                                    <option value="A" <?= (($_REQUEST['search_status'] ?? '') == 'A') ? 'selected' : '' ?>>Active</option>
                                    <option value="I" <?= (($_REQUEST['search_status'] ?? '') == 'I') ? 'selected' : '' ?>>Inactive</option>
                                </select>
                            </div>
                            <div class="frm_grp">
                                <p class="frm-head">&nbsp;</p>
                                <button type="submit" class="badge_primary"><i class="fal fa-search"></i> Search</button>
                            </div>
                        </div>
                    </form>

                    <div class="table_wrap">
                        <table>
                            <thead>
                                <tr>
                                    <th style="width:36px;text-align:center;"><input name="check_all" id="check_all" type="checkbox" /></th>
                                    <th class="sl">Sl. No.</th>
                                    <th>Floor Name</th>
                                    <th>Status</th>
                                    <th>Created On</th>
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
                                    <td style="text-align:center;"><input type="checkbox" name="selected_ids[]" class="checkBoxListing" value="<?= (int)$row['floor_id'] ?>" /></td>
                                    <td class="sl"><?= $i + ((int)($_REQUEST['_pgn_'] ?? 0) * 20) ?></td>
                                    <td><?= htmlspecialchars($row['floor_name']) ?></td>
                                    <td style="text-align:center;">
                                        <?php if ($row['status'] == 'A') { ?>
                                            <n class="badge_padding badge_success w-max-con text-uppercase">Active</n>
                                        <?php } else { ?>
                                            <n class="badge_padding badge_danger w-max-con text-uppercase">Inactive</n>
                                        <?php } ?>
                                    </td>
                                    <td style="text-align:center;"><?= date('d-m-Y H:i', strtotime($row['created_at'])) ?></td>
                                    <td class="action">
                                        <div class="action_div dropdown" role="menu" aria-label="Actions">
                                            <button class="icon_hover badge_dark action-transparent dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false"><?php ellips(); ?></button>
                                            <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                                                <a href="<?= $_SERVER['PHP_SELF'] ?>?show=edit&floor_id=<?= (int)$row['floor_id'] ?><?= $searchString ?>" class="icon_hover badge_secondary action-transparent"><?php edit(); ?>Edit Details</a>
                                                <?php if ($row['status'] == 'A') { ?>
                                                <a href="floor_list.process.php?act=Inactive&id=<?= (int)$row['floor_id'] ?><?= $searchString ?>" class="icon_hover badge_dark action-transparent"><i class="fal fa-ban"></i> Make Inactive</a>
                                                <?php } else { ?>
                                                <a href="floor_list.process.php?act=Active&id=<?= (int)$row['floor_id'] ?><?= $searchString ?>" class="icon_hover badge_success action-transparent"><i class="fal fa-check"></i> Make Active</a>
                                                <?php } ?>
                                                <a href="javascript:void(0);" class="deleteFloorBtn icon_hover badge_danger action-transparent br-5 w-auto" data-href="floor_list.process.php?act=Delete&id=<?= (int)$row['floor_id'] ?><?= $searchString ?>">
                                                    <i class="fal fa-trash"></i> Delete Floor
                                                </a>
                                            </ul>
                                        </div>
                                    </td>
                                </tr>
                                <?php
                                    }
                                } else {
                                ?>
                                <tr>
                                    <td colspan="6" align="center">
                                        <span class="mandatory">No Record.</span>
                                    </td>
                                </tr>
                                <?php } ?>
                            </tbody>
                        </table>
                    </div>

                    <!-- Bulk actions -->
                    <div class="mt-3 mb-3" style="display:flex;gap:10px;align-items:center;flex-wrap:wrap;">
                        <div class="frm_grp" style="width:240px;margin:0;">
                            <select name="multiOperationSelector" id="multiOperationSelector">
                                <option value="">Choose an action...</option>
                                <option value="Active">Make Active</option>
                                <option value="Inactive">Make Inactive</option>
                                <option value="Remove">Remove</option>
                            </select>
                        </div>
                        <button type="button" class="badge_primary" id="applyMultiActionBtn"><i class="fal fa-check"></i> Apply to selected</button>
                    </div>

                    <div class="bbp-pagination">
                        <div class="bbp-pagination-count"><?= $mycms->paginateRecInfo($indexVal) ?></div>
                        <span class="paginationDisplay">
                            <div class="pagination"><a><?= $mycms->paginate($indexVal, 'pagination') ?></a></div>
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php
}

/*============== ADD FLOOR ================*/
function addFloor($cfg, $mycms)
{
    global $searchArray, $searchString;
    ?>
    <div class="com_info_box active" id="addFloor">
        <form name="frmflooradd" method="post" action="floor_list.process.php" id="frmflooradd" onsubmit="return onSubmitAction();">
            <input type="hidden" name="act" value="add_floor" />
            <?php foreach ($searchArray as $key => $val) { ?>
            <input type="hidden" name="<?= $key ?>" id="<?= $key ?>" value="<?= htmlspecialchars($val) ?>" />
            <?php } ?>
            <div class="com_info_box_grid">
                <div class="com_info_box_grid_box">
                    <h5 class="com_info_box_head">
                        <n><span class="text_success"><i class="fal fa-building"></i></span> Floor Details</n>
                        <div class="page_top_wrap_right">
                            <a href="<?= $_SERVER['PHP_SELF'] ?>?<?= ltrim($searchString, '&') ?>" class="badge_dark"><i class="fal fa-arrow-left"></i> Back</a>
                            <button type="submit" id="Save" name="Save" class="badge_success"><i class="fal fa-save"></i> Save</button>
                        </div>
                    </h5>
                    <div class="com_info_box_inner">
                        <div class="form_grid g_3">
                            <div class="frm_grp span_2">
                                <p class="frm-head">Floor Name <span class="mandatory">*</span></p>
                                <input type="text" name="floor_name" id="floor_name" maxlength="255" class="validate[required]" required />
                            </div>
                            <div class="frm_grp">
                                <p class="frm-head">Status <span class="mandatory">*</span></p>
                                <select name="status" id="status" required>
                                    <option value="A" selected>Active</option>
                                    <option value="I">Inactive</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
    <?php
}

/*============== EDIT FLOOR ================*/
function editFloor($cfg, $mycms)
{
    global $searchArray, $searchString;

    $floorId = (int)($_REQUEST['floor_id'] ?? 0);

    $sql            = array();
    $sql['QUERY']   = "SELECT * FROM `rcg_floor_master` WHERE `floor_id` = ? AND `status` != 'D'";
    $sql['PARAM'][] = array('FILD' => 'floor_id', 'DATA' => $floorId, 'TYP' => 'i');
    $result         = $mycms->sql_select($sql);

    if (empty($result)) {
        echo '<span class="mandatory">Floor not found.</span>';
        return;
    }
    $row = $result[0];
    ?>
    <div class="com_info_box active" id="editFloor">
        <form name="frmflooredit" method="post" action="floor_list.process.php" id="frmflooredit" onsubmit="return onSubmitAction();">
            <input type="hidden" name="act" value="edit_floor" />
            <input type="hidden" name="floor_id" id="floor_id" value="<?= (int)$row['floor_id'] ?>" />
            <?php foreach ($searchArray as $key => $val) { ?>
            <input type="hidden" name="<?= $key ?>" id="<?= $key ?>" value="<?= htmlspecialchars($val) ?>" />
            <?php } ?>
            <div class="com_info_box_grid">
                <div class="com_info_box_grid_box">
                    <h5 class="com_info_box_head">
                        <n><span class="text_success"><i class="fal fa-building"></i></span> Floor Details</n>
                        <div class="page_top_wrap_right">
                            <a href="<?= $_SERVER['PHP_SELF'] ?>?<?= ltrim($searchString, '&') ?>" class="badge_dark"><i class="fal fa-arrow-left"></i> Back</a>
                            <button type="submit" id="Save" name="Save" class="badge_success"><i class="fal fa-save"></i> Save</button>
                        </div>
                    </h5>
                    <div class="com_info_box_inner">
                        <div class="form_grid g_3">
                            <div class="frm_grp span_2">
                                <p class="frm-head">Floor Name <span class="mandatory">*</span></p>
                                <input type="text" name="floor_name" id="floor_name" maxlength="255" value="<?= htmlspecialchars($row['floor_name']) ?>" required />
                            </div>
                            <div class="frm_grp">
                                <p class="frm-head">Status <span class="mandatory">*</span></p>
                                <select name="status" id="status" required>
                                    <option value="A" <?= ($row['status'] == 'A') ? 'selected' : '' ?>>Active</option>
                                    <option value="I" <?= ($row['status'] == 'I') ? 'selected' : '' ?>>Inactive</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
    <?php
}
?>
<?php
include_once("./includes/source.php");
include_once('./includes/init.php');
include_once('./includes/function.workshop.php');

/* ---------- Helpers ---------- */
if (!function_exists('qmv')) {
    function qmv($row, $key, $default = '')
    {
        return (isset($row[$key]) && $row[$key] !== '' && $row[$key] !== null) ? htmlspecialchars($row[$key]) : $default;
    }
}
if (!function_exists('asIconHtml')) {
    /* icon column can hold a Font Awesome class ("fal fa-bell") or an image path / url */
    function asIconHtml($icon, $size = 18)
    {
        $icon = trim((string)$icon);
        if ($icon === '') {
            return '<span class="mandatory">--</span>';
        }
        if (preg_match('/^(https?:)?\/\//i', $icon) || preg_match('/\.(png|jpe?g|svg|gif|webp)(\?.*)?$/i', $icon) || strpos($icon, '/') !== false) {
            return '<img src="' . htmlspecialchars($icon) . '" alt="" style="width:' . (int)$size . 'px;height:' . (int)$size . 'px;object-fit:contain;" />';
        }
        return '<i class="' . htmlspecialchars($icon) . '" style="font-size:' . (int)$size . 'px;"></i>';
    }
}

/* ---------- Request params ---------- */
$qmSelf  = $_SERVER['PHP_SELF'];
$qmPg    = (int)($_REQUEST['_pgn_'] ?? 0);
$qmTitle = trim($_REQUEST['src_title'] ?? '');
$qmType  = trim($_REQUEST['src_type'] ?? '');
$show    = $_REQUEST['show'] ?? '';

$searchString = "&_pgn_=" . $qmPg . "&src_title=" . urlencode($qmTitle) . "&src_type=" . urlencode($qmType);

/* ---------- Edit mode: row load ---------- */
$asRow = [];
if ($show === 'edit' && !empty($_REQUEST['setting_id'])) {
    $sqlE = [];
    $sqlE['QUERY']   = "SELECT * FROM `rcg_notification_settings` WHERE `setting_id` = ? AND `row_status` != 'D'";
    $sqlE['PARAM'][] = ['FILD' => 'setting_id', 'DATA' => (int)$_REQUEST['setting_id'], 'TYP' => 'i'];
    $resE  = $mycms->sql_select($sqlE);
    $asRow = $resE ? $resE[0] : [];
}
$asIsEdit  = !empty($asRow);
$isFormView = ($show === 'add') || $asIsEdit;

/* ---------- Type list (filter + datalist) ---------- */
$sqlT = [];
$sqlT['QUERY'] = "SELECT DISTINCT `type` FROM `rcg_notification_settings` WHERE `type` IS NOT NULL AND `type` != '' AND `row_status` != 'D' ORDER BY `type` ASC";
$asTypes = $mycms->sql_select($sqlT) ?: [];

/* ---------- Defaults for add form ---------- */
$asNextSort = 1;
if (!$asIsEdit && $isFormView) {
    $sqlS = [];
    $sqlS['QUERY'] = "SELECT COALESCE(MAX(`sort_order`), 0) + 1 AS next_sort FROM `rcg_notification_settings` WHERE `row_status` != 'D'";
    $resS = $mycms->sql_select($sqlS);
    $asNextSort = $resS ? (int)$resS[0]['next_sort'] : 1;
}

/* ---------- Listing ---------- */
$asList = [];
if (!$isFormView) {
    $con    = "";
    $params = [];
    if ($qmTitle !== '') {
        $con .= " AND s.`title` LIKE ?";
        $params[] = ['FILD' => 'src_title', 'DATA' => '%' . $qmTitle . '%', 'TYP' => 's'];
    }
    if ($qmType !== '') {
        $con .= " AND s.`type` = ?";
        $params[] = ['FILD' => 'src_type', 'DATA' => $qmType, 'TYP' => 's'];
    }

    $sql = [];
    $sql['QUERY'] = "SELECT s.*
        FROM `rcg_notification_settings` AS s
        WHERE s.`row_status` != ?
        $con
        ORDER BY s.`sort_order` ASC, s.`setting_id` ASC";
    $sql['PARAM'][] = ['FILD' => 'row_status', 'DATA' => 'D', 'TYP' => 's'];
    foreach ($params as $p) { $sql['PARAM'][] = $p; }

    $asList = $mycms->sql_select_paginated($indexVal, $sql, 15, $restrt);
}
?>
<link rel="stylesheet" type="text/css" href="https://ruedakolkata.com/ccicon_2025/webmaster/util/font-awesome-4.7.0/css/font-awesome.min.css" />

<body>
    <?php include_once("./includes/left-menu.php"); ?>
    <header>
        <h2>App Settings</h2>
        <?php include_once("./includes/header_right.php"); ?>
    </header>
    <div class="body_wrap">

        <style>
            .as_preview {
                display: flex;
                align-items: center;
                gap: 14px;
                padding: 14px 16px;
                background: #1a2030;
                border: 1px solid #2a3144;
                border-radius: 18px;
                max-width: 460px;
            }
            .as_preview_icon { width: 36px; text-align: center; color: #38bdf8; flex: 0 0 36px; }
            .as_preview_txt { flex: 1 1 auto; min-width: 0; }
            .as_preview_txt b { display: block; font-size: 15px; color: #fff; }
            .as_preview_txt small { display: block; color: #94a3b8; font-size: 12px; margin-top: 2px; }
            .as_preview_chk {
                width: 28px; height: 28px; border-radius: 8px;
                background: #2563eb; color: #fff;
                display: flex; align-items: center; justify-content: center;
                flex: 0 0 28px;
            }
            .as_preview_chk.off { background: transparent; border: 2px solid #475569; color: transparent; }
            .as_key_small { display: block; font-size: 11px; opacity: .65; }

            /* ===== List table (matches Manage Quizzes look) ===== */
            .as_top { display: flex; justify-content: space-between; align-items: center; gap: 12px; flex-wrap: wrap; margin: 18px 0; }
            .as_filter { display: flex; gap: 8px; flex-wrap: wrap; align-items: center; }
            .as_btn_create {
                display: inline-flex; align-items: center; gap: 8px;
                padding: 12px 18px; background: #0d0d0d; border: 1px solid #262626;
                border-radius: 10px; color: #fff; font-size: 14px; font-weight: 500; text-decoration: none;
            }
            .as_btn_create:hover { background: #1a1a1a; color: #fff; }
            .as_table_wrap { overflow-x: auto; -webkit-overflow-scrolling: touch; }
            .as_table { width: 100%; border-collapse: collapse; font-size: 13px; color: #e5e7eb; }
            .as_table thead th {
                background: #0f0f0f; color: #cbd5e1; font-size: 11px; font-weight: 600;
                text-transform: uppercase; letter-spacing: .04em; padding: 14px 12px;
                text-align: left; white-space: nowrap;
            }
            .as_table tbody td { background: #141414; padding: 14px 12px; border-bottom: 1px solid #1f1f1f; vertical-align: middle; }
            .as_table tbody tr:hover td { background: #1a1a1a; }
            .as_table .as_c { text-align: center; }
            .as_pill {
                display: inline-block; padding: 3px 10px; border-radius: 6px; border: 1px solid;
                font-size: 11px; font-weight: 600; text-transform: uppercase; text-decoration: none;
            }
            .as_pill.ok { color: #34d399; background: rgba(16, 185, 129, .12); border-color: rgba(16, 185, 129, .35); }
            .as_pill.no { color: #f87171; background: rgba(239, 68, 68, .12); border-color: rgba(239, 68, 68, .35); }
            .as_dots { width: 30px; height: 26px; border: 1px solid #333; background: #1c1c1c; color: #fff; border-radius: 6px; cursor: pointer; }
            .as_dots:hover { background: #2a2a2a; }
            .as_menu {
                display: none; position: fixed; z-index: 9999; background: #141414;
                border: 1px solid #2a2a2a; border-radius: 10px; padding: 6px; text-align: left;
                box-shadow: 0 10px 30px rgba(0, 0, 0, .6);
            }
            .as_menu.open { display: block; }
            .as_menu a { display: flex; gap: 10px; align-items: center; padding: 9px 12px; color: #e5e7eb; text-decoration: none; border-radius: 6px; font-size: 13px; }
            .as_menu a:hover { background: #222; }
            .as_menu a.danger { color: #f87171; }
            .as_bulk { display: flex; gap: 8px; align-items: center; margin-top: 16px; }
            .as_foot { margin-top: 8px; }
        </style>

        <div class="page_top_wrap mb-3">
            <div class="page_top_wrap_left">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="#">Conference App</a></li>
                        <li class="breadcrumb-item active" aria-current="page"><a href="#">App Settings</a></li>
                    </ol>
                </nav>
                <h2>App Settings</h2>
                <h6>Manage the preference toggles shown inside the mobile app settings popup.</h6>
            </div>
        </div>

<?php if (!$isFormView) { ?>
        <!-- ==================================================================
             LIST VIEW
             ================================================================== -->
        <div class="as_top">
            <form method="get" action="<?= $qmSelf ?>" class="as_filter">
                <input type="text" name="src_title" value="<?= htmlspecialchars($qmTitle) ?>" placeholder="Search title" />
                <select name="src_type">
                    <option value="">All Types</option>
                    <?php foreach ($asTypes as $t) { ?>
                        <option value="<?= htmlspecialchars($t['type']) ?>" <?= ($qmType === $t['type']) ? 'selected' : '' ?>><?= htmlspecialchars($t['type']) ?></option>
                    <?php } ?>
                </select>
                <button type="submit" class="badge_dark"><i class="fal fa-search"></i> Filter</button>
                <?php if ($qmTitle !== '' || $qmType !== '') { ?>
                    <a href="<?= $qmSelf ?>" class="badge_dark"><i class="fal fa-redo"></i> Reset</a>
                <?php } ?>
            </form>

            <a href="<?= $cfg['SECTION_BASE_URL'] ?>app_setting.php?show=add<?= $searchString ?>" class="as_btn_create">
                <i class="fa fa-plus"></i> Create New Setting
            </a>
        </div>

        <div class="as_table_wrap">
            <table class="as_table">
                <thead>
                    <tr>
                        <th class="as_c" style="width:36px;"><input name="check_all" id="check_all" type="checkbox" /></th>
                        <th class="as_c">Sl No</th>
                        <th class="as_c">Icon</th>
                        <th>Title</th>
                        <th>Type</th>
                        <th>Description</th>
                        <th class="as_c">Default</th>
                        <th class="as_c">Sort</th>
                        <th class="as_c">Status</th>
                        <th class="as_c">Action</th>
                    </tr>
                </thead>
                <tbody>
                <?php if ($asList) {
                    $i = 0;
                    foreach ($asList as $row) {
                        $i++;
                        $rid      = (int)$row['setting_id'];
                        $isActive = ($row['row_status'] == 'A'); ?>
                    <tr>
                        <td class="as_c"><input type="checkbox" name="selected_ids[]" class="checkBoxListing" value="<?= $rid ?>" /></td>
                        <td class="as_c"><?= $i + ($qmPg * 15) ?></td>
                        <td class="as_c"><?= asIconHtml($row['icon']) ?></td>
                        <td>
                            <b><?= htmlspecialchars($row['title']) ?></b>
                            <span class="as_key_small"><?= htmlspecialchars($row['setting_key']) ?></span>
                        </td>
                        <td><?= htmlspecialchars($row['type'] ?? '--') ?></td>
                        <td><?= htmlspecialchars($row['description'] ?? '') ?></td>
                        <td class="as_c">
                            <span class="as_pill <?= ((int)$row['default_value'] === 1) ? 'ok' : 'no' ?>"><?= ((int)$row['default_value'] === 1) ? 'On' : 'Off' ?></span>
                        </td>
                        <td class="as_c"><?= (int)$row['sort_order'] ?></td>
                        <td class="as_c">
                            <a href="<?= $cfg['SECTION_BASE_URL'] ?>app_setting.process.php?act=<?= $isActive ? 'Inactive' : 'Active' ?>&id=<?= $rid ?><?= $searchString ?>"
                               class="as_pill <?= $isActive ? 'ok' : 'no' ?>" title="Click to <?= $isActive ? 'deactivate' : 'activate' ?>">
                                <?= $isActive ? 'Active' : 'Inactive' ?>
                            </a>
                        </td>
                        <td class="as_c">
                            <button type="button" class="as_dots" title="Actions"><i class="fa fa-ellipsis-h"></i></button>
                            <div class="as_menu">
                                <a href="<?= $cfg['SECTION_BASE_URL'] ?>app_setting.php?show=edit&setting_id=<?= $rid ?><?= $searchString ?>">
                                    <i class="fa fa-pencil"></i> Edit
                                </a>
                                <a href="<?= $cfg['SECTION_BASE_URL'] ?>app_setting.process.php?act=Remove&id=<?= $rid ?><?= $searchString ?>"
                                   class="danger" onclick="return confirm('Do you really want to remove this record');">
                                    <i class="fa fa-trash"></i> Remove
                                </a>
                            </div>
                        </td>
                    </tr>
                    <?php }
                } else { ?>
                    <tr>
                        <td colspan="10" class="as_c"><span class="mandatory">No Record.</span></td>
                    </tr>
                <?php } ?>
                </tbody>
            </table>
        </div>

        <div class="as_bulk">
            <select name="multiOperationSelector" id="multiOperationSelector">
                <option value="">Choose an action...</option>
                <option value="Active">Active</option>
                <option value="Inactive">Inactive</option>
                <option value="Remove">Remove</option>
            </select>
            <input value="Apply to selected" type="button" class="btn btn-small btn-blue" id="applyMultiActionBtn" />
        </div>

        <div class="as_foot">
            <span class="paginationRecDisplay"><?= $mycms->paginateRecInfo($indexVal) ?></span>
            <span class="paginationDisplay"><?= $mycms->paginate($indexVal, 'pagination') ?></span>
        </div>

<?php } else {
    $fIcon    = $asRow['icon'] ?? '';
    $fTitle   = $asRow['title'] ?? '';
    $fDesc    = $asRow['description'] ?? '';
    $fDefault = $asIsEdit ? (int)$asRow['default_value'] : 1;
    $fSort    = $asIsEdit ? (int)$asRow['sort_order'] : $asNextSort;
    $fAddData = $asRow['additional_data'] ?? '';
?>
        <!-- ==================================================================
             ADD / EDIT FORM
             ================================================================== -->
        <form name="frmappsetting" method="post" action="<?= $cfg['SECTION_BASE_URL'] ?>app_setting.process.php" id="frmappsetting" onsubmit="return onSubmitSetting();">
            <input type="hidden" name="act" value="<?= $asIsEdit ? 'edit_setting' : 'add_setting' ?>" />
            <?php if ($asIsEdit) { ?>
                <input type="hidden" name="setting_id" id="setting_id" value="<?= (int)$asRow['setting_id'] ?>" />
            <?php } ?>
            <input type="hidden" name="_pgn_" value="<?= $qmPg ?>" />
            <input type="hidden" name="src_title" value="<?= htmlspecialchars($qmTitle) ?>" />
            <input type="hidden" name="src_type" value="<?= htmlspecialchars($qmType) ?>" />

            <div class="basic_grid_box">
                <div class="venue_hall_top push_noti_top">
                    <h5>
                        <n><i class="fal fa-cog"></i><?= $asIsEdit ? 'Edit App Setting' : 'Create New App Setting' ?></n>
                        <l>This row appears as a toggle in the app's settings popup.</l>
                    </h5>
                    <div class="page_top_wrap_right">
                        <a href="<?= $qmSelf ?>?_pgn_=<?= $qmPg ?>&src_title=<?= urlencode($qmTitle) ?>&src_type=<?= urlencode($qmType) ?>" class="badge_dark"><i class="fal fa-arrow-left"></i> Back to List</a>
                        <button type="submit" id="SaveSetting" name="SaveSetting" class="badge_success"><i class="fal fa-save"></i> Save</button>
                    </div>
                </div>

                <div class="form_grid g_6">
                    <div class="frm_grp span_6">
                        <p class="frm-head">Title <span class="mandatory">*</span> <small>(setting key is generated automatically from the title)</small></p>
                        <input type="text" name="title" id="as_title" maxlength="255" value="<?= qmv($asRow, 'title') ?>" required />
                    </div>

                    <div class="frm_grp span_6">
                        <p class="frm-head">Description</p>
                        <textarea name="description" id="as_desc" rows="2"><?= qmv($asRow, 'description') ?></textarea>
                    </div>

                    <div class="frm_grp span_3">
                        <p class="frm-head">Type</p>
                        <input type="text" name="type" id="as_type" maxlength="50" list="as_type_list" value="<?= qmv($asRow, 'type') ?>" />
                        <datalist id="as_type_list">
                            <?php foreach ($asTypes as $t) { ?>
                                <option value="<?= htmlspecialchars($t['type']) ?>"></option>
                            <?php } ?>
                        </datalist>
                    </div>
                    <div class="frm_grp span_3">
                        <p class="frm-head">Icon <small>(Font Awesome class or image URL)</small></p>
                        <input type="text" name="icon" id="as_icon" maxlength="255" placeholder="fal fa-bell" value="<?= qmv($asRow, 'icon') ?>" />
                    </div>

                    <div class="frm_grp span_2">
                        <p class="frm-head">Default Value <span class="mandatory">*</span></p>
                        <select name="default_value" id="as_default" required>
                            <option value="1" <?= $fDefault === 1 ? 'selected' : '' ?>>On (checked)</option>
                            <option value="0" <?= $fDefault === 0 ? 'selected' : '' ?>>Off (unchecked)</option>
                        </select>
                    </div>
                    <div class="frm_grp span_2">
                        <p class="frm-head">Sort Order <span class="mandatory">*</span></p>
                        <input type="number" name="sort_order" id="as_sort" value="<?= $fSort ?>" required />
                    </div>
                    <div class="frm_grp span_2">
                        <p class="frm-head">Status <span class="mandatory">*</span></p>
                        <select name="row_status" id="as_status" required>
                            <option value="A" <?= (!$asIsEdit || $asRow['row_status'] === 'A') ? 'selected' : '' ?>>Active</option>
                            <option value="I" <?= ($asIsEdit && $asRow['row_status'] === 'I') ? 'selected' : '' ?>>Inactive</option>
                        </select>
                    </div>

                    <div class="frm_grp span_6">
                        <p class="frm-head">Additional Data <small>(optional, valid JSON)</small></p>
                        <textarea name="additional_data" id="as_adddata" rows="3" placeholder='{"minutes_before": 10}'><?= htmlspecialchars($fAddData) ?></textarea>
                    </div>

                    <div class="frm_grp span_6">
                        <p class="frm-head">App Preview</p>
                        <div class="as_preview">
                            <div class="as_preview_icon" id="pv_icon"><?= asIconHtml($fIcon, 22) ?></div>
                            <div class="as_preview_txt">
                                <b id="pv_title"><?= htmlspecialchars($fTitle !== '' ? $fTitle : 'Setting title') ?></b>
                                <small id="pv_desc"><?= htmlspecialchars($fDesc !== '' ? $fDesc : 'Setting description') ?></small>
                            </div>
                            <div class="as_preview_chk <?= $fDefault === 1 ? '' : 'off' ?>" id="pv_chk"><i class="fa fa-check"></i></div>
                        </div>
                    </div>
                </div>
            </div>
        </form>
<?php } ?>

    </div>
</body>
<?php include_once("./includes/js-source.php"); ?>
<script>
<?php if (!$isFormView) { ?>

    // ---------- Row action "..." dropdown ----------
    function closeAsMenus() { $('.as_menu').removeClass('open'); }
    $(document).on('click', '.as_dots', function (e) {
        e.stopPropagation();
        var $m = $(this).siblings('.as_menu');
        var wasOpen = $m.hasClass('open');
        closeAsMenus();
        if (!wasOpen) {
            var r = this.getBoundingClientRect();
            var w = 160;
            $m.css({ top: r.bottom + 6, left: Math.max(8, r.right - w), width: w }).addClass('open');
        }
    });
    $(document).on('click', closeAsMenus);
    window.addEventListener('scroll', closeAsMenus, true);
    window.addEventListener('resize', closeAsMenus);

    // ---------- Select / Deselect all ----------
    $('#check_all').on('click', function () {
        $('.checkBoxListing').prop('checked', this.checked);
    });

    // ---------- Multi action apply ----------
    $('#applyMultiActionBtn').on('click', function () {
        var action = $('#multiOperationSelector').val();
        var selected = $('.checkBoxListing:checked').map(function () { return $(this).val(); }).get();

        if (action === '') { alert('Please choose an action.'); return; }
        if (selected.length === 0) { alert('Please select at least one setting.'); return; }

        if (confirm('Are you sure you want to ' + action + ' the selected settings?')) {
            $.ajax({
                url: '<?= $cfg['SECTION_BASE_URL'] ?>app_setting.process.php',
                type: 'POST',
                data: { act: 'MultiAction', action: action, ids: selected },
                complete: function () { location.reload(); }
            });
        }
    });
<?php } else { ?>
    // ---------- Live preview ----------
    function buildIcon(icon) {
        icon = $.trim(icon);
        if (icon === '') return '';
        if (/^(https?:)?\/\//i.test(icon) || /\.(png|jpe?g|svg|gif|webp)(\?.*)?$/i.test(icon) || icon.indexOf('/') > -1) {
            return $('<img>').attr('src', icon).css({ width: 22, height: 22, objectFit: 'contain' });
        }
        return $('<i>').addClass(icon).css('font-size', 22);
    }
    function refreshPreview() {
        $('#pv_title').text($.trim($('#as_title').val()) || 'Setting title');
        $('#pv_desc').text($.trim($('#as_desc').val()) || 'Setting description');
        $('#pv_icon').empty().append(buildIcon($('#as_icon').val()));
        $('#pv_chk').toggleClass('off', $('#as_default').val() !== '1');
    }
    $('#as_title, #as_desc, #as_icon, #as_default').on('input change', refreshPreview);

    // ---------- Validation ----------
    function onSubmitSetting() {
        if ($.trim($('#as_title').val()) === '') {
            alert('Please enter the title.');
            return false;
        }
        var ad = $.trim($('#as_adddata').val());
        if (ad !== '') {
            try { JSON.parse(ad); } catch (e) {
                alert('Additional Data must be valid JSON.');
                return false;
            }
        }
        return true;
    }
<?php } ?>
</script>

</html>
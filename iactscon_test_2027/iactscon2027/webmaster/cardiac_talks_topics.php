<?php
include_once("./includes/source.php");
include_once('./includes/init.php');
include_once('./includes/function.workshop.php');

define('GROUP_COVER_URL', '/webmaster/uploads/groups/');
?>

<body>
    <?php include_once("./includes/left-menu.php"); ?>
    <header>
        <h2>Manage Cardiac Talks Topics</h2>
        <?php include_once("./includes/header_right.php"); ?>
    </header>
    <?php
    // One-time flash alert set by cardiac_talks_topics.process.php
    $flash = $_SESSION['topic_alert'] ?? null;
    unset($_SESSION['topic_alert']);
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
        /* Action column: center the "..." button (theme aligns it right / floats it by default) */
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
    <div class="int_alert <?= htmlspecialchars($flash['type']) ?>" id="intAlert">
        <span>
            <i class="fal <?= ($flash['type'] === 'error') ? 'fa-exclamation-circle' : 'fa-check-circle' ?>"></i>
            <?= htmlspecialchars($flash['message']) ?>
        </span>
        <button type="button" id="intAlertClose" aria-label="Close">&times;</button>
    </div>
    <?php } ?>

    <div class="body_wrap">
        <?php
        $pageKey    = "_pgn_";
        $pageKeyVal = (empty($_REQUEST[$pageKey])) ? 0 : $_REQUEST[$pageKey];

        $searchString = "";
        $searchArray  = array();
        $searchArray[$pageKey]           = $pageKeyVal;
        $searchArray['search_name']      = $_REQUEST['search_name'] ?? '';
        $searchArray['search_pref_type'] = $_REQUEST['search_pref_type'] ?? '';
        $searchArray['search_status']    = $_REQUEST['search_status'] ?? '';

        foreach ($searchArray as $searchKey => $searchVal) {
            $searchString .= "&" . $searchKey . "=" . urlencode($searchVal);
        }

        $show = $_REQUEST['show'] ?? '';
        ?>

        <div class="page_top_wrap mb-3">
            <div class="page_top_wrap_left">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="#">Community</a></li>
                        <li class="breadcrumb-item active" aria-current="page"><a href="#">Cardiac Talks Topics</a></li>
                    </ol>
                </nav>
                <h2><?= ($show == 'add') ? 'Create New Topic' : (($show == 'edit') ? 'Edit Topic' : 'Topic Listing') ?></h2>
            </div>
            <?php if ($show == '') { ?>
            <div class="page_top_wrap_right">
                <a href="<?= $_SERVER['PHP_SELF'] ?>?show=add<?= $searchString ?>" class="badge_primary"><i class="fal fa-plus"></i> Create New Topic</a>
            </div>
            <?php } ?>
        </div>

        <div class="com_info_wrap">
            <div class="com_info_right" style="width:100%;">
                <?php
                switch ($show) {
                    case 'add':
                        addTopic($cfg, $mycms);
                        break;
                    case 'edit':
                        editTopic($cfg, $mycms);
                        break;
                    default:
                        showAllTopics($cfg, $mycms);
                        break;
                }
                ?>
            </div>
        </div>
    </div>
</body>
<?php include_once("./includes/js-source.php"); ?>

<!-- Select2 (remove these two lines if js-source.php / source.php already load it) -->
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<script>
    var searchState = <?= json_encode($searchArray) ?>;

    $(document).on('click', '.deleteTopicBtn', function () {
        if (confirm('Do you really want to delete this topic?')) {
            window.location.href = $(this).data('href');
        }
    });

    // ---------- Flash alert: auto hide + close ----------
    $(function () {
        if ($('#intAlert').length) {
            setTimeout(function () { $('#intAlert').fadeOut(300); }, 4000);
        }
    });
    $(document).on('click', '#intAlertClose', function () {
        $('#intAlert').fadeOut(200);
    });

    // Show the interest selector only for "Interest wise"
    function toggleInterestBox() {
        var isInterest = ($('#preference_type').val() === 'interest');
        $('#interestWrap').toggle(isInterest);
        $('#interest_ids').prop('required', isInterest);
    }

    $(function () {
        if ($('#interest_ids').length) {
            $('#interest_ids').select2({
                width: '100%',
                placeholder: 'Select interests',
                closeOnSelect: false
            });
            toggleInterestBox();
        }
    });

    $(document).on('change', '#preference_type', toggleInterestBox);

    // ---------- Select / Deselect all ----------
    $(document).on('click', '#check_all', function () {
        $('.checkBoxListing').prop('checked', this.checked);
    });
    $(document).on('change', '.checkBoxListing', function () {
        $('#check_all').prop('checked', $('.checkBoxListing').length === $('.checkBoxListing:checked').length);
    });

    // ---------- Multi action apply (normal form post so the flash alert survives the redirect) ----------
    $(document).on('click', '#applyMultiActionBtn', function () {
        var action   = $('#multiOperationSelector').val();
        var selected = $('.checkBoxListing:checked').map(function () { return $(this).val(); }).get();

        if (action === '') { alert('Please choose an action.'); return; }
        if (selected.length === 0) { alert('Please select at least one topic.'); return; }

        var label = (action === 'Remove') ? 'remove' : 'mark as ' + action.toLowerCase();
        if (confirm('Are you sure you want to ' + label + ' the selected topics?')) {
            var $f = $('<form method="post" action="cardiac_talks_topics.process.php"></form>');
            $f.append($('<input type="hidden" name="act" value="MultiAction">'));
            $f.append($('<input type="hidden" name="action">').val(action));
            $.each(selected, function (_, id) {
                $f.append($('<input type="hidden" name="ids[]">').val(id));
            });
            $.each(searchState, function (k, v) {
                $f.append($('<input type="hidden">').attr('name', k).val(v));
            });
            $('body').append($f);
            $f.submit();
        }
    });
</script>
</html>
<?php


/*============== HELPER: INTEREST SECTION (Preference + multi select) ================*/
function getActiveInterests($mycms)
{
    $sql = array();
    $sql['QUERY'] = "SELECT `interest_id`, `name` FROM `rcg_interests`
                     WHERE `status` = 'A' ORDER BY `name` ASC";
    $res = $mycms->sql_select($sql);
    return $res ? $res : array();
}

function renderPreferenceSection($mycms, $prefType = 'all', $selectedIds = array())
{
    $interests = getActiveInterests($mycms);
    ?>
    <div class="frm_grp">
        <p class="frm-head">Preference <span class="mandatory">*</span></p>
        <select name="preference_type" id="preference_type" required>
            <option value="all" <?= ($prefType === 'all') ? 'selected' : '' ?>>All</option>
            <option value="interest" <?= ($prefType === 'interest') ? 'selected' : '' ?>>Interest wise</option>
        </select>
    </div>
    <div class="frm_grp span_2" id="interestWrap" style="<?= ($prefType === 'interest') ? '' : 'display:none;' ?>">
        <p class="frm-head">Interests <span class="mandatory">*</span></p>
        <select name="interest_ids[]" id="interest_ids" multiple="multiple" style="width:100%;">
            <?php foreach ($interests as $in) { ?>
                <option value="<?= (int)$in['interest_id'] ?>" <?= in_array((int)$in['interest_id'], $selectedIds, true) ? 'selected' : '' ?>>
                    <?= htmlspecialchars($in['name']) ?>
                </option>
            <?php } ?>
        </select>
    </div>
    <?php
}

/*============== SHOW ALL TOPICS (public groups only) ================*/
function showAllTopics($cfg, $mycms)
{
    global $searchArray, $searchString;

    $con    = "";
    $params = [];

    if (!empty($_REQUEST['search_name'])) {
        $con .= " AND g.`group_name` LIKE ?";
        $params[] = array('FILD' => 'group_name', 'DATA' => '%' . $_REQUEST['search_name'] . '%', 'TYP' => 's');
    }
    if (!empty($_REQUEST['search_pref_type']) && in_array($_REQUEST['search_pref_type'], array('all', 'interest'), true)) {
        $con .= " AND g.`preference_type` = ?";
        $params[] = array('FILD' => 'preference_type', 'DATA' => $_REQUEST['search_pref_type'], 'TYP' => 's');
    }
    if (!empty($_REQUEST['search_status']) && in_array($_REQUEST['search_status'], array('A', 'I'), true)) {
        $con .= " AND g.`status` = ?";
        $params[] = array('FILD' => 'status', 'DATA' => $_REQUEST['search_status'], 'TYP' => 's');
    }

    $sql = [];
    $sql['QUERY'] = "
        SELECT g.id, g.group_name, g.preference_type, g.cover_image, g.total_members, g.status, g.created_at,
            GROUP_CONCAT(
                DISTINCT i.name
                ORDER BY i.name
                SEPARATOR '||'
            ) AS interests
        FROM rcg_groups g
        LEFT JOIN rcg_group_interest_map gm
            ON g.id = gm.group_id AND gm.status = 'A'
        LEFT JOIN rcg_interests i
            ON i.interest_id = gm.interest_id AND i.status = 'A'
        WHERE g.status != 'D' AND g.group_type = 'public'
        $con
        GROUP BY g.id
        ORDER BY g.id DESC
    ";

    foreach ($params as $p) {
        $sql['PARAM'][] = $p;
    }

    $result = $mycms->sql_select_paginated($indexVal, $sql, 50, $restrt);
    ?>
    <div class="com_info_box active" id="topicListing">
        <div class="com_info_box_grid">
            <div class="com_info_box_grid_box">
                <h5 class="com_info_box_head">
                    <n><span class="text_primary"><i class="fal fa-heartbeat"></i></span> Topic Listing</n>
                </h5>
                <div class="com_info_box_inner">

                    <!-- Search / filter -->
                    <form method="get" action="<?= $_SERVER['PHP_SELF'] ?>" class="mb-3">
                        <div class="form_grid g_4">
                            <div class="frm_grp">
                                <p class="frm-head">Topic Name</p>
                                <input type="text" name="search_name" value="<?= htmlspecialchars($_REQUEST['search_name'] ?? '') ?>" />
                            </div>
                            <div class="frm_grp">
                                <p class="frm-head">Preference</p>
                                <select name="search_pref_type">
                                    <option value="">All</option>
                                    <option value="all"      <?= (($_REQUEST['search_pref_type'] ?? '') == 'all')      ? 'selected' : '' ?>>All (open to everyone)</option>
                                    <option value="interest" <?= (($_REQUEST['search_pref_type'] ?? '') == 'interest') ? 'selected' : '' ?>>Interest wise</option>
                                </select>
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
                                    <th>Cover</th>
                                    <th>Topic Name</th>
                                    <th>Preference</th>
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
                                    <td style="text-align:center;"><input type="checkbox" name="selected_ids[]" class="checkBoxListing" value="<?= (int)$row['id'] ?>" /></td>
                                    <td class="sl"><?= $i + ((int)($_REQUEST['_pgn_'] ?? 0) * 50) ?></td>
                                    <td style="text-align:center;">
                                        <?php if (!empty($row['cover_image'])) { ?>
                                           <img src="<?= GROUP_COVER_URL . htmlspecialchars($row['cover_image']) ?>" alt="cover" style="width:50px;height:50px;object-fit:cover;border-radius:6px;" />
                                        <?php } else { echo '—'; } ?>
                                    </td>
                                    <td><?= htmlspecialchars($row['group_name']) ?></td>
                                    <td style="text-align:center;">
                                        <?php
                                        if ($row['preference_type'] === 'interest') {
                                            if (!empty($row['interests'])) {
                                                echo '<div style="display:flex;flex-wrap:wrap;justify-content:center;gap:6px;max-width:320px;margin:0 auto;">';
                                                foreach (explode('||', $row['interests']) as $iname) {
                                                    echo '<n class="badge_padding badge_secondary w-max-con" style="display:inline-block;line-height:1.4;">' . htmlspecialchars($iname) . '</n>';
                                                }
                                                echo '</div>';
                                            } else {
                                                echo '<n class="badge_padding badge_secondary w-max-con">Interest wise</n>';
                                            }
                                        } else {
                                            echo '<n class="badge_padding badge_primary w-max-con text-uppercase">All</n>';
                                        }
                                        ?>
                                    </td>
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
                                                <a href="<?= $_SERVER['PHP_SELF'] ?>?show=edit&group_id=<?= (int)$row['id'] ?><?= $searchString ?>" class="icon_hover badge_secondary action-transparent"><?php edit(); ?>Edit Details</a>
                                                <a href="group_members_list.php?group_id=<?= (int)$row['id'] ?>" class="icon_hover badge_success action-transparent"><i class="fal fa-users"></i> Members</a>
                                                <?php if ($row['status'] == 'A') { ?>
                                                <a href="cardiac_talks_topics.process.php?act=Inactive&id=<?= (int)$row['id'] ?><?= $searchString ?>" class="icon_hover badge_dark action-transparent"><i class="fal fa-ban"></i> Make Inactive</a>
                                                <?php } else { ?>
                                                <a href="cardiac_talks_topics.process.php?act=Active&id=<?= (int)$row['id'] ?><?= $searchString ?>" class="icon_hover badge_success action-transparent"><i class="fal fa-check"></i> Make Active</a>
                                                <?php } ?>
                                                <a href="javascript:void(0);" class="deleteTopicBtn icon_hover badge_danger action-transparent br-5 w-auto" data-href="cardiac_talks_topics.process.php?act=Delete&id=<?= (int)$row['id'] ?><?= $searchString ?>">
                                                    <i class="fal fa-trash"></i> Delete Topic
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
                                    <td colspan="8" align="center">
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

/*============== ADD TOPIC ================*/
function addTopic($cfg, $mycms)
{
    global $searchArray, $searchString;
    ?>
    <div class="com_info_box active" id="addTopic">
        <form name="frmtopicadd" method="post" action="cardiac_talks_topics.process.php" id="frmtopicadd" enctype="multipart/form-data" onsubmit="return onSubmitAction();">
            <input type="hidden" name="act" value="add_topic" />
            <?php
            foreach ($searchArray as $key => $val) {
            ?>
            <input type="hidden" name="<?= $key ?>" id="<?= $key ?>" value="<?= htmlspecialchars($val) ?>" />
            <?php
            }
            ?>
            <div class="com_info_box_grid">
                <div class="com_info_box_grid_box">
                    <h5 class="com_info_box_head">
                        <n><span class="text_success"><i class="fal fa-heartbeat"></i></span> Topic Details</n>
                        <div class="page_top_wrap_right">
                            <a href="<?= $_SERVER['PHP_SELF'] ?>?<?= ltrim($searchString, '&') ?>" class="badge_dark"><i class="fal fa-arrow-left"></i> Back</a>
                            <button type="submit" id="Save" name="Save" class="badge_success"><i class="fal fa-save"></i> Save</button>
                        </div>
                    </h5>
                    <div class="com_info_box_inner">
                        <div class="form_grid g_3">
                            <div class="frm_grp span_2">
                                <p class="frm-head">Topic Name <span class="mandatory">*</span></p>
                                <input type="text" name="group_name" id="group_name" maxlength="255" class="validate[required]" required />
                            </div>
                            <div class="frm_grp">
                                <p class="frm-head">Status <span class="mandatory">*</span></p>
                                <select name="status" id="status" required>
                                    <option value="A" selected>Active</option>
                                    <option value="I">Inactive</option>
                                </select>
                            </div>
                            <div class="frm_grp span_2">
                                <p class="frm-head">Description</p>
                                <textarea name="group_description" id="group_description" rows="4"></textarea>
                            </div>
                            <div class="frm_grp">
                                <p class="frm-head">Cover Image</p>
                                <input type="file" name="cover_image" id="cover_image" accept="image/*" />
                            </div>
                        </div>
                    </div>
                </div>
                <div class="com_info_box_grid_box">
                    <h5 class="com_info_box_head">
                        <n><span class="text_primary"><i class="fal fa-tags"></i></span> Topic Preference</n>
                    </h5>
                    <div class="com_info_box_inner">
                        <div class="form_grid g_3">
                            <?php renderPreferenceSection($mycms, 'all', array()); ?>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
    <?php
}

/*============== EDIT TOPIC ================*/
function editTopic($cfg, $mycms)
{
    global $searchArray, $searchString;

    $groupId = (int)($_REQUEST['group_id'] ?? 0);

    $sql            = array();
    $sql['QUERY']   = "SELECT * FROM `rcg_groups` WHERE `id` = ? AND `status` != 'D' AND `group_type` = 'public'";
    $sql['PARAM'][] = array('FILD' => 'id', 'DATA' => $groupId, 'TYP' => 'i');
    $result         = $mycms->sql_select($sql);

    if (empty($result)) {
        echo '<span class="mandatory">Topic not found.</span>';
        return;
    }
    $row = $result[0];

    // Already saved interests of this topic
    $sql_map            = array();
    $sql_map['QUERY']   = "SELECT `interest_id` FROM `rcg_group_interest_map` WHERE `group_id` = ? AND `status` = 'A'";
    $sql_map['PARAM'][] = array('FILD' => 'group_id', 'DATA' => $row['id'], 'TYP' => 'i');
    $result_map         = $mycms->sql_select($sql_map);

    $selectedIds = array();
    if ($result_map) {
        foreach ($result_map as $m) {
            $selectedIds[] = (int)$m['interest_id'];
        }
    }
    ?>
    <div class="com_info_box active" id="editTopic">
        <form name="frmtopicedit" method="post" action="cardiac_talks_topics.process.php" id="frmtopicedit" enctype="multipart/form-data" onsubmit="return onSubmitAction();">
            <input type="hidden" name="act" value="edit_topic" />
            <input type="hidden" name="group_id" id="group_id" value="<?= (int)$row['id'] ?>" />
            <input type="hidden" name="old_cover_image" value="<?= htmlspecialchars($row['cover_image'] ?? '') ?>" />
            <?php
            foreach ($searchArray as $key => $val) {
            ?>
            <input type="hidden" name="<?= $key ?>" id="<?= $key ?>" value="<?= htmlspecialchars($val) ?>" />
            <?php
            }
            ?>
            <div class="com_info_box_grid">
                <div class="com_info_box_grid_box">
                    <h5 class="com_info_box_head">
                        <n><span class="text_success"><i class="fal fa-heartbeat"></i></span> Topic Details</n>
                        <div class="page_top_wrap_right">
                            <a href="<?= $_SERVER['PHP_SELF'] ?>?<?= ltrim($searchString, '&') ?>" class="badge_dark"><i class="fal fa-arrow-left"></i> Back</a>
                            <button type="submit" id="Save" name="Save" class="badge_success"><i class="fal fa-save"></i> Save</button>
                        </div>
                    </h5>
                    <div class="com_info_box_inner">
                        <div class="form_grid g_3">
                            <div class="frm_grp span_2">
                                <p class="frm-head">Topic Name <span class="mandatory">*</span></p>
                                <input type="text" name="group_name" id="group_name" maxlength="255" value="<?= htmlspecialchars($row['group_name']) ?>" required />
                            </div>
                            <div class="frm_grp">
                                <p class="frm-head">Status <span class="mandatory">*</span></p>
                                <select name="status" id="status" required>
                                    <option value="A" <?= ($row['status'] == 'A') ? 'selected' : '' ?>>Active</option>
                                    <option value="I" <?= ($row['status'] == 'I') ? 'selected' : '' ?>>Inactive</option>
                                </select>
                            </div>
                            <div class="frm_grp span_2">
                                <p class="frm-head">Description</p>
                                <textarea name="group_description" id="group_description" rows="4"><?= htmlspecialchars($row['group_description'] ?? '') ?></textarea>
                            </div>
                            <div class="frm_grp">
                                <p class="frm-head">Cover Image</p>
                                <?php if (!empty($row['cover_image'])) { ?>
                                  <img src="<?= GROUP_COVER_URL . htmlspecialchars($row['cover_image']) ?>" alt="cover" style="width:80px;height:80px;object-fit:cover;border-radius:6px;margin-bottom:8px;" />
                                <?php } ?>
                                <input type="file" name="cover_image" id="cover_image" accept="image/*" />
                            </div>
                        </div>
                    </div>
                </div>
                <div class="com_info_box_grid_box">
                    <h5 class="com_info_box_head">
                        <n><span class="text_primary"><i class="fal fa-tags"></i></span> Topic Preference</n>
                    </h5>
                    <div class="com_info_box_inner">
                        <div class="form_grid g_3">
                            <?php renderPreferenceSection($mycms, $row['preference_type'] ?? 'all', $selectedIds); ?>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
    <?php
}
?>
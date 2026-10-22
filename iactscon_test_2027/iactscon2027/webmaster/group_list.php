<?php
include_once("./includes/source.php");
include_once('./includes/init.php');
include_once('./includes/function.workshop.php');

define('GROUP_COVER_URL', '/webmaster/uploads/groups/');
?>

<body>
    <?php include_once("./includes/left-menu.php"); ?>
    <header>
        <h2>Manage Groups</h2>
        <?php include_once("./includes/header_right.php"); ?>
    </header>
    <div class="body_wrap">
        <?php
        $pageKey    = "_pgn_";
        $pageKeyVal = (empty($_REQUEST[$pageKey])) ? 0 : $_REQUEST[$pageKey];

        $searchString = "";
        $searchArray  = array();
        $searchArray[$pageKey]      = $pageKeyVal;
        $searchArray['search_name'] = $_REQUEST['search_name'] ?? '';
        $searchArray['search_type'] = $_REQUEST['search_type'] ?? '';
        $searchArray['search_pref_type'] = $_REQUEST['search_pref_type'] ?? '';

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
                        <li class="breadcrumb-item active" aria-current="page"><a href="#">Manage Groups</a></li>
                    </ol>
                </nav>
                <h2><?= ($show == 'add') ? 'Create New Group' : (($show == 'edit') ? 'Edit Group' : 'Group Listing') ?></h2>
            
            </div>
            <?php if ($show == '') { ?>
            <div class="page_top_wrap_right">
                <a href="<?= $_SERVER['PHP_SELF'] ?>?show=add<?= $searchString ?>" class="badge_primary"><i class="fal fa-plus"></i> Create New Group</a>
            </div>
            <?php } ?>
        </div>

        <div class="com_info_wrap">
            <div class="com_info_right" style="width:100%;">
                <?php
                switch ($show) {
                    case 'add':
                        addGroup($cfg, $mycms);
                        break;
                    case 'edit':
                        editGroup($cfg, $mycms);
                        break;
                    default:
                        showAllGroups($cfg, $mycms);
                        break;
                }
                ?>
            </div>
        </div>
    </div>
</body>
<?php include_once("./includes/js-source.php"); ?>
<script>
    $(document).on('click', '.deleteGroupBtn', function () {
        if (confirm('Do you really want to delete this group?')) {
            window.location.href = $(this).data('href');
        }
    });

    $(document).on('click', '#addPrefRow', function () {
        $('#prefWrap').append($('#prefRowTemplate').html());
    });
    $(document).on('click', '.removePrefRow', function () {
        var row = $(this).closest('.pref_row');
        if ($('#prefWrap .pref_row').length > 1) {
            row.remove();
        } else {
            row.find('input').val('');
            row.find('select').val('');
        }
    });
</script>
</html>
<?php


/*============== HELPER: PREFERENCE ROWS (Type select + Name text) ================*/
function preferenceRowHtml($type = '', $name = '')
{
    $types = array('university' => 'University', 'institute' => 'Institute', 'specialty' => 'Specialty');

    $html  = '<div class="pref_row" style="display:flex;gap:10px;align-items:center;margin-bottom:10px;">';
    $html .= '<select name="pref_type[]" style="max-width:220px;">';
    $html .= '<option value="">-- Select Type --</option>';
    foreach ($types as $key => $label) {
        $sel   = ($type === $key) ? ' selected' : '';
        $html .= '<option value="' . $key . '"' . $sel . '>' . $label . '</option>';
    }
    $html .= '</select>';
    $html .= '<input type="text" name="pref_name[]" maxlength="255" placeholder="Preference name (e.g. AIIMS Delhi, Cardiology)" value="' . htmlspecialchars($name) . '" style="flex:1;" />';
    $html .= '<button type="button" class="removePrefRow badge_danger"><i class="fal fa-times"></i></button>';
    $html .= '</div>';

    return $html;
}

function renderPreferenceRows($rows = array())
{
    if (empty($rows)) {
        $rows = array(array('type' => '', 'name' => ''));
    }
    ?>
    <div class="frm_grp" style="grid-column:1 / -1;">
        <p class="frm-head">Select the type and type the name. New preferences are created automatically.</p>
        <div id="prefWrap">
            <?php
            foreach ($rows as $r) {
                echo preferenceRowHtml($r['type'], $r['name']);
            }
            ?>
        </div>
        <button type="button" id="addPrefRow" class="badge_primary"><i class="fal fa-plus"></i> Add Another Preference</button>
        <template id="prefRowTemplate"><?= preferenceRowHtml() ?></template>
    </div>
    <?php
}

/*============== SHOW ALL GROUPS ================*/
function showAllGroups($cfg, $mycms)
{
    global $searchArray, $searchString;

    $con    = "";
    $params = [];

    if (!empty($_REQUEST['search_name'])) {
        $con .= " AND g.`group_name` LIKE ?";
        $params[] = array('FILD' => 'group_name', 'DATA' => '%' . $_REQUEST['search_name'] . '%', 'TYP' => 's');
    }
    if (!empty($_REQUEST['search_type'])) {
        $con .= " AND g.`group_type` = ?";
        $params[] = array('FILD' => 'group_type', 'DATA' => $_REQUEST['search_type'], 'TYP' => 's');
    }
      if (!empty($_REQUEST['search_pref_type'])) {
        $con .= " AND EXISTS (
                    SELECT 1 FROM `rcg_group_preference_map` gm2
                    INNER JOIN `rcg_group_preferences` gp2 ON gp2.`id` = gm2.`preference_id`
                    WHERE gm2.`group_id` = g.`id`
                      AND gp2.`status` = 'A'
                      AND gp2.`preference_type` = ?
                  )";
        $params[] = array('FILD' => 'preference_type', 'DATA' => $_REQUEST['search_pref_type'], 'TYP' => 's');
    }

    $sql = [];
    $sql['QUERY'] = "
        SELECT g.id, g.group_name, g.group_type, g.cover_image, g.total_members, g.status, g.created_at,
            GROUP_CONCAT(
                DISTINCT CONCAT(gp.preference_type, '::', gp.preference_name)
                ORDER BY gp.preference_type, gp.preference_name
                SEPARATOR '||'
            ) AS preferences
        FROM rcg_groups g
        LEFT JOIN rcg_group_preference_map gm
            ON g.id = gm.group_id
        LEFT JOIN rcg_group_preferences gp
            ON gp.id = gm.preference_id AND gp.status = 'A'
        WHERE g.status != 'D'
        $con
        GROUP BY g.id
        ORDER BY g.id DESC
    ";

    foreach ($params as $p) {
        $sql['PARAM'][] = $p;
    }

    $result = $mycms->sql_select_paginated($indexVal, $sql, 50, $restrt);
    ?>
    <div class="com_info_box active" id="groupListing">
        <div class="com_info_box_grid">
            <div class="com_info_box_grid_box">
                <h5 class="com_info_box_head">
                    <n><span class="text_primary"><i class="fal fa-users"></i></span> Group Listing</n>
                </h5>
                <div class="com_info_box_inner">

                    <!-- Search / filter -->
                  <form method="get" action="<?= $_SERVER['PHP_SELF'] ?>" class="mb-3">
    <div class="form_grid g_4">
        <div class="frm_grp">
            <p class="frm-head">Group Name</p>
            <input type="text" name="search_name" value="<?= htmlspecialchars($_REQUEST['search_name'] ?? '') ?>" />
        </div>
        <div class="frm_grp">
            <p class="frm-head">Group Visibility</p>
            <select name="search_type">
                <option value="">All</option>
                <option value="public"  <?= (($_REQUEST['search_type'] ?? '') == 'public')  ? 'selected' : '' ?>>Public</option>
                <option value="private" <?= (($_REQUEST['search_type'] ?? '') == 'private') ? 'selected' : '' ?>>Private</option>
            </select>
        </div>
        <div class="frm_grp">
            <p class="frm-head">Preference Type</p>
            <select name="search_pref_type">
                <option value="">All</option>
                <option value="university" <?= (($_REQUEST['search_pref_type'] ?? '') == 'university') ? 'selected' : '' ?>>University</option>
                <option value="institute"  <?= (($_REQUEST['search_pref_type'] ?? '') == 'institute')  ? 'selected' : '' ?>>Institute</option>
                <option value="specialty"  <?= (($_REQUEST['search_pref_type'] ?? '') == 'specialty')  ? 'selected' : '' ?>>Specialty</option>
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
                                    <th class="sl">#</th>
                                    <th>Cover</th>
                                    <th>Group Name</th>
                                    <th>Visibility</th>
                                    <th>Preferences</th>
                                    
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
                                    <td class="sl"><?= $i + ((int)($_REQUEST['_pgn_'] ?? 0) * 50) ?></td>
                                    <td>
                                        <?php if (!empty($row['cover_image'])) { ?>
                                           <img src="<?= GROUP_COVER_URL . htmlspecialchars($row['cover_image']) ?>" alt="cover" style="width:50px;height:50px;object-fit:cover;border-radius:6px;" />
                                        <?php } else { echo '—'; } ?>
                                    </td>
                                    <td><?= htmlspecialchars($row['group_name']) ?></td>
                                    <td>
                                        <?php if ($row['group_type'] == 'public') { ?>
                                            <n class="badge_padding badge_success w-max-con text-uppercase">Public</n>
                                        <?php } else { ?>
                                            <n class="badge_padding badge_dark w-max-con text-uppercase">Private</n>
                                        <?php } ?>
                                    </td>
                                    <td>
                                        <?php
                                        if (!empty($row['preferences'])) {
                                            $prefs = explode('||', $row['preferences']);
                                            foreach ($prefs as $pref) {
                                                list($ptype, $pname) = explode('::', $pref, 2);
                                                echo '<n class="badge_padding badge_secondary w-max-con">' . ucfirst($ptype) . ': ' . htmlspecialchars($pname) . '</n><br>';
                                            }
                                        } else {
                                            echo '—';
                                        }
                                        ?>
                                    </td>
                                  
                                    <td>
                                        <?php if ($row['status'] == 'A') { ?>
                                            <n class="badge_padding badge_success w-max-con text-uppercase">Active</n>
                                        <?php } else { ?>
                                            <n class="badge_padding badge_danger w-max-con text-uppercase">Inactive</n>
                                        <?php } ?>
                                    </td>
                                    <td><?= date('d-m-Y H:i', strtotime($row['created_at'])) ?></td>
                                    <td class="action">
                                        <div class="action_div dropdown" role="menu" aria-label="Actions">
                                            <button class="icon_hover badge_dark action-transparent dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false"><?php ellips(); ?></button>
                                            <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                                                <a href="<?= $_SERVER['PHP_SELF'] ?>?show=edit&group_id=<?= $row['id'] ?><?= $searchString ?>" class="icon_hover badge_secondary action-transparent"><?php edit(); ?>Edit Details</a>
                                                <a href="javascript:void(0);" class="deleteGroupBtn icon_hover badge_danger action-transparent br-5 w-auto" data-href="group_list.process.php?act=Delete&id=<?= $row['id'] ?>">
                                                    <i class="fal fa-trash"></i> Delete Group
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
                                    <td colspan="9" align="center">
                                        <span class="mandatory">No Record.</span>
                                    </td>
                                </tr>
                                <?php } ?>
                            </tbody>
                        </table>
                    </div>
                    <div class="paginationDisplay">
                        <span class="paginationRecDisplay"><?= $mycms->paginateRecInfo($indexVal) ?></span>
                        <span class="paginationDisplay"><?= $mycms->paginate($indexVal, 'pagination') ?></span>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php
}

/*============== ADD GROUP ================*/
function addGroup($cfg, $mycms)
{
    global $searchArray, $searchString;
    ?>
    <div class="com_info_box active" id="addGroup">
        <form name="frmtypeadd" method="post" action="group_list.process.php" id="frmtypeadd" enctype="multipart/form-data" onsubmit="return onSubmitAction();">
            <input type="hidden" name="act" value="add_group" />
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
                        <n><span class="text_success"><i class="fal fa-users"></i></span> Group Details</n>
                        <div class="page_top_wrap_right">
                            <a href="<?= $_SERVER['PHP_SELF'] ?>?<?= ltrim($searchString, '&') ?>" class="badge_dark"><i class="fal fa-arrow-left"></i> Back</a>
                            <button type="submit" id="Save" name="Save" class="badge_success"><i class="fal fa-save"></i> Save</button>
                        </div>
                    </h5>
                    <div class="com_info_box_inner">
                        <div class="form_grid g_3">
                            <div class="frm_grp span_2">
                                <p class="frm-head">Group Name <span class="mandatory">*</span></p>
                                <input type="text" name="group_name" id="group_name" maxlength="255" class="validate[required]" required />
                            </div>
                            <div class="frm_grp">
                                <p class="frm-head">Group Visibility <span class="mandatory">*</span></p>
                                <select name="group_type" id="group_type" required>
                                    <option value="public" selected>Public</option>
                                    <option value="private">Private</option>
                                </select>
                            </div>
                            <div class="frm_grp span_2">
                                <p class="frm-head">Description</p>
                                <textarea name="group_description" id="group_description" rows="4"></textarea>
                            </div>
                            <div class="frm_grp">
                                <p class="frm-head">Status <span class="mandatory">*</span></p>
                                <select name="status" id="status" required>
                                    <option value="A" selected>Active</option>
                                    <option value="I">Inactive</option>
                                </select>
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
                        <n><span class="text_primary"><i class="fal fa-tags"></i></span> Group Preferences (Type + Name)</n>
                    </h5>
                    <div class="com_info_box_inner">
                        <div class="form_grid g_3">
                            <?php renderPreferenceRows(array()); ?>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
    <?php
}

/*============== EDIT GROUP ================*/
function editGroup($cfg, $mycms)
{
    global $searchArray, $searchString;

    $groupId = (int)($_REQUEST['group_id'] ?? 0);

    $sql            = array();
    $sql['QUERY']   = "SELECT * FROM `rcg_groups` WHERE `id` = ? AND `status` != 'D'";
    $sql['PARAM'][] = array('FILD' => 'id', 'DATA' => $groupId, 'TYP' => 's');
    $result         = $mycms->sql_select($sql);

    if (empty($result)) {
        echo '<span class="mandatory">Group not found.</span>';
        return;
    }
    $row = $result[0];

    // Already saved preferences of this group (type + name)
    $sql_map            = array();
    $sql_map['QUERY']   = "SELECT gp.`preference_type`, gp.`preference_name`
                           FROM `rcg_group_preference_map` gm
                           INNER JOIN `rcg_group_preferences` gp ON gp.`id` = gm.`preference_id`
                           WHERE gm.`group_id` = ?
                           ORDER BY gm.`id` ASC";
    $sql_map['PARAM'][] = array('FILD' => 'group_id', 'DATA' => $row['id'], 'TYP' => 's');
    $result_map         = $mycms->sql_select($sql_map);

    $prefRows = array();
    if ($result_map) {
        foreach ($result_map as $m) {
            $prefRows[] = array('type' => $m['preference_type'], 'name' => $m['preference_name']);
        }
    }
    ?>
    <div class="com_info_box active" id="editGroup">
        <form name="frmtypeedit" method="post" action="group_list.process.php" id="frmtypeedit" enctype="multipart/form-data" onsubmit="return onSubmitAction();">
            <input type="hidden" name="act" value="edit_group" />
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
                        <n><span class="text_success"><i class="fal fa-users"></i></span> Group Details</n>
                        <div class="page_top_wrap_right">
                            <a href="<?= $_SERVER['PHP_SELF'] ?>?<?= ltrim($searchString, '&') ?>" class="badge_dark"><i class="fal fa-arrow-left"></i> Back</a>
                            <button type="submit" id="Save" name="Save" class="badge_success"><i class="fal fa-save"></i> Save</button>
                        </div>
                    </h5>
                    <div class="com_info_box_inner">
                        <div class="form_grid g_3">
                            <div class="frm_grp span_2">
                                <p class="frm-head">Group Name <span class="mandatory">*</span></p>
                                <input type="text" name="group_name" id="group_name" maxlength="255" value="<?= htmlspecialchars($row['group_name']) ?>" required />
                            </div>
                            <div class="frm_grp">
                                <p class="frm-head">Group Visibility <span class="mandatory">*</span></p>
                                <select name="group_type" id="group_type" required>
                                    <option value="public"  <?= ($row['group_type'] == 'public')  ? 'selected' : '' ?>>Public</option>
                                    <option value="private" <?= ($row['group_type'] == 'private') ? 'selected' : '' ?>>Private</option>
                                </select>
                            </div>
                            <div class="frm_grp span_2">
                                <p class="frm-head">Description</p>
                                <textarea name="group_description" id="group_description" rows="4"><?= htmlspecialchars($row['group_description'] ?? '') ?></textarea>
                            </div>
                            <div class="frm_grp">
                                <p class="frm-head">Status <span class="mandatory">*</span></p>
                                <select name="status" id="status" required>
                                    <option value="A" <?= ($row['status'] == 'A') ? 'selected' : '' ?>>Active</option>
                                    <option value="I" <?= ($row['status'] == 'I') ? 'selected' : '' ?>>Inactive</option>
                                </select>
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
                        <n><span class="text_primary"><i class="fal fa-tags"></i></span> Group Preferences (Type + Name)</n>
                    </h5>
                    <div class="com_info_box_inner">
                        <div class="form_grid g_3">
                            <?php renderPreferenceRows($prefRows); ?>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
    <?php
}
?>
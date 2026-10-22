<?php
include_once("./includes/source.php");
include_once('./includes/init.php');
include_once('./includes/function.workshop.php');

/* ---------- Helpers ---------- */
if (!function_exists('plv')) {
    function plv($row, $key, $default = '')
    {
        return (isset($row[$key]) && $row[$key] !== '' && $row[$key] !== null) ? htmlspecialchars($row[$key]) : $default;
    }
}
if (!function_exists('pldt')) {
    // MySQL datetime -> value accepted by <input type="datetime-local">
    function pldt($val)
    {
        return (!empty($val) && strtotime($val)) ? date('Y-m-d\TH:i', strtotime($val)) : '';
    }
}
if (!function_exists('pldisp')) {
    function pldisp($val, $fmt = 'd M Y, H:i')
    {
        return (!empty($val) && strtotime($val)) ? date($fmt, strtotime($val)) : '--';
    }
}

/* ---------- Request params ---------- */
$pageKey    = "_pgn_";
$pageKeyVal = (($_REQUEST[$pageKey] ?? '') == "") ? 0 : (int)$_REQUEST[$pageKey];

@$searchString = "";
$searchArray   = array();

$searchArray[$pageKey]   = $pageKeyVal;
$searchArray['start_date'] = $_REQUEST['start_date'] ?? '';

foreach ($searchArray as $searchKey => $searchVal) {
    $searchString .= "&" . $searchKey . "=" . urlencode($searchVal);
}

if (!isset($show)) {
    $show = $_REQUEST['show'] ?? '';
}

$plPageTitle = ($show == 'add') ? 'Create New Poll' : (($show == 'edit') ? 'Edit Poll' : 'Polls');
?>

<body>
    <?php include_once("./includes/left-menu.php"); ?>
    <header>
        <h2>Polls</h2>
        <?php include_once("./includes/header_right.php"); ?>
    </header>

    <div class="body_wrap">

        <div class="page_top_wrap mb-3">
            <div class="page_top_wrap_left">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="#">Conference App</a></li>
                        <li class="breadcrumb-item<?= ($show == 'add' || $show == 'edit') ? '' : ' active' ?>"><a href="<?= $_SERVER['PHP_SELF'] ?>">Polls</a></li>
                        <?php if ($show == 'add' || $show == 'edit') { ?>
                            <li class="breadcrumb-item active" aria-current="page"><?= $plPageTitle ?></li>
                        <?php } ?>
                    </ol>
                </nav>
                <h2><?= $plPageTitle ?></h2>
                <h6>Create live audience polls, publish them, track votes and reset results.</h6>
            </div>
        </div>

        <?php
        switch ($show) {
            case 'add':
                addPoll($cfg, $mycms);
                break;
            case 'edit':
                editPoll($cfg, $mycms);
                break;
            default:
                showAllPolls($cfg, $mycms);
                break;
        }
        ?>

    </div>
    <?php include_once("./includes/popup.php"); ?>
</body>
<?php include_once("./includes/js-source.php"); ?>
<script>
    $('#check_all').on('click', function () {
        $('.checkBoxListing').prop('checked', this.checked);
    });

    $('#applyMultiActionBtn').on('click', function () {
        var action = $('#multiOperationSelector').val();
        var selected = $('.checkBoxListing:checked').map(function () { return $(this).val(); }).get();

        if (action === '') { alert('Please choose an action.'); return; }
        if (selected.length === 0) { alert('Please select at least one poll.'); return; }

        if (confirm('Are you sure you want to ' + action + ' the selected polls?')) {
            $.ajax({
                url: 'manage_poll.process.php',
                type: 'POST',
                data: { act: 'MultiAction', action: action, ids: selected },
                complete: function () { window.location.reload(); }
            });
        }
    });
</script>

</html>
<?php

/*============== SHOW ALL POLLS WINDOW =============*/
function showAllPolls($cfg, $mycms)
{
    global $searchArray, $searchString;

    $indexVal = null;
    $plPg     = (int)($_REQUEST['_pgn_'] ?? 0);
    $startDate = $_REQUEST['start_date'] ?? '';

    $con    = "";
    $params = [];
    if (!empty($startDate)) {
        $con .= " AND DATE(`start_time`) >= ?";
        $params[] = [
            'FILD' => 'start_time',
            'DATA' => $startDate,
            'TYP'  => 's'
        ];
    }

    $sql = [];
    $sql['QUERY'] = "
        SELECT p.id, p.title, p.is_active, p.is_published, p.start_time, p.end_time,
            COALESCE(SUM(po.vote_count), 0) AS total_votes,
            GROUP_CONCAT(
                CONCAT(po.option_text, '::', po.vote_count)
                ORDER BY po.vote_count DESC
                SEPARATOR '||'
            ) AS options_result
        FROM rcg_polls p
        LEFT JOIN rcg_poll_options po 
            ON p.id = po.poll_id
        WHERE p.is_deleted != '1'
        $con
        GROUP BY p.id
        ORDER BY p.id DESC
    ";

    foreach ($params as $p) {
        $sql['PARAM'][] = $p;
    }

    $result = $mycms->sql_select_paginated($indexVal, $sql, 50, $restrt);

    date_default_timezone_set('Asia/Kolkata');
    $current_time = time();
?>
    <form name="frmPollSearch" id="frmPollSearch" action="<?= $_SERVER['PHP_SELF'] ?>" method="get">
        <div class="regi_search_wrap mb-3">
            <div class="regi_search">
                <i class="fal fa-calendar-alt"></i>
                <input type="date" name="start_date" id="start_date" value="<?= htmlspecialchars($startDate) ?>" title="Show polls starting from this date" />
            </div>
            <div class="regi_search_wrap_btn_box">
                <button type="submit" name="searchBtn" id="searchBtn"><i class="fal fa-search"></i> Search</button>
                <?php if ($startDate !== '') { ?>
                    <a href="<?= $_SERVER['PHP_SELF'] ?>"><i class="fal fa-redo"></i> Reset</a>
                <?php } ?>
                <a href="<?= $_SERVER['PHP_SELF'] ?>?show=add<?= $searchString ?>"><i class="fal fa-plus"></i> Create New Poll</a>
            </div>
        </div>
    </form>

    <div class="table_wrap">
        <table>
            <thead>
                <tr>
                    <th class="sl"><input name="check_all" id="check_all" type="checkbox" /></th>
                    <th class="sl">Sl No</th>
                    <th>Poll Title</th>
                    <th>Is Active</th>
                    <th>Is Published</th>
                    <th>Start Time</th>
                    <th>End Time</th>
                    <th>Total Votes</th>
                    <th>Result</th>
                    <th class="action">Action</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $counter = 0;
                if ($result) {
                    foreach ($result as $row) {
                        $counter++;

                        $isLive = false;
                        if ($row['start_time'] !== null && $row['end_time'] !== null) {
                            if ($current_time >= strtotime($row['start_time']) && $current_time <= strtotime($row['end_time'])) {
                                $isLive = true;
                            }
                        }
                ?>
                        <tr class="tlisting">
                            <td align="center" valign="top">
                                <input type="checkbox" name="selected_ids[]" class="checkBoxListing" value="<?= (int)$row['id'] ?>" />
                            </td>
                            <td align="center" valign="top"><?= $counter + ($plPg * 50) ?></td>
                            <td align="left" valign="top"><?= htmlspecialchars($row['title']) ?></td>
                            <td align="center" valign="top">
                                <span class="badge_padding <?= $isLive ? 'badge_success' : 'badge_danger' ?> w-max-con text-uppercase"><?= $isLive ? 'Yes' : 'No' ?></span>
                            </td>
                            <td align="center" valign="top">
                                <span class="badge_padding <?= ($row['is_published'] == '1') ? 'badge_success' : 'badge_dark' ?> w-max-con text-uppercase"><?= ($row['is_published'] == '1') ? 'Yes' : 'No' ?></span>
                            </td>
                            <td align="left" valign="top"><?= pldisp($row['start_time']) ?></td>
                            <td align="left" valign="top"><?= pldisp($row['end_time']) ?></td>
                            <td align="center" valign="top"><?= (int)$row['total_votes'] ?></td>
                            <td align="left" valign="top">
                                <?php
                                if (!empty($row['options_result'])) {
                                    $options = explode('||', $row['options_result']);
                                    foreach ($options as $opt) {
                                        $parts = explode('::', $opt);
                                        $text  = $parts[0] ?? '';
                                        $count = (int)($parts[1] ?? 0);
                                        $percentage = ($row['total_votes'] > 0)
                                            ? round(($count / $row['total_votes']) * 100, 2)
                                            : 0;
                                        echo '<div>' . htmlspecialchars($text) . " ({$percentage}%)</div>";
                                    }
                                } else {
                                    echo '&mdash;';
                                }
                                ?>
                            </td>
                            <td class="action">
                                <div class="action_div dropdown" role="menu" aria-label="Actions for <?= htmlspecialchars($row['title']) ?>">
                                    <button class="icon_hover badge_dark action-transparent dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false" aria-label="Open actions menu for <?= htmlspecialchars($row['title']) ?>"><?php ellips(); ?></button>
                                    <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                                        <a href="<?= $_SERVER['PHP_SELF'] ?>?show=edit&poll_id=<?= (int)$row['id'] ?><?= $searchString ?>" class="icon_hover badge_primary action-transparent"><i class="fal fa-edit"></i>Edit</a>
                                        <a href="<?= $cfg['SECTION_BASE_URL'] ?>manage_poll.process.php?act=reset_poll&poll_id=<?= (int)$row['id'] ?><?= $searchString ?>" class="icon_hover badge_primary action-transparent" onclick="return confirm('Do you really want to reset this poll result?');"><i class="fal fa-redo"></i>Reset Poll</a>
                                        <a href="<?= $cfg['SECTION_BASE_URL'] ?>manage_poll.process.php?act=Remove&id=<?= (int)$row['id'] ?><?= $searchString ?>" class="icon_hover badge_primary action-transparent" onclick="return confirm('Do you really want to remove this record');"><i class="fal fa-trash-alt"></i>Remove</a>
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
                            <span class="mandatory">No Record(s) Found !!</span>
                        </td>
                    </tr>
                <?php
                }
                ?>
            </tbody>
        </table>
    </div>

    <!-- Multi action -->
    <div class="page_top_wrap mt-3">
        <div class="page_top_wrap_left">
            <select name="multiOperationSelector" id="multiOperationSelector">
                <option value="">Choose an action..</option>
                <option value="Active">Active</option>
                <option value="Inactive">Inactive</option>
                <option value="Publish">Publish</option>
                <option value="Unpublish">Unpublish</option>
                <option value="Remove">Remove</option>
            </select>
            <button type="button" id="applyMultiActionBtn" class="badge_dark">Apply to selected</button>
        </div>
    </div>

    <div class="paginationDisplay">
        <span class="paginationRecDisplay"><?= $mycms->paginateRecInfo($indexVal) ?></span>
        <span class="paginationDisplay"><?= $mycms->paginate($indexVal, 'pagination') ?></span>
    </div>
<?php
}

/*================= ADD POLL WINDOW ===============*/
function addPoll($cfg, $mycms)
{
    global $searchArray, $searchString;
?>
    <form name="frmtypeadd" method="post" action="<?= $cfg['SECTION_BASE_URL'] ?>manage_poll.process.php" id="frmtypeadd" onsubmit="return onSubmitAction();">
        <input type="hidden" name="act" value="add_poll" />
        <?php foreach ($searchArray as $key => $val) { ?>
            <input type="hidden" name="<?= htmlspecialchars($key) ?>" id="<?= htmlspecialchars($key) ?>" value="<?= htmlspecialchars($val) ?>" />
        <?php } ?>

        <div class="basic_grid">
            <div class="basic_grid_left">
                <div class="basic_grid_box">
                    <div class="venue_hall_top push_noti_top">
                        <h5>
                            <n><i class="fal fa-poll"></i>Create New Poll</n>
                            <l>Launch real-time polls during keynotes or panel discussions.</l>
                        </h5>
                        <div class="page_top_wrap_right">
                            <a href="<?= $_SERVER['PHP_SELF'] ?>?<?= $searchString ?>" class="badge_dark"><i class="fal fa-arrow-left"></i> Back</a>
                            <button type="submit" name="Save2" id="Save2" class="badge_success"><i class="fal fa-save"></i> Save</button>
                        </div>
                    </div>

                    <div class="form_grid g_6">
                        <div class="frm_grp span_6">
                            <p class="frm-head">Poll Question <span class="mandatory">*</span></p>
                            <input type="text" name="title" id="title" class="validate[required]" required />
                        </div>

                        <div class="frm_grp span_2">
                            <p class="frm-head">Publish <span class="mandatory">*</span></p>
                            <select name="is_published" id="is_published" required>
                                <option value="1" selected>Yes</option>
                                <option value="0">No</option>
                            </select>
                        </div>
                        <div class="frm_grp span_2">
                            <p class="frm-head">Start Time <span class="mandatory">*</span></p>
                            <input type="datetime-local" name="start_time" id="start_time" required />
                        </div>
                        <div class="frm_grp span_2">
                            <p class="frm-head">End Time <span class="mandatory">*</span></p>
                            <input type="datetime-local" name="end_time" id="end_time" required />
                        </div>

                        <div class="frm_grp span_3">
                            <p class="frm-head">Option 1 <span class="mandatory">*</span></p>
                            <input type="text" name="options[]" id="option_1" required />
                        </div>
                        <div class="frm_grp span_3">
                            <p class="frm-head">Option 2 <span class="mandatory">*</span></p>
                            <input type="text" name="options[]" id="option_2" required />
                        </div>
                        <div class="frm_grp span_3">
                            <p class="frm-head">Option 3</p>
                            <input type="text" name="options[]" id="option_3" />
                        </div>
                        <div class="frm_grp span_3">
                            <p class="frm-head">Option 4</p>
                            <input type="text" name="options[]" id="option_4" />
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
<?php
}

/*================= EDIT POLL WINDOW ==============*/
function editPoll($cfg, $mycms)
{
    global $searchArray, $searchString;

    $sql = array();
    $sql['QUERY']   = "SELECT * FROM `rcg_polls`  
                       WHERE `id` = ?";
    $sql['PARAM'][] = array('FILD' => 'id', 'DATA' => $_REQUEST['poll_id'] ?? 0, 'TYP' => 's');
    $result = $mycms->sql_select($sql);
    $row    = $result ? $result[0] : [];

    if (empty($row)) {
        ?>
        <div class="table_wrap">
            <table><tbody><tr><td align="center"><span class="mandatory">Poll not found.</span></td></tr></tbody></table>
        </div>
        <div class="regi_search_wrap mt-3">
            <div class="regi_search_wrap_btn_box">
                <a href="<?= $_SERVER['PHP_SELF'] ?>?<?= $searchString ?>"><i class="fal fa-arrow-left"></i> Back</a>
            </div>
        </div>
        <?php
        return;
    }

    $sql_option = array();
    $sql_option['QUERY']   = "SELECT * FROM `rcg_poll_options`  
                       WHERE `poll_id` = ? ORDER BY `id` ASC";
    $sql_option['PARAM'][] = array('FILD' => 'poll_id', 'DATA' => $row['id'], 'TYP' => 's');
    $result_option = $mycms->sql_select($sql_option);
?>
    <form name="frmtypeedit" method="post" action="<?= $cfg['SECTION_BASE_URL'] ?>manage_poll.process.php" id="frmtypeedit" onsubmit="return onSubmitAction();">
        <input type="hidden" name="act" value="edit_poll" />
        <input type="hidden" name="poll_id" id="poll_id" value="<?= (int)($_REQUEST['poll_id'] ?? 0) ?>" />
        <?php foreach ($searchArray as $key => $val) { ?>
            <input type="hidden" name="<?= htmlspecialchars($key) ?>" id="<?= htmlspecialchars($key) ?>" value="<?= htmlspecialchars($val) ?>" />
        <?php } ?>

        <div class="basic_grid">
            <div class="basic_grid_left">
                <div class="basic_grid_box">
                    <div class="venue_hall_top push_noti_top">
                        <h5>
                            <n><i class="fal fa-poll"></i>Edit Poll</n>
                            <l>Update the question, schedule and options of this poll.</l>
                        </h5>
                        <div class="page_top_wrap_right">
                            <a href="<?= $_SERVER['PHP_SELF'] ?>?<?= $searchString ?>" class="badge_dark"><i class="fal fa-arrow-left"></i> Back</a>
                            <button type="submit" name="Save" id="Save" class="badge_success"><i class="fal fa-save"></i> Save</button>
                        </div>
                    </div>

                    <div class="form_grid g_6">
                        <div class="frm_grp span_6">
                            <p class="frm-head">Poll Question <span class="mandatory">*</span></p>
                            <input type="text" name="title" id="title" class="validate[required]" value="<?= plv($row, 'title') ?>" required />
                        </div>

                        <div class="frm_grp span_2">
                            <p class="frm-head">Publish <span class="mandatory">*</span></p>
                            <select name="is_published" id="is_published" required>
                                <option value="1" <?= ($row['is_published'] == '1') ? 'selected' : ''; ?>>Yes</option>
                                <option value="0" <?= ($row['is_published'] == '0') ? 'selected' : ''; ?>>No</option>
                            </select>
                        </div>
                        <div class="frm_grp span_2">
                            <p class="frm-head">Start Time <span class="mandatory">*</span></p>
                            <input type="datetime-local" name="start_time" id="start_time" value="<?= pldt($row['start_time'] ?? '') ?>" required />
                        </div>
                        <div class="frm_grp span_2">
                            <p class="frm-head">End Time <span class="mandatory">*</span></p>
                            <input type="datetime-local" name="end_time" id="end_time" value="<?= pldt($row['end_time'] ?? '') ?>" required />
                        </div>

                        <?php
                        $option_count = 0;
                        if (!empty($result_option)) {
                            foreach ($result_option as $row_option) {
                                $option_count++;
                        ?>
                                <div class="frm_grp span_3">
                                    <p class="frm-head">Option <?= $option_count ?> <?php if ($option_count < 3) { ?><span class="mandatory">*</span><?php } ?></p>
                                    <input type="hidden" name="options[<?= $option_count ?>][id]" value="<?= (int)$row_option['id'] ?>">
                                    <input type="text" name="options[<?= $option_count ?>][text]" value="<?= htmlspecialchars($row_option['option_text']) ?>" <?= ($option_count < 3) ? 'required' : '' ?>>
                                </div>
                            <?php
                            }
                        }
                        if ($option_count == 0) {
                            // No options exist yet — render 4 blank option fields using the SAME
                            // naming convention as the populated case (options[N][text]) so the
                            // processor sees a consistent structure.
                            for ($i = 1; $i <= 4; $i++) {
                            ?>
                                <div class="frm_grp span_3">
                                    <p class="frm-head">Option <?= $i ?> <?php if ($i < 3) { ?><span class="mandatory">*</span><?php } ?></p>
                                    <input type="hidden" name="options[<?= $i ?>][id]" value="0">
                                    <input type="text" name="options[<?= $i ?>][text]" id="option_<?= $i ?>" value="" <?= ($i < 3) ? 'required' : '' ?>>
                                </div>
                            <?php
                            }
                        }
                        ?>
                    </div>
                </div>
            </div>
        </div>
    </form>
<?php
}
?>
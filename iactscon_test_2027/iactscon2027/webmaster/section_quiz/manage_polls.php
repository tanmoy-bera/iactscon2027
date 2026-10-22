<?php
include_once("../includes/source.php");
include_once('../includes/init.php');
include_once('../includes/function.workshop.php');
?>

<body>
    <?php include_once("../includes/left-menu.php"); ?>
    <header>
        <h2>Manage Polls</h2>
        <?php include_once("../includes/header_right.php"); ?>
    </header>
    <div class="body_wrap">
        <?php
        $pageKey    = "_pgn_";
        $pageKeyVal = (empty($_REQUEST[$pageKey])) ? 0 : $_REQUEST[$pageKey];

        $searchString = "";
        $searchArray  = array();
        $searchArray[$pageKey]     = $pageKeyVal;
        $searchArray['start_date'] = $_REQUEST['start_date'] ?? '';

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
                        <li class="breadcrumb-item active" aria-current="page"><a href="#">Manage Polls</a></li>
                    </ol>
                </nav>
                <h2><?= ($show == 'add') ? 'Create New Poll' : (($show == 'edit') ? 'Edit Poll' : 'Poll Listing') ?></h2>
                <h6>Manage polls, options and live voting results.</h6>
            </div>
            <?php if ($show == '') { ?>
            <div class="page_top_wrap_right">
                <a href="<?= $_SERVER['PHP_SELF'] ?>?show=add<?= $searchString ?>" class="badge_primary"><i class="fal fa-plus"></i> Create New Poll</a>
            </div>
            <?php } ?>
        </div>

        <div class="com_info_wrap">
            <div class="com_info_right" style="width:100%;">
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
        </div>
    </div>
</body>
<?php include_once("../includes/js-source.php"); ?>
<script>
    $(document).on('click', '.deletePollBtn', function () {
        if (confirm('Do you really want to reset this poll result?')) {
            window.location.href = $(this).data('href');
        }
    });
</script>
</html>
<?php

/*============== SHOW ALL POLLS ================*/
function showAllPolls($cfg, $mycms)
{
    global $searchArray, $searchString;

    $con    = "";
    $params = [];
    if (!empty($_REQUEST['start_date'])) {
        $con .= " AND DATE(`start_time`) >= ?";
        $params[] = array('FILD' => 'start_time', 'DATA' => $_REQUEST['start_date'], 'TYP' => 's');
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
    ?>
    <div class="com_info_box active" id="pollListing">
        <div class="com_info_box_grid">
            <div class="com_info_box_grid_box">
                <h5 class="com_info_box_head">
                    <n><span class="text_primary"><i class="fal fa-poll-h"></i></span> Poll Listing</n>
                </h5>
                <div class="com_info_box_inner">
                    <div class="table_wrap">
                        <table>
                            <thead>
                                <tr>
                                    <th class="sl">#</th>
                                    <th>Poll Title</th>
                                    <th>Is Active</th>
                                    <th>Is Published</th>
                                    <th>Start Time</th>
                                    <th>End Time</th>
                                    <th>Total Votes</th>
                                    <th>Result</th>
                                    <th class="action text-right">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                if ($result) {
                                    $i = 0;
                                    foreach ($result as $row) {
                                        $i++;

                                        date_default_timezone_set('Asia/Kolkata');
                                        $current_time = time();
                                        $isActiveNow  = 'No';
                                        if ($row['start_time'] !== null && $row['end_time'] !== null) {
                                            if ($current_time >= strtotime($row['start_time']) && $current_time <= strtotime($row['end_time'])) {
                                                $isActiveNow = 'Yes';
                                            }
                                        }
                                ?>
                                <tr>
                                    <td class="sl"><?= $i + ((int)($_REQUEST['_pgn_'] ?? 0) * 50) ?></td>
                                    <td><?= htmlspecialchars($row['title']) ?></td>
                                    <td>
                                        <?php if ($isActiveNow == 'Yes') { ?>
                                            <n class="badge_padding badge_success w-max-con text-uppercase">Yes</n>
                                        <?php } else { ?>
                                            <n class="badge_padding badge_danger w-max-con text-uppercase">No</n>
                                        <?php } ?>
                                    </td>
                                    <td>
                                        <?php if ($row['is_published'] == '1') { ?>
                                            <n class="badge_padding badge_success w-max-con text-uppercase">Yes</n>
                                        <?php } else { ?>
                                            <n class="badge_padding badge_danger w-max-con text-uppercase">No</n>
                                        <?php } ?>
                                    </td>
                                    <td><?= date('d-m-Y H:i', strtotime($row['start_time'])) ?></td>
                                    <td><?= date('d-m-Y H:i', strtotime($row['end_time'])) ?></td>
                                    <td><?= $row['total_votes'] ?></td>
                                    <td>
                                        <?php
                                        if (!empty($row['options_result'])) {
                                            $options = explode('||', $row['options_result']);
                                            foreach ($options as $opt) {
                                                list($text, $count) = explode('::', $opt);
                                                $percentage = ($row['total_votes'] > 0)
                                                    ? round(($count / $row['total_votes']) * 100, 2)
                                                    : 0;
                                                echo htmlspecialchars($text) . " ({$percentage}%)<br>";
                                            }
                                        } else {
                                            echo '—';
                                        }
                                        ?>
                                    </td>
                                    <td class="action">
                                        <div class="action_div dropdown" role="menu" aria-label="Actions">
                                            <button class="icon_hover badge_dark action-transparent dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false"><?php ellips(); ?></button>
                                            <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                                                <a href="<?= $_SERVER['PHP_SELF'] ?>?show=edit&poll_id=<?= $row['id'] ?><?= $searchString ?>" class="icon_hover badge_secondary action-transparent"><?php edit(); ?>Edit Details</a>
                                                <a href="javascript:void(0);" class="deletePollBtn icon_hover badge_danger action-transparent br-5 w-auto" data-href="manage_poll.process.php?act=Reset&id=<?= $row['id'] ?>">
                                                    <i class="fal fa-redo"></i> Reset Poll
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

/*============== ADD POLL ================*/
function addPoll($cfg, $mycms)
{
    global $searchArray, $searchString;
    ?>
    <div class="com_info_box active" id="addPoll">
        <form name="frmtypeadd" method="post" action="manage_poll.process.php" id="frmtypeadd" onsubmit="return onSubmitAction();">
            <input type="hidden" name="act" value="add_poll" />
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
                        <n><span class="text_success"><i class="fal fa-poll-h"></i></span> Poll Details</n>
                        <div class="page_top_wrap_right">
                           <a href="<?= $_SERVER['PHP_SELF'] ?>?<?= ltrim($searchString, '&') ?>" class="badge_dark"><i class="fal fa-arrow-left"></i> Back</a>
                            <button type="submit" id=edit"Save2" name="Save2" class="badge_success"><i class="fal fa-save"></i> Save</button>
                        </div>
                    </h5>
                    <div class="com_info_box_inner">
                        <div class="form_grid g_3">
                            <div class="frm_grp span_2">
                                <p class="frm-head">Poll Question <span class="mandatory">*</span></p>
                                <input type="text" name="title" id="title" class="validate[required]" required />
                            </div>
                            <div class="frm_grp">
                                <p class="frm-head">Publish <span class="mandatory">*</span></p>
                                <select name="is_published" id="is_published" required>
                                    <option value="1" selected>Yes</option>
                                    <option value="0">No</option>
                                </select>
                            </div>
                            <div class="frm_grp">
                                <p class="frm-head">Start Time <span class="mandatory">*</span></p>
                                <input type="datetime-local" name="start_time" id="start_time" required />
                            </div>
                            <div class="frm_grp">
                                <p class="frm-head">End Time <span class="mandatory">*</span></p>
                                <input type="datetime-local" name="end_time" id="end_time" required />
                            </div>
                        </div>
                    </div>
                </div>
                <div class="com_info_box_grid_box">
                    <h5 class="com_info_box_head">
                        <n><span class="text_primary"><i class="fal fa-list-ul"></i></span> Poll Options</n>
                    </h5>
                    <div class="com_info_box_inner">
                        <div class="form_grid g_2">
                            <div class="frm_grp">
                                <p class="frm-head">Option 1 <span class="mandatory">*</span></p>
                                <input type="text" name="options[]" id="option_1" required />
                            </div>
                            <div class="frm_grp">
                                <p class="frm-head">Option 2 <span class="mandatory">*</span></p>
                                <input type="text" name="options[]" id="option_2" required />
                            </div>
                            <div class="frm_grp">
                                <p class="frm-head">Option 3</p>
                                <input type="text" name="options[]" id="option_3" />
                            </div>
                            <div class="frm_grp">
                                <p class="frm-head">Option 4</p>
                                <input type="text" name="options[]" id="option_4" />
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
    <?php
}

/*============== EDIT POLL ================*/
function editPoll($cfg, $mycms)
{
    global $searchArray, $searchString;

    $sql            = array();
    $sql['QUERY']   = "SELECT * FROM `rcg_polls` WHERE `id` = ?";
    $sql['PARAM'][] = array('FILD' => 'id', 'DATA' => $_REQUEST['poll_id'], 'TYP' => 's');
    $result         = $mycms->sql_select($sql);
    $row            = $result[0];

    $sql_option            = array();
    $sql_option['QUERY']   = "SELECT * FROM `rcg_poll_options` WHERE `poll_id` = ? ORDER BY `id` ASC";
    $sql_option['PARAM'][] = array('FILD' => 'poll_id', 'DATA' => $row['id'], 'TYP' => 's');
    $result_option         = $mycms->sql_select($sql_option);
    ?>
    <div class="com_info_box active" id="editPoll">
        <form name="frmtypeedit" method="post" action="manage_poll.process.php" id="frmtypeedit" onsubmit="return onSubmitAction();">
            <input type="hidden" name="act" value="edit_poll" />
            <input type="hidden" name="poll_id" id="poll_id" value="<?= $_REQUEST['poll_id'] ?>" />
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
                        <n><span class="text_success"><i class="fal fa-poll-h"></i></span> Poll Details</n>
                        <div class="page_top_wrap_right">
                           <a href="<?= $_SERVER['PHP_SELF'] ?>?<?= ltrim($searchString, '&') ?>" class="badge_dark"><i class="fal fa-arrow-left"></i> Back</a>
                            <button type="submit" id="Save" name="Save" class="badge_success"><i class="fal fa-save"></i> Save</button>
                        </div>
                    </h5>
                    <div class="com_info_box_inner">
                        <div class="form_grid g_3">
                            <div class="frm_grp span_2">
                                <p class="frm-head">Poll Question <span class="mandatory">*</span></p>
                                <input type="text" name="title" id="title" value="<?= htmlspecialchars($row['title']) ?>" required />
                            </div>
                            <div class="frm_grp">
                                <p class="frm-head">Publish <span class="mandatory">*</span></p>
                                <select name="is_published" id="is_published" required>
                                    <option value="1" <?= ($row['is_published'] == '1') ? 'selected' : ''; ?>>Yes</option>
                                    <option value="0" <?= ($row['is_published'] == '0') ? 'selected' : ''; ?>>No</option>
                                </select>
                            </div>
                            <div class="frm_grp">
                                <p class="frm-head">Start Time <span class="mandatory">*</span></p>
                                <input type="datetime-local" value="<?= $row['start_time']; ?>" name="start_time" id="start_time" required />
                            </div>
                            <div class="frm_grp">
                                <p class="frm-head">End Time <span class="mandatory">*</span></p>
                                <input type="datetime-local" name="end_time" id="end_time" value="<?= $row['end_time']; ?>" required />
                            </div>
                        </div>
                    </div>
                </div>
                <div class="com_info_box_grid_box">
                    <h5 class="com_info_box_head">
                        <n><span class="text_primary"><i class="fal fa-list-ul"></i></span> Poll Options</n>
                    </h5>
                    <div class="com_info_box_inner">
                        <div class="form_grid g_2">
                            <?php
                            $option_count = 0;
                            foreach ($result_option as $row_option) {
                                $option_count++;
                            ?>
                            <div class="frm_grp">
                                <p class="frm-head">
                                    Option <?= $option_count ?>
                                    <?php if ($option_count < 3) { ?><span class="mandatory">*</span><?php } ?>
                                </p>
                                <input type="hidden" name="options[<?= $option_count ?>][id]" value="<?= $row_option['id']; ?>">
                                <input type="text" name="options[<?= $option_count ?>][text]" value="<?= htmlspecialchars($row_option['option_text']); ?>" <?= ($option_count < 3) ? 'required' : '' ?>>
                            </div>
                            <?php
                            }
                            if ($option_count == 0) {
                            ?>
                            <div class="frm_grp">
                                <p class="frm-head">Option 1 <span class="mandatory">*</span></p>
                                <input type="text" name="options['text'][]" id="option_1" required />
                            </div>
                            <div class="frm_grp">
                                <p class="frm-head">Option 2 <span class="mandatory">*</span></p>
                                <input type="text" name="options['text'][]" id="option_2" required />
                            </div>
                            <div class="frm_grp">
                                <p class="frm-head">Option 3</p>
                                <input type="text" name="options[]" id="option_3" />
                            </div>
                            <div class="frm_grp">
                                <p class="frm-head">Option 4</p>
                                <input type="text" name="options[]" id="option_4" />
                            </div>
                            <?php } ?>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
    <?php
}
?>
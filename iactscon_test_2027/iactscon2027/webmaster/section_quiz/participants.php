<?php
include_once('includes/init.php');

page_header("Participants");

$pageKey = "_pgn_";

$pageKeyVal = ($_REQUEST[$pageKey] == "") ? 0 : $_REQUEST[$pageKey];

@$searchString = "";
$searchArray  = array();

$searchArray[$pageKey]     = $pageKeyVal;
$searchArray['quiz_id']    = $_REQUEST['quiz_id'];
$searchArray['keyword']    = $_REQUEST['keyword'];

foreach ($searchArray as $searchKey => $searchVal) {
    $searchString .= "&" . $searchKey . "=" . $searchVal;
}
?>
<link rel="stylesheet" type="text/css" href="https://ruedakolkata.com/ccicon_2025/webmaster/util/font-awesome-4.7.0/css/font-awesome.min.css" />
<script language="javascript" src="<?= _BASE_URL_ ?>JS/jquery.js"></script>
<div class="container">
    <?php showAllParticipants($cfg, $mycms); ?>
</div>
<?php
page_footer();

/*============== SHOW ALL PARTICIPANTS WINDOW =============*/
function showAllParticipants($cfg, $mycms)
{
    // echo "MARKER-XYZ-123";
    global $searchArray, $searchString;

    if (empty($_REQUEST['quiz_id'])) {
        echo '<span class="mandatory">Quiz ID is required.</span>';
        echo '<p><a href="' . $cfg['SECTION_BASE_URL'] . 'manage_quiz.php">&laquo; Back</a></p>';
        return;
    }

    $quiz_id = (int)$_REQUEST['quiz_id'];

    // Get quiz title for the heading
    $quizSql = [];
    $quizSql['QUERY'] = "SELECT title FROM rcg_quizzes WHERE id = ?";
    $quizSql['PARAM'][] = ['FILD' => 'id', 'DATA' => $quiz_id, 'TYP' => 'i'];
    $quizRow  = $mycms->sql_select($quizSql);
    $quizTitle = $quizRow[0]['title'] ?? '';
?>
    <table width="100%" style="margin-bottom:10px;">
        <tr>
            <td align="left">
                <a href="<?= $cfg['SECTION_BASE_URL'] ?>manage_quiz.php" class="btn btn-small btn-red">&laquo; Back</a>
            </td>
        </tr>
    </table>

    <form name="frmParticipantListing" id="frmParticipantListing" action="<?= $cfg['SECTION_BASE_URL'] ?>participants.php" method="get">
        <input type="hidden" name="quiz_id" id="quiz_id" value="<?= $quiz_id ?>" />
        <input type="hidden" id="excelPrintVal" name="excelPrintVal" />
        <table width="100%" align="center" class="tborder">
            <tr>
                <td colspan="2" align="left" class="tcat">
                    <span style="float:left">Participants — <?= htmlspecialchars($quizTitle) ?></span>
                    <span class="tsearchTool" forType="tsearchTool"></span>
                </td>
            </tr>
            <tr>
                <td colspan="2" style="margin:0px; padding:0px;">

                    <div class="tsearch">
                        <table width="100%">
                            <tr>
                                <td align="right">
                                    Name / Email / Mobile:
                                    <input type="text" name="keyword" id="keyword"
                                        value="<?= htmlspecialchars($_REQUEST['keyword'] ?? '') ?>" />
                                    &nbsp;
                                    <input type="submit" name="searchBtn" id="searchBtn" value="Search" class="btn btn-small btn-blue" />
                                </td>
                            </tr>
                        </table>
                    </div>

                    <table width="100%">
                        <tr class="theader">
                            <td width="50" align="center">Sl No</td>
                            <td width="120" align="left">Name</td>
                            <td width="80" align="center">Type</td>
                            <td width="100" align="left">Group</td>
                            <td width="120" align="left">Email</td>
                            <td width="100" align="left">Mobile</td>
                            <td width="100" align="center">Joined At</td>
                        </tr>
                        <?php
                        $con    = "";
                        $params = [];

                        if (!empty($_REQUEST['keyword'])) {
                            $con .= " AND (u.user_full_name LIKE ? OR m.email LIKE ? OR u.user_email_id LIKE ? OR m.mobile LIKE ? OR u.user_mobile_no LIKE ? OR p.group_name LIKE ?)";
                            $kw = '%' . $_REQUEST['keyword'] . '%';
                            $params[] = ['FILD' => 'user_full_name', 'DATA' => $kw, 'TYP' => 's'];
                            $params[] = ['FILD' => 'm_email',        'DATA' => $kw, 'TYP' => 's'];
                            $params[] = ['FILD' => 'user_email_id',  'DATA' => $kw, 'TYP' => 's'];
                            $params[] = ['FILD' => 'm_mobile',       'DATA' => $kw, 'TYP' => 's'];
                            $params[] = ['FILD' => 'user_mobile_no', 'DATA' => $kw, 'TYP' => 's'];
                            $params[] = ['FILD' => 'group_name',     'DATA' => $kw, 'TYP' => 's'];
                        }

                        // Registered participants (solo + group members)
                        // Prefer email/mobile captured at join-time on the members table;
                        // fall back to the live rcg_user_registration record if those are empty.
                        $sql = [];
                        $sql['QUERY'] = "SELECT 
                                p.participation_type,
                                p.group_name,
                                p.created_at AS joined_at,
                                u.user_full_name,
                                COALESCE(NULLIF(m.email, ''), u.user_email_id)   AS display_email,
                                COALESCE(NULLIF(m.mobile, ''), u.user_mobile_no) AS display_mobile
                            FROM quiz_participation p
                            INNER JOIN rcg_quiz_participation_members m ON m.participation_id = p.id
                            LEFT JOIN rcg_user_registration u ON u.id = m.user_id
                            WHERE p.quiz_id = ?
                            AND m.status = 'Active'
                            $con
                            ORDER BY p.created_at DESC";

                        $sql['PARAM'][] = ['FILD' => 'quiz_id', 'DATA' => $quiz_id, 'TYP' => 'i'];
                        foreach ($params as $p) {
                            $sql['PARAM'][] = $p;
                        }

                        $result = $mycms->sql_select_paginated($indexVal, $sql, 20, $restrt);

                        $i = 0;

                        if ($result) {
                            foreach ($result as $i => $row) {
                                @$i++;
                        ?>
                                <tr class="tlisting">
                                    <td align="center"><?= $i + ($_REQUEST['_pgn_'] * 20) ?></td>
                                    <td align="left"><?= htmlspecialchars($row['user_full_name'] ?? '') ?></td>
                                    <td align="center"><?= ucfirst($row['participation_type']) ?></td>
                                    <td align="left"><?= htmlspecialchars($row['group_name'] ?? '') ?></td>
                                    <td align="left"><?= htmlspecialchars($row['display_email'] ?? '') ?></td>
                                    <td align="left"><?= htmlspecialchars($row['display_mobile'] ?? '') ?></td>
                                    <td align="center"><?php echo !empty($row['joined_at']) ? date('d-m-Y H:i:s', strtotime($row['joined_at'])) : ''; ?></td>
                                </tr>
                            <?php
                            }
                        } else {
                            ?>
                            <tr>
                                <td colspan="7" align="center">
                                    <span class="mandatory">No Record.</span>
                                </td>
                            </tr>
                        <?php } ?>
                    </table>

                    <?php
                    // Guest participants (shown separately, not paginated with the block above)
                    $guestSql = [];
                    $guestSql['QUERY'] = "SELECT device_id, guest_name, latest_visit_time AS joined_at
                        FROM quiz_guest_users
                        WHERE latest_visit_quiz_id = ?
                        ORDER BY latest_visit_time DESC";
                    $guestSql['PARAM'][] = ['FILD' => 'latest_visit_quiz_id', 'DATA' => $quiz_id, 'TYP' => 'i'];
                    $guests = $mycms->sql_select($guestSql);

                    if (!empty($guests)) {
                    ?>
                        <table width="100%" style="margin-top:15px;">
                            <tr class="thighlight">
                                <td colspan="3" align="left">Guest Participants</td>
                            </tr>
                            <tr class="theader">
                                <td width="50" align="center">Sl No</td>
                                <td width="150" align="left">Guest Name</td>
                                <td width="100" align="center">Joined At</td>
                            </tr>
                            <?php $gi = 0;
                            foreach ($guests as $g) {
                                $gi++; ?>
                                <tr class="tlisting">
                                    <td align="center"><?= $gi ?></td>
                                    <td align="left"><?= htmlspecialchars($g['guest_name'] ?: 'Guest') ?></td>
                                    <td align="center"><?php echo !empty($g['joined_at']) ? date('d-m-Y H:i:s', strtotime($g['joined_at'])) : ''; ?></td>
                                </tr>
                            <?php } ?>
                        </table>
                    <?php } ?>

                </td>
            </tr>

            <tr>
                <!-- <td width="50%" align="left">
                    <a href="<?= $cfg['SECTION_BASE_URL'] ?>manage_quiz.php" class="btn btn-small btn-red">&laquo; Back</a>
                </td> -->
                <td width="50%" align="right">&nbsp;</td>
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
?>
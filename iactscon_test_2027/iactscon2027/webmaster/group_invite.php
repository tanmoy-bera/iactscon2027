<?php
include_once("./includes/source.php");
include_once('./includes/init.php');
$currentAdminId = $mycms->getLoggedUserId();

// group comes only from group_list.php -> "Invite Users"
$groupId    = (int)($_REQUEST['group_id'] ?? 0);
$searchName = trim($_REQUEST['search_name'] ?? '');

$groupRow = null;
if ($groupId > 0) {
    $gchk = [];
    $gchk['QUERY'] = "SELECT `id`, `group_name`, `created_by` FROM `rcg_groups` WHERE `id`=? AND `status`='A' AND `group_type`='private'";
    $gchk['PARAM'] = [['FILD' => 'id', 'DATA' => $groupId, 'TYP' => 'i']];
    $found = $mycms->sql_select($gchk);
    if (!empty($found)) {
        $groupRow = $found[0];
    }
}
?>

<body>
    <?php include_once("./includes/left-menu.php"); ?>
    <header>
        <h2>Group Invitations</h2>
        <?php include_once("./includes/header_right.php"); ?>
    </header>
    <?php
    // One-time flash alert set by group_invite.process.php
    $flash = $_SESSION['invite_alert'] ?? null;
    unset($_SESSION['invite_alert']);
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
        /* Action column: center the action button (theme aligns it right / floats it by default) */
        .table_wrap table td.action,
        .table_wrap table th.action { text-align: center !important; }
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

        // group_id is added explicitly in the links below, so it is not part of $searchString
        $searchArray  = array();
        $searchArray[$pageKey]      = $pageKeyVal;
        $searchArray['search_name'] = $searchName;

        $searchString = "";
        foreach ($searchArray as $searchKey => $searchVal) {
            $searchString .= "&" . $searchKey . "=" . urlencode($searchVal);
        }
        ?>

        <div class="page_top_wrap mb-3">
            <div class="page_top_wrap_left">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="#">Community</a></li>
                        <li class="breadcrumb-item"><a href="group_list.php">Manage Groups</a></li>
                        <li class="breadcrumb-item active" aria-current="page"><a href="#">Group Invitations</a></li>
                    </ol>
                </nav>
                <h2>Invite Users<?= $groupRow ? ' — ' . htmlspecialchars($groupRow['group_name']) : '' ?></h2>
            </div>
            <div class="page_top_wrap_right">
                <a href="group_list.php" class="badge_dark"><i class="fal fa-arrow-left"></i> Back to Groups</a>
                <?php if ($groupRow) { ?>
                    <a href="group_members_list.php?group_id=<?= $groupId ?>" class="badge_success"><i class="fal fa-users"></i> Group Members</a>
                <?php } ?>
            </div>
        </div>

        <div class="com_info_wrap">
            <div class="com_info_right" style="width:100%;">

                <?php if (!$groupRow) { ?>
                    <span class="mandatory">Group not found or inactive. Please open this page from Manage Groups &rarr; Invite Users.</span>
                <?php } else { ?>

                <!-- ================= SINGLE TABLE: USERS + INVITATION STATUS ================= -->
                <div class="com_info_box active">
                    <div class="com_info_box_grid">
                        <div class="com_info_box_grid_box">
                            <h5 class="com_info_box_head">
                                <n><span class="text_success"><i class="fal fa-user-plus"></i></span> Invite Users</n>
                            </h5>
                            <div class="com_info_box_inner">

                                <form method="get" action="<?= $_SERVER['PHP_SELF'] ?>" class="mb-3">
                                    <input type="hidden" name="group_id" value="<?= $groupId ?>" />
                                    <div class="form_grid g_3">
                                        <div class="frm_grp span_2">
                                            <p class="frm-head">Search name / email / mobile</p>
                                            <input type="text" name="search_name" value="<?= htmlspecialchars($searchName) ?>" />
                                        </div>
                                        <div class="frm_grp">
                                            <p class="frm-head">&nbsp;</p>
                                            <button type="submit" class="badge_primary"><i class="fal fa-search"></i> Search</button>
                                        </div>
                                    </div>
                                </form>

                                <?php
                                // Placeholder order must match $params order:
                                // 1 in FROM (latest invitation join), 2 in WHERE (exclusions), then search words
                                $params = [
                                    ['FILD' => 'g1', 'DATA' => $groupId, 'TYP' => 'i'],
                                    ['FILD' => 'g2', 'DATA' => $groupId, 'TYP' => 'i'],
                                    ['FILD' => 'g3', 'DATA' => $groupId, 'TYP' => 'i'],
                                ];

                                // active members and users who accepted an invite are NOT listed
                                $where  = " WHERE u.status = 'A'
                                            AND NOT EXISTS (SELECT 1 FROM rcg_group_members gm
                                                            WHERE gm.group_id = ? AND gm.user_id = u.id AND gm.status = 'A')
                                            AND NOT EXISTS (SELECT 1 FROM rcg_group_invitations gx
                                                            WHERE gx.group_id = ? AND gx.invited_user_id = u.id AND gx.status = 'accepted') ";

                                if ($searchName !== '') {
                                    $normalized = preg_replace('/\s+/', ' ', trim($searchName));
                                    $words = explode(' ', $normalized);

                                    $wordConditions = [];
                                    foreach ($words as $idx => $word) {
                                        if ($word === '') continue;
                                        $wordConditions[] = "(u.user_full_name LIKE ? OR u.user_email_id LIKE ? OR u.user_mobile_no LIKE ?)";
                                        $like = "%{$word}%";
                                        $params[] = ['FILD' => "n{$idx}a", 'DATA' => $like, 'TYP' => 's'];
                                        $params[] = ['FILD' => "n{$idx}b", 'DATA' => $like, 'TYP' => 's'];
                                        $params[] = ['FILD' => "n{$idx}c", 'DATA' => $like, 'TYP' => 's'];
                                    }

                                    if (!empty($wordConditions)) {
                                        $where .= " AND (" . implode(" AND ", $wordConditions) . ")";
                                    }
                                }

                                // each user with the LATEST invitation (if any) for this group
                                $usql = [];
                                $usql['QUERY'] = "
                                    SELECT u.id, u.user_full_name, u.user_email_id, u.user_mobile_no,
                                           gi.id AS invitation_id, gi.status AS invite_status,
                                           gi.created_at AS invited_at, gi.responded_at
                                    FROM rcg_user_registration u
                                    LEFT JOIN rcg_group_invitations gi
                                           ON gi.id = (SELECT MAX(gl.id) FROM rcg_group_invitations gl
                                                       WHERE gl.group_id = ? AND gl.invited_user_id = u.id)
                                    $where
                                    ORDER BY (gi.status = 'pending') DESC, u.id DESC
                                ";
                                foreach ($params as $p) { $usql['PARAM'][] = $p; }

                                $users = $mycms->sql_select_paginated($indexVal, $usql, 50, $restrt);

                                $badge = ['pending' => 'badge_dark', 'declined' => 'badge_danger', 'cancelled' => 'badge_secondary'];
                                ?>

                                <div class="table_wrap">
                                    <table>
                                        <thead>
                                            <tr>
                                                <th class="sl">Sl. No.</th>
                                                <th>Name</th>
                                                <th>Email</th>
                                                <th>Mobile</th>
                                                <th>Status</th>
                                                <th>Invited On</th>
                                                <th>Responded On</th>
                                                <th class="action text-right">Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php if ($users): $i = 0; foreach ($users as $u): $i++;
                                                $st = $u['invite_status']; // null = never invited
                                            ?>
                                            <tr>
                                                <td class="sl"><?= $i + ((int)$pageKeyVal * 50) ?></td>
                                                <td><?= htmlspecialchars($u['user_full_name'] ?? '—') ?></td>
                                                <td><?= htmlspecialchars($u['user_email_id'] ?? '—') ?></td>
                                                <td><?= htmlspecialchars($u['user_mobile_no'] ?? '—') ?></td>
                                                <td style="text-align:center;">
                                                    <?php if ($st === null) { ?>
                                                        <n class="badge_padding badge_secondary w-max-con text-uppercase">Not Invited</n>
                                                    <?php } else { ?>
                                                        <n class="badge_padding <?= $badge[$st] ?? 'badge_secondary' ?> w-max-con text-uppercase"><?= $st === 'pending' ? 'Invite Pending' : htmlspecialchars($st) ?></n>
                                                    <?php } ?>
                                                </td>
                                                <td style="text-align:center;"><?= $u['invited_at'] ? date('d-m-Y H:i', strtotime($u['invited_at'])) : '—' ?></td>
                                                <td style="text-align:center;"><?= $u['responded_at'] ? date('d-m-Y H:i', strtotime($u['responded_at'])) : '—' ?></td>
                                                <td class="action">
                                                    <?php if ($st === 'pending') { ?>
                                                        <a href="javascript:void(0);" class="cancelInviteBtn badge_danger action-transparent br-5 w-auto"
                                                           data-href="group_invite.process.php?act=cancel_invite&group_id=<?= $groupId ?><?= $searchString ?>&invitation_id=<?= (int)$u['invitation_id'] ?>">
                                                            <i class="fal fa-times"></i> Cancel Invitation
                                                        </a>
                                                    <?php } else { ?>
                                                        <a href="javascript:void(0);" class="sendInviteBtn badge_primary action-transparent br-5 w-auto"
                                                           data-href="group_invite.process.php?act=send_invite&group_id=<?= $groupId ?><?= $searchString ?>&user_id=<?= (int)$u['id'] ?>">
                                                            <i class="fal fa-paper-plane"></i> <?= $st === null ? 'Send Invite' : 'Invite Again' ?>
                                                        </a>
                                                    <?php } ?>
                                                </td>
                                            </tr>
                                            <?php endforeach; else: ?>
                                            <tr><td colspan="8" align="center"><span class="mandatory">No users available to invite.</span></td></tr>
                                            <?php endif; ?>
                                        </tbody>
                                    </table>
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

                <?php } ?>
            </div>
        </div>
    </div>
</body>
<?php include_once("./includes/js-source.php"); ?>
<script>
    $(document).on('click', '.sendInviteBtn', function () {
        if (confirm('Send group invitation to this user?')) {
            window.location.href = $(this).data('href');
        }
    });
    $(document).on('click', '.cancelInviteBtn', function () {
        if (confirm('Cancel this pending invitation?')) {
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
</script>
</html>
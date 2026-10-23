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

        // ================= SUCCESS / INFO MESSAGE (from redirect) =================
        $msgCode = (int)($_REQUEST['m'] ?? 0);
        $messages = [
            1 => ['text' => 'Invitation sent successfully.', 'class' => 'badge_success'],
            2 => ['text' => 'Invitation cancelled.',         'class' => 'badge_secondary'],
        ];
        ?>

        <?php if ($msgCode > 0 && isset($messages[$msgCode])): ?>
            <div class="alert_msg_wrap mb-3" id="inviteFlashMsg">
                <n class="badge_padding <?= $messages[$msgCode]['class'] ?> w-max-con text-uppercase">
                    <i class="fal fa-check-circle"></i> <?= htmlspecialchars($messages[$msgCode]['text']) ?>
                </n>
            </div>
        <?php endif; ?>

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
                    <span class="mandatory">Group not found, inactive, or not private. Please open this page from Manage Groups &rarr; Invite Users.</span>
                <?php } else { ?>

                <!-- ================= USER SEARCH + INVITE ================= -->
                <div class="com_info_box active mb-3">
                    <div class="com_info_box_grid">
                        <div class="com_info_box_grid_box">
                            <h5 class="com_info_box_head">
                                <n><span class="text_success"><i class="fal fa-user-plus"></i></span> Users available to invite</n>
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
                                // 2 in SELECT (pending subqueries), 2 in WHERE (exclusions), then search words
                                $params = [
                                    ['FILD' => 'g1', 'DATA' => $groupId, 'TYP' => 'i'],
                                    ['FILD' => 'g2', 'DATA' => $groupId, 'TYP' => 'i'],
                                    ['FILD' => 'g3', 'DATA' => $groupId, 'TYP' => 'i'],
                                    ['FILD' => 'g4', 'DATA' => $groupId, 'TYP' => 'i'],
                                ];

                                // users who are already active members OR have accepted an invite are NOT listed
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

                                $usql = [];
                                $usql['QUERY'] = "
                                    SELECT u.id, u.user_full_name, u.user_email_id, u.user_mobile_no,
                                        (SELECT gi.status FROM rcg_group_invitations gi
                                         WHERE gi.group_id = ? AND gi.invited_user_id = u.id AND gi.status='pending'
                                         ORDER BY gi.id DESC LIMIT 1) AS pending_status,
                                        (SELECT gi.id FROM rcg_group_invitations gi
                                         WHERE gi.group_id = ? AND gi.invited_user_id = u.id AND gi.status='pending'
                                         ORDER BY gi.id DESC LIMIT 1) AS pending_invitation_id
                                    FROM rcg_user_registration u
                                    $where
                                    ORDER BY u.id DESC
                                    LIMIT 50
                                ";
                                foreach ($params as $p) { $usql['PARAM'][] = $p; }

                                $users = $mycms->sql_select($usql);
                                ?>

                                <div class="table_wrap">
                                    <table>
                                        <thead>
                                            <tr>
                                                <th class="sl">#</th>
                                                <th>Name</th>
                                                <th>Email</th>
                                                <th>Mobile</th>
                                                <th>Status</th>
                                                <th class="action text-right">Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php if ($users): $i = 0; foreach ($users as $u): $i++; ?>
                                            <tr>
                                                <td class="sl"><?= $i ?></td>
                                                <td><?= htmlspecialchars($u['user_full_name'] ?? '—') ?></td>
                                                <td><?= htmlspecialchars($u['user_email_id'] ?? '—') ?></td>
                                                <td><?= htmlspecialchars($u['user_mobile_no'] ?? '—') ?></td>
                                                <td>
                                                    <?php if ($u['pending_status'] === 'pending') { ?>
                                                        <n class="badge_padding badge_dark w-max-con text-uppercase">Invite Pending</n>
                                                    <?php } else { ?>
                                                        <n class="badge_padding badge_secondary w-max-con text-uppercase">Not Invited</n>
                                                    <?php } ?>
                                                </td>
                                                <td class="action text-right">
                                                    <?php if ($u['pending_status'] === 'pending') { ?>
                                                        <a href="javascript:void(0);" class="cancelInviteBtn badge_danger action-transparent br-5 w-auto"
                                                           data-href="group_invite.process.php?act=cancel_invite&group_id=<?= $groupId ?><?= $searchString ?>&invitation_id=<?= (int)$u['pending_invitation_id'] ?>">
                                                            <i class="fal fa-times"></i> Cancel Invitation
                                                        </a>
                                                    <?php } else { ?>
                                                        <a href="javascript:void(0);" class="sendInviteBtn badge_primary action-transparent br-5 w-auto"
                                                           data-href="group_invite.process.php?act=send_invite&group_id=<?= $groupId ?><?= $searchString ?>&user_id=<?= (int)$u['id'] ?>">
                                                            <i class="fal fa-paper-plane"></i> Send Invite
                                                        </a>
                                                    <?php } ?>
                                                </td>
                                            </tr>
                                            <?php endforeach; else: ?>
                                            <tr><td colspan="6" align="center"><span class="mandatory">No users available to invite.</span></td></tr>
                                            <?php endif; ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- ================= INVITATIONS SENT FOR THIS GROUP ================= -->
                <div class="com_info_box active">
                    <div class="com_info_box_grid">
                        <div class="com_info_box_grid_box">
                            <h5 class="com_info_box_head">
                                <n><span class="text_primary"><i class="fal fa-envelope"></i></span> Invitations Sent</n>
                            </h5>
                            <div class="com_info_box_inner">
                                <?php
                                $invsql = [];
                                $invsql['QUERY'] = "SELECT gi.id, gi.status, gi.created_at, gi.responded_at,
                                                            u.id AS invited_user_id, u.user_full_name, u.user_email_id
                                                     FROM rcg_group_invitations gi
                                                     INNER JOIN rcg_user_registration u ON u.id = gi.invited_user_id
                                                     WHERE gi.group_id = ?
                                                     ORDER BY gi.id DESC
                                                     LIMIT 50";
                                $invsql['PARAM'] = [['FILD' => 'group_id', 'DATA' => $groupId, 'TYP' => 'i']];
                                $invites = $mycms->sql_select($invsql);
                                ?>
                                <div class="table_wrap">
                                    <table>
                                        <thead>
                                            <tr>
                                                <th class="sl">#</th>
                                                <th>User</th>
                                                <th>Status</th>
                                                <th>Sent On</th>
                                                <th>Responded On</th>
                                                <th class="action text-right">Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php if ($invites): $i = 0; foreach ($invites as $iv): $i++; ?>
                                            <tr>
                                                <td class="sl"><?= $i ?></td>
                                                <td><?= htmlspecialchars($iv['user_full_name']) ?> <br><small><?= htmlspecialchars($iv['user_email_id']) ?></small></td>
                                                <td>
                                                    <?php
                                                    $badge = ['pending' => 'badge_dark', 'accepted' => 'badge_success', 'declined' => 'badge_danger', 'cancelled' => 'badge_secondary'];
                                                    $cls = $badge[$iv['status']] ?? 'badge_secondary';
                                                    ?>
                                                    <n class="badge_padding <?= $cls ?> w-max-con text-uppercase"><?= htmlspecialchars($iv['status']) ?></n>
                                                </td>
                                                <td><?= date('d-m-Y H:i', strtotime($iv['created_at'])) ?></td>
                                                <td><?= $iv['responded_at'] ? date('d-m-Y H:i', strtotime($iv['responded_at'])) : '—' ?></td>
                                                <td class="action text-right">
                                                    <?php if ($iv['status'] === 'pending') { ?>
                                                        <a href="javascript:void(0);" class="cancelInviteBtn badge_danger action-transparent br-5 w-auto"
                                                           data-href="group_invite.process.php?act=cancel_invite&group_id=<?= $groupId ?><?= $searchString ?>&invitation_id=<?= (int)$iv['id'] ?>">
                                                            <i class="fal fa-times"></i> Cancel
                                                        </a>
                                                    <?php } elseif ($iv['status'] === 'cancelled' || $iv['status'] === 'declined') { ?>
                                                        <a href="javascript:void(0);" class="sendInviteBtn badge_primary action-transparent br-5 w-auto"
                                                           data-href="group_invite.process.php?act=send_invite&group_id=<?= $groupId ?><?= $searchString ?>&user_id=<?= (int)$iv['invited_user_id'] ?>">
                                                            <i class="fal fa-paper-plane"></i> Send Invite
                                                        </a>
                                                    <?php } else { ?>
                                                        —
                                                    <?php } ?>
                                                </td>
                                            </tr>
                                            <?php endforeach; else: ?>
                                            <tr><td colspan="6" align="center"><span class="mandatory">No invitations sent yet.</span></td></tr>
                                            <?php endif; ?>
                                        </tbody>
                                    </table>
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
</script>
<script>
    (function () {
        var flashEl = document.getElementById('inviteFlashMsg');
        if (flashEl) {
            setTimeout(function () {
                flashEl.style.transition = 'opacity 0.5s ease';
                flashEl.style.opacity = '0';
                setTimeout(function () {
                    flashEl.style.display = 'none';
                }, 500);
            }, 3000);
        }
    })();
</script>
</html>
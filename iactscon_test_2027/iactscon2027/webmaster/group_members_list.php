<?php
include_once("./includes/source.php");
include_once('./includes/init.php');
include_once('./includes/function.workshop.php');

$groupId    = (int)($_REQUEST['group_id'] ?? 0);
$searchName = trim($_REQUEST['search_name'] ?? '');

$groupRow = null;
if ($groupId > 0) {
    $gchk = [];
    $gchk['QUERY'] = "SELECT `id`, `group_name`, `group_type`, `created_by` FROM `rcg_groups` WHERE `id`=? AND `status` != 'D'";
    $gchk['PARAM'] = [['FILD' => 'id', 'DATA' => $groupId, 'TYP' => 'i']];
    $found = $mycms->sql_select($gchk);
    if (!empty($found)) {
        $groupRow = $found[0];
    }
}

// role / chat status / block / promote / demote exist only for private groups.
// Public groups (Cardiac Talks Topics) go back to their own list. Visibility itself is never displayed.
$isPrivate   = ($groupRow && $groupRow['group_type'] === 'private');
$backUrl     = ($groupRow && !$isPrivate) ? 'cardiac_talks_topics.php' : 'group_list.php';
$backLabel   = ($groupRow && !$isPrivate) ? 'Back to Topics' : 'Back to Groups';
$parentLabel = ($groupRow && !$isPrivate) ? 'Cardiac Talks Topics' : 'Manage Groups';
$groupNameSafe = $groupRow ? htmlspecialchars($groupRow['group_name']) : '';
?>

<body>
    <?php include_once("./includes/left-menu.php"); ?>
    <header>
        <h2>Group Members<?= $groupRow ? ' — ' . $groupNameSafe : '' ?></h2>
        <?php include_once("./includes/header_right.php"); ?>
    </header>
    <?php
    // One-time flash alert set by group_members_list.process.php
    $flash = $_SESSION['members_alert'] ?? null;
    unset($_SESSION['members_alert']);
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
        $pageKeyVal = (empty($_REQUEST[$pageKey])) ? 0 : (int)$_REQUEST[$pageKey];

        // group_id is added explicitly in the links below
        $searchArray = array();
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
                        <li class="breadcrumb-item"><a href="<?= $backUrl ?>"><?= $parentLabel ?></a></li>
                        <?php if ($groupRow) { ?>
                        <li class="breadcrumb-item"><a href="#"><?= $groupNameSafe ?></a></li>
                        <?php } ?>
                        <li class="breadcrumb-item active" aria-current="page"><a href="#">Group Members</a></li>
                    </ol>
                </nav>
                <h2>Group Members<?= $groupRow ? ' — ' . $groupNameSafe : '' ?></h2>
            </div>
            <div class="page_top_wrap_right">
                <a href="<?= $backUrl ?>" class="badge_dark"><i class="fal fa-arrow-left"></i> <?= $backLabel ?></a>
                <?php if ($isPrivate) { ?>
                    <a href="group_invite.php?group_id=<?= $groupId ?>" class="badge_primary"><i class="fal fa-user-plus"></i> Invite Users</a>
                <?php } ?>
            </div>
        </div>

        <div class="com_info_wrap">
            <div class="com_info_right" style="width:100%;">

                <?php if (!$groupRow) { ?>
                    <span class="mandatory">Group not found. Please open this page from Manage Groups &rarr; Group Members.</span>
                <?php } else {

                    // ---------- members query ----------
                    // placeholder order: 1 (group block subquery), 2 (WHERE group), then search words
                    $params = [
                        ['FILD' => 'bg', 'DATA' => $groupId, 'TYP' => 'i'],
                        ['FILD' => 'mg', 'DATA' => $groupId, 'TYP' => 'i'],
                    ];
                    $where = " WHERE gm.group_id = ? AND gm.status = 'A' ";

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

                    $sql = [];
                    $sql['QUERY'] = "
                        SELECT gm.id, gm.user_id, gm.role, gm.joined_at,
                               u.user_full_name, u.user_email_id, u.user_mobile_no,
                               (SELECT cb.restriction FROM rcg_chat_blocks cb
                                WHERE cb.block_type = 'admin' AND cb.status = 'A'
                                  AND cb.blocked_user_id = gm.user_id AND cb.group_id = ?
                                LIMIT 1) AS group_block,
                               (SELECT cb2.restriction FROM rcg_chat_blocks cb2
                                WHERE cb2.block_type = 'admin' AND cb2.status = 'A'
                                  AND cb2.blocked_user_id = gm.user_id AND cb2.group_id = 0
                                LIMIT 1) AS global_block
                        FROM rcg_group_members gm
                        INNER JOIN rcg_user_registration u ON u.id = gm.user_id
                        $where
                        ORDER BY gm.role DESC, gm.joined_at ASC
                    ";
                    foreach ($params as $p) { $sql['PARAM'][] = $p; }

                    $result = $mycms->sql_select_paginated($indexVal, $sql, 50, $restrt);

                    $restrictionLabel = ['no_chat' => 'Cannot open chat', 'read_only' => 'View only'];
                ?>

                <div class="com_info_box active" id="groupMembers">
                    <div class="com_info_box_grid">
                        <div class="com_info_box_grid_box">
                            <h5 class="com_info_box_head">
                                <n><span class="text_success"><i class="fal fa-users"></i></span> Active Members — <?= $groupNameSafe ?></n>
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

                                <div class="table_wrap">
                                    <table>
                                        <thead>
                                            <tr>
                                                <th class="sl">Sl. No.</th>
                                                <th>Name</th>
                                                <th>Email</th>
                                                <th>Mobile</th>
                                                <?php if ($isPrivate) { ?>
                                                <th>Role</th>
                                                <th>Chat Status</th>
                                                <?php } ?>
                                                <th>Joined On</th>
                                                <th class="action text-right">Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                            if ($result) {
                                                $i = 0;
                                                foreach ($result as $m) {
                                                    $i++;
                                                    $isOwner = ((int)$m['user_id'] === (int)$groupRow['created_by']);
                                            ?>
                                            <tr>
                                                <td class="sl"><?= $i + ($pageKeyVal * 50) ?></td>
                                                <td><?= htmlspecialchars($m['user_full_name'] ?? '—') ?></td>
                                                <td><?= htmlspecialchars($m['user_email_id'] ?? '—') ?></td>
                                                <td><?= htmlspecialchars($m['user_mobile_no'] ?? '—') ?></td>
                                                <?php if ($isPrivate) { ?>
                                                <td style="text-align:center;">
                                                    <?php if ($isOwner) { ?>
                                                        <n class="badge_padding badge_dark w-max-con text-uppercase">Owner</n>
                                                    <?php } elseif ($m['role'] === 'moderator') { ?>
                                                        <n class="badge_padding badge_primary w-max-con text-uppercase">Moderator</n>
                                                    <?php } else { ?>
                                                        <n class="badge_padding badge_secondary w-max-con text-uppercase">Member</n>
                                                    <?php } ?>
                                                </td>
                                                <td style="text-align:center;">
                                                    <?php
                                                    $hasBadge = false;
                                                    if (!empty($m['group_block'])) {
                                                        $hasBadge = true;
                                                        echo '<n class="badge_padding badge_danger w-max-con text-uppercase">Blocked: ' . htmlspecialchars($restrictionLabel[$m['group_block']] ?? $m['group_block']) . '</n><br>';
                                                    }
                                                    if (!empty($m['global_block'])) {
                                                        $hasBadge = true;
                                                        echo '<n class="badge_padding badge_dark w-max-con text-uppercase">All chats: ' . htmlspecialchars($restrictionLabel[$m['global_block']] ?? $m['global_block']) . '</n>';
                                                    }
                                                    if (!$hasBadge) {
                                                        echo '<n class="badge_padding badge_success w-max-con text-uppercase">Normal</n>';
                                                    }
                                                    ?>
                                                </td>
                                                <?php } ?>
                                                <td style="text-align:center;"><?= $m['joined_at'] ? date('d-m-Y H:i', strtotime($m['joined_at'])) : '—' ?></td>
                                                <td class="action">
                                                    <?php if ($isOwner) { ?>
                                                        <span class="mandatory">Group Owner</span>
                                                    <?php } else { ?>
                                                    <div class="action_div dropdown" role="menu" aria-label="Actions">
                                                        <button class="icon_hover badge_dark action-transparent dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false"><?php ellips(); ?></button>
                                                        <ul class="dropdown-menu">
                                                            <?php if ($isPrivate) { ?>
                                                                <?php if ($m['role'] === 'moderator') { ?>
                                                                    <a href="javascript:void(0);" class="demoteBtn icon_hover badge_secondary action-transparent"
                                                                       data-href="group_members_list.process.php?act=demote_member&group_id=<?= $groupId ?><?= $searchString ?>&user_id=<?= (int)$m['user_id'] ?>">
                                                                        <i class="fal fa-arrow-down"></i> Demote to Member
                                                                    </a>
                                                                <?php } else { ?>
                                                                    <a href="javascript:void(0);" class="promoteBtn icon_hover badge_primary action-transparent"
                                                                       data-href="group_members_list.process.php?act=promote_member&group_id=<?= $groupId ?><?= $searchString ?>&user_id=<?= (int)$m['user_id'] ?>">
                                                                        <i class="fal fa-arrow-up"></i> Promote to Moderator
                                                                    </a>
                                                                <?php } ?>

                                                                <?php if (!empty($m['group_block'])) { ?>
                                                                    <a href="javascript:void(0);" class="unblockBtn icon_hover badge_success action-transparent"
                                                                       data-href="group_members_list.process.php?act=unblock_member&group_id=<?= $groupId ?><?= $searchString ?>&user_id=<?= (int)$m['user_id'] ?>">
                                                                        <i class="fal fa-unlock"></i> Unblock
                                                                    </a>
                                                                <?php } else { ?>
                                                                    <a href="javascript:void(0);" class="blockMemberBtn icon_hover badge_danger action-transparent"
                                                                       data-user-id="<?= (int)$m['user_id'] ?>"
                                                                       data-user-name="<?= htmlspecialchars($m['user_full_name'] ?? '') ?>">
                                                                        <i class="fal fa-ban"></i> Block
                                                                    </a>
                                                                <?php } ?>
                                                            <?php } ?>

                                                            <a href="javascript:void(0);" class="removeMemberBtn icon_hover badge_danger action-transparent"
                                                               data-href="group_members_list.process.php?act=remove_member&group_id=<?= $groupId ?><?= $searchString ?>&user_id=<?= (int)$m['user_id'] ?>">
                                                                <i class="fal fa-user-minus"></i> Remove from Group
                                                            </a>
                                                        </ul>
                                                    </div>
                                                    <?php } ?>
                                                </td>
                                            </tr>
                                            <?php
                                                }
                                            } else {
                                            ?>
                                            <tr><td colspan="<?= $isPrivate ? 8 : 6 ?>" align="center"><span class="mandatory">No members found.</span></td></tr>
                                            <?php } ?>
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

                <!-- ================= BLOCK POPUP (moved to <body> by JS so no parent can clip or cover it) ================= -->
                <div id="blockOverlay" style="display:none;position:fixed;top:0;left:0;right:0;bottom:0;background:rgba(0,0,0,.75);z-index:99999;align-items:center;justify-content:center;padding:15px;">
                    <form method="post" action="group_members_list.process.php"
                          style="background:#151515;color:#fff;border:1px solid #333;border-radius:12px;width:480px;max-width:100%;padding:22px;box-shadow:0 10px 40px rgba(0,0,0,.6);">
                        <input type="hidden" name="act" value="block_member" />
                        <input type="hidden" name="group_id" value="<?= $groupId ?>" />
                        <input type="hidden" name="user_id" id="blockUserId" value="" />
                        <input type="hidden" name="<?= $pageKey ?>" value="<?= $pageKeyVal ?>" />
                        <input type="hidden" name="search_name" value="<?= htmlspecialchars($searchName) ?>" />

                        <h5 style="margin:0 0 4px 0;color:#fff;font-size:18px;">Block <span id="blockUserName"></span></h5>
                        <p style="margin:0 0 18px 0;color:#aaa;font-size:13px;">Group: <?= $groupNameSafe ?></p>

                        <div class="frm_grp" style="margin-bottom:14px;">
                            <p class="frm-head" style="color:#ddd;">Restriction <span class="mandatory">*</span></p>
                            <select name="restriction" required style="width:100%;">
                                <option value="read_only">View only - can see messages, cannot message or react</option>
                                <option value="no_chat">Cannot open this group's chat</option>
                            </select>
                        </div>
                        <div class="frm_grp" style="margin-bottom:20px;">
                            <p class="frm-head" style="color:#ddd;">Reason (optional)</p>
                            <input type="text" name="reason" maxlength="255" style="width:100%;" />
                        </div>

                        <div style="display:flex;justify-content:flex-end;gap:10px;">
                            <button type="button" id="blockCancel" class="badge_dark">Cancel</button>
                            <button type="submit" class="badge_danger"><i class="fal fa-ban"></i> Block</button>
                        </div>
                    </form>
                </div>

                <?php } ?>
            </div>
        </div>
    </div>
</body>
<?php include_once("./includes/js-source.php"); ?>
<script>
    function confirmGo(selector, message) {
        $(document).on('click', selector, function () {
            if (confirm(message)) {
                window.location.href = $(this).data('href');
            }
        });
    }
    confirmGo('.removeMemberBtn', 'Remove this user from the group? They will lose access immediately.');
    confirmGo('.unblockBtn', 'Unblock this user in this group?');
    confirmGo('.promoteBtn', 'Promote this member to moderator?');
    confirmGo('.demoteBtn', 'Demote this moderator to regular member?');

    // move popup to <body> so it sits above everything
    $('#blockOverlay').appendTo('body');

    function closeBlockPopup() { $('#blockOverlay').hide(); }

    $(document).on('click', '.blockMemberBtn', function () {
        $('#blockUserId').val($(this).data('user-id'));
        $('#blockUserName').text($(this).data('user-name'));
        $('#blockOverlay').css('display', 'flex');
    });
    $(document).on('click', '#blockCancel', closeBlockPopup);
    $(document).on('click', '#blockOverlay', function (e) {
        if (e.target === this) closeBlockPopup();
    });
    $(document).on('keydown', function (e) {
        if (e.key === 'Escape') closeBlockPopup();
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
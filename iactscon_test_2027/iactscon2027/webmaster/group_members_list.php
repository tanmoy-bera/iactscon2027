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
?>

<body>
    <?php include_once("./includes/left-menu.php"); ?>
    <header>
        <h2>Group Members</h2>
        <?php include_once("./includes/header_right.php"); ?>
    </header>
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

        // ================= FLASH MESSAGE (from redirect) =================
        $msgCode  = (int)($_REQUEST['m'] ?? 0);
        $messages = [
            1 => ['text' => 'Member removed from group.',    'class' => 'badge_danger'],
            2 => ['text' => 'User blocked in this group.',   'class' => 'badge_danger'],
            3 => ['text' => 'User unblocked.',               'class' => 'badge_success'],
            4 => ['text' => 'Member promoted to moderator.', 'class' => 'badge_success'],
            5 => ['text' => 'Moderator demoted to member.',  'class' => 'badge_secondary'],
        ];
        ?>

        <?php if ($msgCode > 0 && isset($messages[$msgCode])): ?>
            <div class="alert_msg_wrap mb-3" id="membersFlashMsg">
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
                        <li class="breadcrumb-item active" aria-current="page"><a href="#">Group Members</a></li>
                    </ol>
                </nav>
                <h2>Group Members<?= $groupRow ? ' — ' . htmlspecialchars($groupRow['group_name']) : '' ?></h2>
            </div>
            <div class="page_top_wrap_right">
                <a href="group_list.php" class="badge_dark"><i class="fal fa-arrow-left"></i> Back to Groups</a>
                <?php if ($groupRow && $groupRow['group_type'] == 'private') { ?>
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
                                <n><span class="text_success"><i class="fal fa-users"></i></span> Active Members</n>
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
                                                <th class="sl">#</th>
                                                <th>Name</th>
                                                <th>Email</th>
                                                <th>Mobile</th>
                                                <th>Role</th>
                                                <th>Chat Status</th>
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
                                                <td>
                                                    <?php if ($isOwner) { ?>
                                                        <n class="badge_padding badge_dark w-max-con text-uppercase">Owner</n>
                                                    <?php } elseif ($m['role'] === 'moderator') { ?>
                                                        <n class="badge_padding badge_primary w-max-con text-uppercase">Moderator</n>
                                                    <?php } else { ?>
                                                        <n class="badge_padding badge_secondary w-max-con text-uppercase">Member</n>
                                                    <?php } ?>
                                                </td>
                                                <td>
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
                                                <td><?= $m['joined_at'] ? date('d-m-Y H:i', strtotime($m['joined_at'])) : '—' ?></td>
                                                <td class="action text-right">
                                                    <?php if ($isOwner) { ?>
                                                        <span class="mandatory">Group Owner</span>
                                                    <?php } else { ?>
                                                    <div class="action_div dropdown" role="menu" aria-label="Actions">
                                                        <button class="icon_hover badge_dark action-transparent dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false"><?php ellips(); ?></button>
                                                        <ul class="dropdown-menu">
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
                                            <tr><td colspan="8" align="center"><span class="mandatory">No members found.</span></td></tr>
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

                <!-- ================= BLOCK MODAL ================= -->
                <div class="modal fade" id="blockMemberModal" tabindex="-1" aria-hidden="true">
                    <div class="modal-dialog">
                        <form method="post" action="group_members_list.process.php" class="modal-content">
                            <input type="hidden" name="act" value="block_member" />
                            <input type="hidden" name="group_id" value="<?= $groupId ?>" />
                            <input type="hidden" name="user_id" id="blockUserId" value="" />
                            <input type="hidden" name="<?= $pageKey ?>" value="<?= $pageKeyVal ?>" />
                            <input type="hidden" name="search_name" value="<?= htmlspecialchars($searchName) ?>" />
                            <div class="modal-header">
                                <h5 class="modal-title">Block <span id="blockUserName"></span> in <?= htmlspecialchars($groupRow['group_name']) ?></h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <div class="frm_grp mb-3">
                                    <p class="frm-head">Restriction <span class="mandatory">*</span></p>
                                    <select name="restriction" required>
                                        <option value="read_only">View only - can see messages, cannot message or react</option>
                                        <option value="no_chat">Cannot open this group's chat</option>
                                    </select>
                                </div>
                                <div class="frm_grp">
                                    <p class="frm-head">Reason (optional)</p>
                                    <input type="text" name="reason" maxlength="255" />
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="badge_dark" data-bs-dismiss="modal">Cancel</button>
                                <button type="submit" class="badge_danger"><i class="fal fa-ban"></i> Block</button>
                            </div>
                        </form>
                    </div>
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

    $(document).on('click', '.blockMemberBtn', function () {
        $('#blockUserId').val($(this).data('user-id'));
        $('#blockUserName').text($(this).data('user-name'));
        bootstrap.Modal.getOrCreateInstance(document.getElementById('blockMemberModal')).show();
    });

    (function () {
        var flashEl = document.getElementById('membersFlashMsg');
        if (flashEl) {
            setTimeout(function () {
                flashEl.style.transition = 'opacity 0.5s ease';
                flashEl.style.opacity = '0';
                setTimeout(function () { flashEl.style.display = 'none'; }, 500);
            }, 3000);
        }
    })();
</script>
</html>
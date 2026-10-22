<?php
include_once("./includes/source.php");
include_once('./includes/init.php');
include_once('./includes/role_helper.php');

$currentAdminId = $mycms->getLoggedUserId();
?>

<body>
    <?php include_once("./includes/left-menu.php"); ?>
    <header>
        <h2>Manage Moderator</h2>
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

        foreach ($searchArray as $searchKey => $searchVal) {
            $searchString .= "&" . $searchKey . "=" . urlencode($searchVal);
        }

        $searchName = trim($_REQUEST['search_name'] ?? '');
        ?>

        <div class="page_top_wrap mb-3">
            <div class="page_top_wrap_left">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="#">Community</a></li>
                        <li class="breadcrumb-item active" aria-current="page"><a href="#">Manage Moderator</a></li>
                    </ol>
                </nav>
                <h2>Registered Users — Manage Moderator</h2>
            </div>
        </div>

        <div class="com_info_wrap">
            <div class="com_info_right" style="width:100%;">
                <?php
                $where  = " WHERE u.status = 'A' ";
                $params = [];

                if ($searchName !== '') {
                    $where .= " AND (u.user_full_name LIKE ? 
                                     OR u.user_email_id LIKE ? 
                                     OR u.user_mobile_no LIKE ?)";
                    $like = "%{$searchName}%";
                    $params[] = ['FILD' => 'n1', 'DATA' => $like, 'TYP' => 's'];
                    $params[] = ['FILD' => 'n2', 'DATA' => $like, 'TYP' => 's'];
                    $params[] = ['FILD' => 'n3', 'DATA' => $like, 'TYP' => 's'];
                }

                $sql = [];
                $sql['QUERY'] = "
                    SELECT u.id, u.user_unique_sequence, u.user_full_name,
                           u.user_email_id, u.user_mobile_no,
                           (SELECT COUNT(*) FROM rcg_user_roles r
                            WHERE r.user_id=u.id 
                              AND r.role='moderator' 
                              AND r.status='A') AS is_moderator
                    FROM rcg_user_registration u
                    $where
                    ORDER BY u.id DESC
                ";
                foreach ($params as $p) { $sql['PARAM'][] = $p; }

                $result = $mycms->sql_select_paginated($indexVal, $sql, 50, $restrt);
                ?>
                <div class="com_info_box active" id="moderatorListing">
                    <div class="com_info_box_grid">
                        <div class="com_info_box_grid_box">
                            <h5 class="com_info_box_head">
                                <n><span class="text_primary"><i class="fal fa-user-shield"></i></span> Registered Users</n>
                            </h5>
                            <div class="com_info_box_inner">

                                <!-- Search / filter -->
                                <form method="get" action="<?= $_SERVER['PHP_SELF'] ?>" class="mb-3">
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
                                                <th>Sequence</th>
                                                <th>Name</th>
                                                <th>Email</th>
                                                <th>Mobile</th>
                                                <th>Role</th>
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
                                                <td><?= htmlspecialchars($row['user_unique_sequence'] ?? '—') ?></td>
                                                <td><?= htmlspecialchars($row['user_full_name'] ?? '—') ?></td>
                                                <td><?= htmlspecialchars($row['user_email_id'] ?? '—') ?></td>
                                                <td><?= htmlspecialchars($row['user_mobile_no'] ?? '—') ?></td>
                                                <td>
                                                    <?php if ($row['is_moderator']) { ?>
                                                        <n class="badge_padding badge_success w-max-con text-uppercase">Moderator</n>
                                                    <?php } else { ?>
                                                        <n class="badge_padding badge_dark w-max-con text-uppercase">User</n>
                                                    <?php } ?>
                                                </td>
                                                <td class="action">
                                                    <div class="action_div dropdown" role="menu" aria-label="Actions">
                                                        <button class="icon_hover badge_dark action-transparent dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false"><?php ellips(); ?></button>
                                                        <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                                                            <?php if ($row['is_moderator']) { ?>
                                                            <a href="javascript:void(0);" class="removeModeratorBtn icon_hover badge_danger action-transparent br-5 w-auto" data-href="moderator_process.php?act=remove&user_id=<?= $row['id'] ?>">
                                                                <i class="fal fa-user-times"></i> Remove Moderator
                                                            </a>
                                                            <?php } else { ?>
                                                            <a href="javascript:void(0);" class="makeModeratorBtn icon_hover badge_secondary action-transparent br-5 w-auto" data-href="moderator_process.php?act=make&user_id=<?= $row['id'] ?>">
                                                                <i class="fal fa-user-check"></i> Make Moderator
                                                            </a>
                                                            <?php } ?>
                                                        </ul>
                                                    </div>
                                                </td>
                                            </tr>
                                            <?php
                                                }
                                            } else {
                                            ?>
                                            <tr>
                                                <td colspan="7" align="center">
                                                    <span class="mandatory">No users found.</span>
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
            </div>
        </div>
    </div>
</body>
<?php include_once("./includes/js-source.php"); ?>
<script>
    $(document).on('click', '.makeModeratorBtn', function () {
        if (confirm('Make this user a moderator?')) {
            window.location.href = $(this).data('href');
        }
    });

    $(document).on('click', '.removeModeratorBtn', function () {
        if (confirm('Remove moderator role from this user?')) {
            window.location.href = $(this).data('href');
        }
    });
</script>
</html>
<?php
include_once("includes/source.php");
include_once('includes/init.php');
include_once(__DIR__ . "/../includes/function.registration.php");
include_once(__DIR__ . "/../includes/function.delegate.php");

/* ---------------------------------------------------------------------
   Read current filter/search/sort/category state from the URL.
   Everything below (list query, tab hrefs, pre-filled input values)
   is driven from these — a normal full page load/reload, exactly like
   the pagination links you already confirmed work correctly.
   --------------------------------------------------------------------- */
$search       = isset($_GET['search'])        ? trim($_GET['search'])        : '';
$filterName   = isset($_GET['filter_name'])    ? trim($_GET['filter_name'])   : '';
$filterEmail  = isset($_GET['filter_email'])   ? trim($_GET['filter_email'])  : '';
$filterMobile = isset($_GET['filter_mobile'])  ? trim($_GET['filter_mobile']) : '';
$filterCode   = isset($_GET['filter_code'])    ? trim($_GET['filter_code'])   : '';
$currentCategoryId = (isset($_GET['category_id']) && $_GET['category_id'] !== '')
    ? (string) $_GET['category_id']
    : '';
$sort = isset($_GET['sort']) ? trim($_GET['sort']) : 'newest';

/* ---------------------------------------------------------------------
   Build a "?..." url that keeps every OTHER current query param intact
   and just overrides the ones passed in $overrides (empty/null value
   removes that param). Filter/search/sort/tab changes always drop the
   page number so you land on page 1 of the new result set.
   --------------------------------------------------------------------- */
function buildQueryUrl($overrides = array(), $resetPage = true) {
    $params = $_GET;
    if ($resetPage) {
        unset($params['_pgnR001_']);
    }
    foreach ($overrides as $key => $value) {
        if ($value === null || $value === '') {
            unset($params[$key]);
        } else {
            $params[$key] = $value;
        }
    }
    $qs = http_build_query($params);
    return $qs !== '' ? ('?' . $qs) : '?';
}

/* ---------------------------------------------------------------------
   Categories (for the summary boxes + tabs)
   --------------------------------------------------------------------- */
$sqlCat = array();
$sqlCat['QUERY']   = "SELECT `id`, `category_name`, `category_description`
                        FROM `art_exhibition_category`
                        WHERE `status` = ?
                        ORDER BY `id` ASC";
$sqlCat['PARAM'][] = array('FILD' => 'status', 'DATA' => 'A', 'TYP' => 's');
$categories = $mycms->sql_select($sqlCat);
if (empty($categories)) {
    $categories = array();
}

$categoryIconMap = array(
    'NATURE, LANDSCAPE'               => 'fal fa-mountains',
    'WILDLIFE, BIRDS'                 => 'fal fa-dove',
    'STREET & TRAVEL'                 => 'fal fa-compass',
    'PORTRAIT / PAINTING / SKETCHES'  => 'fal fa-palette',
);

/* ---------------------------------------------------------------------
   Counts per category + overall total (active submissions only).
   These are OVERALL totals, independent of any current search/filter,
   by design — they describe the whole dataset, not the current view.
   --------------------------------------------------------------------- */
$sqlCounts = array();
$sqlCounts['QUERY']   = "SELECT `artCategoryId`, COUNT(*) AS cnt
                          FROM `art_exhibition_submissions`
                          WHERE `status` = ?
                          GROUP BY `artCategoryId`";
$sqlCounts['PARAM'][] = array('FILD' => 'status', 'DATA' => 'A', 'TYP' => 's');
$countRows = $mycms->sql_select($sqlCounts);

$countsByCategory = array();
$totalCount = 0;
if (!empty($countRows)) {
    foreach ($countRows as $row) {
        $countsByCategory[(int) $row['artCategoryId']] = (int) $row['cnt'];
        $totalCount += (int) $row['cnt'];
    }
}

/* ---------------------------------------------------------------------
   Submission list — filtered, sorted, and paginated. This is a normal
   full-page GET request each time (search box / filter box / sort /
   tabs / pagination all navigate here with query params), so the
   framework's own pagination + link generation works exactly like it
   already does for you today.
   --------------------------------------------------------------------- */
$sqlList = array();
$sqlList['QUERY'] = "SELECT `id`, `submissionCode`, `artCategoryId`, `artCategoryName`,
                             `user_full_name`, `user_email_id`, `user_mobile_isd_code`,
                             `user_mobile_no`, `user_registration_no`, `gDriveLink`,
                             `photographTitle`, `notes`, `created_dateTime`
                      FROM `art_exhibition_submissions`
                      WHERE `status` = ?";
$sqlList['PARAM'][] = array('FILD' => 'status', 'DATA' => 'A', 'TYP' => 's');

if ($currentCategoryId !== '' && ctype_digit($currentCategoryId)) {
    $sqlList['QUERY']   .= " AND `artCategoryId` = ?";
    $sqlList['PARAM'][]  = array('FILD' => 'artCategoryId', 'DATA' => $currentCategoryId, 'TYP' => 'i');
}
if ($filterName !== '') {
    $sqlList['QUERY']   .= " AND `user_full_name` LIKE ?";
    $sqlList['PARAM'][]  = array('FILD' => 'user_full_name', 'DATA' => '%' . $filterName . '%', 'TYP' => 's');
}
if ($filterEmail !== '') {
    $sqlList['QUERY']   .= " AND `user_email_id` LIKE ?";
    $sqlList['PARAM'][]  = array('FILD' => 'user_email_id', 'DATA' => '%' . $filterEmail . '%', 'TYP' => 's');
}
if ($filterMobile !== '') {
    $sqlList['QUERY']   .= " AND CONCAT(`user_mobile_isd_code`, `user_mobile_no`) LIKE ?";
    $sqlList['PARAM'][]  = array('FILD' => 'user_mobile_no', 'DATA' => '%' . $filterMobile . '%', 'TYP' => 's');
}
if ($filterCode !== '') {
    $sqlList['QUERY']   .= " AND `submissionCode` LIKE ?";
    $sqlList['PARAM'][]  = array('FILD' => 'submissionCode', 'DATA' => '%' . $filterCode . '%', 'TYP' => 's');
}
if ($search !== '') {
    $sqlList['QUERY']   .= " AND (`user_full_name` LIKE ? OR `user_email_id` LIKE ?
                                   OR `user_mobile_no` LIKE ? OR `submissionCode` LIKE ?)";
    $sqlList['PARAM'][]  = array('FILD' => 'user_full_name', 'DATA' => '%' . $search . '%', 'TYP' => 's');
    $sqlList['PARAM'][]  = array('FILD' => 'user_email_id',  'DATA' => '%' . $search . '%', 'TYP' => 's');
    $sqlList['PARAM'][]  = array('FILD' => 'user_mobile_no', 'DATA' => '%' . $search . '%', 'TYP' => 's');
    $sqlList['PARAM'][]  = array('FILD' => 'submissionCode', 'DATA' => '%' . $search . '%', 'TYP' => 's');
}

switch ($sort) {
    case 'oldest':    $sqlList['QUERY'] .= " ORDER BY `created_dateTime` ASC";  break;
    case 'title_asc': $sqlList['QUERY'] .= " ORDER BY `photographTitle` ASC";   break;
    case 'name_asc':  $sqlList['QUERY'] .= " ORDER BY `user_full_name` ASC";    break;
    default:          $sqlList['QUERY'] .= " ORDER BY `created_dateTime` DESC"; break;
}

$submissions = $mycms->sql_select_paginated('R001', $sqlList, 25);
if (empty($submissions)) {
    $submissions = array();
}

function initialsFromName($name) {
    $parts = preg_split('/\s+/', trim($name));
    $parts = array_filter($parts);
    if (empty($parts)) return '—';
    $first = mb_substr(reset($parts), 0, 1);
    $last  = count($parts) > 1 ? mb_substr(end($parts), 0, 1) : '';
    return mb_strtoupper($first . $last);
}

// Continuous serial number that survives pagination — derived from the
// same rec-info text the pagination widget already produces.
$serialStart = 1;
if (preg_match('/(\d+)\s*(?:to|-)\s*\d+/i', strip_tags($mycms->paginateRecInfo('R001')), $m)) {
    $serialStart = (int) $m[1];
}
?>

<body>
    <?php include_once("includes/left-menu.php"); ?>
    <header>
        <h2>Id Card</h2>
        <?php include_once("includes/header_right.php"); ?>
    </header>

    <div class="body_wrap">
        <div class="page_top_wrap mb-3">
            <div class="page_top_wrap_left">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="#">Home</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Art Exhibition Submissions</li>
                    </ol>
                </nav>
                <h2>Art Exhibition Submissions</h2>
                <h6>IACTSCON 2027 Kolkata • Real-time overview and link monitoring for jury review</h6>
            </div>
            <div class="page_top_wrap_right">
                <p class="badge_success"><?php user(); ?>Total Submission: <b><?= (int) $totalCount ?></b></p>
            </div>
        </div>

        <ul class="regi_data_grid_ul mb-3">
            <?php foreach ($categories as $cat) :
                $iconClass = isset($categoryIconMap[$cat['category_name']])
                    ? $categoryIconMap[$cat['category_name']]
                    : 'fal fa-image';
                $catCount = isset($countsByCategory[(int) $cat['id']]) ? $countsByCategory[(int) $cat['id']] : 0;
            ?>
            <li>
                <div>
                    <h5><?= htmlspecialchars(ucwords(strtolower($cat['category_name']))) ?></h5>
                    <h4><?= (int) $catCount ?></h4>
                    <h6 class="text_success"><?= htmlspecialchars($cat['category_description']) ?></h6>
                </div>
                <span class="badge_primary"><i class="<?= htmlspecialchars($iconClass) ?>"></i></span>
            </li>
            <?php endforeach; ?>
        </ul>

        <form method="get" id="submission_search_form" class="regi_search_wrap mb-3">
            <?php foreach ($_GET as $k => $v) : if ($k === 'search' || $k === '_pgnR001_') continue; ?>
                <input type="hidden" name="<?= htmlspecialchars($k) ?>" value="<?= htmlspecialchars($v) ?>">
            <?php endforeach; ?>
            <div class="regi_search">
                <i class="fal fa-search"></i>
                <input type="text" name="search" id="submission_search"
                    value="<?= htmlspecialchars($search) ?>"
                    placeholder="Search by Name, Email, Mobile, or Submission Code...">
            </div>
            <div class="regi_search_wrap_btn_box">
                <div class="list-action mt-0">
                    <select name="sort" id="multiOperationSelector" onchange="document.getElementById('submission_search_form').submit()">
                        <option value="newest"    <?= $sort === 'newest'    ? 'selected' : '' ?>>Newest First</option>
                        <option value="oldest"    <?= $sort === 'oldest'    ? 'selected' : '' ?>>Oldest First</option>
                        <option value="title_asc" <?= $sort === 'title_asc' ? 'selected' : '' ?>>ArtWork Title A-Z</option>
                        <option value="name_asc"  <?= $sort === 'name_asc'  ? 'selected' : '' ?>>Name A-Z</option>
                    </select>
                </div>
            </div>
        </form>

        <form method="get" id="advanced_filter_form" class="spot_distribution_form_wrap filter_wrap mb-3">
            <?php foreach ($_GET as $k => $v) :
                if (in_array($k, array('filter_name', 'filter_email', 'filter_mobile', 'filter_code', 'category_id', '_pgnR001_'))) continue;
            ?>
                <input type="hidden" name="<?= htmlspecialchars($k) ?>" value="<?= htmlspecialchars($v) ?>">
            <?php endforeach; ?>
            <div class="form_grid g_6">
                <div class="frm_grp span_2">
                    <p class="frm-head">First Name</p>
                    <input type="text" name="filter_name" id="filter_name" value="<?= htmlspecialchars($filterName) ?>" placeholder="Search by name">
                </div>
                <div class="frm_grp span_2">
                    <p class="frm-head">Email Address</p>
                    <input type="text" name="filter_email" id="filter_email" value="<?= htmlspecialchars($filterEmail) ?>" placeholder="Search by email">
                </div>
                <div class="frm_grp span_2">
                    <p class="frm-head">Mobile Number</p>
                    <input type="text" name="filter_mobile" id="filter_mobile" value="<?= htmlspecialchars($filterMobile) ?>" placeholder="Search by mobile">
                </div>
                <div class="frm_grp span_2">
                    <p class="frm-head">Submission Code</p>
                    <input type="text" name="filter_code" id="filter_code" value="<?= htmlspecialchars($filterCode) ?>" placeholder="e.g. #00008">
                </div>
                <div class="frm_grp span_2">
                    <p class="frm-head">Category</p>
                    <select name="category_id" id="filter_category">
                        <option value="">All Categories</option>
                        <?php foreach ($categories as $cat) : ?>
                            <option value="<?= (int) $cat['id'] ?>" <?= ($currentCategoryId === (string) $cat['id']) ? 'selected' : '' ?>><?= htmlspecialchars($cat['category_name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form_grid g_6 span_2">
                    <div class="span_1">
                        <button type="button" onclick="window.location.href='<?= htmlspecialchars(buildQueryUrl(array(
                            'filter_name' => '', 'filter_email' => '', 'filter_mobile' => '',
                            'filter_code' => '', 'category_id' => '', 'search' => '', 'sort' => '',
                        ))) ?>'" id="filter_reset_btn" class="mi-1 badge_dark side_form_btn"><i class="fal fa-undo"></i></button>
                    </div>
                    <div class="span_2">
                        <button type="submit" id="filter_apply_btn" class="mi-1 badge_success side_form_btn"><?php check() ?>Check</button>
                    </div>
                </div>
            </div>
        </form>

        <div class="regi_search_wrap mb-3">
            <div class="tracking_analytic_tab" id="category_tabs">
                <button type="button" onclick="window.location.href='<?= htmlspecialchars(buildQueryUrl(array('category_id' => ''))) ?>'" class="<?= $currentCategoryId === '' ? 'active' : '' ?>">All(<?= (int) $totalCount ?>)</button>
                <?php foreach ($categories as $cat) :
                    $catCount = isset($countsByCategory[(int) $cat['id']]) ? $countsByCategory[(int) $cat['id']] : 0;
                ?>
                <button type="button" onclick="window.location.href='<?= htmlspecialchars(buildQueryUrl(array('category_id' => (string) $cat['id']))) ?>'" class="<?= $currentCategoryId === (string) $cat['id'] ? 'active' : '' ?>"><?= htmlspecialchars($cat['category_name']) ?>(<?= (int) $catCount ?>)</button>
                <?php endforeach; ?>
            </div>
        </div>

        <div class="spot_listing" id="spot_listing">
            <?php if (empty($submissions)) : ?>
                <p>No submissions found.</p>
            <?php else : ?>
                <?php foreach ($submissions as $index => $sub) :
                    $iconClass = isset($categoryIconMap[$sub['artCategoryName']])
                        ? $categoryIconMap[$sub['artCategoryName']]
                        : 'fal fa-image';
                    $submittedDate = date('d M Y, h:i A', strtotime($sub['created_dateTime']));
                ?>
                <div class="spot_box" data-id="<?= (int) $sub['id'] ?>">
                    <div class="spot_box_mid art_submission_top">
                        <div class="spot_box_bottom_right">
                            <a href="javascript:void(0);" class="popup-btn icon_hover badge_secondary action-transparent">Submission Code: <?= htmlspecialchars($sub['submissionCode']) ?></a>
                            <a href="javascript:void(0);" class="popup-btn icon_hover badge_primary action-transparent"><?= htmlspecialchars($sub['artCategoryName']) ?></a>
                            <?php if ($index === 0 && $serialStart === 1) : ?>
                                <a href="javascript:void(0);" class="popup-btn icon_hover badge_success action-transparent">Latest Entry</a>
                            <?php endif; ?>
                        </div>
                        <div class="regi_contact m-0">
                            <span><?php calendar(); ?><?= htmlspecialchars($submittedDate) ?></span>
                        </div>
                    </div>
                    <div class="spot_box_top">
                        <div class="spot_name d-flex align-items-start">
                            <div class="regi_img_circle">
                                <span><?= (int) ($serialStart + $index) ?></span>
                            </div>
                            <div>
                                <div class="regi_name"><?= htmlspecialchars($sub['user_full_name']) ?></div>
                                <div class="regi_contact">
                                    <span><?php call(); ?><?= htmlspecialchars($sub['user_mobile_isd_code'] . ' ' . $sub['user_mobile_no']) ?></span>
                                    <span><?php email(); ?><?= htmlspecialchars($sub['user_email_id']) ?></span>
                                </div>
                            </div>
                        </div>

                        <div class="spot_details">
                            <div class="spot_details_box registration_details_box">
                                <small>Submission Details:</small>
                                <h6><?= htmlspecialchars($sub['photographTitle']) ?></h6>
                                <h4>
                                    <span>
                                        <n style="color: var(--colordark);"><i><?= htmlspecialchars($sub['notes'] ?: '—') ?></i></n>
                                    </span>
                                </h4>
                            </div>
                            <div class="spot_details_box">
                                <div class="spot_box_bottom_right art_submission_btn_right">
                                    <a href="<?= htmlspecialchars($sub['gDriveLink']) ?>" target="_blank" rel="noopener"
                                        class=" icon_hover badge_secondary action-transparent">
                                        <i class="fal fa-link"></i>Google Drive<i class="fal fa-sign-out"></i>
                                    </a>
                                    <a href="javascript:void(0);" class=" icon_hover badge_danger action-transparent submission-delete-btn" data-id="<?= (int) $sub['id'] ?>">
                                        <?php delete(); ?>Delete
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            <?php endif; ?>
            <div class="bbp-pagination">
              <div class="bbp-pagination-count"><?= $mycms->paginateRecInfo('R001') ?></div>
                <span class="paginationDisplay">
                    <div class="pagination"><a><?= $mycms->paginate('R001', 'pagination') ?></a></div>
                </span>
            </div>
        </div>
    </div>
</body>
<?php include_once("includes/js-source.php"); ?>
<script>
/**
 * IACTSCON 2027 — Art Exhibition Submissions Listing (admin)
 *
 * Search / filters / sort / category tabs / pagination are all plain
 * full-page GET navigations with query params — the exact mechanism
 * you confirmed already works correctly for pagination. No AJAX-driven
 * re-rendering, no fighting the framework's own link generation.
 *
 * Only delete is AJAX (soft-delete has no need for a page reload to
 * submit), and it reloads the page on success so the list, pagination
 * links, and counts all resync from the server correctly afterward.
 */
document.addEventListener("DOMContentLoaded", () => {
    // Debounced auto-submit of the quick search box as you type.
    const searchInput = document.getElementById("submission_search");
    const searchForm  = document.getElementById("submission_search_form");
    if (searchInput && searchForm) {
        let debounceTimer = null;
        searchInput.addEventListener("input", () => {
            clearTimeout(debounceTimer);
            debounceTimer = setTimeout(() => searchForm.submit(), 700);
        });
    }

    // Delete (soft-delete via AJAX, with confirm), then a plain reload
    // so pagination/counts/list all resync from the server.
    document.querySelectorAll(".submission-delete-btn").forEach((btn) => {
        btn.addEventListener("click", () => {
            const id = btn.getAttribute("data-id");
            if (!id) return;

            if (!confirm("Delete this submission? This cannot be undone from this screen.")) {
                return;
            }

            btn.style.pointerEvents = "none";

            fetch("ajax_art_submissions.php", {
                method: "POST",
                headers: { "Content-Type": "application/x-www-form-urlencoded" },
                body: "act=deleteSubmission&id=" + encodeURIComponent(id),
            })
                .then((res) => res.json())
                .then((data) => {
                    if (data.status === "success") {
                        window.location.reload();
                    } else {
                        alert(data.message || "Could not delete this submission.");
                        btn.style.pointerEvents = "";
                    }
                })
                .catch(() => {
                    alert("Network error. Please try again.");
                    btn.style.pointerEvents = "";
                });
        });
    });
});
</script>

</html>
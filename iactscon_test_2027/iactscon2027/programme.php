<?php
include_once("includes/source.php");
include_once("includes/frontend.init.php");
include_once("includes/function.registration.php");
include_once("includes/function.delegate.php");
include_once("includes/function.invoice.php");
include_once("includes/function.workshop.php");
include_once("includes/function.dinner.php");
include_once("includes/function.accompany.php");
include_once("includes/function.abstract.php");
include_once('includes/function.accommodation.php');
include_once('webmaster/includes/function.php');
/* ==========================================================
   HELPERS
   ========================================================== */
function h($v) { return htmlspecialchars((string)$v, ENT_QUOTES, 'UTF-8'); }
function fmtTime($t) { return $t ? date('h:i A', strtotime($t)) : ''; }
function idList($arr) {
    $arr = array_values(array_unique(array_map('intval', $arr)));
    return $arr ? implode(',', $arr) : '0';
}
$sqlMSG   =  array();
$sqlMSG['QUERY']    = "SELECT * FROM " . _DB_COMPANY_INFORMATION_ . " 
			WHERE `id` = 1";
$result       = $mycms->sql_select($sqlMSG);
$companyInfo =  $result[0];

$startD = $companyInfo['conf_start_date'];
$endD = $companyInfo['conf_end_date'];
$startDate = new DateTime($startD);
$endDate = new DateTime($endD);
$formatted = $startDate->format("M d")."-". $endDate->format("M d");
/* ==========================================================
   1. DATES
   ========================================================== */
$confStart = date('Y-m-d', strtotime($startD));
$confEnd   = date('Y-m-d', strtotime($endD));

$sql = array();
$sql['QUERY'] = "SELECT * FROM "._DB_PROGRAM_SCHEDULE_DATE_."
                  WHERE `status` = 'A'
                    AND DATE(conf_date) BETWEEN '".$confStart."' AND '".$confEnd."'
               ORDER BY conf_date ASC";
$dates = $mycms->sql_select($sql) ?: array();
/* ==========================================================
   2. SESSIONS (all dates, all halls)
   ========================================================== */
$dateIds = array_column($dates, 'id');

$sql = array();
$sql['QUERY'] = "SELECT session.*, hall.hall_title AS master_hall_title
                   FROM "._DB_PROGRAM_SCHEDULE_SESSION_." session
             INNER JOIN "._DB_MASTER_HALL_." hall ON session.session_hall_id = hall.id
                  WHERE session.status = 'A'
                    AND session.session_date_id IN (".idList($dateIds).")
               ORDER BY session.session_start_time ASC, session.session_end_time ASC";
$sessions = $mycms->sql_select($sql) ?: array();
$sessionIds = array_column($sessions, 'id');

/* Hall names can be overridden per date */
$hallNames = array(); // [date_id][hall_id] => name
$sql = array();
$sql['QUERY'] = "SELECT hall_id, date_id, hall_name FROM "._DB_MASTER_HALL_NAME_." WHERE status = 'A'";
$hn = $mycms->sql_select($sql) ?: array();
foreach ($hn as $r) { $hallNames[$r['date_id']][$r['hall_id']] = $r['hall_name']; }

/* ==========================================================
   3. THEMES (groups) & TOPICS (talks)
   ========================================================== */
$sql = array();
$sql['QUERY'] = "SELECT * FROM "._DB_PROGRAM_SCHEDULE_THEME_."
                  WHERE status = 'A' AND schedule_id IN (".idList($sessionIds).")
               ORDER BY theme_time_start ASC, theme_time_end ASC, id ASC";
$themeRows = $mycms->sql_select($sql) ?: array();
$themesBySession = array();
foreach ($themeRows as $r) { $themesBySession[$r['schedule_id']][] = $r; }

$sql = array();
$sql['QUERY'] = "SELECT * FROM "._DB_PROGRAM_SCHEDULE_TOPIC_."
                  WHERE status = 'A' AND schedule_session_id IN (".idList($sessionIds).")
               ORDER BY sequence ASC, id ASC";
$topicRows = $mycms->sql_select($sql) ?: array();
$topicsByTheme = array();
foreach ($topicRows as $r) { $topicsByTheme[$r['schedule_theme_id']][] = $r; }

/* ==========================================================
   4. PARTICIPANTS
   Key: [session_id][theme_id or 0][topic_id or 0] => [type => [names]]
   ========================================================== */
$sql = array();
$sql['QUERY'] = "SELECT sch.session_id, sch.theme_id, sch.topic_id, sch.participant_type, prt.*
                   FROM "._DB_SP_PARTICIPANT_SCHEDULE_." sch
             INNER JOIN "._DB_SP_PARTICIPANT_DETAILS_." prt ON sch.participant_id = prt.id
                  WHERE sch.session_id IN (".idList($sessionIds).")
               ORDER BY sch.participant_type, prt.participant_full_name";
$partRows = $mycms->sql_select($sql) ?: array();

$partMap  = array();
$faculty  = array(); // [participant_id] => info + assignments
foreach ($partRows as $r) {
    $name = trim($r['participant_full_name']);
    if ($name === '') continue;
    $sid = (int)$r['session_id'];
    $thid = (int)$r['theme_id'];
    $tpid = (int)$r['topic_id'];
    $type = trim($r['participant_type']);
    $partMap[$sid][$thid][$tpid][$type][] = $name;

    $pid = $r['id'];
    if (!isset($faculty[$pid])) {
        $faculty[$pid] = array(
            'name'   => $name,
            // NOTE: adjust these column names to match your participant_details table
            'org'    => $r['participant_institutions'] ?? ($r['participant_organization'] ?? ''),
            'email'  => $r['participant_email_id'] ?? '',
            'mobile' => $r['participant_mobile_no'] ?? ($r['participant_phone'] ?? ''),
            'items'  => array(),
        );
    }
    $faculty[$pid]['items'][] = array('session_id' => $sid, 'theme_id' => $thid, 'topic_id' => $tpid, 'type' => $type);
}

function renderParticipants($partMap, $sid, $thid, $tpid) {
    if (empty($partMap[$sid][$thid][$tpid])) return '';
    $out = '';
    foreach ($partMap[$sid][$thid][$tpid] as $type => $names) {
        $out .= '<h6><span>' . h($type) . ':</span> ' . h(implode(', ', array_unique($names))) . '</h6>';
    }
    return $out;
}
function plainParticipants($partMap, $sid, $thid, $tpid) {
    if (empty($partMap[$sid][$thid][$tpid])) return '';
    $all = array();
    foreach ($partMap[$sid][$thid][$tpid] as $names) { $all = array_merge($all, $names); }
    return implode(', ', array_unique($all));
}

/* ==========================================================
   5. ORGANISE: date => hall => sessions
   ========================================================== */
$sessionById = array();
$byDate = array();
foreach ($sessions as $s) {
    $sessionById[$s['id']] = $s;
    $hallId = $s['session_hall_id'];
    $hallName = $hallNames[$s['session_date_id']][$hallId] ?? $s['master_hall_title'];
    $s['_hall_name'] = $hallName;
    $byDate[$s['session_date_id']]['halls'][$hallId] = $hallName;
    $byDate[$s['session_date_id']]['sessions'][] = $s;
}

$facultyList = $faculty;
uasort($facultyList, function ($a, $b) { return strcasecmp($a['name'], $b['name']); });

$eventName = $companyInfo['company_conf_name']; // change or pull from your settings
?>
<body class="programme_body">
    <div class="sc_programme_head">
        <div class="sc_programme_head_left">
            <h6><span>Official Scientific Schedule</span></h6>
            <h4><?= h($eventName) ?></h4>
            <p><?=$companyInfo['sub_header_text']?></p>
            
            <h5><span><i class="fal fa-calendar-alt"></i><?=$formatted?></span>
            <n>•</n>
            <span><i class="fal fa-map-marker-alt"></i><?=$companyInfo['invoice_address']?></span>
            </h5>
        </div>
        <ul>
            <li><n><?= count($dates) ?></n><span>Days</span></li>
            <li><n><?= count($sessions) ?></n><span>Sessions</span></li>
            <li><n><?= count($facultyList) ?></n><span>Faculty</span></li>
        </ul>
    </div>

    <div class="sc_programme_search_wrap">
        <div class="sc_programme_search_wrap_top">
            <ul>
                <li data-tab="scprgsch" class="scprgschfacbtn active"><i class="fal fa-book-open"></i>Session List</li>
                <li data-tab="scprgfac" class="scprgschfacbtn"><i class="fal fa-user-friends"></i>Faculty List</li>
            </ul>
            <button class="scpgrm_save" ><i class="fal fa-bookmark"></i>My Saved (<span id="savedCount">0</span>)</button>
        </div>
        <div class="sc_programme_search_wrap_bottom">
            <div class="sc_programme_search_wrap_bottom_box">
                <i class="fal fa-search"></i>
                <input id="searchSession" placeholder="Search by Session or Topic...">
            </div>
            <div class="sc_programme_search_wrap_bottom_box">
                <i class="fal fa-search"></i>
                <input id="searchFaculty" placeholder="Search by Faculty or Speaker Name...">
            </div>
        </div>
    </div>

    <!-- ===================== SESSION LIST ===================== -->
    <div class="programme_schedule_contant" id="scprgsch" style="display:block;">
        <div class="sc_programme_wrap">
            <div class="sc_programme_left">
                <?php foreach ($dates as $i => $d) {
                    $cnt = isset($byDate[$d['id']]['sessions']) ? count($byDate[$d['id']]['sessions']) : 0; ?>
                <div class="sc_programme_lef_box <?= $i === 0 ? 'active' : '' ?>" data-date="<?= (int)$d['id'] ?>">
                    <h5><n>Day <?= $i + 1 ?></n><span><?= $cnt ?> Sessions</span></h5>
                    <h4><n><?= h(strtoupper(date('d M Y', strtotime($d['conf_date'])))) ?></n><span><?= h(date('l', strtotime($d['conf_date']))) ?></span></h4>
                </div>
                <?php } ?>
            </div>

            <?php foreach ($dates as $i => $d) {
                $did = $d['id'];
                $dateSessions = $byDate[$did]['sessions'] ?? array();
                $dateHalls = $byDate[$did]['halls'] ?? array();
                asort($dateHalls);
            ?>
            <div class="sc_programme_right" data-date="<?= (int)$did ?>" data-headonly="<?= ((int)$did === 5) ? '1' : '0' ?>" style="display:<?= $i === 0 ? 'block' : 'none' ?>;">
                <div class="sc_programme_right_box">
                    <div class="sc_programme_right_box_head">
                        <div class="sc_programme_right_box_head_left">
                            <h4><span>Day <?= $i + 1 ?> Schedule</span><n>•</n>
                                <g><?= h(strtoupper(date('d M Y', strtotime($d['conf_date'])))) ?> (<?= h(date('l', strtotime($d['conf_date']))) ?>)</g></h4>
                            <p>Showing <?= count($dateSessions) ?> scientific session block(s)</p>
                        </div>
                        <div class="sc_programme_right_box_head_right">
                            <span><i class="fal fa-map-marker-alt"></i>Hall:</span>
                            <select class="hallSelect">
                                <option value="all">All Hall (<?= count($dateSessions) ?>)</option>
                                <?php foreach ($dateHalls as $hid => $hname) {
                                    $c = 0; foreach ($dateSessions as $s) { if ($s['session_hall_id'] == $hid) $c++; } ?>
                                <option value="<?= (int)$hid ?>"><?= h($hname) ?> (<?= $c ?>)</option>
                                <?php } ?>
                            </select>
                        </div>
                    </div>

                    <div class="sc_programme_right_box_filter">
                       <div class="halls_owl owl-carousel owl-theme">                          
                            <li class="active" data-hall="all">All Halls <span><?= count($dateSessions) ?></span></li>
                            <?php foreach ($dateHalls as $hid => $hname) {
                                $c = 0; foreach ($dateSessions as $s) { if ($s['session_hall_id'] == $hid) $c++; } ?>
                            <li data-hall="<?= (int)$hid ?>"><?= h($hname) ?> <span><?= $c ?></span></li>
                            <?php } ?>
                       </div>
                    </div>
                    <div class="sc_day_hall_label_wrap" style="display:none;">
                        <h4 style="margin-bottom: 0.9rem;!important" class="sc_day_hall_label"></h4>
                    </div>
                    <ul class="sc_programme_right_box_content_wrap">
                        <div class="my_save_content" style="display:none;">
                            <span><i class="fal fa-calendar"></i></span>
                            <h4>No sessions match your search criteria</h4>
                            <p>Try clearing your session or faculty search queries, or switch to "All Halls".</p>
                            <button type="button" class="resetFilters">Reset All Filters</button>
                        </div>

                        <?php foreach ($dateSessions as $s) {
                            $sid = (int)$s['id'];
                            $themes = $themesBySession[$sid] ?? array();
                            $hasGroups = ($themes && $themes[0]['noTheme'] == 'N');
                            $headOnly = ((int)$s['session_date_id'] === 5);
                            // Session-level speakers summary (for the header line)
                            $sessionPeople = plainParticipants($partMap, $sid, 0, 0);
                            $sessionPeopleHtml = '';
                            if (!empty($partMap[$sid][0][0])) {
                                $parts = array();
                                foreach ($partMap[$sid][0][0] as $type => $names) {
                                    $parts[] = h($type) . ': ' . h(implode(', ', array_unique($names)));
                                }
                                $sessionPeopleHtml = implode(' &nbsp;|&nbsp; ', $parts);
                            }

                            // Search haystack
                            $hay = array($s['session_title'], $sessionPeople);
                            foreach ($themes as $th) {
                                $hay[] = $th['theme_title'];
                                $hay[] = plainParticipants($partMap, $sid, (int)$th['id'], 0);
                                foreach ($topicsByTheme[$th['id']] ?? array() as $tp) {
                                    $hay[] = $tp['topic_title'];
                                    $hay[] = plainParticipants($partMap, $sid, (int)$th['id'], (int)$tp['id']);
                                }
                            }
                            $sessionHay  = strtolower(implode(' ', array($s['session_title'])));
                            foreach ($themes as $th) {
                                $sessionHay .= ' ' . strtolower($th['theme_title']);
                                foreach ($topicsByTheme[$th['id']] ?? array() as $tp) { $sessionHay .= ' ' . strtolower($tp['topic_title']); }
                            }
                            $facultyHay = strtolower(implode(' ', $hay));
                        ?>
                        <li class="prg-session" data-hall="<?= (int)$s['session_hall_id'] ?>" data-session="<?= (int)$sid ?>"
                            data-s="<?= h($sessionHay) ?>" data-f="<?= h($facultyHay) ?>"
                            style="border-color: <?= h($s['session_color']) ?>">
                            <div class="sc_programme_right_box_content_wrap_head">
                                <h6>
                                    <span><?= h($s['session_classifications_id']) ?></span>
                                    <n><i class="fal fa-clock"></i><?= h(date('H:i', strtotime($s['session_start_time']))) ?> - <?= h(date('H:i', strtotime($s['session_end_time']))) ?></n>
                                    <k>•</k>
                                    <g><i class="fal fa-map-marker-alt"></i><?= h($s['_hall_name']) ?></g>
                                </h6>
                                <?php if ($headOnly) {
                                        // Check if title contains break/lunch/tea/dinner keywords
                                        $titleLower = strtolower($s['session_title']);
                                        $showTitle = (
                                            strpos($titleLower, 'break') !== false ||
                                            strpos($titleLower, 'lunch') !== false ||
                                            strpos($titleLower, 'tea') !== false ||
                                            strpos($titleLower, 'dinner') !== false
                                        );
                                        if ($showTitle) { ?>
                                            <h4><?= h($s['session_title']) ?></h4>
                                        <?php }
                                    } else { ?>
                                <h4><?= h($s['session_title']) ?></h4>
                               <? } ?>
                                <?php if ($sessionPeopleHtml) { ?>
                                <h5><span><i class="fal fa-user"></i></span><?= $sessionPeopleHtml ?></h5>
                                <?php } ?>
                                <label class="scpgr_save">
                                    <input type="checkbox" class="saveSession" value="<?= (int)$sid ?>">
                                    <span class="checkmark"><i class="fal fa-bookmark"></i><i class="fas fa-bookmark"></i></span>
                                </label>
                            </div>
                            
                           <? if ($hasGroups) { 
                                // Check if there's any actual content to display
                                $hasContent = false;
                                foreach ($themes as $th) {
                                    if ($th['noTheme'] == 'N' && !empty($topicsByTheme[$th['id']])) {
                                        $hasContent = true;
                                        break;
                                    }
                                }
                                if ($hasContent) { ?>
                            <div class="sc_programme_right_box_content_wrap_content sc_programme_right_box_content_wrap_content_grp">
                                <?php foreach ($themes as $th) {
                                    if ($th['noTheme'] != 'N') continue;
                                    if (empty($topicsByTheme[$th['id']])) continue; ?>
                                <div class="sc_programme_right_box_content_wrap_content_box">
                                    <div class="sc_programme_right_box_content_wrap_content_box_head">
                                        <h5><span><i class="fal fa-clock"></i><?=  h(date('H:i', strtotime(($th['theme_time_start'])))) ?> - <?= h(date('H:i', strtotime(($th['theme_time_end'])))) ?></span></h5>
                                        <h4><?= h($th['theme_title']) ?></h4>
                                        <?= renderParticipants($partMap, $sid, (int)$th['id'], 0) ?>
                                    </div>
                                    <div class="sc_programme_right_box_content_wrap_content_box_bottom">
                                        <?php foreach ($topicsByTheme[$th['id']] ?? array() as $tp) { ?>
                                        <div class="pgschdl_time">
                                            <span><?= (int)$tp['topic_time_duration'] ?>m</span>
                                            <div class="pgschdl_time_right">
                                                <h5><?= h($tp['topic_title']) ?><?= $tp['reference_tag'] ? ' <small>(' . h($tp['reference_tag']) . ')</small>' : '' ?></h5>
                                                <div class="pgschdl_time_right_bottom">
                                                    <?= renderParticipants($partMap, $sid, (int)$th['id'], (int)$tp['id']) ?>
                                                </div>
                                            </div>
                                        </div>
                                        <?php } ?>
                                    </div>
                                </div>
                                <?php } ?>
                            </div>
                            <?php } 
                            } else { 
                                // Check if there are any topics to display
                                $hasTopics = false;
                                foreach ($themes as $th) {
                                    if (!empty($topicsByTheme[$th['id']])) {
                                        $hasTopics = true;
                                        break;
                                    }
                                }
                                if ($hasTopics) { ?>
                            <div class="sc_programme_right_box_content_wrap_content">
                                <div class="sc_programme_right_box_content_wrap_content_box_bottom">
                                    <?php foreach ($themes as $th) {
                                        foreach ($topicsByTheme[$th['id']] ?? array() as $tp) { ?>
                                    <div class="pgschdl_time">
                                        <span><?= (int)$tp['topic_time_duration'] ?>m</span>
                                        <div class="pgschdl_time_right">
                                            <h5><?= h($tp['topic_title']) ?><?= $tp['reference_tag'] ? ' <small>(' . h($tp['reference_tag']) . ')</small>' : '' ?></h5>
                                            <div class="pgschdl_time_right_bottom">
                                                <?= renderParticipants($partMap, $sid, (int)$th['id'], (int)$tp['id']) ?>
                                            </div>
                                        </div>
                                    </div>
                                    <?php } } ?>
                                </div>
                            </div>
                            <?php } 
                            } ?>
                        </li>
                        <?php } ?>
                    </ul>
                </div>
            </div>
            <?php } ?>
        </div>
    </div>

    <!-- ===================== FACULTY LIST ===================== -->
    <div class="programme_schedule_contant" id="scprgfac">
        <div class="sc_faculty_head">
            <h3>Faculty &amp; Speaker Directory
                <n>Browse participating chairpersons, keynote orators, demonstrators, and panel moderators.</n>
            </h3>
            <span><?= count($facultyList) ?> Faculty Members Listed</span>
        </div>
        <div class="sc_faculty_wrap">
            <?php $n = 0; foreach ($facultyList as $pid => $f) { $n++; ?>
            <div class="sc_faculty_box" data-name="<?= h(strtolower($f['name'])) ?>">
                <div class="sc_faculty_box_head">
                    <span><?= $n ?></span>
                    <p><?= h($f['name']) ?><?php if ($f['org']) { ?><n><?= h($f['org']) ?></n><?php } ?></p>
                </div>
                <?php if ($f['email'] || $f['mobile']) { ?>
                <div class="sc_faculty_box_contact">
                    <?php if ($f['email']) { ?><span><i class="fal fa-envelope"></i><?= h($f['email']) ?></span><?php } ?>
                    <?php if ($f['mobile']) { ?><span><i class="fal fa-phone-alt"></i><?= h($f['mobile']) ?></span><?php } ?>
                </div>
                <?php } ?>
                <h6>Assigned Conference Sessions (<?= count($f['items']) ?>)</h6>
                <ul>
                    <?php foreach ($f['items'] as $it) {
                        $s = $sessionById[$it['session_id']] ?? null;
                        if (!$s) continue;
                        $topicTitle = '';
                        if ($it['topic_id']) {
                            foreach ($topicsByTheme[$it['theme_id']] ?? array() as $tp) {
                                if ($tp['id'] == $it['topic_id']) { $topicTitle = $tp['topic_title']; break; }
                            }
                        }
                        $hallName = $hallNames[$s['session_date_id']][$s['session_hall_id']] ?? $s['master_hall_title'];
                        $dateRow = null;
                        foreach ($dates as $dd) { if ($dd['id'] == $s['session_date_id']) { $dateRow = $dd; break; } }
                    ?>
                    <li>
                        <div>
                            <n><?= $dateRow ? h(strtoupper(date('d M Y', strtotime($dateRow['conf_date'])))) . ' | ' . h(date('l', strtotime($dateRow['conf_date']))) : '' ?></n>
                            <span><?= h($it['type']) ?></span>
                        </div>
                        <h4><?= h($s['session_title']) ?></h4>
                        <?php if ($topicTitle) { ?><p>Topic: <?= h($topicTitle) ?></p><?php } ?>
                        <h5>
                            <n><i class="fal fa-clock"></i><?= h(date('H:i', strtotime($s['session_start_time']))) ?> - <?= h(date('H:i', strtotime($s['session_end_time']))) ?></n>
                            <n><i class="fal fa-map-marker-alt"></i><?= h($hallName) ?></n>
                        </h5>
                    </li>
                    <?php } ?>
                </ul>
            </div>
            <?php } ?>
        </div>
    </div>

    <div class="sc_prgm_note">
        <p><i class="fal fa-info-circle"></i>All scientific sessions adhere to IST (Indian Standard Time). Hall capacities are on a first-come, first-served basis.</p>
        <a href="javascript:window.print()"><i class="fal fa-download"></i>Print Schedule</a>
    </div>
</body>
<?php include_once("includes/js-source.php"); ?>
<script>
(function ($) {
    var SAVE_KEY = 'prg_saved_sessions';
    var saved = [];
    try { saved = JSON.parse(localStorage.getItem(SAVE_KEY) || '[]'); } catch (e) { saved = []; }
    var savedOnly = false;

    function activeDay() { return $('.sc_programme_right:visible'); }

    function persist() {
        try { localStorage.setItem(SAVE_KEY, JSON.stringify(saved)); } catch (e) {}
        $('#savedCount').text(saved.length);
    }

    /* ---------- master filter (hall + search + saved) ---------- */
    function applyFilters() {
        var $day = activeDay();
        var hall = $day.find('.sc_programme_right_box_filter li.active').first().data('hall');
        if (hall === undefined) hall = 'all';
        var qs = $.trim($('#searchSession').val()).toLowerCase();
        var qf = $.trim($('#searchFaculty').val()).toLowerCase();
        var shown = 0;

        $day.find('.prg-session').each(function () {
            var $li = $(this), ok = true;
            if (hall !== 'all' && String($li.data('hall')) !== String(hall)) ok = false;
            if (ok && qs && String($li.attr('data-s')).indexOf(qs) === -1) ok = false;
            if (ok && qf && String($li.attr('data-f')).indexOf(qf) === -1) ok = false;
            if (ok && savedOnly && saved.indexOf(String($li.data('session'))) === -1) ok = false;
            $li.toggle(ok);
            if (ok) shown++;
        });
        $day.find('.my_save_content').toggle(shown === 0);
    }
    $('.sc_programme_right').each(function () {
        updateDayHallLabel($(this));
    });
    /* ---------- tabs: sessions / faculty ---------- */
    $('.scprgschfacbtn').on('click', function () {
        $('.programme_schedule_contant').hide();
        $('.scprgschfacbtn').removeClass('active');
        $('#' + $(this).data('tab')).show();
        $(this).addClass('active');
    });

    /* ---------- day switch ---------- */
    $('.sc_programme_lef_box').on('click', function () {
        $('.sc_programme_lef_box').removeClass('active');
        $(this).addClass('active');
        $('.sc_programme_right').hide();
        var $day = $('.sc_programme_right[data-date="' + $(this).data('date') + '"]').show();
        $day.find('.halls_owl').trigger('refresh.owl.carousel');
        $day.find('.sc_programme_right_box_filter li').removeClass('active').first().addClass('active');
        $day.find('.hallSelect').val('all');
        applyFilters();
        updateDayHallLabel($day);
    });

    /* ---------- hall filter (chips + dropdown kept in sync) ---------- */
    $(document).on('click', '.sc_programme_right_box_filter li', function () {
        var $li = $(this);
        var $box = $li.closest('.sc_programme_right');

        // clear active on every chip in this day (including Owl clones)
        $box.find('.sc_programme_right_box_filter li').removeClass('active');

        // activate all chips with the same hall id (handles cloned items)
        var hall = String($li.data('hall'));
        $box.find('.sc_programme_right_box_filter li').filter(function () {
            return String($(this).data('hall')) === hall;
        }).addClass('active');

        $box.find('.hallSelect').val(hall);
        applyFilters();
        updateDayHallLabel($(this).closest('.sc_programme_right'));
    });
    $(document).on('change', '.hallSelect', function () {
        var v = String($(this).val());
        var $box = $(this).closest('.sc_programme_right');
        $box.find('.sc_programme_right_box_filter li').removeClass('active')
            .filter(function () { return String($(this).data('hall')) === v; })
            .addClass('active');
        applyFilters();
         updateDayHallLabel($(this).closest('.sc_programme_right'));
    });
    /* ---------- search ---------- */
    $('#searchSession, #searchFaculty').on('input', function () {
        applyFilters();
        // faculty tab list filter
        var q = $.trim($('#searchFaculty').val()).toLowerCase();
        $('.sc_faculty_box').each(function () {
            $(this).toggle(!q || String($(this).data('name')).indexOf(q) !== -1);
        });
    });

   /* ---------- bookmarks ---------- */
    var warnedThisVisit = false;

    $('.saveSession').each(function () {
        if (saved.indexOf(String($(this).val())) !== -1) $(this).prop('checked', true);
    });
    $('.saveSession').on('change', function () {
        var id = String($(this).val());
        var i = saved.indexOf(id);
        if ($(this).is(':checked')) {
            if (i === -1) saved.push(id);
            if (!warnedThisVisit) {
                warnedThisVisit = true;
                alert('Please note: your saved sessions are stored only in this browser on this device.\n\n' +
                    'If you open the programme in a different browser or device, or clear your browsing data, ' +
                    'your saved sessions will not be available.');
            }
        } else if (i !== -1) {
            saved.splice(i, 1);
        }
        persist();
        if (savedOnly) applyFilters();
    });
    /* ---------- My Saved button ---------- */
    $('.scpgrm_save').off('click').on('click', function (e) {
        e.preventDefault();
        e.stopImmediatePropagation();

        // make sure the Session List tab is showing
        $('.programme_schedule_contant').hide();
        $('#scprgsch').show();
        $('.scprgschfacbtn').removeClass('active');
        $('.scprgschfacbtn[data-tab="scprgsch"]').addClass('active');

        savedOnly = !savedOnly;
        $(this).toggleClass('active', savedOnly);
        applyFilters();
    });
    /* ---------- reset ---------- */
    $('.resetFilters').on('click', function () {
        $('#searchSession, #searchFaculty').val('');
        savedOnly = false;
        $('.scpgrm_save').removeClass('active');
        $('.sc_faculty_box').show();
        var $day = activeDay();
        $day.find('.sc_programme_right_box_filter li').removeClass('active').first().addClass('active');
        $day.find('.hallSelect').val('all');
        applyFilters();
        updateDayHallLabel($day);

    });

    persist();
    applyFilters();
})(jQuery);
 $('.halls_owl').owlCarousel({
        loop: false,
        margin: 6,
        autoWidth: true,

        dots: false,
        dotsData: false,
        nav: true,
        navText: [
            '<i class="fal fa-angle-left"></i>',
            '<i class="fal fa-angle-right"></i>'
        ],
        // mouseDrag: false,
        onInitialized: toggleNavigation,
        onRefreshed: toggleNavigation

    });
    function toggleNavigation(event) {
        var $carousel = $(event.target);

        // Find the actual carousel outer stage container width
        var stageWidth = $carousel.find('.owl-stage').width();

        // Find the parent viewport wrapper width (.owl-stage-outer)
        var outerWidth = $carousel.find('.owl-stage-outer').width();

        // If the stage elements are narrower than the container, hide the navigation controls
        if (stageWidth <= outerWidth) {
            $carousel.find('.owl-nav').hide();
            $carousel.find('.owl-dots').hide(); // Optional: hides dots too
        } else {
            $carousel.find('.owl-nav').show();
            $carousel.find('.owl-dots').show();
        }
    }
function updateDayHallLabel($day) {
    var $wrap  = $day.find('.sc_day_hall_label_wrap');
    var $label = $day.find('.sc_day_hall_label');

    // Only for date id 5 (headOnly)
    if (String($day.data('headonly')) !== '1') {
        $wrap.hide();
        return;
    }

    var hall = String($day.find('.sc_programme_right_box_filter li.active').first().data('hall') || 'all');
    var text = '';

    if (hall === 'all' || hall === 'undefined' || hall === '') {
        text = 'All Workshop Programme';
    } else {
        // find the hall name from the chips (or dropdown option)
        var $chip = $day.find('.sc_programme_right_box_filter li').filter(function () {
            return String($(this).data('hall')) === hall;
        }).first();

        if ($chip.length) {
            // chip text is like "Hall Name 3" — strip trailing count
            text = $.trim($chip.clone().children('span').remove().end().text());
        } else {
            // fallback: dropdown option text
            var $opt = $day.find('.hallSelect option[value="' + hall + '"]');
            if ($opt.length) text = $.trim($opt.text().replace(/\(\d+\)\s*$/, ''));
        }
    }

    $label.text(text);
    $wrap.toggle(!!text);
}
</script>
<style>
    @media print {
    @page { margin: 12mm; }

    html, body, .programme_body {
        background: #fff !important;
        color: #000 !important;
        -webkit-print-color-adjust: exact;
        print-color-adjust: exact;
    }

    /* force readable dark text everywhere */
    .programme_body *,
    .programme_body *::before,
    .programme_body *::after {
        color: #111 !important;
        background: transparent !important;
        box-shadow: none !important;
        text-shadow: none !important;
        opacity: 1 !important;
    }

    /* keep hall/session colour accents and borders visible */
    .prg-session,
    .sc_programme_right_box_content_wrap > li,
    .sc_faculty_box {
        border: 1px solid #888 !important;
        border-left: 4px solid #444 !important;
        page-break-inside: avoid;
        break-inside: avoid;
    }

    /* hide interactive UI */
    .sc_programme_search_wrap,
    .sc_programme_left,
    .sc_programme_right_box_filter,
    .sc_programme_right_box_head_right,
    .scpgr_save,
    .sc_prgm_note a,
    header, .left-menu {
        display: none !important;
    }
}
</style>
</html>
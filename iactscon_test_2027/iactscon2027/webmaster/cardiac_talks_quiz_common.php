<?php
/*==================================================================
   Shared helpers for the quiz sub-pages:
     cardiac_talks_quiz_participants.php
     cardiac_talks_quiz_applicants.php
     cardiac_talks_quiz_leaderboard.php
   (PHP 7.3 compatible - no arrow fns, match, str_contains, ?-> etc.)
==================================================================*/

/* ---------- Load one quiz (with topic + hall names) ---------- */
if (!function_exists('cq_get_quiz')) {
    function cq_get_quiz($mycms, $quizId)
    {
        $quizId = (int)$quizId;
        if ($quizId <= 0) {
            return [];
        }
        $sql = [];
        $sql['QUERY'] = "SELECT q.*, g.group_name, h.hall_title
            FROM `rcg_cardiac_talk_quizzes` AS q
            LEFT JOIN `rcg_groups` AS g ON g.id = q.group_id
            LEFT JOIN `rcg_program_schedule_hall` AS h ON h.id = q.hall_id
            WHERE q.`id` = ? AND q.`row_status` != 'D'";
        $sql['PARAM'][] = ['FILD' => 'id', 'DATA' => $quizId, 'TYP' => 'i'];
        $res = $mycms->sql_select($sql);
        return $res ? $res[0] : [];
    }
}

/* ---------- Application counts: applied / approved / rejected ---------- */
if (!function_exists('cq_counts')) {
    function cq_counts($mycms, $quizId)
    {
        $out = ['Applied' => 0, 'Approved' => 0, 'Rejected' => 0];
        $sql = [];
        $sql['QUERY'] = "SELECT `status`, COUNT(*) AS c
            FROM `rcg_cardiac_talk_quiz_participation`
            WHERE `quiz_id` = ? AND `row_status` = 'A'
            GROUP BY `status`";
        $sql['PARAM'][] = ['FILD' => 'quiz_id', 'DATA' => (int)$quizId, 'TYP' => 'i'];
        $res = $mycms->sql_select($sql);
        if ($res) {
            foreach ($res as $r) {
                if (isset($out[$r['status']])) {
                    $out[$r['status']] = (int)$r['c'];
                }
            }
        }
        return $out;
    }
}

/* ---------- Page styles (same look as the main quiz page) ---------- */
if (!function_exists('cq_styles')) {
    function cq_styles()
    {
        ?>
<style>
    .int_alert { position: fixed; top: 20px; right: 20px; z-index: 99999; min-width: 280px; max-width: 420px; padding: 14px 18px; border-radius: 10px; border: 1px solid; display: flex; gap: 14px; align-items: center; justify-content: space-between; font-size: 14px; box-shadow: 0 10px 30px rgba(0, 0, 0, .6); }
    .int_alert.success { color: #34d399; background: #0f1f1a; border-color: rgba(16, 185, 129, .45); }
    .int_alert.error   { color: #f87171; background: #221212; border-color: rgba(239, 68, 68, .45); }
    .int_alert button  { background: none; border: 0; color: inherit; font-size: 20px; line-height: 1; cursor: pointer; }
    .table_wrap table thead th { text-align: center !important; }
    .table_wrap table td.action, .table_wrap table th.action { text-align: center !important; }
    .table_wrap table td.action .action_div { display: flex !important; justify-content: center !important; align-items: center; float: none !important; margin-left: auto !important; margin-right: auto !important; width: 100%; }

    .group_tabs_wrap { background: transparent; padding: 0; margin-bottom: 18px; overflow-x: auto; -webkit-overflow-scrolling: touch; }
    .group_tabs { display: flex; flex-wrap: nowrap; justify-content: flex-start; align-items: center; gap: 10px; list-style: none; margin: 0; padding: 4px 2px 10px; }
    .group_tabs li { flex: 0 0 auto; }
    .group_tabs a { display: inline-flex; align-items: center; gap: 9px; padding: 10px 22px; background: #14161c; border: 1px solid #262a35; border-radius: 10px; font-size: 14px; font-weight: 600; color: #94a3b8; text-decoration: none; white-space: nowrap; transition: all .25s ease; }
    .group_tabs a:hover { background: #1e222b; border-color: #3b4252; color: #fff; }
    .group_tabs a.active { background: linear-gradient(135deg, #4f46e5, #7c3aed); border-color: transparent; color: #fff; box-shadow: 0 6px 16px rgba(79, 70, 229, .45); }

    .cq_stats { display: grid; grid-template-columns: repeat(auto-fit, minmax(150px, 1fr)); gap: 12px; margin-bottom: 16px; }
    .cq_stat { background: #14161c; border: 1px solid #262a35; border-radius: 10px; padding: 12px 16px; }
    .cq_stat span { display: block; font-size: 12px; color: #94a3b8; text-transform: uppercase; letter-spacing: .04em; }
    .cq_stat b { display: block; font-size: 22px; color: #fff; margin-top: 2px; }
    .cq_notice { background: #14161c; border: 1px solid #262a35; border-radius: 10px; padding: 18px 20px; color: #cbd5e1; }
    .cq_rank_1 td { background: rgba(250, 204, 21, .08); }
    .cq_rank_2 td { background: rgba(148, 163, 184, .08); }
    .cq_rank_3 td { background: rgba(217, 119, 6, .08); }
    .cq_medal_1 { color: #facc15; } .cq_medal_2 { color: #cbd5e1; } .cq_medal_3 { color: #d97706; }
</style>
        <?php
    }
}

/* ---------- One-time flash alert (set by cardiac_talks_quiz.process.php) ---------- */
if (!function_exists('cq_flash')) {
    function cq_flash()
    {
        $flash = isset($_SESSION['cardiac_alert']) ? $_SESSION['cardiac_alert'] : null;
        unset($_SESSION['cardiac_alert']);
        if (!empty($flash['message'])) {
            ?>
            <div class="int_alert <?= htmlspecialchars($flash['type']) ?>" id="intAlert">
                <span>
                    <i class="fal <?= ($flash['type'] === 'error') ? 'fa-exclamation-circle' : 'fa-check-circle' ?>"></i>
                    <?= htmlspecialchars($flash['message']) ?>
                </span>
                <button type="button" id="intAlertClose" aria-label="Close">&times;</button>
            </div>
            <?php
        }
    }
}

/* ---------- Page heading + sub navigation ---------- */
if (!function_exists('cq_header')) {
    function cq_header($quiz, $active)
    {
        $qid        = (int)$quiz['id'];
        $isApproval = (isset($quiz['join_type']) && $quiz['join_type'] === 'approval');
        $isDone     = (isset($quiz['status']) && $quiz['status'] === 'completed');

        $labels = ['participants' => 'Participants', 'applicants' => 'Applicants', 'leaderboard' => 'Leader Board'];
        ?>
        <div class="page_top_wrap mb-3">
            <div class="page_top_wrap_left">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="#">Cardiac Talks</a></li>
                        <li class="breadcrumb-item"><a href="cardiac_talks_discussion_quiz_poll.php?tab=quiz">Manage Quiz</a></li>
                        <li class="breadcrumb-item active" aria-current="page"><a href="#"><?= $labels[$active] ?></a></li>
                    </ol>
                </nav>
                <h2><?= htmlspecialchars($quiz['title']) ?></h2>
                <h6>
                    Topic: <?= htmlspecialchars(isset($quiz['group_name']) && $quiz['group_name'] !== null ? $quiz['group_name'] : '--') ?>
                    &middot; Start: <?= !empty($quiz['start_time']) ? date('d-m-Y H:i', strtotime($quiz['start_time'])) : '--' ?>
                    &middot; Joining: <?= $isApproval ? 'Approval needed' : 'Anyone can join' ?>
                    &middot; Quiz status: <?= htmlspecialchars(ucfirst(isset($quiz['status']) ? $quiz['status'] : '')) ?>
                </h6>
            </div>
            <div class="page_top_wrap_right">
                <a href="cardiac_talks_discussion_quiz_poll.php?tab=quiz" class="badge_dark"><i class="fal fa-arrow-left"></i> Back to Quiz List</a>
            </div>
        </div>

        <div class="group_tabs_wrap">
            <ul class="group_tabs">
                <li><a href="cardiac_talks_quiz_questions.php?quiz_id=<?= $qid ?>"><i class="fal fa-question-circle"></i> Questions</a></li>
                <li><a href="cardiac_talks_quiz_participants.php?quiz_id=<?= $qid ?>" class="<?= $active === 'participants' ? 'active' : '' ?>"><i class="fal fa-users"></i> Participants</a></li>
                <?php if ($isApproval) { ?>
                <li><a href="cardiac_talks_quiz_applicants.php?quiz_id=<?= $qid ?>" class="<?= $active === 'applicants' ? 'active' : '' ?>"><i class="fal fa-user-clock"></i> Applicants</a></li>
                <?php } ?>
                <?php if ($isDone) { ?>
                <li><a href="cardiac_talks_quiz_leaderboard.php?quiz_id=<?= $qid ?>" class="<?= $active === 'leaderboard' ? 'active' : '' ?>"><i class="fal fa-trophy"></i> Leader Board</a></li>
                <?php } ?>
            </ul>
        </div>
        <?php
    }
}

/* ---------- Shared JS: flash alert + POST helper (call AFTER js-source.php) ---------- */
if (!function_exists('cq_scripts')) {
    function cq_scripts()
    {
        ?>
<script>
    $(function () {
        if ($('#intAlert').length) { setTimeout(function () { $('#intAlert').fadeOut(300); }, 4000); }
    });
    $(document).on('click', '#intAlertClose', function () { $('#intAlert').fadeOut(200); });

    // Submit a POST form built on the fly: cqPost(url, {act: 'x', ids: [1,2]})
    function cqPost(url, data) {
        var $f = $('<form method="post"></form>').attr('action', url);
        $.each(data, function (k, v) {
            if ($.isArray(v)) {
                $.each(v, function (i, x) { $('<input type="hidden">').attr('name', k + '[]').val(x).appendTo($f); });
            } else {
                $('<input type="hidden">').attr('name', k).val(v).appendTo($f);
            }
        });
        $('body').append($f);
        $f.submit();
    }
</script>
        <?php
    }
}
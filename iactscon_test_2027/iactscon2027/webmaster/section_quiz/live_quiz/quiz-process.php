<?php
include_once('../includes/init.php'); 
// global $mycms;
include_once("includes/source.php");
include_once("includes/firebase_bootstrap.php");

$quizId = isset($_GET['quiz_id']) ? preg_replace('/[^a-zA-Z0-9_\-]/', '', $_GET['quiz_id']) : null;
if (!$quizId) {
    die("Missing quiz_id");
}

// making a device id 
function getRealIpAddress() {
    if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
        return $_SERVER['HTTP_CLIENT_IP'];
    } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
        return explode(',', $_SERVER['HTTP_X_FORWARDED_FOR'])[0];
    } else {
        return $_SERVER['REMOTE_ADDR'];
    }
}

// Generate or fetch the stable device ID
if (!isset($_COOKIE['session_based_device_id'])) {
    $baseId = bin2hex(random_bytes(32)); // Strong 64-char ID
    setcookie('session_based_device_id', $baseId, time() + (86400 * 365), "/");
} else {
    $baseId = $_COOKIE['session_based_device_id'];
}

$sessionDeviceId = $baseId;
$device_ip_address = getRealIpAddress();
$first_login_for_quiz__id = $quizId;

// Check if device already exists
$sql = array();
$sql['QUERY'] = "SELECT device_id FROM `quiz_guest_users` WHERE `device_id` = ?";
$sql['PARAM'][] = array('FILD' => 'device_id', 'DATA' => $sessionDeviceId, 'TYP' => 's');
$check_device = $mycms->sql_select($sql);

if (!empty($check_device)) {

    // // Update only latest visit + IP address
    // $updateSql = array();
    // $updateSql['QUERY'] = "UPDATE `quiz_guest_users` 
    //                        SET `latest_visit_time` = NOW(),
    //                            `ip_address` = ?
    //                        WHERE `device_id` = ?";
    // $updateSql['PARAM'][] = array('FILD' => 'ip_address', 'DATA' => $device_ip_address, 'TYP' => 's');
    // $updateSql['PARAM'][] = array('FILD' => 'device_id', 'DATA' => $sessionDeviceId, 'TYP' => 's');
    // $mycms->sql_update($updateSql);

} else {

    // Insert first time guest record
    $insertSql = array();
    // $insertSql['QUERY'] = "INSERT INTO `quiz_guest_users`
    //                        (`device_id`, `ip_address`, `first_visit_quiz_id`, `first_visit_time`, `latest_visit_time`)
    //                        VALUES (?, ?, ?, NOW(), NOW())";
    // $insertSql['PARAM'][] = array('FILD' => 'device_id', 'DATA' => $sessionDeviceId, 'TYP' => 's');
    // $insertSql['PARAM'][] = array('FILD' => 'ip_address', 'DATA' => $device_ip_address, 'TYP' => 's');
    // $insertSql['PARAM'][] = array('FILD' => 'first_visit_quiz_id', 'DATA' => $first_login_for_quiz__id, 'TYP' => 's');
    // $mycms->sql_update($insertSql);
    $insertSql['QUERY'] = "INSERT INTO `quiz_guest_users`
                        SET device_id=?, ip_address=?, first_visit_quiz_id=?, first_visit_time=NOW(), latest_visit_time=NOW()";

    $insertSql['PARAM'] = [
        ['FILD' => 'device_id',                  'DATA' => $sessionDeviceId,                'TYP' => 's'],
        ['FILD' => 'ip_address',          'DATA' => $device_ip_address,        'TYP' => 's'],
        ['FILD' => 'first_visit_quiz_id',   'DATA' => $first_login_for_quiz__id, 'TYP' => 's']
    ];

    $mycms->sql_insert($insertSql);

}
// echo "ih";

// initial fetch
// $current = $firebase->get(fb_path_quiz($quizId, 'current_question'));
$current = $firebase->get(fb_path_quiz($quizId, 'current_question'));
$serverNowMs = fb_get_server_ms();
?>

<body class="ccicon">
    <div class="main_bar ">
        <?php include_once("includes/header.php"); ?>
        <div class="main_body">

            <!-- Active question (before/while answering) -->
            <div class="quiz_box d-none" id="box-active">
                <div class="sc_prgm_head">
                    <h3>Quiz</h3>
                    <p>
                        <?php
                        date_default_timezone_set('Asia/Kolkata');
                        // Date August 22, 2025 | day 1 | hall a
                        echo date('F d, Y') . ' | Hall-  <i id="hall_name_active"></i>';
                        ?>
                    </p>
                </div>
                <div class="quiz_head">
                    <span class="timer-container" id="timer-active"></span>
                    <h6><i>Level <i id="level-active"></i></i><i>ROUND <i id="round-active"></i></i></h6>
                    <h4 id="qtextActive">Q. —</h4>
                </div>
                <div>
                    <ul id="optionsActive">
                        <label class="con"><b>A.</b>
                            <i id="oa"></i>
                            <input type="radio" value="A" name="radio">
                            <span class="checkmark"></span>
                        </label>
                        <label class="con"><b>B.</b>
                            <i id="ob"></i>
                            <input type="radio" value="B" name="radio">
                            <span class="checkmark"></span>
                        </label>
                        <label class="con"><b>C.</b>
                            <i id="oc"></i>
                            <input type="radio" value="C" name="radio">
                            <span class="checkmark"></span>
                        </label>
                        <label class="con"><b>D.</b>
                            <i id="od"></i>
                            <input type="radio" value="D" name="radio">
                            <span class="checkmark"></span>
                        </label>
                    </ul>
                </div>
            </div>

            <!-- Answered (locked) -->
            <div class="quiz_box answered d-none" id="box-answered">
                <div class="sc_prgm_head">
                    <h3>Quiz</h3>
                    <p>
                        <?php
                        date_default_timezone_set('Asia/Kolkata');
                        // Date August 22, 2025 | day 1 | hall a
                        echo date('F d, Y') . ' | Hall-  <i id="hall_name_answered"></i>';
                        ?>
                    </p>
                </div>
                <div class="quiz_head">
                    <span id="timer-answered">00</span>
                    <h6><i>Level <i id="level-answered"></i></i><i>ROUND <i id="round-answered"></i></i></h6>
                    <h4 id="qtextAnswered">Q. —</h4>
                </div>
                <div>
                    <ul id="optionsAnswered">
                        <label class="con"><b>A.</b><i id="oa2"></i>
                            <input type="radio" value="A" name="radio2"><span class="checkmark"></span>
                        </label>
                        <label class="con"><b>B.</b><i id="ob2"></i>
                            <input type="radio" value="B" name="radio2"><span class="checkmark"></span>
                        </label>
                        <label class="con"><b>C.</b><i id="oc2"></i>
                            <input type="radio" value="C" name="radio2"><span class="checkmark"></span>
                        </label>
                        <label class="con"><b>D.</b><i id="od2"></i>
                            <input type="radio" value="D" name="radio2"><span class="checkmark"></span>
                        </label>
                    </ul>
                </div>
            </div>

            <!-- Waiting / Thank you (shown if no active question) -->
            <div class="quiz_thank d-none" id="box-wait">
                <h2>Waiting for the question</h2>
            </div>


        </div>
        <?php include_once("includes/footer.php"); ?>
    </div>

    <?php include_once("includes/js-source.php"); ?>

    <script>
        window.__QUIZ_ID__ = <?php echo json_encode($quizId); ?>;
        window.__CURRENT__ = <?php echo json_encode($current ?: null); ?>;
        window.__SERVER_NOW_MS__ = <?php echo (int)$serverNowMs; ?>;
    </script>

    <script>
        (function() {
            const boxActive = document.getElementById('box-active');
            const boxAnswered = document.getElementById('box-answered');
            const boxWait = document.getElementById('box-wait');

            const timerActive = document.getElementById('timer-active');
            const timerAnswered = document.getElementById('timer-answered');

            const qtextActive = document.getElementById('qtextActive');
            const qtextAnswered = document.getElementById('qtextAnswered');

            const lvltextActive = document.getElementById('level-active');
            const lvltextAnswered = document.getElementById('level-answered');

            const roundtextActive = document.getElementById('round-answered');
            const roundtextAnswered = document.getElementById('round-active');

            const halltextActive = document.getElementById('hall_name_active');
            const halltextAnswered = document.getElementById('hall_name_answered');

            const oa = document.getElementById('oa'),
                ob = document.getElementById('ob'),
                oc = document.getElementById('oc'),
                od = document.getElementById('od');
            const oa2 = document.getElementById('oa2'),
                ob2 = document.getElementById('ob2'),
                oc2 = document.getElementById('oc2'),
                od2 = document.getElementById('od2');

            const optsActive = document.querySelectorAll('#optionsActive input[type="radio"]');
            const optsAnswered = document.querySelectorAll('#optionsAnswered input[type="radio"]');

            // session id
            const SESSION_KEY = "quizSessionId";
            let sessionId = localStorage.getItem(SESSION_KEY);
            if (!sessionId) {
                sessionId = "sess_" + Math.random().toString(36).slice(2);
                localStorage.setItem(SESSION_KEY, sessionId);
            }

            // ---- server-client clock sync ----
            let serverOffset = 0; // serverNow - clientNow
            function setServerOffset(serverNowMs) {
                serverOffset = Number(serverNowMs) - Date.now();
            }

            function nowMs() {
                return Date.now() + serverOffset;
            }

            // state
            let current = window.__CURRENT__ || null;
            let selected = null;
            let answered = false;
            let ticker = null; // single interval handle (unified)
            let endAt = null;
            let driftComp = 0;

            const LATENCY_FUDGE = 200; // ms (~0.20s delay cushion)
            let pollDelay = 800;
            const QUIZ_ID = window.__QUIZ_ID__;

            // helpers
            const show = el => el.classList.remove('d-none');
            const hide = el => el.classList.add('d-none');
            const pad2 = n => n < 10 ? "0" + n : "" + n;
            const toMMSS = s => {
                s = Math.max(0, Math.ceil(s));
                return `${pad2(Math.floor(s/60))}:${pad2(s%60)}`;
            };

            function setAllRadiosDisabled(nodelist, dis) {
                nodelist.forEach(r => r.disabled = dis);
            }

            // render question (keeps your design)
            function renderQuestion(q) {
                qtextActive.textContent = "Q. " + (q.question || "");
                qtextAnswered.textContent = "Q. " + (q.question || "");

                oa.textContent = q.option_a || "";
                ob.textContent = q.option_b || "";
                oc.textContent = q.option_c || "";
                od.textContent = q.option_d || "";

                oa2.textContent = q.option_a || "";
                ob2.textContent = q.option_b || "";
                oc2.textContent = q.option_c || "";
                od2.textContent = q.option_d || "";

                lvltextActive.textContent = q.level || '';
                lvltextAnswered.textContent = q.level || '';
                roundtextActive.textContent = q.round || '';
                roundtextAnswered.textContent = q.round || '';

                halltextActive.textContent = q.hall_name || '';
                halltextAnswered.textContent = q.hall_name || '';

                optsActive.forEach((r, i) => {
                    r.checked = false;
                    r.value = ["A", "B", "C", "D"][i];
                    r.disabled = true; // locked until timer enables them
                });
                optsAnswered.forEach((r, i) => {
                    r.checked = false;
                    r.value = ["A", "B", "C", "D"][i];
                    r.disabled = true;
                });

                selected = null;
                answered = false;
                show(boxActive);
                hide(boxAnswered);
                hide(boxWait);
            }

            // lock UI after answer
            function lockUI() {
                setAllRadiosDisabled(optsActive, true);
                optsAnswered.forEach(r => r.checked = (r.value === selected));
                hide(boxActive);
                show(boxAnswered);
                hide(boxWait);
                stopTicker(); // freeze timer after answering
                timerActive.textContent = "00:00";
                timerAnswered.textContent = "00:00";
            }

            // update display using drift compensation
            function updateTimerDisplays(now) {
                if (endAt == null) return;
                const remainEnd = (endAt - (now - driftComp)) / 1000; // apply driftComp
                if (remainEnd > 0) {
                    timerActive.textContent = toMMSS(remainEnd);
                    timerAnswered.textContent = toMMSS(remainEnd);
                } else {
                    timerActive.textContent = "00:00";
                    timerAnswered.textContent = "00:00";
                }
            }

            function stopTicker() {
                if (ticker) {
                    clearInterval(ticker);
                    ticker = null;
                }
            }

            // unified ticker: enables radios while active, locks on timeout
            function startTicker() {
                stopTicker();
                ticker = setInterval(() => {
                    if (!current || endAt == null) return;
                    const n = nowMs();
                    const remainingMs = endAt - (n - driftComp);

                    if (remainingMs <= 0) {
                        // time up
                        if (!answered) answered = true;
                        lockUI();
                        return;
                    }

                    // still active — enable radios only if not already answered
                    if (!answered) {
                        setAllRadiosDisabled(optsActive, false);
                    } else {
                        setAllRadiosDisabled(optsActive, true);
                    }

                    updateTimerDisplays(n);
                }, 150); // frequent enough for smooth display
            }

            // start timer for a question (server start_time must be in ms)
            function startTimer(q) {
                const startAt = Number(q.start_time); // server ms
                const qTimeMs = Number(q.question_time || 0) * 1000;

                // set expected end
                endAt = startAt + qTimeMs;

                // calculate initial drift and add small fudge to avoid early cutoff
                driftComp = (nowMs() - startAt) - LATENCY_FUDGE;

                updateTimerDisplays(nowMs());
                startTicker();
            }

            // function handleAnswerSelect(radioEl) {
            //     const n = nowMs();
            //     const remainAtClick = Math.max(0, Math.ceil((endAt - (n - driftComp)) / 1000));

            //     if ((endAt - (n - driftComp)) > 0 && !answered) {
            //         selected = radioEl.value;
            //         answered = true;

            //         // freeze timer before lockUI changes DOM
            //         const frozenTime = toMMSS(remainAtClick);
            //         document.getElementById("timer-answered").setAttribute("data-frozen", frozenTime);

            //         lockUI();

            //         // restore frozen time after UI is switched
            //         document.getElementById("timer-answered").textContent =
            //             document.getElementById("timer-answered").getAttribute("data-frozen");

            //         fetch("ajax/submit_answer.php", {
            //             method: "POST",
            //             headers: {
            //                 "Content-Type": "application/json"
            //             },
            //             body: new URLSearchParams({
            //                 quiz_id: QUIZ_ID,
            //                 question_id: current.id,
            //                 answer: selected,
            //                 session: sessionId,
            //                 answered_at: nowMs()
            //             })
            //         }).catch(err => console.warn("submit failed:", err));
            //     } else {
            //         radioEl.checked = false; // late click
            //     }
            // }
            // function handleAnswerSelect(radioEl) {
            //     const n = nowMs();
            //     const remainAtClick = Math.max(0, Math.ceil((endAt - (n - driftComp)) / 1000));

            //     if ((endAt - (n - driftComp)) > 0 && !answered) {
            //         selected = radioEl.value;
            //         answered = true;

            //         // freeze timer before lockUI changes DOM
            //         const frozenTime = toMMSS(remainAtClick);
            //         document.getElementById("timer-answered").setAttribute("data-frozen", frozenTime);

            //         lockUI();

            //         // restore frozen time after UI is switched
            //         document.getElementById("timer-answered").textContent =
            //             document.getElementById("timer-answered").getAttribute("data-frozen");

            //         // ✅ Send correct fields
            //         fetch("../answer.process.php?act=submit_answer", {
            //                 method: "POST",
            //                 headers: {
            //                     "Content-Type": "application/json"
            //                 },
            //                 body: JSON.stringify({
            //                     quiz_id: QUIZ_ID,
            //                     question_id: current.id,
            //                     selected_option: selected, // ✅ match backend
            //                     time_taken: remainAtClick, // ✅ match backend
            //                     session: sessionId
            //                 })
            //             })
            //             .then(res => res.json())
            //             .then(data => {
            //                 console.log("Answer response:", data);
            //             })
            //             .catch(err => console.warn("submit failed:", err));
            //     } else {
            //         radioEl.checked = false; // late click
            //     }
            // }
            function handleAnswerSelect(radioEl) {
                const n = nowMs();
                const remainAtClick = Math.max(0, Math.ceil((endAt - (n - driftComp)) / 1000));

                if ((endAt - (n - driftComp)) > 0 && !answered) {
                    selected = radioEl.value;
                    answered = true;

                    // freeze timer before lockUI changes DOM
                    const frozenTime = toMMSS(remainAtClick);
                    document.getElementById("timer-answered").setAttribute("data-frozen", frozenTime);

                    lockUI();

                    // restore frozen time after UI is switched
                    document.getElementById("timer-answered").textContent =
                        document.getElementById("timer-answered").getAttribute("data-frozen");

                    // ✅ Calculate actual time taken
                    const totalTime = Number(current.question_time || 0); // seconds
                    const timeTaken = Math.max(0, totalTime - remainAtClick);

                    // ✅ Send correct fields
                    fetch("../answer.process.php?act=submit_answer", {
                            method: "POST",
                            headers: {
                                "Content-Type": "application/json"
                            },
                            body: JSON.stringify({
                                quiz_id: QUIZ_ID,
                                question_id: current.id,
                                selected_option: selected,
                                time_taken: timeTaken, // in seconds
                                session: sessionId
                            })
                        })
                        .then(res => res.json())
                        .then(data => {
                            console.log("Answer response:", data);
                        })
                        .catch(err => console.warn("submit failed:", err));
                } else {
                    radioEl.checked = false; // late click
                }
            }



            // attach change listeners (radios exist in DOM)
            optsActive.forEach(r => r.addEventListener('change', e => handleAnswerSelect(e.target)));

            // apply question data
            function applyQuestion(q) {
                if (!q || q.status !== 'active') {
                    stopTicker();
                    hide(boxActive);
                    hide(boxAnswered);
                    show(boxWait);
                    current = null;
                    return;
                }

                const needReset = !current || q.id !== current.id || q.start_time !== current.start_time;
                current = q;
                if (needReset) {
                    renderQuestion(q);
                    startTimer(q);
                } else if (!answered) {
                    updateTimerDisplays(nowMs());
                }
            }

            // poll loop (keeps server offset up-to-date)
            async function pollLoop() {
                try {
                    const t0 = Date.now();
                    const res = await fetch(`ajax/get_current_question.php?quiz_id=${encodeURIComponent(QUIZ_ID)}`, {
                        cache: 'no-store'
                    });
                    const t1 = Date.now();
                    if (!res.ok) throw new Error(res.status);
                    const js = await res.json();

                    if (js && js.serverNowMs) {
                        // calibrate using midpoint
                        const clientMid = (t0 + t1) / 2;
                        setServerOffset(Number(js.serverNowMs)); // set server offset
                    }
                    applyQuestion(js.data || null);
                    pollDelay = (js.data && js.data.status === 'active') ? 800 : 1500;
                } catch (e) {
                    console.warn("poll error", e);
                    pollDelay = 2000;
                } finally {
                    setTimeout(pollLoop, pollDelay);
                }
            }

            // bootstrap
            if (window.__CURRENT__) {
                if (window.__SERVER_NOW_MS__) {
                    setServerOffset(window.__SERVER_NOW_MS__);
                }
                applyQuestion(window.__CURRENT__);
            } else {
                hide(boxActive);
                hide(boxAnswered);
                show(boxWait);
            }
            setTimeout(pollLoop, 200);

        })();
    </script>

</body>

</html>
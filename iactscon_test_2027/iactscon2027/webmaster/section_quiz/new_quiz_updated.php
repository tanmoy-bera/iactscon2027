<?php
include_once('includes/init.php');
$pageKey = "_pgn_";
$pageKeyVal = ($_REQUEST[$pageKey] == "") ? 0 : $_REQUEST[$pageKey];
$searchString = "";
$searchArray = [];
$searchArray[$pageKey] = $pageKeyVal;
foreach ($searchArray as $searchKey => $searchVal) {
    $searchString .= "&" . $searchKey . "=" . $searchVal;
}

$quiz_id = isset($_REQUEST['quiz_id']) ? (int)$_REQUEST['quiz_id'] : 0;
$slides_for_js = [];

if ($quiz_id > 0) {
    $sql = [];
    $sql['QUERY'] = "
        SELECT q.id, q.quiz_id, q.level, q.round, q.question_text,
            q.media_url, q.media_type, q.media_description,
            q.timer_override, q.row_status, q.status, q.is_last_question,
            opt.id AS opt_id, opt.option_text, opt.is_correct
        FROM rcg_quiz_questions q
        LEFT JOIN rcg_quiz_question_options opt ON opt.question_id = q.id
        WHERE q.row_status != 'D'
        AND q.quiz_id = ?
        ORDER BY q.round ASC, q.level ASC, q.id ASC, opt.id ASC
    ";
    $sql['PARAM'][] = ['FILD' => 'quiz_id', 'DATA' => $quiz_id, 'TYP' => 'i'];
    $all_questions = $mycms->sql_select($sql);

    if (!empty($all_questions)) {
        $tmp = [];
        foreach ($all_questions as $row) {
            $qid = $row['id'];
            if (!isset($tmp[$qid])) {
                $mediaType = strtolower($row['media_type'] ?? '');
                // $mediaObj  = ['type' => $mediaType ?: 'none', 'images' => [], 'videoUrl' => null, 'audioUrl' => null];
                $mediaObj  = ['type' => $mediaType ?: 'none', 'images' => [], 'videoUrl' => null, 'audioUrl' => null, 'description' => $row['media_description'] ?? ''];
                if (!empty($row['media_url'])) {
                    if ($mediaType === 'image') $mediaObj['images'][] = ltrim($row['media_url'], "/");
                    if ($mediaType === 'video') $mediaObj['videoUrl'] = $row['media_url'];
                    if ($mediaType === 'audio') $mediaObj['audioUrl'] = $row['media_url'];
                }
                $tmp[$qid] = [
                    'id' => $qid, 'question' => $row['question_text'],
                    'options' => [], 'correctIndex' => 0, 'media' => $mediaObj,
                    'round' => (int)$row['round'], 'level' => (int)$row['level'],
                    'extraTime' => (int)$row['timer_override'], 'isLastQuestion' => ($row['is_last_question'] == 1)
                ];
            }
            if ($row['opt_id']) {
                $index = count($tmp[$qid]['options']);
                $tmp[$qid]['options'][] = ["id" => $row['opt_id'], "text" => $row['option_text'], "isCorrect" => ($row['is_correct'] == 1)];
                if ($row['is_correct'] == 1) $tmp[$qid]['correctIndex'] = $index;
            }
        }
        $slides_for_js = array_values($tmp);
    }
}

$sqlQuiz = [];
$sqlQuiz['QUERY']   = "SELECT * FROM `rcg_quizzes` WHERE `id` = ?";
$sqlQuiz['PARAM'][] = ['FILD' => 'id', 'DATA' => $quiz_id, 'TYP' => 'i'];
$qResult    = $mycms->sql_select($sqlQuiz);
$quiz       = $qResult[0] ?? [];
$quizRounds = $quiz['rounds'] ?? 1;
$quizLevels = $quiz['levels'] ?? 1;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Questions</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600&display=swap" rel="stylesheet" />
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="css/new_quiz_details.css" />
    <style>
        #autoSaveStatus {
            display: inline-flex; align-items: center; gap: 6px;
            font-size: 12px; padding: 4px 10px; border-radius: 20px; font-weight: 500;
            transition: opacity 0.3s ease; opacity: 0; pointer-events: none;
            position: fixed; bottom: 20px; right: 24px; z-index: 9999;
            box-shadow: 0 2px 8px rgba(0,0,0,0.12);
        }
        #autoSaveStatus.visible { opacity: 1; }
        #autoSaveStatus.saving  { background:#fffbeb; color:#d97706; border:1px solid #fcd34d; }
        #autoSaveStatus.saved   { background:#f0fdf4; color:#16a34a; border:1px solid #86efac; }
        #autoSaveStatus.error   { background:#e53935; color:#fff;    border:1px solid #fca5a5; }
        .save-dot { width:7px; height:7px; border-radius:50%; background:currentColor; animation:savePulse 0.8s ease-in-out infinite alternate; }
        @keyframes savePulse { from { opacity:0.3; } to { opacity:1; } }
        .slide-card { transition:background 0.15s ease; cursor:pointer; }
        .slide-card:hover { background:#f9fafb; }
        .opt-input:focus { border-color:#6366f1 !important; box-shadow:0 0 0 3px rgba(99,102,241,0.1); }
        .set-correct-btn.active { background:#16a34a !important; border-color:#16a34a !important; color:#fff !important; }
        .set-correct-btn.active i { color:#fff !important; }
        .option-box { cursor: pointer; user-select: none; }
        .option-box:hover { opacity: 0.85; }
        .option-box.empty-option { opacity: 0.5; }
        .notification.error { background: #e53935; color: #fff; border-left: 4px solid #b71c1c; }
    </style>
</head>
<body>

<div class="container"></div>

<div id="autoSaveStatus">
    <span class="save-dot" id="saveDot"></span>
    <span id="autoSaveText">Saving...</span>
</div>

<!-- LEFT PANEL -->
<div class="slides-panel" id="slidesPanel">
    <h4>
        <button class="back-btn" id="leftBackBtn"><i class="fas fa-arrow-left"></i></button>
        Quiz Questions
    </h4>
    <div class="slides-container" id="slidesContainer"></div>
    <button id="addSlideBtn">+ Add Question</button>
</div>

<!-- CENTER PREVIEW -->
<div class="preview-area" id="previewArea">
    <div class="preview-header">
        <div class="preview-title">Question Preview</div>
        <div class="quiz-details-toggle" id="quizDetailsToggle">Quiz Details <i class="fas fa-chevron-down"></i></div>
    </div>
    <div class="quiz-details-panel" id="quizDetailsPanel">
        <div class="quiz-details-grid">
            <div class="quiz-detail-item"><span class="detail-label">Quiz Title:</span><span class="detail-value"><?= $quiz['title'] ?? '---' ?></span></div>
            <div class="quiz-detail-item"><span class="detail-label">Hall:</span><span class="detail-value"><?php
                $sh = []; $sh['QUERY'] = "SELECT hall_title FROM rcg_program_schedule_hall WHERE status='A' AND id=?";
                $sh['PARAM'][] = ['FILD'=>'id','DATA'=>$quiz['hall_id'],'TYP'=>'i'];
                $halls = $mycms->sql_select($sh); echo $halls[0]['hall_title'] ?? '';
            ?></span></div>
            <div class="quiz-detail-item"><span class="detail-label">Quiz Status:</span><span class="detail-value"><?= ucfirst($quiz['status'] ?? '---') ?></span></div>
            <div class="quiz-detail-item"><span class="detail-label">Start Time:</span><span class="detail-value"><?= date('d-m-Y H:i:s', strtotime($quiz['start_time'])) ?></span></div>
            <div class="quiz-detail-item"><span class="detail-label">Rounds:</span><span class="detail-value"><?= $quiz['rounds'] ?? '---' ?></span></div>
            <div class="quiz-detail-item"><span class="detail-label">Levels:</span><span class="detail-value"><?= $quiz['levels'] ?></span></div>
            <div class="quiz-detail-item"><span class="detail-label">Answer Time:</span><span class="detail-value"><?= $quiz['answer_time'] ?> sec</span></div>
            <div class="quiz-detail-item"><span class="detail-label">Buffer Time:</span><span class="detail-value"><?= $quiz['buffer_time'] ?> sec</span></div>
            <div class="quiz-detail-item"><span class="detail-label">View Answer:</span><span class="detail-value"><?php
                $va = ['end'=>'At The End of The Quiz','question'=>'After Each Question','round'=>'After Each Round','level'=>'After Each Level'];
                echo $va[$quiz['view_answer']] ?? '';
            ?></span></div>
            <div class="quiz-detail-item"><span class="detail-label">QR Code:</span><span class="detail-value"><?= ($quiz['enable_qr']=='yes') ? 'Enabled' : 'Disabled' ?></span></div>
            <div class="quiz-detail-item"><span class="detail-label">Seat Limit:</span><span class="detail-value"><?= $quiz['seat_limit'] ?></span></div>
            <div class="quiz-detail-item"><span class="detail-label">Application Closing:</span><span class="detail-value"><?= date('d-m-Y H:i:s', strtotime($quiz['application_close_time'])) ?></span></div>
            <div class="quiz-detail-item"><span class="detail-label">Application Status:</span><span class="detail-value"><?= ucfirst($quiz['application_status'] ?? '---') ?></span></div>
            <div class="quiz-detail-item"><span class="detail-label">Participation Type:</span><span class="detail-value"><?= $quiz['participation_type'] ? ucfirst($quiz['participation_type']) : '' ?></span></div>
            <div class="quiz-detail-item"><span class="detail-label">Correct (Marks):</span><span class="detail-value"><?= $quiz['marks_correct'] ?></span></div>
            <div class="quiz-detail-item"><span class="detail-label">Incorrect (Marks):</span><span class="detail-value"><?= $quiz['marks_incorrect'] ?></span></div>
        </div>
    </div>
    <div class="question-preview">
        <div class="question-meta">
            <div class="meta-badge round"         id="previewRound">Round 1</div>
            <div class="meta-badge level"         id="previewLevel">Level 1</div>
            <div class="meta-badge time"          id="previewTime"         style="display:none">+0s</div>
            <div class="meta-badge last-question" id="previewLastQuestion" style="display:none">Last Question</div>
        </div>
        <h2 id="previewQuestion">Your question will appear here</h2>
        <div class="media-preview" id="mediaPreview"></div>
        <div class="media-description-preview" id="mediaDescPreview" style="display:none;font-size:13px;color:#4b5563;margin-top:8px;"></div>
        <div class="options-grid"  id="previewOptions"></div>
    </div>
</div>

<!-- RIGHT EDIT PANEL -->
<div class="settings-panel" id="settingsPanel">
    <h3>Edit Question</h3>

    <div class="input-group">
        <label>Question Text</label>
        <textarea id="qText" rows="3" placeholder="Type question here..."></textarea>
    </div>
    <div class="input-group">
        <label>Round</label>
        <select id="roundSelect"></select>
    </div>
    <div class="input-group">
        <label>Level</label>
        <select id="levelSelect"></select>
    </div>
    <div class="input-group">
        <label>Extra Time (seconds)</label>
        <input type="number" id="extraTime" min="0" max="300" value="0" />
        <small style="color:#6b7280;font-size:12px;margin-top:5px;display:block;">Additional time beyond the default timer</small>
    </div>
    <div class="media-upload-section">
        <h4>Add Media</h4>
        <div class="media-type-selector">
            <button class="media-type-btn active" data-type="none">None</button>
            <button class="media-type-btn" data-type="image"><i class="fas fa-image"></i></button>
            <button class="media-type-btn" data-type="video"><i class="fas fa-video"></i></button>
            <button class="media-type-btn" data-type="audio"><i class="fas fa-music"></i></button>
        </div>
        <div class="media-upload-content" id="imageUpload" style="display:none">
            <div class="upload-area" id="imageDropZone">
                <i class="fas fa-cloud-upload-alt"></i>
                <p>Drag &amp; drop your images here</p>
                <span>or click to browse</span>
                <input type="file" id="imageInput" name="image" accept="image/*" style="display:none">
            </div>
            <div class="image-gallery" id="imageGallery"></div>
        </div>
        <div class="media-upload-content" id="videoUpload" style="display:none">
            <div class="drive-link-input"><input type="text" id="videoLink" placeholder="Paste Google Drive or YouTube video link..." /></div>
            <div class="preview-media" id="videoPreview"></div>
        </div>
        <div class="media-upload-content" id="audioUpload" style="display:none">
            <div class="drive-link-input"><input type="text" id="audioLink" placeholder="Paste Google Drive audio link..." /></div>
            <div class="preview-media" id="audioPreview"></div>
        </div>
        <div class="input-group" id="mediaDescWrap" style="display:none;margin-top:12px;">
            <label>Media Description <small style="color:#9ca3af;font-weight:400;">(optional)</small></label>
            <textarea id="mediaDescription" rows="3" placeholder="Describe the media (shown with the question)..."></textarea>
        </div>
    </div>
    <div class="input-group">
        <label>Question Type</label>
        <div style="padding:10px 12px;border:1px solid #d1d5db;border-radius:8px;background:#f3f4f6;">Multiple Choice</div>
    </div>
    <div class="input-group">
        <label>Options <small style="color:#9ca3af;font-weight:400;">(leave blank to skip)</small></label>
        <div id="optionsContainer"></div>
        <button class="add-option-btn" id="addOptionBtn">+ Add Option</button>
    </div>
    <div class="input-group">
        <label>Is Last Question</label>
        <div class="checkbox-group">
            <input type="checkbox" id="isLastQuestion" />
            <label for="isLastQuestion">Mark as Last Question</label>
        </div>
        <small style="color:#6b7280;font-size:12px;margin-top:5px;display:block;">This question will be treated as the final question in the quiz</small>
    </div>
    <button class="save-btn" id="saveBtn">Save Question</button>
</div>

<div class="notification" id="notification">Question saved successfully!</div>

<script>
const serverSlides = <?php echo json_encode($slides_for_js, JSON_HEX_TAG|JSON_HEX_AMP|JSON_HEX_APOS|JSON_HEX_QUOT); ?> || [];
const PAGE_QUIZ_ID = <?php echo json_encode($quiz_id); ?>;
window.quizRounds  = <?php echo (int)$quizRounds; ?>;
window.quizLevels  = <?php echo (int)$quizLevels; ?>;
</script>

<script>
// ==================================================================
//  CONSTANTS
// ==================================================================
var PLACEHOLDER_QUESTION = 'New Question';
var PLACEHOLDER_OPTION   = function(n) { return 'Option ' + n; };

// ==================================================================
//  DOM REFS
// ==================================================================
var previewQuestion     = document.getElementById('previewQuestion');
var previewRound        = document.getElementById('previewRound');
var previewLevel        = document.getElementById('previewLevel');
var previewTime         = document.getElementById('previewTime');
var previewLastQuestion = document.getElementById('previewLastQuestion');
var qText               = document.getElementById('qText');
var roundSelect         = document.getElementById('roundSelect');
var levelSelect         = document.getElementById('levelSelect');
var extraTime           = document.getElementById('extraTime');
var isLastQuestion      = document.getElementById('isLastQuestion');
var optionsContainer    = document.getElementById('optionsContainer');
var previewOptions      = document.getElementById('previewOptions');
var addOptionBtn        = document.getElementById('addOptionBtn');
var saveBtn             = document.getElementById('saveBtn');
var addSlideBtn         = document.getElementById('addSlideBtn');
var slidesContainer     = document.getElementById('slidesContainer');
var notification        = document.getElementById('notification');
var mediaPreview        = document.getElementById('mediaPreview');
var imageGallery        = document.getElementById('imageGallery');
var leftBackBtn         = document.getElementById('leftBackBtn');
var quizDetailsToggle   = document.getElementById('quizDetailsToggle');
var quizDetailsPanel    = document.getElementById('quizDetailsPanel');
var autoSaveStatus      = document.getElementById('autoSaveStatus');
var autoSaveText        = document.getElementById('autoSaveText');
var saveDot             = document.getElementById('saveDot');

var mediaTypeButtons    = document.querySelectorAll('.media-type-btn');
var mediaUploadContents = document.querySelectorAll('.media-upload-content');
var imageDropZone       = document.getElementById('imageDropZone');
var imageInput          = document.getElementById('imageInput');
var videoLink           = document.getElementById('videoLink');
var videoPreview        = document.getElementById('videoPreview');
var audioLink           = document.getElementById('audioLink');
var audioPreview        = document.getElementById('audioPreview');
var mediaDescription    = document.getElementById('mediaDescription');
var mediaDescWrap       = document.getElementById('mediaDescWrap');
var mediaDescPreview    = document.getElementById('mediaDescPreview');

// ==================================================================
//  STATE
// ==================================================================
var slides             = [];
var currentSlideId     = null;
var correctAnswerIndex = 0;
var autoSaveHideTimer  = null;
var isSaving           = false;
var isEditingOption    = false;

// ==================================================================
//  SORT HELPER
// ==================================================================
function sortKey(s) {
    return s.id ? s.id : (1e15 + (s.tempId || 0));
}
function sortedSlides(arr) {
    return arr.slice().sort(function(a, b) {
        if (a.round !== b.round) return a.round - b.round;
        if (a.level !== b.level) return a.level - b.level;
        return sortKey(a) - sortKey(b);
    });
}

// ==================================================================
//  LOCALSTORAGE
// ==================================================================
function lsKey(slideRef) {
    var slide = (typeof slideRef === 'object')
        ? slideRef
        : slides.find(function(s) { return (s.id || s.tempId) === slideRef; });
    if (slide && slide.id)     return 'quiz_' + PAGE_QUIZ_ID + '_q_'       + slide.id;
    if (slide && slide.tempId) return 'quiz_' + PAGE_QUIZ_ID + '_pending_' + slide.tempId;
    return 'quiz_' + PAGE_QUIZ_ID + '_pending_unknown';
}

function lsSave(slideRef, data) {
    var slide = (typeof slideRef === 'object')
        ? slideRef
        : slides.find(function(s) { return (s.id || s.tempId) === slideRef; });
    var payload = { ts: Date.now(), data: data };
    if (slide && slide.tempId && !slide.id) payload.tempId = slide.tempId;
    try { localStorage.setItem(lsKey(slideRef), JSON.stringify(payload)); } catch(e) {}
}

function lsLoad(slideRef) {
    try {
        var raw = localStorage.getItem(lsKey(slideRef));
        return raw ? JSON.parse(raw) : null;
    } catch(e) { return null; }
}

function lsClear(slideRef) {
    try { localStorage.removeItem(lsKey(slideRef)); } catch(e) {}
}

function buildDraftSnapshot() {
    var idx = getCurrentSlideIndex();
    if (idx === -1) return null;
    var inputs = document.querySelectorAll('.opt-input');
    var optionTexts = slides[idx].options.map(function(opt, i) {
        var domInput = inputs[i];
        return domInput ? domInput.value : (opt.text || '');
    });
    return {
        question:       qText.value,
        optionTexts:    optionTexts,
        correctIndex:   slides[idx].correctIndex,
        round:          parseInt(roundSelect.value) || 1,
        level:          parseInt(levelSelect.value) || 1,
        extraTime:      parseInt(extraTime.value)   || 0,
        isLastQuestion: isLastQuestion.checked,
        mediaDescription: mediaDescription ? mediaDescription.value : ''
    };
}

// ==================================================================
//  AUTO-CAPITALIZE HELPER
// ==================================================================
function capitalizeFirst(str) {
    if (!str) return str;
    return str.charAt(0).toUpperCase() + str.slice(1);
}
function applyCapitalize(input) {
    var pos   = input.selectionStart;
    var val   = input.value;
    var fixed = capitalizeFirst(val);
    if (fixed !== val) {
        input.value          = fixed;
        input.selectionStart = pos;
        input.selectionEnd   = pos;
    }
}

// ==================================================================
//  BADGE HELPERS
// ==================================================================
function showSavingBadge() {
    clearTimeout(autoSaveHideTimer);
    autoSaveStatus.className = 'visible saving';
    saveDot.style.display    = 'block';
    autoSaveText.textContent = 'Saving...';
}
function showSavedBadge() {
    clearTimeout(autoSaveHideTimer);
    autoSaveStatus.className = 'visible saved';
    saveDot.style.display    = 'none';
    autoSaveText.textContent = 'Saved';
    autoSaveHideTimer = setTimeout(function() { autoSaveStatus.className = ''; }, 2500);
}
function showSaveErrorBadge(msg) {
    clearTimeout(autoSaveHideTimer);
    autoSaveStatus.className = 'visible error';
    saveDot.style.display    = 'none';
    autoSaveText.textContent = msg || 'Save failed';
    autoSaveHideTimer = setTimeout(function() { autoSaveStatus.className = ''; }, 6000);
}

// ==================================================================
//  AUTO-SAVE STRATEGY
// ==================================================================
var dbSaveInterval = null;

function startDbSaveInterval() {
    if (dbSaveInterval) return;
    dbSaveInterval = setInterval(function() {
        if (isSaving || isEditingOption) return;

        var queue = slides
            .filter(function(s) {
                var q = (s.question || '').trim();
                if (!q || q === PLACEHOLDER_QUESTION) return false;
                if (!lsLoad(s)) return false;
                return hasMinimumOptions(s, 2);
            })
            .sort(function(a, b) {
                var aKey = a.id ? (2e15 + a.id) : (a.tempId || 0);
                var bKey = b.id ? (2e15 + b.id) : (b.tempId || 0);
                return aKey - bKey;
            });

        if (queue.length === 0) return;

        var oldest    = queue[0];
        var oldestKey = oldest.id || oldest.tempId;

        if (oldestKey === currentSlideId) {
            performSave(false);
        } else {
            performSaveForSlide(oldest);
        }
    }, 5000);
}

function hasMinimumOptions(slide, min) {
    var nonEmpty = slide.options.filter(function(o) {
        return (o.text || '').trim() !== '';
    });
    return nonEmpty.length >= min;
}

function scheduleAutoSave() {
    var idx = getCurrentSlideIndex();
    if (idx === -1) return;
    lsSave(slides[idx], buildDraftSnapshot());
}

// ==================================================================
//  MEDIA HELPERS
// ==================================================================

function getMediaPayloadState(slide) {
    var media = slide.media || { type: 'none', images: [], videoUrl: null, audioUrl: null };
    return {
        type:      media.type      || 'none',
        imageUrl:  (media.images && media.images[0] && !media.images[0].startsWith('data:'))
                       ? media.images[0].replace(/^\//, '') : '',
        videoUrl:  media.videoUrl  || '',
        audioUrl:  media.audioUrl  || '',
        description: media.description || '',
        cleared:   !!media.cleared
    };
}

function syncMediaAfterSave(slide, serverMediaUrl) {
    if (!slide || !slide.media) return;
    if (serverMediaUrl && slide.media.type === 'image') {
        slide.media.images = [serverMediaUrl];
    }
    delete slide.media.cleared;
}

// ==================================================================
//  PAGE-LEAVE HANDLERS
// ==================================================================
function flushCurrentSlideToState() {
    var idx = getCurrentSlideIndex();
    if (idx === -1) return;
    var s = slides[idx];
    var q = qText.value.trim();
    if (q && q !== PLACEHOLDER_QUESTION) s.question = q;
    s.round          = parseInt(roundSelect.value) || s.round;
    s.level          = parseInt(levelSelect.value) || s.level;
    s.extraTime      = parseInt(extraTime.value)   || 0;
    s.isLastQuestion = isLastQuestion.checked;
    if (mediaDescription) {
        s.media = s.media || { type: 'none', images: [], videoUrl: '', audioUrl: '' };
        s.media.description = mediaDescription.value;
    }
    document.querySelectorAll('.opt-input').forEach(function(inp, i) {
        if (s.options[i]) s.options[i].text = inp.value;
    });
    lsSave(s, buildDraftSnapshot());
}

function buildBeaconPayload(slide) {
    var q = (slide.question || '').trim();
    if (!q || q === PLACEHOLDER_QUESTION) return null;
    if (!hasMinimumOptions(slide, 2)) return null;

    var params = new URLSearchParams();
    var allOptions = slide.options.map(function(opt, idx) {
        return { id: opt.id || '', text: opt.text || '', isCorrect: !!opt.isCorrect };
    });

    var correctInAll = allOptions.findIndex(function(o) { return o.isCorrect; });
    if (correctInAll === -1) correctInAll = 0;

    params.append('act',            slide.id ? 'edit_question' : 'add_question');
    if (slide.id) params.append('question_id', slide.id);
    params.append('quiz_id',        PAGE_QUIZ_ID);
    params.append('question_text',  q);
    params.append('correct_option', 'ABCDEFGHI'[correctInAll] || 'A');
    allOptions.forEach(function(opt, idx) {
        params.append('options[' + idx + '][id]',         opt.id);
        params.append('options[' + idx + '][text]',       opt.text);
        params.append('options[' + idx + '][is_correct]', opt.isCorrect ? 1 : 0);
    });
    params.append('round',            slide.round     || 1);
    params.append('level',            slide.level     || 1);
    params.append('timer_override',   slide.extraTime || 0);
    params.append('is_last_question', slide.isLastQuestion ? 'Yes' : 'No');

    var mp = getMediaPayloadState(slide);
    if (mp.cleared) {
        params.append('media_type', 'none');
        params.append('media_url',  '');
    } else if (mp.type === 'image') {
        params.append('media_type', 'image');
        params.append('media_url',  mp.imageUrl);
    } else if (mp.type === 'video' && mp.videoUrl) {
        params.append('media_type', 'video');
        params.append('media_url',  mp.videoUrl);
    } else if (mp.type === 'audio' && mp.audioUrl) {
        params.append('media_type', 'audio');
        params.append('media_url',  mp.audioUrl);
    } else {
        params.append('media_type', '');
        params.append('media_url',  '');
    }
    params.append('media_description', mp.cleared ? '' : (mp.description || ''));
    return params;
}

function saveAllPendingSlides(useBeacon) {
    slides.forEach(function(slide) {
        var q = (slide.question || '').trim();
        if (!q || q === PLACEHOLDER_QUESTION) return;
        if (!lsLoad(slide)) return;
        if (!hasMinimumOptions(slide, 2)) return;

        var params = buildBeaconPayload(slide);
        if (!params) return;

        if (useBeacon) {
            var blob = new Blob([params.toString()], { type: 'application/x-www-form-urlencoded' });
            navigator.sendBeacon('manage_question.process.php', blob);
            lsClear(slide);
        } else {
            fetch('manage_question.process.php', { method: 'POST', body: params })
                .then(function(res) { return res.json(); })
                .then(function(data) {
                    if (data && data.status == 1) {
                        if (!slide.id && data.id) {
                            lsClear(slide);
                            var oldId = slide.tempId;
                            slide.id = data.id;
                            if (currentSlideId === oldId) currentSlideId = data.id;
                            delete slide.tempId;
                            syncMediaAfterSave(slide, data.media_url || null);
                            try {
                                var lsLastKey = 'quiz_' + PAGE_QUIZ_ID + '_lastSlide';
                                var lsLastRaw = localStorage.getItem(lsLastKey);
                                if (lsLastRaw) {
                                    var p = JSON.parse(lsLastRaw);
                                    if (p.id === oldId) localStorage.setItem(lsLastKey, JSON.stringify({ id: data.id }));
                                }
                            } catch(e) {}
                        }
                        lsClear(slide);
                        showSavedBadge();
                        renderAllSlides();
                    }
                })
                .catch(function() {});
        }
    });
}

function setupPageLeaveHandlers() {
    document.addEventListener('visibilitychange', function() {
        if (document.visibilityState === 'hidden') {
            flushCurrentSlideToState();
            saveAllPendingSlides(false);
        }
    });
    window.addEventListener('beforeunload', function() {
        flushCurrentSlideToState();
        saveAllPendingSlides(true);
    });
}

// ==================================================================
//  INIT
// ==================================================================
function defaultOptions() {
    return [
        { id: null, text: '', isCorrect: true  },
        { id: null, text: '', isCorrect: false },
        { id: null, text: '', isCorrect: false },
        { id: null, text: '', isCorrect: false }
    ];
}

function populateRoundLevelSelectors(maxRounds, maxLevels) {
    roundSelect.innerHTML = '';
    for (var i = 1; i <= maxRounds; i++) {
        var o = document.createElement('option');
        o.value = i; o.text = 'Round ' + i;
        roundSelect.appendChild(o);
    }
    levelSelect.innerHTML = '';
    for (var j = 1; j <= maxLevels; j++) {
        var ol = document.createElement('option');
        ol.value = j; ol.text = 'Level ' + j;
        levelSelect.appendChild(ol);
    }
}

function initEditor() {
    slides = serverSlides.map(function(s) {
        return {
            id:             s.id   || null,
            question:       s.question || PLACEHOLDER_QUESTION,
            options:        (s.options && s.options.length) ? s.options : defaultOptions(),
            correctIndex:   typeof s.correctIndex === 'number' ? s.correctIndex : 0,
            media:          s.media || { type: 'none', images: [], videoUrl: null, audioUrl: null, description: '' },
            round:          s.round      || 1,
            level:          s.level      || 1,
            extraTime:      s.extraTime  || 0,
            isLastQuestion: !!s.isLastQuestion
        };
    });

    var _tempBase = Date.now();
    slides.forEach(function(s, i) { if (!s.id) s.tempId = _tempBase + i; });

    // ── MERGE pending drafts into existing server slides ─────────────────────
    var pendingPrefix = 'quiz_' + PAGE_QUIZ_ID + '_pending_';
    try {
        var keysToRestore = [];
        for (var ki = 0; ki < localStorage.length; ki++) {
            var key = localStorage.key(ki);
            if (key && key.indexOf(pendingPrefix) === 0) keysToRestore.push(key);
        }

        keysToRestore.forEach(function(key) {
            var storedTempId = parseInt(key.replace(pendingPrefix, ''), 10);
            if (!storedTempId || isNaN(storedTempId)) return;

            var raw = localStorage.getItem(key);
            if (!raw) return;
            var entry;
            try { entry = JSON.parse(raw); } catch(e) { return; }
            var draft = entry.data;
            if (!draft || !draft.question || draft.question === PLACEHOLDER_QUESTION) return;

            var matchingServerSlide = null;
            for (var mi = 0; mi < slides.length; mi++) {
                var s = slides[mi];
                if (s.question.trim().toLowerCase() === draft.question.trim().toLowerCase() &&
                    s.round === (draft.round || 1) &&
                    s.level === (draft.level || 1)) {
                    matchingServerSlide = s;
                    break;
                }
            }

            if (matchingServerSlide) {
                matchingServerSlide.question = draft.question;
                matchingServerSlide.round    = draft.round          || 1;
                matchingServerSlide.level    = draft.level          || 1;
                matchingServerSlide.extraTime = typeof draft.extraTime === 'number' ? draft.extraTime : 0;
                matchingServerSlide.isLastQuestion = !!draft.isLastQuestion;
                if (typeof draft.mediaDescription === 'string') {
                    matchingServerSlide.media = matchingServerSlide.media || { type: 'none', images: [], videoUrl: null, audioUrl: null };
                    matchingServerSlide.media.description = draft.mediaDescription;
                }
                if (Array.isArray(draft.optionTexts) && draft.optionTexts.length > 0) {
                    for (var oi = 0; oi < draft.optionTexts.length; oi++) {
                        if (!matchingServerSlide.options[oi]) {
                            matchingServerSlide.options.push({ id: null, text: '', isCorrect: false });
                        }
                        matchingServerSlide.options[oi].text = draft.optionTexts[oi];
                    }
                    if (matchingServerSlide.options.length > draft.optionTexts.length) {
                        matchingServerSlide.options = matchingServerSlide.options.slice(0, draft.optionTexts.length);
                    }
                }
                if (typeof draft.correctIndex === 'number') {
                    matchingServerSlide.correctIndex = draft.correctIndex;
                    matchingServerSlide.options.forEach(function(o, i) { o.isCorrect = (i === draft.correctIndex); });
                }
                try { localStorage.removeItem(key); } catch(e) {}
            } else {
                var restoredOptions = [];
                if (Array.isArray(draft.optionTexts) && draft.optionTexts.length > 0) {
                    restoredOptions = draft.optionTexts.map(function(text, idx) {
                        return { id: null, text: text || '', isCorrect: idx === (draft.correctIndex || 0) };
                    });
                } else {
                    restoredOptions = defaultOptions();
                }
                slides.push({
                    id:             null,
                    tempId:         storedTempId,
                    question:       draft.question,
                    options:        restoredOptions,
                    correctIndex:   typeof draft.correctIndex === 'number' ? draft.correctIndex : 0,
                    media:          { type: 'none', images: [], videoUrl: null, audioUrl: null, description: draft.mediaDescription || '' },
                    round:          draft.round          || 1,
                    level:          draft.level          || 1,
                    extraTime:      typeof draft.extraTime === 'number' ? draft.extraTime : 0,
                    isLastQuestion: draft.isLastQuestion || false
                });
            }
        });
    } catch(e) { console.warn('[initEditor] LS pending restore error:', e); }

    populateRoundLevelSelectors(window.quizRounds, window.quizLevels);

    if (slides.length === 0) {
        currentSlideId = null;
        slidesContainer.innerHTML = "<p style='color:#888;padding:12px;'>No Questions Added Yet</p>";
        document.getElementById('previewArea').style.opacity    = '0.4';
        document.querySelector('.settings-panel').style.display = 'none';
        previewQuestion.textContent = 'Select or Add a Question';
        [previewRound, previewLevel, previewTime, previewLastQuestion].forEach(function(el) { el.style.display = 'none'; });
        setTimeout(function() { addNewSlide(); }, 150);
    } else {
        var lastSlideKey = 'quiz_' + PAGE_QUIZ_ID + '_lastSlide';
        var lastActiveId = null;
        try {
            var raw = localStorage.getItem(lastSlideKey);
            if (raw) {
                var parsed    = JSON.parse(raw);
                var candidate = parsed.id;
                var exists    = slides.some(function(s) { return (s.id || s.tempId) === candidate; });
                if (exists) {
                    lastActiveId = candidate;
                } else {
                    var sorted2  = sortedSlides(slides);
                    var last     = sorted2[sorted2.length - 1];
                    lastActiveId = last ? (last.id || last.tempId) : null;
                }
            }
        } catch(e) {}
        currentSlideId = lastActiveId || (slides[0].id || slides[0].tempId);
        renderAllSlides();
        loadSlide(currentSlideId);
    }

    setupEventListeners();
    setupMediaUpload();
    startDbSaveInterval();
    setupPageLeaveHandlers();
}

// ==================================================================
//  EVENT LISTENERS
// ==================================================================
function setupEventListeners() {
    qText.addEventListener('focus', function() {
        if (qText.value.trim() === PLACEHOLDER_QUESTION) {
            qText.value = '';
            qText.classList.remove('placeholder-active');
        }
    });
    qText.addEventListener('blur', function() {
        if (qText.value.trim() === '') {
            qText.value = PLACEHOLDER_QUESTION;
            qText.classList.add('placeholder-active');
        }
        scheduleAutoSave();
    });
    qText.addEventListener('input', function() {
        applyCapitalize(qText);
        updatePreviewQuestion();
        scheduleAutoSave();
    });

    roundSelect.addEventListener('change',    function() { updatePreviewMeta(); scheduleAutoSave(); });
    levelSelect.addEventListener('change',    function() { updatePreviewMeta(); scheduleAutoSave(); });
    extraTime.addEventListener('input',       function() { updatePreviewMeta(); });
    extraTime.addEventListener('blur',        function() { scheduleAutoSave(); });
    isLastQuestion.addEventListener('change', function() { updatePreviewMeta(); scheduleAutoSave(); });

    addOptionBtn.addEventListener('click',  addOption);
    saveBtn.addEventListener('click',       function() { performSave(true); });
    addSlideBtn.addEventListener('click',   addNewSlide);
    leftBackBtn.addEventListener('click',   function() { history.back(); });
    quizDetailsToggle.addEventListener('click', function() {
        quizDetailsPanel.classList.toggle('expanded');
        quizDetailsToggle.classList.toggle('expanded');
    });

    mediaTypeButtons.forEach(function(btn) {
        btn.addEventListener('click', function() { onMediaTypeUserClick(btn.dataset.type); });
    });
    if (videoLink) videoLink.addEventListener('input', function() { updateVideoPreview(); scheduleAutoSave(); });
    if (audioLink) audioLink.addEventListener('input', function() { updateAudioPreview(); scheduleAutoSave(); });
    if (mediaDescription) {
        mediaDescription.addEventListener('input', function() {
            var i = getCurrentSlideIndex();
            if (i !== -1) slides[i].media.description = mediaDescription.value;
            updateMediaDescPreview();
        });
        mediaDescription.addEventListener('blur', scheduleAutoSave);
    }
}

// ==================================================================
//  MEDIA TYPE — USER CLICK vs PROGRAMMATIC
// ==================================================================

function onMediaTypeUserClick(type) {
    var idx = getCurrentSlideIndex();
    if (idx === -1) return;
    var slide = slides[idx];
    var currentType = slide.media.type || 'none';

    if (type === currentType) return;

    if (type === 'none') {
        slide.media = { type: 'none', images: [], videoUrl: '', audioUrl: '', description: '', cleared: true };
        if (mediaDescription) mediaDescription.value = '';
    } else {
        slide.media.type = type;
        if (type === 'image') {
            slide.media.videoUrl = '';
            slide.media.audioUrl = '';
        } else if (type === 'video') {
            slide.media.images   = [];
            slide.media.audioUrl = '';
        } else if (type === 'audio') {
            slide.media.images   = [];
            slide.media.videoUrl = '';
        }
    }

    applyMediaTypeUI(type);

    if (type === 'image')  { updateImageGallery(); updateMediaPreview(); }
    if (type === 'video')  { videoLink.value = slide.media.videoUrl || ''; updateVideoPreview(); }
    if (type === 'audio')  { audioLink.value = slide.media.audioUrl || ''; updateAudioPreview(); }

    scheduleAutoSave();
}

function applyMediaTypeUI(type) {
    mediaTypeButtons.forEach(function(btn) {
        btn.classList.toggle('active', btn.dataset.type === type);
    });
    mediaUploadContents.forEach(function(c) { c.style.display = 'none'; });

    if (type === 'image') document.getElementById('imageUpload').style.display = 'block';
    if (type === 'video') document.getElementById('videoUpload').style.display = 'block';
    if (type === 'audio') document.getElementById('audioUpload').style.display = 'block';
    if (mediaDescWrap) mediaDescWrap.style.display = (type === 'none') ? 'none' : 'block';
}

// ==================================================================
//  RENDER SLIDE LIST
// ==================================================================
function renderAllSlides() {
    slidesContainer.innerHTML = '';
    var sorted = sortedSlides(slides);
    sorted.forEach(function(slide) {
        var idKey = slide.id || slide.tempId;
        var card  = document.createElement('div');
        card.className  = 'slide-card ' + (idKey === currentSlideId ? 'active' : '');
        card.dataset.id = idKey;

        var indicators = '';
        if (slide.extraTime > 0)  indicators += '<span style="color:#d97706;font-size:10px;margin-right:4px;"><i class="fas fa-clock"></i> +' + slide.extraTime + 's</span>';
        if (slide.isLastQuestion) indicators += '<span style="color:#be185d;font-size:10px;"><i class="fas fa-flag-checkered"></i></span>';

        var isPlaceholder = (!slide.question || slide.question === PLACEHOLDER_QUESTION);
        var label = isPlaceholder
            ? '<span style="color:#9ca3af;font-style:italic;">New question...</span>'
            : (slide.question.length > 30 ? slide.question.substring(0, 30) + '...' : slide.question);

        card.innerHTML =
            '<div class="slide-content">' + label + ' ' + indicators + '</div>' +
            '<div class="slide-meta"><span>R' + slide.round + '</span><span>Lvl ' + slide.level + '</span></div>' +
            '<button class="delete-slide"><i class="fas fa-times"></i></button>';

        slidesContainer.appendChild(card);

        card.addEventListener('click', function(e) {
            if (!e.target.closest('.delete-slide')) selectSlide(idKey);
        });
        card.querySelector('.delete-slide').addEventListener('click', function(e) {
            e.stopPropagation(); deleteSlide(idKey);
        });
    });
}

// ==================================================================
//  PREVIEW
// ==================================================================
function updatePreviewQuestion() {
    previewQuestion.textContent = qText.value || 'Your question will appear here';
    var idx = getCurrentSlideIndex();
    if (idx !== -1) slides[idx].question = qText.value;
    renderAllSlides();
}

function updatePreviewMeta() {
    previewRound.textContent = 'Round ' + roundSelect.value;
    previewLevel.textContent = 'Level ' + levelSelect.value;
    var t = parseInt(extraTime.value) || 0;
    previewTime.textContent           = '+' + t + 's';
    previewTime.style.display         = t > 0 ? 'flex' : 'none';
    previewLastQuestion.style.display = isLastQuestion.checked ? 'flex' : 'none';

    var idx = getCurrentSlideIndex();
    if (idx !== -1) {
        slides[idx].round          = parseInt(roundSelect.value);
        slides[idx].level          = parseInt(levelSelect.value);
        slides[idx].extraTime      = t;
        slides[idx].isLastQuestion = isLastQuestion.checked;
        renderAllSlides();
    }
}

function renderPreviewOptions() {
    var slide = slides.find(function(s) { return (s.id || s.tempId) === currentSlideId; });
    if (!slide) return;
    previewOptions.innerHTML = '';

    slide.options.forEach(function(opt, originalIdx) {
        var text = (typeof opt === 'string') ? opt : (opt.text || '');
        var isEmpty = !text.trim();

        var lbl       = document.createElement('label');
        lbl.className = 'option-box ' +
                        (originalIdx === slide.correctIndex ? 'correct' : '') +
                        (isEmpty ? ' empty-option' : '');
        lbl.style.cursor = 'pointer';

        var radio     = document.createElement('input');
        radio.type    = 'radio';
        radio.name    = 'previewOpt';
        radio.checked = (originalIdx === slide.correctIndex);

        lbl.addEventListener('click', function(e) {
            e.preventDefault();
            setCorrectAnswer(originalIdx);
        });

        lbl.appendChild(radio);
        lbl.appendChild(document.createTextNode(' ' + (text || '(empty)')));
        previewOptions.appendChild(lbl);
    });
}

// ==================================================================
//  MEDIA
// ==================================================================
function setupMediaUpload() {
    if (!imageDropZone) return;
    imageDropZone.addEventListener('click', function() { imageInput.click(); });
    imageInput.addEventListener('change', function(e) {
        var files = Array.from(e.target.files).filter(function(f) { return f.type.startsWith('image/'); });
        previewImageFiles(files);
    });
    imageDropZone.addEventListener('dragover', function(e) {
        e.preventDefault();
        imageDropZone.style.borderColor = '#4f46e5';
        imageDropZone.style.background  = '#f8faff';
    });
    imageDropZone.addEventListener('dragleave', function() {
        imageDropZone.style.borderColor = '#d1d5db';
        imageDropZone.style.background  = '';
    });
    imageDropZone.addEventListener('drop', function(e) {
        e.preventDefault();
        imageDropZone.style.borderColor = '#d1d5db';
        imageDropZone.style.background  = '';
        var files = Array.from(e.dataTransfer.files).filter(function(f) { return f.type.startsWith('image/'); });
        if (files.length) previewImageFiles(files);
    });
}

function previewImageFiles(files) {
    var idx = getCurrentSlideIndex();
    if (idx === -1) return;
    files.forEach(function(file) {
        var reader = new FileReader();
        reader.onload = function(e) {
            slides[idx].media.images = [e.target.result];
            slides[idx].media.type   = 'image';
            delete slides[idx].media.cleared;
            updateImageGallery();
            updateMediaPreview();
            scheduleAutoSave();
        };
        reader.readAsDataURL(file);
    });
}

function updateImageGallery() {
    var idx = getCurrentSlideIndex();
    if (idx === -1) return;
    var images = slides[idx].media.images || [];
    imageGallery.innerHTML = '';
    images.forEach(function(img, i) {
        var displaySrc = img;
        var div = document.createElement('div');
        div.className = 'image-gallery-item';
        div.innerHTML =
            '<img src="' + displaySrc + '" alt="Preview">' +
            '<button class="remove-image" data-img-index="' + i + '"><i class="fas fa-times"></i></button>';
        div.querySelector('.remove-image').addEventListener('click', function() {
            removeImage(i);
        });
        imageGallery.appendChild(div);
    });
}

window.removeImage = function(index) {
    var idx = getCurrentSlideIndex();
    if (idx === -1) return;
    slides[idx].media.images.splice(index, 1);
    if (imageInput) imageInput.value = '';
    if (slides[idx].media.images.length === 0) {
        slides[idx].media.type    = 'none';
        slides[idx].media.description = '';
        slides[idx].media.cleared     = true;
        if (mediaDescription) mediaDescription.value = '';
        applyMediaTypeUI('none');
        mediaPreview.innerHTML = '';
    }
    updateImageGallery();
    updateMediaPreview();
    scheduleAutoSave();
};

function updateVideoPreview() {
    var url = videoLink.value.trim();
    var idx = getCurrentSlideIndex();
    if (idx === -1) return;
    slides[idx].media.videoUrl = url;
    videoPreview.innerHTML = url
        ? '<p>Video link added</p><button class="remove-media" id="removeVideoBtn">Remove</button>'
        : '';
    if (url) {
        document.getElementById('removeVideoBtn').addEventListener('click', function() { removeMedia(); });
    }
    updateMediaPreview();
}

function updateAudioPreview() {
    var url = audioLink.value.trim();
    var idx = getCurrentSlideIndex();
    if (idx === -1) return;
    slides[idx].media.audioUrl = url;
    audioPreview.innerHTML = url
        ? '<p>Audio link added</p><button class="remove-media" id="removeAudioBtn">Remove</button>'
        : '';
    if (url) {
        document.getElementById('removeAudioBtn').addEventListener('click', function() { removeMedia(); });
    }
    updateMediaPreview();
}

window.removeMedia = function() {
    var idx = getCurrentSlideIndex();
    if (idx === -1) return;
    slides[idx].media = { type: 'none', images: [], videoUrl: '', audioUrl: '', description: '', cleared: true };
    if (mediaDescription) mediaDescription.value = '';
    imageGallery.innerHTML = '';
    if (videoPreview) videoPreview.innerHTML = '';
    if (audioPreview) audioPreview.innerHTML = '';
    mediaPreview.innerHTML = '';
    if (videoLink) videoLink.value = '';
    if (audioLink) audioLink.value = '';
    if (imageInput) imageInput.value = '';
    applyMediaTypeUI('none');
    scheduleAutoSave();
};

function extractDriveFileId(url) {
    var m1 = url.match(/\/d\/([a-zA-Z0-9_-]+)/);
    if (m1) return m1[1];
    var m2 = url.match(/id=([a-zA-Z0-9_-]+)/);
    if (m2) return m2[1];
    return null;
}

function extractYoutubeId(url) {
    var m;
    m = url.match(/youtu\.be\/([a-zA-Z0-9_-]+)/);
    if (m) return m[1];
    m = url.match(/[?&]v=([a-zA-Z0-9_-]+)/);
    if (m) return m[1];
    m = url.match(/youtube\.com\/embed\/([a-zA-Z0-9_-]+)/);
    if (m) return m[1];
    m = url.match(/youtube\.com\/shorts\/([a-zA-Z0-9_-]+)/);
    if (m) return m[1];
    m = url.match(/youtube\.com\/live\/([a-zA-Z0-9_-]+)/);
    if (m) return m[1];
    return null;
}

function updateMediaPreview() {
    var idx = getCurrentSlideIndex();
    if (idx === -1) return;
    var media = slides[idx].media;
    mediaPreview.innerHTML = '';
    updateMediaDescPreview();
    if (media.type === 'image' && media.images && media.images.length > 0) {
        mediaPreview.innerHTML = '<img src="' + media.images[0] + '" style="max-width:100%;max-height:200px;border-radius:8px;">';
    }
    if (media.type === 'video' && media.videoUrl) {
        var ytid = extractYoutubeId(media.videoUrl);
        var fid  = extractDriveFileId(media.videoUrl);
        if (ytid) {
            mediaPreview.innerHTML = '<iframe src="https://www.youtube.com/embed/' + ytid + '" width="100%" height="200" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>';
        } else if (fid) {
            mediaPreview.innerHTML = '<iframe src="https://drive.google.com/file/d/' + fid + '/preview" width="100%" height="200" allow="autoplay"></iframe>';
        } else {
            mediaPreview.innerHTML = '<p style="color:#e53935">Invalid video URL</p>';
        }
    }
    if (media.type === 'audio' && media.audioUrl) {
        var afid = extractDriveFileId(media.audioUrl);
        mediaPreview.innerHTML = afid
            ? '<iframe src="https://drive.google.com/file/d/' + afid + '/preview" width="100%" height="80"></iframe>'
            : '<p style="color:#e53935">Invalid audio URL</p>';
    }
}

function updateMediaDescPreview() {
    if (!mediaDescPreview) return;
    var idx = getCurrentSlideIndex();
    var txt = (idx !== -1 && slides[idx].media) ? (slides[idx].media.description || '') : '';
    var type = (idx !== -1 && slides[idx].media) ? (slides[idx].media.type || 'none') : 'none';
    mediaDescPreview.textContent   = txt;
    mediaDescPreview.style.display = (txt.trim() !== '' && type !== 'none') ? 'block' : 'none';
}

// ==================================================================
//  OPTIONS
// ==================================================================
function addOption() {
    var idx = getCurrentSlideIndex();
    if (idx === -1) return;
    slides[idx].options.push({ id: null, text: '', isCorrect: false });
    renderOptionsInputs();
    scheduleAutoSave();
}

function setCorrectAnswer(index) {
    var idx = getCurrentSlideIndex();
    if (idx === -1) return;
    slides[idx].options.forEach(function(o, i) { o.isCorrect = (i === index); });
    slides[idx].correctIndex = index;
    correctAnswerIndex       = index;
    renderOptionsInputs();
    renderPreviewOptions();
    scheduleAutoSave();
}

function deleteOption(index) {
    var idx = getCurrentSlideIndex();
    if (idx === -1) return;
    if (slides[idx].options.length <= 2) { showNotification('You need at least 2 options', 'error'); return; }

    var wasCorrect = slides[idx].options[index].isCorrect;
    slides[idx].options.splice(index, 1);

    if (wasCorrect) {
        slides[idx].options[0].isCorrect = true;
        slides[idx].correctIndex = 0;
    } else {
        slides[idx].correctIndex = slides[idx].options.findIndex(function(o) { return o.isCorrect; });
        if (slides[idx].correctIndex === -1) {
            slides[idx].options[0].isCorrect = true;
            slides[idx].correctIndex = 0;
        }
    }
    correctAnswerIndex = slides[idx].correctIndex;
    renderOptionsInputs();
    renderPreviewOptions();
    scheduleAutoSave();
}

function renderOptionsInputs() {
    var idx = getCurrentSlideIndex();
    if (idx === -1) return;
    var slide = slides[idx];
    optionsContainer.innerHTML = '';

    slide.options.forEach(function(opt, i) {
        var placeholder = PLACEHOLDER_OPTION(i + 1);
        var isDefault   = (!opt.text || opt.text === placeholder);
        var inputVal    = isDefault ? '' : opt.text;

        var wrapper = document.createElement('div');
        wrapper.className = 'option-input-wrapper';
        wrapper.innerHTML =
            '<input type="text"' +
            ' class="opt-input"' +
            ' value="' + inputVal.replace(/"/g, '&quot;') + '"' +
            ' placeholder="' + placeholder + '"' +
            ' data-opt-index="' + i + '">' +
            '<div class="option-actions">' +
            '<button class="option-btn set-correct-btn ' + (i === slide.correctIndex ? 'active' : '') + '"' +
            ' data-opt-index="' + i + '" title="Set as correct answer"><i class="fas fa-check"></i></button>' +
            '<button class="option-btn delete-option-btn" data-opt-index="' + i + '" title="Remove option"><i class="fas fa-times"></i></button>' +
            '</div>';
        optionsContainer.appendChild(wrapper);
    });

    // CRITICAL: bind events to the newly created inputs and buttons
    optionsContainer.querySelectorAll('.opt-input').forEach(function(inp) {
        var optIdx = parseInt(inp.dataset.optIndex);
        inp.addEventListener('focus', function() { isEditingOption = true; });
        inp.addEventListener('blur', function() {
            isEditingOption = false;
            var i   = getCurrentSlideIndex();
            var val = inp.value.trim();
            if (i !== -1) slides[i].options[optIdx].text = val;
            renderPreviewOptions();
            scheduleAutoSave();
        });
        inp.addEventListener('input', function(e) {
            applyCapitalize(e.target);
            var i = getCurrentSlideIndex();
            if (i !== -1) slides[i].options[optIdx].text = e.target.value;
            renderPreviewOptions();
            scheduleAutoSave();
        });
    });

    optionsContainer.querySelectorAll('.set-correct-btn').forEach(function(btn) {
        btn.addEventListener('click', function(e) { setCorrectAnswer(parseInt(e.currentTarget.dataset.optIndex)); });
    });
    optionsContainer.querySelectorAll('.delete-option-btn').forEach(function(btn) {
        btn.addEventListener('click', function(e) { deleteOption(parseInt(e.currentTarget.dataset.optIndex)); });
    });

    renderPreviewOptions();
}

// Helper to detect placeholder text
function isPlaceholderText(text, index) {
    return text === PLACEHOLDER_OPTION(index + 1);
}

// Collect options for saving – always include ALL options, never filter empties
function collectAllOptionsForSave(slide) {
    return slide.options.map(function(opt, idx) {
        var rawText = opt.text || '';
        var cleanText = isPlaceholderText(rawText, idx) ? '' : rawText;
        return {
            id:        opt.id || '',
            text:      cleanText,
            isCorrect: !!opt.isCorrect,
            originalIndex: idx
        };
    });
}

// ==================================================================
//  PERFORM SAVE FOR NON-ACTIVE SLIDE
// ==================================================================
function performSaveForSlide(slide) {
    if (!slide) return;
    var questionText = (slide.question || '').trim();
    if (!questionText || questionText === PLACEHOLDER_QUESTION) return;
    if (!hasMinimumOptions(slide, 2)) return;

    var allOptions = collectAllOptionsForSave(slide);
    var correctInAll = allOptions.findIndex(function(o) { return o.isCorrect; });
    if (correctInAll === -1) correctInAll = 0;

    var existingId         = slide.id || null;
    var savedSlideIdentity = slide.id || slide.tempId;

    var payload = new FormData();
    payload.append('act',            existingId ? 'edit_question' : 'add_question');
    if (existingId) payload.append('question_id', existingId);
    payload.append('quiz_id',        PAGE_QUIZ_ID);
    payload.append('question_text',  questionText);
    payload.append('correct_option', 'ABCDEFGHI'[correctInAll] || 'A');

    allOptions.forEach(function(opt, idx) {
        payload.append('options[' + idx + '][id]',         opt.id);
        payload.append('options[' + idx + '][text]',       opt.text);
        payload.append('options[' + idx + '][is_correct]', opt.isCorrect ? 1 : 0);
    });

    payload.append('round',            slide.round     || 1);
    payload.append('level',            slide.level     || 1);
    payload.append('timer_override',   slide.extraTime || 0);
    payload.append('is_last_question', slide.isLastQuestion ? 'Yes' : 'No');

    var mp = getMediaPayloadState(slide);
    if (mp.cleared) {
        payload.append('media_type', 'none');
        payload.append('media_url',  '');
    } else if (mp.type === 'image') {
        payload.append('media_type', 'image');
        payload.append('media_url',  mp.imageUrl);
    } else if (mp.type === 'video' && mp.videoUrl) {
        payload.append('media_type', 'video');
        payload.append('media_url',  mp.videoUrl);
    } else if (mp.type === 'audio' && mp.audioUrl) {
        payload.append('media_type', 'audio');
        payload.append('media_url',  mp.audioUrl);
    } else {
        payload.append('media_type', '');
        payload.append('media_url',  '');
    }
    payload.append('media_description', mp.cleared ? '' : (mp.description || ''));

    isSaving = true;
    showSavingBadge();

    if (!existingId) {
        try {
            var lsKeyStr = lsKey(slide);
            var lsRaw    = localStorage.getItem(lsKeyStr);
            if (lsRaw) {
                var lsEntry = JSON.parse(lsRaw);
                lsEntry.saveAttempted     = true;
                lsEntry.savedQuestionText = questionText;
                localStorage.setItem(lsKeyStr, JSON.stringify(lsEntry));
            }
        } catch(e) {}
    }

    enhancedFetch('manage_question.process.php', { method: 'POST', body: payload })
        .then(function(res) { return res.text(); })
        .then(function(responseText) {
            isSaving = false;
            var data;
            try { data = JSON.parse(responseText); } catch(e) {
                showSaveErrorBadge('Server error');
                console.error('[performSaveForSlide] Non-JSON response:', responseText);
                return;
            }

            var savedSlide = slides.find(function(s) {
                return (s.id || s.tempId) === savedSlideIdentity;
            });
            if (!savedSlide) return;

            if (data && data.status == 1) {
                if (!savedSlide.id && data.id) {
                    lsClear(savedSlide);
                    var oldTempId = savedSlide.tempId;
                    savedSlide.id = data.id;
                    if (currentSlideId === oldTempId) currentSlideId = data.id;
                    delete savedSlide.tempId;
                    try {
                        var lsLastKey = 'quiz_' + PAGE_QUIZ_ID + '_lastSlide';
                        var lsLastRaw = localStorage.getItem(lsLastKey);
                        if (lsLastRaw) {
                            var lsLastParsed = JSON.parse(lsLastRaw);
                            if (lsLastParsed.id === oldTempId) {
                                localStorage.setItem(lsLastKey, JSON.stringify({ id: data.id }));
                            }
                        }
                    } catch(e) {}
                }
                lsClear(savedSlide);
                syncMediaAfterSave(savedSlide, data.media_url || null);
                showSavedBadge();
            } else {
                showSaveErrorBadge(data && data.message ? data.message : 'Save failed');
            }

            savedSlide.correctIndex = allOptions.findIndex(function(o) { return o.isCorrect; });
            if (savedSlide.correctIndex === -1) savedSlide.correctIndex = 0;

            renderAllSlides();
            if (currentSlideId === (savedSlide.id || savedSlide.tempId)) {
                renderOptionsInputs();
                renderPreviewOptions();
                updateImageGallery();
                updateMediaPreview();
            }
        })
        .catch(function(err) {
            isSaving = false;
            showSaveErrorBadge('Network error');
            console.error('[performSaveForSlide] fetch error:', err);
        });
}

// ==================================================================
//  PERFORM SAVE (active slide)
// ==================================================================
function performSave(isManual) {
    var questionText = qText.value.trim();

    if (!questionText || questionText === PLACEHOLDER_QUESTION) {
        if (isManual) showNotification('Please enter a question', 'error');
        return;
    }

    var slideIndex = getCurrentSlideIndex();
    if (slideIndex === -1) return;

    var allOptions = collectAllOptionsForSave(slides[slideIndex]);

    if (isManual) {
        var nonEmptyCount = allOptions.filter(function(o) { return o.text.trim() !== ''; }).length;
        if (nonEmptyCount < 2) {
            showNotification('Please fill in at least 2 options', 'error');
            return;
        }
    } else {
        if (!hasMinimumOptions(slides[slideIndex], 2)) return;
    }

    var correctInAll = allOptions.findIndex(function(o) { return o.isCorrect; });
    if (correctInAll === -1) {
        if (isManual) {
            showNotification('Please mark a correct answer', 'error');
            return;
        } else {
            correctInAll = 0;
        }
    }

    var savedSlideIdentity = slides[slideIndex].id || slides[slideIndex].tempId;
    var round        = parseInt(roundSelect.value);
    var level        = parseInt(levelSelect.value);
    var time         = parseInt(extraTime.value) || 0;
    var lastQuestion = isLastQuestion.checked;
    var existingId   = slides[slideIndex].id || null;

    var payload = new FormData();
    payload.append('act',           existingId ? 'edit_question' : 'add_question');
    if (existingId) payload.append('question_id', existingId);
    payload.append('quiz_id',       PAGE_QUIZ_ID);
    payload.append('question_text', questionText);
    payload.append('correct_option', 'ABCDEFGHI'[correctInAll] || 'A');

    allOptions.forEach(function(opt, idx) {
        payload.append('options[' + idx + '][id]',         opt.id);
        payload.append('options[' + idx + '][text]',       opt.text);
        payload.append('options[' + idx + '][is_correct]', opt.isCorrect ? 1 : 0);
    });

    payload.append('round',            round);
    payload.append('level',            level);
    payload.append('timer_override',   time);
    payload.append('is_last_question', lastQuestion ? 'Yes' : 'No');

    var mp = getMediaPayloadState(slides[slideIndex]);
    if (mp.cleared) {
        payload.append('media_type', 'none');
        payload.append('media_url',  '');
    } else if (mp.type === 'image') {
        payload.append('media_type', 'image');
        if (imageInput && imageInput.files && imageInput.files.length > 0) {
            payload.append('image',     imageInput.files[0]);
            payload.append('media_url', '');
        } else {
            payload.append('media_url', mp.imageUrl);
        }
    } else if (mp.type === 'video' && mp.videoUrl) {
        payload.append('media_type', 'video');
        payload.append('media_url',  mp.videoUrl);
    } else if (mp.type === 'audio' && mp.audioUrl) {
        payload.append('media_type', 'audio');
        payload.append('media_url',  mp.audioUrl);
    } else {
        payload.append('media_type', '');
        payload.append('media_url',  '');
    }
    payload.append('media_description', mp.cleared ? '' : (mediaDescription ? mediaDescription.value : (mp.description || '')));

    isSaving = true;
    if (isManual) showSavingBadge();

    if (!existingId) {
        try {
            var lsKeyStr = lsKey(slides[slideIndex]);
            var lsRaw    = localStorage.getItem(lsKeyStr);
            if (lsRaw) {
                var lsEntry = JSON.parse(lsRaw);
                lsEntry.saveAttempted     = true;
                lsEntry.savedQuestionText = questionText;
                localStorage.setItem(lsKeyStr, JSON.stringify(lsEntry));
            }
        } catch(e) {}
    }

    enhancedFetch('manage_question.process.php', { method: 'POST', body: payload })
        .then(function(res) { return res.text(); })
        .then(function(responseText) {
            isSaving = false;
            var data;
            try {
                data = JSON.parse(responseText);
            } catch(e) {
                var errMsg = 'Server error';
                if (isManual) showNotification(errMsg, 'error');
                else          showSaveErrorBadge(errMsg);
                console.error('[performSave] Non-JSON response:', responseText);
                return;
            }

            var savedSlide = slides.find(function(s) {
                return (s.id || s.tempId) === savedSlideIdentity;
            });
            if (!savedSlide) { console.warn('[performSave] slide disappeared during fetch'); return; }

            if (data && data.status == 1) {
                if (!savedSlide.id && data.id) {
                    lsClear(savedSlide);
                    savedSlide.id = data.id;
                    if (currentSlideId === savedSlideIdentity) currentSlideId = data.id;
                    delete savedSlide.tempId;
                    try {
                        var lsLastKey = 'quiz_' + PAGE_QUIZ_ID + '_lastSlide';
                        var lsLastRaw = localStorage.getItem(lsLastKey);
                        if (lsLastRaw) {
                            var lsLastParsed = JSON.parse(lsLastRaw);
                            if (lsLastParsed.id === savedSlideIdentity) {
                                localStorage.setItem(lsLastKey, JSON.stringify({ id: data.id }));
                            }
                        }
                    } catch(e) {}
                }
                lsClear(savedSlide);

                if (imageInput) imageInput.value = '';
                syncMediaAfterSave(savedSlide, data.media_url || null);

                showSavedBadge();
                if (isManual) showNotification(data.message || 'Question saved successfully!');
            } else {
                var errMsg = (data && data.message) ? data.message : 'Error saving question';
                if (isManual) showNotification(errMsg, 'error');
                else          showSaveErrorBadge(errMsg);
            }

            savedSlide.question       = questionText;
            savedSlide.correctIndex   = allOptions.findIndex(function(o) { return o.isCorrect; });
            if (savedSlide.correctIndex === -1) savedSlide.correctIndex = 0;
            savedSlide.round          = round;
            savedSlide.level          = level;
            savedSlide.extraTime      = time;
            savedSlide.isLastQuestion = lastQuestion;

            allOptions.forEach(function(saved, idx) {
                if (saved.originalIndex < savedSlide.options.length) {
                    savedSlide.options[saved.originalIndex].id        = saved.id || savedSlide.options[saved.originalIndex].id;
                    savedSlide.options[saved.originalIndex].isCorrect = saved.isCorrect;
                }
            });

            renderAllSlides();
            if (currentSlideId === (savedSlide.id || savedSlide.tempId)) {
                renderOptionsInputs();
                renderPreviewOptions();
                updateImageGallery();
                updateMediaPreview();
            }
        })
        .catch(function(err) {
            isSaving = false;
            var errMsg = 'Network error';
            showSaveErrorBadge(errMsg);
            if (isManual) showNotification(errMsg, 'error');
            console.error('[performSave] fetch error:', err);
        });
}

// ==================================================================
//  ADD / SELECT / LOAD / DELETE SLIDES
// ==================================================================
function addNewSlide() {
    document.querySelector('.settings-panel').style.display = 'block';
    document.getElementById('previewArea').style.opacity    = '1';

    flushCurrentSlideToState();

    var newTempId = Date.now();
    var newSlide  = {
        id: null, tempId: newTempId,
        question: PLACEHOLDER_QUESTION,
        options:  defaultOptions(),
        correctIndex: 0,
        media: { type: 'none', images: [], videoUrl: '', audioUrl: '', description: '' },
        round: 1, level: 1, extraTime: 0, isLastQuestion: false
    };
    slides.push(newSlide);
    renderAllSlides();
    selectSlide(newSlide.tempId);
}

function selectSlide(idKey) {
    currentSlideId = idKey;
    try { localStorage.setItem('quiz_' + PAGE_QUIZ_ID + '_lastSlide', JSON.stringify({ id: idKey })); } catch(e) {}
    renderAllSlides();
    loadSlide(idKey);
}

function loadSlide(idKey) {
    var slide = slides.find(function(s) { return (s.id || s.tempId) === idKey; });
    if (!slide) return;

    var stored = lsLoad(slide);
    if (stored && stored.data && stored.data.question && stored.data.question !== PLACEHOLDER_QUESTION) {
        var draft = stored.data;
        slide.question = draft.question;
        if (Array.isArray(draft.optionTexts) && draft.optionTexts.length > 0) {
            draft.optionTexts.forEach(function(text, i) {
                if (!slide.options[i]) slide.options.push({ id: null, text: '', isCorrect: false });
                slide.options[i].text = text;
            });
            if (draft.optionTexts.length < slide.options.length) {
                slide.options = slide.options.slice(0, draft.optionTexts.length);
            }
        }
        if (typeof draft.correctIndex === 'number') {
            slide.correctIndex = draft.correctIndex;
            slide.options.forEach(function(o, i) { o.isCorrect = (i === draft.correctIndex); });
        }
        if (draft.round)                               slide.round          = draft.round;
        if (draft.level)                               slide.level          = draft.level;
        if (typeof draft.extraTime      === 'number')  slide.extraTime      = draft.extraTime;
        if (typeof draft.isLastQuestion === 'boolean') slide.isLastQuestion = draft.isLastQuestion;
        if (typeof draft.mediaDescription === 'string') {
            slide.media = slide.media || { type: 'none', images: [], videoUrl: '', audioUrl: '' };
            slide.media.description = draft.mediaDescription;
        }
    }

    qText.value = slide.question;
    qText.classList.toggle('placeholder-active', slide.question === PLACEHOLDER_QUESTION);

    correctAnswerIndex     = slide.correctIndex || 0;
    roundSelect.value      = slide.round  || 1;
    levelSelect.value      = slide.level  || 1;
    extraTime.value        = slide.extraTime || 0;
    isLastQuestion.checked = slide.isLastQuestion || false;

    if (imageInput) imageInput.value = '';

    var mediaType = (slide.media && slide.media.type) ? slide.media.type : 'none';
    applyMediaTypeUI(mediaType);
    if (mediaDescription) mediaDescription.value = (slide.media && slide.media.description) ? slide.media.description : '';

    if (mediaType === 'image') {
        updateImageGallery();
        updateMediaPreview();
    } else if (mediaType === 'video') {
        videoLink.value = slide.media.videoUrl || '';
        updateVideoPreview();
    } else if (mediaType === 'audio') {
        audioLink.value = slide.media.audioUrl || '';
        updateAudioPreview();
    } else {
        imageGallery.innerHTML = '';
        if (videoLink)   videoLink.value   = '';
        if (audioLink)   audioLink.value   = '';
        if (videoPreview) videoPreview.innerHTML = '';
        if (audioPreview) audioPreview.innerHTML = '';
        mediaPreview.innerHTML = '';
    }

    updateMediaDescPreview();
    updatePreviewQuestion();
    updatePreviewMeta();
    renderOptionsInputs();
    renderPreviewOptions();

    if (!lsLoad(slide)) {
        lsSave(slide, buildDraftSnapshot());
    }
}

function deleteSlide(idKey) {
    var idx = slides.findIndex(function(s) { return (s.id || s.tempId) === idKey; });
    if (idx === -1) return;
    var slide = slides[idx];

    function getNextActiveId(deletedIdKey, sortedBefore) {
        if (deletedIdKey !== currentSlideId) return currentSlideId;
        if (slides.length === 0) return null;
        var visualIdx   = sortedBefore.findIndex(function(s) { return (s.id || s.tempId) === deletedIdKey; });
        var sortedAfter = sortedSlides(slides);
        if (sortedAfter.length === 0) return null;
        var neighbor = sortedAfter[visualIdx] || sortedAfter[visualIdx - 1] || sortedAfter[0];
        return neighbor ? (neighbor.id || neighbor.tempId) : null;
    }

    if (slide.id) {
        if (!confirm('Delete this question permanently?')) return;
        var sortedBefore = sortedSlides(slides);
        var payload = new FormData();
        payload.append('act', 'Remove');
        payload.append('id',  slide.id);
        enhancedFetch('manage_question.process.php', { method: 'POST', body: payload })
            .then(function(res) { return res.text(); })
            .then(function(responseText) {
                var data;
                try { data = JSON.parse(responseText); } catch(e) {}
                if (data && data.status == 1) {
                    lsClear(slide);
                    showNotification('Deleted');
                    var currentIdx = slides.findIndex(function(s) { return s.id === slide.id; });
                    if (currentIdx !== -1) slides.splice(currentIdx, 1);
                    var nextId = getNextActiveId(idKey, sortedBefore);
                    if (nextId) selectSlide(nextId);
                    else renderAllSlides();
                } else {
                    showNotification((data && data.message) ? data.message : 'Delete failed', 'error');
                }
            })
            .catch(function() { showNotification('Delete failed', 'error'); });
    } else {
        var sortedBefore = sortedSlides(slides);
        lsClear(slide);
        slides.splice(idx, 1);
        var nextId = getNextActiveId(idKey, sortedBefore);
        if (nextId) selectSlide(nextId);
        else renderAllSlides();
    }
}

// ==================================================================
//  UTILITIES
// ==================================================================
function getCurrentSlideIndex() {
    return slides.findIndex(function(s) { return (s.id || s.tempId) === currentSlideId; });
}

function showNotification(message, type) {
    type = type || 'success';
    notification.textContent = message;
    notification.className   = 'notification ' + (type === 'error' ? 'error' : 'success') + ' show';
    var duration = (type === 'error') ? 5000 : 2500;
    setTimeout(function() { notification.classList.remove('show'); }, duration);
}

function enhancedFetch(url, options) {
    return fetch(url, options).then(function(response) {
        if (!response.ok) {
            return response.text().then(function(text) {
                console.error('HTTP Error', response.status, text);
                throw new Error('HTTP ' + response.status + ': ' + response.statusText);
            });
        }
        return response;
    });
}

// Boot
document.addEventListener('DOMContentLoaded', initEditor);
</script>
</body>
</html>
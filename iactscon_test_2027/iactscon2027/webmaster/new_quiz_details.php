<?php
include_once('includes/init.php');
$pageKey = "_pgn_";

$pageKeyVal = ($_REQUEST[$pageKey] == "") ? 0 : $_REQUEST[$pageKey];

@$searchString = "";
$searchArray  = array();

$searchArray[$pageKey]                  = $pageKeyVal;

foreach ($searchArray as $searchKey => $searchVal) {
    $searchString .= "&" . $searchKey . "=" . $searchVal;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Add Question – Concloud</title>
    <link
        href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600&display=swap"
        rel="stylesheet" />
    <link
        href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css"
        rel="stylesheet" />
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: "Inter", sans-serif;
        }

        body {
            background: #fafafa;
            display: flex;
            height: 100vh;
            overflow: hidden;
        }

        /* LEFT SLIDES */
        .slides-panel {
            width: 280px;
            background: #fff;
            border-right: 1px solid #e5e7eb;
            padding: 20px;
            overflow-y: auto;
            transition: all 0.3s ease;
            z-index: 100;
        }

        .slides-panel h4 {
            font-size: 15px;
            margin-bottom: 10px;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .slides-container {
            position: relative;
        }

        .slide-card {
            background: #fff;
            border: 2px solid transparent;
            border-radius: 12px;
            padding: 12px;
            margin-bottom: 8px;
            cursor: pointer;
            transition: 0.2s;
            box-shadow: 0 1px 4px rgba(0, 0, 0, 0.06);
            position: relative;
        }

        .slide-card.active {
            border-color: #4f46e5;
        }

        .slide-card .slide-content {
            font-size: 13px;
            font-weight: 500;
            word-break: break-word;
            margin-bottom: 8px;
        }

        .slide-card .slide-meta {
            font-size: 11px;
            color: #6b7280;
            display: flex;
            gap: 10px;
        }

        .slide-card .slide-meta span {
            color: #6b7280;
        }

        .slide-card .delete-slide {
            background: none;
            border: none;
            color: #ef4444;
            cursor: pointer;
            font-size: 14px;
            opacity: 0;
            transition: opacity 0.2s;
            width: 20px;
            height: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            position: absolute;
            top: 8px;
            right: 8px;
        }

        .slide-card:hover .delete-slide {
            opacity: 1;
        }

        /* CENTER PREVIEW */
        .preview-area {
            flex: 1;
            padding: 0 !important;
            overflow-y: auto;
            display: flex;
            flex-direction: column;
            transition: all 0.3s ease;
        }

        .preview-header {
            display: flex;
            align-items: center;
            padding: 20px 30px;
            border-bottom: 1px solid #e5e7eb;
            background: #fff;
            position: relative;
        }

        .back-btn {
            background: none;
            color: #4f46e5;
            border: none;
            padding: 0;
            border-radius: 8px;
            cursor: pointer;
            font-size: 14px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 0px;
            width: 40px;
            height: 40px;
        }

        .back-btn:hover {
            background: none;
            color: #4f46e5;
            border: 1px solid #4f46e5;
        }

        .preview-title {
            font-size: 18px;
            font-weight: 600;
            color: #374151;
            flex: 1;
        }

        .quiz-details-toggle {
            display: flex;
            align-items: center;
            gap: 8px;
            background: #f3f4f6;
            padding: 8px 16px;
            border-radius: 8px;
            cursor: pointer;
            transition: all 0.2s;
            font-size: 14px;
            font-weight: 500;
        }

        .quiz-details-toggle:hover {
            background: #e5e7eb;
        }

        .quiz-details-toggle i {
            transition: transform 0.3s;
        }

        .quiz-details-toggle.expanded i {
            transform: rotate(180deg);
        }

        .quiz-details-panel {
            background: white;
            border-bottom: 1px solid #e5e7eb;
            padding: 0 30px;
            max-height: 0;
            overflow: hidden;
            transition: all 0.3s ease;
        }

        .quiz-details-panel.expanded {
            min-height: 500px;
            padding: 20px 50px;
            overflow-y: auto;
        }

        .quiz-details-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 15px;
            column-gap: 50px;
        }

        .quiz-detail-item {
            display: flex;
            justify-content: left;
            padding: 8px 0;
            border-bottom: 1px solid #f3f4f6;
        }


        .detail-label {
            font-size: 14px;
            color: #6b7280;
            font-weight: 500;
        }

        .detail-value {
            font-size: 14px;
            color: #374151;
            font-weight: 500;
            text-align: left;
            margin-left: 10px;
        }

        .question-preview {
            background: #fff;
            border: 1px solid #e5e7eb;
            border-radius: 16px;
            padding: 30px;
            width: 95%;
            max-width: 900px;
            margin: 20px auto;
            min-height: 450px;
            position: relative;
        }

        .question-preview h2 {
            font-size: 28px;
            font-weight: 600;
            margin-bottom: 30px;
            word-break: break-word;
        }

        .question-meta {
            display: flex;
            gap: 15px;
            margin-bottom: 20px;
            flex-wrap: wrap;
        }

        .meta-badge {
            background: #f3f4f6;
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 500;
            color: #6b7280;
        }

        .meta-badge.round {
            background: #e0e7ff;
            color: #2d5cff;
        }

        .meta-badge.level {
            background: #d1fae5;
            color: #059669;
        }

        .meta-badge.time {
            background: #fef3c7;
            color: #d97706;
        }

        .meta-badge.last-question {
            background: #fce7f3;
            color: #be185d;
        }

        .media-preview {
            margin: 20px 0;
            text-align: center;
        }

        .media-preview img {
            max-width: 100%;
            max-height: 200px;
            border-radius: 8px;
            margin: 5px;
        }

        .media-preview video {
            max-width: 100%;
            max-height: 200px;
            border-radius: 8px;
        }

        .media-preview audio {
            width: 100%;
            margin: 10px 0;
        }

        .multiple-images {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            gap: 10px;
            margin: 10px 0;
        }

        .multiple-images img {
            max-width: 150px;
            max-height: 150px;
            border-radius: 8px;
            object-fit: cover;
        }

        .options-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 20px;
            margin-top: 20px;
        }

        .option-box {
            border: 1px solid #e5e7eb;
            padding: 14px;
            border-radius: 12px;
            font-size: 16px;
            background: #fafafa;
            display: flex;
            align-items: center;
            gap: 10px;
            cursor: pointer;
            transition: all 0.2s;
            word-break: break-word;
        }

        .option-box:hover {
            background: #f3f4f6;
        }

        .option-box input {
            width: 18px;
            height: 18px;
            flex-shrink: 0;
        }

        .option-box.correct {
            border-color: #40b910;
            background: #ecfdf5;
        }

        /* RIGHT SETTINGS */
        .settings-panel {
            width: 350px;
            background: #fff;
            border-left: 1px solid #e5e7eb;
            padding: 24px;
            overflow-y: auto;
            transition: all 0.3s ease;
        }

        .settings-panel h3 {
            font-size: 17px;
            font-weight: 600;
            margin-bottom: 20px;
        }

        .input-group {
            margin-bottom: 20px;
        }

        .input-group label {
            font-size: 13px;
            margin-bottom: 6px;
            display: block;
            font-weight: 500;
        }

        .input-group select,
        .input-group input,
        .input-group textarea {
            width: 100%;
            padding: 10px 12px;
            border-radius: 8px;
            border: 1px solid #d1d5db;
            font-size: 14px;
        }

        .checkbox-group {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-top: 10px;
        }

        .checkbox-group input[type="checkbox"] {
            width: 18px;
            height: 18px;
        }

        .checkbox-group label {
            margin-bottom: 0;
            font-size: 14px;
            font-weight: 500;
        }

        .option-input-wrapper {
            display: flex;
            align-items: center;
            margin-bottom: 8px;
            gap: 8px;
        }

        .option-input-wrapper input {
            flex: 1;
            min-width: 0;
        }

        .option-actions {
            display: flex;
            gap: 5px;
            flex-shrink: 0;
        }

        .option-btn {
            border: none;
            cursor: pointer;
            font-size: 14px;
            width: 24px;
            height: 24px;
            border-radius: 4px;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }

        .set-correct-btn {
            color: #40b910;
        }

        .set-correct-btn.active {
            background: #40b910;
            color: white;
        }

        .delete-option-btn {
            color: #ef4444;
        }

        .add-option-btn {
            margin-top: 10px;
            background: #4f46e5;
            color: #fff;
            padding: 10px 14px;
            border-radius: 8px;
            cursor: pointer;
            font-size: 14px;
            border: none;
            width: 100%;
        }

        .add-option-btn:hover {
            background: #3b35ac;
            color: #fff;
        }

        .save-btn {
            width: 100%;
            background: #4f46e5;
            color: #fff;
            padding: 12px;
            border-radius: 10px;
            margin-top: 25px;
            border: none;
            font-size: 15px;
            font-weight: 600;
            cursor: pointer;
            transition: background 0.2s;
        }

        .save-btn:hover {
            background: none;
            color: #4f46e5;
            border: 1px solid #4f46e5;
        }

        .notification {
            position: fixed;
            top: 20px;
            right: 20px;
            background: #40b910;
            color: white;
            padding: 12px 20px;
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            z-index: 1000;
            transform: translateX(150%);
            transition: transform 0.3s;
        }

        .notification.show {
            transform: translateX(0);
        }

        .empty-state {
            text-align: center;
            padding: 40px 20px;
            color: #6b7280;
        }

        .empty-state p {
            margin-top: 10px;
            font-size: 14px;
        }

        /* Media Upload Styles */
        .media-upload-section {
            border: 1px solid #e5e7eb;
            border-radius: 8px;
            padding: 15px;
            margin-bottom: 20px;
            background: #fafafa;
        }

        .media-upload-section h4 {
            font-size: 14px;
            margin-bottom: 10px;
            font-weight: 600;
        }

        .media-type-selector {
            display: flex;
            gap: 10px;
            margin-bottom: 15px;
        }

        .media-type-btn {
            flex: 1;
            padding: 8px 12px;
            border: 1px solid #d1d5db;
            border-radius: 6px;
            background: white;
            cursor: pointer;
            font-size: 12px;
            text-align: center;
            transition: all 0.2s;
            min-width: 0;
        }

        .media-type-btn.active {
            background: #4f46e5;
            color: white;
            border-color: #4f46e5;
        }

        .upload-area {
            border: 2px dashed #d1d5db;
            border-radius: 8px;
            padding: 20px;
            text-align: center;
            cursor: pointer;
            transition: all 0.2s;
            margin-bottom: 10px;
        }

        .upload-area:hover {
            border-color: #4f46e5;
            background: #f8faff;
        }

        .upload-area i {
            font-size: 24px;
            color: #9ca3af;
            margin-bottom: 8px;
        }

        .upload-area p {
            font-size: 12px;
            color: #6b7280;
            margin-bottom: 5px;
        }

        .upload-area span {
            font-size: 10px;
            color: #9ca3af;
        }

        .drive-link-input {
            margin-top: 10px;
        }

        .drive-link-input input {
            width: 100%;
            padding: 8px 12px;
            border: 1px solid #d1d5db;
            border-radius: 6px;
            font-size: 12px;
        }

        .preview-media {
            margin-top: 10px;
            text-align: center;
        }

        .preview-media img,
        .preview-media video {
            max-width: 100%;
            max-height: 120px;
            border-radius: 6px;
        }

        .remove-media {
            margin-top: 8px;
            background: #ef4444;
            color: white;
            border: none;
            padding: 5px 10px;
            border-radius: 4px;
            font-size: 11px;
            cursor: pointer;
        }

        .image-gallery {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
            margin-top: 10px;
        }

        .image-gallery-item {
            position: relative;
            width: 80px;
            height: 80px;
            flex-shrink: 0;
        }

        .image-gallery-item img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            border-radius: 6px;
        }

        .image-gallery-item .remove-image {
            position: absolute;
            top: -5px;
            right: -5px;
            background: #ef4444;
            color: white;
            border: none;
            border-radius: 50%;
            width: 20px;
            height: 20px;
            font-size: 12px;
            cursor: pointer;
        }

        /* Mobile Controls */
        .mobile-controls {
            display: none;
            position: fixed;
            bottom: 20px;
            left: 50%;
            transform: translateX(-50%);
            background: white;
            padding: 10px 20px;
            border-radius: 50px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.15);
            z-index: 1000;
            gap: 15px;
        }

        .mobile-control-btn {
            background: #4f46e5;
            color: white;
            border: none;
            width: 40px;
            height: 40px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            font-size: 16px;
        }

        .overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            z-index: 99;
        }

        /* RESPONSIVE DESIGN */
        @media (max-width: 1023px) and (min-width: 768px) {
            body {
                flex-direction: column;
                height: auto;
                min-height: 100vh;
                overflow-y: auto;
            }

            .slides-panel {
                width: 100%;
                height: auto;
                border-right: none;
                border-bottom: 1px solid #e5e7eb;
                padding: 15px;
            }

            .slides-container {
                display: flex;
                gap: 10px;
                overflow-x: auto;
                padding-bottom: 10px;
            }

            .slide-card {
                min-width: 200px;
                margin-bottom: 0;
            }

            .preview-area {
                flex: 1;
                padding: 15px !important;
            }

            .settings-panel {
                width: 100%;
                border-left: none;
                border-top: 1px solid #e5e7eb;
                padding: 20px;
            }

            .question-preview {
                margin: 10px auto;
                padding: 20px;
                min-height: 400px;
            }

            .options-grid {
                grid-template-columns: 1fr;
                gap: 15px;
            }

            .mobile-controls {
                display: flex;
            }

            .quiz-details-grid {
                grid-template-columns: 1fr;
            }
        }

        @media (max-width: 374px) {
            .slides-container {
                flex-direction: column;
            }

            .slide-card {
                min-width: 100%;
            }
        }

        button#addSlideBtn {
            width: 100%;
            padding: 10px;
            background: #4f46e5;
            color: #fff;
            border: none;
            border-radius: 10px;
            margin-top: 10px;
            cursor: pointer;
        }

        button#addSlideBtn:hover {
            background: none;
            color: #4f46e5;
            border: 1px solid #4f46e5;
        }
    </style>
</head>
<?php

$show = $_REQUEST['show'] ?? '';

?>
<body>

    <div class="container">

        <?php
        // DEBUG: Check show value
        // var_dump($show);

        switch ($show) {
            case 'add':
                // addQuiz($cfg, $mycms);
                break;

            default:
                // quiz_details($cfg, $mycms);
                getAllQuestions($_REQUEST['quiz_id'], $mycms);
                break;
        }
        ?>

        <?php

        function quiz_details($cfg, $mycms)
        {
            // echo "in the details func";
            global $searchArray, $searchString;

            $sql 	=	array();
            $sql['QUERY']    = "SELECT * FROM `rcg_quizzes`  
                                WHERE `id` = ?";
            $sql['PARAM'][]	=	array('FILD' => 'id', 'DATA' => $_REQUEST['quiz_id'], 'TYP' => 's');
            $result			 = $mycms->sql_select($sql);
            $row    		 = $result[0];

            $quiz_details = $row;
            
        }

        function getAllQuestions($quiz_id, $mycms)
        {
            // Prepare SQL
            $sql = [];
            $sql['QUERY'] = "
                SELECT 
                    id, 
                    quiz_id, 
                    level, 
                    round, 
                    question_text, 
                    media_url, 
                    option_a, 
                    option_b, 
                    option_c, 
                    option_d, 
                    correct_option, 
                    timer_override, 
                    row_status, 
                    status, 
                    is_last_question
                FROM rcg_quiz_questions
                WHERE row_status != ?
                AND quiz_id = ?
                ORDER BY round ASC, level ASC, id ASC
            ";

            // Add params
            $sql['PARAM'][] = [
                'FILD' => 'row_status',
                'DATA' => 'D',
                'TYP'  => 's'
            ];

            $sql['PARAM'][] = [
                'FILD' => 'quiz_id',
                'DATA' => $quiz_id,
                'TYP'  => 'i'
            ];

            // Fetch ALL rows
            $all_questions = $mycms->sql_select($sql);

            // echo '<pre>';
            // echo json_encode($all_questions);

            echo "<pre>";
            print_r($all_questions);
            echo "</pre>";
            exit;

        }


        ?>
    </div>

    <!-- LEFT PANEL -->
    <div class="slides-panel" id="slidesPanel">
        <h4>
            <button class="back-btn" id="leftBackBtn">
                <i class="fas fa-arrow-left"></i>
            </button>
            Quiz Questions
        </h4>
        <div class="slides-container" id="slidesContainer">
            <!-- Questions will be rendered here -->
        </div>
        <button id="addSlideBtn">+ Add Question</button>
    </div>

    <!-- CENTER PREVIEW -->
    <div class="preview-area" id="previewArea">
        <div class="preview-header">
            <div class="preview-title">Question Preview</div>
            <div class="quiz-details-toggle" id="quizDetailsToggle">
                Quiz Details <i class="fas fa-chevron-down"></i>
            </div>
        </div>

        <!-- Quiz Details Panel -->
        <div class="quiz-details-panel" id="quizDetailsPanel">
            <div class="quiz-details-grid">
                <div class="quiz-detail-item">
                    <span class="detail-label">Quiz Title:</span>
                    <span class="detail-value">Bronchoscopy Quiz</span>
                </div>
                <div class="quiz-detail-item">
                    <span class="detail-label">Quiz Status:</span>
                    <span class="detail-value">Active</span>
                </div>
                <div class="quiz-detail-item">
                    <span class="detail-label">Rounds:</span>
                    <span class="detail-value">5</span>
                </div>
                <div class="quiz-detail-item">
                    <span class="detail-label">Answer Time:</span>
                    <span class="detail-value">30sec</span>
                </div>
                <div class="quiz-detail-item">
                    <span class="detail-label">View Answer:</span>
                    <span class="detail-value">After Each Question</span>
                </div>
                <div class="quiz-detail-item">
                    <span class="detail-label">Seat Limit:</span>
                    <span class="detail-value">10</span>
                </div>
                <div class="quiz-detail-item">
                    <span class="detail-label">Application Status:</span>
                    <span class="detail-value">Active</span>
                </div>
                <div class="quiz-detail-item">
                    <span class="detail-label">Correct Answer(Marks):</span>
                    <span class="detail-value">0.00</span>
                </div>
                <div class="quiz-detail-item">
                    <span class="detail-label">Incorrect Answer(Marks):</span>
                    <span class="detail-value">0.00</span>
                </div>
                <div class="quiz-detail-item">
                    <span class="detail-label">Hall:</span>
                    <span class="detail-value">Mother Teresa</span>
                </div>
                <div class="quiz-detail-item">
                    <span class="detail-label">Start Time:</span>
                    <span class="detail-value">13-11-2025 15:02:00</span>
                </div>
                <div class="quiz-detail-item">
                    <span class="detail-label">Levels:</span>
                    <span class="detail-value">5</span>
                </div>
                <div class="quiz-detail-item">
                    <span class="detail-label">Buffer Time:</span>
                    <span class="detail-value">1 sec</span>
                </div>
                <div class="quiz-detail-item">
                    <span class="detail-label">QR Code:</span>
                    <span class="detail-value">Enable</span>
                </div>
                <div class="quiz-detail-item">
                    <span class="detail-label">Application Closing Time:</span>
                    <span class="detail-value">30-11-2025 18:59:59</span>
                </div>
                <div class="quiz-detail-item">
                    <span class="detail-label">Instruction Bar:</span>
                    <span class="detail-value">Enable</span>
                </div>
            </div>
        </div>

        <div class="question-preview">
            <div class="question-meta">
                <div class="meta-badge round" id="previewRound">Round 1</div>
                <div class="meta-badge level" id="previewLevel">Level 1</div>
                <div class="meta-badge time" id="previewTime" style="display: none">
                    +0s
                </div>
                <div
                    class="meta-badge last-question"
                    id="previewLastQuestion"
                    style="display: none">
                    Last Question
                </div>
            </div>
            <h2 id="previewQuestion">Symbol of Nitrogen is</h2>

            <div class="media-preview" id="mediaPreview"></div>

            <div class="options-grid" id="previewOptions">
                <label class="option-box"><input type="radio" name="opt" /> Na</label>
                <label class="option-box correct"><input type="radio" name="opt" checked /> N</label>
                <label class="option-box"><input type="radio" name="opt" /> Fe</label>
                <label class="option-box"><input type="radio" name="opt" /> Mg</label>
            </div>
        </div>
    </div>

    <!-- RIGHT SETTINGS / EDIT PANEL -->
    <div class="settings-panel" id="settingsPanel">
        <h3>Edit Question</h3>

        <div class="input-group">
            <label>Question Text</label>
            <textarea id="qText" rows="3" placeholder="Type question here...">
        Symbol of Nitrogen is</textarea>
        </div>

        <!-- Round and Level Selection -->
        <div class="input-group">
            <label>Round</label>
            <select id="roundSelect">
                <option value="1">Round 1</option>
                <option value="2">Round 2</option>
            </select>
        </div>

        <div class="input-group">
            <label>Level</label>
            <select id="levelSelect">
                <option value="1">Level 1</option>
                <option value="2">Level 2</option>
            </select>
        </div>

        <!-- Extra Time Section -->
        <div class="input-group">
            <label>Extra Time (in seconds)</label>
            <input
                type="number"
                id="extraTime"
                min="0"
                max="300"
                value="0"
                placeholder="Additional time for this question" />
            <small
                style="
            color: #6b7280;
            font-size: 12px;
            margin-top: 5px;
            display: block;
          ">
                Additional time beyond the default question timer
            </small>
        </div>

        <!-- Media Upload Section -->
        <div class="media-upload-section">
            <h4>Add Media</h4>

            <div class="media-type-selector">
                <button class="media-type-btn active" data-type="none">None</button>
                <button class="media-type-btn" data-type="image">
                    <i class="fas fa-image"></i>
                </button>
                <button class="media-type-btn" data-type="video">
                    <i class="fas fa-video"></i>
                </button>
                <button class="media-type-btn" data-type="audio">
                    <i class="fas fa-music"></i>
                </button>
            </div>

            <!-- Image Upload -->
            <div
                class="media-upload-content"
                id="imageUpload"
                style="display: none">
                <div class="upload-area" id="imageDropZone">
                    <i class="fas fa-cloud-upload-alt"></i>
                    <p>Drag & drop your images here</p>
                    <span>or click to browse (multiple images allowed)</span>
                    <input
                        type="file"
                        id="imageInput"
                        accept="image/*"
                        multiple
                        style="display: none" />
                </div>
                <div class="image-gallery" id="imageGallery"></div>
            </div>

            <!-- Video Upload -->
            <div
                class="media-upload-content"
                id="videoUpload"
                style="display: none">
                <div class="drive-link-input">
                    <input
                        type="text"
                        id="videoLink"
                        placeholder="Paste Google Drive video link..." />
                </div>
                <div class="preview-media" id="videoPreview"></div>
            </div>

            <!-- Audio Upload -->
            <div
                class="media-upload-content"
                id="audioUpload"
                style="display: none">
                <div class="drive-link-input">
                    <input
                        type="text"
                        id="audioLink"
                        placeholder="Paste Google Drive audio link..." />
                </div>
                <div class="preview-media" id="audioPreview"></div>
            </div>
        </div>

        <div class="input-group">
            <label>Question Type</label>
            <div
                style="
            padding: 10px 12px;
            border: 1px solid #d1d5db;
            border-radius: 8px;
            background: #f3f4f6;
            color: #555;
            font-size: 14px;
          ">
                Multiple Choice
            </div>
        </div>

        <div class="input-group">
            <label>Options</label>
            <div id="optionsContainer">
                <div class="option-input-wrapper">
                    <input type="text" value="Na" class="opt-input" />
                    <div class="option-actions">
                        <button class="option-btn set-correct-btn">
                            <i class="fas fa-check"></i>
                        </button>
                        <button class="option-btn delete-option-btn">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                </div>
                <div class="option-input-wrapper">
                    <input type="text" value="N" class="opt-input" />
                    <div class="option-actions">
                        <button class="option-btn set-correct-btn active">
                            <i class="fas fa-check"></i>
                        </button>
                        <button class="option-btn delete-option-btn">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                </div>
                <div class="option-input-wrapper">
                    <input type="text" value="Fe" class="opt-input" />
                    <div class="option-actions">
                        <button class="option-btn set-correct-btn">
                            <i class="fas fa-check"></i>
                        </button>
                        <button class="option-btn delete-option-btn">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                </div>
                <div class="option-input-wrapper">
                    <input type="text" value="Mg" class="opt-input" />
                    <div class="option-actions">
                        <button class="option-btn set-correct-btn">
                            <i class="fas fa-check"></i>
                        </button>
                        <button class="option-btn delete-option-btn">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                </div>
            </div>
            <button class="add-option-btn" id="addOptionBtn">+ Add Option</button>
        </div>

        <!-- Is Last Question Section -->
        <div class="input-group">
            <label>Is Last Question</label>
            <div class="checkbox-group">
                <input type="checkbox" id="isLastQuestion" />
                <label for="isLastQuestion">Mark as Last Question</label>
            </div>
            <small
                style="
            color: #6b7280;
            font-size: 12px;
            margin-top: 5px;
            display: block;
          ">
                This question will be treated as the final question in the quiz
            </small>
        </div>

        <button class="save-btn" id="saveBtn">Save Question</button>
    </div>

    <div class="notification" id="notification">
        Question saved successfully!
    </div>

    <script>
        // DOM Elements
        const previewQuestion = document.getElementById("previewQuestion");
        const previewRound = document.getElementById("previewRound");
        const previewLevel = document.getElementById("previewLevel");
        const previewTime = document.getElementById("previewTime");
        const previewLastQuestion = document.getElementById(
            "previewLastQuestion"
        );
        const qText = document.getElementById("qText");
        const roundSelect = document.getElementById("roundSelect");
        const levelSelect = document.getElementById("levelSelect");
        const extraTime = document.getElementById("extraTime");
        const isLastQuestion = document.getElementById("isLastQuestion");
        const optionsContainer = document.getElementById("optionsContainer");
        const previewOptions = document.getElementById("previewOptions");
        const addOptionBtn = document.getElementById("addOptionBtn");
        const saveBtn = document.getElementById("saveBtn");
        const addSlideBtn = document.getElementById("addSlideBtn");
        const slidesContainer = document.getElementById("slidesContainer");
        const notification = document.getElementById("notification");
        const mediaPreview = document.getElementById("mediaPreview");
        const imageGallery = document.getElementById("imageGallery");
        const leftBackBtn = document.getElementById("leftBackBtn");
        const quizDetailsToggle = document.getElementById("quizDetailsToggle");
        const quizDetailsPanel = document.getElementById("quizDetailsPanel");

        // Media Upload Elements
        const mediaTypeButtons = document.querySelectorAll(".media-type-btn");
        const mediaUploadContents = document.querySelectorAll(
            ".media-upload-content"
        );
        const imageDropZone = document.getElementById("imageDropZone");
        const imageInput = document.getElementById("imageInput");
        const videoLink = document.getElementById("videoLink");
        const videoPreview = document.getElementById("videoPreview");
        const audioLink = document.getElementById("audioLink");
        const audioPreview = document.getElementById("audioPreview");

        // Current state
        let currentSlideId = 1;
        let correctAnswerIndex = 1;
        // let slides = [{
        //         id: 1,
        //         question: "Symbol of Nitrogen is",
        //         options: ["Na", "N", "Fe", "Mg"],
        //         correctIndex: 1,
        //         media: {
        //             type: "none",
        //             images: [],
        //             videoUrl: "",
        //             audioUrl: ""
        //         },
        //         round: 1,
        //         level: 1,
        //         extraTime: 0,
        //         isLastQuestion: false,
        //     },
        //     {
        //         id: 2,
        //         question: "Who is the father of biology?",
        //         options: ["Aristotle", "Darwin", "Einstein", "Newton"],
        //         correctIndex: 0,
        //         media: {
        //             type: "none",
        //             images: [],
        //             videoUrl: "",
        //             audioUrl: ""
        //         },
        //         round: 1,
        //         level: 2,
        //         extraTime: 0,
        //         isLastQuestion: false,
        //     },
        //     {
        //         id: 3,
        //         question: "Capital of India",
        //         options: ["New Delhi", "Kolkata", "Mumbai", "Bengaluru"],
        //         correctIndex: 1,
        //         media: {
        //             type: "none",
        //             images: [],
        //             videoUrl: "",
        //             audioUrl: ""
        //         },
        //         round: 2,
        //         level: 1,
        //         extraTime: 0,
        //         isLastQuestion: false,
        //     },
        // ];
        let slides = [];

        // Initialize the editor
        function initEditor() {
            renderAllSlides();
            loadSlide(currentSlideId);
            setupEventListeners();
            setupMediaUpload();
        }

        // Set up all event listeners
        function setupEventListeners() {
            // Question text update
            qText.addEventListener("input", updatePreviewQuestion);

            // Round and level selection
            roundSelect.addEventListener("change", updatePreviewMeta);
            levelSelect.addEventListener("change", updatePreviewMeta);

            // Extra time update
            extraTime.addEventListener("input", updatePreviewMeta);

            // Last question checkbox
            isLastQuestion.addEventListener("change", updatePreviewMeta);

            // Add option button
            addOptionBtn.addEventListener("click", addOption);

            // Save button
            saveBtn.addEventListener("click", saveQuestion);

            // Add slide button
            addSlideBtn.addEventListener("click", addNewSlide);

            // Back button
            leftBackBtn.addEventListener("click", goBack);

            // Quiz details toggle
            quizDetailsToggle.addEventListener("click", toggleQuizDetails);

            // Media type selection
            mediaTypeButtons.forEach((btn) => {
                btn.addEventListener("click", () => {
                    const type = btn.dataset.type;
                    selectMediaType(type);
                });
            });

            // Drive link inputs
            videoLink.addEventListener("input", updateVideoPreview);
            audioLink.addEventListener("input", updateAudioPreview);
        }

        // Toggle quiz details panel
        function toggleQuizDetails() {
            if (quizDetailsPanel.classList.contains("expanded")) {
                closeQuizDetails();
            } else {
                openQuizDetails();
            }
        }

        // Open quiz details panel
        function openQuizDetails() {
            quizDetailsPanel.classList.add("expanded");
            quizDetailsToggle.classList.add("expanded");
        }

        // Close quiz details panel
        function closeQuizDetails() {
            quizDetailsPanel.classList.remove("expanded");
            quizDetailsToggle.classList.remove("expanded");
        }

        // Go back to previous page
        function goBack() {
            // You can implement navigation logic here
            // For now, just show an alert
            alert("Back button clicked! Implement your navigation logic here.");
        }

        // Render all slides in simple list
        function renderAllSlides() {
            slidesContainer.innerHTML = "";

            // Add slides to container
            slides.forEach((slide) => {
                const slideCard = document.createElement("div");
                slideCard.className = `slide-card ${
            slide.id === currentSlideId ? "active" : ""
          }`;
                slideCard.dataset.id = slide.id;

                // Add indicators for extra time and last question
                let indicators = "";
                if (slide.extraTime > 0) {
                    indicators += `<span style="color: #d97706; font-size: 10px; margin-right: 5px;"><i class="fas fa-clock"></i> +${slide.extraTime}s</span>`;
                }
                if (slide.isLastQuestion) {
                    indicators += `<span style="color: #be185d; font-size: 10px;"><i class="fas fa-flag-checkered"></i></span>`;
                }

                slideCard.innerHTML = `
            <div class="slide-content">
              ${
                slide.question.length > 30
                  ? slide.question.substring(0, 30) + "..."
                  : slide.question
              }
              ${indicators}
            </div>
            <div class="slide-meta">
              <span>R${slide.round}</span>
              <span>Lvl ${slide.level}</span>
            </div>
            <button class="delete-slide"><i class="fas fa-times"></i></button>
          `;
                slidesContainer.appendChild(slideCard);

                // Add event listeners
                slideCard.addEventListener("click", (e) => {
                    if (!e.target.classList.contains("delete-slide")) {
                        selectSlide(slide.id);
                    }
                });

                slideCard
                    .querySelector(".delete-slide")
                    .addEventListener("click", () => deleteSlide(slide.id));
            });
        }

        // Update preview meta (round, level, time, last question)
        function updatePreviewMeta() {
            const round = roundSelect.value;
            const level = levelSelect.value;
            const time = parseInt(extraTime.value) || 0;
            const lastQuestion = isLastQuestion.checked;

            previewRound.textContent = `Round ${round}`;
            previewLevel.textContent = `Level ${level}`;

            // Update time badge
            if (time > 0) {
                previewTime.textContent = `+${time}s`;
                previewTime.style.display = "flex";
            } else {
                previewTime.style.display = "none";
            }

            // Update last question badge
            if (lastQuestion) {
                previewLastQuestion.style.display = "flex";
            } else {
                previewLastQuestion.style.display = "none";
            }

            // ✅ UPDATE LEFT SIDE LIST IN REAL-TIME
            updateCurrentSlideData();
        }

        // ✅ NEW FUNCTION: Update current slide data in real-time
        function updateCurrentSlideData() {
            const slideIndex = slides.findIndex((s) => s.id === currentSlideId);
            if (slideIndex !== -1) {
                slides[slideIndex].round = parseInt(roundSelect.value);
                slides[slideIndex].level = parseInt(levelSelect.value);
                slides[slideIndex].extraTime = parseInt(extraTime.value) || 0;
                slides[slideIndex].isLastQuestion = isLastQuestion.checked;
                renderAllSlides();
            }
        }

        // Set up media upload functionality
        function setupMediaUpload() {
            // Image drop zone click
            imageDropZone.addEventListener("click", () => {
                imageInput.click();
            });

            // Image file selection
            imageInput.addEventListener("change", handleImageUpload);

            // Drag and drop for images
            imageDropZone.addEventListener("dragover", (e) => {
                e.preventDefault();
                imageDropZone.style.borderColor = "#4f46e5";
                imageDropZone.style.background = "#f8faff";
            });

            imageDropZone.addEventListener("dragleave", () => {
                imageDropZone.style.borderColor = "#d1d5db";
                imageDropZone.style.background = "";
            });

            imageDropZone.addEventListener("drop", (e) => {
                e.preventDefault();
                imageDropZone.style.borderColor = "#d1d5db";
                imageDropZone.style.background = "";

                if (e.dataTransfer.files.length > 0) {
                    const files = Array.from(e.dataTransfer.files).filter((file) =>
                        file.type.startsWith("image/")
                    );
                    if (files.length > 0) {
                        previewImageFiles(files);
                    }
                }
            });
        }

        // Select media type
        function selectMediaType(type) {
            // Update active button
            mediaTypeButtons.forEach((btn) => {
                btn.classList.toggle("active", btn.dataset.type === type);
            });

            // Show/hide relevant content
            mediaUploadContents.forEach((content) => {
                content.style.display = "none";
            });

            // Update current slide media type
            const slideIndex = slides.findIndex((s) => s.id === currentSlideId);
            if (slideIndex !== -1) {
                slides[slideIndex].media.type = type;

                // Show relevant upload area
                if (type === "image") {
                    document.getElementById("imageUpload").style.display = "block";
                    updateImageGallery();
                } else if (type === "video") {
                    document.getElementById("videoUpload").style.display = "block";
                    videoLink.value = slides[slideIndex].media.videoUrl || "";
                    updateVideoPreview();
                } else if (type === "audio") {
                    document.getElementById("audioUpload").style.display = "block";
                    audioLink.value = slides[slideIndex].media.audioUrl || "";
                    updateAudioPreview();
                }

                updateMediaPreview();
            }
        }

        // Handle image upload
        function handleImageUpload(e) {
            const files = Array.from(e.target.files).filter((file) =>
                file.type.startsWith("image/")
            );
            if (files.length > 0) {
                previewImageFiles(files);
            }
        }

        // Preview multiple image files
        function previewImageFiles(files) {
            const slideIndex = slides.findIndex((s) => s.id === currentSlideId);
            if (slideIndex === -1) return;

            files.forEach((file) => {
                const reader = new FileReader();
                reader.onload = (e) => {
                    slides[slideIndex].media.images.push(e.target.result);
                    updateImageGallery();
                    updateMediaPreview();
                };
                reader.readAsDataURL(file);
            });
        }

        // Update image gallery in settings panel
        function updateImageGallery() {
            const slideIndex = slides.findIndex((s) => s.id === currentSlideId);
            if (slideIndex === -1) return;

            imageGallery.innerHTML = "";

            slides[slideIndex].media.images.forEach((imageUrl, index) => {
                const galleryItem = document.createElement("div");
                galleryItem.className = "image-gallery-item";
                galleryItem.innerHTML = `
          <img src="${imageUrl}" alt="Preview ${index + 1}">
          <button class="remove-image" onclick="removeImage(${index})"><i class="fas fa-times"></i></button>
        `;
                imageGallery.appendChild(galleryItem);
            });
        }

        // Remove individual image
        function removeImage(index) {
            const slideIndex = slides.findIndex((s) => s.id === currentSlideId);
            if (slideIndex !== -1) {
                slides[slideIndex].media.images.splice(index, 1);
                updateImageGallery();
                updateMediaPreview();
            }
        }

        // Update video preview
        function updateVideoPreview() {
            const url = videoLink.value;
            const slideIndex = slides.findIndex((s) => s.id === currentSlideId);

            if (slideIndex !== -1 && url) {
                slides[slideIndex].media.videoUrl = url;

                if (url.includes("drive.google.com")) {
                    videoPreview.innerHTML = `
            <p>Google Drive video link added</p>
            <button class="remove-media" onclick="removeMedia()">Remove</button>
          `;
                } else {
                    videoPreview.innerHTML = `
            <p>Video link added</p>
            <button class="remove-media" onclick="removeMedia()">Remove</button>
          `;
                }

                updateMediaPreview();
            } else if (slideIndex !== -1) {
                slides[slideIndex].media.videoUrl = "";
                videoPreview.innerHTML = "";
                updateMediaPreview();
            }
        }

        // Update audio preview
        function updateAudioPreview() {
            const url = audioLink.value;
            const slideIndex = slides.findIndex((s) => s.id === currentSlideId);

            if (slideIndex !== -1 && url) {
                slides[slideIndex].media.audioUrl = url;

                if (url.includes("drive.google.com")) {
                    audioPreview.innerHTML = `
            <p>Google Drive audio link added</p>
            <button class="remove-media" onclick="removeMedia()">Remove</button>
          `;
                } else {
                    audioPreview.innerHTML = `
            <p>Audio link added</p>
            <button class="remove-media" onclick="removeMedia()">Remove</button>
          `;
                }

                updateMediaPreview();
            } else if (slideIndex !== -1) {
                slides[slideIndex].media.audioUrl = "";
                audioPreview.innerHTML = "";
                updateMediaPreview();
            }
        }

        // Update media preview in center panel
        function updateMediaPreview() {
            const slideIndex = slides.findIndex((s) => s.id === currentSlideId);
            if (slideIndex === -1) return;

            const media = slides[slideIndex].media;
            mediaPreview.innerHTML = "";

            if (media.type === "image" && media.images.length > 0) {
                if (media.images.length === 1) {
                    mediaPreview.innerHTML = `<img src="${media.images[0]}" alt="Question media">`;
                } else {
                    const multipleImagesDiv = document.createElement("div");
                    multipleImagesDiv.className = "multiple-images";
                    media.images.forEach((imageUrl) => {
                        const img = document.createElement("img");
                        img.src = imageUrl;
                        img.alt = "Question media";
                        multipleImagesDiv.appendChild(img);
                    });
                    mediaPreview.appendChild(multipleImagesDiv);
                }
            } else if (media.type === "video" && media.videoUrl) {
                mediaPreview.innerHTML = `
            <video controls>
                <source src="${media.videoUrl}" type="video/mp4">
                Your browser does not support the video tag.
            </video>
            `;
            } else if (media.type === "audio" && media.audioUrl) {
                mediaPreview.innerHTML = `
            <audio controls>
                <source src="${media.audioUrl}" type="audio/mpeg">
                Your browser does not support the audio element.
            </audio>
            `;
            }
        }

        // Remove media
        function removeMedia() {
            const slideIndex = slides.findIndex((s) => s.id === currentSlideId);
            if (slideIndex !== -1) {
                slides[slideIndex].media.images = [];
                slides[slideIndex].media.videoUrl = "";
                slides[slideIndex].media.audioUrl = "";
                slides[slideIndex].media.type = "none";

                // Reset UI
                imageGallery.innerHTML = "";
                videoPreview.innerHTML = "";
                audioPreview.innerHTML = "";
                mediaPreview.innerHTML = "";
                videoLink.value = "";
                audioLink.value = "";
                imageInput.value = "";

                // Reset media type buttons
                mediaTypeButtons.forEach((btn) => {
                    btn.classList.toggle("active", btn.dataset.type === "none");
                });

                // Hide all upload areas
                mediaUploadContents.forEach((content) => {
                    content.style.display = "none";
                });
            }
        }

        // Update preview when question text changes
        function updatePreviewQuestion() {
            previewQuestion.textContent =
                qText.value || "Your question will appear here";
        }

        // Add a new option
        //   function addOption() {
        //     const optionWrapper = document.createElement("div");
        //     optionWrapper.className = "option-input-wrapper";
        //     optionWrapper.innerHTML = `
        //         <input type="text" class="opt-input" placeholder="New option" />
        //         <div class="option-actions">
        //         <button class="option-btn set-correct-btn"><i class="fas fa-check"></i></button>
        //         <button class="option-btn delete-option-btn"><i class="fas fa-times"></i></button>
        //         </div>
        //     `;
        //     optionsContainer.appendChild(optionWrapper);
        //     setupOptionEventListeners();
        //     renderPreviewOptions();
        //   }

        function addOption() {
            // Limit to maximum 4 options
            if (optionsContainer.children.length >= 4) {
                showNotification("You can add a maximum of 4 options.", "error");
                return;
            }

            const optionWrapper = document.createElement("div");
            optionWrapper.className = "option-input-wrapper";
            optionWrapper.innerHTML = `
        <input type="text" class="opt-input" placeholder="New option" />
        <div class="option-actions">
            <button class="option-btn set-correct-btn"><i class="fas fa-check"></i></button>
            <button class="option-btn delete-option-btn"><i class="fas fa-times"></i></button>
        </div>
    `;
            optionsContainer.appendChild(optionWrapper);
            setupOptionEventListeners();
            renderPreviewOptions();
        }



        // Set up event listeners for option buttons
        function setupOptionEventListeners() {
            document.querySelectorAll(".set-correct-btn").forEach((btn, index) => {
                btn.addEventListener("click", () => setCorrectAnswer(index));
            });

            document
                .querySelectorAll(".delete-option-btn")
                .forEach((btn, index) => {
                    btn.addEventListener("click", () => deleteOption(index));
                });

            document.querySelectorAll(".opt-input").forEach((input) => {
                input.addEventListener("input", renderPreviewOptions);
            });
        }

        // Set the correct answer
        function setCorrectAnswer(index) {
            correctAnswerIndex = index;

            document.querySelectorAll(".set-correct-btn").forEach((btn, i) => {
                if (i === index) {
                    btn.classList.add("active");
                } else {
                    btn.classList.remove("active");
                }
            });

            renderPreviewOptions();
        }

        // Delete an option
        function deleteOption(index) {
            const inputs = document.querySelectorAll(".option-input-wrapper");
            if (inputs.length > 2) {
                inputs[index].remove();

                if (index === correctAnswerIndex) {
                    setCorrectAnswer(0);
                } else if (index < correctAnswerIndex) {
                    setCorrectAnswer(correctAnswerIndex - 1);
                }

                renderPreviewOptions();
            } else {
                showNotification("You need at least 2 options", "error");
            }
        }

        // Render the option inputs based on current data
        function renderOptionsInputs() {
            const currentSlide = slides.find((s) => s.id === currentSlideId);
            if (!currentSlide) return;

            optionsContainer.innerHTML = "";
            currentSlide.options.forEach((option, index) => {
                const optionWrapper = document.createElement("div");
                optionWrapper.className = "option-input-wrapper";
                optionWrapper.innerHTML = `
          <input type="text" class="opt-input" value="${option}" />
          <div class="option-actions">
            <button class="option-btn set-correct-btn ${
              index === currentSlide.correctIndex ? "active" : ""
            }"><i class="fas fa-check"></i></button>
            <button class="option-btn delete-option-btn"><i class="fas fa-times"></i></button>
          </div>
        `;
                optionsContainer.appendChild(optionWrapper);
            });

            setupOptionEventListeners();
        }

        // Render the preview options
        function renderPreviewOptions() {
            const inputs = document.querySelectorAll(".opt-input");

            previewOptions.innerHTML = "";

            inputs.forEach((inp, index) => {
                const label = document.createElement("label");
                label.className = `option-box ${
            index === correctAnswerIndex ? "correct" : ""
          }`;
                label.innerHTML = `<input type='radio' name='opt' ${
            index === correctAnswerIndex ? "checked" : ""
          } /> ${inp.value || "Option " + (index + 1)}`;
                previewOptions.appendChild(label);
            });
        }

        // Save the current question
        function saveQuestion() {
            const questionText = qText.value;
            const options = Array.from(document.querySelectorAll(".opt-input")).map(
                (input) => input.value
            );
            const round = parseInt(roundSelect.value);
            const level = parseInt(levelSelect.value);
            const time = parseInt(extraTime.value) || 0;
            const lastQuestion = isLastQuestion.checked;

            // Validation
            if (!questionText.trim()) {
                showNotification("Please enter a question", "error");
                return;
            }

            if (options.some((opt) => !opt.trim())) {
                showNotification("Please fill in all options", "error");
                return;
            }

            // Update slide data
            const slideIndex = slides.findIndex((s) => s.id === currentSlideId);
            if (slideIndex !== -1) {
                slides[slideIndex].question = questionText;
                slides[slideIndex].options = options;
                slides[slideIndex].correctIndex = correctAnswerIndex;
                slides[slideIndex].round = round;
                slides[slideIndex].level = level;
                slides[slideIndex].extraTime = time;
                slides[slideIndex].isLastQuestion = lastQuestion;

                // Update UI
                renderAllSlides();
            }

            showNotification("Question saved successfully!");
        }

        // Add a new slide
        function addNewSlide() {
            const newId =
                slides.length > 0 ? Math.max(...slides.map((s) => s.id)) + 1 : 1;
            const newSlide = {
                id: newId,
                question: "New Question",
                options: ["Option 1", "Option 2", "Option 3", "Option 4"],
                correctIndex: 0,
                media: {
                    type: "none",
                    images: [],
                    videoUrl: "",
                    audioUrl: ""
                },
                round: 1,
                level: 1,
                extraTime: 0,
                isLastQuestion: false,
            };

            slides.push(newSlide);
            renderAllSlides();
            selectSlide(newId);
        }

        // Select a slide
        function selectSlide(id) {
            currentSlideId = id;

            // Update UI
            document.querySelectorAll(".slide-card").forEach((card) => {
                if (parseInt(card.dataset.id) === id) {
                    card.classList.add("active");
                } else {
                    card.classList.remove("active");
                }
            });

            // Load slide data
            loadSlide(id);
        }

        // Load a slide into the editor
        function loadSlide(id) {
            const slide = slides.find((s) => s.id === id);
            if (!slide) return;

            qText.value = slide.question;
            correctAnswerIndex = slide.correctIndex;
            roundSelect.value = slide.round;
            levelSelect.value = slide.level;
            extraTime.value = slide.extraTime;
            isLastQuestion.checked = slide.isLastQuestion;

            // Load media
            selectMediaType(slide.media.type);
            if (slide.media.type === "image") {
                updateImageGallery();
            } else if (slide.media.type === "video") {
                videoLink.value = slide.media.videoUrl || "";
                updateVideoPreview();
            } else if (slide.media.type === "audio") {
                audioLink.value = slide.media.audioUrl || "";
                updateAudioPreview();
            }

            updatePreviewQuestion();
            updatePreviewMeta();
            renderOptionsInputs();
            renderPreviewOptions();
            updateMediaPreview();
        }

        // Delete a slide
        function deleteSlide(id) {
            if (slides.length <= 1) {
                showNotification("You need at least one slide", "error");
                return;
            }

            // Remove from data
            slides = slides.filter((s) => s.id !== id);

            // Select another slide if we deleted the current one
            if (id === currentSlideId) {
                selectSlide(slides[0].id);
            }

            renderAllSlides();
        }

        // Show notification
        function showNotification(message, type = "success") {
            notification.textContent = message;
            notification.className = `notification ${
          type === "error" ? "error" : "success"
        } show`;

            setTimeout(() => {
                notification.classList.remove("show");
            }, 3000);
        }

        // Style for error notifications
        const style = document.createElement("style");
        style.textContent = `
        .notification.error {
            background: #ef4444;
        }
        `;
        document.head.appendChild(style);

        // Initialize the editor when DOM is loaded
        document.addEventListener("DOMContentLoaded", initEditor);
    </script>
</body>

</html>
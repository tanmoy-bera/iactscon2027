-- Table Quizzes

CREATE TABLE quizzes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    
    title VARCHAR(255) NOT NULL,
    description TEXT DEFAULT NULL,
    
    type ENUM('qualifier', 'main') NOT NULL DEFAULT 'main',         -- Quiz type
    mode ENUM('manual', 'auto') NOT NULL DEFAULT 'manual',          -- Start/Pause mode
    
    total_levels INT NOT NULL DEFAULT 1,
    
    start_time DATETIME DEFAULT NULL,                               -- Scheduled or actual start time
    question_time INT NOT NULL DEFAULT 20,                          -- Per-question time in seconds
    buffer_time INT NOT NULL DEFAULT 5,                             -- Time between questions
    
    status ENUM('draft', 'active', 'completed') NOT NULL DEFAULT 'draft',
    
    parent_id INT DEFAULT NULL,                                     -- Reference to qualifier quiz (for main round)
    
    created_by INT NOT NULL,
    updated_by INT DEFAULT NULL,
    
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    -- Table index
    INDEX idx_quiz_type (type),
    INDEX idx_quiz_status (status),
    INDEX idx_quiz_mode (mode),
    INDEX idx_parent (parent_id),
    INDEX idx_creator (created_by)
);

-- Table Quizzes
CREATE TABLE quiz_questions (
    id INT AUTO_INCREMENT PRIMARY KEY,

    quiz_id INT NOT NULL,
    level INT NOT NULL DEFAULT 1,

    question_text TEXT NOT NULL,

    media_url TEXT DEFAULT NULL COMMENT 'Optional: image, video, or audio URL',

    option_a VARCHAR(255) NOT NULL,
    option_b VARCHAR(255) NOT NULL,
    option_c VARCHAR(255) NOT NULL,
    option_d VARCHAR(255) NOT NULL,

    correct_option ENUM('A', 'B', 'C', 'D') NOT NULL,

    timer_override INT DEFAULT NULL COMMENT 'Overrides quiz default question time (in seconds)',

    status ENUM('draft', 'active', 'archived') NOT NULL DEFAULT 'draft',

    created_by INT NOT NULL,
    updated_by INT DEFAULT NULL,

    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    -- Table index
    INDEX idx_quiz (quiz_id),
    INDEX idx_quiz_level (quiz_id, level),
    INDEX idx_status (status)
);

-- Table answers
CREATE TABLE quiz_answers (
    id INT AUTO_INCREMENT PRIMARY KEY,

    quiz_id INT NOT NULL,
    user_id INT NOT NULL,
    question_id INT NOT NULL,

    selected_option ENUM('A', 'B', 'C', 'D') DEFAULT NULL COMMENT 'NULL if not answered',
    is_correct TINYINT(1) DEFAULT NULL COMMENT '1 = correct, 0 = incorrect, NULL = not answered',

    time_taken DECIMAL(6,3) DEFAULT NULL COMMENT 'Time in seconds, up to 3 decimal places',
    answered_at DATETIME DEFAULT NULL,

    -- Table index
    INDEX idx_quiz (quiz_id),
    INDEX idx_question (question_id),
    INDEX idx_user (user_id),
    INDEX idx_answered (answered_at)
);



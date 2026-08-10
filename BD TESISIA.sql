DROP DATABASE IF EXISTS ai_tesis;
CREATE DATABASE ai_tesis
CHARACTER SET utf8mb4
COLLATE utf8mb4_unicode_ci;

USE ai_tesis;

-- ============================================
-- ROLES
-- ============================================

CREATE TABLE roles (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100) NOT NULL UNIQUE,
    description VARCHAR(255)
) ENGINE=InnoDB;

-- ============================================
-- USERS
-- ============================================

CREATE TABLE users (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(150) NOT NULL,
    last_name VARCHAR(150) NOT NULL,
    email VARCHAR(150) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role_id BIGINT NOT NULL,
    is_active BOOLEAN DEFAULT TRUE,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    FOREIGN KEY (role_id) REFERENCES roles(id)
) ENGINE=InnoDB;

-- ============================================
-- PLANS 
-- ============================================

CREATE TABLE plans (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100) NOT NULL UNIQUE,
    code VARCHAR(10) NULL,
    description TEXT NULL,
    price DECIMAL(10,2) NOT NULL,
    duration_days INT NULL,
    max_sections INT NULL,
    max_messages INT NULL,
    max_exports INT NULL,
    is_active BOOLEAN DEFAULT TRUE,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- ============================================
-- COUPONS
-- ============================================
CREATE TABLE coupons (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    code VARCHAR(50) NOT NULL UNIQUE,
    description VARCHAR(255),

    discount_type ENUM('percentage','fixed') NOT NULL,
    discount_value DECIMAL(10,2) NOT NULL,

    max_uses INT NULL,
    used_count INT DEFAULT 0,

    max_uses_per_user INT DEFAULT 1,

    valid_from DATETIME,
    valid_until DATETIME,

    first_purchase_only BOOLEAN DEFAULT FALSE,

    is_active BOOLEAN DEFAULT TRUE,

    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- ============================================
-- SECTIONS
-- ============================================

CREATE TABLE sections (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    title VARCHAR(200) NOT NULL,
    sub_title VARCHAR(200) NULL,
    type VARCHAR(20) NULL,
    description TEXT,
    objective TEXT,
    order_index INT NULL,
    is_active BOOLEAN DEFAULT TRUE,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- ADD PARA ESTRUCTURA

-- ============================================
-- USER_PLANS
-- ============================================
CREATE TABLE user_plan(
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    user_id BIGINT NOT NULL,
    plan_id BIGINT NOT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    UNIQUE (user_id, plan_id),

    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (plan_id) REFERENCES plans(id) ON DELETE CASCADE 
) ENGINE=InnoDB;


-- ============================================
-- PLAN_NODES
-- ============================================
CREATE TABLE plan_node(
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    user_plan_id BIGINT  NULL,
    plan_id BIGINT NOT NULL,
    parent_id INT NULL,
    titulo VARCHAR(200) NULL,
    orden INT NULL,
    objective TEXT NULL,
    nivel TINYINT NOT NULL,
    codigo VARCHAR(20) NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,


    FOREIGN KEY (plan_id) REFERENCES plans(id) ON DELETE CASCADE
    -- FOREIGN KEY (user_plan_id) REFERENCES user_plan(id) ON DELETE CASCADE

) ENGINE=InnoDB;

-- ============================================
-- QUESTIONS
-- ============================================

CREATE TABLE questions (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    plan_node_id BIGINT NOT NULL,
    question_text TEXT NOT NULL, --
    question_detail TEXT  NULL, --
    question_example TEXT  NULL, --
    -- evidencia_detail TEXT  NULL,     
    validation_detail TEXT  NULL,
    apa_detail TEXT  NULL,
    question_variables JSON, --
    question_warning TEXT NULL,
     -- Tipo de respuesta permitida
    allow_image BOOLEAN DEFAULT FALSE,
   

    type_section VARCHAR(20) NULL,
    order_index INT NULL,
    is_required BOOLEAN DEFAULT FALSE,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,

    FOREIGN KEY (plan_node_id) REFERENCES plan_node(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- FIN ESTRUCTURE

-- ============================================
-- CONVERSATIONS (MÚLTIPLES POR USUARIO) => El estado se guarda
-- ============================================

CREATE TABLE conversations (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    user_plan_id BIGINT NOT NULL, -- X ESTRUCTURE (user_id)
    title VARCHAR(255) DEFAULT 'Proyecto de Tesis',
    summary LONGTEXT,
    status ENUM('active','completed','archived') DEFAULT 'active',
    started_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    last_activity_at DATETIME,

    FOREIGN KEY (user_plan_id) REFERENCES user_plan(id) ON DELETE CASCADE,

    INDEX (user_plan_id),
    INDEX (status)
) ENGINE=InnoDB;

-- ============================================
-- CONVERSATION_MESSAGES (MEMORIA GLOBAL) => Donde se guarda de forma detallada cada mensaje de una conversación
-- ============================================

CREATE TABLE conversation_messages (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    conversation_id BIGINT NOT NULL,
    section_id BIGINT NULL,
    question_id BIGINT NULL,
    role ENUM('system','user','assistant') NOT NULL,
    message_text LONGTEXT NOT NULL,
    tokens_used INT,
    image_ia_count INT DEFAULT 0,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,

    FOREIGN KEY (conversation_id) REFERENCES conversations(id) ON DELETE CASCADE,
    FOREIGN KEY (section_id) REFERENCES sections(id) ON DELETE SET NULL,
    FOREIGN KEY (question_id) REFERENCES questions(id) ON DELETE SET NULL,

    INDEX idx_conversation_created (conversation_id, created_at),
    INDEX (section_id),
    INDEX (question_id)
) ENGINE=InnoDB;

-- ============================================
-- USER_ANSWERS (RESPUESTAS ESTRUCTURADAS)
-- ============================================

CREATE TABLE user_answers (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    user_id BIGINT NOT NULL,
    section_id BIGINT  NULL,
    question_id BIGINT  NULL,
    conversation_id BIGINT NOT NULL,
    answer_text LONGTEXT,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    UNIQUE (conversation_id, question_id),

    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    -- FOREIGN KEY (section_id) REFERENCES sections(id) ON DELETE CASCADE,
    FOREIGN KEY (conversation_id) REFERENCES conversations(id) ON DELETE CASCADE,

    INDEX (conversation_id)
) ENGINE=InnoDB;

-- ============================================
-- references_library
-- ============================================
CREATE TABLE references_library (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    authors TEXT NOT NULL,
    publication_year VARCHAR(10),
    title TEXT NOT NULL,
    publisher VARCHAR(255),
    url TEXT,
    source_type ENUM(
        'book',
        'journal_article',
        'web_page',
        'thesis',
        'report',
        'conference_paper'
    ) DEFAULT 'book',
    reference_hash CHAR(64) UNIQUE NOT NULL,
    doi VARCHAR(100),
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- ============================================
-- answer_reference_rel
-- ============================================
CREATE TABLE answer_reference_rel (
    id BIGINT AUTO_INCREMENT PRIMARY KEY,

    answer_id BIGINT NOT NULL,
    reference_id BIGINT NOT NULL,

    citation_order INT NULL,

    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,

    UNIQUE(answer_id, reference_id),

    FOREIGN KEY (answer_id) REFERENCES user_answers(id) ON DELETE CASCADE,
    FOREIGN KEY (reference_id) REFERENCES references_library(id) ON DELETE CASCADE
);



-- ============================================
-- answer_files
-- ============================================

CREATE TABLE answer_files (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    answer_id BIGINT NOT NULL,

    file_type ENUM('image', 'document') NOT NULL,

    file_path VARCHAR(500) NOT NULL,
    original_name VARCHAR(255),
    mime_type VARCHAR(100),
    size BIGINT,
    description TEXT NULL,
    fuente VARCHAR(255) NULL,
    metadata JSON NULL,
    analysis TEXT NULL,

    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,

    FOREIGN KEY (answer_id)
        REFERENCES user_answers(id)
        ON DELETE CASCADE
);

-- ============================================
-- conversation_section_progress
-- ============================================

CREATE TABLE conversation_section_progress (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,

    user_id BIGINT NOT NULL,
    conversation_id BIGINT NOT NULL,
    section_id BIGINT NOT NULL,

    current_question_id BIGINT NULL,

    status ENUM('not_started','in_progress','completed') DEFAULT 'not_started',
    progress_percentage DECIMAL(5,2) DEFAULT 0,

    started_at DATETIME,
    completed_at DATETIME,

    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP 
    ON UPDATE CURRENT_TIMESTAMP,

    UNIQUE (conversation_id, section_id),

    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (conversation_id) REFERENCES conversations(id) ON DELETE CASCADE,
    FOREIGN KEY (section_id) REFERENCES sections(id) ON DELETE CASCADE,
    FOREIGN KEY (current_question_id) REFERENCES questions(id) ON DELETE SET NULL,

    INDEX (user_id),
    INDEX (conversation_id),
    INDEX (status)

) ENGINE=InnoDB;

-- ============================================
-- MEMORY EMBEDDINGS => Son para tener mapeado el contexto de las conversaciones
-- ============================================

CREATE TABLE memory_embeddings (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    conversation_id BIGINT NOT NULL,
    content LONGTEXT NOT NULL,
    embedding_vector JSON,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,

    FOREIGN KEY (conversation_id) REFERENCES conversations(id) ON DELETE CASCADE,

    INDEX (conversation_id)
) ENGINE=InnoDB;

-- ============================================
-- GENERATED DOCUMENTS
-- ============================================

CREATE TABLE generated_documents (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    user_id BIGINT NOT NULL,
    conversation_id BIGINT NOT NULL,
    file_name VARCHAR(255),
    file_path VARCHAR(500),
    file_type ENUM('pdf','docx'),
    version INT DEFAULT 1,
    status ENUM('generating','completed','failed') DEFAULT 'generating',
    generated_at DATETIME DEFAULT CURRENT_TIMESTAMP,

    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (conversation_id) REFERENCES conversations(id) ON DELETE CASCADE,

    INDEX (user_id),
    INDEX (conversation_id)
) ENGINE=InnoDB;






-- ===================================================

CREATE TABLE plan_sections (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    plan_id BIGINT NOT NULL,
    section_id BIGINT NOT NULL,

    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,

    UNIQUE (plan_id, section_id),

    FOREIGN KEY (plan_id) REFERENCES plans(id) ON DELETE CASCADE,
    FOREIGN KEY (section_id) REFERENCES sections(id) ON DELETE CASCADE,

    INDEX (plan_id),
    INDEX (section_id)
) ENGINE=InnoDB;





-- ================= TABLAS EXCEL
CREATE TABLE tablas (
    id BIGINT AUTO_INCREMENT PRIMARY KEY,
    answer_id BIGINT NOT NULL,
    nombre VARCHAR(255) NOT NULL,
    archivo_original VARCHAR(255),
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,

    FOREIGN KEY (answer_id)
        REFERENCES user_answers(id)
        ON DELETE CASCADE
);


ALTER TABLE tablas
ADD COLUMN data JSON;

ALTER TABLE tablas
ADD fuente VARCHAR(255);



-- ADDDDD PAGO PAQUETES 
CREATE TABLE packages (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100) NOT NULL UNIQUE,
    description TEXT,
    duration_months INT NOT NULL,
    local_price DECIMAL(10,2) NOT NULL,
    international_price DECIMAL(10,2) NOT NULL,
    unit_price DECIMAL(10,2) NOT NULL,
    benefits TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);


CREATE TABLE package_plan (
    package_id BIGINT  NOT NULL,
    plan_id BIGINT  NOT NULL,

    PRIMARY KEY (package_id, plan_id),

    FOREIGN KEY (package_id) REFERENCES packages(id) ON DELETE CASCADE,
    FOREIGN KEY (plan_id) REFERENCES plans(id) ON DELETE CASCADE
);

-- ============================================
-- USER_SUBSCRIPTIONS => Pone el estado en una tabla estado de suscripcion
-- ============================================

CREATE TABLE user_subscriptions (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    user_id BIGINT NOT NULL,
    package_id BIGINT NOT NULL,
    plan_id BIGINT NULL,
    provider_subscription_id VARCHAR(150),
    status ENUM('pending','active','expired','cancelled') DEFAULT 'pending',

    start_date DATETIME,
    end_date DATETIME,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,

    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (package_id) REFERENCES packages(id) ON DELETE RESTRICT,

    INDEX (user_id),
    INDEX (status)
) ENGINE=InnoDB;

-- ============================================
-- GENERATED coupon_plans 
-- ============================================
CREATE TABLE coupon_package (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,

    coupon_id BIGINT NOT NULL,
    package_id BIGINT NOT NULL,

    FOREIGN KEY (coupon_id) REFERENCES coupons(id) ON DELETE CASCADE,
    FOREIGN KEY (package_id) REFERENCES packages(id) ON DELETE CASCADE,

    UNIQUE (coupon_id, package_id)
) ENGINE=InnoDB;


-- ============================================
-- PAYMENTS
-- ============================================
CREATE TABLE payments (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    user_id BIGINT NOT NULL,
    subscription_id BIGINT NULL,
    amount DECIMAL(10,2) NOT NULL,
    currency VARCHAR(10) DEFAULT 'USD',
    payment_provider VARCHAR(50),
    provider_payment_id VARCHAR(150),
    status ENUM('pending','completed','failed','refunded') DEFAULT 'pending',
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,

    payment_type ENUM('subscription','one_time') DEFAULT 'subscription',

    coupon_id BIGINT NULL,
    discount_amount DECIMAL(10,2) DEFAULT 0,
    final_amount DECIMAL(10,2),

    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (subscription_id) REFERENCES user_subscriptions(id) ON DELETE CASCADE,
    FOREIGN KEY (coupon_id) REFERENCES coupons(id) ON DELETE CASCADE,

    INDEX (user_id),
    INDEX (status)
) ENGINE=InnoDB;


-- ================================================
-- La tabla payment_events se usa cuando quieres guardar todos los eventos o notificaciones que envía la pasarela, no solo el pago final.
-- ================================================

CREATE TABLE payment_events (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    payment_id BIGINT NOT NULL,
    event_type VARCHAR(50) NOT NULL,  
    provider_event_id VARCHAR(150),   
    data JSON,                        
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (payment_id) REFERENCES payments(id) ON DELETE CASCADE,
    INDEX (payment_id),
    INDEX (event_type)
) ENGINE=InnoDB;



-- ============================================
-- GENERATED coupon_redemptions => registra quien uso el cupon
-- ============================================
CREATE TABLE coupon_redemptions (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,

    coupon_id BIGINT NOT NULL,
    user_id BIGINT NOT NULL,
    payment_id BIGINT NULL,

    discount_amount DECIMAL(10,2),

    used_at DATETIME DEFAULT CURRENT_TIMESTAMP,

    FOREIGN KEY (coupon_id) REFERENCES coupons(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (payment_id) REFERENCES payments(id) ON DELETE SET NULL,

    INDEX (coupon_id),
    INDEX (user_id)
) ENGINE=InnoDB;

-- ============================================
-- user_token_quota
-- ============================================

CREATE TABLE user_token_quota (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    user_id BIGINT NOT NULL,
    subscription_id BIGINT NOT NULL,      
    tokens_used BIGINT NOT NULL DEFAULT 0, 
    tokens_limit BIGINT NOT NULL DEFAULT 10000, 
    period_start DATETIME,
    period_end DATETIME,

    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (subscription_id) REFERENCES user_subscriptions(id) ON DELETE CASCADE,

    UNIQUE (user_id, subscription_id, period_start), 
    INDEX (user_id),
    INDEX (subscription_id),
    INDEX (period_start),
    INDEX (period_end)
) ENGINE=InnoDB;


-- ==========================
ALTER TABLE conversations
ADD subscription_id BIGINT NULL;

ALTER TABLE conversations
ADD CONSTRAINT fk_subscription
FOREIGN KEY (subscription_id)
REFERENCES user_subscriptions(id)
ON DELETE CASCADE;

ALTER TABLE user_subscriptions
ADD updated_at DATETIME NULL;

ALTER TABLE conversations
ADD created_at DATETIME NULL;
ALTER TABLE conversations
ADD updated_at DATETIME NULL;



ALTER TABLE payments
ADD COLUMN operation_number VARCHAR(100) NULL,
ADD COLUMN security_code VARCHAR(100) NULL,
ADD COLUMN voucher_path VARCHAR(255) NULL;
ALTER TABLE payments
ADD COLUMN updated_at DATETIME NULL;

ALTER TABLE packages
ADD COLUMN num_plans INT NOT NULL DEFAULT 0;

ALTER TABLE packages
ADD COLUMN is_active INT NOT NULL DEFAULT 1;

-- ALTER TABLE conversations
--     ADD COLUMN plan_id BIGINT NOT NULL,
--     ADD CONSTRAINT fk_conversations_plan
--         FOREIGN KEY (plan_id) REFERENCES plans(id),
--     ADD INDEX idx_conversations_plan (plan_id);


-- ============================================
-- QUESTIONS MASTERS
-- ============================================

CREATE TABLE questions_masters (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,

    question TEXT NOT NULL, 
    detail TEXT  NULL, 
    example TEXT  NULL, 
    advertencia TEXT  NULL,
    order_index INT NULL,
    active BOOLEAN DEFAULT TRUE,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE user_answers_diagnostic (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    user_id BIGINT NOT NULL,
    question_master_id BIGINT NOT NULL,
    answer_text LONGTEXT,

    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (question_master_id) REFERENCES questions_masters(id) ON DELETE CASCADE,

    UNIQUE(user_id, question_master_id)
) ENGINE=InnoDB;






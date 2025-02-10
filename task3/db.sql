CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    full_name VARCHAR(150) NOT NULL,
    phone VARCHAR(20) NOT NULL,
    email VARCHAR(255) NOT NULL UNIQUE,
    birth_date DATE NOT NULL,
    gender ENUM('м', 'ж') NOT NULL,
    bio TEXT,
    contract_accepted BOOLEAN NOT NULL DEFAULT FALSE
);

CREATE TABLE programming_languages (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(50) NOT NULL UNIQUE
);

CREATE TABLE user_languages (
    user_id INT NOT NULL,
    language_id INT NOT NULL,
    PRIMARY KEY (user_id, language_id),
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (language_id) REFERENCES programming_languages(id) ON DELETE CASCADE
);

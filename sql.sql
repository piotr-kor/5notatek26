CREATE DATABASE notes_app CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

USE notes_app;

CREATE TABLE notes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    content TEXT NOT NULL
);

-- Wstawiamy 5 pustych notatek
INSERT INTO notes (content) VALUES (''), (''), (''), (''), ('');


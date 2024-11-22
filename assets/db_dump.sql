-- SQL dump for Dalhousie Forum database

CREATE DATABASE IF NOT EXISTS dalhousie_forum;
USE dalhousie_forum;

-- Users Table
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL
);

-- Posts Table
CREATE TABLE posts (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    title VARCHAR(255) NOT NULL,
    content TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Messages Table
CREATE TABLE messages (
    id INT AUTO_INCREMENT PRIMARY KEY,
    sender_id INT NOT NULL,
    receiver_id INT NOT NULL,
    content TEXT NOT NULL,
    timestamp TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (sender_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (receiver_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Likes Table
CREATE TABLE likes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    post_id INT NOT NULL,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (post_id) REFERENCES posts(id) ON DELETE CASCADE
);

-- Upvotes Table
CREATE TABLE upvotes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    post_id INT NOT NULL,
    UNIQUE(user_id, post_id),
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (post_id) REFERENCES posts(id) ON DELETE CASCADE
);

-- Sample Data for Users
INSERT INTO users (username, password) VALUES
('john_doe', 'hashed_password_123'),
('jane_smith', 'hashed_password_456');

-- Sample Data for Posts
INSERT INTO posts (user_id, title, content) VALUES
(1, 'Welcome to Dalhousie Forum', 'This is the first post on the forum!'),
(2, 'Dalhousie Forum Rules', 'Please adhere to the community guidelines.');
(2, 'Quotes', 'Do or do not, there is no try!');
(2, 'Quotes', 'I am one with the Force, and the Force is with me!');

-- Sample Data for Messages
INSERT INTO messages (sender_id, receiver_id, content) VALUES
(1, 2, 'Hello Jane! How are you?'),
(2, 1, 'Hi John! I am good, thanks for asking.');

-- Sample Data for Likes
INSERT INTO likes (user_id, post_id) VALUES
(1, 1),
(2, 1);

-- Sample Data for Upvotes
INSERT INTO upvotes (user_id, post_id) VALUES
(1, 1),
(2, 2);

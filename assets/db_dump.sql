CREATE DATABASE IF NOT EXISTS dalhousie_forum;
USE dalhousie_forum;

-- Users Table
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL
);

-- Posts Table
CREATE TABLE IF NOT EXISTS posts (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    title VARCHAR(255) NOT NULL,
    content TEXT NOT NULL,
    vote_count INT DEFAULT 0 NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    UNIQUE(user_id, title, content) -- Prevents duplicate posts for the same user with identical content
);

-- Messages Table
CREATE TABLE IF NOT EXISTS messages (
    id INT AUTO_INCREMENT PRIMARY KEY,
    sender_id INT NOT NULL,
    receiver_id INT NOT NULL,
    content TEXT NOT NULL,
    timestamp TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (sender_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (receiver_id) REFERENCES users(id) ON DELETE CASCADE,
    UNIQUE(sender_id, receiver_id, content, timestamp) -- Prevents duplicate messages
);

-- Likes Table
CREATE TABLE IF NOT EXISTS likes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    post_id INT NOT NULL,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (post_id) REFERENCES posts(id) ON DELETE CASCADE,
    UNIQUE(user_id, post_id) -- Prevents duplicate likes by the same user on the same post
);

-- Upvotes Table
CREATE TABLE IF NOT EXISTS upvotes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    post_id INT NOT NULL,
    vote_action ENUM('upvote', 'downvote') NOT NULL,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (post_id) REFERENCES posts(id) ON DELETE CASCADE,
    UNIQUE(user_id, post_id) -- Prevents duplicate upvotes/downvotes by the same user on the same post
);

-- Comments Table
CREATE TABLE IF NOT EXISTS comments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    post_id INT NOT NULL,
    user_id INT NOT NULL,
    comment TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (post_id) REFERENCES posts(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    UNIQUE(post_id, user_id, comment, created_at) -- Prevents duplicate comments on the same post by the same user
);

-- Sample Data for Users
INSERT IGNORE INTO users (username, password) VALUES
('john_doe', 'password123'), -- password: "password123"
('jane_smith', 'password123'); -- password: "mypassword456"

-- Sample Data for Posts
INSERT IGNORE INTO posts (user_id, title, content) VALUES
(1, 'Welcome to Dalhousie Forum', 'This is the first post on the forum!'),
(2, 'Dalhousie Forum Rules', 'Please adhere to the community guidelines.'),
(2, 'Quotes', 'Do or do not, there is no try!'),
(2, 'Quotes', 'I am one with the Force, and the Force is with me!'),
(2, 'Queens vs Dalhousie', 'Which university is better for undergrad experience Dalhousie or Queens?'),
(2, 'Honest thoughts, as a student what do you think of Dal?', 'Blunt insight from current Dal students on the commerce program and costs.'),
(1, 'Dalhousie Campus Life', 'Can anyone recommend the best spots to hang out on campus?'),
(2, 'Best Professors at Dal', 'Share your experiences with the best professors at Dalhousie!'),
(1, 'Studying Abroad in Halifax', 'What’s the best way to adapt to life as an international student in Halifax?'),
(2, 'Favorite Study Spots in Halifax', 'Let’s list the most underrated places to study in Halifax.');

-- Sample Data for Messages
INSERT IGNORE INTO messages (sender_id, receiver_id, content) VALUES
(1, 2, 'Hello Jane! How are you?'),
(2, 1, 'Hi John! I am good, thanks for asking.');

-- Sample Data for Comments
INSERT IGNORE INTO comments (post_id, user_id, comment) VALUES
(1, 2, 'This forum is such a great idea!'),
(1, 1, 'I’m excited to see how this grows.'),
(2, 1, 'Let’s ensure discussions stay respectful.'),
(2, 2, 'Agreed! Let’s build a positive community.'),
(3, 1, 'That’s such an inspiring quote!'),
(3, 2, 'Absolutely! It resonates with me as well.'),
(4, 1, 'The Force is strong in this community!'),
(4, 2, 'I love the positivity here.'),
(5, 1, 'Dalhousie’s marine biology program is top-notch.'),
(5, 2, 'Queens has a great undergrad experience though.'),
(6, 1, 'Commerce at Dal is excellent, but Halifax is expensive.'),
(6, 2, 'Agreed, but the education quality is worth it.'),
(7, 2, 'Campus life is vibrant. Highly recommend the student union events.'),
(7, 1, 'I found the DalPlex gym great for making friends.'),
(8, 2, 'Professor Smith’s lectures were amazing.'),
(8, 1, 'I recommend Professor Jones for advanced topics.'),
(9, 2, 'Try walking around downtown. It’s beautiful.'),
(9, 1, 'Pier 21 is a great place to visit as well.'),
(10, 2, 'Tim Hortons is underrated for study sessions.'),
(10, 1, 'The library at Dal is the best spot for focus.');

-- Sample Data for Likes
INSERT IGNORE INTO likes (user_id, post_id) VALUES
(1, 1),
(2, 1);

-- Sample Data for Upvotes
INSERT IGNORE INTO upvotes (user_id, post_id) VALUES
(1, 1),
(2, 2);

-- Assign Random Votes to Posts
UPDATE posts
SET vote_count = FLOOR(RAND() * 1000); -- Generates random numbers between 0 and 999 for vote_count
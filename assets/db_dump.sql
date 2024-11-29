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
    vote_count INT DEFAULT 0 NOT NULL, -- Add vote_count column with default 0
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);



-- Messages Table
CREATE TABLE IF NOT EXISTS messages (
    id INT AUTO_INCREMENT PRIMARY KEY,
    sender_id INT NOT NULL,
    receiver_id INT NOT NULL,
    content TEXT NOT NULL,
    timestamp TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (sender_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (receiver_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Likes Table
CREATE TABLE IF NOT EXISTS likes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    post_id INT NOT NULL,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (post_id) REFERENCES posts(id) ON DELETE CASCADE
);

-- Upvotes Table
CREATE TABLE IF NOT EXISTS upvotes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    post_id INT NOT NULL,
    UNIQUE(user_id, post_id),
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (post_id) REFERENCES posts(id) ON DELETE CASCADE
);
CREATE TABLE IF NOT EXISTS votes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    post_id INT NOT NULL,
    vote_action ENUM('upvote', 'downvote') NOT NULL,
    UNIQUE(user_id, post_id),
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (post_id) REFERENCES posts(id) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS comments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    post_id INT NOT NULL,
    user_id INT NOT NULL,
    comment TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (post_id) REFERENCES posts(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Sample Data for Comments
INSERT IGNORE INTO comments (post_id, user_id, comment) VALUES
(1, 2, 'Welcome to the forum, John! I’m excited to see how this space grows.'),
(1, 1, 'Thanks for starting this forum! It’s a great idea.'),
(2, 1, 'Let’s make sure everyone adheres to these guidelines. Respectful discussions are key.'),
(2, 2, 'Agreed, let’s keep this community positive and productive.'),
(3, 1, 'That’s such a classic quote! It always motivates me.'),
(3, 2, 'Absolutely. Yoda’s wisdom is timeless.'),
(4, 1, 'The Force is strong with this one! Love the Star Wars vibe.'),
(4, 2, 'Chirrut Îmwe is such an underrated character.'),
(5, 1, 'I think Dalhousie’s marine biology program gives it the edge. But Queens has its strengths too.'),
(5, 2, 'Queens is better for undergrad experience overall, but Dal has unique offerings in marine sciences.'),
(5, 1, 'It depends on what you want to study. Marine science? Go to Dal.'),
(6, 2, 'Halifax is a bit expensive, but Dal’s commerce program is solid.'),
(6, 1, 'The commerce program is great, but be prepared for some challenges with the cost of living in Halifax.'),
(6, 2, 'I graduated from Dal’s commerce program, and it was totally worth it.'),
(7, 2, 'Dal has a decent reputation, especially for research. It’s definitely worth considering.'),
(7, 1, 'I’ve heard mixed reviews about Dal, but it’s well-respected in certain fields.'),
(8, 1, 'I’m also looking for a room! Let me know if you find something.'),
(8, 2, 'Try looking on Facebook groups or Reddit. There are a few posts about shared rooms near Dal.'),
(9, 1, 'I’d recommend Dal if you’re into marine science. Queens is more well-rounded though.'),
(9, 2, 'Queens for social life, Dal for academics. Both are good options depending on your priorities.');

-- Sample Data for Users (with hashed passwords)
INSERT IGNORE INTO users (username, password) VALUES
('john_doe', 'password123'), -- password: "password123"
('jane_smith', 'password123'); -- password: "mypassword456"

-- Sample Data for Posts
INSERT IGNORE INTO posts (user_id, title, content) VALUES
(1, 'Welcome to Dalhousie Forum', 'This is the first post on the forum!'),
(2, 'Dalhousie Forum Rules', 'Please adhere to the community guidelines.'),
(2, 'Quotes', 'Do or do not, there is no try!'),
(2, 'Quotes', 'I am one with the Force, and the Force is with me!'),
(2, 'Queens vs Dalhousie', 'Which university is better for undergrad experience Dalhousie or Queens? I want to transfer schools and I want to be sure the school I transfer to has a good sense of community because yes I know I’m bound to feel isolated for a little while at a new school but I don’t want that to be the whole vibe and experience of the school.\n\nPros:\n\nDalhousie has marine biology and oceanography two programs that I’m interested in, as well as arts, that I want to have available as a second major option so I can do wildlife photography and videography after I graduate. Dalhousie is one of the best marine science schools in the country, field work and research on campus.\n\nQueens is in province, has a great geological science department. It is also known to be great for undergrad experience. I know a lot of people at the school, easier transition after transferring.\n\nCons:\n\nI don’t really know anyone at Dalhousie and it’s a different province I don’t know what to expect. Although this could be a good thing because it would be a new unknowing experience. Housing crisis in Halifax. Price of flights.\n\nQueens doesn’t have a marine science department even though they are next to water. They do have great research at the school, but not comparable to Dalhousie. Queens is also cutting back in their arts departments. They may no longer have fine arts but, they still have film studies.\n\nI would be interested in finding out more about both schools and experiences that other people have had at these schools!!\n\nThanks 😊😊.\n\nSource: https://www.reddit.com/r/Dalhousie/comments/1bzgpfq/queens_vs_dalhousie/, accessed on November 28, 2024.'),
(2, 'Honest thoughts, as a student what do you think of Dal?', 'So as the title suggests, I just wanted some blunt insight from current Dal students as to what they think of the uni in general and if it\'s worth applying to. Specific details on the commerce program, city of Halifax and costs would also be appreciated. If it helps any, for context I am an international IB student who\'ll probably be applying to Commerce - Accounting.\n\nSource: https://www.reddit.com/r/Dalhousie/comments/105vp5h/honest_thoughts_as_a_student_what_do_you_think_of/, accessed on November 28, 2024.'),
(1, 'Dal reputation in Canada', 'Does anyone of you know the reputation of Dal across Canada? The ranking isn’t high or low so I am not sure its quality. Would anyone share your experience or perspective, please?\n\nSource: https://www.reddit.com/r/Dalhousie/comments/113bg75/dal_reputation_in_canada/, accessed on November 28, 2024.'),
(1, 'Looking for sharing room around campus', 'I am looking for a shared room near Dalhousie campus, within a budget of $600/month. Please let me know if you have availability or know someone who does.'),
(2, 'Dalhousie or Queens for transfer?', 'Dalhousie is one of the best marine science schools in the country. Queens has a better undergrad experience. Which one would you recommend for transferring students?\n\nSource: https://www.reddit.com/r/Dalhousie/comments/1bzgpfq/queens_vs_dalhousie/, accessed on November 28, 2024.');

-- Sample Data for Messages
INSERT IGNORE INTO messages (sender_id, receiver_id, content) VALUES
(1, 2, 'Hello Jane! How are you?'),
(2, 1, 'Hi John! I am good, thanks for asking.');

-- Sample Data for Likes
INSERT IGNORE INTO likes (user_id, post_id) VALUES
(1, 1),
(2, 1);

-- Sample Data for Upvotes
INSERT IGNORE INTO upvotes (user_id, post_id) VALUES
(1, 1),
(2, 2);
-- Create the masonsroom database
CREATE DATABASE IF NOT EXISTS masonsroom;
-- Use the masonsroom database--
USE
    masonsroom;
    -- Create the users table
CREATE TABLE users(
    user_id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(255) NOT NULL,
    PASSWORD VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL,
    is_admin BOOLEAN NOT NULL DEFAULT FALSE,
    profile_pic VARCHAR(255) DEFAULT "img/user/sword.png",
    banned BOOLEAN NOT NULL DEFAULT FALSE,
    user_bio VARCHAR(120) DEFAULT "Please be nice, I'm new."
);
-- Create the topics table
CREATE TABLE topics(
    topic_id INT AUTO_INCREMENT PRIMARY KEY,
    topic_name VARCHAR(255) NOT NULL,
    topic_img VARCHAR(40) NOT NULL,
    topic_bio TEXT
);
-- Create the posts table
CREATE TABLE posts(
    post_id INT AUTO_INCREMENT PRIMARY KEY,
    topic_id INT NOT NULL,
    user_id INT NOT NULL,
    content TEXT NOT NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    pinned BOOLEAN NOT NULL DEFAULT FALSE,
    score DOUBLE DEFAULT 0,
    post_title TINYTEXT,
    deleted BOOLEAN NOT NULL DEFAULT FALSE,
    tag VARCHAR(20) DEFAULT "[Discussion]",
    FOREIGN KEY(topic_id) REFERENCES topics(topic_id),
    FOREIGN KEY(user_id) REFERENCES users(user_id)
);
-- Create the replies table
CREATE TABLE replies(
    reply_id INT AUTO_INCREMENT PRIMARY KEY,
    post_id INT NOT NULL,
    user_id INT NOT NULL,
    content TEXT NOT NULL,
    parent_id INT DEFAULT NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY(post_id) REFERENCES posts(post_id),
    FOREIGN KEY(user_id) REFERENCES users(user_id),
    FOREIGN KEY(parent_id) REFERENCES replies(reply_id)
); CREATE TABLE rated(
    post_id INT NOT NULL,
    user_id INT NOT NULL,
    rating DOUBLE NOT NULL,
    PRIMARY KEY(post_id, user_id),
    FOREIGN KEY(post_id) REFERENCES posts(post_id),
    FOREIGN KEY(user_id) REFERENCES users(user_id)
); CREATE TABLE reports(
    report_id INT AUTO_INCREMENT PRIMARY KEY,
    post_id INT,
    reply_id INT,
    topic_id INT,
    report TEXT NOT NULL,
    hero_id INT NOT NULL,
    villain_id INT NOT NULL,
    focus VARCHAR(10),
    resolved INT DEFAULT 0,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY(post_id) REFERENCES posts(post_id),
    FOREIGN KEY(topic_id) REFERENCES posts(topic_id),
    FOREIGN KEY(reply_id) REFERENCES replies(reply_id),
    FOREIGN KEY(hero_id) REFERENCES users(user_id),
    FOREIGN KEY(villain_id) REFERENCES users(user_id)
); CREATE TABLE banned(
    ban_id INT AUTO_INCREMENT PRIMARY KEY,
    reason TEXT,
    user_id INT,
    admin_id INT,
    FOREIGN KEY(user_id) REFERENCES users(user_id),
    FOREIGN KEY(admin_id) REFERENCES users(user_id)
); CREATE TABLE admins(
    user_id INT NOT NULL,
    admin_pass VARCHAR(50),
    FOREIGN KEY(user_id) REFERENCES users(user_id)
); CREATE TABLE stats(
    deletedPosts INT,
    bannedUsers INT,
    uniqueToday INT,
    uniqueMonth INT,
    uniqueTotal INT,
    postsToday INT,
    logDate TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
);
CREATE TABLE analytics (
  id int(20) NOT NULL,
  page_url varchar(150) NOT NULL,
  entry_time datetime NOT NULL,
  exit_time datetime NOT NULL,
  ip_address varchar(30) NOT NULL,
  country varchar(50) NOT NULL,
  operating_system varchar(20) NOT NULL,
  browser varchar(20) NOT NULL,
  browser_version varchar(20) NOT NULL,
  created_at timestamp NOT NULL DEFAULT current_timestamp()
);
INSERT INTO users(
    username,
    PASSWORD,
    email,
    is_admin,
    profile_pic
)
VALUES(
    'mason',
    'e43854df32d574e0a680f71b86bf7353',
    'admin@example.com',
    TRUE,
    'img/user/admin.png'
);
INSERT INTO admins(user_id, admin_pass)
VALUES(1, "masonthegoat!");
INSERT INTO topics(
    topic_name,
    topic_img,
    topic_bio
)
VALUES(
    "fruit",
    "img/topics/fruit.png",
    "Anything and Everything Fruit and fruity!"
),("usa", "img/topics/usa.png", "'MERICA"),(
    "canada",
    "img/topics/canada.png",
    "The true north strong and free!"
),(
    "news",
    "img/topics/news.png",
    "If you post here you probably read the newspaper."
),(
    "anime",
    "img/topics/anime.png",
    "こにちは！ All things Anime!"
),(
    "movies",
    "img/topics/movies.png",
    "Luke, This is the movies board"
),(
    "alcohol",
    "img/topics/alcohol.png",
    "The only hobby that is also a problem!"
),(
    "business",
    "img/topics/business.png",
    "This board is for the people who make more than they deserve!"
),(
    "software",
    "img/topics/software.png",
    "Hello World!"
),(
    "wall Street",
    "img/topics/wallStreet.png",
    "'Nobody Knows If A Stock's Going Up, Down Or F***ing Sideways, Least Of All Stockbrokers. But We Have To Pretend We Know.' -Wolf of Wall Street"
),(
    "cars",
    "img/topics/cars.png",
    "New cars, Used cars, Car mods, Trucks, Offroading, if it involves an engine and four wheels it belongs here! (no PT Cruisers)"
),(
    "music",
    "img/topics/music.png",
    "Strings, Reeds, Keys or keyboards, we dont care! Post it here!"
),(
    "tv",
    "img/topics/tv.png",
    "'I am not in danger Skylar, I AM the danger! A guy opens his door and gets shot, and you think that of me? No, I AM the one who knocks.'- Breaking Bad"
),(
    "general",
    "img/topics/general.png",
    "This is for things that don't belong anywhere else, just like you :)"
),(
    "DIY",
    "img/topics/diy.png",
    "Do it yourself! Will it be cheaper? Maybe! Will it be better? Absolutely not!"
);
INSERT INTO `users`(
    `user_id`,
    `username`,
    `password`,
    `email`,
    profile_pic
)
VALUES(
    NULL,
    'Kirbyfan101',
    '1caa76eecff5ea4659403bacbfff2b53',
    'kirby@fake.com',
    'img/user/sword.png'
);
INSERT INTO `users`(
    `user_id`,
    `username`,
    `password`,
    `email`,
    profile_pic
)
VALUES(
    NULL,
    'RealAlien123',
    '5f4dcc3b5aa765d61d8327deb882cf99',
    'alien@spaceship.com',
    'img/user/vanguard.png'
);
INSERT INTO `posts`(
    `post_id`,
    `topic_id`,
    `user_id`,
    `content`,
    `created_at`,
    `pinned`,
    `post_title`,
    `tag`
)
VALUES(
    NULL,
    '11',
    '2',
    'Hey everyone! So i visited the local aquarium the other day and during the live show I picked up one of those rain overcoats. They work surprisingly well to stop your clothes from getting covered in oil! Try this out for yourself and let me know what you think :p',
    '2023-03-01 21:44:43',
    '0',
    'How to change your oil without getting dirty!',
    "[Discussion]"
);
INSERT INTO `posts`(
    `post_id`,
    `topic_id`,
    `user_id`,
    `content`,
    `created_at`,
    `pinned`,
    `score`,
    `post_title`,
    `tag`
)
VALUES(
    NULL,
    '11',
    '1',
    '1. Don\'t be an idiot',
    '2023-03-02 14:03:51',
    '1',
    '0',
    'READ BEFORE POSTING',
    "[Admin]"
);
INSERT INTO `replies`(
    `reply_id`,
    `post_id`,
    `user_id`,
    `content`,
    `parent_id`,
    `created_at`
)
VALUES(
    NULL,
    '1',
    '3',
    'Hey thats a really cool idea! I\'ll have to pick one of those up for next time my wife complains about oil on my clothes XD',
    NULL,
    CURRENT_TIMESTAMP());
INSERT INTO `replies`(
    `post_id`,
    `user_id`,
    `content`,
    `parent_id`
)
VALUES('1', '2', 'Wait... you have a wife?', '1');
INSERT INTO `replies`(
    `post_id`,
    `user_id`,
    `content`,
    `parent_id`
)
VALUES(
    '1',
    '1',
    'Or just... dont get covered in oil?',
    NULL
);
INSERT INTO `replies`(
    `post_id`,
    `user_id`,
    `content`,
    `parent_id`
)
VALUES(
    '1',
    '2',
    'Okay wow, my first post on your website and you reply like that? Thanks.',
    '3'
);
INSERT INTO `replies`(
    `post_id`,
    `user_id`,
    `content`,
    `parent_id`
)
VALUES('1', '1', 'lmao sucks', '4');
INSERT INTO `reports` (`report_id`, `post_id`, `reply_id`, `topic_id`, `report`, `hero_id`, `villain_id`, `focus`, `resolved`, `created_at`) VALUES (NULL, '1', NULL, '11', 'Guys stupid stg', '1', '2', 'post', '0', current_timestamp());
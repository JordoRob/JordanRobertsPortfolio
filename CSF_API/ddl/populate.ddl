CREATE table IF NOT EXISTS responses
(
    id int NOT NULL AUTO_INCREMENT,
    response TEXT NOT NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    username VARCHAR(50) NOT NULL,
    DOG_NAME VARCHAR(50) NOT NULL,
    image_link TEXT,
	PRIMARY KEY (id)
);

//insert
INSERT INTO responses (id, response, username, DOG_NAME, image_link) VALUES (1, 'I love this dog!', 'doglover', 'Buddy', 'https://dogimage.com/buddy.jpg');
INSERT INTO responses (id, response, username, DOG_NAME, image_link) VALUES (2, 'I love this dog!', 'doglover', 'Buddy', 'https://dogimage.com/buddy.jpg');
INSERT INTO responses (id, response, username, DOG_NAME, image_link) VALUES (3, 'I love this dog!', 'doglover', 'Buddy', 'https://dogimage.com/buddy.jpg');
INSERT INTO responses (id, response, username, DOG_NAME, image_link) VALUES (4, 'I love this dog!', 'doglover', 'Buddy', 'https://dogimage.com/buddy.jpg');